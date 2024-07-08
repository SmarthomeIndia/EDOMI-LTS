###[DEF]###
[name    =Drehregler]

[folderid=162]
[xsize    =100]
[ysize    =100]
[text    ={#}]

[var1    =0]
[var2    =7]
[var4    =-1]
[var5    =]
[var6    =]
[var7    =]
[var8    =-1]
[var9    =100]
[var10    =70]
[var11    =100]
[var12    =0]
[var13    =360]
[var14    =30]
[var15    =0]
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
[var1 = select,2,'Modus','0#Potentiometer (relativ)|2#Potentiometer (absolut)|1#Inkrementalgeber (5-Grad-Schritte)|9#Inkrementalgeber (15-Grad-Schritte)']
[var12= text,1,'Startwinkel','']
[var13= text,1,'Endwinkel','']

[row]
[var2 = select,4,'Darstellung','0#neutral|1#Deko|2#Cursor|4#Eingabewert|6#Cursor und Eingabewert|3#Deko und Cursor|5#Deko und Eingabewert|7#Deko, Cursor und Eingabewert']

[row]
[var9 = text,2,'Größe (%)','']
[var10= text,1,'Knopfgröße: von (%)','']
[var11= text,1,'Knopfgröße: bis (%)','']

[row=Schleifspur]
[var15= select,2,'Darstellung','0#deaktiviert|1#eckige Enden|2#runde Enden']
[var14= text,2,'Stärke (%)','']

[row=Wertebereich]
[var5 = text,2,'Minimum (leer=KO-Filter)','']
[var6 = text,2,'Maximum (leer=KO-Filter)','']

[row]
[var7 = text,2,'Raster (leer=KO-Filter)','']
[var8 = select,2,'Nachkommastellen','-1#KO-Filter|#beliebig|0#0 (x)|1#1 (x.y)|2#2 (x.yy)|3#3 (x.yyy)|4#4 (x.yyyy)|5#5 (x.yyyyy)']

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
//Winkelbereich
if (obj.dataset.var1&1) {
obj.dataset.var12=0;
obj.dataset.var13=360;
} else {
var a1=parseInt(obj.dataset.var12);
var a2=parseInt(obj.dataset.var13);
if (isNaN(a1)) {a1=0;}
if (isNaN(a2)) {a2=360;}
if (a1>a2) {
var tmp=a1;
a1=a2;
a2=tmp;
}
if (a1<0) {a1=0;}
if (a1>=360) {a1=359;}
if (a2<=a1) {a2=a1+1;}
if (a2>360) {a2=360;}
obj.dataset.var12=a1;
obj.dataset.var13=a2;
}

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
objSvg.innerHTML=graphics_svg_centerArc("var(--fgc0)",objSvgContainer.offsetWidth,objSvgContainer.offsetHeight,obj.dataset.var12,obj.dataset.var13,{size:obj.dataset.var9,solid:false});
}

return true;
}
###[/EDITOR.JS]###


