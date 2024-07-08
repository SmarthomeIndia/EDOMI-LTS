###[DEF]###
[name        =Verzögerung            ]

[e#1 TRIGGER=Trigger            ]
[e#2        =Dauer (ms) #init=500    ]

[a#1        =            ]

[v#1        =500                    ]
###[/DEF]###


###[HELP]###
Dieser Baustein verzögert ein neues Telegramm an E1 um den an E2 angegeben Zeitraum (Millisekunden).

Wichtig: Trifft während der Verzögerung eines Telegramms ein weiteres Telegramm an E1 ein, startet der Baustein quasi neu. Das vorherige Telegramm wird verworfen!

E1: jedes Telegramm &ne;[leer] triggert den Baustein
E2: Verzögerung in Millisekunden (Änderungen an E2 werden während(!) einer laufenden Verzögerung ignoriert)
A1: nach Ablauf der Verzögerungszeit wird E1 unverändert ausgegeben
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id)
{
    if ($E = logic_getInputs($id)) {

        if (!isEmpty($E[1]['value']) && $E[1]['refresh'] == 1) {
            logic_setVar($id, 1, (getMicrotime() + ($E[2]['value'] / 1000)));
            logic_setState($id, 1, $E[2]['value']);
        }

        if (logic_getState($id) == 1) {

            if (getMicrotime() >= logic_getVar($id, 1)) { //Zeit abgelaufen?
                logic_setOutput($id, 1, $E[1]['value']);
                logic_setState($id, 0);
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
