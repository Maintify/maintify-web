@php
    $allParts = collect();
    foreach($serviceRecords as $record) {
        foreach($record->parts as $part) {
            $part->service_date = $record->service_date;
            $part->workshop_name = $record->workshop->name;
            $allParts->push($part);
        }
    }
    $allParts = $allParts->sortByDesc('service_date');
@endphp

@if($allParts->isEmpty())
    <div style="text-align: center; padding: 48px 16px; background-color: #1A1C1C; border: 1px solid #2E3030; border-radius: 16px;">
        <div style="display: inline-flex; align-items: center; justify-content: center; width: 56px; height: 56px; border-radius: 50%; background-color: rgba(113, 113, 122, 0.1); color: #71717A; margin-bottom: 16px;">
            <svg style="width: 28px; height: 28px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
        <h4 style="font-size: 16px; font-weight: 600; color: #F4F4F5; margin: 0 0 8px;">Belum Ada Suku Cadang Diganti</h4>
        <p style="font-size: 13px; color: #71717A; max-width: 320px; margin: 0 auto;">Belum ada catatan penggantian suku cadang/sparepart untuk kendaraan ini.</p>
    </div>
@else
    <div style="background-color: #1A1C1C; border: 1px solid #2E3030; border-radius: 16px; overflow: hidden;">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px; color: #A1A1AA;">
                <thead>
                    <tr style="background-color: #252828; border-bottom: 1px solid #2E3030; color: #F4F4F5;">
                        <th style="padding: 14px 16px; font-weight: 700;">Tanggal</th>
                        <th style="padding: 14px 16px; font-weight: 700;">Nama Sparepart</th>
                        <th style="padding: 14px 16px; font-weight: 700;">Kategori</th>
                        <th style="padding: 14px 16px; font-weight: 700; text-align: center;">Jumlah</th>
                        <th style="padding: 14px 16px; font-weight: 700; text-align: right;">Harga Satuan</th>
                        <th style="padding: 14px 16px; font-weight: 700; text-align: right;">Subtotal</th>
                        <th style="padding: 14px 16px; font-weight: 700;">Bengkel</th>
                    </tr>
                </thead>
                <tbody style="divide-y: 1px solid #2E3030;">
                    @foreach($allParts as $part)
                        <tr style="border-bottom: 1px solid #2E3030; transition: background-color 150ms;" onmouseover="this.style.backgroundColor='#252828';" onmouseout="this.style.backgroundColor='transparent';">
                            <td style="padding: 14px 16px; white-space: nowrap; color: #F4F4F5; font-weight: 500;">
                                {{ $part->service_date->format('d M Y') }}
                            </td>
                            <td style="padding: 14px 16px; color: #F4F4F5; font-weight: 600;">
                                {{ $part->part_name }}
                            </td>
                            <td style="padding: 14px 16px;">
                                <span style="font-size: 11px; background-color: #252828; border: 1px solid #2E3030; padding: 2px 8px; border-radius: 6px; color: #A1A1AA;">
                                    {{ $part->part_category }}
                                </span>
                            </td>
                            <td style="padding: 14px 16px; text-align: center;">
                                {{ $part->quantity }}
                            </td>
                            <td style="padding: 14px 16px; text-align: right;">
                                {{ 'Rp ' . number_format($part->unit_price, 0, ',', '.') }}
                            </td>
                            <td style="padding: 14px 16px; text-align: right; font-weight: 700; color: #ff9aa4;">
                                {{ $part->formatted_subtotal }}
                            </td>
                            <td style="padding: 14px 16px; color: #ff9aa4; font-weight: 500;">
                                {{ $part->workshop_name }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
