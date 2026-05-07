<?php
$conn = new mysqli("localhost", "root", "", "skdocutrack");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$admin_hash = password_hash('Admin@1234', PASSWORD_DEFAULT);
$staff_hash = password_hash('Staff@1234', PASSWORD_DEFAULT);

// Delete old users and re-insert with fresh hashes
$conn->query("DELETE FROM users");

$conn->query("INSERT INTO users (username, password, role) VALUES 
    ('main_admin', '$admin_hash', 'main_admin'),
    ('staff', '$staff_hash', 'staff')
");

echo "<h2>✅ Done! Passwords reset successfully.</h2>";
echo "<p>Main Admin &rarr; username: <b>main_admin</b> / password: <b>Admin@1234</b></p>";
echo "<p>Staff &rarr; username: <b>staff</b> / password: <b>Staff@1234</b></p>";
echo "<br><a href='login.php'>Go to Login</a>";
echo "<br><br><b style='color:red'>⚠️ DELETE this file immediately after logging in!</b>";
?>