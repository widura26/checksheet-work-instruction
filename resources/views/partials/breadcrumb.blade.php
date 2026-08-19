@if(isset($breadcrumbs) && count($breadcrumbs) > 0)
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb bg-light rounded p-3">
        @foreach($breadcrumbs as $breadcrumb)
            @if($loop->last)
                <li class="breadcrumb-item active" aria-current="page">
                    <i class="{{ $breadcrumb['icon'] ?? 'fas fa-circle' }} me-1"></i>
                    {{ $breadcrumb['title'] }}
                </li>
            @else
                <li class="breadcrumb-item">
                    @if(isset($breadcrumb['url']))
                        <a href="{{ $breadcrumb['url'] }}" class="text-decoration-none">
                            <i class="{{ $breadcrumb['icon'] ?? 'fas fa-home' }} me-1"></i>
                            {{ $breadcrumb['title'] }}
                        </a>
                    @else
                        <i class="{{ $breadcrumb['icon'] ?? 'fas fa-circle' }} me-1"></i>
                        {{ $breadcrumb['title'] }}
                    @endif
                </li>
            @endif
        @endforeach
    </ol>
</nav>
@endif