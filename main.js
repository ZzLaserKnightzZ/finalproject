//delete reprace direct object
//var mouse_touch = {x:undefined ,y:undefined};
var handle_touch = false;

var point_yard = []; //['s1'] = return {x0,y0};
const point_name = ['re1','re2','re3','re4','re5','sto1','sto2','sto3','sto4','sto5','sto6','sto7','sto8','sen1','sen2','sen3','sen4','sen5','sta1','sta2','BOT1','BOT2'];
var cnt_me=0;

var canvas,ctx;
var BOT1,BOT2;
var sci,elec,macha,re,se;

window.onload = function (){
	
	sessionid();
    canvas =document.getElementById('can');
    ctx = canvas.getContext('2d');
    canvas.height = window.innerHeight;
    canvas.width = window.innerWidth;
    

    
    canvas.addEventListener('touchstart', process_touchstart, false);
    canvas.addEventListener('touchmove', process_touchmove, false);
    canvas.addEventListener('touchend', process_touchend, false);
    //draw_background();
     BOT1 = new Robot("BOT1",ctx);
     BOT2 = new Robot("BOT2",ctx);
     
     BOT1.init(Math.floor(canvas.width*0.15),100,100);
     BOT2.init(Math.floor(canvas.width*0.15),100,200);
	 sync_noti();
	 sync_PORT();
	 sync_robot();
	 sync_storage();
	 start_sync();

    load_point();
    sci = new Group(ctx,"science",'red','pink');  
    elec = new Group(ctx,"electronics","gold","orange");
    macha = new Group(ctx,"mechanic",'green','lime');
    
    re = new Group(ctx,"recieve_point",'violet','magenta');
    se = new Group(ctx,"sent_point",'aqua','blue');
	
	sci.AttInputFunction = showInput;
	elec.AttInputFunction = showInput;
	macha.AttInputFunction = showInput;
	
	sci.AttShowFunction = showDetail;
	elec.AttShowFunction  = showDetail;
	macha.AttShowFunction  = showDetail;
	
    
    sci.init(Math.floor(canvas.width*0.1),Math.floor(canvas.height*0.1),canvas.width,Math.floor(canvas.width*0.6),Math.floor(canvas.height*0.4));
    
    elec.init(Math.floor(canvas.width*0.1),Math.floor(canvas.height*0.1),canvas.width,Math.floor(canvas.width*0.6),Math.floor(canvas.height*0.2));
    macha.init(Math.floor(canvas.width*0.1),Math.floor(canvas.height*0.1),canvas.width,Math.floor(canvas.width*0.6),Math.floor(canvas.height*0.7));
    
    re.init(Math.floor(canvas.width*0.1),Math.floor(canvas.height*0.1),canvas.width,Math.floor(canvas.width*0.2),Math.floor(canvas.height*0.2));
    se.init(Math.floor(canvas.width*0.1),Math.floor(canvas.height*0.1),canvas.width,Math.floor(canvas.width*0.2),Math.floor(canvas.height*0.7));
    
	canvas.addEventListener("mousedown",function(e){
		sci.mousePosition = {x:e.x , y:e.y ,handleMouseTouch : true};
		elec.mousePosition  = {x:e.x , y:e.y ,handleMouseTouch : true};
		macha.mousePosition  = {x:e.x , y:e.y ,handleMouseTouch : true};
    });
    
    canvas.addEventListener("mouseup",function(e){
        sci.mousePosition = {x:e.x , y:e.y ,handleMouseTouch : false};
		elec.mousePosition  = {x:e.x , y:e.y ,handleMouseTouch : false};
		macha.mousePosition  = {x:e.x , y:e.y ,handleMouseTouch : false};
    });
    
    canvas.addEventListener("mousemove",function(e){
    });
    //sci.update(undefined,); //out side
    sci.create(3,2,2); //out side
	//elec.update(undefined,);
    elec.create(3,1,2);
	//macha.update(undefined,);
    macha.create(3,1,2);
	//re.update(undefined,);
    re.create(2,1,1);
	//se.update(undefined,);
    se.create(2,1,1);
    
    //load_store_line(canvas.width,canvas.height);
    draw_store();
    

};

