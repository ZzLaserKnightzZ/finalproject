
function Point(txt,x,y)
{
    this.txt = txt;
    this.x = x;
    this.y = y;
}


const  getPoint = function(name,array_ob)
{
    for(var l=0;l<array_ob.length;l++){
        if(array_ob[l].txt === name){
            //alert(array_ob[l].x + ":" + array_ob[l].y);
            return {x:array_ob[l].x, y:array_ob[l].y};
        }
    }
};



function process_touchstart(e){
    
 // handle_touch = true;
 let touch;
//var touch_items;
  if (e.targetTouches.length >= 1){
	//mouse_touch.x= e.touches[0].clientX;
    //mouse_touch.y = e.touches[0].clientY;
	touch = {x:e.touches[0].clientX , y:e.touches[0].clientY , handleMouseTouch : true};
	sci.mousePosition = touch;
	elec.mousePosition  = touch;
	macha.mousePosition  = touch;
  }
  else{
     //mouse_touch = e.touches.item(0);
	//mouse_touch.x = e.touches[0].clientX;
    //mouse_touch.y = e.touches[0].clientY;
	touch = {x:e.touches[0].clientX , y:e.touches[0].clientY , handleMouseTouch : true};
	sci.mousePosition = touch;
	elec.mousePosition  = touch;
	macha.mousePosition  = touch;
  }
     
}

function process_touchmove(e)
{
    
     
}

function process_touchend(e){
    
     //handle_touch = false;
     //call show element
	let touchEnd = {x:e.x , y:e.y ,handleMouseTouch : false};
	sci.mousePosition = touchEnd;
	elec.mousePosition  = touchEnd;
	macha.mousePosition  = touchEnd;
}


function draw_line(){
    
    if(point_yard.length > 1){
          ctx.beginPath();
          ctx.strokeStyle = 'white';
          ctx.lineWidth = 5;
          ctx.moveTo(point_yard[0].x,point_yard[0].y);
          for(var u = 1 ;u < point_yard.length ;u++){
              if(!point_yard[u].txt.startsWith("BOT"))
              ctx.lineTo(point_yard[u].x,point_yard[u].y);
          }
          ctx.closePath();
          ctx.stroke();
    }
      
}

function draw_store(){
    ctx.clearRect(0,0,canvas.width,canvas.height);
    ctx.restore();
    ctx.fillStyle = 'black';
    ctx.fillRect(0,0,canvas.width,canvas.height);

    sci.draw();
    

    elec.draw();
    

    macha.draw();
    

    re.draw();
    

    se.draw();
    
    draw_line();
    draw_robot();
    ctx.save();
    window.requestAnimationFrame(draw_store);
}

function draw_robot(){
    
    BOT1.run();
    BOT1.draw();
    BOT2.run();
    BOT2.draw();
}

function load_point(){

                
               for(var i = 0 ;i < direction.length ; i++){
                    let px =  Math.floor(direction[i].x*window.innerWidth);
                    let py = Math.floor(direction[i].y*window.innerHeight);
                    point_yard[i] = {'txt':direction[i].txt,'x':px,'y':py}; 
                   // console.log(point_yard[i]);
                }
                
}


