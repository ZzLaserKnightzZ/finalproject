var QEURY_URI = [];
var MAINQ_CNT = 0;
var SYNC_BOT = 0;

function main_qury(){  //ทำเสดไม่เสดไม่เกี่ยว
    return setInterval(
        function(){
            //if(QEURY_URI.length == 0) re
            //ajax(QEURY_URI[MAINQ_CNT],callback_admin,true); //ALL in one callback 
			if(SYNC_BOT == 1000) YNC_BOT=0;
            if(MAINQ_CNT == QEURY_URI.length) MAINQ_CNT = 0;

			
			//console.log(MAINQ_CNT);
			if(SYNC_BOT%2 == 0){
				ajax("POST",QEURY_URI[MAINQ_CNT++],"",callback_admin);
			}else{
				ajax("POST","/JSONBOT.php","",callback_admin);
			}
			SYNC_BOT++;
			
        },1000);
}

function start_sync(){
     main_qury();
}

function stop_sync(){
    clearInterval(main_qury);
}

function sync_robot(){
    QEURY_URI.push("/JSONBOT.php");
}

function sync_PORT(){
    QEURY_URI.push("/JSONSENT.php");
	QEURY_URI.push("/JSONRECIEVE.php");
}

function sync_noti(){
	QEURY_URI.push("/JSONREPORT.php");
}

function sync_not(){
	    for(var i = 0 ;i<QEURY_URI.length ;i++){
        if(QEURY_URI[i] === "/JSONREPORT.php")
            delete QEURY_URI[i];
    }
}
function sessionid(){
	QEURY_URI.push("/session.php");
}
function pulse_sync_PORT(){
    for(var i = 0 ;i<QEURY_URI.length ;i++){
        if(QEURY_URI[i] === "/JSONSENT.php" || QEURY_URI[i] === "/JSONRECIEVE.php")
            delete QEURY_URI[i];
    }
}

function pulse__robot_sync(){
    for(var i = 0 ;i<QEURY_URI.length ;i++){
        if(QEURY_URI[i] === "/JSONBOT.php")
            delete QEURY_URI[i];
    }
}

function sync_error(){
    for(var i = 0 ;i<QEURY_URI.length ;i++){
        if(QEURY_URI[i] === "/JSONERROR.php")
            delete QEURY_URI[i];
    }
}

function sync_error(){
	QEURY_URI.push("/JSONERROR.php");
}

function sync_storage(){
     QEURY_URI.push("/JSONSTORAGE.php?STORAGE=science");
	 QEURY_URI.push("/JSONSTORAGE.php?STORAGE=mechanic");
	 QEURY_URI.push("/JSONSTORAGE.php?STORAGE=electronics");	
}

function pulse_storage_sync(){
    for(var i = 0 ;i<QEURY_URI.length ;i++){
        if(QEURY_URI[i] === "/JSONSTORAGE.php")
            delete QEURY_URI[i];
    }
}

function callback_admin(xhttp){ ///ช่างแม่งค่อยไปแยกเอา
	try{
		//ob updater
		console.log(xhttp.responseText);
		var Json = JSON.parse(xhttp.responseText);
		//console.log(Json);
	   //ROBOT{[{BOTNAME:'bot1'...},{}]}
	   if(Json.ROBOT !== undefined){
		   for(var i=0; i < Json.ROBOT.length ; i++){
			   if(Json.ROBOT[i].NAME == BOT1.name){
				 BOT1.update(getPoint( Json.ROBOT[i].LOCATION , point_yard), Json.ROBOT[i].JOBID , Json.ROBOT[i].STATUS);
			   }else if(Json.ROBOT[i].NAME == BOT2.name){
				 BOT2.update(getPoint( Json.ROBOT[i].LOCATION , point_yard), Json.ROBOT[i].JOBID , Json.ROBOT[i].STATUS);
			   }
		   }
	   }
	   
	   if(Json.science !== undefined){
		   sci.update(Json.science); 
	   }
	   
	   if(Json.electronics !== undefined){
		   elec.update(Json.electronics);
	   }
	   
	   if(Json.mechanic !== undefined){
		   macha.update(Json.mechanic);
	   }
	   
	   if(Json.SENT !== undefined){
		   if(Json.SENT){
			   se.update(Json.SENT);
		   }
	   }
	   
	   if(Json.RECIEVE !== undefined){
		   if(Json.RECIEVE){
			   re.update(Json.RECIEVE);
		   }
	   }
	   
	   if(Json.REPORT !== undefined){
		   console.log(Json.REPORT);
		 //if(Json.REPORT.ERROR !== undefined)
		   //for(let i=0;i<Json.REPORT.ERROR.length;i++){ //for(let i of Json.REPORT)
			   //if(Json.REPORT[i] != "")
					add_notification(Json.REPORT);
		   //}
		 //if(Json.REPORT.SUCCESS!== undefined)
		   //for(let i=0;i<Json.REPORT.SUCCESS.length;i++){ //for(let i of Json.REPORT)
			   //if(Json.REPORT[i] != "")
					//add_notification(Json.REPORT.SUCCESS[i],true);
		   //}
		   
	   }
	   
	   //console.log(xhttp.responseText);
	   //console.log(MAINQ_CNT);
	   
	}catch(ex){
		
		console.log(xhttp.responseText);
		console.error(ex);
		//console.log(MAINQ_CNT);
	}
	Json = undefined;
}