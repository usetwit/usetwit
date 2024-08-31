@props([
    'name',
    'required' => false,
    'value' => '',
    'options' => [],
    'placeholder' => '',
    ])


<select
        id="{{ $name }}"
        name="{{ $name }}"
        {{ $attributes->merge(['class' => 'border border-gray-300 rounded-lg w-full invalid:border-red-500 shadow cursor-pointer text-gray-800']) }}
>
    @if($placeholder)
        <option
            value=""
            @if(!$value) selected @endif
            class="cursor-pointer"
        >
            {{ $placeholder }}
        </option>
    @endif

    @foreach($options as $k => $v)
        <option
                value="{{ $k }}"
                @if($k === $value) selected @endif
                class="cursor-pointer"
        >
            {{ $v }}
        </option>
    @endforeach
</select>