function Group(ctx,name,color,empty_color){  //[{floor1:{}},{floor2:{}}]
    
    this.ctx = ctx;//test
    this.sx;
    this.sy;
    this.x;
    this.y;
    this.w;
    this.h;
    this.mx_w;
    this.group_name = name;
    this.color = color;
    this.empty_color = empty_color;
	this.cols = 0;
    this.rows = 0;
    this.floors;
	this.shift_floor = 0;

    this.data_ob = undefined; //[]
    
	this.AttShowFunction = function(){};
	this.AttInputFunction = function(){};
	this.mousePosition = {x:undefined , y:undefined , handleMouseTouch : false};

    
    this.init = function (w , h , mx_w , sx , sy)
	{    
        this.w = w;
        this.mx_w = mx_w;
        this.sx = sx;
        this.sy = sy;
        this.x = this.sx;
        this.y = this.sy;
        this.h = h;
    };
    
    this.update = function ( _ob )
	{  //store [0] = [ {item_name,data,detail},{...}]; ....etc
			//this.data_ob  = _ob;
					console.log(this.group_name);
					console.log(_ob);
					
					if(_ob === undefined){
						 console.exception(this.group_name + ":error object is undefined"); return;  //throw exception  
					}
					
					if(typeof( _ob) !== 'object'){
						//console.log(typeof (_ob));
						console.exception(this.group_name + ":error data type is not object");
					}
					
					if(this.floors !== undefined)
						if( _ob.length != this.floors){
							console.log( _ob.length +":"+ this.floors);
							console.exception(this.group_name + ":error floor index"); return;
						}
					//[]
					//[][]
					if( _ob.length > 1){
						for(let chker = 0 ; chker < _ob.length ; chker++){
							if(this.cols !== undefined && this.rows !== undefined)
							if( _ob[chker].length-1 != (this.cols*this.rows)){
								//console.log(typeof(this.cols));
								console.log( _ob[chker].length +":"+ (this.cols * this.rows+1));
								console.exception(this.group_name + ":error index size"); return;
							}
						}
					}else{
						/*
						//if(_ob[0].length == 1)
							if(this.cols !== undefined && this.rows !== undefined)
								if( ( (_ob[0].length-1) % (this.cols * this.rows)) != 0 ){
									console.log("=="+(_ob[0].length-1) +":"+(this.cols * this.rows));
									console.exception(this.group_name + ":error index size"); return;
								}
						*/
					}
					this.data_ob  = _ob;
			
			
			

    };
    
	
	
    this.shiftFloor = function()
	{
		let cur_floor = this.shift_floor;
        if(++cur_floor < this.floors){
			this.shift_floor++;
		}else{
			this.shift_floor=0;
		}
    }
    
    this.create = function(col,row,floor)
	{   //ทำใหม่เก็บแล้ววาด
        this.cols = col;
        this.rows = row;
        this.floors = floor;
    };
    
    
    this.draw = function()
	{
        
        this.x = this.sx;
        this.y = this.sy;
        let index = 0; //data_ob.length;
        var  rect = {x:0,y:0};
            for(var y=0 ; y < this.rows ; y++){
                for(var x=0 ; x < this.cols ; x++){
					
                    if(this.data_ob == undefined){
							this.ctx.beginPath();
							this.ctx.fillStyle = this.empty_color;
							this.ctx.rect(this.x,this.y,this.w,this.h);
							this.ctx.fill();
							this.ctx.closePath();
							
							let tex = "Loading.."
							this.ctx.beginPath();
							let sizID = this.w / tex;
							let sizNAME = this.w / tex;
							this.ctx.textAlign = 'center';
							this.ctx.fillStyle = 'gray';
							this.ctx.font = sizNAME+"px Arial";
							//this.ctx.fillText(this.data_ob[index].Name , this.x + this.w/2, this.y+this.h/3);  
							this.ctx.fillText(this.group_name , this.x + this.w/2, this.y+this.h/3);
							this.ctx.font = sizID+"px Arial";
							this.ctx.fillText(tex, this.x + this.w/2, this.y+this.h/1.5);
							this.ctx.closePath();
							
					}else{
						
							this.ctx.beginPath();
							let boxcolor = this.empty_color;
							if(this.floors == 1){ //1 floor
								
								if(this.data_ob[0][0].NAME == undefined) {  //data_ob.floor[this.floors].Name[this.index]
									boxcolor = this.color;
									this.ctx.beginPath();
									this.ctx.fillStyle = this.empty_color;
									this.ctx.rect(this.x,this.y,this.w,this.h);
									this.ctx.fill();
									this.ctx.closePath();
									
									let tex = "Loading.."
									this.ctx.beginPath();
									let sizID = this.w / tex;
									let sizNAME = this.w / tex;
									this.ctx.textAlign = 'center';
									this.ctx.fillStyle = 'hsl(360,100%,65%)';
									this.ctx.font = sizNAME+"px Arial";
									//this.ctx.fillText(this.data_ob[index].Name , this.x + this.w/2, this.y+this.h/3);  
									this.ctx.fillText(this.group_name , this.x + this.w/2, this.y+this.h/3);
									this.ctx.font = sizID+"px Arial";
									this.ctx.fillText(tex, this.x + this.w/2, this.y+this.h/1.5);
									this.ctx.closePath();
								}else{
																	
									this.ctx.fillStyle = boxcolor;
									//this.ctx.fillRect(this.x,this.y,this.w,this.h);
									this.ctx.rect(this.x,this.y,this.w,this.h);
									this.ctx.fill();
									this.ctx.closePath();
									/*
									if(this.ctx.isPointInPath(mouse_touch.x,mouse_touch.y) && handle_touch === true ){
									   
									   if(this.data_ob[0][index].NAME == "" && this.data_ob[0][index].ID == ""){
											showInput(this.group_name , this.data_ob[0][index].CRF);
									   }else{
											showDetail(this.group_name , this.data_ob[0][index].CRF,this.data_ob[0][index].NAME ,this.data_ob[0][index].ID ,this.data_ob[0][index].DETAIL);
									   }

									}
									*/
									this.ctx.beginPath();

									let MY_NAME =  "NAME:" + this.data_ob[0][index].NAME;
									let MY_ID = "ID:" +  this.data_ob[0][index].ID;
									if(MY_ID.length <= 6){ MY_ID = "...." + MY_ID;}
									if(MY_NAME.length <= 6){ MY_NAME = "...." + MY_NAME;}
									if(MY_ID.length >= 14){ MY_ID = MY_ID.substring(0, 14); }
									if(MY_NAME.length >= 14){ MY_NAME = MY_NAME.substring(0, 14); }
								
									let sizID = this.w /MY_ID.length; if(sizID > 25){ sizID = 25}//tobig
									let sizNAME = this.w / MY_NAME.length; if(sizNAME > 29){ sizNAME = 29}//tobig
									this.ctx.textAlign = 'center';
									this.ctx.fillStyle = 'gray';
									this.ctx.font = sizNAME+"px Arial";
									this.ctx.fillText("NAME:" + this.data_ob[0][index].NAME , this.x + this.w/2, this.y+this.h/3);  
									//this.ctx.fillText(this.group_name , this.x + this.w/2, this.y+this.h/3);
									this.ctx.font = sizID+"px Arial";
									this.ctx.fillText("ID:" + this.data_ob[0][index].ID , this.x + this.w/2, this.y+this.h/1.5);
									this.ctx.closePath();
								}
								
							}else{ //more then one floor
								
								if(this.data_ob[this.shift_floor][index].NAME.length>1) {  //data_ob.floor[this.floors].Name[this.index]
									boxcolor = this.color;
								}
								
								//var gradient = this.ctx.createLinearGradient(this.x,this.y,this.w,this.h);
								//gradient.addColorStop(0,boxcolor);
								//gradient.addColorStop(1,'rgba(255,255,255,0.6)');
								
								this.ctx.fillStyle = boxcolor;
								//this.ctx.fillRect(this.x,this.y,this.w,this.h);
								this.ctx.rect(this.x,this.y,this.w,this.h);
								this.ctx.fill();
								this.ctx.closePath();

								if(this.ctx.isPointInPath(this.mousePosition.x , this.mousePosition.y) && this.mousePosition.handleMouseTouch === true ){
								   	
									if(this.data_ob[this.shift_floor][index].NAME == "" && this.data_ob[this.shift_floor][index].ID == ""){
										this.AttInputFunction(this.group_name , this.data_ob[this.shift_floor][index].CRF);
								   }else if(this.data_ob[this.shift_floor][index].NAME == "MISSING" && this.data_ob[this.shift_floor][index].ID != "0"){
									    this.AttInputFunction(this.group_name , this.data_ob[this.shift_floor][index].CRF);
								   }else{
										this.AttShowFunction(this.group_name , this.data_ob[this.shift_floor][index].CRF,this.data_ob[this.shift_floor][index].NAME ,this.data_ob[this.shift_floor][index].ID ,this.data_ob[this.shift_floor][index].DETAIL);
								   }

								}
								
								this.ctx.beginPath();
								let MY_NAME =  "NAME:" + this.data_ob[this.shift_floor][index].NAME;
								let MY_ID = "ID:" +  this.data_ob[this.shift_floor][index].ID;
								if(MY_ID.length <= 6){ MY_ID = "...." + MY_ID;}
								if(MY_NAME.length <= 6){ MY_NAME = "...." + MY_NAME;}
								if(MY_ID.length >= 14){ MY_ID = MY_ID.substring(0, 14); }
								if(MY_NAME.length >= 14){ MY_NAME = MY_NAME.substring(0, 14); }
								
								let sizID = this.w /MY_ID.length; if(sizID > 25){ sizID = 25}//tobig
								let sizNAME = this.w / MY_NAME.length; if(sizNAME > 29){ sizNAME = 29}//tobig
								this.ctx.textAlign = 'center';
								this.ctx.fillStyle = 'white';
								this.ctx.font = sizNAME+"px Arial";
								this.ctx.fillText(MY_NAME , this.x + this.w/2, this.y+this.h/3);  
								//this.ctx.fillText(this.group_name , this.x + this.w/2, this.y+this.h/3);
								this.ctx.font = sizID+"px Arial";
								this.ctx.fillText(MY_ID , this.x + this.w/2, this.y+this.h/1.5);
								this.ctx.closePath();
							}
							

							

					}
					

                    
                    this.x += this.w+3;
					if(y == 0)
                    rect.x +=  this.w+3;
                    //if(index < this.data_ob.length)
					index++;
                }
                this.x = this.sx;
                this.y += this.h+3;//+gap
				
				rect.y += this.h+3;
            }
			this.ctx.beginPath();
			this.ctx.lineWidth = 5;
			this.ctx.strokeStyle = "lime";
			this.ctx.rect(this.sx-6,this.sy-6,rect.x +10,rect.y+10);
			this.ctx.stroke();
			this.ctx.closePath();
			this.ctx.beginPath();
			this.ctx.fillStyle = 'lime';
			this.ctx.textAlign = 'left';
			this.ctx.fillText(this.group_name , this.sx,this.sy-10);
			this.ctx.closePath();
			
    }; 
    
}		

