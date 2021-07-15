<?php
	session_start();
	if($_SESSION["SPAECIAL"] !='ALL'){
		header("Location: adminpage.php");
		die();
}
	include('headpage.php');
?>
<title>ลบผู้ใช้งาน</title>
<style>
th ,td{
	text-align:center;
}
</style>
<body>
<div class="container-fluid" >
<h3 style="text-align:center;">Manage User</h3>
	<br>
<div id="div1" class="table-responsive">
	<table class="table table-striped"  id='fix'>
	<tr>
		<th>Username</th><th>ตำแหน่งงาน</th><th>สถานะ</th><?php if( $_SESSION["SPAECIAL"] =='ALL') {?> <th>delete user</th><th>edit user</th><?php 	}?>
	</tr>
	<?php 
		$con = new PDO("mysql:host=localhost;dbname=project;charset=utf8","root","");
		$sql = "select *  from user  JOIN faculty ON user.Fac_id =faculty.fac_id WHERE 1 order by user.USER_ID asc";
		foreach($con->query($sql) as $i){
			echo "<tr><td>".$i['Name']."</td><td>".$i['fac_name']."</td><td>".$i['SPAECIAL']."</td>";	
			if( $_SESSION["SPAECIAL"] =='ALL') {
				
				?>	
		<th>
			<a onclick="return confirm('Press a button!');" href='delete.php?id=<?= $i['USER_ID']?>'>
				<button  type="button" class="btn btn-danger">
					<span class="glyphicon glyphicon-trash" ></span>
				</button>
			</a>
		</th>
		<th >
			<a  href='edit_user.php?id=<?= $i['USER_ID']?>&fac=<?=$i["fac_id"]?>'>
				<button  type="button" class="btn btn-warning">
					<span class="glyphicon glyphicon-edit" ></span>
				</button>
			</a>
		</th>
	<?php 	}
		?></tr><?php
		}?>
	</table>
</div>		 
</div>	
<center><a href="register.php"  target="_blank"><button class="btn btn-primary" >  เพิ่มผู้ใช้  </button></a>
<br><br><center><button class="btn btn-danger" onclick="windowClose()"> ปิดหน้านี้  </button></center>
</body>