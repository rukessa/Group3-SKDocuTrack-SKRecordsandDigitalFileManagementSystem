<?php
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$isAdmin = true; // both roles can manage content
$msg = '';
$msgType = '';

// Handle Add
if (isset($_POST['action']) && $_POST['action'] === 'add') {
    $name    = trim($_POST['name']);
    $role    = trim($_POST['role']);
    $contact = trim($_POST['contact']);
    $address = trim($_POST['address']);
    if ($name && $role) {
        $stmt = $conn->prepare("INSERT INTO members (name, role, contact, address) VALUES (?,?,?,?)");
        $stmt->bind_param("ssss", $name, $role, $contact, $address);
        $stmt->execute();
        $msg = 'Member added successfully!';
        $msgType = 'success';
    } else {
        $msg = 'Name and role are required.';
        $msgType = 'error';
    }
}

// Handle Edit
if (isset($_POST['action']) && $_POST['action'] === 'edit') {
    $id      = (int)$_POST['id'];
    $name    = trim($_POST['name']);
    $role    = trim($_POST['role']);
    $contact = trim($_POST['contact']);
    $address = trim($_POST['address']);
    if ($id && $name && $role) {
        $stmt = $conn->prepare("UPDATE members SET name=?, role=?, contact=?, address=? WHERE id=?");
        $stmt->bind_param("ssssi", $name, $role, $contact, $address, $id);
        $stmt->execute();
        $msg = 'Member updated!';
        $msgType = 'success';
    }
}

// Handle Delete
if (isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id = (int)$_POST['id'];
    if ($id) {
        $conn->query("DELETE FROM members WHERE id=$id");
        $msg = 'Member removed.';
        $msgType = 'success';
    }
}

// Fetch members ordered by role
$members = $conn->query("SELECT * FROM members ORDER BY role, name");

// Role order for grouping
$roleOrder = [
    'SK Chairperson',
    'SK Kagawad',
    'SK Secretary',
    'SK Treasurer',
    'SK Auditor',
    'SK PRO',
    'SK Member',
];

// Group members by role
$grouped = [];
while ($row = $members->fetch_assoc()) {
    $grouped[$row['role']][] = $row;
}

// Sort groups
uksort($grouped, function($a, $b) use ($roleOrder) {
    $ai = array_search($a, $roleOrder);
    $bi = array_search($b, $roleOrder);
    if ($ai === false) $ai = 99;
    if ($bi === false) $bi = 99;
    return $ai <=> $bi;
});
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SK DocuTrack &mdash; Members</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Barlow:wght@300;400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container">
    <div class="page-header">
        <h1>Members</h1>
        <p>SK membership list, organized by role.</p>
        <div class="section-rule"></div>
    </div>

    <?php if ($msg): ?>
    <div class="alert alert-<?= $msgType ?>"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <!-- Add Form -->
    <div class="sk-form">
        <h3>➕ Add New Member</h3>
        <form method="POST">
            <input type="hidden" name="action" value="add">
            <div class="form-row">
                <div class="form-group">
                    <label>Full Name *</label>
                    <input type="text" name="name" placeholder="e.g. Juan dela Cruz" required>
                </div>
                <div class="form-group">
                    <label>Position / Role *</label>
                    <select name="role" required>
                        <option value="">— Select Role —</option>
                        <option>SK Chairperson</option>
                        <option>SK Kagawad</option>
                        <option>SK Secretary</option>
                        <option>SK Treasurer</option>
                        <option>SK Auditor</option>
                        <option>SK PRO</option>
                        <option>SK Member</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Contact No.</label>
                    <input type="text" name="contact" placeholder="09XXXXXXXXX">
                </div>
                <div class="form-group">
                    <label>Address</label>
                    <input type="text" name="address" placeholder="Brgy. San Antonio">
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Add Member</button>
            </div>
        </form>
    </div>

    <!-- Members grouped by role -->
    <?php if (empty($grouped)): ?>
    <div class="empty-state"><div class="icon">👥</div><p>No members yet.</p></div>
    <?php else: ?>
    <?php foreach ($grouped as $roleName => $roleMembers): ?>
    <div style="margin-bottom: 28px;">
        <div class="flex items-center gap-3 mb-4">
            <h2 style="font-family:'Playfair Display',serif; font-size:1.1rem; color:var(--yellow);">
                <?= htmlspecialchars($roleName) ?>
            </h2>
            <span class="badge badge-planned"><?= count($roleMembers) ?></span>
        </div>

        <table class="sk-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Contact</th>
                    <th>Address</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($roleMembers as $row): ?>
                <tr>
                    <td data-label="Name"><strong><?= htmlspecialchars($row['name']) ?></strong></td>
                    <td data-label="Contact" style="color:var(--muted);"><?= htmlspecialchars($row['contact'] ?: '—') ?></td>
                    <td data-label="Address" style="color:var(--muted);"><?= htmlspecialchars($row['address'] ?: '—') ?></td>
                    <td data-label="Actions">
                        <div class="flex gap-2">
                            <button class="btn btn-yellow btn-sm"
                                onclick="openEdit(<?= htmlspecialchars(json_encode($row)) ?>)">Edit</button>
                            <form method="POST" style="display:inline;"
                                onsubmit="return confirm('Remove this member?')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                <button type="submit" class="btn btn-danger btn-sm">Remove</button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Edit Modal -->
<div class="modal-overlay" id="editModal">
    <div class="modal">
        <h3>✏️ Edit Member</h3>
        <form method="POST">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" id="editId">
            <div class="form-group mb-4">
                <label>Full Name *</label>
                <input type="text" name="name" id="editName" required>
            </div>
            <div class="form-group mb-4">
                <label>Position / Role *</label>
                <select name="role" id="editRole" required>
                    <option>SK Chairperson</option>
                    <option>SK Kagawad</option>
                    <option>SK Secretary</option>
                    <option>SK Treasurer</option>
                    <option>SK Auditor</option>
                    <option>SK PRO</option>
                    <option>SK Member</option>
                </select>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Contact No.</label>
                    <input type="text" name="contact" id="editContact">
                </div>
                <div class="form-group">
                    <label>Address</label>
                    <input type="text" name="address" id="editAddress">
                </div>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn btn-cancel" onclick="closeEdit()">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEdit(row) {
    document.getElementById('editId').value      = row.id;
    document.getElementById('editName').value    = row.name;
    document.getElementById('editContact').value = row.contact;
    document.getElementById('editAddress').value = row.address;
    document.getElementById('editRole').value    = row.role;
    document.getElementById('editModal').classList.add('open');
}
function closeEdit() {
    document.getElementById('editModal').classList.remove('open');
}
document.getElementById('editModal').addEventListener('click', function(e) {
    if (e.target === this) closeEdit();
});
</script>
</body>
</html>