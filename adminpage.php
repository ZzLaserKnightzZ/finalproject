<?php
header("Access-Control-Allow-Origin: * ");
header("Catch-Control: no-catch");
session_start();
if( !isset($_SESSION["USERNAME"]) && !isset($_SESSION["FALCUTY"]) && !isset($_SESSION["SPAECIAL"]) ) {
			echo "<script>confirn(' มีคน login จากเครื่องอื่น ');</script>";
			header("Location: login.php");
			die();
		}
?>



<!DOCTYPE html>
<html>

<head>

	<meta charset="UTF-8">
	<title>admin</title>
	<link rel="icon" href="favicon.png">
	<link rel='stylesheet' type='text/css' href='style.css' >
	<script type="text/javascript" src='ajax.js'></script>
	<script type="text/javascript" src='sniper.js'></script> 
	<script type="text/javascript" src='admin.js'></script>  
	
	<script type="text/javascript" src='canvasJS.js'></script> 
	<script type="text/javascript" src='qjson.js'></script> 
	<script type="text/javascript" src='main.js'></script> 
	<link rel='stylesheet' type='text/css' href='control.css' >
	<?php
		//include all
		$MAP_DATA = 'jsmap';
		require_once('map.php');
	?>
</head>

<body>

		<button type = 'button'  id='btnnoti' onclick='show_notification()'> view notification <span class='badge' id='alert_note' > 1 </span> </button>
		<button id='btnoption' onclick='openNav()'>option</button>
		<button id='btnnext' onclick='next_floor()'>next floor</button>
		
		<div id='mySidenav' class='sidenav'>
			<t onclick='closeNav()'> &times;	</t><t><?=$_SESSION['USERNAME']?></t> <br>
			<t ><a href='logout.php' >log out</a>		</t> <br>
			<?php if($_SESSION["SPAECIAL"] =='ALL'){?>
			<t ><a href='search_item.php' target="_blank">Search Item</a></t> <br>
			<t ><a href='delete_user.php' target="_blank">Manage user</a></t> <br>
			<t ><a href='manage_bot.php' target="_blank">Manage robot</a></t> <br>
			
			<?php }?>
		</div>
		
		<div id='notification' >
			<t onclick='hide_notification()'> &times;</t> 
			<t onclick='clear_notification()'> clear </t> 
		</div>
		

		<div id='boxout' >
    		 <center>
			  <br>
			  <br>
    		  <b>DETAIL</b>
			  <div id = 'detail'> </div>
			  ID:<input type="text" id="idText">
			  NAME:<input type="text" id="nameText">
			  DETAIL:<input type="text" id="detialText">
			  <input type = 'button' value='edite' id='edite_item'>
    		  <hr>

    		  <b>OUT:</b>
    		      <select id="op_sent">
                      <option  value="1">station 1</option>
                      <option value="2">station 2</option>
                  </select>
              <input type = 'button' value='out' id='btn_out'>
              
              <input type = 'button'   class='closebtn'  style="font-size : 30px"; value='close' onclick='hide_boxout()'>
            </center>
		</div>
		
		
		<div id='boxin' >
			<center>
				<br>
				<br>
				ADD ITEM
				<br>
				<br>
		        NAME: <input type='text' id='NAME'>
		        <br>
		        ID: <input type='text' id = 'ID'> 
		        <br>
		        DETAIL: <input type='date' value="<?php echo date('Y-m-d'); ?>" id = 'DETAIL' >
		        <br>
    		    <select id="op_additem">
                      <option  value="1">station 1</option>
                      <option value="2">station 2</option>
                </select>
                
                <input type = 'button' value='addItem' id='btn_additem'>
                
                <input type = 'button' class='closebtn' style="font-size : 30px"; value='close' onclick='hide_boxin()' >

            </center>
		</div>



		<noscript ><h1><center><span style="color:red">Your browser does not support JavaScript!</span></center></h1></noscript> 
		
		<canvas id='can'><span style="color:red"> Your browser does not support Web GL! </span></canvas>
		

		
		

	
	
</body>