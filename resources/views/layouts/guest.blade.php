<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="Maintify - Platform Digital Histori Service Kendaraan Motor berbasis QR Code">
        <meta name="theme-color" content="#121414">

        <title>{{ config('app.name', 'Maintify') }} | @yield('title', 'Selamat Datang')</title>

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('images/logo-icon.png') }}">

        <!-- Google Fonts - Inter -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @stack('head')
    </head>
    <body class="font-sans antialiased no-tap-highlight" style="background-color:#121414;color:#F4F4F5;min-height:100vh;">

        {{-- Auth Container --}}
        <div class="auth-container" style="min-height:100vh;display:flex;align-items:stretch;">

            {{-- ========================
                 LEFT PANEL (Desktop only)
                 ======================== --}}
            <div class="hidden lg:flex flex-col justify-between auth-left-panel" aria-hidden="true"
                 style="background:linear-gradient(160deg,#2a0008 0%,#410008 40%,#5E0B15 100%);padding:3rem;width:45%;max-width:520px;position:relative;overflow:hidden;">

                {{-- Decorative Background --}}
                <div style="position:absolute;inset:0;pointer-events:none;">
                    <div style="position:absolute;top:-100px;right:-100px;width:400px;height:400px;border-radius:50%;background:radial-gradient(circle,rgba(255,255,255,0.04) 0%,transparent 70%);"></div>
                    <div style="position:absolute;bottom:-80px;left:-80px;width:300px;height:300px;border-radius:50%;background:radial-gradient(circle,rgba(255,255,255,0.03) 0%,transparent 70%);"></div>
                </div>

                {{-- Logo --}}
                <div style="display:flex;align-items:center;position:relative;z-index:1;">
                    <a href="/" class="flex items-center">
                        <img src="{{ asset('images/logo.png') }}" alt="Maintify" style="height:48px;width:auto;display:block;">
                    </a>
                </div>

                {{-- Hero Content --}}
                <div style="position:relative;z-index:1;">
                    <h2 style="font-size:clamp(28px,3vw,38px);font-weight:800;color:white;line-height:1.15;letter-spacing:-0.03em;margin-bottom:1rem;">
                        Platform Digital<br>
                        Histori Service<br>
                        Kendaraan Motor
                    </h2>
                    <p style="color:rgba(255,255,255,0.65);font-size:15px;line-height:1.7;margin-bottom:2rem;">
                        Kelola, pantau, dan catat semua histori servis kendaraan Anda secara digital dengan QR Code unik.
                    </p>

                    {{-- Feature Pills --}}
                    <div style="display:flex;flex-wrap:wrap;gap:8px;">
                        @foreach([
                            ['icon' => '🔖', 'label' => 'QR Code Unik'],
                            ['icon' => '📋', 'label' => 'Histori Service'],
                            ['icon' => '❤️', 'label' => 'Health Monitoring'],
                            ['icon' => '🏪', 'label' => 'Multi Bengkel'],
                        ] as $feature)
                            <span style="display:inline-flex;align-items:center;gap:6px;padding:6px 12px;background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.15);border-radius:100px;font-size:12px;font-weight:500;color:rgba(255,255,255,0.9);backdrop-filter:blur(8px);">
                                <span>{{ $feature['icon'] }}</span>
                                {{ $feature['label'] }}
                            </span>
                        @endforeach
                    </div>
                </div>

                {{-- Testimonial / Bottom Quote --}}
                <div style="position:relative;z-index:1;padding:20px;background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.1);border-radius:16px;">
                    <p style="color:rgba(255,255,255,0.8);font-size:13px;line-height:1.6;margin-bottom:12px;">
                        "Tidak perlu lagi bawa buku servis kemana-mana. Semua tercatat digital, bisa diakses kapan saja."
                    </p>
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div style="width:32px;height:32px;border-radius:50%;background:rgba(255,255,255,0.2);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:white;">A</div>
                        <div>
                            <p style="font-size:12px;font-weight:600;color:white;">Ahmad R.</p>
                            <p style="font-size:11px;color:rgba(255,255,255,0.5);">Pengguna Maintify</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ========================
                 RIGHT PANEL - Auth Card
                 ======================== --}}
            <div style="flex:1;display:flex;align-items:center;justify-content:center;padding:24px;background:linear-gradient(135deg,#121414 0%,#1a1010 100%);">
                <div class="auth-card fade-in" style="width:100%;max-width:440px;">

                    {{-- Mobile Logo --}}
                    <div style="display:flex;align-items:center;margin-bottom:28px;" class="lg:hidden">
                        <a href="/" class="flex items-center">
                            <img src="{{ asset('images/logo.png') }}" alt="Maintify" style="height:36px;width:auto;display:block;">
                        </a>
                    </div>

                    {{ $slot }}
                </div>
            </div>
        </div>

        @stack('scripts')
        <x-toast-notification />
    </body>
</html>
