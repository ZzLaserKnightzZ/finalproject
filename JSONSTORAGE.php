<?php
//admin
$storage_name = $_GET["STORAGE"];
//$f = $_GET["F"];
//$floor = "'%F".$f."'";
$servername = "localhost";
$username = "root";
$password = "";

try {
		$con = new PDO("mysql:host=$servername;dbname=project", $username, $password);
		// set the PDO error mode to exception
		$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$result = $con->prepare("SELECT * FROM storage WHERE fac_id = (SELECT fac_id FROM faculty WHERE fac_name = '".$storage_name."') AND storage_id LIKE '%F1'");  //
		$result->execute();
			
			echo '{"'.$storage_name.'":[[';
			while($rs = $result->fetch()){
				//echo '{"NAME":"'.$rs['NAME'].'","LOCATION":"'.$rs['LOCATION'].'","STATUS":"'.$rs['STATUS'].'","JOBID":"'.$rs['JOBID'].'","PULSE":"'.$rs['PULSE'].'"},';
				//if($rs['ON_STORAGE'] == 1)
				if($rs['ON_STORAGE'] == 1 && $rs['NAME']!=  ''){
					echo '{"CRF":"'.$rs['storage_id'].'","NAME":"'.$rs['NAME'].'","ID":"'.$rs['PRODUCT_ID'].'","DETAIL":"'.$rs['DETAIL'].'"},';
				}else if($rs['ON_STORAGE'] == 1 && $rs['NAME']==  ''){
					echo '{"CRF":"'.$rs['storage_id'].'","NAME":"UNKNOW","ID":"0","DETAIL":"anonymous item"},';
				}else if($rs['ON_STORAGE'] == 0 && $rs['NAME']!=  ''){
					echo '{"CRF":"'.$rs['storage_id'].'","NAME":"MISSING","ID":"'.$rs['PRODUCT_ID'].'","DETAIL":"missing product"},';
				}else{
					echo '{"CRF":"'.$rs['storage_id'].'","NAME":"","ID":"","DETAIL":""},';
				}
			}
			
			echo "{}]";
			echo ",[";
		$result = $con->prepare("SELECT * FROM storage WHERE fac_id = (SELECT fac_id FROM faculty WHERE fac_name = '".$storage_name."') AND storage_id LIKE '%F2'");  //
		$result->execute();
			
			while($rs = $result->fetch()){
				//echo '{"NAME":"'.$rs['NAME'].'","LOCATION":"'.$rs['LOCATION'].'","STATUS":"'.$rs['STATUS'].'","JOBID":"'.$rs['JOBID'].'","PULSE":"'.$rs['PULSE'].'"},';
				if($rs['ON_STORAGE'] == 1 && $rs['NAME']!=  ''){
					echo '{"CRF":"'.$rs['storage_id'].'","NAME":"'.$rs['NAME'].'","ID":"'.$rs['PRODUCT_ID'].'","DETAIL":"'.$rs['DETAIL'].'"},';
				}else if($rs['ON_STORAGE'] == 1 && $rs['NAME']==  ''){
					echo '{"CRF":"'.$rs['storage_id'].'","NAME":"UNKNOW","ID":"0","DETAIL":"anonymous item"},';
				}else if($rs['ON_STORAGE'] == 0 && $rs['NAME']!=  ''){
					echo '{"CRF":"'.$rs['storage_id'].'","NAME":"MISSING","ID":"'.$rs['PRODUCT_ID'].'","DETAIL":"missing product"},';
				}else{
					echo '{"CRF":"'.$rs['storage_id'].'","NAME":"","ID":"","DETAIL":""},';
				}
			}
			echo "{}]]}";
			die();
	}
catch(PDOException $e)
    {
    echo "Connection failed: " . $e->getMessage();
    }
	
?>
