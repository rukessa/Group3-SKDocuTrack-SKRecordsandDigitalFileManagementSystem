<?php
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Stats
$totalProjects = $conn->query("SELECT COUNT(*) as c FROM projects")->fetch_assoc()['c'];
$totalMembers  = $conn->query("SELECT COUNT(*) as c FROM members")->fetch_assoc()['c'];
$totalIncome   = $conn->query("SELECT SUM(amount) as s FROM funds WHERE type='Income'")->fetch_assoc()['s'] ?? 0;
$totalExpense  = $conn->query("SELECT SUM(amount) as s FROM funds WHERE type='Expense'")->fetch_assoc()['s'] ?? 0;
$netFunds      = $totalIncome - $totalExpense;

// Recent projects
$recentProjects = $conn->query("SELECT * FROM projects ORDER BY project_date DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SK DocuTrack &mdash; Dashboard</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Barlow:wght@300;400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container">
    <div class="page-header">
        <h1>Dashboard</h1>
        <p>Welcome back, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong>. Here's what's happening.</p>
        <div class="section-rule"></div>
    </div>

    <!-- Stats -->
    <div class="stat-grid">
        <div class="stat-card">
            <div class="stat-num"><?= $totalProjects ?></div>
            <div class="stat-label">Total Projects</div>
        </div>
        <div class="stat-card">
            <div class="stat-num"><?= $totalMembers ?></div>
            <div class="stat-label">SK Members</div>
        </div>
        <div class="stat-card">
            <div class="stat-num" style="color:#86efac;">₱<?= number_format($totalIncome,0) ?></div>
            <div class="stat-label">Total Income</div>
        </div>
        <div class="stat-card">
            <div class="stat-num" style="color:#fca5a5;">₱<?= number_format($totalExpense,0) ?></div>
            <div class="stat-label">Total Expenses</div>
        </div>
        <div class="stat-card">
            <div class="stat-num" style="color:<?= $netFunds >= 0 ? '#86efac' : '#fca5a5' ?>;">
                <?= $netFunds < 0 ? '-' : '' ?>₱<?= number_format(abs($netFunds),0) ?>
            </div>
            <div class="stat-label">Net Balance</div>
        </div>
    </div>

    <!-- Recent Projects -->
    <div class="card">
        <div class="flex justify-between items-center mb-4">
            <h2 style="font-family:'Playfair Display',serif; font-size:1.2rem; color:#fff;">Recent Projects</h2>
            <a href="projects.php" class="btn btn-blue btn-sm">View All</a>
        </div>

        <?php if ($recentProjects->num_rows > 0): ?>
        <table class="sk-table">
            <thead>
                <tr>
                    <th>Project Title</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $recentProjects->fetch_assoc()): ?>
                <tr>
                    <td data-label="Title"><?= htmlspecialchars($row['title']) ?></td>
                    <td data-label="Date"><?= date('M d, Y', strtotime($row['project_date'])) ?></td>
                    <td data-label="Status">
                        <span class="badge badge-<?= strtolower($row['status']) ?>">
                            <?= $row['status'] ?>
                        </span>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="empty-state"><div class="icon">📂</div><p>No projects yet.</p></div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>