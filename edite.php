<?php
session_start();
//admin
//user
if(!isset($_SESSION["USERNAME"])) die(); //anonymous user fuck off

$servername = "localhost";
$username = "root";
$password = "";
$sqlcom = "";

//admin post recieve -> get
$CRF = $_GET['CRF'];
$ID = $_GET['ID'];
$DETAIL = $_GET['DETAIL'];
$NAME = $_GET['NAME'];
$FALCUTY = "";
$sqlcom = "UPDATE `storage` SET `PRODUCT_ID` = ?,`NAME` = ?,`DETAIL` = ? WHERE fac_id = (SELECT fac_id FROM faculty WHERE fac_name = ? ) AND storage_id = ?";

if($_SESSION["SPAECIAL"] == "ALL"){
	//$sqlcom = "UPDATE `".$_GET["FALCUTY"]."` SET `ID` = ?,`NAME` = ?,`DETAIL` = ? WHERE `CRF` = ? ";
	//$sqlcom = "UPDATE `storage` SET `PRODUCT_ID` = ?,`NAME` = ?,`DETAIL` = ? WHERE fac_id = (SELECT fac_id FROM faculty WHERE fac_name = ? ) AND storage_id = ?";
	$FALCUTY = $_GET["FALCUTY"];
}else{
	//$sqlcom = "UPDATE `".$_SESSION["FALCUTY"]."` SET `ID` = ?,`NAME` = ?,`DETAIL` = ? WHERE `CRF` = ? ";
	//$sqlcom = "UPDATE `storage` SET `PRODUCT_ID` = ?,`NAME` = ?,`DETAIL` = ? WHERE fac_id = (SELECT fac_id FROM faculty WHERE fac_name = ? ) AND storage_id = ?";
	$FALCUTY = $_SESSION["FALCUTY"];
}

try {
    $conn = new PDO("mysql:host=$servername;dbname=project", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$result = $conn->prepare($sqlcom)->execute([$ID,$NAME,$DETAIL,$FALCUTY,$CRF]);
	echo '{"res":"ok"}';
	}
catch(PDOException $e)
    {
     //echo "Connection failed: " . $e->getMessage();
	 echo '{"res":"fail"}';
    }

?>