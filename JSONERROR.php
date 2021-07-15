<?php
session_start();
//all
//admin
//sqlcom = select * from inprogress where error = true 
//user
//sqlcom = select * from inprogress where error = true and falcuty = $['']
//open db exac

//echo '{res:$error_data,success:data}'; //alert

$sqlcom = "";

if(isset($_SESSION["USERNAME"])){
	$sqlcom = 'SELECT * FROM inprogress WHERE ERROR != '' AND FALCUTY = '.$_SESSION["FALCUTY"].'';
}else if(isset($_SESSION["USERNAME"]) && $_SESSION["FALCUTY"] == "ALL"){
	$sqlcom = 'SELECT * FROM inprogress WHERE ERROR != ''';
}

$servername = "localhost";
$username = "root";
$password = "";


try {
	
    $con = new PDO("mysql:host=$servername;dbname=project", $username, $password);
    // set the PDO error mode to exception
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
	//$sqlcom = "SELECT * FROM ".$_SESSION["FALCUTY"];
	$result = $con->prepare($sqlcom);
	$result->execute();
	while($rs = $result->fetch()){
		echo '{res:"ERROR ITEM ID:'.$rs['ID'].' NAME:'.$rs['NAME'].' ERROR->'.$rs['ERROR'].' LOCATION->'.$rs['CURRENT_LOC'].'"}';
	}
}catch(PDOException $e)
    {
    echo "Connection failed: " . $e->getMessage();
    }

?>
