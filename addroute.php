<?php
session_start();
if(isset($_SESSION['IdUser']) == "")
{
	header("Location: index.php");
}
include_once 'dbconnect.php';

$IdUser = $_SESSION["IdUser"];
$IdLocation = mysqli_real_escape_string($conn, $_GET['IdLocation']);
$IdSession = mysqli_real_escape_string($conn, $_GET['IdSession']);
$IdAttempt = mysqli_real_escape_string($conn, $_GET['IdAttempt']);

if(isset($_POST['btn-addroute']))
{
	$Type = mysqli_real_escape_string($conn, $_POST['Type']);
	$IdLocation = mysqli_real_escape_string($conn, $_POST['IdLocation']);
	$Color = mysqli_real_escape_string($conn, $_POST['Color']);
	$Name = mysqli_real_escape_string($conn, $_POST['Name']);
	$Sublocation = mysqli_real_escape_string($conn, $_POST['Sublocation']);
	$Rating = mysqli_real_escape_string($conn, $_POST['Rating']);

	//echo "<script>alert('Type="  . $Type . " IdLocation="  . $IdLocation . " Color="  . $Color . " Name="  . $Name . " Sublocation="  . $Sublocation . " Rating="  . $Rating . "');</script>";
	
	if(mysqli_query($conn, "INSERT INTO Route (Type, IdLocation, Color, Name, Sublocation, Rating, Removed) VALUES('$Type', '$IdLocation', '$Color', '$Name', '$Sublocation', '$Rating', '0')"))
	{
		if (isset($IdLocation) && !empty($IdLocation))
			header("Location: routes.php?IdLocation=" . $IdLocation);
		else if (isset($IdAttempt) && !empty($IdAttempt))
			header("Location: editattempt.php?IdSession=" . $IdSession . "&IdAttempt=" . $IdAttempt);
		else
			header("Location: addattempt.php?IdSession=" . $IdSession);
	?>
		<?php
	}
	else
	{
	?>
		<script>alert('Error adding route...');</script>
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
<form method="post">
<table class="noborder align="center" width="30%" border="0">
	<tr><td class="noborder"><label><h1>Add route</h1></label></td></tr>
	<td class="noborder">
		<select name="IdLocation"" required onChange="RefreshLocation(this.value)">
			<?php
			if (!isset($IdLocation) || empty($IdLocation))
				echo '<option disabled selected>--Please select a location--</option>';
			$sql = "select Id, Name from Location";
			$result = $conn->query($sql);
			while ($row = $result->fetch_assoc()) 
			{
				echo '<option value="' . $row["Id"]. '"';
				if ($row["Id"] == $IdLocation)
					echo ' selected';
				echo '>';
				echo $row["Name"];
				echo '</option>';
			}
			?>
		</select>
	</td>
	<tr><td class="noborder">
		<div id="RouteTypeContainer"></div>
	</td></tr>
	<tr><td class="noborder"><input type="text" name="Name" required placeholder="Name"/></td></tr>
	<tr><td class="noborder"><input type="text" name="Sublocation" required placeholder="Where is it?"/></td></tr>
	<tr><td class="noborder"><input type="text" name="Rating" required placeholder="Rating"/></td></tr>
	<tr>
		<td class="noborder">
			Color
			<?php include_once 'colorSelector.php'; ?>
		</td>
	</tr>
</table>
<br>
<button style="width:100px" type="submit" name="btn-addroute">OK</button>&nbsp;&nbsp;
<input type="button" value="Cancel" style="width:100px" onClick="history.go(-1);" />
</form>
</div>
</center>

<script>
RefreshLocation(<?php echo $IdLocation ?>);

function RefreshLocation(idLocation)
{
	var container = document.getElementById("RouteTypeContainer"); 

	if (window.XMLHttpRequest)
		xmlhttpRouteTypes = new XMLHttpRequest();
	else 
		xmlhttpRouteTypes = new ActiveXObject("Microsoft.XMLHTTP");
	xmlhttpRouteTypes.onreadystatechange = function() 
	{
		if (xmlhttpRouteTypes.readyState == 4 && xmlhttpRouteTypes.status == 200) 
		{
			container.innerHTML = xmlhttpRouteTypes.responseText;
			cell.style = "";
		}
	};
	xmlhttpRouteTypes.open("GET", "getRouteTypes.php?IdLocation=" + idLocation, true);
	xmlhttpRouteTypes.send();
}
</script>

</body>
</html>