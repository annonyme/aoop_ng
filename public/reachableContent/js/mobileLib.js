//element with id=installFirefoxOS contains install-button + etc .. später mal entfernen und an methode übergeben lassen
function installOnFirefoxOs(manifestName){
	if (navigator.mozApps) {
		var request = navigator.mozApps.getSelf();
		request.onsuccess = function() {
				request = navigator.mozApps.install(location.protocol + "//" + location.host + location.pathname.replace(/index.html$/,"") + manifestName);
				request.onerror = function() {
					document.getElementById("installFirefoxOS").style.display="none";
					console.log("Install failed: " + this.error.name);
				};	
				request.onsuccess=function(){
					document.getElementById("installFirefoxOS").style.display="none";
				}
		};
	}
}

function showIfInstallableOnFirefoxOS(manifestName){
	var show=false;
	if(navigator.mozApps && location.protocol.match(/^http/)){
		var req = navigator.mozApps.checkInstalled(location.protocol + "//" + location.host + location.pathname.replace(/index.html$/,"") + manifestName);
        req.onsuccess  = function() {
			  if(req.result){
				document.getElementById("installFirefoxOS").style.display="none";
				req.result.checkForUpdate();
			  }
			  else{
				document.getElementById("installFirefoxOS").style.display="";
			  }			  
        };
    }
	else{
		document.getElementById("installFirefoxOS").style.display="none";
	}
}

function getScreenResolution(){
	var result=new Array();
	result["x"]=window.innerWidth;
	result["y"]=window.innerHeight;
	return result;
}

function getAspectRatioOfScreen(){
	return window.innerWidth/window.innerHeight;
}

function scaleWidthByHeight(height,maxRation){
	var ratio=getAspectRatioOfScreen();
	if(ratio>maxRation){
		ratio=maxRation;
	}
	//alert(ratio+"("+window.screen.width+"/"+window.screen.height+")");
	return Math.round(height/ratio);	
}

function scaleHeightByWidth(width,maxRation){
	var ratio=getAspectRatioOfScreen();
	if(ratio>maxRation){
		ratio=maxRation;
	}
	//alert(ratio+"("+window.screen.width+"/"+window.screen.height+")");
	return Math.round(width*ratio);	
}

function checkGeolocation(){
	var result=false;
	if(navigator.geolocation){
		result=true;
	}
	return result;
}

/**
 * use in callback-function:
 * position.coords.latitude
 * position.coords.longitude
 * 
 * @param callback
 */
function getGeolocationAsync(callback){
	if(navigator.geolocation){
		console.log("geolocation-api called");
		navigator.geolocation.getCurrentPosition(callback);
	}
	else{
		callback(null);
		console.log("geolocation-api not supported!");
	}
}