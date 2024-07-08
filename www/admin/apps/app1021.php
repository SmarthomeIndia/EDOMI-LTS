<?
/*
*/
?><? ?><? require("../../shared/php/config.php");
require(MAIN_PATH . "/www/shared/php/base.php");
require(MAIN_PATH . "/www/shared/php/incl_http.php");
require(MAIN_PATH . "/www/admin/include/php/config.php");
require(MAIN_PATH . "/www/admin/include/php/base.php");
require(MAIN_PATH . "/www/admin/include/php/incl_items.php");
sql_connect();
if (checkAdmin($sid)) {
    cmd($cmd);
}
function cmd($cmd)
{
    global $appId, $winId, $data, $dataArr, $phpdata, $phpdataArr, $sid;
    global $global_weekdays;
    if ($cmd == 'initApp') { ?>
        var n="
        <div class='appWindowDrag' id='<? echo $winId; ?>-global'>";
            n+="
            <div class='appTitelDrag' onMouseDown='dragWindowStart(\"<? echo $appId; ?>\",\"<? echo $winId; ?>-global\");'>
                Vorschau: <? echo(($phpdataArr[2] == 1) ? 'Schaltzeiten' : 'Termine'); ?>
                <div class='cmdClose' onClick='closeWindow(\"<? echo $winId; ?>\");'></div>
                <div class='cmdHelp' onClick='openWindow(9999,\"<? echo $appId; ?>\");'></div>
            </div>
            ";
            n+="
            <div id='<? echo $winId; ?>-main' style='width:700px;'></div>
            ";
            n+="
        </div>";
        document.getElementById("<? echo $winId; ?>").innerHTML=n;
        dragWindowRestore("<? echo $appId; ?>","<? echo $winId; ?>-global");
        <? cmd('start');
    }
    if ($cmd == 'start') { ?>
        var n="
        <div class='appMenu'>";
            n+="
            <div class='cmdButton' onClick='closeWindow(\"<? echo $winId; ?>\");'>Schliessen</div>
            ";
            n+="
        </div>";
        n+="
        <div id='<? echo $winId; ?>-form1' class='appContent' style='padding:6px;'>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd0' data-type='1' value='<? echo $phpdataArr[0]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd1' data-type='1' value='<? echo $phpdataArr[1]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd2' data-type='1' value='<? echo $phpdataArr[2]; ?>'></input>";
            n+="
            <div id='<? echo $winId; ?>-preview'></div>
            ";
            n+="
        </div>";
        document.getElementById("<? echo $winId; ?>-main").innerHTML=n;
        ajax("preview","<? echo $appId; ?>","<? echo $winId; ?>","<? echo $data; ?>",controlGetFormData("<? echo $winId; ?>-form1"));
    <? }
    if ($cmd == 'previewDec') {
        if ($phpdataArr[0] > 1000) {
            $phpdataArr[0]--; ?>
            document.getElementById("<? echo $winId; ?>-fd0").value='<? ajaxValue($phpdataArr[0]); ?>';
            <? $cmd = 'preview';
        }
    }
    if ($cmd == 'previewInc') {
        if ($phpdataArr[0] < 9999) {
            $phpdataArr[0]++; ?>
            document.getElementById("<? echo $winId; ?>-fd0").value='<? ajaxValue($phpdataArr[0]); ?>';
            <? $cmd = 'preview';
        }
    }
    if ($cmd == 'preview') {
        $filter = array();
        if ($phpdataArr[2] == 1) {
            $ss1 = sql_call("SELECT * FROM edomiProject.editTimerData WHERE (targetid=" . $phpdataArr[1] . ")");
            while ($n = sql_result($ss1)) {
                $filter[] = array($n['mode'], $n['d0'], $n['d1'], $n['d2'], $n['d3'], $n['d4'], $n['d5'], $n['d6'], $n['day1'], $n['month1'], $n['year1'], $n['day2'], $n['month2'], $n['year2'], $n['cmdid']);
            }
            sql_close($ss1);
        } else if ($phpdataArr[2] == 2) {
            $ss1 = sql_call("SELECT * FROM edomiProject.editAgendaData WHERE (targetid=" . $phpdataArr[1] . ")");
            while ($n = sql_result($ss1)) {
                $filter[] = array($n['date1'], $n['date2'], $n['step'], $n['unit'], $n['cmdid']);
            }
            sql_close($ss1);
        } ?>
        var n="
        <table width='100%' border='0' cellpadding='0' cellspacing='3'>";
            n+="
            <tr>";
                n+="
                <td colspan='4' align='center'>";
                    n+="
                    <table width='100%' border='0' cellpadding='0' cellspacing='0' style='table-layout:auto;'>";
                        n+="
                        <tr valign='middle'>";
                            n+="
                            <td>
                                <div class='cmdButton cmdButtonL'
                                     onClick='ajax(\"previewDec\",\"<? echo $appId; ?>\",\"<? echo $winId; ?>\",\"<? echo $data; ?>\",controlGetFormData(\"<? echo $winId; ?>-form1\"));'>
                                    &lt;
                                </div>
                            </td>
                            ";
                            n+="
                            <td width='100%' align='center' style='color:#ffffff;'>
                                <div class='cmdButton cmdButtonM' style='width:100%;'>Vorschau für <? echo $phpdataArr[0]; ?></div>
                            </td>
                            ";
                            n+="
                            <td align='right'>
                                <div class='cmdButton cmdButtonR'
                                     onClick='ajax(\"previewInc\",\"<? echo $appId; ?>\",\"<? echo $winId; ?>\",\"<? echo $data; ?>\",controlGetFormData(\"<? echo $winId; ?>-form1\"));'>
                                    &gt;
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
                </td>
                ";
                n+="
            </tr>
            ";
            n+="
            <tr valign='top' bgcolor='#ffffff'>";
                n+="
                <td><? echo print_Calendar(1, $phpdataArr[0], $phpdataArr[2], $filter, true); ?></td>
                ";
                n+="
                <td><? echo print_Calendar(2, $phpdataArr[0], $phpdataArr[2], $filter, true); ?></td>
                ";
                n+="
                <td><? echo print_Calendar(3, $phpdataArr[0], $phpdataArr[2], $filter, true); ?></td>
                ";
                n+="
                <td><? echo print_Calendar(4, $phpdataArr[0], $phpdataArr[2], $filter, true); ?></td>
                ";
                n+="
            </tr>
            ";
            n+="
            <tr valign='top' bgcolor='#ffffff'>";
                n+="
                <td><? echo print_Calendar(5, $phpdataArr[0], $phpdataArr[2], $filter, true); ?></td>
                ";
                n+="
                <td><? echo print_Calendar(6, $phpdataArr[0], $phpdataArr[2], $filter, true); ?></td>
                ";
                n+="
                <td><? echo print_Calendar(7, $phpdataArr[0], $phpdataArr[2], $filter, true); ?></td>
                ";
                n+="
                <td><? echo print_Calendar(8, $phpdataArr[0], $phpdataArr[2], $filter, true); ?></td>
                ";
                n+="
            </tr>
            ";
            n+="
            <tr valign='top' bgcolor='#ffffff'>";
                n+="
                <td><? echo print_Calendar(9, $phpdataArr[0], $phpdataArr[2], $filter, true); ?></td>
                ";
                n+="
                <td><? echo print_Calendar(10, $phpdataArr[0], $phpdataArr[2], $filter, true); ?></td>
                ";
                n+="
                <td><? echo print_Calendar(11, $phpdataArr[0], $phpdataArr[2], $filter, true); ?></td>
                ";
                n+="
                <td><? echo print_Calendar(12, $phpdataArr[0], $phpdataArr[2], $filter, true); ?></td>
                ";
                n+="
            </tr>
            ";
            n+="
        </table>";
        document.getElementById("<? echo $winId; ?>-preview").innerHTML=n;
    <? }
}

sql_disconnect(); ?>
