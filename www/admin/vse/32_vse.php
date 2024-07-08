###[DEF]###
[name    =Terminschaltuhr]

[folderid=164]
[xsize    =250]
[ysize    =200]

[var1    =0 #root=101]
[var3    =3]
[var5    =1]
[var6    =1]
[var7    =-2]
[var8    =2]
[var9    =5]
[var10    =]
[var11    =1]

[flagText        =0]
[flagKo1        =2]
[flagKo2        =0]
[flagKo3        =1]
[flagPage        =0]
[flagCmd        =0]
[flagDesign        =1]
[flagDynDesign    =1]

[captionKo1        =Steuerungs-KO der Terminschaltuhr (0=Aus, 1=Ein)]
###[/DEF]###


###[PROPERTIES]###
[columns=50,50]
[row]
[var1 = root,2,'Terminschaltuhr',101]

[row=Bedienung]
[var3 = select,1,'Schaltflächen','0#keine|1#Steuerung|2#Termine bearbeiten/hinzufügen|3#Steuerung und Termine bearbeiten/hinzufügen']
[var11= select,1,'Statusanzeige','0#deaktiviert|1#Indikatorfarbe|2#Zusatzhintergrundfarbe 1']

[row=Darstellung]
[var6 = select,1,'Datumsanzeige','0#normale Anzeige|1#Kurzform für Gestern..Übermorgen']
[var5 = select,1,'Vergangene Termine','0#nicht anzeigen|1#kontrastreduziert anzeigen|2#normal anzeigen']

[row]
[var7 = text,1,'Zeitraum: von (Tage ab heute)','']
[var8 = text,1,'Zeitraum: bis (Tage ab heute)','']

[row]
[var10= text,2,'Kopf-/Fusszeilenhöhe (px, leer=Standard)','']

[row=Aktualisierung]
[var9 = select,2,'Aktualisierung per Intervall','0#keine automatische Aktualisierung|1#jede Minute|5#alle 5 Minuten|10#alle 10 Minuten|15#alle 15 Minuten|30#alle 30 Minuten|60#jede Stunde|120#alle 2 Stunden|300#alle 5 Stunden']
###[/PROPERTIES]###


###[ACTIVATION.PHP]###
<?
//gaid auf das Steuerungs-KO der TSU setzen
$tmp = sql_getValues('edomiProject.editAgenda', 'gaid', 'id=' . $item['var1']);
if ($tmp !== false) {
    sql_call("UPDATE edomiLive.visuElement SET gaid=" . $tmp['gaid'] . " WHERE id=" . $item['id']);
}
?>
###[/ACTIVATION.PHP]###


###[EDITOR.PHP]###
<?
$property[0] = sql_getValue('edomiProject.editAgenda', 'name', 'id=' . $item['var1']);
?>
###[/EDITOR.PHP]###


###[EDITOR.JS]###
VSE_VSEID=function(elementId,obj,meta,property,isPreview,koValue) {
var mheight=(obj.dataset.var10!='')?obj.dataset.var10:40;
var n="
<table cellpadding='0' cellspacing='0' border='0' style='position:absolute; left:0; top:0; width:100%; height:100%;'>";
    n+="
    <tr style='height:"+mheight+"px;'>";
        n+="
        <td width='20%' align='center'>&lt;</td>
        ";
        n+="
        <td width='60%' align='center'>
            <div style='max-height:"+mheight+"px; overflow:hidden;'>"+property[0]+"</div>
        </td>
        ";
        n+="
        <td width='20%' align='center'>&gt;</td>
        ";
        n+="
    </tr>
    ";
    n+="
    <tr>
        <td colspan='3' align='center' style='border-top:1px dotted; border-bottom:1px dotted;'>"+((isPreview)?"":"<span class='app2_pseudoElement'>TERMINSCHALTUHR</span>")+"
        </td>
    </tr>
    ";
    if (obj.dataset.var3>0) {
    if (obj.dataset.var11>0) {var color=((obj.dataset.var11==1)?visu_indiColor:"var(--bgc1)");} else {var color="transparent";}
    n+="
    <tr style='height:"+mheight+"px;'>
        <td colspan='3'>";
            n+="
            <table cellpadding='0' cellspacing='0' width='100%' height='100%' border='0'>";
                n+="
                <tr align='center'>";
                    if (obj.dataset.var3 & 1) {n+="
                    <td
                    "+((koValue==0)?"style='background:"+color+";'":"")+">"+graphics_svg_icon(0)+"
        </td>
        ";}
        if (obj.dataset.var3 & 2) {n+="
        <td>"+graphics_svg_icon(2)+"</td>
        ";}
        if (obj.dataset.var3 & 1) {n+="
        <td
        "+((koValue==1)?"style='background:"+color+";'":"")+">"+graphics_svg_icon(1)+"</td>";}
        n+="
    </tr>
    ";
    n+="
</table>";
n+="</td></tr>";
}
n+="</table>";
obj.innerHTML=n;

//Text immer zentrieren, kein Padding
obj.style.textAlign="center";
obj.style.padding="0";

return property[0];
}

###[/EDITOR.JS]###


###[VISU.PHP]###
<?
function PHP_VSE_VSEID($cmd, $json1, $json2)
{
    global $global_weekdays;

    if ($cmd == 'tsuEdit') {
        $ss1 = sql_call("SELECT * FROM edomiLive.agendaData WHERE (id=" . $json1['dbId'] . ")");
        if ($n = sql_result($ss1)) {
            $fd[0] = $n['id'];
            $fd[1] = $n['name'];
            $fd[2] = $n['cmdid'];
            $fd[3] = $n['hour'];
            $fd[4] = $n['minute'];
            $fd[5] = sql_getDate($n['date1']);
            $fd[6] = sql_getDate($n['date2']);
            $fd[7] = $n['step'];
            $fd[8] = $n['unit'];
            $fd[9] = $n['d7'];
            //Formatieren
            $fd[3] = sprintf("%02d", $fd[3]);
            $fd[4] = sprintf("%02d", $fd[4]);
            if (!($fd[7] > 0)) {
                $fd[7] = '';
            }
        } else {
            //Neuer Eintrag
            $fd[0] = -1;
            $fd[1] = '';
            $fd[2] = 0;
            $fd[3] = sprintf("%02d", date('H'));
            $fd[4] = sprintf("%02d", date('i'));
            $fd[5] = date('d.m.Y');
            $fd[6] = '';
            $fd[7] = '';
            $fd[8] = 0;
            $fd[9] = 0;
        }

        $tmp_auxKo = null;
        $tmp = sql_getValue('edomiLive.agenda', 'gaid2', 'id=' . $json1['agendaId']);
        if ($tmp >= 1) {
            $tmp = sql_getValues('edomiLive.RAMko', 'name', 'id=' . $tmp);
            if ($tmp !== false) {
                $tmp['name'] = str_replace('|', '', $tmp['name']);
                $tmp['name'] = str_replace('<', '&lt;', $tmp['name']);
                $tmp['name'] = str_replace('>', '&gt;', $tmp['name']);
                $tmp_auxKo = $tmp['name'] . '|' . $tmp['name'] . '|<s>' . $tmp['name'] . '</s>';
            }
        }

        $tmp_makros = '0#-?-|';
        $ss1 = sql_call("SELECT targetid FROM edomiLive.agendaMacroList WHERE (agendaid=" . $json1['agendaId'] . ") ORDER BY sort ASC, id ASC");
        while ($n = sql_result($ss1)) {
            //Sonderzeichen filtern wegen SELECT-Liste: | und ; sind verboten!
            $tmp = sql_getValues('edomiLive.macro', 'id,name', 'id=' . $n['targetid']);
            if ($tmp !== false) {
                $tmp['name'] = str_replace('|', '', $tmp['name']);
                $tmp['name'] = str_replace('<', '&lt;', $tmp['name']);
                $tmp['name'] = str_replace('>', '&gt;', $tmp['name']);
                $tmp_makros .= $tmp['id'] . '#' . $tmp['name'] . '|';
            }
        }
        sql_close($ss1);
        $tmp_makros = rtrim($tmp_makros, '|');
        ?>
        var n="
        <table cellpadding='0' cellspacing='0' width='100%' height='100%' border='0'>";
            n+="
            <tr valign='bottom'>";
                n+="
                <td>";

                    n+="
                    <table cellpadding='2' cellspacing='0' width='100%' height='100%' border='0' style='max-height:200px;'>";
                        n+="
                        <tr height='20%'>";
                            n+="
                            <td colspan='3' align='left'>"+visuElement_formNewInput("<? echo $json1['elementId']; ?>",1,100)+"</td>
                            ";
                            n+="
                        </tr>
                        ";

                        n+="
                        <tr height='20%'>";
                            n+="
                            <td align='left'>"+visuElement_formNewInput("<? echo $json1['elementId']; ?>",5,10)+"</td>
                            ";
                            n+="
                            <td align='right' style='white-space:nowrap;'>";
                                n+="
                                <table cellpadding='0' cellspacing='0' width='100%' height='100%' border='0' style='table-layout:auto;'>";
                                    n+="
                                    <tr>";
                                        n+="
                                        <td>"+visuElement_formNewInput("<? echo $json1['elementId']; ?>",3,2)+"</td>
                                        ";
                                        n+="
                                        <td width='1' align='center'>&nbsp;:&nbsp;</td>
                                        ";
                                        n+="
                                        <td>"+visuElement_formNewInput("<? echo $json1['elementId']; ?>",4,2)+"</td>
                                        ";
                                        n+="
                                    </tr>
                                    ";
                                    n+="
                                </table>
                                ";
                                n+="
                            </td>
                            ";
                            n+="
                            <td>"+visuElement_formNewCheckmulti("<? echo $json1['elementId']; ?>",9,"<? if (isEmpty($tmp_auxKo)) {
                                    echo 'display:none;';
                                } ?>")+"
                            </td>
                            ";
                            n+="
                        </tr>
                        ";

                        n+="
                        <tr height='20%'>";
                            n+="
                            <td align='left' style='white-space:nowrap;'>"+visuElement_formNewInput("<? echo $json1['elementId']; ?>",7,10)+"</td>
                            ";
                            n+="
                            <td align='left' style='white-space:nowrap;'>"+visuElement_formNewSelect("<? echo $json1['elementId']; ?>",8)+"</td>
                            ";
                            n+="
                            <td align='left' style='white-space:nowrap;'>"+visuElement_formNewInput("<? echo $json1['elementId']; ?>",6,10)+"</td>
                            ";
                            n+="
                        </tr>
                        ";

                        n+="
                        <tr height='20%'>";
                            n+="
                            <td colspan='3' align='right'>"+visuElement_formNewSelect("<? echo $json1['elementId']; ?>",2)+"</td>
                            ";
                            n+="
                        </tr>
                        ";

                        n+="
                        <tr height='20%' align='center'>";
                            <?
                            if ($fd[0] > 0) {
                                ?>
                                n+="
                                <td>
                                    <div class='controlButton'
                                         onClick='visuElement_callPhp(\"tsuDelete\",{elementId:\"<? echo $json1['elementId']; ?>\",agendaId:\"<? echo $json1['agendaId']; ?>\",dbId:\"<? echo $fd[0]; ?>\"},null);'>
                                        Löschen
                                    </div>
                                </td>";
                                     n+="
                                <td>
                                    <div class='controlButton' onClick='VSE_VSEID_ShowInfoEdit(\"<? echo $json1['elementId']; ?>\",true);'>Abbrechen</div>
                                </td>";
                                     n+="
                                <td>
                                    <div class='controlButton'
                                         onClick='visuElement_callPhp(\"tsuSave\",{elementId:\"<? echo $json1['elementId']; ?>\",agendaId:\"<? echo $json1['agendaId']; ?>\",dbId:\"<? echo $fd[0]; ?>\"},visuElement_formGetValues(\"<? echo $json1['elementId']; ?>\"));'>
                                        <b>Übernehmen</b></div>
                                </td>";
                                <?
                            } else {
                                ?>
                                n+="
                                <td>&nbsp;</td>";
                                               n+="
                                <td>
                                    <div class='controlButton' onClick='VSE_VSEID_ShowInfoEdit(\"<? echo $json1['elementId']; ?>\",true);'>Abbrechen</div>
                                </td>";
                                     n+="
                                <td>
                                    <div class='controlButton'
                                         onClick='visuElement_callPhp(\"tsuSave\",{elementId:\"<? echo $json1['elementId']; ?>\",agendaId:\"<? echo $json1['agendaId']; ?>\",dbId:\"<? echo $fd[0]; ?>\"},visuElement_formGetValues(\"<? echo $json1['elementId']; ?>\"));'>
                                        <b>Erstellen</b></div>
                                </td>";
                                <?
                            }
                            ?>
                            n+="
                        </tr>
                        ";
                        n+="
                    </table>
                    ";
                    n+="
                </td>
                ";
                n+="
            </tr>
            ";
            n+="
        </table>";

        var obj=document.getElementById("e-<? echo $json1['elementId']; ?>-edit");
        if (obj) {
        obj.innerHTML=n;

        visuElement_formSetControl("<? echo $json1['elementId']; ?>",1,"Name","<? echo escapeString($fd[1]); ?>");
        visuElement_formSetControl("<? echo $json1['elementId']; ?>",2,"<? echo escapeString($tmp_makros); ?>","<? echo escapeString($fd[2]); ?>");
        visuElement_formSetControl("<? echo $json1['elementId']; ?>",3,"h","<? echo escapeString($fd[3]); ?>");
        visuElement_formSetControl("<? echo $json1['elementId']; ?>",4,"m","<? echo escapeString($fd[4]); ?>");
        visuElement_formSetControl("<? echo $json1['elementId']; ?>",5,"Datum","<? echo escapeString($fd[5]); ?>");
        visuElement_formSetControl("<? echo $json1['elementId']; ?>",6,"Enddatum","<? echo escapeString($fd[6]); ?>");
        visuElement_formSetControl("<? echo $json1['elementId']; ?>",7,"Intervall","<? echo escapeString($fd[7]); ?>");
        visuElement_formSetControl("<? echo $json1['elementId']; ?>",8,"0#Tage|1#Wochen|2#Monate|3#Jahre","<? echo escapeString($fd[8]); ?>");
        visuElement_formSetControl("<? echo $json1['elementId']; ?>",9,"<? echo escapeString($tmp_auxKo); ?>","<? echo escapeString($fd[9]); ?>");

        scrollToTop("e-<? echo $json1['elementId']; ?>-edit");
        }
        <?
    }

    if ($cmd == 'tsuSave') {
        $ok = true;
        if (isEmpty($json2[3])) {
            $json2[3] = 0;
        }
        if (isEmpty($json2[4])) {
            $json2[4] = 0;
        }

        if (strtotime($json2[5]) === false) {
            $ok = false;
        }
        $json2[5] = date('Y-m-d', strtotime($json2[5]));

        if (!isEmpty($json2[6])) {
            if (strtotime($json2[6]) === false) {
                $ok = false;
            }
            $json2[6] = date('Y-m-d', strtotime($json2[6]));
        }

        if ($json1['agendaId'] > 0 && $ok && is_numeric($json2[3]) && $json2[3] >= 0 && $json2[3] <= 23 && is_numeric($json2[4]) && $json2[4] >= 0 && $json2[4] <= 59) {
            sql_save('edomiLive.agendaData', (($json1['dbId'] > 0) ? $json1['dbId'] : null), array(
                'targetid' => $json1['agendaId'],
                'fixed' => 0,
                'name' => "'" . sql_encodeString($json2[1]) . "'",
                'cmdid' => sql_encodeString($json2[2], true),
                'hour' => intval($json2[3]),
                'minute' => intval($json2[4]),
                'date1' => sql_encodeString($json2[5], true),
                'date2' => sql_encodeString($json2[6], true),
                'step' => ((intval($json2[7]) > 0) ? intval($json2[7]) : 0),
                'unit' => sql_nearestNumber($json2[8], '0,1,2,3'),
                'd7' => sql_nearestNumber($json2[9], '0,1,2')
            ));
            ?>
            VSE_VSEID_ShowInfoEdit(<? echo $json1['elementId']; ?>,true);
            <?
        } else {
            ?>
            shakeObj("e-<? echo $json1['elementId']; ?>-edit");
            <?
        }
    }


    if ($cmd == 'tsuDelete') {
        if ($json1['dbId'] > 0) {
            sql_call("DELETE FROM edomiLive.agendaData WHERE (id=" . $json1['dbId'] . ")");
            ?>
            VSE_VSEID_ShowInfoEdit(<? echo $json1['elementId']; ?>,true);
            <?
        }
    }


    if ($cmd == 'tsuInfo') {
        //Auflistung der anstehenden Termine
        ?>
        var n="
        <table cellpadding='2' cellspacing='0' width='100%' border='0' style='table-layout:auto;'>";
            <?
            $t1 = intval($json1['rangeFrom']);
            $t2 = intval($json1['rangeTo']);
            if ($t1 > $t2) {
                $tmp = $t1;
                $t1 = $t2;
                $t2 = $tmp;
            }
            if (abs($t2 - $t1) > 31) {
                $t2 = $t1 + 31;
            }    //max. 31 Tage (Ressourcen sparen)

            for ($t = $t1; $t <= $t2; $t++) {
                PHP_VSE_VSEID_printEvents($json1['agendaId'], $json1['viewMode'], $json1['dateMode'], date('Y-m-d', strtotime('now ' . $t . ' days')));
            }
            ?>
            n+="
        </table>";

        var obj=document.getElementById("e-<? echo $json1['elementId']; ?>-edit");
        if (obj) {
        obj.innerHTML=n;
        document.getElementById("e-<? echo $json1['elementId']; ?>-infotext").innerHTML="<? echo escapeString(sql_getValue('edomiLive.agenda', 'name', 'id=' . $json1['agendaId']), 1); ?>";
        scrollToTop("e-<? echo $json1['elementId']; ?>-edit");
        }
        <?
    }

    if ($cmd == 'tsuInfoEdit') {
        //Auflistung zur Bearbeitung
        ?>
        var n="
        <table cellpadding='2' cellspacing='0' width='100%' border='0' style='table-layout:auto;'>";
            <?
            $tmp_auxKo = false;
            $tmp = sql_getValue('edomiLive.agenda', 'gaid2', 'id=' . $json1['agendaId']);
            if ($tmp >= 1) {
                $tmp = sql_getValues('edomiLive.RAMko', 'name', 'id=' . $tmp);
                if ($tmp !== false) {
                    $tmp_auxKo = $tmp['name'];
                }
            }

            $ss1 = sql_call("SELECT * FROM edomiLive.agendaData WHERE (targetid=" . $json1['agendaId'] . ") ORDER BY id DESC");
            while ($n = sql_result($ss1)) {

                $info1 = '';
                if ($n['step'] > 0) {
                    $info1 = 'alle ' . $n['step'];
                    if ($n['unit'] == 0) {
                        $info1 .= ' Tage';
                    }
                    if ($n['unit'] == 1) {
                        $info1 .= ' Wochen';
                    }
                    if ($n['unit'] == 2) {
                        $info1 .= ' Monate';
                    }
                    if ($n['unit'] == 3) {
                        $info1 .= ' Jahre';
                    }
                    if (!isEmpty($n['date2'])) {
                        $info1 .= ' bis ' . sql_getDate($n['date2']);
                    }
                    $info1 .= '<br>';
                }

                if ($n['fixed'] == 0) {
                    ?>
                    n+="<tr onClick='VSE_VSEID_Edit(\"<? echo $json1['elementId']; ?>\",\"<? echo $n[id]; ?>\");' valign='top'>";
                    <?
                } else {
                    ?>
                    n+="<tr style='opacity:0.75;' valign='top'>";
                    <?
                }

                $status = sql_getValue('edomiLive.macro', 'name', "id='" . $n['cmdid'] . "'");
                ?>
                n+="
                <td align='left'>";
                    n+="
                    <div style='word-break:break-all;'><? echo escapeString($n['name'], 1); ?></div>
                    ";
                    n+="
                    <div style='margin-top:3px; word-break:break-all;'>&gt; <? if (isEmpty($status)) {
                            echo "-?-";
                        } else {
                            echo escapeString($status, 1);
                        } ?></div>
                    ";
                    n+="
                </td>";
                n+="
                <td align='right'>";
                    n+="
                    <div style='white-space:nowrap;'><? echo sql_getDate($n['date1']); ?> / <? echo sprintf("%02d", $n['hour']); ?>
                        :<? echo sprintf("%02d", $n['minute']); ?>&nbsp;Uhr
                        <div>";
                            n+="
                            <div style='margin-top:3px; white-space:nowrap;'><? echo $info1; ?></div>
                            ";
                            n+="
                            <div style='margin-top:3px; word-break:break-all;'><? if ($tmp_auxKo !== false) {
                                    if ($n['d7'] == 1) {
                                        echo escapeString($tmp_auxKo, 1);
                                    } else if ($n['d7'] == 2) {
                                        echo '<s>&nbsp;';
                                        echo escapeString($tmp_auxKo, 1);
                                        echo '&nbsp;</s>';
                                    }
                                } ?>
                                <div>";
                                    n+="
                </td>";
                n+="</tr>";
                         n+="
                <tr>
                    <td colspan='2'>
                        <div style='width:100%; height:1px; border-bottom:1px solid; opacity:0.25;'></div>
                    </td>
                </tr>";
                <?
            }
            ?>
            n+="
        </table>";

        var obj=document.getElementById("e-<? echo $json1['elementId']; ?>-edit");
        if (obj) {
        obj.innerHTML=n;
        document.getElementById("e-<? echo $json1['elementId']; ?>-infotext").innerHTML="<? echo escapeString(sql_getValue('edomiLive.agenda', 'name', 'id=' . $json1['agendaId']), 1); ?>";
        scrollToTop("e-<? echo $json1['elementId']; ?>-edit");
        }
        <?
    }
}

function PHP_VSE_VSEID_printEvents($targetId, $var5, $var6, $d1)
{
    global $global_weekdays;

    //ggf. Kurzform eines Datum generieren (und ggf. Wochentag ergänzen)
    $tmp = sql_getDate($d1);
    if ($var6 == 1 && $tmp == date('d.m.Y', strtotime('now -2 day'))) {
        $tmp = 'Vorgestern';
    } else if ($var6 == 1 && $tmp == date('d.m.Y', strtotime('now -1 day'))) {
        $tmp = 'Gestern';
    } else if ($var6 == 1 && $tmp == date('d.m.Y')) {
        $tmp = 'Heute';
    } else if ($var6 == 1 && $tmp == date('d.m.Y', strtotime('now +1 day'))) {
        $tmp = 'Morgen';
    } else if ($var6 == 1 && $tmp == date('d.m.Y', strtotime('now +2 day'))) {
        $tmp = 'Übermorgen';
    } else {
        $tmp = $global_weekdays[date('N', strtotime($tmp)) - 1] . ', ' . $tmp;
    }

    $ss1 = sql_call("SELECT * FROM edomiLive.agendaData WHERE targetid=" . $targetId . " AND (step=0 OR date2='' OR date2 IS NULL OR date2>='" . $d1 . "') AND
		(
			(step=0 AND date1='" . $d1 . "')
		OR
			(step>0 AND unit=0 AND date1<='" . $d1 . "' AND (DATEDIFF('" . $d1 . "',date1) MOD step)=0)
		OR
			(step>0 AND unit=1 AND date1<='" . $d1 . "' AND (DATEDIFF('" . $d1 . "',date1) MOD (step*7))=0)
		OR
			(step>0 AND unit=2 AND date1<='" . $d1 . "' AND ((TIMESTAMPDIFF(MONTH,date1,'" . $d1 . "') MOD step=0) AND (DATE_ADD(date1,INTERVAL TIMESTAMPDIFF(MONTH,date1,'" . $d1 . "') MONTH)='" . $d1 . "')))
		OR
			(step>0 AND unit=3 AND date1<='" . $d1 . "' AND ((TIMESTAMPDIFF(YEAR,date1,'" . $d1 . "') MOD step=0) AND (DATE_ADD(date1,INTERVAL TIMESTAMPDIFF(YEAR,date1,'" . $d1 . "') YEAR)='" . $d1 . "')))
		)
		ORDER BY hour ASC,minute ASC
	");

    if ($n = sql_result($ss1)) {
        if ($var5 > 0 || strtotime('now') <= strtotime($d1 . ' ' . $n['hour'] . ':' . $n['minute'] . ':59')) {
            ?>
            n+="
            <tr valign='top' style='margin:0; padding:5px;'>
                <td align='left' colspan='2'><? echo $tmp; ?></td>
            </tr>";
            <?
        }

        $ok = false;
        do {
            if ($var5 > 0 || strtotime('now') <= strtotime($d1 . ' ' . $n['hour'] . ':' . $n['minute'] . ':59')) {
                $ok = true;
                ?>
                n+="
                <tr valign='top'
                    style='<? echo(($var5 == 1 && (strtotime('now') > strtotime($d1 . ' ' . $n['hour'] . ':' . $n['minute'] . ':59'))) ? 'opacity:0.75;' : ''); ?> margin:0; padding:5px;'>
                    ";
                    n+="
                    <td align='left' width='1' style='white-space:nowrap;'><? echo sprintf("%02d", $n['hour']); ?>
                        :<? echo sprintf("%02d", $n['minute']); ?></td>
                    ";
                    n+="
                    <td align='left' style='word-break:break-all;'><? echo escapeString($n['name'], 1); ?></td>
                    ";
                    n+="
                </tr>";
                <?
            }
        } while ($n = sql_result($ss1));

        if ($ok) {
            ?>
            n+="
            <tr>
                <td colspan='2'>
                    <div style='width:100%; height:1px; border-bottom:1px solid; opacity:0.25;'></div>
                </td>
            </tr>";
            <?
        }
    }
    sql_close($ss1);
}

?>

###[/VISU.PHP]###


###[VISU.JS]###
VSE_VSEID_CONSTRUCT=function(elementId,obj) {

var mheight=(obj.dataset.var10!='')?obj.dataset.var10:40;

var n="
<table cellpadding='0' cellspacing='0' border='0' style='position:absolute; left:0; top:0; width:100%; height:100%;'>";
    n+="
    <tr style='height:"+mheight+"px;'>";
        n+="
        <td width='20%' align='center' id='e-"+elementId+"-last'>&lt;</td>
        ";
        n+="
        <td width='60%' align='center' id='e-"+elementId+"-info'>
            <div id='e-"+elementId+"-infotext' style='max-height:"+mheight+"px; overflow:hidden;'></div>
        </td>
        ";
        n+="
        <td width='20%' align='center' id='e-"+elementId+"-next'>&gt;</td>
        ";
        n+="
    </tr>
    ";
    n+="
    <tr>
        <td colspan='3' align='center' style='border-top:1px solid;"+((obj.dataset.var3>0)?"border-bottom:1px solid;":"")+"'>
            <div style='position:relative; height:100%;'>
                <div id='e-"+elementId+"-edit' style='position:absolute; top:0; left:0; right:0; bottom:0; overflow-x:hidden; overflow-y:auto;'></div>
            </div>
        </td>
    </tr>
    ";
    if (obj.dataset.var3>0) {
    n+="
    <tr style='height:"+mheight+"px;'>
        <td colspan='3'>";
            n+="
            <table cellpadding='0' cellspacing='0' border='0' style='width:100%; height:100%;'>";
                n+="
                <tr>";
                    if (obj.dataset.var3 & 1) {n+="
                    <td id='e-"+elementId+"-off'>"+graphics_svg_icon(0)+"</td>
                    ";}
                    if (obj.dataset.var3 & 2) {n+="
                    <td id='e-"+elementId+"-add'>"+graphics_svg_icon(2)+"</td>
                    ";}
                    if (obj.dataset.var3 & 1) {n+="
                    <td id='e-"+elementId+"-on'>"+graphics_svg_icon(1)+"</td>
                    ";}
                    n+="
                </tr>
                ";
                n+="
            </table>
            ";
            n+="
        </td>
    </tr>
    ";
    }
    n+="
</table>";
obj.innerHTML=n;

obj.dataset.editmode=0;
VSE_VSEID_ShowInfo(elementId);

visuElement_onClick(document.getElementById("e-"+elementId+"-last"),function(veId,objId){scrollUp("e-"+veId+"-edit");});
visuElement_onClick(document.getElementById("e-"+elementId+"-info"),function(veId,objId){VSE_VSEID_ShowInfo(veId);});
visuElement_onClick(document.getElementById("e-"+elementId+"-next"),function(veId,objId){scrollDown("e-"+veId+"-edit");});

if (obj.dataset.var3 & 1) {
visuElement_onClick(document.getElementById("e-"+elementId+"-off"),function(veId,objId){visuElement_setKoValue(veId,1,0);});
visuElement_onClick(document.getElementById("e-"+elementId+"-on"),function(veId,objId){visuElement_setKoValue(veId,1,1);});
}
if (obj.dataset.var3 & 2) {
visuElement_onClick(document.getElementById("e-"+elementId+"-edit"),function(veId,objId){VSE_VSEID_ShowInfoEdit(veId);},false);
visuElement_onClick(document.getElementById("e-"+elementId+"-add"),function(veId,objId){VSE_VSEID_New(veId);});
}
}

VSE_VSEID_REFRESH=function(elementId,obj,isInit,isRefresh,isLive,isActive,koValue) {
//Text immer zentrieren, kein Padding
obj.style.textAlign="center";
obj.style.padding="0";

if (obj.dataset.var11!=0) {
var s0=document.getElementById("e-"+elementId+"-off");
var s1=document.getElementById("e-"+elementId+"-on");
if (s0 && s1) {
if (koValue==1) {
s0.style.background="none";
s1.style.background=((obj.dataset.var11==1)?visu_indiColor:"var(--bgc1)");
} else {
s0.style.background=((obj.dataset.var11==1)?visu_indiColor:"var(--bgc1)");
s1.style.background="none";
}
}
}
}

VSE_VSEID_ShowInfo=function(elementId) {
var d=document.getElementById("e-"+elementId);

if (d) {
if (d.dataset.var9>0) {
visuElement_setTimeout(elementId,1,d.dataset.var9*60000,function(id){VSE_VSEID_ShowInfo(id);});
}
d.dataset.editmode=0;
visuElement_callPhp("tsuInfo",{elementId:elementId,agendaId:d.dataset.var1,viewMode:d.dataset.var5,dateMode:d.dataset.var6,rangeFrom:d.dataset.var7,rangeTo:d.dataset.var8},null);
}
}

VSE_VSEID_ShowInfoEdit=function(elementId,force) {
var d=document.getElementById("e-"+elementId);
if (d) {
if (!force && d.dataset.editmode==1) {return;}
if (d.dataset.var9>0) {visuElement_clearTimeout(elementId,1);}
d.dataset.editmode=1;
visuElement_callPhp("tsuInfoEdit",{elementId:elementId,agendaId:d.dataset.var1},null);
}
}

VSE_VSEID_New=function(elementId) {
var d=document.getElementById("e-"+elementId);
if (d) {
if (d.dataset.var9>0) {visuElement_clearTimeout(elementId,1);}
d.dataset.editmode=1;
if (d.dataset.var3 & 2) {
visuElement_callPhp("tsuEdit",{elementId:elementId,agendaId:d.dataset.var1,dbId:-1},null);
}
}
}

VSE_VSEID_Edit=function(elementId,id) {
var d=document.getElementById("e-"+elementId);
if (d) {
if (d.dataset.var9>0) {visuElement_clearTimeout(elementId,1);}
if (d.dataset.var3 & 2) {
visuElement_callPhp("tsuEdit",{elementId:elementId,agendaId:d.dataset.var1,dbId:id},null);
}
}
}
###[/VISU.JS]###


###[HELP]###
Das Visuelement "Terminschaltuhr" stellt eine
<link>konfigurierte Terminschaltuhr***1000-101</link> in der Visualisierung dar und ermöglicht das Steuern der Terminschaltuhr, sowie das Hinzufügen und Bearbeiten von Terminen.

<b>Hinweis:</b>
Die Terminschaltuhr arbeitet unabhängig von diesem Visuelement. Das Visuelement ist also nicht erforderlich, um eine Terminschaltuhr zu nutzen. Das Visuelement "Terminschaltuhr" ermöglicht jedoch das Hinzufügen und Bearbeiten von Terminen zur Laufzeit.

<h2>Spezifische Eigenschaften</h2>
Für weitere Einstellungen und Optionen siehe:
<link>Allgemeine Informationen zu Visuelementen***1002</link>

<ul>
    <li>Terminschaltuhr: Auswahl der
        <link>
        konfigurierten Terminschaltuhr***1000-101</link>, die angezeigt und bearbeitet werden soll
    </li>

    <li>
        Schaltflächen: legt fest, ob die Terminschaltuhr aktiviert/deaktiviert werden kann und ob Termine bearbeitet/hinzugefügt werden dürfen
        <ul>
            <li>Hinweis: die Schaltflächen werden mit Symbolen beschriftet (von links nach rechts: deaktivieren, hinzufügen, aktivieren)</li>
            <li>Wichtig: Die Terminschaltuhr kann unabhängig davon stets über das entsprechende KO gesteuert werden, Termine können bereits in der Konfiguration
                der Terminschaltuhr definiert werden.
            </li>
        </ul>
    </li>

    <li>
        Statusanzeige: legt fest, ob und wie der aktuelle Status (KO1) angezeigt wird
        <ul>
            <li>deaktiviert: keine Statusanzeige</li>
            <li>Indikatorfarbe: die entsprechende Schaltfläche wird mit der
                <link>
                Indikatorfarbe***1000-21</link> hinterlegt
            </li>
            <li>Zusatzhintergrundfarbe 1: die entsprechende Schaltfläche wird mit der
                <link>
                Zusatzhintergrundfarbe 1***1003</link> hinterlegt
            </li>
        </ul>
    </li>

    <li>
        Datumsanzeige: optional wird ein Termindatum in der Terminauflistung ggf. mit "vorgestern" bis "übermorgen" ersetzt (alle anderen Zeitpunkte werden in
        der Form "Wochentag, tt.mm.jjjj" angezeigt)
    </li>

    <li>
        Vergangene Termine: legt fest, ob und wie bereits vergangene Termine (innerhalb des gewählten Zeitraums) angezeigt werden
        <ul>
            <li>nicht anzeigen: vergangene Termine (und die Datumsangabe) werden nicht angezeigt</li>
            <li>kontrastreduziert anzeigen: vergangene Termine werden kontrastreduziert angezeigt</li>
            <li>normal anzeigen: vergangene Termine werden unverändert angezeigt</li>
            <li>Hinweis: Wir der Zeitpunkt eines Termin erreicht (z.B. 17:00:00 Uhr), wird der Termin unabhängig von den o.g. Einstellungen <i>mindestens</i>
                noch 1 Minute lang angezeigt (z.B. bis 17:00:59). Die genaue Anzeigedauer hängt von der Einstellung "Aktualisierung" ab (s.u.), bzw. vom
                Zeitpunkt einer manuellen Aktualisierung.
            </li>
        </ul>
    </li>

    <li>
        Zeitraum (von/bis): legt fest, welcher Bereich (in Tagen) relativ zum aktuellen Tag in der Terminauflistung angezeigt werden soll
        <ul>
            <li>die Angabe "-2" bis "2" zeigt den Zeitraum "vorgestern" bis "übermorgen" an (2 Tage zurück bis 2 Tage voraus)</li>
            <li>die Angabe "0" bis "7" zeigt die nächsten 7 Tage einschließlich heute an (0 Tage zurück bis 7 Tage voraus)</li>
            <li>Hinweis: Der maximal anzeigbare Zeitraum beträgt 31 Tage. Wird dieser Zeitraum überschritten, wird automatisch der Zeitraum "Beginn + 31 Tage"
                angezeigt.
            </li>
        </ul>
    </li>

    <li>Kopf-/Fusszeilenhöhe: legt optional die Höhe der Kopf- und Fusszeile in Pixeln fest</li>

    <li>
        Aktualisierung per Intervall: legt fest, ob und wie häufig die Terminauflistung automatisch aktualisiert werden soll
        <ul>
            <li>Hinweis: Die Terminauflistung wird unabhängig von dieser Einstellung bei jedem Seitenaufruf und manuell durch das Anklicken der Titelleiste
                (s.u.) aktualisiert.
            </li>
        </ul>
    </li>
</ul>


<h2>Kommunikationsobjekte</h2>
Dieses Visuelement kann (optional) folgende Kommunikationsobjekte (KO) verwalten:

<ul>
    <li>
        KO1: Steuerungs-KO der Terminschaltuhr
        <ul>
            <li>dieses KO ist stets mit dem Steuerungs-KO der zugewiesenen Terminschaltuhr verknüpft</li>
            <li>dieser KO-Wert wird zur <span style="background:#e0ffe0;">Steuerung und Beschriftung</span> verwendet (Designs, Funktionen und Formeln)</li>
            <li>dieser KO-Wert wird zudem beim Bedienen der Terminschaltuhr in der Visualisierung entsprechend gesetzt (Terminschaltuhr: Aus/Ein)</li>
        </ul>
    </li>

    <li>
        KO3: Steuerung des dynamischen Designs
        <ul>
            <li>dieser KO-Wert wird ausschließlich zur Steuerung eines
                <link>
                dynamischen Designs***1003</link> verwendet
            </li>
            <li>wenn dieses KO angegeben wurde, wird ein dynamisches Design durch dieses <i>KO3</i> gesteuert</li>
            <li>wenn dieses KO nicht angegeben wurde, wird ein dynamisches Design durch das <i>KO1</i> gesteuert</li>
        </ul>
    </li>
</ul>


<h2>Besonderheiten</h2>
<ul>
    <li>Bereits in der
        <link>
        Konfiguration der Terminschaltuhr***1000-101</link> können Termine fest vorgegeben werden - diese sind in der Visualisierung nicht veränderbar!
    </li>
    <li>Designs: Innenabstand und Textausrichtung werden ignoriert</li>
    <li>Seitensteuerung/Befehle stehen nicht zu Verfügung</li>
</ul>


<h2>Bedienung in der Visualisierung</h2>
Standardmäßig zeigt das Visuelement alle Termine des konfigurierten Zeitraums (s.o.) an. Durch Anklicken der Terminauflistung wird in die Bearbeitungsansicht (s.u.) gewechselt.
Das Visuelement ist in 3 Teilbereiche gegliedert (von oben nach unten):

<ul>
    <li>
        Titelleiste:
        <ul>
            <li>hier werden Pfeil-Schaltflächen zum Blättern durch die Termine (Scrollen), sowie der Name der
                <link>
                Terminschaltuhr***1000-101</link> angezeigt
            </li>
            <li>ein Klick auf den Namen der Terminschaltuhr aktualisiert den Inhalt der Terminauflistung, bzw. wechselt aus der Bearbeitungsansicht (s.u.)
                zurück zur Terminauflistung
            </li>
        </ul>
    </li>

    <li>
        Termine (Auflistung):
        <ul>
            <li>hier werden alle Termine des konfigurierten Zeitraums (s.o.) in der Vordergrundfarbe des Visuelements aufgelistet</li>
            <li>durch Anklicken dieses Bereichs wird in die Bearbeitungsansicht (s.u.) gewechselt</li>
        </ul>
    </li>

    <li>
        Steuerungs-Schaltflächen:
        <ul>
            <li>"aus": deaktiviert die Terminschaltuhr (das Steuerungs-KO der Terminschaltuhr wird auf den Wert "0" gesetzt)</li>
            <li>"+": fügt einen neuen Termin hinzu</li>
            <li>"ein": aktiviert die Terminschaltuhr (das Steuerungs-KO der Terminschaltuhr wird auf den Wert "1" gesetzt)</li>
        </ul>
    </li>
</ul>


Durch Anklicken der Terminauflistung wird in die Bearbeitungsansicht gewechselt, durch Anklicken der Titelleiste wird wieder zurück zur Terminauflistung gewechselt:

<ul>
    <li>
        Titelleiste:
        <ul>
            <li>hier werden Pfeil-Schaltflächen zum Blättern durch die konfigurierten Termine (Scrollen), sowie der Name der
                <link>
                Terminschaltuhr***1000-101</link> angezeigt
            </li>
            <li>ein Klick auf den Namen der Terminschaltuhr wechselt aus der Bearbeitungsansicht zurück zur Terminauflistung (s.o.)</li>
        </ul>
    </li>

    <li>
        Termine:
        <ul>
            <li>hier werden alle konfigurierten Termine dieser Terminschaltuhr angezeigt</li>
            <li>durch Anklicken eines Termins kann dieser bearbeitet oder gelöscht werden (siehe auch:
                <link>
                Konfiguration einer Terminschaltuhr***1000-101</link>)
            </li>
            <li>vorkonfigurierte (fixe) Termine werden kontrastreduziert angezeigt (diese Termine sind in der Visualisierung nicht veränderbar)</li>
        </ul>
    </li>

    <li>Steuerungs-Schaltflächen: (s.o.)</li>
</ul>
###[/HELP]###


