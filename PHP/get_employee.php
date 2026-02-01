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

// Get employee ID from query parameter
$E_id = isset($_GET['E_id']) ? trim($_GET['E_id']) : '';

if (empty($E_id)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Employee ID is required.']);
    exit;
}

// Fetch specific employee
$stmt = $conn->prepare("SELECT E_id, First_Name, Last_Name, Department, Phone, email, Attendance FROM employee_details WHERE E_id = ?");

if (!$stmt) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . htmlspecialchars($conn->error)]);
    exit;
}

$stmt->bind_param('s', $E_id);

if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Query execution error: ' . htmlspecialchars($stmt->error)]);
    $stmt->close();
    exit;
}

$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $employee = $result->fetch_assoc();
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'employee' => $employee
    ]);
} else {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Employee with ID ' . htmlspecialchars($E_id) . ' not found.']);
}

$stmt->close();
$conn->close();
?>

