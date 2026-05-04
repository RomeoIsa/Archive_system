<?php
if (!isset($_SESSION)) {
    session_start();
}

$role = $_SESSION['role'] ?? 'student';
$sidebarClass = ($role === 'staff') ? 'sidebar-staff' : 'sidebar-student';
?>
<head>
     <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<div class="sidebar <?php echo $sidebarClass; ?> p-3">
    <div>
        <h4 class="text-light"><i class="bi bi-bar-chart-line-fill"></i> UniArchive</h4>
        <p class="text-light small"><?php echo ucfirst($role); ?></p>
    </div>

    <ul class="nav flex-column mt-4 flex-grow-1">

    <!-- BASE MENU (EVERYONE) -->
    <li>
        <a href="dashboard.php" class="nav-link text-light <?php echo ($activePage == 'dashboard') ? 'active' : ''; ?>">
            Dashboard
        </a>
    </li>

    <li>
        <a href="library.php" class="nav-link text-light <?php echo ($activePage == 'library') ? 'active' : ''; ?>">
            Browse Materials
        </a>
    </li>

    <li>
        <a href="files.php" class="nav-link text-light <?php echo ($activePage == 'files') ? 'active' : ''; ?>">
            My Uploads
        </a>
    </li>

    <!-- STAFF ONLY MENU -->
    <?php if ($role === 'staff'): ?>

        <li>
            <a href="#" class="nav-link text-light <?php echo ($activePage == 'earnings') ? 'active' : ''; ?>">
                Earnings
            </a>
        </li>

        <li>
            <a href="#" class="nav-link text-light <?php echo ($activePage == 'downloads') ? 'active' : ''; ?>">
                Downloads & Analytics
            </a>
        </li>

    <?php endif; ?>

    <!-- STATIC ITEMS (UNCHANGED) -->
    <li><a class="nav-link text-light">Favorites</a></li>
    <li><a class="nav-link text-light">Recents</a></li>
    <li><a class="nav-link text-light">Messages</a></li>
    <li><a class="nav-link text-light">Profile settings</a></li>

</ul>

    <a href="logout.php" class="btn btn-danger w-100 mt-auto">Logout</a>
</div>
