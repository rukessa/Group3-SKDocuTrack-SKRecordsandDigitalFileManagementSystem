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

$msg = '';
$msgType = '';

if (isset($_POST['create'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm_password'];
    $role     = $_POST['role'];

    // Validation
    if (empty($username) || strlen($username) < 3) {
        $msg = 'Username must be at least 3 characters.';
        $msgType = 'error';
    } elseif (strlen($password) < 8) {
        $msg = 'Password must be at least 8 characters.';
        $msgType = 'error';
    } elseif (!preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $msg = 'Password must contain at least one uppercase letter and one number.';
        $msgType = 'error';
    } elseif ($password !== $confirm) {
        $msg = 'Passwords do not match.';
        $msgType = 'error';
    } else {
        // Check duplicate username
        $check = $conn->prepare("SELECT id FROM users WHERE username=?");
        $check->bind_param("s", $username);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $msg = 'Username already exists.';
            $msgType = 'error';
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?,?,?)");
            $stmt->bind_param("sss", $username, $hashed, $role);
            if ($stmt->execute()) {
                $msg = "User '{$username}' created successfully!";
                $msgType = 'success';
            } else {
                $msg = 'Database error. Please try again.';
                $msgType = 'error';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SK DocuTrack &mdash; Create User</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Barlow:wght@300;400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container">
    <div class="page-header">
        <h1>Create User</h1>
        <p>Add a new system account. Main Admin only.</p>
        <div class="section-rule"></div>
    </div>

    <?php if ($msg): ?>
    <div class="alert alert-<?= $msgType ?>"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <div class="sk-form" style="max-width:520px;">
        <h3>👤 New Account</h3>
        <form method="POST" id="createForm" novalidate>
            <div class="form-group mb-4">
                <label>Username *</label>
                <input type="text" name="username" id="usernameField" placeholder="Minimum 3 characters" required>
                <div id="usernameErr" style="color:#fca5a5; font-size:0.8rem; margin-top:4px; display:none;"></div>
            </div>
            <div class="form-group mb-4">
                <label>Role *</label>
                <select name="role" required>
                    <option value="main_admin">Main Admin</option>
                    <option value="staff" selected>Staff</option>
                </select>
            </div>
            <div class="form-group mb-4">
                <label>Password *</label>
                <input type="password" name="password" id="passwordField" placeholder="Min 8 chars, 1 uppercase, 1 number">
                <div id="strengthBar" style="height:4px; border-radius:2px; margin-top:6px; transition:all .3s; background:transparent;"></div>
                <div id="strengthLabel" style="font-size:0.72rem; color:var(--muted); margin-top:3px;"></div>
            </div>
            <div class="form-group mb-4">
                <label>Confirm Password *</label>
                <input type="password" name="confirm_password" id="confirmField" placeholder="Re-enter password">
                <div id="matchErr" style="color:#fca5a5; font-size:0.8rem; margin-top:4px; display:none;">Passwords do not match.</div>
            </div>
            <div class="form-actions">
                <button type="submit" name="create" class="btn btn-primary">Create Account</button>
                <a href="settings.php" class="btn btn-cancel btn-sm">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
// Password strength
const pw = document.getElementById('passwordField');
const bar = document.getElementById('strengthBar');
const lbl = document.getElementById('strengthLabel');
pw.addEventListener('input', () => {
    const v = pw.value;
    let score = 0;
    if (v.length >= 8)         score++;
    if (/[A-Z]/.test(v))       score++;
    if (/[0-9]/.test(v))       score++;
    if (/[^A-Za-z0-9]/.test(v))score++;
    const colors = ['#ef4444','#f97316','#eab308','#22c55e'];
    const labels = ['Weak','Fair','Good','Strong'];
    if (v.length === 0) { bar.style.background='transparent'; lbl.textContent=''; return; }
    const i = Math.min(score-1, 3);
    bar.style.background = colors[i];
    bar.style.width = ((score/4)*100) + '%';
    lbl.style.color = colors[i];
    lbl.textContent = labels[i];
});

// Match check
const cf = document.getElementById('confirmField');
const me = document.getElementById('matchErr');
cf.addEventListener('input', () => {
    me.style.display = (cf.value && cf.value !== pw.value) ? 'block' : 'none';
});
</script>
</body>
</html>