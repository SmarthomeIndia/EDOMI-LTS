/* 
*/ 
importScripts('camview_global.js');

onmessage=function(e) {
	var property={};
	var src={};
	var dst={};

	property.dstw=e.data.dstw;
	property.dsth=e.data.dsth;
	property.aspect=e.data.aspect;
	property.srctyp=e.data.srctyp;
	property.fov=e.data.fov;
	property.angle1=e.data.angle1;
	property.angle2=e.data.angle2;
	property.xoffset=e.data.xoffset;
	property.yoffset=e.data.yoffset;
	property.distort=e.data.distort;
	property.srcw=e.data.srcw;
	property.srch=e.data.srch;
	property.srcMx=e.data.srcMx;
	property.srcMy=e.data.srcMy;
	property.srcR=e.data.srcR;
	property.sin_a_1=e.data.sin_a_1;
	property.cos_a_1=e.data.cos_a_1;
	property.sinZ=e.data.sinZ;
	property.cosZ=e.data.cosZ;	
	src.imgdata=e.data.srcdata;
	dst.imgdata=e.data.dstdata;

	for (var y=0;y<property.dsth;y++) {
		for (var x=0;x<property.dstw;x++) {
			var p=camView_getPixel(property,x,y,property.dstw,property.dsth);
			if (p[0]>=0) {
				var i1=(p[0]*4)+(p[1]*src.imgdata.width*4);
				var i2=(parseInt(property.dstw-x-1)*4)+(parseInt(y)*property.dstw*4);
				dst.imgdata.data[i2]=src.imgdata.data[i1];
				dst.imgdata.data[i2+1]=src.imgdata.data[i1+1];
				dst.imgdata.data[i2+2]=src.imgdata.data[i1+2];
				dst.imgdata.data[i2+3]=255;
			}
		}
	}

    postMessage({dstdata:dst.imgdata});
};
