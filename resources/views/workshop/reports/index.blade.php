<x-app-layout>
    <x-slot name="pageTitle">Laporan Operasional</x-slot>
    <x-slot name="breadcrumb">Analisis & Ekspor Data Bengkel</x-slot>

    <div class="card" style="padding: 40px; text-align: center; max-width: 600px; margin: 40px auto;">
        <div style="background-color: rgba(59, 130, 246, 0.1); color: #60a5fa; width: 64px; height: 64px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
            <svg style="width: 32px; height: 32px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
        </div>
        <h2 style="font-size: 20px; font-weight: 700; color: #F4F4F5; margin-bottom: 8px;">Modul Laporan Operasional</h2>
        <p style="font-size: 14px; color: #A1A1AA; line-height: 1.5; margin-bottom: 24px;">
            Halaman ini akan segera hadir pada Sprint berikutnya (Task 9.3.1) untuk menyediakan laporan bulanan lengkap, analisis tren servis, performa mekanik, serta fitur ekspor laporan ke format CSV dan PDF.
        </p>
        <a href="{{ route('dashboard') }}" class="btn" style="background-color: #410008; color: #fff; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-block;">
            Kembali ke Dashboard
        </a>
    </div>
</x-app-layout>
