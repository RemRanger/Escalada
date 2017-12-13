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
	$DateUntil = mysqli_real_escape_string($conn, $_POST['DateUntil']);
	$DateFrom = mysqli_real_escape_string($conn, $_POST['DateFrom']);
	$ReturnUrl = mysqli_real_escape_string($conn, $_POST['ReturnUrl']);

	if ($DateFrom == '')
		$DateFrom = 'NULL';
	else 
		$DateFrom = '\'' . $DateFrom . '\'';
	if ($DateUntil == '')
		$DateUntil = 'NULL';
	else
		$DateUntil = '\'' . $DateUntil . '\'';
		
	if (!mysqli_query($conn, "UPDATE Route set IdLocation = $IdLocation, Type = '$Type', Color = '$Color', Name = '$Name', Sublocation = '$Sublocation', Rating = '$Rating', DateFrom = $DateFrom, DateUntil = $DateUntil WHERE Id = $IdRoute"))
		die(mysqli_error($conn));
	
	$PictureUrl = mysqli_real_escape_string($conn, $_POST['PictureUrl']);
	if ($PictureUrl != null)
	{
		$PictureFileName = basename($PictureUrl);
		if(mysqli_query($conn, "UPDATE Route set PictureFileName = '$PictureFileName' WHERE Id = $IdRoute"))
		{
			$data = file_get_contents($PictureUrl);
			$fileName = 'RoutePictures/' . $PictureFileName;
			file_put_contents($fileName, $data);
		}
		else
			die(mysqli_error($conn));
	}
		
	if (isset($ReturnUrl))
		header("Location: " . $ReturnUrl);
	else
		header("Location: routes.php?IdLocation=" . $IdLocation);
}

$sql = "
select IdLocation, Type, Color, Name, Sublocation, Rating, DateFrom, DateUntil, PictureFileName
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
	$DateFrom = $row["DateFrom"];
	$DateUntil = $row["DateUntil"];
	$PictureFileName = $row["PictureFileName"];
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
<tr>
	<td>
		<table class="noborder" border="0">
			<tr>
				<td class="noborder"  style="text-align: left">Location</td>
				<td class="noborder"  style="text-align: left">
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
			</tr>
			<tr>
				<td class="noborder" style="text-align: left">Type</td>
				<td class="noborder" style="text-align: left"><div id="RouteTypeContainer"></div></td>
			</tr>
			<tr><td class="noborder" style="text-align: left">Name</td><td class="noborder" style="text-align: left"><input type="text" name="Name" required placeholder="Name" value="<?php echo $Name ?>" /></td></tr>
			<tr><td class="noborder" style="text-align: left">Where</td><td class="noborder" style="text-align: left"><input type="text" name="Sublocation" required placeholder="Where is it?" value="<?php echo $Sublocation ?>" /></td></tr>
			<tr><td class="noborder" style="text-align: left">Rating</td><td class="noborder" style="text-align: left"><input type="text" name="Rating" required placeholder="Rating" value="<?php echo $Rating ?>" /></td></tr>
			<tr><td class="noborder" style="text-align: left">Created</td><td class="noborder" style="text-align: left"><input type="date" name="DateFrom" placeholder="DateFrom" value="<?php echo $DateFrom ?>"/></td></tr>
			<tr><td class="noborder" style="text-align: left">Removed</td><td class="noborder" style="text-align: left"><input type="date" name="DateUntil" placeholder="DateUntil" value="<?php echo $DateUntil ?>"/></td></tr>
			<tr>
				<td class="noborder" style="text-align: left">Color</td>
				<td class="noborder" style="text-align: left">
					<?php include_once 'colorSelector.php'; ?>
				</td>
			</tr>
			<tr><td class="noborder" style="text-align: left">Picture</td><td class="noborder"  style="text-align: left"><input type="text" name="PictureUrl" placeholder="Enter Image URL" style="width: 100%"></td>
			</tr>
		</table>
	</td>
<?php if ($PictureFileName != null)
{
?>
	<td>
		<img src="RoutePictures/<?php echo $PictureFileName ?>" />
	</td>
<?php
}
?>
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
