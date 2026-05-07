<?php
$conn = new mysqli("localhost", "root", "", "skdocutrack");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$fixes = [];

// Fix 1: Add 'type' column to funds if missing
$result = $conn->query("SHOW COLUMNS FROM funds LIKE 'type'");
if ($result->num_rows === 0) {
    $conn->query("ALTER TABLE funds ADD COLUMN type ENUM('Income','Expense') NOT NULL DEFAULT 'Income' AFTER amount");
    $fixes[] = "✅ Added 'type' column to funds table.";
} else {
    $fixes[] = "ℹ️ 'type' column already exists in funds.";
}

// Fix 2: Add 'description' column to funds if missing
$result = $conn->query("SHOW COLUMNS FROM funds LIKE 'description'");
if ($result->num_rows === 0) {
    $conn->query("ALTER TABLE funds ADD COLUMN description VARCHAR(255) NOT NULL DEFAULT '' AFTER id");
    $fixes[] = "✅ Added 'description' column to funds table.";
} else {
    $fixes[] = "ℹ️ 'description' column already exists in funds.";
}

// Fix 3: Add 'status' column to projects if missing
$result = $conn->query("SHOW COLUMNS FROM projects LIKE 'status'");
if ($result->num_rows === 0) {
    $conn->query("ALTER TABLE projects ADD COLUMN status ENUM('Planned','Ongoing','Completed') NOT NULL DEFAULT 'Planned'");
    $fixes[] = "✅ Added 'status' column to projects table.";
} else {
    $fixes[] = "ℹ️ 'status' column already exists in projects.";
}

// Fix 4: Add 'description' column to projects if missing
$result = $conn->query("SHOW COLUMNS FROM projects LIKE 'description'");
if ($result->num_rows === 0) {
    $conn->query("ALTER TABLE projects ADD COLUMN description TEXT AFTER title");
    $fixes[] = "✅ Added 'description' column to projects table.";
} else {
    $fixes[] = "ℹ️ 'description' column already exists in projects.";
}

// Fix 5: Add 'contact' and 'address' to members if missing
$result = $conn->query("SHOW COLUMNS FROM members LIKE 'contact'");
if ($result->num_rows === 0) {
    $conn->query("ALTER TABLE members ADD COLUMN contact VARCHAR(50) AFTER role");
    $fixes[] = "✅ Added 'contact' column to members table.";
} else {
    $fixes[] = "ℹ️ 'contact' column already exists in members.";
}

$result = $conn->query("SHOW COLUMNS FROM members LIKE 'address'");
if ($result->num_rows === 0) {
    $conn->query("ALTER TABLE members ADD COLUMN address VARCHAR(255) AFTER contact");
    $fixes[] = "✅ Added 'address' column to members table.";
} else {
    $fixes[] = "ℹ️ 'address' column already exists in members.";
}

// Fix 6: Add 'created_at' to users if missing
$result = $conn->query("SHOW COLUMNS FROM users LIKE 'created_at'");
if ($result->num_rows === 0) {
    $conn->query("ALTER TABLE users ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP");
    $fixes[] = "✅ Added 'created_at' column to users table.";
} else {
    $fixes[] = "ℹ️ 'created_at' column already exists in users.";
}

echo "<h2>🔧 Database Fix Results</h2><ul>";
foreach ($fixes as $f) echo "<li>$f</li>";
echo "</ul>";
echo "<br><a href='dashboard.php'><b>→ Go to Dashboard</b></a>";
echo "<br><br><span style='color:red;'><b>⚠️ Delete this file after use!</b></span>";
?>