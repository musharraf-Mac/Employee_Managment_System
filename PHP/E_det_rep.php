
<?php
require 'db.php';


require_once __DIR__ . '/FPDF/fpdf.php';


if (isset($_GET['action']) && $_GET['action'] === 'fetch') {
	// Fetch all employee monthly data from employee_details only
		$sql = "SELECT E_id, First_Name AS Name, Attendance, E_Leave AS `leave days`, Working_hour AS `Working hours` FROM employee_details";
	$result = $conn->query($sql);
	$data = [];
	if ($result && $result->num_rows > 0) {
		while ($row = $result->fetch_assoc()) {
			$data[] = $row;
		}
	}
	header('Content-Type: application/json');
	echo json_encode($data);
	exit;
}


if (isset($_GET['action']) && $_GET['action'] === 'pdf' && isset($_GET['id'])) {
	// Prevent any output before PDF
	if (ob_get_length()) ob_end_clean();
	$emp_id = $conn->real_escape_string($_GET['id']);
	$sql = "SELECT E_id, First_Name AS Name, Attendance, E_Leave AS `leave days`, Working_hour AS `Working hours` FROM employee_details WHERE E_id = '$emp_id' LIMIT 1";
	$result = $conn->query($sql);
	if ($result && $row = $result->fetch_assoc()) {
		$pdf = new FPDF();
		$pdf->AddPage();
		$pdf->SetFont('Arial', 'B', 16);
		$pdf->Cell(0, 10, 'Employee Monthly Report', 0, 1, 'C');
		$pdf->Ln(10);
		$pdf->SetFont('Arial', '', 12);
		$pdf->Cell(50, 10, 'Employee ID:', 0, 0);
		$pdf->Cell(0, 10, $row['E_id'], 0, 1);
		$pdf->Cell(50, 10, 'Name:', 0, 0);
		$pdf->Cell(0, 10, $row['Name'], 0, 1);
		$pdf->Cell(50, 10, 'Attendance:', 0, 0);
		$pdf->Cell(0, 10, $row['Attendance'] ?? '0', 0, 1);
		$pdf->Cell(50, 10, 'Leave Days (Month):', 0, 0);
		$pdf->Cell(0, 10, $row['leave days'], 0, 1);
		$pdf->Cell(50, 10, 'Working Hours (Month):', 0, 0);
		$pdf->Cell(0, 10, $row['Working hours'], 0, 1);
		$pdf->Output('D', 'Employee_Report_' . $row['E_id'] . '.pdf');
		exit;
	} else {
		if (ob_get_length()) ob_end_clean();
		echo 'Employee not found.';
		exit;
	}
}

// Handle admin update (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'update') {
	$input = json_decode(file_get_contents('php://input'), true);
	$eid = isset($input['E_id']) ? trim($input['E_id']) : '';
	$leave = isset($input['E_Leave']) ? intval($input['E_Leave']) : null;
	$hours = isset($input['Working_hour']) ? intval($input['Working_hour']) : null;
	$attendance = isset($input['Attendance']) ? intval($input['Attendance']) : null;
	if ($eid === '' || $leave === null || $hours === null || $attendance === null) {
		http_response_code(400);
		echo json_encode(['success' => false, 'message' => 'Invalid input.']);
		exit;
	}
	$eid = $conn->real_escape_string($eid);
	$sql = "UPDATE employee_details SET E_Leave = $leave, Working_hour = $hours, Attendance = $attendance WHERE E_id = '$eid'";
	if ($conn->query($sql) === TRUE) {
		echo json_encode(['success' => true, 'message' => 'Update successful.']);
	} else {
		http_response_code(500);
		echo json_encode(['success' => false, 'message' => 'Update failed.']);
	}
	exit;
}

http_response_code(400);
echo 'Invalid request.';
exit;
?>