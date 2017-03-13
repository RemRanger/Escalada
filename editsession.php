<?php
session_start();
if(isset($_SESSION['IdUser']) == "")
{
	header("Location: index.php");
}
include_once 'dbconnect.php';

$IdUser = $_SESSION["IdUser"];
$IdSession = $_GET["IdSession"];
if(isset($_POST['btn-editsession']))
{
	$IdLocation = mysqli_real_escape_string($conn, $_POST['IdLocation']);
	$Year = mysqli_real_escape_string($conn, $_POST['Year']);
	$Month = mysqli_real_escape_string($conn, $_POST['Month']);
	$Day = mysqli_real_escape_string($conn, $_POST['Day']);
	$Date = $Year . '-' . $Month . '-' . $Day;
	$PartnerIds = $_POST['PartnerIds'];
	$Comment = mysqli_real_escape_string($conn, $_POST['Comment']);
	
	//echo 'Partner Ids: ' . implode(", ", $PartnerIds);

	$result = $conn->query("SELECT IdUser FROM SessionToUser WHERE IdSession = $IdSession");
	$existingUserIds = [];
  while ($row = $result->fetch_assoc()) 
	{
		array_push($existingUserIds, $row[IdUser]);
	}	
	//echo 'Existing user Ids: ' . implode(", ", $existingUserIds);
	
	if(!mysqli_query($conn, "UPDATE Session set IdLocation = $IdLocation, Date = '$Date' WHERE Id = $IdSession"))
		die(mysqli_error($conn));
	
	if(!mysqli_query($conn, "UPDATE SessionToUser SET Comment= '$Comment' WHERE IdSession = $IdSession AND IdUser = $IdUser"))
			die(mysqli_error($conn));

	$deletedUserIds = [];
	foreach ($existingUserIds as $idOtherUser)
	{
		if ($idOtherUser != $IdUser && (empty($PartnerIds) || !in_array($idOtherUser, $PartnerIds)))
			array_push($deletedUserIds, $idOtherUser);
	}
	//echo 'Deleted user Ids: ' . implode(", ", $deletedUserIds);

	foreach ($deletedUserIds as $deletedUserId)
	{
		if (is_numeric($deletedUserId))
		{
			if(!mysqli_query($conn, "DELETE FROM SessionToUser WHERE IdSession = $IdSession AND IdUser = $deletedUserId"))
				die(mysqli_error($conn));
		}
	}

	$newUserIds = [];
	if (!empty($PartnerIds))
	{
		foreach ($PartnerIds as $idOtherUser)
		{
			if ($idOtherUser != $IdUser && !in_array($idOtherUser, $existingUserIds))
				array_push($newUserIds, $idOtherUser);
		}
	}
	//echo 'New user Ids: ' . implode(", ", $newUserIds);
	foreach ($newUserIds as $newUserId)
	{
		if (is_numeric($newUserId))
		{
			if(!mysqli_query($conn, "INSERT INTO SessionToUser (IdSession, IdUser) VALUES('$IdSession', '$newUserId')"))
				die(mysqli_error($conn));
		}
	}	
	
	header("Location: sessions.php");
}

$sql = "
select Ses.IdLocation, Ses.Date, SesToUsr.Comment
from Session Ses
join SessionToUser SesToUsr on SesToUsr.IdSession = Ses.Id
where Ses.Id = $IdSession and SesToUsr.IdUser = $IdUser
";
$result = $conn->query($sql);
if ($row = $result->fetch_assoc()) 
{	
	$IdLocation = $row["IdLocation"];
	$Date = $row["Date"];
	$Comment = $row["Comment"];
	
	$Day = date('d', strtotime($Date));
	$Month = date('m', strtotime($Date));
	$Year = date('Y', strtotime($Date));
}
else
	die(mysqli_error($conn))

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="style.css" type="text/css" />

</head>
<body>
<center>
<?php include_once 'mainmenu.php';?>
<div id="login-form">
<form id="sessionform" method="post">
<table class="noborder align="center" width="30%" border="0">
	<tr><td class="noborder"><label><h1>Edit session</h1></label></td></tr>
	<tr>
		<td class="noborder">
			<?php include_once 'datepicker.php';?>
		</td>
	</tr>
	<tr>
		<td class="noborder">
			<select name="IdLocation"" required>
				<?php
				echo '<option disabled';
				if (!empty($IdLocation))
					echo ' selected';
				echo '>--Please select a location--</option>';
				$sql = "select Id, Name from Location";
				$result = $conn->query($sql);
				while ($row = $result->fetch_assoc()) 
				{
					echo '<option value="' . $row["Id"] . '"';
					if ($row["Id"] == $IdLocation)
						echo ' selected';
					echo '>' . $row["Name"] . '</option>';
				}
				?>
			</select>
		</td>
	</tr>
	<tr>
		<td class="noborder">
			<?php $partnerCount = $conn->query("select count(*) from User where Id <> $IdUser")->fetch_row()[0] ?>
			<select name="PartnerIds[]" multiple size='<?php echo min($partnerCount + 1, 30) ?>'>
				<option disabled selected>--Were you with others? If so, please select--</option>
				<?php
				$sql = "select Id, FirstName, LastName, SessionToUser.IdUser from User 
					    left outer join SessionToUser on SessionToUser.IdSession = $IdSession and SessionToUser.IdUser = User.Id
						where Id <> $IdUser";
				$result = $conn->query($sql);
				while ($row = $result->fetch_assoc()) 
				{
					echo '<option value="' . $row["Id"] . '"';
					if ($row["IdUser"] != null)
						echo ' selected';
					echo '>'. $row["FirstName"] . ' ' . $row["LastName"] . '</option>';
				}
				?>
			</select>
		</td>
	</tr>
	<tr><td class="noborder"><textarea rows="4" cols="50" name="Comment" form="sessionform"><?php echo $Comment ?></textarea></td></tr>
</table>
<br>
<button style="width:100px" type="submit" name="btn-editsession">OK</button>&nbsp;&nbsp;
<input type="button" value="Cancel" style="width:100px" onClick="history.go(-1);" />
</form>
</div>
</center>

</body>
</html>
