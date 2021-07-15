function $GetE(e){
	return document.getElementById(e);
}

function $GetC(e){
	return document.getElementsByClassName(e);
}

function $Create(e){
	return document.createElement(e);
}

function $New_txt(t){
	return document.createTextNode(t);
}

function $append_node(elemen,child){
	$GetE(elemen).appendChild(child);
}

function $Context(e){
	return $GetE(e).getContext('2d');
}