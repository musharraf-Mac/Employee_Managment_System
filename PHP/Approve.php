<?php
require 'db.php';

$token = $_GET['token'] ?? '';
if (!preg_match('/^[a-f0-9]{64}$/', $token)) exit('Invalid token.');

$now = (new DateTime())->format('Y-m-d H:i:s');

// Lock and fetch
$conn->begin_transaction();
$sel = $conn->prepare("SELECT id,First_Name,Last_Name,phone,email,expires_at,status FROM admin_info_temp WHERE admin_token = ? FOR UPDATE");
$sel->bind_param('s', $token);
$sel->execute();
$res = $sel->get_result();
$row = $res->fetch_assoc();

if (!$row) { $conn->rollback(); exit('Token not found.'); }
if ($row['status'] !== 'pending') { $conn->rollback(); exit('Already processed.'); }
if ($now > $row['expires_at']) { $conn->rollback(); exit('Token expired.'); }

// mark approved and create user_token
$userToken = bin2hex(random_bytes(32));
$upd = $conn->prepare("UPDATE admin_info_temp SET status='approved', user_token=?, expires_at=DATE_ADD(NOW(), INTERVAL 48 HOUR) WHERE id = ?");
$upd->bind_param('si', $userToken, $row['id']);
$upd->execute();
$conn->commit();

// send set-password link to user
$base = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http')
      . '://' . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
$setPassUrl = $base . '/set_password.php?token=' . $userToken;

$subject = 'Your registration was approved — set your password';
$message = "Hello {$row['First_Name']},\n\nYour registration was approved. Please set your account password here:\n\n$setPassUrl\n\nThis link expires in 48 hours.";
@mail($row['email'], $subject, $message, "From: no-reply@example.com\r\n");
file_put_contents(__DIR__ . '/approval_links.log', "[".date('c')."] Sent set-password to {$row['email']}\nSet-pass: $setPassUrl\n\n", FILE_APPEND);

echo "User approved and emailed.";