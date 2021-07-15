<?php
	session_start();
	if(!isset($_SESSION['USERNAME'])){
		header('location:login.php');
		die();
	}
	include('headpage.php');
	$mode = $_GET['mode'];
?>
<table class="table table-striped" id='fix'>
		<tr>
			<th>PRODUCT_ID</th><th>NAME</th><th>DETAIL</th><th>Faculty</th><th>Floor</th><th>Slot</th><th>Status</th>
		</tr>
	<?php
	if($mode == 'edit'){
		//echo $_GET['floor'].$_GET['slot'].$_GET['fac'];
		$floor = $_GET['floor'];
		$slot =  $_GET['slot'];
		$faculty = $_GET['fac'];
		if($floor == 0 && $slot == 0 && $faculty == 0){
			$sql = "select * from storage join faculty ON storage.fac_id = faculty.fac_id 
					order by `faculty`.`fac_name` , storage.floor , storage.slot ASC";
	   }else if($floor == 0 && $slot == 0 && $faculty != 0){ 
			$sql = "select * from storage join faculty ON storage.fac_id = faculty.fac_id 
					where faculty.fac_id = $faculty 
					order by `faculty`.`fac_name` , storage.floor  , storage.slot ASC";
	   }else if($floor == 0 && $slot != 0 && $faculty == 0){ 
			$sql = "select * from storage join faculty ON storage.fac_id = faculty.fac_id 
					where storage.slot=$slot 
					order by `faculty`.`fac_name` , storage.floor  , storage.slot ASC";
	   }else if($floor == 0 && $slot != 0 && $faculty != 0){ 
			$sql = "select * from storage join faculty ON storage.fac_id = faculty.fac_id 
					where storage.slot=$slot AND faculty.fac_id = $faculty 
					order by `faculty`.`fac_name` , storage.floor  , storage.slot ASC";
	   }else if($floor != 0 && $slot == 0 && $faculty == 0){ 
			$sql = "select * from storage join faculty ON storage.fac_id = faculty.fac_id 
					where storage.floor=$floor 
					order by `faculty`.`fac_name` , storage.floor  , storage.slot ASC";
	   }else if($floor != 0 && $slot == 0 && $faculty != 0){ 
			$sql = "select * from storage join faculty ON storage.fac_id = faculty.fac_id 
					where storage.floor=$floor  AND faculty.fac_id = $faculty 
					order by `faculty`.`fac_name` , storage.floor  , storage.slot ASC";
	   }else if($floor != 0 && $slot != 0 && $faculty == 0){ 
			$sql = "select * from storage join faculty ON storage.fac_id = faculty.fac_id 
					where storage.floor=$floor AND storage.slot=$slot
					order by `faculty`.`fac_name` , storage.floor  , storage.slot ASC";
	   }else if($floor != 0 && $slot != 0 && $faculty != 0){ 
			$sql = "select * from storage join faculty ON storage.fac_id = faculty.fac_id 
					where storage.floor=$floor AND storage.slot=$slot AND faculty.fac_id = $faculty 
					order by `faculty`.`fac_name` , storage.floor  , storage.slot ASC";
	   }
		foreach($con->query($sql) as $row){
				echo "<tr>";
				echo "<td ><input class = 'e_pid' type = 'number' value='".$row['PRODUCT_ID']."' min = '0' max = '999999999999'></td>
					  <td ><input class = 'e_name' type = 'text' value='".$row['NAME']."' size = 12></td>
					  <td ><input class = 'e_detail' type = 'datetime-locale' ></td>	
					  <td>".$row['fac_name']."</td>
					  <td>".$row['floor']."</td>
					  <td>".$row['slot']."</td>";
					   if($row['ON_STORAGE'] == 0){
						echo "<td>Empty</td>";
					  }else{
						echo "<td>Full</td>";
					  }
				echo "</tr>";
			}
	}else if($mode == 'cancel'){
		$sql = "select * from storage join faculty ON storage.fac_id = faculty.fac_id 
				order by `faculty`.`fac_name` , storage.floor , storage.slot ASC";
		sql($sql);
	}else if($mode == 'save'){
		$old_pid = array();
		$sql = "select * from storage join faculty ON storage.fac_id = faculty.fac_id 
				order by `faculty`.`fac_name` , storage.floor , storage.slot ASC";
		foreach($con->query($sql) as $row){
				array_push($old_pid,$row['PRODUCT_ID']);
		}
		$e_name = array();
		$e_name = explode(",",$_GET['e_name']);
		$e_detail = array();
		$e_detail = explode(",",$_GET['e_detail']);
		$e_pid = array();
		$e_pid = explode(",",$_GET['e_pid']);	
		for($i = 0 ; $i < count($e_name) ; $i++){
			//echo $e_name[$i]." | ".$e_dstin[$i]." | ".$e_dstout[$i]."<br>";
			$sql = "update storage set 
					NAME='$e_name[$i]'
					,PRODUCT_ID='$e_pid[$i]'
					,DETAIL='$e_detail[$i]' 
					where ID = ".($i+1)."";
			//$con->exec($sql);
			if($con->exec($sql) == 0){
				$_SESSION['error'] = 'update incomplete !!';
			}else {
				$_SESSION['complete'] = 'update complete !!';;
			}
		}
		$sql = "select * from storage join faculty ON storage.fac_id = faculty.fac_id 
				order by `faculty`.`fac_name` , storage.floor , storage.slot ASC";
		sql($sql);
	}else if($mode == 'time'){
		$floor = $_GET['floor'];
		$slot =  $_GET['slot'];
		$faculty = $_GET['fac'];
		if($floor == 0 && $slot == 0 && $faculty == 0){
			$sql = "select * from storage join faculty ON storage.fac_id = faculty.fac_id 
					order by `faculty`.`fac_name` , storage.floor , storage.slot ASC";
	   }else if($floor == 0 && $slot == 0 && $faculty != 0){ 
			$sql = "select * from storage join faculty ON storage.fac_id = faculty.fac_id 
					where faculty.fac_id = $faculty 
					order by `faculty`.`fac_name` , storage.floor  , storage.slot ASC";
	   }else if($floor == 0 && $slot != 0 && $faculty == 0){ 
			$sql = "select * from storage join faculty ON storage.fac_id = faculty.fac_id 
					where storage.slot=$slot 
					order by `faculty`.`fac_name` , storage.floor  , storage.slot ASC";
	   }else if($floor == 0 && $slot != 0 && $faculty != 0){ 
			$sql = "select * from storage join faculty ON storage.fac_id = faculty.fac_id 
					where storage.slot=$slot AND faculty.fac_id = $faculty 
					order by `faculty`.`fac_name` , storage.floor  , storage.slot ASC";
	   }else if($floor != 0 && $slot == 0 && $faculty == 0){ 
			$sql = "select * from storage join faculty ON storage.fac_id = faculty.fac_id 
					where storage.floor=$floor 
					order by `faculty`.`fac_name` , storage.floor  , storage.slot ASC";
	   }else if($floor != 0 && $slot == 0 && $faculty != 0){ 
			$sql = "select * from storage join faculty ON storage.fac_id = faculty.fac_id 
					where storage.floor=$floor  AND faculty.fac_id = $faculty 
					order by `faculty`.`fac_name` , storage.floor  , storage.slot ASC";
	   }else if($floor != 0 && $slot != 0 && $faculty == 0){ 
			$sql = "select * from storage join faculty ON storage.fac_id = faculty.fac_id 
					where storage.floor=$floor AND storage.slot=$slot
					order by `faculty`.`fac_name` , storage.floor  , storage.slot ASC";
	   }else if($floor != 0 && $slot != 0 && $faculty != 0){ 
			$sql = "select * from storage join faculty ON storage.fac_id = faculty.fac_id 
					where storage.floor=$floor AND storage.slot=$slot AND faculty.fac_id = $faculty 
					order by `faculty`.`fac_name` , storage.floor  , storage.slot ASC";
	   }
		sql($sql);
	}
function sql($sql){
	 $con = new PDO("mysql:host=localhost;dbname=project;charset=utf8","root","");
	foreach($con->query($sql) as $row){
				echo "<tr>";
				echo "<td >".$row['PRODUCT_ID']."</td>
					  <td >".$row['NAME']."</td>
					  <td >".$row['DETAIL']."</td>
					  <td>".$row['fac_name']."</td>
					  <td>".$row['floor']."</td>
					  <td>".$row['slot']."</td>";
					   if($row['ON_STORAGE'] == 0){
						echo "<td>Empty</td>";
					  }else{
						echo "<td>Full</td>";
					  }
				echo "</tr>";
			}
		
}
	?>
	</table>