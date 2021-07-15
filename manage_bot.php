<?php
	session_start();
	if($_SESSION["SPAECIAL"] !='ALL'){
		header('location:login.php');
		die();
	}
	include('headpage.php');
?>
<head>
	<title>Manage Robot</title>

</head>
<center><h1>Manage Robot</h1></center>
<div class="container-fluid" >
	<br>
	<div class='container-fluid' id="div1">
	<table class='table table-striped' id='fix'>
		<tr>
			<th id='box'>select bot</th><th>NAME</th><th>DST_IN</th><th>DST_OUT</th><th>JOB_ID</th><th>STATUS</th><th>LOCATION</th><th>Online-Offline</th>
		</tr>
	<?php
		$sql = 'select * from robot';
		foreach($con->query($sql) as $row){
			echo "<tr>";
			echo "<th class='a' ><input type='checkbox' class='name' value=".$row['bot_name']."></th>";
			echo "<td>".$row['bot_name']."</td>
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
	?>
	</table>
</div>	
<center id='btn'>
		<button type="button" class="btn btn-primary" onclick=add()>เพิ่ม</button>&ensp;
		<button type="button" class="btn btn-danger" onclick=delete_bot()>ลบ</button>&ensp;
		<button type="button" class="btn btn-warning" onclick=edit()>แก้ไข</button>
</center><br><br>
<center><h1>Notifacation Robot</h1></center>
<?php include('noti.php');?>
<br><center><button class="btn btn-danger" onclick="windowClose()"> ปิดหน้านี้  </button>
</center>
</div>	
<script>
var myVar = setInterval(myTimer, 1000);
	
function myTimer() {
	myTimernoti();
	xml("edit_bot.php?mode=cancel");
}
	function edit(){
		clearInterval(myVar);
		xml("edit_bot.php?mode=edit");
		var btn = "";
		btn += "<button type='button' class='btn btn-primary' onclick=accept_edit()>ยืนยัน</button>&ensp;";
		btn += "<button type='button' class='btn btn-danger' onclick=cancel()>ยกเลิก</button>&ensp;";
		
		document.getElementById('btn').innerHTML = btn;
	}
	function accept_edit(){
		var e_name  = [];
		var e_dstin = [];
		var e_dstout = [];
		var e_name_txt = document.getElementsByClassName("e_name");
		var e_dstin_txt = document.getElementsByClassName("e_dstin");
		var e_dstout_txt = document.getElementsByClassName("e_dstout");
		//console.log(e_name.length+" "+e_dstin.length+" "+e_dstout.length);
		var i ;
		for(i = 0 ; i < e_name_txt.length ; i++){
			//console.log(e_name_txt[i].value);
			e_name.push(e_name_txt[i].value);
			e_dstin.push(e_dstin_txt[i].value);
			e_dstout.push(e_dstout_txt[i].value);
		}
		xml("edit_bot.php?mode=save&e_name="+e_name+"&e_dstin="+e_dstin+"&e_dstout="+e_dstout);
		btn();
	}
	function btn(){
		var btn = "";
		btn += "<button type='button' class='btn btn-primary' onclick=add()>เพิ่ม</button>&ensp;";
		btn += "<button type='button' class='btn btn-danger' onclick=delete_bot()>ลบ</button>&ensp;";
		btn += "<button type='button' class='btn btn-warning' onclick=edit()>แก้ไข</button>&ensp;";
		document.getElementById('btn').innerHTML = btn;
		myVar = setInterval(myTimer, 1000);	
	}
	function delete_bot(){
		clearInterval(myVar);
		var box = document.getElementById("box");
		box.style.display = "block";
		//document.getElementsByClassName("a").ClassName = 'show' ;
		var x = document.getElementsByClassName("a");
		var i ;
		//console.log(x.length);
		for(i=0;i<x.length;i++){
			x[i].style.display = 'block';
		}
		var btn = "";
		btn += "<button type='button' class='btn btn-primary' onclick=accept_delete()>ยืนยัน</button>&ensp;";
		btn += "<button type='button' class='btn btn-danger' onclick=cancel()>ยกเลิก</button>&ensp;";
		
		document.getElementById('btn').innerHTML = btn;
	}
	function accept_delete(){
		var name_array = [] ;
		document.getElementById("box").style.display = "none";
		var name = document.getElementsByClassName("name");
		var x = document.getElementsByClassName("a");
		var i ;
		for(i=0;i<name.length;i++){
			//console.log(name[i].value);
			if(name[i].checked == true){
				name_array.push(name[i].value);
			}
			
			x[i].style.display = 'none';
		}
		//console.log(name_array);
		xml("edit_bot.php?mode=delete&name="+name_array);
		btn();
	}
	function add(){
		clearInterval(myVar);
		var str = "";
		var btn1 = "";
		btn1 += "<button type='button' class='btn btn-primary' onclick=update()>ยืนยัน</button>&ensp;";
		btn1 += "<button type='button' class='btn btn-danger' onclick=cancel()>ยกเลิก</button>&ensp;";
		document.getElementById('btn').innerHTML = btn1;
		url =  "edit_bot.php?mode=add";
		xml(url);
	}
	function cancel(){
		xml("edit_bot.php?mode=cancel");
		btn();
	}
	function update(){
		var name = document.getElementById('names').value;
		var dstin = document.getElementById('dstin').value;
		var dstout = document.getElementById('dstout').value;
		var location = document.getElementById('location').value;
		//console.log("<"+name+">"+"<"+dstin+">"+"<"+dstout+">");
		url =  "edit_bot.php?mode=update&names="+name+"&dstin="+dstin+"&dstout="+dstout+"&location="+location;
		xml(url);
		btn();
	}
	function xml(url){
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				var str = this.responseText ; 
				document.getElementById('div1').innerHTML = str;
			}
		};
		xmlhttp.open("GET", url , true);
		xmlhttp.send();
	}
</script>