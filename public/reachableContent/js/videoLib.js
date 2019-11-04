function captureImgAsDataURLFromVideo(videoId,atTime){
	var video=document.getElementById(videoId);
	if(atTime>0){
		video.currentTime=atTime;
	}
	var canvas=document.createElement("canvas");
	//.videoWidth + .videoHeight
	var context=canvas.getContext("2d");
	
	context.drawImage(video,0,0,canvas.width,canvas.height);
	return canvas.toDataURL();
}

function VideoElement(){
	this.elementId="";
	this.timeOffset=0;
	
	this.fullLength=0; //.duration
	
	this.begin=0;
	this.end=0;
	
	this.x=0;
	this.y=0;
	
	this.width=0;
	this.height=0;
	
	this.rotation=0;
	
	this.thumbNailAsDataURL="";
	
	function getImage(globalTime){
		var result="";
		if(this.duration==0){
			var video=document.getElementById(this.elementId);
			this.fullLength=video.duration;
		}
		var cutLength=this.fullLength-(this.fullLength-this.end)-this.begin
		if(globalTime>this.timeOffset && globalTime<(this.timeOffset+cutLength)){
			var time=(globalTime-this.timeOffset)+this.begin;
			result=caputreImgAsDataURLFromVideo(this.elementId,time);
		}
		return result;
	}
	
	this.getImage=getImage;
}