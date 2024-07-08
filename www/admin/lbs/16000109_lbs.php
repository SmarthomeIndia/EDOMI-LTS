###[DEF]###
[name        =Impuls/Trigger (präzise)    ]

[e#1 TRIGGER=Trigger #init=0        ]
[e#2        =Timer starten #init=0    ]
[e#3        =Dauer (ms) #init=500    ]

[a#1        =                    ]

[v#1        =500                    ]
[v#2        =0                        ]
###[/DEF]###


###[HELP]###
Dieser Baustein generiert einen Impuls. Im Grunde verhält sich der Baustein wie ein einfacher Timer.

Ein neues Telegramm &ne;0 an E1 startet den Baustein, A1 wird auf 1 gesetzt.
Die Länge des Impulses ist zunächst "unendlich", d.h. A1 bleibt auf 1!

Trifft nun ein neues Telegramm =0 an E1 ein wird der Baustein wieder gestoppt, A1 wird auf 0 gesetzt.
Erst wenn ein neues Telegramm &ne;0 an E2(!) eintrifft während(!) E1&ne;0 ist, beginnt der interne Timer zu arbeiten und setzt A1 nach Ablauf der Zeit an E3 wieder auf 0.

Beispiel:
Ein virtuelles KO triggert E1. A1 setzt nun eine KNX-GA auf 1. Diese GA startet nun an E2 den Timer, sobald die GA physisch auf den Bus gesendet wurde.
Die Impulsdauer ist somit wesentlich genauer, da die reale KNX-GA den Timer startet (andernfalls hängt die Impulsdauer wesentlich von der KNX-Queue etc. ab).

E1: &ne;0 = Starten. Jedes weitere Telegramm während der Laufzeit wird ignoriert, sofern der Timer bereits läuft.
E2: &ne;0 = Timer starten: Der Timer startet nur, wenn E1 zuvor ein neues Telegramm &ne;0 empfangen hat
E3: Impulsdauer (ms!): 0/[leer]=keine (d.h.: A1 wird 1 und schnellstmöglich wieder auf 0 gesetzt)
A1: Beim Start 1, nach Ablauf der Triggerzeit 0
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id)
{
    if ($E = logic_getInputs($id)) {

        if (logic_getState($id) == 0) {

            if ($E[1]['value'] != 0 && $E[1]['refresh'] == 1) {                                            //Ausgang =1 setzen (E1!=0)
                logic_setVar($id, 2, 1);
                logic_setOutput($id, 1, 1);
            } else {
                if (logic_getVar($id, 2) == 1 && $E[1]['value'] == 0 && $E[1]['refresh'] == 1) {        //Ausgang =0 setzen (Abbruch durch E1=0)
                    logic_setVar($id, 2, 0);
                    logic_setOutput($id, 1, 0);
                } else {
                    if (logic_getVar($id, 2) == 1 && $E[2]['value'] != 0 && $E[2]['refresh'] == 1) {    //Timer starten (E2!=0)
                        logic_setVar($id, 2, 0);
                        if ($E[3]['value'] <= 0) {
                            $E[3]['value'] = 0;
                        }
                        logic_setVar($id, 1, (getMicrotime() + ($E[3]['value'] / 1000)));
                        logic_setState($id, 1, $E[3]['value']);
                    }
                }
            }

        } else {

            if (getMicrotime() >= logic_getVar($id, 1)) {                                        //Timer abgelaufen
                logic_setVar($id, 2, 0);
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