window.onresize = function(){
    
    canvas.height = window.innerHeight;
    canvas.width = window.innerWidth;
    //loadline_bot(); //reload line store size
    load_point();
	
	BOT1.resiz(Math.floor(canvas.width*0.15));
    BOT2.resiz(Math.floor(canvas.width*0.15));
	
	sci.init(Math.floor(canvas.width*0.1),Math.floor(canvas.height*0.1),canvas.width,Math.floor(canvas.width*0.6),Math.floor(canvas.height*0.4));
		
	elec.init(Math.floor(canvas.width*0.1),Math.floor(canvas.height*0.1),canvas.width,Math.floor(canvas.width*0.6),Math.floor(canvas.height*0.2));

	macha.init(Math.floor(canvas.width*0.1),Math.floor(canvas.height*0.1),canvas.width,Math.floor(canvas.width*0.6),Math.floor(canvas.height*0.7));
		
	re.init(Math.floor(canvas.width*0.1),Math.floor(canvas.height*0.1),canvas.width,Math.floor(canvas.width*0.2),Math.floor(canvas.height*0.2));

	se.init(Math.floor(canvas.width*0.1),Math.floor(canvas.height*0.1),canvas.width,Math.floor(canvas.width*0.2),Math.floor(canvas.height*0.7));
    
    
};

function clear_notification(){
	
	var tag = $GetE("notification");
	var arr = tag.childNodes;	
	var cnt_tag = arr.length;
	while(--cnt_tag > 3){ //0 null el
	//console.log(tag);	
	//console.log(tag.lastChild);
		tag.removeChild(tag.lastChild);
	}
	alert_noti();

}


function next_floor(){
	sci.shiftFloor();
    elec.shiftFloor();
    macha.shiftFloor();
	let btnnext = $GetE("btnnext");
	btnnext.innerHTML = "FLOOR >"+(sci.shift_floor + 1);
}


function alert_noti(){
	var alrt_note = $GetE("alert_note");
	var tag = $GetE("notification");
	let arr = tag.childNodes;
	let cnt_tag = arr.length-1;
	
	if(cnt_tag > 3){
		if(cnt_tag - 3 > 0){
			console.log("logtag:");
			console.log(typeof(cnt_tag));
			console.log(cnt_tag - 3);
			console.log("logtag:");
			alrt_note.style.display = 'block';
			alrt_note.innerHTML = cnt_tag - 3 ;
		}
	}else{
		alrt_note.style.display = 'none';
	}
}

function notification_Remove(item_id, manner , node_id)
{
	var tag = $GetE("notification");
	var note_node = $GetE(node_id+"");
	//console.log(node_id+"");
	tag.removeChild(note_node);
	clearPGS(item_id,manner);
	alert_noti();
}

function addNode_notification(tex_node)
{
	var tag = $GetE("notification");
	for(let newTxtLoop = 0 ; newTxtLoop < tex_node.length ; newTxtLoop++){
		var node_t = $Create("t");
		let node_text = $New_txt(tex_node[newTxtLoop]);

		let my_id = tex_node[newTxtLoop].split('>');
		let rand_id = Math.floor(Math.random()*1000);
		node_t.setAttribute('id', 'nodeid'+rand_id );
		let btn_clear = $Create("button");
		//btn_clear.setAttribute('content', my_id );
		btn_clear.setAttribute('class', 'clearbtn');
		btn_clear.innerHTML = '&times';

		//if(noti_type){
			//btn_clear.setAttribute('value','update'); 
			//btn_clear.setAttribute('onclick','notification_Remove("'+my_id+'","update","nodeid"+'+rand_id+');'); 
		//}else{
			btn_clear.setAttribute('value','clear');
			btn_clear.setAttribute('onclick','notification_Remove("'+my_id[1]+'","clear","nodeid"+'+rand_id+');'); 		
		//}
		
		node_t.appendChild(btn_clear);
		node_t.appendChild(node_text);
		
		$append_node("notification",node_t);
	}
}

