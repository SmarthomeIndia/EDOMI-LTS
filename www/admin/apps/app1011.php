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
        $liveValue = null;
        $liveLbsId = 0;
        $liveItemId = 0;
        $liveTyp = $phpdataArr[2];
        if (checkLiveProjectData()) {
            if ($phpdataArr[2] == 0) {
                $ss1 = sql_call("SELECT value FROM edomiLive.RAMlogicLink WHERE (elementid=" . $phpdataArr[0] . " AND eingang=" . $phpdataArr[1] . ")");
                if ($n = sql_result($ss1)) {
                    $liveValue = $n['value'];
                    $liveLbsId = $phpdataArr[0];
                    $liveItemId = $phpdataArr[1];
                }
                sql_close($ss1);
            } else {
                $ss1 = sql_call("SELECT value FROM edomiLive.RAMlogicLink WHERE (linkid=" . $phpdataArr[0] . " AND ausgang=" . $phpdataArr[1] . " AND linktyp=1) LIMIT 0,1");
                if ($n = sql_result($ss1)) {
                    $liveValue = $n['value'];
                    $liveLbsId = $phpdataArr[0];
                    $liveItemId = $phpdataArr[1];
                }
                sql_close($ss1);
            }
        }
        if ($liveLbsId > 0 && $liveItemId > 0) { ?>
            var n="
            <div class='appWindowDrag' id='<? echo $winId; ?>-global'>";
                n+="
                <div class='appTitelDrag' onMouseDown='dragWindowStart(\"<? echo $appId; ?>\",\"<? echo $winId; ?>-global\");'>Live: Eingangs-/Ausgangswert
                    <div class='cmdClose' onClick='closeWindow(\"<? echo $winId; ?>\");'></div>
                    <div class='cmdHelp' onClick='openWindow(9999,\"<? echo $appId; ?>\");'></div>
                </div>
                ";
                n+="
                <div id='<? echo $winId; ?>-main' style='width:400px;'></div>
                ";
                n+="
            </div>";
            document.getElementById("<? echo $winId; ?>").innerHTML=n;
            dragWindowRestore("<? echo $appId; ?>","<? echo $winId; ?>-global");

            var n="
            <div class='appMenu'>";
                n+="
                <div class='cmdButton cmdButtonL' onClick='closeWindow(\"<? echo $winId; ?>\");'>Abbrechen</div>
                ";
                n+="
                <div class='cmdButton cmdButtonR'
                     onClick='ajax(\"setValue\",\"<? echo $appId; ?>\",\"<? echo $winId; ?>\",\"<? echo $data; ?>\",controlGetFormData(\"<? echo $winId; ?>-form1\"));'>
                    <b>Wert setzen(!)</b></div>
                ";
                n+="
                <div class='cmdButton' onClick='ajax(\"initApp\",\"<? echo $appId; ?>\",\"<? echo $winId; ?>\",\"<? echo $data; ?>\",\"<? echo $phpdata; ?>\");'
                     style='float:right;'>Aktualisieren
                </div>
                ";
                n+="
            </div>";

            n+="
            <div id='<? echo $winId; ?>-form1' class='appContent' style='padding:4px;'>";
                n+="<input type='hidden' id='<? echo $winId; ?>-fd0' data-type='1' value='<? echo $phpdataArr[0]; ?>'></input>";
                n+="<input type='hidden' id='<? echo $winId; ?>-fd1' data-type='1' value='<? echo $phpdataArr[1]; ?>'></input>";
                n+="<input type='hidden' id='<? echo $winId; ?>-fd2' data-type='1' value='<? echo $phpdataArr[2]; ?>'></input>";
                n+="
                <table width='100%' border='0' cellpadding='5' cellspacing='0'>";
                    n+="
                    <tr>
                        <td class='formTitel'><? echo getLbsInstanceInfo($liveLbsId, $liveItemId, $liveTyp); ?></td>
                    </tr>
                    ";
                    n+="
                    <tr>
                        <td><textarea id='<? echo $winId; ?>-fd3' data-type='1' maxlength='10000' rows='10' wrap='soft' class='control1'
                                      style='width:100%; height:120px; background:#e8e800; resize:none;'></textarea></td>
                    </tr>
                    ";
                    n+="
                </table>
                ";
                n+="
            </div>";

            document.getElementById("<? echo $winId; ?>-main").innerHTML=n;

            document.getElementById("<? echo $winId; ?>-fd3").value="<? ajaxValue($liveValue); ?>";
            appAll_setAutofocus("<? echo $winId; ?>-main");
            window.setTimeout(function(){document.getElementById("<? echo $winId; ?>-fd3").select();},100);

            controlInitAll("<? echo $winId; ?>-form1");
        <? } else { ?>
            jsConfirm("Der Eingang/Ausgang existiert nicht im Live-Projekt bzw. der Ausgang ist nicht belegt (oder der Logikbaustein existiert nicht im Live-Projekt).","","none");
            closeWindow("<? echo $winId; ?>");
        <? }
    }
    if ($cmd == 'setValue') {
        if (checkLiveProjectData()) {
            if ($phpdataArr[2] == 0) {
                sql_call("UPDATE edomiLive.RAMlogicLink SET refresh=1,value='" . sql_encodeValue($phpdataArr[3]) . "' WHERE (elementid=" . $phpdataArr[0] . " AND eingang=" . $phpdataArr[1] . ")");
            } else {
                sql_call("UPDATE edomiLive.RAMlogicLink SET refresh=1,value='" . sql_encodeValue($phpdataArr[3]) . "' WHERE (linkid=" . $phpdataArr[0] . " AND ausgang=" . $phpdataArr[1] . " AND linktyp=1)");
            }
        } ?>
        closeWindow("<? echo $winId; ?>");
    <? }
}

sql_disconnect(); ?>

