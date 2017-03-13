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

<h1>My sessions</h1>

<?php
$IdUser = $_SESSION["IdUser"];

include_once 'dbconnect.php';

if (!empty($IdUser))
{
	echo '<button style="width:100px" name="btn-addSession" onclick="window.location=\'addsession.php\'">Add session</button>';
	echo "<br><br>";
}

$sql = "
select Ses.Id IdSes, SesToUsr.Comment, UNIX_TIMESTAMP(Ses.Date) SesDate, Loc.Name LocName
from Session Ses
join SessionToUser SesToUsr on SesToUsr.IdSession = Ses.Id
join Location Loc on Loc.Id = Ses.IdLocation
where SesToUsr.IdUser = $IdUser
order by Ses.Date desc
";
$result = $conn->query($sql);

if ($result->num_rows > 0) 
{
    echo '<table>';
    echo '  <tr><th>Date</th><th>Location</th><th>With</th><th>Comment</th><th /></tr>';
    while ($row = $result->fetch_assoc()) 
    {
			$IdSes = $row['IdSes'];
			echo '  <tr>';
			echo '     <td nowrap><a href="usersession.php?IdSession=' . $IdSes . '">' . date("D d-M-Y", $row["SesDate"]) . '</a></td>';
			echo '     <td nowrap>' . $row["LocName"] . '</td>';
			echo '     <td>';

			$sqlWith = "
			select Usr.Id, Usr.FirstName, Usr.LastName 
			from User Usr
			join SessionToUser SesToUsr on SesToUsr.IdUser = Usr.Id
			where SesToUsr.IdSession = $IdSes and SesToUsr.IdUser <> $IdUser
			";
			$resultWith = $conn->query($sqlWith);
			if ($resultWith->num_rows > 0) 
			{
				$index = 0;
				while ($rowWith = $resultWith->fetch_assoc()) 
				{
					if ($index > 0)
					{
						if ($index < $resultWith->num_rows - 1)
							echo ', ';
						else
							echo ' and ';
					}
					echo $rowWith[FirstName] . ' ' . $rowWith[LastName];
					$index++;
				}
			}		
			echo '    </td>';
			echo '    <td>' . $row["Comment"] . '</td>';
			if (!empty($_SESSION["IdUser"]))
				echo '<td><button style="width:40px" onclick="window.location=\'editsession.php?IdSession=' . $IdSes . '\'">Edit</button></td>';
			echo '  </tr>';
    }
    echo "</table>";
} 
else 
{
    echo "0 results";
}

echo "<br>";
if (!empty($_SESSION["IdUser"]))
	echo '<button style="width:100px" name="btn-addSession" onclick="window.location=\'addsession.php\'">Add session</button>';

$conn->close();

?>
</body>
</html> 