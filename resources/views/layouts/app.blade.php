<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - Agrivet Supply System</title>
    <nav class="navbar navbar-light bg-transparent mb-3 d-md-none">
    <button id="sidebarToggle" class="btn btn-outline-success">
        <i class="bi bi-list"></i> Menu
    </button>
    </nav>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <!-- Tailwind (Play CDN for quick utility usage) -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        :root {
            --primary-color: #2d5a3d;
            --primary-light: #4a7c59;
            --primary-dark: #1e3a21;
            --secondary-color: #8b4513;
            --accent-color: #d4a574;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
            --border-radius: 12px;
            --box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            --box-shadow-hover: 0 20px 40px rgba(0, 0, 0, 0.12);
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, 'Apple Color Emoji', 'Segoe UI Emoji';
            background: linear-gradient(135deg, #e8f5e8 0%, #f0f8f0 100%);
            min-height: 100vh;
        }
        
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 280px;
            height: 100vh;
            background: linear-gradient(180deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            z-index: 1000;
            transition: all 0.3s ease;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        }
        
        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .sidebar-brand {
            display: flex;
            align-items: center;
            font-size: 1.2rem;
            font-weight: 600;
            color: white;
        }
        
        .sidebar-brand i {
            font-size: 1.5rem;
            margin-right: 10px;
            color: #d4a574;
        }
        
        .sidebar-user {
            padding: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .user-info {
            display: flex;
            align-items: center;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
        }
        
        .user-avatar i {
            font-size: 1.2rem;
        }
        
        .user-details {
            flex: 1;
        }
        
        .user-name {
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 2px;
        }
        
        .user-role {
            font-size: 0.8rem;
        }
        
        .role-badge {
            background: rgba(255, 255, 255, 0.2);
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        .sidebar-nav {
            flex: 1;
            padding: 20px 0;
        }
        
        .nav-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .nav-item {
            margin-bottom: 4px;
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s ease;
            border-radius: 0;
            position: relative;
        }
        
        .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            transform: translateX(5px);
        }
        
        .nav-link.active {
            background: rgba(255, 255, 255, 0.15);
            color: white;
            font-weight: 600;
        }
        
        .nav-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: #d4a574;
        }
        
        .nav-link i {
            font-size: 1.1rem;
            margin-right: 12px;
            width: 20px;
            text-align: center;
        }
        
        .sidebar-footer {
            padding: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .logout-btn {
            width: 100%;
            background: rgba(255, 255, 255, 0.1);
            border: none;
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .logout-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-1px);
        }
        
        .logout-btn i {
            margin-right: 10px;
        }
        
        .navbar-brand {
            font-weight: 600;
        }
        
        .card {
            border: 1px solid rgba(45, 90, 61, 0.08);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            margin-bottom: 1.5rem;
            overflow: hidden;
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: var(--box-shadow-hover);
        }
        /* Blur helper applied to the page wrapper when a modal needs focus */
        .blurred .main-content,
        .blurred .sidebar {
            filter: blur(6px) grayscale(0.02);
            transition: filter 200ms ease;
            pointer-events: none;
            user-select: none;
        }
        /* Do not apply blur to modal elements (modal is appended to body)
           but keep sidebar clickable disabled visually while modal open */
        
        .bg-primary-custom {
            background-color: var(--primary-color) !important;
        }
        
        .btn-primary-custom {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            border-radius: var(--border-radius);
            padding: 10px 16px;
            font-weight: 600;
            box-shadow: 0 6px 14px rgba(45, 90, 61, 0.15);
        }
        
        .btn-primary-custom:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
            transform: translateY(-1px);
        }

        .btn {
            border-radius: var(--border-radius);
        }

        .table {
            background: rgba(255, 255, 255, 0.98);
            border-radius: var(--border-radius);
            overflow: hidden;
        }

        .table thead.table-dark th {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            border: none;
        }

        .form-control {
            border: 2px solid #e3e6f0;
            border-radius: var(--border-radius);
            padding: 10px 12px;
            transition: all .2s ease;
            background: rgba(255, 255, 255, 0.96);
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(45, 90, 61, 0.15);
        }
        
        .critical-stock {
            background-color: #fff3cd !important;
            border-left: 4px solid #ffc107;
        }
        
        .low-stock {
            background-color: #f8d7da !important;
            border-left: 4px solid #dc3545;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 18px 20px;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            margin-bottom: 18px;
        }
        .page-header h1 { margin: 0; font-size: 1.35rem; font-weight: 600; }
        
        .main-content {
            margin-left: 280px;
            min-height: 100vh;
            background: linear-gradient(135deg, #e8f5e8 0%, #f0f8f0 100%);
            padding: 20px;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .main-content {
                margin-left: 0;
            }
        }


        .sidebar {
    position: fixed;
    top: 0;
    left: 0;
    width: 280px;
    height: 100vh;
    background: linear-gradient(180deg, var(--primary-color) 0%, var(--primary-dark) 100%);
    color: white;
    z-index: 1050;
    transition: transform 0.3s ease;
    box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
}

/* Hidden by default on mobile */
@media (max-width: 768px) {
    .sidebar {
        transform: translateX(-100%);
    }

    .sidebar.active {
        transform: translateX(0);
    }
}

    </style>
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        @include('layouts.navigation')
        
        <!-- Main Content -->
        <div class="main-content">
            @yield('content')
        </div>
    </div>

    <!-- Bootstrap & jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <!-- Chart.js for dashboards -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.3.0/dist/chart.umd.min.js"></script>


    <script src="{{ asset('js/inventory.js') }}"></script>
    <script src="{{ asset('js/heartbeat.js') }}"></script>
    
    @stack('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.querySelector('.sidebar');
        const toggleBtn = document.getElementById('sidebarToggle');

        toggleBtn?.addEventListener('click', function() {
            sidebar.classList.toggle('active');
        });
    });
