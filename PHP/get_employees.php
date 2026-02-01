<?php
session_start();
header('Content-Type: application/json');
require "db.php";

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Admin session not found. Please login first.']);
    exit;
}

// Fetch all employees
$query = "SELECT E_id, First_Name, Last_Name, Department, Phone, email, Attendance FROM employee_details ORDER BY E_id ASC";
$result = $conn->query($query);

if ($result) {
    $employees = [];
    while ($row = $result->fetch_assoc()) {
        $employees[] = $row;
    }
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'employees' => $employees,
        'count' => count($employees)
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Query failed: ' . htmlspecialchars($conn->error)
    ]);
}

$conn->close();
?>
