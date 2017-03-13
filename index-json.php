<?php
session_start();
include_once 'dbconnect.php';

$sql = "
SELECT Att.Id, Rou.Name RouteName, Rou.Type, Rou.Rating, Rou.Color, Att.Completed, Att.WithoutFault, Loc.Name LocName, Rou.Sublocation, 
		Usr.FirstName, Usr.LastName, Ses.Date SesDate, Ses.Id IdSession, Usr.Id IdUser
FROM Attempt Att
JOIN User Usr on Usr.Id = Att.IdUser
JOIN Route Rou on Rou.Id = Att.IdRoute
JOIN Session Ses on Ses.Id = Att.IdSession
JOIN Location Loc on Loc.Id = Ses.IdLocation 
ORDER BY Ses.Date desc, Att.Id desc
";
$result = $conn->query($sql);

$data = $result->fetch_all(MYSQLI_ASSOC);

echo json_encode($data);

$conn->close();
?>

