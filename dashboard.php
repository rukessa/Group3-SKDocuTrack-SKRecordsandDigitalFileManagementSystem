<?php
require "auth_check.php";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head> 
<body>

<div class="dashboard">
    <h1>Welcome, <?php echo $_SESSION['UserName']; ?>!</h1>

    <p>You are successfully logged in.</p>

    <a href="logout.php">Logout</a>
</div>
</body>
</html>