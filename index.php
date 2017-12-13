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
?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>

<center>

<?php include_once 'mainmenu.php';?>

<h1>Latest activity</h1>

<?php

$sql = "
SELECT Rou.Id IdRoute, Rou.Name RouteName, Rou.Type, Rou.Rating, Rou.Color, Rou.DateUntil, Att.Result, Att.Percentage, Loc.Name LocName, Rou.Sublocation, 
		Usr.FirstName, Usr.LastName, UNIX_TIMESTAMP(Ses.Date) SesDate, Ses.Id IdSession, Usr.Id IdUser, Rou.PictureFileName
FROM Attempt Att
JOIN User Usr on Usr.Id = Att.IdUser
JOIN Route Rou on Rou.Id = Att.IdRoute
JOIN Session Ses on Ses.Id = Att.IdSession
JOIN Location Loc on Loc.Id = Ses.IdLocation 
ORDER BY Ses.Date desc, Ses.Id desc, Usr.Id, Att.Order, Att.Id
";
$result = $conn->query($sql);

if ($result->num_rows > 0) 
{
	echo '<table>';
	$lastSessionId = 0;
	$lastUserId = 0;
	while ($row = $result->fetch_assoc()) 
  {
		if ($row["Result"] == 0)
			$resultPic = "result-fail.png";
		else if ($row["Result"] == 1)
			$resultPic = "result-faults.png";
		else if ($row["Result"] == 2)
			$resultPic = "result-success.png";
		
		if ($row["IdSession"] != $lastSessionId)
		{
			//if ($lastSessionId != 0)
			//	echo '<tr><td class="noborder" colspan="100">&nbsp;</td></tr>';
			
			echo '<tr style="background-color:rgba(0, 0, 0, 0.2)">';
			echo '<td colspan="100">';
			echo date("D d-M-Y", $row["SesDate"]) . ', ' . $row["LocName"];
			echo '</td>';
			echo '</tr>';
		}
		if ($row["IdUser"] != $lastUserId || $row["IdSession"] != $lastSessionId)
		{
			echo '<tr style="background-color:rgba(0, 0, 0, 0.1)">';
			echo '<td colspan="100">';
			echo 'Climber:&nbsp;<a href="usersession.php?IdSession=' . $row["IdSession"] . '&IdUser=' . $row["IdUser"] . '">';
			echo $row["FirstName"] . ' ' . $row["LastName"];
			echo '</a> ';
			echo '</td>';
			echo '</tr>';
			echo '  <tr>';
			echo '    <th colspan="2">Route</th>';
			echo '    <th align="left">Type</th>';
			echo '    <th align="left">Rating</th>';
			echo '    <th align="left">Location</th>';
			echo '    <th/>';
			echo '    <th width="16"><img src="result-finish.png"></th>';
			echo '   </tr>';
		}
		
		echo '<tr><td width="16" bgcolor="' . $row["Color"] . ' "></td>';
		echo '<td>';
		if (isset($_SESSION['IdUser']))
			echo '<a href="userroutestats.php?IdRoute=' . $row["IdRoute"] . '&IdUser=' . $_SESSION['IdUser'] . '">';
		if ($row["DateUntil"] != null)
			echo '<del>';
		echo $row["RouteName"] . '</td>';
		if (isset($_SESSION['IdUser']))
			echo '</a>';
		echo '<td>' . $row["Type"] . '</td><td>' . $row["Rating"] . '</td><td>' . $row["Sublocation"] . '</td>';
		echo '<td width="16px">';
		if ($row["PictureFileName"] != null)
			echo '<a href="RoutePictures/' . $row["PictureFileName"] . '" target="_blank"><img src="picture.png"></a>';
		echo '</td>';
		if ($row["Result"] == 0 && $row["Percentage"] !== NULL)
			echo '<td style="color: red">' . $row[Percentage] . '%</td>';
		else
			echo '<td align="center"><img src="' . $resultPic . '"></td>';
		echo '</tr>';
		
		$lastSessionId = $row["IdSession"];
		$lastUserId = $row["IdUser"];
	}
	echo "</table>";
} 
else 
{
    echo "0 results";
}

$conn->close();
?>

</body>
</html> 

