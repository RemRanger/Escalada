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

<?php
$IdLocation = $_GET["IdLocation"];
$IdUser = $_SESSION["IdUser"];
if (!isset($IdUser))
	$IdUser = 0;
$Type = $_GET["Type"];
$Sort = $_GET["Sort"];
$PrevSort = $_GET["PrevSort"];
$SortDirection = $_GET["SortDirection"];
$History = $_GET["History"];

$sql = "select EditRoutes from LocationUser where IdLocation = $IdLocation and IdUser = $IdUser";
$result = $conn->query($sql);
if ($row = $result->fetch_assoc()) 
	$CanEditRoutes = $row["EditRoutes"];
else
	$CanEditRoutes = 0;

if (isset($Sort) && isset($PrevSort) && isset($SortDirection))
{	
	if ($Sort == $PrevSort)
		$SortDirection = 1 - $SortDirection;
	else 
		$SortDirection = 0;
}

$thisUrl = "routes.php?IdLocation=" . $IdLocation;

include_once 'dbconnect.php';

echo '<h1>Routes</h1>';

$sql = "select Name, DefaultRouteType from Location Loc where Loc.Id = $IdLocation";
$result = $conn->query($sql);
if ($row = $result->fetch_assoc()) 
{
	if (!isset($Type))
		$Type = $row["DefaultRouteType"];
	
	echo $row["Name"];
	echo '<br><br>';
}
if ($CanEditRoutes)
{
	echo '<button style="width:100px" name="btn-addroute" onclick="window.location=\'addroute.php?IdLocation=' . $IdLocation . '\'">Add route</button>';
	echo '<br><br>';
}

$sql = "select RouteType from LocationRouteType where IdLocation = $IdLocation";
$result = $conn->query($sql);
echo '<select id="Type" onChange="refresh()"';
echo '>';
while ($row = $result->fetch_assoc()) 
	echo '  <option value="' . $row["RouteType"] . '"' . ($Type == $row["RouteType"] || $result->num_rows < 2 ? ' selected' : '') . '>' . $row["RouteType"] . '</option>';
echo '</select>';
echo '<input type="checkbox" id="History"';
if ($History == "True")
	echo ' checked="True"';
echo ' onChange="refresh()">History</input>';
echo '<br><br>';

$sql = "
select Rou.Id, Rou.Color, Rou.Name, Rou.Type, Rou.Rating, Rou.Sublocation, Rou.Removed, 
	(select Max(Result) from Attempt where IdUser = $IdUser and IdRoute = Rou.Id) Result,
	(select Max(Percentage) from Attempt where IdUser = $IdUser and IdRoute = Rou.Id) Percentage,
	!IsNull(Rou.Picture) HasPicture
from Route Rou 
where Rou.IdLocation = $IdLocation and (Rou.Type = '$Type' || $result->num_rows < 2)
";
if ($History != "True")
	$sql .= "and Rou.Removed = 0";


if (isset($Sort))
{
	$sql .= " order by " . $Sort;
	if ($SortDirection == 1)
		$sql .= " desc";

	if ($Sort == "Result")
	{
		$sql .= ", Percentage";
		if ($SortDirection == 1)
			$sql .= " desc";
	}
}
else
	$sql .= " order by Id desc";

$result = $conn->query($sql);

if ($result->num_rows > 0) 
{
	echo '<table>';
	echo '  <tr>';
	echo '<th colspan="2"><a href="' . $thisUrl . '&Sort=Name&PrevSort=' . $Sort . '&SortDirection=' . $SortDirection . '">Route</a></th>';
	echo '<th><a href="' . $thisUrl . '&Sort=Rating&PrevSort=' . $Sort . '&SortDirection=' . $SortDirection . '">Rating</a></th>';
	echo '<th align="left"><a href="' . $thisUrl . '&Sort=Sublocation&PrevSort=' . $Sort . '&SortDirection=' . $SortDirection . '">Location</a></th>';
	echo '<th/>';
	if ($CanEditRoutes)
	{
			echo '<th><a href="' . $thisUrl . '&Sort=Result&PrevSort=' . $Sort . '&SortDirection=' . $SortDirection . '"><img src="result-finish.png"></a></th><th></th>';
	}
	echo '</tr>';
	while ($row = $result->fetch_assoc()) 
	{
		if (!isset($row["Result"]))
			$resultPic = null;
		else if ($row["Result"] == 0)
			$resultPic = "result-fail.png";
		else if ($row["Result"] == 1)
			$resultPic = "result-faults.png";
		else if ($row["Result"] == 2)
			$resultPic = "result-success.png";

		echo '  <tr><td width="16" bgcolor="' . $row[Color] . ' "></td>';
		echo '<td>';
		if ($CanEditRoutes)
			echo '<a href="userroutestats.php?IdUser=' . $IdUser . '&IdRoute=' . $row["Id"] . '">';
		if ($row["Removed"] == 1)
			echo '<del>';
		echo $row["Name"];
		if ($CanEditRoutes)
			echo '</a>';
		echo '</td>';
		echo '<td>' . $row["Rating"] . '</td><td>' . $row["Sublocation"] . '</td>';
		echo '</td>';
		echo '<td>';
		if ($row["HasPicture"] == 1)
			echo '<a href="getRouteImage.php?Id=' . $row[Id] . '"><img src="picture.png"></a>';
		echo '</td>';
		if ($CanEditRoutes)
		{
			if ($row["Result"] == 0 && $row["Percentage"] !== NULL)
				echo '<td style="color: red">' . $row[Percentage] . '%</td>';
			else
				echo '<td align="center"><img src="' . $resultPic . '"></td>';
			echo '<td><button style="width:40px" onclick="window.location=\'editroute.php?IdRoute=' . $row[Id] . '\'"><img src="edit.png"></button></td>';
		}
		echo '</tr>';
	}
	echo "</table>";
} 
else 
{
    echo "No results";
}

//echo "<br>";
//if ($CanEditRoutes)
//	echo '<button style="width:100px" name="btn-addroute" onclick="window.location=\'addroute.php?IdLocation=' . $IdLocation . '\'">Add route</button>';

$conn->close();

?>

<script>
function refresh()
{
	var routeTypeCombo = document.getElementById("Type");
	var checkbox = document.getElementById("History");

	var type = routeTypeCombo.value;
	var history = checkbox.checked ? "True" : "False";
	
	window.location= 'routes.php?IdLocation=<?php echo $IdLocation ?>&Type=' + type + '&History=' + history;
}
</script>

</body>
</html> 