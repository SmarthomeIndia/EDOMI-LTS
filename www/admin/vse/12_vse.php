###[DEF]###
[name    =Dimmer/RGB/HSV]

[folderid=162]
[xsize    =100]
[ysize    =100]

[var1    =4]
[var2    =7]
[var3    =3]
[var4    =-1]
[var5    =3]
[var6    =3]
[var9    =90]
[var10    =70]
[var11    =100]
[var18    =100]
[var19    =1]

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
[var1 = select,4,'Modus','0#Dimmer (Inkrementalgeber)|1#RGB-Dimmer (Inkrementalgeber)|2#HSV-Dimmer (Inkrementalgeber)|4#Dimmer (Potentiometer)|5#RGB-Dimmer (Potentiometer)|6#HSV-Dimmer (Potentiometer)']

[row]
[var2 = select,2,'Darstellung','0#neutral|1#Deko|2#Cursor|4#Eingabewert|6#Cursor und Eingabewert|3#Deko und Cursor|5#Deko und Eingabewert|7#Deko, Cursor und Eingabewert']
[var5 = select,2,'Statusanzeige','0#deaktiviert|1#Dimmwert|2#EIN-Indikator|3#Dimmwert und EIN-Indikator']

[row]
[var9 = text,2,'Größe (%)','']
[var10= text,1,'Knopfgröße: von (%)','']
[var11= text,1,'Knopfgröße: bis (%)','']

[row=Schaltflächen]
[var3 = select,2,'Schaltflächen','0#keine Beschriftung, EIN/AUS deaktiviert|2#keine Beschriftung, EIN/AUS aktiviert|1#Beschriftung, EIN/AUS deaktiviert|3#Beschriftung, EIN/AUS aktiviert']
[var6 = select,2,'Klick-Indikatoren','0#deaktiviert|1#nur EIN/AUS-Schaltflächen|2#nur RGB/HS-Schaltflächen|3#alle Schaltflächen']

[row=Zyklisches Setzen]
[var4 = select,4,'KO2 zyklisch setzen','-1#deaktiviert|0#aktiviert|100#aktiviert (alle 100 ms setzen)|250#aktiviert (alle 250 ms setzen)|500#aktiviert (alle 500 ms setzen)|1000#aktiviert (alle 1000 ms setzen)']

[row=Verbundenes Visuelement (Knopf)]
[var18= text,2,'Radius (%)','']
[var19= select,2,'Rotation','0#deaktiviert|1#aktiviert']
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
if (parseFloat(obj.dataset.var10)<-100) {obj.dataset.var10=-100;}
if (parseFloat(obj.dataset.var10)>100) {obj.dataset.var10=100;}
if (isNaN(parseFloat(obj.dataset.var11))) {obj.dataset.var11=0;}
if (parseFloat(obj.dataset.var11)<-100) {obj.dataset.var11=-100;}
if (parseFloat(obj.dataset.var11)>100) {obj.dataset.var11=100;}
}
###[/SHARED.JS]###


###[EDITOR.JS]###
VSE_VSEID=function(elementId,obj,meta,property,isPreview,koValue) {
var tmp=['','','',''];
if (!(obj.dataset.var1&3)) {
tmp=['','',graphics_svg_icon(0),graphics_svg_icon(1)];
} else if (obj.dataset.var1&1) {
tmp=['R','G',graphics_svg_icon(0),'B'];
} else if (obj.dataset.var1&2) {
tmp=['H','S',graphics_svg_icon(0),graphics_svg_icon(1)];
}

var n="
<table cellpadding='0' cellspacing='0' border='0' style='width:100%; height:100%;'>";
    n+="
    <tr valign='top'>";
        n+="
        <td align='left' style='padding-left:5%; padding-top:5%; width:50%;'>"+tmp[0]+"</td>
        ";
        n+="
        <td align='right' style='padding-right:5%; padding-top:5%; width:50%;'>"+tmp[1]+"</td>
        ";
        n+="
    </tr>
    ";
    n+="
    <tr valign='bottom'>";
        n+="
        <td align='left' style='padding-left:5%; padding-bottom:5%; width:50%;'>"+tmp[2]+"</td>
        ";
        n+="
        <td align='right' style='padding-right:5%; padding-bottom:5%; width:50%;'>"+tmp[3]+"</td>
        ";
        n+="
    </tr>
    ";
    n+="
</table>";
n+="
<div id='"+obj.id+"-svgcontainer' style='display:block; position:absolute; left:0; top:0; width:100%; height:100%;'>";
    n+="
    <svg id='"+obj.id+"-svg' style='display:block; left:0; top:0; width:100%; height:100%; box-sizing:border-box;'></svg>
    ";
    n+="
</div>";
n+="
<div style='position:absolute; left:0; top:0; right:0; bottom:0;'>
    <table cellpadding='0' cellspacing='0' border='0' style='width:100%; height:100%;'>
        <tr>
            <td>"+meta.itemText+"</td>
        </tr>
    </table>
</div>";
obj.innerHTML=n;

//Text immer zentrieren, kein Padding
obj.style.textAlign="center";
obj.style.padding="0";

VSE_VSEID_parseVar(obj);

if (obj.dataset.var9>0) {
var objSvg=document.getElementById(obj.id+"-svg");
var objSvgContainer=document.getElementById(obj.id+"-svgcontainer");
objSvg.innerHTML=graphics_svg_centerCircle("var(--fgc0)",objSvgContainer.offsetWidth,objSvgContainer.offsetHeight,{size:obj.dataset.var9,solid:false});
}

return true;
}
###[/EDITOR.JS]###


