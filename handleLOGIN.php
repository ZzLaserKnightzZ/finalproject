<?php
// Start the session
session_start();
session_unset();
$user = $_POST["USERNAME"]."";
$pass = $_POST["PASSWORD"]."";
if($user !='a' && $pass!= 'a' && $_SERVER['SERVER_NAME'] == 'localhost'){
	header("Location:login.php");
	die();
}
echo $user.$pass;
try {
    $con = new PDO("mysql:host=localhost;dbname=project;charset=utf8", "root", "");
    // set the PDO error mode to exception
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //echo "Connected successfully";
		$sqlcom = "SELECT * FROM user WHERE USERNAME = '".$user."' AND PWD = SHA1('$pass')";
		$result = $con->query($sqlcom);
		echo session_id();
		if($result->rowCount() == 1){
			$data = $result->fetch(PDO::FETCH_ASSOC);
			//set user
			if($data['id_session'] != session_id()){
				
			
				$_SESSION["USERNAME"] = $data['Name'];
				$_SESSION["FALCUTY"] = $data['Fac_id'];
				$_SESSION["SPAECIAL"] = $data['SPAECIAL'];
				$_SESSION["status"] = $data['status'];
				$_SESSION["id"] = session_id();
				
				$sql = "update user set status = 'online' , id_session = '".$_SESSION["id"]."'  where USERNAME = '".$data['USERNAME']."'" ;
				echo $sql."<br>";
				echo $con->exec($sql);
				//echo "ok";
				header("Location:adminpage.php");
				die();
				// echo "ad";
			}else {
				header("Location:adminpage.php");
				die();
			}
			
		}else{
			//fail header(login); + alert
			//echo "no";
			$_SESSION["ALERT"] = "FAIL";
			// echo "f";
			header("Location:login.php");
			die();
		}
		


	}
catch(PDOException $e)
    {
    echo "Connection failed: " . $e->getMessage();
    }
	



?>