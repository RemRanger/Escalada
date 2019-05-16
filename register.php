<?php
session_start();
if(isset($_SESSION['IdUser'])!="")
{
	header("Location: index.php");
}
include_once 'dbconnect.php';

if(isset($_POST['btn-signup']))
{
 $uname = mysqli_real_escape_string($conn, $_POST['uname']);
 $fname = mysqli_real_escape_string($conn, $_POST['fname']);
 $lname = mysqli_real_escape_string($conn, $_POST['lname']);
 $gender = mysqli_real_escape_string($conn, $_POST['gender']);
 $email = mysqli_real_escape_string($conn, $_POST['email']);
 $upass = md5(mysqli_real_escape_string($conn, $_POST['pass']));
 
 if(mysqli_query($conn, "INSERT INTO User(Username, FirstName, LastName, Gender, Email, Password) VALUES('$uname', '$fname', '$lname', '$gender', '$email', '$upass')"))
 {
		header("Location: index.php");
  ?>
        <script>alert('successfully registered ');</script>
        <?php
 }
 else
 {
  ?>
        <script>alert('error while registering you...');</script>
        <?php
 }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Login & Registration System</title>
<link rel="stylesheet" href="style.css" type="text/css" />
<title>&Xi;SC&Lambda;L&Lambda;D&Lambda;</title>
<link rel="icon" type="image/x-icon" href="favicon.ico">

</head>
<body>
<center>
<?php include_once 'mainmenu.php';?>
<div id="login-form">
<form method="post">
<table class="noborder align="center" width="30%" border="0">
	<tr><td class="noborder"><label><h1>Register</h1></label></td></tr>
	<tr><td class="noborder"><input type="text" name="fname" placeholder="First Name" required /></td></tr>
	<tr><td class="noborder"><input type="text" name="lname" placeholder="Last Name" required /></td></tr>
	<tr><td class="noborder"><input type="radio" name="gender" value="M" required>Male&nbsp;&nbsp;<input type="radio" name="gender" value="F" required>Female</td>
	<tr><td class="noborder"><input type="email" name="email" placeholder="Your Email" required /></td></tr>
	<tr><td class="noborder"><input type="text" name="uname" placeholder="User Name" required /></td></tr>
	<tr><td class="noborder"><input type="password" name="pass" placeholder="Your Password" required /></td></tr>
</table>
<br>
<button type="submit" name="btn-signup">Register</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="login.php">Login</a></form>
</div>
</center>
</body>
</html>