
<?php
require 'db.php';


require_once __DIR__ . '/../FPDF/fpdf.php';


if (isset($_GET['action']) && $_GET['action'] === 'fetch') {
	// Fetch all employee monthly data from employee_details only
	$sql = "SELECT E_id, First_Name AS Name, `leave days`, `Working hours` FROM employee_details";
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
	$emp_id = $conn->real_escape_string($_GET['id']);
	$sql = "SELECT E_id, First_Name AS Name, `leave days`, `Working hours` FROM employee_details WHERE E_id = '$emp_id' LIMIT 1";
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
		$pdf->Cell(50, 10, 'Leave Days (Month):', 0, 0);
		$pdf->Cell(0, 10, $row['leave days'], 0, 1);
		$pdf->Cell(50, 10, 'Working Hours (Month):', 0, 0);
		$pdf->Cell(0, 10, $row['Working hours'], 0, 1);
		$pdf->Output('D', 'Employee_Report_' . $row['E_id'] . '.pdf');
		exit;
	} else {
		echo 'Employee not found.';
		exit;
	}
}

http_response_code(400);
echo 'Invalid request.';
exit;
?>