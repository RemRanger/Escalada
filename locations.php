<?php
session_start();
include_once 'dbconnect.php';
?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" type="text/css" href="style.css">
<style>
	#map 
	{
		width: 500px;
		height: 500px;
	}
</style>
</head>
<body>

<center>

<?php include_once 'mainmenu.php';?>
<h1>Locations</h1>

<?php

$sql = "SELECT Id, Name, WebsiteUrl, Latitude, Longitude, (SELECT COUNT(*) FROM LocationRouteType WHERE IdLocation = Location.Id AND RouteType != 'Boulder') RopeTypes 
		FROM Location";
$result = $conn->query($sql);

if ($result->num_rows > 0) 
{
	echo '<table>';
	echo '  <tr><th colspan="2">Location</th><th>Website</th>';

	$Ids = [];
	$Latitude = [];
	$Longitude = [];
	$RopeTypes = [];
	$Name = [];
	$Url = [];

	$SumLat = 0;
	$SumLong = 0;
	$Count = 0;
	while ($row = $result->fetch_assoc()) 
	{
		$Id = $row["Id"];
		$Latitude[$Id] = $row["Latitude"];
		$Longitude[$Id] = $row["Longitude"];
		$Name[$Id] = $row["Name"];
		$Url[$Id] = $row["WebsiteUrl"];
		$RopeTypes[$Id] = $row["RopeTypes"];
		echo '  <tr>';
		echo '    <td>' . $row["Name"] . '</td>';
		echo '<td><a href="routes.php?IdLocation=' . $row["Id"] . '">Routes</a></td>';
		if (!empty($Url[$Id]))
		{
			if ($ret = parse_url($Url[$Id]) )
			{
				if ( !isset($ret["scheme"]))
					$Url[$Id] = 'http://' . $Url[$Id];
			}
			echo '    <td><a href="' . $Url[$Id] . '">' . $row["WebsiteUrl"] . '</a></td>';
		}
		echo '  </tr>';
		
		array_push($Ids, $Id);
		
		if (!isset($minLat) || $Latitude[$Id] < $minLat)
			$minLat = $Latitude[$Id];
		if (!isset($maxLat) || $Latitude[$Id] > $maxLat)
			$maxLat = $Latitude[$Id];
		if (!isset($minLong) || $Longitude[$Id] < $minLong)
			$minLong = $Longitude[$Id];
		if (!isset($maxLong) || $Longitude[$Id] > $maxLong)
			$maxLong = $Longitude[$Id];
		
		$SumLat += $Latitude[$Id];
		$SumLong += $Longitude[$Id];
		$Count++;
	}
	echo '</table>';
	
	$midLat = $SumLat/$Count;
	$midLong = $SumLong/$Count;
	
	echo '<br>';
	echo '<div id="map"></div>';
	echo '<script>';
	echo '	function initMap() ';
	echo '	{';
	echo '		var a = {lat: '. $midLat . ', lng: ' . $midLong . '};';
	echo '		var map = new google.maps.Map(document.getElementById("map"), {zoom: 0, center: a});';
	foreach ($Ids as $Id)
	{
		echo '		var location_' . $Id . ' = {lat: ' . $Latitude[$Id] . ', lng: ' . $Longitude[$Id] . '};';
      	//echo '      var icon = {path: google.maps.SymbolPath.CIRCLE, strokeColor: "' . ($RopeTypes[$Id] > 0 ? 'red' : 'blue') . '", scale: 5};';
       	echo '      var icon = "https://maps.google.com/mapfiles/kml/paddle/' . ($RopeTypes[$Id] > 0 ? 'red' : 'blu') . '-stars-lv.png";';
		echo '		var marker_' . $Id . ' = new google.maps.Marker({position: location_' . $Id . ', map: map, icon: ' . icon . ', title: "' . $Name[$Id] . '", url: "' . $Url[$Id] . '"});';
		echo '    google.maps.event.addListener(marker_' . $Id . ', "click", function() {window.location.href = this.url;});';	 
		echo '    map.fitBounds(new google.maps.LatLngBounds(new google.maps.LatLng(' . $minLat . ', ' . $minLong . '), new google.maps.LatLng('. $maxLat . ', ' . $maxLong . ')));';	 
	}
	echo '	}';
	echo '</script>';

		
	$conn->close();
} 
else 
{
    echo "0 results";
}

?>

<script async defer
	src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD11s3QW5R_71Ywy8UmdJ6LhZlaVBPkawI&callback=initMap">
</script>


</body>
</html> 