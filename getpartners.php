<?php
session_start();
include_once 'dbconnect.php';

$idSession = $_GET["idSession"];
$IdUser = $_SESSION["IdUser"];

$sql = "
	select Usr.FirstName, Usr.LastName
	from User Usr 
	join SessionToUser SesToUsr on SesToUsr.IdUser = Usr.Id
	where SesToUsr.IdSession = $idSession and SesToUsr.IdUser <> $IdUser
";

$result = $conn->query($sql);
if ($result->num_rows > 0) 
{
	echo 'With ';
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
		echo $row["FirstName"] . ' ' . $row["LastName"];
		$index++;
	}
}
?>