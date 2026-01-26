<?php
define ('BRVO_API_KEY','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');
define ('SENDER_EMAIL','musharrafcm97@outlook.com');
define ('SENDER_NAME','Musharraf');

function sendBrevoEmail($to, $subject, $htmlContent, $textContent = '') {
    
    // Prepare recipient
    if (is_string($to)) {
        $toArray = [['email' => $to]];
    } else {
        $toArray = [$to];
    }
    
    // Prepare email data
    $data = [
        'sender' => [
            'name' => SENDER_NAME,
            'email' => SENDER_EMAIL
        ],
        'to' => $toArray,
        'subject' => $subject,
        'htmlContent' => $htmlContent
    ];
    
    // Add text content if provided
    if (!empty($textContent)) {
        $data['textContent'] = $textContent;
    }
    
    // Initialize cURL
    $ch = curl_init('https://api.brevo.com/v3/smtp/email');
    
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => [
            'accept: application/json',
            'api-key: ' . BREVO_API_KEY,
            'content-type: application/json'
        ],
        CURLOPT_TIMEOUT => 30
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    // Handle cURL errors
    if ($error) {
        return [
            'success' => false,
            'message' => 'cURL Error: ' . $error,
            'messageId' => null
        ];
    }
    
    // Parse response
    $responseData = json_decode($response, true);
    
    // Check if successful (HTTP 201 = Created)
    if ($httpCode === 201) {
        return [
            'success' => true,
            'message' => 'Email sent successfully!',
            'messageId' => $responseData['messageId'] ?? null
        ];
    } else {
        return [
            'success' => false,
            'message' => $responseData['message'] ?? 'Unknown error occurred',
            'messageId' => null
        ];
    }
}

/**
 * Send admin approval notification email
 */
function sendAdminApprovalEmail($adminEmail, $userData, $approveUrl, $denyUrl, $expiresAt) {
    
    $subject = '🔔 New Admin Registration Pending Approval';
    
    $htmlContent = "
    <!DOCTYPE html>
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #4CAF50; color: white; padding: 20px; text-align: center; }
            .content { padding: 20px; background: #f9f9f9; }
            .info-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
            .info-table td { padding: 10px; border-bottom: 1px solid #ddd; }
            .info-table td:first-child { font-weight: bold; width: 120px; }
            .btn { display: inline-block; padding: 12px 30px; margin: 10px 5px; text-decoration: none; border-radius: 5px; font-weight: bold; }
            .btn-approve { background: #4CAF50; color: white; }
            .btn-deny { background: #f44336; color: white; }
            .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>📋 New Registration Request</h1>
            </div>
            <div class='content'>
                <p>A new admin registration request has been submitted:</p>
                
                <table class='info-table'>
                    <tr><td>Name:</td><td>{$userData['First_Name']} {$userData['Last_Name']}</td></tr>
                    <tr><td>Email:</td><td>{$userData['email']}</td></tr>
                    <tr><td>Phone:</td><td>{$userData['phone']}</td></tr>
                    <tr><td>Employee ID:</td><td>{$userData['E_id']}</td></tr>
                    <tr><td>Position:</td><td>{$userData['pos']}</td></tr>
                </table>
                
                <p style='text-align: center;'>
                    <a href='{$approveUrl}' class='btn btn-approve'>✅ APPROVE</a>
                    <a href='{$denyUrl}' class='btn btn-deny'>❌ DENY</a>
                </p>
                
                <p style='color: #666; font-size: 12px;'>
                    ⏰ This request expires on: {$expiresAt}
                </p>
            </div>
            <div class='footer'>
                <p>Employee Management System</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    $textContent = "
    New Admin Registration Request
    
    Name: {$userData['First_Name']} {$userData['Last_Name']}
    Email: {$userData['email']}
    Phone: {$userData['phone']}
    Employee ID: {$userData['E_id']}
    Position: {$userData['pos']}
    
    Approve: {$approveUrl}
    Deny: {$denyUrl}
    
    Expires: {$expiresAt}
    ";
    
    return sendBrevoEmail($adminEmail, $subject, $htmlContent, $textContent);
}

/**
 * Send password setup email to approved user
 */
function sendPasswordSetupEmail($userEmail, $userName, $setPasswordUrl) {
    
    $subject = '✅ Registration Approved - Set Your Password';
    
    $htmlContent = "
    <!DOCTYPE html>
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #2196F3; color: white; padding: 20px; text-align: center; }
            .content { padding: 20px; background: #f9f9f9; }
            .btn { display: inline-block; padding: 15px 40px; margin: 20px 0; background: #4CAF50; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; }
            .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>🎉 Welcome!</h1>
            </div>
            <div class='content'>
                <p>Hello <strong>{$userName}</strong>,</p>
                
                <p>Great news! Your registration has been <strong>approved</strong>.</p>
                
                <p>Please click the button below to set your password and complete your account setup:</p>
                
                <p style='text-align: center;'>
                    <a href='{$setPasswordUrl}' class='btn'>🔐 Set Your Password</a>
                </p>
                
                <p style='color: #666; font-size: 12px;'>
                    If the button doesn't work, copy and paste this link:<br>
                    {$setPasswordUrl}
                </p>
                
                <p style='color: #f44336; font-size: 12px;'>
                    ⚠️ This link expires in 48 hours.
                </p>
            </div>
            <div class='footer'>
                <p>Employee Management System</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    return sendBrevoEmail($userEmail, $subject, $htmlContent);
}

/**
 * Send welcome email after password is set
 */
function sendWelcomeEmail($userEmail, $userName) {
    
    $subject = '🎊 Welcome to Employee Management System';
    
    $htmlContent = "
    <!DOCTYPE html>
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #4CAF50; color: white; padding: 20px; text-align: center; }
            .content { padding: 20px; background: #f9f9f9; }
            .btn { display: inline-block; padding: 15px 40px; margin: 20px 0; background: #2196F3; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; }
            .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>🎊 Welcome Aboard!</h1>
            </div>
            <div class='content'>
                <p>Hello <strong>{$userName}</strong>,</p>
                
                <p>Your account has been successfully created!</p>
                
                <p>You can now log in to the Employee Management System and start managing employees.</p>
                
                <p style='text-align: center;'>
                    <a href='https://your-website.com/login.html' class='btn'>🔑 Login Now</a>
                </p>
            </div>
            <div class='footer'>
                <p>Employee Management System</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    return sendBrevoEmail($userEmail, $subject, $htmlContent);
}
?>