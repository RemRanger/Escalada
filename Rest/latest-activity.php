<?php
header("Content-Type:application/json");
header("Access-Control-Allow-Origin: *");
session_start();
include_once 'dbconnect.php';

$sql = "
SELECT Rou.Id routeId, Rou.Name routeName, Rou.Type type, Rou.Rating rating , Rou.Color color, Rou.DateUntil dateUntil, Att.Result result, Att.Percentage percentage, Loc.Name locationName, Rou.Sublocation subLocation, 
		Usr.FirstName firstName, Usr.LastName lastName, Ses.Date sessionDate, Ses.Id sessionId, Usr.Id userId, Rou.PictureFileName pictureFileName
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
	$outp = $result->fetch_all(MYSQLI_ASSOC);

	echo json_encode($outp, JSON_NUMERIC_CHECK);
}

$conn->close();
?>

