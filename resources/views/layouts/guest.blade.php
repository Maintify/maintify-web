<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="Maintify - Platform Digital Histori Service Kendaraan Motor">

        <title>{{ config('app.name', 'Maintify') }} | @yield('title', 'Selamat Datang')</title>

        <!-- Preconnect -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">

        <!-- Auth Page Wrapper -->
        <div class="auth-container">

            <!-- Left Panel - Branding (hidden on mobile) -->
            <div class="hidden lg:flex flex-col justify-between w-1/2 max-w-lg p-12 text-white">
                <!-- Logo -->
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-lg">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2L3 7L12 12L21 7L12 2Z" fill="#410008"/>
                            <path d="M3 17L12 22L21 17" stroke="#410008" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M3 12L12 17L21 12" stroke="#410008" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <span class="text-2xl font-bold tracking-tight">Maintify</span>
                </div>

                <!-- Hero Text -->
                <div class="space-y-4">
                    <h1 class="text-4xl font-bold leading-tight">
                        Platform Digital<br>
                        Histori Service<br>
                        Kendaraan Motor
                    </h1>
                    <p class="text-white/70 text-lg leading-relaxed">
                        Kelola, pantau, dan catat semua histori servis kendaraan Anda secara digital dengan QR Code unik.
                    </p>
                </div>

                <!-- Feature Pills -->
                <div class="flex flex-wrap gap-2">
                    @foreach(['QR Code Unik', 'Histori Service', 'Health Monitoring', 'Multi Bengkel'] as $feature)
                        <span class="px-3 py-1.5 bg-white/10 border border-white/20 rounded-full text-sm font-medium backdrop-blur-sm">
                            {{ $feature }}
                        </span>
                    @endforeach
                </div>
            </div>

            <!-- Right Panel - Auth Card -->
            <div class="flex items-center justify-center w-full lg:w-auto px-4">
                <div class="auth-card fade-in w-full max-w-md">

                    <!-- Mobile Logo -->
                    <div class="flex items-center gap-2 mb-6 lg:hidden">
                        <div class="w-8 h-8 bg-[#410008] rounded-lg flex items-center justify-center">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 2L3 7L12 12L21 7L12 2Z" fill="white"/>
                                <path d="M3 17L12 22L21 17" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M3 12L12 17L21 12" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <span class="text-xl font-bold text-[#410008]">Maintify</span>
                    </div>

                    {{ $slot }}
                </div>
            </div>
        </div>

    </body>
</html>
