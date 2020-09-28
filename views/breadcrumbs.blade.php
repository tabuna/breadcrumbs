@foreach ($generate() as $crumbs)
    @if ($crumbs->url() && !$loop->last)
        <li class="{{$class}}">
            <a href="{{ $crumbs->url() }}">
                {{ $crumbs->title() }}
            </a>
        </li>
    @else
        <li class="{{$class}} {{$active}}">
            {{ $crumbs->title() }}
        </li>
    @endif
@endforeach
