/* 
*/ 
function graphics_svg_icon(icon) {
	var n="<svg class='svgIcon' viewBox='0 0 100 100'>";
	if (icon==0) {			//Aus
		n+="<circle cx='50' cy='50' r='40' stroke='currentColor' stroke-width='10' fill='none'/>";
	} else if (icon==1) {	//Ein
		n+="<circle cx='50' cy='50' r='40' stroke='currentColor' stroke-width='10' fill='none'/>";
		n+="<line x1='50' y1='0' x2='50' y2='45' stroke='currentColor' stroke-width='10'/>";
	} else if (icon==2) {	//Hinzufügen
		n+="<line x1='50' y1='10' x2='50' y2='90' stroke='currentColor' stroke-width='10'/>";
		n+="<line x1='10' y1='50' x2='90' y2='50' stroke='currentColor' stroke-width='10'/>";
	} else if (icon==3) {	//Play
		n+="<polygon points='10,10 90,50 10,90' stroke='currentColor' stroke-width='10' fill='none'/>";
	} else if (icon==4) {	//Record
		n+="<circle cx='50' cy='50' r='40' stroke='currentColor' stroke-width='10' fill='currentColor'/>";
	}
	n+="</svg>";
	return n;
}

function graphics_svg_centerCircle(fgcolor,w,h,para) {
	if (w>=h) {var r=h/2;} else {var r=w/2;}
	if ((r*para.size/100-1/2)<=0) {return "";}
	return "<circle cx='"+(w/2)+"' cy='"+(h/2)+"' r='"+(r*para.size/100-1/2)+"' stroke='"+fgcolor+"' stroke-width='1' "+((para.solid)?"":"stroke-dasharray='1,1'")+" vector-effect='non-scaling-stroke' fill='none'/>";
}

function graphics_svg_centerArc(fgcolor,w,h,a1,a2,para) {
	var mx=w/2;
	var my=h/2;
	if (w>=h) {var r=h/2;} else {var r=w/2;}
	if ((r*para.size/100-1/2)<=0) {return "";}
	var p1=getPolar(a1,(r*para.size/100-1/2));
	var p2=getPolar(a2-0.01,(r*para.size/100-1/2));

	if (para.linewidth===undefined) {para.linewidth=1;} else {para.linewidth=r*para.linewidth/100;}
	if (para.linecap===undefined) {para.linecap=1;}

	return "<path d='M "+p1.x+" "+p1.y+" A "+(r*para.size/100-1/2)+" "+(r*para.size/100-1/2)+" 0 "+((a2-a1>180)?1:0)+" 1 "+p2.x+" "+p2.y+"' fill='none' stroke='"+fgcolor+"' stroke-linecap='"+((para.linecap==2)?"round":"butt")+"' stroke-width='"+para.linewidth+"' "+((para.solid)?"":"stroke-dasharray='1,1'")+" vector-effect='non-scaling-stroke'/>";

	function getPolar(angle,radius) {
		return {x:Math.sin((-angle)*Math.PI/180)*radius+mx,y:Math.cos((-angle)*Math.PI/180)*radius+my};
	}
}

function graphics_svg_centerLine(fgcolor,w,h,para) {
	if (w>=h) {
		return "<line x1='"+(w/2*para.offset/100)+"' y1='"+(h/2)+"' x2='"+(w-w/2*para.offset/100)+"' y2='"+(h/2)+"' stroke='"+fgcolor+"' stroke-width='1' "+((para.solid)?"":"stroke-dasharray='1,1'")+" vector-effect='non-scaling-stroke' fill='none'/>";
	} else {
		return "<line x1='"+(w/2)+"' y1='"+(h/2*para.offset/100)+"' x2='"+(w/2)+"' y2='"+(h-h/2*para.offset/100)+"' stroke='"+fgcolor+"' stroke-width='1' "+((para.solid)?"":"stroke-dasharray='1,1'")+" vector-effect='non-scaling-stroke' fill='none'/>";
	}
}

