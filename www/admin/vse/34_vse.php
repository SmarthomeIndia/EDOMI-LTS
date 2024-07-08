###[DEF]###
[name    =Befehle]

[folderid=164]
[xsize    =100]
[ysize    =50]

[var1    =1]

[flagText        =1]
[flagKo1        =1]
[flagKo2        =0]
[flagKo3        =0]
[flagPage        =1]
[flagCmd        =1]
[flagDesign        =0]
[flagDynDesign    =0]

[captionText    =KO-Wert]
###[/DEF]###


###[PROPERTIES]###
[columns=100]
[row]
[var1 = select,1,'Ausführung','1#bei Seitenaufruf|2#KO-gesteuert|3#bei Seitenaufruf und KO-gesteuert']
###[/PROPERTIES]###


###[EDITOR.JS]###
VSE_VSEID=function(elementId,obj,meta,property,isPreview,koValue) {
if (isPreview) {
var n="
<table cellpadding='0' cellspacing='0' width='100%' height='100%' border='0'>
    <tr>
        <td><span class='app2_pseudoElement'>(UNSICHTBAR)</span></td>
    </tr>
</table>";
} else {
var n="
<table cellpadding='0' cellspacing='0' width='100%' height='100%' border='0'>
    <tr>
        <td><span class='app2_pseudoElement'>BEFEHLE</span></td>
    </tr>
</table>";
}
obj.innerHTML=n;

//Text immer zentrieren, kein Padding
obj.style.textAlign="center";
obj.style.padding="0";

return false;
}
###[/EDITOR.JS]###


###[VISU.JS]###
VSE_VSEID_CONSTRUCT=function(elementId,obj) {
//unsichtbares Visuelement
}

VSE_VSEID_REFRESH=function(elementId,obj,isInit,isRefresh,isLive,isActive,koValue) {
//VE ist immer unsichtbar
obj.style.display="none";

if (isInit && obj.dataset.var1&1) {
visuElement_doCommands(elementId);
} else if (isRefresh && obj.dataset.var1&2 && visuElement_hasKo(elementId,1)) {
if (koValue.toString()===visuElement_getText(elementId).toString()) {
visuElement_doCommands(elementId);
}
}
}
###[/VISU.JS]###


###[HELP]###
Das Visuelement "Befehle" ermöglicht die automatische Ausführung der Angaben unter "Seitensteuerung" und "Befehle". Sobald die Visuseite, die dieses Visuelement enthält aufgerufen wird, werden ggf. die entsprechenden Seitensteuerungen/Befehle ausgeführt (ohne Nutzerinteraktion). Optional kann die Ausführung auch KO-gesteuert erfolgen.

<b>Hinweis:</b>
Dieses Visuelement ist in der Visualisierung nicht sichtbar.

<b>Achtung:</b>
Das automatisierte Aufrufen von Visuseiten kann u.U. zu einer Endlosschleife führen, z.B. wenn das Visuelement derart konfiguriert wurde, dass die das Visuelement enthaltene Visuseite erneut aufgerufen wird.


<h2>Spezifische Eigenschaften</h2>
Für weitere Einstellungen und Optionen siehe:
<link>Allgemeine Informationen zu Visuelementen***1002</link>

<ul>
    <li>
        Ausführung: legt fest, unter welchen Bedingungen die Befehle ausgeführt werden sollen
        <ul>
            <li>bei Seitenaufruf: die Ausführung erfolgt bei jedem Aufruf der Visuseite, die dieses Visuelement enthält</li>
            <li>KO-gesteuert: die Ausführung erfolgt immer dann, wenn der Wert des KOs (s.u.) der Angabe im Feld "Beschriftung" entspricht</li>
            <li>Wichtig: Sofern die o.g. Bedingung erfüllt ist, erfolgt die Ausführung bei jedem Seitenaufruf erneut, bis die o.g. Bedingung nicht mehr erfüllt
                ist.
            </li>
        </ul>
    </li>
</ul>


<h2>Kommunikationsobjekte</h2>
Dieses Visuelement kann (optional) folgende Kommunikationsobjekte (KO) verwalten:

<ul>
    <li>
        KO1: Steuerung
        <ul>
            <li>dieses KO wird ausschließlich zur Ausführung der Seitensteuerungen/Befehle (s.o.) verwendet, indem dessen Wert mit der Angabe im Feld
                "Beschriftung" verglichen wird
            </li>
        </ul>
    </li>
</ul>


<h2>Besonderheiten</h2>
<ul>
    <li>
        Verhalten des Visuelements:
        <ul>
            <li>wird die Visuseite, die das Visuelement enthält erneut aufgerufen, erfolgt die Ausführung der Seitensteuerungen/Befehle ggf. erneut</li>
        </ul>
    </li>

    <li>im Feld "Beschriftung" muss ggf. ein KO-Vergleichswert angegeben werden (s.o.)</li>
    <li>das Feld "Beschriftung" wird <i>nicht</i> zur <span style="background:#e0ffe0;">Steuerung und Beschriftung</span> verwendet, sondern repräsentiert
        ausschließlich den KO-Vergleichswert zur KO-gesteuerten Ausführung der Befehle
    </li>
    <li>Designs stehen nicht zu Verfügung (das Visuelement ist in der Visualisierung nicht sichtbar)</li>
</ul>


<h2>Bedienung in der Visualisierung</h2>
In der Visualisierung ist dieses Element vollständig unsichtbar und daher nicht bedienbar. Die Steuerung erfolgt ausschließlich über den KO-Wert des zugewiesenen KOs bzw. automatisch beim Aufruf der Visuseite.
###[/HELP]###
