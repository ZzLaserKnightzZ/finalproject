<?php
	include('headpage.php');
	//session_start();
	if(!isset($_SESSION['USERNAME']) && $_SESSION['SPAECIAL']!='ALL'){
		//header('location:adminpage.php');
		//die();
		echo $_SESSION["USERNAME"].$_SESSION["SPAECIAL"];
	}
?>
<script>
	//var myVar = setInterval(myTimernoti, 500);
	
function myTimernoti() {
	var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				var str = this.responseText ; 
				document.getElementById('div2').innerHTML = str;
			}
		};
		xmlhttp.open("GET", "noti_delete.php?mode=time", true);
		xmlhttp.send();
}
function delete_inprogress(product_id,mode){
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				var str = this.responseText ; 
				document.getElementById('div2').innerHTML = str;
			}
		};
		xmlhttp.open("GET", "noti_delete.php?name="+product_id+"&mode="+mode, true);
		xmlhttp.send();
}
</script>
<body>
<div class="table-responsive" id='div2'>
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
		<th width="10%">SENT</th>
		<th width="10%">STORAGE_ID</th>
		<th width="10%">PRODUCT_ID</th>
		<th width="10%">LINE_NAME</th>
		<th width="6%">NAME</th>
		<th width="6%">DETAIL</th>
		<th width="12%">CURRENT_LOC</th>
		<th width="15%">ERROR</th>
		
	</tr>
<?php
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
		<td width='10%'>".$row['SENT']."</td>
		<td width='10%'>".$row['STORAGE_ID']."</td>
		<td width='10%'>".$row['PRODUCT_ID']."</td>
		<td width='10%'>".$row['LINE_NAME']."</td>
		<td width='6%'>".$row['NAME']."</td>
		<td width='6%'>".$row['DETAIL']."</td>
		<td width='12%'>".$row['CURRENT_LOC']."</td>
		<td width='15%'>".$row['ERROR']."</td>
		
	</tr>";
	}
?>
	</table>
	</div>
</body>