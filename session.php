<?php
	session_start();
	
	$con = new PDO("mysql:host=localhost;dbname=project;charset=utf8", "root", "");
	$sqlcom = "SELECT * FROM user WHERE Name = '".$_SESSION['USERNAME']."'";
	//echo $sqlcom;
	$result = $con->query($sqlcom);
	if($result->rowCount() == 1){
		$data = $result->fetch(PDO::FETCH_ASSOC);
		if($data['id_session'] != session_id()){
			//echo 'clear!!';
			session_unset();
			die();
		}else {
			//echo 'not clear!!';
		}
	}
	echo '{"asdasd":"sdsdasd"}';
?>