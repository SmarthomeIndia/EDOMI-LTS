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
            <div class='appTitelDrag' onMouseDown='dragWindowStart(\"<? echo $appId; ?>\",\"<? echo $winId; ?>-global\");'>Termin (Terminschaltuhr)
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
        $ss1 = sql_call("SELECT * FROM edomiProject.editAgendaData WHERE (id=" . $dataArr[4] . ") ORDER BY id ASC");
        if ($n = sql_result($ss1)) {
            $fd[1] = $n['id'];
            $fd[2] = $n['targetid'];
            $fd[3] = $n['cmdid'];
            $fd[4] = $n['hour'];
            $fd[5] = $n['minute'];
            $fd[6] = sql_getDate($n['date1']);
            $fd[7] = sql_getDate($n['date2']);
            $fd[8] = $n['step'];
            $fd[9] = $n['unit'];
            $fd[10] = $n['name'];
            $fd[11] = $n['d7'];
            $fd[4] = sprintf("%02d", $fd[4]);
            $fd[5] = sprintf("%02d", $fd[5]);
            if (!($fd[8] > 0)) {
                $fd[8] = '';
            }
        } else {
            $fd[1] = -1;
            $fd[2] = $dataArr[2];
            $fd[3] = '';
            $fd[4] = '';
            $fd[5] = '';
            $fd[6] = '';
            $fd[7] = '';
            $fd[8] = '';
            $fd[9] = 0;
            $fd[10] = '';
            $fd[11] = 0;
        }
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
                    <col width='16%'>
                    ";
                    n+="
                    <col width='16%'>
                    ";
                    n+="
                    <col width='16%'>
                    ";
                    n+="
                    <col width='1%'>
                    ";
                    n+="
                    <col width='1%'>
                    ";
                    n+="
                    <col width='24%'>
                    ";
                    n+="
                    <col width='25%'>
                    ";
                    n+="
                </colgroup>
                ";
                n+="
                <tr>";
                    n+="
                    <td colspan='3'>";
                        n+="Name<br><input type='text' id='<? echo $winId; ?>-fd10' data-type='1' maxlength='100' value='' class='control1' autofocus
                                           style='width:100%;'></input>";
                        n+="
                    </td>
                    ";
                    n+="
                    <td rowspan='3' style='border-right:1px solid #a0a0a0;'>
                        <div style='width:1px;'></div>
                    </td>
                    ";
                    n+="
                    <td rowspan='3'>
                        <div style='width:1px;'></div>
                    </td>
                    ";
                    n+="
                    <td colspan='2'><b>Terminwiederholung</b></td>
                    ";
                    n+="
                </tr>
                ";
                n+="
                <tr>";
                    n+="
                    <td>";
                        n+="Datum<br><input id='<? echo $winId; ?>-fd6' type='text' data-type='1' maxlength='10' value='' class='control1' style='width:100%;'>";
                        n+="
                    </td>
                    ";
                    n+="
                    <td>";
                        n+="Uhrzeit<br><input id='<? echo $winId; ?>-fd4' type='text' data-type='1' maxlength='2' value='' class='control1' style='width:25px;'>
                        : <input id='<? echo $winId; ?>-fd5' type='text' data-type='1' maxlength='2' value='' class='control1' style='width:25px;'> Uhr ";
                        n+="
                    </td>
                    ";
                    n+="
                    <td>Zusatzbedingung<br>
                        <div id='<? echo $winId; ?>-fd11' data-type='4' data-value='<? echo $fd[11]; ?>' data-list='KO|KO|<s>&nbsp;KO&nbsp;</s>|'
                             class='control5' style='width:100%;'>&nbsp;
                        </div>
                    </td>
                    ";
                    n+="
                    <td>";
                        n+="Intervall (leer=einmalig)<br><input id='<? echo $winId; ?>-fd8' type='text' data-type='1' value='' class='control1'
                                                                style='width:100%;'>";
                        n+="
                    </td>
                    ";
                    n+="
                    <td>";
                        n+="Einheit<br>
                        <div id='<? echo $winId; ?>-fd9' data-type='6' data-value='<? echo $fd[9]; ?>' data-list='0|Tage;1|Wochen;2|Monate;3|Jahre;'
                             class='control6' style='width:100%;'>&nbsp;
                        </div>
                        ";
                        n+="
                    </td>
                    ";
                    n+="
                </tr>
                ";
                n+="
                <tr>";
                    n+="
                    <td colspan='3'>";
                        n+="Makro<br>
                        <div id='<? echo $winId; ?>-fd3' data-type='1000' data-root='95' data-value='<? echo $fd[3]; ?>' data-options='typ=1' class='control10'
                             style='width:100%;'>&nbsp;
                        </div>
                        ";
                        n+="
                    </td>
                    ";
                    n+="
                    <td colspan='2'>";
                        n+="Enddatum (leer=unendlich)<br><input id='<? echo $winId; ?>-fd7' type='text' data-type='1' maxlength='10' value='' class='control1'
                                                                style='width:100%;'>";
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

        document.getElementById("<? echo $winId; ?>-fd4").value='<? ajaxValue($fd[4]); ?>';
        document.getElementById("<? echo $winId; ?>-fd5").value='<? ajaxValue($fd[5]); ?>';
        document.getElementById("<? echo $winId; ?>-fd6").value='<? ajaxValue($fd[6]); ?>';
        document.getElementById("<? echo $winId; ?>-fd7").value='<? ajaxValue($fd[7]); ?>';
        document.getElementById("<? echo $winId; ?>-fd8").value='<? ajaxValue($fd[8]); ?>';
        document.getElementById("<? echo $winId; ?>-fd10").value='<? ajaxValue($fd[10]); ?>';

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
        $filter = array($phpdataArr[6], $phpdataArr[7], $phpdataArr[8], $phpdataArr[9], $phpdataArr[3]); ?>
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
                <td><? echo print_Calendar(1, $phpdataArr[0], 2, $filter); ?></td>
                ";
                n+="
                <td><? echo print_Calendar(2, $phpdataArr[0], 2, $filter); ?></td>
                ";
                n+="
                <td><? echo print_Calendar(3, $phpdataArr[0], 2, $filter); ?></td>
                ";
                n+="
                <td><? echo print_Calendar(4, $phpdataArr[0], 2, $filter); ?></td>
                ";
                n+="
            </tr>
            ";
            n+="
            <tr valign='top' bgcolor='#ffffff'>";
                n+="
                <td><? echo print_Calendar(5, $phpdataArr[0], 2, $filter); ?></td>
                ";
                n+="
                <td><? echo print_Calendar(6, $phpdataArr[0], 2, $filter); ?></td>
                ";
                n+="
                <td><? echo print_Calendar(7, $phpdataArr[0], 2, $filter); ?></td>
                ";
                n+="
                <td><? echo print_Calendar(8, $phpdataArr[0], 2, $filter); ?></td>
                ";
                n+="
            </tr>
            ";
            n+="
            <tr valign='top' bgcolor='#ffffff'>";
                n+="
                <td><? echo print_Calendar(9, $phpdataArr[0], 2, $filter); ?></td>
                ";
                n+="
                <td><? echo print_Calendar(10, $phpdataArr[0], 2, $filter); ?></td>
                ";
                n+="
                <td><? echo print_Calendar(11, $phpdataArr[0], 2, $filter); ?></td>
                ";
                n+="
                <td><? echo print_Calendar(12, $phpdataArr[0], 2, $filter); ?></td>
                ";
                n+="
            </tr>
            ";
            n+="
        </table>";
        document.getElementById("<? echo $winId; ?>-preview").innerHTML=n;
    <? }
    if ($cmd == 'saveItem') {
        $dbId = db_itemSave('editAgendaData', $phpdataArr);
        if ($dbId > 0) { ?>
            controlReturn("<? echo $winId; ?>","<? echo $dataArr[0]; ?>","<? echo $dataArr[2]; ?>");
        <? } else { ?>
            shakeObj("<? echo $winId; ?>");
        <? }
    }
    if ($cmd == 'deleteItem') {
        db_itemDelete('editAgendaData', $phpdataArr[0]); ?>
        controlReturn("","<? echo $dataArr[0]; ?>","<? echo $dataArr[2]; ?>");
    <? }
    if ($cmd == 'duplicateItem') {
        db_itemDuplicate('editAgendaData', $phpdataArr[0]); ?>
        controlReturn("","<? echo $dataArr[0]; ?>","<? echo $dataArr[2]; ?>");
    <? }
}

sql_disconnect(); ?>
