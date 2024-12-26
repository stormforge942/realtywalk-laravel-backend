
<li class="nav-item {{ Request::is('users*') ? 'active' : '' }}">
    <a class="nav-link" href="{!! route('users.index') !!}">
        <i class="nav-icon icon-cursor"></i>
        <span>Users</span>
    </a>
</li>
<li class="nav-item {{ Request::is('builders*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('builders.index') }}">
        <i class="nav-icon icon-cursor"></i>
        <span>Builders</span>
    </a>
</li>
