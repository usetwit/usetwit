<div class="grid grid-cols-2 gap-4 mb-2 border-b border-gray-200">
    <div class="p-4">
        <label for="{{ $name }}" class="font-bold text-slate-700 w-full block">
            {{ $title }}

            @if($required)
                <sup class="text-red-500 font-normal">
                    *required
                </sup>
            @endif
        </label>

        @isset($description)
            <span class="text-sm text-gray-800">{{ $description }}</span>
        @endif
    </div>
    <div class="p-4">
        {{ $slot }}
    </div>
</div>
