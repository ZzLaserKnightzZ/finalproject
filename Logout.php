<?php
if(!isset($_SESSION["USERNAME"])){
	header("Location:login.php"); 
}
session_start();
$con = new PDO("mysql:host=localhost;dbname=project;charset=utf8", "root", "");
$sql = "update user set status = 'offline' , id_session = '' where Name = '".$_SESSION["USERNAME"]."'" ;
echo $sql;	
echo $con->exec($sql);

session_unset();


header("Location:login.php"); 
?>