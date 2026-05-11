<?php
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$isAdmin = ($_SESSION['role'] === 'main_admin' || $_SESSION['role'] === 'staff');
$msg = '';
$msgType = '';

// Handle Add
if (isset($_POST['action']) && $_POST['action'] === 'add' && $isAdmin) {
    $title   = trim($_POST['title']);
    $desc    = trim($_POST['description']);
    $date    = $_POST['project_date'];
    $status  = $_POST['status'];
    if ($title && $date) {
        $stmt = $conn->prepare("INSERT INTO projects (title, description, project_date, status) VALUES (?,?,?,?)");
        $stmt->bind_param("ssss", $title, $desc, $date, $status);
        $stmt->execute();
        $msg = 'Project added successfully!';
        $msgType = 'success';
    } else {
        $msg = 'Title and date are required.';
        $msgType = 'error';
    }
}

// Handle Edit
if (isset($_POST['action']) && $_POST['action'] === 'edit' && $isAdmin) {
    $id     = (int)$_POST['id'];
    $title  = trim($_POST['title']);
    $desc   = trim($_POST['description']);
    $date   = $_POST['project_date'];
    $status = $_POST['status'];
    if ($id && $title && $date) {
        $stmt = $conn->prepare("UPDATE projects SET title=?, description=?, project_date=?, status=? WHERE id=?");
        $stmt->bind_param("ssssi", $title, $desc, $date, $status, $id);
        $stmt->execute();
        $msg = 'Project updated successfully!';
        $msgType = 'success';
    }
}

// Handle Delete
if (isset($_POST['action']) && $_POST['action'] === 'delete' && $isAdmin) {
    $id = (int)$_POST['id'];
    if ($id) {
        $conn->query("DELETE FROM projects WHERE id=$id");
        $msg = 'Project deleted.';
        $msgType = 'success';
    }
}

$month = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('m');
if ($month < 1 || $month > 12) $month = (int)date('m');

$months = [
    1=>'January',2=>'February',3=>'March',4=>'April',
    5=>'May',6=>'June',7=>'July',8=>'August',
    9=>'September',10=>'October',11=>'November',12=>'December'
];

$stmt = $conn->prepare("SELECT * FROM projects WHERE MONTH(project_date)=? ORDER BY project_date ASC");
$stmt->bind_param("i", $month);
$stmt->execute();
$projects = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SK DocuTrack &mdash; Projects</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Barlow:wght@300;400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container">
    <div class="page-header">
        <h1>Projects</h1>
        <p>Browse and manage SK projects by month.</p>
        <div class="section-rule"></div>
    </div>

    <?php if ($msg): ?>
    <div class="alert alert-<?= $msgType ?>"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <!-- Month Tabs -->
    <div class="month-tabs">
        <?php foreach ($months as $num => $name): ?>
        <a href="?month=<?= $num ?>" class="month-tab <?= $month==$num?'active':'' ?>">
            <?= $name ?>
        </a>
        <?php endforeach; ?>
    </div>

    <!-- Add Form -->
    <?php if ($isAdmin): ?>
    <div class="sk-form">
        <h3>➕ Add New Project</h3>
        <form method="POST">
            <input type="hidden" name="action" value="add">
            <div class="form-row">
                <div class="form-group" style="grid-column: span 2;">
                    <label>Project Title *</label>
                    <input type="text" name="title" placeholder="e.g. Livelihood Seminar" required>
                </div>
                <div class="form-group">
                    <label>Date *</label>
                    <input type="date" name="project_date" required>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="status">
                        <option value="Planned">Planned</option>
                        <option value="Ongoing">Ongoing</option>
                        <option value="Completed">Completed</option>
                    </select>
                </div>
            </div>
            <div class="form-group mb-4">
                <label>Description</label>
                <textarea name="description" placeholder="Brief description..."></textarea>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Add Project</button>
            </div>
        </form>
    </div>
    <?php endif; ?>

    <!-- Projects Table -->
    <?php if ($projects->num_rows > 0): ?>
    <table class="sk-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Title</th>
                <th>Description</th>
                <th>Status</th>
                <?php if ($isAdmin): ?><th>Actions</th><?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $projects->fetch_assoc()): ?>
            <tr>
                <td data-label="Date" style="white-space:nowrap;">
                    <?= date('M d, Y', strtotime($row['project_date'])) ?>
                </td>
                <td data-label="Title"><strong><?= htmlspecialchars($row['title']) ?></strong></td>
                <td data-label="Description" style="color:var(--muted); font-size:0.85rem;">
                    <?= htmlspecialchars($row['description'] ?: '—') ?>
                </td>
                <td data-label="Status">
                    <span class="badge badge-<?= strtolower($row['status']) ?>">
                        <?= $row['status'] ?>
                    </span>
                </td>
                <?php if ($isAdmin): ?>
                <td data-label="Actions">
                    <div class="flex gap-2">
                        <button class="btn btn-yellow btn-sm"
                            onclick="openEdit(<?= htmlspecialchars(json_encode($row)) ?>)">Edit</button>
                        <form method="POST" style="display:inline;"
                            onsubmit="return confirm('Delete this project?')">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </div>
                </td>
                <?php endif; ?>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <?php else: ?>
    <div class="empty-state">
        <div class="icon">📂</div>
        <p>No projects found for <?= $months[$month] ?>.</p>
    </div>
    <?php endif; ?>
</div>

<!-- Edit Modal -->
<?php if ($isAdmin): ?>
<div class="modal-overlay" id="editModal">
    <div class="modal">
        <h3>✏️ Edit Project</h3>
        <form method="POST">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" id="editId">
            <div class="form-group mb-4">
                <label>Project Title *</label>
                <input type="text" name="title" id="editTitle" required>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Date *</label>
                    <input type="date" name="project_date" id="editDate" required>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" id="editStatus">
                        <option value="Planned">Planned</option>
                        <option value="Ongoing">Ongoing</option>
                        <option value="Completed">Completed</option>
                    </select>
                </div>
            </div>
            <div class="form-group mb-4">
                <label>Description</label>
                <textarea name="description" id="editDesc"></textarea>
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
    document.getElementById('editId').value    = row.id;
    document.getElementById('editTitle').value = row.title;
    document.getElementById('editDate').value  = row.project_date;
    document.getElementById('editDesc').value  = row.description;
    document.getElementById('editStatus').value = row.status;
    document.getElementById('editModal').classList.add('open');
}
function closeEdit() {
    document.getElementById('editModal').classList.remove('open');
}
document.getElementById('editModal').addEventListener('click', function(e) {
    if (e.target === this) closeEdit();
});
</script>
<?php endif; ?>
</body>
</html>