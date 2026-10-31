<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\View\View;

class TrackingController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $role = $user?->role?->slug;

        abort_unless(in_array($role, ['client', 'admin', 'direktur', 'petugas-lapangan'], true), 403);

        $orders = Order::with([
            'client',
            'survey.assignedUser',
            'survey.approver',
            'project.assignedUser',
            'project.planning.production.assignedUser',
            'project.projectItems.item',
            'project.fieldDocumentations.uploader',
            'project.rating',
        ])
            ->when($role === 'client', function ($query) use ($user) {
                $query->where('client_id', $user->id);
            })
            ->latest()
            ->paginate(10);

        $orders->getCollection()->transform(function (Order $order) {
            $order->setAttribute('tracking_summary', $this->getTrackingData($order));
            return $order;
        });

        return view('tracking.index', compact('orders', 'role'));
    }

    public function show(Order $order): View
    {
        $user = auth()->user();
        $role = $user?->role?->slug;

        abort_unless(in_array($role, ['client', 'admin', 'direktur', 'petugas-lapangan'], true), 403);

        if ($role === 'client' && $order->client_id !== $user->id) {
            abort(403);
        }

        $order->load([
            'client',
            'survey.assignedUser',
            'survey.approver',
            'survey.requester',
            'project.assignedUser',
            'project.planning.production.assignedUser',
            'project.projectItems.item',
            'project.fieldDocumentations.uploader',
            'project.documents.uploader',
            'project.rating.client',
        ]);

        $tracking = $this->getTrackingData($order);

        return view('tracking.show', compact('order', 'tracking', 'role'));
    }

    public function getTrackingData(Order $order): array
    {
        $survey = $order->survey;
        $project = $order->project;
        $planning = $project?->planning;
        $production = $planning?->production;
        $rating = $project?->rating;
        $projectItems = $project?->projectItems ?? collect();
        $fieldDocs = $project?->fieldDocumentations ?? collect();

        $step1Done = true;
        $step2Done = $survey && $survey->approved_at !== null;
        $step2Current = $survey && ! $step2Done;

        $step3Done = $survey && $survey->status === 'selesai';
        $step3Current = $step2Done && ! $step3Done;

        $step4Done = $project !== null;
        $step4Current = $step3Done && ! $step4Done;

        $step5Done = ($production && $production->status === 'completed') || ($project && $project->status === 'completed');
        $step5Current = $step4Done && ! $step5Done;

        $step6Done = $project && $project->status === 'completed';
        $step6Current = false;

        $steps = [
            [
                'number' => 1,
                'title' => 'Pengajuan Survey',
                'subtitle' => 'Client ke Admin',
                'state' => $step1Done ? 'done' : 'pending',
                'time' => $order->created_at?->format('d/m/Y H:i'),
            ],
            [
                'number' => 2,
                'title' => 'Surat Perintah Survey',
                'subtitle' => 'Approval Direktur',
                'state' => $step2Done ? 'done' : ($step2Current ? 'active' : 'pending'),
                'time' => $survey?->approved_at?->format('d/m/Y H:i') ?? ($survey?->submitted_to_direktur_at?->format('d/m/Y H:i')),
            ],
            [
                'number' => 3,
                'title' => 'Hasil Survey',
                'subtitle' => 'Petugas Lapangan',
                'state' => $step3Done ? 'done' : ($step3Current ? 'active' : 'pending'),
                'time' => $step3Done ? $survey?->updated_at?->format('d/m/Y H:i') : ($survey?->scheduled_at?->format('d/m/Y H:i')),
            ],
            [
                'number' => 4,
                'title' => 'Persiapan & Barang',
                'subtitle' => 'Project, Planning & Stok',
                'state' => $step4Done ? 'done' : ($step4Current ? 'active' : 'pending'),
                'time' => $project?->created_at?->format('d/m/Y H:i'),
            ],
            [
                'number' => 5,
                'title' => 'Pengerjaan Lapangan',
                'subtitle' => 'Produksi & Dokumentasi',
                'state' => $step5Done ? 'done' : ($step5Current ? 'active' : 'pending'),
                'time' => $production?->updated_at?->format('d/m/Y H:i'),
            ],
            [
                'number' => 6,
                'title' => 'Selesai',
                'subtitle' => 'Stok Kembali',
                'state' => $step6Done ? 'done' : ($step6Current ? 'active' : 'pending'),
                'time' => $step6Done ? $project?->updated_at?->format('d/m/Y H:i') : null,
            ],
        ];

        $logs = [];

        $logs[] = [
            'state' => 'done',
            'badge' => 'Order & Survey',
            'title' => 'Pengajuan Order dan Permintaan Survey Dibuat',
            'description' => "Order {$order->order_number} untuk kegiatan {$order->event_name} ({$order->event_location}) telah diajukan oleh {$order->company_name} ke Admin.",
            'actor' => $order->client?->name ?? $order->contact_person,
            'timestamp' => $order->created_at?->format('d M Y, H:i'),
        ];

        if ($survey && ($survey->submitted_to_direktur_at || in_array($survey->status, ['menunggu_persetujuan_direktur', 'dijadwalkan', 'berlangsung', 'selesai', 'ditolak_direktur'], true))) {
            $logs[] = [
                'state' => 'done',
                'badge' => 'Verifikasi Admin',
                'title' => 'Admin Meneruskan Pengajuan Survey ke Direktur',
                'description' => 'Admin telah menetapkan jadwal serta petugas lapangan (' . ($survey->assignedUser?->name ?? '-') . ') dan mengirim pengajuan ke Direktur.',
                'actor' => 'Admin',
                'timestamp' => ($survey->submitted_to_direktur_at ?? $survey->updated_at)?->format('d M Y, H:i'),
            ];
        } else {
            $logs[] = [
                'state' => 'active',
                'badge' => 'Proses Admin',
                'title' => 'Menunggu Admin Memproses Pengajuan Survey',
                'description' => 'Pengajuan survey sedang diverifikasi oleh Admin sebelum diteruskan ke Direktur.',
                'actor' => 'Admin',
                'timestamp' => 'Sedang berjalan',
            ];
        }

        if ($survey && $survey->approved_at) {
            $logs[] = [
                'state' => 'done',
                'badge' => 'SPS Diterbitkan',
                'title' => "Disetujui Direktur - Surat Perintah Survey ({$survey->sps_number}) Dikirim ke Petugas Lapangan",
                'description' => "Direktur menyetujui jadwal survey. Surat Perintah Survey {$survey->sps_number} diteruskan ke petugas lapangan ({$survey->assignedUser?->name}).",
                'actor' => $survey->approver?->name ?? 'Direktur',
                'timestamp' => $survey->approved_at->format('d M Y, H:i'),
            ];
        } elseif ($survey && $survey->status === 'ditolak_direktur') {
            $logs[] = [
                'state' => 'rejected',
                'badge' => 'Ditolak Direktur',
                'title' => 'Pengajuan Survey Dikembalikan oleh Direktur',
                'description' => 'Catatan: ' . ($survey->rejection_notes ?? '-'),
                'actor' => 'Direktur',
                'timestamp' => $survey->updated_at?->format('d M Y, H:i'),
            ];
        } elseif ($survey && $survey->status === 'menunggu_persetujuan_direktur') {
            $logs[] = [
                'state' => 'active',
                'badge' => 'Menunggu Direktur',
                'title' => 'Menunggu Approval Direktur untuk Surat Perintah Survey',
                'description' => 'Menunggu persetujuan Direktur sebelum Surat Perintah Survey dikirim ke petugas lapangan.',
                'actor' => 'Direktur',
                'timestamp' => 'Menunggu persetujuan',
            ];
        } else {
            $logs[] = [
                'state' => 'pending',
                'badge' => 'Belum Berjalan',
                'title' => 'Persetujuan Direktur & Penerbitan Surat Perintah Survey',
                'description' => 'Surat Perintah Survey akan dikirim ke petugas lapangan setelah disetujui Direktur.',
                'actor' => 'Direktur',
                'timestamp' => '-',
            ];
        }

        if ($survey && $survey->status === 'selesai') {
            $logs[] = [
                'state' => 'done',
                'badge' => 'Survey Selesai',
                'title' => 'Petugas Lapangan Menyelesaikan Survey Lokasi',
                'description' => 'Hasil temuan: ' . ($survey->findings ?: 'Data hasil survey telah diinput.'),
                'actor' => $survey->assignedUser?->name ?? 'Petugas Lapangan',
                'timestamp' => $survey->updated_at?->format('d M Y, H:i'),
            ];
        } elseif ($survey && $survey->approved_at && in_array($survey->status, ['dijadwalkan', 'berlangsung'], true)) {
            $logs[] = [
                'state' => 'active',
                'badge' => $survey->status === 'berlangsung' ? 'Sedang Survey' : 'Terjadwal',
                'title' => $survey->status === 'berlangsung'
                    ? 'Petugas Lapangan Sedang Melakukan Survey'
                    : 'Surat Perintah Survey Diterima Petugas Lapangan',
                'description' => "Petugas lapangan ({$survey->assignedUser?->name}) melaksanakan survey di {$survey->location}.",
                'actor' => $survey->assignedUser?->name ?? 'Petugas Lapangan',
                'timestamp' => $survey->scheduled_at?->format('d M Y, H:i') ?? '-',
            ];
        } else {
            $logs[] = [
                'state' => 'pending',
                'badge' => 'Belum Berjalan',
                'title' => 'Pelaksanaan Survey Lapangan',
                'description' => 'Petugas lapangan melakukan pengecekan lokasi berdasarkan Surat Perintah Survey.',
                'actor' => 'Petugas Lapangan',
                'timestamp' => '-',
            ];
        }

        if ($project) {
            $logs[] = [
                'state' => 'done',
                'badge' => 'Project Aktif',
                'title' => "Project ({$project->project_number}) Dibuat",
                'description' => "Project {$project->title} telah dibuat. Koordinator lapangan: " . ($project->assignedUser?->name ?? '-'),
                'actor' => 'Admin',
                'timestamp' => $project->created_at?->format('d M Y, H:i'),
            ];

            if ($projectItems->isNotEmpty()) {
                $summaryItems = $projectItems->map(function ($pi) {
                    return "{$pi->item?->name} ({$pi->quantity} {$pi->item?->unit})";
                })->implode(', ');

                $logs[] = [
                    'state' => 'done',
                    'badge' => 'Stok Barang',
                    'title' => 'Pengambilan Stok Barang untuk Proyek',
                    'description' => "Barang yang dibawa ke lokasi proyek: {$summaryItems}.",
                    'actor' => 'Admin',
                    'timestamp' => $projectItems->first()?->allocated_at?->format('d M Y, H:i') ?? $project->created_at?->format('d M Y, H:i'),
                ];
            }

            if ($planning) {
                $logs[] = [
                    'state' => in_array($planning->status, ['approved', 'in_progress', 'completed'], true) ? 'done' : 'active',
                    'badge' => 'Planning',
                    'title' => "Planning ({$planning->planning_number}) - {$planning->title}",
                    'description' => $planning->work_scope ?? $planning->description,
                    'actor' => 'Admin',
                    'timestamp' => $planning->created_at?->format('d M Y, H:i'),
                ];
            }
        } else {
            $logs[] = [
                'state' => 'pending',
                'badge' => 'Belum Berjalan',
                'title' => 'Pembuatan Project, Planning & Alokasi Stok Barang',
                'description' => 'Penjadwalan proyek dan alokasi barang dilakukan setelah survey lapangan selesai.',
                'actor' => 'Admin',
                'timestamp' => '-',
            ];
        }

        if ($production) {
            $logs[] = [
                'state' => $production->status === 'completed' ? 'done' : 'active',
                'badge' => "Produksi {$production->progress}%",
                'title' => $production->status === 'completed'
                    ? "Pengerjaan Produksi ({$production->production_number}) Selesai"
                    : "Pengerjaan Produksi Sedang Berjalan ({$production->progress}%)",
                'description' => $production->description,
                'actor' => $production->assignedUser?->name ?? 'Petugas Lapangan',
                'timestamp' => $production->updated_at?->format('d M Y, H:i'),
            ];
        } else {
            $logs[] = [
                'state' => 'pending',
                'badge' => 'Belum Berjalan',
                'title' => 'Pelaksanaan Produksi & Instalasi di Lapangan',
                'description' => 'Pengerjaan lapangan dilaksanakan sesuai perencanaan proyek.',
                'actor' => 'Petugas Lapangan',
                'timestamp' => '-',
            ];
        }

        if ($fieldDocs->isNotEmpty()) {
            foreach ($fieldDocs as $doc) {
                $logs[] = [
                    'state' => 'done',
                    'badge' => 'Dokumentasi Lapangan',
                    'title' => "Input Dokumentasi Lapangan: {$doc->title}",
                    'description' => $doc->description . ($doc->progress_percentage !== null ? " (Progress: {$doc->progress_percentage}%)" : ''),
                    'actor' => $doc->uploader?->name ?? 'Petugas Lapangan',
                    'timestamp' => $doc->created_at?->format('d M Y, H:i'),
                ];
            }
        }

        if ($project && $project->status === 'completed') {
            $returnedItemsText = '';
            if ($projectItems->isNotEmpty()) {
                $returnedList = $projectItems->map(function ($pi) {
                    return "{$pi->quantity} {$pi->item?->unit} {$pi->item?->name}";
                })->implode(', ');
                $returnedItemsText = " Barang proyek ({$returnedList}) telah dikembalikan ke stok awal.";
            }

            $logs[] = [
                'state' => 'done',
                'badge' => 'Selesai',
                'title' => 'Pekerjaan Selesai & Barang Kembali ke Stok Awal',
                'description' => "Pekerjaan untuk event {$order->event_name} telah selesai." . $returnedItemsText,
                'actor' => 'Admin',
                'timestamp' => $project->updated_at?->format('d M Y, H:i'),
            ];

            if ($rating) {
                $logs[] = [
                    'state' => 'done',
                    'badge' => "Rating {$rating->rating}/5",
                    'title' => 'Ulasan Pekerjaan dari Client',
                    'description' => $rating->review,
                    'actor' => $rating->client?->name ?? 'Client',
                    'timestamp' => $rating->created_at?->format('d M Y, H:i'),
                ];
            }
        } else {
            $logs[] = [
                'state' => 'pending',
                'badge' => 'Tahap Akhir',
                'title' => 'Penyelesaian Pekerjaan & Pengembalian Stok Barang',
                'description' => 'Pekerjaan selesai dan barang yang dipakai proyek dikembalikan ke stok awal.',
                'actor' => 'Admin',
                'timestamp' => '-',
            ];
        }

        $doneStepsCount = collect($steps)->where('state', 'done')->count();
        $overallProgress = (int) round(($doneStepsCount / count($steps)) * 100);
        if ($project && $project->status === 'completed') {
            $overallProgress = 100;
        }

        $currentHeadline = 'Pengajuan Survey Diterima Admin';
        if ($project && $project->status === 'completed') {
            $currentHeadline = 'Pekerjaan Selesai & Stok Barang Telah Dikembalikan';
        } elseif ($production) {
            $currentHeadline = "Pengerjaan Produksi Lapangan ({$production->progress}%)";
        } elseif ($planning) {
            $currentHeadline = 'Perencanaan Teknis & Persiapan Barang';
        } elseif ($project) {
            $currentHeadline = 'Project Aktif';
        } elseif ($survey) {
            if ($survey->status === 'diajukan_client') {
                $currentHeadline = 'Menunggu Admin Meneruskan Survey ke Direktur';
            } elseif ($survey->status === 'menunggu_persetujuan_direktur') {
                $currentHeadline = 'Menunggu Approval Direktur (Surat Perintah Survey)';
            } elseif ($survey->status === 'ditolak_direktur') {
                $currentHeadline = 'Pengajuan Survey Perlu Revisi';
            } elseif ($survey->status === 'dijadwalkan') {
                $currentHeadline = "Surat Perintah Survey ({$survey->sps_number}) Dikirim ke Petugas";
            } elseif ($survey->status === 'berlangsung') {
                $currentHeadline = 'Survey Lapangan Sedang Berlangsung';
            } elseif ($survey->status === 'selesai') {
                $currentHeadline = 'Survey Selesai';
            }
        }

        return [
            'steps' => $steps,
            'logs' => array_reverse($logs),
            'chronological_logs' => $logs,
            'overall_progress' => $overallProgress,
            'current_headline' => $currentHeadline,
        ];
    }
}
