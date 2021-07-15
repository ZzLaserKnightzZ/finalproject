//loadDoc(mothod,Path,data,cFunction)
function add_user(){
	var new_usr = prompt("Please enter new user (name:pass:store)", "enter user name");
	if (new_usr != null) {
		var r = confirm("new (name:pass:store) = " + new_usr);
		if (r == true) {
			ajax("POST","/admin/add_user/", "?NEW_USER="+new_usr , callBack)
		} else {
			
		} 
	}
}

function delete_user(){
	var del_usr = prompt("Please enter user name(only)", "enter user name");
	if (del_usr != null) {
		var r = confirm("new (name) = " + del_usr);
		if (r == true) {
			ajax("POST","/admin/delete_user/", "?NAME="+del_usr , callBack)
		} else {
			
		} 
	}
}
/**/
function load_file(){ //POST /admin/load_file?NAME=
	var f_name = $GetE("txt").value;
	loadDoc("POST","/admin/load_file","?FILE_NAME=" + f_name , load_txt);
}



function load_txt(xhttp){
	var data = xhttp.responseText;
	$GetE("txt_area").value = data; //wrong
}

function show_edite(){
	$GetE("sneaker").style.display = "block";
	$GetE("txt_area").style.display = "block";
	closeNav();
}

function hide_edite(){
	$GetE("sneaker").style.display = "none";
	$GetE("txt_area").style.display = "none";
}
/**/
function openNav() {
	$GetE("mySidenav").style.width = "250px";
	//$GetC("main").style.marginLeft = "250px";
	document.body.style.backgroundColor = "rgba(0,0,0,0.4)";
	$GetE("btnoption").style.display ='none'; 
	$GetE("btnnext").style.display ='none';
}

function closeNav() {
	$GetE("mySidenav").style.width = "0";
	//$GetC("main").style.marginLeft= "0";
	document.body.style.backgroundColor = "white";
	$GetE("btnoption").style.display ='block';
	$GetE("btnnext").style.display ='block';
}
	
/* Get the documentElement (<html>) to display the page in fullscreen */
var elem = document.documentElement;

/* View in fullscreen */
function openFullscreen() {
  if (elem.requestFullscreen) {
    elem.requestFullscreen();
  } else if (elem.mozRequestFullScreen) { /* Firefox */
    elem.mozRequestFullScreen();
  } else if (elem.webkitRequestFullscreen) { /* Chrome, Safari and Opera */
    elem.webkitRequestFullscreen();
  } else if (elem.msRequestFullscreen) { /* IE/Edge */
    elem.msRequestFullscreen();
  }
}

/* Close fullscreen */
function closeFullscreen() {
  if (document.exitFullscreen) {
    document.exitFullscreen();
  } else if (document.mozCancelFullScreen) { /* Firefox */
    document.mozCancelFullScreen();
  } else if (document.webkitExitFullscreen) { /* Chrome, Safari and Opera */
    document.webkitExitFullscreen();
  } else if (document.msExitFullscreen) { /* IE/Edge */
    document.msExitFullscreen();
  }
}
	
	
	

		