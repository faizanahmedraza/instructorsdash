<li class="nav-item {{ (request()->is('reports.index')) ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('reports.index') }}">
        <i class="fas fa-book-open"></i>
        <span>@lang('Reports')</span></a>
</li>
