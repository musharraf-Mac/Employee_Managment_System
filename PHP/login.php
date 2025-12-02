<?php
session_start();
require 'db.php';

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// Validate input
if ($email === '' || $password === '') {
    exit('Email and password required.');
}

// Get user from database
$stmt = $conn->prepare("SELECT E_id, First_Name, Last_Name, phone, email, Position, password_hash FROM admin_info WHERE email = ?");
if (!$stmt) {
    exit('DB prepare failed: ' . htmlspecialchars($conn->error));
}
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();
$stmt->close();

// Check if user exists before accessing array keys
if (!$admin) {
    exit('Invalid email or password.');
}

// Verify the password (column is password_hash, not password)
if (password_verify($password, $admin['password_hash'])) {
    // Password correct - set session with correct column names
    $_SESSION['admin_id'] = $admin['E_id'];
    $_SESSION['admin_name'] = $admin['First_Name'] . ' ' . $admin['Last_Name'];
    $_SESSION['admin_email'] = $admin['email'];    
    $conn->close();
    ?>
    <script>
        alert("Login successful! Welcome");
        window.location.href = "/Employee_Managment_System/Admin_db.html";
    </script>
    
    <?php
} else { ?>
    // Invalid credentials
    <script>
        alert("Invalid email or password.");  
        window.location.href = "/Employee_Managment_System/login.html";      
    </script>
    <?php
}

$conn->close();
?>