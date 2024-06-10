###[DEF]###
[name			= 	Mediacenter (macOS)]

[e#1 IMPORTANT	= Aktiviert	#init=1	]
[e#2 OPTION		= MAC;IP-Adresse	]
[e#3 TRIGGER	= System			]
[e#4 TRIGGER	= Airplay (System)	]
[e#5 TRIGGER	= iTunes: Ein/Aus 	] 
[e#6 TRIGGER	= iTunes: Airplay 	]
[e#7 TRIGGER	= iTunes: Steuerung	]
[e#8 TRIGGER	= iTunes: Playlist	]
[e#9 TRIGGER	= eyeTV: Ein/Aus	] 
[e#10 TRIGGER	= eyeTV: Zapp		]
[e#11 TRIGGER	= eyeTV: Kanal		]
[e#12 OPTION	= EDOMI: NIC-Name	]

[a#1		= Status			]
[a#2		= System			]
[a#3		= Airplay 			]
[a#4		= iTunes: Ein/Aus	]
[a#5		= iTunes: Trackname	]
[a#6		= iTunes: Airplay 	]
[a#7		= iTunes: Status	]
[a#8		= iTunes: Playlist	]
[a#9		= eyeTV: Ein/Aus	]
[a#10		= eyeTV: Kanalname	]
[a#11		= eyeTV: Kanal		]
###[/DEF]###


###[HELP]###
<b>ACHTUNG: Diese Hilfe ist noch unvollständig!</b>

Dieser Baustein steuert einen MacOS-Computer bzw. dessen Applikationen (iTunes und EyeTV).

<b>Hinweise zur Einrichtung eines MacOS-Systems im Abschnitt [INFO] am Ende dieses Baustein-Quelltextes.</b>

Sofern der Baustein aktiviert ist (E1&ne;0), wird der Host-Status zyklisch abgefragt.

Über die Eingänge E3..E11 der Host bzw. iTunes und EyeTV gesteuert (Befehle). Die Befehle werden mit einer Rate von max. 2/s ausgeführt.

E1: &ne;0=Baustein ist aktiviert, 0=Baustein ist deaktiviert
E2: MAC-Adresse und IP-Adresse:Port des Computers (in der Form "xx:xx:xx:xx:xx:xx;192.168.0.1:8000"), ohne diese Angabe startet der Baustein nicht
E3: Computer-Steuerung: 0=Ruhezustand, 1=Aufwecken, 2=Neustarten, 3=Runterfahren
E4: Airplay: 0=aus, 1=EG, 2=OG
E5: iTunes: 0=beenden, 1=starten
E6: iTunes: Airplay: 0=aus, 1=EG, 2=OG
E7: iTunes: Wiedergabesteuerung: 0=stop, 1=play, 2=pause, 3=skip+, 4=skip-
E8: iTunes: Playlist-Auswahl: Name der abzuspielenden Playlist
E9: eyeTV: 0=beenden, 1=starten
E10: eyeTV: Zapping: 0=Kanal-, 1=Kanal+
E11: eyeTV: Kanal-Auswahl: 1..&infin;=Kanal-Nummer
E12: Name der Netzwerkschnittstelle des EDOMI-Servers, leer=automatisch ermitteln

A1: letzter Status des Bausteins: 0=deaktiviert, 1=aktiviert, 2=Abfrage-Fehler, 3=Fehler beim Absetzen eines Befehls (A1 wird z.B. nach einem Fehler nicht zurückgesetzt, der letzte Zustand bleibt also stets erhalten)
A2 (SBC): macOS-Status: 0=ausgeschaltet (nicht erreichbar), 1=eingeschaltet, 2=Neustart angefordert (Sollwert), 3=Shutdown angefordert (Sollwert)
A3 (SBC): Airplay-Status (Sollwert): 0=aus, 1=EG, 2=OG
A4 (SBC): iTunes: Status: 0=beendet, 1=gestartet
A5 (SBC): iTunes: aktueller Trackname beim Abspielen
A6 (SBC): iTunes: Airplay-Status (Sollwert): 0=aus, 1=EG, 2=OG
A7 (SBC): iTunes: Wiedergabestatus: 0=stop, 1=play, 2=pause
A8 (SBC): iTunes: aktueller Playlist-Name beim Abspielen
A9 (SBC): eyeTV: Status: 0=beendet, 1=gestartet
A10 (SBC): eyeTV: aktueller Kanal-Name
A11 (SBC): eyeTV: aktuelle Kanal-Nummer

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
$cmdInterval=1;						//Befehlsintervall in Sekunden
$pollIntervals=array(3,5,60);		//Abfrage-Intervalle: nach einem Befehl, wenn System=Ein, wenn System=Aus (Idle) 
$pollInterval0_timeout=30;			//nach einem Befehl bleibt das Abfrage-Intervall[0] für diese Zeitspanne erhalten
//------------------------------------------------------------------------------------------------------------

$pollMode=1;						//0=nach einem Befehl, 1=wenn System Ein (Default nach Start), 2=wenn System Aus
$pollTimerOld=0;
$pollInterval0_timer=false;
$cmdTimerOld=0;

$outSBC=array(false,null,null,null,null,null,null,null,null,null,null,null);

if ($E=logic_getInputs($id)) {
	$server=explode(';',$E[2]['value']);
	$nicName=$E[12]['value'];
	if (isEmpty($nicName)) {$nicName=LB_LBSID_getNicName();}			
} else {
	$server=false;
	$nicName=null;
}

logic_setOutput($id,1,1);

while (logic_getEdomiState()==1 && $server!==false && !isEmpty($nicName)) {
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
			$tmp=LB_LBSID_getStatus($server[1]);
			if ($tmp!==false) {

				if (array_key_exists('system',$tmp)) {					
					if (strval($outSBC[3])!=strval($tmp['system'])) {
						$outSBC[3]=1;
						logic_setOutput($id,2,1);
						//nach dem Aufwachen: onwakeup senden
//### wird natürlich auch bei EDOMI-Neustart gemacht... (V3 ist ja dann leer)
						LB_LBSID_sendCommand($server[1],'system','onwakeup','');
					}		
				}

				if (array_key_exists('itunes',$tmp)) {					
					if (strval($outSBC[5])!=strval($tmp['itunes'])) {
						$outSBC[5]=$tmp['itunes'];
						logic_setOutput($id,4,$tmp['itunes']);
					}		
				}

				if (array_key_exists('itunes-status',$tmp)) {					
					if (strval($outSBC[7])!=strval($tmp['itunes-status'])) {
						$outSBC[7]=$tmp['itunes-status'];
						logic_setOutput($id,7,$tmp['itunes-status']);
					}		
				}
				if (array_key_exists('itunes-track',$tmp)) {					
					if (strval($outSBC[2])!=strval($tmp['itunes-track'])) {
						$outSBC[2]=$tmp['itunes-track'];
						logic_setOutput($id,5,$tmp['itunes-track']);
					}		
				}
				if (array_key_exists('itunes-playlist',$tmp)) {					
					if (strval($outSBC[8])!=strval($tmp['itunes-playlist'])) {
						$outSBC[8]=$tmp['itunes-playlist'];
						logic_setOutput($id,8,$tmp['itunes-playlist']);
					}		
				}

				if (array_key_exists('eyetv',$tmp)) {					
					if (strval($outSBC[9])!=strval($tmp['eyetv'])) {
						$outSBC[9]=$tmp['eyetv'];
						logic_setOutput($id,9,$tmp['eyetv']);
					}		
				}

				if (array_key_exists('eyetv-channelname',$tmp)) {					
					if (strval($outSBC[10])!=strval($tmp['eyetv-channelname'])) {
						$outSBC[10]=$tmp['eyetv-channelname'];
						logic_setOutput($id,10,$tmp['eyetv-channelname']);
					}		
				}

				if (array_key_exists('eyetv-channelid',$tmp)) {					
					if (strval($outSBC[11])!=strval($tmp['eyetv-channelid'])) {
						$outSBC[11]=$tmp['eyetv-channelid'];
						logic_setOutput($id,11,$tmp['eyetv-channelid']);
					}		
				}

				$pollMode=1;
			} else {
				if (strval($outSBC[3])!=strval(0)) {
					$outSBC[3]=0;
					logic_setOutput($id,2,0);
				}		
				logic_setOutput($id,1,2);
				$pollMode=2;
			}
		}


		//Befehl absetzen
		if ($doCmd) {
			if ($E=logic_getInputsQueued($id)) {
				if ($E[3]['refresh']==1 || $E[4]['refresh']==1 || $E[5]['refresh']==1 || $E[6]['refresh']==1 || $E[7]['refresh']==1 || $E[8]['refresh']==1 || $E[9]['refresh']==1 || $E[10]['refresh']==1 || $E[11]['refresh']==1) {
					$ok=false;
		
					if ($E[3]['refresh']==1) {
						if (intval($E[3]['value'])==0) {
							//Ruhezustand
							$ok=LB_LBSID_sendCommand($server[1],'system','sleep','');
	
						} else if (intval($E[3]['value'])==1) {
							//Einschalten (Aufwachen)
							shell_exec('/sbin/ether-wake -i '.$nicName.' '.$server[0]);
							$ok=true;
	
						} else if (intval($E[3]['value'])==2) {
							//Neustart
							$ok=LB_LBSID_sendCommand($server[1],'system','restart','');
							if ($ok) {logic_setOutput($id,2,2);}
	
						} else if (intval($E[3]['value'])==3) {
							//Shutdown
							$ok=LB_LBSID_sendCommand($server[1],'system','shutdown','');
							if ($ok) {logic_setOutput($id,2,3);}
						}					
					}
	
					if ($E[4]['refresh']==1) {
						//Airplay: 0=aus (Beamer), 1=EG, 2=OG
						$tmp=intval($E[4]['value']);
						if ($tmp>=0 && $tmp<=2) {
							$ok=LB_LBSID_sendCommand($server[1],'airplay_screen',($tmp+1),'');
							if ($ok) {logic_setOutput($id,3,$tmp);}
						}
					}
	
					if ($E[5]['refresh']==1) {
						//iTunes: 0=beenden, 1=starten
						if (intval($E[5]['value'])==0) {
							$ok=LB_LBSID_sendCommand($server[1],'itunes','quit','');
						} else if (intval($E[5]['value'])==1) {
							$ok=LB_LBSID_sendCommand($server[1],'itunes','launch','');
						}
					}
	
					if ($E[6]['refresh']==1) {
						//iTunes: Airplay: 0=aus (Denon), 1=EG, 2=OG
						$tmp=intval($E[6]['value']);
						if ($tmp>=0 && $tmp<=2) {
							$ok=LB_LBSID_sendCommand($server[1],'itunes','airplay',($tmp+1));
							if ($ok) {logic_setOutput($id,6,$tmp);}
						}
					}
	
					if ($E[7]['refresh']==1) {
						//iTunes: 0=stop, 1=play, 2=pause, 3=skip+, 4=skip-
						$tmp=intval($E[7]['value']);
						if ($tmp==0) {
							$ok=LB_LBSID_sendCommand($server[1],'itunes','stop','');
						} else if ($tmp==1) {
							$ok=LB_LBSID_sendCommand($server[1],'itunes','play','');
						} else if ($tmp==2) {
							$ok=LB_LBSID_sendCommand($server[1],'itunes','pause','');
						} else if ($tmp==3) {
							$ok=LB_LBSID_sendCommand($server[1],'itunes','skipnext','');
						} else if ($tmp==4) {
							$ok=LB_LBSID_sendCommand($server[1],'itunes','skipprev','');
						}
					}
	
					if ($E[8]['refresh']==1) {
						//iTunes: Playlist abspielen (E8=Name der Playlist)
						if (!isEmpty($E[8]['value'])) {
							$ok=LB_LBSID_sendCommand($server[1],'itunes','playlist',$E[8]['value']);
						}
					}
	
					if ($E[9]['refresh']==1) {
						//eyeTV: 0=beenden, 1=starten
						if (intval($E[9]['value'])==0) {
							$ok=LB_LBSID_sendCommand($server[1],'eyetv','quit','');
						} else if (intval($E[9]['value'])==1) {
							$ok=LB_LBSID_sendCommand($server[1],'eyetv','launch','');
						}
					}
	
					if ($E[10]['refresh']==1) {
						//eyeTV: 1=Kanal+, 0=Kanal-
						$tmp=intval($E[10]['value']);
						if ($tmp==1) {
							$ok=LB_LBSID_sendCommand($server[1],'eyetv','channelup','');
						} else if ($tmp==0) {
							$ok=LB_LBSID_sendCommand($server[1],'eyetv','channeldown','');
						}
					}
	
					if ($E[11]['refresh']==1) {
						//eyeTV: Kanal auswählen
						$tmp=intval($E[11]['value']);
						if ($tmp>0) {
							$ok=LB_LBSID_sendCommand($server[1],'eyetv','setchannel',$tmp);
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

function LB_LBSID_getStatus($ipAddress) {
	$ctx=stream_context_create(array('http' => array('timeout'=>10)));
	$r=file_get_contents('http://'.$ipAddress.'/?arg1=status&nocache='.getMicrotimeInt(),false,$ctx);
	if ($r!==false) {
		$rr=array();
		if (substr($r,0,9)=='system=1;') {
			$n=explode(';',$r,-1);
			for ($t=0;$t<count($n);$t++) {
				$tmp=explode('=',$n[$t]);
				$rr[$tmp[0]]=$tmp[1];
			}
		} else {
			$rr['system']=1;	//Fehlermeldung vom Applescript, aber System ist ja dennoch Ein...
		}
		return $rr;
	}
	return false;
}

function LB_LBSID_sendCommand($ipAddress,$arg1,$arg2,$arg3) {
	$ctx=stream_context_create(array('http' => array('timeout'=>10)));
	$r=file_get_contents('http://'.$ipAddress.'/?arg1='.$arg1.'&arg2='.$arg2.'&arg3='.$arg3,false,$ctx);
	if ($r!==false) {return true;}
	return false;
}

function LB_LBSID_getNicName() {
	exec("ip -o -4 route show to default",$tmp,$void);
	$n=explode(' ',$tmp[0],5);
	if (count($n)>=5) {
		$tmp=trim($n[4]);
		return $tmp;
	}
	return null;
}
?>
###[/EXEC]###


###[INFO]###
Einrichtung des Zielsystems (MacOS)

Die folgenden Systemeinstellungen sind anzupassen:
	Das Ausführen von "Terminal" muss erlaubt werden:
	- "Sicherheit > Privatsphäre > Terminal.app" erlauben
	- ggf. auch "Scripteditor" erlauben (falls remote.scpt debugged/erweitert werden soll)
	- Autostart: in "Systemeinstellungen > Benutzer > Anmeldeobjekte" einfügen:
		- "iTunes"
		- "iTunes Helper"
		- "EyeTV Helder"
		- "daemon.command" (s.u.)

	
Die folgenden Dateien sind (z.B. im Ordner "$HOME") zu erstellen:
	Datei "daemon.command":
		- dieses Shellscript startet einen minimalen PHP-Server (in macOS ist dieser PHP-Server vorinstalliert)
		- ggf. muss die Datei ausführbar gemacht werden (per chmod +x)
		- ggf. muss der Pfad ($HOME/macEDOMI) in der Datei angepasst werden
		- Autostart: die Datei in "Systemeinstellungen > Benutzer > Anmeldeobjekte" einfügen
		- alternativ kann der PHP-Server auch manuell im Terminal gestartet werden: php -S 0.0.0.0:8000 -t $HOME/macEDOMI
		- Dateiinhalt:
			php -S 0.0.0.0:8000 -t $HOME/macEDOMI


	Datei "index.php":
		- wird von extern per HTTP-Request aufgerufen (und startet dann das AppleScript "remote.scpt")
		- erwartet stets 1..3 Parameter in der Form Befehl/Option: http://<IP>:8000?arg1=<BEFEHL>&arg2=<OPTION1>&arg3=<OPTION2>
		- Beispiel: http://<IP>:8000?arg1=system&arg2=sleep (aktiviert den Ruhezustand)
		- Dateiinhalt:
			$arg1=$_GET['arg1'];
			$arg2=$_GET['arg2'];
			$arg3=$_GET['arg3'];
			if ($arg1=='ping') {
				echo '1';
			} else if ($arg1!='') {
				if ($arg2=='') {$arg2='null';}
				if ($arg3=='') {$arg3='null';}
				$r=shell_exec('osascript remote.scpt '.$arg1.' '.$arg2.' '.$arg3);
				echo utf8_decode($r);
			}

		
	Datei "remote.scpt":
		- steuert den Mac bzw. iTunes und EyeTV
		- wird von index.php auf dem Mac ausgeführt (AppleScript)
		- Dateiinhalt (Apple-ScriptEditor verwenden!):
			on run argv
				set r to "error"
				if (count of argv) ≥ 1 then
					set arg1 to item 1 of argv
					set arg2 to item 2 of argv
					set arg3 to item 3 of argv
		
					if (arg1 = "status") then set r to cmd_status()
					if (arg1 = "system") then set r to cmd_system(arg2)
					if (arg1 = "airplay_screen") then set r to cmd_airplay_screenmirroring(arg2)
					if (arg1 = "airplay_speaker") then set r to cmd_airplay_speaker(arg2)
					if (arg1 = "itunes") then set r to cmd_itunes(arg2, arg3)
					if (arg1 = "eyetv") then set r to cmd_eyetv(arg2, arg3)
				end if
				return r
			end run

			# ----------------------------------------------------------------------------------
			# Befehle
			# ----------------------------------------------------------------------------------
			on cmd_status()
				set s to "system=1;"
	
				if application "iTunes" is running then
					set s to s & "itunes=1;"
					set tr to ""
					set pl to ""
					tell application "iTunes"
						if the player state is stopped then
							set s to s & "itunes-status=0;"
						else if the player state is paused then
							set s to s & "itunes-status=2;"
							set tr to name of current track
							set pl to name of current playlist
						else
							set s to s & "itunes-status=1;"
							set tr to name of current track
							set pl to name of current playlist
						end if
					end tell
					set tr to replaceString(tr, "=", "-")
					set tr to replaceString(tr, ";", ",")
					set pl to replaceString(pl, "=", "-")
					set pl to replaceString(pl, ";", ",")
					set s to s & "itunes-track=" & tr & ";"
					set s to s & "itunes-playlist=" & pl & ";"
				else
					set s to s & "itunes=0;"
				end if
	
				if application "EyeTV" is running then
					set s to s & "eyetv=1;"
					set chid to ""
					set chname to ""
					tell application "EyeTV"
						set chname to name of channel [current channel]
						set chid to channel number of channel [current channel]
					end tell
					set chname to replaceString(chname, "=", "-")
					set chname to replaceString(chname, ";", ",")
					set s to s & "eyetv-channelname=" & chname & ";"
					set s to s & "eyetv-channelid=" & chid & ";"
				else
					set s to s & "eyetv=0;"
				end if
	
				return s
			end cmd_status

			on cmd_system(cmd)
				if (cmd = "beep") then beep
				if (cmd = "onwakeup") then
					# Monitor einschalten
					do shell script "caffeinate -u -t 2"
					# Terminal (PHP) minimieren
					tell application "Terminal" to set miniaturized of window 1 to true
					# iTunes starten 
					cmd_itunes("launch", "")
				end if
				if (cmd = "sleep") then
					tell application "iTunes" to quit
					tell application "Photos" to quit
					tell application "EyeTV" to quit
					tell application "Screen Sharing" to quit
					delay 3
					tell application "System Events" to sleep
					# tell application "Finder" to sleep
				end if
				if (cmd = "restart") then tell application "Finder" to restart
				if (cmd = "shutdown") then
					tell application "Terminal" to quit
					tell application "Finder" to shut down
				end if
				return 1
			end cmd_system

			on cmd_airplay_screenmirroring(deviceID)
				# Airplay-Screenmirroring
				set deviceNames to {"Airplay deaktivieren", "Apple TV EG", "Apple TV OG"}
				set tmp to item deviceID of deviceNames
				try
					tell application "System Events"
						tell process "SystemUIServer"
							tell (menu bar item 1 of menu bar 1 whose description contains "Monitore")
								select
								delay 0.1
								key code 125 using command down
								delay 0.1
								click (menu item 1 where its name starts with tmp) of menu 1
							end tell
						end tell
					end tell
				end try
				delay 5
				set volume output volume 100
				return 1
			end cmd_airplay_screenmirroring

			on cmd_airplay_speaker(deviceID)
				# Airplay-Lautsprecher (systemweit)
				set deviceNames to {"Kopfhörer", "Apple TV EG", "Apple TV OG"}
				set tmp to item deviceID of deviceNames
				tell application "System Events"
					tell process "SystemUIServer"
						try
							key down option
							tell (menu bar item 1 of menu bar 1 whose description contains "Tonlautstärke")
								select
								delay 0.1
								key code 125 using command down
								delay 0.1
								click (menu item 1 where its name starts with tmp) of menu 1
							end tell
						end try
						key up option
					end tell
				end tell
				delay 5
				set volume output volume 100
				return 1
			end cmd_airplay_speaker

			on cmd_itunes(cmd, arg)
				if (cmd = "launch") then
					if application "iTunes" is not frontmost application then tell application "iTunes" to activate
					tell application "iTunes" to set sound volume to 100
				end if
	
				if application "iTunes" is running then
					if (cmd = "volume") then tell application "iTunes" to set sound volume to arg
					if (cmd = "playlist") then tell application "iTunes" to play playlist arg
					if (cmd = "stop") then tell application "iTunes" to stop
					if (cmd = "play") then tell application "iTunes" to play
					if (cmd = "pause") then tell application "iTunes" to playpause
					if (cmd = "skipnext") then tell application "iTunes" to play (next track)
					if (cmd = "skipprev") then tell application "iTunes" to play (previous track)
		
					if (cmd = "airplay") then
						tell application "iTunes" to set deviceNames to (get name of AirPlay devices)
						set tmp to item arg of deviceNames
						tell application "iTunes" to set selected of AirPlay device tmp to true
						delay 5
						set volume output volume 100
						tell application "iTunes" to set sound volume to 100
					end if
					if (cmd = "quit") then tell application "iTunes" to quit
				end if
				return 1
			end cmd_itunes

			on cmd_eyetv(cmd, arg)
				if (cmd = "launch") then
					if application "EyeTV" is not frontmost application then
						tell application "EyeTV" to activate
						tell application "EyeTV"
							delay 5
							enter full screen
							volume_change level 1
						end tell
					end if
				end if
				if (cmd = "quit") then
					if application "EyeTV" is running then tell application "EyeTV" to quit
				end if
				if (cmd = "setchannel") then
					if application "EyeTV" is running then tell application "EyeTV" to channel_change channel number arg
				end if
				if (cmd = "channelup") then
					if application "EyeTV" is running then tell application "EyeTV" to channel_up
				end if
				if (cmd = "channeldown") then
					if application "EyeTV" is running then tell application "EyeTV" to channel_down
				end if
				return 1
			end cmd_eyetv

			on replaceString(theText, oldString, newString)
				set AppleScript's text item delimiters to oldString
				set tempList to every text item of theText
				set AppleScript's text item delimiters to newString
				set theText to the tempList as string
				set AppleScript's text item delimiters to ""
				return theText
			end replaceString
###[/INFO]###
