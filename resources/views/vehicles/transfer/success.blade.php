<x-app-layout>
    @slot('pageTitle', 'Transfer Berhasil')

    <div style="max-width: 600px; margin: 60px auto 0; padding: 24px 16px; text-align: center;">
        
        <div style="width: 80px; height: 80px; border-radius: 50%; background: rgba(34, 197, 94, 0.1); border: 2px solid #22c55e; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px;">
            <svg style="width: 40px; height: 40px; color: #22c55e;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>

        <h1 style="font-size: 24px; font-weight: 800; color: #F4F4F5; margin: 0 0 12px;">Transfer Berhasil Diselesaikan</h1>
        
        <p style="font-size: 14px; color: #A1A1AA; line-height: 1.6; margin: 0 0 32px;">
            Kepemilikan kendaraan <strong>{{ $transfer->vehicle->brand }} {{ $transfer->vehicle->model }}</strong> ({{ $transfer->vehicle->plate_number }}) telah resmi dipindahkan ke <strong>{{ $transfer->toUser->name }}</strong>. Anda tidak lagi memiliki akses ke kendaraan ini maupun riwayat servisnya.
        </p>

        <a href="{{ route('dashboard') }}" style="display: inline-flex; align-items: center; justify-content: center; padding: 14px 28px; background-color: #F4F4F5; border-radius: 12px; color: #121414; font-size: 14px; font-weight: 700; text-decoration: none; transition: background-color 150ms;" onmouseover="this.style.backgroundColor='#E4E4E7'" onmouseout="this.style.backgroundColor='#F4F4F5'">
            Kembali ke Garasi
        </a>

    </div>
</x-app-layout>
