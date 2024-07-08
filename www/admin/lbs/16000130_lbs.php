###[DEF]###
[name        =Datum/Uhrzeit            ]

[e#1 TRIGGER    =Trigger            ]

[a#1        =Datum (DPT11)    ]
[a#2        =Zeit (DPT10)    ]
[a#3        =Datum            ]
[a#4        =Wochentag        ]
[a#5        =WochentagID    ]
[a#6        =Kalenderwoche    ]
[a#7        =Tag            ]
[a#8        =Monat            ]
[a#9        =Jahr            ]
[a#10        =Uhrzeit        ]
[a#11        =Stunde            ]
[a#12        =Minute            ]
[a#13        =Sekunde        ]
[a#14        =Mikrosekunden    ]
###[/DEF]###


###[HELP]###
Dieser Baustein wird durch ein neues Telegramm &ne;0 an E1 getriggert und setzt die Ausgänge auf die aktuellen Datum- und Zeitwerte.

An A1 wird das aktuelle Datum in einem internen Format ausgegeben und kann direkt auf eine DPT11-GA gegeben werden.

An A2 wird die aktuelle Uhrzeit und der aktuelle Wochentag in einem internen Format ausgegeben und kann direkt auf eine DPT10-GA gegeben werden.


E1: &ne;0 = Trigger

A1: Datum im (internen) DPT11-Format (kann direkt auf eine DPT11-GA gegeben werden)
A2: Tag.Uhrzeit im (internen) DPT10-Format (kann direkt auf eine DPT10-GA gegeben werden)
A3: Datum (tt.mm.jjjj)
A4: Wochentag im Klartext (z.B. Montag)
A5: Wochentag-ID (1=Montag, 2=Dienstag, ..., 7=Sonntag)
A6: Kalenderwoche (nach ISO-8601: die Woche beginnt am Montag)
A7: Tag
A8: Monat
A9: Jahr
A10: Uhrzeit (hh:mm:ss)
A11: Stunde
A12: Minute
A13: Sekunde
A14: Mikrosekunde (als Float)
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id)
{
    if ($E = logic_getInputs($id)) {
        if ($E[1]['value'] != 0 && $E[1]['refresh'] == 1) {
            global $global_weekdays;
            $t = microtime(true);

            logic_setOutput($id, 1, date("Y-m-d", $t));    //internes Format für DPT11
            logic_setOutput($id, 2, date("N.H:i:s", $t));    //internes Format für DPT10
            logic_setOutput($id, 3, date("d.m.Y", $t));
            logic_setOutput($id, 4, $global_weekdays[date("N", $t) - 1]);
            logic_setOutput($id, 5, date("N", $t));
            logic_setOutput($id, 6, date("W", $t));
            logic_setOutput($id, 7, date("d", $t));
            logic_setOutput($id, 8, date("m", $t));
            logic_setOutput($id, 9, date("Y", $t));
            logic_setOutput($id, 10, date("H:i:s", $t));
            logic_setOutput($id, 11, date("H", $t));
            logic_setOutput($id, 12, date("i", $t));
            logic_setOutput($id, 13, date("s", $t));
            logic_setOutput($id, 14, strval($t - floor($t)));
        }
    }
}

?>
###[/LBS]###


###[EXEC]###
<?

?>
###[/EXEC]###
