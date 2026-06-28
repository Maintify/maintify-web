@props([
    'label'       => null,
    'id'          => null,
    'type'        => 'text',
    'error'       => null,
    'hint'        => null,
    'required'    => false,
    'leadingIcon' => false,
    'search'      => false,
])

<div class="form-group">
    @if($label)
        <label for="{{ $id }}" class="form-label {{ $required ? 'form-label-required' : '' }}">
            {{ $label }}
        </label>
    @endif

    @if($search)
        <div class="search-input-wrapper">
            <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input
                id="{{ $id }}"
                type="search"
                {{ $attributes->merge(['class' => 'search-input ' . ($error ? 'form-input-error' : '')]) }}>
        </div>
    @else
        <input
            id="{{ $id }}"
            type="{{ $type }}"
            {{ $attributes->merge(['class' => 'form-input ' . ($error ? 'form-input-error' : '')]) }}>
    @endif

    @if($error)
        <p class="form-error">{{ $error }}</p>
    @elseif($hint)
        <p class="form-hint">{{ $hint }}</p>
    @endif
</div>
