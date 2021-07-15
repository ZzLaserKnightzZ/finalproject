<?php
session_start();
//all
//admin
//sqlcom = select * from inprogress where error = true 
//user
//sqlcom = select * from inprogress where error = true and falcuty = $['']
//open db exac
//echo '{res:$error_data,success:data}'; //alert
if(!isset($_SESSION["USERNAME"])) die();

$sqlcom = "";
$FALCUTY = $_SESSION["FALCUTY"];

if($_SESSION["SPAECIAL"] == "ALL"){
	$sqlcom_error = "SELECT * FROM inprogress WHERE ERROR != ? ";
	$sqlcom_done = "SELECT * FROM inprogress WHERE DONE = ? ";
}else{
	
	
	$sqlcom_error = "SELECT * FROM `inprogress` join faculty on inprogress.STO_NAME = faculty.fac_name  WHERE ERROR != ? AND faculty.fac_id = ".$FALCUTY;  //".$FALCUTY."  SELECT * FROM `inprogress` join faculty on inprogress.STO_NAME = faculty.fac_name  WHEREWHERE ERROR != ? AND faculty.fac_id = ".$FALCUTY."  
	$sqlcom_done = "SELECT * FROM `inprogress` join faculty on inprogress.STO_NAME = faculty.fac_name  WHERE DONE = ? AND faculty.fac_id = ".$FALCUTY; 
	
	//$sqlcom = "SELECT * FROM inprogress WHERE DONE = Y OR ERROR != ' ' AND FALCUTY = '".$FALCUTY."'";
}

$servername = "localhost";
$username = "root";
$password = "";


try {
	
    $con = new PDO("mysql:host=$servername;dbname=project", $username, $password);
    // set the PDO error mode to exception
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
	//$sqlcom = "SELECT * FROM ".$_SESSION["FALCUTY"];
	$result = $con->prepare($sqlcom_error);
	$result->execute([""]);
	$json_error ="";
	$json_success ="";
	$cnt = 0;
	if($result->rowCount() > 0){
		//$json = '{"REPORT":';
		while($rs = $result->fetch()){
			$json_error .= '"ERROR ITEM ID>'.$rs['PRODUCT_ID'].'> NAME>'.$rs['NAME'].'> ERROR->'.$rs['ERROR'].' LOCATION->'.$rs['CURRENT_LOC'].'",';
			$cnt++;
		}
		
	}
	
	$result = $con->prepare($sqlcom_done);
	$result->execute(["Y"]);
	if($result->rowCount() > 0){
		while($rs = $result->fetch()){
			$json_success .= '"SUCCESS ITEM ID>'.$rs['PRODUCT_ID'].'> NAME>'.$rs['NAME'].'> LOCATION->'.$rs['CURRENT_LOC'].'",';
			$cnt++;
		}
		//if($json != "")
	
			 
	}
	
	if(strlen($json_error) >= 1) $json_error = substr($json_error,0,strlen($json_error)-1);
	if(strlen($json_success) >= 1) $json_success = substr($json_success,0,strlen($json_success)-1); 
	
	if($json_error != "" && $json_success != ""){
		
		echo '{"REPORT":{"ERROR":['.$json_error.'],"SUCCESS":['.$json_success.']}}'; //{"REPORT":{"error":["","",""]},{"success":["","",""]}}
		
	}else if($json_error != "" && $json_success == ""){
		
		echo '{"REPORT":{"ERROR":['.$json_error.'],"SUCCESS":[]}}'; //{"REPORT":{"error":["","",""]},{"success":["","",""]}}
		
	}else if($json_error == "" && $json_success != ""){
		
		echo '{"REPORT":{"ERROR":[],"SUCCESS":['.$json_success.']}}'; //{"REPORT":{"error":["","",""]},{"success":["","",""]}}
		
	}else{
		
		echo '{"REPORT":{"ERROR":[],"SUCCESS":[]}}'; //{"REPORT":{"error":["","",""]},{"success":["","",""]}}
		
	}
	

	
}catch(PDOException $e)
    {
    echo "Connection failed: " . $e->getMessage();
    }

?>