###[VISU.JS]###
VSE_VSEID_CONSTRUCT=function(elementId,obj) {
var n="
<table cellpadding='0' cellspacing='0' border='0' style='width:100%; height:100%;'>";
    n+="
    <tr>
        <td><span id='e-"+elementId+"-text'></span></td>
    </tr>
    ";
    n+="
</table>";
n+="
<div id='e-"+elementId+"-svgcontainer' style='display:block; position:absolute; left:0; top:0; width:100%; height:100%;'>";
    n+="
    <svg id='e-"+elementId+"-svg' style='display:block; left:0; top:0; width:100%; height:100%; box-sizing:border-box;'></svg>
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
visuElement_onDrag(document.getElementById("e-"+elementId+"-svgcontainer"),((obj.dataset.var1&2)?0:1),2,obj.dataset.var4);
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
var mousePos=visuElement_getMousePosition(obj,dragObj,0);
var modus=((obj.dataset.var1&2)?0:1);
if (obj.dataset.var1&1) {modus=2;}
var pos=visuElement_mapDragValue(mousePos,null,1,1,modus,obj.dataset.var5,obj.dataset.var6,null,null,obj.dataset.var12,obj.dataset.var13,obj.dataset.var8,obj.dataset.var7,((obj.dataset.var1&8)?15:5));
var value=pos.valuex;
return value;
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
var pos=getPos();

if (isActive) {
if (obj.dataset.var2&2) {var color=visu_indiColor;} else {var color="var(--fgc0)";}
if ((obj.dataset.var2&1 || obj.dataset.var2&2) && obj.dataset.var9>0) {n+=graphics_svg_centerArc(color,svgWidth,svgHeight,obj.dataset.var12,obj.dataset.var13,{size:obj.dataset.var9,solid:true});}
if (obj.dataset.var14>0 && obj.dataset.var15>0) {n+=slime("var(--fgc1)",pos,{size:obj.dataset.var9,width:obj.dataset.var14,cap:obj.dataset.var15});}
if (obj.dataset.var2&1 || obj.dataset.var2&2) {n+=radialLine(color,svgWidth,svgHeight,pos,{s1:obj.dataset.var10,s2:obj.dataset.var11,width:2});}
if (obj.dataset.var2&4) {document.getElementById("e-"+elementId+"-editvalue").innerHTML=VSE_VSEID_formatEditvalue(visuElement_parseString(visuElement_getCaption(elementId),koValue));}
} else {
var color="var(--fgc0)";
if (obj.dataset.var2&1 && obj.dataset.var9>0) {n+=graphics_svg_centerArc(color,svgWidth,svgHeight,obj.dataset.var12,obj.dataset.var13,{size:obj.dataset.var9,solid:true});}
if (obj.dataset.var14>0 && obj.dataset.var15>0) {n+=slime("var(--fgc1)",pos,{size:obj.dataset.var9,width:obj.dataset.var14,cap:obj.dataset.var15});}
if (obj.dataset.var2&1) {n+=radialLine(color,svgWidth,svgHeight,pos,{s1:obj.dataset.var10,s2:obj.dataset.var11,width:2});}
}

document.getElementById("e-"+elementId+"-svg").innerHTML=n;

//Verbundenes Visuelement (Knopf)
if (obj.dataset.linkid>0) {
var pSvg=visuElement_getAbsoluteChildPosition(obj,objSvgContainer);
var a=getPos()+visuElement_getAngle(obj);
var w=svgWidth;
var h=svgHeight;
if (w>=h) {var r=h/2;} else {var r=w/2;}
if (!isNaN(parseFloat(obj.dataset.var18))) {r*=parseFloat(obj.dataset.var18)/100;}
var p=math_polarToXY(pSvg.xm,pSvg.ym,a,r);
if (obj.dataset.var19==1) {var aa=getPos()+visuElement_getAngle(obj);} else {var aa=visuElement_getAngle(obj);}

visuElement_modify(obj.dataset.linkid,{
para:{x:p.x,y:p.y,a:aa},
func:function(obj,para){VSE_VSEID_modifyLinkedElement(obj,para);}
});
}
}

function getPos() {
var pos=math_mapValue(koValue,obj.dataset.var5,obj.dataset.var6,obj.dataset.var12,obj.dataset.var13);
return pos;
}

function slime(fgcolor,pos,para) {
return graphics_svg_centerArc(fgcolor,svgWidth,svgHeight,obj.dataset.var12,pos,{size:obj.dataset.var9,solid:true,linewidth:para.width,linecap:para.cap});
}

