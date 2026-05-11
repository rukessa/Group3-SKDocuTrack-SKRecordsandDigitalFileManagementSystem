<?php
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SESSION['role'] !== 'main_admin') {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SK DocuTrack &mdash; Settings</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Barlow:wght@300;400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container">
    <div class="page-header">
        <h1>Settings</h1>
        <p>Manage system users and administrative configuration.</p>
        <div class="section-rule"></div>
    </div>

    <div class="card-grid">
        <!-- Create User -->
        <a href="create_user.php" style="text-decoration:none;">
            <div class="card" style="cursor:pointer; text-align:center; padding:36px 24px;">
                <div style="font-size:2.5rem; margin-bottom:14px;">👤</div>
                <h3 style="font-family:'Playfair Display',serif; color:#fff; margin-bottom:8px;">Create User</h3>
                <p style="color:var(--muted); font-size:0.85rem;">Add a new admin or staff account to the system.</p>
            </div>
        </a>

        <!-- Change Password -->
        <a href="change_password.php" style="text-decoration:none;">
            <div class="card" style="cursor:pointer; text-align:center; padding:36px 24px;">
                <div style="font-size:2.5rem; margin-bottom:14px;">🔑</div>
                <h3 style="font-family:'Playfair Display',serif; color:#fff; margin-bottom:8px;">Change Password</h3>
                <p style="color:var(--muted); font-size:0.85rem;">Update your admin account password securely.</p>
            </div>
        </a>

        <!-- Manage Users -->
        <a href="manage_users.php" style="text-decoration:none;">
            <div class="card" style="cursor:pointer; text-align:center; padding:36px 24px;">
                <div style="font-size:2.5rem; margin-bottom:14px;">⚙️</div>
                <h3 style="font-family:'Playfair Display',serif; color:#fff; margin-bottom:8px;">Manage Users</h3>
                <p style="color:var(--muted); font-size:0.85rem;">View and delete system user accounts.</p>
            </div>
        </a>
    </div>
</div>
</body>
</html>