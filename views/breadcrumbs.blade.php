@foreach ($generate() as $crumbs)
    @if ($crumbs->url() && !$loop->last)
        <li {{ $attributes->merge(['class' => $class]) }}>
            <a href="{{ $crumbs->url() }}">
                {!! $title($crumbs) !!}
            </a>
        </li>
    @else
        <li {{ $attributes->merge(['class' => $class. ' '. $active]) }}>
            {!! $title($crumbs) !!}
        </li>
    @endif
@endforeach
