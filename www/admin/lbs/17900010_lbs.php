###[DEF]###
[name            =    Pool: Steuerung]

[e#1    TRIGGER    =    Betriebsart                        #init=0        ]
[e#2    TRIGGER    =    Automatik: Filtern                #init=0        ]
[e#3            =    Chlorierung                                    ]
[e#4            =    Pegelanpassung                                ]
[e#5    OPTION    =    Ventile: Verfahrzeit (s)        #init=90    ]
[e#6    OPTION    =    Rückspülintervall (h)            #init=50    ]
[e#7    OPTION    =    Rückspüldauer: automatisch (s)    #init=30    ]
[e#8    OPTION    =    Rückspüldauer: saugen/manuell (s) #init=45    ]
[e#9            =    Status: Umwälzpumpe                            ]
[e#10            =    Status: Chlorpumpe                            ]
[e#11            =    Ventilstatus: Jet                            ]
[e#12            =    Ventilstatus: Drain                            ]
[e#13            =    Ventilstatus: Filter                        ]
[e#14            =    Ventilstatus: Backwash                        ]
[e#15    OPTION    =    Logging                            #init=0        ]


[a#1            =    Status                                ]
[a#2            =    Ventilstellung                        ]
[a#3            =    Störung                                ]
[a#4            =    Pegelanpassung                        ]
[a#5            =    Umwälzpumpe                            ]
[a#6            =    Chlorpumpe                            ]
[a#7            =    Ventil: Jet/Drain                    ]
[a#8            =    Ventil: Filter/Backwash                ]
[a#9            =    Zulaufventil                        ]


[v#1            =0                        ]
[v#2 REMANENT    =0                        ]
###[/DEF]###


###[HELP]###
Dieser Baustein steuert und überwacht eine entsprechend verfügbare und angepasste Technik eines Pools.

Vorausgesetzt werden eine Sandfilteranlage und 4 Kugelventile mit Stellantrieben, dabei sind jeweils 2 Ventile elektrisch (oder mechanisch) alternierend miteinander verbunden (Jet+Drain, Filter+Backwash) und z.B. über einen Jalousien-Aktor ansteuerbar.
Die Endschalter der Ventile müssen jeweils eine Rückmeldung (z.B. per Binäreingang) auf die Eingänge E23..E26 geben (1=offen, 0=geschlossen).
Erwartet wird zudem eine (Schlauch)pumpe zur Chlorierung und eine Umwälzpumpe, zum Nachfüllen bei zu niedrigem Wasserstand wird ein Magnetventil (oder ähnliches) erwartet.

Der LBS arbeitet nur, wenn E1&ne;0 ist. E1=0 führt stets dazu, dass sämtliche Maßnahmen beendet werden und die Ventile verfahren in eine "sichere" Position (z.B. "Filtern"), damit der Pool nicht leerlaufen kann (je nach Bauart wäre dies z.B. in der Rückspül-Stellung möglich).


<h2>Betriebsarten</h2>
Der Wert an E1 legt die gewünschte Betriebsart fest, ggf. werden beim Wechsel in einer Betriebsart die Ventile entsprechend verfahren.
<ul>
    <li>
        0: "Aus"
        <ul>
            <li>der Baustein ist deaktiviert und muss ggf. über E1 in die gewünschten Betriebsart versetzt werden</li>
            <li>die Ventile werden in einer sichere Stellung verfahren und alle Pumpen und das Zulaufventil werden abgeschaltet bzw. geschlossen</li>
        </ul>
    </li>

    <li>
        1: "Automatik"
        <ul>
            <li>dies ist die normale Betriebsart, d.h. je nach Wert an E2 wird über den Skimmer gefiltert und ggf. wird der Pegel angepasst oder eine
                Chlorierung durchgeführt
            </li>
            <li>der Baustein wartet in dieser Betriebsart auf entsprechende Ereignisse (E2..E4) und führt die erforderlichen Maßnahmen ggf. durch</li>
            <li>E2 (0/1) sollte i.d.R. mittels einer Zeitschaltuhr regelmäßig getriggert werden um das Filtern zu starten (E2=1) bzw. zu beenden (E2=0)</li>
            <li>E2=0: "Automatik-Standby" (das Filtern ist deaktiviert, der Baustein wartet auf E2=1)
            <li>E2=1: "Automatik-Filtern" (das Filtern ist aktiviert, der Baustein wartet auf E2=0)
            <li>Ventilstellung: "Skimmer&rarr;Filter&rarr;Jet"</li>
        </ul>
    </li>

    <li>
        2: "Saugen"
        <ul>
            <li>in dieser Betriebsart wird der Pool i.d.R. manuell mittels eines Saugers gereinigt</li>
            <li>das angesaugte Wasser wird durch den Sandfilter gepumpt und wieder in den Pool abgegeben (Jet)</li>
            <li>beendet wird das Saugen i.d.R. durch einen Wechsel in die Betriebsart "Automatik" (E1=1) oder in eine beliebige andere Betriebsart</li>
            <li>ggf. wird beim Beenden des Saugens automatisch eine Rückspülung eingeleitet (s.u.)</li>
            <li>Ventilstellung: "Skimmer&rarr;Filter&rarr;Jet" (erwartet wird das Anschließen des Saugers an den Skimmer, d.h. die Ventilstellung entspricht der
                beim Filtern)
            </li>
        </ul>
    </li>

    <li>
        3: "Saugen>Gulli"
        <ul>
            <li>in dieser Betriebsart wird der Pool i.d.R. manuell mittels eines Saugers gereinigt</li>
            <li>das angesaugte Wasser wird am Sandfilter vorbei in den Gulli(!) gepumpt (z.B. um sehr feine Schmutzpartikel aus dem Pool zu entfernen)</li>
            <li>beendet wird das Saugen i.d.R. durch einen Wechsel in die Betriebsart "Automatik" (E1=1) oder in eine beliebige andere Betriebsart</li>
            <li>Ventilstellung: "Sauger&rarr;(Filter)&rarr;Drain" (der Sandfilter wird trotz der Ventilstellung "Filter" umgangen, sofern die Installation
                entsprechend ausgelegt ist)
            </li>
        </ul>
    </li>

    <li>
        4: "Rückspülen"
        <ul>
            <li>in dieser Betriebsart wird der Sandfilter in umkehrter Flussrichtung durchströmt, um diesen zu reinigen</li>
            <li>das angesaugte Wasser wird "rückwärts" durch den Sandfilter in den Gulli gepumpt</li>
            <li>beendet wird das Rückspülen automatisch (E8) oder ggf. vorzeitig durch Wechsel in eine andere Betriebsart</li>
            <li>Ventilstellung: "Sauger&rarr;Backwash&rarr;Drain"</li>
        </ul>
    </li>

    <li>
        5: "Filtern"
        <ul>
            <li>in dieser Betriebsart wird das Filtern unmittelbar und dauerhaft aktiviert (vgl. Betriebsart "Automatik-Filtern")</li>
            <li>beendet wird das Filtern durch Wechsel in eine andere Betriebsart</li>
            <li>beim Beenden des Filterns wird <i>keine</i> automatische Rückspülung eingeleitet</li>
            <li>Ventilstellung: "Skimmer&rarr;Filter&rarr;Jet"</li>
        </ul>
    </li>

    <li>
        "Auffüllen": nur über die Pegelanpassung (E4) aktivierbar (s.u.)
        <ul>
            <li>in dieser Betriebsart werden sämtliche Maßnahmen umittelbar beendet und eine Auffüllung mit Frischwasser eingeleitet (A9=1)</li>
            <li>ein Wechsel in eine andere Betriebsart mittels E1 ist in dieser Betriebsart nicht möglich (mit Ausnahme von "Aus"), da E4 nunmehr die
                Betriebsart festlegt (s.u.)
            </li>
            <li>Ventilstellung: "Skimmer&rarr;Filter&rarr;Jet"</li>
        </ul>
    </li>

    <li>
        "Abpumpen": nur über die Pegelanpassung (E4) aktivierbar (s.u.)
        <ul>
            <li>in dieser Betriebsart werden sämtliche Maßnahmen umittelbar beendet und ein Abpumpen eingeleitet</li>
            <li>ein Wechsel in eine andere Betriebsart mittels E1 ist in dieser Betriebsart nicht möglich (mit Ausnahme von "Aus"), da E4 nunmehr die
                Betriebsart festlegt (s.u.)
            </li>
            <li>Ventilstellung: "Sauger&rarr;(Filter)&rarr;Drain"</li>
        </ul>
    </li>
</ul>


<h3>Rückspülung</h3>
Die Rückspülung des Sandfilters erfolgt ggf. automatisch beim Wechsel in die Betriebsart "Automatik-Standby" (E1=1 und E2=0), sofern die Betriebszeit der Umwälzpumpe seit der letzten Rückspülung den Sollwert (E6) erreicht hat.
Beendet wird die Rückspülung automatisch nach der an E7 angegeben Dauer (auch bei einem manuellen Anstoßen der Rückspülung wird diese stets automatisch beendet, dann allerdings nach der Dauer an E8). Dabei wird stets in die Betriebsart "Automatik" gewechselt ("Standby" oder "Filtern", je nach Status von E2).

Wird nach der Betriebsart "Saugen" (E1=2)
<i>unmittelbar</i> in die Betriebsart "Automatik" gewechselt (E1=1) wird stets automatisch (für die Dauer an E8) rückgespült - unabhängig von E2. Erst nach der automatischen Beendigung des Rückspülens wird je nach E2 in die Betriebsart "Automatik-Standby" oder "Automatik-Filtern" gewechselt.


<h3>Chlorierung</h3>
Eine Chlorierung (Chlorpumpe wird eingeschaltet) erfolgt nur in der Betriebsart "Automatik-Filtern" (E1=1 und E2=1) und bei einer Chlorierungs-Anforderung (E3&ne;0). Eine Chlorierung erfolgt also nur, wenn die Umwälzpumpe läuft.
Wird während der Chlorierung in eine andere Betriebsart als "Automatik-Filtern" gewechselt, läuft die Umwälzpumpe noch ca. 3 Sekunden lang nach. Dies soll verhindern, dass in der Rohrleitung konzentrierte Chlorlösung verbleibt.

E3 kann z.B. mit Hilfe des
<link>LBS 17900011***lbs_17900011</link> getriggert werden.


<h3>Pegelanpassung</h3>
Die Überwachung und Anpassung des Pegels erfolgt in allen Betriebsarten (mit Ausnahme von "Aus").

An E4 wird ggf. die erforderliche Maßnahme erwartet:
<ul>
    <li>E4=0: keine Pegelanpassung erforderlich bzw. laufende Pegelanpassung beenden (Pegel ist im Normalbereich)</li>
    <li>E4=1: der Pegel ist zu niedrig, es wird unmittelbar in die Betriebsart "Auffüllen" gewechselt (s.o.)</li>
    <li>E4=2: der Pegel ist niedrig, das Zulaufventil wird geöffnet (A9=1) um Frischwasser nachzufüllen</li>
    <li>E4=3: der Pegel ist zu hoch, es wird unmittelbar in die Betriebsart "Abpumpen" gewechselt (s.o.)</li>
</ul>

Falls in die Betriebsart "Auffüllen" (E4=1) oder "Abpumpen" (E4=3) gewechselt wurde, wird automatisch in die Betriebsart "Automatik" gewechselt sobald E4=0 bzw. E4=2 wird.

E4 kann z.B. mit Hilfe des
<link>LBS 17900012***lbs_17900012</link> getriggert werden.


<h3>Wichtige Hinweise</h3>
<ul>
    <li>bei jedem Schaltvorgang von Umwälz- bzw. Chlorpumpe wird auf eine Statusrückmeldung an E9 bzw. E10 mit einem Timeout von 5 Sekunden gewartet (bleibt
        diese aus, wird in den Not-Aus-Modus gewechselt, s.u.)
    </li>
    <li>ebenso wird bei jedem Verfahren der Ventile auf eine Statusrückmeldung an E11..E14 mit einem Timeout (E5) gewartet (bleibt diese aus bzw. entspricht der
        Ventilstatus nicht den Erwartungen, wird in den Not-Aus-Modus gewechselt, s.u.)
    </li>
    <li>Die Schaltausgänge für die Pumpen und Ventile sind zur Sicherheit nicht in jeder Situation "SBC"-Ausgänge (nur bei Änderung), d.h. ggf. wird ein Ausgang
        mehrfach z.B. auf 0 gesetzt.
    </li>
    <li>Falls ein interner Fehler auftritt oder z.B. die erwartete Rückmeldung der Ventile ausbleibt, wird stets in einen Not-Aus-Modus gewechselt (d.h. die
        Ventile werden in einer sichere Stellung verfahren und alle Pumpen und das Zulaufventil werden abgeschaltet bzw. geschlossen). Der Baustein wird dann
        deaktiviert und muss über E1 erneut in die gewünschten Betriebsart versetzt werden.
    </li>
    <li>Wenn EDOMI beendet (bzw. neugestartet) wird, erfolgt ebenfalls ein Wechselt in den Not-Aus-Modus. Jedoch wird in diesem Fall nicht auf eine Rückmeldung
        der Ventile etc. gewartet.
    </li>
</ul>

<h2>Eingänge</h2>

E1: Betriebsart (s.o.)
E2: Filtern in der Betriebsart "Automatik": 1=Filtern, 0=nicht Filtern
E3: Chlorierung (s.o.)
E4: Pegelanpassung (s.o.)
E5: Mindest-Verfahrzeit der Kugelventile (in Sekunden)
E6: Intervall für das automatische Rückspülen (Betriebszeit der Umwälzpumpe in Stunden)
E7: Dauer der automatischen Rückspülung (in Sekunden)
E8: Dauer der manuellen Rückspülung bzw. der automatischen Rückspülung nach dem Saugen (in Sekunden)
E9: Status der Umwälzpumpe: 1=Ein, 0=Aus
E10: Status der Chlorpumpe: 1=Ein, 0=Aus
E11..E14: Status der entsprechenden Ventile: 1=Offen, 0=Geschlossen
E15: 1 = Protokollierung aktivieren (im Individual-Log "LBS17900010-&lt;Instanz-ID&gt;")


<h2>Ausgänge</h2>
A1: aktuelle Betriebsart bzw. Status
<ul>
    <li>-1=die Ventile verfahren gerade bzw. die Betriebsart wird gerade gewechselt</li>
    <li>0="aus" bzw. Not-Aus</li>
    <li>1="Automatik-Standby"</li>
    <li>2="Automatik-Filtern"</li>
    <li>3="Rückspülung" (automatisch oder manuell)</li>
    <li>4="Saugen"</li>
    <li>5="Saugen>Gulli"</li>
    <li>6="Abpumpen" (Pegelanpassung)</li>
    <li>7="Auffüllen" (Pegelanpassung)</li>
    <li>8="Filtern" (manuell)</li>
</ul>

A2: aktuelle Ventilstellung (ggf. verzögert)
<ul>
    <li>0=unbekannt bzw. Ventile verfahren gerade</li>
    <li>1=Automatik-Filtern, Automatik-Standby, Saugen, Filtern (manuell)</li>
    <li>2=Saugen>Gulli, Abpumpen</li>
    <li>3=Rückspülen</li>
</ul>

A3: bei einer Störung (z.B. Timeout beim Verfahren der Ventile) wird A3 auf 1 gesetzt, wenn die Störung behoben wird (z.B. durch erneutes Verfahren) wird A3 auf 0 gesetzt

A4: ggf. Status der Pegelanpassung
<ul>
    <li>0=keine Pegelanpassung bzw. laufende Pegelanpassung wurde beendet</li>
    <li>1=Betriebsart "Auffüllen" aktiv</li>
    <li>2=Nachfüllung aktiv (das Zulaufventil ist geöffnet)</li>
    <li>3=Betriebsart "Abpumpen" aktiv</li>
</ul>

A5: Steuerung der Umwälzpumpe: 1=Ein, 0=Aus
A6: Steuerung der Chlorpumpe: 1=Ein, 0=Aus
A7: Steuerung der Ventile "Jet" und "Drain": 0=Jet öffnen (und Drain schließen), 1=Drain öffnen (und Jet schließen)
A8: Steuerung der Ventile "Filter" und "Backwash": 0=Filter öffnen (und Backwash schließen), 1=Backwash öffnen (und Filter schließen)
A9: Steuerung des Zulaufventils: 1=Öffnen, 0=Schließen
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id)
{
    if (($E = logic_getInputs($id)) && ($V = logic_getVars($id))) {
        //EXEC ggf. starten
        if (logic_getStateExec($id) == 0) {
            if ($E[1]['refresh'] == 1 && $E[1]['value'] > 0) {
                logic_setInputsQueued($id, array(1 => $E[1], 2 => $E[2]));
                logic_callExec(LBSID, $id);
            }

        } else {
            //Aus/Notaus?
            if ($E[1]['refresh'] == 1 && $E[1]['value'] == 0) {
                logic_setVar($id, 1, 1);    //im EXEC: Schleifen ggf. Abbrechen
            }
            if ($E[1]['refresh'] == 1 || $E[2]['refresh'] == 1) {
                logic_setInputsQueued($id, array(1 => $E[1], 2 => $E[2]));
            }
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
$void = new LB_LBSID_pool($id);
sql_disconnect();


/*
###
beim ersten Start nach Neustart:
- A4,5,6,7 berechenen und ausgeben! Sonst sind die leer, bis gepumpt wird oder sonstwas passiert
=> eine VAR nehmen, die quasi Neustart bestimmt (per init =0 und dann 1 oder so)

###
- A6: anders? 0=aus, 1=nachfüllen, 2=nachfüllen kritisch, 3=abpumpen


######################################
######################################
######################################
Probleme:
- Neustart während Pegelanpassung (nachfüllen): nix passiert, erst nachdem PEGEL sich geändert hat... (Pegelstand triggert...)
	=> doof...

- nach Pegel-kritisch-Nachfüllen kommt Nachfüllen (unkritisch), aber A1 war nicht 1 (sondern irgendwas...)
	=> prüfen!
######################################
######################################
######################################

*/


class LB_LBSID_pool
{
    private $id, $E, $V;

    private $outSBC = array(false, null, null, null, null, null, null, null, null, null);    //SBC-Puffer für A1..A9
    private $outCaptions = array(false, 'Status', 'Ventilstellung', 'Störung', 'Pegelanpassung', 'Pumpe', 'Chlorpumpe', 'Ventil Jet/Drain', 'Ventil Filter/Backwash', 'Zulaufventil');    //Logging

    private $request = -1;        //geparste Anforderung vom LBS
    private $requestCaptions = array('AUS(0)', 'AUTOMATIK-STANDBY(1)', 'AUTOMATIK-FILTERN(2)', 'RÜCKSPÜLEN(3)', 'SAUGEN(4)', 'SAUGEN>GULLI(5)', '', '', 'MANUELL-FILTERN(8)');    //Logging
    /*
        0=Aus
        1=Auto/Idle
        2=Auto/Filtern
        3=Backwash
        4=Saugen
        5=Saugen>Gulli
        6/7 gibt es hier nicht!
        8=Manuell Filtern
    */

    private $workMode = 0;        //tatsächlicher Betriebsmodus
    private $workModeCaptions = array('AUS(0)', 'AUTOMATIK-STANDBY(1)', 'AUTOMATIK-FILTERN(2)', 'RÜCKSPÜLEN(3)', 'SAUGEN(4)', 'SAUGEN>GULLI(5)', 'PEGEL>ABPUMPEN(6)', 'PEGEL>NACHFÜLLEN(7)', 'MANUELL-FILTERN(8)');    //Logging
    /*
        -1=Ventile verfahren gerade oder unbekannte Ventilstellung
        0=aus/Exit/Notaus
        1=Automatik: Idle
        2=Automatik: Filtern
        3=Automatik/Manuell: Rückspülung
        4=Manuell: Saugen
        5=Manuell: Saugen>Gulli
        6=Pegelanpassung (Abpumpen)
        7=Pegelanpassung (kritisch Nachfüllen)
        8=Manuell Filtern
    */

    private $valveCaptions = array('0:unbekannt', '1:Filter>Jet', '2:(Filter)>Gulli', '3:Rückspülung>Gulli');    //Logging

    private $levelMode = 0;        //aktueller Status der Pegelanpassung: -1=Nachfüllen im laufenden Betrieb, 0=aus, 1=es wird kritisch nachgefüllt, 2=es wird kritisch abgepumpt
    private $chlorMode = false;
    private $pump_ts = -1;
    private $backwash_ts = -1;
    private $backwash_timeout = 0;


    public function __construct($id)
    {
        $this->id = $id;
        $this->proc_run();
    }

    private function proc_run()
    {
        while (logic_getEdomiState() == 1 && $this->getV()) {

            //LBS-Request parsen
            if ($E = logic_getInputsQueued($this->id)) {
                logic_deleteInputsQueued($this->id);    //alle LBS-Requests löschen, die während der Abarbeitung (z.B. Ventilstellung anfahren) aufgelaufen sind

                $this->setV(1, 0);    //Schleifenabbruch zurücksetzen
                if ($E[1]['value'] == 0) {
                    $this->request = 0;

                } else {
                    if ($E[1]['refresh'] == 1) {
                        if ($E[1]['value'] == 1) {
                            if ($E[2]['value'] == 1) {
                                $this->request = 2;
                            } else {
                                $this->request = 1;
                            }

                        } else if ($E[1]['value'] == 2) {
                            $this->request = 4;

                        } else if ($E[1]['value'] == 3) {
                            $this->request = 5;

                        } else if ($E[1]['value'] == 4) {
                            $this->request = 3;

                        } else if ($E[1]['value'] == 5) {
                            $this->request = 8;
                        }

                    } else if ($E[2]['refresh'] == 1 && ($E[1]['value'] == 1 || $this->workMode == 1 || $this->workMode == 2)) {
                        if ($E[2]['value'] == 1) {
                            $this->request = 2;
                        } else {
                            $this->request = 1;
                        }
                    }
                }
            }

            //LBS-Request ausführen
            if ($this->request >= 0 && $this->getE()) {
                $this->log('Betriebsart-Anforderung: ' . $this->requestCaptions[$this->request], 'REQUEST');

                if ($this->request > 0) {
                    if ($this->levelMode <= 0) {

                        if ($this->request == 1) {

                            if ($this->workMode == 4 && $this->E[8]['value'] > 0) {
                                $this->log('Rückspülen (nach dem Saugen)');
                                if ($this->setWorkMode(3)) {
                                    $this->pump_ts = -1;
                                    $this->setV(2, 0);
                                    $this->backwash_ts = getMicrotime();
                                    $this->backwash_timeout = $this->E[8]['value'];
                                }

                            } else {
                                if ($this->E[6]['value'] > 0 && $this->E[7]['value'] > 0 && $this->V[2] >= ($this->E[6]['value'] * 3600)) {
                                    $this->log('Rückspülen (zyklisch)');
                                    if ($this->setWorkMode(3)) {
                                        $this->pump_ts = -1;
                                        $this->setV(2, 0);
                                        $this->backwash_ts = getMicrotime();
                                        $this->backwash_timeout = $this->E[7]['value'];
                                    }

                                } else {
                                    $this->setWorkMode(1);
                                }
                            }

                        } else if ($this->request == 2) {
                            if ($this->workMode == 4 && $this->E[8]['value'] > 0) {
                                $this->log('Rückspülen (nach dem Saugen)');
                                if ($this->setWorkMode(3)) {
                                    $this->pump_ts = -1;
                                    $this->setV(2, 0);
                                    $this->backwash_ts = getMicrotime();
                                    $this->backwash_timeout = $this->E[8]['value'];
                                }

                            } else {
                                $this->setWorkMode(2);
                            }

                        } else if ($this->request == 3) {
                            if ($this->setWorkMode(3)) {
                                $this->pump_ts = -1;
                                $this->setV(2, 0);
                                $this->backwash_ts = getMicrotime();
                                $this->backwash_timeout = $this->E[8]['value'];
                            }

                        } else if ($this->request == 4) {
                            $this->setWorkMode(4);

                        } else if ($this->request == 5) {
                            $this->setWorkMode(5);

                        } else if ($this->request == 8) {
                            $this->setWorkMode(8);

                        }
                    } else {
                        $this->log('Betriebsart-Anforderung ' . $this->requestCaptions[$this->request] . ' ignoriert (Pegelanpassung ist aktiv)');
                    }

                } else if ($this->request == 0) {
                    $this->quit();
                }

                $this->request = -1;    //Request zurücksetzen
            }

            //zyklische Verarbeitung
            if ($this->getE()) {
                //Pegelcheck
                $this->checkLevel();

                //Chlorierung
                $this->setChlor();


                //Betriebszeit der Pumpe seit letzter Rückspülung
                if (($this->workMode == 2 || $this->workMode == 4 || $this->workMode == 8) && $this->E[9]['value'] == 1) {

                    if ($this->pump_ts >= 0) {
                        $this->setV(2, $this->V[2] + (getMicrotime() - $this->pump_ts));
                    }

                    $this->pump_ts = getMicrotime();    //Zeitpunkt der letzten Berechnung merken

                } else {
                    $this->pump_ts = -1;
                }

                //Rückspülung: Beenden nach Ablauf des Timers
                if ($this->workMode == 3 && $this->E[9]['value'] == 1 && $this->backwash_timeout > 0 && $this->levelMode <= 0) {
                    if ($this->backwash_ts >= 0 && getMicrotime() >= ($this->backwash_ts + $this->backwash_timeout)) {
                        $this->log('Rückspülung automatisch beendet');
                        $this->backwash_timeout = 0;
                        $this->setWorkModeAuto();
                    }
                }
            }

            $this->out(1, $this->workMode, true);    //aktueller workMode
            sleep(1);
        }

        if (logic_getEdomiState() == 1) {
            $this->out(3, 1, true);    //Störung!
        }
        $this->quit();
    }


    private function log($msg, $level = '')
    {
        if ($this->E[15]['value'] == 1) {
            writeToCustomLog('LBS17900010-' . $this->id, $level, $msg);
        }
    }

    private function getE()
    {
        if ($this->E = logic_getInputs($this->id)) {
            return true;
        }
        return false;
    }

    private function getV()
    {
        if ($this->V = logic_getVars($this->id)) {
            return true;
        }
        return false;
    }

    private function setV($id, $value)
    {
        $this->V[$id] = $value;
        logic_setVar($this->id, $id, $value);
    }

    private function out($id, $value, $sbc = false)
    {
        if (is_null($this->outSBC[$id]) || $this->outSBC[$id] != $value || !$sbc) {
            $this->log($this->outCaptions[$id] . ' -> ' . $value . ' ' . (($sbc) ? '(SBC)' : ''), 'A' . $id);
            logic_setOutput($this->id, $id, $value);
        }
        $this->outSBC[$id] = $value;
    }

    private function setWorkModeAuto()
    {
        if ($this->E[2]['value'] == 1) {
            $this->setWorkMode(2);
        } else {
            $this->setWorkMode(1);
        }
    }

    private function setWorkMode($mode)
    {
        $position = 0;
        if ($mode == 1) {
            $position = 1;
            $pump = false;
        }
        if ($mode == 2) {
            $position = 1;
            $pump = true;
        }
        if ($mode == 3) {
            $position = 3;
            $pump = true;
        }
        if ($mode == 4) {
            $position = 1;
            $pump = true;
        }
        if ($mode == 5) {
            $position = 2;
            $pump = true;
        }
        if ($mode == 6) {
            $position = 2;
            $pump = true;
        }
        if ($mode == 7) {
            $position = 1;
            $pump = false;
        }
        if ($mode == 8) {
            $position = 1;
            $pump = true;
        }

        if ($position > 0) {
            $this->log('Ventilstellung ' . $this->valveCaptions[$position] . ' für workMode ' . $this->workModeCaptions[$mode] . ' anfahren');
            $this->workMode = -1;
            $this->out(1, -1, true);    //Status: Ventile werden verfahren

            if ($this->getValves() == $position) {
                //Ventilstellung passt bereits
                $this->log($this->workModeCaptions[$mode] . ' aktiv - Ventilstellung ist bereits ' . $this->valveCaptions[$position] . ' (Pumpe: ' . (($pump) ? 'EIN' : 'AUS') . ')', 'AKTIV--->');
                $this->workMode = $mode;
                $this->out(3, 0, true);    //keine Störung

                if ($pump) {
                    $this->setPump(1);
                } else {
                    $this->setPump(0);
                }

                return true;

            } else {

                //Ventilstellung anfahren
                $this->setPump(0);

                $newPosition = $this->setValves($position);
                if ($newPosition == $position) {
                    $this->log($this->workModeCaptions[$mode] . ' aktiv - Ventilstellung ' . $this->valveCaptions[$newPosition] . ' wurde angefahren (Pumpe: ' . (($pump) ? 'EIN' : 'AUS') . ')', 'AKTIV--->');
                    $this->workMode = $mode;
                    $this->out(3, 0, true);    //keine Störung
                    if ($pump) {
                        $this->setPump(1);
                    }
                    return true;

                } else if ($newPosition == 0) {
                    $this->log('Ventilstellung ' . $this->valveCaptions[$position] . ' konnte nicht angefahren werden (Timeout)! => Not-Aus...', 'FEHLER');
                    $this->out(3, 1, true);    //Störung!
                    $this->quit();    //Notaus!

                } else {
                    //Abbruch durch Aus-Request
                    $this->quit();    //Aus
                }
            }
        }
    }

    private function setValves($position)
    {
        $this->log('Ventilstellung anfahren: ' . $this->valveCaptions[$position]);

        //Ventile anfahren
        if ($position == 1) {
            //Filtern/Standby/Saugen
            $this->out(7, 0);
            $this->out(8, 0);
        } else if ($position == 2) {
            //Saugen>Gulli/Abpumpen
            $this->out(7, 1);
            $this->out(8, 0);
        } else if ($position == 3) {
            //Backwash
            $this->out(7, 1);
            $this->out(8, 1);
        }

        $t = getMicrotime();
        while (logic_getEdomiState() == 1 && (getMicrotime() - $t) < $this->E[5]['value']) {
            sleep(1);

            if ($this->getValves() == $position) {
                return $position;
            }

            $this->V[1] = logic_getVar($this->id, 1);
            if ($this->V[1] == 1) {
                //Notaus angefordert
                $this->log('Anfahren der Ventilstellung durch Notaus-Anforderung abgebrochen (Ventile verfahren ggf. weiter)');
                return -1;
            }
        }
        return 0;
    }

    private function getValves()
    {
        $tmp = 0;
        if ($this->getE()) {
            if ($this->E[11]['value'] == 1 && $this->E[12]['value'] == 0 && $this->E[13]['value'] == 1 && $this->E[14]['value'] == 0) {
                $tmp = 1;
            }    //Filtern/Standby/Saugen
            if ($this->E[11]['value'] == 0 && $this->E[12]['value'] == 1 && $this->E[13]['value'] == 1 && $this->E[14]['value'] == 0) {
                $tmp = 2;
            }    //Saugen>Gulli/Abpumpen
            if ($this->E[11]['value'] == 0 && $this->E[12]['value'] == 1 && $this->E[13]['value'] == 0 && $this->E[14]['value'] == 1) {
                $tmp = 3;
            }    //Backwash
        }
        $this->out(2, $tmp, true);    //Ventilstellung
        return $tmp;
    }

    private function setPump($mode)
    {
        $this->log('Pumpe ' . (($mode == 1) ? 'einschalten' : 'ausschalten') . ' (mit Rückmeldung)');

        //wenn Chlorpumpe ausgeschaltet wurde und Pumpe noch läuft: Pumpe 3s nachlaufen lassen (um Rohr freizuspülen)
        $preState = 0;
        if ($this->getE() && $this->E[10]['value'] == 1) {
            $preState = 1;
        }
        if ($this->setChlorpump(0) === true) {
            if ($mode == 0 && $preState == 1) {
                if ($this->E[9]['value'] == 1) {
                    $this->log('Chlorpumpe war eingeschaltet: Pumpe 3s nachlaufen lassen...');
                    $t = getMicrotime();
//### 3s fix?
                    while (logic_getEdomiState() == 1 && (getMicrotime() - $t) < 3) {
                        sleep(1);
                        $this->V[1] = logic_getVar($this->id, 1);
                        if ($this->V[1] == 1) {
                            //Notaus angefordert
                            $this->log('Nachlaufen der Pumpe durch Notaus-Anforderung abgebrochen');
                            return -1;
                        }
                    }
                }
            }

        } else {
            //Notaus angefordert (während die Chlorpumpe ausgeschaltet wurde)
            $this->log('Schalten der Pumpe durch Notaus-Anforderung abgebrochen');
            return -1;
        }


        $this->out(5, $mode);    //Pumpe ein/aus

        $t = getMicrotime();
        while (logic_getEdomiState() == 1 && (getMicrotime() - $t) < 10) {
            sleep(1);

            if ($this->getE()) {
                if (($mode == 1 && $this->E[9]['value'] == 1) || ($mode == 0 && $this->E[9]['value'] != 1)) {
                    return true;
                }
            }

            $this->V[1] = logic_getVar($this->id, 1);
            if ($this->V[1] == 1) {
                //Notaus angefordert
                $this->log('Schalten der Pumpe durch Notaus-Anforderung abgebrochen');
                return -1;
            }
        }

        $this->log('Schalten der Pumpe gescheitert (Timeout) => Notaus', 'FEHLER');
        $this->out(3, 1, true);    //Störung!
        $this->quit();
    }

    private function setChlorpump($mode)
    {
        $this->log('Chlorpumpe ' . (($mode == 1) ? 'einschalten' : 'ausschalten') . ' (mit Rückmeldung)');

        $this->chlorMode = (($mode == 1) ? true : false);

        $this->out(6, $mode);    //Chlorpumpe ein/aus

        $t = getMicrotime();
        while (logic_getEdomiState() == 1 && (getMicrotime() - $t) < 10) {
            sleep(1);

            if ($this->getE()) {
                if (($mode == 1 && $this->E[10]['value'] == 1) || ($mode == 0 && $this->E[10]['value'] != 1)) {
                    return true;
                }
            }

            $this->V[1] = logic_getVar($this->id, 1);
            if ($this->V[1] == 1) {
                //Notaus angefordert
                $this->log('Schalten der Chlorpumpe durch Notaus-Anforderung abgebrochen');
                return -1;
            }
        }

        $this->log('Schalten der Chlorpumpe gescheitert (Timeout) => Notaus', 'FEHLER');
        $this->out(3, 1, true);    //Störung!
        $this->quit();
    }

    private function setInletValve($mode)
    {
        $this->log('Zulaufventil ' . (($mode == 1) ? 'einschalten' : 'ausschalten'));

        $this->out(9, $mode);    //Zulaufventil ein/aus (ohne Rückmeldung)

        return true;

        /*
        ### mit Rückmeldung (E28=Platzhalter für Ventilstatus)
                $t=getMicrotime();
                while (logic_getEdomiState()==1 && (getMicrotime()-$t)<10) {
                    sleep(1);

                    if ($this->getE()) {
                        if (($mode==1 && $this->E[28]['value']==1) || ($mode==0 && $this->E[28]['value']!=1)) {
                            return true;
                        }
                    }

                    $this->V[1]=logic_getVar($this->id,1);
                    if ($this->V[1]==1) {
                        //Notaus angefordert
                        $this->log('Schalten des Zulaufventils durch Notaus-Anforderung abgebrochen');
                        return -1;
                    }
                }

                $this->log('Schalten des Zulaufventils gescheitert (Timeout) => Notaus','FEHLER');
                $this->out(3,1,true);	//Störung!
                $this->quit();
        */
    }

    private function checkLevel()
    {
        if ($this->E[4]['value'] == 3) {
            if ($this->levelMode != 2) {
                $this->log('Pegel zu hoch => abpumpen', 'PEGEL');
                $this->levelMode = 2;        //Pegelmodus: Abpumpen
                $this->setInletValve(0);    //Zulaufventil: Aus
                $this->setWorkMode(6);        //Abpumpen
                $this->out(4, 3, true);
            }

        } else if ($this->E[4]['value'] == 1) {
            if ($this->levelMode != 1) {
                $this->log('Pegel zu niedrig => Standby und nachfüllen', 'PEGEL');
                $this->levelMode = 1;        //Pegelmodus: Nachfüllen (kritisch)
                $this->setWorkMode(7);        //kritisch Nachfüllen
                $this->setInletValve(1);    //Zulaufventil: Ein
                $this->out(4, 1, true);
            }

        } else if ($this->E[4]['value'] == 2) {
            if ($this->levelMode != -1) {
                $this->log('Pegel niedrig => nachfüllen', 'PEGEL');

                if ($this->levelMode > 0) {
//###					$this->setWorkMode(1);		//Auto-Standby
                    $this->setWorkModeAuto();
                }

                $this->levelMode = -1;        //Pegelmodus: Nachfüllen
                $this->setInletValve(1);    //Zulaufventil: Ein
                $this->out(4, 2, true);
            }

        } else if ($this->E[4]['value'] == 0) {
            if ($this->levelMode != 0) {
                $this->log('Pegel normalisiert', 'PEGEL');

                $this->setInletValve(0);    //Zulaufventil: Aus

                if ($this->levelMode > 0) {
                    $this->setWorkModeAuto();
                }

                $this->levelMode = 0;        //Pegelmodus: Aus
            }
            $this->out(4, 0, true);
        }
    }

    private function setChlor()
    {
        if ($this->E[3]['value'] != 0) {
            if (($this->workMode == 2 || $this->workMode == 8) && $this->E[9]['value'] == 1 && !$this->chlorMode) {
                $this->setChlorpump(1);
            }
        } else if ($this->chlorMode) {
            $this->setChlorpump(0);
        }
    }

    private function quit()
    {
        //Aus, Not-Aus oder EDOMI wurde beendet
        $this->log('Aus/Not-Aus', 'QUIT');

        $this->out(6, 0);        //Chlorpumpe aus
        $this->out(5, 0);        //Pumpe aus
        $this->out(9, 0);        //Zulaufventil aus
        $this->out(4, 0, true);    //Pegelanpassung ist aus

        $this->workMode = 0;
        $this->setV(1, 0);        //Schleifenabbruch zurücksetzen

        //Ventile ggf. in Filterstellung bringen
        $tmp = $this->getValves();
        if ($tmp != 1) {
            $this->log('Ventilstellung ' . $this->valveCaptions[1] . ' für workMode ' . $this->workModeCaptions[1] . ' (Sicherheitsstellung) anfahren');
            $tmp = $this->setValves(1);

            if ($tmp == 1) {
                $this->log('Ventilstellung ' . $this->valveCaptions[1] . ' angefahren (Sicherheitsstellung)', 'OK');

            } else if ($tmp == 0) {
                $this->log('Ventilstellung ' . $this->valveCaptions[1] . ' (Sicherheitsstellung) konnte u.U. nicht angefahren werden (Timeout bzw. EDOMI wurde beendet)! Unbekannte Ventilstellung!', 'FEHLER');
                $this->workMode = -1;
                $this->out(3, 1, true);    //Störung!
            }
        }

        $this->out(1, 0, true);    //Status: Aus

        sql_disconnect();
        exit();
    }

}

?>
###[/EXEC]###
