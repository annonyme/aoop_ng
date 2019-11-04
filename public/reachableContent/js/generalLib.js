function callUrl(url,sync){
	var ajax=null;
	try {                        // W3C-Standard
		ajax = new XMLHttpRequest();
	} catch(w3c) {
		try {                        // Internet Explorer
			ajax = new ActiveXObject("Msxml2.XMLHTTP");
		} catch(msie) {
			try {                // Internet Explorer alt
				ajax = new ActiveXObject("Microsoft.XMLHTTP");
			} catch(msie_alt) {
				return "Konnte Ajax-Verbindung nicht aufbauen.";
			}
		}
	}

	ajax.open('GET', url, sync);
	ajax.setRequestHeader('Content-Type', 'text/plain');
			
	var value="";
	try{
		ajax.send();	
		value=ajax.responseText;
	}
	catch(error){
		alert(error);
	}			
	return value;			
}
		
function addChildToElement(child, element, clear){
	if(clear){
		removeAllChildrenFromElement(element);
	}
	element.appendChild(child);
	return child;
}
		
function removeAllChildrenFromElement(element){			
	while(element.childNodes.length>0){
		element.removeChild(element.firstChild);
	}
	return element;
}

function setInnerTextOfElement(element,text){
	removeAllChildrenFromElement(element);
	element.appendChild(document.createTextNode(text));
	return element;
}

function addInnerLinkOfElement(element,url,text,newTab,onclickAsText,cssClass){
	var a=document.createElement("a");
	a.href=url;
	a.setAttribute("class",cssClass);
	a.setAttribute("onclick",onclickAsText);
	if(text.length==0){
		text=url;
	}
	
	if(newTab){
		a.setAttribute("target","_blank");
	}
	a.appendChild(document.createTextNode(text))
	
	return a;
}
		
function randomCode(count){
	var chars=new Array();
	chars[0]="a";
	chars[1]="b";
	chars[2]="c";
	chars[3]="d";
	chars[4]="e";
	chars[5]="f";
	chars[6]="g";
	chars[7]="h";
	chars[8]="i";
	chars[9]="j";
	chars[10]="1";
	chars[11]="2";
	chars[12]="3";
	chars[13]="4";
	chars[14]="5";
	chars[15]="6";
	chars[16]="7";
	chars[17]="8";
	chars[18]="9";
	chars[19]="0";
	chars[20]="x";
			
	var result="";
	for(var i=0;i<count;i++){
		result+=chars[Math.floor((Math.random()*chars.length)+0)];
	}
			
	return result;
}

function randomNumber(from, till){
	return Math.floor((Math.random() * till) + from);
}

function selectOptionById(id,selection){
	var children=selection.childNodes;
	for(var i=0;i<children.length;i++){
		var child=children[i];
		if(child.nodeName.toLowerCase()=="option"){
			if(child.value==id){
				child.selected=true;
			}
			else{
				child.selected=false;
			}
		}
	}
}

function deselectOptions(selection){
	var children=selection.childNodes;
	for(var i=0;i<children.length;i++){
		var child=children[i];
		if(child.nodeName.toLowerCase()=="option"){
			child.selected=false;
		}
	}
}

function getCurrentTimeStamp(){
	if (!Date.now) {
		Date.now = function() { return new Date().getTime(); };
	}
	return Date.now();
}

////////////////////////////////////////////
// 			file upload functions         //
////////////////////////////////////////////

/*
<?php
//see $_REQUEST["lastChunk"] to know if it is the last chunk-part or not
if(isset($_FILES["upfile"])){
	file_put_contents($_REQUEST["filename"],file_get_contents($_FILES["upfile"]["tmp_name"]),FILE_APPEND);
}
?>
*/

function createChunksOfFile(file,chunkSize){
	var chunks=new Array();
				
	var filesize=file.size
				
	var counter=0
	while(filesize>counter){
		var chunk=file.slice(counter,(counter+chunkSize));
		counter=counter+chunkSize;
					
		chunks[chunks.length]=chunk;
	}
				
	return chunks;
}	

function uploadFiles(files,url,params,progressId){
	for(var iFi=0;iFi<files.length;iFi++){
		var file=files[iFi];
		
		var filenamePrefix=randomCode(5)+"_";
				
		var chunks=createChunksOfFile(file,250000);
		
		if(document.getElementById(progressId)){
			var field=document.getElementById(progressId);
			if(field.nodeName.toLowerCase()=="progress"){
				field.setAttribute("min","0");
				field.setAttribute("max","100");
				field.value=0;
			}
			else{
				ui.removeAllChildrenFromElement(field);
				field.appendChild(document.createTextNode("0%"));
			}
		}
		
		for(var i=0;i<chunks.length;i++){
			var last=false;
			if(i==(chunks.length-1)){
				last=true;
			}
			uploadFileChunk(chunks[i],filenamePrefix+file.name,url,params,last);
			if(progressId!=null && progressId.length>0 && document.getElementById(progressId)){
				var field=document.getElementById(progressId);
				var pro=Math.round((i+1)/chunks.length*100)|0;						
				
				if(field.nodeName.toLowerCase()=="progress"){
					field.value=pro;
				}
				else{
					ui.removeAllChildrenFromElement(field);
					field.appendChild(document.createTextNode(pro+"%"));
				}
			}
		}
	}
}	

