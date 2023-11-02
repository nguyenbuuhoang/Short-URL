<div id="sidebar-wrapper" data-simplebar="" data-simplebar-auto-hide="true">
    <div class="brand-logo">
        <a href="{{ route('admin.index') }}">
            <img src="{{ asset('template/assets/images/logo-icon.png') }}" class="logo-icon" alt="logo icon">
            <h5 class="logo-text">Dashtreme Admin</h5>
        </a>
    </div>
    <ul class="sidebar-menu do-nicescroll">
        <li class="sidebar-header">MAIN NAVIGATION</li>
        <li>
            <a href="{{ route('admin.index') }}">
                <i class="zmdi zmdi-view-dashboard"></i> <span>Dashboard</span>
            </a>
        </li>
        <li>
            <a href="#">
                <i class="zmdi zmdi-link"></i> <span>Quản lý User | Role</span>
            </a>
        </li>
        <li>
            <a href="#">
                <i class="zmdi zmdi-shield-security"></i> <span>Phân quyền Role</span>
            </a>
        </li>
    </ul>
</div>
