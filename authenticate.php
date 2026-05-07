<?php
include 'config.php';

if (!isset($_POST['username']) || !isset($_POST['password'])) {
    header("Location: login.php?error=Invalid+request.");
    exit();
}

$username = trim($_POST['username']);
$password = $_POST['password'];

// Basic server-side validation
if (empty($username) || strlen($password) < 6) {
    header("Location: login.php?error=Invalid+username+or+password.");
    exit();
}

$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    if (password_verify($password, $row['password'])) {
        // Regenerate session ID on login (security best practice)
        session_regenerate_id(true);
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['role'] = $row['role'];

        header("Location: dashboard.php");
        exit();
    } else {
        header("Location: login.php?error=Incorrect+password.+Please+try+again.");
        exit();
    }
} else {
    header("Location: login.php?error=No+account+found+with+that+username.");
    exit();
}
?>