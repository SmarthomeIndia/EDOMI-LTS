<?
/*
*/
?><? ?><? require("../../shared/php/config.php");
require(MAIN_PATH . "/www/shared/php/base.php");
require(MAIN_PATH . "/www/shared/php/incl_http.php");
require(MAIN_PATH . "/www/admin/include/php/config.php");
require(MAIN_PATH . "/www/admin/include/php/base.php");
require(MAIN_PATH . "/www/admin/include/php/incl_items.php");
if (file_exists(MAIN_PATH . '/www/admin/vse/vse_include_admin.php')) {
    require(MAIN_PATH . '/www/admin/vse/vse_include_admin.php');
} else { ?>
    closeWindow("<? echo $winId; ?>");
    jsConfirm("<b>Es sind keine Visuelemente verfügbar!</b><br>
    <br>Das Hinzufügen oder Bearbeiten von Visuelement-Instanzen ist daher nicht möglich.","","none");
    <? exit();
}
sql_connect();
if (checkAdmin($sid)) {
    cmd($cmd);
}
function cmd($cmd)
{
    global $appId, $winId, $data, $dataArr, $phpdata, $phpdataArr, $sid;
    if ($cmd == 'initApp') { ?>
        var n="
        <div class='appWindowDrag' id='<? echo $winId; ?>-global'>";
            n+="
            <div class='appTitelDrag' onMouseDown='dragWindowStart(\"<? echo $appId; ?>\",\"<? echo $winId; ?>-global\");'><span id='<? echo $winId; ?>-title'>Visuelement</span>
                <div class='cmdClose' onClick='closeWindow(\"<? echo $winId; ?>\");'></div>
                <div id='<? echo $winId; ?>-help' class='cmdHelp' data-helpid='<? echo $appId; ?>' onClick='openWindow(9999,this.dataset.helpid);'></div>
            </div>
            ";
            n+="
            <div id='<? echo $winId; ?>-main'></div>
            ";
            n+="
        </div>";
        document.getElementById("<? echo $winId; ?>").innerHTML=n;
        dragWindowRestore("<? echo $appId; ?>","<? echo $winId; ?>-global");
        <? $ss1 = sql_call("SELECT * FROM edomiProject.editVisuElement WHERE (id=" . $dataArr[2] . ") ORDER BY id ASC");
        if ($n = sql_result($ss1)) {
            $fd[0] = 0;
            $fd[1] = $n['id'];
            $fd[2] = $n['xpos'];
            $fd[3] = $n['ypos'];
            $fd[6] = $n['controltyp'];
            $fd[7] = $n['gaid'];
            $fd[8] = $n['zindex'];
            $fd[13] = $n['gotopageid'];
            $fd[14] = $n['closepopup'];
            $fd[15] = $n['xsize'];
            $fd[16] = $n['ysize'];
            $fd[17] = $n['gaid2'];
            $fd[18] = $n['text'];
            $fd[19] = $n['initonly'];
            $fd[20] = $n['name'];
            $fd[21] = $n['groupid'];
            $fd[22] = $n['linkid'];
            $fd[23] = $n['gaid3'];
            $fd[31] = $n['var1'];
            $fd[32] = $n['var2'];
            $fd[33] = $n['var3'];
            $fd[34] = $n['var4'];
            $fd[35] = $n['var5'];
            $fd[36] = $n['var6'];
            $fd[37] = $n['var7'];
            $fd[38] = $n['var8'];
            $fd[39] = $n['var9'];
            $fd[40] = $n['var10'];
            $fd[41] = $n['var11'];
            $fd[42] = $n['var12'];
            $fd[43] = $n['var13'];
            $fd[44] = $n['var14'];
            $fd[45] = $n['var15'];
            $fd[46] = $n['var16'];
            $fd[47] = $n['var17'];
            $fd[48] = $n['var18'];
            $fd[49] = $n['var19'];
            $fd[50] = $n['var20'];
            $fd[94] = $n['closepopupid'];
            $fd[95] = $n['dynstylemode'];
            $fd[96] = $n['galive'];
            $fd[97] = $n['visuid'];
            $fd[98] = $n['pageid'];
        } else { ?>
            closeWindow("<? echo $winId; ?>");
            <? return;
        }
        sql_close($ss1);
        if ($fd[6] == 0) { ?>
            document.getElementById('<? echo $winId; ?>-help').dataset.helpid="2-1";
            var n="
            <div class='appMenu'>";
                n+="
                <div class='cmdButton cmdButtonL' onClick='closeWindow(\"<? echo $winId; ?>\");'>Abbrechen</div>
                ";
                n+="
                <div class='cmdButton cmdButtonR'
                     onClick='ajax(\"saveItem\",\"<? echo $appId; ?>\",\"<? echo $winId; ?>\",\"<? echo $data; ?>\",controlGetFormData(\"<? echo $winId; ?>-form1\"));'>
                    <b>Übernehmen</b></div>
                ";
                n+="
            </div>";
            n+="
            <div class='appContent' id='<? echo $winId; ?>-form1' style='width:500px; padding:8px;'>";
                n+="<input type='hidden' id='<? echo $winId; ?>-fd0' data-type='1' value='<? echo $fd[0]; ?>'></input>";
                n+="<input type='hidden' id='<? echo $winId; ?>-fd1' data-type='1' value='<? echo $fd[1]; ?>'></input>";
                n+="<input type='hidden' id='<? echo $winId; ?>-fd6' data-type='1' value='<? echo $fd[6]; ?>'></input>";
                n+="<input type='hidden' id='<? echo $winId; ?>-fd97' data-type='1' value='<? echo $fd[97]; ?>'></input>";
                n+="<input type='hidden' id='<? echo $winId; ?>-fd98' data-type='1' value='<? echo $fd[98]; ?>'></input>";
                n+="
                <table width='100%' border='0' cellpadding='2' cellspacing='0'>";
                    n+="
                    <colgroup>";
                        n+="
                        <col width='70%'>
                        ";
                        n+="
                        <col width='30%'>
                        ";
                        n+="
                    </colgroup>
                    ";
                    n+="
                    <tr>";
                        n+="
                        <td colspan='2'>Name<br><input type='text' id='<? echo $winId; ?>-fd20' data-type='1' value='' autofocus class='control1'
                                                       style='width:100%;'></input></td>
                        ";
                        n+="
                    </tr>
                    ";
                    n+="
                    <tr>";
                        n+="
                        <td>KO<br>
                            <div id='<? echo $winId; ?>-fd7' data-type='1000' data-root='30' data-value='<? echo $fd[7]; ?>' data-options='typ=1'
                                 class='control10' style='width:100%;'>&nbsp;
                            </div>
                        </td>
                        ";
                        n+="
                        <td>KO-Wert<br><input type='text' id='<? echo $winId; ?>-fd18' data-type='1' value='' autofocus class='control1'
                                              style='width:100%;'></input></td>
                        ";
                        n+="
                    </tr>
                    ";
                    n+="
                </table>
                ";
                n+="
            </div>";
            document.getElementById("<? echo $winId; ?>-main").innerHTML=n;
            document.getElementById('<? echo $winId; ?>-title').innerHTML="Gruppe<? if ($fd[1] > 0) {
                echo "&nbsp;<span class='idBig'>" . $fd[1] . "</span>";
            } ?></span>";

            document.getElementById("<? echo $winId; ?>-fd18").value='<? ajaxValue($fd[18]); ?>';
            document.getElementById("<? echo $winId; ?>-fd20").value='<? ajaxValue($fd[20]); ?>';
            appAll_setAutofocus("<? echo $winId; ?>-main");

            controlInitAll("<? echo $winId; ?>-form1");
        <? } else {
            $vseDef = sql_getValues('edomiProject.editVisuElementDef', '*', 'id=' . $fd[6] . ' AND errcount=0');
            $vsePROPERTIES = 'PHP_VSE_' . $fd[6] . '_PROPERTIES';
            if ($vseDef !== false && function_exists($vsePROPERTIES)) {
                $tmp_groups = '0|(keine);';
                if ($fd[97] > 0 && $fd[98] > 0) {
                    $ss1 = sql_call("SELECT id,name FROM edomiProject.editVisuElement WHERE (visuid=" . $fd[97] . " AND pageid=" . $fd[98] . " AND controltyp=0) ORDER BY id ASC");
                    while ($n = sql_result($ss1)) {
                        $n['name'] = preg_replace("/[\|\;]/", '', $n['name']);
                        $tmp_groups .= $n['id'] . '|' . $n['name'] . ' (' . $n['id'] . ');';
                    }
                    sql_close($ss1);
                } ?>
                document.getElementById('<? echo $winId; ?>-help').dataset.helpid="<? echo $appId; ?>-<? echo $fd[6]; ?>";

                var option=new Array();

                var n="
                <div class='appMenu'>";
                    n+="
                    <div class='cmdButton cmdButtonL' onClick='closeWindow(\"<? echo $winId; ?>\");'>Abbrechen</div>
                    ";
                    n+="
                    <div class='cmdButton cmdButtonM'
                         onClick='ajax(\"saveItem\",\"<? echo $appId; ?>\",\"<? echo $winId; ?>\",\"<? echo $data; ?>\",controlGetFormData(\"<? echo $winId; ?>-form1\"));'>
                        <b>Übernehmen</b></div>
                    ";
                    n+="
                    <div class='cmdButton cmdButtonR'
                         onClick='ajax(\"saveItemPreview\",\"<? echo $appId; ?>\",\"<? echo $winId; ?>\",\"<? echo $data; ?>\",controlGetFormData(\"<? echo $winId; ?>-form1\"));'>
                        Anwenden
                    </div>
                    ";
                    n+="
                </div>";

                n+="
                <div id='<? echo $winId; ?>-form1' class='appContent' style='width:816px; max-height:820px; padding:0;'>";
                    n+="<input type='hidden' id='<? echo $winId; ?>-fd0' data-type='1' value='<? echo $fd[0]; ?>'></input>";
                    n+="<input type='hidden' id='<? echo $winId; ?>-fd1' data-type='1' value='<? echo $fd[1]; ?>'></input>";
                    n+="<input type='hidden' id='<? echo $winId; ?>-fd6' data-type='1' value='<? echo $fd[6]; ?>'></input>";
                    n+="<input type='hidden' id='<? echo $winId; ?>-fd22' data-type='1' value='<? echo $fd[22]; ?>'></input>";
                    n+="<input type='hidden' id='<? echo $winId; ?>-fd97' data-type='1' value='<? echo $fd[97]; ?>'></input>";
                    n+="<input type='hidden' id='<? echo $winId; ?>-fd98' data-type='1' value='<? echo $fd[98]; ?>'></input>";
                    n+="
                    <table width='100%' border='0' cellpadding='0' cellspacing='0'>";
                        n+="
                        <colgroup>";
                            n+="
                            <col width='50%'>
                            ";
                            n+="
                            <col width='50%'>
                            ";
                            n+="
                        </colgroup>
                        ";
                        n+="
                        <tr height='1' valign='top'>";
                            n+="
                            <td style='background:#e0e0d9; padding:8px;'>";
                                n+="
                                <table width='100%' border='0' cellpadding='2' cellspacing='0'>";
                                    n+="
                                    <colgroup>";
                                        n+="
                                        <col width='24%'>
                                        ";
                                        n+="
                                        <col width='24%'>
                                        ";
                                        n+="
                                        <col width='4%'>
                                        ";
                                        n+="
                                        <col width='24%'>
                                        ";
                                        n+="
                                        <col width='24%'>
                                        ";
                                        n+="
                                    </colgroup>
                                    ";
                                    n+="
                                    <tr>";
                                        n+="
                                        <td colspan='5'>Name<br><input type='text' id='<? echo $winId; ?>-fd20' data-type='1' value='' autofocus
                                                                       class='control1' style='width:100%;'></input></td>
                                        ";
                                        n+="
                                    </tr>
                                    ";
                                    n+="
                                    <tr>";
                                        n+="
                                        <td colspan='4'>Gruppe<br>
                                            <div id='<? echo $winId; ?>-fd21' data-type='6' data-value='<? echo $fd[21]; ?>' data-list='' class='control6'
                                                 style='width:100%;'>&nbsp;
                                            </div>
                                        </td>
                                        ";
                                        n+="
                                        <td>Z-Index<br><input type='text' id='<? echo $winId; ?>-fd8' data-type='1' value='' class='control1'
                                                              style='width:100%;'></input></td>
                                        ";
                                        n+="
                                    </tr>
                                    ";
                                    n+="
                                    <tr>";
                                        n+="
                                        <td>X-Position (px)<br><input type='text' id='<? echo $winId; ?>-fd2' data-type='1' value='' class='control1'
                                                                      style='width:100%;'></input></td>
                                        ";
                                        n+="
                                        <td>Y-Position (px)<br><input type='text' id='<? echo $winId; ?>-fd3' data-type='1' value='' class='control1'
                                                                      style='width:100%;'></input></td>
                                        ";
                                        n+="
                                        <td>&nbsp;</td>
                                        ";
                                        n+="
                                        <td>Breite (px)<br><input type='text' id='<? echo $winId; ?>-fd15' data-type='1' value='' class='control1'
                                                                  style='width:100%;'></input></td>
                                        ";
                                        n+="
                                        <td>Höhe (px)<br><input type='text' id='<? echo $winId; ?>-fd16' data-type='1' value='' class='control1'
                                                                style='width:100%;'></input></td>
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
                            <td rowspan='3' style='padding:8px;'>";
                                n+="
                                <table width='100%' border='0' cellpadding='2' cellspacing='0'>";
                                    n+="
                                    <colgroup>";
                                        n+="
                                        <col width='70%'>
                                        ";
                                        n+="
                                        <col width='30%'>
                                        ";
                                        n+="
                                    </colgroup>
                                    ";
                                    <? $firstRow = true;
                                    if ($vseDef['flagtext'] == 1) { ?>
                                        n+="
                                        <tr>
                                            <td colspan='2' class='formSubTitel' <? echo(($firstRow) ? "style='padding-top:0;'" : ""); ?>>
                                                Beschriftung<? if (!isEmpty($vseDef['captiontext'])) {
                                                    echo '&nbsp;&gt;&nbsp;';
                                                    ajaxEcho($vseDef['captiontext']);
                                                } ?>
                                                <hr>
                                            </td>
                                        </tr>";
                                             n+="
                                        <tr>
                                            <td colspan='2'><textarea id='<? echo $winId; ?>-fd18' data-type='1' maxlength='10000' rows='3' wrap='soft'
                                                                      class='control1' onkeydown='if (event.keyCode==9) {appAll_enableTabKey(this);}'
                                                                      style='width:100%; height:61px; resize:none; tab-size:4; background:#e0ffe0;'></textarea>
                                            </td>
                                        </tr>";
                                        <? $firstRow = false;
                                    }
                                    if ($vseDef['flagdesign'] == 1) { ?>
                                        n+="
                                        <tr>
                                            <td colspan='2' class='formSubTitel' <? echo(($firstRow) ? "style='padding-top:0;'" : ""); ?>>Design
                                                <div id='<? echo $winId; ?>-fd19' data-type='6' data-value='<? echo $fd[19]; ?>'
                                                     data-list='0|immer verwenden;1|nur bei Aufbau;' class='control6' style='font-size:10px; float:right;'>
                                                    &nbsp;
                                                </div>
                                                <hr>
                                            </td>
                                        </tr>";
                                             n+="
                                        <tr>
                                            <td colspan='2'>
                                                <div id='<? echo $winId; ?>-list1' data-type='1003' data-value='<? echo $fd[1]; ?>' data-itemid='0'
                                                     data-options='' class='control10' style='width:100%;'>&nbsp;
                                                </div>
                                            </td>
                                        </tr>";
                                        <? $firstRow = false;
                                    }
                                    if ($vseDef['flagdyndesign'] == 1) { ?>
                                        n+="
                                        <tr>
                                            <td colspan='2' class='formSubTitel' <? echo(($firstRow) ? "style='padding-top:0;'" : ""); ?>>Dynamische
                                                Designs<? if (!isEmpty($vseDef['captiontext'])) {
                                                    echo '&nbsp;&gt;&nbsp;';
                                                    ajaxEcho($vseDef['captiontext']);
                                                } ?>
                                                <div id='<? echo $winId; ?>-fd95' data-type='6' data-value='<? echo $fd[95]; ?>'
                                                     data-list='0|automatisch;2|Zahl;1|String;' class='control6' style='font-size:10px; float:right;'>&nbsp;
                                                </div>
                                                <hr>
                                            </td>
                                        </tr>";
                                             n+="
                                        <tr>
                                            <td colspan='2'>
                                                <div id='<? echo $winId; ?>-list2' data-type='1003' data-value='<? echo $fd[1]; ?>' data-itemid='0'
                                                     data-options='-dynDesigns' class='controlList' style='width:100%; height:120px;'>&nbsp;
                                                </div>
                                            </td>
                                        </tr>";
                                        <? $firstRow = false;
                                    }
                                    if ($vseDef['flagko1'] > 0 || $vseDef['flagko2'] == 1 || $vseDef['flagko3'] == 1) {
                                        if ($vseDef['flagko1'] > 0) { ?>
                                            n+="
                                            <tr>
                                                <td colspan='2' class='formSubTitel' <? echo(($firstRow) ? "style='padding-top:0;'" : ""); ?>>
                                                    Kommunikationsobjekte
                                                    <div id='<? echo $winId; ?>-fd96' data-type='6' data-value='<? echo $fd[96]; ?>'
                                                         data-list='0|Live-Vorschau deaktiviert;-1|KO1: Live-Vorschau (ohne Haltezeit);100|KO1: 0.1 Sekunden Haltezeit;250|KO1: 0.25 Sekunden Haltezeit;500|KO1: 0.5 Sekunden Haltezeit;1000|KO1: 1 Sekunde Haltezeit;2000|KO1: 2 Sekunden Haltezeit;3000|KO1: 3 Sekunden Haltezeit;5000|KO1: 5 Sekunden Haltezeit;10000|KO1: 10 Sekunden Haltezeit;15000|KO1: 15 Sekunden Haltezeit;20000|KO1: 20 Sekunden Haltezeit;25000|KO1: 25 Sekunden Haltezeit;30000|KO1: 30 Sekunden Haltezeit;60000|KO1: 60 Sekunden Haltezeit;'
                                                         class='control6' style='font-size:10px; float:right;'>&nbsp;
                                                    </div>
                                                    <hr>
                                                </td>
                                            </tr>";
                                        <? } else { ?>
                                            n+="
                                            <tr>
                                                <td colspan='2' class='formSubTitel' <? echo(($firstRow) ? "style='padding-top:0;'" : ""); ?>>
                                                    Kommunikationsobjekte<input type='hidden' id='<? echo $winId; ?>-fd96' data-type='1' value='0'></input>
                                                    <hr>
                                                </td>
                                            </tr>";
                                        <? }
                                        $firstRow = false; ?>
                                        n+="
                                        <tr>";
                                            <? if ($vseDef['flagko1'] == 1) { ?>
                                                n+="
                                                <td colspan='2'>KO1: <? ajaxEcho($vseDef['captionko1']); ?><br>
                                                    <div id='<? echo $winId; ?>-fd7' data-type='1000' data-root='30' data-value='<? echo $fd[7]; ?>'
                                                         data-options='typ=1' class='control10' style='width:100%; background:#e0ffe0;'>&nbsp;
                                                    </div>
                                                </td>";
                                            <? } else if ($vseDef['flagko1'] == 2) { ?>
                                                n+="
                                                <td colspan='2'>KO1: <? ajaxEcho($vseDef['captionko1']); ?><br><span class='control10'
                                                                                                                     style='width:100%; color:#a0a0a0; background:#e0ffe0; cursor:default;'>(wird bei Aktivierung eingefügt)</span>
                                                </td>";
                                            <? } ?>
                                            n+="
                                        </tr>";
                                        <? if ($vseDef['flagko2'] == 1) { ?>
                                            n+="
                                            <tr>
                                                <td colspan='2'>KO2: <? ajaxEcho($vseDef['captionko2']); ?><br>
                                                    <div id='<? echo $winId; ?>-fd17' data-type='1000' data-root='30' data-value='<? echo $fd[17]; ?>'
                                                         data-options='typ=1' class='control10' style='width:100%;'>&nbsp;
                                                    </div>
                                                </td>
                                            </tr>";
                                        <? }
                                        if ($vseDef['flagko3'] == 1) { ?>
                                            n+="
                                            <tr>
                                                <td colspan='2'>KO3: Steuerung des dynamischen Designs<br>
                                                    <div id='<? echo $winId; ?>-fd23' data-type='1000' data-root='30' data-value='<? echo $fd[23]; ?>'
                                                         data-options='typ=1' class='control10' style='width:100%;'>&nbsp;
                                                    </div>
                                                </td>
                                            </tr>";
                                        <? }
                                    }
                                    if ($vseDef['flagpage'] == 1) { ?>
                                        n+="
                                        <tr>
                                            <td colspan='2' class='formSubTitel' <? echo(($firstRow) ? "style='padding-top:0;'" : ""); ?>>Seitensteuerung
                                                <hr>
                                            </td>
                                        </tr>";
                                             n+="
                                        <tr>
                                            <td colspan='2'>Seite aufrufen<br>
                                                <div id='<? echo $winId; ?>-fd13' data-type='1000' data-root='22_<? echo $fd[97]; ?>'
                                                     data-value='<? echo $fd[13]; ?>' data-options='typ=1' class='control10' style='width:100%;'>&nbsp;
                                                </div>
                                            </td>
                                        </tr>";
                                             n+="
                                        <tr>";
                                            <? $firstRow = false;
                                            if (!isEmpty(sql_getValue('edomiProject.editVisuPage', 'id', 'id=' . $fd[98] . ' AND pagetyp=1'))) { ?>
                                                n+="
                                                <td>Popup schliessen<br>
                                                    <div id='<? echo $winId; ?>-fd94' data-type='1000' data-root='22_<? echo $fd[97]; ?>'
                                                         data-value='<? echo $fd[94]; ?>' data-options='typ=1' class='control10' style='width:100%;'>&nbsp;
                                                    </div>
                                                </td>";
                                                     n+="
                                                <td><br>
                                                    <div id='<? echo $winId; ?>-fd14' data-type='5' data-value='<? echo $fd[14]; ?>' class='control5'
                                                         style='width:100%;'>dieses Popup
                                                    </div>
                                                </td>";
                                            <? } else { ?>
                                                n+="
                                                <td colspan='2'>Popup schliessen<br>
                                                    <div id='<? echo $winId; ?>-fd94' data-type='1000' data-root='22_<? echo $fd[97]; ?>'
                                                         data-value='<? echo $fd[94]; ?>' data-options='typ=1' class='control10' style='width:100%;'>&nbsp;
                                                    </div>
                                                </td>";
                                            <? } ?>
                                            n+="
                                        </tr>";
                                    <? }
                                    if ($vseDef['flagcmd'] == 1) { ?>
                                        n+="
                                        <tr>
                                            <td colspan='2' class='formSubTitel' <? echo(($firstRow) ? "style='padding-top:0;'" : ""); ?>>Befehle
                                                <hr>
                                            </td>
                                        </tr>";
                                             n+="
                                        <tr>
                                            <td colspan='2'>
                                                <div id='<? echo $winId; ?>-list3' data-type='1007' data-value='<? echo $fd[1]; ?>' data-itemid='0'
                                                     data-db='editVisuCmdList' class='controlList' style='height:120px;'>&nbsp;
                                                </div>
                                            </td>
                                        </tr>";
                                        <? $firstRow = false;
                                    } ?>
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
                        <tr height='1' valign='top'>";
                            n+="
                            <td class='formSubTitel' style='background:#e0e0d9; padding:8px 8px 0 8px;'>Spezifische Eigenschaften
                                <hr>
                            </td>
                            ";
                            n+="
                        </tr>
                        ";
                        n+="
                        <tr valign='top'>";
                            n+="
                            <td style='background:#e0e0d9; padding:0 8px 8px 8px;'>";
                                <? $vsePROPERTIES(); ?>
                                n+="
                            </td>
                            ";
                            n+="
                        </tr>
                        ";
                        n+="
                    </table>
                    ";
                    n+="
                </div>";

                document.getElementById("<? echo $winId; ?>-main").innerHTML=n;
                for (var t=31;t<=50;t++) {
                var tmp=document.getElementById("<? echo $winId; ?>-fd"+t);
                if (!tmp) {
                var n=document.createElement("input");
                n.id="<? echo $winId; ?>-fd"+t;
                n.type="hidden";
                n.dataset.type="1";
                n.value="";
                document.getElementById("<? echo $winId; ?>-form1").appendChild(n);
                } else {
                if (tmp.dataset.type==10) {tmp.placeholder=option[t];}
                if (tmp.dataset.type==11) {tmp.dataset.list=option[t];}
                if (tmp.dataset.type==12) {tmp.innerHTML=option[t];}
                if (tmp.dataset.type==13) {tmp.dataset.list=option[t];}
                }
                }

                document.getElementById('<? echo $winId; ?>-title').innerHTML="<? ajaxEcho($vseDef['name']); ?><? if ($fd[1] > 0) {
                    echo "&nbsp;<span class='idBig'>" . $fd[1] . "</span>";
                } ?>";

                document.getElementById("<? echo $winId; ?>-fd2").value='<? ajaxValue($fd[2]); ?>';
                document.getElementById("<? echo $winId; ?>-fd3").value='<? ajaxValue($fd[3]); ?>';
                document.getElementById("<? echo $winId; ?>-fd8").value='<? ajaxValue($fd[8]); ?>';

                document.getElementById("<? echo $winId; ?>-fd15").value='<? ajaxValue($fd[15]); ?>';
                document.getElementById("<? echo $winId; ?>-fd16").value='<? ajaxValue($fd[16]); ?>';

                if (document.getElementById("<? echo $winId; ?>-fd18")) {
                document.getElementById("<? echo $winId; ?>-fd18").value='<? ajaxValue($fd[18]); ?>';
                }

                document.getElementById("<? echo $winId; ?>-fd20").value='<? ajaxValue($fd[20]); ?>';
                appAll_setAutofocus("<? echo $winId; ?>-main");

                document.getElementById("<? echo $winId; ?>-fd21").dataset.list='<? ajaxValue($tmp_groups); ?>';

                <? for ($t = 31; $t <= 50; $t++) { ?>
                    var tmp=document.getElementById("<? echo $winId; ?>-fd<? echo $t; ?>");
                    if (tmp) {
                    if (tmp.dataset.type==10) {
                    tmp.value='<? ajaxValue($fd[$t]); ?>';
                    } else {
                    tmp.dataset.value='<? ajaxValue($fd[$t]); ?>';
                    }
                    }
                <? } ?>
                controlInitAll("<? echo $winId; ?>-form1");
            <? } else { ?>
                closeWindow("<? echo $winId; ?>");
                jsConfirm("<b>Das Visuelement (<? echo $fd[6]; ?>) ist fehlerhaft oder nicht vorhanden!</b><br>
                <br>Eine Bearbeitung der Visuelement-Instanz ist daher nicht möglich.","","none");
                <? return;
            }
        }
    }
    if ($cmd == 'saveItem' || $cmd == 'saveItemPreview') {
        $dbId = db_itemSave('editVisuElement', $phpdataArr);
        if ($dbId > 0) {
            if ($cmd == 'saveItemPreview') {
                echo 'controlReturn("","' . $dataArr[0] . '","' . $dataArr[2] . '");';
            } else {
                echo 'controlReturn("' . $winId . '","' . $dataArr[0] . '","' . $dataArr[2] . '");';
            }
        } else { ?>
            shakeObj("<? echo $winId; ?>");
        <? }
    }
}

sql_disconnect(); ?>