function graphics_svg_scale(fgcolor,w,h,para) {
	var n="";
	if (para.tickCount>=1) {	
		if (isNaN(parseFloat(para.angleFrom))) {para.angleFrom=0;}
		if (isNaN(parseFloat(para.angleTo))) {para.angleTo=360;}
		if (isNaN(parseFloat(para.tickSizeTo))) {para.tickSizeTo=0;}
		if (isNaN(parseFloat(para.subtickSizeFrom))) {para.subtickSizeFrom=0;}
		if (isNaN(parseFloat(para.subtickSizeTo))) {para.subtickSizeTo=0;}
		if (isNaN(parseFloat(para.captionSize))) {para.captionSize=0;}
		if (isNaN(parseFloat(para.contourSize))) {para.contourSize=0;}

		para.size=parseFloat(para.size);
		if (isNaN(para.size)) {para.size=0;}
		if (para.size<0) {para.size=0;}
		if (para.size>100) {para.size=100;}

		if (para.mode==1) {
			var mx=w/2;
			var my=h/2;
			if (w>=h) {var r=h/2;} else {var r=w/2;}
			r*=para.size/100;

			if (para.angleFrom>para.angleTo) {
				var tmp=para.angleFrom;
				para.angleFrom=para.angleTo;
				para.angleTo=tmp;
			}
			var x0=para.angleFrom;
			var x1=para.angleTo;

			var f1=(x1-x0)/para.tickCount;			
			var f2=f1/para.subtickCount;
			if (para.contourWidth>0) {
				if (Math.abs(x1-x0)>=360) {
					var rr=r-para.contourWidth/2;
					n+="<circle cx='"+mx+"' cy='"+my+"' r='"+(para.contourSize*rr/100)+"' fill='none' stroke='"+fgcolor+"' stroke-width='"+(para.contourWidth)+"' vector-effect='non-scaling-stroke'/>";
				} else {
					var xa=Math.sin((-(0*f1+x0))*Math.PI/180);
					var ya=Math.cos((-(0*f1+x0))*Math.PI/180);
					var xb=Math.sin((-(para.tickCount*f1+x0))*Math.PI/180);
					var yb=Math.cos((-(para.tickCount*f1+x0))*Math.PI/180);
					var rr=r-para.contourWidth/2;
					n+="<path d='M "+(xa*para.contourSize*rr/100+mx)+" "+(ya*para.contourSize*rr/100+my)+" A "+(para.contourSize*rr/100)+" "+(para.contourSize*rr/100)+" 0 "+((x1-x0>180)?1:0)+" 1 "+(xb*para.contourSize*rr/100+mx)+" "+(yb*para.contourSize*rr/100+my)+"' stroke='"+fgcolor+"' stroke-linecap='butt' stroke-width='"+para.contourWidth+"' fill='none' vector-effect='non-scaling-stroke'/>";
				}
			}
		} else {
			var horizontal=((w>=h)?true:false);				
			var ww=w-w/2*(100-para.size)/100;
			var hh=h-h/2*(100-para.size)/100;
			
			if (horizontal) {
				var x0=para.tickWidth/2+w/2*(100-para.size)/100;
				var x1=ww-para.tickWidth/2;
			} else {
				var x0=para.tickWidth/2+h/2*(100-para.size)/100;
				var x1=hh-para.tickWidth/2;
			}

			var f1=(x1-x0)/para.tickCount;			
			var f2=f1/para.subtickCount;
			if (para.contourWidth>0) {
				if (horizontal) {
					n+="<line x1='"+(w/2*(100-para.size)/100)+"' y1='"+(para.contourSize*h/100)+"' x2='"+(w-w/2*(100-para.size)/100)+"' y2='"+(para.contourSize*h/100)+"' stroke='"+fgcolor+"' stroke-linecap='butt' stroke-width='"+para.contourWidth+"' vector-effect='non-scaling-stroke'/>";
				} else {
					n+="<line x1='"+(para.contourSize*w/100)+"' y1='"+(h/2*(100-para.size)/100)+"' x2='"+(para.contourSize*w/100)+"' y2='"+(h-h/2*(100-para.size)/100)+"' stroke='"+fgcolor+"' stroke-linecap='butt' stroke-width='"+para.contourWidth+"' vector-effect='non-scaling-stroke'/>";
				}
			}
		}
					
		for (var t1=0;t1<=para.tickCount;t1++) {
			draw_tick(t1*f1+x0);
			if (t1<para.tickCount) {
				for (var t2=1;t2<=para.subtickCount-1;t2++) {
					draw_subtick(t1*f1+x0+t2*f2);
				}
			}
		}
	}
	return n;

	function draw_tick(pos) {
		if (para.mode==1) {
			var x=Math.sin((-pos)*Math.PI/180);
			var y=Math.cos((-pos)*Math.PI/180);
			n+="<line x1='"+(x*para.tickSizeFrom*r/100+mx)+"' y1='"+(y*para.tickSizeFrom*r/100+my)+"' x2='"+(x*para.tickSizeTo*r/100+mx)+"' y2='"+(y*para.tickSizeTo*r/100+my)+"' stroke='"+fgcolor+"' stroke-linecap='butt' stroke-width='"+para.tickWidth+"' vector-effect='non-scaling-stroke'/>";
			if (para.captionStep>0 && t1%para.captionStep==0) {
				var tmp=para.captionRangeFrom+t1*(para.captionRangeTo-para.captionRangeFrom)/para.tickCount;
				if (para.captionFixed>=0) {tmp=parseFloat(tmp).toFixed(para.captionFixed);}
				if ((para.captionRangeFrom>para.captionRangeTo && tmp!=para.captionRangeTo) || (para.captionRangeFrom<para.captionRangeTo && tmp!=para.captionRangeFrom) || Math.abs(para.angleTo-para.angleFrom)<360) {	//bei 360 Grad: den ersten (bzw. den "kleinsten") Tick nicht beschriften
					tmp=tmp+String(para.captionSuffix);
					n+="<text x='"+(x*para.captionSize*r/100+mx)+"' y='"+(y*para.captionSize*r/100+my)+"' text-anchor='middle' dominant-baseline='central' fill='"+fgcolor+"'>"+tmp+"</text>";
				}
			}
		} else {
			if (horizontal) {
				n+="<line x1='"+pos+"' y1='"+(para.tickSizeFrom*h/100)+"' x2='"+pos+"' y2='"+(para.tickSizeTo*h/100)+"' stroke='"+fgcolor+"' stroke-linecap='butt' stroke-width='"+para.tickWidth+"' vector-effect='non-scaling-stroke'/>";
			} else {
				n+="<line x1='"+(para.tickSizeFrom*w/100)+"' y1='"+pos+"' x2='"+(para.tickSizeTo*w/100)+"' y2='"+pos+"' stroke='"+fgcolor+"' stroke-linecap='butt' stroke-width='"+para.tickWidth+"' vector-effect='non-scaling-stroke'/>";
			}
			
			if (para.captionStep>0 && t1%para.captionStep==0) {
				var tmp=para.captionRangeFrom+t1*(para.captionRangeTo-para.captionRangeFrom)/para.tickCount;
				if (para.captionFixed>=0) {tmp=parseFloat(tmp).toFixed(para.captionFixed);}
				tmp=tmp+String(para.captionSuffix);
				if (horizontal) {
					if (t1==0) {
						n+="<text x='"+pos+"' y='"+(para.captionSize*h/100)+"' text-anchor='"+((para.size==100)?"start":"middle")+"' dominant-baseline='central' fill='"+fgcolor+"'>"+tmp+"</text>";
					} else if (t1==para.tickCount) {
						n+="<text x='"+pos+"' y='"+(para.captionSize*h/100)+"' text-anchor='"+((para.size==100)?"end":"middle")+"' dominant-baseline='central' fill='"+fgcolor+"'>"+tmp+"</text>";
					} else {
						n+="<text x='"+pos+"' y='"+(para.captionSize*h/100)+"' text-anchor='middle' dominant-baseline='central' fill='"+fgcolor+"'>"+tmp+"</text>";
					}
				} else {
					if (t1==0) {
						n+="<text x='"+(para.captionSize*w/100)+"' y='"+pos+"' text-anchor='middle' dominant-baseline='"+((para.size==100)?"hanging":"central")+"' fill='"+fgcolor+"'>"+tmp+"</text>";
					} else if (t1==para.tickCount) {
						n+="<text x='"+(para.captionSize*w/100)+"' y='"+pos+"' text-anchor='middle' "+((para.size==100)?"":"dominant-baseline='central'")+" fill='"+fgcolor+"'>"+tmp+"</text>";
					} else {
						n+="<text x='"+(para.captionSize*w/100)+"' y='"+pos+"' text-anchor='middle' dominant-baseline='central' fill='"+fgcolor+"'>"+tmp+"</text>";
					}
				}
			}
		}
	}

	function draw_subtick(pos) {
		if (para.mode==1) {
			var x=Math.sin((-pos)*Math.PI/180);
			var y=Math.cos((-pos)*Math.PI/180);	
			n+="<line x1='"+(x*para.subtickSizeFrom*r/100+mx)+"' y1='"+(y*para.subtickSizeFrom*r/100+my)+"' x2='"+(x*para.subtickSizeTo*r/100+mx)+"' y2='"+(y*para.subtickSizeTo*r/100+my)+"' stroke='"+fgcolor+"' stroke-linecap='butt' stroke-width='"+para.subtickWidth+"' vector-effect='non-scaling-stroke'/>";
		} else {
			if (horizontal) {
				n+="<line x1='"+pos+"' y1='"+(para.subtickSizeFrom*h/100)+"' x2='"+pos+"' y2='"+(para.subtickSizeTo*h/100)+"' stroke='"+fgcolor+"' stroke-linecap='butt' stroke-width='"+para.subtickWidth+"' vector-effect='non-scaling-stroke'/>";
			} else {
				n+="<line x1='"+(para.subtickSizeFrom*w/100)+"' y1='"+pos+"' x2='"+(para.subtickSizeTo*w/100)+"' y2='"+pos+"' stroke='"+fgcolor+"' stroke-linecap='butt' stroke-width='"+para.subtickWidth+"' vector-effect='non-scaling-stroke'/>";
			}
		}
	}	
}

