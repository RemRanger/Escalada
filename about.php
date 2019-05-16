<?php
session_start();
include_once 'dbconnect.php';
?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" type="text/css" href="style.css">
<title>&Xi;SC&Lambda;L&Lambda;D&Lambda;</title>
<link rel="icon" type="image/x-icon" href="favicon.ico">
</head>
<body>

<div align="center" class="card">
  <?php include_once 'mainmenu.php';?>
  <div class="card-body">
    <div class="container-fluid">
      <div class="text-center"><h1 style="color:darkslateblue">&Xi;SC&Lambda;L&Lambda;D&Lambda;&nbsp;&nbsp;&nbsp;</h1></div>
      <div class="text-center" style="font-style:italic">A climber's log</div>
      <div class="text-center"><br />Developed on PHP</div>
      <div class="text-center">
        <img src="http://icons.iconarchive.com/icons/papirus-team/papirus-apps/128/github-bartzaalberg-php-tester-icon.png" class="img-responsive center-block" style="max-height:100px;padding-top:10px;padding-bottom:10px" />
        <div class="text-center">by</div>
        <div class="text-center"><h3>RΞM</h3></div>
        <div class="text-center"><a href="http://www.remranger.com">www.remranger.com</a></div>
      </div>
    </div>
  </div>
</div>

</body>
</html> 