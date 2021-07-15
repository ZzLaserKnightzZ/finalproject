<?php      
	session_start();
	include('headpage.php');
	if($_SESSION["SPAECIAL"] !='ALL'){
		header('location:login.php');
		die();
	}
	$id=$_GET['id'];
	$fac=$_GET['fac'];
	
?>
<!doctype html>
<head>
	<title>ลงทะเบียน</title>
	
</head>

<html> 
<body ><br><br>
<?php
    if(isset($_SESSION['error'])){
?>
	<div class="row">
		<div class="col-md-offset-3 col-md-6">
			<div class="alert alert-danger">
				<?php echo $_SESSION['error'];?>
            </div>
		</div>
	</div>
<?php
		unset($_SESSION['error']);
	}
        else if (isset($_SESSION['success'])){
?>
    <div class="row">
		<div class="col-md-offset-3 col-md-6">
			<div class="alert alert-success">
				<?php echo $_SESSION['success'];?>
            </div>
		</div>
	</div>
<?php
		unset($_SESSION['success']);
        }
		
?>		
			
<div class="row">
	<div class="col-md-offset-3 col-md-6">
		<div class="panel panel-primary">
				<div class="panel-heading"> เพิ่มบัญชีผู้ใช้งานระบบ </div>
			<div class="panel-body">
				<div class="row">
					<div class="col-md-12">
						<form class="form-horizontal" action="edit_save.php" method="post">
                            <div class="form-group">
								<label class="control-label col-sm-3" for="login">ชื่อบัญชี : </label>
								<input type='hidden' value='<?=$id?>' name='id'>
						<?php
							$con = new PDO("mysql:host=localhost;dbname=project;charset=utf8", "root", "");
							$sqlcom = "SELECT * FROM user WHERE USER_ID = $id";
							$result = $con->query($sqlcom);
							if($result->rowCount() == 1){
								$data = $result->fetch(PDO::FETCH_ASSOC);
							
						?>
								<div class="col-sm-9">          
									<input type="login" class="form-control"  value="<?=$data['USERNAME']?>"  name="login">
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3" for="name">ชื่อ-นามสกุล :</label>
								<div class="col-sm-9">          
									<input type="name" class="form-control" value="<?=$data['Name']?>" name="name">
								</div>
							</div>
						<?php 
							}
						?>
							<div class="form-group">
								<label class="control-label col-sm-3" for="name">แผนก</label>
								<div class="col-sm-3">          
									<?php
										$con = new PDO("mysql:host=localhost;dbname=project;charset=utf8","root","");
										$sql = "select * from faculty";
										foreach($con->query($sql) as $row){
											if($row['fac_id'] == $fac){
												echo "<input type='radio'   name='faculty' value=".$row['fac_id']." checked>".$row['fac_name']." <br>";
											}else{
												echo "<input type='radio'   name='faculty' value=".$row['fac_id']." >".$row['fac_name']." <br>";
											}
											
									}?>
									
								</div>
							</div>
							<div class="form-group" style="text-align:center">    
								<button type="submit" class="btn btn-primary"><span class='glyphicon glyphicon-floppy-disk'></span> แก้ไข </button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<center><button class="btn btn-danger" onclick="windowClose()"> ปิดหน้านี้  </button></center>
</body>
</html>