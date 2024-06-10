###[DEF]###
[name		=Ausgangsbox: Telegramm	]
[titel		=Ausgangsbox			]

[e#1 TRIGGER=Telegramm				]
###[/DEF]###


###[HELP]###
Dieser Baustein führt beliebige Befehle aus und schließt somit i.d.R. eine Verkettung von Logikbausteinen ab.

Getriggert wird der Baustein durch jedes(!) Telegramm an E1 - dieser Baustein wird auch durch ein "leeres" Telegramm getriggert.

E1: jedes(!) Telegramm (auch "leere" Telegramme) triggert den Baustein und führt zur Ausführung der zugewiesenen Befehle

<b>Wichtig:</b>
Dieser Baustein ist für gewöhnliche Logiken nicht(!) geeignet, da die meisten Logikbausteine leere Telegramme ignorieren und somit nicht an den Ausgängen bereitstellen. Daher ist dieser Baustein ausschließlich für spezielle Community-Logikbausteine einsetzbar.

<b>Hinweis:</b>
Die Darstellung dieser Ausgangsbox kann in der <link>Basis-Konfiguration***a-1</link> modifiziert werden (z.B. kann diese Ausgangsbox auf Wunsch einzeilig dargestellt werden). In der Vorschau werden diese Darstellungsoptionen jedoch ignoriert.
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id) {
	if ($E=logic_getInputs($id)) {
		if ($E[1]['refresh']==1) {
			mainLogicExecuteCmdList($id,$E[1]['value']);
		}
	}
}
?>
###[/LBS]###


###[EXEC]###
<?

?>
###[/EXEC]###
