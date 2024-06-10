###[DEF]###
[name		=	]

[e#1		= 	]

[a#1		=	]
###[/DEF]###


###[HELP]###
Vorlage: LBS mit EXEC-Script
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id) {
	if ($E=logic_getInputs($id)) {
	
		//eigener Code...

		//bei Bedarf das EXEC-Script starten:
		//logic_callExec(LBSID,$id);
		
	}
}
?>
###[/LBS]###


###[EXEC]###
<?
require(dirname(__FILE__)."/../../../../main/include/php/incl_lbsexec.php");

//bei Bedarf kann hier die maximale Ausführungszeit des Scripts angepasst werden (Default: 30 Sekunden)
//Beispiele:
//set_time_limit(0);	//Script soll unendlich laufen (kein Timeout)
//set_time_limit(60);	//Script soll maximal 60 Sekunden laufen

sql_connect();


//eigener Code...


sql_disconnect();
?>
###[/EXEC]###
