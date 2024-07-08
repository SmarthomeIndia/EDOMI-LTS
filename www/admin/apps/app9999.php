<?
/*
*/
?><? ?><? require("../../shared/php/config.php");
require(MAIN_PATH . "/www/shared/php/base.php");
require(MAIN_PATH . "/www/shared/php/incl_http.php");
require(MAIN_PATH . "/www/admin/include/php/config.php");
require(MAIN_PATH . "/www/admin/include/php/base.php");
sql_connect();
if (checkAdmin($sid)) {
    cmd($cmd);
}
function cmd($cmd)
{
    global $appId, $winId, $data, $dataArr, $phpdata, $phpdataArr, $sid;
    if ($cmd == 'initApp') {
        if (isEmpty($dataArr[0])) {
            $dataArr[0] = 'start';
        }
        if (!isEmpty($phpdataArr[0])) {
            $tmp = sql_getValues('edomiProject.editLogicElement', 'functionid', 'id=' . $phpdataArr[0]);
            if ($tmp !== false) {
                $dataArr[0] = 'lbs_' . $tmp['functionid'] . '-' . $phpdataArr[0];
            }
        } ?>
        var n="
        <table border='0' cellpadding='0' cellspacing='0' class='appWindowNormal'
               style='display:table; width:100%; height:100%; table-layout:auto; border-radius:0; box-shadow:none;'>";
            n+="
            <tr>
                <td colspan='2' height='1'>";
                    n+="
                    <div class='appTitel'>Hilfe</span>
                        <div class='cmdClose' onClick='closeDesktopHelp();'></div>
                        <div class='cmdHelp' data-helpid='<? echo $appId; ?>' onClick='ajax(\"showHelp\",\"9999\",\"<? echo $winId; ?>\",\"a-0\",\"\");'></div>
                        <span id='<? echo $winId; ?>-filename' style='color:#a0a0a0; margin-right:5px; float:right;'></div>
                    ";
                    n+="
                    <div class='appMenu'>";
                        n+="
                        <table id='<? echo $winId; ?>-form1' border='0' cellpadding='0' cellspacing='0' style='width:100%; height:100%; table-layout:auto;'>";
                            n+="
                            <tr>";
                                n+="
                                <td width='30px' style='padding-left:2px;'>
                                    <div id='<? echo $winId; ?>-menubutton' class='cmdButton' onClick='app9999_showMenu(\"<? echo $winId; ?>\",true);'
                                         style='min-width:30px; width:30px;'>&equiv;
                                    </div>
                                </td>
                                ";
                                n+="
                                <td width='30px' style='padding-left:2px;'>
                                    <div class='cmdButton' onClick='app9999_historyBack(\"<? echo $winId; ?>\",\"<? echo $winId; ?>-main\");'
                                         style='min-width:30px; width:30px;'>&lt;
                                    </div>
                                </td>
                                ";
                                n+="
                                <td style='padding-left:2px;'><input type='text' id='<? echo $winId; ?>-fd1' data-type='1' autofocus value=''
                                                                     onkeydown='if (event.keyCode==13) {ajax(\"search\",\"<? echo $appId; ?>\",\"<? echo $winId; ?>\",\"<? echo $data; ?>\",controlGetFormData(\"<? echo $winId; ?>-form1\"));}'
                                                                     class='control1' style='width:100%; height:27px; margin:0; outline:none;'></input></td>
                                ";
                                n+="
                                <td width='30px' style='padding-left:2px;'>
                                    <div id='<? echo $winId; ?>-helpexternal' class='cmdButton' data-helpfn='0'
                                         onClick='window.open(\"help.php?file=\"+this.dataset.helpfn,\"_blank\");' style='min-width:30px; width:30px;'>&#x21D7;
                                    </div>
                                </td>
                                ";
                                n+="
                            </tr>
                            ";
                            n+="
                        </table>
                        ";
                        n+="
                    </div>
                    ";
                    n+="
                </td>
            </tr>
            ";

            n+="
            <tr>";
                n+="
                <td class='appContentBlank' style='padding-top:10px;'>";
                    n+="
                    <div id='<? echo $winId; ?>-main' style='display:block; padding:2px; width:100%; height:100%; overflow:auto;'>";

                        n+="
                        <div id='<? echo $winId; ?>-menu'
                             style='position:absolute; top:75px; left:6px; bottom:6px; padding:3px; border-radius:3px; border:1px solid #c0c0c0; background:#f7f7f7; overflow:auto;'>
                            ";
                            <? if ($help = ajaxValueHTML(helpToHtml($winId, 'navigation'))) { ?>
                                n+='<? echo $help; ?>';
                            <? } ?>
                            n+="
                        </div>
                        ";

                        n+="
                        <table border='0' cellpadding='0' cellspacing='0' style='width:100%; height:100%; table-layout:fixed;'>";
                            n+="
                            <tr id='<? echo $winId; ?>-live' valign='top' height='1'>";
                                n+="
                                <td style='padding-bottom:20px;'>";
                                    n+="
                                    <div class='formTitel'>Live-Werte: Logikbaustein-Instanz <span id='<? echo $winId; ?>-info4' class='idBig'></span>
                                        <div class='cmdHelp' onClick='openWindow(9999,\"1-1\");'></div>
                                    </div>
                                    <hr>
                                    ";
                                    n+="
                                    <table width='100%' border='0' cellpadding='0' cellspacing='3' style='border-radius:3px; background:#e0e000;'>";
                                        n+="
                                        <colgroup>";
                                            n+="
                                            <col width='33%'>
                                            ";
                                            n+="
                                            <col width='33%'>
                                            ";
                                            n+="
                                            <col width='33%'>
                                            ";
                                            n+="
                                        </colgroup>
                                        ";
                                        n+="
                                        <tr>";
                                            n+="
                                            <td colspan='3' valign='top'>
                                                <div id='<? echo $winId; ?>-info0'
                                                     style='overflow:auto; box-sizing:border-box; width:100%; height:100%; background:#e8e800;'></div>
                                            </td>
                                            ";
                                            n+="
                                        </tr>
                                        ";
                                        n+="
                                        <tr>";
                                            n+="
                                            <td style='padding:2px; width:100%; background:#bbbb00;'>Eingänge</td>
                                            ";
                                            n+="
                                            <td style='padding:2px; width:100%; background:#bbbb00;'>Ausgänge (nur belegte)</td>
                                            ";
                                            n+="
                                            <td style='padding:2px; width:100%; background:#bbbb00;'>Variablen</td>
                                            ";
                                            n+="
                                        </tr>
                                        ";
                                        n+="
                                        <tr>";
                                            n+="
                                            <td valign='top'>
                                                <div id='<? echo $winId; ?>-info2'
                                                     style='overflow:auto; box-sizing:border-box; width:100%; height:170px; background:#e8e800;'></div>
                                            </td>
                                            ";
                                            n+="
                                            <td valign='top'>
                                                <div id='<? echo $winId; ?>-info3'
                                                     style='overflow:auto; box-sizing:border-box; width:100%; height:170px; background:#e8e800;'></div>
                                            </td>
                                            ";
                                            n+="
                                            <td valign='top'>
                                                <div id='<? echo $winId; ?>-info1'
                                                     style='overflow:auto; box-sizing:border-box; width:100%; height:170px; background:#e8e800;'></div>
                                            </td>
                                            ";
                                            n+="
                                        </tr>
                                        ";
                                        n+="
                                        <tr>";
                                            n+="
                                            <td colspan='3'>
                                                <div id='<? echo $winId; ?>-info5'></div>
                                            </td>
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
                            <tr valign='top'>";
                                n+="
                                <td>
                                    <div id='<? echo $winId; ?>-help' class='columnContent'
                                         style='line-height:1.5; display:table; -webkit-user-select:auto;'></div>
                                </td>
                                ";
                                n+="
                            </tr>
                            ";

                            n+="
                        </table>
                        ";
                        n+="
                    </div>
                    ";
                    n+="
                </td>
                ";

                n+="
            </tr>
            ";
            n+="
        </table>";


        document.getElementById("<? echo $winId; ?>").innerHTML=n;
        app9999_showMenu("<? echo $winId; ?>",false);
        <? cmd('showHelp');
    }
    if ($cmd == 'showHelp') { ?>
        app9999_showMenu("<? echo $winId; ?>",false);
        document.getElementById("<? echo $winId; ?>-live").style.display="none";
        <? $helpFn = $dataArr[0];
        $helpFnFull = $dataArr[0];
        if (substr($helpFnFull, 0, 4) == 'lbs_') {
            $tmp = explode('-', $helpFnFull);
            if (isset($tmp[1])) {
                $helpFn = $tmp[0];
                $phpdataArr[0] = $tmp[1];
                cmd('liveVars');
            }
        }
        if ($help = ajaxValueHTML(helpToHtml($winId, $helpFn))) { ?>
            app9999_historyAdd("<? echo $winId; ?>-main","<? echo $helpFnFull; ?>");
            document.getElementById("<? echo $winId; ?>-filename").innerHTML='<? echo $helpFn; ?>.htm';
            document.getElementById("<? echo $winId; ?>-helpexternal").dataset.helpfn='<? echo $helpFn; ?>';
            document.getElementById("<? echo $winId; ?>-help").innerHTML='<? echo $help; ?><br><br><br>';
            scrollToTop("<? echo $winId; ?>-main");
        <? } else { ?>
            app9999_historyAdd("<? echo $winId; ?>-main","<? echo $helpFnFull; ?>");
            document.getElementById("<? echo $winId; ?>-filename").innerHTML='<span style="color:#e00000;"><? echo $helpFn; ?>.htm</span>';
            document.getElementById("<? echo $winId; ?>-helpexternal").dataset.helpfn='<? echo $helpFn; ?>';
            document.getElementById("<? echo $winId; ?>-help").innerHTML='Es ist keine Hilfe für dieses Thema verfügbar.';
            scrollToTop("<? echo $winId; ?>-main");
        <? } ?>
        appAll_setAutofocus("<? echo $winId; ?>-form1");
    <? }
    if ($cmd == 'search') {
        if (!isEmpty($phpdataArr[1])) {
            helpSearchRequest($phpdataArr[1], 'result');
            $dataArr[0] = 'result';
            cmd('showHelp');
        }
    }
    if ($cmd == 'liveVars') {
        if (checkLiveProjectData()) {
            $ss1 = sql_call("SELECT status,statusexec FROM edomiLive.RAMlogicElement WHERE (id=" . $phpdataArr[0] . ")");
            if ($n = sql_result($ss1)) { ?>
                document.getElementById("<? echo $winId; ?>-info4").innerHTML="<? echo $phpdataArr[0]; ?>";
                document.getElementById("<? echo $winId; ?>-info5").innerHTML="
                <div class='cmdButton'
                     onClick='ajax(\"liveVars\",\"<? echo $appId; ?>\",\"<? echo $winId; ?>\",\"<? echo $data; ?>\",\"<? echo $phpdataArr[0]; ?>\");'
                     style='width:100%;'>Aktualisieren
                </div>";

                var n='
                <table style="width:100%; height:100%; white-space:nowrap; table-layout:auto;" border="0" cellpadding="2" cellspacing="0">';
                    <? if ($n['status'] == 1) { ?>
                        n+='
                        <tr>
                            <td align="center" style="background:#ff8000;">Status: zyklischer Aufruf (mit kleinst möglichem Intervall)</td>
                        </tr>';
                    <? } else if ($n['status'] > 1) { ?>
                        n+='
                        <tr>
                            <td align="center" style="background:#ff8000;">Status: zyklischer Aufruf (alle <? echo $n['status']; ?> ms)</td>
                        </tr>';
                    <? } else { ?>
                        n+='
                        <tr>
                            <td align="center" style="background:#bbbb00;">Status: inaktiv</td>
                        </tr>';
                    <? }
                    $tmp = sql_getValue('edomiLive.logicExecQueue', 'COUNT(DISTINCT ts)', 'elementid=' . $phpdataArr[0]);
                    if ($n['statusexec'] != 0 || $tmp > 0) { ?>
                        n+='
                        <tr>
                            <td align="center" style="background:#bbbb00;"><? echo(($n['statusexec'] != 0) ? 'EXEC-Script: wird ausgeführt / ' : ''); ?>
                                EXEC-Queue-Einträge: <? echo $tmp; ?></td>
                        </tr>';
                    <? } ?>
                    n+='
                </table>';
                document.getElementById("<? echo $winId; ?>-info0").innerHTML=n;

                var n='
                <table style="white-space:nowrap; table-layout:auto;" border="0" cellpadding="2" cellspacing="0">';
                    <? $ss2 = sql_call("SELECT * FROM edomiLive.RAMlogicElementVar WHERE (elementid=" . $phpdataArr[0] . ") ORDER BY varid ASC");
                    while ($nn = sql_result($ss2)) { ?>
                        n+='
                        <tr style="<? if ($nn['remanent'] == 1) {
                            echo 'color:#f00000;';
                        } ?>">';
                            n+='
                            <td>V<? echo $nn['varid']; ?></td>
                            ';
                            n+='
                            <td>=</td>
                            ';
                            n+='
                            <td><? ajaxEcho($nn['value']); ?></td>
                            ';
                            n+='
                        </tr>';
                    <? } ?>
                    n+='
                </table>';
                document.getElementById("<? echo $winId; ?>-info1").innerHTML=n;

                var n='
                <table style="white-space:nowrap; table-layout:auto;" border="0" cellpadding="2" cellspacing="0">';
                    <? $ss2 = sql_call("SELECT eingang,value FROM edomiLive.RAMlogicLink WHERE (elementid=" . $phpdataArr[0] . ") ORDER BY eingang ASC");
                    while ($nn = sql_result($ss2)) { ?>
                        n+='
                        <tr>';
                            n+='
                            <td>E<? echo $nn['eingang']; ?></td>
                            ';
                            n+='
                            <td>=</td>
                            ';
                            n+='
                            <td><? ajaxEcho($nn['value']); ?></td>
                            ';
                            n+='
                        </tr>';
                    <? } ?>
                    n+='
                </table>';
                document.getElementById("<? echo $winId; ?>-info2").innerHTML=n;

                var n='
                <table style="white-space:nowrap; table-layout:auto;" border="0" cellpadding="2" cellspacing="0">';
                    <? $ss2 = sql_call("SELECT DISTINCT(ausgang),value FROM edomiLive.RAMlogicLink WHERE (linktyp=1 AND linkid=" . $phpdataArr[0] . ") ORDER BY ausgang ASC");
                    while ($nn = sql_result($ss2)) { ?>
                        n+='
                        <tr>';
                            n+='
                            <td>A<? echo $nn['ausgang']; ?></td>
                            ';
                            n+='
                            <td>=</td>
                            ';
                            n+='
                            <td><? ajaxEcho($nn['value']); ?></td>
                            ';
                            n+='
                        </tr>';
                    <? } ?>
                    n+='
                </table>';
                document.getElementById("<? echo $winId; ?>-info3").innerHTML=n;

                document.getElementById("<? echo $winId; ?>-live").style.display="table-row";
            <? }
        }
    }
}

sql_disconnect(); ?>
