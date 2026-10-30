@extends('layouts.app')

@section('title', 'Detail BAPP | SIMOP')
@section('page-title', 'Detail BAPP')

@section('content')
    @php
        $role = auth()->user()?->role?->slug;

        $production = $bapp->production;
        $planning = $production?->planning;
        $project = $planning?->project;
        $workEstimate = $project?->workEstimate;
        $order = $workEstimate?->order;

        $productionProgress = max(
            0,
            min(100, (int) ($production?->progress ?? 0))
        );

        $productionValid =
            $production
            && $production->status === 'completed'
            && $productionProgress === 100;

        $projectCancelled =
            $project?->status === 'cancelled';

        $canProcess =
            $role === 'admin'
            && $productionValid
            && !$projectCancelled
            && in_array(
                $bapp->status,
                ['draft', 'submitted'],
                true
            );

        $bappStatusLabels = [
            'draft' => 'Draft',
            'submitted' => 'Diajukan',
            'approved' => 'Disetujui',
        ];

        $bappStatusColors = [
            'draft' => 'bg-slate-100 text-slate-700',
            'submitted' => 'bg-amber-100 text-amber-700',
            'approved' => 'bg-green-100 text-green-700',
        ];

        $productionStatusLabels = [
            'pending' => 'Menunggu',
            'in_progress' => 'Dalam Pengerjaan',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
        ];

        $productionStatusColors = [
            'pending' => 'bg-slate-100 text-slate-700',
            'in_progress' => 'bg-amber-100 text-amber-700',
            'completed' => 'bg-green-100 text-green-700',
            'cancelled' => 'bg-red-100 text-red-700',
        ];

        $projectStatusLabels = [
            'planning' => 'Planning',
            'ongoing' => 'Berjalan',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
        ];

        $projectStatusColors = [
            'planning' => 'bg-slate-100 text-slate-700',
            'ongoing' => 'bg-blue-100 text-blue-700',
            'completed' => 'bg-green-100 text-green-700',
            'cancelled' => 'bg-red-100 text-red-700',
        ];

        $bappStatusLabel = $bappStatusLabels[$bapp->status]
            ?? ucfirst($bapp->status);

        $bappStatusColor = $bappStatusColors[$bapp->status]
            ?? 'bg-slate-100 text-slate-700';

        $productionStatusLabel = $productionStatusLabels[$production?->status]
            ?? '-';

        $productionStatusColor = $productionStatusColors[$production?->status]
            ?? 'bg-slate-100 text-slate-700';

        $projectStatusLabel = $projectStatusLabels[$project?->status]
            ?? '-';

        $projectStatusColor = $projectStatusColors[$project?->status]
            ?? 'bg-slate-100 text-slate-700';
    @endphp

    <div class="mx-auto max-w-6xl space-y-6">
        @if (session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 p-4 text-sm font-medium text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm font-medium text-red-700">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                <p class="font-semibold">
                    Terdapat data yang perlu diperbaiki:
                </p>

                <ul class="mt-2 list-inside list-disc space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div>
            <a
                href="{{ route('bapps.index') }}"
                class="text-sm font-semibold text-blue-600 hover:text-blue-800"
            >
                &larr; Kembali ke BAPP
            </a>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-5 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">
                        Berita Acara Penyelesaian Pekerjaan
                    </p>

                    <h2 class="mt-1 text-2xl font-bold text-slate-900">
                        {{ $bapp->bapp_number }}
                    </h2>

                    <div class="mt-3 flex flex-wrap gap-x-5 gap-y-2 text-sm text-slate-500">
                        <p>
                            Dibuat oleh
                            <span class="font-semibold text-slate-700">
                                {{ $bapp->creator?->name ?? '-' }}
                            </span>
                        </p>

                        <p>
                            Tanggal
                            <span class="font-semibold text-slate-700">
                                {{ $bapp->completion_date?->format('d/m/Y') ?? '-' }}
                            </span>
                        </p>
                    </div>
                </div>

                <span class="inline-flex w-fit rounded-full px-4 py-2 text-sm font-semibold {{ $bappStatusColor }}">
                    {{ $bappStatusLabel }}
                </span>
            </div>
        </div>

        @if ($projectCancelled)
            <div class="rounded-xl border border-red-200 bg-red-50 p-5">
                <h3 class="font-bold text-red-800">
                    Project Telah Dibatalkan
                </h3>

                <p class="mt-1 text-sm leading-6 text-red-700">
                    BAPP tidak dapat diproses lebih lanjut karena Project terkait telah dibatalkan.
                </p>
            </div>
        @elseif (!$productionValid && $bapp->status !== 'approved')
            <div class="rounded-xl border border-red-200 bg-red-50 p-5">
                <h3 class="font-bold text-red-800">
                    Produksi & Instalasi Tidak Memenuhi Syarat
                </h3>

                <p class="mt-1 text-sm leading-6 text-red-700">
                    BAPP hanya dapat diproses jika Produksi & Instalasi berstatus Selesai dengan progress 100%.
                </p>
            </div>
        @elseif ($bapp->status === 'draft')
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-5">
                <div class="flex items-start gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-slate-200 font-bold text-slate-700">
                        1
                    </div>

                    <div>
                        <h3 class="font-bold text-slate-800">
                            BAPP Masih Draft
                        </h3>

                        <p class="mt-1 text-sm leading-6 text-slate-600">
                            Data BAPP telah dibuat dan belum diajukan. Tahap berikutnya adalah mengajukan BAPP.
                        </p>
                    </div>
                </div>
            </div>
        @elseif ($bapp->status === 'submitted')
            <div class="rounded-xl border border-amber-200 bg-amber-50 p-5">
                <div class="flex items-start gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-amber-100 font-bold text-amber-700">
                        2
                    </div>

                    <div>
                        <h3 class="font-bold text-amber-800">
                            BAPP Telah Diajukan
                        </h3>

                        <p class="mt-1 text-sm leading-6 text-amber-700">
                            BAPP telah masuk ke tahap pengajuan. Tahap berikutnya adalah persetujuan BAPP.
                        </p>
                    </div>
                </div>
            </div>
        @elseif ($bapp->status === 'approved')
            <div class="rounded-xl border border-green-200 bg-green-50 p-5">
                <div class="flex items-start gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-green-100 font-bold text-green-700">
                        ✓
                    </div>

                    <div>
                        <h3 class="font-bold text-green-800">
                            BAPP Telah Disetujui
                        </h3>

                        <p class="mt-1 text-sm leading-6 text-green-700">
                            BAPP telah final. Project telah dinyatakan selesai dengan progress 100%.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        @if ($role === 'direktur')
            <div class="rounded-xl border border-blue-200 bg-blue-50 p-5">
                <p class="text-sm font-semibold text-blue-800">
                    Mode Monitoring Direktur
                </p>

                <p class="mt-1 text-sm leading-6 text-blue-700">
                    Direktur dapat melihat detail BAPP dan status penyelesaian Project, tetapi tidak dapat mengubah status BAPP.
                </p>
            </div>
        @elseif ($role === 'petugas-lapangan')
            <div class="rounded-xl border border-blue-200 bg-blue-50 p-5">
                <p class="text-sm font-semibold text-blue-800">
                    Informasi BAPP Pekerjaan
                </p>

                <p class="mt-1 text-sm leading-6 text-blue-700">
                    Anda dapat melihat BAPP dari Produksi & Instalasi yang ditugaskan kepada Anda. Perubahan status dilakukan oleh Administrator.
                </p>
            </div>
        @endif

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="text-lg font-bold text-slate-900">
                    Informasi BAPP
                </h3>

                <dl class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Nomor BAPP
                        </dt>

                        <dd class="mt-1 text-sm font-semibold text-slate-800">
                            {{ $bapp->bapp_number }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Status
                        </dt>

                        <dd class="mt-2">
                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $bappStatusColor }}">
                                {{ $bappStatusLabel }}
                            </span>
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Tanggal Penyelesaian
                        </dt>

                        <dd class="mt-1 text-sm font-medium text-slate-800">
                            {{ $bapp->completion_date?->format('d/m/Y') ?? '-' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Dibuat Oleh
                        </dt>

                        <dd class="mt-1 text-sm font-medium text-slate-800">
                            {{ $bapp->creator?->name ?? '-' }}
                        </dd>
                    </div>

                    <div class="sm:col-span-2">
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Lokasi
                        </dt>

                        <dd class="mt-1 text-sm font-medium text-slate-800">
                            {{ $bapp->location ?: '-' }}
                        </dd>
                    </div>

                    <div class="sm:col-span-2">
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Hasil Pekerjaan
                        </dt>

                        <dd class="mt-1 whitespace-pre-line text-sm leading-6 text-slate-700">
                            {{ $bapp->work_result ?: 'Tidak ada informasi hasil pekerjaan.' }}
                        </dd>
                    </div>

                    <div class="sm:col-span-2">
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Catatan
                        </dt>

                        <dd class="mt-1 whitespace-pre-line text-sm leading-6 text-slate-700">
                            {{ $bapp->notes ?: 'Tidak ada catatan.' }}
                        </dd>
                    </div>
                </dl>
            </div>

            <div class="space-y-6">
                <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-bold text-slate-900">
                        Pekerjaan
                    </h3>

                    <div class="mt-5 space-y-5">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Produksi & Instalasi
                            </p>

                            @if ($production)
                                <a
                                    href="{{ route('productions.show', $production) }}"
                                    class="mt-1 inline-block text-sm font-semibold text-blue-600 hover:text-blue-800"
                                >
                                    {{ $production->production_number }} — {{ $production->title }}
                                </a>
                            @else
                                <p class="mt-1 text-sm text-slate-500">
                                    -
                                </p>
                            @endif
                        </div>

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Project
                            </p>

                            @if ($project)
                                <a
                                    href="{{ route('projects.show', $project) }}"
                                    class="mt-1 inline-block text-sm font-semibold text-blue-600 hover:text-blue-800"
                                >
                                    {{ $project->project_number }} — {{ $project->title }}
                                </a>
                            @else
                                <p class="mt-1 text-sm text-slate-500">
                                    -
                                </p>
                            @endif
                        </div>

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Work Estimate
                            </p>

                            <p class="mt-1 text-sm font-medium text-slate-800">
                                {{ $workEstimate?->estimate_number ?? '-' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Order
                            </p>

                            <p class="mt-1 text-sm font-medium text-slate-800">
                                {{ $order?->order_number ?? '-' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Petugas Lapangan
                            </p>

                            <p class="mt-1 text-sm font-medium text-slate-800">
                                {{ $production?->assignedUser?->name ?? '-' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Status Produksi
                            </p>

                            <div class="mt-2">
                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $productionStatusColor }}">
                                    {{ $productionStatusLabel }}
                                </span>
                            </div>
                        </div>

                        <div>
                            <div class="flex items-center justify-between gap-4">
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Progress Produksi
                                </p>

                                <span class="text-sm font-bold text-green-600">
                                    {{ $productionProgress }}%
                                </span>
                            </div>

                            <div class="mt-2 h-2.5 overflow-hidden rounded-full bg-slate-200">
                                <div
                                    class="h-full rounded-full bg-green-500"
                                    style="width: {{ $productionProgress }}%"
                                ></div>
                            </div>
                        </div>

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Status Project
                            </p>

                            <div class="mt-2">
                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $projectStatusColor }}">
                                    {{ $projectStatusLabel }}
                                </span>
                            </div>

                            @if (
                                $bapp->status === 'approved'
                                && $project?->status === 'completed'
                            )
                                <p class="mt-2 text-xs font-medium text-green-600">
                                    Project selesai setelah BAPP disetujui.
                                </p>
                            @elseif (
                                $bapp->status !== 'approved'
                                && $project?->status === 'ongoing'
                            )
                                <p class="mt-2 text-xs text-slate-500">
                                    Project tetap berjalan sampai BAPP disetujui.
                                </p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-bold text-slate-900">
                        Alur BAPP
                    </h3>

                    <div class="mt-6">
                        <div class="flex gap-4">
                            <div class="flex flex-col items-center">
                                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-green-500 text-sm font-bold text-white">
                                    ✓
                                </div>

                                <div class="h-10 w-0.5 bg-green-300"></div>
                            </div>

                            <div class="pb-5">
                                <p class="font-semibold text-green-700">
                                    Draft
                                </p>

                                <p class="mt-1 text-xs text-slate-500">
                                    BAPP telah dibuat.
                                </p>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <div class="flex flex-col items-center">
                                @if (in_array($bapp->status, ['submitted', 'approved'], true))
                                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-green-500 text-sm font-bold text-white">
                                        ✓
                                    </div>
                                @else
                                    <div class="flex h-9 w-9 items-center justify-center rounded-full border-2 border-blue-500 bg-white text-sm font-bold text-blue-600">
                                        2
                                    </div>
                                @endif

                                <div class="h-10 w-0.5 {{ in_array($bapp->status, ['submitted', 'approved'], true) ? 'bg-green-300' : 'bg-slate-200' }}"></div>
                            </div>

                            <div class="pb-5">
                                <p class="font-semibold {{ in_array($bapp->status, ['submitted', 'approved'], true) ? 'text-green-700' : 'text-slate-600' }}">
                                    Diajukan
                                </p>

                                <p class="mt-1 text-xs text-slate-500">
                                    BAPP diajukan untuk proses persetujuan.
                                </p>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <div>
                                @if ($bapp->status === 'approved')
                                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-green-500 text-sm font-bold text-white">
                                        ✓
                                    </div>
                                @else
                                    <div class="flex h-9 w-9 items-center justify-center rounded-full border-2 border-slate-300 bg-white text-sm font-bold text-slate-400">
                                        3
                                    </div>
                                @endif
                            </div>

                            <div>
                                <p class="font-semibold {{ $bapp->status === 'approved' ? 'text-green-700' : 'text-slate-600' }}">
                                    Disetujui
                                </p>

                                <p class="mt-1 text-xs text-slate-500">
                                    BAPP menjadi final dan Project dinyatakan selesai.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                @if (
                    $canProcess
                    && $bapp->status === 'draft'
                )
                    <div class="rounded-xl border border-blue-200 bg-blue-50 p-6">
                        <h3 class="text-lg font-bold text-blue-900">
                            Ajukan BAPP
                        </h3>

                        <p class="mt-1 text-sm leading-6 text-blue-700">
                            Pastikan seluruh informasi BAPP sudah benar. Setelah diajukan, BAPP tidak dapat dikembalikan menjadi Draft.
                        </p>

                        <form
                            method="POST"
                            action="{{ route('bapps.status', $bapp) }}"
                            class="mt-5"
                        >
                            @csrf
                            @method('PATCH')

                            <input
                                type="hidden"
                                name="status"
                                value="submitted"
                            >

                            <button
                                type="submit"
                                onclick="return confirm('Ajukan BAPP ini? Setelah diajukan, BAPP tidak dapat dikembalikan menjadi Draft.')"
                                class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"
                            >
                                Ajukan BAPP
                            </button>
                        </form>
                    </div>
                @endif

                @if (
                    $canProcess
                    && $bapp->status === 'submitted'
                )
                    <div class="rounded-xl border border-green-200 bg-green-50 p-6">
                        <h3 class="text-lg font-bold text-green-900">
                            Setujui BAPP
                        </h3>

                        <p class="mt-1 text-sm leading-6 text-green-700">
                            Persetujuan bersifat final. Setelah BAPP disetujui, Project otomatis berubah menjadi Selesai dengan progress 100%.
                        </p>

                        <form
                            method="POST"
                            action="{{ route('bapps.status', $bapp) }}"
                            class="mt-5"
                        >
                            @csrf
                            @method('PATCH')

                            <input
                                type="hidden"
                                name="status"
                                value="approved"
                            >

                            <button
                                type="submit"
                                onclick="return confirm('Setujui BAPP ini? Project akan dinyatakan selesai dan tindakan ini tidak dapat dibatalkan.')"
                                class="inline-flex items-center justify-center rounded-lg bg-green-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-green-700"
                            >
                                Setujui BAPP
                            </button>
                        </form>
                    </div>
                @endif

                @if ($bapp->status === 'approved')
                    <div class="rounded-xl border border-green-200 bg-white p-6 shadow-sm">
                        <h3 class="text-lg font-bold text-slate-900">
                            Status Akhir
                        </h3>

                        <div class="mt-4 flex items-start gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-green-100 font-bold text-green-700">
                                ✓
                            </div>

                            <div>
                                <p class="font-semibold text-green-700">
                                    BAPP Disetujui
                                </p>

                                <p class="mt-1 text-sm leading-6 text-slate-500">
                                    BAPP telah final. Pelaksanaan pekerjaan selesai dan Project telah ditutup dengan progress 100%.
                                </p>
                            </div>
                        </div>
                    </div>
                @elseif (
                    $role === 'admin'
                    && !$canProcess
                )
                    <div class="rounded-xl border border-red-200 bg-red-50 p-5">
                        <p class="text-sm font-semibold text-red-700">
                            BAPP Tidak Dapat Diproses
                        </p>

                        <p class="mt-1 text-sm leading-6 text-red-600">
                            Pastikan Production berstatus Selesai dengan progress 100% dan Project tidak dibatalkan.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection