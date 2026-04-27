<?php
session_start();
include("config.php"); // IMPORTANT: must be first

if (!isset($_POST['username']) || !isset($_POST['password'])) {
    die("No input received");
}

$username = $_POST['username'];
$password = $_POST['password'];

if (!$conn) {
    die("Database connection failed");
}

$stmt = $conn->prepare("SELECT * FROM user WHERE UserName = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    if ($password == $row['Password']) {
        $_SESSION['UserID'] = $row['UserID'];
        $_SESSION['Role'] = $row['Role'];

        header("Location: dashboard.php");
        exit;
    } else {
        echo "Wrong password";
    }
} else {
    echo "User not found";
}
?>