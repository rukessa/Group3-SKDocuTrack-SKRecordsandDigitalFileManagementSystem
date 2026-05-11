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

if (isset($_POST['change'])) {
    $current  = $_POST['current_password'];
    $newpass  = $_POST['new_password'];
    $confirm  = $_POST['confirm_password'];

    // Validate new password
    if (strlen($newpass) < 8) {
        $msg = 'New password must be at least 8 characters.';
        $msgType = 'error';
    } elseif (!preg_match('/[A-Z]/', $newpass) || !preg_match('/[0-9]/', $newpass)) {
        $msg = 'Password must contain at least one uppercase letter and one number.';
        $msgType = 'error';
    } elseif ($newpass !== $confirm) {
        $msg = 'New passwords do not match.';
        $msgType = 'error';
    } else {
        // Verify current password
        $stmt = $conn->prepare("SELECT password FROM users WHERE id=?");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if (!password_verify($current, $row['password'])) {
            $msg = 'Current password is incorrect.';
            $msgType = 'error';
        } else {
            $hashed = password_hash($newpass, PASSWORD_DEFAULT);
            $upd = $conn->prepare("UPDATE users SET password=? WHERE id=?");
            $upd->bind_param("si", $hashed, $_SESSION['user_id']);
            $upd->execute();
            $msg = 'Password changed successfully!';
            $msgType = 'success';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SK DocuTrack &mdash; Change Password</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Barlow:wght@300;400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container">
    <div class="page-header">
        <h1>Change Password</h1>
        <p>Update your account password. Main Admin only.</p>
        <div class="section-rule"></div>
    </div>

    <?php if ($msg): ?>
    <div class="alert alert-<?= $msgType ?>"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <div class="sk-form" style="max-width:480px;">
        <h3>🔑 Update Password</h3>
        <form method="POST" novalidate>
            <div class="form-group mb-4">
                <label>Current Password *</label>
                <input type="password" name="current_password" placeholder="Your current password" required>
            </div>
            <div class="form-group mb-4">
                <label>New Password *</label>
                <input type="password" name="new_password" id="npw" placeholder="Min 8 chars, 1 uppercase, 1 number">
                <div id="strengthBar" style="height:4px; border-radius:2px; margin-top:6px; transition:all .3s; background:transparent;"></div>
                <div id="strengthLabel" style="font-size:0.72rem; color:var(--muted); margin-top:3px;"></div>
            </div>
            <div class="form-group mb-4">
                <label>Confirm New Password *</label>
                <input type="password" name="confirm_password" id="cpw" placeholder="Re-enter new password">
                <div id="matchErr" style="color:#fca5a5; font-size:0.8rem; margin-top:4px; display:none;">Passwords do not match.</div>
            </div>
            <div class="form-actions">
                <button type="submit" name="change" class="btn btn-primary">Change Password</button>
                <a href="settings.php" class="btn btn-cancel btn-sm">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
const npw = document.getElementById('npw');
const bar = document.getElementById('strengthBar');
const lbl = document.getElementById('strengthLabel');
npw.addEventListener('input', () => {
    const v = npw.value;
    let score = 0;
    if (v.length >= 8)          score++;
    if (/[A-Z]/.test(v))        score++;
    if (/[0-9]/.test(v))        score++;
    if (/[^A-Za-z0-9]/.test(v)) score++;
    const colors = ['#ef4444','#f97316','#eab308','#22c55e'];
    const labels = ['Weak','Fair','Good','Strong'];
    if (!v.length) { bar.style.background='transparent'; lbl.textContent=''; return; }
    const i = Math.min(score-1,3);
    bar.style.background = colors[i];
    bar.style.width = ((score/4)*100)+'%';
    lbl.style.color = colors[i];
    lbl.textContent = labels[i];
});
const cpw = document.getElementById('cpw');
const me = document.getElementById('matchErr');
cpw.addEventListener('input', () => {
    me.style.display = (cpw.value && cpw.value !== npw.value) ? 'block' : 'none';
});
</script>
</body>
</html>