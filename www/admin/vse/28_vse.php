###[DEF]###
[name    =Anrufarchiv]

[folderid=163]
[xsize    =250]
[ysize    =200]

[var1    =0 #root=127]
[var2    =0]
[var3    =30]
[var4    =2]
[var5    =0]
[var6    =2]
[var9    =70]
[var10    =]

[flagText        =0]
[flagKo1        =1]
[flagKo2        =0]
[flagKo3        =1]
[flagPage        =1]
[flagCmd        =1]
[flagDesign        =1]
[flagDynDesign    =1]

[captionKo1        =Steuerung (leer=ggf. Status-KO des Archivs)]
###[/DEF]###


###[PROPERTIES]###
[columns=50,50]
[row]
[var1 = root,2,'Anrufarchiv',127]

[row=Aktualisierung]
[var5 = check,2,'','Aktualisierung per KO']

[row=Darstellung]
[var2 = select,2,'Anruftyp','0#nur eingehende Anrufe anzeigen|1#nur ausgehende Anrufe anzeigen|2#alle Anrufe anzeigen']

[row=Zeitstempel]
[var4 = select,1,'Format','0#ohne|1#nur Uhrzeit|2#Datum/Uhrzeit|3#Datum/Uhrzeit/Mikrosekunden']
[var9 = select,1,'Opazität','100#100%|90#90%|80#80%|70#70%|60#60%|50#50%|40#40%|30#30%|20#20%|10#10%']

[row]
[var3 = select,2,'Nachladen','10#weitere 10 Einträge laden|20#weitere 20 Einträge laden|30#weitere 30 Einträge laden|40#weitere 40 Einträge laden|50#weitere 50 Einträge laden']

[row]
[var6 = select,2,'Abtrennung','0#keine|1#Freiraum|2#Linie']

[row]
[var10= text,2,'Kopfzeilenhöhe (px, leer=Standard)','']
###[/PROPERTIES]###


###[ACTIVATION.PHP]###
<?
//gaid auf das Status-KO des Archivs setzen, falls kein anderes KO angegeben wurde
$tmp = sql_getValues('edomiProject.editArchivPhone', 'outgaid', 'id=' . $item['var1'] . ' AND outgaid>0');
if ($tmp !== false) {
    sql_call("UPDATE edomiLive.visuElement SET gaid=" . $tmp['outgaid'] . " WHERE (id=" . $item['id'] . " AND (gaid=0 OR gaid IS NULL))");
}
?>
###[/ACTIVATION.PHP]###


###[EDITOR.PHP]###
<?
$property[0] = sql_getValue('edomiProject.editArchivPhone', 'name', 'id=' . $item['var1']);
?>
###[/EDITOR.PHP]###


###[EDITOR.JS]###
VSE_VSEID=function(elementId,obj,meta,property,isPreview,koValue) {
var mheight=(obj.dataset.var10!='')?obj.dataset.var10:40;
var n="
<table cellpadding='0' cellspacing='0' border='0' style='position:absolute; left:0; top:0; width:100%; height:100%;'>";
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
    n+="
    <tr>
        <td colspan='3' align='center' style='border-top:1px dotted;'>"+((isPreview)?"":"<span class='app2_pseudoElement'>ANRUFARCHIV</span>")+"</td>
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

    if ($cmd == 'archivInfo') {
        if ($json1['cursor'] == 0) {
            //erster Aufruf (es wurde also noch nicht nachgeladen)
            $json1['cursor'] = $json1['load']; //var3 (Default-Anzahl der Einträge beim ersten Start)
            $scrollToTop = true;
        } else {
            //es wurde nachgeladen
            $scrollToTop = false;
        }
        ?>
        var n="";
        n+="
        <table cellpadding='2' cellspacing='0' width='100%' border='0' style='table-layout:auto;'>";
            <?
            if ($json1['callMode'] == 0) {
                $ss1 = sql_call("SELECT * FROM edomiLive.archivPhoneData WHERE (targetid=" . $json1['archivId'] . " AND typ=0) ORDER BY datetime DESC,ms DESC LIMIT 0," . $json1['cursor']);
            }
            if ($json1['callMode'] == 1) {
                $ss1 = sql_call("SELECT * FROM edomiLive.archivPhoneData WHERE (targetid=" . $json1['archivId'] . " AND typ=1) ORDER BY datetime DESC,ms DESC LIMIT 0," . $json1['cursor']);
            }
            if ($json1['callMode'] == 2) {
                $ss1 = sql_call("SELECT * FROM edomiLive.archivPhoneData WHERE (targetid=" . $json1['archivId'] . ") ORDER BY datetime DESC,ms DESC LIMIT 0," . $json1['cursor']);
            }
            $count = 0;
            while ($n = sql_result($ss1)) {
                $ss2 = sql_call("SELECT * FROM edomiLive.phoneBook WHERE (CONCAT(TRIM(phone1),TRIM(phone2))='" . trim($n['phone']) . "')");
                if ($nn = sql_result($ss2)) {
                    $info = $nn['name'] . " (" . $nn['id'] . ")";
                } else {
                    $info = $n['phone'];
                    if ($info == '') {
                        $info = '(anonym)';
                    }
                }
                if ($n['typ'] == 0) {
                    $typ = '&lt;';
                } else {
                    $typ = '&gt;';
                }
                ?>
                n+="
                <tr valign='top'>";
                <?
                if ($json1['timeMode'] == 1) {
                    ?>
                    n+="
                    <td align='left' width='1'
                        style='opacity:<? echo $json1['timeOpacity'] / 100; ?>; white-space:nowrap;'><? echo sql_getTime($n['datetime']); ?></td>";
                    <?
                } else if ($json1['timeMode'] == 2) {
                    ?>
                    n+="
                    <td align='left' width='1'
                        style='opacity:<? echo $json1['timeOpacity'] / 100; ?>; white-space:nowrap;'><? echo sql_getDateTime($n['datetime']); ?></td>";
                    <?
                } else if ($json1['timeMode'] == 3) {
                    ?>
                    n+="
                    <td align='left' width='1'
                        style='opacity:<? echo $json1['timeOpacity'] / 100; ?>; white-space:nowrap;'><? echo sql_getDateTime($n['datetime']); ?><span
                            style='opacity:0.5;'>.<? echo sprintf("%06d", $n['ms']); ?></span></td>";
                    <?
                }
                ?>
                n+="
                <td align='left' width='1'><? echo $typ; ?></td>";
                n+="
                <td align='left' style='word-break:break-all;'><? echo escapeString($info, 1); ?></td>";
                <?
                if ($json1['rowMode'] == 1) {
                    ?>
                    n+="
                    <tr>
                        <td colspan='3'>
                            <div style='width:100%; height:1px;'></div>
                        </td>
                    </tr>";
                    <?
                } else if ($json1['rowMode'] == 2) {
                    ?>
                    n+="
                    <tr>
                        <td colspan='3'>
                            <div style='width:100%; height:1px; border-bottom:1px solid; opacity:0.25;'></div>
                        </td>
                    </tr>";
                    <?
                }
                ?>
                n+="</tr>";
                <?
                $count++;
            }

            if ($count >= $json1['cursor']) {
                ?>
                n+="
                <tr>
                    <td id='e-<? echo $json1['elementId']; ?>-loadmore' colspan='3' align='center'>
                        <div style='margin:5px; padding:5px;'>&middot;&middot;&middot;</div>
                    </td>
                </tr>";
                     n+="
                <tr>
                    <td colspan='3'>
                        <div style='width:100%; height:1px; border-bottom:1px solid; opacity:0.25;'></div>
                    </td>
                </tr>";
                <?
            }
            ?>
            n+="
        </table>";
        VSE_VSEID_callbackLoaded(<? echo $json1['elementId']; ?>,n,<? echo $json1['cursor']; ?>,<? echo(($scrollToTop) ? 'true' : 'false'); ?>,"<? echo escapeString(sql_getValue('edomiLive.archivPhone', 'name', 'id=' . $json1['archivId']), 1); ?>");
        visuElement_onClick(document.getElementById("e-<? echo $json1['elementId']; ?>-loadmore"),function(veId,objId){VSE_VSEID_LoadMore(veId,<? echo $json1['load']; ?>);});
        <?
    }
}

?>
###[/VISU.PHP]###


###[VISU.JS]###
VSE_VSEID_CONSTRUCT=function(elementId,obj) {

var mheight=(obj.dataset.var10!='')?obj.dataset.var10:40;

var n="
<table cellpadding='0' cellspacing='0' border='0' style='position:absolute; left:0; top:0; width:100%; height:100%;'>";
    n+="
    <tr style='height:"+mheight+"px;'>";
        n+="
        <td width='20%' align='center' id='e-"+elementId+"-last'>&lt;</td>
        ";
        n+="
        <td width='60%' align='center' id='e-"+elementId+"-info'>
            <div id='e-"+elementId+"-infotext' style='max-height:"+mheight+"px; overflow:hidden;'></div>
        </td>
        ";
        n+="
        <td width='20%' align='center' id='e-"+elementId+"-next'>&gt;</td>
        ";
        n+="
    </tr>
    ";
    n+="
    <tr>
        <td colspan='3' align='center' style='border-top:1px solid;'>
            <div style='position:relative; height:100%;'>
                <div id='e-"+elementId+"-edit' style='position:absolute; top:0; left:0; right:0; bottom:0; overflow-x:hidden; overflow-y:auto;'></div>
            </div>
        </td>
    </tr>
    ";
    n+="
</table>";
obj.innerHTML=n;

obj.dataset.cursor="";
obj.dataset.blocked=0;

if (visuElement_hasCommands(elementId)) {
visuElement_onClick(document.getElementById("e-"+elementId+"-edit"),function(veId,objId){visuElement_doCommands(veId);});
}

visuElement_onClick(document.getElementById("e-"+elementId+"-last"),function(veId,objId){scrollUp("e-"+veId+"-edit");});
visuElement_onClick(document.getElementById("e-"+elementId+"-info"),function(veId,objId){VSE_VSEID_ShowInfo(veId,0);});
visuElement_onClick(document.getElementById("e-"+elementId+"-next"),function(veId,objId){scrollDown("e-"+veId+"-edit");});
}

VSE_VSEID_REFRESH=function(elementId,obj,isInit,isRefresh,isLive,isActive,koValue) {
//Text immer zentrieren, kein Padding
obj.style.textAlign="center";
obj.style.padding="0";

if (isInit || (isRefresh && obj.dataset.var5==1)) {
VSE_VSEID_ShowInfo(elementId,0);
}
}

VSE_VSEID_ShowInfo=function(elementId,mode) {
//mode: 0=Neustart, 1=Nachladen
var d=document.getElementById("e-"+elementId);
if (d) {
if (d.dataset.blocked==0) {
d.dataset.blocked=1;

if (mode==0) {d.dataset.cursor=0;}
visuElement_callPhp("archivInfo",{elementId:elementId,cursor:d.dataset.cursor,archivId:d.dataset.var1,callMode:d.dataset.var2,load:d.dataset.var3,timeMode:d.dataset.var4,rowMode:d.dataset.var6,timeOpacity:d.dataset.var9},null);
}
}
}

VSE_VSEID_LoadMore=function(elementId,rows) {
var d=document.getElementById("e-"+elementId);
if (d) {
d.dataset.cursor=parseInt(d.dataset.cursor)+rows;
VSE_VSEID_ShowInfo(elementId,1);
}
}

VSE_VSEID_callbackLoaded=function(elementId,content,cursor,scroll,title) {
var d=document.getElementById("e-"+elementId);
if (d) {
d.dataset.blocked=0;

d.dataset.cursor=cursor;
document.getElementById("e-"+elementId+"-infotext").innerHTML=title;
document.getElementById("e-"+elementId+"-edit").innerHTML=content;
if (scroll) {scrollToTop("e-"+elementId+"-edit");}
}
}
###[/VISU.JS]###


###[HELP]###
Das Visuelement "Anrufarchiv" stellt den Inhalt eines
<link>konfigurierten Anrufarchivs***1000-127</link> in der Visualisierung tabellarisch dar.

<h2>Spezifische Eigenschaften</h2>
Für weitere Einstellungen und Optionen siehe:
<link>Allgemeine Informationen zu Visuelementen***1002</link>

<ul>
    <li>Anrufarchiv: Auswahl des
        <link>
        konfigurierten Anrufarchivs***1000-127</link>, dessen Inhalt angezeigt werden soll
    </li>

    <li>
        Aktualisierung per KO: legt fest, ob die angezeigten Inhalte des Anrufarchivs bei Änderung des KO1-Wertes (s.u.) aktualisiert werden soll
        <ul>
            <li>deaktiviert: die angezeigten Inhalte werden nur manuell aktualisiert (durch einen Klick auf die Titelleiste)</li>
            <li>aktiviert: die angezeigten Inhalte werden zusätzlich bei jeder KO1-Wertänderung aktualisiert</li>
        </ul>
    </li>

    <li>Anruftyp: legt fest, welche Anrufe angezeigt werden sollen (eingehend/ausgehend)</li>

    <li>
        Nachladen: legt fest, wieviele Einträge maximal übertragen werden
        <ul>
            <li>die Daten werden blockweise aus dem Anrufarchiv übertragen</li>
            <li>beim ersten Aufruf des Visuelements wird die angegebene Anzahl an Einträgen geladen</li>
        </ul>
    </li>

    <li>Zeitstempel: legt ggf. die Formatierung und die Opazität des Zeitstempels eines Archiveintrags fest</li>

    <li>Abtrennung: fügt ggf. zwischen den Einträgen einen Freiraum bzw. eine Trennlinie ein</li>

    <li>Kopfzeilenhöhe: legt optional die Höhe der Kopfzeile in Pixeln fest</li>
</ul>


<h2>Kommunikationsobjekte</h2>
Dieses Visuelement kann (optional) folgende Kommunikationsobjekte (KO) verwalten:

<ul>
    <li>
        KO1: Steuerung
        <ul>
            <li>dieser KO-Wert wird zur <span style="background:#e0ffe0;">Steuerung und Beschriftung</span> verwendet (Designs, Funktionen und Formeln)</li>
            <li>immer wenn das KO auf einen Wert gesetzt wird, wird das Visuelement ggf. aktualisiert (siehe "Aktualisierung per KO")</li>
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
Falls KO1 nicht angegeben wurde, wird das KO1 bei einer Aktivierung automatisch das Status-KO des Anrufarchivs verknüpft (sofern vorhanden). Bei der Verwendung des Status-KO des Anrufarchivs wird das Visuelement bei jeder Änderung des Archivinhaltes ggf. automatisch aktualisiert.


<h2>Besonderheiten</h2>
<ul>
    <li>das Feld "Beschriftung" steht nicht zu Verfügung, bzw. wird ignoriert</li>
    <li>Designs: Innenabstand und Textausrichtung werden ignoriert</li>
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
            <li>hier werden Pfeil-Schaltflächen zum Blättern durch die Einträge (Scrollen), sowie der Name des
                <link>
                Anrufarchivs***1000-127</link> angezeigt
            </li>
            <li>ein Klick auf den Namen des Anrufarchivs aktualisiert den Inhalt des Visuelements</li>
        </ul>
    </li>

    <li>
        Archiveinträge:
        <ul>
            <li>hier werden die geladenen Einträge des Archivs angezeigt (absteigend nach Zeitstempel sortiert, d.h. die neuesten Einträge werden am Anfag der
                Auflistung angezeigt)
            </li>
            <li>ein ausgehender Anruf wird mit "&gt;", ein eingehender Anruf wird mit "&lt;" gekennzeichnet</li>
            <li>sofern ein zur Rufnummer passender Eintrag im
                <link>
                Telefonbuch***1000-125</link> vorliegt, wird der Name des Anrufers bzw. des Angerufenen angezeigt (ansonsten wird die Rufnummer angezeigt)
            </li>
            <li>mit einem Klick auf diesen Bereich werden alle zugewiesenen Seitensteuerungen/Befehle ausgeführt</li>
        </ul>
    </li>
</ul>
###[/HELP]###


