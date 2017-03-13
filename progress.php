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
$IdUser = $_SESSION["IdUser"];
$History = $_GET["History"];

include_once 'dbconnect.php';

echo '<h1>Progress: toprope</h1>';

echo '<input type="checkbox" id="History"';
if (isset($History)) 
	echo ' checked="True"';
echo ' onChange="setHistory(this)">History</input>';

$sql = "
select Rou.Id IdRoute, Rou.Removed, Rou.Rating, Rou.Color, Rou.Name, Loc.Name Location, Rou.Sublocation, Ses.Id IdSession, UNIX_TIMESTAMP(Ses.Date) SesDate, Max(Att.Result) Result, Max(Att.Percentage) Percentage
from Route Rou
join Location Loc on Loc.Id = Rou.IdLocation
left outer join Attempt Att on Att.IdRoute = Rou.Id
left outer join Session Ses on Ses.Id = Att.IdSession
where (Att.IdUser = $IdUser or Att.IdUser is null) and Rou.Type = 'Toprope'";
if (!isset($History))
	$sql .= "and Rou.Removed = 0";
$sql .= "
group by Rou.Id, Rou.Removed, Rou.Rating, Rou.Color, Rou.Name, Loc.name, Rou.Sublocation, Ses.Id, Ses.Date
order by Rou.Rating desc, Max(Att.Result) desc, Max(case Att.Result when 0 then Att.Percentage else 0 end) desc, Ses.Date";
$result = $conn->query($sql);

$pieChartScript = "";

if ($result->num_rows > 0) 
{
	echo '<table>';
	echo '  <tr><th><img src="result-finish.png"></th><th colspan="2">Route</th><th align="left">Venue</th><th align="left">Location</th><th align="left">Session</th></tr>';
	$hasRoute = [];
	$lastRating = "";
	$resultCount = [];
	$percentageCount = [];
	$statsIndex = 0;
	$routeCount = 0;
  while ($row = $result->fetch_assoc()) 
  {
		$idRoute = $row["IdRoute"];
		if (!array_key_exists($idRoute, $hasRoute))
		{
			$resultIndex = $row["Result"];
			if (!isset($row["Result"]))
				 $resultIndex = -1;
			
			$resultBgColor = "transparent";
			
			if ($resultIndex == -1)
				$resultPic = "result-not-climbed.png";
			else if ($resultIndex == 0)
				$resultPic = "result-fail.png";
			else if ($resultIndex == 1)
				$resultPic = "result-faults.png";
			else if ($resultIndex == 2)
				$resultPic = "result-success.png";


			if ($row["Rating"] != $lastRating)
			{
				$pieChartScript .= WriteStats($statsIndex++, $routeCount, $resultCount, $percentageCount, $lastRating);
				$routeCount = 0;
				$resultCount[-1] = 0;
				$resultCount[0] = 0;
				$resultCount[1] = 0;
				$resultCount[2] = 0;
				for ($percentage = 0; $percentage <= 95; $percentage += 5)
					$percentageCount[$percentage] = 0;
				
				echo '<tr style="background-color:rgba(0, 0, 0, 0.1)">';
				echo '<td colspan="100">';
				echo 'Rating: ' . $row["Rating"];
				echo '</td>';
				echo '</tr>';
			}

			echo '<tr>';
			if ($resultIndex == 0 && $row["Percentage"] !== NULL)
				echo '<td style="color: red">' . $row[Percentage] . '%</td>';
			else
				echo '<td align="center" style="background-color: $bgColor"><img src="' . $resultPic . '"></td>';
			echo '<td width="16" bgcolor="' . $row[Color] . ' "></td>';
			echo '<td><a href="userroutestats.php?IdUser=' . $IdUser . '&IdRoute=' . $idRoute . '">';
			if ($row["Removed"] == 1)
				echo '<del>';
			echo $row["Name"] . '</a></td>';
			echo '<td>' . $row["Location"] . '</td><td>' . $row["Sublocation"] . '</td>';
			echo '<td>';
			if (isset($row["SesDate"]))
				echo '<a href="usersession.php?IdSession=' . $row["IdSession"] . '">' . date("D d-M-Y", $row["SesDate"]) . '</a>';
			echo '</td>';
			echo '</tr>';
			
			
			$resultCount[$resultIndex] += 1;
			if ($resultIndex == 0)
				$percentageCount[$row["Percentage"]] += 1;
			
			$lastRating = $row["Rating"];
			$hasRoute[$idRoute] = true;
			$routeCount++;
		}
  }
	
	$pieChartScript .= WriteStats($statsIndex++, $routeCount, $resultCount, $percentageCount, $lastRating);
	
  echo "</table>";

	echo '<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>';
	echo '<script type="text/javascript">' . PHP_EOL;
	echo 'google.charts.load(\'current\', {\'packages\':[\'corechart\']});' . PHP_EOL;
	echo $pieChartScript . PHP_EOL;
	echo '</script>' . PHP_EOL;
} 
else 
{
    echo "No results";
}

$conn->close();

function WriteStats($index, $routeCount, $resultCount, $percentageCount, $rating)
{
	$script = '';
	
	if (sizeof($resultCount) > 0)
	{
		echo '<tr style="height: 300px;">';
		echo '<td colspan="100" id="piechart' . $index . '" >(Statistics)</td>';
		echo '</tr>';

		$script .= '
google.charts.setOnLoadCallback(drawChart' . $index . ');' . PHP_EOL . '
function drawChart' . $index . '()' . PHP_EOL . '
{
	var data = google.visualization.arrayToDataTable(
	[
		[\'Resultaat\', \'Aantal\'],
'; 
		
		for ($result = -1; $result <= 2; $result++) 
		{
			if ($result == 0)
			{
				for ($percentage = 0; $percentage <= 95; $percentage += 5)
					$script .= '[\'' . $percentage . '%\', ' . $percentageCount[$percentage] . '],';
			}
			else
			{
				switch ($result)
				{
					case -1: $res = "Not climbed"; break;
					case 1: $res = "Completed, with fall or block"; break;
					case 2: $res = "Completed in one go"; break;
				}
			$script .= '[\'' . $res . '\', ' . $resultCount[$result] . '],';
			}
		}
		$script .= ']);';

$script .= '	
	var options =
	{
	title: \'' . $rating . ' (' . $routeCount . ' route' . ($routeCount != 1 ? 's' : '') . ')\',
	backgroundColor: \'transparent\',
	is3D: true,
	colors: [\'lightgray\', ';
	$blue = 0;
	for ($percentage = 0; $percentage <= 95; $percentage += 5)
	{
			$script .= '\'#' . sprintf("%02X", 255 - $blue) . '00' . sprintf("%02X", $blue) . '\', ';
			$blue += 10;
	}
$script .= '\'blue\', \'green\'],
	titleTextStyle: { fontSize: 18 }
	};

	var chart = new google.visualization.PieChart(document.getElementById(\'piechart' . $index . '\'));

	chart.draw(data, options);
}
';
	}
	
	return $script;
}


?>
<script>
function setHistory(checkbox)
{
	if (checkbox.checked)
		window.location = 'progress.php?History=True';
	else
		window.location= 'progress.php';
}
</script>
</body>
</html> 