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

        <a class="nav-link nav-group-toggle {{ $isSubitemActive ? 'active' : '' }}" href="#"
            aria-expanded="{{ $isSubitemActive ? 'true' : 'false' }}"
            aria-controls="nav-group-{{ \Illuminate\Support\Str::slug($label) }}">
            @if(!empty($icon))
                <svg class="nav-icon">
                    <use
                        xlink:href="{{ asset('template/vendors/@coreui/icons/svg/free.svg#' . $icon) }}">
                    </use>
                </svg>
            @endif
            <span class="sidebar-label">{{ $label }}</span>
        </a>

        <div class="nav-group-items" id="nav-group-{{ \Illuminate\Support\Str::slug($label) }}"
            @if(!$isSubitemActive) inert @endif>
            <ul class="nav-group-items-list">
                @foreach($subitems as $subItem)
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs($subItem['route']) ? 'active' : '' }}"
                            href="{{ route($subItem['route']) }}">
                            <span class="sidebar-label">{{ $subItem['label'] }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    @else
                <a class="nav-link {{ $isActive ? 'active' : '' }}" href="{{ route($route) }}">
            @if(!empty($icon))
                <svg class="nav-icon">
                    <use
                        xlink:href="{{ asset('template/vendors/@coreui/icons/svg/free.svg#' . $icon) }}">
                    </use>
                </svg>
            @endif
            <span class="sidebar-label">{{ $label }}</span>
        </a>
    @endif
</li>
