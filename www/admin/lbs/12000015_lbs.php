###[DEF]###
[name		=Ausgangsbox: =0		]
[titel		=Ausgangsbox			]

[e#1 TRIGGER==0				]
###[/DEF]###


###[HELP]###
Dieser Baustein führt beliebige Befehle aus und schließt somit i.d.R. eine Verkettung von Logikbausteinen ab.

Getriggert wird der Baustein durch ein Telegramm =0 an E1.

E1: jedes Telegramm =0 triggert den Baustein und führt zur Ausführung der zugewiesenen Befehle

<b>Wichtig:</b>
Ein Telegramm mit einem nicht-nummerischen Wert (z.B. "EDOMI") führt ebenfalls zum Triggern des Bausteins (nicht-nummerische Werte entsprechen intern dem Zahlenwert 0).

<b>Hinweis:</b>
Die Darstellung dieser Ausgangsbox kann in der <link>Basis-Konfiguration***a-1</link> modifiziert werden (z.B. kann diese Ausgangsbox auf Wunsch einzeilig dargestellt werden). In der Vorschau werden diese Darstellungsoptionen jedoch ignoriert.
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id) {
	if ($E=logic_getInputs($id)) {
		if ($E[1]['value']==0 && $E[1]['refresh']==1) {
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
