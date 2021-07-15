
<?php
session_start();
//autdite user

if(!isset($_SESSION["USERNAME"]) && !isset($_SESSION["FALCUTY"])){
	die();
}

//chk storage
//chk inprogress
//add inprogress

$servername = "localhost";
$username = "root";
$password = "";
//http://localhost/removeItem.php?SENT=1&CRF=C1R1F1&%20GROUP=electronics
//getItem(crf,sent)
$SENT = $_GET['SENT'];
$CRF = $_GET['CRF'];
$GROUP ="";
$AUTO_SELECT_ROBOT = "";
$arr_botName = [];		
$GROUP = $_GET['GROUP'];
$in = "F2";
$out = ["F2","F2:L1:P1:R0","F1:L1:P1:R0:F1:R0"]; //start end sto line

try {
		$conn = new PDO("mysql:host=$servername;dbname=project", $username, $password);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sqlcom = "SELECT WORK_ROUNDS,bot_name FROM robot WHERE ONLINE = '1' ORDER BY WORK_ROUNDS ASC";	
		$result = $conn->prepare($sqlcom);
		$result->execute();
		if($result->rowCount() >= 1){
			while($rs = $result->fetch()){ 
			//echo $rs['bot_name'];
				array_push($arr_botName ,  $rs['bot_name']  );
			}
		}
		//delete robot has job
		$sqlcom = "SELECT bot_name,DONE,PAY FROM robot left JOIN inprogress ON robot.bot_name = inprogress.BY_ROBOT_NAME";	
		$result = $conn->prepare($sqlcom);
		$result->execute();
		if($result->rowCount() >= 1){
			while($rs = $result->fetch()){
				if($rs['DONE'] == "N"){		
					for($ibott = 0 ; $ibott < count($arr_botName) ; $ibott++){
						$tmp = array_shift($arr_botName);
						if( $tmp == $rs['bot_name']){
							//unset($arr_botName[$ibott]);
						}else{
							array_push($arr_botName , $tmp);
						}
					}
				}
			}
		}
		
		if(count($arr_botName) > 0){
			//var_dump($arr_botName);
			ksort($arr_botName);
			$AUTO_SELECT_ROBOT = array_shift($arr_botName);
			
		}else{
			$AUTO_SELECT_ROBOT = "";
		}
		
		$sqlcom = "SELECT * FROM storage WHERE fac_id = (SELECT fac_id FROM faculty WHERE fac_name = '".$GROUP."') AND STORAGE_ID = '$CRF'";  //
		$result = $conn->query($sqlcom);
		//echo $result->rowCount();
		if($result->rowCount() == 1){
				$data = $result->fetch(PDO::FETCH_ASSOC);
				$slot = $data['slot'];
				if($data['ON_STORAGE'] == 0 ){
					echo '{"res":"no item"}';
				}else{
					
					//ไปถึงจุดเลี้ยวพอ +จะรับหรือส่ง+เลี้ยงกลับไปจุดออก
					$send = "G".substr($CRF,strlen($CRF)-1,strlen($CRF));
					$DST = $data['DST_IN'].":".$send.":".$data['DST_OUT'];
					$ID = $data['PRODUCT_ID'];
					$NAME = $data['NAME'];
					$DETAIL = $data['DETAIL'];
					//$sqlcom = "SELECT * FROM inprogress WHERE FALCUTY = '".$_SESSION["FALCUTY"]."' AND STORAGE_ID = '$CRF'";
					$sqlcom = "SELECT * FROM inprogress WHERE STO_NAME = '".$GROUP."' AND STORAGE_ID = '$CRF'" ;
					$result = $conn->query($sqlcom);
					
					if($result->rowCount() == 1){
						//fail
						echo '{"res":"this point is progressing"}';
					}else{
						//ok
						//recieved
						//LINE_NAME
						//count F from dst
						$stopoint = "sto:".Forward_couneter($DST);
						if($ID == 0){ 
							$ID = rand(100,1000)."";
							$NAME = "N/A";
						}
						$sql = "INSERT INTO `inprogress`(`BY_ROBOT_NAME`,`DONE`, `PAY`, `STO_NAME`, `RECIEVE`,`LINE_NAME`,`DST`,`SENT`, `STORAGE_ID`,`PRODUCT_ID`, `NAME`, `DETAIL`, `CURRENT_LOC`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)";
						$conn->prepare($sql)->execute([$AUTO_SELECT_ROBOT,"N", "N",$GROUP,$in,$stopoint,$DST,$out[(int)$SENT],$CRF,$ID,$NAME,$DETAIL,"sto"]); //11
						//history
						$sql = "INSERT INTO `data`(`username`, `robot_name`, `product_id`, `product_name`, `product_faculty`, `floor`, `slot`, `note`) VALUES (?,?,?,?,?,?,?,?)";
						$conn->prepare($sql)->execute([ $_SESSION["USERNAME"] , $AUTO_SELECT_ROBOT , $ID , $NAME , $GROUP , substr($CRF,strlen($CRF)-1,strlen($CRF)) , $slot , "job is't done"]); //11
						echo '{"res":"success"}';
					}
				}

		}else{
			//that location is unavailable
			echo '{"res":"error storage_id point"}';
		}
		
		


	}
catch(PDOException $e)
    {
		echo "Connection failed: " . $e->getMessage();
    }
	
 	function Forward_couneter($dst)
	{
		$count = 0;
		$arrdst = explode(":",$dst);
		for($i = 0 ; $i < count($arrdst) ; $i++){
			if(startsWith($arrdst [$i]  , "F" )){
				$intforward = substr($arrdst [$i] , 1 , strlen($arrdst [$i]));
				if(is_numeric($intforward))
					$count += (int)$intforward;
			}
			if(startsWith($arrdst [$i]  , "G" )) break;
		}
		return $count;
	}
	
	function startsWith ($string , $startString) 
	{ 
		$len = strlen($startString); 
		return (substr($string, 0, $len) === $startString); 
	} 
  
	
?>
