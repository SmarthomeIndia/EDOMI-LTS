###[DEF]###
[name        =KO-Initialisierung            ]

[e#1 TRIGGER=Trigger                    ]
[e#2        =KO &harr;                    ]
###[/DEF]###


###[HELP]###
Dieser Baustein dient der Initialisierung eines (leeren) Kommunikationsobjekts.

Getriggert wird der Baustein durch ein beliebiges Telegramm &ne;[leer] an E1.

Sofern an E2 ein Kommunikationsobjekt anliegt, wird dieses KO auf den Wert an E1 gesetzt - das KO muss jedoch [leer] sein. Ist das KO nicht [leer] wird es nicht verändert.

<b>Anwendungsbeispiel:</b>
Ein internes KO soll beim Start einmalig(!) auf den Wert einer KNX-GA (z.B. Status-GA) gesetzt werden, um diese KO-Werte abzugleichen. An E1 liegt die KNX-GA an, an E2 das interne KO (das zunächst leer ist).
Beim Start wird nun die Status-GA per Initscan abgefragt (sofern konfiguriert) und der Baustein wird getriggert. Da das interne KO noch leer ist, ist die Bedingung erfüllt und das interne KO erhält nun den Wert der Status-GA.

<b>Wichtig:</b>
Dieser Baustein verfügt über keinerlei Ausgänge, jedoch wird das Kommunikationsobjekt an E2 ggf. auf einen Wert gesetzt!

E1: jedes Telegramm &ne;[leer] triggert den Baustein und setzt das KO an E2 auf diesen Wert, sofern das KO an E2 [leer] ist
E2: KO: dieses Kommunikationsobjekt wird auf den Wert an E1 gesetzt, sofern das KO [leer] ist
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id)
{
    if ($E = logic_getInputs($id)) {
        if (!isEmpty($E[1]['value']) && $E[1]['refresh'] == 1 && isEmpty($E[2]['value'])) {
            logic_setInputKoValue($id, 2, $E[1]['value']);
        }
    }
}

?>
###[/LBS]###


###[EXEC]###
<?

?>
###[/EXEC]###
