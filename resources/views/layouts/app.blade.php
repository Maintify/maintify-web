<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="{{ $metaDescription ?? 'Maintify - Platform Digital Histori Service Kendaraan Motor berbasis QR Code' }}">
        <meta name="theme-color" content="#121414">

        <title>{{ config('app.name', 'Maintify') }} | {{ $pageTitle ?? 'Dashboard' }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3e%3cpath d='M12 2L3 7L12 12L21 7L12 2Z' fill='%23410008'/%3e%3cpath d='M3 17L12 22L21 17' stroke='%23410008' stroke-width='2'/%3e%3c/svg%3e">

        <!-- Google Fonts - Inter -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Additional Head Content -->
        @stack('head')
    </head>
    <body class="font-sans antialiased no-tap-highlight" style="background-color:#121414;color:#F4F4F5;">

        {{-- ============================================================
             APP SHELL
             ============================================================ --}}
        <div class="app-shell" id="app-shell">

            {{-- =====================
                 SIDEBAR OVERLAY (Mobile)
                 ===================== --}}
            <div class="sidebar-overlay" id="sidebar-overlay" onclick="closeSidebar()"></div>

            {{-- =====================
                 SIDEBAR
                 ===================== --}}
            <aside class="sidebar" id="sidebar" role="navigation" aria-label="Main Navigation">

                {{-- Sidebar Header --}}
                <div class="sidebar-header">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                        <div class="sidebar-logo">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 2L3 7L12 12L21 7L12 2Z" fill="white"/>
                                <path d="M3 17L12 22L21 17" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M3 12L12 17L21 12" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <span class="sidebar-brand">Maintify</span>
                    </a>
                </div>

                {{-- Sidebar Navigation --}}
                <nav class="sidebar-nav" aria-label="Sidebar navigation">

                    @auth
                        @php
                            $userRole = Auth::user()->role ?? 'vehicle_owner';
                            $firstVehicle = Auth::user()->vehicles()->first();
                            $serviceHistoryUrl = $firstVehicle ? route('vehicles.service-history', $firstVehicle) : route('vehicles.index');
                        @endphp

                        @if($userRole === 'vehicle_owner')
                            {{-- Vehicle Owner Navigation --}}
                            <div class="sidebar-nav-section">
                                <p class="sidebar-nav-label">Menu Utama</p>

                                <a href="{{ route('dashboard') }}"
                                   id="nav-dashboard"
                                   class="sidebar-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                                    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                    </svg>
                                    <span>Beranda</span>
                                </a>

                                <a href="{{ route('vehicles.index') }}"
                                   id="nav-vehicles"
                                   class="sidebar-nav-item {{ request()->routeIs('vehicles*') ? 'active' : '' }}">
                                    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2 2h8l2-2zM8 6h4l2 4H6l2-4z"/>
                                    </svg>
                                    <span>Kendaraan Saya</span>
                                </a>

                                <a href="{{ route('vehicles.index') }}"
                                   id="nav-qr"
                                   class="sidebar-nav-item {{ request()->routeIs('vehicles.qr*') ? 'active' : '' }}">
                                    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 3h7v7H3zM14 3h7v7h-7zM3 14h7v7H3z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14 14h3v3m0 4h3m-3 0v-4"/>
                                    </svg>
                                    <span>QR Code</span>
                                </a>

                                <a href="{{ $serviceHistoryUrl }}"
                                   id="nav-service"
                                   class="sidebar-nav-item {{ request()->routeIs('vehicles.service-history') ? 'active' : '' }}">
                                    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                                    </svg>
                                    <span>Riwayat Service</span>
                                </a>

                                <a href="{{ route('workshops.nearby') }}"
                                   id="nav-workshop"
                                   class="sidebar-nav-item {{ request()->routeIs('workshops.nearby') ? 'active' : '' }}">
                                    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <span>Bengkel Terdekat</span>
                                </a>
                            </div>

                        @elseif($userRole === 'workshop')
                            {{-- Workshop Navigation --}}
                            <div class="sidebar-nav-section">
                                <p class="sidebar-nav-label">Workshop</p>

                                <a href="{{ route('dashboard') }}" id="nav-ws-dashboard"
                                   class="sidebar-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                                    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                    </svg>
                                    <span>Dashboard</span>
                                </a>

                                 <a href="{{ route('workshop.scan') }}" id="nav-scan"
                                    class="sidebar-nav-item {{ request()->routeIs('workshop.scan*') ? 'active' : '' }}">
                                     <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8H3a2 2 0 00-2 2v6a2 2 0 002 2h2m2-12V4a2 2 0 012-2h4a2 2 0 012 2v1"/>
                                     </svg>
                                     <span>Scan QR</span>
                                 </a>

                                 <a href="{{ route('workshop.spareparts.index') }}" id="nav-ws-spareparts"
                                    class="sidebar-nav-item {{ request()->routeIs('workshop.spareparts*') ? 'active' : '' }}">
                                     <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                     </svg>
                                     <span>Katalog Sparepart</span>
                                 </a>

                                 <a href="{{ route('workshop.customers.index') }}" id="nav-ws-customers"
                                    class="sidebar-nav-item {{ request()->routeIs('workshop.customers*') ? 'active' : '' }}">
                                     <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                     </svg>
                                     <span>Daftar Pelanggan</span>
                                 </a>

                                 @if(Auth::user()->workshop)
                                     <a href="{{ route('workshop.staff.index') }}" id="nav-ws-staff"
                                        class="sidebar-nav-item {{ request()->routeIs('workshop.staff*') ? 'active' : '' }}">
                                         <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                         </svg>
                                         <span>Kelola Staf</span>
                                     </a>
                                     <a href="{{ route('workshop.profile.edit') }}" id="nav-ws-profile"
                                        class="sidebar-nav-item {{ request()->routeIs('workshop.profile*') ? 'active' : '' }}">
                                         <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                         </svg>
                                         <span>Profil Bengkel</span>
                                     </a>
                                     <a href="{{ route('workshop.reports.index') }}" id="nav-ws-reports"
                                        class="sidebar-nav-item {{ request()->routeIs('workshop.reports*') ? 'active' : '' }}">
                                         <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                         </svg>
                                         <span>Laporan Operasional</span>
                                     </a>
                                 @endif

                                <a href="#" id="nav-ws-service"
                                   class="sidebar-nav-item {{ request()->routeIs('workshop.service*') ? 'active' : '' }}">
                                    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    <span>Service Records</span>
                                </a>
                            </div>

                        @elseif($userRole === 'super_admin')
                            {{-- Super Admin Navigation --}}
                            <div class="sidebar-nav-section">
                                <p class="sidebar-nav-label">Admin Panel</p>

                                <a href="{{ route('dashboard') }}" id="nav-admin-dashboard"
                                   class="sidebar-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                                    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                    </svg>
                                    <span>Dashboard</span>
                                </a>

                                <a href="{{ route('admin.users.index') }}" id="nav-admin-users"
                                   class="sidebar-nav-item {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                                    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <span>User Management</span>
                                </a>

                                <a href="{{ route('admin.workshops.index') }}" id="nav-admin-workshops"
                                   class="sidebar-nav-item {{ request()->routeIs('admin.workshops*') ? 'active' : '' }}">
                                    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    <span>Workshop Management</span>
                                </a>

                                <a href="{{ route('admin.vehicles.index') }}" id="nav-admin-vehicles"
                                   class="sidebar-nav-item {{ request()->routeIs('admin.vehicles*') ? 'active' : '' }}">
                                    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2 2h8l2-2zM8 6h4l2 4H6l2-4z"/>
                                    </svg>
                                    <span>Vehicle Monitoring</span>
                                </a>

                                <a href="#" id="nav-admin-analytics"
                                   class="sidebar-nav-item {{ request()->routeIs('admin.analytics*') ? 'active' : '' }}">
                                    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                    <span>Analytics</span>
                                </a>

                                <a href="{{ route('admin.audit-logs.index') }}" id="nav-admin-audit-logs"
                                   class="sidebar-nav-item {{ request()->routeIs('admin.audit-logs*') ? 'active' : '' }}">
                                    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                                    </svg>
                                    <span>Audit Logs</span>
                                </a>

                                <a href="{{ route('admin.settings.index') }}" id="nav-admin-settings"
                                   class="sidebar-nav-item {{ request()->routeIs('admin.settings*') ? 'active' : '' }}">
                                    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <span>Global Settings</span>
                                </a>
                            </div>
                        @endif

                        {{-- Common Bottom Items --}}
                        <div class="sidebar-nav-section">
                            <p class="sidebar-nav-label">Akun</p>

                            <a href="{{ route('profile.edit') }}" id="nav-profile"
                               class="sidebar-nav-item {{ request()->routeIs('profile*') ? 'active' : '' }}">
                                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <span>Profile</span>
                            </a>

                            <a href="#" id="nav-settings"
                               class="sidebar-nav-item {{ request()->routeIs('settings*') ? 'active' : '' }}">
                                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span>Settings</span>
                            </a>
                        </div>
                    @endauth
                </nav>

                {{-- Sidebar Footer - User Info + Logout --}}
                @auth
                <div class="sidebar-footer">
                    <div class="flex items-center gap-3">
                        <div class="avatar-sm flex-shrink-0" style="background-color:#410008;border-radius:50%;display:flex;align-items:center;justify-content:center;width:36px;height:36px;">
                            <span style="color:#fff;font-size:14px;font-weight:600;">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p style="font-size:13px;font-weight:600;color:#F4F4F5;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                {{ Auth::user()->name }}
                            </p>
                            <p style="font-size:11px;color:#71717A;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                {{ Auth::user()->email }}
                            </p>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" id="sidebar-logout-btn"
                                class="btn-icon btn-ghost"
                                title="Logout"
                                style="padding:6px;">
                                <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
                @endauth
            </aside>

            {{-- =====================
                 MAIN WRAPPER
                 ===================== --}}
            <div class="main-wrapper">

                {{-- =====================
                     TOPBAR
                     ===================== --}}
                <header class="topbar" id="topbar" role="banner">
                    <div class="topbar-left">
                        {{-- Hamburger (Mobile) --}}
                        <button
                            id="sidebar-toggle"
                            class="btn-icon btn-ghost lg:hidden"
                            onclick="toggleSidebar()"
                            aria-label="Toggle Navigation"
                            aria-expanded="false"
                            aria-controls="sidebar">
                            <svg style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>

                        {{-- Page Title / Breadcrumb --}}
                        <div>
                            @isset($pageTitle)
                                <h1 style="font-size:16px;font-weight:600;color:#F4F4F5;line-height:1.2;">{{ $pageTitle }}</h1>
                            @endisset
                            @isset($breadcrumb)
                                <p style="font-size:12px;color:#71717A;">{{ $breadcrumb }}</p>
                            @endisset
                        </div>
                    </div>

                    <div class="topbar-right">
                        {{-- Notifications --}}
                        <x-notification-bell />

                        {{-- User Avatar Dropdown --}}
                        @auth
                        <div class="relative" x-data="{ open: false }">
                            <button
                                @click="open = !open"
                                id="topbar-user-menu"
                                class="flex items-center gap-2 px-2 py-1.5 rounded-lg transition-colors"
                                style="color:#A1A1AA;"
                                :style="open ? 'background-color:#2A2D2D;color:#F4F4F5;' : ''"
                                @mouseenter="$el.style.backgroundColor='#2A2D2D';$el.style.color='#F4F4F5'"
                                @mouseleave="!open && ($el.style.backgroundColor='transparent') && ($el.style.color='#A1A1AA')"
                                aria-expanded="false"
                                aria-haspopup="true">
                                <div class="avatar-sm" style="background-color:#410008;border-radius:50%;display:flex;align-items:center;justify-content:center;width:32px;height:32px;">
                                    <span style="color:#fff;font-size:13px;font-weight:600;">
                                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                    </span>
                                </div>
                                <svg style="width:14px;height:14px;" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24" class="transition-transform">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            {{-- Dropdown --}}
                            <div x-show="open"
                                 @click.away="open = false"
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 translate-y-1"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-100"
                                 x-transition:leave-start="opacity-100 translate-y-0"
                                 x-transition:leave-end="opacity-0 translate-y-1"
                                 class="absolute right-0 mt-2 w-52 rounded-xl py-1.5 z-50"
                                 style="background-color:#252828;border:1px solid #2E3030;box-shadow:0 8px 32px rgba(0,0,0,0.4);top:100%;">

                                <div class="px-3 py-2" style="border-bottom:1px solid #2E3030;">
                                    <p style="font-size:13px;font-weight:600;color:#F4F4F5;">{{ Auth::user()->name }}</p>
                                    <p style="font-size:11px;color:#71717A;">{{ Auth::user()->email }}</p>
                                </div>

                                <a href="{{ route('profile.edit') }}"
                                   id="topbar-profile-link"
                                   class="flex items-center gap-2.5 px-3 py-2 text-sm transition-colors"
                                   style="color:#A1A1AA;"
                                   @mouseenter="$el.style.backgroundColor='#2A2D2D';$el.style.color='#F4F4F5'"
                                   @mouseleave="$el.style.backgroundColor='';$el.style.color='#A1A1AA'">
                                    <svg style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    Profile
                                </a>

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                            id="topbar-logout-btn"
                                            class="flex items-center gap-2.5 px-3 py-2 text-sm w-full transition-colors"
                                            style="color:#A1A1AA;"
                                            @mouseenter="$el.style.backgroundColor='#2A2D2D';$el.style.color='#F4F4F5'"
                                            @mouseleave="$el.style.backgroundColor='';$el.style.color='#A1A1AA'">
                                        <svg style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                        </svg>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                        @endauth
                    </div>
                </header>

                {{-- =====================
                     PAGE CONTENT
                     ===================== --}}
                <main class="page-content" id="page-content" role="main">
                    {{ $slot }}
                </main>

                {{-- =====================
                     BOTTOM NAVIGATION (Mobile)
                     ===================== --}}
                @auth
                @php $userRoleNav = Auth::user()->role ?? 'user'; @endphp
                <nav class="bottom-nav" id="bottom-nav" role="navigation" aria-label="Bottom Navigation">

                    @if($userRoleNav === 'user' || $userRoleNav === 'vehicle_owner')
                        <a href="{{ route('dashboard') }}"
                           id="bottom-nav-home"
                           class="bottom-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                           aria-label="Beranda">
                            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            <span>Beranda</span>
                        </a>

                        <a href="{{ route('vehicles.index') }}"
                           id="bottom-nav-vehicle"
                           class="bottom-nav-item {{ request()->routeIs('vehicles*') ? 'active' : '' }}"
                           aria-label="My Vehicle">
                            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2 2h8l2-2zM8 6h4l2 4H6l2-4z"/>
                            </svg>
                            <span>Kendaraan</span>
                        </a>

                        <a href="#"
                           id="bottom-nav-qr"
                           class="bottom-nav-item {{ request()->routeIs('qr*') ? 'active' : '' }}"
                           aria-label="QR Code">
                            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 3h7v7H3zM14 3h7v7h-7zM3 14h7v7H3z"/>
                                <rect x="16" y="16" width="2" height="2" fill="currentColor"/>
                                <rect x="20" y="20" width="2" height="2" fill="currentColor"/>
                                <rect x="16" y="20" width="2" height="2" fill="currentColor"/>
                                <rect x="20" y="16" width="2" height="2" fill="currentColor"/>
                            </svg>
                            <span>QR</span>
                        </a>

                        <a href="{{ $serviceHistoryUrl }}"
                           id="bottom-nav-service"
                           class="bottom-nav-item {{ request()->routeIs('vehicles.service-history') ? 'active' : '' }}"
                           aria-label="Riwayat Service">
                            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <span>Riwayat</span>
                        </a>

                        <a href="{{ route('profile.edit') }}"
                           id="bottom-nav-profile"
                           class="bottom-nav-item {{ request()->routeIs('profile*') ? 'active' : '' }}"
                           aria-label="Profile">
                            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <span>Profil</span>
                        </a>

                    @elseif($userRoleNav === 'workshop')
                        <a href="{{ route('dashboard') }}" id="bottom-nav-ws-home"
                           class="bottom-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            <span>Dashboard</span>
                        </a>
                        <a href="{{ route('workshop.scan') }}" id="bottom-nav-scan"
                           class="bottom-nav-item {{ request()->routeIs('workshop.scan*') ? 'active' : '' }}">
                            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 3h7v7H3zM14 3h7v7h-7zM3 14h7v7H3z"/>
                            </svg>
                            <span>Scan</span>
                        </a>
                        <a href="{{ route('profile.edit') }}" id="bottom-nav-ws-profile"
                           class="bottom-nav-item {{ request()->routeIs('profile*') ? 'active' : '' }}">
                            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <span>Profil</span>
                        </a>
                    @endif

                </nav>
                @endauth

            </div>{{-- /main-wrapper --}}
        </div>{{-- /app-shell --}}

        {{-- =====================
             MOBILE SIDEBAR JAVASCRIPT
             ===================== --}}
        <script>
            function toggleSidebar() {
                const sidebar  = document.getElementById('sidebar');
                const overlay  = document.getElementById('sidebar-overlay');
                const toggle   = document.getElementById('sidebar-toggle');
                const isOpen   = sidebar.classList.contains('open');

                if (isOpen) {
                    closeSidebar();
                } else {
                    sidebar.classList.add('open');
                    overlay.classList.add('active');
                    toggle.setAttribute('aria-expanded', 'true');
                    document.body.style.overflow = 'hidden';
                }
            }

            function closeSidebar() {
                const sidebar = document.getElementById('sidebar');
                const overlay = document.getElementById('sidebar-overlay');
                const toggle  = document.getElementById('sidebar-toggle');

                sidebar.classList.remove('open');
                overlay.classList.remove('active');
                if (toggle) toggle.setAttribute('aria-expanded', 'false');
                document.body.style.overflow = '';
            }

            // Close sidebar on resize to desktop
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 1024) { closeSidebar(); }
            });
        </script>

        @stack('scripts')
        <x-toast-notification />
    </body>
</html>
