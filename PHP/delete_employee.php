<?php
session_start();
require "db.php";

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Admin session not found.']);
    exit;
}

// Get JSON data
$input = json_decode(file_get_contents('php://input'), true);

$E_id = isset($input['E_id']) ? trim($input['E_id']) : '';

if (empty($E_id)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Employee ID is required.']);
    exit;
}

// Delete employee
$stmt = $conn->prepare("DELETE FROM employee_details WHERE E_id = ?");

if (!$stmt) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Query preparation failed.']);
    exit;
}

$stmt->bind_param('s', $E_id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Employee deleted successfully.'
        ]);
    } else {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Employee not found.']);
    }
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Delete failed: ' . htmlspecialchars($stmt->error)]);
}

$stmt->close();
$conn->close();
?>
