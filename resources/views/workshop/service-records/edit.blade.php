<x-app-layout>
    @slot('pageTitle', 'Ubah Service Record')
    @slot('breadcrumb', 'Workshop / Scan / Ubah Service')

    @push('head')
        <style>
            /* ── Part Row Animation ── */
            .part-row { animation: fadeSlideIn 0.25s ease-out; }
            @keyframes fadeSlideIn {
                from { opacity: 0; transform: translateY(-8px); }
                to   { opacity: 1; transform: translateY(0); }
            }

            /* ── Input Focus Glow ── */
            .form-input:focus { box-shadow: 0 0 0 3px rgba(239,68,68,0.15); }

            /* ── Summary Badge Pulse ── */
            .total-badge { transition: all 0.3s ease; }
        </style>
    @endpush

    <div class="max-w-4xl mx-auto px-4 py-8">

        {{-- ── Header ── --}}
        <div class="flex items-center gap-4 mb-8">
            <a href="{{ route('workshop.scan') }}"
               class="flex items-center justify-center w-10 h-10 rounded-xl bg-zinc-800 border border-zinc-700 hover:border-zinc-500 transition-colors">
                <svg class="w-5 h-5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-zinc-100 tracking-tight">Ubah Service Record</h1>
                <p class="text-sm text-zinc-500 mt-0.5">Perbarui detail service untuk kendaraan di bawah ini</p>
            </div>
        </div>

        {{-- ── Vehicle Summary Card ── --}}
        <div class="bg-[#1A1C1C] border border-[#2E3030] rounded-2xl p-5 mb-6 flex items-center gap-5">
            <div class="flex-shrink-0 w-14 h-14 rounded-xl bg-red-950/30 border border-red-900/40 flex items-center justify-center">
                <svg class="w-7 h-7 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-base font-bold text-zinc-100">{{ $vehicle->brand }} {{ $vehicle->model }} ({{ $vehicle->year }})</p>
                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-1">
                    <span class="text-xs font-semibold text-red-400 bg-red-950/30 border border-red-900/40 px-2 py-0.5 rounded">{{ $vehicle->plate_number }}</span>
                    <span class="text-xs text-zinc-500">Pemilik: <span class="text-zinc-300 font-medium">{{ $vehicle->owner?->name ?? 'N/A' }}</span></span>
                    <span class="text-xs text-zinc-500">Odometer: <span class="text-zinc-300 font-medium">{{ number_format($vehicle->current_odometer) }} km</span></span>
                </div>
            </div>
            {{-- Health Badge --}}
            <div class="flex-shrink-0">
                @php
                    $healthColor = match($vehicle->health_status) {
                        'good'     => ['bg' => 'rgba(34,197,94,0.1)', 'border' => 'rgba(34,197,94,0.25)', 'text' => '#4ade80', 'dot' => '#22c55e'],
                        'warning'  => ['bg' => 'rgba(245,158,11,0.1)', 'border' => 'rgba(245,158,11,0.25)', 'text' => '#fbbf24', 'dot' => '#f59e0b'],
                        'critical' => ['bg' => 'rgba(239,68,68,0.1)',   'border' => 'rgba(239,68,68,0.25)',   'text' => '#f87171', 'dot' => '#ef4444'],
                        default    => ['bg' => 'rgba(113,113,122,0.1)', 'border' => 'rgba(113,113,122,0.25)', 'text' => '#a1a1aa', 'dot' => '#71717a'],
                    };
                    $healthLabel = match($vehicle->health_status) {
                        'good' => 'Baik', 'warning' => 'Peringatan', 'critical' => 'Kritis', default => ucfirst($vehicle->health_status ?? '-'),
                    };
                @endphp
                <span style="display:inline-flex;align-items:center;gap:5px;padding:5px 12px;background:{{ $healthColor['bg'] }};border:1px solid {{ $healthColor['border'] }};border-radius:100px;font-size:12px;font-weight:600;color:{{ $healthColor['text'] }};">
                    <span style="width:6px;height:6px;border-radius:50%;background:{{ $healthColor['dot'] }};"></span>
                    {{ $healthLabel }}
                </span>
            </div>
        </div>

        {{-- ── Form ── --}}
        <form id="service-record-form"
              method="POST"
              action="{{ route('workshop.service-records.update', $serviceRecord) }}"
              x-data="serviceRecordForm({{ $vehicle->id }}, {{ $vehicle->current_odometer }}, {{ json_encode($serviceRecord) }})">
            @csrf
            @method('PUT')
            <input type="hidden" name="vehicle_id" value="{{ $vehicle->id }}">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- ── LEFT: Main Form ── --}}
                <div class="lg:col-span-2 space-y-5">

                    {{-- Service Type ── --}}
                    <div class="bg-[#181A1A] border border-[#2E3030] rounded-2xl p-5">
                        <h2 class="text-sm font-semibold text-zinc-400 uppercase tracking-wider mb-4">Informasi Service</h2>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            {{-- Service Type --}}
                            <div class="sm:col-span-2">
                                <label for="service_type" class="block text-sm font-medium text-zinc-300 mb-1.5">
                                    Jenis Service <span class="text-red-500">*</span>
                                </label>
                                <select id="service_type"
                                        name="service_type"
                                        x-model="serviceType"
                                        required
                                        class="form-input w-full bg-zinc-900 border border-zinc-700 text-zinc-100 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-red-500 transition-colors">
                                    <option value="">-- Pilih Jenis Service --</option>
                                    @foreach($serviceTypes as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('service_type')
                                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Service Date --}}
                            <div>
                                <label for="service_date" class="block text-sm font-medium text-zinc-300 mb-1.5">
                                    Tanggal Service <span class="text-red-500">*</span>
                                </label>
                                <input type="date"
                                       id="service_date"
                                       name="service_date"
                                       value="{{ old('service_date', $serviceRecord->service_date->toDateString()) }}"
                                       max="{{ now()->toDateString() }}"
                                       required
                                       class="form-input w-full bg-zinc-900 border border-zinc-700 text-zinc-100 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-red-500 transition-colors">
                                @error('service_date')
                                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Odometer --}}
                            <div>
                                <label for="odometer_at_service" class="block text-sm font-medium text-zinc-300 mb-1.5">
                                    Odometer Saat Service (km) <span class="text-red-500">*</span>
                                </label>
                                <input type="number"
                                       id="odometer_at_service"
                                       name="odometer_at_service"
                                       x-model="odometer"
                                       required
                                       class="form-input w-full bg-zinc-900 border border-zinc-700 text-zinc-100 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-red-500 transition-colors">
                                @error('odometer_at_service')
                                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Status --}}
                            <div>
                                <label for="status" class="block text-sm font-medium text-zinc-300 mb-1.5">
                                    Status Service <span class="text-red-500">*</span>
                                </label>
                                <select id="status"
                                        name="status"
                                        required
                                        class="form-input w-full bg-zinc-900 border border-zinc-700 text-zinc-100 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-red-500 transition-colors">
                                    <option value="completed" {{ old('status', $serviceRecord->status) === 'completed' ? 'selected' : '' }}>Selesai</option>
                                    <option value="in_progress" {{ old('status', $serviceRecord->status) === 'in_progress' ? 'selected' : '' }}>Dalam Pengerjaan</option>
                                </select>
                                @error('status')
                                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Total Cost --}}
                            <div>
                                <label for="total_cost" class="block text-sm font-medium text-zinc-300 mb-1.5">
                                    Total Biaya (Rp) <span class="text-red-500">*</span>
                                </label>
                                <input type="number"
                                       id="total_cost"
                                       name="total_cost"
                                       x-model.number="manualCost"
                                       min="0"
                                       step="1000"
                                       required
                                       class="form-input w-full bg-zinc-900 border border-zinc-700 text-zinc-100 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-red-500 transition-colors">
                                @error('total_cost')
                                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Mechanic Notes --}}
                            <div class="sm:col-span-2">
                                <label for="mechanic_notes" class="block text-sm font-medium text-zinc-300 mb-1.5">Catatan Mekanik</label>
                                <textarea id="mechanic_notes"
                                          name="mechanic_notes"
                                          rows="3"
                                          maxlength="2000"
                                          placeholder="Contoh: Ganti oli mesin Shell Helix 10W-40, filter oli diganti, kondisi rem depan mulai tipis..."
                                          class="form-input w-full bg-zinc-900 border border-zinc-700 text-zinc-100 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-red-500 transition-colors resize-none">{{ old('mechanic_notes', $serviceRecord->mechanic_notes) }}</textarea>
                                @error('mechanic_notes')
                                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- ── Spareparts ── --}}
                    <div class="bg-[#181A1A] border border-[#2E3030] rounded-2xl p-5">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h2 class="text-sm font-semibold text-zinc-400 uppercase tracking-wider">Sparepart Digunakan</h2>
                                <p class="text-xs text-zinc-600 mt-0.5">Opsional — tambah baris untuk setiap sparepart</p>
                            </div>
                            <button type="button"
                                    @click="addPart()"
                                    class="flex items-center gap-1.5 px-3 py-1.5 bg-zinc-800 hover:bg-zinc-700 border border-zinc-700 hover:border-zinc-500 text-zinc-300 hover:text-zinc-100 text-xs font-medium rounded-lg transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                Tambah Sparepart
                            </button>
                        </div>

                        {{-- Parts List --}}
                        <div id="parts-container" class="space-y-3">
                            <template x-if="parts.length === 0">
                                <div class="text-center py-8 border-2 border-dashed border-zinc-800 rounded-xl">
                                    <svg class="w-8 h-8 text-zinc-700 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z"/>
                                    </svg>
                                    <p class="text-xs text-zinc-600">Belum ada sparepart. Klik tombol "Tambah Sparepart" untuk menambah.</p>
                                </div>
                            </template>

                            <template x-for="(part, index) in parts" :key="index">
                                <div class="part-row bg-zinc-900/50 border border-zinc-800 rounded-xl p-4">
                                    <div class="flex items-start gap-3">
                                        <div class="flex-1 grid grid-cols-2 sm:grid-cols-4 gap-3">
                                            {{-- Part Name --}}
                                            <div class="col-span-2 sm:col-span-2">
                                                <label class="block text-xs text-zinc-500 mb-1">Nama Sparepart *</label>
                                                <input type="text"
                                                       :name="'parts[' + index + '][part_name]'"
                                                       x-model="part.part_name"
                                                       list="spareparts-datalist"
                                                       @input="autofillPart(index, part.part_name)"
                                                       placeholder="Misal: Oli Shell Helix 10W-40"
                                                       class="form-input w-full bg-zinc-900 border border-zinc-700 text-zinc-100 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-red-500 transition-colors">
                                            </div>
                                            {{-- Quantity --}}
                                            <div>
                                                <label class="block text-xs text-zinc-500 mb-1">Jumlah *</label>
                                                <input type="number"
                                                       :name="'parts[' + index + '][quantity]'"
                                                       x-model.number="part.quantity"
                                                       min="1"
                                                       placeholder="1"
                                                       @input="recalcTotal()"
                                                       class="form-input w-full bg-zinc-900 border border-zinc-700 text-zinc-100 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-red-500 transition-colors">
                                            </div>
                                            {{-- Unit Price --}}
                                            <div>
                                                <label class="block text-xs text-zinc-500 mb-1">Harga Satuan (Rp) *</label>
                                                <input type="number"
                                                       :name="'parts[' + index + '][unit_price]'"
                                                       x-model.number="part.unit_price"
                                                       min="0"
                                                       step="500"
                                                       placeholder="0"
                                                       @input="recalcTotal()"
                                                       class="form-input w-full bg-zinc-900 border border-zinc-700 text-zinc-100 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-red-500 transition-colors">
                                            </div>
                                            {{-- Category --}}
                                            <div class="col-span-2 sm:col-span-1">
                                                <label class="block text-xs text-zinc-500 mb-1">Kategori</label>
                                                <input type="text"
                                                       :name="'parts[' + index + '][part_category]'"
                                                       x-model="part.part_category"
                                                       placeholder="Misal: Oli, Filter"
                                                       class="form-input w-full bg-zinc-900 border border-zinc-700 text-zinc-100 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-red-500 transition-colors">
                                            </div>
                                            {{-- Subtotal display --}}
                                            <div class="col-span-2 sm:col-span-1 flex items-end">
                                                <div class="w-full bg-zinc-800/50 rounded-lg px-3 py-2 text-xs text-zinc-400">
                                                    Subtotal: <span class="text-zinc-200 font-semibold" x-text="'Rp ' + (part.quantity * part.unit_price).toLocaleString('id-ID')"></span>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- Remove Button --}}
                                        <button type="button"
                                                @click="removePart(index)"
                                                class="mt-5 flex-shrink-0 w-8 h-8 rounded-lg bg-red-950/40 border border-red-900/30 hover:bg-red-900/50 flex items-center justify-center transition-colors">
                                            <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- ── RIGHT: Summary & Submit ── --}}
                <div class="space-y-4">

                    {{-- Order Summary Card --}}
                    <div class="bg-[#181A1A] border border-[#2E3030] rounded-2xl p-5 sticky top-6">
                        <h2 class="text-sm font-semibold text-zinc-400 uppercase tracking-wider mb-4">Ringkasan</h2>

                        <div class="space-y-3">
                            {{-- Service Type --}}
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-zinc-500">Jenis Service</span>
                                <span class="text-xs font-medium text-zinc-300" x-text="serviceTypeLabel || '-'"></span>
                            </div>

                            {{-- Oil Change Alert --}}
                            <template x-if="serviceType === 'oil_change'">
                                <div class="flex items-center gap-2 bg-emerald-950/30 border border-emerald-900/30 rounded-xl p-3">
                                    <svg class="w-4 h-4 text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <p class="text-xs text-emerald-400">Kondisi oli akan di-reset ke 100%</p>
                                </div>
                            </template>

                            <div class="h-px bg-zinc-800"></div>

                            {{-- Spareparts Count --}}
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-zinc-500">Sparepart</span>
                                <span class="text-xs font-medium text-zinc-300" x-text="parts.length + ' item'"></span>
                            </div>

                            {{-- Parts Subtotal --}}
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-zinc-500">Subtotal Sparepart</span>
                                <span class="text-xs font-medium text-zinc-300" x-text="'Rp ' + partsSubtotal.toLocaleString('id-ID')"></span>
                            </div>

                            <div class="h-px bg-zinc-800"></div>

                            {{-- Total --}}
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-semibold text-zinc-300">Total Biaya</span>
                                <span class="text-sm font-bold text-red-400 total-badge" x-text="'Rp ' + totalCost.toLocaleString('id-ID')"></span>
                            </div>
                        </div>

                        {{-- Validation errors global --}}
                        @if($errors->any())
                            <div class="mt-4 p-3 bg-red-950/30 border border-red-900/30 rounded-xl">
                                <p class="text-xs font-semibold text-red-400 mb-1">Ada kesalahan:</p>
                                <ul class="space-y-0.5">
                                    @foreach($errors->all() as $error)
                                        <li class="text-xs text-red-300">• {{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- Submit Button --}}
                        <button type="submit"
                                id="submit-btn"
                                class="mt-5 w-full py-3 bg-red-600 hover:bg-red-700 disabled:opacity-50 text-white font-bold rounded-xl shadow-lg hover:shadow-red-900/30 transition-all flex items-center justify-center gap-2 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Perbarui Service Record
                        </button>

                        <a href="{{ route('workshop.scan') }}"
                           class="mt-3 w-full py-2.5 bg-zinc-800 hover:bg-zinc-700 text-zinc-400 hover:text-zinc-200 font-medium rounded-xl transition-all flex items-center justify-center gap-2 text-sm">
                            Batal
                        </a>
                    </div>

                </div>
            </div>

            <datalist id="spareparts-datalist">
                @foreach($spareparts as $part)
                    <option value="{{ $part->name }}"></option>
                @endforeach
            </datalist>
        </form>
    </div>

    @push('scripts')
    <script>
        function serviceRecordForm(vehicleId, currentOdometer, serviceRecord) {
            return {
                vehicleId: vehicleId,
                currentOdometer: currentOdometer,
                serviceType: serviceRecord.service_type,
                odometer: serviceRecord.odometer_at_service,
                manualCost: parseFloat(serviceRecord.total_cost),
                parts: serviceRecord.parts || [],
                catalog: @json($spareparts),

                get serviceTypeLabel() {
                    const labels = @json($serviceTypes);
                    return labels[this.serviceType] || '';
                },

                get partsSubtotal() {
                    return this.parts.reduce((sum, p) => sum + (parseFloat(p.quantity) * parseFloat(p.unit_price)), 0);
                },

                get totalCost() {
                    return this.manualCost;
                },

                addPart() {
                    this.parts.push({
                        part_name: '',
                        quantity: 1,
                        unit_price: 0,
                        part_category: '',
                    });
                },

                removePart(index) {
                    this.parts.splice(index, 1);
                    this.recalcTotal();
                },

                autofillPart(index, val) {
                    const matched = this.catalog.find(p => p.name.toLowerCase() === val.toLowerCase());
                    if (matched) {
                        this.parts[index].part_name = matched.name;
                        this.parts[index].unit_price = parseFloat(matched.price);
                        this.parts[index].part_category = matched.category || '';
                        this.recalcTotal();
                    }
                },

                recalcTotal() {
                    this.manualCost = this.partsSubtotal;
                },
            };
        }
    </script>
    @endpush
</x-app-layout>
