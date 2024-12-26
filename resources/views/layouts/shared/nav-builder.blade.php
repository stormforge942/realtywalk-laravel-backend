<div class="c-sidebar-brand">
  @if($logo = \App\Models\Setting::getLogo())
    <img
      class="c-sidebar-brand-full"
      src="{{ $logo }}"
      height="39"
      alt="RW Logo"
    />
  @else
    <span class="c-sidebar-brand-full">IMAGE LOGO</span>
  @endif

  @php
  $builder_settings = \App\Models\Setting::getBy('builder_settings');
  $builder_link_enabled = $builder_settings['builder_link_enabled'] ?? true;
  $builder_menu_id = \App\Models\Menu::where('name', 'Builders')->first()?->id;
  @endphp

  @if($logo = \App\Models\Setting::getLogo(true))
    <img
      class="c-sidebar-brand-minimized"
      src="{{ $logo }}"
      height="39"
      alt="RW Logo"
    />
  @else
    <span class="c-sidebar-brand-minimized">LOGO</span>
  @endif

</div>

<ul class="c-sidebar-nav">
  @if(isset($appMenus['sidebar menu']))

  @foreach($appMenus['sidebar menu'] as $menuel)
    @if($menuel['slug'] === 'link')
      @if(!$builder_link_enabled && $menuel['parent_id'] == $builder_menu_id)
        @continue
      @endif
      <li class="c-sidebar-nav-item">
        <a class="c-sidebar-nav-link" href="{{ $menuel['href'] }}system">
          @if($menuel['hasIcon'] === true && $menuel['iconType'] === 'coreui')
            <i class="{{ $menuel['icon'] }} c-sidebar-nav-icon"></i>
          @endif

          {{ $menuel['name'] }}
        </a>
      </li>
    @elseif($menuel['slug'] === 'dropdown')
      @if(!$builder_link_enabled && $menuel['id'] == $builder_menu_id)
        @continue
      @endif

      @php renderDropdown($menuel) @endphp
    @elseif($menuel['slug'] === 'title')
      <li class="c-sidebar-nav-title">
        @if($menuel['hasIcon'] === true && $menuel['iconType'] === 'coreui')
          <i class="{{ $menuel['icon'] }} c-sidebar-nav-icon"></i>
        @endif

        {{ $menuel['name'] }}
      </li>
    @endif
  @endforeach

  @endif
</ul>

<button
  class="c-sidebar-minimizer c-class-toggler"
  type="button"
  data-target="_parent"
  data-class="c-sidebar-minimized"
></button>
