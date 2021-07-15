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
$FALCUTY;
$ID = $_GET['ID'];

$MANAGE = $_GET['MANAGE']; //updat delete

if($_SESSION["SPAECIAL"] == "ALL"){  //admin -> host/clearPGS.php?FALCUTY=""&MANAGE=""&ID=""
	//$FALCUTY = $_GET["FALCUTY"];
	$sqlcom = "SELECT * FROM inprogress WHERE PRODUCT_ID = ?";
	//echo "admin";
}else{ //admin -> host/clearPGS.php?FALCUTY=science&MANAGE=update&ID=555
	$FALCUTY = $_SESSION["FALCUTY"];
	$sqlcom = "SELECT * FROM inprogress WHERE PRODUCT_ID = ? AND FALCUTY = '".$FALCUTY."'";
	//echo "user";
}

$servername = "localhost";
$username = "root";
$password = "";
$name = "";
$detail = "";
$crf = "";

try {
	
    $conn = new PDO("mysql:host=$servername;dbname=project", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	

	//เลื่อนไปให้ิbot
	if($MANAGE == "update"){
		//$sqlcom = "SELECT * FROM ".$_SESSION["FALCUTY"];
		/*
		$result = $conn->prepare($sqlcom);
		$result->execute([$ID]);
		
		if($result->rowCount() == 1){
			//$json = '{"REPORT":';
			while($rs = $result->fetch()){
				//จับ data
				$crf = $rs['STORAGE_ID'];
				$name = $rs['NAME'];
				$detail = $rs['DETAIL'];
				//echo $crf.$name.$detail;
			}
			
		}
			

		$result = $conn->prepare("UPDATE `storage` SET `NAME`= ?,`PRODUCT_ID`= ?,`DETAIL`= ? WHERE `STORAGE_ID` = ? AND fac_id = (SELECT fac_id FROM faculty WHERE fac_name = '".$FALCUTY."')");  //".$FALCUTY."
		$result->execute([$name,$ID,$detail,$crf]); //if(1)
		*/
		$result = $conn->prepare("DELETE FROM inprogress WHERE `PRODUCT_ID` = ?");
		$result->execute([$ID]); //if(1)
		echo '{"res":"UPDATED"}';
		
	}else if($MANAGE == "clear"){ //wrong or item not found
		//delete
		$result = $conn->prepare("DELETE FROM `inprogress` WHERE `PRODUCT_ID` = ?");
		$result->execute([$ID]); //if(1)
		echo '{"res":"DELETED"}';
	}



	
	
}catch(PDOException $e)
    {
    echo "Connection failed: " . $e->getMessage();
    }

?>
