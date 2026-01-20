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

// Validate input
$E_id = isset($input['E_id']) ? trim($input['E_id']) : '';
$First_Name = isset($input['First_Name']) ? trim($input['First_Name']) : '';
$Last_Name = isset($input['Last_Name']) ? trim($input['Last_Name']) : '';
$Department = isset($input['Department']) ? trim($input['Department']) : '';
$phone = isset($input['phone']) ? trim($input['phone']) : '';
$email = isset($input['email']) ? trim($input['email']) : '';

if (empty($E_id) || empty($First_Name) || empty($phone)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Required fields are missing.']);
    exit;
}

// Update employee details
$stmt = $conn->prepare("UPDATE employee_details SET First_Name = ?, Last_Name = ?, Department = ?, phone = ?, email = ? WHERE E_id = ?");

if (!$stmt) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Query preparation failed.']);
    exit;
}

$stmt->bind_param('ssssss', $First_Name, $Last_Name, $Department, $phone, $email, $E_id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Employee details updated successfully.'
        ]);
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'No changes made or employee not found.']);
    }
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Update failed: ' . htmlspecialchars($stmt->error)]);
}

$stmt->close();
$conn->close();
?>
