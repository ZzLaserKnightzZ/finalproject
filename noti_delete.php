<?php include('headpage.php');
?>
<table class='table table-striped' width="2000" cellspacing="0" cellpadding="0" >
	<tr>
		<th width="3%">DELETE</th>
		<th width="3%">RESET</th>
		<th width="3%">SET Y</th>
		<th width="4%">DONE</th>
		<th width="3%">PAY</th>
		<th width="13%">BY_ROBOT_NAME</th>
		<th width="8%">STO_NAME</th>
		<th width="11%">RECIEVE</th>
		<th width="20%">DST</th>
		<th width="5%">SENT</th>
		<th width="10%">STORAGE_ID</th>
		<th width="10%">PRODUCT_ID</th>
		<th width="10%">LINE_NAME</th>
		<th width="6%">NAME</th>
		<th width="6%">DETAIL</th>
		<th width="10%">CURRENT_LOC</th>
		<th width="15%">ERROR</th>
		
	</tr>
<?php
	$mode = $_GET['mode'];
	
	if($mode == 'delete'){
		$name =  $_GET['name'];
		$sql = "DELETE FROM `inprogress` WHERE STORAGE_ID='$name'";
		$con->exec($sql);
		// if($con->exec($sql) == 0){
			// echo "error";
		// }else {
			// echo "OK";
		// }
	}else if($mode == 'reset'){
		$name =  $_GET['name'];
		$sql = "update inprogress set PAY = 'N' , DONE = 'N' where STORAGE_ID = '$name'";
		$con->exec($sql);
		// if($con->exec($sql) == 0){
			// echo "error";
		// }else {
			// echo "OK";
		// }
	}else if($mode == 'set_y'){
		$name =  $_GET['name'];
		$sql = "update inprogress set PAY = 'Y' , DONE = 'Y' where STORAGE_ID = '$name'";
		$con->exec($sql);
		// if($con->exec($sql) == 0){
			// echo "error";
		// }else {
			// echo "OK";
		// }
	}
	
	$sql = "select * from inprogress ";
	foreach($con->query($sql) as $row){
		echo "<tr>
		<th width='3%'><button  type='button' class='btn btn-danger' onclick=delete_inprogress('".$row['STORAGE_ID']."','delete')><span class='glyphicon glyphicon-trash' ></span></button></th>
		<th width='3%'><button  type='button' class='btn btn-warning' onclick=delete_inprogress('".$row['STORAGE_ID']."','reset')><span class='glyphicon glyphicon-edit' ></span></button></th>
		<th width='3%'><button  type='button' class='btn btn-primary' onclick=delete_inprogress('".$row['STORAGE_ID']."','set_y')><span class='glyphicon glyphicon-save' ></span></button></th>
		<td width='4%'>".$row['DONE']."</td>
		<td width='3%'>".$row['PAY']."</td>
		<td width='13%'>".$row['BY_ROBOT_NAME']."</td>
		<td width='8%'>".$row['STO_NAME']."</td>
		<td width='11%'>".$row['RECIEVE']."</td>
		<td width='20%'>".$row['DST']."</td>
		<td width='5%'>".$row['SENT']."</td>
		<td width='10%'>".$row['STORAGE_ID']."</td>
		<td width='10%'>".$row['PRODUCT_ID']."</td>
		<td width='10%'>".$row['LINE_NAME']."</td>
		<td width='6%'>".$row['NAME']."</td>
		<td width='6%'>".$row['DETAIL']."</td>
		<td width='10%'>".$row['CURRENT_LOC']."</td>
		<td width='15%'>".$row['ERROR']."</td>
		
	</tr>";
	}
?>
</table>