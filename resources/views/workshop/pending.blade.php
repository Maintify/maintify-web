<x-app-layout>
    <x-slot name="pageTitle">Menunggu Verifikasi</x-slot>

    <div style="min-height:60vh;display:flex;align-items:center;justify-content:center;">
        <div style="max-width:480px;width:100%;text-align:center;">

            {{-- Icon --}}
            <div style="width:80px;height:80px;border-radius:24px;background:rgba(245,158,11,0.1);border:1px solid rgba(245,158,11,0.2);display:flex;align-items:center;justify-content:center;margin:0 auto 24px;">
                <svg style="width:36px;height:36px;color:#fbbf24;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>

            {{-- Status --}}
            @php $workshop = Auth::user()->workshop; @endphp

            @if($workshop && $workshop->status === 'rejected')
                {{-- Ditolak --}}
                <div style="width:80px;height:80px;border-radius:24px;background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.2);display:flex;align-items:center;justify-content:center;margin:-80px auto 24px;">
                    <svg style="width:36px;height:36px;color:#f87171;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h2 style="font-size:22px;font-weight:700;color:#F4F4F5;margin-bottom:8px;">Pendaftaran Ditolak</h2>
                <p style="color:#71717A;font-size:14px;line-height:1.6;margin-bottom:16px;">
                    Maaf, pendaftaran bengkel Anda tidak dapat disetujui saat ini.
                </p>
                @if($workshop->rejection_reason)
                    <div style="background-color:rgba(239,68,68,0.08);border:1px solid rgba(239,68,68,0.2);border-radius:10px;padding:12px 16px;margin-bottom:24px;text-align:left;">
                        <p style="font-size:12px;color:#f87171;font-weight:600;margin-bottom:4px;">Alasan Penolakan:</p>
                        <p style="font-size:13px;color:#A1A1AA;">{{ $workshop->rejection_reason }}</p>
                    </div>
                @endif
                <p style="color:#71717A;font-size:13px;">
                    Hubungi kami di
                    <a href="mailto:support@maintify.app" style="color:#ff9aa4;">support@maintify.app</a>
                    untuk informasi lebih lanjut.
                </p>

            @else
                {{-- Pending --}}
                <h2 style="font-size:22px;font-weight:700;color:#F4F4F5;margin-bottom:8px;">Menunggu Verifikasi</h2>
                <p style="color:#71717A;font-size:14px;line-height:1.6;margin-bottom:24px;">
                    Pendaftaran bengkel <strong style="color:#A1A1AA;">{{ $workshop?->name }}</strong> sedang dalam proses verifikasi oleh tim admin Maintify. Proses ini membutuhkan <strong style="color:#fbbf24;">1-2 hari kerja</strong>.
                </p>

                {{-- Info Cards --}}
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;margin-bottom:24px;">
                    @foreach([
                        ['icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'label' => 'Akun Dibuat', 'color' => '#4ade80', 'bg' => 'rgba(34,197,94,0.1)', 'done' => true],
                        ['icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'label' => 'Verifikasi Admin', 'color' => '#fbbf24', 'bg' => 'rgba(245,158,11,0.1)', 'done' => false],
                        ['icon' => 'M5 13l4 4L19 7', 'label' => 'Akun Aktif', 'color' => '#71717A', 'bg' => 'rgba(113,113,122,0.1)', 'done' => false],
                    ] as $step)
                        <div style="background-color:{{ $step['bg'] }};border-radius:12px;padding:12px 8px;text-align:center;">
                            <svg style="width:20px;height:20px;color:{{ $step['color'] }};margin:0 auto 6px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $step['icon'] }}"/>
                            </svg>
                            <p style="font-size:11px;color:{{ $step['color'] }};font-weight:500;">{{ $step['label'] }}</p>
                        </div>
                    @endforeach
                </div>

                <p style="color:#71717A;font-size:12px;">
                    Anda akan mendapat notifikasi email ketika akun disetujui.
                    Pertanyaan? Hubungi <a href="mailto:support@maintify.app" style="color:#ff9aa4;">support@maintify.app</a>
                </p>
            @endif

            {{-- Logout --}}
            <form method="POST" action="{{ route('logout') }}" style="margin-top:24px;">
                @csrf
                <button type="submit" class="btn-secondary btn-sm">
                    <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Keluar
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