function radialLine(fgcolor,w,h,a,para) {
if (w>=h) {var r=h/2;} else {var r=w/2;}
var p1=math_polarToXY(w/2,h/2,a,r*para.s1/100);
var p2=math_polarToXY(w/2,h/2,a,r*para.s2/100);
return "
<line x1='"+p1.x+"' y1='"+p1.y+"' x2='"+p2.x+"' y2='"+p2.y+"' stroke-linecap='butt' stroke='"+fgcolor+"' stroke-width='"+para.width+"'
      vector-effect='non-scaling-stroke'/>";
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
Das Visuelement "Drehregler" ermöglicht das Bearbeiten eines KO-Wertes mit Hilfe eines virtuellen Potentiometers oder Inkrementalgebers (Endlosdrehregler).

<h2>Spezifische Eigenschaften</h2>
Für weitere Einstellungen und Optionen siehe:
<link>Allgemeine Informationen zu Visuelementen***1002</link>

<ul>
    <li>
        Modus: legt das Eingabeverhalten des Drehreglers fest
        <ul>
            <li>Potentiometer (relativ/absolut): der Drehregler verhält sich wie ein Potentiometer mit einem Bewegungsumfang von maximal 0..360 Grad</li>
            <li>Inkrementalgeber (stets relativ): Der Drehregler verhält sich wie ein Inkrementalgeber, d.h. bei jeder Winkeländerung von 5 bzw. 15 Grad wird
                der Wert je nach Drehrichtung erhöht oder erniedrigt. Die Schrittweite der Wertänderung ist dabei von den u.g. Parametern abhängig (Raster und
                Nachkommastellen).
            </li>
            <li>relativ: Der Wert von KO2 wird relativ zum aktuellen Wert (KO1) verändert, d.h. der Drehregler kann an einer beliebigen Position "angefasst"
                werden, ohne dass eine Wertänderung erfolgt. Erst beim Bewegen des Drehreglers wird der Wert relativ zu dieser Startposition abgeändert.
            </li>
            <li>absolut: Der Wert von KO2 wird unabhängig vom aktuellen Wert (KO1) gesetzt, d.h. beim "Anfassen" des Drehreglers wird bereits der mit dieser
                Position korrespondierende Wert gesetzt.
            </li>
            <li>Hinweis: Der Winkel 0 Grad befindet sich auf 6-Uhr-Position, im Uhrzeigersinn wird der Wert stets erhöht.</li>
        </ul>
    </li>

    <li>
        Startwinkel/Endwinkel: legt den Bewegungsumfang des Drehreglers im <i>ganzzahligen</i> Bereich von 0..360 (Grad) fest (nur im Modus "Potentiometer")
        <ul>
            <li>beide Werte sind als absolute Winkelwerte anzugeben, z.B. führt 45/315 zu einem 3/4-Kreis (270 Grad), der nach unten offen ist</li>
            <li>Bezugspunkt (0 Grad) ist die 6-Uhr-Position</li>
            <li>Winkelwerte werden stets im Uhrzeigersinn umgesetzt, z.B. führt die Angabe 0/90 zu einem Viertelkreis unten links</li>
            <li>Wichtig: der Startwinkel muss stets kleiner als der Endwinkel sein</li>
        </ul>
    </li>

    <li>
        Darstellung: legt das Erscheinungsbild des Drehreglers fest
        <ul>
            <li>Deko (Dekoration): zeigt den Bewegungsumfang ("Schleifbahn") und einen einfachen Knopf (aktuelle Position) an</li>
            <li>Cursor: zeigt während der Bedienung einen Cursor in Indikatorfarbe an</li>
            <li>Eingabewert: zeigt während der Bedienung den eingestellten Wert in Indikatorfarbe an</li>
        </ul>
    </li>

    <li>
        Größe: legt den Durchmesser der grafischen Anzeige (Schleifbahn) relativ zur Größe des Visuelements fest
        <ul>
            <li>erlaubt sind Werte von 0..100 Prozent (0=keine Schleifbahn anzeigen)</li>
            <li>Hinweis: Diese Angabe wirkt sich lediglich auf das Erscheinungsbild aus (funktioniell keine Bedeutung).</li>
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
        Schleifspur: legt ggf. das Erscheinungsbild der Schleifspur fest
        <ul>
            <li>Darstellung: aktiviert ggf. die Anzeige der Schleifspur mit runden oder eckigen Enden</li>
            <li>Stärke: legt die relative Stärke der Schleifspur fest (in 0..100 Prozent der Höhe bzw. Breite des Visuelements)</li>
            <li>Hinweis: die Schleifspur wird stets zentriert auf der "Schleifbahn" (s.o.) angezeigt</li>
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
            <li>Hinweis: Im Modus "Inkrementalgeber" legt dieser Wert ggf. die Schrittweite jeder Wertänderung fest. Wird kein Rasterwert angegeben, ist die
                Anzahl der Nachkommastellen (s.u.) ausschlaggebend: Der Wert wird z.B. bei 2 Nachkommastellen um 0.01 erhöht bzw. erniedrigt.
        </ul>
    </li>

    <li>
        Nachkommastellen: die Eingabe wird ggf. auf die angegebene Anzahl von Nachkommastellen gerundet
        <ul>
            <li>Option "KO-Filter": ggf. werden die KO-Filtereinstellungen angewendet</li>
            <li>Hinweis: Im Modus "Inkrementalgeber" legt dieser Wert ggf. die Schrittweite jeder Wertänderung fest, sofern kein Rasterwert (s.o.) angegeben
                wurde: Der Wert wird z.B. bei 2 Nachkommastellen um 0.01 erhöht bzw. erniedrigt.
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
        Verbundenes Visuelement (Knopf): falls ein Visuelement mit dem Drehregler
        <link>
        verbunden***2</link> wurde, wird das verbundene Visuelement an der aktuellen Knopf-Position angezeigt (ggf. auch rotiert)
        <ul>
            <li>Radius: relativer Abstand des verbundenen Visuelements vom Mittelpunkt der grafischen Anzeige des Drehreglers (siehe "Größe"), erlaubt sind
                Angaben im Bereich -&infin;..&infin;
            </li>
            <li>Rotation: das verbundene Visuelement wird ggf. mit der aktuellen Einstellung des Drehreglers rotiert</li>
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
            <li>dieser KO-Wert wird ggf. als Wert und Position angezeigt und dient als Grundlage für eine relative Wertänderung</li>
            <li>dieser KO-Wert wird zudem zur <span style="background:#e0ffe0;">Steuerung und Beschriftung</span> verwendet (Designs, Funktionen und Formeln)
            </li>
        </ul>
    </li>

    <li>
        KO2: Wert setzen
        <ul>
            <li>dieses KO wird auf den per Drehregler eingestellten Wert gesetzt</li>
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
    <li>Designs: Innenabstand und Textausrichtung werden ignoriert (die Textausrichtung ist stets zentriert)</li>
    <li>Seitensteuerung/Befehle stehen nicht zu Verfügung</li>
</ul>


<h2>Tipps</h2>
<h3>Individuellen "Knopf" erzeugen</h3>
Mit Hilfe eines weiteren Visuelements (z.B. einem Universalelement) kann bei Bedarf ein individueller "Knopf" zur Anzeige der aktuellen Position des Drehreglers erzeugt werden:

Zunächst wird z.B. ein Universalelement beispielsweise mit einem Hintergrundbild angelegt:
<ul>
    <li>als KO1 wird das KO2 des Drehreglers angegeben</li>
    <li>ggf. kann hier die Option "Live-Vorschau" aktiviert werden, um das Universalelement in Echtzeit während der Bedienung des Drehreglers reagieren zu
        lassen
    </li>
    <li>im Design des Visuelements wird der Eigenschaft "X-Position" die Formel "{polarX(...)}" und der Eigenschaft "Y-Position" die Formel "{polarY(...)}"
        zugewiesen (die Funktionsparameter werden in der Folge erläutert)
    </li>
    <li>beide Angaben zusammen führen zu einer wertabhängigen Positionierung des Universalalements auf einer Kreisbahn</li>
    <li>abschließend wird das Universalelement exakt(!) in der linken oberen Ecke des Drehreglers positioniert</li>
    <li>falls das Seitenverhältnis des Drehreglers nicht 1:1 beträgt, muss die Position des Universalelements entsprechend angepaßt werden: Ziel ist es das
        Universalelement in der linken oberen Ecke einer gedachten rechteckigen Begrenzung um die Kreisbahn des Drehreglers zu positionieren.
    </li>
</ul>

Die Parameter der o.g. Formeln
<link>polarX(minValue,maxValue,minAngle,maxAngle,Radius)***r-3</link> bzw.
<link>polarY(...)***r-3</link> sind wie folgt zu wählen:
<ul>
    <li>minValue/maxValue: der Wertebereich (Minimum/Maximum) des Drehreglers</li>
    <li>minAngle/maxAngle: der Winkelbereich des Drehreglers (beim Modus "360 Grad" bzw. "Inkrementalgeber" sind 0/360 anzugeben, beim Modus "270 Grad" sind
        45/315 anzugeben)
    </li>
    <li>Radius: der Radius entspricht der halben Breite des Drehreglers (ggf. abzüglich Rahmenbreite), bzw. dessen gedachter rechteckiger Begrenzung seiner
        Kreisbahn
    </li>
    <li>Hinweis: Der Radius kann ggf. angepaßt werden, z.B. um das Universalelement (Knopf) exakt auf der Kreisbahn zu positionieren. In der Regel ist es z.B.
        sinnvoll, die halbe "Größe" des Knopfes vom Radius abzuziehen, damit der Knopf mit seinem Mittelpunkt auf der Kreisbahn liegt.
    </li>
</ul>

<b>Hinweis:</b>
Zur Positionsanzeige kann alternativ (oder zusätzlich) z.B. auch eine
<link>Wertanzeige***1002-27</link> verwendet werden.


<h2>Bedienung in der Visualisierung</h2>
Durch das Anklicken (und Festhalten) einer beliebigen Stelle des Visuelements wird die Eingabe gestartet.
Ein Verschieben der Maus (mit gedrückter Maustaste) um das Zentrum des Visuelements führt zu einer Änderung des Eingabewerts.
Das Maß der Wertänderung hängt dabei (je nach Modus) vom Abstand der Maus zum Zentrum des Visuelements ab.
Ein Loslassen der Maustaste beendet die Eingabe, KO2 wird ggf. auf den eingestellten Wert gesetzt.
###[/HELP]###
