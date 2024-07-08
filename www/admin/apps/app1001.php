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
            <div class='appTitelDrag' onMouseDown='dragWindowStart(\"<? echo $appId; ?>\",\"<? echo $winId; ?>-global\");'>Kommunikationsobjekt (Szenen)
                <div class='cmdClose cmdCloseDisabled'></div>
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
        $ss1 = sql_call("SELECT * FROM edomiProject.editSceneList WHERE (id=" . $dataArr[4] . ")");
        if ($n = sql_result($ss1)) {
            $fd[1] = $n['id'];
            $fd[2] = $n['targetid'];
            $fd[3] = $n['gaid'];
            $fd[4] = $n['gavalue'];
            $fd[5] = $n['learngaid'];
            $fd[6] = $n['valuegaid'];
        } else {
            $fd[1] = -1;
            $fd[2] = $dataArr[2];
            $fd[3] = 0;
            $fd[4] = '';
            $fd[5] = 0;
            $fd[6] = 0;
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
            <? if ($fd[1] > 0 && $fd[5] > 0 && checkLiveProjectData()) { ?>
                n+="
                <div class='cmdButton' onClick='openWindow(1012,\"\",\"<? echo $fd[1]; ?>\");' style='background:#e8e800; float:right;'>Live</div>";
            <? } ?>
            n+="
        </div>";
        n+="
        <div id='<? echo $winId; ?>-form1' class='appContent' style='padding:4px;'>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd1' data-type='1' value='<? echo $fd[1]; ?>' class='control1'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd2' data-type='1' value='<? echo $fd[2]; ?>' class='control1'></input>";
            n+="
            <table width='100%' border='0' cellpadding='5' cellspacing='0'>";
                n+="
                <tr>";
                    n+="
                    <td width='75%'>KO<br>
                        <div id='<? echo $winId; ?>-fd3' data-type='1000' data-root='30' data-value='<? echo $fd[3]; ?>' data-options='typ=1;reset=0'
                             class='control10' style='width:100%;'>&nbsp;
                        </div>
                    </td>
                    ";
                    n+="
                    <td>Initialwert<br><input type='text' id='<? echo $winId; ?>-fd4' data-type='1' value='' class='control1' style='width:100%;'></input></td>
                    ";
                    n+="
                </tr>
                ";
                n+="
                <tr>
                    <td colspan='2'>Lern-KO (leer=nicht lernbar)<br>
                        <div id='<? echo $winId; ?>-fd5' data-type='1000' data-root='30' data-value='<? echo $fd[5]; ?>' data-options='typ=1' class='control10'
                             style='width:100%;'>&nbsp;
                        </div>
                    </td>
                </tr>
                ";
                n+="
                <tr>
                    <td colspan='2'>Wert-KO<br>
                        <div id='<? echo $winId; ?>-fd6' data-type='1000' data-root='30' data-value='<? echo $fd[6]; ?>' data-options='typ=1' class='control10'
                             style='width:100%;'>&nbsp;
                        </div>
                    </td>
                </tr>
                ";
                n+="
            </table>
            ";
            n+="
        </div>";
        document.getElementById("<? echo $winId; ?>-main").innerHTML=n;

        document.getElementById("<? echo $winId; ?>-fd4").value='<? ajaxValue($fd[4]); ?>';

        controlInitAll("<? echo $winId; ?>-form1");
    <? }
    if ($cmd == 'saveItem') {
        $dbId = db_itemSave('editSceneList', $phpdataArr);
        if ($dbId > 0) { ?>
            controlReturn("<? echo $winId; ?>","<? echo $dataArr[0]; ?>","<? echo $dataArr[2]; ?>");
        <? } else { ?>
            shakeObj("<? echo $winId; ?>");
        <? }
    }
    if ($cmd == 'deleteItem') {
        db_itemDelete('editSceneList', $phpdataArr[0]); ?>
        controlReturn("","<? echo $dataArr[0]; ?>","<? echo $dataArr[2]; ?>");
    <? }
}

sql_disconnect(); ?>