function add_notification(Json_notifcation)
{
	
	clear_notification();
	addNode_notification(Json_notifcation.ERROR);
	addNode_notification(Json_notifcation.SUCCESS);
	alert_noti();
	
}

function show_notification(){
	var btnnoti = document.getElementById('notification');
	btnnoti.style.width = "300px";
	var btnnoti = document.getElementById('btnnoti').style.display = "none";;
}

function hide_notification(){
	var btnnoti = document.getElementById('notification');
	btnnoti.style.width = "0px";
	var btnnoti = document.getElementById('btnnoti').style.display = "block";;
}



function showDetail(group,crf,name,id,detail){

//showDetail(this.group_name , this.data_ob[index].CRF,this.data_ob[index].Name ,this.data_ob[index].ID ,this.data_ob[index].Detail);

        var boxout = document.getElementById('boxout');
            //boxout.style.display = "block";
            boxout.style.height = "250px";
            //box.style.zIndex = "0";
        var deatil = document.getElementById('detail').innerHTML = crf;
		var idText = document.getElementById('idText');
			idText.value = id;
			idText.disabled = true;
		var nameText = document.getElementById('nameText');
			nameText.value = name;
			nameText.disabled = true;
		var detialText = document.getElementById('detialText');
			detialText.value = detail;
			detialText.disabled = true;
        //ref chk out el.onclick(datsa){}
        //ref change

        var btn_out = document.getElementById('btn_out');
           btn_out.onclick = function(){
                //this.crf;
                //out function(falcuty,this.crf,sent) -> ajax(data,callback,true)
				var obsent = document.getElementById('op_sent').value;
				//alert(obsent);
				getItem(group,crf,obsent);
				hide_boxout();
            };
		
		 var btn_edite = document.getElementById('edite_item');
		 btn_edite.onclick=function(){
			var idText = document.getElementById('idText');
			var nameText = document.getElementById('nameText');
			var detialText = document.getElementById('detialText');
			//alert(bnt_el);
			if(this.value == "edite"){
				this.value = "update";
					//idText.value = id;
				idText.disabled = false;
					//nameText.value = name;
				nameText.disabled =  false;
					//detialText.value = detail;
				detialText.disabled =  false;
				
			}else{
				//inputext.disable
				//update 
				ajax("GET","/edite.php","?FALCUTY="+group+"&ID="+idText.value+"&NAME="+nameText.value+"&DETAIL="+detialText.value+"&CRF="+crf,callBack);
				//alert("?FALCUTY="+group+"ID="+idText.value+"&NAME="+nameText.value+"&DETAIL="+detialText.value);
				this.value = "edite";
				idText.disabled = true;
				nameText.disabled =  true;
				detialText.disabled =  true;

			}
		}
}

function showInput(group,crf){// showInput(this.group_name , this.data_ob[index].CRF);

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
				//alert(recieve+":"+name+":"+id+":"+description);
				addItem(group,recieve,crf,name,id,description); 
				hide_boxin();
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

function getItem(group,crf,sent){
    //args  -> ajax(data,callback,true)
	//alert(group+crf+sent);
	ajax("POST","/removeItem.php","?GROUP="+group+"&CRF="+crf+"&SENT="+sent,callBack);
}



function addItem(group,recieve,crf,name,id,detail){
    //args  -> ajax(data,callback,true)
	ajax("GET","/addItem.php","?GROUP="+group+"&RECIEVE="+recieve+"&CRF="+crf+"&NAME="+name+"&ID="+id+"&DETAIL="+detail,callBack);
}


function clearPGS(id,manner){
    //args  -> ajax(data,callback,true)
	ajax("GET","/clearPGS.php","?ID="+id+"&MANAGE="+manner,callBack);
}