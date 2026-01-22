<?php 
require_once __DIR__ . '/db.php';
$token = $_GET['token'] ?? '';
if (!preg_match('/^[a-f0-9]{64}$/', $token)) {
    exit('Invalid token.');
}
// Delete the pending registration
$stmt = $conn->prepare("UPDATE admin_info_temp SET status = 'denied' WHERE admin_token = ?");
if ($stmt) {
    $stmt->bind_param('s', $token);
    $stmt->execute();
    $stmt->close();
}
$conn->close();
echo "<script>
    alert('The registration request has been denied.');
    window.location.href = '../index.html';
</script>";
?>