<?php
session_start();
//autdite user

if(!isset($_SESSION["USER"]) && !isset($_SESSION["FALCUTY"])){
	die();
}

//chk storage
//chk inprogress
//add inprogress

$servername = "localhost";
$username = "root";
$password = "";

//ajax("POST","/addItem","?RECIEVE="+recieve+"&CRF="+crf+"&NAME="+name+"&ID="+id+"&DETAIL="+detial,callBack);
//http://localhost/addItem.php?GROUP=electronics&RECIEVE=1&CRF=C1R1F1&NAME=hello&ID=1235656&DETAIL=asdasdsad
$RECIEVE = $_GET['RECIEVE'];
$CRF = $_GET['CRF'];
$NAME = $_GET['NAME'];
$ID = $_GET['ID'];
$DETAIL = $_GET['DETAIL'];
$GROUP;
$AUTO_SELECT_ROBOT = "";
$GROUP = $_GET['GROUP'];
$arr_botName = [];

$in = ["F2","F1:L1:G1:R0:F1","F2:L1:G1:R0"];
$out = ["F2","F2","F1"];

if( has_NAN($ID."") == false ){
	echo '{"res":"error id is not number"}';
	die();
}

try {
		$conn = new PDO("mysql:host=$servername;dbname=project", $username, $password);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		// $sqlcom = "SELECT * FROM `robot` left join inprogress on inprogress.BY_ROBOT_NAME = robot.bot_name
		           // WHERE ONLINE = '1' and bot_name =(SELECT BY_ROBOT_NAME FROM inprogress where DONE != 'N' or ISNULL(DONE) LIMIT 0,1) 
				   // ORDER BY WORK_ROUNDS ASC LIMIT 0,1";
	
		
		// $sqlcom = "SELECT bot_name,DONE,PAY,WORK_ROUNDS,ONLINE FROM robot left JOIN inprogress ON robot.bot_name = inprogress.BY_ROBOT_NAME";
		// $result = $conn->query($sqlcom);
		// if($result->rowCount() == 1){
			// $data = $result->fetch(PDO::FETCH_ASSOC);
			// $AUTO_SELECT_ROBOT = $data['bot_name'];
		// }
		
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
		$sqlcom = "SELECT * FROM storage WHERE fac_id = (SELECT fac_id FROM faculty WHERE fac_name = '".$GROUP."') AND STORAGE_ID = '$CRF' ";  //".$GROUP."
		$result = $conn->query($sqlcom);
		
		if($result->rowCount() == 1){
				
				$data = $result->fetch(PDO::FETCH_ASSOC);
				$dist_dst_in;
				$slot = $data['slot'];
				if($data['ON_STORAGE'] == 0 ){
					//ไปถึงจุดเลี้ยวพอ +จะรับหรือส่ง+เลี้ยงกลับไปจุดออก
					$pass = "P".substr($CRF,strlen($CRF)-1,strlen($CRF));
					$dist_dst_in = $data['DST_IN'];
					$DST = $data['DST_IN'].":".$pass.":".$data['DST_OUT'];
					
					//$sqlcom = "SELECT * FROM inprogress WHERE FALCUTY = '".$_SESSION["FALCUTY"]."' AND CRF = '$CRF'"; //กันของที่ตั้งอยู่ยังไม่ขนไป
					$sqlcom = "SELECT * FROM `inprogress` left join storage on storage.PRODUCT_ID = inprogress.PRODUCT_ID where inprogress.PRODUCT_ID = $ID";
					//echo $sqlcom;
					$result = $conn->query($sqlcom);
					//echo $result->rowCount();
					if($result->rowCount() >= 1){
						//fail
						echo '{"res":"this point is progressing"}';
					}else{
						//ok
						//recieved
						$dist = "sto:".Forward_couneter($dist_dst_in);
						$sql = "INSERT INTO `inprogress`(`BY_ROBOT_NAME`,`DONE`, `PAY`, `STO_NAME`, `RECIEVE`,`DST`,`SENT`, `STORAGE_ID`,`PRODUCT_ID`, `LINE_NAME`, `NAME`, `DETAIL`, `CURRENT_LOC`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)";
						$conn->prepare($sql)->execute([$AUTO_SELECT_ROBOT,"N","N",$GROUP,$in[(int)$RECIEVE],$DST,$out[0],$CRF,$ID,$dist,$NAME,$DETAIL,"re".$RECIEVE]); //11
						//success
						//history
						$sql = "INSERT INTO `data`(`username`, `robot_name`, `product_id`, `product_name`, `product_faculty`, `floor`, `slot`, `note`) VALUES (?,?,?,?,?,?,?,?)";
						$conn->prepare($sql)->execute([ $_SESSION["USERNAME"] , $AUTO_SELECT_ROBOT , $ID , $NAME , $GROUP , substr($CRF,strlen($CRF)-1,strlen($CRF)) , $slot , "job is't done"]); //11
						
						echo '{"res":"success"}';
					}
				}else{
					echo '{"res":"this point is available"}';
				}

		}else{
			//that location is unavailable
			echo '{"res":"error CRF point"}';
			//echo '{"res":"'.$GROUP.'"}';
		}
		
		


	}
catch(PDOException $e)
    {
	echo '{"res":"Connection failed " '. $e->getMessage().'"}';
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
			if(startsWith($arrdst [$i]  , "P" )) break;
		}
		return $count;
	}
	
		function startsWith ($string , $startString) 
	{ 
		$len = strlen($startString); 
		return (substr($string, 0, $len) === $startString); 
	}  
	
	function has_NAN($text)
	{

		$char  =  str_split($text);
		for($i = 0 ; $i < strlen($text) ; $i++){
			
			//echo $char[$i].":".ord($char[$i])."<br>"; 			 
			if(  ord($char[$i]) < 48 || ord($char[$i]) > 57){
				return false;
			}
		}
		return true;
	}
	
?>
