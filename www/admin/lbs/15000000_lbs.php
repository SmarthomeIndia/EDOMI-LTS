###[DEF]###
[name        =Formelberechnung    ]

[e#1 IMPORTANT    =Formel        ]
[e#2 OPTION        =Modus    #init=1    ]
[e#3 TRIGGER=$x                ]
[e#4 TRIGGER=$a                ]
[e#5 TRIGGER=$b                ]
[e#6 TRIGGER=$c                ]
[e#7 TRIGGER=$d                ]
[e#8 TRIGGER=$e                ]

[a#1        =Ergebnis        ]
[a#2        =Fehler            ]
###[/DEF]###


###[HELP]###
Dieser Baustein setzt Variablen bzw. Konstanten in eine beliebige mathematische Formel ein. Dabei ist eine gültige PHP-Syntax zu verwenden.

An E1 wird eine gültige mathematische Formel (PHP) erwartet, z.B. "$a-$x+$b" (ohne ""). Sämtliche PHP-Funktionen stehen grundsätzlich zu Verfügung, daher ist mit größter Sorgfalt zu arbeiten (s.u.).
Auch Bedingungen wie "(($x>=1)?1:0)" sind möglich.

Der Baustein wird bei jeder Änderung an E1 bzw. E3..E8 getriggert und führt eine erneute Berechnung aus. Wird E1 [leer] belassen, wird keine Berechnung ausgeführt.
Bei einem Fehler wie "$x/0" wird i.d.R. A1 auf [leer] gesetzt (A2 bleibt unverändert). Ein Fehler in der Formel an E1 führt i.d.R. dazu, dass A2 auf 1 gesetzt wird (z.B. E1=";").
Ein syntaktischer Fehler an E1 wie z.B. "$x+blablabla()" führt je nach Modus (E2) zum Absturz(!) von EDOMI (s.u.).

Wird E2 auf 0 gesetzt, arbeitet der Baustein direkt in der Logik-Engine. Eine fehlerhafte Formel an E1 kann daher EDOMI zu Absturz bringen!
Wird E2 hingegen auf 1 gesetzt, wird der Baustein in einem eigenen PHP-Prozess (EXEC) ausgeführt. Eine fehlerhafte Formel an E1 führt daher nicht zum Absturz von EDOMI, jedoch arbeitet der Baustein weniger performant.

Empfehlung:
Um Abstürze während der Entwicklung einer Formel zu vermeiden, kann E2 zunächst auf 1 gesetzt werden. Erst wenn die Berechnung stabil ausgeführt wird, kann E2 auf 0 gesetzt werden.

Wichtig:
Die Berechnung wird mittels der PHP-Funktion "eval()" ausgeführt, daher sind sämtliche Parameter mit größter Sorgfalt anzugeben. Eine Überprüfung der Angaben findet nicht(!) statt!


E1: Formel, z.B. "($x+($a*2))*pow($b,2)"
E2: Modus: 0=LBS (performanter), 1=EXEC (sicherer)
E3..E8: Variablen $x bzw. $a..$e

A1: Ergebnis der Berechnung oder [leer]
A2: wird bei einem Fehler auf 1 gesetzt (aber niemals wieder auf 0)
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id)
{
    if ($E = logic_getInputs($id)) {
        if (!isEmpty($E[1]['value']) && ($E[1]['refresh'] == 1 || $E[3]['refresh'] == 1 || $E[4]['refresh'] == 1 || $E[5]['refresh'] == 1 || $E[6]['refresh'] == 1 || $E[7]['refresh'] == 1 || $E[8]['refresh'] == 1)) {

            if ($E[2]['value'] == 1) {
                logic_callExec(LBSID, $id, true);

            } else {
                $f = $E[1]['value'];
                $x = $E[3]['value'];
                $a = $E[4]['value'];
                $b = $E[5]['value'];
                $c = $E[6]['value'];
                $d = $E[7]['value'];
                $e = $E[8]['value'];

                if (!isEmpty($f)) {
                    $r = '';
                    if (eval('$r=' . $f . ';return true;') === true) {
                        logic_setOutput($id, 1, $r);
                    } else {
                        logic_setOutput($id, 2, 1);
                    }
                }
            }
        }
    }
}

?>
###[/LBS]###


###[EXEC]###
<?
require(dirname(__FILE__) . "/../../../../main/include/php/incl_lbsexec.php");

sql_connect();

if ($E = logic_getInputs($id)) {
    $f = $E[1]['value'];
    $x = $E[3]['value'];
    $a = $E[4]['value'];
    $b = $E[5]['value'];
    $c = $E[6]['value'];
    $d = $E[7]['value'];
    $e = $E[8]['value'];

    if (!isEmpty($f)) {
        $r = '';
        if (eval('$r=' . $f . ';return true;') === true) {
            logic_setOutput($id, 1, $r);
        } else {
            logic_setOutput($id, 2, 1);
        }
    }
}

sql_disconnect();
?>
###[/EXEC]###

