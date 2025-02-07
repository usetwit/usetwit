@props([
    'maxlength' => 255,
    'name',
    'placeholder' => 'Enter text...',
    'type' => 'text',
    'required' => false,
    'value' => '',
    ])


<input
        id="{{ $name }}"
        name="{{ $name }}"
        type="{{ $type }}"
        maxlength="{{ $maxlength }}"
        @if($required) required @endif
        {{ $attributes->merge(['class' => 'border rounded-lg w-full invalid:border-red-500 shadow-sm text-gray-800 ' . ($errors->has($name) ? 'border-red-500' : 'border-gray-300')]) }}
        placeholder="{{ $placeholder }}"
        value="{{ old($name, $value) }}"
>
