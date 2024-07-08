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
    if ($cmd == 'initApp') { ?>
        var n="
        <div class='appWindowDrag' id='<? echo $winId; ?>-global'>";
            n+="
            <div class='appTitelDrag' onMouseDown='dragWindowStart(\"<? echo $appId; ?>\",\"<? echo $winId; ?>-global\");'>Makro-Vorgabe (Zeitschaltuhr)
                <div class='cmdClose' onClick='closeWindow(\"<? echo $winId; ?>\");'></div>
                <div class='cmdHelp' onClick='openWindow(9999,\"<? echo $appId; ?>\");'></div>
            </div>
            ";
            n+="
            <div id='<? echo $winId; ?>-main' style='width:450px;'></div>
            ";
            n+="
        </div>";
        document.getElementById("<? echo $winId; ?>").innerHTML=n;
        dragWindowRestore("<? echo $appId; ?>","<? echo $winId; ?>-global");
        <? cmd('start');
    }
    if ($cmd == 'start') {
        $ss1 = sql_call("SELECT * FROM edomiProject.editTimerMacroList WHERE (id=" . $dataArr[4] . ") ORDER BY id ASC");
        if ($n = sql_result($ss1)) {
            $fd[1] = $n['id'];
            $fd[2] = $n['timerid'];
            $fd[3] = $n['targetid'];
            $fd[4] = $n['sort'];
        } else {
            $fd[1] = -1;
            $fd[2] = $dataArr[2];
            $fd[3] = 0;
            $fd[4] = 0;
        } ?>
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
            n+="<input type='hidden' id='<? echo $winId; ?>-fd1' data-type='1' value='<? echo $fd[1]; ?>' class='control1'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd2' data-type='1' value='<? echo $fd[2]; ?>' class='control1'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd4' data-type='1' value='<? echo $fd[4]; ?>' class='control1'></input>";
            n+="
            <table width='100%' border='0' cellpadding='5' cellspacing='0'>";
                n+="
                <tr>";
                    n+="
                    <td width='75%'>Makro<br>
                        <div id='<? echo $winId; ?>-fd3' data-type='1000' data-root='95' data-value='<? echo $fd[3]; ?>' data-options='typ=4;reset=0'
                             class='control10' style='width:100%;'>&nbsp;
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
        </div>";
        document.getElementById("<? echo $winId; ?>-main").innerHTML=n;
        controlInitAll("<? echo $winId; ?>-form1");
    <? }
    if ($cmd == 'saveItem') {
        $dbId = db_itemSave('editTimerMacroList', $phpdataArr);
        if ($dbId > 0) { ?>
            controlReturn("<? echo $winId; ?>","<? echo $dataArr[0]; ?>","<? echo $dataArr[2]; ?>");
        <? } else { ?>
            shakeObj("<? echo $winId; ?>");
        <? }
    }
    if ($cmd == 'deleteItem') {
        db_itemDelete('editTimerMacroList', $phpdataArr[0]); ?>
        controlReturn("","<? echo $dataArr[0]; ?>","<? echo $dataArr[2]; ?>");
    <? }
    if ($cmd == 'sortDecItem' || $cmd == 'sortIncItem') {
        $ss1 = sql_call("SELECT id,sort FROM edomiProject.editTimerMacroList WHERE (id=" . $phpdataArr[0] . ")");
        if ($item = sql_result($ss1)) {
            if ($cmd == 'sortDecItem') {
                $ss2 = sql_call("SELECT id,sort FROM edomiProject.editTimerMacroList WHERE (timerid=" . $dataArr[2] . " AND sort<" . $item['sort'] . ") ORDER BY sort DESC LIMIT 0,1");
            }
            if ($cmd == 'sortIncItem') {
                $ss2 = sql_call("SELECT id,sort FROM edomiProject.editTimerMacroList WHERE (timerid=" . $dataArr[2] . " AND sort>" . $item['sort'] . ") ORDER BY sort ASC LIMIT 0,1");
            }
            if ($n = sql_result($ss2)) {
                sql_call("UPDATE edomiProject.editTimerMacroList SET sort=" . $n['sort'] . " WHERE (id=" . $phpdataArr[0] . ")");
                sql_call("UPDATE edomiProject.editTimerMacroList SET sort=" . $item['sort'] . " WHERE (id=" . $n['id'] . ")");
            }
        } ?>
        controlReturn("","<? echo $dataArr[0]; ?>","<? echo $dataArr[2]; ?>");
    <? }
}

sql_disconnect(); ?>
