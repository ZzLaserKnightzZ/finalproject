<?php
	session_start();
	if($_SESSION['STATUS']=='ALL'){
		
	
	$user_id = $_GET['id'];
	$con = new PDO("mysql:host=localhost;dbname=project;charset=utf8","root","");
	$sql = "DELETE FROM `user` WHERE USER_ID=$user_id";
	$con->exec($sql);
	header('location:delete_user.php');
	}
?>