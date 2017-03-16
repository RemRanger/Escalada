<?php
session_start();
if(isset($_SESSION['IdUser']) == "")
{
	header("Location: index.php");
}
include_once 'dbconnect.php';

$IdUser = $_SESSION["IdUser"];
$IdRoute = $_GET["IdRoute"];
if(isset($_POST['btn-editroute']))
{
	$IdLocation = mysqli_real_escape_string($conn, $_POST['IdLocation']);
	$Type = mysqli_real_escape_string($conn, $_POST['Type']);
	$Color = mysqli_real_escape_string($conn, $_POST['Color']);
	$Name = mysqli_real_escape_string($conn, $_POST['Name']);
	$Sublocation = mysqli_real_escape_string($conn, $_POST['Sublocation']);
	$Rating = mysqli_real_escape_string($conn, $_POST['Rating']);
	$Removed = mysqli_real_escape_string($conn, $_POST['Removed']);
	$ReturnUrl = mysqli_real_escape_string($conn, $_POST['ReturnUrl']);

	if(!mysqli_query($conn, "UPDATE Route set IdLocation = $IdLocation, Type = '$Type', Color = '$Color', Name = '$Name', Sublocation = '$Sublocation', Rating = '$Rating', Removed = $Removed WHERE Id = $IdRoute"))
		die(mysqli_error($conn));
	
	if (isset($ReturnUrl))
		header("Location: " . $ReturnUrl);
	else
		header("Location: routes.php?IdLocation=" . $IdLocation);
}

$sql = "
select IdLocation, Type, Color, Name, Sublocation, Rating, Removed
from Route
where Id = $IdRoute
";
$result = $conn->query($sql);
if ($row = $result->fetch_assoc()) 
{	
	$IdLocation = $row["IdLocation"];
	$Type = $row["Type"];
	$Color = $row["Color"];
	$Name = $row["Name"];
	$Sublocation = $row["Sublocation"];
	$Rating = $row["Rating"];
	$Removed = $row["Removed"];
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
<h1>Edit route</h1>
<div id="login-form">
<form id="sessionform" method="post">
<table class="noborder" border="0">
	<td class="noborder" >Location</td>
	<td class="noborder" >
		<select name="IdLocation" required onChange="RefreshLocation(this.value)">
			<?php
			if (!isset($IdLocation) || empty($IdLocation))
				echo '<option disabled selected>--Please select a location--</option>';
			$sql = "select Id, Name from Location";
			$result = $conn->query($sql);
			while ($row = $result->fetch_assoc()) 
			{
				echo '<option value="' . $row["Id"]. '"';
				if ($IdLocation == $row["Id"])
					echo ' selected';
				echo '>';
				echo $row["Name"];
				echo '</option>';
			}
			?>
		</select>
	</td>
	<tr>
		<td class="noborder">Type</td>
		<td class="noborder">
		<div id="RouteTypeContainer"></div>
	</td></tr>
	<tr><td class="noborder">Name</td><td class="noborder"><input type="text" name="Name" required placeholder="Name" value="<?php echo $Name ?>" /></td></tr>
	<tr><td class="noborder">Where</td><td class="noborder"><input type="text" name="Sublocation" required placeholder="Where is it?" value="<?php echo $Sublocation ?>" /></td></tr>
	<tr><td class="noborder">Rating</td><td class="noborder"><input type="text" name="Rating" required placeholder="Rating" value="<?php echo $Rating ?>" /></td></tr>
	<tr><td class="noborder">Removed</td><td class="noborder"><input type="hidden" name="Removed" value="0" /><input type="checkbox" name="Removed" placeholder="Removed" value="1" <?php if ($Removed == 1) echo 'checked' ?> /></td></tr>
	<tr>
		<td class="noborder">Color</td>
		<td class="noborder">
			<?php include_once 'colorSelector.php'; ?>
		</td>
	</tr>
</table>
<br>
<button style="width:100px" type="submit" name="btn-editroute">OK</button>&nbsp;&nbsp;
<input type="button" value="Cancel" style="width:100px" onClick="history.go(-1);" />
<input type="hidden" name="ReturnUrl" value="<?php echo $_SERVER['HTTP_REFERER']; ?>" style="width:100px"" />
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
	xmlhttpRouteTypes.open("GET", "getRouteTypes.php?IdLocation=" + idLocation + "&Type=<?php echo $Type ?>", true);
	xmlhttpRouteTypes.send();
}
</script>

</body>
</html>
