###[DEF]###
[name        =    Arduino: 26-Bit-Wiegand-Leser (3-fach)]

[e#1 IMPORTANT    =    Aktiviert #init=1                ]
[e#2 IMPORTANT    =    KO &harr; Daten                    ]
[e#3            =    KO &harr; ID                    ]
[e#4            =    KO &harr; Code                    ]
[e#5            =    KO &harr; Lesegerät                ]
[e#6            =    KO &harr; Funktion                ]
[e#7            =    KO &harr; Name                    ]
[e#8 TRIGGER    =    Befehl                            ]
[e#9 OPTION        =    Klartext: Lesegeräte    #init=(keins);Lesegerät 1;Lesegerät 2;Lesegerät 3                            ]
[e#10 OPTION    =    Klartext: Funktionen    #init=(keine);Funktion 1;Funktion 2                                            ]
[e#11 OPTION    =    Klartext: Gültig        #init=Gültigen Eintrag {ID} ({CODE} / {NAME}) an Lesegerät {DEVICE} ({DEVICENAME}) erkannt -> Funktion {FUNCTION} ({FUNCTIONNAME})        ]
[e#12 OPTION    =    Klartext: Ungültig        #init=Ungültigen Eintrag {ID} ({CODE} / {NAME}) an Lesegerät {DEVICE} ({DEVICENAME}) erkannt -> Funktion {FUNCTION} ({FUNCTIONNAME})    ]
[e#13 OPTION    =    Klartext: Unbekannt        #init=Unbekannten Code {CODE} an Lesegerät {DEVICE} ({DEVICENAME}) erkannt                        ]
[e#14 OPTION    =    Klartext: Fehler        #init=Einlesefehler an Lesegerät {DEVICENAME} ({DEVICE})                                        ]
[e#15 OPTION    =    Blockieren (s)            #init=30            ]
[e#16 OPTION    =    Programmiermodus (s)    #init=60            ]
[e#17 OPTION    =    Schnittstelle            #init=/dev/ttyUSB0    ]
[e#18 OPTION    =    Logging                #init=0                ]

[a#1        =    Funktion                            ]
[a#2        =    Blockiert                            ]
[a#3        =    Fehler                                ]
[a#4        =    Klartext                            ]
[a#5        =    Code 1                                ]
[a#6        =    Code 2                                ]
[a#7        =    Code 3                                ]
[a#8        =    Programmiermodus                    ]
[a#9        =    Datensätze                            ]
[a#10        =    Verbindung                            ]
[a#11        =    Klartext: Gültig                    ]
[a#12        =    Klartext: Ungültig                    ]
[a#13        =    Klartext: Unbekannt                    ]
[a#14        =    Klartext: Fehler                    ]
###[/DEF]###


###[HELP]###
Dieser Baustein kommuniziert mit einem Arduino, der mit dem entsprechenden Sketch "26-Bit-Wiegand-Leser" programmiert wurde. Der Sketch ist im Abschnitt [INFO] des Quelltextes dieses Bausteins hinterlegt und muss zunächst auf einen Arduino übertragen werden.

Der Arduino dient als Schnittstelle zwischen EDOMI und bis zu drei 26-Bit-Wiegand-Lesegeräten (z.B. RFID-Leser) und ist wie im Sketch definiert entsprechend mit den Lesegeräten zu verbinden (Wiegand-Datenleitungen D0 und D1).
Eingelesene Codes (z.B. RFID-Transponder) werden über eine serielle Verbindung (i.d.R. per USB) vom Arduino an diesen Baustein übermittelt und ausgewertet.
Auf diese Weise können z.B. RFID-Transponder komfortabel über eine Visualisierung verwaltet und hinzugefügt werden, zudem kann z.B. beim Einlesen eines gültigen Transponders entsprechend reagiert werden (z.B. Triggern einer weiteren Logik zur Türöffnung, etc.).

Mit E1&ne;0 wird der Baustein aktiviert, mit E1=0 deaktiviert. Bei der Aktivierung wird zunächst die serielle Verbindung zum Arduino aufgebaut (über die an E17 definierte Schnittstelle, z.B. "/dev/ttyUSB0"), anschließend wird A10 auf 1 gesetzt. Der Verbindungsaufbau wird solange wiederholt, bis eine Verbindung zustande gekommen ist oder E1=0 gesetzt wird. Die Verbindung wird zudem zyklisch überwacht und ggf. neu aufgebaut. Sobald die Verbindung zum Arduino verloren geht (auch durch Deaktivierung des Bausteins) wird A10 auf 0 gesetzt.


<h2>Grundlegende Funktionsweise</h2>
Der Baustein unterscheidet grundsätzlich zwei Betriebsmodi:

<h3>Normaler Betrieb</h3>
Im normalen Betrieb des Bausteins übermittelt der Arduino einen eingelesenen Code (z.B. eines RFID-Transponders) und das entsprechende Lesegerät (z.B. einen RFID-Leser).
Der Baustein ermittelt dann, ob der Code und das Lesegerät mit einem Datensatz korrespondieren und gibt ggf. die im Datensatz hinterlegte Funktions-ID an A1 aus (weitere Ausgänge werden ggf. ebenfalls aktualisiert).
Mit einem nachgeschalteten Vergleicher kann nun auf diese Funktions-ID reagiert werden (z.B. Türöffnung).

<h3>Programmiermodus</h3>
Im Programmiermodus können neue Datensätze angelegt oder bestehende verändert bzw. gelöscht werden.
Beim Einlesen eines Codes im Programmiermodus werden alle entsprechenden Datensätze hervorgehoben (siehe A9).
Während der Programmiermodus aktiv ist erfolgt keine Ausgabe an A1 (und weiteren Ausgängen), um eine unerwünschte Auslösung von Funktionen zu vermeiden.


<h2>Konfiguration der seriellen Verbindung</h2>
Die Kommunikation mit dem Arduino erfolgt über eine serielle Verbindung (i.d.R. über USB). Die Konfiguration der Parameter (u.a. 9600 Baud) erfolgt automatisch durch diesen Baustein.

<h3>Dauerhafte Zuweisung eines USB-Ports</h3>
Bei Bedarf sollte der physische USB-Port dauerhaft (per UDEV-Regel) mit dem Arduino verknüpft werden, damit es bei der Verwendung von mehreren Arduino-Projekten nicht zu einer Zuweisung eines zufälligen USB-Ports kommen kann.

Mit den folgenden Schritten (z.B. per SSH) wird auf dem EDOMI-Server ein eigener Name für den entsprechenden USB-Port definiert ("/dev/&lt;NAME&gt;"):
<ul>
    <li>
        USB-Live-Monitor starten (zeigt Änderungen an den USB-Ports z.B. beim Einstecken an): <i>udevadm monitor</i>
        <ul>
            <li>Arduino anschließen und den &lt;PFAD&gt; des entsprechenden USB-Port ausfindig machen (z.B. "3-1:1.0")</li>
        </ul>
    </li>

    <li>
        UDEV-Regel-Datei mit folgendem Inhalt anlegen: <i>nano /etc/udev/rules.d/99-usb-serial.rules</i>
        <ul>
            <li>
                KERNELS=="&lt;PFAD&gt;", SUBSYSTEM=="tty", SYMLINK+="&lt;NAME&gt;"
                (ggf. weitere USB-Port-Zuweisungen nach dem gleichen Schema festlegen)
            </li>
        </ul>
    </li>

    <li>abschließend die UDEV-Regel-Datei aktivieren (und den EDOMI-Server sicherheitshalber neustarten): <i>udevadm trigger</i></li>
</ul>

Der entsprechende Arduino ist nun dauerhaft an dem zugewiesenen physischen Port unter "/dev/&lt;NAME&gt;" erreichbar und muss selbstverständlich stets an genau diesem USB-Port angeschlossen werden.


<h2>Eingänge</h2>
E1: 1=Baustein aktivieren, 0=Baustein deaktivieren

E2: an diesem Eingang wird ein remanentes internes KO vom Typ "Variant" erwartet, das als Datenspeicher für sämtliche Codes (und weitere Metadaten) dient
<ul>
    <li>Wichtig: E2 dient zugleich auch als "Ausgang", d.h. das verknüpfte KO wird vom Baustein ggf. auf einen Wert gesetzt (nur im Programmiermodus)</li>
    <li>KO an E2 dient als Datenspeicher für diesen Baustein mit folgendem Aufbau: "CODE|DEVICE|FUNCTION|NAME;CODE|DEVICE|FUNCTION|NAME;..."
        <ul>
            <li>CODE: der Code z.B. eines RFID-Transponders (24-Bit bzw. 0..16777215)</li>
            <li>DEVICE: 1/2/3 = der Datensatz ist beim Einlesen von einem der Lesegeräte 1..3 gültig, 0 = der Datensatz ist für alle Lesegeräte ungültig
                (funktionslos)
            </li>
            <li>FUNCTION: 1..&infin; = der Datensatz repräsentiert die Funktion 1..&infin;, 0 = der Datensatz ist funktionslos</li>
            <li>NAME: beliebiger Name des Datensatzes (die Zeichen ";" und "|" sind nicht erlaubt)</li>
            <li>Hinweis: Jeder Datensatz erhält intern eine fortlaufende ID (1..&infin;).</li>
        </ul>
    </li>

    <li>E2 triggert zudem den LBS, z.B. falls der KO-Wert extern verändert wird</li>
    <li>bei jeder Änderung an E2 (auch durch den Baustein selbst) wird A9 (Liste) aktualisiert</li>
</ul>

E3..E7: diese Eingänge werden nur im Programmiermodus berücksichtigt
<ul>
    <li>Wichtig: E3..E7 dienen zugleich auch als "Ausgang", d.h. das jeweils verknüpfte KO wird vom Baustein ggf. auf einen Wert gesetzt</li>
    <li>beim Abrufen eines vorhandenen Datensatzes (E2) werden die KOs an E3..E7 wie folgt auf einen Wert gesetzt:
        <ul>
            <li>E3 (ID): ID 1..&infin; des Datensatzes</li>
            <li>E4 (Code): Code aus dem Datensatz</li>
            <li>E5 (Lesegerät): zugewiesenes Lesegerät (1..3) aus dem Datensatz</li>
            <li>E6 (Funktion): zugewiesene Funktions-ID aus dem Datensatz</li>
            <li>E7 (Name): Name des Eintrags aus dem Datensatz</li>
        </ul>
    </li>

    <li>beim Einlesen eines Codes werden die KOs an E3..E7 wie folgt auf einen Wert gesetzt:
        <ul>
            <li>E3 (ID): 0</li>
            <li>E4 (Code): eingelesender Code</li>
            <li>E5 (Lesegerät): Lesegerät-ID (1..3), das den Code eingelesen hat</li>
            <li>E6 (Funktion): 0</li>
            <li>E7 (Name): [leer]</li>
        </ul>
    </li>

    <li>beim Absetzes eines Befehls (E8) werden die aktuellen Werte an E3..E7 als Parameter übergeben (E3..E7 können z.B. in einer Visualisierung mit
        Eingabefeldern editierbar präsentiert werden)
    </li>
    <li>Hinweis: Im normalen Betrieb (nicht im Programmiermodus) werden E3..E7 vollständig ignoriert.</li>
</ul>

E8: dieser Eingang ruft einen Datensatz ab, aktiviert den Programmiermodus oder triggert (bei aktiviertem Programmiermodus) einen Befehl
<ul>
    <li>folgende Wertzuweisungen an E8 lösen den entsprechenden Befehl aus:
        <ul>
            <li>1..&infin;: Datensatz mit der ID 1..&infin; abrufen (nur im Programmiermodus)
                <ul>
                    <li>ein vorhandener Datensatz (mit der ID 1..&infin;) wird abgerufen, E3..E7 werden mit den Werten des soeben abgerufenen Datensatzes
                        aktualisiert
                    </li>
                </ul>
            </li>

            <li>0: Programmiermodus beenden (nur im Programmiermodus)
                <ul>
                    <li>der Programmiermodus wird ggf. automatisch nach Ablauf der festgelegten Zeit (E16) beendet</li>
                    <li>beim (Neu)start dieses Bausteins wird der Programmiermodus stets automatisch beendet</li>
                </ul>
            </li>

            <li>-1: Programmiermodus aktivieren (nur im normalen Betrieb)
                <ul>
                    <li>bei aktiviertem Programmiermodus führt ein Einlesevorgang lediglich zur Ausgabe des eingelesenen Codes an E4, zudem werden alle
                        entsprechenden Datensätze (mit diesem Code) hervorgehoben (siehe A9)
                    </li>
                    <li>der Programmiermodus wird ggf. automatisch nach Ablauf der festgelegten Zeit (E16) beendet</li>
                </ul>
            </li>

            <li>-2: Speichern eines vorhandenen Datensatzes (nur im Programmiermodus)
                <ul>
                    <li>E3=1..&infin;: ein vorhandener Datensatz (mit der ID 1..&infin; an E3) wird mit den Werten an E4..E7 überschrieben</li>
                    <li>E3=0/[leer]/[ungültige ID]: der Befehl wird ignoriert</li>
                    <li>nach einem erfolgreichen Speichern des Datensatzes werden E3..E7 mit den Werten des soeben gespeicherten Datensatzes aktualisiert</li>
                </ul>
            </li>

            <li>-3: Hinzufügen eines neuen Datensatzes (nur im Programmiermodus)
                <ul>
                    <li>ein neuer Datensatz wird mit den Werten an E4..E7 hinzugefügt (E3 wird ignoriert)</li>
                    <li>nach einem erfolgreichen Hinzufügen eines Datensatzes werden E3..E7 mit den Werten des soeben hinzugefügten Datensatzes aktualisiert
                    </li>
                </ul>
            </li>

            <li>-4: Abrufen eines vorhandenen Datensatzes (nur im Programmiermodus)
                <ul>
                    <li>E3=1..&infin;: ein vorhandener Datensatz (mit der ID 1..&infin; an E3) wird abgerufen, E3..E7 werden mit den Werten des soeben
                        abgerufenen Datensatzes aktualisiert
                    </li>
                </ul>
            </li>

            <li>-5: Filtern von vorhandenen Datensätzen (nur im Programmiermodus)
                <ul>
                    <li>filtert die Ausgabe an A9 in Abhängigkeit von den Werten an E4..E7 (UND-verknüpft), alle den Filterkriterien entsprechenden Datensätze
                        werden hervorgehoben (siehe A9)
                    </li>
                    <li>ein Wert=[leer] an E4..E7 führt dazu, dass der entsprechende Parameter ignoriert wird</li>
                    <li>der Wert an E7 (Name) bestimmt auch die Anzahl der zu suchenden Zeichen, d.h. E7="Abc" findet auch Name="Abcde", nicht jedoch Name="Ab"
                        (Groß- und Kleinschreibung wird ignoriert)
                    </li>
                </ul>
            </li>

            <li>-6: Löschen eines vorhandenen Datensatzes (nur im Programmiermodus)
                <ul>
                    <li>E3=1..&infin;: ein vorhandener Datensatz (mit der ID 1..&infin; an E3) wird gelöscht</li>
                    <li>E3=0/[leer]/[ungültige ID]: der Befehl wird ignoriert</li>
                    <li>nach einem erfolgreichen Löschen eines Datensatzes werden E3..E7 mit dem Wert [leer] aktualisiert</li>
                </ul>
            </li>
        </ul>
    </li>
</ul>


<h3>Klartext-Konfiguration</h3>
Die Eingänge E9..E14 definieren einige Strings, die für eine lesbare Ausgabe an einigen Ausgängen verwendet werden.

<b>Wichtig:</b>
Änderungen an diesen Eingängen werden nur beim Start des Bausteins (E1=1) übernommen. Änderungen im laufenden Betrieb des Bausteins werden ignoriert.

Der Eingang E9 definiert die Namen der Lesegeräte 1..3:
<ul>
    <li>
        E9: Namen der Lesegeräte als semikolon-separierter String: "&lt;NONE&gt;;&lt;DEVICE1&gt;;&lt;DEVICE2&gt;;&lt;DEVICE3&gt;"
        <ul>
            <li>NONE: kein Lesegerät, d.h. der entsprechende Eintrag ist funktionslos</li>
            <li>DEVICE1..3: Name des Lesegerätes 1..3</li>
        </ul>
    </li>
</ul>

Der Eingang E10 definiert die Namen der Funktionen 1..&infin;:
<ul>
    <li>
        E10: Namen der Funktionen als semikolon-separierter String: "&lt;NONE&gt;;&lt;FUNCTION1&gt;;&lt;FUNCTION2&gt;;..."
        <ul>
            <li>NONE: keine Funktion, d.h. dem entsprechenden Eintrag ist keine Funktion zugewiesen</li>
            <li>FUNCTION1..&infin;: Name der Funktion 1..&infin;</li>
        </ul>
    </li>
</ul>

Die Eingänge E11..E14 definieren jeweils einen Klartext-String, der ggf. an A4 und A11.A14 ausgegeben wird. Wird einem Eingang kein String zugewiesen (der Eingang ist also =[leer]), erfolgt für dieses Ereignis keine entsprechende Klartext-Ausgabe.
Für diese Strings stehen jeweils die folgenden Variablen zu Verfügung:
<ul>
    <li>{ID}: ID eines Datensatzes (1..&infin;)</li>
    <li>{CODE}: Code (24 bit), z.B. eines RFID-Transponders</li>
    <li>{NAME}: Name des Datensatzes</li>
    <li>{DEVICE}: Lesegerät (1..3)</li>
    <li>{DEVICENAME}: Name des Lesegerätes (1..3), definiert an E9</li>
    <li>{FUNCTION}: Funktion (1..&infin;)</li>
    <li>{FUNCTIONAME}: Name der Funktion (1..&infin;), definiert an E10</li>
</ul>

<ul>
    <li>
        E11: Klartext-String für "gültiger Eintrag für ein Lesegerät erkannt"
    </li>
</ul>

<ul>
    <li>
        E12: Klartext-String für "ungültiger Eintrag für ein Lesegerät erkannt"
        <ul>
            <li>Hinweis: "ungültiger Eintrag" bedeutet, dass der eingelesene Code zwar als Eintrag vorhanden ist, jedoch für das einlesenede Lesegerät nicht
                freigegeben oder keine gültige Funktion 1..&infin; zugewiesen wurde
            </li>
        </ul>
    </li>
</ul>

<ul>
    <li>
        E13: Klartext-String für "unbekannten Code an einem Lesegerät erkannt"
        <ul>
            <li>Hinweis: es stehen nur die Variablen {CODE}, {DEVICE} und {DEVICENAME} zu Verfügung</li>
        </ul>
    </li>
</ul>

<ul>
    <li>
        E14: Klartext-String für "fehlerhaftes Einlesen"
        <ul>
            <li>Hinweis: es stehen nur die Variablen {DEVICE} und {DEVICENAME} zu Verfügung</li>
        </ul>
    </li>
</ul>


<h3>Einstellungen</h3>
<b>Wichtig:</b>
Änderungen an diesen Eingängen werden nur beim Start des Bausteins (E1=1) übernommen. Änderungen im laufenden Betrieb des Bausteins werden ignoriert.

E15: 0=nicht blockieren, 1..&infin;=Blockierzeit in Sekunden (nach dem Einlesen eines unbekannten Codes (d.h. der Code ist unbekannt, ungültig oder es ist keine Funktion zugewiesen) kann das weitere Einlesen bei Bedarf blockiert werden)
<ul>
    <li>die Blockierzeit wird bei jedem Fehlversuch verdoppelt (beginnend mit 1s), bis die maximale Blockierzeit (E15) erreicht ist</li>
    <li>erst wenn ein gültiger Code erkannt wird, wird die Blockierzeit zurückgesetzt und beginnt beim nächsten Fehlversuch wieder bei 1s</li>
    <li>der Programmiermodus ist von einer Blockierung ausgenommen</li>
    <li>Hinweis: Bei Neustart des Bausteins wird Blockierung vollständig zurückgesetzt.</li>
</ul>

E16: 0=Programmiermodus nicht automatisch beenden, 1..&infin;=Zeit in Sekunden bis der Programmiermodus automatisch beendet wird
<ul>
    <li>ist das automatische Beenden des Programmiermodus aktiviert, wird der interne Timer bei jedem Befehl und bei jedem Einlesen eines Codes zurückgesetzt
        (ähnlich wie bei einem Bildschirmschoner)
    </li>
</ul>

E17: Pfad zur seriellen Schnittstelle, z.B. "/dev/ttyUSB0" (Hinweise unter "Konfiguration der seriellen Verbindung" beachten)

E18: Protokollierung (Debug): &ne;0=aktivieren (im Individual-Log "LBS17900020-&lt;Instanz-ID&gt;"), 0=deaktivieren


<h2>Ausgänge</h2>
A1: Funktions-ID des zuletzt eingelesenen Datensatzes (nur im normalen Betrieb)
<ul>
    <li>1..&infin; = es wurde ein gültiger Code an einem Lesegerät eingelesen, dem entsprechenden Datensatz ist diese Funktions-ID zugewiesen</li>
    <li>0 = ungültiger, unbekannter oder funktionsloser (Funktions-ID=0) Code wurde eingelesen</li>
    <li>Hinweis: Im Regelfall sollte A1 mit einem Vergleicher verbunden werden, um je nach Funktions-ID die gewünschte Funktion (z.B. Türöffnung) auslösen zu
        können.
    </li>
</ul>

A2: wenn ein ungültiger, unbekannter oder funktionsloser (Funktions-ID=0) eingelesen wurde, wird A2 ggf. auf die aktuelle Blockierzeit gesetzt (nur im normalen Betrieb)
<ul>
    <li>1..&infin; = das Einlesen wird für diesen Zeitraum (in Sekunden) blockiert</li>
    <li>0 = das Einlesen ist wieder freigegeben (die Blockierzeit ist abgelaufen)</li>
    <li>Wichtig: A2 wird nur initial auf die aktuelle Blockierzeit gesetzt, d.h. die Blockierzeit wird nicht heruntergezählt.</li>
</ul>

A3: 1..3 = das Einlesen an einem Lesegerät 1..3 war fehlerhaft (nur im normalen Betrieb)
<ul>
    <li>ggf. erfolgt zudem eine Klartextausgabe an A4 bzw. A14</li>
</ul>

A4: Klartext-Ausgabe aller an E11..E14 definierten Ereignisses (nur im normalen Betrieb)

A5..7: letzter eingelesener Code an Lesegerät 1, 2 bzw. 3 (nur im normalen Betrieb)
<ul>
    <li>Wichtig: Es erfolgt keine Prüfung des Codes! Ausgegeben wird stets der zuletzt eingelesene Code (z.B. für eigene Auswertungen).</li>
</ul>

A8: Programmiermodus
<ul>
    <li>1=Programmiermodus aktiv</li>
    <li>0=Programmiermodus nicht aktiv (normaler Betrieb)</li>
    <li>Hinweis: Beim (Neu)start des Bausteins ist der Programmiermodus stets nicht aktiv.</li>
</ul>

A9: Ausgabe sämtlicher Datensätze als speziell formatiertem String
<ul>
    <li>die Ausgabe eignet sich z.B. als Datenquelle für das Visuelement Liste/Tabelle (mit den entsprechenden Einstellungen)</li>
    <li>
        die Ausgabe erfolgt stets in diesem Format: "ID|NAME|CODE|DEVICE|FUNCTION;ID|NAME|CODE|DEVICE|FUNCTION;..."
        <ul>
            <li>
                ID: 1..&infin; = die ID des Datensatzes
                <ul>
                    <li>falls der Datensatz den Filterkriterien (s.o.) entspricht, wird an die ID ein "*" angehängt (z.B. "12*")</li>
                    <li>dies kann z.B. mit dem Visuelement Liste/Tabelle als "Hervorhebung" ausgewertet und dargestellt werden</li>
                </ul>
            </li>
            <li>NAME: der Name des Datensatzes</li>
            <li>CODE: der Code des Datensatzes</li>
            <li>DEVICE: 1..3 = das zugewiesene Lesegerät des Datensatzes</li>
            <li>FUNCTION: 0..&infin; = die zugewiesene Funktions-ID des Datensatzes</li>
        </ul>
    </li>

    <li>Hinweis: A9 wird immer dann aktualisiert, wenn E2 getriggert wird oder ein entsprechender Befehl (E8) abgesetzt wird (bei jeder Änderung der Datensätze
        wird A9 aktualisiert)
    </li>
</ul>

A10: Status der seriellen Verbindung zum Arduino (s.o.): 1=Verbindung erfolgreich hergestellt, 0=keine Verbindung (bzw. Baustein ist deaktviert)

A11..A14: Klartext-Ausgabe des an E11..E14 definierten Ereignisses (nur im normalen Betrieb)
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id)
{
    if ($E = logic_getInputs($id)) {
        if (logic_getStateExec($id) == 0) {
            if ($E[1]['refresh'] == 1 && $E[1]['value'] != 0 && (!isEmpty($E[17]['value']))) {
                logic_callExec(LBSID, $id);
            }

        } else {
            logic_setInputsQueued($id, $E, true, array(1, 2, 8));
        }
    }
}

?>
###[/LBS]###


###[EXEC]###
<?
require(dirname(__FILE__) . "/../../../../main/include/php/incl_lbsexec.php");

set_time_limit(0);
restore_error_handler();
error_reporting(0);

sql_connect();
$void = new LB_LBSID_wiegand($id);
sql_disconnect();


class LB_LBSID_wiegand
{
    private $cmd_interval = 1;
    private $cmd_timer = false;

    private $blocked = false;
    private $block_timeout = 0.5;
    private $block_timer = 0;

    private $prg_timer = 0;

    private $exitLBS = false;
    private $tty = false;
    private $pingInterval = 30;
    private $id;
    private $E;
    private $outSBC = array(false, null, null, null, null, null, null, null, null, null, null, null, null, null, null);

    private $dataArr = array();
    private $selectLast = array(false, false, false, false);
    private $prgMode;
    private $deviceNames;
    private $niceOk;
    private $niceDeniedKnown;
    private $niceDeniedUnknown;
    private $niceError;
    private $blockMaxTimeout;
    private $prgModeTimeout;
    private $ttyPath;
    private $logging;

    public function __construct($id)
    {
        $this->id = $id;

        if ($this->E = logic_getInputs($this->id)) {

            $this->deviceNames = explode(';', $this->E[9]['value'] . ';;;', 5);
            $this->functionNames = explode(';', $this->E[10]['value']);
            $this->niceOk = $this->E[11]['value'];
            $this->niceDeniedKnown = $this->E[12]['value'];
            $this->niceDeniedUnknown = $this->E[13]['value'];
            $this->niceError = $this->E[14]['value'];
            $this->blockMaxTimeout = ((intVal($this->E[15]['value']) > 0) ? intVal($this->E[15]['value']) : 0);
            $this->prgModeTimeout = ((intVal($this->E[16]['value']) > 0) ? intVal($this->E[16]['value']) : 0);
            $this->ttyPath = $this->E[17]['value'];
            $this->logging = $this->E[18]['value'];

            $this->out(8, 0);
            $this->out_stringToList($this->E[2]['value']);

            logic_deleteInputsQueued($this->id);

            do {
                if ($this->tty_open()) {
                    $this->proc_loop();
                    $this->tty_close();
                    if (!$this->exitLBS) {
                        sleep(1);
                    }    //Reconnect-Pause
                }
            } while (!$this->exitLBS && logic_getEdomiState() == 1);
            $this->log('EXEC beendet', '');
        }
    }

    private function tty_open()
    {
        do {
            if ($this->E = logic_getInputs($this->id)) {
                if ($this->E[1]['value'] == 0) {
                    $this->exitLBS = true;
                }
                if (!$this->exitLBS) {
                    $err = 0;
                    $tmp = array();

                    $this->log('Konfigurieren: ' . $this->ttyPath, 'TTY');
                    exec("/bin/stty -F /dev/ttyUSB0 cs8 9600 ignbrk -brkint -icrnl -imaxbel -opost -onlcr -isig -icanon -iexten -echo -echoe -echok -echoctl -echoke noflsh -ixon -crtscts", $tmp, $err);

                    if (!$err) {
                        $this->log('Verbindung herstellen: ' . $this->ttyPath, 'TTY');
                        $this->tty = fopen($this->ttyPath, "r+");
                        if ($this->tty !== false) {
                            $this->log('Verbunden mit ' . $this->ttyPath, 'TTY-OK');
                            stream_set_blocking($this->tty, false);
                            $this->out(10, 1);
                            return true;
                        }
                    }

                    $this->log('Verbindungsaufbau mit ' . $this->ttyPath . ' gescheitert -> Neuer Verbindungsaufbau', 'TTY-ERROR');
                }
            }
            if (!$this->exitLBS) {
                sleep(1);
            }    //Reconnect-Pause
        } while (!$this->exitLBS && logic_getEdomiState() == 1);
        return false;
    }

    private function tty_close()
    {
        $this->log('Verbindung mit ' . $this->ttyPath . ' geschlossen', 'TTY-CLOSE');
        $this->out(10, 0);
        fclose($this->tty);
    }

    private function proc_loop()
    {
        if ($this->E = logic_getInputs($this->id)) {
            $pingTimer = getMicrotime();
            $connected = true;
            $rx1 = '';
            $rx = '';
            while (!$this->exitLBS && $connected && logic_getEdomiState() == 1) {

                $this->checkBlockTimer();

                $rx1 = fread($this->tty, 1);
                if ($rx1 == '#') {
                    $rx .= $rx1;

                } else if (!isEmpty($rx) && ($rx1 == "\n" || strlen($rx) > 10000)) {
                    if ($rx1 == "\n") {
                        $rx = substr($rx, 1, strlen($rx) - 1);
                        $pingTimer = getMicrotime();
                        $this->parseResponse($rx);

                    } else {
                        $this->log('Lesefehler (>10.000 Byte)', 'RX-ERROR');
                    }

                    $rx = '';

                } else if (!isEmpty($rx)) {
                    $rx .= $rx1;
                }

                if ($this->checkCmdTimer()) {
                    if ($this->E = logic_getInputsQueued($this->id, false, true)) {
                        if ($this->E[1]['queue'] == 1 && $this->E[1]['refresh'] == 1 && $this->E[1]['value'] == 0) {
                            $this->exitLBS = true;

                        } else {
                            if ($this->E[2]['queue'] == 1 && $this->E[2]['refresh'] == 1) {
                                $this->out_stringToList($this->E[2]['value']);
                            }
                            if ($this->E[8]['queue'] == 1 && $this->E[8]['refresh'] == 1) {
                                $this->parseCmd($this->E[8]['value']);
                            }
                        }
                    }
                    $this->setCmdTimer();
                }

                if ($this->checkPrgTimeout()) {
                    $this->setPrgMode(false);
                }

                if (isEmpty($rx)) {
                    usleep(1000 * 100);    //Idle: 100ms
                } else {
                    usleep(1000 * 1);        //nicht Idle: 1ms
                }

                if ((getMicrotime() - $pingTimer) > $this->pingInterval) {
                    $this->log('Verbindung mit ' . $this->ttyPath . ' verloren (kein PING nach ' . $this->pingInterval . 's empfangen) -> Neuer Verbindungsaufbau', 'TTY-ERROR');
                    $connected = false;
                }
            }
        }
    }

    private function checkBlockTimer()
    {
        if ($this->blocked && (getMicrotime() - $this->block_timer) >= $this->block_timeout) {
            $this->blocked = false;
            $this->out(2, 0, true);
        }
    }

    private function setBlockTimer()
    {
        if ($this->blockMaxTimeout > 0) {
            $this->blocked = true;
            $this->block_timer = getMicrotime();
            $this->block_timeout = intVal($this->block_timeout * 2);
            if ($this->block_timeout > $this->blockMaxTimeout) {
                $this->block_timeout = $this->blockMaxTimeout;
            }
            $this->out(2, $this->block_timeout, true);

        } else {
            $this->blocked = false;
        }
    }

    private function resetBlockTimer()
    {
        $this->block_timeout = 0.5;
    }

    private function checkPrgTimeout()
    {
        if ($this->prgModeTimeout > 0 && $this->prgMode && (getMicrotime() - $this->prg_timer) >= $this->prgModeTimeout) {
            return true;
        }
        return false;
    }

    private function setPrgTimer()
    {
        $this->prg_timer = getMicrotime();
    }

    private function checkCmdTimer()
    {
        if ($this->cmd_timer === false || (getMicrotime() - $this->cmd_timer) >= $this->cmd_interval) {
            return true;
        }
        return false;
    }

    private function setCmdTimer()
    {
        $this->cmd_timer = getMicrotime();
    }

    private function parseCmd($cmd)
    {
        $this->setPrgTimer();
        if ($cmd == 0 && $this->prgMode) {
            $this->setPrgMode(false);

        } else if ($cmd == -1 && !$this->prgMode) {
            $this->setPrgMode(true);

        } else if ($cmd >= 1 && $this->prgMode) {
            $this->loadData(intVal($cmd));

        } else if ($cmd == -2 && $this->prgMode) {
            if ($this->E[3]['value'] >= 1) {
                $this->saveData($this->E[3]['value'], $this->E[4]['value'], $this->E[5]['value'], $this->E[6]['value'], $this->E[7]['value']);
                $this->selectRefresh();
            }

        } else if ($cmd == -3 && $this->prgMode) {
            $this->saveData(0, $this->E[4]['value'], $this->E[5]['value'], $this->E[6]['value'], $this->E[7]['value']);
            $this->selectRefresh();

        } else if ($cmd == -4 && $this->prgMode) {
            $this->loadData($this->E[3]['value']);

        } else if ($cmd == -5 && $this->prgMode) {
            $this->selectData($this->E[4]['value'], $this->E[5]['value'], $this->E[6]['value'], $this->E[7]['value']);

        } else if ($cmd == -6 && $this->prgMode) {
            $this->removeData($this->E[3]['value']);
            $this->selectRefresh();
            logic_setInputKoValue($this->id, 3, '');
            logic_setInputKoValue($this->id, 4, '');
            logic_setInputKoValue($this->id, 5, '');
            logic_setInputKoValue($this->id, 6, '');
            logic_setInputKoValue($this->id, 7, '');
        }
    }

    private function parseResponse($rx)
    {
        $par = explode(';', $rx . ';;', 4);

        if ($par[0] == 'P') {
            $this->pingInterval = ((intval($par[1]) >= 1) ? intval($par[1]) * 2 : 3);    //Ping-Intervall vom Arduino übernehmen (zur Sicherheit wird das Intervall verdoppelt, Default=3)
        } else {
            $this->log($rx, 'RX');
        }

        if ($par[0] == 'R') {
            if ($this->prgMode) {
                $this->setPrgTimer();
                logic_setInputKoValue($this->id, 3, 0);
                logic_setInputKoValue($this->id, 4, $par[1]);
                logic_setInputKoValue($this->id, 5, $par[2]);
                logic_setInputKoValue($this->id, 6, '');
                logic_setInputKoValue($this->id, 7, '');
                $this->selectData($par[1], '', '', '');

            } else if (!$this->blocked) {
                $tmp = $this->checkCode($par[1], $par[2]);
                if ($tmp === false) {
                    if (!isEmpty($this->niceDeniedUnknown)) {
                        $nice = $this->getNice($this->niceDeniedUnknown, '', $par[1], $par[2], '', '');
                        $this->out(4, $nice);
                        $this->out(13, $nice);
                    }
                    $this->out(1, 0);
                    $this->setBlockTimer();

                } else if ($tmp[5]) {
                    if (!isEmpty($this->niceOk)) {
                        $nice = $this->getNice($this->niceOk, $tmp[0], $tmp[1], $par[2], $tmp[3], $tmp[4]);
                        $this->out(4, $nice);
                        $this->out(11, $nice);
                    }
                    $this->out(1, $tmp[3]);
                    $this->resetBlockTimer();

                } else {
                    if (!isEmpty($this->niceDeniedKnown)) {
                        $nice = $this->getNice($this->niceDeniedKnown, $tmp[0], $tmp[1], $par[2], $tmp[3], $tmp[4]);
                        $this->out(4, $nice);
                        $this->out(12, $nice);
                    }
                    $this->out(1, 0);
                    $this->setBlockTimer();
                }
                if ($par[2] == 1) {
                    $this->out(5, $par[1]);
                }
                if ($par[2] == 2) {
                    $this->out(6, $par[1]);
                }
                if ($par[2] == 3) {
                    $this->out(7, $par[1]);
                }
            }
        }

        if ($par[0] == 'E') {
            if (!isEmpty($this->niceError)) {
                $nice = $this->getNice($this->niceError, '', '', $par[1], '', '');
                $this->out(4, $nice);
                $this->out(14, $nice);
            }
            $this->out(3, $par[1]);
        }
    }

    private function selectData($code, $device, $function, $name)
    {
        foreach ($this->dataArr as $id => $data) {
            if (
                ($code !== false && (isEmpty($code) || $code == $data[0])) &&
                ($device !== false && (isEmpty($device) || $device == $data[1])) &&
                ($function !== false && (isEmpty($function) || $function == $data[2])) &&
                ($name !== false && (isEmpty($name) || strToUpper($name) == substr(strToUpper($data[3]), 0, strlen($name))))
            ) {
                $this->dataArr[$id][4] = 1;
            } else {
                $this->dataArr[$id][4] = 0;
            }
        }
        $this->out_dataToList();
        $this->selectLast = array($code, $device, $function, $name);
    }

    private function selectRefresh()
    {
        $this->selectData($this->selectLast[0], $this->selectLast[1], $this->selectLast[2], $this->selectLast[3]);
    }

    private function selectNone()
    {
        foreach ($this->dataArr as $id => $data) {
            $this->dataArr[$id][4] = 0;
        }
        $this->selectLast = array(false, false, false, false);
        $this->out_dataToList();
    }

    private function saveData($id, $code, $device, $function, $name)
    {
        if (!isEmpty($id) && !isEmpty($code)) {
            $device = intVal(trim($device));
            $device = (($device >= 0 && $device < count($this->deviceNames)) ? $device : 0);
            $function = intVal(trim($function));
            $function = (($function >= 0 && $function < count($this->functionNames)) ? $function : 0);
            $name = str_replace(';', '', $name);
            $name = str_replace('|', '', $name);
            if ($id == 0) {
                $this->dataArr[] = array(trim($code), $device, $function, trim($name), 0);
                $this->out_dataToString();
                $this->loadData(count($this->dataArr));

            } else if ($id <= count($this->dataArr)) {
                $this->dataArr[$id - 1] = array(trim($code), $device, $function, trim($name), 0);
                $this->out_dataToString();
                $this->loadData($id);
            }
        }
    }

    private function removeData($id)
    {
        if (!isEmpty($id) && $id > 0 && $id <= count($this->dataArr)) {
            array_splice($this->dataArr, $id - 1, 1);
            $this->out_dataToString();
        }
    }

    private function loadData($id)
    {
        if (!isEmpty($id) && $id > 0 && $id <= count($this->dataArr)) {
            logic_setInputKoValue($this->id, 3, $id);
            logic_setInputKoValue($this->id, 4, $this->dataArr[$id - 1][0]);
            logic_setInputKoValue($this->id, 5, $this->dataArr[$id - 1][1]);
            logic_setInputKoValue($this->id, 6, $this->dataArr[$id - 1][2]);
            logic_setInputKoValue($this->id, 7, $this->dataArr[$id - 1][3]);
        }
    }

    private function checkCode($code, $device)
    {
        //Device=Ok und Funktion=Ok
        foreach ($this->dataArr as $id => $data) {
            if ($data[0] == $code && $data[1] == $device && $data[2] > 0) {
                return array($id + 1, $data[0], $data[1], $data[2], $data[3], true);    //Gültig
            }
        }

        //Device=Ok (aber Funktion=0)
        foreach ($this->dataArr as $id => $data) {
            if ($data[0] == $code && $data[1] == $device) {
                return array($id + 1, $data[0], $data[1], $data[2], $data[3], false);    //Ungültig (keine Funktion!)
            }
        }

        //Device!=Ok
        foreach ($this->dataArr as $id => $data) {
            if ($data[0] == $code) {
                return array($id + 1, $data[0], $data[1], $data[2], $data[3], false);    //Ungültig (kein Device! und u.U. auch keine Funktion)
            }
        }
        return false;    //Code nicht vorhanden
    }

    private function getNice($r, $tId, $tCode, $tDevice, $tFunction, $tName)
    {
        $r = str_ireplace('{ID}', $tId, $r);
        $r = str_ireplace('{CODE}', $tCode, $r);
        $r = str_ireplace('{DEVICE}', $tDevice, $r);
        $r = str_ireplace('{DEVICENAME}', (($tDevice >= 0 && $tDevice < count($this->deviceNames)) ? $this->deviceNames[$tDevice] : '?'), $r);
        $r = str_ireplace('{FUNCTION}', $tFunction, $r);
        $r = str_ireplace('{FUNCTIONNAME}', (($tFunction >= 0 && $tFunction < count($this->functionNames)) ? $this->functionNames[$tFunction] : '?'), $r);
        $r = str_ireplace('{NAME}', $tName, $r);
        return $r;
    }

    private function setPrgMode($mode)
    {
        $this->selectNone();
        logic_setInputKoValue($this->id, 3, '');
        logic_setInputKoValue($this->id, 4, '');
        logic_setInputKoValue($this->id, 5, '');
        logic_setInputKoValue($this->id, 6, '');
        logic_setInputKoValue($this->id, 7, '');

        if ($mode) {
            $this->prgMode = true;
            $this->out(8, 1, true);

        } else {
            $this->prgMode = false;
            $this->out(8, 0, true);
        }
    }

    private function out_stringToList($n)
    {
        $this->dataArr = array();
        $tmp = explode(';', $n);
        foreach ($tmp as $k => $v) {
            $tmp2 = explode('|', $v, 4);
            if (!isEmpty($tmp2[0])) {
                $tmp2[0] = trim($tmp2[0]);
                $tmp2[1] = intVal($tmp2[1]);
                if ($tmp2[1] < 1 || $tmp2[1] > 3) {
                    $tmp2[1] = 0;
                }
                $tmp2[2] = intVal($tmp2[2]);
                if ($tmp2[2] < 0) {
                    $tmp2[2] = 0;
                }
                $tmp2[3] = trim($tmp2[3]);
                $tmp2[4] = 0;
                $this->dataArr[] = $tmp2;
            }
        }
        $this->selectRefresh();
        $this->out_dataToList();
    }

    private function out_dataToList()
    {
        $n = '';
        foreach ($this->dataArr as $id => $data) {
            $tmp1 = (($data[1] >= 0 && $data[1] < count($this->deviceNames)) ? $this->deviceNames[$data[1]] : '?');
            $tmp2 = (($data[2] >= 0 && $data[2] < count($this->functionNames)) ? $this->functionNames[$data[2]] : '?');
            $n .= ($id + 1) . (($data[4] == 1) ? '*' : '') . '|' . $data[3] . '|' . $data[0] . '|' . $tmp1 . '|' . $tmp2 . ';';
        }
        $this->out(9, rtrim($n, ';'), true);
    }

    private function out_dataToString()
    {
        $n = '';
        foreach ($this->dataArr as $id => $data) {
            $n .= $data[0] . '|' . $data[1] . '|' . $data[2] . '|' . $data[3] . ';';
        }
        logic_setInputKoValue($this->id, 2, rtrim($n, ';'));
    }

    private function out($id, $value, $sbc = false)
    {
        if (is_null($this->outSBC[$id]) || $this->outSBC[$id] != $value || !$sbc) {
//			$this->log($value,'-> A'.$id);
            logic_setOutput($this->id, $id, $value);
        }
        $this->outSBC[$id] = $value;
    }

    private function log($msg, $level = '')
    {
        if ($this->logging != 0) {
            writeToCustomLog('LBS17900021-' . $this->id, $level, $msg);
        }
    }
}

?>
###[/EXEC]###


###[INFO]###
/*
---------------------------------------------------------------------------
Arduino-Sketch: 26-Bit-Wiegand-Leser (3-fach) - z.B. für RFID-Transponder
(c) 2020 Dr. Christian Gärtner
www.edomi.de
---------------------------------------------------------------------------

Ausgaben:
- die Ausgaben beginnen immer mit "#" und enden mit CHR(10)
- <CODE> = eingelesener Code (24-Bit, d.h. ohne Parität-Bits)
    -
    <READDEVICE> = Lesegerät 1..3

        #P;[PING_INTERVAL] Heartbeat: wird beim Start und zyklisch (alle PING_INTERVAL Sekunden) ausgegeben
        #R;<CODE>;
            <READDEVICE><CODE> wurde von
                    <READDEVICE> eingelesen
                        #E;
                        <READDEVICE> Lesefehler an
                            <READDEVICE> (kein 26-Bit-Code oder sonstiges)
                                */

                                #define PING_INTERVAL 10 //HEARTBEAT-Intervall (s)

                                #define PIN_INVERT true //true=Eingänge für Wiegand-Data invertieren und Pullup aktivieren (z.B. bei Verwendung von Optokopplern
                                mit invertiertem Eingang), false=Eingänge nicht invertieren
                                #define PIN_READER1_D0 2 //(2) Eingang: Wiegand-Data0 für Reader 1 (0=kein Reader 1 vorhanden)
                                #define PIN_READER1_D1 3 //(3) Eingang: Wiegand-Data1 für Reader 1
                                #define PIN_READER2_D0 4 //(4) Eingang: Wiegand-Data0 für Reader 2 (0=kein Reader 2 vorhanden)
                                #define PIN_READER2_D1 5 //(5) Eingang: Wiegand-Data1 für Reader 2
                                #define PIN_READER3_D0 0 //(6) Eingang: Wiegand-Data0 für Reader 3 (0=kein Reader 3 vorhanden)
                                #define PIN_READER3_D1 0 //(7) Eingang: Wiegand-Data1 für Reader 3

                                unsigned long PING_timer;

                                void setup() {
                                if (PIN_READER1_D0>0) {if (PIN_INVERT) {pinMode(PIN_READER1_D0,INPUT_PULLUP);} else {pinMode(PIN_READER1_D0,INPUT);}}
                                if (PIN_READER1_D1>0) {if (PIN_INVERT) {pinMode(PIN_READER1_D1,INPUT_PULLUP);} else {pinMode(PIN_READER1_D1,INPUT);}}
                                if (PIN_READER2_D0>0) {if (PIN_INVERT) {pinMode(PIN_READER2_D0,INPUT_PULLUP);} else {pinMode(PIN_READER2_D0,INPUT);}}
                                if (PIN_READER2_D1>0) {if (PIN_INVERT) {pinMode(PIN_READER2_D1,INPUT_PULLUP);} else {pinMode(PIN_READER2_D1,INPUT);}}
                                if (PIN_READER3_D0>0) {if (PIN_INVERT) {pinMode(PIN_READER3_D0,INPUT_PULLUP);} else {pinMode(PIN_READER3_D0,INPUT);}}
                                if (PIN_READER3_D1>0) {if (PIN_INVERT) {pinMode(PIN_READER3_D1,INPUT_PULLUP);} else {pinMode(PIN_READER3_D1,INPUT);}}

                                Serial.begin(9600);
                                Serial.setTimeout(500);

                                sendPing(true);
                                }

                                void loop() {
                                long token=getIdFromReaders();
                                if (token>0) {
                                Serial.print("#R;"+String(token_getId(token))+";"+String(token_getActiveReader(token))+"\n");
                                }
                                }

                                long getIdFromReaders() {
                                byte reader=0;
                                int readerId=-1;
                                byte bitId=0;
                                unsigned long resultRaw=0;
                                unsigned long DATA_timer;
                                bool d0;
                                bool d1;
                                static bool f0[3]={HIGH,HIGH,HIGH};
                                static bool f1[3]={HIGH,HIGH,HIGH};

                                do {

                                if (readerId>=0) {
                                //Lesevorgang durch Leser "readerId" gestartet
                                reader=readerId;

                                } else {
                                //Idle (keine Lesevorgang gestartet)
                                sendPing(false);
                                }

                                if (((reader==0 && PIN_READER1_D0>0) || (reader==1 && PIN_READER2_D0>0) || (reader==2 && PIN_READER3_D0>0)) && (readerId<0 ||
                                readerId==reader)) {
                                if (reader==0) {
                                d0=digitalRead(PIN_READER1_D0);
                                d1=digitalRead(PIN_READER1_D1);
                                } else if (reader==1) {
                                d0=digitalRead(PIN_READER2_D0);
                                d1=digitalRead(PIN_READER2_D1);
                                } else if (reader==2) {
                                d0=digitalRead(PIN_READER3_D0);
                                d1=digitalRead(PIN_READER3_D1);
                                }

                                //fallende Flanke an d0 = 0 (und d1 muss high sein laut Spec)
                                if (d0==LOW && f0[reader]==HIGH && d1==HIGH) {
                                readerId=reader;
                                resultRaw=bitClear(resultRaw,bitId);
                                bitId++;
                                DATA_timer=millis();
                                f0[reader]=LOW;
                                }
                                if (d0==HIGH && f0[reader]==LOW) {
                                f0[reader]=HIGH;
                                }

                                //fallende Flanke an d1 = 1 (und d0 muss high sein laut Spec)
                                if (d1==LOW && f1[reader]==HIGH && d0==HIGH) {
                                readerId=reader;
                                resultRaw=bitSet(resultRaw,bitId);
                                bitId++;
                                DATA_timer=millis();
                                f1[reader]=LOW;
                                }
                                if (d1==HIGH && f1[reader]==LOW) {
                                f1[reader]=HIGH;
                                }
                                }

                                if (readerId<0) {
                                reader++;
                                if (reader>2) {reader=0;}
                                }

                                if (readerId>=0 && (bitId>26 || ((unsigned long)(millis()-DATA_timer>50)))) {
                                if (bitId==26) {
                                //Parität prüfen (auch invertiert, da manche Leser offenbar pO und pE vertauschen)
                                bool pE=false;
                                bool pO=false;
                                for (byte t=0;t<12;t++) {
                                if (bitRead(resultRaw,t+13)) {pE=!pE;}
                                if (bitRead(resultRaw,t+1)) {pO=!pO;}
                                }
                                if (((bitRead(resultRaw,25)==pE) && (bitRead(resultRaw,0)!=pO)) || ((bitRead(resultRaw,25)!=pE) && (bitRead(resultRaw,0)==pO)))
                                {
                                //Konvertierung in eigenes internes Format
                                resultRaw=bitClear(resultRaw,25); //Parität-Bit löschen
                                resultRaw>>=1; //Parität-Bit löschen
                                resultRaw<<=4; //Platz schaffen für readerId und Flag
                                resultRaw=bitSet(resultRaw,3); //Flag setzen (=gültig)
                                resultRaw|=bitSet(resultRaw,readerId); //readerId
                                return resultRaw;
                                }
                                Serial.print("#E;"+String(readerId+1)+"\n");
                                return -1;

                                } else {
                                Serial.print("#E;"+String(readerId+1)+"\n");
                                return -1;
                                }
                                }
                                } while (true);
                                }

                                unsigned long token_getId(unsigned long token) {
                                return (token>>4);
                                }

                                byte token_getActiveReader(unsigned long token) {
                                if (token&1) {return 1;}
                                if (token&2) {return 2;}
                                if (token&4) {return 3;}
                                return 0;
                                }

                                void sendPing(bool now) {
                                if (now || ((unsigned long)(millis()-PING_timer>(PING_INTERVAL*1000)))) {
                                Serial.print("#P;"+String(PING_INTERVAL)+"\n");
                                PING_timer=millis();
                                }
                                }
                                ###[/INFO]###