function graphics_svg_clock(fgcolor,w,h,para,koValue) {
	var n="";
	if (para.mode>0) {	

		if (isNaN(parseFloat(para.pointerWidth))) {para.pointerWidth=0;}
		if (isNaN(parseFloat(para.scalaSize))) {para.scalaSize=0;}
		if (isNaN(parseFloat(para.scalaWidth))) {para.scalaWidth=0;}
		if (isNaN(parseFloat(para.minuteSize))) {para.minuteSize=0;}
		if (isNaN(parseFloat(para.hourSize))) {para.hourSize=0;}

		var mx=w/2;
		var my=h/2;
		if (w>=h) {var r=h/2;} else {var r=w/2;}

		if (koValue.toString().substr(2,1)==":") {
			//KO = Uhrzeit-String
			koValue=parseInt(koValue.substr(0,2)*60)+parseInt(koValue.substr(3,2))+parseFloat(koValue.substr(6,2)/60);
		}
		koValue=koValue%1440; //alle 24h wieder bei 0 beginnen

		if (para.pointerWidth==0) {para.pointerWidth=r/30;}
		if (para.minuteSize==0) {para.minuteSize=r-r/6;}
		if (para.hourSize==0) {para.hourSize=r-r/3;}
		if (para.scalaSize==0) {para.scalaSize=r/6;}
		drawScala();
		if (para.contourWidth!="" && parseFloat(para.contourWidth)>=0) {
			if (isNaN(parseFloat(para.contourWidth))) {para.contourWidth=0;}
			if (para.contourWidth==0) {para.contourWidth=r/30;}
			n+="<circle cx='"+mx+"' cy='"+my+"' r='"+(r-para.contourWidth/2)+"' fill='none' stroke='"+fgcolor+"' stroke-width='"+(para.contourWidth)+"' vector-effect='non-scaling-stroke'/>";
		}
		drawPointer((koValue-(60*(koValue/60).toFixed(0)))*(360/60),para.minuteSize,para.pointerWidth);
		drawPointer(koValue/60*(360/12),para.hourSize,para.pointerWidth);
		if (para.mode==2 || para.mode==4) {
			n+="<circle cx='"+mx+"' cy='"+my+"' r='"+para.pointerWidth+"' fill='"+fgcolor+"' stroke='none' vector-effect='non-scaling-stroke'/>";
		}
		if (para.mode==3 || para.mode==4) {
			drawPointer((koValue-parseInt(koValue))*360,para.minuteSize,para.pointerWidth/2);
		}
	}
	return n;

	function drawScala() {
		if (para.fulldayOpacity>0) {
			var tmp=koValue/1440*360-0.01;
			var x1=Math.sin(180*Math.PI/180)*(r-para.scalaSize/4)+mx;
			var y1=Math.cos(180*Math.PI/180)*(r-para.scalaSize/4)+my;
			var x2=Math.sin((-tmp+180)*Math.PI/180)*(r-para.scalaSize/4)+mx;
			var y2=Math.cos((-tmp+180)*Math.PI/180)*(r-para.scalaSize/4)+my;
			n+="<path d='M "+x1+" "+y1+" A "+(r-para.scalaSize/4)+" "+(r-para.scalaSize/4)+" 0 "+((tmp>180)?1:0)+" 1 "+x2+" "+y2+"' fill='none' stroke='"+fgcolor+"' stroke-linecap='butt' stroke-width='"+(para.scalaSize/2)+"' vector-effect='non-scaling-stroke' style='opacity:"+(para.fulldayOpacity/100)+";'/>";
		}

		if (para.scalaOpacity>0) {
			if (para.scalaWidth==0) {para.scalaWidth=r/30;}
			if (para.scalaMode==1) {var step=6;} else {var step=30;}

			for (var t=0;t<359;t+=step) {
				var x=Math.sin(t*Math.PI/180);
				var y=Math.cos(t*Math.PI/180);

				if (t%30==0) {
					n+="<line x1='"+(x*r+mx)+"' y1='"+(y*r+my)+"' x2='"+(x*(r-para.scalaSize)+mx)+"' y2='"+(y*(r-para.scalaSize)+my)+"' stroke='"+fgcolor+"' stroke-linecap='butt' stroke-width='"+para.scalaWidth+"' vector-effect='non-scaling-stroke' style='opacity:"+(para.scalaOpacity/100)+";'/>";
				} else {
					n+="<line x1='"+(x*r+mx)+"' y1='"+(y*r+my)+"' x2='"+(x*(r-para.scalaSize/2)+mx)+"' y2='"+(y*(r-para.scalaSize/2)+my)+"' stroke='"+fgcolor+"' stroke-linecap='butt' stroke-width='"+para.scalaWidth+"' vector-effect='non-scaling-stroke' style='opacity:"+(para.scalaOpacity/100)+";'/>";
				}
			}
		}
	}
		
	function drawPointer(a,len,pw) {
		var x=Math.sin((-a-180)*Math.PI/180)*len+mx;
		var y=Math.cos((-a-180)*Math.PI/180)*len+my;
		if (!isNaN(x) && !isNaN(x)) {
			n+="<line x1='"+mx+"' y1='"+my+"' x2='"+x+"' y2='"+y+"' stroke='"+fgcolor+"' stroke-linecap='round' stroke-width='"+pw+"' vector-effect='non-scaling-stroke'/>";
		}
	}
}

