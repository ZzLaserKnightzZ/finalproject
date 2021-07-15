
function ajax(method,Path,data,cFunction) {
    
  var xhttp;
  xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
		  console.log(this.responseText);
		  cFunction(this);
    }
  };
  //console.log(Path+data);
  xhttp.open(method,''+Path+data,true);
  xhttp.send();

}


function callBack(xhttp){
	try{
		var json = JSON.parse(xhttp.responseText);
		console.log(json);
		if(json.res != undefined){

			alert(json.res); 

		}
		
		if(json.set){ //.store
			storage.setItem(json.key,json.value);
		}
		if(json.get){
			var vulue=localStorage.getItem(json.key);
			ajax("/store?"+vulue, undefined)
		}
	}catch(ex){
		console.log(ex);
	}
	
}