var txt="";
var btn = "";
window.onload = function(){
	
   $GetE('login').disabled = true;
   draw();
   
};
function clear(){
	$GetE('verify').value="";
}

function change(){
	
	var text = $GetE('verify').value;
    if( text === txt){
        $GetE('login').disabled = false;
		alert('ok'+btn +':'+txt);
    }else{
        $GetE('login').disabled = true;
		alert('o'+btn +':'+txt);
    }
	
}

function text_random(){
	
    var txt = "";
    for(var i = 0;i < 7; i++){
        txt += String.fromCharCode(65 + Math.floor(Math.random()*25));
    }
    return txt;
	
}

function draw(){
	
   var c = $GetE('capchar');
   var ctx = $Context('capchar');
   c.width = Math.ceil(window.innerWidth*0.20);
   c.height = Math.ceil(window.innerHeight*0.05);
   
   txt = text_random();
   
   var siz = c.width/txt.length;
   var t = [];
   t = txt.split('');
   ctx.font = siz+"px Arial";
   var x = 0;
   ctx.beginPath();
   ctx.fillText(txt,siz,c.height);
   ctx.closePath();

   for(var i = 0;i < 40; i++){
    ctx.beginPath();
    ctx.fillStyle = 'rgba('+Math.floor(Math.random()*255)+','+Math.floor(Math.random()*255)+','+Math.floor(Math.random()*250)+','+Math.random()*0.6+')';
    ctx.arc(Math.floor(Math.random()*c.width),Math.floor(Math.random()*c.width),Math.floor(Math.random()*c.width/4),0,Math.PI*2,false);
    ctx.fill();
    ctx.closePath();
   }
}