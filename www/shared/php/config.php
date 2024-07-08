<?
/*
*/
?><?
/*
======================================================================================================================================================
ADMIN/VISU/MAIN: Konfiguration
======================================================================================================================================================
*/

//Grundeinstellungen
//------------------------------------------------------------------------------------------------------------------------------------------
//Zeitzone
date_default_timezone_set('Europe/Berlin');

//Zeichensatz: UTF-8
ini_set('mbstring.internal_encoding', 'UTF-8');

//Fehler-Level
error_reporting(E_ALL);


//------------------------------------------------------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------------------------------------------------
//Diese Konstanten nicht verändern! Die Konfiguration ausschließlich in /edomi.ini vornehmen!
//------------------------------------------------------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------------------------------------------------

//EDOMI-Version (x.yy)
define('global_version', '2.03');

//IP-Adresse des Servers
define('global_serverIP', '192.168.0.235');

//IP-Adresse: Visualisierung (Websocket)
define('global_visuIP', '192.168.0.235');

//IP-Adresse: KNX-Socket (UDP-Socket-Binding)
define('global_knxIP', '192.168.0.235');


//Pfad zu EDOMI
define('MAIN_PATH', '/usr/local/edomi');

//Pfad zu den mySQL-Datenbanken
define('MYSQL_PATH', '/var/lib/mysql');

//Pfad zu den Backups (außerhalb(!) des EDOMI-Pfades)
//das Verzeichnis muss nicht unbedingt existieren - es wird automatisch erzeugt
define('BACKUP_PATH', '/var/edomi-backups');

//max. Speicherreservierung pro RAM-DB (in Byte)
define('global_mySqlMaxRAMperDB', 100 * 1024 * 1024);

//Wochentage
$global_weekdays = array('Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag', 'Sonntag');

//Monate
$global_months = array('Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember');

//Prozesse
$global_procNames = array('EXEC', 'MAIN', 'SYSINFO', 'KNX', 'LOGIC', 'QUEUE', 'PHONE', 'VISU', 'DVR');


