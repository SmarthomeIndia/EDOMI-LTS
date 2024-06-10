/* 
*/ 
function camView_getPixel(property,x,y,dstW,dstH,getRaw) {
	if (property.srctyp==1) {
		var xn=((1-x/dstW*2))*property.fov*property.aspect;
		var yn=((1-y/dstH*2))*property.fov;
					
		//Rotation
		var xp=-(-property.cosZ*xn-(property.sinZ*yn));
		var yp=(property.sinZ*xn+(-property.cosZ*yn));
		if (getRaw===true) {return [xp,yp,0];}

		//Umrechnen und Translation
		var xr=parseInt((xp+property.xoffset)*property.srcR+property.srcMx);
		var yr=parseInt((yp+property.yoffset)*property.srcR+property.srcMy);
		
		if (xr<0 || xr>=property.srcw || yr<0 || yr>=property.srch) {return [-1,-1];}
		
		return [xr,yr];
		
	} else if (property.srctyp>=2) {
		var xn=((1-x/dstW*2)+Math.tan(property.xoffset*Math.PI/2)/Math.tan(property.fov))*Math.tan(property.fov)*property.aspect;
		var yn=((1-y/dstH*2)-Math.tan(property.yoffset*Math.PI/2)/Math.tan(property.fov))*Math.tan(property.fov);

		//Definitionslücke bei 0/0 => Nachbarpixel nehmen
		if (xn==0 && yn==0) {var xn=((1-(x+1)/dstW*2)+Math.tan(property.xoffset*Math.PI/2)/Math.tan(property.fov))*Math.tan(property.fov)*property.aspect;}

		//Projektion
		var d=Math.sqrt(xn*xn+yn*yn);
		var c=Math.atan(d);
		var sin_c=Math.sin(c);
		var cos_c=Math.cos(c);
		var a1=Math.asin(cos_c*property.sin_a_1+yn*sin_c*property.cos_a_1/d)+Math.PI/2;
		var a2=Math.atan2(xn*sin_c,(d*property.cos_a_1*cos_c-yn*property.sin_a_1*sin_c))-Math.PI/2;

		//Kugeloberfläche
		var zp=Math.sin(a1)*Math.sin(a2);
		if (getRaw!==true && zp>0) {return [-1,-1];}
		var xp=Math.sin(a1)*Math.cos(a2);
		var yp=Math.cos(a1);

		//Zentralprojektion
		xp=xp/(1-zp/property.distort);
		yp=yp/(1-zp/property.distort);

		//Z-Rotation
		var xpp=property.cosZ*xp-property.sinZ*yp;
		var ypp=property.sinZ*xp+property.cosZ*yp;
		if (getRaw===true) {return [xp,yp,zp];}
					
		//Umrechnen
		var xr=parseInt(xpp*property.srcR+property.srcMx);
		var yr=parseInt(ypp*property.srcR+property.srcMy);

		return [xr,yr];		
	}
}
