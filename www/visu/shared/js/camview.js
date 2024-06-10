/* 
*/ 

function class_camView() {
	var that=this;

	//---------------------------
	var renderMode=2;	//Rendermodus (ggf. anpassbar): 0=JS erzwingen, 1=webGL erzwingen, 2=automatisch (browseranhängig)
	//---------------------------
	
	var webgl={active:true,program:false};
	var src={};
	var dst={};
	var property={};
	var downscaled=false;
	var renderWorker=null;
	var renderWorkerBuffer=null;
	
	this.setProperty=function(p,value) {
		property[p]=value;
	}

	this.initLoadRender=function(cb,cbErr) {	
		//Init, loadImage, Render
		that.init();
		that.loadImage(true,cb,cbErr);
	}
	
	this.loadRender=function(cb,cbErr) {	
		//loadImage, Render
		that.loadImage(true,cb,cbErr);
	}

	this.init=function() {
		if (property.srccanvas===false) {
			property.preview=false;
			src.canvas=document.createElement('canvas');
		} else {
			property.preview=true;
			src.canvas=property.srccanvas;
		}
	
		if (!src.canvas) {return false;}
		src.ctx=src.canvas.getContext("2d");

		dst.canvas=property.dstcanvas;
		if (!dst.canvas) {return false;}

		if (renderMode==0) {
			webgl.active=false;
			dst.ctx=dst.canvas.getContext("2d");
		} else if (renderMode==1) {
			webgl.active=true;
			dst.ctx=dst.canvas.getContext("webgl") || dst.canvas.getContext("experimental-webgl");
		} else {
			webgl.active=true;
			try {
				dst.ctx=dst.canvas.getContext("webgl") || dst.canvas.getContext("experimental-webgl");
			} catch(e) {}
			if (!dst.ctx) {
				webgl.active=false;
				dst.ctx=dst.canvas.getContext("2d");
			}
		}
	}

	this.webgl_init=function() {

		if (!src.img.complete || !dst.ctx) {return false;}

		webgl.program=createProgram();

		var texCoordLocation=dst.ctx.getAttribLocation(webgl.program,"a_texCoord");
		var texCoordBuffer=dst.ctx.createBuffer();
		dst.ctx.bindBuffer(dst.ctx.ARRAY_BUFFER,texCoordBuffer);
		dst.ctx.bufferData(dst.ctx.ARRAY_BUFFER,new Float32Array([0.0,0.0,1.0,0.0,0.0,1.0,0.0,1.0,1.0,0.0,1.0,1.0]),dst.ctx.STATIC_DRAW);
		dst.ctx.enableVertexAttribArray(texCoordLocation);
		dst.ctx.vertexAttribPointer(texCoordLocation,2,dst.ctx.FLOAT,false,0,0);

		var texture=dst.ctx.createTexture();
		dst.ctx.bindTexture(dst.ctx.TEXTURE_2D,texture);
		dst.ctx.texParameteri(dst.ctx.TEXTURE_2D,dst.ctx.TEXTURE_WRAP_S,dst.ctx.CLAMP_TO_EDGE);
		dst.ctx.texParameteri(dst.ctx.TEXTURE_2D,dst.ctx.TEXTURE_WRAP_T,dst.ctx.CLAMP_TO_EDGE);
		dst.ctx.texParameteri(dst.ctx.TEXTURE_2D,dst.ctx.TEXTURE_MIN_FILTER,dst.ctx.LINEAR);
		dst.ctx.texParameteri(dst.ctx.TEXTURE_2D,dst.ctx.TEXTURE_MAG_FILTER,dst.ctx.LINEAR);
		dst.ctx.texImage2D(dst.ctx.TEXTURE_2D,0,dst.ctx.RGB,dst.ctx.RGB,dst.ctx.UNSIGNED_BYTE,src.img);

		return true;

		function createProgram() {
			//VERTEX_SHADER
			var n="";
			n+="attribute vec2 a_position;";
			n+="attribute vec2 a_texCoord;";
			n+="uniform vec2 u_resolution;";
			n+="varying vec2 v_texCoord;";
			n+="void main() {";
			n+="	float fx = (a_position.x/u_resolution.x)*2.0-1.0;";
			n+="	float fy = (a_position.y/u_resolution.y)*2.0-1.0;";
			n+="	gl_Position = vec4(vec2(fx,fy), 0, 1);";
			n+="	v_texCoord = a_texCoord;";
			n+="}";
			var s1=n;
	
			//FRAGMENT_SHADER
			var n="";
			n+="precision highp float;";	//### mediump wäre schneller, aber iOS rendert dann bei 0/0 in schwarz... (Definitionslücke)
			n+="varying vec2 v_texCoord;";
			n+="uniform sampler2D u_image;";
	
			n+="uniform float property_aspect;";
			n+="uniform float property_fov;";
			n+="uniform float property_xoffset;";
			n+="uniform float property_yoffset;";
			n+="uniform float property_distort;";
			n+="uniform float property_srcw;";
			n+="uniform float property_srch;";
			n+="uniform float property_srcRraw;";
			n+="uniform float property_srcRfak;";
			n+="uniform float property_sin_a_1;";
			n+="uniform float property_cos_a_1;";
			n+="uniform float property_sinZ;";
			n+="uniform float property_cosZ;";
	
			if (property.srctyp==1) {
				n+="void main() {";
				n+="	vec2 p=v_texCoord;";
	
				n+="	float xn=(1.0-p.x*2.0)*property_fov*property_aspect;";
				n+="	float yn=(1.0-p.y*2.0)*property_fov;";
	
				n+="	float xp=-(-property_cosZ*xn-(property_sinZ*yn));";
				n+="	float yp=(property_sinZ*xn+(-property_cosZ*yn));";
	
				n+="	float src_aspect=property_srcw/property_srch;";
				n+="	xp-=property_xoffset;";
				n+="	yp=(yp-property_yoffset)*src_aspect;";
				n+="	float xr=(1.0-xp/2.0)-0.5;";
				n+="	float yr=(1.0-yp/2.0)-0.5;";
				n+="	gl_FragColor = texture2D(u_image,vec2(xr,yr));";
				n+="}";	
				
			} else if (property.srctyp>=2) {
				n+="void main() {";
				n+="	const float pi=3.1415927;";
				n+="	vec2 p=v_texCoord;";
				n+="	float xn=((1.0-p.x*2.0)-tan(property_xoffset*pi/2.0)/tan(property_fov))*tan(property_fov)*property_aspect;";
				n+="	float yn=((1.0-p.y*2.0)+tan(property_yoffset*pi/2.0)/tan(property_fov))*tan(property_fov);";
	
				n+="	float d=sqrt(xn*xn+yn*yn);";
				n+="	float c=atan(d);";
				n+="	float sin_c=sin(c);";
				n+="	float cos_c=cos(c);";
				n+="	float a1=asin(cos_c*-property_sin_a_1+yn*sin_c*property_cos_a_1/d)+pi/2.0;";
				n+="	float a2=atan(xn*sin_c,(d*property_cos_a_1*cos_c-yn*-property_sin_a_1*sin_c))-pi/2.0;";
	
				n+="	float zp=sin(a1)*sin(a2);";
				n+="	float xp=sin(a1)*cos(a2);";
				n+="	float yp=cos(a1);";
	
				n+="	xp=xp/(1.0-zp/property_distort);";
				n+="	yp=yp/(1.0-zp/property_distort);";
	
				n+="	float xpp=property_cosZ*xp-property_sinZ*yp;";
				n+="	float ypp=property_sinZ*xp+property_cosZ*yp;";
	
				n+="	float src_aspect=property_srcw/property_srch;";
				n+="	xp=xpp*property_srcRraw/property_srcRfak;";
				n+="	yp=ypp*property_srcRraw*src_aspect/property_srcRfak;";
				n+="	float xr=(1.0-xp/2.0)-0.5;";
				n+="	float yr=(1.0-yp/2.0)-0.5;";
	
				n+="	gl_FragColor = texture2D(u_image,vec2(xr,yr));";	
				n+="}";	
			}
			var s2=n;
	
			var prg=dst.ctx.createProgram();
			dst.ctx.attachShader(prg,compileShader(s1,dst.ctx.VERTEX_SHADER));
			dst.ctx.attachShader(prg,compileShader(s2,dst.ctx.FRAGMENT_SHADER));
			dst.ctx.linkProgram(prg);
			dst.ctx.useProgram(prg);
			return prg;
	
			function compileShader(src,typ) {
				var shader=dst.ctx.createShader(typ);
				dst.ctx.shaderSource(shader,src);
				dst.ctx.compileShader(shader);
				return shader;
			};
		}
	}

	this.webgl_render=function() {
		if (property.preview) {
			if (property.aspect>=1) {
				dst.canvas.style.width=parseInt(property.dstcanvassize)+"px";
				dst.canvas.style.height=parseInt(property.dstcanvassize/property.aspect)+"px";			
			} else {
				dst.canvas.style.width=parseInt(property.dstcanvassize*property.aspect)+"px";
				dst.canvas.style.height=parseInt(property.dstcanvassize)+"px";			
			}
		}
		dst.canvas.width=property.dstw;
		dst.canvas.height=property.dsth;

		dst.ctx.viewport(0,0,dst.canvas.width,dst.canvas.height);

		setShaderVar("property_aspect",property.aspect);
		setShaderVar("property_fov",property.fov);
		setShaderVar("property_xoffset",property.xoffset);
		setShaderVar("property_yoffset",property.yoffset);
		setShaderVar("property_distort",property.distort);
		setShaderVar("property_srcw",property.srcw);
		setShaderVar("property_srch",property.srch);
		setShaderVar("property_srcRraw",(property.db_srcr+1000)/1000);	//### wie property.srcR, jedoch ohne Radius
		setShaderVar("property_srcRfak",src.w/src.h);					//### relativer Radius (für Modus 1)
		setShaderVar("property_sin_a_1",property.sin_a_1);
		setShaderVar("property_cos_a_1",property.cos_a_1);
		setShaderVar("property_sinZ",property.sinZ);
		setShaderVar("property_cosZ",property.cosZ);

		var resolutionLocation=dst.ctx.getUniformLocation(webgl.program,"u_resolution");
		dst.ctx.uniform2f(resolutionLocation,dst.canvas.width,dst.canvas.height);
	
		var positionLocation=dst.ctx.getAttribLocation(webgl.program,"a_position");
		var buffer=dst.ctx.createBuffer();
		dst.ctx.bindBuffer(dst.ctx.ARRAY_BUFFER, buffer);
		dst.ctx.bufferData(dst.ctx.ARRAY_BUFFER,new Float32Array([0,0,dst.canvas.width,0,0,dst.canvas.height,0,dst.canvas.height,dst.canvas.width,0,dst.canvas.width,dst.canvas.height]),dst.ctx.STATIC_DRAW);
		dst.ctx.enableVertexAttribArray(positionLocation);
		dst.ctx.vertexAttribPointer(positionLocation,2,dst.ctx.FLOAT,false,0,0);

		dst.ctx.drawArrays(dst.ctx.TRIANGLES,0,6);
		callback(property.callback_render);

		function setShaderVar(pName,pValue) {
			var tmp=dst.ctx.getUniformLocation(webgl.program,pName);
			dst.ctx.uniform1f(tmp,pValue);
		}
	}
	
	this.loadImage=function(doRender,cb,cbErr) {	
		downscaled=false;
		src.img=new Image();
		src.img.onload=function() {
			if (webgl.active) {
				if (!that.webgl_init()) {
					callback(cbErr);
					return;
				}
			}
			if (doRender) {
				that.render(cb);
			} else {
				callback(cb);
			}	
		};
		src.img.onerror=function() {
			callback(cbErr);
		};
		src.img.src=property.url;
	}

	this.loadImageToImage=function(cb,cbErr) {
		property.dstimage.onload=function() {
			callback(cb);
		};
		property.dstimage.onerror=function() {
			callback(cbErr);
		};
		property.dstimage.src=property.url;	
	}

	this.render=function(cb) {
		property.callback_render=cb;
		if (scaleSrcImage()) {
			getPropertys();
	
			if (property.preview) {
				if (property.aspect>=1) {
					property.dstw=parseInt(property.dstcanvassize);
					property.dsth=parseInt(property.dstcanvassize/property.aspect);
				} else {
					property.dstw=parseInt(property.dstcanvassize*property.aspect);
					property.dsth=parseInt(property.dstcanvassize);
				}
			} else {
				property.dstw=property.db_dstw;
				property.dsth=property.db_dsth;
			}
	
			var res=getResolution();
			if (property.dstw>res[0] || property.dsth>res[1]) {
				property.dstw=res[0];
				property.dsth=res[1];
			}
	
			if (property.dstw<1) {property.dstw=1;}
			if (property.dsth<1) {property.dsth=1;}
	
			if (property.preview) {
				var psize=3;
				var size=psize*(Math.sqrt(src.w*src.w+src.h*src.h)/Math.sqrt(src.cw*src.cw+src.ch*src.ch));
				src.ctx.fillStyle=apps_colorSelected;
				for (var x=0;x<property.dstw;x++) {
					var p=camView_getPixel(property,x,0,property.dstw,property.dsth);
					if (p[0]>=0) {src.ctx.fillRect(p[0]-size/2,p[1]-size/2,size,size);}
					var p=camView_getPixel(property,x,property.dsth,property.dstw,property.dsth);
					if (p[0]>=0) {src.ctx.fillRect(p[0]-size/2,p[1]-size/2,size,size);}
				}
				for (var y=0;y<property.dsth;y++) {
					var p=camView_getPixel(property,0,y,property.dstw,property.dsth);
					if (p[0]>=0) {src.ctx.fillRect(p[0]-size/2,p[1]-size/2,size,size);}
					var p=camView_getPixel(property,property.dstw,y,property.dstw,property.dsth);
					if (p[0]>=0) {src.ctx.fillRect(p[0]-size/2,p[1]-size/2,size,size);}
				}
			}

			if (webgl.active) {
				that.webgl_render();
			} else {	
				if (renderWorker) {
					renderWorkerBuffer=property;
				} else {
					render_worker();
				}
			}
		}
	}
	
	function render_worker() {
		dst.imgdata=new ImageData(property.dstw,property.dsth);
		renderWorker=new Worker("../shared/js/camview_worker.js");
		renderWorker.onmessage=render_workerMsg;
		renderWorker.postMessage({
			dstw:property.dstw,
			dsth:property.dsth,
			aspect:property.aspect,
			srctyp:property.srctyp,
			fov:property.fov,
			angle1:property.angle1,
			angle2:property.angle2,
			xoffset:property.xoffset,
			yoffset:property.yoffset,
			distort:property.distort,
			srcw:property.srcw,
			srch:property.srch,
			srcMx:property.srcMx,
			srcMy:property.srcMy,
			srcR:property.srcR,
			sin_a_1:property.sin_a_1,
			cos_a_1:property.cos_a_1,
			sinZ:property.sinZ,
			cosZ:property.cosZ,
			srcdata:src.imgdata,
			dstdata:dst.imgdata
		});
	}

	function render_workerMsg(event) {
		if (property.preview) {
			if (property.aspect>=1) {
				dst.canvas.style.width=parseInt(property.dstcanvassize)+"px";
				dst.canvas.style.height=parseInt(property.dstcanvassize/property.aspect)+"px";			
			} else {
				dst.canvas.style.width=parseInt(property.dstcanvassize*property.aspect)+"px";
				dst.canvas.style.height=parseInt(property.dstcanvassize)+"px";			
			}
		}

		dst.canvas.width=event.data.dstdata.width;
		dst.canvas.height=event.data.dstdata.height;
		dst.ctx.putImageData(event.data.dstdata,0,0);

		renderWorker.terminate();
		renderWorker=null;

		callback(property.callback_render);

		if (renderWorkerBuffer) {
			property=renderWorkerBuffer;
			renderWorkerBuffer=null;
			render_worker();
		}
	};

	function scaleSrcImage() {
		if (downscaled!=property.db_srcs) {
			var w=parseInt(src.img.width*property.db_srcs/100);
			var h=parseInt(src.img.height*property.db_srcs/100);
			if (w==0 || h==0) {return false;}

			if (property.srctyp==1) {			//2D
				src.w=w;
				src.h=h;
				if (src.w>=src.h) {
					src.r=src.w/2;
				} else {
					src.r=src.h/2;
				}
			} else if (property.srctyp==2) {	//3D: Kreis
				if (w>=h) {
					src.w=w;
					src.h=h;
					src.r=src.h/2;
				} else {
					src.w=w;
					src.h=h;
					src.r=src.w/2;
				}
			} else if (property.srctyp==3) {	//3D: Ellipse
				if (w>=h) {
					src.w=w;
					src.h=w;
					src.r=src.h/2;
				} else {
					src.w=h;
					src.h=h;
					src.r=src.w/2;
				}
			}

			src.canvas.width=src.w;
			src.canvas.height=src.h;
			src.ctx.drawImage(src.img,0,0,src.w,src.h);
			src.rawimgdata=src.ctx.getImageData(0,0,src.w,src.h);

			if (property.preview) {
				var cw=property.srccanvassize;
				var ch=property.srccanvassize;
				var a=src.w/src.h;
				if (a<1) {cw*=a;}
				if (a>1) {ch/=a;}
				src.canvas.style.width=parseInt(cw)+"px";
				src.canvas.style.height=parseInt(ch)+"px";
				src.cw=parseInt(cw);
				src.ch=parseInt(ch);
			}
			downscaled=property.db_srcs;
		}
		src.ctx.putImageData(src.rawimgdata,0,0);
		src.imgdata=src.ctx.getImageData(0,0,src.w,src.h);
		return true;
	}
	
	function getPropertys() {	
		if (property.db_dstw<=0) {property.db_dstw=1;}
		if (property.db_dsth<=0) {property.db_dsth=1;}
		property.aspect=property.db_dstw/property.db_dsth;
		
		//auch per VSE 20 anpassbar:
		if (property.db_zoom<0) {property.db_zoom=0;}
		if (property.db_zoom>500) {property.db_zoom=500;}
		if (property.db_a1<-90) {property.db_a1=-90;}
		if (property.db_a1>90) {property.db_a1=90;}
		if (property.db_a2<-180) {property.db_a2=-180;}
		if (property.db_a2>180) {property.db_a2=180;}
		if (property.db_x<-100) {property.db_x=-100;}
		if (property.db_x>100) {property.db_x=100;}
		if (property.db_y<-100) {property.db_y=-100;}
		if (property.db_y>100) {property.db_y=100;}
		if (property.srctyp==1) {property.fov=1-property.db_zoom/500;} else if (property.srctyp>=2) {property.fov=((1-property.db_zoom/500)*90)/180*Math.PI;}
		if (property.fov==0) {property.fov=0.001;}	
		property.angle1=property.db_a1*Math.PI/180;
		property.angle2=property.db_a2*Math.PI/180;
		property.xoffset=property.db_x/100;
		property.yoffset=property.db_y/100;

		if (property.db_srcd>=0) {
			property.distort=Math.PI*(1+property.db_srcd/100);
		} else {
			property.distort=Math.PI/(1+Math.abs(property.db_srcd/100));
		}

		property.srcMx=src.w/2;
		property.srcMy=src.h/2;		
		property.srcR=parseInt(src.r*(property.db_srcr+1000)/1000);
		property.sin_a_1=Math.sin(property.angle1);
		property.cos_a_1=Math.cos(property.angle1);
		property.sinZ=Math.sin(property.angle2);
		property.cosZ=Math.cos(property.angle2);
		property.srcw=src.w;
		property.srch=src.h;
	}

	function getResolution() {
		var ax=new Array();
		var ay=new Array();
		
		var p1=camView_getPixel(property,0+0.001,property.db_dsth+0.001,property.db_dstw,property.db_dsth,true);
		var p2=camView_getPixel(property,property.db_dstw+0.001,property.db_dsth+0.001,property.db_dstw,property.db_dsth,true);
		ax.push(parseInt(Math.sqrt((p1[0]-p2[0])*(p1[0]-p2[0])+(p1[1]-p2[1])*(p1[1]-p2[1])+(p1[2]-p2[2])*(p1[2]-p2[2]))*property.srcR));
		var p1=camView_getPixel(property,0+0.001,property.db_dsth/2+0.001,property.db_dstw,property.db_dsth,true);
		var p2=camView_getPixel(property,property.db_dstw+0.001,property.db_dsth/2+0.001,property.db_dstw,property.db_dsth,true);
		ax.push(parseInt(Math.sqrt((p1[0]-p2[0])*(p1[0]-p2[0])+(p1[1]-p2[1])*(p1[1]-p2[1])+(p1[2]-p2[2])*(p1[2]-p2[2]))*property.srcR));
		var p1=camView_getPixel(property,0+0.001,0+0.001,property.db_dstw,property.db_dsth,true);
		var p2=camView_getPixel(property,property.db_dstw+0.001,0+0.001,property.db_dstw,property.db_dsth,true);
		ax.push(parseInt(Math.sqrt((p1[0]-p2[0])*(p1[0]-p2[0])+(p1[1]-p2[1])*(p1[1]-p2[1])+(p1[2]-p2[2])*(p1[2]-p2[2]))*property.srcR));

		var p1=camView_getPixel(property,property.db_dstw+0.001,0+0.001,property.db_dstw,property.db_dsth,true);
		var p2=camView_getPixel(property,property.db_dstw+0.001,property.db_dsth+0.001,property.db_dstw,property.db_dsth,true);
		ay.push(parseInt(Math.sqrt((p1[0]-p2[0])*(p1[0]-p2[0])+(p1[1]-p2[1])*(p1[1]-p2[1])+(p1[2]-p2[2])*(p1[2]-p2[2]))*property.srcR));
		var p1=camView_getPixel(property,property.db_dstw/2+0.001,0+0.001,property.db_dstw,property.db_dsth,true);
		var p2=camView_getPixel(property,property.db_dstw/2+0.001,property.db_dsth+0.001,property.db_dstw,property.db_dsth,true);
		ay.push(parseInt(Math.sqrt((p1[0]-p2[0])*(p1[0]-p2[0])+(p1[1]-p2[1])*(p1[1]-p2[1])+(p1[2]-p2[2])*(p1[2]-p2[2]))*property.srcR));
		var p1=camView_getPixel(property,0+0.001,0+0.001,property.db_dstw,property.db_dsth,true);
		var p2=camView_getPixel(property,0+0.001,property.db_dsth+0.001,property.db_dstw,property.db_dsth,true);
		ay.push(parseInt(Math.sqrt((p1[0]-p2[0])*(p1[0]-p2[0])+(p1[1]-p2[1])*(p1[1]-p2[1])+(p1[2]-p2[2])*(p1[2]-p2[2]))*property.srcR));

		var rx=Math.max(ax[0],ax[1],ax[2]);
		var ry=Math.max(ay[0],ay[1],ay[2]);

		if (property.aspect>=1) {
			ry=parseInt(rx/property.aspect);
		} else {
			rx=parseInt(ry*property.aspect);
		}

		if (rx<1) {rx=1;}
		if (ry<1) {ry=1;}

		return [rx,ry];
	}

	function callback(func) {
		if (func!==undefined) {eval(func);}
	}
}
