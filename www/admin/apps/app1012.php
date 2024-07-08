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
        $live = sql_getValues('edomiLive.sceneList', 'gavalue', "id=" . $phpdataArr[0] . " AND learngaid>0");
        if (checkLiveProjectData() && $live !== false) { ?>
            var n="
            <div class='appWindowDrag' id='<? echo $winId; ?>-global'>";
                n+="
                <div class='appTitelDrag' onMouseDown='dragWindowStart(\"<? echo $appId; ?>\",\"<? echo $winId; ?>-global\");'>Live: Lern-KO-Wert
                    (Szeneneintrag)
                    <div class='cmdClose' onClick='closeWindow(\"<? echo $winId; ?>\");'></div>
                    <div class='cmdHelp' onClick='openWindow(9999,\"<? echo $appId; ?>\");'></div>
                </div>
                ";
                n+="
                <div id='<? echo $winId; ?>-main' style='width:400px;'></div>
                ";
                n+="
                <div class='appMenu'>";
                    n+="
                    <div class='cmdButton cmdButtonL' onClick='closeWindow(\"<? echo $winId; ?>\");'>Abbrechen</div>
                    ";
                    n+="
                    <div class='cmdButton cmdButtonR'
                         onClick='ajax(\"setKoValue\",\"<? echo $appId; ?>\",\"<? echo $winId; ?>\",\"<? echo $data; ?>\",controlGetFormData(\"<? echo $winId; ?>-form1\"));'>
                        <b>Wert setzen(!)</b></div>
                    ";
                    n+="
                    <div class='cmdButton'
                         onClick='ajax(\"initApp\",\"<? echo $appId; ?>\",\"<? echo $winId; ?>\",\"<? echo $data; ?>\",\"<? echo $phpdata; ?>\");'
                         style='float:right;'>Aktualisieren
                    </div>
                    ";
                    n+="
                </div>
                ";
                n+="
                <div id='<? echo $winId; ?>-form1' class='appContent'>";
                    n+="<input type='hidden' id='<? echo $winId; ?>-fd0' data-type='1' value='<? echo $phpdataArr[0]; ?>'></input>";
                    n+="<textarea id='<? echo $winId; ?>-fd1' data-type='1' maxlength='10000' rows='10' wrap='soft' class='control1'
                                  style='width:100%; height:120px; background:#e8e800; resize:none;'></textarea>";
                    n+="
                </div>
                ";
                n+="
            </div>";
            document.getElementById("<? echo $winId; ?>").innerHTML=n;
            dragWindowRestore("<? echo $appId; ?>","<? echo $winId; ?>-global");
            document.getElementById("<? echo $winId; ?>-fd1").value="<? ajaxValue($live['gavalue']); ?>";
            window.setTimeout(function(){document.getElementById("<? echo $winId; ?>-fd1").select();},100);
            controlInitAll("<? echo $winId; ?>-form1");
        <? } else { ?>
            jsConfirm("Im Live-Projekt existiert kein Lern-KO für diesen Szeneneintrag.","","none");
            closeWindow("<? echo $winId; ?>");
        <? }
    }
    if ($cmd == 'setKoValue') {
        if (checkLiveProjectData()) {
            sql_call("UPDATE edomiLive.sceneList SET gavalue=" . sql_encodeValue($phpdataArr[1], true) . " WHERE id=" . $phpdataArr[0]);
            $ss1 = sql_call("SELECT gavalue,valuegaid FROM edomiLive.sceneList WHERE id=" . $phpdataArr[0] . " AND valuegaid>0");
            if ($n = sql_result($ss1)) {
                $ss2 = sql_call("SELECT id FROM edomiLive.RAMko WHERE id=" . $n['valuegaid']);
                if ($nn = sql_result($ss2)) {
                    sql_call("INSERT INTO edomiLive.RAMknxRead (mode,gatyp,gaid,value) VALUES (4,0," . $nn['id'] . ",'" . sql_encodeValue($n['gavalue']) . "')");
                }
                sql_close($ss2);
            }
            sql_close($ss1);
        } ?>
        closeWindow("<? echo $winId; ?>");
    <? }
}

sql_disconnect(); ?>

