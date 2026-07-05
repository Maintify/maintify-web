@if($serviceRecords->isEmpty())
    <div style="text-align: center; padding: 48px 16px; background-color: #1A1C1C; border: 1px solid #2E3030; border-radius: 16px;">
        <div style="display: inline-flex; align-items: center; justify-content: center; width: 56px; height: 56px; border-radius: 50%; background-color: rgba(113, 113, 122, 0.1); color: #71717A; margin-bottom: 16px;">
            <svg style="width: 28px; height: 28px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
        </div>
        <h4 style="font-size: 16px; font-weight: 600; color: #F4F4F5; margin: 0 0 8px;">Belum Ada Riwayat Servis</h4>
        <p style="font-size: 13px; color: #71717A; max-width: 320px; margin: 0 auto;">Kendaraan ini belum pernah melakukan servis di bengkel mitra Maintify.</p>
    </div>
@else
    <div style="position: relative; padding-left: 20px; margin-left: 12px; border-left: 2px solid #2E3030;">
        @foreach($serviceRecords as $record)
            <div style="position: relative; margin-bottom: 32px;">
                {{-- Timeline Dot --}}
                <div style="position: absolute; left: -29px; top: 4px; width: 16px; height: 16px; border-radius: 50%; background-color: #121414; border: 3px solid {{ $record->status === 'completed' ? '#4ade80' : '#fbbf24' }}; z-index: 10;"></div>

                {{-- Card Content --}}
                <div style="background-color: #1A1C1C; border: 1px solid #2E3030; border-radius: 16px; padding: 20px; transition: border-color 150ms;" onmouseover="this.style.borderColor='#3E4040';" onmouseout="this.style.borderColor='#2E3030';">
                    {{-- Header --}}
                    <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: flex-start; gap: 12px; margin-bottom: 12px;">
                        <div>
                            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                                <h4 style="font-size: 15px; font-weight: 700; color: #F4F4F5; margin: 0;">
                                    {{ $record->service_type_label_readable }}
                                </h4>
                                <span style="font-size: 10px; font-weight: 700; padding: 2px 8px; border-radius: 100px; text-transform: uppercase; background-color: {{ $record->status === 'completed' ? 'rgba(74, 222, 128, 0.1)' : 'rgba(251, 191, 36, 0.1)' }}; color: {{ $record->status === 'completed' ? '#4ade80' : '#fbbf24' }}; border: 1px solid {{ $record->status === 'completed' ? 'rgba(74, 222, 128, 0.2)' : 'rgba(251, 191, 36, 0.2)' }};">
                                    {{ $record->status === 'completed' ? 'Selesai' : 'Diproses' }}
                                </span>
                            </div>
                            <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 8px; font-size: 12px; color: #A1A1AA;">
                                <span style="font-weight: 600; color: #ff9aa4;">{{ $record->workshop->name }}</span>
                                <span style="color: #3A3D3D;">•</span>
                                <span>Odometer: {{ number_format($record->odometer_at_service) }} Km</span>
                            </div>
                        </div>
                        <div style="text-align: right; font-size: 12px; color: #71717A;">
                            <div style="font-weight: 600; color: #F4F4F5; margin-bottom: 2px;">
                                {{ $record->service_date->format('d M Y') }}
                            </div>
                            <div>{{ $record->service_date->format('H:i') }} WIB</div>
                        </div>
                    </div>

                    {{-- Mechanic Note --}}
                    @if($record->mechanic_notes)
                        <div style="background-color: #252828; border-left: 3px solid #ff9aa4; padding: 12px; border-radius: 0 8px 8px 0; margin-bottom: 16px; font-size: 13px;">
                            <span style="display: block; font-size: 11px; font-weight: 700; color: #71717A; text-transform: uppercase; margin-bottom: 4px; letter-spacing: 0.05em;">Catatan Mekanik ({{ $record->performedBy ? $record->performedBy->full_name : 'Mekanik' }})</span>
                            <p style="color: #A1A1AA; margin: 0; line-height: 1.5;">{{ $record->mechanic_notes }}</p>
                        </div>
                    @endif

                    {{-- Parts List --}}
                    @if($record->parts->isNotEmpty())
                        <div style="border-top: 1px solid #2E3030; padding-top: 12px;">
                            <span style="display: block; font-size: 11px; font-weight: 700; color: #71717A; text-transform: uppercase; margin-bottom: 8px; letter-spacing: 0.05em;">Sparepart Diganti</span>
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                @foreach($record->parts as $part)
                                    <div style="display: flex; justify-content: space-between; align-items: center; font-size: 13px; padding: 6px 10px; background-color: #1E2020; border-radius: 8px; border: 1px solid #2E3030;">
                                        <div>
                                            <span style="font-weight: 600; color: #F4F4F5;">{{ $part->part_name }}</span>
                                            <span style="font-size: 11px; color: #71717A; background-color: #252828; padding: 1px 6px; border-radius: 4px; margin-left: 6px;">{{ $part->part_category }}</span>
                                        </div>
                                        <div style="font-size: 12px; color: #A1A1AA; text-align: right;">
                                            <span>{{ $part->quantity }} x {{ 'Rp ' . number_format($part->unit_price, 0, ',', '.') }}</span>
                                            <span style="display: block; font-weight: 600; color: #ff9aa4;">{{ $part->formatted_subtotal }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Footer/Cost --}}
                    <div style="display: flex; justify-content: flex-end; align-items: center; border-top: 1px solid #2E3030; margin-top: 16px; padding-top: 12px; font-size: 14px;">
                        <span style="color: #71717A; font-weight: 500; margin-right: 8px;">Total Biaya:</span>
                        <span style="font-size: 16px; font-weight: 800; color: #4ade80;">{{ $record->formatted_cost }}</span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
