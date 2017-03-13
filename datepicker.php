<?php 
// echo "Year=" . $Year; 
// echo ", Month=" . $Month; 
// echo ", Day=" . $Day; 
?>

<select id="Year" name="Year" required>
	<?php
	for ($year = date('Y'); $year >= 1990 ; $year--)
	{
		echo '<option';
		if (!empty($Year))
		{
			if ($year == $Year)
				echo ' selected';		
		}
		else
		{ 
			if ($year == date('Y'))
				echo ' selected';
		}
		echo '>' . $year . '</option>';
	}
	?>
</select required>
<select id="Month" name="Month" required onchange="UpdateDays()">
	<?php
	for ($month = 1; $month <= 12 ; $month++)
	{
		$dateObj   = DateTime::createFromFormat('!m', $month);
		$monthName = $dateObj->format('F');
		echo '<option';
		if (!empty($Month))
		{
			if ($month == $Month)
				echo ' selected';		
		}
		else
		{ 
			if ($month == date('m'))
			  echo ' selected';		
		}
		echo ' value="' . $month . '">' . $monthName . '</option>';
	}
	?>
</select>
<select id="Day" name="Day">
	<option selected disabled>--Day--</option>
</select>

<script>
UpdateDays();

function UpdateDays()
{
	var year = document.getElementById("Year").value;
	var month = document.getElementById("Month").value;
<?php
	if (empty($Day))
		echo 'var selectedDay = new Date().getDate();';
	else
		echo 'var selectedDay = ' . $Day . ';';
?>
	
	var options = "<option selected disabled>--Day--</option>";
	for (day = 1; day <= daysInMonth(year, month); day++)
	{
		options += "<option";
		if (day == selectedDay)
			options += " selected";
		options += ">" + day + "</option>"
	}
	document.getElementById("Day").innerHTML = options;
}

function daysInMonth(year, month)
{
    return new Date(year, month, 0).getDate();
}
</script>