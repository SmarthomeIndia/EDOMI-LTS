###[DEF]###
[name    =Zeitschaltuhr]

[folderid=164]
[xsize    =250]
[ysize    =200]

[var1    =0 #root=100]
[var2    =0]
[var3    =3]
[var4    =]
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

[captionKo1        =Steuerungs-KO der Zeitschaltuhr (0=Aus, 1=Ein)]
###[/DEF]###


###[PROPERTIES]###
[columns=50,50]
[row]
[var1 = root,2,'Zeitschaltuhr',100]

[row=Bedienung]
[var3 = select,2,'Schaltflächen','0#keine|1#Steuerung|2#Schaltzeiten bearbeiten/hinzufügen|3#Steuerung und Schaltzeiten bearbeiten/hinzufügen']

[row]
[var11= select,2,'Statusanzeige','0#deaktiviert|1#Indikatorfarbe|2#Zusatzhintergrundfarbe 1']

[row=Darstellung]
[var2 = select,1,'Sortierung','0#ID|1#Wochentag/Uhrzeit']
[var4 = text,1,'Analoguhr (px, leer=ohne)','']

[row]
[var10= text,2,'Kopf-/Fusszeilenhöhe (px, leer=Standard)','']
###[/PROPERTIES]###


###[ACTIVATION.PHP]###
<?
//gaid auf das Steuerungs-KO der ZSU setzen
$tmp = sql_getValues('edomiProject.editTimer', 'gaid', 'id=' . $item['var1']);
if ($tmp !== false) {
    sql_call("UPDATE edomiLive.visuElement SET gaid=" . $tmp['gaid'] . " WHERE id=" . $item['id']);
}
?>
###[/ACTIVATION.PHP]###


###[EDITOR.PHP]###
<?
$property[0] = sql_getValue('edomiProject.editTimer', 'name', 'id=' . $item['var1']);
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
        <td colspan='3' align='center' style='border-top:1px dotted; border-bottom:1px dotted;'>"+((isPreview)?"":"<span class='app2_pseudoElement'>ZEITSCHALTUHR</span>")+"
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

    if ($cmd == 'zsuEdit') {
        $ss1 = sql_call("SELECT * FROM edomiLive.timerData WHERE (id=" . $json1['dbId'] . ")");
        if ($n = sql_result($ss1)) {
            $fd[0] = $n['id'];
            $fd[1] = $n['d0'];
            $fd[2] = $n['d1'];
            $fd[3] = $n['d2'];
            $fd[4] = $n['d3'];
            $fd[5] = $n['d4'];
            $fd[6] = $n['d5'];
            $fd[7] = $n['d6'];
            $fd[8] = $n['hour'];
            $fd[9] = $n['minute'];
            $fd[10] = $n['day1'];
            $fd[11] = $n['month1'];
            $fd[12] = $n['year1'];
            $fd[13] = $n['day2'];
            $fd[14] = $n['month2'];
            $fd[15] = $n['year2'];
            $fd[16] = $n['cmdid'];
            $fd[17] = $n['mode'];
            $fd[18] = $n['d7'];
            //Formatieren
            $fd[8] = sprintf("%02d", $fd[8]);
            $fd[9] = sprintf("%02d", $fd[9]);
            if (isEmpty($fd[10]) || $fd[10] < 1 || $fd[10] > 31) {
                $fd[10] = '';
            } else {
                $fd[10] = sprintf("%02d", $fd[10]);
            }
            if (isEmpty($fd[11]) || $fd[11] < 1 || $fd[11] > 12) {
                $fd[11] = '';
            } else {
                $fd[11] = sprintf("%02d", $fd[11]);
            }
            if (isEmpty($fd[12]) || $fd[12] < 0 || $fd[12] > 9999) {
                $fd[12] = '';
            } else {
                $fd[12] = sprintf("%04d", $fd[12]);
            }
            if (isEmpty($fd[13]) || $fd[13] < 1 || $fd[13] > 31) {
                $fd[13] = '';
            } else {
                $fd[13] = sprintf("%02d", $fd[13]);
            }
            if (isEmpty($fd[14]) || $fd[14] < 1 || $fd[14] > 12) {
                $fd[14] = '';
            } else {
                $fd[14] = sprintf("%02d", $fd[14]);
            }
            if (isEmpty($fd[15]) || $fd[15] < 0 || $fd[15] > 9999) {
                $fd[15] = '';
            } else {
                $fd[15] = sprintf("%04d", $fd[15]);
            }
        } else {
            //Neuer Eintrag
            $fd[0] = -1;
            $fd[1] = 0;
            $fd[2] = 0;
            $fd[3] = 0;
            $fd[4] = 0;
            $fd[5] = 0;
            $fd[6] = 0;
            $fd[7] = 0;
            $fd[8] = '';
            $fd[9] = '';
            $fd[10] = '';
            $fd[11] = '';
            $fd[12] = '';
            $fd[13] = '';
            $fd[14] = '';
            $fd[15] = '';
            $fd[16] = 0;
            $fd[17] = 0;
            $fd[18] = 0;
        }
        sql_close($ss1);

        $tmp_auxKo = null;
        $tmp = sql_getValue('edomiLive.timer', 'gaid2', 'id=' . $json1['timerId']);
        if ($tmp >= 1) {
            $tmp = sql_getValues('edomiLive.RAMko', 'name', 'id=' . $tmp);
            if ($tmp !== false) {
                $tmp['name'] = str_replace('|', '', $tmp['name']);        //Trenner rausfiltern
                $tmp['name'] = str_replace('<', '&lt;', $tmp['name']);    //HTML-Tags rausfiltern
                $tmp['name'] = str_replace('>', '&gt;', $tmp['name']);
                $tmp_auxKo = $tmp['name'] . '|' . $tmp['name'] . '|<s>' . $tmp['name'] . '</s>';
            }
        }

        $tmp_makros = '0#-?-|';
        $ss1 = sql_call("SELECT targetid FROM edomiLive.timerMacroList WHERE (timerid=" . $json1['timerId'] . ") ORDER BY sort ASC, id ASC");
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
                            <td align='right' style='white-space:nowrap;'>";
                                n+="
                                <table cellpadding='0' cellspacing='0' width='100%' height='100%' border='0' style='table-layout:auto;'>";
                                    n+="
                                    <tr>";
                                        n+="
                                        <td>"+visuElement_formNewInput("<? echo $json1['elementId']; ?>",8,2)+"</td>
                                        ";
                                        n+="
                                        <td width='1' align='center'>&nbsp;:&nbsp;</td>
                                        ";
                                        n+="
                                        <td>"+visuElement_formNewInput("<? echo $json1['elementId']; ?>",9,2)+"</td>
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
                            <td colspan='2'>"+visuElement_formNewCheckmulti("<? echo $json1['elementId']; ?>",18,"<? if (isEmpty($tmp_auxKo)) {
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
                            <td colspan='3' align='left'>";
                                n+="
                                <table cellpadding='0' cellspacing='0' width='100%' height='100%' border='0'>";
                                    n+="
                                    <tr valign='bottom'>";
                                        n+="
                                        <td width='10%'>"+visuElement_formNewCheck("<? echo $json1['elementId']; ?>",1)+"</td>
                                        ";
                                        n+="
                                        <td width='1%' align='center'>&nbsp;</td>
                                        ";
                                        n+="
                                        <td width='10%'>"+visuElement_formNewCheck("<? echo $json1['elementId']; ?>",2)+"</td>
                                        ";
                                        n+="
                                        <td width='1%' align='center'>&nbsp;</td>
                                        ";
                                        n+="
                                        <td width='10%'>"+visuElement_formNewCheck("<? echo $json1['elementId']; ?>",3)+"</td>
                                        ";
                                        n+="
                                        <td width='1%' align='center'>&nbsp;</td>
                                        ";
                                        n+="
                                        <td width='10%'>"+visuElement_formNewCheck("<? echo $json1['elementId']; ?>",4)+"</td>
                                        ";
                                        n+="
                                        <td width='1%' align='center'>&nbsp;</td>
                                        ";
                                        n+="
                                        <td width='10%'>"+visuElement_formNewCheck("<? echo $json1['elementId']; ?>",5)+"</td>
                                        ";
                                        n+="
                                        <td width='1%' align='center'>&nbsp;</td>
                                        ";
                                        n+="
                                        <td width='10%'>"+visuElement_formNewCheck("<? echo $json1['elementId']; ?>",6)+"</td>
                                        ";
                                        n+="
                                        <td width='1%' align='center'>&nbsp;</td>
                                        ";
                                        n+="
                                        <td width='10%'>"+visuElement_formNewCheck("<? echo $json1['elementId']; ?>",7)+"</td>
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
                        </tr>
                        ";

                        n+="
                        <tr height='20%'>";
                            n+="
                            <td align='left' colspan='3' style='white-space:nowrap;'>";
                                n+="
                                <table cellpadding='0' cellspacing='0' width='100%' height='100%' border='0' style='table-layout:auto;'>";
                                    n+="
                                    <tr>";
                                        n+="
                                        <td width='10%'>"+visuElement_formNewInput("<? echo $json1['elementId']; ?>",10,2)+"</td>
                                        ";
                                        n+="
                                        <td width='1%' align='center'>&middot;</td>
                                        ";
                                        n+="
                                        <td width='10%'>"+visuElement_formNewInput("<? echo $json1['elementId']; ?>",11,2)+"</td>
                                        ";
                                        n+="
                                        <td width='1%' align='center'>&middot;</td>
                                        ";
                                        n+="
                                        <td width='22%'>"+visuElement_formNewInput("<? echo $json1['elementId']; ?>",12,4)+"</td>
                                        ";
                                        n+="
                                        <td width='1%' align='center'>&nbsp;</td>
                                        ";
                                        n+="
                                        <td width='10%'>"+visuElement_formNewCheck("<? echo $json1['elementId']; ?>",17)+"</td>
                                        ";
                                        n+="
                                        <td width='1%' align='center'>&nbsp;</td>
                                        ";
                                        n+="
                                        <td width='10%'>"+visuElement_formNewInput("<? echo $json1['elementId']; ?>",13,2)+"</td>
                                        ";
                                        n+="
                                        <td width='1%' align='center'>&middot;</td>
                                        ";
                                        n+="
                                        <td width='10%'>"+visuElement_formNewInput("<? echo $json1['elementId']; ?>",14,2)+"</td>
                                        ";
                                        n+="
                                        <td width='1%' align='center'>&middot;</td>
                                        ";
                                        n+="
                                        <td width='22%'>"+visuElement_formNewInput("<? echo $json1['elementId']; ?>",15,4)+"</td>
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
                        </tr>
                        ";

                        n+="
                        <tr height='20%'>";
                            n+="
                            <td colspan='3' align='left'>"+visuElement_formNewSelect("<? echo $json1['elementId']; ?>",16)+"</td>
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
                                         onClick='visuElement_callPhp(\"zsuDelete\",{elementId:\"<? echo $json1['elementId']; ?>\",timerId:\"<? echo $json1['timerId']; ?>\",dbId:\"<? echo $fd[0]; ?>\"},null);'>
                                        Löschen
                                    </div>
                                </td>";
                                     n+="
                                <td>
                                    <div class='controlButton' onClick='VSE_VSEID_ShowInfo(\"<? echo $json1['elementId']; ?>\");'>Abbrechen</div>
                                </td>";
                                     n+="
                                <td>
                                    <div class='controlButton'
                                         onClick='visuElement_callPhp(\"zsuSave\",{elementId:\"<? echo $json1['elementId']; ?>\",timerId:\"<? echo $json1['timerId']; ?>\",dbId:\"<? echo $fd[0]; ?>\"},visuElement_formGetValues(\"<? echo $json1['elementId']; ?>\"));'>
                                        <b>Übernehmen</b></div>
                                </td>";
                                <?
                            } else {
                                ?>
                                n+="
                                <td>&nbsp;</td>";
                                               n+="
                                <td>
                                    <div class='controlButton' onClick='VSE_VSEID_ShowInfo(\"<? echo $json1['elementId']; ?>\");'>Abbrechen</div>
                                </td>";
                                     n+="
                                <td>
                                    <div class='controlButton'
                                         onClick='visuElement_callPhp(\"zsuSave\",{elementId:\"<? echo $json1['elementId']; ?>\",timerId:\"<? echo $json1['timerId']; ?>\",dbId:\"<? echo $fd[0]; ?>\"},visuElement_formGetValues(\"<? echo $json1['elementId']; ?>\"));'>
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

        visuElement_formSetControl("<? echo $json1['elementId']; ?>",1,"<? echo escapeString(substr($global_weekdays[0], 0, 2)); ?>","<? echo escapeString($fd[1]); ?>");
        visuElement_formSetControl("<? echo $json1['elementId']; ?>",2,"<? echo escapeString(substr($global_weekdays[1], 0, 2)); ?>","<? echo escapeString($fd[2]); ?>");
        visuElement_formSetControl("<? echo $json1['elementId']; ?>",3,"<? echo escapeString(substr($global_weekdays[2], 0, 2)); ?>","<? echo escapeString($fd[3]); ?>");
        visuElement_formSetControl("<? echo $json1['elementId']; ?>",4,"<? echo escapeString(substr($global_weekdays[3], 0, 2)); ?>","<? echo escapeString($fd[4]); ?>");
        visuElement_formSetControl("<? echo $json1['elementId']; ?>",5,"<? echo escapeString(substr($global_weekdays[4], 0, 2)); ?>","<? echo escapeString($fd[5]); ?>");
        visuElement_formSetControl("<? echo $json1['elementId']; ?>",6,"<? echo escapeString(substr($global_weekdays[5], 0, 2)); ?>","<? echo escapeString($fd[6]); ?>");
        visuElement_formSetControl("<? echo $json1['elementId']; ?>",7,"<? echo escapeString(substr($global_weekdays[6], 0, 2)); ?>","<? echo escapeString($fd[7]); ?>");
        visuElement_formSetControl("<? echo $json1['elementId']; ?>",8,"h","<? echo escapeString($fd[8]); ?>");
        visuElement_formSetControl("<? echo $json1['elementId']; ?>",9,"m","<? echo escapeString($fd[9]); ?>");
        visuElement_formSetControl("<? echo $json1['elementId']; ?>",10,"T","<? echo escapeString($fd[10]); ?>");
        visuElement_formSetControl("<? echo $json1['elementId']; ?>",11,"M","<? echo escapeString($fd[11]); ?>");
        visuElement_formSetControl("<? echo $json1['elementId']; ?>",12,"J","<? echo escapeString($fd[12]); ?>");
        visuElement_formSetControl("<? echo $json1['elementId']; ?>",13,"T","<? echo escapeString($fd[13]); ?>");
        visuElement_formSetControl("<? echo $json1['elementId']; ?>",14,"M","<? echo escapeString($fd[14]); ?>");
        visuElement_formSetControl("<? echo $json1['elementId']; ?>",15,"J","<? echo escapeString($fd[15]); ?>");
        visuElement_formSetControl("<? echo $json1['elementId']; ?>",16,"<? echo escapeString($tmp_makros); ?>","<? echo escapeString($fd[16]); ?>");
        visuElement_formSetControl("<? echo $json1['elementId']; ?>",17,"&gt;","<? echo escapeString($fd[17]); ?>");
        visuElement_formSetControl("<? echo $json1['elementId']; ?>",18,"<? echo escapeString($tmp_auxKo); ?>","<? echo escapeString($fd[18]); ?>");

        scrollToTop("e-<? echo $json1['elementId']; ?>-edit");
        }
        <?
    }

    if ($cmd == 'zsuSave') {
        if (isEmpty($json2[10]) || $json2[10] < 1 || $json2[10] > 31) {
            $json2[10] = null;
        }
        if (isEmpty($json2[11]) || $json2[11] < 1 || $json2[11] > 12) {
            $json2[11] = null;
        }
        if (isEmpty($json2[12]) || $json2[12] < 0 || $json2[12] > 9999) {
            $json2[12] = null;
        }
        if (isEmpty($json2[13]) || $json2[13] < 1 || $json2[13] > 31) {
            $json2[13] = null;
        }
        if (isEmpty($json2[14]) || $json2[14] < 1 || $json2[14] > 12) {
            $json2[14] = null;
        }
        if (isEmpty($json2[15]) || $json2[15] < 0 || $json2[15] > 9999) {
            $json2[15] = null;
        }

        //mind. 1 Tag angegeben?
        $dayOk = false;
        for ($t = 1; $t <= 7; $t++) {
            if ($json2[$t] == 1) {
                $dayOk = true;
                break;
            }
        }

        if ($json1['timerId'] > 0 && $dayOk && is_numeric($json2[8]) && $json2[8] >= 0 && $json2[8] <= 23 && is_numeric($json2[9]) && $json2[9] >= 0 && $json2[9] <= 59) {
            sql_save('edomiLive.timerData', (($json1['dbId'] > 0) ? $json1['dbId'] : null), array(
                'targetid' => $json1['timerId'],
                'fixed' => 0,
                'd0' => (($json2[1] > 0) ? 1 : 0),
                'd1' => (($json2[2] > 0) ? 1 : 0),
                'd2' => (($json2[3] > 0) ? 1 : 0),
                'd3' => (($json2[4] > 0) ? 1 : 0),
                'd4' => (($json2[5] > 0) ? 1 : 0),
                'd5' => (($json2[6] > 0) ? 1 : 0),
                'd6' => (($json2[7] > 0) ? 1 : 0),
                'd7' => sql_nearestNumber($json2[18], '0,1,2'),
                'hour' => intval($json2[8]),
                'minute' => intval($json2[9]),
                'day1' => sql_encodeString($json2[10], true),
                'month1' => sql_encodeString($json2[11], true),
                'year1' => sql_encodeString($json2[12], true),
                'day2' => sql_encodeString($json2[13], true),
                'month2' => sql_encodeString($json2[14], true),
                'year2' => sql_encodeString($json2[15], true),
                'mode' => (($json2[17] > 0) ? 1 : 0),
                'cmdid' => sql_encodeString($json2[16], true)
            ));
            ?>
            VSE_VSEID_ShowInfo(<? echo $json1['elementId']; ?>);
            <?
        } else {
            ?>
            shakeObj("e-<? echo $json1['elementId']; ?>-edit");
            <?
        }
    }

    if ($cmd == 'zsuDelete') {
        if ($json1['dbId'] > 0) {
            sql_call("DELETE FROM edomiLive.timerData WHERE (id=" . $json1['dbId'] . ")");
            ?>
            VSE_VSEID_ShowInfo(<? echo $json1['elementId']; ?>);
            <?
        }
    }

    if ($cmd == 'zsuInfo') {
        ?>
        var n="
        <table cellpadding='2' cellspacing='0' width='100%' border='0' style='table-layout:auto;'>";
            <?
            $tmp_auxKo = false;
            $tmp = sql_getValue('edomiLive.timer', 'gaid2', 'id=' . $json1['timerId']);
            if ($tmp >= 1) {
                $tmp = sql_getValues('edomiLive.RAMko', 'name', 'id=' . $tmp);
                if ($tmp !== false) {
                    $tmp_auxKo = $tmp['name'];
                }
            }

            if ($json1['sort'] == 0) {
                $ss1 = sql_call("SELECT * FROM edomiLive.timerData WHERE (targetid=" . $json1['timerId'] . ") ORDER BY id DESC");
            } else {
                //Sortierung: erstes Vorkommen eines Wochentages (und Uhrzeit)
                $ss1 = sql_call("SELECT * FROM edomiLive.timerData WHERE (targetid=" . $json1['timerId'] . ") ORDER BY LENGTH(BIN(CONCAT(d6*1+d5*2+d4*4+d3*8+d2*16+d1*32+d0*64))) DESC,hour ASC,minute ASC,id DESC");
            }
            while ($n = sql_result($ss1)) {

                $days = '';
                for ($t = 0; $t < 7; $t++) {
                    if ($n['d' . $t] == 1) {
                        $days .= "<span style='border-bottom:1px solid;'>" . substr($global_weekdays[$t], 0, 2) . "</span>&nbsp;";
                    } else {
                        $days .= "<span style='border-bottom:1px solid transparent; opacity:0.75;'>" . substr($global_weekdays[$t], 0, 2) . "</span>&nbsp;";
                    }
                }

                $noDate = true;
                if (isEmpty($n['day1'])) {
                    $n['day1'] = "&middot;&middot;";
                } else {
                    $n['day1'] = sprintf("%02d", $n['day1']);
                    $noDate = false;
                }
                if (isEmpty($n['month1'])) {
                    $n['month1'] = "&middot;&middot;";
                } else {
                    $n['month1'] = sprintf("%02d", $n['month1']);
                    $noDate = false;
                }
                if (isEmpty($n['year1'])) {
                    $n['year1'] = "&middot;&middot;&middot;&middot;";
                } else {
                    $n['year1'] = sprintf("%04d", $n['year1']);
                    $noDate = false;
                }
                if (isEmpty($n['day2'])) {
                    $n['day2'] = "&middot;&middot;";
                } else {
                    $n['day2'] = sprintf("%02d", $n['day2']);
                    $noDate = false;
                }
                if (isEmpty($n['month2'])) {
                    $n['month2'] = "&middot;&middot;";
                } else {
                    $n['month2'] = sprintf("%02d", $n['month2']);
                    $noDate = false;
                }
                if (isEmpty($n['year2'])) {
                    $n['year2'] = "&middot;&middot;&middot;&middot;";
                } else {
                    $n['year2'] = sprintf("%04d", $n['year2']);
                    $noDate = false;
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
                if ($json1['clock'] >= 1) {

                    ?>
                    n+="
                    <td valign='middle' width='<? echo $json1['clock']; ?>'>";
                        var tmp=graphics_svg_clock("var(--fgc0)",<? echo $json1['clock']; ?>,<? echo $json1['clock']; ?>
                        ,{mode:1,pointerWidth:0,scalaOpacity:0,scalaSize:0,scalaWidth:0,scalaMode:0,fulldayOpacity:0,minuteSize:0,hourSize:0,contourWidth:"0"},"<? echo sprintf("%02d", $n['hour']); ?>
                        :<? echo sprintf("%02d", $n['minute']); ?>");
                        n+="
                        <svg style='display:block; width:<? echo $json1['clock']; ?>px; height:<? echo $json1['clock']; ?>px;'>"+tmp+"</svg>
                        ";
                        n+="
                    </td>";
                    <?
                }
                ?>
                n+="
                <td>";
                    n+="
                    <div style='word-break:break-all;'><? echo sprintf("%02d", $n['hour']); ?>:<? echo sprintf("%02d", $n['minute']); ?>&nbsp;Uhr</div>
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
                    n+="<span style='white-space:nowrap;'><? echo trim($days); ?></span><br>";
                    <?
                    if (!$noDate) {
                        ?>
                        n+="
                        <div style='margin-top:3px; white-space:nowrap;'><? echo $n['day1']; ?>.<? echo $n['month1']; ?>
                            .<? echo $n['year1']; ?> <? echo(($n['mode'] == 0) ? '&middot;' : '&gt;'); ?> <? echo $n['day2']; ?>.<? echo $n['month2']; ?>
                            .<? echo $n['year2']; ?></div>";
                        <?
                    }
                    ?>
                    n+="
                    <div style='margin-top:3px; word-break:break-all;'><? if ($tmp_auxKo !== false) {
                            if ($n['d7'] == 1) {
                                echo escapeString($tmp_auxKo, 1);
                            } else if ($n['d7'] == 2) {
                                echo '<s>&nbsp;';
                                echo escapeString($tmp_auxKo, 1);
                                echo '&nbsp;</s>';
                            }
                        } ?></div>
                    ";
                    n+="
                </td>";
                n+="</tr>";
                         n+="
                <tr>
                    <td colspan='<? echo(($json1['clock'] >= 1) ? 3 : 2); ?>'>
                        <div style='width:100%; height:1px; background:var(--fgc0); opacity:0.25;'></div>
                    </td>
                </tr>";
                <?
            }
            sql_close($ss1);
            ?>
            n+="
        </table>";

        var obj=document.getElementById("e-<? echo $json1['elementId']; ?>-edit");
        if (obj) {
        obj.innerHTML=n;
        document.getElementById("e-<? echo $json1['elementId']; ?>-infotext").innerHTML="<? echo escapeString(sql_getValue('edomiLive.timer', 'name', 'id=' . $json1['timerId']), 1); ?>";
        scrollToTop("e-<? echo $json1['elementId']; ?>-edit");
        }
        <?
    }
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

VSE_VSEID_ShowInfo(elementId);

visuElement_onClick(document.getElementById("e-"+elementId+"-last"),function(veId,objId){scrollUp("e-"+veId+"-edit");});
visuElement_onClick(document.getElementById("e-"+elementId+"-info"),function(veId,objId){VSE_VSEID_ShowInfo(veId);});
visuElement_onClick(document.getElementById("e-"+elementId+"-next"),function(veId,objId){scrollDown("e-"+veId+"-edit");});

if (obj.dataset.var3 & 1) {
visuElement_onClick(document.getElementById("e-"+elementId+"-off"),function(veId,objId){visuElement_setKoValue(veId,1,0);});
visuElement_onClick(document.getElementById("e-"+elementId+"-on"),function(veId,objId){visuElement_setKoValue(veId,1,1);});
}
if (obj.dataset.var3 & 2) {
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
visuElement_callPhp("zsuInfo",{elementId:elementId,timerId:d.dataset.var1,sort:d.dataset.var2,clock:d.dataset.var4},null);
}
}

VSE_VSEID_New=function(elementId) {
var d=document.getElementById("e-"+elementId);
if (d) {
if (d.dataset.var3 & 2) {
visuElement_callPhp("zsuEdit",{elementId:elementId,timerId:d.dataset.var1,dbId:-1},null);
}
}
}

VSE_VSEID_Edit=function(elementId,id) {
var d=document.getElementById("e-"+elementId);
if (d) {
if (d.dataset.var3 & 2) {
visuElement_callPhp("zsuEdit",{elementId:elementId,timerId:d.dataset.var1,dbId:id},null);
}
}
}
###[/VISU.JS]###


###[HELP]###
Das Visuelement "Zeitschaltuhr" stellt eine
<link>konfigurierte Zeitschaltuhr***1000-100</link> in der Visualisierung dar und ermöglicht das Steuern der Zeitschaltuhr, sowie das Hinzufügen und Bearbeiten von Schaltzeiten.

<b>Hinweis:</b>
Die Zeitschaltuhr arbeitet unabhängig von diesem Visuelement. Das Visuelement ist also nicht erforderlich, um eine Zeitschaltuhr zu nutzen. Das Visuelement "Zeitschaltuhr" ermöglicht jedoch das Hinzufügen und Bearbeiten von Schaltzeiten zur Laufzeit.

<h2>Spezifische Eigenschaften</h2>
Für weitere Einstellungen und Optionen siehe:
<link>Allgemeine Informationen zu Visuelementen***1002</link>

<ul>
    <li>Zeitschaltuhr: Auswahl der
        <link>
        konfigurierten Zeitschaltuhr***1000-100</link>, die angezeigt und bearbeitet werden soll
    </li>

    <li>
        Schaltflächen: legt fest, ob die Zeitschaltuhr aktiviert/deaktiviert werden kann und ob Schaltzeiten bearbeitet/hinzugefügt werden dürfen
        <ul>
            <li>Hinweis: die Schaltflächen werden mit Symbolen beschriftet (von links nach rechts: deaktivieren, hinzufügen, aktivieren)</li>
            <li>Wichtig: Die Zeitschaltuhr kann unabhängig davon stets über das entsprechende KO gesteuert werden, Schaltzeiten können bereits in der
                Konfiguration der Zeitschaltuhr definiert werden.
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
        Sortierung: legt die Sortierung der angezeigten Schaltzeiten fest
        <ul>
            <li>ID: die Schaltzeiten werden absteigend nach deren ID angezeigt</li>
            <li>Wochentag/Uhrzeit: die Schaltzeiten werden aufsteigend nach Wochentag und Uhrzeit angezeigt</li>
        </ul>
    </li>

    <li>Analoguhr: legt fest, ob vor jeder Schaltzeit eine Analoguhr angezeigt werden soll und legt deren Größe in Pixeln fest (keine Skala, nur
        Stunden-/Minutenzeiger)
    </li>

    <li>Kopf-/Fusszeilenhöhe: legt optional die Höhe der Kopf- und Fusszeile in Pixeln fest</li>
</ul>


<h2>Kommunikationsobjekte</h2>
Dieses Visuelement kann (optional) folgende Kommunikationsobjekte (KO) verwalten:

<ul>
    <li>
        KO1: Steuerungs-KO der Zeitschaltuhr
        <ul>
            <li>dieses KO ist stets mit dem Steuerungs-KO der zugewiesenen Zeitschaltuhr verknüpft</li>
            <li>dieser KO-Wert wird zur <span style="background:#e0ffe0;">Steuerung und Beschriftung</span> verwendet (Designs, Funktionen und Formeln)</li>
            <li>dieser KO-Wert wird zudem beim Bedienen der Zeitschaltuhr in der Visualisierung entsprechend gesetzt (Zeitschaltuhr: Aus/Ein)</li>
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
        Konfiguration der Zeitschaltuhr***1000-100</link> können Schaltzeiten fest vorgegeben werden - diese sind in der Visualisierung nicht veränderbar!
    </li>
    <li>Designs: Innenabstand und Textausrichtung werden ignoriert</li>
    <li>Seitensteuerung/Befehle stehen nicht zu Verfügung</li>
</ul>


<h2>Bedienung in der Visualisierung</h2>
Das Visuelement ist in 3 Teilbereiche gegliedert (von oben nach unten):

<ul>
    <li>
        Titelleiste:
        <ul>
            <li>hier werden Pfeil-Schaltflächen zum Blättern durch die Schaltzeiten (Scrollen), sowie der Name der
                <link>
                Zeitschaltuhr***1000-100</link> angezeigt
            </li>
            <li>ein Klick auf den Namen der Zeitschaltuhr aktualisiert den Inhalt des Visuelements</li>
        </ul>
    </li>

    <li>
        Schaltzeiten (Auflistung):
        <ul>
            <li>in der Visualisierung erstellte Schaltzeiten werden in der Vordergrundfarbe des Visuelements angezeigt</li>
            <li>durch Anklicken einer Schaltzeit kann diese bearbeitet oder gelöscht werden (siehe auch:
                <link>
                Konfiguration einer Zeitschaltuhr***1000-100</link>)
            </li>
            <li>vorkonfigurierte (fixe) Schaltzeiten werden kontrastreduziert angezeigt (diese Schaltzeiten sind in der Visualisierung nicht veränderbar)</li>
        </ul>
    </li>

    <li>
        Steuerungs-Schaltflächen:
        <ul>
            <li>"aus": deaktiviert die Zeitschaltuhr (das Steuerungs-KO der Zeitschaltuhr wird auf den Wert "0" gesetzt)</li>
            <li>"+": fügt eine neue Schaltzeit hinzu</li>
            <li>"ein": aktiviert die Zeitschaltuhr (das Steuerungs-KO der Zeitschaltuhr wird auf den Wert "1" gesetzt)</li>
        </ul>
    </li>
</ul>
###[/HELP]###


