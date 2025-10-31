<?php
$conn=mysqli_connect("localhost","root","","employee_mgt");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>