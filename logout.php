<?php
session_start();

if(!isset($_SESSION['IdUser']))
{
 header("Location: index.php");
}

if(isset($_GET['logout']))
{
 session_destroy();
 unset($_SESSION['IdUser']);
 header("Location: index.php");
}
?>