###[DEF]###
[name    =Schieberegler]

[folderid=162]
[xsize    =100]
[ysize    =50]
[text    ={#}]

[var1    =0]
[var2    =7]
[var3    =0]
[var4    =-1]
[var5    =]
[var6    =]
[var7    =]
[var8    =-1]
[var9    =100]
[var10    =50]
[var11    =50]
[var12    =0]
[var13    =1]
[var19    =0]

[flagText        =1]
[flagKo1        =1]
[flagKo2        =1]
[flagKo3        =1]
[flagPage        =0]
[flagCmd        =0]
[flagDesign        =1]
[flagDynDesign    =1]

[captionKo1        =Status]
[captionKo2        =Wert setzen]
###[/DEF]###


###[PROPERTIES]###
[columns=25,25,25,25]
[row]
[var1 = select,4,'Modus','0#Potentiometer (relativ)|1#Potentiometer (absolut)']

[row]
[var2 = select,2,'Darstellung','0#neutral|1#Deko|2#Cursor|4#Eingabewert|6#Cursor und Eingabewert|3#Deko und Cursor|5#Deko und Eingabewert|7#Deko, Cursor und Eingabewert']
[var3 = select,2,'Bewegungsrichtung','0#links/oben=Minimum|1#rechts/unten=Minimum']

[row]
[var9 = text,2,'Größe (%)','']
[var13= select,1,'Knopf','0#deaktiviert|1#Linie|2#Kreis|3#Quadrat']
[var10= text,1,'Knopfgröße (%)','']

[row=Schleifspur]
[var12= select,2,'Darstellung','0#deaktiviert|1#eckige Enden|2#runde Enden']
[var11= text,2,'Stärke (%)','']

[row=Wertebereich]
[var5 = text,2,'Minimum (leer=KO-Filter)','']
[var6 = text,2,'Maximum (leer=KO-Filter)','']

[row]
[var7 = text,2,'Raster (leer=KO-Filter)','']
[var8 = select,2,'Nachkommastellen','-1#KO-Filter|#beliebig|0#0 (x)|1#1 (x.y)|2#2 (x.yy)|3#3 (x.yyy)|4#4 (x.yyyy)|5#5 (x.yyyyy)']

[row=Zyklisches Setzen]
[var4 = select,4,'KO2 zyklisch setzen','-1#deaktiviert|0#aktiviert|100#aktiviert (alle 100 ms setzen)|250#aktiviert (alle 250 ms setzen)|500#aktiviert (alle 500 ms setzen)|1000#aktiviert (alle 1000 ms setzen)']

[row=Verbundenes Visuelement (Knopf)]
[var19= text,4,'Abstand von Mittellinie (%)','']
###[/PROPERTIES]###


###[ACTIVATION.PHP]###
<?
//gaid und gaid2 gegenseitig ergänzen, falls nicht angegeben
if (($item['gaid'] > 0) && !($item['gaid2'] > 0)) {
    sql_call("UPDATE edomiLive.visuElement SET gaid2=" . $item['gaid'] . " WHERE id=" . $item['id']);
}
if (!($item['gaid'] > 0) && ($item['gaid2'] > 0)) {
    sql_call("UPDATE edomiLive.visuElement SET gaid=" . $item['gaid2'] . " WHERE id=" . $item['id']);
}

//Min/Max/Raster/Nachkommastellen ggf. aus KO-Konfiguration/DPT-Array übernehmen (vmin/vmax/vstep/vlist)
$tmp = sql_getValues('edomiProject.editKo', 'valuetyp,vmin,vmax,vstep,vlist', 'id=' . $item['gaid2']);
if ($tmp !== false) {
    //Min/Max ggf. aus DPT-Array holen
    if (isEmpty($tmp['vmin'])) {
        $tmp['vmin'] = $global_dptData[$tmp['valuetyp']][0];
    }
    if (isEmpty($tmp['vmax'])) {
        $tmp['vmax'] = $global_dptData[$tmp['valuetyp']][1];
    }
    //leere Werte ersetzen
    if (isEmpty($item['var5'])) {
        sql_call("UPDATE edomiLive.visuElement SET var5='" . sql_encodeValue($tmp['vmin']) . "' WHERE (id=" . $item['id'] . ")");
    }
    if (isEmpty($item['var6'])) {
        sql_call("UPDATE edomiLive.visuElement SET var6='" . sql_encodeValue($tmp['vmax']) . "' WHERE (id=" . $item['id'] . ")");
    }
    if (isEmpty($item['var7'])) {
        sql_call("UPDATE edomiLive.visuElement SET var7='" . sql_encodeValue($tmp['vstep']) . "' WHERE (id=" . $item['id'] . ")");
    }
    if ($item['var8'] == '-1') {
        sql_call("UPDATE edomiLive.visuElement SET var8='" . sql_encodeValue($tmp['vlist']) . "' WHERE (id=" . $item['id'] . ")");
    }
}

//Min/Max ggf. vertauschen
if ($item['var5'] > $item['var6'] && !isEmpty($item['var6'])) {
    sql_call("UPDATE edomiLive.visuElement SET var5='" . sql_encodeValue($item['var6']) . "',var6='" . sql_encodeValue($item['var5']) . "' WHERE (id=" . $item['id'] . ")");
}
?>
###[/ACTIVATION.PHP]###


###[SHARED.JS]###
VSE_VSEID_parseVar=function(obj) {
//Schleifbahngröße
if (isNaN(parseFloat(obj.dataset.var9))) {obj.dataset.var9=0;}
if (parseFloat(obj.dataset.var9)<0) {obj.dataset.var9=0;}
if (parseFloat(obj.dataset.var9)>100) {obj.dataset.var9=100;}

//Knopfgröße
if (isNaN(parseFloat(obj.dataset.var10))) {obj.dataset.var10=0;}
if (parseFloat(obj.dataset.var10)<0) {obj.dataset.var10=0;}
if (parseFloat(obj.dataset.var10)>100) {obj.dataset.var10=100;}
}
###[/SHARED.JS]###


###[EDITOR.JS]###
VSE_VSEID=function(elementId,obj,meta,property,isPreview,koValue) {
var n=""
n+="
<div id='"+obj.id+"-svgcontainer' style='display:block; position:absolute; left:0; top:0; width:100%; height:100%;'>";
    n+="
    <svg id='"+obj.id+"-svg' style='display:block; left:0; top:0; width:100%; height:100%; box-sizing:border-box;'></svg>
    ";
    n+="
</div>";
n+="
<table cellpadding='0' cellspacing='0' border='0' style='width:100%; height:100%;'>
    <tr>
        <td>"+meta.itemText+"</td>
    </tr>
</table>";
obj.innerHTML=n;

//Text immer zentrieren, kein Padding
obj.style.textAlign="center";
obj.style.padding="0";

VSE_VSEID_parseVar(obj);

if (obj.dataset.var9>0) {
var objSvg=document.getElementById(obj.id+"-svg");
var objSvgContainer=document.getElementById(obj.id+"-svgcontainer");
objSvg.innerHTML=graphics_svg_centerLine("var(--fgc0)",objSvgContainer.offsetWidth,objSvgContainer.offsetHeight,{solid:false,offset:100-obj.dataset.var9});
}

return true;
}

###[/EDITOR.JS]###


###[VISU.JS]###
VSE_VSEID_CONSTRUCT=function(elementId,obj) {
var n="
<div id='e-"+elementId+"-svgcontainer' style='display:block; position:absolute; left:0; top:0; width:100%; height:100%;'>";
    n+="
    <svg id='e-"+elementId+"-svg' style='display:block; left:0; top:0; width:100%; height:100%; box-sizing:border-box;'></svg>
    ";
    n+="
</div>";
n+="
<div style='display:block; position:absolute; left:0; top:0; right:0; bottom:0; pointer-events:none;'>";
    n+="
    <table cellpadding='0' cellspacing='0' border='0' style='width:100%; height:100%;'>
        <tr>
            <td><span id='e-"+elementId+"-text'></span></td>
        </tr>
    </table>
    ";
    n+="
</div>";
if (obj.dataset.var2&4) {n+="
<div id='e-"+elementId+"-editvalue'
     style='display:none; position:absolute; left:0; top:0; right:0; bottom:0; color:"+visu_indiColorText+"; pointer-events:none;'></div>";}
obj.innerHTML=n;

if (isNaN(parseFloat(obj.dataset.var5)) || isNaN(parseFloat(obj.dataset.var6))) {
obj.dataset.var5=0;
obj.dataset.var6=100;
}

VSE_VSEID_parseVar(obj);

if (visuElement_hasKo(elementId,2)) {
visuElement_onDrag(document.getElementById("e-"+elementId+"-svgcontainer"),((obj.dataset.var1&1)?0:1),2,obj.dataset.var4);
}
}

VSE_VSEID_REFRESH=function(elementId,obj,isInit,isRefresh,isLive,isActive,koValue) {
//Text immer zentrieren, kein Padding
obj.style.textAlign="center";
obj.style.padding="0";

document.getElementById("e-"+elementId+"-text").innerHTML=visuElement_parseString(visuElement_getText(elementId),koValue);
VSE_VSEID_render(elementId,isActive,koValue);
}

VSE_VSEID_DRAGSTART=function(elementId,dragObj) {
var obj=document.getElementById("e-"+elementId);
if (obj) {
//KO-Wert als Startposition merken (für relativen Modus)
var kovalue=visuElement_getKoValue(elementId,1);
visuElement_mapDragValueReset(kovalue);

if (obj.dataset.var2&4) {
document.getElementById("e-"+elementId+"-text").style.display="none";
document.getElementById("e-"+elementId+"-editvalue").style.display="block";
}
}
}

VSE_VSEID_DRAGMOVE=function(elementId,dragObj) {
var obj=document.getElementById("e-"+elementId);
if (obj) {
//KO-Wert aus Position ermitteln
var mousePos=VSE_VSEID_getMousePos(obj,dragObj,((obj.dataset.var3&1)?3:0));
var size=VSE_VSEID_getSize(elementId);
var pos=visuElement_mapDragValue(mousePos,null,(size.o+1),0,((obj.dataset.var1&1)?0:1),obj.dataset.var5,obj.dataset.var6,obj.dataset.var5,obj.dataset.var6,null,null,obj.dataset.var8,obj.dataset.var7,null);
if (size.o==0) {
return pos.valuex;
} else {
return pos.valuey;
}
}
}

VSE_VSEID_DRAGEND=function(elementId,dragObj,dragValue) {
var obj=document.getElementById("e-"+elementId);
if (obj) {
if (obj.dataset.var2&4) {
document.getElementById("e-"+elementId+"-text").style.display="inline";
document.getElementById("e-"+elementId+"-editvalue").style.display="none";
}
}
}

VSE_VSEID_render=function(elementId,isActive,koValue) {
var obj=document.getElementById("e-"+elementId);
var objSvg=document.getElementById("e-"+elementId+"-svg");
var objSvgContainer=document.getElementById("e-"+elementId+"-svgcontainer");
if (obj && objSvg && objSvgContainer) {
var n="";
var svgWidth=objSvgContainer.offsetWidth;
var svgHeight=objSvgContainer.offsetHeight;
var size=VSE_VSEID_getSize(elementId);
var pos=getPos();

if (isActive) {
if (obj.dataset.var2&2) {var color=visu_indiColor;} else {var color="var(--fgc0)";}
if ((obj.dataset.var2&1 || obj.dataset.var2&2) && obj.dataset.var9>0) {n+=track(color,pos,{size:obj.dataset.var9,knobsize:obj.dataset.var10,width:1});}
if (obj.dataset.var11>0 && obj.dataset.var12>0) {n+=slime("var(--fgc1)",pos,{size:obj.dataset.var9,width:obj.dataset.var11,cap:obj.dataset.var12});}
if (obj.dataset.var13>0 && obj.dataset.var10>0) {n+=knob(color,pos,{size:obj.dataset.var10,width:2});}
if (obj.dataset.var2&4) {document.getElementById("e-"+elementId+"-editvalue").innerHTML=VSE_VSEID_formatEditvalue(visuElement_parseString(visuElement_getCaption(elementId),koValue));}

} else {
var color="var(--fgc0)";
if (obj.dataset.var2&1 && obj.dataset.var9>0) {n+=track(color,pos,{size:obj.dataset.var9,knobsize:obj.dataset.var10,width:1});}
if (obj.dataset.var11>0 && obj.dataset.var12>0) {n+=slime("var(--fgc1)",pos,{size:obj.dataset.var9,width:obj.dataset.var11,cap:obj.dataset.var12});}
if (obj.dataset.var13>0 && obj.dataset.var10>0) {n+=knob(color,pos,{size:obj.dataset.var10,width:2});}
}

document.getElementById("e-"+elementId+"-svg").innerHTML=n;

//Verbundenes Visuelement (Knopf)
if (obj.dataset.linkid>0) {
var pSvg=visuElement_getAbsoluteChildPosition(obj,objSvgContainer);
var r=0;
var d=getPos();
var a=visuElement_getAngle(obj);
var w=svgWidth;
var h=svgHeight;

if (w>=h) {
if (!isNaN(parseFloat(obj.dataset.var19))) {r=h/2*parseFloat(obj.dataset.var19)/100;}
var pc=math_polarToXY(0,0,a,r);
var p=math_polarToXY(0,0,a-90,d-w/2);
} else {
if (!isNaN(parseFloat(obj.dataset.var19))) {r=w/2*parseFloat(obj.dataset.var19)/100;}
var pc=math_polarToXY(0,0,a-90,r);
var p=math_polarToXY(0,0,a,d-h/2);
}

visuElement_modify(obj.dataset.linkid,{
para:{x:pSvg.xm+p.x+pc.x,y:pSvg.ym+p.y+pc.y,a:a},
func:function(obj,para){VSE_VSEID_modifyLinkedElement(obj,para);}
});
}
}

function getPos() {
var pos=math_mapValue(koValue,obj.dataset.var5,obj.dataset.var6,size.p1,size.p2);
if (obj.dataset.var3&1) {pos=size.p2-pos+size.p1;}
return pos;
}

function track(fgcolor,pos,para) {
if (size.o==0) {
if (obj.dataset.var13>=2) {
var r="";
var x1=(svgWidth/2-svgWidth/2*para.size/100);
var x2=(pos-(svgHeight/2*para.knobsize/100));
if (x2>=x1) {r+="
<line x1='"+x1+"' y1='"+(svgHeight/2)+"' x2='"+x2+"' y2='"+(svgHeight/2)+"' stroke='"+fgcolor+"' stroke-width='"+para.width+"'
      vector-effect='non-scaling-stroke' fill='none'/>";}
var x1=(svgWidth/2+svgWidth/2*para.size/100);
var x2=(pos+(svgHeight/2*para.knobsize/100));
if (x2<=x1) {r+="
<line x1='"+x1+"' y1='"+(svgHeight/2)+"' x2='"+x2+"' y2='"+(svgHeight/2)+"' stroke='"+fgcolor+"' stroke-width='"+para.width+"'
      vector-effect='non-scaling-stroke' fill='none'/>";}
return r;
} else {
return "
<line x1='"+(svgWidth/2-svgWidth/2*para.size/100)+"' y1='"+(svgHeight/2)+"' x2='"+(svgWidth/2+svgWidth/2*para.size/100)+"' y2='"+(svgHeight/2)+"'
      stroke='"+fgcolor+"' stroke-width='"+para.width+"' vector-effect='non-scaling-stroke' fill='none'/>";
}
} else {
if (obj.dataset.var13>=2) {
var r="";
var y1=(svgHeight/2-svgHeight/2*para.size/100);
var y2=(pos-(svgWidth/2*para.knobsize/100));
if (y2>=y1) {r+="
<line y1='"+y1+"' x1='"+(svgWidth/2)+"' y2='"+y2+"' x2='"+(svgWidth/2)+"' stroke='"+fgcolor+"' stroke-width='"+para.width+"' vector-effect='non-scaling-stroke'
      fill='none'/>";}
var y1=(svgHeight/2+svgHeight/2*para.size/100);
var y2=(pos+(svgWidth/2*para.knobsize/100));
if (y2<=y1) {r+="
<line y1='"+y1+"' x1='"+(svgWidth/2)+"' y2='"+y2+"' x2='"+(svgWidth/2)+"' stroke='"+fgcolor+"' stroke-width='"+para.width+"' vector-effect='non-scaling-stroke'
      fill='none'/>";}
return r;
} else {
return "
<line x1='"+(svgWidth/2)+"' y1='"+(svgHeight/2-svgHeight/2*para.size/100)+"' x2='"+(svgWidth/2)+"' y2='"+(svgHeight/2+svgHeight/2*para.size/100)+"'
      stroke='"+fgcolor+"' stroke-width='"+para.width+"' vector-effect='non-scaling-stroke' fill='none'/>";
}
}
}

function slime(fgcolor,pos,para) {
if (size.o==0) {
if (obj.dataset.var3&1) {
return "
<line x1='"+pos+"' y1='"+(svgHeight/2)+"' x2='"+(svgWidth/2+svgWidth/2*para.size/100)+"' y2='"+(svgHeight/2)+"'
      stroke-linecap='"+((para.cap==2)?"round":"butt")+"' stroke='"+fgcolor+"' stroke-width='"+(svgHeight*para.width/100)+"' vector-effect='non-scaling-stroke'
      fill='none'/>";
} else {
return "
<line x1='"+pos+"' y1='"+(svgHeight/2)+"' x2='"+(svgWidth/2-svgWidth/2*para.size/100)+"' y2='"+(svgHeight/2)+"'
      stroke-linecap='"+((para.cap==2)?"round":"butt")+"' stroke='"+fgcolor+"' stroke-width='"+(svgHeight*para.width/100)+"' vector-effect='non-scaling-stroke'
      fill='none'/>";
}
} else {
if (obj.dataset.var3&1) {
return "
<line x1='"+(svgWidth/2)+"' y1='"+pos+"' x2='"+(svgWidth/2)+"' y2='"+(svgHeight/2+svgHeight/2*para.size/100)+"'
      stroke-linecap='"+((para.cap==2)?"round":"butt")+"' stroke='"+fgcolor+"' stroke-width='"+(svgWidth*para.width/100)+"' vector-effect='non-scaling-stroke'
      fill='none'/>";
} else {
return "
<line x1='"+(svgWidth/2)+"' y1='"+pos+"' x2='"+(svgWidth/2)+"' y2='"+(svgHeight/2-svgHeight/2*para.size/100)+"'
      stroke-linecap='"+((para.cap==2)?"round":"butt")+"' stroke='"+fgcolor+"' stroke-width='"+(svgWidth*para.width/100)+"' vector-effect='non-scaling-stroke'
      fill='none'/>";
}
}
}

function knob(fgcolor,pos,para) {
if (size.o==0) {
if (obj.dataset.var13==1) {return "
<line x1='"+pos+"' y1='"+(svgHeight/2-svgHeight/2*para.size/100)+"' x2='"+pos+"' y2='"+(svgHeight/2+svgHeight/2*para.size/100)+"' stroke-linecap='butt'
      stroke='"+fgcolor+"' stroke-width='"+para.width+"' vector-effect='non-scaling-stroke' fill='none'/>";}
if (obj.dataset.var13==2) {return "
<circle cx='"+pos+"' cy='"+(svgHeight/2)+"' r='"+(svgHeight/2*para.size/100)+"' stroke-linecap='butt' stroke='"+fgcolor+"' stroke-width='"+para.width+"'
        vector-effect='non-scaling-stroke' fill='"+visuElement_getBgColor(obj,1)+"'/>";}
if (obj.dataset.var13==3) {return "
<rect x='"+(pos-svgHeight/2*para.size/100)+"' y='"+(svgHeight/2-svgHeight/2*para.size/100)+"' width='"+(svgHeight*para.size/100)+"'
      height='"+(svgHeight*para.size/100)+"' stroke-linecap='butt' stroke='"+fgcolor+"' stroke-width='"+para.width+"' vector-effect='non-scaling-stroke'
      fill='"+visuElement_getBgColor(obj,1)+"'/>";}
} else {
if (obj.dataset.var13==1) {return "
<line x1='"+(svgWidth/2-svgWidth/2*para.size/100)+"' y1='"+pos+"' x2='"+(svgWidth/2+svgWidth/2*para.size/100)+"' y2='"+pos+"' stroke-linecap='butt'
      stroke='"+fgcolor+"' stroke-width='"+para.width+"' vector-effect='non-scaling-stroke' fill='none'/>";}
if (obj.dataset.var13==2) {return "
<circle cx='"+(svgWidth/2)+"' cy='"+pos+"' r='"+(svgWidth/2*para.size/100)+"' stroke-linecap='butt' stroke='"+fgcolor+"' stroke-width='"+para.width+"'
        vector-effect='non-scaling-stroke' fill='"+visuElement_getBgColor(obj,1)+"'/>";}
if (obj.dataset.var13==3) {return "
<rect x='"+(svgWidth/2-svgWidth/2*para.size/100)+"' y='"+(pos-svgWidth/2*para.size/100)+"' width='"+(svgWidth*para.size/100)+"'
      height='"+(svgWidth*para.size/100)+"' stroke-linecap='butt' stroke='"+fgcolor+"' stroke-width='"+para.width+"' vector-effect='non-scaling-stroke'
      fill='"+visuElement_getBgColor(obj,1)+"'/>";}
}
}
}

VSE_VSEID_getMousePos=function(obj,dragObj,flip) {
var p=visuElement_getMousePosition(obj,dragObj,flip);
var s=(100-obj.dataset.var9)/100;
p.x-=p.w*s/2;
p.y-=p.h*s/2;
p.w-=p.w*s;
p.h-=p.h*s;
return p;
}

VSE_VSEID_getSize=function(elementId) {
var obj=document.getElementById("e-"+elementId);
var objSvg=document.getElementById("e-"+elementId+"-svg");
var objSvgContainer=document.getElementById("e-"+elementId+"-svgcontainer");
if (obj && objSvg && objSvgContainer) {
var w=objSvgContainer.offsetWidth;
var h=objSvgContainer.offsetHeight;
var s=(100-obj.dataset.var9)/100;
if (w>=h) {
var p1=w/2*s;
var p2=w-w/2*s;
return {o:0,p1:p1,p2:p2};
} else {
var p1=h/2*s;
var p2=h-h/2*s;
return {o:1,p1:p1,p2:p2};
}
}
return false;
}

VSE_VSEID_formatEditvalue=function(n) {
return "
<table cellpadding='0' cellspacing='0' border='0' style='width:100%; height:100%;'>
    <tr>
        <td><span style='background:"+visu_indiColor+"; color:"+visu_indiColorText+"; padding:1px; border-radius:3px;'>"+n+"</span></td>
    </tr>
</table>";
}

VSE_VSEID_modifyLinkedElement=function(obj,para) {
var x=para.x-obj.offsetWidth/2+visuElement_getOffset(obj,0);
var y=para.y-obj.offsetHeight/2+visuElement_getOffset(obj,1);
var a=para.a+visuElement_getAngle(obj);
obj.style.left=x+"px";
obj.style.top=y+"px";
obj.style.webkitTransform="rotate("+a+"deg)";
}
###[/VISU.JS]###


###[HELP]###
Das Visuelement "Schieberegler" ermöglicht das Bearbeiten eines KO-Wertes mit Hilfe eines virtuellen Schiebepotentiometers.

<h2>Spezifische Eigenschaften</h2>
Für weitere Einstellungen und Optionen siehe:
<link>Allgemeine Informationen zu Visuelementen***1002</link>

<ul>
    <li>
        Modus: legt das Eingabeverhalten des Schiebereglers fest
        <ul>
            <li>Potentiometer (relativ): Der Wert von KO2 wird relativ zum aktuellen Wert (KO1) verändert, d.h. der Schieberegler kann an einer beliebigen
                Position "angefasst" werden, ohne dass eine Wertänderung erfolgt. Erst beim Bewegen des Schiebereglers wird der Wert relativ zu dieser
                Startposition abgeändert.
            </li>
            <li>Potentiometer (absolut): Der Wert von KO2 wird unabhängig vom aktuellen Wert (KO1) gesetzt, d.h. beim "Anfassen" des Schiebereglers wird bereits
                der mit dieser Position korrespondierende Wert gesetzt.
            </li>
        </ul>
    </li>

    <li>
        Darstellung: legt das Erscheinungsbild des Schiebereglers fest
        <ul>
            <li>Deko (Dekoration): zeigt den Pfad ("Schleifbahn") an</li>
            <li>Cursor: hebt während der Bedienung ggf. den Knopf und die Schleifbahn (s.u.) in Indikatorfarbe hervor</li>
            <li>Eingabewert: zeigt während der Bedienung den eingestellten Wert in Indikatorfarbe an</li>
        </ul>
    </li>

    <li>
        Bewegungsrichtung: legt fest, in welcher Bewegungsrichtung der Wert erhöht bzw. erniedrigt wird
        <ul>
            <li>Hinweis: die Ausrichtung des Schiebereglers (horizontal oder vertikal) erfolgt stets automatisch anhand des Seitenverhältnisses (Breite:Höhe):
                Ist "Breite &ge; Höhe" wird der Schieberegler horizontal ausgerichtet, ansonsten vertikal.
            </li>
        </ul>
    </li>

    <li>
        Größe: legt die Länge der grafischen Anzeige (Schleifbahn) relativ zur Größe des Visuelements fest
        <ul>
            <li>Hinweis: Ein Wert &lt;100(%) verkürzt die Schleifbahn und somit den Bewegungsumfang des Schiebereglers entsprechend.</li>
        </ul>
    </li>

    <li>
        Knopf: zeigt ggf. einen einfachen Knopf an der aktuellen Position (eingestellter Wert) an
        <ul>
            <li>deaktiviert: es wird kein Knopf angezeigt</li>
            <li>Linie: der Knopf wird als Linie angezeigt</li>
            <li>Kreis: der Knopf wird als Kreiskontur angezeigt, optional wird der Kreis mit der
                <link>
                Zusatzhintergrundfarbe 1***1003</link> gefüllt
            </li>
            <li>Quadrat: der Knopf wird als Quadratkontur angezeigt, optional wird der Kreis mit der
                <link>
                Zusatzhintergrundfarbe 1***1003</link> gefüllt
            </li>
        </ul>
    </li>

    <li>
        Knopfgröße: legt die relative Größe des Knopfes fest
        <ul>
            <li>erlaubt sind Werte von 0..100 Prozent</li>
            <li>Hinweis: Diese Angabe wirkt sich lediglich auf das Erscheinungsbild aus (funktioniell keine Bedeutung).</li>
        </ul>
    </li>

    <li>
        Schleifspur: legt ggf. das Erscheinungsbild der Schleifspur fest
        <ul>
            <li>Darstellung: aktiviert ggf. die Anzeige der Schleifspur mit runden oder eckigen Enden</li>
            <li>Stärke: legt die relative Stärke der Schleifspur fest (in 0..100 Prozent der Höhe bzw. Breite des Visuelements)</li>
            <li>Hinweis: die Schleifspur beginnt je nach "Bewegungsrichtung" (s.o.) stets entsprechend links oder rechts (bzw. oben oder unten)</li>
            <li>Wichtig: die Farbe der Schleifspur wird mit der
                <link>
                Zusatzvordergrundfarbe 1***1003</link> festgelegt
            </li>
        </ul>
    </li>

    <li>
        Minimum (Integer/Float): unterer Grenzwert der Eingabe
        <ul>
            <li>wird dieses Feld [leer] belassen, werden ggf. die KO-Filtereinstellungen angewendet</li>
            <li>Wichtig: Falls das Minimum und/oder das Maximum nicht definiert wurde, wird stets ein Wertebereich von 0..100 erzeugt.</li>
        </ul>
    </li>

    <li>
        Maximum (Integer/Float): oberer Grenzwert der Eingabe
        <ul>
            <li>wird dieses Feld [leer] belassen, werden ggf. die KO-Filtereinstellungen angewendet</li>
            <li>Wichtig: Falls das Minimum und/oder das Maximum nicht definiert wurde, wird stets ein Wertebereich von 0..100 erzeugt.</li>
        </ul>
    </li>

    <li>
        Raster (Integer/Float): die Eingabe wird auf einen Wert mit dieser "Schrittweite" umgerechnet
        <ul>
            <li>z.B. Raster=0.5: die Eingabe 0.45 wird zu 0, die Eingabe 2.98 wird zu 2.5 umgerechnet</li>
            <li>wird dieses Feld [leer] belassen, werden ggf. die KO-Filtereinstellungen angewendet</li>
        </ul>
    </li>

    <li>
        Nachkommastellen: die Eingabe wird ggf. auf die angegebene Anzahl von Nachkommastellen gerundet
        <ul>
            <li>Option "KO-Filter": ggf. werden die KO-Filtereinstellungen angewendet</li>
        </ul>
    </li>

    <li>
        KO2 zyklisch setzen: legt fest, wann und wie häufig KO2 auf einen Wert gesetzt werden soll
        <ul>
            <li>deaktiviert: das KO wird nur beim Beenden ("Loslassen") der Eingabe auf den entsprechenden Wert gesetzt</li>
            <li>aktiviert: das KO wird beim Beenden und <i>während</i> der Eingabe (jedoch nur bei einer Wertänderung) auf den entsprechenden Wert gesetzt -
                dies wird u.U. zu einer hohen Buslast führen!
            </li>
            <li>aktiviert (alle ... ms setzen): das KO wird beim Beenden und <i>während</i> der Eingabe (jedoch nur bei einer Wertänderung) auf den
                entsprechenden Wert gesetzt, jedoch nur in dem angegebenen Intervall
            </li>
            <li>Wichtig: Ist diese Option aktiviert, wird das Visuelement keine Live-Vorschau-Werte bereitstellen.</li>
        </ul>
    </li>

    <li>
        Verbundenes Visuelement (Knopf): falls ein Visuelement mit dem Schieberegler
        <link>
        verbunden***2</link> wurde, wird das verbundene Visuelement an der aktuellen Knopf-Position angezeigt (ggf. auch rotiert)
        <ul>
            <li>Abstand von Mittellinie: relativer Abstand des verbundenen Visuelements von der Mittellinie der grafischen Anzeige des Schiebereglers (siehe
                "Größe"), erlaubt sind Angaben im Bereich -&infin;..&infin;
            </li>
            <li>Hinweis: Die Design-Angaben "X/Y-Position" des verbundenen Visuelements werden zusätzlich (Addition) angewendet.</li>
            <li>Wichtig: Als Urspung für die Positionierung wird stets die Mitte des verbundenen Visuelements angenommen.</li>
        </ul>
    </li>
</ul>


<h2>Kommunikationsobjekte</h2>
Dieses Visuelement kann (optional) folgende Kommunikationsobjekte (KO) verwalten:

<ul>
    <li>
        KO1: Status
        <ul>
            <li>dieser KO-Wert wird ggf. als Wert und Position angezeigt</li>
            <li>dieser KO-Wert wird zudem zur <span style="background:#e0ffe0;">Steuerung und Beschriftung</span> verwendet (Designs, Funktionen und Formeln)
            </li>
        </ul>
    </li>

    <li>
        KO2: Wert setzen
        <ul>
            <li>dieses KO wird auf den per Schieberegler eingestellten Wert gesetzt</li>
            <li>Hinweis: Dieser KO-Wert wird während der Bedienung des Visuelements als Vorschau-Wert für das KO1 aller anderen Visuelemente mit
                <link>
                aktivierter Live-Vorschau***1002</link> bereitgestellt.
            </li>
        </ul>
    </li>

    <li>
        KO3: Steuerung des dynamischen Designs
        <ul>
            <li>dieser KO-Wert wird ausschließlich zur Steuerung eines
                <link>
                dynamischen Designs***1003</link> verwendet
            </li>
            <li>wenn dieses KO angegeben wurde, wird ein dynamisches Design durch dieses <i>KO3</i> gesteuert</li>
            <li>wenn dieses KO nicht angegeben wurde, wird ein dynamisches Design durch das <i>KO1</i> gesteuert</li>
        </ul>
    </li>
</ul>

<b>Hinweis:</b>
Bei einer Aktivierung ergänzen sich KO1 und KO2 gegenseitig: Wird z.B. KO1 nicht angegeben, wird KO1 automatisch mit dem gleichen KO wie KO2 verknüpft (und umgekehrt).


<h2>Besonderheiten</h2>
<ul>
    <li>die Ausrichtung des Schiebereglers (horizontal oder vertikal) erfolgt anhand des Seitenverhältnisses (Breite:Höhe) automatisch</li>
    <li>die Eigenschaften Minimum und Maximum müssen stets angegeben werden (im Visuelement bzw. in den KO-Einstellungen)</li>
    <li>Designs: Innenabstand und Textausrichtung werden ignoriert (die Textausrichtung ist stets zentriert)</li>
    <li>Seitensteuerung/Befehle stehen nicht zu Verfügung</li>
</ul>


<h2>Tipps</h2>
<h3>Individuellen "Knopf" erzeugen</h3>
Mit Hilfe eines weiteren Visuelements (z.B. einem Universalelement) kann bei Bedarf ein individueller "Knopf" zur Anzeige der aktuellen Position des Schiebereglers erzeugt werden:

Zunächst wird z.B. ein Universalelement beispielsweise mit einem Hintergrundbild angelegt:
<ul>
    <li>als KO1 wird das KO2 des Schiebereglers angegeben</li>
    <li>ggf. kann hier die Option "Live-Vorschau" aktiviert werden, um das Universalelement in Echtzeit während der Bedienung des Schiebereglers reagieren zu
        lassen
    </li>
    <li>je nach Ausrichtung des Schiebereglers (horizontal oder vertikal) wird im Design des Visuelements der Eigenschaft "X-Position" (horizontal) oder
        "Y-Position" (vertikal) die Formel "{range(...)}" zugewiesen (die Funktionsparameter werden in der Folge erläutert)
    </li>
    <li>diese Angabe führt zu einer wertabhängigen Positionierung des Universalalements auf einem linearen Pfad</li>
    <li>
        abschließend wird das Universalelement exakt(!) an einer Kante des Schiebereglers positioniert, je nach Ausrichtung und Bewegungsrichtung:
        <ul>
            <li>Bewegungsrichtung links/oben=Minimum: das Universalelement muss an der linken bzw. oberen Kante des Schiebereglers positioniert werden</li>
            <li>Bewegungsrichtung rechts/unten=Minimum: das Universalelement muss an der rechten bzw. unteren Kante des Schiebereglers positioniert werden</li>
        </ul>
    </li>
    <li>Hinweis: Die genaue Position kann ggf. angepaßt werden, z.B. um das Universalelement (Knopf) exakt auf dem Pfad zu positionieren. In der Regel ist es
        z.B. sinnvoll, den Knopf mittig auf der entsprechenden Kante zu positionieren.
    </li>
</ul>

Die Parameter der o.g. Formel
<link>range(minValue,maxValue,Range)***r-3</link> sind wie folgt zu wählen:
<ul>
    <li>minValue/maxValue: der Wertebereich (Minimum/Maximum) des Schiebereglers</li>
    <li>Range: je nach Ausrichtung des Schiebereglers ist dessen Breite bzw. Höhe anzugeben</li>
    <li>
        Range: je nach Ausrichtung des Schiebereglers ist dessen Breite (horizontal) bzw. Höhe (vertikal) anzugeben (ggf. abzüglich Rahmenbreite), zudem muss je
        nach Bewegungsrichtung der Wert negiert werden:
        <ul>
            <li>Bewegungsrichtung links/oben=Minimum: "Range" muss positiv angegeben werden, z.B. "100"</li>
            <li>Bewegungsrichtung rechts/unten=Minimum: "Range" muss negativ angegeben werden, z.B. "-100"</li>
        </ul>
    </li>
</ul>

<b>Hinweis:</b>
Zur Positionsanzeige kann alternativ (oder zusätzlich) z.B. auch eine
<link>Wertanzeige***1002-27</link> oder eine
<link>dynamische Hintergrundfarbe***1000-25</link> verwendet werden.


<h2>Bedienung in der Visualisierung</h2>
Durch das Anklicken (und Festhalten) einer beliebigen Stelle des Visuelements wird die Eingabe gestartet.
Ein Verschieben der Maus (mit gedrückter Maustaste) führt je nach Ausrichtung des Schiebereglers (horizontal oder vertikal) zu einer Änderung des Eingabewerts.
Ein Loslassen der Maustaste beendet die Eingabe, KO2 wird ggf. auf den eingestellten Wert gesetzt.
###[/HELP]###
