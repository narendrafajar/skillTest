<nav class="sidebar sidebar-offcanvas" id="sidebar">
        <ul class="nav">
          <li class="nav-item">
            <a class="nav-link" href="{{route('dashboard')}}">
              <i class="mdi mdi-grid-large menu-icon"></i>
              <span class="menu-title">{{__('Dashboard')}}</span>
            </a>
          </li>
          <li class="nav-item nav-category">{{__('Products')}}</li>
          <li class="nav-item">
            <a class="nav-link" href="{{route('products')}}">
              <i class="menu-icon mdi mdi-file-document"></i>
              <span class="menu-title">{{__('Product List')}}</span>
            </a>
          </li>
        </ul>
      </nav>