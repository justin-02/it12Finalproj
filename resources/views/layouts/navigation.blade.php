<div class="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-brand">
            <i class="bi bi-tree"></i>
            <span>Demonteverde Agrivet</span>
        </div>
    </div>
    
    @auth
    <div class="sidebar-user">
        <div class="user-info">
            <div class="user-avatar">
                <i class="bi bi-person-circle"></i>
            </div>
            <div class="user-details">
                <div class="user-name">{{ auth()->user()->name }}</div>
                <div class="user-role">
                    <span class="role-badge">{{ ucfirst(auth()->user()->role) }}</span>
                </div>
            </div>
        </div>
    </div>
    
    <nav class="sidebar-nav">
        <ul class="nav-list">
            @if(auth()->user()->isAdmin())
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('admin/dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                        <i class="bi bi-speedometer2"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('admin/sales-report') ? 'active' : '' }}" href="{{ route('admin.sales-report') }}">
                        <i class="bi bi-graph-up"></i>
                        <span>Sales Report</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('admin/inventory-monitor') ? 'active' : '' }}" href="{{ route('admin.inventory-monitor') }}">
                        <i class="bi bi-boxes"></i>
                        <span>Inventory Monitor</span>
                    </a>
                </li>
            @elseif(auth()->user()->isInventory())
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('inventory/dashboard') ? 'active' : '' }}" href="{{ route('inventory.dashboard') }}">
                        <i class="bi bi-speedometer2"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('inventory/products') ? 'active' : '' }}" href="{{ route('inventory.products') }}">
                        <i class="bi bi-box-seam"></i>
                        <span>Products</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#stockInModal">
                        <i class="bi bi-arrow-down-circle"></i>
                        <span>Stock In</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#stockOutModal">
                        <i class="bi bi-arrow-up-circle"></i>
                        <span>Stock Out</span>
                    </a>
                </li>
            @elseif(auth()->user()->isCashier())
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('cashier/dashboard') ? 'active' : '' }}" href="{{ route('cashier.dashboard') }}">
                        <i class="bi bi-speedometer2"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('cashier/transactions') ? 'active' : '' }}" href="{{ route('cashier.transactions') }}">
                        <i class="bi bi-receipt"></i>
                        <span>Transactions</span>
                    </a>
                </li>
            @elseif(auth()->user()->isHelper())
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('helper/dashboard') ? 'active' : '' }}" href="{{ route('helper.dashboard') }}">
                        <i class="bi bi-speedometer2"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
            @endif
        </ul>
    </nav>
    
    <div class="sidebar-footer">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-btn">
                <i class="bi bi-box-arrow-right"></i>
                <span>Logout</span>
            </button>
        </form>
    </div>
    @endauth
</div>