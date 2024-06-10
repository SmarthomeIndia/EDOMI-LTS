###[DEF]###
[name		=String: KO-Werte einsetzen		]

[e#1 TRIGGER=Trigger 	]

[a#1		=		]
###[/DEF]###


###[HELP]###
Dieser Baustein ersetzt beliebig viele Platzhalter (Variablen) durch die entsprechenden KO-Werte.

Die nachfolgenden Variablen werden bei jedem Triggern des Bausteins durch einen String &ne;[leer] an E1 durch den aktuellen KO-Wert ersetzt. Das Ergebnis wird an A1 ausgegeben:

<ul>
	<li>
		"{id}": Repräsentiert den Wert eines KOs mit dieser ID
		<ul>
			<li>z.B. "Die aktuelle Server-IP ist {3}" wird zu "Die aktuelle Server-IP ist 1.2.3.4"</li>
			<li>Hinweis: Existiert das KO nicht, wird der Ausdruck unverändert ausgegeben.</li>
		</ul>
	</li>

	<li>
		"{x/y/z}": Repräsentiert den Wert einer KNX-GA
		<ul>
			<li>z.B. "Temperatur = {1/2/3} Grad Celsius" wird zu "Temperatur = 23.5 Grad Celsius"</li>
			<li>Hinweis: Existiert die KNX-GA nicht, wird der Ausdruck unverändert ausgegeben.</li>
		</ul>
	</li>
</ul>

Hinweis: In einem String kann eine beliebige Anzahl der o.g. Variablen angegeben werden.

E1: String (&ne;[leer] Triggert den Baustein)
A1: Ergebnis als String (maximal 10.000 Zeichen, überzählige Zeichen werden abgeschnitten)
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id) {
	if ($E=logic_getInputs($id)) {

		if (!isEmpty($E[1]['value']) && $E[1]['refresh']==1) {
			logic_setOutput($id,1,parseGAValues($E[1]['value']));
		}
		
	}
}
?>
###[/LBS]###


###[EXEC]###
<?

?>
###[/EXEC]###