//DPTs
//$global_dpt[id]: Bezeichnung
//$global_dptData[id][0]: unterer Grenzwert
//$global_dptData[id][1]: oberer Grenzwert
//$global_dptData[id][2]: Datentyp (für interne Zwecke, z.B. Vergleiche bei dynamischen Designs): 0=Variant, 1=INT, 2=FLOAT, 3=STRING, 4=STRING (spezielles Format)
$global_dpt[0] = 'Variant (10.000 Zeichen)';
$global_dptData[0][0] = NULL;
$global_dptData[0][1] = NULL;
$global_dptData[0][2] = 0;
$global_dpt[1] = 'DPT 1 (1 Bit)';
$global_dptData[1][0] = 0;
$global_dptData[1][1] = 1;
$global_dptData[1][2] = 1;
$global_dpt[2] = 'DPT 2 (2 Bit)';
$global_dptData[2][0] = 0;
$global_dptData[2][1] = 3;
$global_dptData[2][2] = 1;
$global_dpt[3] = 'DPT 3 (4 Bit)';
$global_dptData[3][0] = 0;
$global_dptData[3][1] = 15;
$global_dptData[3][2] = 1;
$global_dpt[4] = 'DPT 4 (1 Byte CHAR)';
$global_dptData[4][0] = 0;
$global_dptData[4][1] = 255;
$global_dptData[4][2] = 3;
$global_dpt[5] = 'DPT 5 (1 Byte unsigned INT)';
$global_dptData[5][0] = 0;
$global_dptData[5][1] = 255;
$global_dptData[5][2] = 1;
$global_dpt[6] = 'DPT 6 (1 Byte signed INT)';
$global_dptData[6][0] = -128;
$global_dptData[6][1] = 127;
$global_dptData[6][2] = 1;
$global_dpt[7] = 'DPT 7 (2 Byte unsigned INT)';
$global_dptData[7][0] = 0;
$global_dptData[7][1] = 65535;
$global_dptData[7][2] = 1;
$global_dpt[8] = 'DPT 8 (2 Byte signed INT)';
$global_dptData[8][0] = -32767;
$global_dptData[8][1] = 32767;
$global_dptData[8][2] = 1;
$global_dpt[9] = 'DPT 9 (2 Byte FLOAT)';
$global_dptData[9][0] = -671088.64;
$global_dptData[9][1] = 670760.96;
$global_dptData[9][2] = 2;
$global_dpt[10] = 'DPT 10 (3 Byte TIME)';
$global_dptData[10][0] = NULL;
$global_dptData[10][1] = NULL;
$global_dptData[10][2] = 4;
$global_dpt[11] = 'DPT 11 (3 Byte DATE)';
$global_dptData[11][0] = NULL;
$global_dptData[11][1] = NULL;
$global_dptData[11][2] = 4;
$global_dpt[12] = 'DPT 12 (4 Byte unsigned INT)';
$global_dptData[12][0] = 0;
$global_dptData[12][1] = 4294967295;
$global_dptData[12][2] = 1;
$global_dpt[13] = 'DPT 13 (4 Byte signed INT)';
$global_dptData[13][0] = -2147483647;
$global_dptData[13][1] = 2147483647;
$global_dptData[13][2] = 1;
$global_dpt[14] = 'DPT 14 (4 Byte FLOAT)';
$global_dptData[14][0] = NULL;
$global_dptData[14][1] = NULL;
$global_dptData[14][2] = 2;
$global_dpt[16] = 'DPT 16 (14 Byte STRING)';
$global_dptData[16][0] = NULL;
$global_dptData[16][1] = NULL;
$global_dptData[16][2] = 3;
$global_dpt[17] = 'DPT 17 (1 Byte Szenen Nummer)';
$global_dptData[17][0] = 0;
$global_dptData[17][1] = 63;
$global_dptData[17][2] = 1;
$global_dpt[232] = 'DPT 232 (3xByte)';
$global_dptData[232][0] = NULL;
$global_dptData[232][1] = NULL;
$global_dptData[232][2] = 4;
$global_dpt[99999] = 'KNX-Rohdaten (!)';
$global_dptData[99999][0] = NULL;
$global_dptData[99999][1] = NULL;
$global_dptData[99999][2] = 4;

//Diagramm: Graphen (die Reihenfolge wird berücksichtigt)
$global_charttyp[0] = 'deaktiviert';
$global_charttyp[1] = 'Linien (linear)';
$global_charttyp[2] = 'Linien (Flanken)';
$global_charttyp[3] = 'Balken (dynamische Breite)';
$global_charttyp[9] = 'Balken (konstante Breite)';
$global_charttyp[103] = 'Alpha-Balken (dynamische Breite)';
$global_charttyp[10] = 'Bezierkurve';
$global_charttyp[4] = 'Bezierkurve (mittelwertig)';
$global_charttyp[101] = 'Fläche (linear)';
$global_charttyp[102] = 'Fläche (Flanken)';
$global_charttyp[11] = 'Alpha-Fläche';
$global_charttyp[110] = 'Bezierfläche';
$global_charttyp[104] = 'Bezierfläche (mittelwertig)';
$global_charttyp[5] = 'Punktwolke (Kreise)';
$global_charttyp[6] = 'Punktwolke (Kreuze)';
$global_charttyp[7] = 'Punktwolke (Striche)';
$global_charttyp[8] = 'Punktwolke (Punkte)';

//Default-FG-Farbe für Visuelemente (visu/main.css, shared/main.js, shared/config.php)
define('global_visu_defaultFgColor', '#000000');

//String-Separatoren (nicht ändern!) - auch in /shared/include/js/main.js
//z.B. für Visu-Styles, etc.
define('SEPARATOR1', chr(29));        //bis 1.18: $
define('SEPARATOR2', chr(30));        //bis 1.18: @
//für die Ajax-Übertragung
define('AJAX_SEPARATOR1', chr(28));    //bis 1.18: ~
define('AJAX_SEPARATOR2', chr(31));    //bis 1.18: |


