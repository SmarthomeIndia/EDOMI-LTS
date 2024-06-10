###[DEF]###
[name			= 	Denon Ceol N9]

[e#1 IMPORTANT	= Aktiviert	#init=1	]
[e#2 OPTION		= IP-Adresse		]
[e#3 TRIGGER	= Ein/Aus			]
[e#4 TRIGGER	= Quelle			]
[e#5 TRIGGER	= Lautstärke 		]
[e#6 TRIGGER	= Stumm	 			]
[e#7 TRIGGER	= Favorit	 		]
[e#8 TRIGGER	= Befehl	 		]
[e#9 OPTION		= Trenner	#init=0 ]

[a#1		= Status			]
[a#2		= Info				]
[a#3		= Ein/Aus			]
[a#4		= Quelle			]
[a#5		= Lautstärke		]
[a#6		= Stumm				]
[a#7		= Favorit			]
[a#8		= Befehl			]
###[/DEF]###


###[HELP]###
<b>ACHTUNG: Diese Hilfe ist noch unvollständig!</b>

Dieser Baustein ermöglich die Steuerung eines DENON CEOL N9 (ggf. auch ähnliche Typen).

Sofern der Baustein aktiviert ist (E1&ne;0), wird der Status des Geräts zyklisch abgefragt (z.B. Betriebszustand, Lautstärke, etc.).

Über die Eingänge E3..E8 können Paramter des Geräts festgelegt werden (Befehle). Die Befehle werden mit einer Rate von max. 2/s ausgeführt, um das Gerät nicht zu überlasten.

E1: &ne;0=Baustein ist aktiviert, 0=Baustein ist deaktiviert
E2: IP-Adresse des Geräts (in der Form 111.222.333.444), ohne diese Angabe startet der Baustein nicht
E3: 1=Gerät einschalten, 0=Gerät ausschalten
E4: Eingangsquelle des Geräts festlegen: 1=Internet Radio, 2=Analog-In, 3=Radio, 4=USB, 5=CD, 6=Digital-In1, 7=Digital-In2, 8=Server (z.B. Airplay, etc.), 9=Bluetooth
E5: 0..60=Lautstärke des Geräts festlegen
E6: 1=Stumm schalten, 0=Stummschaltung aufheben
E7: 1..50=Favorit abrufen
E8: eigenen Befehl absetzen (aus der Denon-API: /goform/formiPhoneAppDirect.xml?&lt;BEFEHL&gt;)
E9: Zeilen-Trennzeichen für A2: 0="&lt;br&gt;", 1=";"

A1: letzter Status des Bausteins: 0=deaktiviert, 1=aktiviert, 2=Abfrage-Fehler, 3=Fehler beim Absetzen eines Befehls (A1 wird z.B. nach einem Fehler nicht zurückgesetzt, der letzte Zustand bleibt also stets erhalten)
A2 (SBC): Informationen über den aktuellen Zustands des Geräts: je nach Zustand entspricht dies z.B. dem Display-Inhalt, einzelne Zeilen werden je nach E9 mit "&lt;br&gt;" oder ";" separiert (z.B. "Zeile1;Zeile2;...")
A3 (SBC): aktueller Betriebszustand des Geräts (1=Ein, 0=Aus)
A4 (SBC): aktuelle Eingangsquelle des Geräts: 1=Internet Radio, 2=Analog-In, 3=Radio, 4=USB, 5=CD, 6=Digital-In1, 7=Digital-In2, 8=Server (z.B. Airplay, etc.), 9=Bluetooth
A5 (SBC): aktuelle Lautstärke des Geräts: das Abfragen der aktuellen Lautstärke funktioniert leider nicht zuverlässig, daher wird A5 stets beim Setzen der Lautstärke (E5) auf den Wert von E5 gesetzt (und ggf. bei erfolgreichem Polling erneut gesetzt)
A6 (SBC): aktueller Stummschaltung-Zustand (1=Stumm, 0=Aus)
A7 (SBC): aktuell gewählter Favorit: das Abfragen des aktuellen Favoriten ist nicht möglich, daher wird A7 stets auf den Wert von E7 gesetzt
A8: bei einem erfolgreich abgesetzten Befehl (E8) wird dieser Ausgangs auf den Wert von E8 gesetzt (der Befehl wird jedoch nicht auf seine Gültigkeit geprüft)

Hinweis:
SBC wird bei jedem Start des Bausteins (E1) zurückgesetzt.
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id) {
	if ($E=logic_getInputs($id)) {
		if (logic_getStateExec($id)==0) {
			if ($E[1]['refresh']==1 && $E[1]['value']!=0 && (!isEmpty($E[2]['value']))) {
				logic_setInputsQueued($id,$E);	
				logic_callExec(LBSID,$id);
			}
		} else {
			logic_setInputsQueued($id,$E);	
		}
	}
}
?>
###[/LBS]###


###[EXEC]###
<?
require(dirname(__FILE__)."/../../../../main/include/php/incl_lbsexec.php");
set_time_limit(0);

restore_error_handler();
error_reporting(0);

sql_connect();

//------------------------------------------------------------------------------------------------------------
$cmdInterval=0.5;					//Befehlsintervall in Sekunden
$pollIntervals=array(1,3,60);		//Abfrage-Intervalle: nach einem Befehl, wenn System=Ein, wenn System=Aus (Idle) 
$pollInterval0_timeout=10;			//nach einem Befehl bleibt das Abfrage-Intervall[0] für diese Zeitspanne erhalten

$const_sources=array('NET','IRADIO','ANALOGIN','TUNER','USB','CD','DIGITALIN1','DIGITALIN2','SERVER','BLUETOOTH');
//------------------------------------------------------------------------------------------------------------

$pollTimerOld=0;
$cmdTimerOld=0;

$outSBC=array(false,null,null,null,null,null,null,null,null);

if ($E=logic_getInputs($id)) {$serverIp=$E[2]['value'];} else {$serverIp='';}

logic_setOutput($id,1,1);

while (logic_getEdomiState()==1 && (!isEmpty($serverIp))) {

	$doPoll=false;
	$doCmd=false;

	if ($pollInterval0_timer!==false) {
		if ((getMicrotime()-$pollInterval0_timer)>=$pollInterval0_timeout) {$pollInterval0_timer=false;}
		$tmp=0;
	} else {
		$tmp=$pollMode;
	}
	if ((getMicrotime()-$pollTimerOld)>=$pollIntervals[$tmp]) {
		$pollTimerOld=getMicrotime();
		$doPoll=true;
	}
	if ((getMicrotime()-$cmdTimerOld)>=$cmdInterval) {
		$cmdTimerOld=getMicrotime();
		$doCmd=true;
	}
	
	if ($doPoll || $doCmd) {

		//Status-Polling
		if ($doPoll) {
			$xml=LB_LBSID_getStatus($serverIp,'goform/formMainZone_MainZoneXmlStatusLite.xml');
			if ($xml!==false) {
				$xml2=LB_LBSID_getStatus($serverIp,'goform/formNetAudio_StatusXml.xml');
			
				//Info
				if ($xml2!==false) {
					$tmp='';
					foreach ($xml2->{'szLine'}->{'value'} as $value) {
						$tmp.=str_replace(';','',$value).(($E[9]['value']==1)?';':'<br>');
					}
					if (strval($outSBC[2])!=strval($tmp)) {
						logic_setOutput($id,2,$tmp);
						$outSBC[2]=$tmp;
					}
				}
				
				$tmp=(($xml->{'Power'}->{'value'}=='ON')?1:0);
				if (strval($outSBC[3])!=strval($tmp)) {
					logic_setOutput($id,3,$tmp);
					$outSBC[3]=$tmp;
				}
				if ($tmp==1) {
					$pollMode=1;
				} else {
					$pollMode=2;
				}
	
				$value=$xml->{'InputFuncSelect'}->{'value'};
				$tmp=array_search($value,$const_sources);
				if ($tmp!==false) {
					if ($tmp>=1) {
						if (strval($outSBC[4])!=strval($tmp)) {
							logic_setOutput($id,4,$tmp);
							$outSBC[4]=$tmp;
						}
					} else if ($tmp==0 && $xml2!==false) {
					
						//Quelle ist NET -> Details ermitteln
						$value=$xml2->{'NetFuncSelect'}->{'value'};
						$tmp=array_search($value,$const_sources);
						if ($tmp!==false) {
							if ($tmp>=1) {
								if (strval($outSBC[4])!=strval($tmp)) {
									logic_setOutput($id,4,$tmp);
									$outSBC[4]=$tmp;
								}
							}
						}
					}
				}
	
				$value=$xml->{'MasterVolume'}->{'value'};
				if (!isEmpty($value) && $value!='--') {$tmp=floatval($value)+80;} else {$tmp=0;}
				if (strval($outSBC[5])!=strval($tmp)) {
					logic_setOutput($id,5,$tmp);
					$outSBC[5]=$tmp;
				}
	
				$tmp=(($xml->{'Mute'}->{'value'}=='on')?1:0);
				if (strval($outSBC[6])!=strval($tmp)) {
					logic_setOutput($id,6,$tmp);
					$outSBC[6]=$tmp;
				}
	
			} else {
				logic_setOutput($id,1,2);
				$pollMode=2;
				
				//Polling-Problem: Als Zustand wird "Aus" angenommen...
				logic_setOutput($id,3,0);
				$outSBC[3]=0;
			}
		}
	
		//Befehl absetzen
		if ($doCmd) {
			if ($E=logic_getInputsQueued($id)) {
				if ($E[3]['refresh']==1 || $E[4]['refresh']==1 || $E[5]['refresh']==1 || $E[6]['refresh']==1 || $E[7]['refresh']==1 || $E[8]['refresh']==1) {
					$ok=false;
		
					if ($E[3]['refresh']==1) {
						if (intval($E[3]['value'])!=0) {$tmp=1;} else {$tmp=0;}
						$ok=LB_LBSID_sendCommand($serverIp,(($tmp==1)?'PWON':'PWSTANDBY'));
	
					} else if ($E[4]['refresh']==1) {
						$tmp=intval($E[4]['value']);
						if ($tmp>=1 && $tmp<=9) {$ok=LB_LBSID_sendCommand($serverIp,'SI'.$const_sources[$tmp]);}
	
					} else if ($E[5]['refresh']==1) {
						$tmp=intval($E[5]['value']);
						if ($tmp>=0 && $tmp<=60) {
							$ok=LB_LBSID_sendCommand($serverIp,'MV'.sprintf('%02d',$tmp));
							if ($ok) {logic_setOutput($id,5,$tmp);}	//Polling-Status ist unzuverlässig
						}
	
					} else if ($E[6]['refresh']==1) {
						if (intval($E[6]['value'])!=0) {$tmp=1;} else {$tmp=0;}
						$ok=LB_LBSID_sendCommand($serverIp,(($tmp==1)?'MUON':'MUOFF'));
	
					} else if ($E[7]['refresh']==1) {
						$tmp=intval($E[7]['value']);
						if ($tmp>=1 && $tmp<=50) {
							$ok=LB_LBSID_sendCommand($serverIp,'FVC'.sprintf('%02d',$tmp));
							if ($ok) {
								//Polling-Status gibt es nicht
								if (strval($outSBC[7])!=strval($tmp)) {
									logic_setOutput($id,7,$tmp);
									$outSBC[7]=$tmp;
								}
							}
						}
	
					} else if ($E[8]['refresh']==1) {
						$tmp=$E[8]['value'];
						$tmp=str_replace('/','',$tmp);
						$tmp=str_replace(';','',$tmp);
						if (!isEmpty($tmp)) {
							$ok=LB_LBSID_sendCommand($serverIp,$tmp);
							if ($ok) {logic_setOutput($id,8,$tmp);}
						}
					}
		
					if ($ok) {
						$pollMode=0;
						$pollInterval0_timer=getMicrotime();
					} else {
						logic_setOutput($id,1,3);
					}
	
				}
				if ($E[1]['value']==0) {break;}
			}
		}
	}

	usleep(1000*100);
}

logic_setOutput($id,1,0);

sql_disconnect();

function LB_LBSID_getStatus($ipAddress,$url) {
	$ctx=stream_context_create(array('http' => array('timeout'=>10)));
	$r=file_get_contents('http://'.$ipAddress.'/'.$url.'?nocache='.getMicrotimeInt(),false,$ctx);
	if ($r!==false) {
		$tmp=simplexml_load_string($r);
		if (!isEmpty($tmp)) {return $tmp;}
	}
	return false;
}

function LB_LBSID_sendCommand($ipAddress,$n) {
	$ctx=stream_context_create(array('http' => array('timeout'=>10)));
	$r=file_get_contents('http://'.$ipAddress.'/goform/formiPhoneAppDirect.xml?'.$n,false,$ctx);
	if ($r!==false) {return true;}
	return false;
}

?>
###[/EXEC]###
