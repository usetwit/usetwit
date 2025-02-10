@props([
    'text' => 'Submit',
    'type' => 'submit',
    'icon' => ''
])

<button class="rounded-lg bg-slate-700 hover:bg-slate-600 text-white py-2 px-8 my-4 mx-auto flex"
        type="{{ $type }}"
>
    @if($icon !== '') {!! Icons::icon($icon, 'mr-1') !!} @endif {{ $text }}
</button>
