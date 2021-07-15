<?php
//no secure
//no encrypt

/*
//botname=BOT1&req=new job || report 
$REQ = $_GET['REQ'];
$BOT_NAME =  $_GET['NAME'];
/*
$JOBID =  $_GET['JOBID'];
$LOCATION =  $_GET['LOCATION'];
$ERROR = $_GET['ERROR'];
*/
$start_time = microtime(true); 

$servername = "localhost";
$username = "root";
$password = "";

$MAP_DATA = 'botmap'; //load map
//#includes("map.php");
require_once('map.php');
header("content-type: none");
$BOT_NAME = $_GET['BOT_NAME'];
$REQ = $_GET['REQ'];
$LOCATION;// = $_GET['LOCATION'];
$STATUS;// = $_GET['STATUS'];
$JOBID;// = $_GET['JOBID'];
$ONLINE =0;
$HASJOB2 ;
$NAME ="";
//$LIST_POINT = array("1"=>'re1',"2"=>'re2',"3"=>'re3',"4"=>'re4',"5"=>'sto1',"6"=>'sto2',"7"=>'sto3',"8"=>'sto4',"9"=>'sto5',"10"=>'sto6',"11"=>'sto7',"12"=>'sto8',"13"=>'sto9',"14"=>'sto10',"15"=>'sen1',"16"=>'sen2',"17"=>'sen3',"18"=>'sta1',"19"=>'sta2',"20"=>'sta3',"101"=>'BOT1',"100"=>'BOT2');
			$dst_in = "";
			$dst_out = "";
			$rec = "";
			$sto = "";
			$sen = "";
			$ID = "";
			$JOB = "";
			$step_out = '';
			$loc_list = "re:0>sto:0>sen:0>sta:0"; //re/sto/sen/sta
//var_dump($LIST_POINT);

