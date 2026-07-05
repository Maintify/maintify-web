<x-app-layout>
    <x-slot name="pageTitle">
        @if($workshop && $workshop->isRejected())
            Pendaftaran Ditolak
        @elseif($workshop && $workshop->isRevisionNeeded())
            Revisi Diperlukan
        @else
            Menunggu Verifikasi
        @endif
    </x-slot>

    <div style="min-height:80vh;display:flex;align-items:center;justify-content:center;padding:24px;">
        <div style="max-width:520px;width:100%;">

            {{-- ============================================
                 STATUS: REJECTED
                 ============================================ --}}
            @if($workshop && $workshop->isRejected())
                {{-- Rejected Icon --}}
                <div style="display:flex;justify-content:center;margin-bottom:28px;">
                    <div style="width:88px;height:88px;border-radius:24px;background:rgba(239,68,68,0.08);border:1px solid rgba(239,68,68,0.2);display:flex;align-items:center;justify-content:center;position:relative;">
                        <svg style="width:40px;height:40px;color:#f87171;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{-- Pulse ring --}}
                        <div style="position:absolute;inset:-4px;border-radius:28px;border:2px solid rgba(239,68,68,0.15);animation:pulse-ring 2s ease-in-out infinite;"></div>
                    </div>
                </div>

                {{-- Title & Description --}}
                <div style="text-align:center;margin-bottom:24px;">
                    <h1 style="font-size:24px;font-weight:700;color:#F4F4F5;margin-bottom:8px;letter-spacing:-0.02em;">
                        Pendaftaran Ditolak
                    </h1>
                    <p style="color:#A1A1AA;font-size:14px;line-height:1.7;">
                        Maaf, pendaftaran bengkel
                        <strong style="color:#F4F4F5;">{{ $workshop->name }}</strong>
                        tidak dapat disetujui saat ini.
                    </p>
                </div>

                {{-- Rejection Reason Card --}}
                @if($workshop->rejection_reason)
                    <div style="background:rgba(239,68,68,0.06);border:1px solid rgba(239,68,68,0.18);border-radius:16px;padding:20px;margin-bottom:24px;">
                        <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
                            <svg style="width:16px;height:16px;color:#f87171;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                            </svg>
                            <span style="font-size:13px;font-weight:600;color:#f87171;">Alasan Penolakan</span>
                        </div>
                        <p style="font-size:14px;color:#D4D4D8;line-height:1.7;">{{ $workshop->rejection_reason }}</p>
                    </div>
                @endif

                {{-- Contact Support --}}
                <div style="background:var(--color-card, #252828);border:1px solid var(--color-border, #2E3030);border-radius:16px;padding:20px;text-align:center;margin-bottom:24px;">
                    <p style="color:#71717A;font-size:13px;line-height:1.7;margin-bottom:12px;">
                        Jika Anda merasa ada kesalahan atau ingin mengajukan banding, silakan hubungi tim support kami.
                    </p>
                    <a href="mailto:support@maintify.app"
                       style="display:inline-flex;align-items:center;gap:6px;padding:10px 20px;background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.2);border-radius:10px;font-size:13px;font-weight:500;color:#f87171;transition:all 0.2s ease;text-decoration:none;"
                       onmouseover="this.style.background='rgba(239,68,68,0.18)'"
                       onmouseout="this.style.background='rgba(239,68,68,0.1)'">
                        <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        support@maintify.app
                    </a>
                </div>

            {{-- ============================================
                 STATUS: REVISION NEEDED
                 ============================================ --}}
            @elseif($workshop && $workshop->isRevisionNeeded())
                {{-- Revision Icon --}}
                <div style="display:flex;justify-content:center;margin-bottom:28px;">
                    <div style="width:88px;height:88px;border-radius:24px;background:rgba(245,158,11,0.08);border:1px solid rgba(245,158,11,0.2);display:flex;align-items:center;justify-content:center;position:relative;">
                        <svg style="width:40px;height:40px;color:#fbbf24;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                        {{-- Pulse ring --}}
                        <div style="position:absolute;inset:-4px;border-radius:28px;border:2px solid rgba(245,158,11,0.15);animation:pulse-ring 2s ease-in-out infinite;"></div>
                    </div>
                </div>

                {{-- Title & Description --}}
                <div style="text-align:center;margin-bottom:24px;">
                    <h1 style="font-size:24px;font-weight:700;color:#F4F4F5;margin-bottom:8px;letter-spacing:-0.02em;">
                        Revisi Diperlukan
                    </h1>
                    <p style="color:#A1A1AA;font-size:14px;line-height:1.7;">
                        Pendaftaran bengkel
                        <strong style="color:#F4F4F5;">{{ $workshop->name }}</strong>
                        memerlukan perbaikan sebelum dapat disetujui.
                    </p>
                </div>

                {{-- Revision Reason Card --}}
                @if($workshop->rejection_reason)
                    <div style="background:rgba(245,158,11,0.06);border:1px solid rgba(245,158,11,0.18);border-radius:16px;padding:20px;margin-bottom:24px;">
                        <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
                            <svg style="width:16px;height:16px;color:#fbbf24;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            <span style="font-size:13px;font-weight:600;color:#fbbf24;">Catatan dari Admin</span>
                        </div>
                        <p style="font-size:14px;color:#D4D4D8;line-height:1.7;">{{ $workshop->rejection_reason }}</p>
                    </div>
                @endif

                {{-- Contact Support --}}
                <div style="background:var(--color-card, #252828);border:1px solid var(--color-border, #2E3030);border-radius:16px;padding:20px;text-align:center;margin-bottom:24px;">
                    <p style="color:#71717A;font-size:13px;line-height:1.7;margin-bottom:12px;">
                        Silakan perbaiki data pendaftaran sesuai catatan di atas, lalu hubungi admin untuk proses review ulang.
                    </p>
                    <a href="mailto:support@maintify.app"
                       style="display:inline-flex;align-items:center;gap:6px;padding:10px 20px;background:rgba(245,158,11,0.1);border:1px solid rgba(245,158,11,0.2);border-radius:10px;font-size:13px;font-weight:500;color:#fbbf24;transition:all 0.2s ease;text-decoration:none;"
                       onmouseover="this.style.background='rgba(245,158,11,0.18)'"
                       onmouseout="this.style.background='rgba(245,158,11,0.1)'">
                        <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        Hubungi Support
                    </a>
                </div>

            {{-- ============================================
                 STATUS: PENDING (default)
                 ============================================ --}}
            @else
                {{-- Pending Icon with animation --}}
                <div style="display:flex;justify-content:center;margin-bottom:28px;">
                    <div style="width:88px;height:88px;border-radius:24px;background:rgba(245,158,11,0.08);border:1px solid rgba(245,158,11,0.2);display:flex;align-items:center;justify-content:center;position:relative;">
                        <svg style="width:40px;height:40px;color:#fbbf24;animation:spin-slow 4s linear infinite;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{-- Pulse ring --}}
                        <div style="position:absolute;inset:-4px;border-radius:28px;border:2px solid rgba(245,158,11,0.15);animation:pulse-ring 2s ease-in-out infinite;"></div>
                    </div>
                </div>

                {{-- Title & Description --}}
                <div style="text-align:center;margin-bottom:28px;">
                    <h1 style="font-size:24px;font-weight:700;color:#F4F4F5;margin-bottom:8px;letter-spacing:-0.02em;">
                        Menunggu Verifikasi
                    </h1>
                    <p style="color:#A1A1AA;font-size:14px;line-height:1.7;">
                        Pendaftaran bengkel
                        <strong style="color:#F4F4F5;">{{ $workshop?->name ?? 'Anda' }}</strong>
                        sedang dalam proses verifikasi oleh tim admin Maintify.
                        Proses ini membutuhkan
                        <strong style="color:#fbbf24;">1–2 hari kerja</strong>.
                    </p>
                </div>

                {{-- Progress Stepper --}}
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;margin-bottom:28px;">
                    {{-- Step 1: Akun Dibuat (Done) --}}
                    <div style="background:rgba(34,197,94,0.06);border:1px solid rgba(34,197,94,0.18);border-radius:14px;padding:16px 10px;text-align:center;">
                        <div style="width:36px;height:36px;border-radius:10px;background:rgba(34,197,94,0.12);display:flex;align-items:center;justify-content:center;margin:0 auto 8px;">
                            <svg style="width:18px;height:18px;color:#4ade80;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <p style="font-size:11px;font-weight:600;color:#4ade80;margin-bottom:2px;">Akun Dibuat</p>
                        <p style="font-size:10px;color:#71717A;">Selesai</p>
                    </div>

                    {{-- Step 2: Verifikasi Admin (In Progress) --}}
                    <div style="background:rgba(245,158,11,0.06);border:1px solid rgba(245,158,11,0.18);border-radius:14px;padding:16px 10px;text-align:center;">
                        <div style="width:36px;height:36px;border-radius:10px;background:rgba(245,158,11,0.12);display:flex;align-items:center;justify-content:center;margin:0 auto 8px;">
                            <svg style="width:18px;height:18px;color:#fbbf24;animation:pulse 2s ease-in-out infinite;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <p style="font-size:11px;font-weight:600;color:#fbbf24;margin-bottom:2px;">Verifikasi Admin</p>
                        <p style="font-size:10px;color:#71717A;">Dalam proses</p>
                    </div>

                    {{-- Step 3: Akun Aktif (Pending) --}}
                    <div style="background:rgba(113,113,122,0.06);border:1px solid rgba(113,113,122,0.12);border-radius:14px;padding:16px 10px;text-align:center;">
                        <div style="width:36px;height:36px;border-radius:10px;background:rgba(113,113,122,0.08);display:flex;align-items:center;justify-content:center;margin:0 auto 8px;">
                            <svg style="width:18px;height:18px;color:#52525B;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <p style="font-size:11px;font-weight:600;color:#52525B;margin-bottom:2px;">Akun Aktif</p>
                        <p style="font-size:10px;color:#3F3F46;">Menunggu</p>
                    </div>
                </div>

                {{-- Workshop Info Card --}}
                @if($workshop)
                    <div style="background:var(--color-card, #252828);border:1px solid var(--color-border, #2E3030);border-radius:16px;padding:20px;margin-bottom:24px;">
                        <p style="font-size:12px;font-weight:600;color:#71717A;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:12px;">Informasi Bengkel</p>
                        <div style="display:grid;gap:10px;">
                            <div style="display:flex;justify-content:space-between;align-items:center;">
                                <span style="font-size:13px;color:#71717A;">Nama Bengkel</span>
                                <span style="font-size:13px;font-weight:500;color:#F4F4F5;">{{ $workshop->name }}</span>
                            </div>
                            <div style="height:1px;background:var(--color-border, #2E3030);"></div>
                            <div style="display:flex;justify-content:space-between;align-items:center;">
                                <span style="font-size:13px;color:#71717A;">Pemilik</span>
                                <span style="font-size:13px;font-weight:500;color:#F4F4F5;">{{ $workshop->owner_name }}</span>
                            </div>
                            <div style="height:1px;background:var(--color-border, #2E3030);"></div>
                            <div style="display:flex;justify-content:space-between;align-items:center;">
                                <span style="font-size:13px;color:#71717A;">Kota</span>
                                <span style="font-size:13px;font-weight:500;color:#F4F4F5;">{{ $workshop->city }}, {{ $workshop->province }}</span>
                            </div>
                            <div style="height:1px;background:var(--color-border, #2E3030);"></div>
                            <div style="display:flex;justify-content:space-between;align-items:center;">
                                <span style="font-size:13px;color:#71717A;">Status</span>
                                <span style="display:inline-flex;align-items:center;gap:5px;padding:4px 10px;background:rgba(245,158,11,0.1);border:1px solid rgba(245,158,11,0.2);border-radius:100px;font-size:11px;font-weight:600;color:#fbbf24;">
                                    <span style="width:6px;height:6px;border-radius:50%;background:#fbbf24;animation:pulse 2s ease-in-out infinite;"></span>
                                    Menunggu Verifikasi
                                </span>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Email notification note --}}
                <div style="text-align:center;margin-bottom:24px;">
                    <div style="display:inline-flex;align-items:center;gap:8px;padding:10px 16px;background:rgba(99,102,241,0.06);border:1px solid rgba(99,102,241,0.15);border-radius:10px;">
                        <svg style="width:14px;height:14px;color:#818cf8;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <span style="font-size:12px;color:#A1A1AA;">Anda akan mendapat notifikasi email ketika akun disetujui.</span>
                    </div>
                </div>
            @endif

            {{-- Logout Button --}}
            <div style="text-align:center;">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            style="display:inline-flex;align-items:center;gap:6px;padding:10px 24px;background:var(--color-card, #252828);border:1px solid var(--color-border, #2E3030);border-radius:10px;font-size:13px;font-weight:500;color:#A1A1AA;cursor:pointer;transition:all 0.2s ease;"
                            onmouseover="this.style.background='var(--color-hover, #2A2D2D)';this.style.color='#F4F4F5'"
                            onmouseout="this.style.background='var(--color-card, #252828)';this.style.color='#A1A1AA'">
                        <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Keluar
                    </button>
                </form>
            </div>

        </div>
    </div>

    {{-- Animations --}}
    <style>
        @keyframes pulse-ring {
            0%, 100% { opacity: 0.3; transform: scale(1); }
            50% { opacity: 0.8; transform: scale(1.04); }
        }
        @keyframes spin-slow {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
    </style>
</x-app-layout>
