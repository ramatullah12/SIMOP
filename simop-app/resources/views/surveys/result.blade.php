<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Form Hasil Survei Lapangan & Tanda Tangan Pelanggan
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    {{ $survey->sps_number }} — {{ $survey->customer_display_name }}
                </p>
            </div>
            <a href="{{ route('surveys.show', $survey) }}" class="text-sm text-gray-600 hover:text-gray-900">
                &larr; Kembali ke Rincian Survei
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="rounded-lg bg-green-100 border border-green-300 text-green-800 px-4 py-3 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="rounded-lg bg-red-100 border border-red-300 text-red-800 px-4 py-3 text-sm">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-lg bg-red-100 border border-red-300 text-red-800 px-4 py-3 text-sm">
                    <p class="font-bold mb-1">Periksa kembali data Anda:</p>
                    <ul class="list-disc pl-5 space-y-0.5">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Ringkasan SPS -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 text-xs">
                    <div>
                        <span class="text-gray-500">Nomor SPS</span>
                        <p class="font-bold text-blue-800 text-sm mt-0.5">{{ $survey->sps_number }}</p>
                    </div>
                    <div>
                        <span class="text-gray-500">Nama Pelanggan</span>
                        <p class="font-semibold text-gray-900 text-sm mt-0.5">{{ $survey->customer_display_name }}</p>
                    </div>
                    <div>
                        <span class="text-gray-500">Lokasi Survei</span>
                        <p class="font-semibold text-gray-900 mt-0.5">{{ $survey->location }}</p>
                    </div>
                    <div>
                        <span class="text-gray-500">Petugas Survei</span>
                        <p class="font-semibold text-gray-900 mt-0.5">{{ $survey->assignedUser?->name ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- BAGIAN 1: FORM PENGISIAN HASIL SURVEI -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200 p-6 space-y-6">
                <div class="border-b pb-3 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-gray-900 text-base">Bagian 1: Hasil Pemeriksaan Lapangan</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Petugas mengisi temuan fisik, kondisi lapangan, dokumentasi foto, dan kesimpulan.</p>
                    </div>
                    @if ($survey->isSigned())
                        <span class="px-2.5 py-1 rounded bg-green-100 text-green-800 text-xs font-semibold">
                            Terkunci (Sudah Ditandatangani)
                        </span>
                    @endif
                </div>

                <form method="POST" action="{{ route('surveys.update', $survey) }}" enctype="multipart/form-data" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Tanggal Pelaksanaan Survei <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="executed_on" required
                                   value="{{ old('executed_on', $survey->executed_on?->format('Y-m-d') ?? now()->toDateString()) }}"
                                   @disabled($survey->isSigned())
                                   class="w-full rounded-md border-gray-300 text-sm disabled:bg-gray-100">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Nama Petugas Lapangan
                            </label>
                            <input type="text" value="{{ $survey->assignedUser?->name ?? auth()->user()->name }}" disabled
                                   class="w-full rounded-md border-gray-200 bg-gray-100 text-sm text-gray-600">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Hasil Pemeriksaan Lapangan <span class="text-red-500">*</span>
                        </label>
                        <textarea name="findings" rows="4" required
                                  @disabled($survey->isSigned())
                                  placeholder="Tuliskan detail pemeriksaan lapangan (ukuran area/ruangan, kapasitas panggung/booth, jalur evakuasi, titik instalasi, dll.)"
                                  class="w-full rounded-md border-gray-300 text-sm disabled:bg-gray-100">{{ old('findings', $survey->findings) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Kondisi Lokasi <span class="text-red-500">*</span>
                        </label>
                        <textarea name="location_condition" rows="3" required
                                  @disabled($survey->isSigned())
                                  placeholder="Kondisi lantai, cuaca/indoor-outdoor, akses loading barang, jarak parkir ke venue, ketersediaan daya listrik dan titik stop kontak..."
                                  class="w-full rounded-md border-gray-300 text-sm disabled:bg-gray-100">{{ old('location_condition', $survey->location_condition) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Keterangan atau Catatan Hasil Survei
                        </label>
                        <textarea name="result_notes" rows="2"
                                  @disabled($survey->isSigned())
                                  placeholder="Catatan kendala teknis, perizinan venue, atau permintaan khusus dari pelanggan..."
                                  class="w-full rounded-md border-gray-300 text-sm disabled:bg-gray-100">{{ old('result_notes', $survey->result_notes) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Kesimpulan Hasil Survei <span class="text-red-500">*</span>
                        </label>
                        <textarea name="conclusion" rows="3" required
                                  @disabled($survey->isSigned())
                                  placeholder="Kesimpulan rekomendasi teknis (misal: lokasi siap digunakan, butuh genset tambahan, butuh penyesuaian ukuran booth, dll.)"
                                  class="w-full rounded-md border-gray-300 text-sm disabled:bg-gray-100">{{ old('conclusion', $survey->conclusion) }}</textarea>
                    </div>

                    <!-- Dokumentasi Foto Lokasi -->
                    <div class="border-t pt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Dokumentasi Foto Lokasi (Opsional, Maks. 10 Foto @ 5 MB)
                        </label>
                        @if (! $survey->isSigned())
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-center">
                                <div class="sm:col-span-2">
                                    <input type="file" name="photos[]" multiple accept="image/*"
                                           class="w-full text-xs text-gray-600 file:mr-3 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                </div>
                                <div>
                                    <input type="text" name="photo_caption" placeholder="Keterangan foto (opsional)..."
                                           class="w-full rounded-md border-gray-300 text-xs">
                                </div>
                            </div>
                        @endif

                        @if ($survey->photos->isNotEmpty())
                            <div class="mt-4 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                                @foreach ($survey->photos as $photo)
                                    <div class="relative group border rounded-lg overflow-hidden bg-gray-50">
                                        <img src="{{ route('surveys.photos.show', [$survey, $photo]) }}"
                                             alt="{{ $photo->caption ?: 'Foto Lokasi' }}"
                                             class="w-full h-28 object-cover">
                                        <div class="p-1.5 text-[11px] text-gray-600 truncate">
                                            {{ $photo->caption ?: $photo->file_name }}
                                        </div>
                                        @if (! $survey->isSigned())
                                            <button type="submit" form="delPhoto_{{ $photo->id }}"
                                                    onclick="return confirm('Hapus foto ini?')"
                                                    class="absolute top-1 right-1 px-1.5 py-0.5 rounded bg-red-600 text-white text-[10px] font-bold opacity-90 hover:opacity-100 shadow">
                                                ×
                                            </button>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    @if (! $survey->isSigned())
                        <div class="pt-3 flex justify-end">
                            <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white rounded-md text-sm font-semibold hover:bg-blue-700 shadow-sm">
                                Simpan Hasil Pemeriksaan Lapangan
                            </button>
                        </div>
                    @endif
                </form>

                @if (! $survey->isSigned())
                    @foreach ($survey->photos as $photo)
                        <form id="delPhoto_{{ $photo->id }}" method="POST" action="{{ route('surveys.photos.destroy', [$survey, $photo]) }}" class="hidden">
                            @csrf
                            @method('DELETE')
                        </form>
                    @endforeach
                @endif
            </div>

            <!-- BAGIAN 2: TANDA TANGAN DIGITAL PELANGGAN -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg border-2 border-indigo-200 p-6 space-y-6">
                <div class="border-b pb-3">
                    <span class="inline-flex px-2.5 py-0.5 rounded bg-indigo-100 text-indigo-800 text-xs font-bold uppercase tracking-wider">
                        Bukti Pelaksanaan
                    </span>
                    <h3 class="font-bold text-gray-900 text-lg mt-1">Bagian 2: Tanda Tangan Digital Pelanggan</h3>
                    <p class="text-xs text-gray-600 mt-1">
                        Pelanggan membubuhkan tanda tangan langsung di layar perangkat (HP, tablet, mouse, atau touchpad) sebagai bukti telah memeriksa dan menyetujui hasil survei lapangan.
                    </p>
                </div>

                @if ($survey->isSigned())
                    <!-- Tanda Tangan Sudah Tersimpan -->
                    <div class="rounded-lg bg-green-50 border border-green-200 p-5 space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2 text-green-800">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span class="font-bold text-sm">Tanda Tangan Digital Pelanggan Berhasil Disimpan</span>
                            </div>
                            <span class="text-xs text-gray-500">
                                {{ $survey->signed_at?->format('d M Y, H:i:s') }} WIB
                            </span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs pt-2 border-t border-green-200">
                            <div>
                                <span class="text-gray-500">Nama Penandatangan:</span>
                                <p class="font-bold text-gray-900 text-sm mt-0.5">{{ $survey->signer_name }}</p>
                                @if ($survey->signer_identity)
                                    <span class="text-gray-500 mt-1 block">Identitas / No. Pelanggan:</span>
                                    <p class="font-medium text-gray-800">{{ $survey->signer_identity }}</p>
                                @endif
                                <p class="text-emerald-700 font-medium mt-2">
                                    ✓ Pelanggan telah menyetujui pernyataan hasil survei lapangan.
                                </p>
                            </div>
                            <div class="flex flex-col items-center justify-center p-3 rounded bg-white border border-gray-200">
                                <span class="text-[11px] text-gray-400 mb-1">Pratinjau Tanda Tangan</span>
                                <img src="{{ route('surveys.signature.image', $survey) }}"
                                     alt="Tanda Tangan Pelanggan"
                                     class="max-h-24 max-w-full object-contain">
                            </div>
                        </div>

                        @if ($survey->status === \App\Models\Survey::STATUS_WAITING_RESULT)
                            <!-- Tombol Kirim & Selesaikan Survei -->
                            <div class="pt-3 border-t border-green-200 flex flex-col sm:flex-row items-center justify-between gap-3">
                                <p class="text-xs text-gray-600">
                                    Semua data hasil survei dan tanda tangan pelanggan telah lengkap.
                                </p>
                                <form method="POST" action="{{ route('surveys.submit-result', $survey) }}">
                                    @csrf
                                    <button type="submit"
                                            onclick="return confirm('Kirim dan selesaikan survei ini? Dokumen hasil resmi akan diterbitkan.')"
                                            class="px-6 py-2.5 bg-emerald-600 text-white rounded-md text-sm font-bold hover:bg-emerald-700 shadow-md">
                                        Kirim & Selesaikan Survei
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                @else
                    <!-- Form Signature Pad -->
                    @if (! $survey->findings || ! $survey->location_condition || ! $survey->conclusion)
                        <div class="rounded-lg bg-amber-50 border border-amber-300 p-4 text-xs text-amber-800">
                            <strong>Perhatian:</strong> Silakan lengkapi dan klik tombol <strong>"Simpan Hasil Pemeriksaan Lapangan"</strong> di Bagian 1 terlebih dahulu sebelum meminta tanda tangan pelanggan.
                        </div>
                    @endif

                    <form method="POST" action="{{ route('surveys.signature.save', $survey) }}" id="signatureForm" class="space-y-4">
                        @csrf
                        <input type="hidden" name="signature" id="signatureInput">

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Nama Lengkap Pelanggan / Perwakilan <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="signer_name" required
                                       value="{{ old('signer_name', $survey->order?->contact_person ?: $survey->customer_display_name) }}"
                                       placeholder="Nama pelanggan yang menandatangani"
                                       class="w-full rounded-md border-gray-300 text-sm">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Identitas / No. Telepon Pelanggan (Opsional)
                                </label>
                                <input type="text" name="signer_identity"
                                       value="{{ old('signer_identity', $survey->order?->phone) }}"
                                       placeholder="No. KTP / Telepon / NIP / ID"
                                       class="w-full rounded-md border-gray-300 text-sm">
                            </div>
                        </div>

                        <!-- Canvas Signature Pad -->
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                    Area Tanda Tangan Pelanggan <span class="text-red-500">*</span>
                                </label>
                                <button type="button" id="clearSigBtn"
                                        class="text-xs text-red-600 hover:text-red-800 font-semibold px-2 py-0.5 rounded border border-red-200 hover:bg-red-50">
                                    Hapus Tanda Tangan
                                </button>
                            </div>

                            <p class="text-xs text-gray-500 italic mb-2">
                                "Silakan bubuhkan tanda tangan Anda pada area di bawah ini."
                            </p>

                            <div class="w-full border-2 border-dashed border-gray-300 rounded-lg bg-gray-50 hover:bg-white transition-colors relative" style="touch-action: none;">
                                <canvas id="signatureCanvas" class="w-full rounded-lg block" height="220" style="cursor: crosshair;"></canvas>
                                <div id="canvasPlaceholder" class="pointer-events-none absolute inset-0 flex flex-col items-center justify-center text-gray-400 text-xs">
                                    <svg class="w-8 h-8 text-gray-300 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                    </svg>
                                    <span>Goreskan tanda tangan menggunakan jari, stylus, mouse, atau touchpad</span>
                                </div>
                            </div>
                        </div>

                        <!-- Pernyataan Persetujuan -->
                        <div class="rounded-lg bg-indigo-50/60 border border-indigo-200 p-4">
                            <label class="flex items-start gap-3 cursor-pointer">
                                <input type="checkbox" name="signer_agreed" value="1" required
                                       @checked(old('signer_agreed'))
                                       class="mt-1 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="text-xs text-gray-800 leading-relaxed">
                                    Saya selaku pelanggan / perwakilan resmi menyatakan bahwa survei lapangan ini telah benar-benar dilaksanakan, seluruh informasi hasil pemeriksaan telah saya periksa, dan saya menyetujui hasil pelaksanaan survei ini.
                                </span>
                            </label>
                        </div>

                        <div class="pt-2 flex items-center justify-end gap-3">
                            <button type="submit" id="saveSigBtn"
                                    class="px-6 py-2.5 bg-indigo-600 text-white rounded-md text-sm font-semibold hover:bg-indigo-700 shadow-md">
                                Simpan Tanda Tangan Pelanggan
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <!-- Script Signature Pad Canvas Responsive -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const canvas = document.getElementById('signatureCanvas');
            if (!canvas) return;

            const ctx = canvas.getContext('2d');
            const clearBtn = document.getElementById('clearSigBtn');
            const sigInput = document.getElementById('signatureInput');
            const placeholder = document.getElementById('canvasPlaceholder');
            const form = document.getElementById('signatureForm');

            let isDrawing = false;
            let hasDrawn = false;
            let lastX = 0;
            let lastY = 0;

            function resizeCanvas() {
                const rect = canvas.getBoundingClientRect();
                const dpr = window.devicePixelRatio || 1;
                // Preserve content if redrawn
                const tempCanvas = document.createElement('canvas');
                tempCanvas.width = canvas.width;
                tempCanvas.height = canvas.height;
                const tempCtx = tempCanvas.getContext('2d');
                tempCtx.drawImage(canvas, 0, 0);

                canvas.width = rect.width * dpr;
                canvas.height = 220 * dpr;
                ctx.scale(dpr, dpr);
                ctx.lineWidth = 2.5;
                ctx.lineCap = 'round';
                ctx.lineJoin = 'round';
                ctx.strokeStyle = '#1e3a8a'; // Deep navy blue ink

                if (hasDrawn) {
                    ctx.drawImage(tempCanvas, 0, 0, rect.width, 220);
                }
            }

            resizeCanvas();
            window.addEventListener('resize', resizeCanvas);

            function getPos(e) {
                const rect = canvas.getBoundingClientRect();
                let clientX = e.clientX;
                let clientY = e.clientY;
                if (e.touches && e.touches.length > 0) {
                    clientX = e.touches[0].clientX;
                    clientY = e.touches[0].clientY;
                }
                return {
                    x: clientX - rect.left,
                    y: clientY - rect.top
                };
            }

            function startDraw(e) {
                e.preventDefault();
                isDrawing = true;
                const pos = getPos(e);
                lastX = pos.x;
                lastY = pos.y;
                if (!hasDrawn && placeholder) {
                    placeholder.style.display = 'none';
                }
            }

            function draw(e) {
                if (!isDrawing) return;
                e.preventDefault();
                const pos = getPos(e);
                ctx.beginPath();
                ctx.moveTo(lastX, lastY);
                ctx.lineTo(pos.x, pos.y);
                ctx.stroke();
                lastX = pos.x;
                lastY = pos.y;
                hasDrawn = true;
            }

            function stopDraw(e) {
                if (!isDrawing) return;
                isDrawing = false;
            }

            canvas.addEventListener('mousedown', startDraw);
            canvas.addEventListener('mousemove', draw);
            window.addEventListener('mouseup', stopDraw);

            canvas.addEventListener('touchstart', startDraw, { passive: false });
            canvas.addEventListener('touchmove', draw, { passive: false });
            window.addEventListener('touchend', stopDraw);
            window.addEventListener('touchcancel', stopDraw);

            clearBtn.addEventListener('click', function () {
                const rect = canvas.getBoundingClientRect();
                ctx.clearRect(0, 0, rect.width, 220);
                hasDrawn = false;
                sigInput.value = '';
                if (placeholder) {
                    placeholder.style.display = 'flex';
                }
            });

            form.addEventListener('submit', function (e) {
                if (!hasDrawn) {
                    e.preventDefault();
                    alert('Silakan bubuhkan tanda tangan terlebih dahulu pada area yang disediakan.');
                    return false;
                }
                // Export clean PNG
                sigInput.value = canvas.toDataURL('image/png');
            });
        });
    </script>
</x-app-layout>

