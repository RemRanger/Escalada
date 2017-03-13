<?php
session_start();
include_once 'dbconnect.php';
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

<h1>Climbers</h1>

<?php

$sql = "SELECT FirstName, LastName, Gender, Id FROM User";
$result = $conn->query($sql);

if ($result->num_rows > 0) 
{
    echo '<table>';
    echo '  <tr><th colspan="2">Climber</th></tr>';
    while ($row = $result->fetch_assoc()) 
    {
		if ($row["Gender"] == 'F')
			$genderColor = "violet";
		else
			$genderColor = "blue";
		
		echo '  <tr><td width="16" bgcolor="' . $genderColor . ' "></td><td>' . $row["FirstName"] . ' ' . $row["LastName"] . '</td></tr>';
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