define('global_sqlHost', '127.0.0.1');
define('global_sqlUser', 'root');
define('global_sqlPass', '');

define('global_autoBackup', true);
define('global_backupKeep', 7);

define('global_logSysEnabled', 2);
define('global_logErrEnabled', 2);
define('global_logVisuEnabled', 2);
define('global_logLogicEnabled', 2);
define('global_logMonEnabled', 2);
define('global_logMonForce', 0);
define('global_logCustomEnabled', 2);

define('global_logTextSeparator', '');
define('global_daylyStats', true);
define('global_logSysKeep', 7);
define('global_logErrKeep', 7);
define('global_logVisuKeep', 7);
define('global_logLogicKeep', 7);
define('global_logMonKeep', 2);
define('global_logCustomKeep', 1);
define('global_logTraceLevelKnx', 1);
define('global_logVisuWebsocket', 1);

define('global_logStatistics', true);

define('global_dateTimeWarning', 0);
define('global_daylyWarnMail', false);
define('global_mailNotifyOnReboot', false);

define('global_serverHeartbeat', '');
define('global_serverConsoleInterval', true);

define('global_camLiveMaxRefresh', 1);
define('global_logLevelCam', 2);

define('global_logicWaitMin', 10);
define('global_logicWaitMax', 100);
define('global_logicLoopMax', 5000);
define('global_cmdQueueMaxRate', 10);
define('global_cmdQueueTimeout', 600);

define('global_dbAutoRepair', true);
define('global_serverWANIP', 0);
define('global_autoupdate', true);
define('global_urlAutoupdate', 'http://62.75.208.51/download/updates');

define('global_adminRefresh', 1000);
define('global_koMonMaxCount', 250);
define('global_liveAutoReboot', false);
define('global_liveAutostart', 5);
define('global_adminVEzindex', 0);
define('global_adminFont', 'EDOMIfont,Lucida Grande,Arial');
define('global_duplicateSuffix', false);
define('global_logicStyleTheme', 1);
define('global_logicStyleOutbox', 0);

define('global_visuWebsocketPort', 8080);
define('global_visuWebsocketEvent', 1000);
define('global_visuWebsocketKo', 200);
define('global_visuFont', 'EDOMIfont,Lucida Grande,Arial');
define('global_visuFontSize', 10);
define('global_visuBgColor', '#343434');

define('global_knxRouterIp', '192.168.0.6');
define('global_knxRouterPort', 3671);
define('global_cEserverPort', 50000);
define('global_dEserverPort', 50001);
define('global_knxConnectionTimeout', 300);
define('global_knxMaxSendRate', 20);
define('global_InitScanTry', 3);
define('global_InitScanTryCheck', 30);
define('global_InitScanWrite', false);
define('global_knxWait', 10);
define('global_knxHeartbeat', 30);
define('global_knxHeartbeatTimeout', 5);
define('global_knxOpenTimeout', 10);
define('global_knxWriteTimeout', 1);
define('global_knxRateInterval', 1);
define('global_knxReconnectOnSeqErr', false);
define('global_knxUnknownGA', 3);
define('global_knxLogSeqErr', true);

define('global_mailHost', '');
define('global_mailPort', 587);
define('global_mailSecure', 'tls');
define('global_mailLogin', '');
define('global_mailPassword', '');
define('global_mailFromAdr', '');
define('global_mailDefaultToAdr', '');

define('global_irIp', '');
define('global_irPort', 21000);
define('global_irTimeout', 5);

define('global_fbIp', '');
define('global_fbCallMonPort', 1012);
define('global_fbSoapPort', 49000);
define('global_fbLogin', '');
define('global_fbPassword', '');

define('global_emailGatewayActive', false);
define('global_knxGatewayActive', false);
define('global_phoneGatewayActive', false);
define('global_phoneMonitorActive', false);
define('global_irGatewayActive', false);
define('global_dvrActive', false);

define('global_dvrPath', '');
define('global_dvrMountcheck', false);
?>
