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
		$result = $con->prepare("SELECT * FROM inprogress");
		$result->execute();
		

		while($rs = $result->fetch()){
			if(startsWith($rs['CURRENT_LOC'],"REC")){
				$REC .= '{"CURRENT_LOC":"'.$rs['CURRENT_LOC'].'","ID":"'.$rs['ID'].'","NAME":"'.$rs['NAME'].'","DETAIL":"'.$rs['DETAIL'].'"},';
			}
		}
		echo '{RECIEVE:['.$REC.'{"CURRENT_LOC":"","ID":"","NAME":"","DETAIL":""}]}';
		//if($STA != "" || $SEN != "")
		//echo '{"PORTSENDRECIEVE":{RECIEVE:['.$STA.'{"CURRENT_LOC":"","ID":"","NAME":"","DETAIL":""}],SENT:['.$SEN.'{"CURRENT_LOC":"","ID":"","NAME":"","DETAIL":""}]}}';

	}
catch(PDOException $e)
    {
    echo "Connection failed: " . $e->getMessage();
    }
	
?>
