<?php

session_start();
	if(isset($_SESSION["USERNAME"]) && isset($_SESSION["FALCUTY"]) && isset($_SESSION["SPAECIAL"])){
	header("Location:adminpage.php");
	die();
}


?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>login</title>
	<script  src='logincapchar1.js'> </script>
	<script  src='sniper.js'> </script>
	<style>
		body{
			margin:0;
			background-color:rgba(0,0,150,0.6);
			
		}
		.login{
			margin:15% 30% 0 30%;
			background-color:gray;
			resize: vertical;
			overflow: auto;
		}
		.login p:first-child{
			color:lime;
		}

		h3{
			color:lime;
		}
	</style>
</head>

<body>
<noscript ><h1><center><span style="color:red">Your browser does not support JavaScript!</span></center></h1></noscript>
<div class='login'>	<center>
<form action="handleLOGIN.php" method='POST'>
	<p> 	 <h3>LOGIN</h3></p>
	<p> 	</p>
	<p>		USERNAME:  <input type='text' name='USERNAME'> </p>
	<p>     PASSWORD:  <input type='password' name='PASSWORD'> </p>
	<p>		<canvas id='capchar' > </canvas></p>
	<p>		<input type='text' id='verify' onchange='change()'> &nbsp&nbsp&nbsp <input type='button' value='refrash' onclick='draw()'> </p>
	<p>     <input type='submit' value='login' id='login' onclick='clear()'>  </p>
</form>
</center>
<?php
	if(isset($_SESSION['ALERT'])){
		echo "<script>alert('Login incorrect!!');</script>";
		echo "<script>console.log('".$_SESSION['ALERT']."');</script>";
		unset($_SESSION['ALERT']);
	}
?>
</div>	
	
	
</body>

</html>