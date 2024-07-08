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
            <div class='appTitelDrag' onMouseDown='dragWindowStart(\"<? echo $appId; ?>\",\"<? echo $winId; ?>-global\");'>Schaltzeit (Zeitschaltuhr)
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
    if ($cmd == 'start') {
        $ss1 = sql_call("SELECT * FROM edomiProject.editTimerData WHERE (id=" . $dataArr[4] . ") ORDER BY id ASC");
        if ($n = sql_result($ss1)) {
            $fd[1] = $n['id'];
            $fd[2] = $n['targetid'];
            $fd[3] = $n['d0'];
            $fd[4] = $n['d1'];
            $fd[5] = $n['d2'];
            $fd[6] = $n['d3'];
            $fd[7] = $n['d4'];
            $fd[8] = $n['d5'];
            $fd[9] = $n['d6'];
            $fd[10] = $n['hour'];
            $fd[11] = $n['minute'];
            $fd[12] = $n['day1'];
            $fd[13] = $n['month1'];
            $fd[14] = $n['year1'];
            $fd[15] = $n['day2'];
            $fd[16] = $n['month2'];
            $fd[17] = $n['year2'];
            $fd[18] = $n['cmdid'];
            $fd[19] = $n['mode'];
            $fd[20] = $n['d7'];
            $fd[10] = sprintf("%02d", $fd[10]);
            $fd[11] = sprintf("%02d", $fd[11]);
            if (isEmpty($fd[12]) || $fd[12] < 1 || $fd[12] > 31) {
                $fd[12] = '';
            } else {
                $fd[12] = sprintf("%02d", $fd[12]);
            }
            if (isEmpty($fd[13]) || $fd[13] < 1 || $fd[13] > 12) {
                $fd[13] = '';
            } else {
                $fd[13] = sprintf("%02d", $fd[13]);
            }
            if (isEmpty($fd[14]) || $fd[14] < 0 || $fd[14] > 9999) {
                $fd[14] = '';
            } else {
                $fd[14] = sprintf("%04d", $fd[14]);
            }
            if (isEmpty($fd[15]) || $fd[15] < 1 || $fd[15] > 31) {
                $fd[15] = '';
            } else {
                $fd[15] = sprintf("%02d", $fd[15]);
            }
            if (isEmpty($fd[16]) || $fd[16] < 1 || $fd[16] > 12) {
                $fd[16] = '';
            } else {
                $fd[16] = sprintf("%02d", $fd[16]);
            }
            if (isEmpty($fd[17]) || $fd[17] < 0 || $fd[17] > 9999) {
                $fd[17] = '';
            } else {
                $fd[17] = sprintf("%04d", $fd[17]);
            }
        } else {
            $fd[1] = -1;
            $fd[2] = $dataArr[2];
            $fd[3] = 0;
            $fd[4] = 0;
            $fd[5] = 0;
            $fd[6] = 0;
            $fd[7] = 0;
            $fd[8] = 0;
            $fd[9] = 0;
            $fd[10] = '';
            $fd[11] = '';
            $fd[12] = '';
            $fd[13] = '';
            $fd[14] = '';
            $fd[15] = '';
            $fd[16] = '';
            $fd[17] = '';
            $fd[18] = '';
            $fd[19] = 0;
            $fd[20] = 0;
        }
        sql_close($ss1);
        $fd[0] = date('Y'); ?>
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
        <div id='<? echo $winId; ?>-form1' class='appContent' style='padding:4px;'>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd0' data-type='1' value='<? echo $fd[0]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd1' data-type='1' value='<? echo $fd[1]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd2' data-type='1' value='<? echo $fd[2]; ?>'></input>";
            n+="
            <table width='100%' border='0' cellpadding='5' cellspacing='0'>";
                n+="
                <colgroup>";
                    n+="
                    <col width='24%'>
                    ";
                    n+="
                    <col width='25%'>
                    ";
                    n+="
                    <col width='1%'>
                    ";
                    n+="
                    <col width='1%'>
                    ";
                    n+="
                    <col width='49%'>
                    ";
                    n+="
                </colgroup>
                ";
                n+="
                <tr>";
                    n+="
                    <td>";
                        n+="Uhrzeit<br><input id='<? echo $winId; ?>-fd10' type='text' data-type='1' maxlength='2' autofocus value='' class='control1'
                                              style='width:25px;'> : ";
                        n+="<input id='<? echo $winId; ?>-fd11' type='text' data-type='1' maxlength='2' value='' class='control1' style='width:25px;'> Uhr ";
                        n+="
                    </td>
                    ";
                    n+="
                    <td>Zusatzbedingung<br>
                        <div id='<? echo $winId; ?>-fd20' data-type='4' data-value='<? echo $fd[20]; ?>' data-list='KO|KO|<s>&nbsp;KO&nbsp;</s>|'
                             class='control5' style='width:100%;'>&nbsp;
                        </div>
                    </td>
                    ";
                    n+="
                    <td rowspan='2' style='border-right:1px solid #a0a0a0;'>
                        <div style='width:1px;'></div>
                    </td>
                    ";
                    n+="
                    <td rowspan='2'>
                        <div style='width:1px;'></div>
                    </td>
                    ";
                    n+="
                    <td>";
                        n+="Wochentage<br>
                        <div id='<? echo $winId; ?>-fd3' data-type='5' data-value='<? echo $fd[3]; ?>' class='control5'
                             style='width:25px;'><? echo substr($global_weekdays[0], 0, 2); ?></div>&nbsp;&nbsp;";
                        n+="
                        <div id='<? echo $winId; ?>-fd4' data-type='5' data-value='<? echo $fd[4]; ?>' class='control5'
                             style='width:25px;'><? echo substr($global_weekdays[1], 0, 2); ?></div>&nbsp;&nbsp;";
                        n+="
                        <div id='<? echo $winId; ?>-fd5' data-type='5' data-value='<? echo $fd[5]; ?>' class='control5'
                             style='width:25px;'><? echo substr($global_weekdays[2], 0, 2); ?></div>&nbsp;&nbsp;";
                        n+="
                        <div id='<? echo $winId; ?>-fd6' data-type='5' data-value='<? echo $fd[6]; ?>' class='control5'
                             style='width:25px;'><? echo substr($global_weekdays[3], 0, 2); ?></div>&nbsp;&nbsp;";
                        n+="
                        <div id='<? echo $winId; ?>-fd7' data-type='5' data-value='<? echo $fd[7]; ?>' class='control5'
                             style='width:25px;'><? echo substr($global_weekdays[4], 0, 2); ?></div>&nbsp;&nbsp;";
                        n+="
                        <div id='<? echo $winId; ?>-fd8' data-type='5' data-value='<? echo $fd[8]; ?>' class='control5'
                             style='width:25px;'><? echo substr($global_weekdays[5], 0, 2); ?></div>&nbsp;&nbsp;";
                        n+="
                        <div id='<? echo $winId; ?>-fd9' data-type='5' data-value='<? echo $fd[9]; ?>' class='control5'
                             style='width:25px;'><? echo substr($global_weekdays[6], 0, 2); ?></div>&nbsp;&nbsp;";
                        n+="
                    </td>
                    ";
                    n+="
                </tr>
                ";
                n+="
                <tr>";
                    n+="
                    <td colspan='2'>Makro<br>
                        <div id='<? echo $winId; ?>-fd18' data-type='1000' data-root='95' data-value='<? echo $fd[18]; ?>' data-options='typ=1'
                             class='control10' style='width:100%;'>&nbsp;
                        </div>
                    </td>
                    ";
                    n+="
                    <td>";
                        n+="Filter<br><input id='<? echo $winId; ?>-fd12' type='text' data-type='1' maxlength='2' placeholder='T' value='' class='control1'
                                             style='width:25px;'>.";
                        n+="<input id='<? echo $winId; ?>-fd13' type='text' data-type='1' maxlength='2' placeholder='M' value='' class='control1'
                                   style='width:25px;'>.";
                        n+="<input id='<? echo $winId; ?>-fd14' type='text' data-type='1' maxlength='4' placeholder='J' value='' class='control1'
                                   style='width:40px;'>";
                        n+="&nbsp;&nbsp;<div id='<? echo $winId; ?>-fd19' data-type='5' data-value='<? echo $fd[19]; ?>' class='control5' style='width:25px;'>
                            &gt;
                        </div>&nbsp;&nbsp;";
                        n+="<input id='<? echo $winId; ?>-fd15' type='text' data-type='1' maxlength='2' placeholder='T' value='' class='control1'
                                   style='width:25px;'>.";
                        n+="<input id='<? echo $winId; ?>-fd16' type='text' data-type='1' maxlength='2' placeholder='M' value='' class='control1'
                                   style='width:25px;'>.";
                        n+="<input id='<? echo $winId; ?>-fd17' type='text' data-type='1' maxlength='4' placeholder='J' value='' class='control1'
                                   style='width:40px;'>";
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
            <div id='<? echo $winId; ?>-preview'></div>
            ";
            n+="
        </div>";
        document.getElementById("<? echo $winId; ?>-main").innerHTML=n;

        document.getElementById("<? echo $winId; ?>-fd10").value='<? ajaxValue($fd[10]); ?>';
        document.getElementById("<? echo $winId; ?>-fd11").value='<? ajaxValue($fd[11]); ?>';
        document.getElementById("<? echo $winId; ?>-fd12").value='<? ajaxValue($fd[12]); ?>';
        document.getElementById("<? echo $winId; ?>-fd13").value='<? ajaxValue($fd[13]); ?>';
        document.getElementById("<? echo $winId; ?>-fd14").value='<? ajaxValue($fd[14]); ?>';
        document.getElementById("<? echo $winId; ?>-fd15").value='<? ajaxValue($fd[15]); ?>';
        document.getElementById("<? echo $winId; ?>-fd16").value='<? ajaxValue($fd[16]); ?>';
        document.getElementById("<? echo $winId; ?>-fd17").value='<? ajaxValue($fd[17]); ?>';

        controlInitAll("<? echo $winId; ?>-form1");
        appAll_setAutofocus("<? echo $winId; ?>-form1");
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
        $filter = array($phpdataArr[19], $phpdataArr[3], $phpdataArr[4], $phpdataArr[5], $phpdataArr[6], $phpdataArr[7], $phpdataArr[8], $phpdataArr[9], $phpdataArr[12], $phpdataArr[13], $phpdataArr[14], $phpdataArr[15], $phpdataArr[16], $phpdataArr[17], $phpdataArr[18]); ?>
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
                                <div class='cmdButton cmdButtonM'
                                     onClick='ajax(\"preview\",\"<? echo $appId; ?>\",\"<? echo $winId; ?>\",\"<? echo $data; ?>\",controlGetFormData(\"<? echo $winId; ?>-form1\"));'
                                     style='width:100%;'>Vorschau für <? echo $phpdataArr[0]; ?></div>
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
                <td><? echo print_Calendar(1, $phpdataArr[0], 1, $filter); ?></td>
                ";
                n+="
                <td><? echo print_Calendar(2, $phpdataArr[0], 1, $filter); ?></td>
                ";
                n+="
                <td><? echo print_Calendar(3, $phpdataArr[0], 1, $filter); ?></td>
                ";
                n+="
                <td><? echo print_Calendar(4, $phpdataArr[0], 1, $filter); ?></td>
                ";
                n+="
            </tr>
            ";
            n+="
            <tr valign='top' bgcolor='#ffffff'>";
                n+="
                <td><? echo print_Calendar(5, $phpdataArr[0], 1, $filter); ?></td>
                ";
                n+="
                <td><? echo print_Calendar(6, $phpdataArr[0], 1, $filter); ?></td>
                ";
                n+="
                <td><? echo print_Calendar(7, $phpdataArr[0], 1, $filter); ?></td>
                ";
                n+="
                <td><? echo print_Calendar(8, $phpdataArr[0], 1, $filter); ?></td>
                ";
                n+="
            </tr>
            ";
            n+="
            <tr valign='top' bgcolor='#ffffff'>";
                n+="
                <td><? echo print_Calendar(9, $phpdataArr[0], 1, $filter); ?></td>
                ";
                n+="
                <td><? echo print_Calendar(10, $phpdataArr[0], 1, $filter); ?></td>
                ";
                n+="
                <td><? echo print_Calendar(11, $phpdataArr[0], 1, $filter); ?></td>
                ";
                n+="
                <td><? echo print_Calendar(12, $phpdataArr[0], 1, $filter); ?></td>
                ";
                n+="
            </tr>
            ";
            n+="
        </table>";
        document.getElementById("<? echo $winId; ?>-preview").innerHTML=n;
    <? }
    if ($cmd == 'saveItem') {
        $dbId = db_itemSave('editTimerData', $phpdataArr);
        if ($dbId > 0) { ?>
            controlReturn("<? echo $winId; ?>","<? echo $dataArr[0]; ?>","<? echo $dataArr[2]; ?>");
        <? } else { ?>
            shakeObj("<? echo $winId; ?>");
        <? }
    }
    if ($cmd == 'deleteItem') {
        db_itemDelete('editTimerData', $phpdataArr[0]); ?>
        controlReturn("","<? echo $dataArr[0]; ?>","<? echo $dataArr[2]; ?>");
    <? }
    if ($cmd == 'duplicateItem') {
        db_itemDuplicate('editTimerData', $phpdataArr[0]); ?>
        controlReturn("","<? echo $dataArr[0]; ?>","<? echo $dataArr[2]; ?>");
    <? }
}

sql_disconnect(); ?>
