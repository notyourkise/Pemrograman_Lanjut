<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Hospital Management System' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8f9fa;
            overflow: hidden;
            height: 100vh;
        }
        
        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 260px;
            background: linear-gradient(180deg, #1a1a1a 0%, #2d2d2d 100%);
            padding: 0;
            overflow-y: auto;
            z-index: 1000;
            box-shadow: 4px 0 10px rgba(0, 0, 0, 0.1);
        }
        
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }
        
        .sidebar::-webkit-scrollbar-track {
            background: #2d2d2d;
        }
        
        .sidebar::-webkit-scrollbar-thumb {
            background: #d4af37;
            border-radius: 3px;
        }
        
        .sidebar-header {
            padding: 25px 20px;
            background: linear-gradient(135deg, #d4af37 0%, #c9a428 100%);
            text-align: center;
            border-bottom: 3px solid #d4af37;
        }
        
        .sidebar-header h4 {
            color: #1a1a1a;
            font-weight: 700;
            font-size: 18px;
            margin: 0;
            letter-spacing: 0.5px;
        }
        
        .sidebar-header .icon {
            font-size: 32px;
            color: #1a1a1a;
            margin-bottom: 8px;
        }
        
        .user-info {
            padding: 20px;
            border-bottom: 1px solid rgba(212, 175, 55, 0.2);
            text-align: center;
        }
        
        .user-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #d4af37 0%, #c9a428 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 12px;
            font-size: 28px;
            color: #1a1a1a;
            font-weight: bold;
            box-shadow: 0 4px 10px rgba(212, 175, 55, 0.3);
        }
        
        .user-info h6 {
            color: white;
            margin: 0 0 5px 0;
            font-size: 15px;
            font-weight: 600;
        }
        
        .user-role {
            display: inline-block;
            padding: 4px 12px;
            background: rgba(212, 175, 55, 0.2);
            border: 1px solid #d4af37;
            border-radius: 12px;
            font-size: 11px;
            color: #d4af37;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }
        
        .sidebar-menu {
            padding: 15px 0;
        }
        
        .menu-label {
            padding: 15px 20px 8px;
            color: #888;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
        }
        
        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 14px 20px;
            color: #ccc;
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
            font-size: 14px;
            font-weight: 500;
        }
        
        .sidebar-menu a:hover {
            background: rgba(212, 175, 55, 0.1);
            color: #d4af37;
            border-left-color: #d4af37;
            padding-left: 25px;
        }
        
        .sidebar-menu a.active {
            background: rgba(212, 175, 55, 0.15);
            color: #d4af37;
            border-left-color: #d4af37;
        }
        
        .sidebar-menu a i {
            width: 24px;
            margin-right: 12px;
            font-size: 18px;
        }
        
        .logout-btn {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 20px;
            border-top: 1px solid rgba(212, 175, 55, 0.2);
        }
        
        .logout-btn button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            border: none;
            border-radius: 8px;
            color: white;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .logout-btn button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.4);
        }
        
        .logout-btn button i {
            margin-right: 8px;
        }
        
        /* Main Content */
        .main-content {
            margin-left: 260px;
            height: 100vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        
        .top-bar {
            background: white;
            padding: 20px 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #f0f0f0;
            flex-shrink: 0;
        }
        
        .top-bar h5 {
            margin: 0;
            color: #2d2d2d;
            font-weight: 600;
            font-size: 20px;
        }
        
        .system-info {
            display: flex;
            align-items: center;
            gap: 25px;
            font-size: 13px;
            color: #666;
        }
        
        .system-info-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .system-info-item i {
            color: #d4af37;
            font-size: 16px;
        }
        
        .realtime-clock {
            font-weight: 600;
            color: #2d2d2d;
            font-size: 14px;
        }
        
        .version-badge {
            background: linear-gradient(135deg, #d4af37 0%, #c9a428 100%);
            color: #1a1a1a;
            padding: 4px 12px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 11px;
        }
        
        .top-bar .breadcrumb {
            margin: 0;
            background: transparent;
            padding: 0;
            font-size: 14px;
        }
        
        @media (max-width: 992px) {
            .system-info {
                display: none;
            }
        }
        
        .content-area {
            padding: 30px;
            overflow-y: auto;
            flex: 1;
        }
        
        .content-area::-webkit-scrollbar {
            width: 8px;
        }
        
        .content-area::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        .content-area::-webkit-scrollbar-thumb {
            background: #d4af37;
            border-radius: 4px;
        }
        
        .content-area::-webkit-scrollbar-thumb:hover {
            background: #c9a428;
        }
        
        /* Mobile Toggle */
        .mobile-toggle {
            display: none;
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1001;
            background: #1a1a1a;
            border: none;
            color: #d4af37;
            padding: 10px 15px;
            border-radius: 8px;
            cursor: pointer;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        }
        
        .mobile-toggle i {
            font-size: 20px;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                left: -260px;
                transition: left 0.3s ease;
            }
            
            .sidebar.active {
                left: 0;
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .mobile-toggle {
                display: block;
            }
            
            .top-bar {
                padding-left: 70px;
            }
        }
    </style>
</head>
<body>
    <?php $auth = Auth::getInstance(); ?>
    
    <?php if ($auth->check()): ?>
    <!-- Mobile Toggle Button -->
    <button class="mobile-toggle" onclick="toggleSidebar()">
        <i class="bi bi-list"></i>
    </button>
    
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="icon">
                <i class="bi bi-hospital-fill"></i>
            </div>
            <h4>Hospital Management</h4>
        </div>
        
        <div class="user-info">
            <div class="user-avatar">
                <?= strtoupper(substr($auth->user()->getFullName(), 0, 1)) ?>
            </div>
            <h6><?= htmlspecialchars($auth->user()->getFullName()) ?></h6>
            <span class="user-role"><?= $auth->user()->getRoleLabel() ?></span>
        </div>
        
        <div class="sidebar-menu">
            <div class="menu-label">Main Menu</div>
            
            <a href="<?= url('dashboard') ?>" class="<?= ($_SERVER['REQUEST_URI'] == url('dashboard', false) || $_SERVER['REQUEST_URI'] == base_url() . '/') ? 'active' : '' ?>">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
            
            <?php if ($auth->can('patients.view')): ?>
            <a href="<?= url('patients') ?>" class="<?= strpos($_SERVER['REQUEST_URI'], '/patients') !== false ? 'active' : '' ?>">
                <i class="bi bi-people"></i>
                <span>Patients</span>
            </a>
            <?php endif; ?>
            
            <?php if ($auth->can('appointments.view')): ?>
                <?php if ($auth->hasRole('doctor')): ?>
                <a href="<?= url('appointments/doctor') ?>" class="<?= strpos($_SERVER['REQUEST_URI'], '/appointments') !== false ? 'active' : '' ?>">
                    <i class="bi bi-calendar-check"></i>
                    <span>My Appointments</span>
                </a>
                <?php elseif ($auth->hasRole('receptionist')): ?>
                <a href="<?= url('appointments/receptionist') ?>" class="<?= strpos($_SERVER['REQUEST_URI'], '/appointments') !== false ? 'active' : '' ?>">
                    <i class="bi bi-calendar-check"></i>
                    <span>Appointments</span>
                </a>
                <?php else: ?>
                <a href="<?= url('appointments') ?>" class="<?= strpos($_SERVER['REQUEST_URI'], '/appointments') !== false ? 'active' : '' ?>">
                    <i class="bi bi-calendar-check"></i>
                    <span>Appointments</span>
                </a>
                <?php endif; ?>
            <?php endif; ?>
            
            <?php if ($auth->hasRole('admin')): ?>
            <div class="menu-label">Administration</div>
            
            <a href="<?= url('users') ?>" class="<?= strpos($_SERVER['REQUEST_URI'], '/users') !== false ? 'active' : '' ?>">
                <i class="bi bi-people-fill"></i>
                <span>Users Management</span>
            </a>
            <?php endif; ?>
        </div>
        
        <div class="logout-btn">
            <form method="POST" action="<?= url('logout') ?>">
                <button type="submit">
                    <i class="bi bi-box-arrow-right"></i>
                    Logout
                </button>
            </form>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        <div class="top-bar">
            <div>
                <h5><?= $title ?? 'Dashboard' ?></h5>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= url('dashboard') ?>">Home</a></li>
                        <?php if (isset($title) && $title != 'Dashboard'): ?>
                        <li class="breadcrumb-item active"><?= $title ?></li>
                        <?php endif; ?>
                    </ol>
                </nav>
            </div>
            
            <div class="system-info">
                <div class="system-info-item">
                    <i class="bi bi-calendar3"></i>
                    <span><?= date('D, M d, Y') ?></span>
                </div>
                <div class="system-info-item">
                    <i class="bi bi-clock"></i>
                    <span class="realtime-clock" id="realtimeClock">--:--:--</span>
                </div>
                <div class="system-info-item">
                    <span class="version-badge">v8.0</span>
                </div>
            </div>
        </div>
        
        <div class="content-area">
            <!-- Flash Messages -->
            <?= Flash::display() ?>
            
    <?php endif; ?>
    
    <script>
        // Realtime Clock
        function updateClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            const timeString = hours + ':' + minutes + ':' + seconds;
            
            const clockElement = document.getElementById('realtimeClock');
            if (clockElement) {
                clockElement.textContent = timeString;
            }
        }
        
        // Update clock immediately and then every second
        updateClock();
        setInterval(updateClock, 1000);
        
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('active');
        }
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const toggle = document.querySelector('.mobile-toggle');
            
            if (window.innerWidth <= 768) {
                if (!sidebar.contains(event.target) && !toggle.contains(event.target)) {
                    sidebar.classList.remove('active');
                }
            }
        });
    </script>
