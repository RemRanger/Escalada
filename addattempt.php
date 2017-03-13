<?php
session_start();
if(isset($_SESSION['IdUser']) == "")
{
	header("Location: index.php");
}
include_once 'dbconnect.php';

$IdUser = $_SESSION["IdUser"];

if(isset($_POST['btn-addattempt']))
{
	$IdSession = mysqli_real_escape_string($conn, $_POST['IdSession']);
	$IdRoute = mysqli_real_escape_string($conn, $_POST['IdRoute']);
	$Result = mysqli_real_escape_string($conn, $_POST['Result']);
	if ($Result == 0)
		$Percentage = mysqli_real_escape_string($conn, $_POST['Percentage']);
	else
		$Percentage = 'NULL';
	$Comment = mysqli_real_escape_string($conn, $_POST['Comment']);

	if(mysqli_query($conn, "INSERT INTO Attempt (IdUser, IdSession, IdRoute, Result, Percentage, Comment) VALUES('$IdUser', '$IdSession', '$IdRoute', '$Result', '$Percentage', '$Comment')"))
	{
		header("Location: usersession.php?IdSession=" . $IdSession);
	?>
		<?php
	}
	else
	{
	?>
		<script>alert('Error adding attempt...');</script>
		<?php
	}
}
else
	$IdSession = mysqli_real_escape_string($conn, $_GET['IdSession']);

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
<form id="attemptform" method="post">
<table class="noborder align="center" width="30%" border="0">
	<tr><td class="noborder"><label><h1>Add attempt</h1></label></td></tr>
	<tr>
		<td class="noborder">
			<select name="IdSession" onChange="RefreshSession(this.value)" required>
				<option disabled selected>--Please select a session--</option>
				<?php
				$sql = "
					select Ses.Id IdSes, UNIX_TIMESTAMP(Ses.Date) SesDate, Loc.Name LocName
					from Session Ses
					join SessionToUser SesToUsr on SesToUsr.IdSession = Ses.Id
					join Location Loc on Loc.Id = Ses.IdLocation
					where SesToUsr.IdUser = $IdUser
					order by Ses.Date desc
					";
				$result = $conn->query($sql);
				while ($row = $result->fetch_assoc()) 
				{
					echo '<option value="' . $row["IdSes"] . '"';
					if ($row["IdSes"] == $IdSession)
						echo " selected";
					echo '>' . date("D d-M-Y", $row["SesDate"]) . ' at ' . $row["LocName"] . '</option>';
				}
				?>
			</select>
		</td>
	</tr>
	<tr><td class="noborder"><label id="WithWhom"></label></td></tr>
	<tr>
	<tr>
		<td id="RouteSelectorCell" <?php if (empty($IdSession)) echo 'style="display:none"' ?> class="noborder">
			<div style="border: outset; border-width: 1px ;height: 400px; overflow-y: auto" id="RouteSelector"></div>
			<a href="addroute.php?IdSession=<?php echo $IdSession ?>&IdAttempt=<?php echo $IdAttempt ?> ">Add route</a>
		</td>
	</tr>
	<tr><td class="noborder">Completed:
		<select id="Result" name="Result"  onChange="ResultChanged()">
			<option value="0" <?php if ($Result == 0) echo ' selected' ?>>Partly:</option>
			<option value="1" <?php if ($Result == 1) echo ' selected' ?>>With fall or block</option>
			<option value="2" <?php if ($Result == 2) echo ' selected' ?>>In one go</option>
		</select>
		<span id="PercentageBlock">
			<select id="Percentage" name="Percentage">
				<?php
				for ($percentageStep = 0; $percentageStep < 100; $percentageStep += 5)
				{
					echo '<option value="' . $percentageStep . '"';
					if ($percentageStep == $Percentage) 
						echo ' selected';
					echo '>' . $percentageStep . '</option>';
				}
				?>
			</select> %
		</span>
		</td></tr>
	<tr><td class="noborder"><textarea rows="4" cols="50" name="Comment" form="attemptform"><?php echo $Comment ?></textarea></td></tr>
</table>
<br>
<button style="width:100px" type="submit" name="btn-addattempt">OK</button>&nbsp;&nbsp;
<input type="button" value="Cancel" style="width:100px" onClick="history.go(-1);" />
</form>
</div>
</center>

<script>
<?php
if (!empty($IdSession))
	echo 'RefreshSession(' . $IdSession . ');';
?>
ResultChanged();

function RefreshSession(idSession)
{
	RefreshPartners(idSession);
	RefreshRoutes(idSession);
}

function RefreshPartners(idSession) 
{
	var withwhom = document.getElementById("WithWhom"); 

	if (window.XMLHttpRequest)
		xmlhttpPartners = new XMLHttpRequest();
	else 
		xmlhttpPartners = new ActiveXObject("Microsoft.XMLHTTP");
	xmlhttpPartners.onreadystatechange = function() 
	{
		//if (xmlhttpPartners.readystate == 4 && xmlhttpPartners.status == 200) 
			withwhom.textContent = xmlhttpPartners.responseText;
	};
	xmlhttpPartners.open("GET", "getpartners.php?idSession=" + idSession, true);
	xmlhttpPartners.send();
}

function RefreshRoutes(idSession)
{
	var routeSelector = document.getElementById("RouteSelector"); 
	var routeSelectorCell = document.getElementById("RouteSelectorCell"); 

	if (window.XMLHttpRequest)
		xmlhttpRoutes = new XMLHttpRequest();
	else 
		xmlhttpRoutes = new ActiveXObject("Microsoft.XMLHTTP");
	xmlhttpRoutes.onreadystatechange = function() 
	{
		if (xmlhttpRoutes.readyState == 4 && xmlhttpRoutes.status == 200) 
		{
			routeSelector.innerHTML = xmlhttpRoutes.responseText;
			routeSelectorCell.style = "";
		}
	};
	xmlhttpRoutes.open("GET", "routeSelector.php?includeRemoved=0&idSession=" + idSession, true);
	xmlhttpRoutes.send();
}

function ResultChanged()
{
	var selectResult = document.getElementById("Result"); 
	var blockPercentage = document.getElementById("PercentageBlock"); 
	
	var result = selectResult.options[selectResult.selectedIndex].value;
	if (result == 0)
		blockPercentage.style.visibility = "";
	else
		blockPercentage.style.visibility = "hidden"
}

function handleRouteRowClick(idRoute)
{
	var routeRadio = document.getElementById("RouteRadio_" + idRoute);
	if (routeRadio != null)
	{
		routeRadio.checked = true;
		handleRouteClick(idRoute);
	}
}

function handleRouteClick(idRoute)
{
	var table = document.getElementById("RouteTable");
	if (table != null)
	{
		for (var i = 0, row; row = table.rows[i]; i++)
				row.style = null;
	
		var routeRow = document.getElementById("RouteRow_" + idRoute);
		if (routeRow != null)
		{
			routeRow.style.color = "white";
			routeRow.style.backgroundColor = "black";
			routeRow.style.opacity = 0.5;
		}
	}
}</script>

</body>
</html>