###[VISU.JS]###
VSE_VSEID_CONSTRUCT=function(elementId,obj) {
if (!(obj.dataset.var1&3)) {
//Dimmer
var n="";
if (obj.dataset.var3&2) {
n+="
<table cellpadding='0' cellspacing='0' width='100%' height='100%' border='0'>";
    n+="
    <tr valign='top'>";
        n+="
        <td align='left' style='padding-left:5%; padding-top:5%; width:50%;'>&nbsp;</td>
        ";
        n+="
        <td align='right' style='padding-right:5%; padding-top:5%; width:50%;'>&nbsp;</td>
        ";
        n+="
    </tr>
    ";
    n+="
    <tr valign='bottom'>";
        n+="
        <td id='e-"+elementId+"-off' align='left' style='padding-left:5%; padding-bottom:5%; width:50%;'>
            <div id='e-"+elementId+"-offcaption' style='display:inline-block; padding:2px; border-radius:2px;'>"+((obj.dataset.var3&1)?graphics_svg_icon(0):"&nbsp;")+"</div>
        </td>
        ";
        n+="
        <td id='e-"+elementId+"-on' align='right' style='padding-right:5%; padding-bottom:5%; width:50%;'>
            <div id='e-"+elementId+"-oncaption' style='display:inline-block; padding:2px; border-radius:2px;'>"+((obj.dataset.var3&1)?graphics_svg_icon(1):"&nbsp;")+"</div>
        </td>
        ";
        n+="
    </tr>
    ";
    n+="
</table>";
}
n+=getContent();
obj.innerHTML=n;

obj.dataset.wheelid=0;
VSE_VSEID_parseVar(obj);

if (visuElement_hasKo(elementId,2)) {
visuElement_onDown(document.getElementById("e-"+elementId+"-wheel0"),function(veId,objId){VSE_VSEID_dragWheel(veId,0);},false);
if (obj.dataset.var3&2) {
visuElement_onClick(document.getElementById("e-"+elementId+"-off"),function(veId,objId){VSE_VSEID_Switch(veId,0);},false);
visuElement_onClick(document.getElementById("e-"+elementId+"-on"),function(veId,objId){VSE_VSEID_Switch(veId,1);},false);
}
}

} else if (obj.dataset.var1&1) {
//RGB-Dimmer
var n="";
n+="
<table cellpadding='0' cellspacing='0' width='100%' height='100%' border='0'>";
    n+="
    <tr valign='top'>";
        n+="
        <td id='e-"+elementId+"-wheel1' align='left' style='padding-left:5%; padding-top:5%; width:50%;'>
            <div id='e-"+elementId+"-wheel1caption' style='display:inline-block; padding:2px; border-radius:2px;'>"+((obj.dataset.var3&1)?"R":"&nbsp;")+"</div>
        </td>
        ";
        n+="
        <td id='e-"+elementId+"-wheel2' align='right' style='padding-right:5%; padding-top:5%; width:50%;'>
            <div id='e-"+elementId+"-wheel2caption' style='display:inline-block; padding:2px; border-radius:2px;'>"+((obj.dataset.var3&1)?"G":"&nbsp;")+"</div>
        </td>
        ";
        n+="
    </tr>
    ";
    n+="
    <tr valign='bottom'>";
        n+="
        <td id='e-"+elementId+"-off' align='left' style='padding-left:5%; padding-bottom:5%; width:50%;'>
            <div id='e-"+elementId+"-offcaption' style='display:inline-block; padding:2px; border-radius:2px;'>"+((obj.dataset.var3&1)?graphics_svg_icon(0):"&nbsp;")+"</div>
        </td>
        ";
        n+="
        <td id='e-"+elementId+"-wheel3' align='right' style='padding-right:5%; padding-bottom:5%; width:50%;'>
            <div id='e-"+elementId+"-wheel3caption' style='display:inline-block; padding:2px; border-radius:2px;'>"+((obj.dataset.var3&1)?"B":"&nbsp;")+"</div>
        </td>
        ";
        n+="
    </tr>
    ";
    n+="
</table>";
n+=getContent();
obj.innerHTML=n;

obj.dataset.wheelid=0;
VSE_VSEID_parseVar(obj);

if (visuElement_hasKo(elementId,2)) {
visuElement_onDown(document.getElementById("e-"+elementId+"-wheel0"),function(veId,objId){VSE_VSEID_dragWheel(veId,0);},false);
visuElement_onDown(document.getElementById("e-"+elementId+"-wheel1"),function(veId,objId){VSE_VSEID_dragWheel(veId,1);},false);
visuElement_onDown(document.getElementById("e-"+elementId+"-wheel2"),function(veId,objId){VSE_VSEID_dragWheel(veId,2);},false);
visuElement_onDown(document.getElementById("e-"+elementId+"-wheel3"),function(veId,objId){VSE_VSEID_dragWheel(veId,3);},false);
if (obj.dataset.var3&2) {
visuElement_onClick(document.getElementById("e-"+elementId+"-off"),function(veId,objId){VSE_VSEID_Switch(veId,0);},false);
}
}

} else if (obj.dataset.var1&2) {
//HSV-Dimmer
var n="";
n+="
<table cellpadding='0' cellspacing='0' width='100%' height='100%' border='0'>";
    n+="
    <tr valign='top'>";
        n+="
        <td id='e-"+elementId+"-wheel1' align='left' style='padding-left:5%; padding-top:5%; width:50%;'>
            <div id='e-"+elementId+"-wheel1caption' style='display:inline-block; padding:2px; border-radius:2px;'>"+((obj.dataset.var3&1)?"H":"&nbsp;")+"</div>
        </td>
        ";
        n+="
        <td id='e-"+elementId+"-wheel2' align='right' style='padding-right:5%; padding-top:5%; width:50%;'>
            <div id='e-"+elementId+"-wheel2caption' style='display:inline-block; padding:2px; border-radius:2px;'>"+((obj.dataset.var3&1)?"S":"&nbsp;")+"</div>
        </td>
        ";
        n+="
    </tr>
    ";
    if (obj.dataset.var3&2) {
    n+="
    <tr valign='bottom'>";
        n+="
        <td id='e-"+elementId+"-off' align='left' style='padding-left:5%; padding-bottom:5%; width:50%;'>
            <div id='e-"+elementId+"-offcaption' style='display:inline-block; padding:2px; border-radius:2px;'>"+((obj.dataset.var3&1)?graphics_svg_icon(0):"&nbsp;")+"</div>
        </td>
        ";
        n+="
        <td id='e-"+elementId+"-on' align='right' style='padding-right:5%; padding-bottom:5%; width:50%;'>
            <div id='e-"+elementId+"-oncaption' style='display:inline-block; padding:2px; border-radius:2px;'>"+((obj.dataset.var3&1)?graphics_svg_icon(1):"&nbsp;")+"</div>
        </td>
        ";
        n+="
    </tr>
    ";
    }
    n+="
</table>";
n+=getContent();
obj.innerHTML=n;

obj.dataset.wheelid=0;
VSE_VSEID_parseVar(obj);

if (visuElement_hasKo(elementId,2)) {
visuElement_onDown(document.getElementById("e-"+elementId+"-wheel0"),function(veId,objId){VSE_VSEID_dragWheel(veId,0);},false);
visuElement_onDown(document.getElementById("e-"+elementId+"-wheel1"),function(veId,objId){VSE_VSEID_dragWheel(veId,1);},false);
visuElement_onDown(document.getElementById("e-"+elementId+"-wheel2"),function(veId,objId){VSE_VSEID_dragWheel(veId,2);},false);
if (obj.dataset.var3&2) {
visuElement_onClick(document.getElementById("e-"+elementId+"-off"),function(veId,objId){VSE_VSEID_Switch(veId,0);},false);
visuElement_onClick(document.getElementById("e-"+elementId+"-on"),function(veId,objId){VSE_VSEID_Switch(veId,1);},false);
}
}
}

function getContent() {
var n="";
n+="
<div id='e-"+elementId+"-svgcontainer' style='display:block; position:absolute; left:0; top:0; width:100%; height:100%; pointer-events:none;'>";
    n+="
    <svg id='e-"+elementId+"-svg' style='display:block; left:0; top:0; width:100%; height:100%; box-sizing:border-box; pointer-events:none;'></svg>
    ";
    n+="
</div>";
n+="
<table cellpadding='0' cellspacing='0' border='0' style='position:absolute; left:0; top:0; width:100%; height:100%; pointer-events:none;'>";
    n+="
    <tr>
        <td><span id='e-"+elementId+"-text'></span></td>
    </tr>
    ";
    n+="
</table>";
n+="
<div id='e-"+elementId+"-wheel0' style='position:absolute; left:0; top:0; width:100%; height:100%; box-sizing:border-box; border-radius:100%;'></div>";
if (obj.dataset.var2&4) {n+="
<div id='e-"+elementId+"-editvalue'
     style='display:none; position:absolute; left:0; top:0; right:0; bottom:0; color:"+visu_indiColorText+"; pointer-events:none;'></div>";}
return n;
}
}

VSE_VSEID_REFRESH=function(elementId,obj,isInit,isRefresh,isLive,isActive,koValue) {
//Text immer zentrieren, kein Padding
obj.style.textAlign="center";
obj.style.padding="0";

document.getElementById("e-"+elementId+"-text").innerHTML=visuElement_parseString(visuElement_getText(elementId),koValue);
visuElement_centerAndAspect(obj,document.getElementById("e-"+elementId+"-wheel0"),obj.dataset.var9);    //wheel0 dient nur als unsichtbarer Anfasser (das SVG kann nicht geclipped werden, so dass die Buttons nicht bedienbar wären)
VSE_VSEID_render(elementId,isActive,koValue);
}

VSE_VSEID_dragWheel=function(elementId,wheelId) {
var obj=document.getElementById("e-"+elementId);
if (obj) {
obj.dataset.wheelid=wheelId;
//Drag-Objekt ist immer svgcontainer (R/G/B bzw. H/S dienen nur zur Definition der wheelId)
visuElement_dragStart(document.getElementById("e-"+elementId+"-svgcontainer"),1,2,obj.dataset.var4);
}
}

VSE_VSEID_DRAGSTART=function(elementId,dragObj) {
var obj=document.getElementById("e-"+elementId);
if (obj) {

if (obj.dataset.var2&4) {
document.getElementById("e-"+elementId+"-text").style.display="none";
document.getElementById("e-"+elementId+"-editvalue").style.display="block";
}
if (obj.dataset.var3&1 && obj.dataset.var6&2 && obj.dataset.wheelid>0) {document.getElementById("e-"+elementId+"-wheel"+obj.dataset.wheelid+"caption").style.background=visu_indiColor;}

//KO parsen und als Startposition merken (für relativen Modus)
var kovalue=visuElement_getKoValue(elementId,1);
var value=0;
if (!(obj.dataset.var1&3)) {
if (parseInt(kovalue)>=0 && parseInt(kovalue)<=255) {
value=parseInt(kovalue);
}
} else if (obj.dataset.var1&1) {
var rgb=RGBHEXtoRGB(kovalue);
if (rgb!==false) {
if (obj.dataset.wheelid==0) { //Helligkeit (V)
var hsv=RGBHEXtoHSV(kovalue);
if (hsv!==false) {value=hsv[2];}
}
if (obj.dataset.wheelid==1) {value=rgb[0];} //R
if (obj.dataset.wheelid==2) {value=rgb[1];} //G
if (obj.dataset.wheelid==3) {value=rgb[2];} //B
}
} else if (obj.dataset.var1&2) {
var hsv=HSVHEXtoHSV(kovalue);
if (hsv!==false) {
if (obj.dataset.wheelid==0) {value=hsv[2];} //V
if (obj.dataset.wheelid==1) {value=hsv[0];} //H
if (obj.dataset.wheelid==2) {value=hsv[1];} //S
}
}
visuElement_mapDragValueReset(value);
}
}

VSE_VSEID_DRAGMOVE=function(elementId,dragObj) {
var obj=document.getElementById("e-"+elementId);
if (obj) {
//KO-Wert aus Position ermitteln
var mousePos=visuElement_getMousePosition(obj,dragObj,0);
var kovalue=visuElement_getKoValue(elementId,1);
var pos=visuElement_mapDragValue(mousePos,null,1,1,((obj.dataset.var1&4)?1:2),0,255,null,null,0,360,0,1,5);
var value="";

if (!(obj.dataset.var1&3)) {
return pos.valuex;
} else if (obj.dataset.var1&1) {
var rgb=RGBHEXtoRGB(kovalue);
if (rgb===false) {rgb=[0,0,0];}
if (obj.dataset.wheelid==1) {value=RGBtoRGBHEX(pos.valuex,rgb[1],rgb[2]);}
if (obj.dataset.wheelid==2) {value=RGBtoRGBHEX(rgb[0],pos.valuex,rgb[2]);}
if (obj.dataset.wheelid==3) {value=RGBtoRGBHEX(rgb[0],rgb[1],pos.valuex);}
if (obj.dataset.wheelid==0) { //Sonderfall: Helligkeit (V)
var hsv=RGBHEXtoHSV(kovalue);
if (hsv===false) {hsv=[0,0,0];}
if (hsv!==false) {value=HSVtoRGBHEX(hsv[0],hsv[1],pos.valuex);}
}
if (value!="") {return value;}
} else if (obj.dataset.var1&2) {
var hsv=HSVHEXtoHSV(kovalue);
if (hsv===false) {hsv=[0,0,0];}
if (obj.dataset.wheelid==0) {value=HSVtoHSVHEX(hsv[0],hsv[1],pos.valuex);}
if (obj.dataset.wheelid==1) {value=HSVtoHSVHEX(pos.valuex,hsv[1],hsv[2]);}
if (obj.dataset.wheelid==2) {value=HSVtoHSVHEX(hsv[0],pos.valuex,hsv[2]);}
if (value!="") {return value;}
}
}
}

VSE_VSEID_DRAGEND=function(elementId,dragObj,dragValue) {
var obj=document.getElementById("e-"+elementId);
if (obj) {
document.getElementById("e-"+elementId+"-text").style.display="inline";
if (obj.dataset.var2&4) {document.getElementById("e-"+elementId+"-editvalue").style.display="none";}
if (obj.dataset.var3&1 && obj.dataset.var6&2 && obj.dataset.wheelid>0) {document.getElementById("e-"+elementId+"-wheel"+obj.dataset.wheelid+"caption").style.background="none";}
}
}

VSE_VSEID_Switch=function(elementId,value) {
var obj=document.getElementById("e-"+elementId);
if (obj) {
var newValue="";
if (value==0) {
if (obj.dataset.var3&1 && obj.dataset.var6&1) {visuElement_indicate(document.getElementById("e-"+elementId+"-offcaption"));}
if (!(obj.dataset.var1&3)) {newValue="0";}
if (obj.dataset.var1&1) {newValue="000000";}
if (obj.dataset.var1&2) {
var hsv=HSVHEXtoHSV(visuElement_getKoValue(elementId,1));
if (hsv!==false) {newValue=HSVtoHSVHEX(hsv[0],hsv[1],0);} else {newValue="000000";}
}
} else if (value==1) {
if (obj.dataset.var3&1 && obj.dataset.var6&1) {visuElement_indicate(document.getElementById("e-"+elementId+"-oncaption"));}
if (!(obj.dataset.var1&3)) {newValue="255";}
if (obj.dataset.var1&1) {newValue="ffffff";}
if (obj.dataset.var1&2) {
var hsv=HSVHEXtoHSV(visuElement_getKoValue(elementId,1));
if (hsv!==false) {newValue=HSVtoHSVHEX(hsv[0],hsv[1],255);} else {newValue="ffffff";}
}
}

if (newValue!="") {visuElement_setKoValue(obj.dataset.id,2,newValue);}
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

if (obj.dataset.var5&1 || obj.dataset.var5&2) {
if (!(obj.dataset.var1&3)) {
var hsv=RGBtoHSV(koValue,koValue,koValue);
n+=status(svgWidth,svgHeight,hsv);
}
if (obj.dataset.var1&1) {
var hsv=RGBHEXtoHSV(koValue);
n+=status(svgWidth,svgHeight,hsv);
}
if (obj.dataset.var1&2) {
var hsv=HSVHEXtoHSV(koValue);
n+=status(svgWidth,svgHeight,hsv);
}
}

if (isActive) {
if (obj.dataset.var2&2) {var color=visu_indiColor;} else {var color="var(--fgc0)";}

if (!(obj.dataset.var1&3)) {
var pos=getPosH();
if (obj.dataset.var9>0 && (obj.dataset.var2&1 || obj.dataset.var2&2)) {n+=radialLine(color,svgWidth,svgHeight,pos,{s1:obj.dataset.var10,s2:obj.dataset.var11,width:2});}
if (obj.dataset.var2&4) {document.getElementById("e-"+elementId+"-editvalue").innerHTML=VSE_VSEID_formatEditvalue((koValue/2.55).toFixed(1)+" %");}
} else if (obj.dataset.var1&1) {
var rgb=RGBHEXtoRGB(koValue);
if (rgb===false) {rgb=[0,0,0];}
if (obj.dataset.wheelid==0) {var pos=getPosH();}
if (obj.dataset.wheelid==1) {var pos=math_mapValue(parseInt(rgb[0]),0,255,0,360);}
if (obj.dataset.wheelid==2) {var pos=math_mapValue(parseInt(rgb[1]),0,255,0,360);}
if (obj.dataset.wheelid==3) {var pos=math_mapValue(parseInt(rgb[2]),0,255,0,360);}
if (obj.dataset.var9>0 && (obj.dataset.var2&1 || obj.dataset.var2&2)) {n+=radialLine(color,svgWidth,svgHeight,pos,{s1:obj.dataset.var10,s2:obj.dataset.var11,width:2});}
if (obj.dataset.var2&4) {document.getElementById("e-"+elementId+"-editvalue").innerHTML=VSE_VSEID_formatEditvalue((pos/3.6).toFixed(1)+" %");}
} else if (obj.dataset.var1&2) {
var hsv=HSVHEXtoHSV(koValue);
if (hsv===false) {hsv=[0,0,0];}
if (obj.dataset.wheelid==0) {var pos=math_mapValue(parseInt(hsv[2]),0,255,0,360);}
if (obj.dataset.wheelid==1) {var pos=math_mapValue(parseInt(hsv[0]),0,255,0,360);}
if (obj.dataset.wheelid==2) {var pos=math_mapValue(parseInt(hsv[1]),0,255,0,360);}
if (obj.dataset.var9>0 && (obj.dataset.var2&1 || obj.dataset.var2&2)) {n+=radialLine(color,svgWidth,svgHeight,pos,{s1:obj.dataset.var10,s2:obj.dataset.var11,width:2});}
if (obj.dataset.wheelid==1 && obj.dataset.var2&4) {document.getElementById("e-"+elementId+"-editvalue").innerHTML=VSE_VSEID_formatEditvalue(pos.toFixed(0)+" &deg;");}
if ((obj.dataset.wheelid==0 || obj.dataset.wheelid==2) && obj.dataset.var2&4) {document.getElementById("e-"+elementId+"-editvalue").innerHTML=VSE_VSEID_formatEditvalue((pos/3.6).toFixed(1)+" %");}
}

if (obj.dataset.var9>0 && (obj.dataset.var2&1 || obj.dataset.var2&2)) {n+=graphics_svg_centerCircle(color,svgWidth,svgHeight,{size:obj.dataset.var9,solid:true});}

} else if (obj.dataset.var2&1) {
var color="var(--fgc0)";

//aktuelle Helligkeit ermitteln
var pos=getPosH();
if (obj.dataset.var9>0) {n+=radialLine(color,svgWidth,svgHeight,pos,{s1:obj.dataset.var10,s2:obj.dataset.var11,width:2});}

if (obj.dataset.var9>0) {n+=graphics_svg_centerCircle(color,svgWidth,svgHeight,{size:obj.dataset.var9,solid:true});}
}

document.getElementById("e-"+elementId+"-svg").innerHTML=n;

//Verbundenes Visuelement (Knopf)
if (obj.dataset.linkid>0) {
var pSvg=visuElement_getAbsoluteChildPosition(obj,objSvgContainer);
var a=pos+visuElement_getAngle(obj);
var w=svgWidth;
var h=svgHeight;
if (w>=h) {var r=h/2;} else {var r=w/2;}
if (!isNaN(parseFloat(obj.dataset.var18))) {r*=parseFloat(obj.dataset.var18)/100;}
var p=math_polarToXY(pSvg.xm,pSvg.ym,a,r);
if (obj.dataset.var19==1) {var aa=pos+visuElement_getAngle(obj);} else {var aa=visuElement_getAngle(obj);}

visuElement_modify(obj.dataset.linkid,{
para:{x:p.x,y:p.y,a:aa,display:true},
func:function(obj,para){VSE_VSEID_modifyLinkedElement(obj,para);}
});
}
}

function getPosH() {
var pos=0;
var hsv=false;
if (!(obj.dataset.var1&3)) {var hsv=RGBtoHSV(koValue,koValue,koValue);}
if (obj.dataset.var1&1) {var hsv=RGBHEXtoHSV(koValue);}
if (obj.dataset.var1&2) {var hsv=HSVHEXtoHSV(koValue);}
if (hsv!==false) {pos=math_mapValue(parseInt(hsv[2]),0,255,0,360);}
return pos;
}

function radialLine(fgcolor,w,h,a,para) {
if (w>=h) {var r=h/2*obj.dataset.var9/100;} else {var r=w/2*obj.dataset.var9/100;}
var p1=math_polarToXY(w/2,h/2,a,r*para.s1/100);
var p2=math_polarToXY(w/2,h/2,a,r*para.s2/100);
return "
<line x1='"+p1.x+"' y1='"+p1.y+"' x2='"+p2.x+"' y2='"+p2.y+"' stroke-linecap='butt' stroke='"+fgcolor+"' stroke-width='"+para.width+"'
      vector-effect='non-scaling-stroke'/>";
}

function centerCircleFill(color,w,h,para) {
if (w>=h) {var r=h/2;} else {var r=w/2;}
if ((r*para.size/100-1/2)<=0) {return "";}
return "
<circle cx='50%' cy='50%' r='"+(r*para.size/100-1/2)+"' stroke='none' vector-effect='non-scaling-stroke' fill='"+color+"'/>";
}

function centerCircle(fgcolor,w,h,para) {
if (w>=h) {var r=h/2;} else {var r=w/2;}
if ((r*para.size/100-1/2)<=0) {return "";}
return "
<circle cx='50%' cy='50%' r='"+(r*para.size/100-1/2)+"' stroke='"+fgcolor+"' stroke-width='"+para.width+"' vector-effect='non-scaling-stroke' fill='none'/>";
}

function status(w,h,hsv) {
var n="";
if (hsv!==false && hsv[2]>0) {
if (obj.dataset.var5&1) {
var tmp=HSVtoRGBA(hsv[0],hsv[1],hsv[2]);
if (obj.dataset.var9>0) {n+=centerCircleFill("rgba("+tmp[0]+","+tmp[1]+","+tmp[2]+","+tmp[3]+")",w,h,{size:obj.dataset.var9});}
}
if (obj.dataset.var5&2) {
var tmp=HSVtoRGB(hsv[0],hsv[1],255);
if (obj.dataset.var9>=1) {
var ringWith=10; //Ringdicke in Prozent
if (w>=h) {var ww=h/2*obj.dataset.var9/100*ringWith/100;} else {var ww=w/2*obj.dataset.var9/100*ringWith/100;}
var ff=(100-ringWith)/100*0.5+0.5;
if (ww>0) {n+=centerCircle("rgb("+parseInt(tmp[0])+","+parseInt(tmp[1])+","+parseInt(tmp[2])+")",w,h,{size:obj.dataset.var9*ff,width:ww});}
}
}
}
return n;
}
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
Das Visuelement "Dimmer/RGB/HSV" ermöglicht das Bearbeiten eines KO-Wertes mit Hilfe eines virtuellen Potentiometers oder Inkrementalgebers (Endlosdrehregler) bzw. diverser Schaltflächen. Dieses Visuelement ist dafür ausgelegt einen Dimmwert bzw. RGB-/HSV-Farbwert anzuzeigen und einzustellen.

<h2>Spezifische Eigenschaften</h2>
Für weitere Einstellungen und Optionen siehe:
<link>Allgemeine Informationen zu Visuelementen***1002</link>

<ul>
    <li>
        Modus: Auswahl des Dimmer-Typs
        <ul>
            <li>Dimmer: ermöglicht die Einstellung eines Helligkeitswertes von 0..100% (KO2 wird auf einen Wert 0..255 gesetzt)</li>
            <li>RGB-Dimmer: ermöglicht die Einstellung eines RGB-Farbwertes (KO2 wird auf einen Wert 000000..FFFFFF gesetzt)</li>
            <li>HSV-Dimmer: ermöglicht die Einstellung eines HSV-Farbwertes (KO2 wird auf einen Wert 000000..FFFFFF gesetzt)</li>
            <li>Inkrementalgeber: Der Dimmer verhält sich wie ein Inkrementalgeber, d.h. bei jeder Winkeländerung von 5 Grad wird der Wert je nach Drehrichtung
                erhöht oder erniedrigt.
            </li>
            <li>Potentiometer: der Dimmer verhält sich wie ein Potentiometer mit einem Bewegungsumfang von 0..360 Grad</li>
            <li>Hinweis: Der Wert von KO2 wird stets relativ zum aktuellen Wert (KO1) verändert, d.h. der Drehregler des Dimmers kann an einer beliebigen
                Position "angefasst" werden, ohne dass eine Wertänderung erfolgt. Erst beim Bewegen des Drehreglers wird der Wert relativ zu dieser
                Startposition abgeändert.
            </li>
        </ul>
    </li>

    <li>
        Darstellung: legt das Erscheinungsbild des Dimmers fest
        <ul>
            <li>Deko (Dekoration): zeigt den Bewegungsumfang ("Schleifbahn") und einen einfachen Knopf an</li>
            <li>Cursor: zeigt während der Bedienung einen Cursor in Indikatorfarbe an</li>
            <li>Eingabewert: zeigt während der Bedienung den eingestellten Wert in Indikatorfarbe an (Prozentwert bzw. "Hue" in Grad)</li>
        </ul>
    </li>

    <li>
        Statusanzeige: legt fest, ob und wie der aktuell eingestellte Farb- bzw. Helligkeitswert angezeigt werden soll
        <ul>
            <li>Dimmwert: der Bereich innerhalb des Drehreglers wird mit dem aktuellen Farb- bzw. Helligkeitswert ausgefüllt, die Helligkeit wird dabei als
                Transparenz visualisiert (eine geringe Helligkeit führt zu einer hohen Transparenz der angezeigten Füllung)
            </li>
            <li>EIN-Indikator: die innere Umrandung des Drehreglers wird in der aktuellen Farbe angezeigt, jedoch stets mit maximaler Helligkeit (dies
                ermöglicht eine visuelle Kontrolle den EIN-Zustands auch bei gering eingestellter Helligkeit)
            </li>
            <li>Hinweis: Während der Bedienung des Visuelements wird stets der aktuelle Farb- bzw. Helligkeitswert (wie bei "Dimmwert" beschrieben) angezeigt.
            </li>
        </ul>
    </li>

    <li>
        Größe: legt den Durchmesser der grafischen Anzeige (Drehregler) relativ zur Größe des Visuelements fest
        <ul>
            <li>erlaubt sind Werte von 0..100 Prozent</li>
            <li>ein Wert von 0(%) führt dazu, dass der zentrale Drehregler nicht angezeigt wird: Die Einstellung des Dimm- bzw. Helligkeitswertes ist dann nicht
                mehr möglich, jedoch können die Schaltflächen H/S bzw. R/G/B dennoch bedient werden.
            </li>
            <li>Hinweis: Diese Angabe wirkt sich auf das Erscheinungsbild aus, zudem wird die Größe des aktiven Bereichs für den Drehregler festgelegt.</li>
        </ul>
    </li>

    <li>
        Knopfgröße (von/bis): legt die relative Größe des "Knopfes" und des Eingabe-Cursors fest (sofern "Deko" bzw. "Cursor" aktiviert ist)
        <ul>
            <li>erlaubt sind Werte von -100..100 Prozent</li>
            <li>Hinweis: Diese Angabe wirkt sich lediglich auf das Erscheinungsbild aus (funktioniell keine Bedeutung).</li>
        </ul>
    </li>

    <li>
        Schaltflächen: legt fest, ob die Schaltflächen beschriftet und (teilweise) deaktiviert werden sollen
        <ul>
            <li>Hinweis: die Ein-/Aus-Schaltflächen werden mit Symbolen beschriftet (links: ein, rechts: aus)</li>
            <li>Beschriftung: zeigt die Beschriftung der Schaltflächen an oder blendet diese aus (z.B. für eine eigene Beschriftung mit Hilfe eines hinterlegten
                Bildes)
            </li>
            <li>EIN/AUS: aktiviert oder deaktiviert die Funktion der Schaltflächen "EIN" bzw. "AUS"</li>
        </ul>
    </li>

    <li>
        Klick-Indikatoren: legt fest, ob beim Anklicken der entsprechenden Schaltflächen der Klick-Indikator angezeigt werden soll
        <ul>
            <li>deaktiviert: es werden keine Klick-Indikatoren angezeigt</li>
            <li>nur EIN/AUS-Schaltflächen: beim Anklicken der EIN/AUS-Schaltflächen wird der Klick-Indikator für die entsprechende Schaltfläche angezeigt</li>
            <li>nur RGB/HS-Schaltflächen: beim Anklicken und Einstellen der RGB- bzw. HS-Werte wird der Klick-Indikator für die entsprechende Schaltfläche
                angezeigt
            </li>
            <li>alle Schaltflächen: der Klick-Indikator wird bei allen o.g. Schaltflächen angezeigt</li>
            <li>Hinweis: Der Klick-Indikator wird nur für die jeweilige <i>Beschriftung</i> der Schaltfläche angezeigt, nicht für die gesamte Schaltfläche.
                Werden die Schaltflächen nicht beschriftet, wird auch kein Klick-Indikator angezeigt.
            </li>
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
        Verbundenes Visuelement (Knopf): falls ein Visuelement mit dem Dimmer
        <link>
        verbunden***2</link> wurde, wird das verbundene Visuelement an der aktuellen Knopf-Position angezeigt (ggf. auch rotiert)
        <ul>
            <li>Radius: relativer Abstand des verbundenen Visuelements vom Mittelpunkt der grafischen Anzeige des Dimmers (siehe "Größe"), erlaubt sind Angaben
                im Bereich -&infin;..&infin;
            </li>
            <li>Rotation: das verbundene Visuelement wird ggf. mit der aktuellen Einstellung des Dimmers rotiert</li>
            <li>Hinweis: Die Design-Angaben "X/Y-Position" und "Drehung" des verbundenen Visuelements werden zusätzlich (Addition) angewendet.</li>
            <li>Wichtig: Als Urspung für die Positionierung/Rotation wird stets die Mitte des verbundenen Visuelements angenommen.</li>
        </ul>
    </li>
</ul>


<h2>Kommunikationsobjekte</h2>
Dieses Visuelement kann (optional) folgende Kommunikationsobjekte (KO) verwalten:

<ul>
    <li>
        KO1: Status
        <ul>
            <li>dieser KO-Wert wird ggf. als Wert und Position angezeigt und dient als Grundlage für die stets relative Wertänderung</li>
            <li>dieser KO-Wert wird zudem zur <span style="background:#e0ffe0;">Steuerung und Beschriftung</span> verwendet (Designs, Funktionen und Formeln)
            </li>
        </ul>
    </li>

    <li>
        KO2: Wert setzen
        <ul>
            <li>dieses KO wird auf den per Dimmer eingestellten Wert gesetzt</li>
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

<b>Hinweis:</b>
Beim Modus "Dimmer" sollten die KOs mit dem Datentyp "Variant" bzw. "DPT 5" konfiguriert werden.
Beim Modus "RGB-/HSV-Dimmer" sollten die KOs mit dem Datentyp "Variant" bzw. "DPT 232" konfiguriert werden.


<h2>Besonderheiten</h2>
<ul>
    <li>Designs: Innenabstand und Textausrichtung werden ignoriert (die Textausrichtung ist stets zentriert)</li>
    <li>Seitensteuerung/Befehle stehen nicht zu Verfügung</li>
</ul>


<h2>Bedienung in der Visualisierung</h2>
Je nach Modus (Dimmer, RGB-/HSV-Dimmer) haben die Schaltflächen und Drehregler des Visuelements eine unterschiedliche Bedeutung (s.u.). Grundsätzlich erfolgt die Bedienung jedoch in jedem Modus auf die gleiche Weise:

Im Zentrum des Visuelements ist ein Drehregler positioniert (Bedienung siehe Visuelement
<link>Drehregler***1002-11</link>), der zur Einstellung eines gewählten Parameters dient. Um diesen Drehregler herum sind bis zu 4 Schaltflächen angeordnet, die entweder durch Anklicken unittelbar eine Aktion ausführen (Ein/Aus) oder zur Definition des gewünschten Drehregler-Parameters (R/G/B, bzw. H/S) dienen (Anklicken und Festhalten).

Beim Einstellen eines Wertes mit dem Drehregler führt ein Loslassen der Maustaste zum Beenden der Einstellung.

<b>Modus "Dimmer":</b>
Die Schaltflächen "ein" bzw. "aus" setzen KO2 beim Anklicken auf den Wert "255" bzw. "0".
Der Drehregler definiert einen Wert im Bereich 0..255 - angezeigt wird jedoch ggf. ein Helligkeitswert im Bereich 0..100%.

<b>Modus "RGB-Dimmer":</b>
Die Schaltfläche "aus" setzt KO2 beim Anklicken auf den RGB-Wert "000000".
Der Drehregler definiert standardmäßig die Helligkeit, d.h. der aktuell eingestellte RGB-Wert wird entsprechend umgerechnet.
Die Schaltflächen "R", "G" und "B" definieren den gewünschten Rot-, Grün- und Blauanteil des Farbwertes: Durch Anklicken und Festhalten der entsprechenden Schaltfläche kann der gewünschte Wert mit Hilfe des Drehreglers eingestellt werden.
Hinweis: Prinzipbedingt kann die Helligkeit u.U. nur unter Verlust der Farbinformation berechnet werden!

<b>Modus "HSV-Dimmer":</b>
Die Schaltflächen "ein" bzw. "aus" setzen KO2 beim Anklicken auf den Wert "xxxx00" bzw. "xxxxFF" ("xxxx" repräsentiert dabei den aktuell eingestellten Farbwert ("H" bzw. "S") und wird im Gegensatz zum RGB-Dimmer nicht durch die Helligkeitseinstellung beeinflusst).
Der Drehregler definiert standardmäßig die Helligkeit ("V"), d.h. der aktuell eingestellte HSV-Wert wird entsprechend umgerechnet.
Die Schaltflächen "H" und "S" definieren den gewünschten Farbwert bzw. die Sättigung: Durch Anklicken und Festhalten der entsprechenden Schaltfläche kann der gewünschte Wert mit Hilfe des Drehreglers eingestellt werden.
###[/HELP]###
