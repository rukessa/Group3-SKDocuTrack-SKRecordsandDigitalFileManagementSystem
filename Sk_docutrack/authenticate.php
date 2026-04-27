<?php
include 'config.php';

$username = $_POST['username'];
$password = $_POST['password'];

$sql = "SELECT * FROM User WHERE Username='$username' AND Password='$password'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    $_SESSION['user_id'] = $row['UserID'];
    $_SESSION['role'] = $row['Role'];

    header("Location: dashboard.php");
} else {
    echo "Invalid login!";
}
?>