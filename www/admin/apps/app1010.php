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
        $koValue = '';
        $koTyp = 0;
        if (checkLiveProjectData()) {
            $ss1 = sql_call("SELECT value,gatyp,visuts FROM edomiLive.RAMko WHERE (id=" . $phpdataArr[0] . ")");
            if ($n = sql_result($ss1)) {
                $koValue = $n['value'];
                $koTyp = $n['gatyp'];
                $koTs = $n['visuts'];
                if (isEmpty($koTs)) {
                    $koTs = "<span style='color:#a0a0a0;'>(keine Änderung seit Start)</span>";
                } else {
                    $tmp = getDateFromTimestampVisu($koTs);
                    $koTs = $tmp[0] . ' / ' . $tmp[1] . ".<span style='color:#a0a0a0;'>" . $tmp[2] . "</span>";
                }
            }
            sql_close($ss1);
        }
        if ($koTyp > 0) { ?>
            var n="
            <div class='appWindowDrag' id='<? echo $winId; ?>-global'>";
                n+="
                <div class='appTitelDrag' onMouseDown='dragWindowStart(\"<? echo $appId; ?>\",\"<? echo $winId; ?>-global\");'>Live: KO-Wert
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
                     onClick='ajax(\"setKoValue\",\"<? echo $appId; ?>\",\"<? echo $winId; ?>\",\"<? echo $data; ?>\",controlGetFormData(\"<? echo $winId; ?>-form1\"));'>
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
                n+="
                <table width='100%' border='0' cellpadding='5' cellspacing='0'>";
                    n+="
                    <tr>
                        <td>
                            <div class='formTitel'><? echo getGaInfo(1, $phpdataArr[0]); ?></div>
                            <br>Letzte Änderung: <? echo $koTs; ?></td>
                    </tr>
                    ";
                    <? if ($koTyp == 1) { ?>
                        n+="
                        <tr>
                            <td><input type='text' id='<? echo $winId; ?>-fd2' data-type='1' value='' autofocus class='control1'
                                       style='width:100%; background:#e8e800;'></input></td>
                        </tr>";
                    <? } else { ?>
                        n+="
                        <tr>
                            <td><textarea id='<? echo $winId; ?>-fd2' data-type='1' maxlength='10000' rows='10' wrap='soft' class='control1'
                                          style='width:100%; height:120px; background:#e8e800; resize:none;'></textarea></td>
                        </tr>";
                    <? }
                    if ($phpdataArr[1] >= 0 && $phpdataArr[0] != $phpdataArr[1]) { ?>
                        n+="
                        <tr>
                            <td style='color:#f00000;'><b>Achtung:</b> Im Live-Projekt ist der Eingang des Logikbausteins mit diesem KO verknüpft, während der
                                Eingang im Arbeitsprojekt inwzischen mit einem anderen KO verknüpft wurde.
                            </td>
                        </tr>";
                    <? } else if ($phpdataArr[1] < 0) { ?>
                        n+="
                        <tr>
                            <td style='color:#f00000;'><b>Achtung:</b> Im Live-Projekt existieren keine Eingangsboxen, daher wird das im Arbeitsprojekt(!)
                                verknüpfte KO verwendet. Unter Umständen weicht die Belegung der mit der Eingangsbox verbundenen Eingänge im Live-Projekt
                                hiervon ab!
                            </td>
                        </tr>";
                    <? } ?>
                    n+="
                </table>
                ";
                n+="
            </div>";

            document.getElementById("<? echo $winId; ?>-main").innerHTML=n;

            document.getElementById("<? echo $winId; ?>-fd2").value="<? ajaxValue($koValue); ?>";
            appAll_setAutofocus("<? echo $winId; ?>-main");
            window.setTimeout(function(){document.getElementById("<? echo $winId; ?>-fd2").select();},100);

            controlInitAll("<? echo $winId; ?>-form1");
        <? } else { ?>
            jsConfirm("Das KO existiert nicht im Live-Projekt.","","none");
            closeWindow("<? echo $winId; ?>");
        <? }
    }
    if ($cmd == 'setKoValue') {
        if (checkLiveProjectData()) {
            $ss1 = sql_call("SELECT id FROM edomiLive.RAMko WHERE (id='" . $phpdataArr[0] . "')");
            if ($n = sql_result($ss1)) {
                sql_call("INSERT INTO edomiLive.RAMknxRead (mode,gatyp,gaid,value) VALUES (4,0," . $n['id'] . ",'" . sql_encodeValue($phpdataArr[2]) . "')");
            }
            sql_close($ss1);
        } ?>
        closeWindow("<? echo $winId; ?>");
    <? }
}

sql_disconnect(); ?>