function graphics_svg_gauge(fgcolor,w,h,para,koValue) {
	var n="";
	if (para.mode>0) {	
		var mx=w/2;
		var my=h/2;
		if (w>=h) {var r=h/2;} else {var r=w/2;}

		if (isNaN(parseFloat(para.angleFrom))) {para.angleFrom=0;}
		if (isNaN(parseFloat(para.angleTo))) {para.angleTo=360;}
		if (isNaN(parseFloat(para.pointerWidth))) {para.pointerWidth=0;}
		if (isNaN(parseFloat(para.rangeSize))) {para.rangeSize=0;}

		para.size=parseFloat(para.size);
		if (isNaN(para.size)) {para.size=0;}
		if (para.size<0) {para.size=0;}
		if (para.size>100) {para.size=100;}
		r*=para.size/100;

		para.vmin=parseFloat(para.vmin);
		para.vmax=parseFloat(para.vmax);
		if (para.vmin>para.vmax) {
			var tmp=para.vmin;
			para.vmin=para.vmax;
			para.vmax=tmp;
		}
		var range=Math.abs(para.vmax-para.vmin);
		var values=koValue.split(";",3);

		values[0]=parseFloat(values[0]);
		if (values[0]<para.vmin) {values[0]=para.vmin;}
		if (values[0]>para.vmax) {values[0]=para.vmax;}

		if (para.invert) {values[0]=para.vmax-values[0]+para.vmin;}

		if (para.mode<100) {
			para.angleFrom=parseFloat(para.angleFrom);
			para.angleTo=parseFloat(para.angleTo);		
			if (para.angleFrom>para.angleTo) {
				var tmp=para.angleFrom;
				para.angleFrom=para.angleTo;
				para.angleTo=tmp;
			}
			if (para.angleTo-para.angleFrom>360) {
				para.angleFrom=para.angleTo+360;
			}
			var arange=Math.abs(para.angleTo-para.angleFrom);
			var avalue=parseFloat((arange/range)*(values[0]-para.vmin)+para.angleFrom);				
	
		} else {
			if (w>=h) {
				var arange=w*(para.size)/100;
			} else {
				var arange=h*(para.size)/100;
			}
			var avalue=parseFloat((arange/range)*(values[0]-para.vmin));				
		}

		if (para.rangeSize==0) {para.rangeSize=r/15;}

		drawRange();

		if (!isNaN(avalue)) {
			if (para.mode==100) {
				//Linear: Strich
				if (para.pointerWidth==0) {para.pointerWidth=r/30;}
				if (w>=h) {
					n+="<line x1='"+(w/2*(100-para.size)/100+avalue)+"' y1='"+(h/2-para.rangeSize/2+para.pointerWidth/2)+"' x2='"+(w/2*(100-para.size)/100+avalue)+"' y2='"+(h/2+para.rangeSize/2-para.pointerWidth/2)+"' stroke='"+fgcolor+"' stroke-linecap='round' stroke-width='"+para.pointerWidth+"' vector-effect='non-scaling-stroke'/>";
				} else {
					n+="<line x1='"+(w/2-para.rangeSize/2+para.pointerWidth/2)+"' y1='"+(h/2*(100-para.size)/100+avalue)+"' x2='"+(w/2+para.rangeSize/2-para.pointerWidth/2)+"' y2='"+(h/2*(100-para.size)/100+avalue)+"' stroke='"+fgcolor+"' stroke-linecap='round' stroke-width='"+para.pointerWidth+"' vector-effect='non-scaling-stroke'/>";
				}

			} else if (para.mode==101) {
				//Linear: Kreis
				if (para.pointerWidth>0) {var len=para.pointerWidth;} else {var len=para.rangeSize/2;}
				if (w>=h) {
					n+="<circle cx='"+(w/2*(100-para.size)/100+avalue)+"' cy='"+(h/2)+"' r='"+len+"' fill='"+fgcolor+"' stroke='none' vector-effect='non-scaling-stroke'/>";
				} else {
					n+="<circle cx='"+(w/2)+"' cy='"+(h/2*(100-para.size)/100+avalue)+"' r='"+len+"' fill='"+fgcolor+"' stroke='none' vector-effect='non-scaling-stroke'/>";
				}

			} else if (para.mode==102 || para.mode==103) {
				//Linear: Balken
				if (para.pointerWidth==0) {para.pointerWidth=para.rangeSize;}
				if (para.invert) {
					if (w>=h) {
						n+="<line x1='"+(w-w/2*(100-para.size)/100)+"' y1='"+(h/2)+"' x2='"+(w/2*(100-para.size)/100+avalue)+"' y2='"+(h/2)+"' fill='none' stroke='"+fgcolor+"' stroke-linecap='"+((para.mode==102)?'butt':'round')+"' stroke-width='"+para.pointerWidth+"' vector-effect='non-scaling-stroke'/>";
					} else {
						n+="<line x1='"+(w/2)+"' y1='"+(h-h/2*(100-para.size)/100)+"' x2='"+(w/2)+"' y2='"+(h/2*(100-para.size)/100+avalue)+"' fill='none' stroke='"+fgcolor+"' stroke-linecap='"+((para.mode==102)?'butt':'round')+"' stroke-width='"+para.pointerWidth+"' vector-effect='non-scaling-stroke'/>";
					}
				} else {
					if (w>=h) {
						n+="<line x1='"+(w/2*(100-para.size)/100)+"' y1='"+(h/2)+"' x2='"+(w/2*(100-para.size)/100+avalue)+"' y2='"+(h/2)+"' fill='none' stroke='"+fgcolor+"' stroke-linecap='"+((para.mode==102)?'butt':'round')+"' stroke-width='"+para.pointerWidth+"' vector-effect='non-scaling-stroke'/>";
					} else {
						n+="<line x1='"+(w/2)+"' y1='"+(h/2*(100-para.size)/100)+"' x2='"+(w/2)+"' y2='"+(h/2*(100-para.size)/100+avalue)+"' fill='none' stroke='"+fgcolor+"' stroke-linecap='"+((para.mode==102)?'butt':'round')+"' stroke-width='"+para.pointerWidth+"' vector-effect='non-scaling-stroke'/>";
					}
				}

			} else if (para.mode==1 || para.mode==2) {
				//Polar: Zeiger
				if (para.pointerWidth==0) {para.pointerWidth=r/30;}
				var p1=math_polarToXY(mx,my,avalue,r-para.pointerWidth/2);
				n+="<line x1='"+mx+"' y1='"+my+"' x2='"+p1.x+"' y2='"+p1.y+"' stroke='"+fgcolor+"' stroke-linecap='round' stroke-width='"+para.pointerWidth+"' vector-effect='non-scaling-stroke'/>";
				if (para.mode==2) {
					n+="<circle cx='"+mx+"' cy='"+my+"' r='"+para.pointerWidth+"' fill='"+fgcolor+"' stroke='none' vector-effect='non-scaling-stroke'/>";
				}
	
			} else if (para.mode==3) {
				//Polar: Strich
				if (para.pointerWidth==0) {para.pointerWidth=r/30;}
				var p1=math_polarToXY(mx,my,avalue,r-para.pointerWidth/2);
				var p2=math_polarToXY(mx,my,avalue,r-para.rangeSize+para.pointerWidth/2);
				n+="<line x1='"+p1.x+"' y1='"+p1.y+"' x2='"+p2.x+"' y2='"+p2.y+"' stroke='"+fgcolor+"' stroke-linecap='round' stroke-width='"+para.pointerWidth+"' vector-effect='non-scaling-stroke'/>";

			} else if (para.mode==4) {
				//Polar: Kreis
				if (para.pointerWidth>0) {var len=para.pointerWidth;} else {var len=para.rangeSize/2;}
				var p1=math_polarToXY(mx,my,avalue,r-len);
				n+="<circle cx='"+p1.x+"' cy='"+p1.y+"' r='"+len+"' fill='"+fgcolor+"' stroke='none' vector-effect='non-scaling-stroke'/>";

			} else if (para.mode==5) {
				//Polar: Segment
				if (para.invert) {
					var p2=math_polarToXY(mx,my,para.angleTo,r);
					var p1=math_polarToXY(mx,my,avalue+0.01,r);
					n+="<path d='M "+p1.x+" "+p1.y+" A "+r+" "+r+" 0 "+((avalue-para.angleTo<-180)?1:0)+" 1 "+p2.x+" "+p2.y+" L "+mx+" "+my+" Z' fill='"+fgcolor+"' stroke='none' vector-effect='non-scaling-stroke'/>";
				} else {
					var p1=math_polarToXY(mx,my,para.angleFrom,r);
					var p2=math_polarToXY(mx,my,avalue-0.01,r);
					n+="<path d='M "+p1.x+" "+p1.y+" A "+r+" "+r+" 0 "+((avalue-para.angleFrom>180)?1:0)+" 1 "+p2.x+" "+p2.y+" L "+mx+" "+my+" Z' fill='"+fgcolor+"' stroke='none' vector-effect='non-scaling-stroke'/>";
				}
				
			} else if (para.mode==6 || para.mode==7) {
				//Polar: Kontur
				if (para.pointerWidth==0) {para.pointerWidth=para.rangeSize;}
				if (para.invert) {
					var p2=math_polarToXY(mx,my,para.angleTo,r-para.pointerWidth/2);
					var p1=math_polarToXY(mx,my,avalue+0.01,r-para.pointerWidth/2);
					n+="<path d='M "+p1.x+" "+p1.y+" A "+(r-para.pointerWidth/2)+" "+(r-para.pointerWidth/2)+" 0 "+((avalue-para.angleTo<-180)?1:0)+" 1 "+p2.x+" "+p2.y+"' fill='none' stroke='"+fgcolor+"' stroke-linecap='"+((para.mode==6)?'butt':'round')+"' stroke-width='"+para.pointerWidth+"' vector-effect='non-scaling-stroke'/>";
				} else {
					var p1=math_polarToXY(mx,my,para.angleFrom,r-para.pointerWidth/2);
					var p2=math_polarToXY(mx,my,avalue-0.01,r-para.pointerWidth/2);
					n+="<path d='M "+p1.x+" "+p1.y+" A "+(r-para.pointerWidth/2)+" "+(r-para.pointerWidth/2)+" 0 "+((avalue-para.angleFrom>180)?1:0)+" 1 "+p2.x+" "+p2.y+"' fill='none' stroke='"+fgcolor+"' stroke-linecap='"+((para.mode==6)?'butt':'round')+"' stroke-width='"+para.pointerWidth+"' vector-effect='non-scaling-stroke'/>";
				}
			}
		}	
	}
	return n;

	function drawRange() {
		if (para.rangeOpacity>0) {
			if (para.mode<100) {
				var a1=para.angleFrom;
				var a2=para.angleTo;
				if (values[1]!="" && !isNaN(values[1])) {
					if (para.invert) {var a1=parseFloat((arange/range)*(para.vmax-values[1])+para.angleFrom);} else {var a1=parseFloat((arange/range)*(values[1]-para.vmin)+para.angleFrom);}
				}				
				if (values[2]!="" && !isNaN(values[2])) {
					if (para.invert) {var a2=parseFloat((arange/range)*(para.vmax-values[2])+para.angleFrom);} else {var a2=parseFloat((arange/range)*(values[2]-para.vmin)+para.angleFrom);}
				}
				if (a1<para.angleFrom) {a1=para.angleFrom;}
				if (a2>para.angleTo) {a2=para.angleTo;}
				if (a1>a2) {var tmp=a1; a1=a2; a2=tmp;}

				if (para.rangeMode==0 || para.rangeMode==1) {
					var p1=math_polarToXY(mx,my,a1,(r-para.rangeSize/2));
					var p2=math_polarToXY(mx,my,a2-0.01,(r-para.rangeSize/2));
					n+="<path d='M "+p1.x+" "+p1.y+" A "+(r-para.rangeSize/2)+" "+(r-para.rangeSize/2)+" 0 "+((a2-a1>180)?1:0)+" 1 "+p2.x+" "+p2.y+"' fill='none' stroke='"+fgcolor+"' stroke-linecap='"+((para.rangeMode==0)?'butt':'round')+"' stroke-width='"+para.rangeSize+"' vector-effect='non-scaling-stroke' style='opacity:"+(para.rangeOpacity/100)+";'/>";
				} else if (para.rangeMode==2) {
					var p1=math_polarToXY(mx,my,a1,r);
					var p2=math_polarToXY(mx,my,a2-0.01,r);
					n+="<path d='M "+p1.x+" "+p1.y+" A "+r+" "+r+" 0 "+((a2-a1>180)?1:0)+" 1 "+p2.x+" "+p2.y+" L "+mx+" "+my+" Z' fill='"+fgcolor+"' stroke='none' vector-effect='non-scaling-stroke' style='opacity:"+(para.rangeOpacity/100)+";'/>";
				}
				
			} else {
				var a1=0;
				var a2=arange;
				if (values[1]!="" && !isNaN(values[1])) {
					if (para.invert) {var a1=parseFloat((arange/range)*(para.vmax-values[1]));} else {var a1=parseFloat((arange/range)*(values[1]-para.vmin));}
				}				
				if (values[2]!="" && !isNaN(values[2])) {
					if (para.invert) {var a2=parseFloat((arange/range)*(para.vmax-values[2]));} else {var a2=parseFloat((arange/range)*(values[2]-para.vmin));}
				}
				if (a1<0) {a1=0;}
				if (a2>arange) {a2=arange;}
				if (a1>a2) {var tmp=a1; a1=a2; a2=tmp;}

				if (w>=h) {
					n+="<line x1='"+(w/2*(100-para.size)/100+a1)+"' y1='"+(h/2)+"' x2='"+(w/2*(100-para.size)/100+a2)+"' y2='"+(h/2)+"' fill='none' stroke='"+fgcolor+"' stroke-linecap='"+((para.rangeMode==0)?'butt':'round')+"' stroke-width='"+para.rangeSize+"' vector-effect='non-scaling-stroke' style='opacity:"+(para.rangeOpacity/100)+";'/>";
				} else {
					n+="<line x1='"+(w/2)+"' y1='"+(h/2*(100-para.size)/100+a1)+"' x2='"+(w/2)+"' y2='"+(h/2*(100-para.size)/100+a2)+"' fill='none' stroke='"+fgcolor+"' stroke-linecap='"+((para.rangeMode==0)?'butt':'round')+"' stroke-width='"+para.rangeSize+"' vector-effect='non-scaling-stroke' style='opacity:"+(para.rangeOpacity/100)+";'/>";
				}
			}
		}
	}
}