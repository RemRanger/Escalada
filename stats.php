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
$IdUser = $_SESSION["IdUser"];

include_once 'dbconnect.php';
?>

<div id="piechart" style="width: 500px; height: 400px;">(Statistics)</div>


<?php
$sql = "
select Rou.Rating, Att.Result, count(*)
from Route Rou 
join Attempt Att on Att.IdRoute = Rou.Id
where Att.IdUser = 1 and Rou.Type = 'Toprope' and Rating = '5c'
group by Rou.Rating, Att.Result
order by Rou.Rating, Att.Result";
$result = $conn->query($sql);
echo '<br>';
echo '<table>';
echo '<tr><th>Rating</th><th>Result</th><th>Count</th></tr>';
while ($row = $result->fetch_assoc()) 
{
	echo '<td>' . $row["Rating"] . '</td>';
	echo '<td>' . $row["Result"] . '</td>';
	echo '<td>' . $row["Count"] . '</td>';
	echo '</tr>';
}
echo '</table>';
echo '<br>';


echo '<br>';
echo '<button style="width:100px" name="btn-back" onclick="window.history.back();">Back</button>';

?>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
	google.charts.load('current', {'packages':['corechart']});
	google.charts.setOnLoadCallback(drawChart);

	function drawChart() 
	{
		var data = google.visualization.arrayToDataTable(
		[
			['Resultaat', 'Aantal'], 
<?php 
			$sql = "
			select Rou.Rating, Att.Result, count(*) Count
			from Route Rou 
			join Attempt Att on Att.IdRoute = Rou.Id
			where Att.IdUser = $IdUser and Rou.Type = 'Toprope' and Rating = '5c'
			group by Rou.Rating, Att.Result
			order by Rou.Rating, Att.Result";
			$result = $conn->query($sql);
			while ($row = $result->fetch_assoc()) 
			{
				switch ($row["Result"])
				{
					 case 0: $res = "Partly"; break;
					 case 1: $res = "Completed, with fall or block"; break;
					 case 2: $res = "Completed in one go"; break;
				}
				echo '[\'' . $res . '\', ' . $row["Count"] . '],';
			}
?>
			]);

		var options = 
		{
			title: '5c',
			backgroundColor: 'transparent',
			is3D: true,
			colors: ['red', 'blue', 'green'],
			titleTextStyle: { fontSize: 18 }
		};

		var chart = new google.visualization.PieChart(document.getElementById('piechart'));

		chart.draw(data, options);
	}
</script>

<?php
$conn->close();
?>


</body>
</html> 