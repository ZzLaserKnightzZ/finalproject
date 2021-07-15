<?php include('headpage.php');
$mode = $_GET['mode'];
function qrobot(){
	$con = new PDO("mysql:host=localhost;dbname=project;charset=utf8","root","");
	$sql = 'select * from robot';
		foreach($con->query($sql) as $row){
			echo "<tr>";
			echo "<th class='a' ><input type='checkbox' class='name' value=".$row['bot_name']."></th>";
			echo "<td >".$row['bot_name']."</td>
				  <td>".$row['DST_IN']."</td>
				  <td>".$row['DST_OUT']."</td>
				  <td>".$row['JOBID']."</td>
				  <td>".$row['STATUS']."</td>
				  <td>".$row['LOCATION']."</td>";
			if($row['ONLINE'] == 0){
				echo "<td>Offline</td>";
			}else{
				echo "<td>Online</td>";
			}	
			echo "</tr>";
		}
}
?>
<table class='table table-striped'  id='fix'>
		<tr>
			<th id='box'>select bot</th><th>NAME</th><th>DST_IN</th><th>DST_OUT</th><th>JOB_ID</th><th>STATUS</th><th>LOCATION</th><th>Online-Offline</th>
		</tr>
	<?php
	if($mode == 'edit'){
		$sql = 'select * from robot';
		foreach($con->query($sql) as $row){
			echo "<tr>";
			echo "<th class='a' ><input type='checkbox' class='name' value=".$row['bot_name']."></th>";
			echo "<td ><input type='text' class='e_name' value=".$row['bot_name']."> </td>
				  <td><input type='text' class='e_dstin' value=".$row['DST_IN']." </td>
				  <td><input type='text' class='e_dstout' value=".$row['DST_OUT']." </td>
				  <td>".$row['JOBID']."</td>
				  <td>".$row['STATUS']."</td>
				  <td>".$row['LOCATION']."</td>";
			if($row['ONLINE'] == 0){
				echo "<td>Offline</td>";
			}else{
				echo "<td>Online</td>";
			}		  
			echo "</tr>";
		}
	}else if($mode == 'save'){
		$NAME = array();
		$sql = 'select bot_name from robot';
		foreach($con->query($sql) as $row){
			array_push($NAME,$row['bot_name']);
		}
		//print_r($NAME);
		$e_name = array();
		$e_name = explode(",",$_GET['e_name']);
		$e_dstin = array();
		$e_dstin = explode(",",$_GET['e_dstin']);
		$e_dstout = array();
		$e_dstout = explode(",",$_GET['e_dstout']);		
		for($i = 0 ; $i < count($e_name) ; $i++){
			//echo $e_name[$i]." | ".$e_dstin[$i]." | ".$e_dstout[$i]."<br>";
			$sql = "update robot set NAME='$e_name[$i]'
			,DST_IN='$e_dstin[$i]'
			,DST_OUT='$e_dstout[$i]' 
			where bot_name = '$bot_name[$i]'";
			$con->exec($sql);
			// if($con->exec($sql) == 0){
			// echo "error";
		// }else {
			// echo "OK";
		//}
				
			
		}
		//echo $e_name." | ".$e_dstin." | ".$e_dstout;
		qrobot();
		
	}else if($mode == 'add'){
		qrobot();
		echo "<tr>
				<td><input type='text' name='NAME' size = 2 id='names'></td>
				<td><input type='text' name='DST_IN' size = 4  id='dstin'></td>
				<td><input type='text' name='DST_OUT' size = 4  id='dstout'></td>
				<td><input type='text' name='JOBID' value='none' disabled size = 2></td>
				<td><input type='text' name='STATUS' value='STANDBY' disabled size = 6></td>
				<td><input type='text' name='LOCATION' size = 2 id='location'></td>
				<td><input type='text' name='ONLINE' value= 0 size = 2 id='online' disabled></td>
			</tr>";
	}else if($mode == 'cancel'){
		qrobot();
	}else if($mode == 'delete'){
		$name =  $_GET['name'];
		$str = array();
		$str = explode(',',$name);
		for($i = 0 ; $i < count($str) ; $i++){
			//echo $str[$i]."<br>";
			$sql = "DELETE FROM `robot` WHERE bot_name='$str[$i]'";
			$con->exec($sql);
			/*if($con->exec($sql) == 0){
				echo "error";
			}else {
				echo "OK";
			}*/
		}
		qrobot();
	}else if($mode == 'update'){
		$name = $_GET['names'];
		$dstin = $_GET['dstin'];
		$dstout = $_GET['dstout'];
		$location = $_GET['location'];
		//$online = $_GET['online'];
		$sql = "insert into robot (bot_name,DST_IN,DST_OUT,JOBID,STATUS,LOCATION)
						values ('$name','$dstin','$dstout','none','STANDNY','$location')";
						//echo $sql;
		if($con->exec($sql)== 0){
			qrobot();
		}else{
			qrobot();
		}
	}
		?>
</table>