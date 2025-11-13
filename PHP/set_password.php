<?php
require_once __DIR__ . '/db.php';

// Helper to render a simple HTML page
function render_page($title, $bodyHtml) {
    echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>' . htmlspecialchars($title) . '</title>'
        . '<link rel="stylesheet" type="text/css" href="../CSS/set_password.css">'
        . '<script src="../JS/set_password.js"></script>'
        . '</head><body>'
        . $bodyHtml
        . '</body></html>';
}

// POST: process token and set password
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'] ?? '';
    $password = $_POST['pass'] ?? '';
    $confirm_pass = $_POST['confirm_pass'] ?? '';

    if ($password === '' || $confirm_pass === '') {
        render_page('Error', '<p>Both password fields are required.</p>');
        exit;
    }
    if ($password !== $confirm_pass) {
        render_page('Error', '<p>Passwords do not match.</p>');
        exit;
    }

    if (!preg_match('/^[a-f0-9]{64}$/', $token)) {
        render_page('Error', '<p>Invalid token.</p>');
        exit;
    }

    // Start transaction
    if (!($conn instanceof mysqli)) {
        render_page('Error', '<p>Database connection error.</p>');
        exit;
    }

    $conn->begin_transaction();

    // Lock pending row and fetch details
    $sel = $conn->prepare("SELECT E_id, First_Name, Last_Name, phone, email, Position FROM admin_info_temp WHERE user_token = ? AND status = 'approved' AND expires_at > NOW() FOR UPDATE");
    if (!$sel) {
        $err = $conn->error;
        $conn->rollback();
        render_page('Error', '<p>DB prepare failed: ' . htmlspecialchars($err) . '</p>');
        exit;
    }

    $sel->bind_param('s', $token);
    if (!$sel->execute()) {
        $err = $sel->error ?: $conn->error;
        $sel->close();
        $conn->rollback();
        render_page('Error', '<p>DB execute failed: ' . htmlspecialchars($err) . '</p>');
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
        render_page('Error', '<p>Invalid or expired token.</p>');
        exit;
    }

    // Insert into final admin_info table
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $ins = $conn->prepare("INSERT INTO admin_info (E_id, First_Name, Last_Name, phone, email, Position, password_hash, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
    if (!$ins) {
        $err = $conn->error;
        $conn->rollback();
        render_page('Error', '<p>DB prepare failed (insert): ' . htmlspecialchars($err) . '</p>');
        exit;
    }

    $ins->bind_param('sssssss', $pending['E_id'], $pending['First_Name'], $pending['Last_Name'], $pending['phone'], $pending['email'], $pending['Position'], $hashed_password);
    if (!$ins->execute()) {
        // If insert fails due to duplicate (user already exists), treat as error
        $err = $ins->error ?: $conn->error;
        $ins->close();
        $conn->rollback();
        render_page('Error', '<p>DB insert failed: ' . htmlspecialchars($err) . '</p>');
        exit;
    }
    $ins->close();

    $conn->commit();

    render_page('Success', '<div class="p_cont"><h2>Password set</h2><p>Your account has been created. <a href="/Employee_Managment_System/login.html">Login</a></p></div>');
    exit;
}

// GET: show form if token present and valid
$token = $_GET['token'] ?? '';
if (!preg_match('/^[a-f0-9]{64}$/', $token)) {
    render_page('Invalid', '<p>Invalid token.</p>');
    exit;
}

$body = '<div class="p_cont">'
    . '<h2>Set Your Password</h2>'
    . '<form id="setPasswordForm" method="POST" action="">'
    . '<input type="hidden" name="token" value="' . htmlspecialchars($token) . '">'
    . '<input class="pass_c" type="password" name="pass" id="pass" placeholder="enter password" required>'
    . '<input class="pass_c" type="password" name="confirm_pass" id="confirm_pass" placeholder="confirm password" required>'
    . '<input class="sub_but" type="submit" value="Set Password">'
    . '</form></div>';

render_page('Set password', $body);
