<table>

<?php
//$colors = array("AliceBlue","AntiqueWhite","Aqua","Aquamarine","Azure","Beige","Bisque","Black","BlanchedAlmond","Blue","BlueViolet","Brown","BurlyWood","CadetBlue","Chartreuse","Chocolate","Coral","CornflowerBlue","Cornsilk","Crimson","Cyan","DarkBlue","DarkCyan","DarkGoldenRod","DarkGray","DarkGrey","DarkGreen","DarkKhaki","DarkMagenta","DarkOliveGreen","Darkorange","DarkOrchid","DarkRed","DarkSalmon","DarkSeaGreen","DarkSlateBlue","DarkSlateGray","DarkSlateGrey","DarkTurquoise","DarkViolet","DeepPink","DeepSkyBlue","DimGray","DimGrey","DodgerBlue","FireBrick","FloralWhite","ForestGreen","Fuchsia","Gainsboro","GhostWhite","Gold","GoldenRod","Gray","Grey","Green","GreenYellow","HoneyDew","HotPink","IndianRed","Indigo","Ivory","Khaki","Lavender","LavenderBlush","LawnGreen","LemonChiffon","LightBlue","LightCoral","LightCyan","LightGoldenRodYellow","LightGray","LightGrey","LightGreen","LightPink","LightSalmon","LightSeaGreen","LightSkyBlue","LightSlateGray","LightSlateGrey","LightSteelBlue","LightYellow","Lime","LimeGreen","Linen","Magenta","Maroon","MediumAquaMarine","MediumBlue","MediumOrchid","MediumPurple","MediumSeaGreen","MediumSlateBlue","MediumSpringGreen","MediumTurquoise","MediumVioletRed","MidnightBlue","MintCream","MistyRose","Moccasin","NavajoWhite","Navy","OldLace","Olive","OliveDrab","Orange","OrangeRed","Orchid","PaleGoldenRod","PaleGreen","PaleTurquoise","PaleVioletRed","PapayaWhip","PeachPuff","Peru","Pink","Plum","PowderBlue","Purple","Red","RosyBrown","RoyalBlue","SaddleBrown","Salmon","SandyBrown","SeaGreen","SeaShell","Sienna","Silver","SkyBlue","SlateBlue","SlateGray","SlateGrey","Snow","SpringGreen","SteelBlue","Tan","Teal","Thistle","Tomato","Turquoise","Violet","Wheat","White","WhiteSmoke","Yellow","YellowGreen");
$colorArrays = array
(
	array("Pink","LightPink","HotPink","DeepPink","PaleVioletRed","MediumVioletRed"),
	array("LightSalmon","Salmon","DarkSalmon","LightCoral","IndianRed","Crimson","FireBrick","DarkRed","Red"),
	array("OrangeRed","Tomato","Coral","DarkOrange","Orange"),
	array("Yellow","LightYellow","LemonChiffon","LightGoldenrodYellow","PapayaWhip","Moccasin","PeachPuff","PaleGoldenrod","Khaki","DarkKhaki","Gold"),
	array("Cornsilk","BlanchedAlmond","Bisque","NavajoWhite","Wheat","BurlyWood","Tan","RosyBrown","SandyBrown","Goldenrod","DarkGoldenrod","Peru","Chocolate","SaddleBrown","Sienna","Brown","Maroon"),
	array("DarkOliveGreen","Olive","OliveDrab","YellowGreen","LimeGreen","Lime","LawnGreen","Chartreuse","GreenYellow","SpringGreen","MediumSpringGreen","LightGreen","PaleGreen","DarkSeaGreen","MediumAquamarine","MediumSeaGreen","SeaGreen","ForestGreen","Green","DarkGreen"),
	array("Aqua","Cyan","LightCyan","PaleTurquoise","Aquamarine","Turquoise","MediumTurquoise","DarkTurquoise","LightSeaGreen","CadetBlue","DarkCyan","Teal"),
	array("LightSteelBlue","PowderBlue","LightBlue","SkyBlue","LightSkyBlue","DeepSkyBlue","DodgerBlue","CornflowerBlue","SteelBlue","RoyalBlue","Blue","MediumBlue","DarkBlue","Navy","MidnightBlue"),
	array("Lavender","Thistle","Plum","Violet","Orchid","Fuchsia","Magenta","MediumOrchid","MediumPurple","BlueViolet","DarkViolet","DarkOrchid","DarkMagenta","Purple","Indigo","DarkSlateBlue","SlateBlue","MediumSlateBlue"),
	array("White","Snow","Honeydew","MintCream","Azure","AliceBlue","GhostWhite","WhiteSmoke","Seashell","Beige","OldLace","FloralWhite","Ivory","AntiqueWhite","Linen","LavenderBlush","MistyRose"),
	array("Gainsboro","LightGray","Silver","DarkGray","Gray","DimGray","LightSlateGray","SlateGray","DarkSlateGray","Black")
);

$colCount = 14;
foreach ($colorArrays as $colorArray)
{
	echo "<tr>";

	foreach ($colorArray as $coloroption)
	{
		echo '<td style="width: 20 px; height: 25px; background-color: ';
		echo $coloroption;
		echo'">';
		echo '<input type="radio" name="Color" value="';
		echo $coloroption;
		echo '"';
		if (strcasecmp($coloroption, $Color) == 0)
			echo ' checked';
		echo '/>';
		echo '</td>';
	}

	echo "</tr>";
}
?>

</table>
