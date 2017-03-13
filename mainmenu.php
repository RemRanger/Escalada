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
	"index.php" => "&Xi;SC&Lambda;L&Lambda;D&Lambda;",
	"locations.php" => "Locations",
	"climbers.php" => "Climbers",
	"about.php" => "About"
];

echo '<ul class="topnav">';
foreach ($pages as $page => $pageName)
{
	echo '	<li><a href="' . $page . '"';
	if($pageUrl == $page) 
		echo 'class="active"';
	echo '>' . $pageName .'</a></li>';
}

if (!empty($_SESSION["IdUser"])) 
{
	echo '<li class="dropdown">';
	echo '  <a href="#" class="dropbtn"">' . $row["FirstName"] . '</a>';
	echo '    <div class="dropdown-content">';
	echo '    	<a href="sessions.php">My sessions</a>';
	echo '    	<a href="progress.php">Progress</a>';
	echo '    	<a href="whatsnext.php">What\'s next</a>';
	echo '    	<a href="logout.php?logout">Logout</a>';
	echo '    </div>';
	echo '</li>';
}
else
	echo '<li><a href="login.php">Login</a></li>';

echo '</ul>';

?>
