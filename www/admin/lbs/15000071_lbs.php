###[DEF]###
[name        =Zufallszahl            ]

[e#1 TRIGGER=Trigger            ]
[e#2        =von #init=0            ]
[e#3        =bis #init=1            ]
[e#4        =Nachkommastellen #init=0    ]

[a#1        =                    ]
###[/DEF]###


###[HELP]###
Dieser Baustein erzeugt eine (pseudo) Zufallszahl. Jedes neue Telegramm &ne;0 an E1 erzeugt eine neue Zufallszahl an A1.

E2 und E3 bestimmten den Wertebereich der erzeugten Zufallszahl, E4 bestimmt die Anzahl der Nachkommastellen der erzeugten Zufallszahl.

Beispiel:
Wenn E2=-10, E3=10 und E4=3 gesetzt sind, wird die Zufallszahl an A1 im Bereich von -10.000 bis 10.000 liegen.

E1: &ne;0 = Trigger
E2: untere Grenze des Wertebereichs, kann auch negativ sein (es sind nur ganze Zahlen erlaubt!)
E3: obere Grenze des Wertebereichs (es sind nur ganze Zahlen erlaubt!)
E4: Anzahl der Nachkommastellen (0=Ganzzahl)
A1: erzeugte Zufallszahl
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id)
{
    if ($E = logic_getInputs($id)) {
        if ($E[1]['value'] != 0 && $E[1]['refresh'] == 1) { //Start
            if (is_numeric($E[2]['value']) && is_numeric($E[3]['value']) && is_numeric($E[4]['value'])) {
                $r = mt_rand($E[2]['value'] * (pow(10, $E[4]['value'])), $E[3]['value'] * (pow(10, $E[4]['value']))) / pow(10, $E[4]['value']);
                logic_setOutput($id, 1, $r);
            }
        }
    }
}

?>
###[/LBS]###


###[EXEC]###
<?

?>
###[/EXEC]###
