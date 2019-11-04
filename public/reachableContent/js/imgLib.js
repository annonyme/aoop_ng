function makeGrayScale(ctx){
	var idata=ctx.getImageData(0,0,ctx.canvas.width,ctx.canvas.height);
	var data=idata.data;
	for(var i=0;i<data.length;i+=4){
		var grayValue=data[i]*0.3+data[i+1]*0.59+data[i+2]*0.11;
		data[i]=grayValue;
		data[i+1]=grayValue;
		data[i+2]=grayValue;
		//dont change alpha value
	}
	ctx.putImageData(idata,0,0);
	return ctx;
}

function makeGrayScaleImage(img,imageType,qual){
	var canvas=document.createElementById("canvas");
	canvas.width=img.width;
	canvas.height=img.height;
	
	var ctx=canvas.getContext("2d");
	ctx.drawImage(img,0,0,canvas.width,canvas.height);
	
	ctx=makeGrayScale(ctx);
	return ctx.canvas.toDataURL("image/"+imageType,qual);
}

//percent scaling size in %, rotate 0 or 90 or 180 or 270 , qual is 1.0->0.1 for jpeg
function rescaleImageAsDataURL(img,percent,rotate,qual){
	var canvas=document.createElement("canvas");
	var ctx=cavnas.getContext("2d");
	var scaling=percent;
	var width=Math.round((img.width/100)*scaling);
	var height=Math.round((img.height/100)*scaling);					
					
	if(rotate>0){
		if(rotate!=180){
			ctx.canvas.width=height;
			ctx.canvas.height=width;
		}
		else{
			ctx.canvas.width=width;
			ctx.canvas.height=height;
		}					
					
		ctx.save();
		ctx.translate(ctx.canvas.width/2,ctx.canvas.height/2);
		ctx.rotate(((Math.PI / 180)*rotate));
		ctx.drawImage(img,0-(width/2),0-(height/2),width,height);
		ctx.restore();
	}
	else{
		ctx.canvas.width=width;
		ctx.canvas.height=height;
		ctx.drawImage(img.image,0,0,width,height);
	}
	return ctx.canvas.toDataURL("image/jpeg",qual);				
}

function loadImgFromFile(file,onloadFunction){
	var URL = window.URL || window.webkitURL;
	var imgURL = URL.createObjectURL(file);
	var img=new Image();
	img.src=imgURL;
	URL.revokeObjectURL(imgURL);
	img.onload=onloadFunction;
	return img;
}

function rotateImage90(img){
	var canvas=document.createElement("canvas");
	var ctx=canvas.getContext("2d");
	
	canvas.width=img.height;
	canvas.height=img.width;
	
	ctx.save();
	ctx.translate(canvas.width,0);
	ctx.rotate(((Math.PI / 180)*90));
	ctx.drawImage(img,0,0,img.width,img.height);
	ctx.restore();
	
	return canvas.toDataURL();
}

function rescaleImageScaleOnLongestSide(img,width){
	var longestSide=img.width;
	if(img.height>img.width){
		longestSide=img.height;
	}	
	var scale=longestSide/width;
	
	if(longestSide<width){
		scale=1;
	}	
	
	var canvas=document.createElement("canvas");
	var ctx=canvas.getContext("2d");
	
	canvas.width=Math.round(img.width/scale);
	canvas.height=Math.round(img.height/scale);
	
	ctx.drawImage(img,0,0,canvas.width,canvas.height);
	
	var newImage=new Image();
	newImage.src=canvas.toDataURL();
	return newImage;
}

function rescaleImageScaleOnLongestSideAsFile(img,width){
	var reImg=rescaleImageScaleOnLongestSide(img,width);
	return convertImgWithDataURISrcToFile(reImg);
}

function reconstructDecColorValueFromSingleDecColorValue(color){
	var r=coded>>16;
	var g=coded>>8 & 0xFF;
	var b=coded & 0xFF;
	return {
		red:r,
		green:g,
		blue:b,
		colorDec:color
	};
}

function singleDecValueColor(r,g,b){
	var color=b | (g << 8) | (r << 16);
	return {
		red:r,
		green:g,
		blue:b,
		colorDec:color
	};
}

function convertImgWithDataURISrcToFile(imgElement){
	return dataURItoBlob(imgElement.src);
}

function setCopyToImgTag(img, copyTagId){
	if(document.getElementById(copyTagId)){
		document.getElementById(copyTagId).src=img.src;
	}
}

// copied from http://stackoverflow.com/questions/9600295/automatically-change-text-color-to-assure-readability
function invertColor(hexTripletColor) {
    var color = hexTripletColor;
    color = color.substring(1);           // remove #
    color = parseInt(color, 16);          // convert to integer
    color = 0xFFFFFF ^ color;             // invert three bytes
    color = color.toString(16);           // convert to hex
    color = ("000000" + color).slice(-6); // pad with leading zeros
    color = "#" + color;                  // prepend #
    return color;
}