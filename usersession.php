<?php
session_start();
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

<?php
$IdSession = $_GET["IdSession"];
$IdUser = $_GET["IdUser"];
if (empty($IdUser))
	$IdUser = $_SESSION["IdUser"];

include_once 'dbconnect.php';

$sql = "
select UNIX_TIMESTAMP(Ses.Date) SesDate, Usr.FirstName, Usr.LastName, Loc.Name LocName, SesToUsr.Comment
from Session Ses
join SessionToUser SesToUsr on SesToUsr.IdSession = Ses.Id
join User Usr on Usr.Id = SesToUsr.IdUser
join Location Loc on Loc.Id = Ses.IdLocation
where Ses.Id = $IdSession and Usr.Id = $IdUser
";
$result = $conn->query($sql);
if ($row = $result->fetch_assoc()) 
{	
	echo '<h1>Session: ' . date("D d-M-Y", $row["SesDate"]) . '</h1>';
	echo $row["Comment"];
	echo '<br>';
	echo '<table class="noborder">';
	echo '<tr><td class="noborder">Climber:</td><td class="noborder">' . $row["FirstName"] . ' ' . $row["LastName"] . '</td><br>';
	$LocName = $row["LocName"];
}
else
	echo '<table class="noborder">';

$sql = "
select Usr.Id, Usr.FirstName, Usr.LastName 
from User Usr
join SessionToUser SesToUsr on SesToUsr.IdUser = Usr.Id
where SesToUsr.IdSession = $IdSession and SesToUsr.IdUser <> $IdUser
";
$result = $conn->query($sql);
if ($result->num_rows > 0) 
{
	echo '<tr><td class="noborder">With:</td><td class="noborder">';
	$index = 0;
  while ($row = $result->fetch_assoc()) 
	{
		if ($index > 0)
		{
			if ($index < $result->num_rows - 1)
				echo ', ';
			else
				echo ' and ';
		}
		echo $row[FirstName] . ' ' . $row[LastName];
		$index++;
	}
	echo '</td></tr>';
}

echo '<tr><td class="noborder">At:</td><td class="noborder">' . $LocName . '</td></tr>';
echo '</table>';
echo '<br>';

$sql = "
select Rou.Id IdRoute, Rou.Color, Rou.Name, Rou.Type, Rou.Rating, Rou.Sublocation, Rou.DateUntil, Rou.PictureFileName, Att.Result, Att.Percentage, Att.Comment, Att.Id IdAttempt
from Attempt Att
join Route Rou on Rou.Id = Att.IdRoute
where Att.IdSession = $IdSession and Att.IdUser = $IdUser
order by Att.Order, Att.Id
";
$result = $conn->query($sql);

if ($result->num_rows > 0) 
{
  echo '<table>';
  echo '  <tr><th colspan="2">Route</th><th align="left">Type</th><th>Rating</th><th align="left">Location</th><th/><th width="16"><img src="result-finish.png"></th><th>Comment</th>';
	if ($_SESSION["IdUser"] == $IdUser)
		echo '<th></th>';
	echo '</tr>';
  while ($row = $result->fetch_assoc()) 
  {
		if ($row["Result"] == 0)
			$resultPic = "result-fail.png";
		else if ($row["Result"] == 1)
			$resultPic = "result-faults.png";
		else  if ($row["Result"] == 2)
			$resultPic = "result-success.png";
		
		echo '  <tr><td width="16" bgcolor="' . $row[Color] . ' "></td>';
		echo '<td nowrap>';
		if ($_SESSION["IdUser"] == $IdUser)
			echo '<a href="userroutestats.php?IdRoute=' . $row["IdRoute"] . '&IdUser=' . $IdUser . '">';
		if ($row["DateUntil"] != null)
			echo '<del>';
		echo $row["Name"];
		if ($_SESSION["IdUser"] == $IdUser)
			echo '</a>';
		echo '</td>';
		echo '<td nowrap>' . $row["Type"] . '</td><td nowrap>' . $row["Rating"] . '</td><td nowrap>' . $row["Sublocation"] . '</td>';
		echo '<td width="16px">';
		if ($row["PictureFileName"] != null)
			echo '<a href="RoutePictures/' . $row["PictureFileName"] . '"><img src="picture.png"></a>';
		echo '</td>';
		if ($row["Result"] == 0 && $row["Percentage"] !== NULL)
			echo '<td style="color: red">' . $row[Percentage] . '%</td>';
		else
			echo '<td align="center"><img src="' . $resultPic . '"></td>';
		echo '</td>';
		echo '<td>' . $row["Comment"] . '</td>';
		if ($_SESSION["IdUser"] == $IdUser)
			echo '<td><button style="width:40px" onclick="window.location=\'editattempt.php?&IdAttempt=' . $row[IdAttempt] . '\'"><img src="edit.png"></button></td>';
		echo '</tr>';
  }
  echo "</table>";
} 
else 
{
    echo "No results";
}

echo "<br>";
if ($_SESSION["IdUser"] == $IdUser)
	echo '<button style="width:100px" name="btn-addAttempt" onclick="window.location=\'addattempt.php?&IdSession=' . $IdSession . '\'">Add attempt</button>&nbsp;&nbsp';
echo '<button style="width:100px" onClick="history.go(-1);">Back</button>';

$conn->close();

?>
</body>
</html> 