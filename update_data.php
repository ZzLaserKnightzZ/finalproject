<?php
	$newdata = $_GET['q'];
	$id = $_GET['id'];
	$table = $_GET['table'];
	//echo $newdata."<br>".$id."<br>".$table;
	$con = new PDO("mysql:host=localhost;dbname=project;charset=utf8","root","");
	$sql = "UPDATE storage SET $table = '$newdata' WHERE ID = $id";
	if($con->exec($sql)== 0){
		echo "update not complete";
	}else{
		$sql = "SELECT $table FROM `storage` WHERE ID = $id";
		$result = $con->query($sql);
		$data = $result->fetch(PDO::FETCH_ASSOC);
		echo "$data[$table]";
	}
?>