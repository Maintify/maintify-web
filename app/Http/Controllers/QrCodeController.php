<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Services\QrCodeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class QrCodeController extends Controller
{
    /**
     * Display the vehicle's QR Code page.
     */
    public function show(Vehicle $vehicle): View
    {
        // Ensure only the owner can view
        if ($vehicle->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke halaman QR Code ini.');
        }

        return view('vehicles.qr-code', compact('vehicle'));
    }

    /**
     * Download the QR Code image.
     */
    public function download(Vehicle $vehicle): StreamedResponse|RedirectResponse
    {
        // Ensure only the owner can download
        if ($vehicle->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke halaman QR Code ini.');
        }

        if (! $vehicle->qr_code_url) {
            return redirect()->back()->with('error', 'QR Code belum tersedia.');
        }

        $relativePath = str_replace('/storage/', '', $vehicle->qr_code_url);

        if (! Storage::disk('public')->exists($relativePath)) {
            return redirect()->back()->with('error', 'File QR Code tidak ditemukan.');
        }

        $extension = pathinfo($relativePath, PATHINFO_EXTENSION);
        $downloadName = "QR_{$vehicle->plate_number}.{$extension}";

        return Storage::disk('public')->download($relativePath, $downloadName);
    }

    /**
     * Regenerate the QR Code for the vehicle.
     */
    public function regenerate(Vehicle $vehicle, QrCodeService $qrCodeService): RedirectResponse
    {
        // Ensure only the owner can regenerate
        if ($vehicle->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses untuk membuat ulang QR Code ini.');
        }

        $qrCodeService->regenerateForVehicle($vehicle);

        return redirect()->route('vehicles.qr.show', $vehicle)
            ->with('success', 'Digital ID QR Code berhasil dibuat ulang. Kode lama telah hangus.');
    }
}