try {
	
    $conn = new PDO("mysql:host=$servername;dbname=project", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

				
			
		if($REQ == "newjob"){ //localhost/handleROBOT.php?BOT_NAME=BOT1&REQ=newjob

			
			$result = $conn->query("SELECT * FROM robot WHERE bot_name = '".$BOT_NAME."'");
			if($result->rowCount() == 1){
				$dt_rs = $result->fetch(PDO::FETCH_ASSOC);
				$dst_in = $dt_rs["DST_IN"];
				$dst_out = $dt_rs["DST_OUT"];
				$step_out = $dt_rs["STEP_OUT"];
			}
			
			$result = $conn->query("SELECT * FROM inprogress WHERE DONE = 'N' AND PAY = 'N' AND BY_ROBOT_NAME = '".$BOT_NAME."' LIMIT 0,1"); //where done = n pa
			$result->execute();
			
			if($result->rowCount() >= 1){
				$data = $result->fetch(PDO::FETCH_ASSOC);
				$rec = $data['RECIEVE'];
				$sto = $data['DST'];
				$sen = $data['SENT'];
				$ID = $data['PRODUCT_ID'];
				$JOB = "YOURJOB:".$ID.">".$step_out.">".$loc_list."<".$dst_out.">".$rec.">".$sto.">".$sen.">".$dst_in; //ID=5454>fasd>asdsas>adsad //แก้ไหม่5ตัว
			}

			if($ID != ""){  //when robot waas added by input
				$sql = "UPDATE `inprogress` SET `BY_ROBOT_NAME` = ?, `PAY` = ? WHERE `PRODUCT_ID` = ?";
				$conn->prepare($sql)->execute([$BOT_NAME,"Y", $ID]);
				if($result){
					echo $BOT_NAME.">".$JOB;
				}
			}else{   //server has job and no have robot avialble
				$sql = "UPDATE `inprogress` SET `BY_ROBOT_NAME` = ? WHERE `BY_ROBOT_NAME` = ?";
				$conn->prepare($sql)->execute([$BOT_NAME,""]);
			}
			
			date_default_timezone_set("Asia/Bangkok");	
			$dateTime = new DateTime();
			$result = $conn->prepare("UPDATE `robot` SET `LAST_REQUEST`= ? WHERE `bot_name` = ?"); //& ERROR == ""
			$result->execute([$dateTime->format('Y-m-d H:i:s'), $BOT_NAME]);	
					//all robot
					//date cmp
					//time cmp
					
					$dateTime = new DateTime();
					$list_bot = [];// = array();  //if more data = low memory
					$sqlcom = "SELECT * FROM robot";
					$result = $conn->prepare($sqlcom);
					$result->execute();
					while($rs = $result->fetch()){ ///pulse ตัวตามหลัง
					
						$datetimeDB = new DateTime($rs['LAST_REQUEST']);
						$interval = $dateTime->diff($datetimeDB);
						//sql select
						//if(t1 > t2)
						$botname_name = $rs['bot_name']."";
						if($interval->y == 0 && $interval->m == 0 && $interval->d == 0 && $interval->h == 0 && $interval->i >= 0 && $interval->i <= 3  ){
							array_push($list_bot  , array($botname_name  => 1) );
						}else{
							array_push($list_bot  , array($botname_name  => 0) );
						} 
						//echo $rs['NAME'];
					}

					//var_dump($list_bot);
					//sql update
					$sqlcom = "UPDATE `robot` SET `ONLINE`= ? WHERE `bot_name` = ?";   //` ->bind_param  ROW_NUMBER()
					$result = $conn->prepare($sqlcom);
					$result->bindParam(1, $ONLINE, PDO::PARAM_INT);
					$result->bindParam(2, $NAME, PDO::PARAM_STR);	
					foreach($list_bot as $key => $val){
						
						foreach($val as $v => $s){
							//var_dump($key);
							$NAME = $v; //botname
							//var_dump($s);
							$ONLINE = $s;
							//$NAME = array_search( ,$val);  ///problem
							//echo $ONLINE.":".$v;
							$result->execute();
							//echo (String)$key;
							//echo $val;
						}

					}

			
		}else if($REQ == "canstep"){
			//locate
			///+กันชน before p 2
			//add state
			
			//$_GET[];
			//$result = $conn->prepare('UPDATE `robot` SET `STATUS`= ?,`JOBID`= ?,`LOCATION`= ? WHERE `NAME` = ?');
			//$result->execute([,]);
			$LOCATION = $_GET['LOCATION'];
			$STATUS = $_GET['STATUS'];
			//if($STATUS == "BACKING") $LOCATION=$BOT_NAME;
			$result = $conn->prepare('UPDATE `robot` SET `STATUS`= ?,`LOCATION`= ? WHERE `bot_name` = ?');
			$result->execute([$STATUS , $LOCATION , $BOT_NAME]);
			//search  $LIST_POINT[];
			//i can go
			//echo
			$sbot_point = (int) array_search($LOCATION , $LIST_POINT); //return key
			//echo $sbot_point;
			$List_distace = array();
			//array_push($List_distace , 0);
			$ack = 0;
			$sqlcom = "SELECT LOCATION FROM robot WHERE bot_name != '".$BOT_NAME."'";  //หาที่อยู่ของตัวอื่นๆ
			$result = $conn->prepare($sqlcom);
			$result->execute();
			if($result->rowCount() >= 1)
			while($rs = $result->fetch()){ ///pulse ตัวตามหลัง
				$another_bot = (int) array_search($rs['LOCATION'] , $LIST_POINT);
				if($another_bot > 0 && $another_bot <= 23){
					//echo $another_bot;
					//if($another_bot >= 23 && $another_bot <= 24){ $another_bot = 3}  //ที่จอด
					if($sbot_point+1 == 21 && $another_bot >= 23 && $another_bot <= 24){ array_push($List_distace , 2); } //ดักตั้งแต่ sen0 เดินได้แค่2ไปจนสุดเส้นไหม่ sta0
					if($sbot_point == $another_bot && $another_bot >= 23 && $another_bot <= 24){ array_push($List_distace , 0); }//จะออกแต่จุดจุดนั้นคือจุดแรกแต่มีตัวอื่นอยู่
					if( $another_bot+1 >= $sbot_point ){ //i can go 2 p
						$fdistace;
						if(($another_bot+1) >= 23 || ($sbot_point+3) >= 23){ //max distance robot
							$fdistace = 4; //none robot
						}else{
	
							if(($another_bot+1) == ($sbot_point+3)){
								$fdistace = 0;
							}else{
								$fdistace = ($another_bot+1) - ($sbot_point+3);
								if($fdistace <= 0) $fdistace = 0;
								//echo $another_bot.":".$sbot_point.":".$fdistace;
							}
							
						}
						array_push($List_distace , $fdistace);
					}
				}
			}
			//var_dump($LIST_POINT);
			//echo var_dump($List_distace);
			if(count($List_distace) > 0){
				$ack = min($List_distace);
				if($ack > 4) $ack = 4;
			}else{
				$ack = 4;
			}
			//$end_time = microtime(true);
			//$execution_time = ($end_time - $start_time); 
			//echo $execution_time.'sec' ;
			echo $BOT_NAME.">cango>".$ack;
			
			if($_GET['HASJOB2'] == "0"){ //search db => is new job
				$my_dst = "";
				$my_send = "";
				$my_product_id = "";
				$next_line_name = "";
				$bot_satnby = "";
				$gcurrent_item = "";
				//chk cuurent job id g
				if($_GET['JOBID'] != ""){
					$sqlcom = "SELECT LINE_NAME FROM inprogress WHERE PRODUCT_ID = ? AND RECIEVE LIKE '%G%'";
					$result = $conn->prepare($sqlcom);  //get data from productid	
					$result->execute([$_GET['JOBID']]);
								
					if($result->rowCount() == 1){ //have job
						$rs = $result->fetch();
						$gcurrent_item = $rs['LINE_NAME'];	
						//echo $gcurrent_item;
					}
				}
				//echo "y";
				//find job is next point and this is 'P'
				$sqlcom = "SELECT * FROM inprogress WHERE SENT LIKE '%P%' AND DONE = ? AND PAY = ? LIMIT 0,1";
				$result = $conn->prepare($sqlcom);  //get data from productid
				$result->execute(["N","N"]);

				if($result->rowCount() == 1){
					//echo "count +1";
					while($rs = $result->fetch()){
						//จับ data
						$my_dst = $rs['DST'];
						$my_send = $rs['SENT'];
						$my_product_id = $rs['PRODUCT_ID'];
						$next_line_name = $rs['LINE_NAME'];
					}
					//select botdstin
					$sqlcom = "SELECT DST_IN FROM `robot` WHERE bot_name = '".$BOT_NAME."'";  //wม่ป้องกันยังไม่รับงานได้งานซ้อนไป
					$result = $conn->query($sqlcom);
					if($result->rowCount() == 1){
						$data = $result->fetch(PDO::FETCH_ASSOC);
						$bot_satnby = $data['DST_IN'];
						//echo "yeds";
					}
					//cut LR before  g
					//echo "linename:".$LOCATION .":".$line_name;
					//echo ":".has_job_point($LOCATION , $next_line_name);
					//echo "line name".$line_name;
					//echo $gcurrent_item.">".$next_line_name;
					if($JOBID != ""){  //hasjob
						//echo "hello";
						//echo "hellgfdfo";
						//echo $gcurrent_item.">".$next_line_name;
						//echo has_job_point($gcurrent_item , $next_line_name , 0) ;
						if(has_job_point($gcurrent_item , $next_line_name , 0) == true){
							//echo "hh";
							$half_way = get_half_way($my_dst);
							$loc_list2 = $next_line_name.">sen:0>sta:0";  //BOT1>stop_point>sto:9>0978978>sto:sto:9>sen:0>sta:0<R1:G1:L0:F2>F1:R1:F2:R1:F0
							$sql = "UPDATE `inprogress` SET `BY_ROBOT_NAME` = ?, `PAY` = ? WHERE `PRODUCT_ID` = ?";
							if($conn->prepare($sql)->execute([$BOT_NAME,"Y", $my_product_id]) == 1){
								echo "\r\n".$BOT_NAME.">stop_point>".$next_line_name.">".$my_product_id.">".$loc_list2."<".$half_way.">".$my_send.">".$bot_satnby;
							}
						}
					}else{
						if(has_job_point($LOCATION , $next_line_name , 3) == true){
							$half_way = get_half_way($my_dst);
							$loc_list2 = $next_line_name.">sen:0>sta:0";  //BOT1>stop_point>sto:9>0978978>sto:sto:9>sen:0>sta:0<R1:G1:L0:F2>F1:R1:F2:R1:F0
							$sql = "UPDATE `inprogress` SET `BY_ROBOT_NAME` = ?, `PAY` = ? WHERE `PRODUCT_ID` = ?";
							if($conn->prepare($sql)->execute([$BOT_NAME,"Y", $my_product_id]) == 1){
								echo "\r\n".$BOT_NAME.">stop_point>".$next_line_name.">".$my_product_id.">".$loc_list2."<".$half_way.">".$my_send.">".$bot_satnby;
							}
						}
					}
				}
			}
			
		}else if($REQ == "report"){  //http://localhost/handleROBOT.php?BOT_NAME=BOT1&HASJOB2=0&JOBID=4655&REQ=report&STATUS=working&LOCATION=STA1
			
			$LOCATION = $_GET['LOCATION'];
			$STATUS = $_GET['STATUS'];
			$JOBID = $_GET['JOBID'];
			$HASJOB2 = $_GET['HASJOB2'];
			
			$result = $conn->prepare('UPDATE `robot` SET `STATUS`= ?,`JOBID`= ?,`LOCATION`= ? WHERE `bot_name` = ?');
			$result->execute([$STATUS , $JOBID , $LOCATION , $BOT_NAME]);
			
			$result = $conn->prepare('UPDATE `inprogress` SET `CURRENT_LOC`= ? WHERE `PRODUCT_ID` = ?');
			$result->execute([$LOCATION , $JOBID ]);
			
			$sqlcom = "SELECT LOCATION FROM robot WHERE bot_name != '".$BOT_NAME."'";  //+online
			$result = $conn->prepare($sqlcom);
			$result->execute();
			if($result->rowCount() >= 1){
				$sbot_point =  array_search( $LOCATION , $LIST_POINT); 
				while($rs = $result->fetch()){ ///pulse ตัวตามหลัง
					$another_bot = array_search($rs['LOCATION'] , $LIST_POINT);
					if($sbot_point - $another_bot > 0) //อยู่ข้างหน้าแน่ๆ
						if(($sbot_point - $another_bot) >= 1 && ($sbot_point - $another_bot) <= 3 && $another_bot < count($LIST_POINT) -2 ){  //maxforward
							echo $BOT_NAME.">emergencystop";
							die();
						}
				}
			}
			
			//echo "y";
			if($HASJOB2 == "0"){ //search db => is new job
				$my_dst = "";
				$my_send = "";
				$my_product_id = "";
				$next_line_name = "";
				$bot_satnby = "";
				$gcurrent_item = "";
				//chk cuurent job id g
				if($JOBID != ""){
					$sqlcom = "SELECT LINE_NAME FROM inprogress WHERE PRODUCT_ID = ? AND RECIEVE LIKE '%G%'";
					$result = $conn->prepare($sqlcom);  //get data from productid	
					$result->execute([$JOBID]);
								
					if($result->rowCount() == 1){ //have job
						$rs = $result->fetch();
						$gcurrent_item = $rs['LINE_NAME'];	
						//echo $gcurrent_item;
					}
				}
				//echo "y";
				//find job is next point and this is 'P'
				$sqlcom = "SELECT * FROM inprogress WHERE SENT LIKE '%P%' AND DONE = ? AND PAY = ? LIMIT 0,1";
				$result = $conn->prepare($sqlcom);  //get data from productid
				$result->execute(["N","N"]);

				if($result->rowCount() == 1){
					//echo "count +1";
					while($rs = $result->fetch()){
						//จับ data
						$my_dst = $rs['DST'];
						$my_send = $rs['SENT'];
						$my_product_id = $rs['PRODUCT_ID'];
						$next_line_name = $rs['LINE_NAME'];
					}
					//select botdstin
					$sqlcom = "SELECT DST_IN FROM `robot` WHERE bot_name = '".$BOT_NAME."'";  //wม่ป้องกันยังไม่รับงานได้งานซ้อนไป
					$result = $conn->query($sqlcom);
					if($result->rowCount() == 1){
						$data = $result->fetch(PDO::FETCH_ASSOC);
						$bot_satnby = $data['DST_IN'];
						//echo "yeds";
					}
					//cut LR before  g
					//echo "linename:".$LOCATION .":".$line_name;
					//echo ":".has_job_point($LOCATION , $next_line_name);
					//echo "line name".$line_name;
					//echo $gcurrent_item.">".$next_line_name;
					if($JOBID != ""){  //hasjob
						//echo "hello";
						//echo "hellgfdfo";
						//echo $gcurrent_item.">".$next_line_name;
						//echo has_job_point($gcurrent_item , $next_line_name , 0) ;
						if(has_job_point($gcurrent_item , $next_line_name , 0) == true){
							//echo "hh";
							$half_way = get_half_way($my_dst);
							$loc_list2 = $next_line_name.">sen:0>sta:0";  //BOT1>stop_point>sto:9>0978978>sto:sto:9>sen:0>sta:0<R1:G1:L0:F2>F1:R1:F2:R1:F0
							$sql = "UPDATE `inprogress` SET `BY_ROBOT_NAME` = ?, `PAY` = ? WHERE `PRODUCT_ID` = ?";
							if($conn->prepare($sql)->execute([$BOT_NAME,"Y", $my_product_id]) == 1){
								echo "\r\n".$BOT_NAME.">stop_point>".$next_line_name.">".$my_product_id.">".$loc_list2."<".$half_way.">".$my_send.">".$bot_satnby;
							}
						}
					}else{
						if(has_job_point($LOCATION , $next_line_name , 3) == true){
							$half_way = get_half_way($my_dst);
							$loc_list2 = $next_line_name.">sen:0>sta:0";  //BOT1>stop_point>sto:9>0978978>sto:sto:9>sen:0>sta:0<R1:G1:L0:F2>F1:R1:F2:R1:F0
							$sql = "UPDATE `inprogress` SET `BY_ROBOT_NAME` = ?, `PAY` = ? WHERE `PRODUCT_ID` = ?";
							if($conn->prepare($sql)->execute([$BOT_NAME,"Y", $my_product_id]) == 1){
								echo "\r\n".$BOT_NAME.">stop_point>".$next_line_name.">".$my_product_id.">".$loc_list2."<".$half_way.">".$my_send.">".$bot_satnby;
							}
						}
					}
				}
			}

			
		}else if($REQ == "job_success"){ //http://localhost/handleROBOT.php?BOT_NAME=BOT1&REQ=job_success&JOBID=999999
			//$sql = "UPDATE inprogress SET DONE = ? WHERE ID = ?";
			//sql = "SELECT * FROM inprogress WHERE ID = ?"
			//sql = "UPDATE ? SET ID = ?,NAME = ?,DETAIL = ? WHERE CRF = ?";
			$name = "";
			$detail = "";
			$crf = "";
			$fac_idss = "";
			$str_send = "";
			
			$HASJOB2 = $_GET['HASJOB2'];
			$JOBID = $_GET['JOBID'];
			$result = $conn->prepare("UPDATE `inprogress` SET `DONE`= ? WHERE `PRODUCT_ID` = ?"); //& ERROR == ""
			$result->execute(["Y",$JOBID]);	
			//echo "/r/n/r/n2";
			$result = $conn->prepare("UPDATE `robot` SET `WORK_ROUNDS`= WORK_ROUNDS + 1 WHERE `bot_name` = ?"); //& ERROR == ""
			$result->execute([$BOT_NAME]);
			//echo "1";
			$result = $conn->prepare("SELECT * FROM inprogress WHERE PRODUCT_ID = ?");  //get data from productid
			$result->execute([$JOBID]);
			
			if($result->rowCount() == 1){
				//$json = '{"REPORT":';
				while($rs = $result->fetch()){
					//จับ data
					$crf = $rs['STORAGE_ID'];
					$name = $rs['NAME'];
					$detail = $rs['DETAIL'];
					$fac_idss =  $rs['STO_NAME'];
					$str_send = $rs['SENT'];
					//echo $crf.$name.$detail;
				}
				
			}
				//chk error again none
			if( strpos($str_send , "P") === false){ // send update else delete
				$result = $conn->prepare("UPDATE `storage` SET `NAME`= ?,`PRODUCT_ID`= ?,`DETAIL`= ? WHERE `STORAGE_ID` = ? AND fac_id = (SELECT fac_id FROM faculty WHERE fac_name = ?)");  //".$FALCUTY."
				$result->execute([$name,$JOBID,$detail,$crf,$fac_idss]); //if(1)
			}else{
				$result = $conn->prepare("UPDATE `storage` SET `NAME`= ?,`PRODUCT_ID`= ?,`DETAIL`= ? WHERE `STORAGE_ID` = ? AND fac_id = (SELECT fac_id FROM faculty WHERE fac_name = ?)");  //".$FALCUTY."
				$result->execute(["","","",$crf,$fac_idss]); //if(1)
			}
				$result = $conn->prepare("UPDATE `data` SET `note`= ? WHERE product_id = ?");  //".$FALCUTY."
				$result->execute(['success',$JOBID]); //if(1)

			
			//$result = $conn->prepare("DELETE FROM inprogress WHERE `PRODUCT_ID` = ?");
			//$result->execute([$JOBID]);
						// ไม่รูว่าเลยหรือยัง
			//เชคว่ามันอยู่ด้านหน้าแน่นะ
			//search db => is new job
			// ไม่รูว่าเลยหรือยัง
			//เชคว่ามันอยู่ด้านหน้าแน่นะ
			if($HASJOB2 == "0"){ //search db => is new job
				$my_dst = "";
				$my_send = "";
				$my_product_id = "";
				$next_line_name = "";
				$bot_satnby = "";
				$gcurrent_item = "";
				//chk cuurent job id g
				if($JOBID != ""){
					$sqlcom = "SELECT LINE_NAME FROM inprogress WHERE PRODUCT_ID = ? AND RECIEVE LIKE '%G%'";
					$result = $conn->prepare($sqlcom);  //get data from productid	
					$result->execute([$JOBID]);
								
					if($result->rowCount() == 1){ //have job
						$rs = $result->fetch();
						$gcurrent_item = $rs['LINE_NAME'];	
						//echo $gcurrent_item;
					}
				}
				//echo "y";
				//find job is next point and this is 'P'
				$sqlcom = "SELECT * FROM inprogress WHERE SENT LIKE '%P%' AND DONE = ? AND PAY = ? LIMIT 0,1";
				$result = $conn->prepare($sqlcom);  //get data from productid
				$result->execute(["N","N"]);

				if($result->rowCount() == 1){
					//echo "count +1";
					while($rs = $result->fetch()){
						//จับ data
						$my_dst = $rs['DST'];
						$my_send = $rs['SENT'];
						$my_product_id = $rs['PRODUCT_ID'];
						$next_line_name = $rs['LINE_NAME'];
					}
					//select botdstin
					$sqlcom = "SELECT DST_IN FROM `robot` WHERE bot_name = '".$BOT_NAME."'";  //wม่ป้องกันยังไม่รับงานได้งานซ้อนไป
					$result = $conn->query($sqlcom);
					if($result->rowCount() == 1){
						$data = $result->fetch(PDO::FETCH_ASSOC);
						$bot_satnby = $data['DST_IN'];
						//echo "yeds";
					}
					//cut LR before  g
					//echo "linename:".$LOCATION .":".$line_name;
					//echo ":".has_job_point($LOCATION , $next_line_name);
					//echo "line name".$line_name;
					//echo $gcurrent_item.">".$next_line_name;
					if($JOBID != ""){  //hasjob
						//echo "hello";
						//echo "hellgfdfo";
						//echo $gcurrent_item.">".$next_line_name;
						//echo has_job_point($gcurrent_item , $next_line_name , 0) ;
						if(has_job_point($gcurrent_item , $next_line_name , 0) == true){
							//echo "hh";
							$half_way = get_half_way($my_dst);
							$loc_list2 = $next_line_name.">sen:0>sta:0";  //BOT1>stop_point>sto:9>0978978>sto:sto:9>sen:0>sta:0<R1:G1:L0:F2>F1:R1:F2:R1:F0
							$sql = "UPDATE `inprogress` SET `BY_ROBOT_NAME` = ?, `PAY` = ? WHERE `PRODUCT_ID` = ?";
							if($conn->prepare($sql)->execute([$BOT_NAME,"Y", $my_product_id]) == 1){
								echo "\r\n".$BOT_NAME.">stop_point>".$next_line_name.">".$my_product_id.">".$loc_list2."<".$half_way.">".$my_send.">".$bot_satnby;
							}
						}
					}else{
						if(has_job_point($LOCATION , $next_line_name , 0) == true){
							$half_way = get_half_way($my_dst);
							$loc_list2 = $next_line_name.">sen:0>sta:0";  //BOT1>stop_point>sto:9>0978978>sto:sto:9>sen:0>sta:0<R1:G1:L0:F2>F1:R1:F2:R1:F0
							$sql = "UPDATE `inprogress` SET `BY_ROBOT_NAME` = ?, `PAY` = ? WHERE `PRODUCT_ID` = ?";
							if($conn->prepare($sql)->execute([$BOT_NAME,"Y", $my_product_id]) == 1){
								echo "\r\n".$BOT_NAME.">stop_point>".$next_line_name.">".$my_product_id.">".$loc_list2."<".$half_way.">".$my_send.">".$bot_satnby;
							}
						}
					}
				}
			}
			
		}else if($REQ == "error"){ //?BOTNAME = dasdas & REQ = error	& ID = 5665 
			//select falcuty crf set alert error
			
			$error = $_GET['ERROR']; //type etc
			$JOBID = $_GET['JOBID'];
			$result = $conn->prepare("UPDATE `inprogress` SET `ERROR`= ? WHERE `PRODUCT_ID` = ?");
			$result->execute([$error,$JOBID]);
			
			$result = $conn->prepare("UPDATE `data` SET `note`= ? WHERE product_id = ?");  //".$FALCUTY."
			$result->execute(['fail',$JOBID]); //if(1)
				
		}else if($REQ == "step_out"){ //http://localhost/handleROBOT.php?BOT_NAME=BOT1&JOBID=4655&REQ=step_out&STATUS=working&LOCATION=sta1

			$LOCATION = $_GET['LOCATION'];
			$STATUS = $_GET['STATUS'];
			$JOBID = $_GET['JOBID'];
			
			//search  $LIST_POINT[];
			//i can go
			//echo
			$sbot_point =  array_search( $LOCATION , $LIST_POINT); //return key
			$forward_distance = $sbot_point + 4;
			$back_distance = $sbot_point - 5;

			//echo $sbot_point.":";
			//for($t=0 ;$t<count($LIST_POINT);$t++)
			//echo $LIST_POINT[$t];
			if($forward_distance > (count($LIST_POINT) - 2)){  //>= 23
				$forward_distance = $forward_distance - (count($LIST_POINT) - 2);
			}
			if($back_distance < 0){
				$back_distance = (count($LIST_POINT) - 2) - $back_distance;  //circle  sum distace - stanby robot - abs(2-5)
			}
			
			//echo 'a:'.$forward_distance.":b:".$back_distance;
			sleep(rand(1,2));
			$ack = "0";
			$sqlcom = "SELECT LOCATION FROM robot WHERE bot_name != '".$BOT_NAME."'";  //+online
			$result = $conn->prepare($sqlcom);
			$result->execute();
			if($result->rowCount() >= 1){
				while($rs = $result->fetch()){ ///pulse ตัวตามหลัง
			
					$another_bot = array_search($rs['LOCATION'] , $LIST_POINT); //max array size 0-23 = 23+1
					$ack = "1";
					//echo ":c:".$another_bot;
					if( $another_bot <= $forward_distance || $another_bot  >= $back_distance && $another_bot < (count($LIST_POINT) - 2)){ //i can go 2 point = 22
						$ack = "0";
						//echo $rs['LOCATION'].'yesnear'.$another_bot;
						break;
					}
					
				}

			}else{
				$ack = "1";
			}
			
			//sleep(rand(1,2));
			
			if($ack == "1"){
				//chk 
			$sqlcom = "SELECT LOCATION FROM robot WHERE bot_name != '".$BOT_NAME."'";  //+online
			$result = $conn->prepare($sqlcom);
			$result->execute();
					if($result->rowCount() >= 1){
						while($rs = $result->fetch()){ ///pulse ตัวตามหลัง
					
							$another_bot = array_search($rs['LOCATION'] , $LIST_POINT); //max array size 0-23 = 23+1
							$ack = "1";
							//echo ":c:".$another_bot;
							if( $another_bot <= $forward_distance  || $another_bot  >= $back_distance && $another_bot < (count($LIST_POINT) - 2)){ //i can go 2 point = 22
								$ack = "0";
								//echo $rs['LOCATION'].'yesnear'.$another_bot;
								break;
							}
							
						}
					}
					if($ack == "1"){
						$result = $conn->prepare('UPDATE `robot` SET `STATUS`= ?,`JOBID`= ?,`LOCATION`= ? WHERE `bot_name` = ?');
						$result->execute([$STATUS,$JOBID,$LOCATION,$BOT_NAME]);
					}else{
						$result = $conn->prepare('UPDATE `robot` SET `STATUS`= ?,`JOBID`= ?,`LOCATION`= ? WHERE `bot_name` = ?');
						$result->execute([$STATUS,$JOBID,$BOT_NAME,$BOT_NAME]);
					}
					//echo "ysegoooo>>>>";
			}

			$end_time = microtime(true);
			$execution_time = ($end_time - $start_time); 
			//echo $execution_time.'sec' ;
			echo $BOT_NAME.">canstep_out>".$ack;
			//echo $BOT_NAME.">cango>3";

		}

		
	}
catch(PDOException $e)
    {
    echo "Connection failed: " . $e->getMessage();
    }
	
	
	
	function has_job_point($point1 , $next_point , $onlynextp=0)
	{
		$p1 = explode(":",$point1);
		$p2 = explode(":",$next_point);
		//echo ">>>>>";
		//echo $p1[0].":".$p1[1];
		//echo $p2[0].":".$p2[1];
		//echo $p1[0] == $p2[0];
		//echo ":".$p1[1];
		//echo var_dump($p1[1]);
		//echo var_dump($p2[1]);
		//echo intval($p1[1]) > intval($p2[1]);
		//echo $int1 >= $int2;
		if($p1[0] == $p2[0] && intval($p1[1])+ $onlynextp <= intval($p2[1]) ) {
			
			return true;
		}
	
		return false;
	}
	
	function get_half_way($way)
	{
		$arrdst = explode(":",$way);
		$found_G = false;
		$newdst ="";
		for($i = 0 ; $i < count($arrdst) ; $i++){
			if(startsWith($arrdst [$i]  , "G" ) && !$found_G ){
				$newdst.= $arrdst [$i-1].':';
				$found_G = true;
			}
			if($found_G){
				$newdst.= $arrdst [$i].":";
			}
		}
		return substr($newdst , 0 ,strlen($newdst)-1);
	}
	
	function startsWith ($string , $startString) 
	{ 
		$len = strlen($startString); 
		return (substr($string, 0, $len) === $startString); 
	} 
	
?>

