document.addEventListener('DOMContentLoaded', () => {
    const scannerContainer = document.getElementById('qr-reader');
    const startBtn = document.getElementById('start-scan-btn');
    const stopBtn = document.getElementById('stop-scan-btn');
    const statusText = document.getElementById('scanner-status');
    const resultModal = document.getElementById('scan-result-modal');
    const closeModalBtn = document.getElementById('close-modal-btn');
    
    // Result elements
    const resBrandModel = document.getElementById('res-brand-model');
    const resPlate = document.getElementById('res-plate');
    const resVin = document.getElementById('res-vin');
    const resOwner = document.getElementById('res-owner');
    const resOdometer = document.getElementById('res-odometer');
    const resOilLife = document.getElementById('res-oil-life');
    const resHealth = document.getElementById('res-health');
    const resActionBtn = document.getElementById('res-action-btn');

    let html5QrCode = null;
    let isScanning = false;

    // Initialize html5-qrcode
    if (scannerContainer) {
        html5QrCode = new Html5Qrcode("qr-reader");
    }

    const startScanner = () => {
        if (!html5QrCode) return;
        
        statusText.textContent = "Meminta akses kamera...";
        statusText.className = "text-yellow-500 font-medium text-sm animate-pulse";
        
        // Start scanner with environment (back) camera preferred
        html5QrCode.start(
            { facingMode: "environment" },
            {
                fps: 10,
                qrbox: (width, height) => {
                    const size = Math.min(width, height) * 0.7;
                    return { width: size, height: size };
                }
            },
            (decodedText, decodedResult) => {
                // Successful QR Scan
                handleScanSuccess(decodedText);
            },
            (errorMessage) => {
                // Verbose log: camera scanning frames
            }
        ).then(() => {
            isScanning = true;
            statusText.textContent = "Kamera aktif. Silakan scan QR Code.";
            statusText.className = "text-emerald-500 font-medium text-sm";
            startBtn.classList.add('hidden');
            stopBtn.classList.remove('hidden');
        }).catch((err) => {
            console.error("Gagal memulai scanner: ", err);
            statusText.textContent = "Gagal mengakses kamera. Pastikan izin diberikan.";
            statusText.className = "text-rose-500 font-medium text-sm";
        });
    };

    const stopScanner = () => {
        if (html5QrCode && isScanning) {
            html5QrCode.stop().then(() => {
                isScanning = false;
                statusText.textContent = "Scanner dinonaktifkan.";
                statusText.className = "text-zinc-500 text-sm";
                startBtn.classList.remove('hidden');
                stopBtn.classList.add('hidden');
            }).catch((err) => {
                console.error("Gagal menghentikan scanner: ", err);
            });
        }
    };

    const showToast = (message, type = 'error') => {
        // Simple elegant toast function
        const toast = document.createElement('div');
        toast.className = `fixed bottom-4 right-4 z-50 flex items-center gap-2 px-4 py-3 rounded-lg shadow-xl text-sm border font-medium transition-all transform translate-y-10 opacity-0`;
        
        if (type === 'error') {
            toast.style.backgroundColor = '#1C0003';
            toast.style.borderColor = '#4A000A';
            toast.style.color = '#FDA4AF';
        } else {
            toast.style.backgroundColor = '#022C22';
            toast.style.borderColor = '#064E3B';
            toast.style.color = '#6EE7B7';
        }

        toast.innerHTML = `
            <svg class="w-5 height-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${type === 'error' ? 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z' : 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'}"/>
            </svg>
            <span>${message}</span>
        `;
        
        document.body.appendChild(toast);
        
        // Trigger animation
        setTimeout(() => {
            toast.classList.remove('translate-y-10', 'opacity-0');
        }, 10);

        setTimeout(() => {
            toast.classList.add('translate-y-10', 'opacity-0');
            setTimeout(() => toast.remove(), 300);
        }, 4000);
    };

    const handleScanSuccess = (decodedText) => {
        // Pause or stop scanning immediately to prevent duplicate scans
        stopScanner();

        // Check if URL and extract token or use string directly
        let token = decodedText;
        try {
            if (decodedText.includes('/qr/resolve/')) {
                const parts = decodedText.split('/qr/resolve/');
                token = parts[parts.length - 1];
            }
        } catch (e) {
            console.error(e);
        }

        statusText.textContent = "Memverifikasi QR Code...";
        statusText.className = "text-amber-500 font-medium text-sm animate-pulse";

        // Perform AJAX request to verify token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch('/workshop/scan/resolve', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ qr_token: token })
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw err; });
            }
            return response.json();
        })
        .then(data => {
            if (data.status === 'success') {
                showToast("QR Code berhasil diverifikasi!", "success");
                displayVehicleData(data.data);
            } else {
                throw data;
            }
        })
        .catch(err => {
            const message = err.message || "Gagal memproses QR Code";
            showToast(message, "error");
            statusText.textContent = message;
            statusText.className = "text-rose-500 font-medium text-sm";
            
            // Re-enable start button
            startBtn.classList.remove('hidden');
        });
    };

    const displayVehicleData = (vehicle) => {
        // Populate modal fields
        resBrandModel.textContent = `${vehicle.brand} ${vehicle.model} (${vehicle.year})`;
        resPlate.textContent = vehicle.plate_number;
        resVin.textContent = vehicle.vin || '-';
        resOwner.textContent = vehicle.owner_name;
        resOdometer.textContent = `${vehicle.current_odometer.toLocaleString('id-ID')} km`;
        resOilLife.textContent = `${vehicle.oil_life_percentage}%`;
        
        // Health Status badge styling
        resHealth.textContent = vehicle.health_status.toUpperCase();
        if (vehicle.health_status === 'good') {
            resHealth.className = "px-2 py-0.5 rounded text-xs font-semibold bg-emerald-950 text-emerald-300 border border-emerald-800";
        } else if (vehicle.health_status === 'needs_service') {
            resHealth.className = "px-2 py-0.5 rounded text-xs font-semibold bg-amber-950 text-amber-300 border border-amber-800";
        } else {
            resHealth.className = "px-2 py-0.5 rounded text-xs font-semibold bg-rose-950 text-rose-300 border border-rose-800";
        }

        // Set action button link to add service record
        resActionBtn.href = `/workshop/service-records/create?vehicle_id=${vehicle.vehicle_id}`;

        // Show modal
        resultModal.classList.remove('hidden');
        resultModal.classList.add('flex');
    };

    // Event Listeners
    if (startBtn) {
        startBtn.addEventListener('click', startScanner);
    }

    if (stopBtn) {
        stopBtn.addEventListener('click', stopScanner);
    }

    if (closeModalBtn) {
        closeModalBtn.addEventListener('click', () => {
            resultModal.classList.add('hidden');
            resultModal.classList.remove('flex');
            // Reset status
            statusText.textContent = "Scanner dinonaktifkan.";
            statusText.className = "text-zinc-500 text-sm";
            startBtn.classList.remove('hidden');
            stopBtn.classList.add('hidden');
        });
    }
});
