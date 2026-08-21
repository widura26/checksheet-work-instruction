@php
    $hasSubitems = isset($subitems)
        && is_array($subitems)
        && count($subitems) > 0;

    $isActive = isset($route)
        && request()->routeIs($route);

    $isSubitemActive = $hasSubitems
        && collect($subitems)->contains(function ($item) {
            return isset($item['route'])
                && request()->routeIs($item['route']);
        });
@endphp

<li class="{{ $navtype }} {{ $isSubitemActive ? 'show' : '' }}">

    @if($hasSubitems)

        <a class="nav-link nav-group-toggle {{ $isSubitemActive ? 'active' : '' }}" href="#">
            @if(!empty($icon))
                <svg class="nav-icon">
                    <use
                        xlink:href="{{ asset('template/vendors/@coreui/icons/svg/free.svg#' . $icon) }}">
                    </use>
                </svg>
            @endif
            {{ $label }}
        </a>

        <ul class="nav-group-items">
            @foreach($subitems as $subItem)
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs($subItem['route']) ? 'active' : '' }}"
                        href="{{ route($subItem['route']) }}">
                        {{ $subItem['label'] }}
                    </a>
                </li>
            @endforeach
        </ul>
    @else
        <a class="nav-link {{ $isActive ? 'active' : '' }}" href="{{ route($route) }}">
            @if(!empty($icon))
                <svg class="nav-icon">
                    <use
                        xlink:href="{{ asset('template/vendors/@coreui/icons/svg/free.svg#' . $icon) }}">
                    </use>
                </svg>
            @endif
            {{ $label }}
        </a>
    @endif
</li>
