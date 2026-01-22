<?php
require __DIR__ . '/db.php';

// Collect and sanitize input
$First_Name = trim($_POST['First_Name'] ?? '');
$Last_Name  = trim($_POST['Last_Name'] ?? '');
$phone      = trim($_POST['phone'] ?? '');
$email      = trim($_POST['email'] ?? '');
$E_id       = trim($_POST['E_id'] ?? '');
$pos        = trim($_POST['pos'] ?? '');

// Basic validation
$errors = [];
if ($First_Name === '') $errors[] = 'First name required';
if ($Last_Name === '')  $errors[] = 'Last name required';
if ($phone === '')      $errors[] = 'Phone required';
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email required';
if ($E_id === '')      $errors[] = 'E_id required';
if ($pos === '')       $errors[] = 'Position required';

if ($errors) {
    // show first error and stop
    header('Content-Type: text/html; charset=utf-8');
    echo '<p>' . htmlspecialchars($errors[0]) . '</p>';
    exit;
}

// Prepare insert into pending table with admin token
$adminToken = bin2hex(random_bytes(32));
$expiresAt = (new DateTime('+48 hours'))->format('Y-m-d H:i:s');

$sql = "INSERT INTO admin_info_temp (First_Name, Last_Name, phone, email, E_id, Position, admin_token, expires_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param('ssssssss', $First_Name, $Last_Name, $phone, $email, $E_id, $pos, $adminToken, $expiresAt);
    if ($stmt->execute()) {
        // Build admin approval/deny links (logged for local dev)
        $base = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
        // NOTE: InfinityFree/Linux is case-sensitive; file is Approve.php
        $approveUrl = $base . '/Approve.php?token=' . urlencode($adminToken);
        $denyUrl = $base . '/deny.php?token=' . urlencode($adminToken);

        $adminEmail = 'musharrafcm97@outlook.com'; 
        $subject = 'New admin registration pending approval';
        $message = "New registration:\n\nName: $First_Name $Last_Name\nEmail: $email\nPhone: $phone\nE_id: $E_id\nPosition: $pos\n\nApprove: $approveUrl\nDeny: $denyUrl\n\nExpires: $expiresAt";
        $headers = "From: no-reply@example.com\r\n";

        // Try to send email (may fail on local environment), also log links to a file
        @mail($adminEmail, $subject, $message, $headers);
        file_put_contents(__DIR__ . '/approval_links.log', "[" . date('c') . "] $email\nApprove: $approveUrl\nDeny: $denyUrl\n\n", FILE_APPEND);

        // Inform the user
        ?>
        <script>
            alert("Your information has been submitted for review. You will be notified once approved.");
            // Redirect relative to /PHP/reg.php
            window.location.href = "../index.html";
        </script>
        <?php
        $stmt->close();
        $conn->close();
        exit;
    } else {
        $err = $stmt->error;
        $stmt->close();
        $conn->close();
        header('Content-Type: text/plain; charset=utf-8', true, 500);
        echo "Database error: " . htmlspecialchars($err);
        exit;
    }
} else {
    header('Content-Type: text/plain; charset=utf-8', true, 500);
    echo "Prepare failed: " . htmlspecialchars($conn->error);
    exit;
}
?>