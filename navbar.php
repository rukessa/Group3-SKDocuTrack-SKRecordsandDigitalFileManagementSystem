<?php
// navbar.php — include on every protected page
// Requires $conn and $_SESSION already set via config.php
$current = basename($_SERVER['PHP_SELF']);
?>
<nav class="topnav">
    <div class="nav-brand">
        <img src="sk_logo.png" alt="SK" class="nav-logo">
        <span>SK DocuTrack</span>
    </div>
    <div class="nav-links">
        <a href="dashboard.php" class="<?= $current=='dashboard.php'?'active':'' ?>">Dashboard</a>
        <a href="projects.php"  class="<?= $current=='projects.php' ?'active':'' ?>">Projects</a>
        <a href="members.php"   class="<?= $current=='members.php'  ?'active':'' ?>">Members</a>
        <a href="funds.php"     class="<?= $current=='funds.php'    ?'active':'' ?>">Funds</a>
        <?php if ($_SESSION['role'] === 'main_admin'): ?>
        <a href="settings.php"  class="<?= $current=='settings.php' ?'active':'' ?>">Settings</a>
        <?php endif; ?>
    </div>
    <div class="nav-user">
        <span class="nav-username">
            <?= htmlspecialchars($_SESSION['username']) ?>
            <span class="role-badge <?= $_SESSION['role'] === 'main_admin' ? 'badge-admin' : 'badge-staff' ?>">
                <?= $_SESSION['role'] === 'main_admin' ? 'Main Admin' : 'Staff' ?>
            </span>
        </span>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>
</nav>