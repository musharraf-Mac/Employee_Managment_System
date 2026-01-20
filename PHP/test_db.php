<?php
session_start();
require "db.php";

echo "<h2>Database Test</h2>";

// Check connection
if ($conn->connect_error) {
    echo "Connection failed: " . $conn->connect_error;
    exit;
}

echo "<p>✓ Database connected successfully</p>";

// Check if employee_details table exists
$result = $conn->query("SHOW TABLES LIKE 'employee_details'");
if ($result->num_rows > 0) {
    echo "<p>✓ employee_details table exists</p>";
} else {
    echo "<p>✗ employee_details table does NOT exist</p>";
    exit;
}

// Get column structure
echo "<h3>Table Structure:</h3>";
$columns = $conn->query("DESCRIBE employee_details");
echo "<ul>";
while ($col = $columns->fetch_assoc()) {
    echo "<li>" . $col['Field'] . " (" . $col['Type'] . ")</li>";
}
echo "</ul>";

// Count employees
$count = $conn->query("SELECT COUNT(*) as total FROM employee_details");
$row = $count->fetch_assoc();
echo "<p>Total employees in database: <strong>" . $row['total'] . "</strong></p>";

// List all employees
if ($row['total'] > 0) {
    echo "<h3>All Employees:</h3>";
    $employees = $conn->query("SELECT E_id, First_Name, Last_Name, Department, phone, email FROM employee_details");
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>E_ID</th><th>First Name</th><th>Last Name</th><th>Department</th><th>Phone</th><th>Email</th></tr>";
    while ($emp = $employees->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($emp['E_id']) . "</td>";
        echo "<td>" . htmlspecialchars($emp['First_Name']) . "</td>";
        echo "<td>" . htmlspecialchars($emp['Last_Name']) . "</td>";
        echo "<td>" . htmlspecialchars($emp['Department']) . "</td>";
        echo "<td>" . htmlspecialchars($emp['phone']) . "</td>";
        echo "<td>" . htmlspecialchars($emp['email']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>⚠️ No employees found in database</p>";
}

$conn->close();
?>
