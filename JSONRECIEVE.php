<?php
//all user
//$storage_name = $_GET["STO_NAME"];
$servername = "localhost";
$username = "root";
$password = "";

$REC = "";

function startsWith ($string, $startString) 
{ 
    $len = strlen($startString); 
    return (substr($string, 0, $len) === $startString); 
} 

try {
    $con = new PDO("mysql:host=$servername;dbname=project", $username, $password);
    // set the PDO error mode to exception
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //echo "Connected successfully";
		$result = $con->prepare("SELECT * FROM inprogress WHERE CURRENT_LOC LIKE 're%'");// 
		$result->execute();
		
		if($result->rowCount() > 0){
			while($rs = $result->fetch(PDO::FETCH_ASSOC)){
				if(startsWith($rs['CURRENT_LOC'],"re")){
					$REC .= '{"CURRENT_LOC":"'.$rs['CURRENT_LOC'].'","ID":"'.$rs['PRODUCT_ID'].'","NAME":"'.$rs['NAME'].'","DETAIL":"'.$rs['DETAIL'].'"},';
				}else{
					$REC .= '{"CURRENT_LOC":"","ID":"","NAME":"","DETAIL":""},';
				}
			}
			echo '{"RECIEVE":[['.$REC.'{"CURRENT_LOC":"","ID":"","NAME":"","DETAIL":""}]]}';
		}else{
			echo '{"RECIEVE":[[{"CURRENT_LOC":"","ID":"","NAME":"","DETAIL":""},{"CURRENT_LOC":"","ID":"","NAME":"","DETAIL":""}]]}';
		}


	}
catch(PDOException $e)
    {
    echo "Connection failed: " . $e->getMessage();
    }
	
?>
