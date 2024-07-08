###[DEF]###
[name    =Sprachausgabe]

[folderid=164]
[xsize    =100]
[ysize    =50]

[var1    =de-DE]
[var2    =1]
[var3    =1]

[flagText        =1]
[flagKo1        =1]
[flagKo2        =0]
[flagKo3        =1]
[flagPage        =0]
[flagCmd        =0]
[flagDesign        =0]
[flagDynDesign    =1]

[captionText    =Sprachausgabe]

[flagSpeech        =1]
###[/DEF]###


###[PROPERTIES]###
[columns=100]
[row]
[var1 = select,1,'Sprache','de-DE#Deutsch|en-US#Englisch|es-ES#Spanisch|fr-FR#Französisch']

[row]
[var2 = select,1,'Geschwindigkeit','0.25#sehr langsam|0.5#langsam|1#normal|1.5#schnell|1.75#sehr schnell']

[row]
[var3 = select,1,'Tonhöhe','0.25#sehr tief|0.5#tief|1#normal|1.5#hoch|1.75#sehr hoch']
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
        <td><span class='app2_pseudoElement'>SPRACHAUSGABE</span></td>
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

if (isInit || isRefresh) {
var n=visuElement_parseString(visuElement_getText(elementId),koValue);
if (n=="STOP") {
visuTextToSpeechStop();
} else {
visuTextToSpeechPlay("",obj.dataset.var1,obj.dataset.var2,obj.dataset.var3,n);
}
}
}
###[/VISU.JS]###


###[HELP]###
Das Visuelement "Sprachausgabe" ermöglich das Vorlesen eines beliebigen Textes durch eine browserinterne Funktion. Die Sprachausgabe wird von den meisten aktuellen Browsern unterstützt und funktioniert unabhängig von einer Internetverbindung.

<b>Hinweis:</b>
Dieses Visuelement ist in der Visualisierung nicht sichtbar, daher werden sämtliche Designeigenschaften mit Ausnahme der Eigenschaft "Beschriftung" (s.u.) ignoriert.


<h2>Spezifische Eigenschaften</h2>
Für weitere Einstellungen und Optionen siehe:
<link>Allgemeine Informationen zu Visuelementen***1002</link>

<ul>
    <li>
        Sprache: legt fest, in welcher Sprache die Sprachausgabe erfolgen soll
        <ul>
            <li>Hinweis: Je nach Endgerät sind nicht alle angezeigten Sprachen verfügbar.</li>
        </ul>
    </li>

    <li>
        Geschwindigkeit: legt fest, mit welcher Sprechgeschwindigkeit die Sprachausgabe erfolgen soll
        <ul>
            <li>Hinweis: Je nach Endgerät variiert die Geschwindigkeit der Sprachausgabe unabhängig von dieser Einstellung.</li>
        </ul>
    </li>

    <li>Tonhöhe: legt fest, in welcher Tonlage die Sprachausgabe erfolgen soll</li>
</ul>


<h2>Kommunikationsobjekte</h2>
Dieses Visuelement kann (optional) folgende Kommunikationsobjekte (KO) verwalten:

<ul>
    <li>
        KO1: Steuerung
        <ul>
            <li>dieser KO-Wert wird zur <span style="background:#e0ffe0;">Steuerung und Beschriftung</span> verwendet (Designs, Funktionen und Formeln)</li>
            <li>immer wenn das KO auf einen Wert gesetzt wird, wird der entsprechende Text ("Beschriftung") per Sprachausgabe ausgegeben</li>
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


<h2>Besonderheiten</h2>
<ul>
    <li>
        Verhalten des Visuelements:
        <ul>
            <li>wird im Feld "Beschriftung" des Basis-Designs bereits ein Text angegeben, wird dieser Text unmittelbar vorgelesen sobald die entsprechende
                Visuseite angezeigt wird
            </li>
            <li>die Wiedergabe läuft auch bei einem Seitenwechsel weiter</li>
            <li>wird die Visuseite, die das Visuelement enthält erneut aufgerufen, beginnt die Wiedergabe von vorn</li>
            <li>wird ein dynamisches Design verwendet, wird das Vorlesen des entsprechenden Textes bei jedem Setzen des KOs neugestartet</li>
            <li>der
                <link>
                Befehl "Visu/Visuaccount: Sprachausgabe"***1007</link> (z.B. Logik) beendet die aktuelle Sprachausgabe und wird dann ausgeführt (Sprachausgaben
                werden also nicht "gemischt")
            </li>
        </ul>
    </li>

    <li>Im Feld "Beschriftung" (auch in dynamischen Designs) muss der vozulesende Text angegeben werden.</li>
    <li>die Sprachausgabe kann mit der Angabe "STOP" (Grossbuchstaben!) in der Beschriftung jederzeit beendet werden</li>
    <li>Designs: alle Designeigenschaften mit Ausnahme von "Beschriftung" werden ignoriert (das Visuelement ist in der Visualisierung nicht sichtbar)</li>
    <li>Seitensteuerung/Befehle stehen nicht zu Verfügung</li>
</ul>


<h2>Bedienung in der Visualisierung</h2>
In der Visualisierung ist dieses Element vollständig unsichtbar und daher nicht bedienbar. Die Steuerung erfolgt ausschließlich über den KO-Wert des zugewiesenen KOs.

<b>Wichtig:</b>
Auf einigen Endgeräten (z.B. iOS-basierten Geräten) ist unter Umständen die Tonausgabe und/oder die Sprachausgabe erst dann verfügbar, wenn diese einmalig mit einem Klick (Nutzerinteraktion) aktiviert wurde. In diesem Fall wird am oberen Bildschirmrand die Meldung "Tonausgabe aktivieren" angezeigt und sollte mit einem Klick bestätigt werden (siehe auch
<link>Visualisierung***b-0</link>).
###[/HELP]###


