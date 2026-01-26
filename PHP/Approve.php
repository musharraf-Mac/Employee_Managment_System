<?php
require 'db.php';


$token = $_GET['token'] ?? '';
if (!preg_match('/^[a-f0-9]{64}$/', $token)) exit('Invalid token.');

$now = (new DateTime())->format('Y-m-d H:i:s');

// Lock and fetch (robust: check prepare and get_result availability)
if (!($conn instanceof mysqli)) {
      exit('Database connection is not mysqli.');
}

$conn->begin_transaction();
$sel = $conn->prepare("SELECT E_id, First_Name,Last_Name,phone,email,expires_at,status FROM admin_info_temp WHERE admin_token = ? FOR UPDATE");
if (!$sel) {
      $err = $conn->error;
      $conn->rollback();
      exit('DB prepare failed: ' . htmlspecialchars($err));
}

$sel->bind_param('s', $token);
if (!$sel->execute()) {
      $err = $sel->error ?: $conn->error;
      $sel->close();
      $conn->rollback();
      exit('DB execute failed: ' . htmlspecialchars($err));
}

// Fetch result: prefer get_result (requires mysqlnd), otherwise use bind_result
if (method_exists($sel, 'get_result')) {
      $res = $sel->get_result();
      $row = $res ? $res->fetch_assoc() : null;
} else {
      $sel->store_result();
      if ($sel->num_rows === 0) {
            $row = null;
      } else {
            $sel->bind_result($E_id, $First_Name, $Last_Name, $phone, $emailRow, $expires_at, $status);
            $sel->fetch();
            $row = [
                  'E_id' => $E_id,
                  'First_Name' => $First_Name,
                  'Last_Name' => $Last_Name,
                  'phone' => $phone,
                  'email' => $emailRow,
                  'expires_at' => $expires_at,
                  'status' => $status,
            ];
      }
}

if (!$row) { $conn->rollback(); exit('Token not found.'); }
if ($row['status'] !== 'pending') { $conn->rollback(); exit('Already processed.'); }
if ($now > $row['expires_at']) { $conn->rollback(); exit('Token expired.'); }

// mark approved and create user_token
$userToken = bin2hex(random_bytes(32));
// Update the pending row using E_id as primary key
$upd = $conn->prepare("UPDATE admin_info_temp SET status='approved', user_token=?, expires_at=DATE_ADD(NOW(), INTERVAL 48 HOUR) WHERE E_id = ?");
if (!$upd) {
      $err = $conn->error;
      $conn->rollback();
      exit('DB prepare failed (update): ' . htmlspecialchars($err));
}

$upd->bind_param('ss', $userToken, $row['E_id']);
if (!$upd->execute()) {
      $err = $upd->error ?: $conn->error;
      $upd->close();
      $conn->rollback();
      exit('DB update failed: ' . htmlspecialchars($err));
}

$upd->close();
$conn->commit();

// send set-password link to user
$base = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http')
      . '://' . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
$setPassUrl = $base . '/set_password.php?token=' . $userToken;

$subject = 'Your registration was approved — set your password';
$message = "Hello {$row['First_Name']},\n\nYour registration was approved. Please set your account password here:\n\n$setPassUrl\n\nThis link expires in 48 hours.";

require_once __DIR__ . '/brevo_mail.php';

// Send set-password email to user using Brevo
$userName = $row['First_Name'] . ' ' . $row['Last_Name'];
$userEmail = $row['email'];

$base = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http')
      . '://' . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
$setPassUrl = $base . '/set_password.php?token=' . $userToken;

// Send email using Brevo
$emailResult = sendPasswordSetupEmail($userEmail, $userName, $setPassUrl);

if ($emailResult['success']) {
    echo "<script>
        alert('Registration approved! The user has been notified to set their password.');
        window.location.href = '/Employee_Managment_System/Admin_db.html';
    </script>";
} else {
    // Email failed but approval succeeded - show the link manually
    echo "<p>Approval successful but email failed to send.</p>";
    echo "<p>Please send this link to the user manually:</p>";
    echo "<p><a href='$setPassUrl'>$setPassUrl</a></p>";
}
?>