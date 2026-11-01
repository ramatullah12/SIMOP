<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Survey;
use App\Models\SurveyPhoto;
use App\Models\User;
use App\Services\SurveyActivityLogger as Activity;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class SurveyController extends Controller
{
    /** Disk privat untuk tanda tangan & foto survei (storage/app/private). */
    private const DISK = 'local';

    // ------------------------------------------------------------------
    // Daftar & detail
    // ------------------------------------------------------------------

    public function index(Request $request): View
    {
        $user = auth()->user();
        $role = $this->role();

        abort_unless(in_array($role, ['admin', 'direktur', 'petugas-lapangan', 'client'], true), 403);

        $statusFilter = $request->query('status');

        $surveys = Survey::with(['order.client', 'assignedUser', 'approver', 'requester'])
            ->when($role === 'client', function ($query) use ($user) {
                $query->whereHas('order', fn ($q) => $q->where('client_id', $user->id))
                    ->whereNotIn('status', [Survey::STATUS_DRAFT, 'diajukan_client']);
            })
            ->when($role === 'direktur', function ($query) {
                $query->whereNotIn('status', [Survey::STATUS_DRAFT, 'diajukan_client']);
            })
            ->when($role === 'petugas-lapangan', function ($query) use ($user) {
                // Petugas hanya menerima tugas yang sudah disetujui Direktur
                $query->where('assigned_to', $user->id)
                    ->whereNotNull('approved_at')
                    ->whereIn('status', array_merge(Survey::OFFICER_STATUSES, ['dijadwalkan']));
            })
            ->when($statusFilter, fn ($query) => $query->where('status', $statusFilter))
            ->when($role === 'direktur', function ($query) {
                $query->orderByRaw("CASE WHEN status = 'menunggu_persetujuan_direktur' THEN 0 ELSE 1 END");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $statusOptions = collect(Survey::statusLabels())
            ->except(['diajukan_client', 'dijadwalkan'])
            ->when($role !== 'admin', fn ($c) => $c->except([Survey::STATUS_DRAFT]));

        return view('surveys.index', compact('surveys', 'role', 'statusFilter', 'statusOptions'));
    }

    public function show(Survey $survey): View
    {
        $role = $this->role();

        $this->authorizeView($survey);

        $survey->load(['order.client', 'assignedUser', 'approver', 'rejecter', 'requester', 'project', 'photos', 'activities']);

        $fieldOfficers = $role === 'admin' ? $this->fieldOfficers() : collect();

        return view('surveys.show', compact('survey', 'role', 'fieldOfficers'));
    }

    // ------------------------------------------------------------------
    // Pengajuan oleh Admin
    // ------------------------------------------------------------------

    public function create(Request $request): View
    {
        // Pengajuan survei hanya boleh dibuat Admin
        abort_unless($this->role() === 'admin', 403, 'Pengajuan survei hanya dapat dibuat oleh Admin.');

        $orders = Order::with('client')
            ->whereNotIn('status', ['selesai'])
            ->latest()
            ->get();

        $fieldOfficers = $this->fieldOfficers();
        $selectedOrderId = $request->query('order_id');
        $surveyTypes = Survey::SURVEY_TYPES;

        return view('surveys.create', compact('orders', 'fieldOfficers', 'selectedOrderId', 'surveyTypes'));
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($this->role() === 'admin', 403, 'Pengajuan survei hanya dapat dibuat oleh Admin.');

        $submitNow = $request->input('action') === 'submit';
        $validated = $request->validate($this->requestRules($submitNow), $this->requestMessages());

        $survey = DB::transaction(function () use ($validated, $submitNow) {
            $order = Order::findOrFail($validated['order_id']);

            $survey = Survey::create([
                'order_id' => $order->id,
                'requested_by' => auth()->id(),
                'customer_name' => $validated['customer_name'],
                'location' => $validated['location'],
                'survey_type' => $validated['survey_type'],
                'purpose' => $validated['purpose'],
                'assigned_to' => $validated['assigned_to'] ?? null,
                'scheduled_at' => $validated['scheduled_at'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'status' => Survey::STATUS_DRAFT,
            ]);

            Activity::log($survey, 'dibuat', 'Admin membuat pengajuan survei untuk ' . $survey->customer_name . '.', null, Survey::STATUS_DRAFT);

            if ($submitNow) {
                $this->sendToDirector($survey);
            }

            if (in_array($order->status, ['masuk', 'diproses', 'baru'], true)) {
                $order->update(['status' => 'menunggu_survey']);
            }

            return $survey;
        });

        return redirect()
            ->route('surveys.show', $survey)
            ->with('success', $submitNow
                ? 'Pengajuan survei berhasil dibuat dan dikirim ke Direktur untuk persetujuan.'
                : 'Pengajuan survei disimpan sebagai draf. Kirim ke Direktur bila data sudah lengkap.');
    }

    public function edit(Survey $survey): View
    {
        $role = $this->role();

        if ($role === 'admin') {
            abort_unless($survey->isEditableByAdmin(), 403, 'Pengajuan yang sudah dikirim atau disetujui tidak dapat diubah.');

            $survey->load(['order.client', 'assignedUser']);
            $orders = Order::with('client')->latest()->get();
            $fieldOfficers = $this->fieldOfficers();
            $surveyTypes = Survey::SURVEY_TYPES;

            return view('surveys.edit', compact('survey', 'orders', 'fieldOfficers', 'surveyTypes'));
        }

        if ($role === 'petugas-lapangan') {
            $this->authorizeOfficer($survey);
            abort_unless($survey->status === Survey::STATUS_WAITING_RESULT, 403, 'Form hasil survei hanya dapat diisi setelah pemeriksaan lapangan selesai.');

            $survey->load(['order.client', 'assignedUser', 'approver', 'photos']);

            return view('surveys.result', compact('survey'));
        }

        abort(403);
    }

    public function update(Request $request, Survey $survey): RedirectResponse
    {
        $role = $this->role();

        if ($role === 'petugas-lapangan') {
            return $this->saveResult($request, $survey);
        }

        abort_unless($role === 'admin', 403);
        abort_unless($survey->isEditableByAdmin(), 403, 'Pengajuan yang sudah dikirim atau disetujui tidak dapat diubah.');

        $submitNow = $request->input('action') === 'submit';
        $validated = $request->validate($this->requestRules($submitNow), $this->requestMessages());

        DB::transaction(function () use ($survey, $validated, $submitNow) {
            $fields = ['order_id', 'customer_name', 'location', 'survey_type', 'purpose', 'assigned_to', 'scheduled_at', 'notes'];
            $before = $survey->only($fields);

            $survey->update([
                'order_id' => $validated['order_id'],
                'customer_name' => $validated['customer_name'],
                'location' => $validated['location'],
                'survey_type' => $validated['survey_type'],
                'purpose' => $validated['purpose'],
                'assigned_to' => $validated['assigned_to'] ?? null,
                'scheduled_at' => $validated['scheduled_at'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            $changes = $this->diff($before, $survey->fresh()->only($fields));

            Activity::log(
                $survey,
                'diperbarui',
                $survey->status === Survey::STATUS_REJECTED
                    ? 'Admin memperbaiki pengajuan yang dikembalikan Direktur.'
                    : 'Admin memperbarui draf pengajuan survei.',
                $survey->status,
                $survey->status,
                $changes
            );

            if ($submitNow) {
                $this->sendToDirector($survey);
            }
        });

        return redirect()
            ->route('surveys.show', $survey)
            ->with('success', $submitNow
                ? 'Perbaikan pengajuan disimpan dan dikirim ulang ke Direktur.'
                : 'Perubahan pengajuan survei berhasil disimpan.');
    }

    /** Admin mengirim draf / pengajuan hasil perbaikan ke Direktur. */
    public function submitToDirektur(Request $request, Survey $survey): RedirectResponse
    {
        abort_unless($this->role() === 'admin', 403);
        abort_unless($survey->isEditableByAdmin(), 403, 'Pengajuan ini sudah dikirim atau sudah diputuskan Direktur.');

        // Form cepat di halaman detail boleh melengkapi petugas, jadwal, dan lokasi
        $validated = $request->validate([
            'assigned_to' => ['required', Rule::exists('users', 'id')],
            'scheduled_at' => ['required', 'date'],
            'location' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ], $this->requestMessages());

        if (! $survey->survey_type || ! $survey->purpose) {
            return back()->with('error', 'Lengkapi jenis kebutuhan dan tujuan survei melalui tombol "Ubah Pengajuan" sebelum dikirim ke Direktur.');
        }

        DB::transaction(function () use ($survey, $validated) {
            $survey->update([
                'assigned_to' => $validated['assigned_to'],
                'scheduled_at' => $validated['scheduled_at'],
                'location' => $validated['location'],
                'notes' => $validated['notes'] ?? $survey->notes,
            ]);

            $this->sendToDirector($survey);
        });

        return redirect()
            ->route('surveys.show', $survey)
            ->with('success', 'Pengajuan survei dikirim ke Direktur. Menunggu keputusan Direktur.');
    }

    // ------------------------------------------------------------------
    // Keputusan Direktur
    // ------------------------------------------------------------------

    public function approveByDirektur(Request $request, Survey $survey): RedirectResponse
    {
        $user = auth()->user();
        abort_unless($this->role() === 'direktur', 403);

        $request->validate(['approval_notes' => ['nullable', 'string', 'max:2000']]);

        $result = DB::transaction(function () use ($survey, $user, $request) {
            $survey = Survey::whereKey($survey->id)->lockForUpdate()->firstOrFail();

            if ($survey->status !== Survey::STATUS_WAITING_DIRECTOR) {
                return 'Pengajuan ini tidak sedang menunggu persetujuan Direktur.';
            }

            if (! $survey->assigned_to || ! $survey->scheduled_at) {
                return 'Petugas survei atau jadwal belum ditentukan Admin. Tolak pengajuan agar Admin melengkapinya.';
            }

            $survey->update([
                'status' => Survey::STATUS_APPROVED,
                'approved_by' => $user->id,
                'approved_at' => now(),
                'approval_notes' => $request->input('approval_notes'),
                'rejection_notes' => null,
                'rejected_by' => null,
                'rejected_at' => null,
            ]);

            Activity::log(
                $survey,
                'disetujui',
                'Direktur menyetujui pengajuan survei.' . ($request->filled('approval_notes') ? ' Catatan: ' . $request->input('approval_notes') : ''),
                Survey::STATUS_WAITING_DIRECTOR,
                Survey::STATUS_APPROVED
            );

            // SPS diterbitkan otomatis oleh sistem setelah disetujui
            $spsNumber = $this->generateSpsNumber($survey);

            $survey->update([
                'sps_number' => $spsNumber,
                'sps_issued_at' => now(),
                'status' => Survey::STATUS_WAITING_EXECUTION,
            ]);

            Activity::log(
                $survey,
                'sps_terbit',
                "Sistem menerbitkan Surat Perintah Survei {$spsNumber} untuk {$survey->assignedUser?->name}.",
                Survey::STATUS_APPROVED,
                Survey::STATUS_WAITING_EXECUTION,
                [],
                null,
                true
            );

            return $survey;
        });

        if (is_string($result)) {
            return back()->with('error', $result);
        }

        return redirect()
            ->route('surveys.show', $result)
            ->with('success', "Pengajuan disetujui. Surat Perintah Survei {$result->sps_number} terbit dan diteruskan ke {$result->assignedUser?->name}.");
    }

    public function rejectByDirektur(Request $request, Survey $survey): RedirectResponse
    {
        $user = auth()->user();
        abort_unless($this->role() === 'direktur', 403);

        $validated = $request->validate([
            'rejection_notes' => ['required', 'string', 'min:5', 'max:2000'],
        ], [
            'rejection_notes.required' => 'Alasan penolakan wajib diisi.',
            'rejection_notes.min' => 'Alasan penolakan terlalu singkat.',
        ]);

        $result = DB::transaction(function () use ($survey, $user, $validated) {
            $survey = Survey::whereKey($survey->id)->lockForUpdate()->firstOrFail();

            if ($survey->status !== Survey::STATUS_WAITING_DIRECTOR) {
                return 'Pengajuan ini tidak sedang menunggu persetujuan Direktur.';
            }

            $survey->update([
                'status' => Survey::STATUS_REJECTED,
                'rejection_notes' => $validated['rejection_notes'],
                'rejected_by' => $user->id,
                'rejected_at' => now(),
            ]);

            Activity::log(
                $survey,
                'ditolak',
                'Direktur menolak pengajuan. Alasan: ' . $validated['rejection_notes'],
                Survey::STATUS_WAITING_DIRECTOR,
                Survey::STATUS_REJECTED
            );

            return $survey;
        });

        if (is_string($result)) {
            return back()->with('error', $result);
        }

        return redirect()
            ->route('surveys.show', $result)
            ->with('success', 'Pengajuan ditolak dan dikembalikan ke Admin untuk diperbaiki.');
    }

    // ------------------------------------------------------------------
    // Pelaksanaan oleh Petugas Survei
    // ------------------------------------------------------------------

    public function start(Survey $survey): RedirectResponse
    {
        $this->authorizeOfficer($survey);

        if (! in_array($survey->status, [Survey::STATUS_WAITING_EXECUTION, 'dijadwalkan'], true)) {
            return back()->with('error', 'Survei ini tidak dalam status menunggu pelaksanaan.');
        }

        $before = $survey->status;
        $survey->update(['status' => Survey::STATUS_IN_PROGRESS, 'started_at' => now()]);

        Activity::log($survey, 'survei_dimulai', 'Petugas memulai pelaksanaan survei di lokasi.', $before, Survey::STATUS_IN_PROGRESS);

        return back()->with('success', 'Status survei: sedang berlangsung. Selamat bertugas.');
    }

    public function finishField(Survey $survey): RedirectResponse
    {
        $this->authorizeOfficer($survey);

        if ($survey->status !== Survey::STATUS_IN_PROGRESS) {
            return back()->with('error', 'Survei belum dimulai atau sudah melewati tahap pemeriksaan lapangan.');
        }

        $survey->update([
            'status' => Survey::STATUS_WAITING_RESULT,
            'field_finished_at' => now(),
            'executed_on' => $survey->executed_on ?? now()->toDateString(),
        ]);

        Activity::log($survey, 'pemeriksaan_selesai', 'Petugas menyelesaikan pemeriksaan lapangan, hasil survei mulai dilengkapi.', Survey::STATUS_IN_PROGRESS, Survey::STATUS_WAITING_RESULT);

        return redirect()
            ->route('surveys.edit', $survey)
            ->with('success', 'Pemeriksaan lapangan ditandai selesai. Silakan isi Form Hasil Survei.');
    }

    private function saveResult(Request $request, Survey $survey): RedirectResponse
    {
        $this->authorizeOfficer($survey);

        if ($survey->status !== Survey::STATUS_WAITING_RESULT) {
            return back()->with('error', 'Form hasil survei hanya dapat diisi pada tahap pengisian hasil.');
        }

        if ($survey->isSigned()) {
            return back()->with('error', 'Hasil survei sudah ditandatangani pelanggan dan terkunci. Hubungi Admin bila perlu koreksi.');
        }

        $validated = $request->validate([
            'executed_on' => ['required', 'date', 'before_or_equal:today'],
            'findings' => ['required', 'string'],
            'location_condition' => ['required', 'string'],
            'result_notes' => ['nullable', 'string'],
            'conclusion' => ['required', 'string'],
            'photos' => ['nullable', 'array', 'max:10'],
            'photos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'photo_caption' => ['nullable', 'string', 'max:255'],
        ], [
            'executed_on.required' => 'Tanggal pelaksanaan survei wajib diisi.',
            'executed_on.before_or_equal' => 'Tanggal pelaksanaan tidak boleh melebihi hari ini.',
            'findings.required' => 'Hasil pemeriksaan lapangan wajib diisi.',
            'location_condition.required' => 'Kondisi lokasi wajib diisi.',
            'conclusion.required' => 'Kesimpulan hasil survei wajib diisi.',
            'photos.*.image' => 'File dokumentasi harus berupa gambar.',
            'photos.*.max' => 'Ukuran setiap foto maksimal 5 MB.',
        ]);

        DB::transaction(function () use ($request, $survey, $validated) {
            $fields = ['executed_on', 'findings', 'location_condition', 'result_notes', 'conclusion'];
            $before = $survey->only($fields);

            $survey->update(collect($validated)->only($fields)->all());

            $uploaded = 0;
            foreach ($request->file('photos', []) as $file) {
                $path = $file->store('survey-photos/' . $survey->id, self::DISK);
                $survey->photos()->create([
                    'uploaded_by' => auth()->id(),
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'caption' => $validated['photo_caption'] ?? null,
                ]);
                $uploaded++;
            }

            Activity::log(
                $survey,
                'hasil_disimpan',
                'Petugas mengisi / memperbarui Form Hasil Survei' . ($uploaded ? " dan mengunggah {$uploaded} foto lokasi." : '.'),
                $survey->status,
                $survey->status,
                $this->diff($before, $survey->fresh()->only($fields))
            );
        });

        return redirect()
            ->route('surveys.edit', $survey)
            ->with('success', 'Hasil survei tersimpan. Lanjutkan dengan tanda tangan pelanggan.');
    }

    public function destroyPhoto(Survey $survey, SurveyPhoto $photo): RedirectResponse
    {
        $this->authorizeOfficer($survey);
        abort_unless($photo->survey_id === $survey->id, 404);

        if ($survey->status !== Survey::STATUS_WAITING_RESULT || $survey->isSigned()) {
            return back()->with('error', 'Foto tidak dapat dihapus karena hasil survei sudah terkunci.');
        }

        Storage::disk(self::DISK)->delete($photo->file_path);
        $name = $photo->file_name;
        $photo->delete();

        Activity::log($survey, 'foto_dihapus', "Petugas menghapus foto lokasi ({$name}).", $survey->status, $survey->status);

        return back()->with('success', 'Foto lokasi dihapus.');
    }

    /** Menyimpan tanda tangan pelanggan dari signature pad (PNG ke storage). */
    public function saveSignature(Request $request, Survey $survey): RedirectResponse
    {
        $this->authorizeOfficer($survey);

        $validated = $request->validate([
            'signature' => ['required', 'string'],
            'signer_name' => ['required', 'string', 'max:255'],
            'signer_identity' => ['nullable', 'string', 'max:100'],
            'signer_agreed' => ['accepted'],
        ], [
            'signature.required' => 'Tanda tangan pelanggan belum dibubuhkan.',
            'signer_name.required' => 'Nama lengkap pelanggan wajib diisi.',
            'signer_agreed.accepted' => 'Pelanggan harus menyetujui pernyataan hasil survei.',
        ]);

        if ($survey->status !== Survey::STATUS_WAITING_RESULT) {
            return back()->with('error', 'Tanda tangan hanya dapat diberikan pada tahap pengisian hasil survei.');
        }

        if ($survey->isSigned()) {
            return back()->with('error', 'Tanda tangan pelanggan sudah tersimpan dan tidak dapat diganti. Koreksi hanya melalui Admin.');
        }

        if (! $survey->findings || ! $survey->location_condition || ! $survey->conclusion || ! $survey->executed_on) {
            return back()->with('error', 'Simpan Form Hasil Survei terlebih dahulu agar pelanggan dapat menyetujui isinya.');
        }

        $binary = $this->decodePng($validated['signature']);
        if ($binary === null) {
            return back()->withErrors(['signature' => 'Format tanda tangan tidak valid atau area tanda tangan masih kosong.'])->withInput();
        }

        $path = 'survey-signatures/survey-' . $survey->id . '-' . now()->format('YmdHis') . '-' . Str::random(6) . '.png';

        DB::transaction(function () use ($survey, $validated, $binary, $path) {
            Storage::disk(self::DISK)->put($path, $binary);

            $survey->update([
                'signature_path' => $path,
                'signer_name' => $validated['signer_name'],
                'signer_identity' => $validated['signer_identity'] ?? null,
                'signer_agreed' => true,
                'signed_at' => now(),
            ]);

            Activity::log(
                $survey,
                'ditandatangani',
                "Pelanggan {$validated['signer_name']} membubuhkan tanda tangan digital sebagai persetujuan hasil survei (dicatat melalui perangkat petugas).",
                $survey->status,
                $survey->status,
                ['signature_path' => [null, $path]]
            );
        });

        return back()->with('success', 'Tanda tangan pelanggan tersimpan. Silakan kirim hasil survei.');
    }

    public function submitResult(Survey $survey): RedirectResponse
    {
        $this->authorizeOfficer($survey);

        $result = DB::transaction(function () use ($survey) {
            $survey = Survey::whereKey($survey->id)->lockForUpdate()->firstOrFail();

            if ($survey->status !== Survey::STATUS_WAITING_RESULT) {
                return 'Survei tidak dalam tahap pengisian hasil.';
            }

            if (! $survey->findings || ! $survey->location_condition || ! $survey->conclusion || ! $survey->executed_on) {
                return 'Form Hasil Survei belum lengkap.';
            }

            if (! $survey->isSigned() || ! Storage::disk(self::DISK)->exists($survey->signature_path)) {
                return 'Survei belum dapat diselesaikan karena tanda tangan pelanggan belum diberikan.';
            }

            $survey->update([
                'status' => Survey::STATUS_DONE,
                'completed_at' => now(),
            ]);

            if ($survey->order && ! in_array($survey->order->status, ['disetujui', 'selesai'], true)) {
                $survey->order->update(['status' => 'survey_selesai']);
            }

            Activity::log($survey, 'selesai', 'Petugas mengirimkan hasil survei beserta tanda tangan pelanggan. Survei selesai.', Survey::STATUS_WAITING_RESULT, Survey::STATUS_DONE);

            return $survey;
        });

        if (is_string($result)) {
            return back()->with('error', $result);
        }

        return redirect()
            ->route('surveys.show', $result)
            ->with('success', 'Hasil survei berhasil dikirim. Dokumen hasil survei sudah dapat diunduh.');
    }

    /**
     * Prosedur koreksi oleh Admin: membuka kembali hasil yang sudah ditandatangani.
     * Tanda tangan lama tidak dihapus dari storage dan tercatat di riwayat.
     */
    public function openCorrection(Request $request, Survey $survey): RedirectResponse
    {
        abort_unless($this->role() === 'admin', 403);

        $validated = $request->validate([
            'correction_reason' => ['required', 'string', 'min:5', 'max:2000'],
        ], [
            'correction_reason.required' => 'Alasan koreksi wajib diisi.',
        ]);

        if (! in_array($survey->status, [Survey::STATUS_WAITING_RESULT, Survey::STATUS_DONE], true) || ! $survey->isSigned()) {
            return back()->with('error', 'Koreksi hanya untuk hasil survei yang sudah ditandatangani pelanggan.');
        }

        DB::transaction(function () use ($survey, $validated) {
            $before = $survey->status;
            $old = $survey->only(['signature_path', 'signer_name', 'signed_at']);

            $survey->update([
                'status' => Survey::STATUS_WAITING_RESULT,
                'completed_at' => null,
                'signature_path' => null,
                'signer_name' => null,
                'signer_identity' => null,
                'signer_agreed' => false,
                'signed_at' => null,
            ]);

            if ($survey->order?->status === 'survey_selesai' && ! $survey->order->project) {
                $survey->order->update(['status' => 'menunggu_survey']);
            }

            Activity::log(
                $survey,
                'koreksi_dibuka',
                'Admin membuka koreksi hasil survei. Alasan: ' . $validated['correction_reason'] . '. Tanda tangan lama diarsipkan, pelanggan perlu menandatangani ulang.',
                $before,
                Survey::STATUS_WAITING_RESULT,
                [
                    'tanda_tangan_lama' => $old['signature_path'],
                    'penandatangan_lama' => $old['signer_name'],
                    'waktu_tanda_tangan_lama' => optional($old['signed_at'])->format('Y-m-d H:i:s'),
                ]
            );
        });

        return back()->with('success', 'Koreksi dibuka. Petugas dapat memperbaiki hasil dan meminta tanda tangan ulang pelanggan.');
    }

    // ------------------------------------------------------------------
    // File privat & dokumen PDF
    // ------------------------------------------------------------------

    public function signature(Survey $survey): Response
    {
        $this->authorizeView($survey);
        abort_unless($survey->signature_path && Storage::disk(self::DISK)->exists($survey->signature_path), 404);

        return Storage::disk(self::DISK)->response($survey->signature_path, null, [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'private, no-store',
        ]);
    }

    public function photo(Survey $survey, SurveyPhoto $photo): Response
    {
        $this->authorizeView($survey);
        abort_unless($photo->survey_id === $survey->id && Storage::disk(self::DISK)->exists($photo->file_path), 404);

        return Storage::disk(self::DISK)->response($photo->file_path, null, ['Cache-Control' => 'private, max-age=600']);
    }

    /** Dokumen pengajuan + keputusan Direktur. */
    public function requestPdf(Request $request, Survey $survey): Response
    {
        abort_unless(in_array($this->role(), ['admin', 'direktur'], true), 403);
        $survey->load(['order.client', 'assignedUser', 'approver', 'rejecter', 'requester']);

        return $this->pdf($request, 'surveys.pdf.request', compact('survey'), 'Pengajuan-Survei-' . $survey->id);
    }

    public function spsPdf(Request $request, Survey $survey): Response
    {
        $this->authorizeView($survey);
        abort_if($this->role() === 'client', 403);
        abort_unless($survey->approved_at && $survey->sps_number, 404, 'Surat Perintah Survei belum diterbitkan.');

        $survey->load(['order.client', 'assignedUser', 'approver', 'requester']);

        return $this->pdf($request, 'surveys.pdf.sps', compact('survey'), 'SPS-' . $survey->sps_number);
    }

    public function resultPdf(Request $request, Survey $survey): Response
    {
        $this->authorizeView($survey);
        abort_if($this->role() === 'client', 403);
        abort_unless($survey->status === Survey::STATUS_DONE, 404, 'Dokumen hasil survei tersedia setelah survei selesai.');

        $survey->load(['order.client', 'assignedUser', 'approver', 'photos']);

        $signature = $survey->signature_path && Storage::disk(self::DISK)->exists($survey->signature_path)
            ? 'data:image/png;base64,' . base64_encode(Storage::disk(self::DISK)->get($survey->signature_path))
            : null;

        $photos = $survey->photos->take(6)->map(function ($photo) {
            if (! Storage::disk(self::DISK)->exists($photo->file_path)) {
                return null;
            }
            $mime = Storage::disk(self::DISK)->mimeType($photo->file_path) ?: 'image/jpeg';

            return [
                'src' => 'data:' . $mime . ';base64,' . base64_encode(Storage::disk(self::DISK)->get($photo->file_path)),
                'caption' => $photo->caption ?: $photo->file_name,
            ];
        })->filter()->values();

        return $this->pdf($request, 'surveys.pdf.result', compact('survey', 'signature', 'photos'), 'Hasil-Survei-' . ($survey->sps_number ?: $survey->id));
    }

    public function destroy(Survey $survey): RedirectResponse
    {
        abort_unless($this->role() === 'admin', 403);

        $files = $survey->photos()->pluck('file_path')->all();
        if ($survey->signature_path) {
            $files[] = $survey->signature_path;
        }

        $survey->delete();
        Storage::disk(self::DISK)->delete($files);

        return redirect()
            ->route('surveys.index')
            ->with('success', 'Data survei berhasil dihapus.');
    }

    // ------------------------------------------------------------------
    // Helper
    // ------------------------------------------------------------------

    private function role(): ?string
    {
        return auth()->user()?->role?->slug;
    }

    private function fieldOfficers()
    {
        return User::whereHas('role', fn ($q) => $q->where('slug', 'petugas-lapangan'))
            ->orderBy('name')
            ->get();
    }

    private function authorizeView(Survey $survey): void
    {
        $user = auth()->user();
        $role = $this->role();

        abort_unless(in_array($role, ['admin', 'direktur', 'petugas-lapangan', 'client'], true), 403);

        if ($role === 'direktur') {
            abort_if(in_array($survey->status, [Survey::STATUS_DRAFT, 'diajukan_client'], true), 403, 'Pengajuan ini masih berupa draf Admin.');
        }

        if ($role === 'client') {
            abort_unless($survey->order?->client_id === $user->id, 403);
            abort_if(in_array($survey->status, [Survey::STATUS_DRAFT, 'diajukan_client'], true), 403);
        }

        if ($role === 'petugas-lapangan') {
            $this->authorizeOfficer($survey);
        }
    }

    /** Petugas hanya bisa mengakses tugas miliknya yang sudah disetujui Direktur. */
    private function authorizeOfficer(Survey $survey): void
    {
        $user = auth()->user();

        abort_unless(
            $this->role() === 'petugas-lapangan'
                && $survey->assigned_to === $user->id
                && $survey->approved_at !== null
                && in_array($survey->status, array_merge(Survey::OFFICER_STATUSES, ['dijadwalkan']), true),
            403,
            'Tugas survei ini belum disetujui Direktur atau bukan tugas Anda.'
        );
    }

    private function sendToDirector(Survey $survey): void
    {
        $before = $survey->status;

        $survey->update([
            'status' => Survey::STATUS_WAITING_DIRECTOR,
            'submitted_to_direktur_at' => now(),
        ]);

        Activity::log(
            $survey,
            'dikirim_direktur',
            $before === Survey::STATUS_REJECTED
                ? 'Admin mengirim ulang pengajuan hasil perbaikan ke Direktur.'
                : 'Admin mengirim pengajuan survei ke Direktur.',
            $before,
            Survey::STATUS_WAITING_DIRECTOR
        );
    }

    private function requestRules(bool $submitNow): array
    {
        $required = $submitNow ? 'required' : 'nullable';

        return [
            'order_id' => ['required', Rule::exists('orders', 'id')],
            'customer_name' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'survey_type' => ['required', 'string', 'max:100'],
            'purpose' => ['required', 'string', 'max:2000'],
            'assigned_to' => [$required, Rule::exists('users', 'id')->where(function ($q) {
                $q->whereIn('role_id', fn ($sub) => $sub->select('id')->from('roles')->where('slug', 'petugas-lapangan'));
            })],
            'scheduled_at' => [$required, 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    private function requestMessages(): array
    {
        return [
            'order_id.required' => 'Pilih data pelanggan / pesanan yang akan disurvei.',
            'customer_name.required' => 'Nama pelanggan wajib diisi.',
            'location.required' => 'Alamat lokasi survei wajib diisi.',
            'survey_type.required' => 'Jenis kebutuhan survei wajib dipilih.',
            'purpose.required' => 'Tujuan survei wajib diisi.',
            'assigned_to.required' => 'Pilih Petugas Survei sebelum mengirim ke Direktur.',
            'assigned_to.exists' => 'Petugas yang dipilih bukan Petugas Lapangan.',
            'scheduled_at.required' => 'Tentukan jadwal survei sebelum mengirim ke Direktur.',
        ];
    }

    private function generateSpsNumber(Survey $survey): string
    {
        $romans = [1 => 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
        $now = now();
        $number = sprintf('SPS/%04d/SIMOP/%s/%s', $survey->id, $romans[(int) $now->format('n')], $now->format('Y'));

        // Pengaman tambahan bila nomor sudah pernah dipakai
        $suffix = 1;
        $candidate = $number;
        while (Survey::where('sps_number', $candidate)->where('id', '!=', $survey->id)->exists()) {
            $candidate = $number . '-' . $suffix++;
        }

        return $candidate;
    }

    private function decodePng(string $dataUrl): ?string
    {
        if (! str_starts_with($dataUrl, 'data:image/png;base64,')) {
            return null;
        }

        $binary = base64_decode(substr($dataUrl, strlen('data:image/png;base64,')), true);

        if ($binary === false || strlen($binary) < 500 || strlen($binary) > 2 * 1024 * 1024) {
            return null;
        }

        // Pastikan benar-benar file PNG
        if (substr($binary, 0, 8) !== "\x89PNG\r\n\x1a\n") {
            return null;
        }

        return $binary;
    }

    private function diff(array $before, array $after): array
    {
        $changes = [];
        foreach ($after as $key => $value) {
            $old = $before[$key] ?? null;
            $oldStr = $old instanceof \DateTimeInterface ? $old->format('Y-m-d H:i') : (string) $old;
            $newStr = $value instanceof \DateTimeInterface ? $value->format('Y-m-d H:i') : (string) $value;
            if ($oldStr !== $newStr) {
                $changes[$key] = [$oldStr, $newStr];
            }
        }

        return $changes;
    }

    private function pdf(Request $request, string $view, array $data, string $filename): Response
    {
        $pdf = Pdf::loadView($view, $data)->setPaper('a4', 'portrait');
        $filename = Str::slug(str_replace('/', '-', $filename)) . '.pdf';

        return $request->boolean('print') ? $pdf->stream($filename) : $pdf->download($filename);
    }
}
