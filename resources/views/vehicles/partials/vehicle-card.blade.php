<div class="card" style="display: flex; flex-direction: column; height: 100%; overflow: hidden; padding: 0; background-color: #1A1C1C; border: 1px solid #2E3030; border-radius: 16px; transition: transform 200ms, border-color 200ms; cursor: pointer; position: relative;" onmouseover="this.style.transform='translateY(-4px)';this.style.borderColor='#3E4040';" onmouseout="this.style.transform='none';this.style.borderColor='#2E3030';" onclick="window.location='{{ route('vehicles.show', $vehicle) }}'">
    {{-- Card Photo / Header Image --}}
    <div style="position: relative; width: 100%; height: 160px; background-color: #252828; display: flex; align-items: center; justify-content: center; overflow: hidden; border-bottom: 1px solid #2E3030;">
        @if($vehicle->photo_url)
            <img src="{{ $vehicle->photo_url }}" alt="{{ $vehicle->brand }} {{ $vehicle->model }}" style="width: 100%; height: 100%; object-fit: cover;">
        @else
            {{-- Modern vector silhouette representation --}}
            <svg style="width: 64px; height: 64px; color: #71717A;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2 2h8l2-2z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M8 9h2v2H8V9zm0-4h2v2H8V5zm0 8h2v2H8v-2zM20 9h2v2h-2V9zm0-4h2v2h-2V5zm0 8h2v2h-2v-2z"/>
            </svg>
        @endif

        {{-- Status Badge Overlay --}}
        <div style="position: absolute; top: 12px; right: 12px; z-index: 10;">
            @if($vehicle->health_status === 'good')
                <span class="badge-success" style="padding: 4px 10px; font-size: 11px; font-weight: 700; border-radius: 100px; display: inline-flex; align-items: center; gap: 4px; box-shadow: 0 4px 12px rgba(0,0,0,0.5);">
                    <span class="badge-dot" style="background-color: #4ade80;"></span>
                    Aktif
                </span>
            @elseif($vehicle->health_status === 'warning')
                <span class="badge-warning" style="padding: 4px 10px; font-size: 11px; font-weight: 700; border-radius: 100px; display: inline-flex; align-items: center; gap: 4px; box-shadow: 0 4px 12px rgba(0,0,0,0.5);">
                    <span class="badge-dot" style="background-color: #fbbf24;"></span>
                    Perlu Service
                </span>
            @else
                <span class="badge-danger" style="padding: 4px 10px; font-size: 11px; font-weight: 700; border-radius: 100px; display: inline-flex; align-items: center; gap: 4px; box-shadow: 0 4px 12px rgba(0,0,0,0.5);">
                    <span class="badge-dot" style="background-color: #f87171;"></span>
                    Bermasalah
                </span>
            @endif
        </div>

        {{-- Fuel Type Overlay --}}
        <div style="position: absolute; bottom: 12px; left: 12px; z-index: 10;">
            @php
                $fuelLabels = [
                    'gasoline' => 'Bensin',
                    'diesel' => 'Diesel',
                    'electric' => 'Listrik',
                    'hybrid' => 'Hybrid',
                ];
                $fuelLabel = $fuelLabels[$vehicle->fuel_type] ?? ucfirst($vehicle->fuel_type);
            @endphp
            <span class="badge-primary" style="padding: 4px 10px; font-size: 10px; font-weight: 700; border-radius: 6px; text-transform: uppercase; letter-spacing: 0.05em; background-color: rgba(18, 20, 20, 0.75); border: 1px solid rgba(255, 255, 255, 0.1); backdrop-filter: blur(4px);">
                {{ $fuelLabel }}
            </span>
        </div>
    </div>

    {{-- Card Body --}}
    <div style="padding: 20px; display: flex; flex-direction: column; flex-grow: 1; gap: 16px;">
        {{-- Vehicle Identity --}}
        <div>
            <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 8px;">
                <h4 style="font-size: 16px; font-weight: 700; color: #F4F4F5; margin: 0; line-height: 1.3;">
                    {{ $vehicle->brand }} {{ $vehicle->model }}
                </h4>
                <span style="font-size: 11px; font-weight: 500; color: #71717A; background-color: #252828; padding: 2px 6px; border-radius: 4px; border: 1px solid #2E3030; flex-shrink: 0;">
                    {{ $vehicle->year }}
                </span>
            </div>
            <p style="font-size: 13px; font-weight: 600; color: #A1A1AA; font-family: monospace; margin: 4px 0 0;">
                {{ $vehicle->plate_number }}
            </p>
        </div>

        {{-- Stats and Indicators --}}
        <div style="display: flex; flex-direction: column; gap: 12px; padding: 12px; background-color: #252828; border: 1px solid #2E3030; border-radius: 10px;">
            {{-- Mileage / Odometer --}}
            <div style="display: flex; justify-content: space-between; align-items: center; font-size: 12px;">
                <span style="color: #71717A; font-weight: 500;">Mileage</span>
                <span style="color: #F4F4F5; font-weight: 600;">{{ number_format($vehicle->current_odometer) }} Km</span>
            </div>

            <hr style="border: 0; border-top: 1px solid #2E3030; margin: 0;">

            {{-- Health Indicator --}}
            <div style="display: flex; flex-direction: column; gap: 4px;">
                <div style="display: flex; justify-content: space-between; align-items: center; font-size: 11px;">
                    <span style="color: #71717A; font-weight: 500;">Kondisi Kendaraan</span>
                    <span style="color: #F4F4F5; font-weight: 600;">{{ $vehicle->health_score }}%</span>
                </div>
                <div style="width: 100%; height: 6px; background-color: #1E2020; border-radius: 100px; overflow: hidden;">
                    @php
                        $healthColor = $vehicle->health_score >= 80 ? '#4ade80' : ($vehicle->health_score >= 50 ? '#fbbf24' : '#f87171');
                    @endphp
                    <div style="width: {{ $vehicle->health_score }}%; height: 100%; background-color: {{ $healthColor }}; border-radius: 100px;"></div>
                </div>
            </div>

            <hr style="border: 0; border-top: 1px solid #2E3030; margin: 0;">

            {{-- Oil Life Indicator --}}
            <div style="display: flex; flex-direction: column; gap: 4px;">
                <div style="display: flex; justify-content: space-between; align-items: center; font-size: 11px;">
                    <span style="color: #71717A; font-weight: 500;">Umur Oli (Oil Life)</span>
                    <span style="color: #F4F4F5; font-weight: 600;">{{ $vehicle->oil_life_percentage ?? 100 }}%</span>
                </div>
                <div style="width: 100%; height: 6px; background-color: #1E2020; border-radius: 100px; overflow: hidden;">
                    @php
                        $oilScore = $vehicle->oil_life_percentage ?? 100;
                        $oilColor = $oilScore >= 40 ? '#4ade80' : ($oilScore >= 15 ? '#fbbf24' : '#f87171');
                    @endphp
                    <div style="width: {{ $oilScore }}%; height: 100%; background-color: {{ $oilColor }}; border-radius: 100px;"></div>
                </div>
            </div>
        </div>

        {{-- View Details Quick Action Link --}}
        <div style="margin-top: auto; display: flex; align-items: center; gap: 4px; font-size: 12px; font-weight: 600; color: #ff9aa4; transition: color 150ms;" onmouseover="this.style.color='#ff5f71';" onmouseout="this.style.color='#ff9aa4';">
            Detail Kendaraan
            <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
            </svg>
        </div>
    </div>
</div>
