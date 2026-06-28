@props([
    'title'    => null,
    'subtitle' => null,
    'actions'  => null,
])

<div class="page-header">
    <div>
        @if($title)
            <h1 class="page-title">{{ $title }}</h1>
        @endif
        @if($subtitle)
            <p class="page-subtitle">{{ $subtitle }}</p>
        @endif
    </div>

    @if($actions || isset($actions))
        <div class="flex items-center gap-2 flex-shrink-0">
            {{ $actions }}
        </div>
    @endif
</div>
