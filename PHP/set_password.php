<?php
require_once __DIR__ . '/db.php';

// Helper to render a simple HTML page
function render_page($title, $bodyHtml, $isError = false, $isSuccess = false) {
    $iconClass = $isError ? 'fa-exclamation-circle' : ($isSuccess ? 'fa-check-circle' : 'fa-lock');
    $iconColor = $isError ? '#ef4444' : ($isSuccess ? '#22c55e' : '#6366f1');
    
    echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . htmlspecialchars($title) . '</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --secondary: #0ea5e9;
            --accent: #f43f5e;
            --glass: rgba(255, 255, 255, 0.1);
            --glass-border: rgba(255, 255, 255, 0.2);
        }
        
        body {
            font-family: "Poppins", sans-serif;
            background: linear-gradient(135deg, #1e1b4b 0%, #312e81 50%, #1e1b4b 100%);
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0.1;
            z-index: -1;
        }
        
        .container {
            width: 100%;
            max-width: 500px;
        }
        
        .card {
            background: var(--glass);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            padding: 2.5rem;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
            animation: fadeInUp 0.6s ease;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .card-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, ' . $iconColor . ' 0%, ' . $iconColor . ' 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            box-shadow: 0 15px 35px rgba(99, 102, 241, 0.4);
        }
        
        .card-icon i {
            font-size: 2.25rem;
            color: white;
        }
        
        .card-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .card-header h1 {
            color: white;
            font-size: 1.75rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .card-header p {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.95rem;
        }
        
        .form-group {
            margin-bottom: 1.25rem;
        }
        
        .form-group label {
            display: block;
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.9rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }
        
        .input-group {
            position: relative;
            display: flex;
            align-items: center;
        }
        
        .input-group i {
            position: absolute;
            left: 1rem;
            color: rgba(255, 255, 255, 0.5);
            font-size: 1rem;
            transition: color 0.3s ease;
        }
        
        .input-group input {
            width: 100%;
            padding: 1rem 1rem 1rem 3rem;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            color: white;
            font-size: 1rem;
            font-family: "Poppins", sans-serif;
            transition: all 0.3s ease;
        }
        
        .input-group input::placeholder {
            color: rgba(255, 255, 255, 0.4);
        }
        
        .input-group input:focus {
            outline: none;
            border-color: var(--primary);
            background: rgba(255, 255, 255, 0.12);
        }
        
        .input-group:focus-within i {
            color: var(--primary);
        }
        
        .btn-submit {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            border: none;
            border-radius: 12px;
            color: white;
            font-size: 1rem;
            font-weight: 600;
            font-family: "Poppins", sans-serif;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(99, 102, 241, 0.4);
        }
        
        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(99, 102, 241, 0.5);
        }
        
        .btn-submit:active {
            transform: scale(0.98);
        }
        
        .success-content {
            text-align: center;
        }
        
        .success-content h2 {
            color: white;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
        
        .success-content p {
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }
        
        .success-content a {
            display: inline-block;
            padding: 0.75rem 2rem;
            background: linear-gradient(135deg, var(--secondary) 0%, #0284c7 100%);
            color: white;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 10px 25px rgba(14, 165, 233, 0.4);
        }
        
        .success-content a:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 35px rgba(14, 165, 233, 0.5);
        }
        
        .error-content {
            text-align: center;
        }
        
        .error-content p {
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }
        
        .error-content a {
            display: inline-block;
            padding: 0.75rem 2rem;
            background: linear-gradient(135deg, var(--accent) 0%, #e11d48 100%);
            color: white;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 10px 25px rgba(244, 63, 94, 0.4);
        }
        
        .error-content a:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 35px rgba(244, 63, 94, 0.5);
        }
        
        @media (max-width: 480px) {
            .card {
                padding: 2rem 1.5rem;
            }
            
            .card-header h1 {
                font-size: 1.5rem;
            }
        }
    </style>
    <script src="../JS/set_password.js"></script>
</head>
<body>
    <div class="overlay"></div>
    <div class="container">
        <div class="card">
            ' . $bodyHtml . '
        </div>
    </div>
</body>
</html>';
}

// POST: process token and set password
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'] ?? '';
    $password = $_POST['pass'] ?? '';
    $confirm_pass = $_POST['confirm_pass'] ?? '';

    if ($password === '' || $confirm_pass === '') {
        $body = '<div class="card-icon"><i class="fas fa-exclamation-circle"></i></div>'
            . '<div class="card-header"><h1>Validation Error</h1></div>'
            . '<div class="error-content"><p>Both password fields are required.</p>'
            . '<a href="javascript:history.back()">Go Back</a></div>';
        render_page('Error', $body, true);
        exit;
    }
    if ($password !== $confirm_pass) {
        $body = '<div class="card-icon"><i class="fas fa-exclamation-circle"></i></div>'
            . '<div class="card-header"><h1>Validation Error</h1></div>'
            . '<div class="error-content"><p>Passwords do not match.</p>'
            . '<a href="javascript:history.back()">Go Back</a></div>';
        render_page('Error', $body, true);
        exit;
    }

    if (!preg_match('/^[a-f0-9]{64}$/', $token)) {
        $body = '<div class="card-icon"><i class="fas fa-exclamation-circle"></i></div>'
            . '<div class="card-header"><h1>Invalid Token</h1></div>'
            . '<div class="error-content"><p>Invalid or malformed token.</p>'
            . '<a href="../index.html">Return Home</a></div>';
        render_page('Error', $body, true);
        exit;
    }

    // Start transaction
    if (!($conn instanceof mysqli)) {
        $body = '<div class="card-icon"><i class="fas fa-exclamation-circle"></i></div>'
            . '<div class="card-header"><h1>Error</h1></div>'
            . '<div class="error-content"><p>Database connection error.</p>'
            . '<a href="../index.html">Return Home</a></div>';
        render_page('Error', $body, true);
        exit;
    }

    $conn->begin_transaction();

    // Lock pending row and fetch details
    $sel = $conn->prepare("SELECT E_id, First_Name, Last_Name, phone, email, Position FROM admin_info_temp WHERE user_token = ? AND status = 'approved' AND expires_at > NOW() FOR UPDATE");
    if (!$sel) {
        $err = $conn->error;
        $conn->rollback();
        $body = '<div class="card-icon"><i class="fas fa-exclamation-circle"></i></div>'
            . '<div class="card-header"><h1>Error</h1></div>'
            . '<div class="error-content"><p>Database error: ' . htmlspecialchars($err) . '</p>'
            . '<a href="../index.html">Return Home</a></div>';
        render_page('Error', $body, true);
        exit;
    }

    $sel->bind_param('s', $token);
    if (!$sel->execute()) {
        $err = $sel->error ?: $conn->error;
        $sel->close();
        $conn->rollback();
        $body = '<div class="card-icon"><i class="fas fa-exclamation-circle"></i></div>'
            . '<div class="card-header"><h1>Error</h1></div>'
            . '<div class="error-content"><p>Database error: ' . htmlspecialchars($err) . '</p>'
            . '<a href="../index.html">Return Home</a></div>';
        render_page('Error', $body, true);
        exit;
    }

    // fetch result
    if (method_exists($sel, 'get_result')) {
        $res = $sel->get_result();
        $pending = $res ? $res->fetch_assoc() : null;
    } else {
        $sel->store_result();
        if ($sel->num_rows === 0) {
            $pending = null;
        } else {
            $sel->bind_result($E_id, $First_Name, $Last_Name, $phone, $emailRow, $Position);
            $sel->fetch();
            $pending = [
                'E_id' => $E_id,
                'First_Name' => $First_Name,
                'Last_Name' => $Last_Name,
                'phone' => $phone,
                'email' => $emailRow,
                'Position' => $Position,
            ];
        }
    }
    $sel->close();

    if (!$pending) {
        $conn->rollback();
        $body = '<div class="card-icon"><i class="fas fa-exclamation-circle"></i></div>'
            . '<div class="card-header"><h1>Invalid Token</h1></div>'
            . '<div class="error-content"><p>Invalid or expired token. Please request a new one.</p>'
            . '<a href="../index.html">Return Home</a></div>';
        render_page('Error', $body, true);
        exit;
    }

    // Insert into final admin_info table
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $ins = $conn->prepare("INSERT INTO admin_info (E_id, First_Name, Last_Name, phone, email, Position, password_hash, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
    if (!$ins) {
        $err = $conn->error;
        $conn->rollback();
        $body = '<div class="card-icon"><i class="fas fa-exclamation-circle"></i></div>'
            . '<div class="card-header"><h1>Error</h1></div>'
            . '<div class="error-content"><p>Database error: ' . htmlspecialchars($err) . '</p>'
            . '<a href="../index.html">Return Home</a></div>';
        render_page('Error', $body, true);
        exit;
    }

    $ins->bind_param('sssssss', $pending['E_id'], $pending['First_Name'], $pending['Last_Name'], $pending['phone'], $pending['email'], $pending['Position'], $hashed_password);
    if (!$ins->execute()) {
        // If insert fails due to duplicate (user already exists), treat as error
        $err = $ins->error ?: $conn->error;
        $ins->close();
        $conn->rollback();
        $body = '<div class="card-icon"><i class="fas fa-exclamation-circle"></i></div>'
            . '<div class="card-header"><h1>Error</h1></div>'
            . '<div class="error-content"><p>Database error: ' . htmlspecialchars($err) . '</p>'
            . '<a href="../index.html">Return Home</a></div>';
        render_page('Error', $body, true);
        exit;
    }
    $ins->close();

    $conn->commit();

    $body = '<div class="card-icon"><i class="fas fa-check-circle"></i></div>'
        . '<div class="card-header"><h1>Success!</h1><p>Your password has been set</p></div>'
        . '<div class="success-content">'
        . '<p>Your account has been created successfully. You can now login with your credentials.</p>'
        . '<a href="../login.html">Go to Login</a>'
        . '</div>';
    render_page('Success', $body, false, true);
    exit;
}

// GET: show form if token present and valid
$token = $_GET['token'] ?? '';
if (!preg_match('/^[a-f0-9]{64}$/', $token)) {
    $body = '<div class="card-icon"><i class="fas fa-exclamation-circle"></i></div>'
        . '<div class="card-header"><h1>Invalid Token</h1></div>'
        . '<div class="error-content"><p>The token provided is invalid or has expired.</p>'
        . '<a href="../index.html">Return Home</a></div>';
    render_page('Invalid Token', $body, true);
    exit;
}

$body = '<div class="card-icon"><i class="fas fa-lock"></i></div>'
    . '<div class="card-header"><h1>Set Your Password</h1><p>Create a secure password for your account</p></div>'
    . '<form id="setPasswordForm" method="POST" action="">'
    . '<input type="hidden" name="token" value="' . htmlspecialchars($token) . '">'
    . '<div class="form-group">'
    . '<label>Password</label>'
    . '<div class="input-group">'
    . '<i class="fas fa-lock"></i>'
    . '<input type="password" name="pass" id="pass" placeholder="Enter password" required>'
    . '</div>'
    . '</div>'
    . '<div class="form-group">'
    . '<label>Confirm Password</label>'
    . '<div class="input-group">'
    . '<i class="fas fa-lock"></i>'
    . '<input type="password" name="confirm_pass" id="confirm_pass" placeholder="Confirm password" required>'
    . '</div>'
    . '</div>'
    . '<button class="btn-submit" type="submit"><i class="fas fa-key"></i> Set Password</button>'
    . '</form>';

render_page('Set Password', $body);
exit;
