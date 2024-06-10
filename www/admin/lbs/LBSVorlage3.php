###[DEF]###
[name		=	]

[e#1		= Autostart #init=1]
###[/DEF]###


###[HELP]###
Vorlage: LBS-Dämon

Der Baustein wird automatisch gestartet, sobald EDOMI gestartet wird (E1 hat einen Initialwert).
Das EXEC-Script wird dann einmalig gestartet und läuft in einer Schleife solange, bis EDOMI beendet oder neugestartet wird.
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id) {
	if ($E=logic_getInputs($id)) {

		if (logic_getStateExec($id)==0) {	//EXEC-Script läuft noch nicht
			logic_callExec(LBSID,$id);		//Exec-Script starten
			
		} else {							//EXEC-Script wird bereits ausgeführt
			//...
		}

	}
}
?>
###[/LBS]###


###[EXEC]###
<?
require(dirname(__FILE__)."/../../../../main/include/php/incl_lbsexec.php");

set_time_limit(0);

sql_connect();

//-------------------------------
//Dämon wurde gestartet
//-------------------------------
//eigener Code...

while(logic_getEdomiState()==1) {	//Hauptschleife (wird beim Beenden oder Neustart von EDOMI verlassen)
							//Wichtig: logic_getEdomiState() sorgt zudem dafür, dass die Datenbank-Verbindung aufrechterhalten wird!

	//eigener Code...

	usleep(1000*10);		//CPU-Last verteilen (die Länge der Pause sollte je nach Bedarf angepasst werden - je länger, desto ressourcenschonender)
}

//-------------------------------
//Dämon wurde beendet (EDOMI wurde beendet oder neugestartet)
//Achtung: Nach ca. 3 Sekunden wird der Prozess vom Betriebssystem hart beendet!
//-------------------------------
//eigener Code, z.B. Aufräumen, Verbindungen trennen, etc.


sql_disconnect();
?>
###[/EXEC]###
