<?php
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="sidebar">
    <h2>SK System</h2>

    <a href="#">Dashboard</a>
    <a href="#">Projects</a>
    <a href="#">Documents</a>
    <a href="#">Financial Records</a>

    <?php if($_SESSION['role'] == 'admin'): ?>
        <a href="#">Manage Users</a>
    <?php endif; ?>

    <a href="logout.php" class="logout">Logout</a>
</div>

<div class="main">
    <h1>Welcome to Dashboard</h1>

    <div class="cards">
        <div class="card">Projects</div>
        <div class="card">Members</div>
        <div class="card">Funds</div>
    </div>
</div>

</body>
</html>