</script>

<!-- Idle warning modal and idle detection -->
<div class="modal fade" id="idleModal" tabindex="-1" aria-labelledby="idleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="idleModalLabel">You're about to be logged out</h5>
      </div>
      <div class="modal-body">
        <p>We detected inactivity. You will be logged out in <strong id="idleCountdown">60</strong> seconds. Move your mouse or press a key to stay signed in.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" id="staySignedIn">Stay signed in</button>
        <button type="button" class="btn btn-danger" id="forceLogout">Log out now</button>
      </div>
    </div>
  </div>
</div>

<script>
// Idle detection + auto-logout
(function(){
    const idleThreshold = 5 * 60 * 1000; // 5 minutes
    const warningDuration = 60; // seconds countdown in modal
    let idleTimer = null;
    let countdownTimer = null;
    let remaining = warningDuration;
    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    function resetIdleTimer() {
        // if modal showing, hide and reset countdown
        const modalEl = document.getElementById('idleModal');
        if ($(modalEl).hasClass('show')) {
            $(modalEl).modal('hide');
        }
        remaining = warningDuration;
        document.getElementById('idleCountdown').textContent = remaining;

        if (countdownTimer) {
            clearInterval(countdownTimer);
            countdownTimer = null;
        }

        if (idleTimer) clearTimeout(idleTimer);
        idleTimer = setTimeout(onIdleReached, idleThreshold);
    }

    function onIdleReached() {
        // show modal and start countdown
        const modalEl = document.getElementById('idleModal');
        $(modalEl).modal({backdrop: 'static', keyboard: false});
        $(modalEl).modal('show');

        remaining = warningDuration;
        document.getElementById('idleCountdown').textContent = remaining;

        countdownTimer = setInterval(function(){
            remaining -= 1;
            document.getElementById('idleCountdown').textContent = remaining;
            if (remaining <= 0) {
                clearInterval(countdownTimer);
                countdownTimer = null;
                doLogout();
            }
        }, 1000);
    }

    function doLogout() {
        fetch('{{ route('logout') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrf,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({})
        }).finally(()=>{
            // Redirect to login page after logout
            window.location.href = '{{ route('login') }}';
        });
    }

    // user interactions that keep session alive
    ['mousemove','mousedown','keydown','scroll','touchstart'].forEach(function(evt){
        window.addEventListener(evt, resetIdleTimer, {passive: true});
    });

    // stay signed in button
    document.addEventListener('click', function(e){
        if (e.target && e.target.id === 'staySignedIn') {
            resetIdleTimer();
        }
        if (e.target && e.target.id === 'forceLogout') {
            doLogout();
        }
    });

    // start
    resetIdleTimer();
})();
</script>

</body>
</html>