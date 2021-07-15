<?php
	session_start();
	$login 		= 	$_POST['login'];
	$name 		= 	$_POST['name'];
	$faculty 	= 	$_POST['faculty'];
	$id 		= 	$_POST['id'];
	if($login != '' && $name != '' && $faculty != '' ){
		$con = new PDO("mysql:host=localhost;dbname=project;charset=utf8","root","");
		$sql = "update user set USERNAME = '$login' , Name = '$name' , fac_id = $faculty where USER_ID = $id";
		//$con->exec($sql);
		if($con->exec($sql)==1){
			$_SESSION['success'] = "update complete  ".$faculty ;
		}else{
			$_SESSION['error'] = "update not complete";
		}
	}else {
		$_SESSION['error'] = "กรอกข้อมูลไม่ครบถ้วน กรุณากรอกให้ครบถ้วน";
	}
	$con = null ;
	header("Location: edit_user.php?id=$id&fac=$faculty");
?>