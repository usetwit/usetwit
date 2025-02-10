@unless ($breadcrumbs->isEmpty())
    <ol class="flex flex-wrap items-center mb-4 lg:mb-6 mt-1 mx-2 lg:mt-2 lg:mx-4 text-slate-500 dark:text-slate-200 breadcrumbs text-xs">
        @foreach ($breadcrumbs as $breadcrumb)
            @if (!is_null($breadcrumb->url) && !$loop->last)
                <li><a href="{{ $breadcrumb->url }}"
                       class="text-slate-500 hover:text-orange-500 dark:text-teal-500"
                    >{{ $breadcrumb->title }}</a></li>
            @else
                <li>{{ $breadcrumb->title }}</li>
            @endif
        @endforeach
    </ol>
@endunless
