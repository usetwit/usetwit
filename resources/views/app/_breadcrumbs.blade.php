@unless ($breadcrumbs->isEmpty())
    <ol class="flex flex-wrap items-center mb-4 mt-0 mx-2 lg:mb-8 lg:mt-2 lg:mx-4 text-slate-500 breadcrumbs text-xs">
        @foreach ($breadcrumbs as $breadcrumb)

            @if (!is_null($breadcrumb->url) && !$loop->last)
                <li><a href="{{ $breadcrumb->url }}" class="text-slate-500 hover:text-orange-600">{{ $breadcrumb->title }}</a></li>
            @else
                <li>{{ $breadcrumb->title }}</li>
            @endif

        @endforeach
    </ol>
@endunless