var uploadWorkloadQueue=new Array();

function Workload(){
	this.name="";
	this.url="";
	this.params=new Array();
	this.progressId="";
	this.file=null;
	this.removeElementAfterUploadId="";
	
	this.afterUploadNotificationText="";
	
	this.onRemoveCallbackFunction=null;
	
	this.addToQueue=function(){
		this.name=randomCode(8);
		uploadWorkloadQueue[this.name]=this;
		window.setTimeout("uploadWorkload('"+this.name+"',50)");
	}
}	

function uploadWorkload(name){
	if(uploadWorkloadQueue[name]){
		var wl=uploadWorkloadQueue[name];
		//alert(wl.progressId);		
		var resultValue=uploadFile(wl.file,wl.url,wl.params,wl.progressId);		
		if(wl.removeElementAfterUploadId!=""){
			var el=document.getElementById(wl.removeElementAfterUploadId)
			if(el){
				el.parentNode.removeChild(el);
			}
		}
		if(wl.afterUploadNotificationText!=""){
			showNotification(wl.afterUploadNotificationText);
		}
		
		if(wl.onRemoveCallbackFunction!=null){
			wl.onRemoveCallbackFunction(resultValue);
		}
		uploadWorkloadQueue[name]=null;
	}
	else{
		alert("error workload not found ["+name+"]");
	}
}
			
function uploadFile(file,url,params,progressId){
	var filenamePrefix=randomCode(5)+"_";
				
	var chunks=createChunksOfFile(file,250000);
	
	if(document.getElementById(progressId)){
		var field=document.getElementById(progressId);
		if(field.nodeName.toLowerCase()=="progress"){
			field.setAttribute("min","0");
			field.setAttribute("max","100");
			field.value=0;
		}
		else{
			removeAllChildrenFromElement(field);
			field.appendChild(document.createTextNode("0%"));
		}		
	}
	
	var result;
	for(var i=0;i<chunks.length;i++){
		var last=false;
		if(i==(chunks.length-1)){
			last=true;
		}
		result=uploadFileChunk(chunks[i],filenamePrefix+file.name,url,params,last);
		if(progressId!=null && progressId.length>0 && document.getElementById(progressId)){
			var field=document.getElementById(progressId);
			var pro=Math.round((i+1)/chunks.length*100);
			if(field.nodeName.toLowerCase()=="progress"){
				field.value=pro;
			}
			else{
				removeAllChildrenFromElement(field);				
				field.appendChild(document.createTextNode(pro+"%"));
			}			
		}
	}
	//filenamePrefix+file.name;
	return result;
}
			
function uploadFileChunk(chunk,filename,url,params,lastChunk) {
	var formData = new FormData();
	formData.append('upfile', chunk, filename);
	formData.append("filename",filename);
	
	for(key in params){
		formData.append(key,params[key]);
	}
				
	if(lastChunk){
		formData.append("lastChunk","true");
	}
	else{
		formData.append("lastChunk","false");
	}	

	var xhr = new XMLHttpRequest();
					
	xhr.open("POST", url,false); //false=synchron;
	xhr.send(formData);	
	console.log("upload response-text: "+xhr.responseText);	
	return xhr.responseText;		
}

function dataURItoBlob(dataURI) {
	// convert base64 to raw binary data held in a string
	var byteString = atob(dataURI.split(',')[1]);
	 
	// separate out the mime component
	var mimeString = dataURI.split(',')[0].split(':')[1].split(';')[0];
	 
	// write the bytes of the string to an ArrayBuffer
	var arrayBuffer = new ArrayBuffer(byteString.length);
	var _ia = new Uint8Array(arrayBuffer);
	for (var i = 0; i < byteString.length; i++) {
		_ia[i] = byteString.charCodeAt(i);
	}
	 
	var dataView = new DataView(arrayBuffer);
	var blob = new Blob([dataView], { type: mimeString });
	return blob;
}

function hiddeAllSiblings(element){
	var parent=element.parentNode;
	var children=parent.childNodes;
	for(var i=0;i<children.length;i++){
		var node=children[i];
		if(node!=element){
			node.style.display="none";
		}
	}
}

function showAllSiblings(element){
	var parent=element.parentNode;
	var children=parent.childNodes;
	for(var i=0;i<children.length;i++){
		var node=children[i];
		node.style.display="";
	}
}

function showNotification(text){
	if (!("Notification" in window)) {
		console.log("This browser does not support desktop notification");
	}
	else if (Notification.permission === "granted") {
		var notification = new Notification(text);
	}
	else if (Notification.permission !== 'denied') {
		Notification.requestPermission(function (permission) {
				if (permission === "granted") {
					var notification = new Notification(text);
				}
		});
	}
}

function getCurrentDomain(){
	var url=window.location.href;
	url=url.replace(/http(s)?:\/\//,"");
	var parts=url.split(/\//);
	return parts[0];
}