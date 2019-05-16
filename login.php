<?php
session_start();
include_once 'dbconnect.php';

if (isset($_SESSION['IdUser']) != "")
{
	header("Location: index.php");
	//echo 'Logged in already';
}
if(isset($_POST['btn-login']))
{
 $username = mysqli_real_escape_string($conn, $_POST['username']);
 $upass = mysqli_real_escape_string($conn, $_POST['pass']);
 
 $res=$conn->query("SELECT Id, Password FROM User WHERE UserName='$username'");
 $row=$res->fetch_assoc();
 if($row['Password'] == md5($upass))
 {
	$_SESSION['IdUser'] = $row['Id'];
	header("Location: index.php");
 }
 else
 {
	?>
        <script>alert('Wrong user name/password.');</script>
        <?php
 }
 
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="style.css" type="text/css" />
<title>&Xi;SC&Lambda;L&Lambda;D&Lambda;</title>
<link rel="icon" type="image/x-icon" href="favicon.ico">
</head>
<body>
<center>
<?php include_once 'mainmenu.php';?>
<div id="login-form">
<form method="post">
<table class="noborder" align="center" width="30%" border="0">
	<tr>
		<td class="noborder"><label><h1>Login</h1></label></td>
	</tr>
	<tr>
		<td class="noborder"><input type="text" name="username" placeholder="Your user name" required /></td>
	</tr>
	<tr>
		<td class="noborder"><input type="password" name="pass" placeholder="Your Password" required /></td>
	</tr>
</table>
<br>
<button type="submit" name="btn-login">Login</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="register.php">Register</a>
</form>
</div>
</center>
</body>
</html>