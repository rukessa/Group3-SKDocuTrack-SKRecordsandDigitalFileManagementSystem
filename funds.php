<?php
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$msg = '';
$msgType = '';

// Handle Add
if (isset($_POST['action']) && $_POST['action'] === 'add') {
    $desc   = trim($_POST['description']);
    $amount = (float)$_POST['amount'];
    $type   = $_POST['type'];
    $date   = $_POST['fund_date'];
    if ($desc && $amount > 0 && $date) {
        $stmt = $conn->prepare("INSERT INTO funds (description, amount, type, fund_date) VALUES (?,?,?,?)");
        $stmt->bind_param("sdss", $desc, $amount, $type, $date);
        $stmt->execute();
        $msg = 'Fund entry added!';
        $msgType = 'success';
    } else {
        $msg = 'All fields are required and amount must be positive.';
        $msgType = 'error';
    }
}

// Handle Edit
if (isset($_POST['action']) && $_POST['action'] === 'edit') {
    $id     = (int)$_POST['id'];
    $desc   = trim($_POST['description']);
    $amount = (float)$_POST['amount'];
    $type   = $_POST['type'];
    $date   = $_POST['fund_date'];
    if ($id && $desc && $amount > 0 && $date) {
        $stmt = $conn->prepare("UPDATE funds SET description=?, amount=?, type=?, fund_date=? WHERE id=?");
        $stmt->bind_param("sdssi", $desc, $amount, $type, $date, $id);
        $stmt->execute();
        $msg = 'Fund entry updated!';
        $msgType = 'success';
    }
}

// Handle Delete
if (isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id = (int)$_POST['id'];
    if ($id) {
        $conn->query("DELETE FROM funds WHERE id=$id");
        $msg = 'Fund entry deleted.';
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

// Fetch entries for the month
$stmt = $conn->prepare("SELECT * FROM funds WHERE MONTH(fund_date)=? ORDER BY fund_date ASC");
$stmt->bind_param("i", $month);
$stmt->execute();
$funds = $stmt->get_result();

// Monthly totals
$stmt2 = $conn->prepare("SELECT type, SUM(amount) as total FROM funds WHERE MONTH(fund_date)=? GROUP BY type");
$stmt2->bind_param("i", $month);
$stmt2->execute();
$totalsResult = $stmt2->get_result();
$totals = ['Income' => 0, 'Expense' => 0];
while ($t = $totalsResult->fetch_assoc()) {
    $totals[$t['type']] = $t['total'];
}
$net = $totals['Income'] - $totals['Expense'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SK DocuTrack &mdash; Funds</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Barlow:wght@300;400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container">
    <div class="page-header">
        <h1>Funds</h1>
        <p>Track income and expenses by month.</p>
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

    <!-- Monthly Summary -->
    <div class="funds-summary">
        <div class="funds-summary-card">
            <div class="amount amount-income">₱<?= number_format($totals['Income'], 2) ?></div>
            <div class="label">Total Income</div>
        </div>
        <div class="funds-summary-card">
            <div class="amount amount-expense">₱<?= number_format($totals['Expense'], 2) ?></div>
            <div class="label">Total Expenses</div>
        </div>
        <div class="funds-summary-card">
            <div class="amount <?= $net >= 0 ? 'amount-income' : 'amount-expense' ?>">
                <?= $net < 0 ? '-' : '' ?>₱<?= number_format(abs($net), 2) ?>
            </div>
            <div class="label">Net Balance</div>
        </div>
    </div>

    <!-- Add Form -->
    <div class="sk-form">
        <h3>➕ Add Fund Entry</h3>
        <form method="POST">
            <input type="hidden" name="action" value="add">
            <div class="form-row">
                <div class="form-group" style="grid-column: span 2;">
                    <label>Description *</label>
                    <input type="text" name="description" placeholder="e.g. DILG Quarterly Allocation" required>
                </div>
                <div class="form-group">
                    <label>Amount (₱) *</label>
                    <input type="number" name="amount" step="0.01" min="0.01" placeholder="0.00" required>
                </div>
                <div class="form-group">
                    <label>Type *</label>
                    <select name="type" required>
                        <option value="Income">Income</option>
                        <option value="Expense">Expense</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Date *</label>
                    <input type="date" name="fund_date" required>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Add Entry</button>
            </div>
        </form>
    </div>

    <!-- Funds Table -->
    <?php if ($funds->num_rows > 0): ?>
    <table class="sk-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Description</th>
                <th>Type</th>
                <th class="text-right">Amount</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $funds->fetch_assoc()): ?>
            <tr>
                <td data-label="Date" style="white-space:nowrap;">
                    <?= date('M d, Y', strtotime($row['fund_date'])) ?>
                </td>
                <td data-label="Description"><strong><?= htmlspecialchars($row['description']) ?></strong></td>
                <td data-label="Type">
                    <span class="badge badge-<?= strtolower($row['type']) ?>">
                        <?= $row['type'] ?>
                    </span>
                </td>
                <td data-label="Amount" class="text-right"
                    style="color:<?= $row['type']==='Income'?'#86efac':'#fca5a5' ?>; font-weight:600;">
                    <?= $row['type']==='Expense'?'-':'+' ?>₱<?= number_format($row['amount'], 2) ?>
                </td>
                <td data-label="Actions">
                    <div class="flex gap-2">
                        <button class="btn btn-yellow btn-sm"
                            onclick="openEdit(<?= htmlspecialchars(json_encode($row)) ?>)">Edit</button>
                        <form method="POST" style="display:inline;"
                            onsubmit="return confirm('Delete this entry?')">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <?php else: ?>
    <div class="empty-state">
        <div class="icon">💰</div>
        <p>No fund entries for <?= $months[$month] ?>.</p>
    </div>
    <?php endif; ?>
</div>

<!-- Edit Modal -->
<div class="modal-overlay" id="editModal">
    <div class="modal">
        <h3>✏️ Edit Fund Entry</h3>
        <form method="POST">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" id="editId">
            <div class="form-group mb-4">
                <label>Description *</label>
                <input type="text" name="description" id="editDesc" required>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Amount (₱) *</label>
                    <input type="number" name="amount" id="editAmount" step="0.01" min="0.01" required>
                </div>
                <div class="form-group">
                    <label>Type *</label>
                    <select name="type" id="editType">
                        <option value="Income">Income</option>
                        <option value="Expense">Expense</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Date *</label>
                    <input type="date" name="fund_date" id="editDate" required>
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
    document.getElementById('editId').value     = row.id;
    document.getElementById('editDesc').value   = row.description;
    document.getElementById('editAmount').value = row.amount;
    document.getElementById('editType').value   = row.type;
    document.getElementById('editDate').value   = row.fund_date;
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