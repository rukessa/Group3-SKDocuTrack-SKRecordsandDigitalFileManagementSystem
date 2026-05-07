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

if (isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id = (int)$_POST['id'];
    // Prevent self-deletion
    if ($id === (int)$_SESSION['user_id']) {
        $msg = 'You cannot delete your own account.';
        $msgType = 'error';
    } elseif ($id) {
        $conn->query("DELETE FROM users WHERE id=$id");
        $msg = 'User deleted.';
        $msgType = 'success';
    }
}

$users = $conn->query("SELECT id, username, role, created_at FROM users ORDER BY created_at ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SK DocuTrack &mdash; Manage Users</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Barlow:wght@300;400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container">
    <div class="page-header">
        <div class="flex justify-between items-center">
            <div>
                <h1>Manage Users</h1>
                <p>View and delete system accounts.</p>
            </div>
            <a href="create_user.php" class="btn btn-primary">+ Create User</a>
        </div>
        <div class="section-rule"></div>
    </div>

    <?php if ($msg): ?>
    <div class="alert alert-<?= $msgType ?>"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <table class="sk-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Username</th>
                <th>Role</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1; while ($row = $users->fetch_assoc()): ?>
            <tr>
                <td data-label="#"><?= $i++ ?></td>
                <td data-label="Username">
                    <strong><?= htmlspecialchars($row['username']) ?></strong>
                    <?php if ($row['id'] == $_SESSION['user_id']): ?>
                    <span style="font-size:0.7rem; color:var(--yellow); margin-left:6px;">(You)</span>
                    <?php endif; ?>
                </td>
                <td data-label="Role">
                    <span class="badge <?= $row['role']==='main_admin'?'badge-admin':'badge-staff' ?>">
                        <?= $row['role'] === 'main_admin' ? 'Main Admin' : 'Staff' ?>
                    </span>
                </td>
                <td data-label="Created" style="color:var(--muted); font-size:0.85rem;">
                    <?= date('M d, Y', strtotime($row['created_at'])) ?>
                </td>
                <td data-label="Actions">
                    <?php if ($row['id'] != $_SESSION['user_id']): ?>
                    <form method="POST" style="display:inline;"
                        onsubmit="return confirm('Delete user \'<?= htmlspecialchars($row['username']) ?>\'?')">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                    </form>
                    <?php else: ?>
                    <span style="color:var(--muted); font-size:0.8rem;">—</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>