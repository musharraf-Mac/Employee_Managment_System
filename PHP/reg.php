<?php
require 'db.php';
$First_Name=trim($_POST['First_Name']?? '');
$Last_Name=trim($_POST['Last_Name']?? '');
$phone=trim($_POST['phone']?? '');
$email=trim($_POST['email']?? '');
$E_id=trim($_POST['E_id'])?? '';
$pos=trim($_POST['pos'])?? '';
$sql="INSERT INTO admin_info_temp (First_Name,Last_Name,phone,email,E_id,Position) VALUES ('$First_Name','$Last_Name','$phone','$email','$E_id','$pos')";
if (mysqli_query($conn, $sql)) {
    ?><script>
        alert("Registration Successful");
        window.location.href = "/Employee_Managment_System/login.html";
    </script><?php
} 
else {
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}
?>