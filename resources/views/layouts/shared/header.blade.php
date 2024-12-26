<header class="c-header c-header-light c-header-fixed c-header-with-subheader">
  <button class="c-header-toggler c-class-toggler d-lg-none mr-auto" type="button" data-target="#sidebar" data-class="c-sidebar-show">
    <span class="c-header-toggler-icon"></span>
  </button>

  <button class="c-header-toggler c-class-toggler ml-3 d-md-down-none" type="button" data-target="#sidebar" data-class="c-sidebar-lg-show" responsive="true">
    <span class="c-header-toggler-icon"></span>
  </button>

  @php
  use App\MenuBuilder\FreelyPositionedMenus;

  if (isset($appMenus['top menu'])) {
  FreelyPositionedMenus::render($appMenus['top menu'] , 'c-header-', 'd-md-down-none');
  }
  @endphp

  <ul class="c-header-nav ml-auto mr-4">
    {{-- <li class="c-header-nav-item d-md-down-none mx-2">
      <a class="c-header-nav-link">
        <svg class="c-icon">
          <use xlink:href="{{ config('app.url') }}/icons/coreui/free.svg#cil-bell"></use>
    </svg>
    </a>
    </li>

    <li class="c-header-nav-item d-md-down-none mx-2">
      <a class="c-header-nav-link">
        <svg class="c-icon">
          <use xlink:href="{{ config('app.url') }}/icons/coreui/free.svg#cil-list-rich"></use>
        </svg>
      </a>
    </li>

    <li class="c-header-nav-item d-md-down-none mx-2">
      <a class="c-header-nav-link">
        <svg class="c-icon">
          <use xlink:href="{{ config('app.url') }}/icons/coreui/free.svg#cil-envelope-open"></use>
        </svg>
      </a>
    </li> --}}

    <li class="c-header-nav-item dropdown">
      <a class="c-header-nav-link" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
        <div class="c-avatar">
          <img class="c-avatar-img" src="{{ Auth::user()->picture_path ?: Avatar::create(Auth::user()->name)->toBase64() }}" alt="{{ Auth::user()->email }}" />
        </div>
      </a>

      <div class="dropdown-menu dropdown-menu-right pt-0">
        <div class="dropdown-header bg-light py-2">
          <strong>Settings</strong>
        </div>
        <a class="dropdown-item" href="{{ route('profile.index') }}">
          <svg class="c-icon mr-2">
            <use xlink:href="{{ config('app.url') }}/icons/coreui/free.svg#cil-user"></use>
          </svg> Profile
        </a>
        <div class="dropdown-divider"></div>
        <a class="dropdown-item" href="{{ route('logout') }}">
          <svg class="c-icon mr-2">
            <use xlink:href="{{ config('app.url') }}/icons/coreui/free.svg#cil-account-logout"></use>
          </svg> Logout
        </a>
      </div>
    </li>
  </ul>

  <div class="c-subheader px-3">
    <ol class="breadcrumb border-0 m-0">
      <li class="breadcrumb-item">
        <a href="{{ url('/system') }}">Home</a>
      </li>

      @for($i = 2; $i <= count(Request::segments()); $i++) @php $segment=Request::segment($i); @endphp @if(is_numeric($segment) && $i !=count(Request::segments())) @continue @endif @if($i < count(Request::segments())) <li class="breadcrumb-item">
        <a href="{{ route($segment.'.index') }}">
          {{ ucwords(str_replace('_', ' ', $segment)) }}
        </a>
        </li>
        @else
        <li class="breadcrumb-item active">
          {{ is_numeric($segment) ? 'Details' : ucwords(str_replace('_', ' ', $segment)) }}
        </li>
        @endif
        @endfor
    </ol>
  </div>
</header>
