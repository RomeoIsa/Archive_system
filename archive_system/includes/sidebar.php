<?php
if (!isset($_SESSION)) {
    session_start();
}

$role = $_SESSION['role'] ?? 'student';
$sidebarClass = ($role === 'staff') ? 'sidebar-staff' : 'sidebar-student';
?>

<div class="sidebar <?php echo $sidebarClass; ?> p-3">
    <div>
        <h4 class="text-light">📚 UniArchive</h4>
        <p class="text-light small"><?php echo ucfirst($role); ?></p>
    </div>

    <ul class="nav flex-column mt-4 flex-grow-1">

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

        <li><a class="nav-link text-light">Offline</a></li>
        <li><a class="nav-link text-light">Recents</a></li>
        <li><a class="nav-link text-light">Favorites</a></li>
        <li><a class="nav-link text-light">Messages</a></li>
        <li><a class="nav-link text-light">Profile settings</a></li>
        

    </ul>

    <a href="logout.php" class="btn btn-danger w-100 mt-auto">Logout</a>
</div>
