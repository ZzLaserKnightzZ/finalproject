<?php
//admin
//$storage_name = $_GET["STO_NAME"];
$servername = "localhost";
$username = "root";
$password = "";

try {
    $con = new PDO("mysql:host=$servername;dbname=project", $username, $password);
    // set the PDO error mode to exception
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //echo "Connected successfully";
		$result = $con->prepare("SELECT * FROM robot");
		$result->execute();
		
		echo '{"ROBOT":[';
		while($rs = $result->fetch()){
			echo '{"NAME":"'.$rs['bot_name'].'","LOCATION":"'.$rs['LOCATION'].'","STATUS":"'.$rs['STATUS'].'","JOBID":"'.$rs['JOBID'].'"},'; //STATUS
			//echo '{"NAME":"'.$rs['bot_name'].'","STATUS":"'.$rs['STATUS'].'","JOBID":"'.$rs['JOBID'].'","PULSE":"'.$rs['PULSE'].'"},';
		}
		echo "{}]}";
		
		//echo '[{"result":"hello", "count":42},{}]';
	}
catch(PDOException $e)
    {
    echo "Connection failed: " . $e->getMessage();
    }
	
?>
