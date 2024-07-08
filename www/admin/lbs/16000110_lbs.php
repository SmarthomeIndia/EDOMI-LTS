###[DEF]###
[name        =Impuls/Trigger            ]

[e#1 TRIGGER=Trigger  #init=0    ]
[e#2        =Dauer (ms) #init=500    ]

[a#1        =                    ]

[v#1        =500                    ]
###[/DEF]###


###[HELP]###
Dieser Baustein generiert einen Impuls. Im Grunde verhält sich der Baustein wie ein einfacher Timer.

E1: &ne;0 = Starten. Jedes weitere Telegramm während der Laufzeit wird ignoriert!
E2: Impulsdauer (ms!): 0/[leer]=keine (d.h.: A1 wird 1 und schnellstmöglich wieder auf 0 gesetzt)
A1: Beim Start 1, nach Ablauf der Triggerzeit 0
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id)
{
    if ($E = logic_getInputs($id)) {

        if (logic_getState($id) == 0) {

            if ($E[1]['value'] != 0 && $E[1]['refresh'] == 1) {
                if ($E[2]['value'] <= 0) {
                    $E[2]['value'] = 0;
                }
                logic_setVar($id, 1, (getMicrotime() + ($E[2]['value'] / 1000)));
                logic_setOutput($id, 1, 1);
                logic_setState($id, 1, $E[2]['value']);
            }

        } else {

            if (getMicrotime() >= logic_getVar($id, 1)) {
                logic_setOutput($id, 1, 0);
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
