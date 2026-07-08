<?php

namespace App\Http\Controllers;

use App\Http\Requests\InitiateTransferRequest;
use App\Models\AuditLog;
use App\Models\Notification;
use App\Models\OwnershipTransfer;
use App\Models\User;
use App\Models\Vehicle;
use App\Services\OwnershipTransferService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OwnershipTransferController extends Controller
{
    public function __construct(
        protected OwnershipTransferService $transferService,
    ) {}

    /**
     * Show the transfer initiation form with vehicle summary.
     *
     * Acceptance Criteria (Task 8.1.1):
     * - Vehicle summary displayed. (FR-063)
     * - Only the current owner can access this form.
     * - Blocks if a pending transfer already exists.
     */
    public function create(Vehicle $vehicle): View|RedirectResponse
    {
        // Verify ownership
        if ($vehicle->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke kendaraan ini.');
        }

        // Check for existing pending transfer
        $pendingTransfer = OwnershipTransfer::where('vehicle_id', $vehicle->id)
            ->where('status', OwnershipTransfer::STATUS_PENDING_RECIPIENT)
            ->first();

        if ($pendingTransfer) {
            return redirect()->route('vehicles.show', $vehicle)
                ->with('error', 'Kendaraan ini sudah memiliki permintaan transfer yang sedang menunggu persetujuan.');
        }

        return view('vehicles.transfer.initiate', compact('vehicle'));
    }

    /**
     * Process the transfer initiation.
     *
     * Acceptance Criteria (Task 8.1.1):
     * - Recipient identified by email or phone. (FR-064)
     * - System verifies recipient has a Maintify account. (FR-065)
     * - If recipient not found, suggest inviting them to register.
     * - Transfer record created with status `pending_recipient`.
     * - Notification sent to recipient.
     */
    public function store(InitiateTransferRequest $request, Vehicle $vehicle): RedirectResponse
    {
        // Verify ownership
        if ($vehicle->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke kendaraan ini.');
        }

        // Check for existing pending transfer
        $pendingTransfer = OwnershipTransfer::where('vehicle_id', $vehicle->id)
            ->where('status', OwnershipTransfer::STATUS_PENDING_RECIPIENT)
            ->first();

        if ($pendingTransfer) {
            return redirect()->route('vehicles.show', $vehicle)
                ->with('error', 'Kendaraan ini sudah memiliki permintaan transfer yang sedang menunggu persetujuan.');
        }

        // Lookup recipient by email or phone
        $identifier = $request->recipient_identifier;
        $recipient = null;

        if ($request->isEmail()) {
            $recipient = User::where('email', $identifier)->first();
        } elseif ($request->isPhone()) {
            // Normalize phone number: strip spaces/dashes
            $normalizedPhone = preg_replace('/[\s\-]/', '', $identifier);
            $recipient = User::where('phone_number', $normalizedPhone)->first();
        } else {
            // Try email first, then phone as fallback
            $recipient = User::where('email', $identifier)->first();
            if (! $recipient) {
                $normalizedPhone = preg_replace('/[\s\-]/', '', $identifier);
                $recipient = User::where('phone_number', $normalizedPhone)->first();
            }
        }

        // Recipient not found — suggest invite
        if (! $recipient) {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'recipient_identifier' => 'Penerima tidak ditemukan di sistem Maintify. Pastikan email atau nomor telepon sudah benar, atau ajak mereka untuk mendaftar terlebih dahulu.',
                ]);
        }

        // Cannot transfer to self
        if ($recipient->id === auth()->id()) {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'recipient_identifier' => 'Anda tidak dapat mentransfer kendaraan ke diri sendiri.',
                ]);
        }

        // Create transfer record
        $transfer = OwnershipTransfer::create([
            'vehicle_id' => $vehicle->id,
            'from_user_id' => auth()->id(),
            'to_user_id' => $recipient->id,
            'status' => OwnershipTransfer::STATUS_PENDING_RECIPIENT,
            'requested_at' => now(),
            'expires_at' => now()->addDays(7),
        ]);

        // Send notification to recipient
        Notification::create([
            'user_id' => $recipient->id,
            'type' => 'transfer_request',
            'title' => 'Permintaan Transfer Kendaraan',
            'message' => auth()->user()->name . ' ingin mentransfer kepemilikan kendaraan ' . $vehicle->brand . ' ' . $vehicle->model . ' (' . $vehicle->plate_number . ') kepada Anda. Silakan tinjau dan setujui permintaan ini.',
            'is_read' => false,
        ]);

        // Audit log
        AuditLog::record(
            'transfer_initiated',
            'OwnershipTransfer',
            $transfer->id,
            [
                'vehicle_id' => $vehicle->id,
                'from_user_id' => auth()->id(),
                'to_user_id' => $recipient->id,
                'recipient_identifier' => $identifier,
            ]
        );

        return redirect()->route('vehicles.show', $vehicle)
            ->with('success', 'Permintaan transfer kepemilikan berhasil dikirim ke ' . $recipient->name . '. Menunggu persetujuan penerima.');
    }

    /**
     * Recipient approves the transfer request.
     */
    public function approve(OwnershipTransfer $transfer): RedirectResponse
    {
        try {
            $this->transferService->approve($transfer, auth()->user());
            return redirect()->back()->with('success', 'Permintaan transfer berhasil disetujui. Menunggu konfirmasi akhir dari pemilik saat ini.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Recipient rejects the transfer request.
     */
    public function reject(OwnershipTransfer $transfer): RedirectResponse
    {
        try {
            $this->transferService->reject($transfer, auth()->user());
            return redirect()->back()->with('success', 'Permintaan transfer berhasil ditolak.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Owner reviews the approved transfer before final confirmation.
     */
    public function review(OwnershipTransfer $transfer): View|RedirectResponse
    {
        if ($transfer->from_user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke transfer ini.');
        }

        if ($transfer->status !== OwnershipTransfer::STATUS_APPROVED) {
            return redirect()->route('vehicles.show', $transfer->vehicle_id)
                ->with('error', 'Transfer ini belum disetujui atau sudah tidak valid.');
        }

        $vehicle = clone $transfer->vehicle;
        $recipient = $transfer->toUser;

        return view('vehicles.transfer.review', compact('transfer', 'vehicle', 'recipient'));
    }

    /**
     * Owner confirms the transfer, executing it.
     */
    public function confirm(Request $request, OwnershipTransfer $transfer): RedirectResponse
    {
        $request->validate([
            'disclaimer_agreed' => 'required|accepted',
        ], [
            'disclaimer_agreed.required' => 'Anda harus menyetujui pernyataan untuk melanjutkan.',
            'disclaimer_agreed.accepted' => 'Anda harus menyetujui pernyataan untuk melanjutkan.',
        ]);

        try {
            $disclaimerText = 'Saya menyatakan setuju untuk memindahkan kepemilikan kendaraan secara permanen. Seluruh data kendaraan dan riwayat servis akan menjadi hak milik penerima sepenuhnya. Saya memahami bahwa aksi ini bersifat final dan tidak dapat dibatalkan.';
            $this->transferService->confirm($transfer, auth()->user(), $disclaimerText);
            
            return redirect()->route('transfers.success', $transfer);
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Success page after confirmation.
     */
    public function success(OwnershipTransfer $transfer): View
    {
        if ($transfer->from_user_id !== auth()->id() && $transfer->to_user_id !== auth()->id()) {
            abort(403, 'Anda tidak berhak melihat halaman ini.');
        }

        return view('vehicles.transfer.success', compact('transfer'));
    }
}
