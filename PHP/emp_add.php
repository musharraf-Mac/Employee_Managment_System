<?php 
session_start(); // Start session to access admin info
require "db.php";

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if admin is logged in
if (!isset($_SESSION['admin_id']) || !isset($_SESSION['admin_name'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Admin session not found. Please login first.']);
    exit;
}

// Get admin info from session
$admin_id = $_SESSION['admin_id'];
$admin_name = $_SESSION['admin_name'];

// Get and sanitize input
$E_id = trim($_POST['E_id'] ?? '');
$First_Name = trim($_POST['First_Name'] ?? '');
$Last_Name = trim($_POST['Last_Name'] ?? '');
$Department = trim($_POST['Department'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$email = trim($_POST['email'] ?? '');

// Validate required fields
if (empty($E_id) || empty($First_Name) || empty($phone)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Required fields missing (E_id, First_Name, phone).']);
    exit;
}

// Check if employee ID already exists
$check_stmt = $conn->prepare("SELECT E_id FROM employee_details WHERE E_id = ?");
if ($check_stmt) {
    $check_stmt->bind_param('s', $E_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Employee ID already exists.']);
        $check_stmt->close();
        exit;
    }
    $check_stmt->close();
}

// Prepare and execute statement with modified_by field (use admin_id as it's a foreign key)
$stmt = $conn->prepare("INSERT INTO employee_details (E_id, First_Name, Last_Name, Department, phone, email, modified_by) VALUES (?, ?, ?, ?, ?, ?, ?)");

if (!$stmt) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'DB prepare failed: ' . htmlspecialchars($conn->error)]);
    $conn->close();
    exit;
}

// Bind parameters - use admin_id for modified_by foreign key
$stmt->bind_param('sssssss', $E_id, $First_Name, $Last_Name, $Department, $phone, $email, $admin_id);

if ($stmt->execute()) {
    // Success response
    http_response_code(200);
    echo json_encode([
        'success' => true, 
        'message' => 'Employee added successfully by ' . htmlspecialchars($admin_name) . '.',
        'employee' => [
            'E_id' => $E_id,
            'First_Name' => $First_Name,
            'Last_Name' => $Last_Name,
            'modified_by' => $admin_name
        ]
    ]);
} else {
    // Database error
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Insert failed: ' . htmlspecialchars($stmt->error)]);
}

$stmt->close();
$conn->close();
?>