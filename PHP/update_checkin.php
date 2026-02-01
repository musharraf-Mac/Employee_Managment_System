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
$check_in_time = isset($input['check_in_time']) ? trim($input['check_in_time']) : '';
$notes = isset($input['notes']) ? trim($input['notes']) : '';

if (empty($E_id) || empty($check_in_time)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Employee ID and check-in time are required.']);
    exit;
}

// Get today's date
$today = date('Y-m-d');
$check_in_timestamp = $today . ' ' . $check_in_time . ':00';

// Check if employee exists
$check_stmt = $conn->prepare("SELECT E_id FROM employee_details WHERE E_id = ?");
if (!$check_stmt) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Query failed.']);
    exit;
}

$check_stmt->bind_param('s', $E_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows === 0) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Employee not found.']);
    $check_stmt->close();
    exit;
}

$check_stmt->close();

// Check if checkin table exists, if not create it
$table_check = "CREATE TABLE IF NOT EXISTS employee_checkin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    E_id VARCHAR(50) NOT NULL,
    check_in_time DATETIME NOT NULL,
    notes TEXT,
    created_by VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (E_id) REFERENCES employee_details(E_id) ON DELETE CASCADE
)";

if (!$conn->query($table_check)) {
    // Table might already exist, continue anyway
}

// Insert check-in record
$stmt = $conn->prepare("INSERT INTO employee_checkin (E_id, check_in_time, notes, created_by) VALUES (?, ?, ?, ?)");

if (!$stmt) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Query preparation failed.']);
    exit;
}

$admin_name = $_SESSION['admin_name'] ?? 'System';
$stmt->bind_param('ssss', $E_id, $check_in_timestamp, $notes, $admin_name);

if ($stmt->execute()) {
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Check-in recorded successfully for ' . htmlspecialchars($E_id) . ' at ' . htmlspecialchars($check_in_time)
    ]);
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Check-in failed: ' . htmlspecialchars($stmt->error)]);
}

$stmt->close();
$conn->close();
?>
<?php
// This endpoint is deprecated. Check-in feature has been removed.
http_response_code(410);
echo json_encode(['success' => false, 'message' => 'Check-in feature is no longer available.']);
?>
