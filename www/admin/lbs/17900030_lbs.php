###[DEF]###
[name        =    PC-Steuerung                        ]

[e#1            =    Betriebszustand                                    ]
[e#2            =    Bildschirm                                        ]
[e#3 TRIGGER    =    Neustart                                        ]
[e#4 OPTION        =    PC: MAC;IP-Adresse                                ]
[e#5 OPTION        =    PC: SSH-Login;Passwort                            ]
[e#6 OPTION        =    EDOMI: NIC-Name                                    ]
[e#7 OPTION        =    Überwachungsintervall (s)            #init=30    ]
[e#8 IMPORTANT    =    Aktiviert                            #init=1        ]

[a#1        =    Betriebszustand                        ]
[a#2        =    Bildschirm                            ]
[a#3        =    Zustandsänderung                    ]
[a#4        =    Status                                ]
###[/DEF]###


###[HELP]###
Dieser Baustein steuert und überwacht einen Linux-PC (getestet mit CentOS 6.5 und 7) über das Netzwerk. Auf dem PC muss lediglich ein SSH-Dienst aktiviert und ggf. die Möglichkeit zu einem Wake-On-LAN (WoL) gegeben sein.

Der Baustein übermittelt die Befehle zur Steuerung und Statusabfrage ausschließlich per SSH. Zum Aufwecken des PCs per WoL wird ein Magic-Paket gesendet.

Der Betriebszustand des PCs kann mittels E1 gesteuert werden:
<ul>
    <li>E1=0: der PC wird in den Ruhezustand versetzt</li>
    <li>E1=1: der PC wird per Wake-On-LAN eingeschaltet</li>
    <li>E1=-1: der PC wird heruntergefahren</li>
    <li>E1=[leer]: der Betriebszustand des PCs wird nicht verändert</li>
</ul>

Mit E2 kann der Bildschirm des PCs gesteuert werden, sofern der PC eingeschaltet ist:
<ul>
    <li>E2=0: der Bildschirm wird ausgeschaltet</li>
    <li>E2=1: der Bildschirm wird eingeschaltet</li>
    <li>E2=[leer]: der Zustand des Bildschirms wird nicht verändert</li>
</ul>

Mit E3=1 wird der PC neu gestartet (reboot), jedoch nur wenn dieser eingeschaltet ist.

Der Zustand des PCs wird zyklisch überwacht: Sofern keine Zustandsänderung mittels E1..E3 angefordert wird, wird der aktuelle Status des PCs mit dem an E7 festgelegten Interval abgefragt. Sobald mittels E1..E3 eine Zustandsänderung angefordert wird, verkürzt sich das Abfrageinterval auf 1 Sekunde. Eine angeforderte Zustandsänderung wird dabei automatisch wiederholt, bis der aktuelle Status der Anforderung entspricht. Sobald der aktuelle Status der Anforderung entspricht, wird das Intervall wieder auf den an E7 festgelegten Wert gesetzt.

<b>Hinweis:</b>
Generell überwacht der Baustein zyklisch den aktuellen Status des PCs. Wurde der PC z.B. heruntergefahren (E1=-1) und fährt anschließend (z.B. nach einem Stromausfall oder manuell gesteuert) wieder hoch, wird der PC erneut heruntergefahren.
Ein Neustart (E3=1) wird jedoch nicht zyklisch überwacht, da es sich nicht um einen definierten Zustand handelt.


<h2>Erforderliche Einstellungen</h2>
An E4 wird die MAC-Adresse und die IP-Adresse des PCs erwartet (getrennt mit einem Semikolon, z.B. "xx:yy:xx:yy:xx:yy;192.168.0.1").

An E5 wird der Benutzername und das Passwort für den SSH-Zugang des PCs erwartet (getrennt mit einem Semikolon, z.B. "root;123456").

An E6 wird die Bezeichnung der Netzwerkschnittelle (NIC) des EDOMI-Servers für die WoL-Funktionalität benötigt (z.B. "eth0" oder "enp1s0"). Wenn E6=[leer] ist, versucht der Baustein den Namen der Standard-Netzwerkschnittstelle automatisch zu ermitteln.

E7 legt das Überwachungsintervall (in Sekunden) für den normalen Betrieb des Bausteins fest: Der Baustein fragt den Status der PCs zyklisch in diesem Intervall ab.

<b>Wichtig:</b>
Änderungen an E4..E7 werden erst nach einem Neustart (E8) des Bausteins (bzw. von EDOMI) übernommen.


<h2>Eingänge</h2>
E1: 1=Einschalten (Wake-On-LAN), 0=Ruhezustand, -1=Herunterfahren (nur möglich wenn PC eingeschaltet ist)
E2: 1=Bildschirm einschalten, 0=Bildschirm ausschalten (nur möglich wenn PC eingeschaltet ist)
E3: 1=PC neustarten (nur möglich wenn PC eingeschaltet ist)
E4..E7: (siehe oben)
E8: Baustein aktivieren/deaktvieren: &ne;0=Baustein aktivieren, 0=Baustein deaktivieren


<h2>Ausgänge</h2>
A1: 1=PC ist eingeschaltet, 0=PC ist im Ruhezustand oder heruntergefahren (oder startet gerade neu)
A2: 1=Bildschirm ist eingeschaltet, 0=Bildschirm ist ausgeschaltet
A3: 1=der gewünschte Zustand stimmt (noch) nicht mit dem tätsächlichen Zustand überein, 0=der gewünschte Zustand wurde erreicht
A4: 1=Baustein ist aktiviert und arbeitet, 0=Baustein ist deaktiviert, -1=Baustein arbeitet aufgrund eines Fehlers nicht

<b>Hinweis:</b>
Die Ausgänge werden nur bei einer Änderung gesetzt (SBC), jedoch auch bei jedem Start des Bausteins (E8) bzw. EDOMI.


<h2>Wichtige Hinweise</h2>
<ul>
    <li>Der Baustein nutzt das Linux-Kommando "expect" zum Einloggen per SSH. "expect" wird bei einer <i>EDOMI-Neuinstalltion</i> ab Version 2.03
        mitinstalliert. "expect" kann ggf. nachträglich installiert werden: <i>yum install expect</i></li>
    <li>Vor der ersten Nutzung dieses Bausteins muss einmalig eine SSH-Session vom EDOMI-Server zum PC aufgebaut werden, um diverse notwendige Schlüssel zu
        generieren: <i>ssh &lt;PC-Login&gt;@&lt;PC-IP-Adresse&gt;</i></li>
    <li>Der PC sollte i.d.R. aussschließlich mit diesem Baustein gesteuert werden, damit der Status stets auf dem neuesten Stand ist. Wird der PC z.B. lokal in
        den Standby versetzt, wird der Baustein dies u.U. erst nach dem an E7 festgelegten Interval bemerken.
    </li>
    <li>Die Überwachung des Zustandes "Eingeschaltet" bzw. "Ausgeschaltet" erfolgt mittls einem "ping". Der PC sollte daher im eingeschalteten Zustand innerhalb
        von 3 Sekunden auf ein "ping" antworten (dies ist bei normaler Konfiguration der Fall).
    </li>
</ul>


<h3>Hilfreiche Linux-Befehle:</h3>
<ul>
    <li>Name der Netzwerkschnittstelle und MAC-Adresse herausfinden: "nmcli" oder "ifconfig -a"</li>
</ul>
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id)
{
    if ($E = logic_getInputs($id)) {
        if (logic_getStateExec($id) == 0 && $E[8]['refresh'] == 1 && $E[8]['value'] != 0) {
            logic_callExec(LBSID, $id);
        } else {
            logic_setInputsQueued($id, $E, true, array(3));
        }
    }
}

?>
###[/LBS]###


###[EXEC]###
<?
require(dirname(__FILE__) . "/../../../../main/include/php/incl_lbsexec.php");
set_time_limit(0);

sql_connect();
$void = new LB_LBSID_remote($id);
sql_disconnect();


class LB_LBSID_remote
{
    private $cmd_interval = 1;
    private $cmd_timer = false;
    private $poll_interval = 1;
    private $poll_idleInterval;
    private $poll_timer = false;

    private $localPower = false;
    private $localPowerPre = false;
    private $localDisplay = false;
    private $localDisplayPre = false;

    private $remotePower = false;
    private $remoteDisplay = false;
    private $remoteLan;
    private $remoteSsh;
    private $localNic;

    private $id;
    private $E;
    private $outSBC = array(false, null, null, null, null);

    public function __construct($id)
    {
        $this->id = $id;

        if ($this->E = logic_getInputs($this->id)) {
            $this->remoteLan = explode(';', $this->E[4]['value'], 2);
            $this->remoteSsh = explode(';', $this->E[5]['value'], 2);
            $this->localNic = ((isEmpty($this->E[6]['value'])) ? $this->getNicName() : $this->E[6]['value']);
            $this->poll_idleInterval = ((intval($this->E[7]['value']) < 1) ? 1 : intval($this->E[7]['value']));

            if (isEmpty($this->remoteLan[0]) || isEmpty($this->remoteSsh[0]) || isEmpty($this->localNic)) {
                $this->out(4, -1);

            } else {
                $this->out(4, 1);
                $this->proc_loop();
                $this->out(4, 0);
            }
        }
    }

    private function proc_loop()
    {
        while (logic_getEdomiState() == 1) {

            if ($this->checkPollTimer()) {
                $this->getRemoteStatus();
                if ($this->remotePower == 1) {
                    $this->out(1, 1, true);
                } else {
                    $this->out(1, 0, true);
                }
                if ($this->remoteDisplay == 1) {
                    $this->out(2, 1, true);
                } else {
                    $this->out(2, 0, true);
                }

                //Soll-Ist-Prüfung: Power
                $tmp1 = false;
                if ($this->localPower !== false && $this->remotePower != $this->localPower) {

                    if ($this->localPower >= 0) {
                        $tmp1 = $this->setRemotePower($this->localPower);

                    } else if ($this->localPower == -1) {
                        if ($this->remotePower == 0) {
                            $mp1 = false;
                        } else {
                            $mp1 = true;
                        }

                    } else if ($this->localPower == -2) {
                        if ($this->remotePower == 0) {
                            $mp1 = false;
                        } else {
                            $tmp1 = $this->setRemotePower($this->localPower);
                        }

                    } else {
                        $tmp1 = true;
                    }
                }

                //Soll-Ist-Prüfung: Display
                $tmp2 = false;
                if ($this->localDisplay !== false && $this->remoteDisplay != $this->localDisplay) {
                    $tmp2 = $this->setRemoteDisplay($this->localDisplay);
                }

                if ($tmp1 || $tmp2) {
                    $this->setPollTimer(true);
                } else {
                    $this->setPollTimer(false);
                }
            }


            if ($this->checkCmdTimer()) {
                if ($this->E = logic_getInputsQueued($this->id, false, true)) {

                    if ($this->E[8]['value'] == 0) {
                        break;
                    }


                    if ($this->E[3]['queue'] == 1 && $this->E[3]['refresh'] == 1 && $this->E[3]['value'] == 1 && $this->remotePower == 1) {
                        $this->localPower = -1;                                                        //Reboot

                    } else {
                        if (isEmpty($this->E[1]['value'])) {
                            $this->localPower = false;

                        } else if ($this->E[1]['value'] == 0) {                                        //PC Aus
                            $this->localPower = 0;

                        } else if ($this->E[1]['value'] == 1) {                                        //PC Ein
                            $this->localPower = 1;

                        } else if ($this->E[1]['value'] == -1 && $this->remotePower == 1) {                //Shutdown (nur wenn PC eingeschaltet ist)
                            $this->localPower = -2;

                        } else {
                            $this->localPower = false;
                        }


                        if (isEmpty($this->E[2]['value'])) {
                            $this->localDisplay = false;

                        } else if ($this->E[2]['value'] == 0) {                                        //Display Aus
                            $this->localDisplay = 0;

                        } else if ($this->E[2]['value'] == 1) {                                        //Display Ein
                            $this->localDisplay = 1;

                        } else {
                            $this->localDisplay = false;
                        }
                    }


                    if ($this->localPower !== false && $this->localPower != $this->remotePower && ($this->localPowerPre === false || $this->localPower != $this->localPowerPre)) {
                        $this->localPowerPre = $this->localPower;
                        if ($this->setRemotePower($this->localPower)) {
                            $this->setPollTimer(true);
                        }
                    }

                    if ($this->localDisplay !== false && $this->localDisplay != $this->remoteDisplay && ($this->localDisplayPre === false || $this->localDisplay != $this->localDisplayPre)) {
                        $this->localDisplayPre = $this->localDisplay;
                        if ($this->setRemoteDisplay($this->localDisplay)) {
                            $this->setPollTimer(true);
                        }
                    }
                }
                $this->setCmdTimer();
            }

            usleep(1000 * 100);
        }
    }

    private function out($id, $value, $sbc = false)
    {
        if (is_null($this->outSBC[$id]) || $this->outSBC[$id] != $value || !$sbc) {
            logic_setOutput($this->id, $id, $value);
        }
        $this->outSBC[$id] = $value;
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

    private function checkPollTimer()
    {
        if ($this->poll_timer === false || (getMicrotime() - $this->poll_timer) >= $this->poll_interval) {
            return true;
        }
        return false;
    }

    private function setPollTimer($mode)
    {
        if ($mode) {
            $this->poll_interval = 1;
            $this->out(3, 1);
        } else {
            $this->poll_interval = $this->poll_idleInterval;
            $this->out(3, 0);
        }
        $this->poll_timer = getMicrotime();
    }

    private function getRemoteStatus()
    {
        $this->remotePower = 0;
        $this->remoteDisplay = 0;

        if ($this->getPowerStatus()) {
            $this->remotePower = 1;
            if ($this->getDisplayStatus()) {
                $this->remoteDisplay = 1;
            }
        }
    }

    private function setRemotePower($mode)
    {
        if ($mode == -2 && $this->remotePower == 1) {
            $this->setPowerStatus(-2);
            return true;

        } else if ($mode == -1 && $this->remotePower == 1) {
            $this->setPowerStatus(-1);
            return true;

        } else if ($mode == 0 && $this->remotePower == 1) {
            $this->setPowerStatus(0);
            return true;

        } else if ($mode == 1 && $this->remotePower == 0) {
            $this->setPowerStatus(1);
            return true;
        }
        return false;
    }

    private function setRemoteDisplay($mode)
    {
        if ($mode == 0 && $this->remotePower == 1) {
            $this->setDisplayStatus(0);
            return true;

        } else if ($mode == 1 && $this->remotePower == 1) {
            $this->setDisplayStatus(1);
            return true;
        }
        return false;
    }

    private function getPowerStatus()
    {
        exec('ping -c 1 -W 3 ' . $this->remoteLan[1], $void, $tmp);
        if ($tmp == 0) {
            return true;
        }
        return false;
    }

    private function setPowerStatus($mode)
    {
        if ($mode == -2) {
            $this->sshSendCommand('shutdown -h now');

        } else if ($mode == -1) {
            $this->sshSendCommand('reboot');

        } else if ($mode == 0) {
            $this->sshSendCommand('systemctl suspend');

        } else if ($mode == 1) {
            shell_exec('/sbin/ether-wake -i ' . $this->localNic . ' ' . $this->remoteLan[0]);
        }
    }

    private function getDisplayStatus()
    {
        $tmp = $this->sshSendCommand('DISPLAY=:0; export DISPLAY; xset -q');
        foreach ($tmp as $n) {
            $n = trim($n);
            $nn = explode(' ', $n, 3);
            if (strToUpper($nn[0]) == 'MONITOR' && strToUpper($nn[2]) == 'ON') {
                return true;
            }
        }
        return false;
    }

    private function setDisplayStatus($mode)
    {
        if ($mode == 0) {
            $this->sshSendCommand('DISPLAY=:0; export DISPLAY; xset dpms force off');
        } else if ($mode == 1) {
            $this->sshSendCommand('DISPLAY=:0; export DISPLAY; xset dpms force on');
        }
    }

    private function sshSendCommand($cmd)
    {
        exec("expect -c 'set timeout 3; spawn timeout 3 ssh " . $this->remoteSsh[0] . "@" . $this->remoteLan[1] . " \"" . $cmd . "\"; expect \"password:\"; send \"" . $this->remoteSsh[1] . "\\r\"; expect eof'", $tmp, $void);
        return $tmp;
    }

    private function getNicName()
    {
        exec("ip -o -4 route show to default", $tmp, $void);
        $n = explode(' ', $tmp[0], 5);
        if (count($n) >= 5) {
            $tmp = trim($n[4]);
            return $tmp;
        }
        return null;
    }
}

?>
###[/EXEC]###
