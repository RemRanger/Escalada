<?php
session_start();
include_once 'dbconnect.php';

if(isset($_SESSION['IdUser']))
{
	$result=$conn->query("SELECT * FROM User WHERE Id=" . $_SESSION['IdUser']);
	$row = $result->fetch_assoc();
	$userFirstName=$row["FirstName"];
	//echo 'Logged in: ' . $_SESSION['IdUser'] . '<br>';
}
//else
//	echo 'Not logged in ' . $_SESSION['IdUser'] . '<br>';

$pageUrl = basename($_SERVER['PHP_SELF']);
$pages = 
[
	"index.php" => "Home",
	"locations.php" => "Locations",
	"climbers.php" => "Climbers",
	"about.php" => "About"
];

echo '<ul class="topnav">';
echo '<li><img src="favicon.ico" style="vertical-align:middle" width="32px"/><span style="color:darkslateblue; font-weight:800">&nbsp;&nbsp;&Xi;SC&Lambda;L&Lambda;D&Lambda;&nbsp;</span></li>';
foreach ($pages as $page => $pageName)
{
	echo '	<li><a href="' . $page . '"';
	if($pageUrl == $page) 
		echo 'class="active"';
	echo '>' . $pageName .'</a></li>';
}

if (!empty($_SESSION["IdUser"])) 
{
	echo '<li style="color:darkslateblue">&nbsp;&nbsp;Welcome ' . $row["FirstName"] . '&nbsp;&nbsp;</li>';
	echo '<li><a href="sessions.php">My sessions</a></li>';
	echo '<li><a href="progress.php">Progress</a></li>';
	echo '<li><a href="whatsnext.php">What\'s next</a></li>';
	echo '<li><a href="logout.php?logout">Logout</a></li>';
}
else
	echo '<li><a href="login.php">Login</a></li>';

echo '</ul>';

?>
