@foreach ($generate() as $crumbs)
    @if ($crumbs->url() && !$loop->last)
        <li {{ $attributes->merge(['class' => $class]) }}>
            <a href="{{ $crumbs->url() }}">
                {{ $crumbs->title() }}
            </a>
        </li>
    @else
        <li {{ $attributes->merge(['class' => $class. ' '. $active]) }}>
            {{ $crumbs->title() }}
        </li>
    @endif
@endforeach
