@props([
    'rows' => 3,
    'cols' => 1,
    'type' => 'card', // card | list | text | table
])

@if($type === 'card')
    <div class="grid grid-cols-1 md:grid-cols-{{ $cols }} gap-6 animate-pulse">
        @for($i = 0; $i < $rows; $i++)
            <div class="rounded-xl p-5 bg-zinc-900 border border-zinc-800 space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-zinc-800 rounded-lg"></div>
                        <div class="space-y-2">
                            <div class="h-4 w-32 bg-zinc-800 rounded"></div>
                            <div class="h-3 w-20 bg-zinc-800 rounded"></div>
                        </div>
                    </div>
                    <div class="h-6 w-16 bg-zinc-800 rounded-full"></div>
                </div>
                <div class="space-y-2 pt-2">
                    <div class="h-3 w-full bg-zinc-800 rounded"></div>
                    <div class="h-3 w-5/6 bg-zinc-800 rounded"></div>
                </div>
                <div class="flex items-center justify-between pt-4 border-t border-zinc-800/50">
                    <div class="h-4 w-24 bg-zinc-800 rounded"></div>
                    <div class="h-8 w-8 bg-zinc-800 rounded-lg"></div>
                </div>
            </div>
        @endfor
    </div>
@elseif($type === 'list')
    <div class="space-y-4 animate-pulse">
        @for($i = 0; $i < $rows; $i++)
            <div class="flex items-center justify-between p-4 bg-zinc-900 border border-zinc-800 rounded-xl">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-zinc-800 rounded-full"></div>
                    <div class="space-y-2">
                        <div class="h-4 w-28 bg-zinc-800 rounded"></div>
                        <div class="h-3 w-16 bg-zinc-800 rounded"></div>
                    </div>
                </div>
                <div class="h-4 w-12 bg-zinc-800 rounded"></div>
            </div>
        @endfor
    </div>
@elseif($type === 'table')
    <div class="border border-zinc-800 rounded-xl overflow-hidden animate-pulse">
        <div class="bg-zinc-900 px-4 py-3 border-b border-zinc-800 flex justify-between">
            <div class="h-4 w-24 bg-zinc-800 rounded"></div>
            <div class="h-4 w-24 bg-zinc-800 rounded"></div>
            <div class="h-4 w-24 bg-zinc-800 rounded"></div>
        </div>
        <div class="divide-y divide-zinc-800 bg-zinc-900/50">
            @for($i = 0; $i < $rows; $i++)
                <div class="px-4 py-3.5 flex justify-between">
                    <div class="h-3 w-32 bg-zinc-800 rounded"></div>
                    <div class="h-3 w-20 bg-zinc-800 rounded"></div>
                    <div class="h-3 w-16 bg-zinc-800 rounded"></div>
                </div>
            @endfor
        </div>
    </div>
@else
    <div class="space-y-3 animate-pulse">
        @for($i = 0; $i < $rows; $i++)
            <div class="h-4 bg-zinc-800 rounded w-full"></div>
        @endfor
    </div>
@endif
