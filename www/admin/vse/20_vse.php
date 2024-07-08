###[DEF]###
[name    =Kameraarchiv]

[folderid=163]
[xsize    =240]
[ysize    =180]

[var1    =0 #root=82]
[var2    =0 #root=83]
[var3    =0]
[var4    =0]
[var5    =0]
[var6    =1]
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

[captionKo1        =Steuerung (leer=ggf. Status-KO des Archivs)]
[captionKo2        =Archiv-Metadaten setzen]
###[/DEF]###


###[PROPERTIES]###
[columns=50,50]
[row]
[var1 = root,2,'Kameraarchiv',82]

[row]
[var2 = root,2,'Kameraansicht',83]

[row=Darstellung und Aktualisierung]
[var4 = check,2,'','Aktualisierung per KO']

[row]
[var5 = select,1,'Positionsanzeige','0#deaktiviert|1#Indikatorfarbe|2#Zusatzhintergrundfarbe 1']
[var6 = text,1,'Höhe (px)','']

[row]
[var3 = select,2,'Zeitstempel','0#Datum/Uhrzeit|1#Datum/Uhrzeit/Mikrosekunden|2#Wochentag/Datum/Uhrzeit|3#Wochentag/Datum/Uhrzeit/Mikrosekunden']

[row]
[var7 = select,2,'Kopfzeilenposition','0#oben|1#unten']

[row]
[var10= text,2,'Kopfzeilenhöhe (px, leer=Standard)','']
###[/PROPERTIES]###


###[ACTIVATION.PHP]###
<?
//gaid auf das Status-KO des Archivs setzen, falls kein anderes KO angegeben wurde
$tmp = sql_getValues('edomiProject.editArchivCam', 'outgaid', 'id=' . $item['var1'] . ' AND outgaid>0');
if ($tmp !== false) {
    sql_call("UPDATE edomiLive.visuElement SET gaid=" . $tmp['outgaid'] . " WHERE (id=" . $item['id'] . " AND (gaid=0 OR gaid IS NULL))");
}
?>
###[/ACTIVATION.PHP]###


###[EDITOR.PHP]###
<?
$property[0] = sql_getValue('edomiProject.editArchivCam', 'name', 'id=' . $item['var1']);
?>
###[/EDITOR.PHP]###


###[EDITOR.JS]###
VSE_VSEID=function(elementId,obj,meta,property,isPreview,koValue) {
var mheight=(obj.dataset.var10!='')?obj.dataset.var10:40;
var n="
<table cellpadding='0' cellspacing='0' border='1' style='position:absolute; left:0; top:0; width:100%; height:100%;'>";
    if (obj.dataset.var7==0) {
    n+="
    <tr style='height:"+mheight+"px;'>";
        n+="
        <td width='20%' align='center'>&lt;</td>
        ";
        n+="
        <td width='60%' align='center'>
            <div style='max-height:"+mheight+"px; overflow:hidden;'>"+property[0]+"</div>
        </td>
        ";
        n+="
        <td width='20%' align='center'>&gt;</td>
        ";
        n+="
    </tr>
    ";
    if (obj.dataset.var5>0) {n+="
    <tr style='height:"+obj.dataset.var6+"px;'>
        <td colspan='3' style='border-top:1px dotted;'>
            <div
                style='position:relative; left:0; bottom:0; width:20%; height:"+obj.dataset.var6+"px; background:"+((obj.dataset.var5==1)?visu_indiColor:"var(--bgc1)")+";'></div>
        </td>
    </tr>
    ";}
    n+="
    <tr>
        <td colspan='3' align='center' style='border-top:1px dotted;'>"+((isPreview)?meta.itemText:"<span class='app2_pseudoElement'>KAMERAARCHIV</span>")+"
        </td>
    </tr>
    ";

    } else {
    n+="
    <col width='20%'>
    ";
    n+="
    <col width='60%'>
    ";
    n+="
    <col width='20%'>
    ";
    n+="
    <tr>
        <td colspan='3' align='center' style='border-bottom:1px dotted;'>"+((isPreview)?meta.itemText:"<span class='app2_pseudoElement'>KAMERAARCHIV</span>")+"
        </td>
    </tr>
    ";
    if (obj.dataset.var5>0) {n+="
    <tr style='height:"+obj.dataset.var6+"px;'>
        <td colspan='3' style='border-bottom:1px dotted;'>
            <div
                style='position:relative; left:0; bottom:0; width:20%; height:"+obj.dataset.var6+"px; background:"+((obj.dataset.var5==1)?visu_indiColor:"var(--bgc1)")+";'></div>
        </td>
    </tr>
    ";}
    n+="
    <tr style='height:"+mheight+"px;'>";
        n+="
        <td width='20%' align='center'>&lt;</td>
        ";
        n+="
        <td width='60%' align='center'>
            <div style='max-height:"+mheight+"px; overflow:hidden;'>"+property[0]+"</div>
        </td>
        ";
        n+="
        <td width='20%' align='center'>&gt;</td>
        ";
        n+="
    </tr>
    ";
    }
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
    global $global_weekdays;

    if ($cmd == 'camArchiv') {
        ?>
        var viewport=document.getElementById("e-<? echo $json1['elementId']; ?>-viewport");
        var image=document.getElementById("e-<? echo $json1['elementId']; ?>-image");
        var canvas=document.getElementById("e-<? echo $json1['elementId']; ?>-canvas");
        var title=document.getElementById("e-<? echo $json1['elementId']; ?>-infotext");
        <?
        $archivName = sql_getValue('edomiLive.archivCam', 'name', 'id=' . $json1['archivId']);

        //mode: 1=vor, -1=zurück, 0=aktuellster Eintrag
        if ($json1['mode'] == 0) { //aktuellsten Eintrag (dies ist Default beim Öffnen)
            $n = sql_getValues('edomiLive.archivCamData', '*', "(targetid=" . $json1['archivId'] . ") ORDER BY datetime DESC,ms DESC LIMIT 0,1");
        }
        if ($json1['mode'] == 1) { //Navigation: Älteres Bild
            $n = sql_getValues('edomiLive.archivCamData', '*', "(targetid=" . $json1['archivId'] . " AND CONCAT(datetime,LPAD(ms,6,'0'))<'" . $json1['cursor'] . "') ORDER BY datetime DESC,ms DESC LIMIT 0,1");
        }
        if ($json1['mode'] == -1) { //Navigation: Jüngeres Bild
            $n = sql_getValues('edomiLive.archivCamData', '*', "(targetid=" . $json1['archivId'] . " AND CONCAT(datetime,LPAD(ms,6,'0'))>'" . $json1['cursor'] . "') ORDER BY datetime ASC,ms ASC LIMIT 0,1");
        }

        if ($n !== false) {
            $fn = getArchivCamFilename($n['targetid'], $n['camid'], $n['datetime'], $n['ms']) . '.jpg';

            if ($json1['timeMode'] == 0) {
                $tmp = sql_getDateTime($n['datetime']);
            } else if ($json1['timeMode'] == 1) {
                $tmp = sql_getDateTime($n['datetime']) . "<span style='opacity:0.75;'>." . sprintf("%06d", $n['ms']) . "</span>";
            } else if ($json1['timeMode'] == 2) {
                $tmp = $global_weekdays[date('N', strtotime($n['datetime'])) - 1] . " / " . sql_getDateTime($n['datetime']);
            } else if ($json1['timeMode'] == 3) {
                $tmp = $global_weekdays[date('N', strtotime($n['datetime'])) - 1] . " / " . sql_getDateTime($n['datetime']) . "<span style='opacity:0.75;'>." . sprintf("%06d", $n['ms']) . "</span>";
            }

            if (isEmpty($json1['camViewId'])) {
                $json1['camViewId'] = 0;
            }

            $cursor = $n['datetime'] . sprintf("%06d", $n['ms']);

            //KO2 ggf. setzen
            ?>
            if (visuElement_hasKo("<? echo $json1['elementId']; ?>",2)) {
            var n="<? echo date('d.m.Y;H:i:s', strtotime($n['datetime'])); ?>;<? echo $n['ms']; ?>;<? echo $fn; ?>;";
            visuElement_setKoValue("<? echo $json1['elementId']; ?>",2,n);
            }
            <?
            //Positionsanzeige
            if ($json1['posMode'] > 0) {
                $archivCount = sql_getCount('edomiLive.archivCamData', "targetid=" . $json1['archivId']);
                $archivPos = sql_getCount('edomiLive.archivCamData', "targetid=" . $json1['archivId'] . " AND CONCAT(datetime,LPAD(ms,6,'0'))>'" . $cursor . "'");
                $p1 = $archivPos * 100 / $archivCount;
                $p2 = 100 / $archivCount;
                ?>
                if (viewport) {VSE_VSEID_showPosition("<? echo $json1['elementId']; ?>","<? echo $p1; ?>","<? echo $p2; ?>");}
                <?
            }

            $camView = sql_getValues('edomiLive.camView', '*', 'id=' . $json1['camViewId']);
            if ($camView !== false) {
                if ($camView['srctyp'] == 0) {
                    ?>
                    if (viewport) {
                    title.innerHTML="<? echo escapeString($archivName, 1); ?><br><? echo $tmp; ?>";
                    var camView=new class_camView();
                    camView.setProperty("url","../data/liveproject/cam/archiv/<? echo $fn; ?>?ts=<? echo getTimestampId(); ?>");
                    camView.setProperty("dstimage",image);
                    camView.loadImageToImage("VSE_VSEID_callbackArchivLoaded(\"<? echo $json1['elementId']; ?>\",\"<? echo $cursor; ?>\",1,true)","VSE_VSEID_callbackArchivLoaded(\"<? echo $json1['elementId']; ?>\",\"<? echo $cursor; ?>\",0,false)");
                    }
                    <?
                } else {
                    ?>
                    if (viewport) {
                    title.innerHTML="<? echo escapeString($archivName, 1); ?><br><? echo $tmp; ?>";
                    var camView=new class_camView();
                    camView.setProperty("url","../data/liveproject/cam/archiv/<? echo $fn; ?>?ts=<? echo getTimestampId(); ?>");
                    camView.setProperty("srccanvas",false);
                    camView.setProperty("dstcanvas",canvas);

                    camView.setProperty("srctyp",parseInt("<? echo $camView['srctyp']; ?>"));
                    camView.setProperty("db_zoom",parseInt("<? echo $camView['zoom']; ?>"));
                    camView.setProperty("db_a1",parseInt("<? echo(($camView['srctyp'] == 1) ? 0 : $camView['a1']); ?>"));
                    camView.setProperty("db_a2",parseInt("<? echo $camView['a2']; ?>"));
                    camView.setProperty("db_x",parseInt("<? echo $camView['x']; ?>"));
                    camView.setProperty("db_y",parseInt("<? echo $camView['y']; ?>"));
                    camView.setProperty("db_dstw",parseInt(viewport.offsetWidth));
                    camView.setProperty("db_dsth",parseInt(viewport.offsetHeight));
                    camView.setProperty("db_srcr",parseInt("<? echo(($camView['srctyp'] == 1) ? 0 : $camView['srcr']); ?>"));
                    camView.setProperty("db_srcd",parseInt("<? echo(($camView['srctyp'] == 1) ? 0 : $camView['srcd']); ?>"));
                    camView.setProperty("db_srcs",parseInt("<? echo $camView['srcs']; ?>"));
                    camView.initLoadRender("VSE_VSEID_callbackArchivLoaded(\"<? echo $json1['elementId']; ?>\",\"<? echo $cursor; ?>\",2,true)","VSE_VSEID_callbackArchivLoaded(\"<? echo $json1['elementId']; ?>\",\"<? echo $cursor; ?>\",0,false)");
                    }
                    <?
                }
            } else {
                //kein camView definiert => Rohbild anzeigen
                ?>
                if (viewport) {
                title.innerHTML="<? echo escapeString($archivName, 1); ?><br><? echo $tmp; ?>";
                var camView=new class_camView();
                camView.setProperty("url","../data/liveproject/cam/archiv/<? echo $fn; ?>?ts=<? echo getTimestampId(); ?>");
                camView.setProperty("dstimage",image);
                camView.loadImageToImage("VSE_VSEID_callbackArchivLoaded(\"<? echo $json1['elementId']; ?>\",\"<? echo $cursor; ?>\",1,true)","VSE_VSEID_callbackArchivLoaded(\"<? echo $json1['elementId']; ?>\",\"<? echo $cursor; ?>\",0,false)");
                }
                <?
            }
        } else {
            if (sql_getCount('edomiLive.archivCamData', "targetid=" . $json1['archivId']) == 0) {
                //Archiv ist leer
                ?>
                if (viewport) {
                title.innerHTML="<? echo escapeString($archivName, 1); ?>";
                VSE_VSEID_callbackArchivLoaded("<? echo $json1['elementId']; ?>","",0,false);
                }
                <?
            } else {
                //Cursor ist an einem Anschlag (Anfang oder Ende)
                ?>
                if (viewport) {
                VSE_VSEID_callbackArchivLoaded("<? echo $json1['elementId']; ?>","",-1,false);
                }
                <?
            }
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
    n+="<img id='e-"+elementId+"-image' draggable='false' style='display:none; position:absolute; left:0; top:0; width:100%; height:100%;'></img>";
    n+="
    <canvas id='e-"+elementId+"-canvas' style='display:none; position:absolute; left:0; top:0; width:100%; height:100%;'></canvas>
    ";

    n+="
    <table cellpadding='0' cellspacing='0' border='0' style='position:absolute; left:0; top:0; width:100%; height:100%;'>";
        if (obj.dataset.var7==0) {
        n+="
        <tr style='height:"+mheight+"px;'>";
            n+="
            <td width='20%' id='e-"+elementId+"-last'>&lt;</td>
            ";
            n+="
            <td width='60%' id='e-"+elementId+"-info'>
                <div id='e-"+elementId+"-infotext' style='max-height:"+mheight+"px; overflow:hidden;'></div>
            </td>
            ";
            n+="
            <td width='20%' id='e-"+elementId+"-next'>&gt;</td>
            ";
            n+="
        </tr>
        ";
        if (obj.dataset.var5>0) {n+="
        <tr style='height:"+obj.dataset.var6+"px;'>
            <td colspan='3' style='border-top:1px solid;'>
                <div id='e-"+elementId+"-pos'
                     style='position:relative; left:0; bottom:0; width:100%; height:"+obj.dataset.var6+"px; background:"+((obj.dataset.var5==1)?visu_indiColor:"var(--bgc1)")+";'></div>
            </td>
        </tr>
        ";}
        n+="
        <tr>
            <td id='e-"+elementId+"-cmd' colspan='3' style='border-top:1px solid;'><span id='e-"+elementId+"-text'></span></td>
        </tr>
        ";

        } else {
        n+="
        <col width='20%'>
        ";
        n+="
        <col width='60%'>
        ";
        n+="
        <col width='20%'>
        ";
        n+="
        <tr>
            <td id='e-"+elementId+"-cmd' colspan='3' style='border-bottom:1px solid;'><span id='e-"+elementId+"-text'></span></td>
        </tr>
        ";
        if (obj.dataset.var5>0) {n+="
        <tr style='height:"+obj.dataset.var6+"px;'>
            <td colspan='3' style='border-bottom:1px solid;'>
                <div id='e-"+elementId+"-pos'
                     style='position:relative; left:0; bottom:0; width:100%; height:"+obj.dataset.var6+"px; background:"+((obj.dataset.var5==1)?visu_indiColor:"var(--bgc1)")+";'></div>
            </td>
        </tr>
        ";}
        n+="
        <tr style='height:"+mheight+"px;'>";
            n+="
            <td width='20%' id='e-"+elementId+"-last'>&lt;</td>
            ";
            n+="
            <td width='60%' id='e-"+elementId+"-info'>
                <div id='e-"+elementId+"-infotext' style='max-height:"+mheight+"px; overflow:hidden;'></div>
            </td>
            ";
            n+="
            <td width='20%' id='e-"+elementId+"-next'>&gt;</td>
            ";
            n+="
        </tr>
        ";
        }
        n+="
    </table>
    ";

    n+="
    <div id='e-"+elementId+"-reloadanim' class='reloadAnim'></div>
    ";
    n+="
</div>";
obj.innerHTML=n;

obj.dataset.cursor="";
obj.dataset.blocked=0;

if (visuElement_hasCommands(elementId)) {
visuElement_onClick(document.getElementById("e-"+elementId+"-cmd"),function(veId,objId){visuElement_doCommands(veId);},false);
}

if (obj.dataset.var1>0) {
visuElement_onClick(document.getElementById("e-"+elementId+"-last"),function(veId,objId){VSE_VSEID_ArchivNavigate(veId,-1);});
visuElement_onClick(document.getElementById("e-"+elementId+"-info"),function(veId,objId){VSE_VSEID_ArchivNavigate(veId,0);});
visuElement_onClick(document.getElementById("e-"+elementId+"-next"),function(veId,objId){VSE_VSEID_ArchivNavigate(veId,1);});
}
}

VSE_VSEID_REFRESH=function(elementId,obj,isInit,isRefresh,isLive,isActive,koValue) {
//Text immer zentrieren, kein Padding
obj.style.textAlign="center";
obj.style.padding="0";

document.getElementById("e-"+elementId+"-text").innerHTML=visuElement_parseString(visuElement_getText(elementId),koValue);

if (!isInit && !isRefresh) {return;}

if (obj.dataset.var1>0 && (isInit || (isRefresh && obj.dataset.var4==1))) {
VSE_VSEID_ArchivNavigate(elementId,0);
}
}

VSE_VSEID_ArchivNavigate=function(elementId,mode) {
//mode: -1=jüngeres Bild, 1=älteres Bild, 0=aktuellster Archiveintrag oder aktueller Cursor (also das zuletzt angezeigte Archivbild)
var d=document.getElementById("e-"+elementId);
if (d) {
if (d.dataset.blocked==0) {
d.dataset.blocked=1;
document.getElementById("e-"+elementId+"-reloadanim").style.display="block";
visuElement_callPhp("camArchiv",{elementId:elementId,cursor:d.dataset.cursor,mode:mode,archivId:d.dataset.var1,camViewId:d.dataset.var2,timeMode:d.dataset.var3,posMode:d.dataset.var5},null);
}
}
}

VSE_VSEID_showPosition=function(elementId,p1,p2) {
var obj=document.getElementById("e-"+elementId);
if (obj && obj.dataset.var5>0) {
var pos=document.getElementById("e-"+elementId+"-pos");
pos.style.left=p1+"%";
pos.style.width=p2+"%";
}
}

VSE_VSEID_callbackArchivLoaded=function(elementId,cursor,target,status) {
var d=document.getElementById("e-"+elementId);
if (d) {

var image=document.getElementById("e-"+elementId+"-image");
var canvas=document.getElementById("e-"+elementId+"-canvas");
if (target==0) {
image.style.display="none";
canvas.style.display="none";
} else if (target==1) {
image.style.display="block";
canvas.style.display="none";
} else if (target==2) {
image.style.display="none";
canvas.style.display="block";
}

if (status) {d.dataset.cursor=cursor;}

document.getElementById("e-"+elementId+"-reloadanim").style.display="none";
d.dataset.blocked=0;
}
}
###[/VISU.JS]###


###[HELP]###
Das Visuelement "Kameraarchiv" ermöglicht das Anzeigen der Kamerabilder eines
<link>konfigurierten Kameraarchivs***1000-82</link>.

<h2>Spezifische Eigenschaften</h2>
Für weitere Einstellungen und Optionen siehe:
<link>Allgemeine Informationen zu Visuelementen***1002</link>

<ul>
    <li>Kameraarchiv: Auswahl des
        <link>
        konfigurierten Kameraarchivs***1000-82</link>, dessen Inhalt angezeigt werden soll
    </li>

    <li>
        Kameraansicht: Auswahl einer
        <link>
        konfigurierten Kameraansicht***1000-83</link>, die für die Anzeige der Archivbilder verwendet werden soll
        <ul>
            <li>Hinweis: diese Angabe ist optional, ohne Angabe einer Kameraansicht wird das Original-Archivbild angezeigt</li>
            <li>Achtung: prinzipiell könnte hier auch eine Kameraansicht ausgewählt werden, die <i>nicht</i> zur konfigurierten Kamera des Archivs gehört (nicht
                zu empfehlen)
            </li>
        </ul>
    </li>

    <li>
        Aktualisierung per KO: legt fest, ob das Archivbild bei Änderung des KO1-Wertes (s.u.) aktualisiert werden soll
        <ul>
            <li>deaktiviert: das Archivbild wird nicht automatisch aktualisiert</li>
            <li>aktiviert: das Archivbild wird bei jeder KO1-Wertänderung aktualisiert, d.h. es wird stets das aktuellste Archivbild geladen und angezeigt</li>
        </ul>
    </li>

    <li>
        Positionsanzeige: ggf. kann die (zeitliche) Position des aktuell angezeigten Archivbildes bezogen auf das gesamte Archiv angezeigt werden (unterhalb der
        Trennline der Titelzeile)
        <ul>
            <li>deaktiviert: keine Positionsanzeige</li>
            <li>Indikatorfarbe: die Position wird mit der
                <link>
                Indikatorfarbe***1000-21</link> dargestellt
            </li>
            <li>Zusatzhintergrundfarbe 1: die Position wird mit der
                <link>
                Zusatzhintergrundfarbe 1***1003</link> dargestellt
            </li>
        </ul>
    </li>

    <li>Höhe (der Positionsanzeige): legt die Höhe der Positionsanzeige in Pixeln fest</li>

    <li>Zeitstempel: legt das Format des angezeigten Archivbild-Zeitstempels fest</li>

    <li>Kopfzeilenposition: legt die Position der Kopfzeile (oben oder unten) fest</li>

    <li>Kopfzeilenhöhe: legt optional die Höhe der Kopfzeile in Pixeln fest</li>
</ul>


<h2>Kommunikationsobjekte</h2>
Dieses Visuelement kann (optional) folgende Kommunikationsobjekte (KO) verwalten:

<ul>
    <li>
        KO1: Steuerung
        <ul>
            <li>dieser KO-Wert wird zur <span style="background:#e0ffe0;">Steuerung und Beschriftung</span> verwendet (Designs, Funktionen und Formeln)</li>
            <li>immer wenn das KO auf einen Wert gesetzt wird, wird das Archivbild ggf. aktualisiert (siehe "Aktualisierung per KO")</li>
        </ul>
    </li>

    <li>
        KO2: Archiv-Metadaten setzen
        <ul>
            <li>dieses KO wird ggf. auf diverse Metadaten des aktuell angezeigten Archivbilds gesetzt ("Datum;Uhrzeit;Mikrosekunden;Dateiname;")</li>
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
Falls KO1 nicht angegeben wurde, wird das KO1 bei einer Aktivierung automatisch das Status-KO des Kameraarchivs verknüpft (sofern vorhanden). Bei der Verwendung des Status-KO des Kameraarchivs wird das Visuelement bei jeder Änderung des Kameraarchivs ggf. automatisch aktualisiert (siehe "Aktualisierung per KO").


<h2>Besonderheiten</h2>
<ul>
    <li>Designs: Innenabstand und Textausrichtung werden ignoriert</li>
    <li>Seitensteuerung/Befehle werden nur bei einem Klick ausserhalb der Titelzeile ausgeführt</li>
    <li>Hinweis: wenn keine Seitensteuerungen/Befehle zugewiesen wurden, verhält sich dieses Visuelement dennoch nicht
        <link>
        klicktransparent***1002</link></li>
</ul>


<h2>Bedienung in der Visualisierung</h2>
Das Visuelement ist in 2 Teilbereiche gegliedert (von oben nach unten):

<ul>
    <li>
        Titelleiste:
        <ul>
            <li>hier werden Pfeil-Schaltflächen zum Blättern durch die Archivbilder, der Name des
                <link>
                Kameraarchivs***1000-82</link> und ein Zeitstempel des angezeigten Archivbilds angezeigt
            </li>
            <li>ein Klick auf den Namen des Kameraarchivs aktualisiert den Inhalt des Visuelements und führt zur Anzeige des aktuellsten Archivbildes</li>
        </ul>
    </li>

    <li>
        Archivbild:
        <ul>
            <li>hier wird standardmäßig das aktuellste Archivbild angezeigt, mit den o.g. Pfeil-Schaltflächen kann durch das Kameraarchiv navigiert werden</li>
            <li>mit einem Klick auf diesen Bereich werden ggf. die zugewiesenen "Seitensteuerungen/Befehle" ausgeführt</li>
        </ul>
    </li>
</ul>
###[/HELP]###


