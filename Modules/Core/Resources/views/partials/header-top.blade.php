<nav class="navbar navbar-expand topbar mb-4 static-top">
  <button id="sidebarToggleTop" class="btn d-md-none rounded-circle mr-3">
  <i class="fa fa-bars"></i>
  </button>
  <!-- Topbar Navbar -->
  <ul class="navbar-nav">
    {!! menuHeaderTopLeft() !!}
  </ul>
 

  <ul class="navbar-nav ml-auto">
    
    {!! menuHeaderTop() !!}

    <li class="nav-item dropdown no-arrow">
      <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <span class="mr-2 d-none d-lg-inline text-gray-600 small">
          @if($active_language)
            {{ $active_language }}
          @endif
        </span>
        <i class="fas fa-language fa-lg"></i>
      </a>
      <!-- Dropdown - User Information -->
      <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
        @foreach($languages as $item)
        <a href="{{ route('localize', $item) }}" rel="alternate" hreflang="{{ $item }}" class="dropdown-item">
          {{ $item }}
        </a>
        @endforeach
      </div>
    </li>


    <li class="nav-item dropdown no-arrow">
      <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <span class="mr-2 d-none d-lg-inline text-gray-600 small">{{ Auth::user()->name }}</span>
        <i class="fas fa-laugh-wink"></i>
      </a>
      <!-- Dropdown - User Information -->
      <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
        @can('admin')
        <a class="dropdown-item" href="{{ route('settings.index') }}">
          <i class="fas fa-user-secret"></i>
          @lang('Administrator')
        </a>
        @endcan
        <a class="dropdown-item" href="{{ route('accountsettings.index') }}">
          <i class="fas fa-user"></i>
          @lang('Account Settings')
        </a>
        <div class="dropdown-divider"></div>
        <a class="dropdown-item" href="{{ route('logout') }}">
          <i class="fas fa-sign-out-alt"></i>
          @lang('Logout')
        </a>
      </div>
    </li>
  </ul>
</nav>