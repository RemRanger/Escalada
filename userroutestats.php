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
$IdRoute = $_GET["IdRoute"];
$IdUser = $_GET["IdUser"];

include_once 'dbconnect.php';

$sql = "
select Name, Color, Rating, Removed, Sublocation from Route where Id = $IdRoute
";
$result = $conn->query($sql);
if ($row = $result->fetch_assoc()) 
{	
	echo '<br>';
	echo '<table>';
	echo '<tr><th colspan="2">Route</th><th>Rating</th><th>Location</th></tr>';
	echo '<tr><td width="16" bgcolor="' . $row[Color] . ' "></td><td>';
	if ($row["Removed"] == 1)
		echo '<del>';
	echo $row["Name"];
	if (!empty($_SESSION["IdUser"]))
		echo '&nbsp;&nbsp;<button style="width:40px" onclick="window.location=\'editroute.php?&IdRoute=' . $IdRoute . '\'">Edit</button>';
	echo '</td>';
	echo '<td>' . $row["Rating"] . '</td>';
	echo '<td>' . $row["Sublocation"] . '</td>';
	echo '</tr>';
	echo '</table>';
	echo '<br>';
}
else
	echo '<table class="noborder">';

$sql = "
select Att.Result, Att.Percentage, Att.Comment, UNIX_TIMESTAMP(Ses.Date) SesDate, Ses.Id IdSession
from Attempt Att
join Session Ses on Ses.Id = Att.IdSession
where Att.IdUser = $IdUser and Att.IdRoute = $IdRoute
order by Ses.Date desc, Result desc, Percentage desc
";
$result = $conn->query($sql);
if ($result->num_rows > 0) 
{
	echo '<table>';
	echo '  <tr><th width="16"><img src="result-finish.png"></th><th>Session</th><th>Comment</th>';
	echo '</tr>';
  while ($row = $result->fetch_assoc()) 
  {
		if ($row["Result"] == 0)
			$resultPic = "result-fail.png";
		else if ($row["Result"] == 1)
			$resultPic = "result-faults.png";
		else  if ($row["Result"] == 2)
			$resultPic = "result-success.png";
		
		if ($row["Result"] == 0 && $row["Percentage"] !== NULL)
			echo '<td style="color: red">' . $row[Percentage] . '%</td>';
		else
			echo '<td align="center"><img src="' . $resultPic . '"></td>';
		echo '</td>';
		echo '<td nowrap><a href="usersession.php?IdUser=' . $IdUser . '&IdSession=' . $row["IdSession"] . '"</a>' . date("D d-M-Y", $row["SesDate"]) . '</td>';
		echo '<td>' . $row["Comment"] . '</td>';
		echo '</tr>';
  }
  echo "</table>";
} 
else 
{
    echo "No results";
}

echo '<br>';
echo '<button style="width:100px" name="btn-back" onclick="window.history.back();">Back</button>';

$conn->close();

?>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {

        var data = google.visualization.arrayToDataTable([
          ['Task', 'Hours per Day'],
          ['Work',     11],
          ['Eat',      2],
          ['Commute',  2],
          ['Watch TV', 2],
          ['Sleep',    7]
        ]);

        var options = {
          title: 'My Daily Activities'
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart'));

        chart.draw(data, options);
      }
</script>

</body>
</html> 