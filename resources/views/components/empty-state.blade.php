@props([
    'title'       => 'Empty',
    'description' => null,
    'icon'        => null,
    'action'      => null,
    'actionHref'  => '#',
    'actionLabel' => 'Get Started',
])

<div class="empty-state">
    <div class="empty-state-icon">
        @if($icon)
            {{ $icon }}
        @else
            <svg style="width:32px;height:32px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
        @endif
    </div>

    <p class="empty-state-title">{{ $title }}</p>

    @if($description)
        <p class="empty-state-desc mt-1">{{ $description }}</p>
    @endif

    @if($action || $slot->isNotEmpty())
        <div class="mt-5">
            {{ $slot }}
        </div>
    @endif
</div>
