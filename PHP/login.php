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
        // Store admin info in sessionStorage for frontend access
        sessionStorage.setItem('admin_id', '<?php echo htmlspecialchars($admin['E_id']); ?>');
        sessionStorage.setItem('admin_name', '<?php echo htmlspecialchars($admin['First_Name'] . ' ' . $admin['Last_Name']); ?>');
        sessionStorage.setItem('admin_email', '<?php echo htmlspecialchars($admin['email']); ?>');
        
        alert("Login successful! Welcome <?php echo htmlspecialchars($admin['First_Name']); ?>");
        // Use relative redirect (InfinityFree path may not be /Employee_Managment_System)
        window.location.href = "../Admin_db.html";
    </script>
    
    <?php
} else { ?>
    <script>
        alert("Invalid email or password.");  
        window.location.href = "../login.html";
    </script>
    <?php
}

$conn->close();
?>