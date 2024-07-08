###[DEF]###
[name        =Vorzeichen                ]

[e#1 TRIGGER=    ]

[a#1        =&gt;0                ]
[a#2        ==0                    ]
[a#3        =&lt;0                ]
###[/DEF]###


###[HELP]###
Dieser Baustein setzt A1, A2 oder A3 in Abhängigkeit des Wertes an E1:

<ul>
    <li>ist der (numerische) Wert an E1 &gt;0, wird A1=1 gesetzt</li>
    <li>ist der (numerische) Wert an E1 =0, wird A2=1 gesetzt</li>
    <li>ist der (numerische) Wert an E1 &lt;0, wird A3=1 gesetzt</li>
</ul>

E1: jedes neue Telegramm &ne;[leer] triggert den Baustein
A1: falls E1&gt;0 ist, wird dieser Ausgang auf 1 gesetzt
A2: falls E1=0 ist, wird dieser Ausgang auf 1 gesetzt
A3: falls E1&lt;0 ist, wird dieser Ausgang auf 1 gesetzt
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id)
{
    if ($E = logic_getInputs($id)) {
        if ($E[1]['refresh'] == 1 && !isEmpty($E[1]['value'])) {
            if ($E[1]['value'] > 0) {

                logic_setOutput($id, 1, 1);

            } else if ($E[1]['value'] == 0) {

                logic_setOutput($id, 2, 1);

            } else if ($E[1]['value'] < 0) {

                logic_setOutput($id, 3, 1);

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
