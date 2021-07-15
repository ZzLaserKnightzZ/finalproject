<?php

session_start();
if(!isset($_SESSION["USERNAME"]) && !isset($_SESSION["FALCUTY"])  ){
	header("Location: login.php");
}

?>

<!DOCTYPE html>
<html>
	<head>
		<title>Page Title</title>
	</head>
	<style>
	body {
		
		margin:0;
		background-image: -webkit-linear-gradient(left, hsl(200, 100%, 60%), #FFF); 
		background-image:    -moz-linear-gradient(left, hsl(200, 100%, 60%), #FFF); 
		background-image:     -ms-linear-gradient(left, hsl(200, 100%, 60%), #FFF); 
		background-image:      -o-linear-gradient(left, hsl(200, 100%, 60%), #FFF); 
		background-image:         linear-gradient(left, hsl(200, 100%, 60%), #FFF);

	}

	.button{
		width: 50%;
		display: inline-block;
		height: 60px;
		background-color:magenta;
		font-size:15px;
		border-color:aqua;
		border-radius: 5px;
		 
	}

	.button:hover{
		
		height: 70px;
		background-color:yellow;
		font-size:18px;
		
	}
	.head{
		margin:0 0 3% 0;
		padding:10% 5% 5% 5%;
		/*background-color:aqua;*/
	}
	
	
	#boxin{

		display:block;
		overflow-x: auto;
		position:absolute;
		width:100%;
		height:0px;
		background-color:gray;
		transition: height 2.5s;

	}



	#boxout{

		display:block;
		overflow-x: auto;
		position:absolute;
		width:100%;
		height:0px;
		background-color:gray;
		transition: height 2.5s;

	}

	.closebtn{
		margin:0;
		border: none;
		background-color:magenta;
		width:100%;
		height:130px
	}

	

	
	</style>
	<script type="text/javascript" src='ajax.js'></script>
	<script type="text/javascript" src='sniper.js'></script>
	<script>

function select_show(CRF,ID,NAME,DETAIL,p){
	//console.log(p);
	//console.log(p);
	if(ID == "" || NAME == "" ){	
		showInput(CRF+":"+p);
	}else{
		showDetail(CRF,ID,NAME,DETAIL,p);
	}
	
}

function showDetail(crf,id,name,detail,btn_val){
	
	//showDetail(this.group_name , this.data_ob[index].CRF,this.data_ob[index].Name ,this.data_ob[index].ID ,this.data_ob[index].Detail);
		
        var boxout = document.getElementById('boxout');
            //boxout.style.display = "block";
            boxout.style.height = "250px";
            //box.style.zIndex = "0";
		//alert( detail+":"+btn);
        var dt = document.getElementById('detail').innerHTML = detail+":"+btn_val;
        //ref chk out el.onclick(datsa){}
        //ref change
        var btn_change = document.getElementById('btn_change');
            btn_change.onclick = function(){
                //this.crf;
                //change function(falcuty,this.crf,new this.crf) -> ajax(data,callback,true)
				var newcrf = document.getElementById('ob_change').value;
				alert(crf+":"+newcrf);
				changeItem(crf,newcrf);
				
            };
        var btn_out = document.getElementById('btn_out');
           btn_out.onclick = function(){
                //this.crf;
                //out function(falcuty,this.crf,sent) -> ajax(data,callback,true)
				var obsent = document.getElementById('op_sent').value;
				alert(crf+":"+obsent);
				getItem(crf,obsent);
            };
	
}

function showInput(crf){
		 var crf_val = document.getElementById('crf');
			 crf_val.innerHTML = crf;
	     var boxin = document.getElementById('boxin');
            //boxin.style.display = "block";
            boxin.style.height = "250px";
            

         var btn_aditem = document.getElementById('btn_additem');
            btn_aditem.onclick = function(){

                //out function(this.crf) -> ajax(data,callback,true)
				//additem function(falcuty,recieve,crf,name,id,detial) -> ajax(data,callback,true)
				var name = document.getElementById('NAME').value;
				var id = document.getElementById('ID').value;
				var description = document.getElementById('DETAIL').value;
				
				var recieve = document.getElementById('op_additem').value;
				alert(recieve+":"+name+":"+id+":"+description);
				addItem(recieve,crf,name,id,description); 
            };
		
}


function hide_boxin() {
    var boxin = document.getElementById('boxin');
	
    boxin.style.height = "0px";
	
}

function hide_boxout() {  
    var boxout = document.getElementById('boxout');
	//boxout.style.display = "inline";
	boxout.style.height = "0px";
    
}

function getItem(crf,sent){
    //args  -> ajax(data,callback,true)
	ajax("GET","/removeItem.php","?CRF="+crf+"&SENT="+sent,callBack);
}

function changeItem(get_crf,sent_crf){
    //args  -> ajax(data,callback,true)
	ajax("GET","/changeItem","?CRF="+get_crf+"&SENT="+sent_crf,callBack);
}

function addItem(recieve,crf,name,id,detail){
    //args  -> ajax(data,callback,true)
	
	ajax("GET","/addItem.php","?RECIEVE="+recieve+"&CRF="+crf+"&NAME="+name+"&ID="+id+"&DETAIL="+detail,callBack);
	alert(recieve+":"+crf+":"+name+":"+id+":"+detail);
}



</script>
<body>
		<div id='boxout' >
    		 <center>
			 
			  
			  <br>
    		  <b>DETAIL:</b>
    		  <div id = 'detail'> adj fonsiz color type </div>
    		  <hr>
    		   <b>CHANGE:</b>
    		        <select id="ob_change">
                      <option  value="1">station 1</option>
                      <option value="2">station 2</option>
                    </select>
                    <input type = 'button' value='change' id='btn_change'>
    		  <hr>
    		  <b>OUT:</b>
    		      <select id="op_sent">
                      <option  value="1">station 1</option>
                      <option value="2">station 2</option>
                  </select>
              <input type = 'button' value='werewr' id='btn_out'>
              
              <input type = 'button'   class='closebtn' value='close' onclick='hide_boxout()'>
            </center>
		</div>
		
		<div id='boxin' >
			<center>
				<br>
				<div id = 'crf'> adj fonsiz color type </div>
				pls a;sd;sadsadsad
				<br>
		        NAME: <input type='text' id='NAME'>
		        <br>
		        ID: <input type='text' id = 'ID'> 
		        <br>
		        DETAIL: <input type='text' id = 'DETAIL' >
		        <br>
    		    <select id="op_additem">
                      <option  value="1">station 1</option>
                      <option value="2">station 2</option>
                </select>
                
                <input type = 'button' value='addItem' id='btn_additem'>
                
                <input type = 'button' class='closebtn' value='close' onclick='hide_boxin()' >

            </center>
		</div>

		<noscript ><h1><center><span style="color:red">Your browser does not support JavaScript!</span></center></h1></noscript> 


	    <div class = 'head'>
			
	         sdfsdfjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjassaasdlasdasdasd
	    </div>

<?php
$servername = "localhost";
$username = "root";
$password = "";


try {
	
    $con = new PDO("mysql:host=$servername;dbname=project", $username, $password);
    // set the PDO error mode to exception
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
	$sqlcom = "SELECT * FROM ".$_SESSION["FALCUTY"];
	$result = $con->prepare($sqlcom);
	$result->execute();
	$cnt = 1;
	$line = 1;
	while($rs = $result->fetch()){
		if($cnt % 2 == 0){
			$line++;
			echo '<button  class="button" value = "LINE '.$line.': FLOOR 2: '.$rs['ID'].":".$rs['NAME'].'" onclick = \'select_show("'.$rs['CRF'].'","'.$rs['ID'].'","'.$rs['NAME'].'","'.$rs['DETAIL'].'",this.value)\' >ID = '.$rs['ID'].":NAME = ".$rs['NAME'].'</button> <br>';
		}else{
			echo '<button  class="button" value = "LINE '.$line.': FLOOR 1: '.$rs['ID'].":".$rs['NAME'].'" onclick = \'select_show("'.$rs['CRF'].'","'.$rs['ID'].'","'.$rs['NAME'].'","'.$rs['DETAIL'].'",this.value)\' >ID = '.$rs['ID'].":NAME = ".$rs['NAME'].'</button>';
		}
		$cnt++;
	}
}catch(PDOException $e)
    {
    echo "Connection failed: " . $e->getMessage();
    }

?>

</body>
</html>

