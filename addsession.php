<?php
session_start();
if(isset($_SESSION['IdUser']) == "")
{
	header("Location: index.php");
}
include_once 'dbconnect.php';

$IdUser = $_SESSION["IdUser"];

if(isset($_POST['btn-addsession']))
{
	$IdLocation = mysqli_real_escape_string($conn, $_POST['IdLocation']);
	$Year = mysqli_real_escape_string($conn, $_POST['Year']);
	$Month = mysqli_real_escape_string($conn, $_POST['Month']);
	$Day = mysqli_real_escape_string($conn, $_POST['Day']);
	$Date = $Year . '-' . $Month . '-' . $Day;
	$PartnerIds = $_POST['PartnerIds'];
	$Comment = mysqli_real_escape_string($conn, $_POST['Comment']);

	$success = True;
	if(!mysqli_query($conn, "INSERT INTO Session (IdLocation, Date) VALUES('$IdLocation', '$Date')"))
	{
		$success = False;
		?>
		<script>alert('Error adding session...');</script>
		<?php
	}
	
	$IdSession = $conn->insert_id;
	if ($success)
	{
		if(!mysqli_query($conn, "INSERT INTO SessionToUser (IdSession, IdUser, Comment) VALUES('$IdSession', '$IdUser', '$Comment')"))
		{
			$success = False;
			?>
			<script>alert('Error adding session user...');</script>
			<?php
		}
	}

	if ($success && !empty($PartnerIds))
	{
		foreach ($PartnerIds as $IdPartner)
		{
			if(!mysqli_query($conn, "INSERT INTO SessionToUser (IdSession, IdUser) VALUES('$IdSession', '$IdPartner')"))
			{
				$success = False;
				break;
			}
		}	
	}

	if ($success)
		header("Location: addattempt.php");
	else
	{
		?>
		<script>alert('Error adding session partners...');</script>
		<?php
	}
}

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
	<tr><td class="noborder"><label><h1>Add session</h1></label></td></tr>
	<tr>
		<td class="noborder">
			<?php include_once 'datepicker.php';?>
		</td>
	</tr>
	<tr>
		<td class="noborder">
			<select name="IdLocation"" required>
				<option disabled selected>--Please select a location--</option>
				<?php
				$sql = "select Id, Name from Location";
				$result = $conn->query($sql);
				while ($row = $result->fetch_assoc()) 
					echo '<option value="' . $row["Id"]. '">' . $row["Name"] . '</option>';
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
				$sql = "select Id, FirstName, LastName from User where Id <> $IdUser";
				$result = $conn->query($sql);
				while ($row = $result->fetch_assoc()) 
					echo '<option value="' . $row["Id"]. '">' . $row["FirstName"] . ' ' . $row["LastName"] . '</option>';
				?>
			</select>
		</td>
	</tr>
	<tr><td class="noborder"><textarea rows="4" cols="50" name="Comment" form="sessionform"></textarea></td></tr>
</table>
<br>
<button style="width:100px" type="submit" name="btn-addsession">OK</button>&nbsp;&nbsp;
<input type="button" value="Cancel" style="width:100px" onClick="history.go(-1);" />
</form>
</div>
</center>

</body>
</html>