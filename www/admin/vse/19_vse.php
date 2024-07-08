###[DEF]###
[name    =Kamera]

[folderid=161]
[xsize    =240]
[ysize    =180]

[var1    =0 #root=83]
[var2    =3]
[var3    =3]
[var4    =3]
[var6    =0]
[var7    =0]
[var10    =]

[flagText        =1]
[flagKo1        =1]
[flagKo2        =1]
[flagKo3        =1]
[flagPage        =1]
[flagCmd        =1]
[flagDesign        =1]
[flagDynDesign    =1]

[captionKo1        =Steuerung (ggf. PTZ)]
[captionKo2        =PTZ-Wert setzen]
###[/DEF]###


###[PROPERTIES]###
[columns=50,50]
[row]
[var1 = root,2,'Kameraansicht',83]

[row=Darstellung]
[var4 = select,1,'Titel-/Zoom-Zeile','0#unsichtbar|1#nur Abgrenzung|2#nur Titel|3#Abgrenzung und Titel']
[var10= text,1,'Höhe (px, leer=Standard)','']

[row=Verhalten]
[var2 = select,2,'Aktualisierung per Intervall','-1#MJPEG-Stream direkt einbinden|0#deaktiviert|1#jede Sekunde|2#alle 2 Sekunden|3#alle 3 Sekunden|4#alle 4 Sekunden|5#alle 5 Sekunden|10#alle 10 Sekunden|15#alle 15 Sekunden|20#alle 20 Sekunden|30#alle 30 Sekunden|60#jede Minute|120#alle 2 Minuten|180#alle 3 Minuten|300#alle 5 Minuten']

[row]
[var3 = select,1,'PTZ','0#deaktiviert|1#nur per KO|2#nur manuell|3#per KO und manuell']
[var6 = select,1,'PTZ: Steuerungsrichtung','0#Joystick|1#Natürlich']

[row]
[var7 = select,2,'PTZ: Auflösung während der manuellen Steuerung','0#unverändert|5#5%|10#10%|20#20%|30#30%|40#40%|50#50%|60#60%|70#70%|80#80%|90#90%|100#100%']
###[/PROPERTIES]###


###[ACTIVATION.PHP]###
<?
?>
###[/ACTIVATION.PHP]###


###[EDITOR.PHP]###
<?
$property[0] = sql_getValue('edomiProject.editCamView', 'name', 'id=' . $item['var1']);
?>
###[/EDITOR.PHP]###


###[EDITOR.JS]###
VSE_VSEID=function(elementId,obj,meta,property,isPreview,koValue) {
var mheight=(obj.dataset.var10!='')?obj.dataset.var10:40;
var n="
<table cellpadding='0' cellspacing='0' border='0' style='position:absolute; left:0; top:0; width:100%; height:100%;'>";
    n+="
    <tr style='height:"+mheight+"px;'>
        <td style='"+((obj.dataset.var4&1)?"border-bottom:1px dotted;":"")+"'>"+((obj.dataset.var4&2)?property[0]:"")+"</td>
    </tr>
    ";
    n+="
    <tr>
        <td>"+((isPreview)?meta.itemText:"<span class='app2_pseudoElement'>KAMERA</span>")+"</td>
    </tr>
    ";
    n+="
</table>";
obj.innerHTML=n;

//Text immer zentrieren, kein Padding
obj.style.textAlign="center";
obj.style.padding="0";

return property[0];
}
###[/EDITOR.JS]###


###[VISU.PHP]###
<?
function PHP_VSE_VSEID($cmd, $json1, $json2)
{
    require(MAIN_PATH . "/www/shared/php/incl_camera.php");

    if ($cmd == 'camStreamInit') {
        ?>
        var veVar=visuElement_getGlobal("<? echo $json1['elementId']; ?>");
        var viewport=document.getElementById("e-<? echo $json1['elementId']; ?>-viewport");
        <?
        $camView = sql_getValues('edomiLive.camView', '*', 'id=' . $json1['camViewId']);
        if ($camView !== false) {
            $url = sql_getValue('edomiLive.cam', 'url', 'id=' . $camView['camid'] . ' AND mjpeg=1');
            if (!isEmpty($url)) {
                ?>
                if (veVar && viewport && veVar.id=="<? echo $json1['camViewId']; ?>") {
                document.getElementById("e-<? echo $json1['elementId']; ?>-info").innerHTML="<? echo escapeString($camView['name'], 1); ?>";
                VSE_VSEID_callbackLiveFrame("<? echo $json1['elementId']; ?>",5,"<? echo escapeString($url); ?>");
                }
                <?
            } else {
                ?>
                if (veVar && viewport) {VSE_VSEID_callbackLiveFrame("<? echo $json1['elementId']; ?>",4,false);}
                <?
            }
        } else {
            ?>
            if (veVar && viewport) {VSE_VSEID_callbackLiveFrame("<? echo $json1['elementId']; ?>",3,false);}
            <?
        }
    }

    if ($cmd == 'camLiveInit') {
        ?>
        var veVar=visuElement_getGlobal("<? echo $json1['elementId']; ?>");
        var viewport=document.getElementById("e-<? echo $json1['elementId']; ?>-viewport");
        var image=document.getElementById("e-<? echo $json1['elementId']; ?>-image");
        var canvas=document.getElementById("e-<? echo $json1['elementId']; ?>-canvas");
        <?
        $camView = sql_getValues('edomiLive.camView', '*', 'id=' . $json1['camViewId']);
        if ($camView !== false) {
            $imgUrl = getLiveCamImg($camView['camid'], 1);
            if ($imgUrl) {
                if ($camView['srctyp'] == 0) {
                    ?>
                    if (veVar && viewport && veVar.id=="<? echo $json1['camViewId']; ?>") {
                    document.getElementById("e-<? echo $json1['elementId']; ?>-info").innerHTML="<? echo escapeString($camView['name'], 1); ?>";
                    veVar.srctyp=parseInt("<? echo $camView['srctyp']; ?>");
                    veVar.camView.setProperty("url","../data/liveproject/cam/live/<? echo $imgUrl; ?>?&ts=<? echo getTimestampId(); ?>");
                    veVar.camView.setProperty("dstimage",image);
                    veVar.camView.loadImageToImage("VSE_VSEID_callbackLiveFrame(\"<? echo $json1['elementId']; ?>\",2)","VSE_VSEID_callbackLiveFrame(\"<? echo $json1['elementId']; ?>\",1)");
                    }
                    <?
                } else {
                    ?>
                    if (veVar && viewport && veVar.id=="<? echo $json1['camViewId']; ?>") {
                    document.getElementById("e-<? echo $json1['elementId']; ?>-info").innerHTML="<? echo escapeString($camView['name'], 1); ?>";

                    veVar.srctyp=parseInt("<? echo $camView['srctyp']; ?>");
                    veVar.zoom=parseFloat(veVar.zoom);
                    veVar.v1=parseFloat(veVar.v1);
                    veVar.v2=parseFloat(veVar.v2);
                    if (isNaN(veVar.zoom)) {veVar.zoom=parseFloat("<? echo($camView['zoom'] / 5); ?>");}
                    veVar.camView.setProperty("db_zoom",parseFloat(veVar.zoom)*5);
                    if (veVar.srctyp==1) {
                    if (isNaN(veVar.v1)) {veVar.v1=parseFloat("<? echo($camView['x']); ?>");}
                    veVar.camView.setProperty("db_x",parseFloat(veVar.v1));
                    if (isNaN(veVar.v2)) {veVar.v2=parseFloat("<? echo($camView['y']); ?>");}
                    veVar.camView.setProperty("db_y",parseFloat(veVar.v2));
                    veVar.camView.setProperty("db_a1",0);
                    veVar.camView.setProperty("db_a2",parseInt("<? echo $camView['a2']; ?>"));

                    } else {
                    if (isNaN(veVar.v1)) {veVar.v1=parseFloat("<? echo($camView['a1']); ?>");}
                    veVar.camView.setProperty("db_a1",parseFloat(veVar.v1));
                    if (isNaN(veVar.v2)) {veVar.v2=parseFloat("<? echo($camView['a2']); ?>");}
                    veVar.camView.setProperty("db_a2",parseFloat(veVar.v2));
                    veVar.camView.setProperty("db_x",parseInt("<? echo $camView['x']; ?>"));
                    veVar.camView.setProperty("db_y",parseInt("<? echo $camView['y']; ?>"));
                    }
                    veVar.a2=parseFloat("<? echo($camView['a2']); ?>");    //Drehwinkel bei srytyp=1

                    veVar.camView.setProperty("srctyp",parseInt("<? echo $camView['srctyp']; ?>"));
                    veVar.camView.setProperty("url","../data/liveproject/cam/live/<? echo $imgUrl; ?>?&ts=<? echo getTimestampId(); ?>");
                    veVar.camView.setProperty("srccanvas",false);
                    veVar.camView.setProperty("dstcanvas",canvas);
                    veVar.camView.setProperty("db_dstw",parseInt(viewport.offsetWidth));
                    veVar.camView.setProperty("db_dsth",parseInt(viewport.offsetHeight));
                    veVar.camView.setProperty("db_srcr",parseInt("<? echo(($camView['srctyp'] == 1) ? 0 : $camView['srcr']); ?>"));
                    veVar.camView.setProperty("db_srcd",parseInt("<? echo(($camView['srctyp'] == 1) ? 0 : $camView['srcd']); ?>"));

                    veVar.camView.setProperty("db_srcs",parseInt("<? echo $camView['srcs']; ?>"));
                    veVar.srcs=parseInt("<? echo($camView['srcs']); ?>");

                    veVar.camView.initLoadRender("VSE_VSEID_callbackLiveFrame(\"<? echo $json1['elementId']; ?>\",2)","VSE_VSEID_callbackLiveFrame(\"<? echo $json1['elementId']; ?>\",1)");
                    }
                    <?
                }
            } else {
                ?>
                if (veVar && viewport) {VSE_VSEID_callbackLiveFrame("<? echo $json1['elementId']; ?>",1);}
                <?
            }
        } else {
            ?>
            if (veVar && viewport) {VSE_VSEID_callbackLiveFrame("<? echo $json1['elementId']; ?>",0);}
            <?
        }
    }

    if ($cmd == 'camLiveUpdate') {
        ?>
        var veVar=visuElement_getGlobal("<? echo $json1['elementId']; ?>");
        var viewport=document.getElementById("e-<? echo $json1['elementId']; ?>-viewport");
        var image=document.getElementById("e-<? echo $json1['elementId']; ?>-image");
        var canvas=document.getElementById("e-<? echo $json1['elementId']; ?>-canvas");
        <?
        $camView = sql_getValues('edomiLive.camView', '*', 'id=' . $json1['camViewId']);
        if ($camView !== false) {
            $imgUrl = getLiveCamImg($camView['camid'], 1);
            if ($imgUrl) {
                if ($camView['srctyp'] == 0) {
                    ?>
                    if (veVar && viewport && veVar.id=="<? echo $json1['camViewId']; ?>") {
                    veVar.camView.setProperty("url","../data/liveproject/cam/live/<? echo $imgUrl; ?>?&ts=<? echo getTimestampId(); ?>");
                    veVar.camView.loadImageToImage("VSE_VSEID_callbackLiveFrame(\"<? echo $json1['elementId']; ?>\",2)","VSE_VSEID_callbackLiveFrame(\"<? echo $json1['elementId']; ?>\",1)");
                    }
                    <?
                } else {
                    ?>
                    if (veVar && viewport && veVar.id=="<? echo $json1['camViewId']; ?>") {
                    veVar.camView.setProperty("url","../data/liveproject/cam/live/<? echo $imgUrl; ?>?&ts=<? echo getTimestampId(); ?>");
                    veVar.camView.loadRender("VSE_VSEID_callbackLiveFrame(\"<? echo $json1['elementId']; ?>\",2)","VSE_VSEID_callbackLiveFrame(\"<? echo $json1['elementId']; ?>\",1)");
                    }
                    <?
                }
            } else {
                ?>
                if (veVar && viewport) {VSE_VSEID_callbackLiveFrame("<? echo $json1['elementId']; ?>",1);}
                <?
            }
        } else {
            ?>
            if (veVar && viewport) {VSE_VSEID_callbackLiveFrame("<? echo $json1['elementId']; ?>",0);}
            <?
        }
    }
}

?>
###[/VISU.PHP]###


###[VISU.JS]###
VSE_VSEID_CONSTRUCT=function(elementId,obj) {
var mheight=(obj.dataset.var10!='')?obj.dataset.var10:40;
var n="";
n+="
<div id='e-"+elementId+"-viewport' style='display:block; position:absolute; left:0; top:0; width:100%; height:100%;'>";
    n+="
    <div id='e-"+elementId+"-stream' style='display:none; position:absolute; left:0; top:0; width:100%; height:100%;'></div>
    ";
    n+="<img id='e-"+elementId+"-image' draggable='false' style='display:none; position:absolute; left:0; top:0; width:100%; height:100%;'></img>";
    n+="
    <canvas id='e-"+elementId+"-canvas' style='display:none; position:absolute; left:0; top:0; width:100%; height:100%;'></canvas>
    ";

    n+="
    <table cellpadding='0' cellspacing='0' border='0' style='position:absolute; left:0; top:0; width:100%; height:100%;'>";
        n+="
        <tr id='e-"+elementId+"-zoom' style='height:"+mheight+"px;'>
            <td style='"+((obj.dataset.var4&1)?"border-bottom:1px solid;":"")+"'><span id='e-"+elementId+"-info'
                                                                                       style='display:"+((obj.dataset.var4&2)?"inline":"none")+";'></span></td>
        </tr>
        ";
        n+="
        <tr>
            <td><span id='e-"+elementId+"-text'></span></td>
        </tr>
        ";
        n+="
    </table>
    ";

    n+="
    <div id='e-"+elementId+"-reloadanim' class='reloadAnim'></div>
    ";
    n+="
</div>";

obj.innerHTML=n;

visuElement_newGlobal(elementId,{camView:new class_camView(),id:obj.dataset.var1,zoom:"",v1:"",v2:"",a2:0,srctyp:0,ptz:false,mousex:null,mousey:null,srcs:100});

if (obj.dataset.var2>=0 && obj.dataset.var3&2) {
visuElement_onDrag(obj,0,0,-1);
visuElement_onDrag(document.getElementById("e-"+elementId+"-zoom"),0,0,-1);

} else if (visuElement_hasCommands(elementId)) {
visuElement_onClick(obj,function(veId,objId){visuElement_doCommands(veId);},false);    //Indikator wird ohnehin verdeckt, also false
}
}

VSE_VSEID_REFRESH=function(elementId,obj,isInit,isRefresh,isLive,isActive,koValue) {
//Text immer zentrieren, kein Padding
obj.style.textAlign="center";
obj.style.padding="0";

document.getElementById("e-"+elementId+"-text").innerHTML=visuElement_parseString(visuElement_getText(elementId),koValue);

if (!isInit && !isRefresh) {return;}

var veVar=visuElement_getGlobal(elementId);
if (veVar) {

if (visuElement_hasKo(elementId,1)) {
var currentId=veVar.id;
var n=(koValue+";;;;").split(";");
if (!isNaN(parseInt(n[0]))) {veVar.id=parseInt(n[0]);}
if (!(obj.dataset.var3&1)) {
n[1]="";
n[2]="";
n[3]="";
}
if (!isNaN(parseFloat(n[1]))) {veVar.zoom=parseFloat(n[1]);}
if (!isNaN(parseFloat(n[2]))) {veVar.v1=parseFloat(n[2]);}
if (!isNaN(parseFloat(n[3]))) {veVar.v2=parseFloat(n[3]);}
if (!isNaN(parseInt(n[0])) && veVar.id==currentId && koValue.indexOf(";")<0) {
veVar.zoom="";
veVar.v1="";
veVar.v2="";
isInit=true;
} else if (isInit || veVar.id!=currentId) {
if (isNaN(parseFloat(n[1]))) {veVar.zoom="";}
if (isNaN(parseFloat(n[2]))) {veVar.v1="";}
if (isNaN(parseFloat(n[3]))) {veVar.v2="";}
}
}

if (isInit) {
VSE_VSEID_ajax(elementId,true);
} else if (visuElement_hasKo(elementId,1)) {
if (veVar.id!=currentId) {
VSE_VSEID_ajax(elementId,true);
} else if (veVar.ptz) {
veVar.camView.setProperty("db_zoom",veVar.zoom*5);
if (veVar.srctyp==1) {
veVar.camView.setProperty("db_x",veVar.v1);
veVar.camView.setProperty("db_y",veVar.v2);
} else {
veVar.camView.setProperty("db_a1",veVar.v1);
veVar.camView.setProperty("db_a2",veVar.v2);
}
veVar.camView.render();
}
}
}
}

VSE_VSEID_DRAGSTART=function(elementId,dragObj) {
var obj=document.getElementById("e-"+elementId);
if (obj) {
var pos=visuElement_getMousePosition(obj,dragObj,0);
var veVar=visuElement_getGlobal(elementId);
if (veVar && veVar.ptz) {
veVar.mousex=pos.x;
veVar.mousey=pos.y;

if (obj.dataset.var7!=0) {
veVar.camView.setProperty("db_srcs",obj.dataset.var7);
}
} else {
return false;
}
}
}

VSE_VSEID_DRAGMOVE=function(elementId,dragObj) {
var obj=document.getElementById("e-"+elementId);
if (obj) {
var pos=visuElement_getMousePosition(obj,dragObj,0);
var veVar=visuElement_getGlobal(elementId);
if (veVar && veVar.ptz) {
var dx=pos.x-veVar.mousex;
var dy=pos.y-veVar.mousey;

if (dragObj.id=="e-"+elementId+"-zoom") {
if (veVar.srctyp==1) {veVar.zoom+=dx/(veVar.zoom/10+1);} else {veVar.zoom+=dx/10;}
if (veVar.zoom<0) {veVar.zoom=0;}
if (veVar.zoom>100) {veVar.zoom=100;}
veVar.camView.setProperty("db_zoom",veVar.zoom*5);
} else {
var i=((obj.dataset.var6==0)?1:-1);
var f=2+(veVar.zoom/100)*2;
if (veVar.srctyp==1) {
var tmp=math_rotatePoint(veVar.mousex,veVar.mousey,pos.x,pos.y,veVar.a2,-veVar.mousex,-veVar.mousey)
veVar.v1+=i*tmp.x/f;
veVar.v2+=i*tmp.y/f;
if (veVar.v1<-100) {veVar.v1=-100;}
if (veVar.v1>100) {veVar.v1=100;}
if (veVar.v2<-100) {veVar.v2=-100;}
if (veVar.v2>100) {veVar.v2=100;}
veVar.camView.setProperty("db_x",veVar.v1);
veVar.camView.setProperty("db_y",veVar.v2);
} else {
veVar.v1-=i*dy/f;
veVar.v2+=i*dx/f;
if (veVar.v1<0) {veVar.v1=0;}
if (veVar.v1>90) {veVar.v1=90;}
if (veVar.v2<-180) {veVar.v2=180;}
if (veVar.v2>180) {veVar.v2=-180;}
veVar.camView.setProperty("db_a1",veVar.v1);
veVar.camView.setProperty("db_a2",veVar.v2);
}
}

veVar.mousex=pos.x;
veVar.mousey=pos.y;
veVar.camView.render();
} else {
return false;
}
}
}

VSE_VSEID_DRAGEND=function(elementId,dragObj,dragValue) {
var obj=document.getElementById("e-"+elementId);
if (obj) {

if (obj.dataset.var7!=0) {
var veVar=visuElement_getGlobal(elementId);
if (veVar && veVar.ptz) {
veVar.camView.setProperty("db_srcs",veVar.srcs);
veVar.camView.render();
}
}

if (visuElement_hasKo(elementId,2)) {
var veVar=visuElement_getGlobal(elementId);
if (veVar && veVar.ptz) {
var n=veVar.id+";"+veVar.zoom+";"+veVar.v1+";"+veVar.v2+";";
visuElement_setKoValue(elementId,2,n);
}
}
}
}

VSE_VSEID_ajax=function(elementId,init) {
var d=document.getElementById("e-"+elementId);
if (d) {
var veVar=visuElement_getGlobal(elementId);
if (veVar) {
document.getElementById("e-"+elementId+"-reloadanim").style.display="block";
visuElement_clearTimeout(elementId,1);

if (init===true) {
veVar.ptz=false;
if (d.dataset.var2>=0) {
visuElement_callPhp("camLiveInit",{elementId:elementId,camViewId:veVar.id},null);
} else {
visuElement_callPhp("camStreamInit",{elementId:elementId,camViewId:veVar.id},null);
}
} else {
if (d.dataset.var2>=0) {
visuElement_callPhp("camLiveUpdate",{elementId:elementId,camViewId:veVar.id},null);
}
}
}
}
}

VSE_VSEID_callbackLiveFrame=function(elementId,status,url) {
var obj=document.getElementById("e-"+elementId);
if (obj) {
var veVar=visuElement_getGlobal(elementId);
if (veVar) {
document.getElementById("e-"+elementId+"-reloadanim").style.display="none";
veVar.ptz=false;

if (status==5) {        //Stream: Ok
var stream=document.getElementById("e-"+elementId+"-stream");
stream.innerHTML="
<iframe id='e-"+elementId+"-mjpeg' src='about:blank'
        style='position:absolute; left:0; top:0; width:100%; height:100%; margin:0; padding:0; border:none; pointer-events:none; overflow:hidden;'></iframe>";
var mjpeg=document.getElementById("e-"+elementId+"-mjpeg");
if (mjpeg) {
mjpeg.contentWindow.document.open('text/htmlreplace');
mjpeg.contentWindow.document.write("
<html>
<body style='background:transparent; margin:0; padding:0; -webkit-user-select:none;'><img draggable='false'
                                                                                          style='position:absolute; left:0; top:0; width:100%; height:100%;'
                                                                                          src='"+url+"'></img></body>
</html>");
mjpeg.contentWindow.document.close();
setStatus(true);
} else {
setStatus(false);
}

} else if (status==4) {    //Stream: URL fehlerhaft/keine MJPEG-Kamera
setStatus(false);

} else if (status==3) {    //Stream: Ansicht nicht vorhanden
setStatus(false);

} else if (status==2) {    //Frame: Ok
setStatus(true);
setTimer();
veVar.ptz=((veVar.srctyp>0)?true:false);

} else if (status==1) {    //Frame: Fehler beim Polling/Rendern
setStatus(false);
setTimer();

} else {                //Frame: Ansicht nicht vorhanden
setStatus(false);
}
}
}

function setStatus(status) {
var image=document.getElementById("e-"+elementId+"-image");
var canvas=document.getElementById("e-"+elementId+"-canvas");
var stream=document.getElementById("e-"+elementId+"-stream");

if (status) {
if (obj.dataset.var2>=0) {
stream.style.display="none";
clearObject(stream.id,0);
if (veVar.srctyp==0) {
image.style.display="block";
canvas.style.display="none";
} else {
image.style.display="none";
canvas.style.display="block";
}
} else {
image.style.display="none";
canvas.style.display="none";
stream.style.display="block";
}

} else {
document.getElementById("e-"+elementId+"-info").innerHTML="";
image.style.display="none";
canvas.style.display="none";
stream.style.display="none";
clearObject(stream.id,0);
}
}

function setTimer() {
if (veVar.id>0 && obj.dataset.var2>0) {
visuElement_setTimeout(elementId,1,obj.dataset.var2*1000,function(id){VSE_VSEID_ajax(id);});
}
}
}
###[/VISU.JS]###


###[HELP]###
Das Visuelement "Kamera" ermöglicht das Anzeigen eines Livebildes einer
<link>konfigurierten Kameraansicht***1000-83</link>. Die Kameraansicht kann ggf. per KO oder manuell per Maus angepasst werden (virtuelles PTZ).

<b>Hinweis:</b>
Die PTZ-Option bewirkt lediglich eine Änderung der
<link>Bildbearbeitungs-Einstellungen der konfigurierten Kameraansicht***1020</link>. Die Steuerung echter PTZ-Hardware ist mit diesem Visuelement
<i>nicht</i> möglich.

<h2>Spezifische Eigenschaften</h2>
Für weitere Einstellungen und Optionen siehe:
<link>Allgemeine Informationen zu Visuelementen***1002</link>

<ul>
    <li>
        Kameraansicht: Auswahl einer
        <link>
        konfigurierten Kameraansicht***1000-83</link>, deren Livebild angezeigt werden soll
        <ul>
            <li>Wichtig: die direkte Auswahl einer
                <link>
                konfigurierten Kamera***1000-81</link> ist nicht möglich, es muss stets eine Kameraansicht konfiguriert und ausgewählt werden
            </li>
        </ul>
    </li>

    <li>
        Titel-/Zoom-Zeile: legt fest, ob der Name der Kameraansicht angezeigt werden soll
        <ul>
            <li>unsichtbar: die "Titel-/Zoom-Zeile" wird nicht angezeigt, ist aber ggf. dennoch zum Zoomen vorhanden</li>
            <li>Abgrenzung: die "Titel-/Zoom-Zeile" wird durch eine Linie abgegrenzt</li>
            <li>Titel: der Name der Kameraansicht wird angezeigt</li>
        </ul>
    </li>

    <li>Höhe: legt die Höhe der "Titel-/Zoom-Zeile" fest (in Pixeln)</li>

    <li>
        Aktualisierung per Intervall: legt fest, in welchem Intervall das Livebild aktualisiert werden soll
        <ul>
            <li>MJPEG-Stream direkt einbinden: Ein ggf. konfigurierter MJPEG-Stream wird direkt von der Visualisierung (Client) abgerufen und angezeigt. Die
                gewählte Kameraansicht wird daher <i>nicht</i> auf den angezeigten Stream angewendet, stattdessen wird der Stream der in der Kameraansicht
                gewählten Kamera direkt angezeigt.
            </li>
            <li>deaktiviert: das Livebild wird nicht per Intervall aktualisiert (ggf. jedoch per KO, s.o.)</li>
            <li>"alle x Sekunden/Minuten": das Livebild wird in dem ausgewählten Intervall aktualisiert (und ggf. zusätzlich per KO, s.o.)</li>
            <li>Das Aktualisierungsintervall bezieht sich stets auf das letzte erfolgreiche Abrufen eines Kamerabildes: Die Zeitmessung beginnt erst, wenn ein
                Kamerabild vollständig von der Kamera abgerufen wurde.
            </li>
            <li>Hinweis: Die Kamerabilder werden u.U. zwischengespeichert, um eine Kamera bei mehrfachen Zugriffen zur selben Zeit zu entlasten (siehe
                <link>
                Basis-Konfiguration***a-1</link> bzw.
                <link>
                Kameraeinstellungen***1000-81</link>)
            </li>
            <li>Hinweis: Es erfolgt <i>keine</i> Livebild-Aktualisierung per Intervall, wenn das Livebild direkt als MJPEG-Stream eingebunden wird.</li>
        </ul>
    </li>

    <li>
        PTZ: legt fest, ob die (virtuellen) PTZ-Parameter der Kameraansicht (z.B. Zoom, Schwenk-Winkel, etc.) per KO oder per Maus modifiziert werden kann
        <ul>
            <li>per KO: die PTZ-Parameter können per KO1 angepasst werden (s.u.)</li>
            <li>
                manuell: die PTZ-Parameter können per Maus angepasst werden
                <ul>
                    <li>Klicken und Ziehen nach rechts bzw. links in der "Titel-/Zoom-Zeile" bewirkt eine Änderung des Zoom-Faktors</li>
                    <li>Klicken und Ziehen ausserhalb der "Titel-/Zoom-Zeile" verändert den Blickwinkel (bei "360-Grad-Entzerrung") bzw. die Position (bei
                        "Ausschnitt") der Ansicht
                    </li>
                </ul>
            </li>
        </ul>
    </li>

    <li>
        PTZ Steuerungsrichtung: bei manueller Steuerung kann die Wirkung der Mausbewegung bei Bedarf umgekehrt werden
        <ul>
            <li>Joystick: eine Mausbewegung z.B. nach links bewirkt ein Schwenken der Ansicht nach links (mit der Maus wird quasi die Schwenkrichtung
                vorgegeben)
            </li>
            <li>Natürlich: eine Mausbewegung z.B. nach links bewirkt ein Schwenken der Ansicht nach rechts (die Ansicht wird quasi mit der Maus verschoben)</li>
        </ul>
    </li>

    <li>
        PTZ Auflösung während der manuellen Steuerung: bei manueller Steuerung kann die Auflösung des Kamerabildes vorübergehend z.B. reduziert werden, um auf
        leistungsschwachen Endgeräten ein ruckelfreies Bewegen zu ermöglichen
        <ul>
            <li>unverändert: die Auflösung wird unverändert aus den Kameraeinstellungen übernommen</li>
            <li>5..100%: die Auflösung wird während der manuellen Steuerung auf diesen Wert gesetzt, abschließend wird das Kamerabild mit der Auflösung aus den
                Kameraeinstellungen erneut gerendert
            </li>
        </ul>
    </li>
</ul>


<h2>Kommunikationsobjekte</h2>
Dieses Visuelement kann (optional) folgende Kommunikationsobjekte (KO) verwalten:

<ul>
    <li>
        KO1: Steuerung (ggf. PTZ)
        <ul>
            <li>dieser KO-Wert wird zur <span style="background:#e0ffe0;">Steuerung und Beschriftung</span> verwendet (Designs, Funktionen und Formeln)</li>
            <li>zudem kann mit diesem KO ggf. die Kameraansicht angepasst werden (s.u.)</li>
        </ul>
    </li>

    <li>
        KO2: PTZ-Wert setzen
        <ul>
            <li>dieses KO wird ggf. auf diverse Metadaten gesetzt (s.u.)</li>
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


<h3>Anpassen der Kameraansicht per KO1</h3>
Die aktuelle Kameraansicht kann mit KO1 jederzeit angepasst werden, sofern die entsprechende Option "PTZ: per KO" ausgewählt wurde.

<b>Wichtig:</b>
Bei deaktivierter(!) Option "PTZ: per KO" kann die Kameraansicht dennoch per KO1 gewechselt werden (z.B. "3" wechselt zur Kameraansicht "3"), jedoch werden alle weiteren Parameter (Zoom, etc.) ignoriert und aus den Vorgaben der konfigurierten Kameraansicht übernommen.

Der Wert von KO1 muss dabei diesem Schema folgen:
<ul>
    <li>
        "Kameraansicht-ID;Zoom;Parameter1;Parameter2;" (ohne "")
        <ul>
            <li>Kameraansicht-ID: die ID der
                <link>
                konfigurierten Kameraansicht***1000-83</link></li>
            <li>Zoom: Zoom-Faktor im Bereich 0..100 (100=maximaler Zoom)</li>
            <li>Parameter1: Neige-Winkel im Bereich -90..90 (bei "360-Grad-Entzerrung") bzw. X-Position im Bereich -100..100 (bei "Ausschnitt")</li>
            <li>Parameter2: Schwenk-Winkel im Bereich -180..180 (bei "360-Grad-Entzerrung") bzw. Y-Position im Bereich -100..100 (bei "Ausschnitt")</li>
        </ul>
    </li>

    <li>
        nicht angegebene Parameter werden unverändert angewendet:
        <ul>
            <li>"7;;20": setzt nur Parameter1 auf "20" und wechselt zur Kameraansicht "7" (Zoom und Parameter2 bleiben unverändert)</li>
            <li>";50": setzt nur den Zoom-Faktor auf "50"</li>
            <li>"": bewirkt keinerlei Änderungen</li>
        </ul>
    </li>

    <li>
        beim Wechsel der Kameraansicht ohne die Angabe weiterer Parameter bzw. beim ersten Aufruf (Seitenaufbau) des Visuelements werden die Vorgaben aus der
        konfigurierten Kameraansicht übernommen
        <ul>
            <li>"3" (ohne Semikolon oder weitere Angaben!): wechselt zur Kameraansicht "3" und übernimmt dabei die Vorgaben aus der konfigurierten
                Kameraansicht
            </li>
        </ul>
    </li>
</ul>

<b>Hinweis:</b>
Sofern die Option "PTZ: manuell" ausgewählt wurde, wird zudem KO2(!) ggf. nach dem gleichen Schema auf einen entsprechenden Wert gesetzt, sobald die Kameraansicht manuell per Maus modifiziert wurde.


<h2>Besonderheiten</h2>
<ul>
    <li>die Kameraansicht kann ggf. per KO1 angepasst werden (s.o.), sofern ein entsprechender PTZ-Modus ausgewählt wurde</li>
    <li>Designs: Innenabstand und Textausrichtung werden ignoriert</li>
    <li>Seitensteuerung/Befehle stehen nur dann zu Verfügung, wenn die Option "MJPEG-Stream einbinden" bzw. kein manueller PTZ-Modus ausgewählt wurde</li>
    <li>Hinweis: wenn keine Seitensteuerungen/Befehle zugewiesen wurden, verhält sich dieses Visuelement dennoch nicht
        <link>
        klicktransparent***1002</link></li>
</ul>


<h2>Bedienung in der Visualisierung</h2>
Je nach PTZ-Modus kann die Kameraansicht ggf. im Sinne eines virtuellen PTZ per KO oder manuell per Maus angepasst werden:

Klicken und Ziehen innerhalb des Visuelements bewirkt eine Änderung des Blickwinkels oder der Position.
Ein Klicken und Ziehen nach rechts bzw. links innerhalb der Titel-/Zoom-Zeile (auch wenn diese unsichtbar ist) verändert den Zoom-Faktor der Kameraansicht.

<b>Hinweis:</b>
Bei gewählter Option "MJPEG-Stream direkt einbinden" kann die Ansicht nicht verändert werden.

###[/HELP]###
