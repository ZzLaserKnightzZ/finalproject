<?php
session_start();
//all
//admin
//sqlcom = select * from inprogress where error = true 
//user
//sqlcom = select * from inprogress where error = true and falcuty = $['']
//open db exac
//echo '{res:$error_data,success:data}'; //alert

$servername = "localhost";
$username = "root";
$password = "";

//$REQ = $_GET[''];
$STO = $_GET['storage_name']; /// get
$DATA = $_GET['val'];
$NUM = 0;
$STATE = 0;

//mistake--------------------------------------------------------------
$mistake = "";
//var_dump($index_arr);
if($STO == "science"){
	$var_0x20 = substr($DATA,0,6);
	$var_0x21 = substr($DATA,6,strlen($DATA));
	$mistake = $var_0x20.$var_0x21;
	//echo $mistake;
}else if($STO == "electronics"){
	$var_0x23 = $DATA;
	$mistake = $var_0x23;
	//echo $var_0x23;
}else if($STO == "mechanic"){ //currect
	$sub_43 = substr($DATA,2,2);
	$reverse = strrev($sub_43);
	str_replace($DATA , $reverse , $sub_43);
	$var_0x22 = strrev($DATA );
	$mistake = $var_0x22;
	
	$rev_floor = "";
	for($index = 0 ; $index < strlen($mistake) ; $index+=2 ){  //revrse floor
		$floor_rev = strrev(substr($mistake , $index , 2));
		$rev_floor .= $floor_rev;
	}
	
	$mistake = $rev_floor;
	//echo $mistake;
}
//echo $mistake;


///echo $rev_floor;
$index_arr =  str_split($mistake);///get   array(0,0,1,1,1,0);
//mistake----------------------------------------------------------------

try {
	
    $con = new PDO("mysql:host=$servername;dbname=project", $username, $password);
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	/*
	DROP TABLE watchdogTABLE;
	CREATE TABLE watchdogTABLE AS SELECT CRF FROM electronics;
	next update
	UPDATE `electronics` SET `ON_STORAGE`= 1 WHERE `CRF` = (SELECT CRF FROM watchdogTABLE LIMIT 3,1);
	*/
		
		$ranTableName = "watchdogTABLE".rand(1000,9000);
		$sqlcom = "CREATE TABLE ".$ranTableName." AS SELECT storage_id FROM storage WHERE fac_id = (SELECT fac_id FROM faculty WHERE fac_name = '".$STO."')"; //` ->bind_param  ROW_NUMBER()
		$result = $con->prepare($sqlcom);	
		$result->execute();
		
		$sqlcom = "UPDATE `storage` SET `ON_STORAGE`= ? WHERE `storage_id` = (SELECT storage_id FROM ".$ranTableName." LIMIT ?,1) AND fac_id = (SELECT fac_id FROM faculty  WHERE fac_name = '".$STO."')"; //` ->bind_param  ROW_NUMBER()
		$result = $con->prepare($sqlcom);
		$result->bindParam(1, $STATE, PDO::PARAM_INT);
		$result->bindParam(2, $NUM, PDO::PARAM_INT);
		
		for($i = 0; $i < count($index_arr) ; $i++){
			$STATE = $index_arr[$i];
			$NUM = $i;
			//echo $index_arr[$i];
			$result->execute();
		}
		
		$sqlcom = "DROP TABLE ".$ranTableName; //` ->bind_param  ROW_NUMBER()
		$result = $con->prepare($sqlcom);	
		$result->execute();
}catch(PDOException $e){
    echo "Connection failed: " . $e->getMessage();
}

?>