function Robot(name,ctx){
    this.hand = true;
    this.ctx = ctx;
    this.name = name;
    this.text = name;
    this.cx;
    this.cy;
    this.r;
    this.circle_rad;
    this.lineone_rad;
    this.linetwo_rad;
    this.line_siz;
    this.progress = {x:undefined,y:undefined};
    this.Status = "";
    this.color = "100,50,199"; //linear gradient
    this.q_location = { x:undefined , y:undefined };
    //this.mouse = { x:undefined , y:undefined };
	
    this.update = function (point,id,Status){
        //position{x,y} 
        console.log(point);
        this.text = id; 
        this.Status = Status;
        if(point == undefined){ 
            console.log("update point error"); 
        }else{
			this.q_location = point;
        }
    };
    
    this.init = function (w,cx,cy){
        
        this.cx = cx;
        this.cy = cy;
        this.r = Math.floor(w/2);
        this.circle_rad = Math.floor(this.r/2);
        this.lineone_rad = this.circle_rad + Math.floor(this.r*0.05);
        this.linetwo_rad = this.circle_rad + Math.floor(this.r*0.01);
        this.line_siz = Math.floor(this.r*0.05);
        
    };
    
    this.resiz = function (w){
        this.r = Math.floor(w/2);
        this.circle_rad = Math.floor(this.r/2);
        this.lineone_rad = this.circle_rad + Math.floor(this.r*0.05);
        this.linetwo_rad = this.circle_rad + Math.floor(this.r*0.01);
        this.line_siz = Math.floor(this.r*0.05);
    };
    
    
    this.deg1 = {
        deg_start:0,
        deg_stop:180,
        
        start:function(){
            return this.deg_start * Math.PI / 180 ;
        } , 
        stop : function(){
             return   this.deg_stop + this.deg_start  * Math.PI / 180 ;
        }  
    };
        
    this.deg2 = {
        deg_start:360,
        deg_stop:180,
        
        start:function(){
            return this.deg_start * Math.PI / 180 ;
        } , 
        stop : function(){
            return this.deg_stop + this.deg_start * Math.PI / 180 ;
        }    
    };
    
    this.run = function (){
        
        if(this.Status.startsWith("ERROR")){
			this.text = this.Status;
            this.color = "300,10,90";
            if(this.hand === true){
                this.circle_rad++;
                this.lineone_rad++;
                this.linetwo_rad++;
                if(this.circle_rad >=  this.r){ this.hand = false; }
            }else{
                this.circle_rad--;
                this.lineone_rad--;
                this.linetwo_rad--;
                if(this.circle_rad <= Math.floor(this.r/2)){ this.hand = true; }
            }
        }else if(this.Status.startsWith("LOADING_ITEM")){
			this.color = "220,230,90";
		}else if(this.Status.startsWith("RUNNING")){
			this.color = "100,50,199";
			this.resiz(this.r*2);
		}
		
		if(this.Status == "STANBY"){
			this.color = "100,50,199";
			this.resiz(this.r*2);
		}

    
        this.deg1.deg_start++;
        this.deg2.deg_start--;

        if(this.deg1.deg_start >= 359){
            this.deg1.deg_start = 0;
            this.deg2.deg_start = 359;
        }
        
        
        if(this.cx < this.progress.x){
            this.cx++;
        }
        
        if(this.cx  > this.progress.x){
            this.cx--;
        }
        
        if(this.cy < this.progress.y){
            this.cy++;
        }
        
        if(this.cy > this.progress.y){
            this.cy--;
        }
        

        if(this.progress != this.q_location){
			this.progress = this.q_location;      
        }
    };
    
    this.getRadius = function(deg){
        return  deg*Math.PI / 180;
    };
    

      
    this.draw = function(){
		
        let fz;
		if(this.text.length > 3){
			fz =  this.circle_rad*2;
			fz = Math.floor(fz / this.text.length);
		}else{
			fz = 18;
		}
        
			
        this.ctx.beginPath();
        this.ctx.arc( this.cx, this.cy, this.circle_rad,0, 2*Math.PI,false);
        this.ctx.fillStyle = 'rgba('+this.color+',0.6)';
        this.ctx.fill();
        this.ctx.closePath(); 
		
        //if(this.ctx.isPointInPath(this.mouse.x , this.mouse.y)){
			
		//}
		
		/*
		for(let me=0 ; me < 360 ; me+=45){
			this.ctx.beginPath();
			//this.ctx.rotate( this.getRadius(me));
			let xx = this.cx + this.circle_rad * Math.cos(this.getRadius(me));
			let yy = this.cy + this.circle_rad * Math.sin(this.getRadius(me));
			this.ctx.arc( xx,  yy , this.linetwo_rad * 0.2, 0 , Math.PI*2, true);
			this.ctx.fillStyle = 'white';
			this.ctx.fill();
			this.ctx.closePath(); 
		}
		*/
        this.ctx.beginPath();
        this.ctx.lineWidth = this.line_siz;
		this.ctx.lineCap = "round";
        this.ctx.strokeStyle = 'lime';
        this.ctx.arc(this.cx ,  this.cy ,  this.lineone_rad , this.deg1.start() , this.deg1.stop() , true);
        this.ctx.stroke();
        this.ctx.closePath(); 
        
        this.ctx.beginPath();
        this.ctx.lineWidth = this.line_siz;
		this.ctx.lineCap = "round";
        this.ctx.strokeStyle = 'pink';
        this.ctx.arc( this.cx ,  this.cy ,  this.linetwo_rad , this.deg2.start() , this.deg2.stop() , true);
        this.ctx.stroke();
        this.ctx.closePath(); 
        
		this.ctx.beginPath();
        this.ctx.font = fz + "px Georgia";
        this.ctx.textAlign = "center";
        this.ctx.fillStyle = "cyan";
        this.ctx.fillText(this.name , this.cx,this.cy + fz);  //tune
        this.ctx.fillText(this.text , this.cx,this.cy - fz);
        this.ctx.closePath();  
    };
}


