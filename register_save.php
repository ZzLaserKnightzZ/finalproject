<?php
	session_start();
	$login 		= 	$_POST['login'];
	$pwd 		= 	$_POST['pwd'];
	$name 		= 	$_POST['name'];
	$faculty 	= 	$_POST['faculty'];
	if($login != '' && $pwd != '' && $name != '' && $faculty != '' ){
		$con = new PDO("mysql:host=localhost;dbname=project;charset=utf8","root","");
		$sql = "INSERT INTO user (USERNAME  , PWD     		,  NAME    , Fac_id 	 , SPAECIAL) 
						  VALUES ('$login'	, sha1('$pwd') 	, '$name'  , '$faculty'  , 'M')";
		//$con->exec($sql);
		if($con->exec($sql)==1){
			$_SESSION['success'] = "เพิ่มบัญชีผู้ใช้เรียบร้อยแล้ว";
		}else{
			$_SESSION['error'] = "ชื่อบัญชีซ้ำหรือฐานข้อมูลมีปัญหา";
		}
	}else {
		$_SESSION['error'] = "กรอกข้อมูลไม่ครบถ้วน กรุณากรอกให้ครบถ้วน";
	}
	$con = null ;
	header("Location: register.php");
?>