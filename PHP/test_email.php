<?php
require_once 'brevo_mail.php';

// Test sending an email
$result = sendBrevoEmail(
    'musharrafcm97@outlook.com',  // Send to yourself
    '🧪 Test Email from Employee Management System',
    '<h1>Hello!</h1><p>This is a test email from your Employee Management System.</p>',
    'Hello! This is a test email.'
);

echo '<pre>';
print_r($result);
echo '</pre>';

if ($result['success']) {
    echo '<h2 style="color: green;">✅ Email sent successfully!</h2>';
} else {
    echo '<h2 style="color: red;">❌ Email failed: ' . $result['message'] . '</h2>';
}
?>