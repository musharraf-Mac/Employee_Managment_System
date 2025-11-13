<?php 
require "db.php";
$E_id = trim($_POST['E_id'] ?? '');
$First_Name = trim($_POST['First_Name'] ?? '');
$Last_Name = trim($_POST['Last_Name'] ?? '');
$Department = trim($_POST['Department'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$stmt = $conn->prepare("INSERT INTO employee_details (E_id, First_Name, Last_Name, Department, phone) VALUES (?, ?, ?, ?, ?)");
if ($stmt) {
    $stmt->bind_param('sssss', $E_id, $First_Name, $Last_Name, $Department, $phone);
     if ($stmt->execute()) {
        echo "<script>
            alert('Employee added successfully.');
            window.location.href = '/Employee_Managment_System/admin_db.html';
        </script>";
    } else {
        exit('Insert failed: ' . htmlspecialchars($stmt->error));
    }
    $stmt->close();
}
else {
    $err = $conn->error;
    exit('DB prepare failed: ' . htmlspecialchars($err));
}
$conn->close();
?>