<?php
	session_start();
	if(!isset($_SESSION['USERNAME'])){
		header('location:login.php');
		die();
	}
	include('headpage.php');
?>
<head>
	<title>Search Product</title>
</head>
<script>
$(document).ready(function(){
	$("#slot").change(function(){
		q_search ();
	});
	$("#faculty").change(function(){
		q_search ();	
	});
	$("#floor").change(function(){
		q_search ();
	});
});
function q_search (){
	var floor = $("#floor").val();
	var slot = $("#slot").val();
	var faculty = $("#faculty").val();
	$.getJSON('search_item_q.php',{"floor":floor,"slot":slot,"faculty":faculty},function(data){		
		var str = "<table class='table table-striped' id='fix'><tr><th>PRODUCT_ID</th><th>NAME</th><th>DETAIL</th><th>Faculty</th><th>Floor</th><th>Slot</th><th>Status</th></tr>";
		$.each(data, function(i, item){	
				str+="<tr>";
				str+="<td >"+item.PRODUCT_ID+"</td>";
				str+="<td >"+item.NAME+"</td>";
				str+="<td >"+item.DETAIL+"</td>";
				str+="<td>"+item.fac_name+"</td>";
				str+="<td>"+item.floor+"</td>";
				str+="<td>"+item.slot+"</td>";
				if(item.ON_STORAGE == 0){
					str+="<td>Empty</td>";
				}else{
					str+="<td>Full</td>";
				}
				str+="</tr>";
			
		});
		str+="</table>";
		$('#div1').html(str);
	});
	var btn = "<button type='button' class='btn btn-warning' onclick=edit()>แก้ไข</button>&ensp;";
	document.getElementById('btn').innerHTML = btn;
}
function accept_edit(){
	var e_name  = [];
	var e_detail = [];
	var e_pid = [];
	var e_name_txt = document.getElementsByClassName("e_name");
	var e_detail_txt = document.getElementsByClassName("e_detail");
	var e_pid_txt = document.getElementsByClassName("e_pid");
	var i ;
	for(i = 0 ; i < e_name_txt.length ; i++){
		//console.log(e_name_txt[i].value);
		e_name.push(e_name_txt[i].value);
		e_detail.push(e_detail_txt[i].value);
		e_pid.push(e_pid_txt[i].value);
	}
	var url = "edit_item.php?mode=save&e_name="+e_name+"&e_detail="+e_detail+"&e_pid="+e_pid ;
	xml(url);
	$('select').prop('selectedIndex', 0);
	var btn = "";
	btn += "<button type='button' class='btn btn-warning' onclick=edit()>แก้ไข</button>&ensp;";
	document.getElementById('btn').innerHTML = btn;
}
function edit(){
	clearInterval(myVar);
	var floor =document.getElementById('floor').value;
	var slot =document.getElementById('slot').value;
	var fac =document.getElementById('faculty').value;
	console.log(floor,slot,fac);
	var url =  "edit_item.php?mode=edit&floor="+floor+"&slot="+slot+"&fac="+fac ;
	xml(url);	
	var btn = "";
	btn += "<button type='button' class='btn btn-primary' onclick=accept_edit()>ยืนยัน</button>&ensp;";
	btn += "<button type='button' class='btn btn-danger' onclick=cancel()>ยกเลิก</button>&ensp;";
	document.getElementById('btn').innerHTML = btn;
}
function cancel(){
	var url = "edit_item.php?mode=cancel";
	xml(url);
	$('select').prop('selectedIndex', 0);
	var btn = "<button type='button' class='btn btn-warning' onclick=edit()>แก้ไข</button>&ensp;";
	document.getElementById('btn').innerHTML = btn;
	myVar = setInterval(myTimer, 1000);
}
function xml (url){
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			var str = this.responseText ; 
			document.getElementById('div1').innerHTML = str;
		}
	};
	xmlhttp.open("GET",url, true);
	xmlhttp.send();
}
var myVar = setInterval(myTimer, 1000);
	
function myTimer() {
	var floor =document.getElementById('floor').value;
	var slot =document.getElementById('slot').value;
	var fac =document.getElementById('faculty').value;
	xml("edit_item.php?mode=time&floor="+floor+"&slot="+slot+"&fac="+fac);
}
</script>
<center><h1>Search Product in Storage</h1></center>

<div class="container-fluid" >
	<div class="dropdown">	
		<b> Faculty : </b>
		<select class="btn btn-default dropdown-toggle" name="faculty" id="faculty">
			<option value="0">--ALL--</option>
			<?php
				$sql = "select * from faculty";
				foreach($con->query($sql) as $row){
					echo "<option value=".$row['fac_id'].">".$row['fac_name']."</option>";
				}
			?>
		</select>
		<b> floor : </b>
		<select class="btn btn-default dropdown-toggle" name="floor" id="floor">
			<option value="0">--ALL--</option>
			<option value="1">--1--</option>
			<option value="2">--2--</option>
		</select>
		<b> Slot : </b>
		<select class="btn btn-default dropdown-toggle" name="slot" id="slot">
			<option value="0">--ALL--</option>
			<option value="1">--1--</option>
			<option value="2">--2--</option>
			<option value="3">--3--</option>
		</select>
	</div>
	<br>
<div class='container-fluid' id="div1">
	<table class="table table-striped" id='fix'>
		<tr>
			<th>PRODUCT_ID</th><th>NAME</th><th>DETAIL</th><th>Faculty</th><th>Floor</th><th>Slot</th><th>Status</th>
		</tr>
	<?php
		$sql = "select * from storage join faculty ON storage.fac_id = faculty.fac_id 
				order by `faculty`.`fac_name` , storage.floor , storage.slot ASC";
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
			
	?>
	</table>		
</div>		
<?php if($_SESSION["SPAECIAL"] =='ALL'){?>
<center id='btn'>
		<button type="button" class="btn btn-warning" onclick='edit()'>แก้ไข</button>
	</center> 
<?php } ?>
<br><center><button class="btn btn-danger" onclick="windowClose()"> ปิดหน้านี้  </button></center>
</div>	