<?
/*
*/
?><? ?><? require("../../shared/php/config.php");
require(MAIN_PATH . "/www/shared/php/base.php");
require(MAIN_PATH . "/www/shared/php/incl_http.php");
require(MAIN_PATH . "/www/visu/include/php/config.php");
require(MAIN_PATH . "/www/visu/include/php/base.php");
sql_connect();
$visuCmd = checkVisuSid($json1['visuId'], $sid, true);
if ($cmd == 'login' || $visuCmd) {
    cmd($cmd, $visuCmd);
} else {
    $ss1 = sql_call("SELECT * FROM edomiLive.visu ORDER BY id ASC");
    if ($n = sql_result($ss1)) {
        $html = "";
        do {
            $html .= "<option value='" . $n['id'] . "' " . (($json1['visuId'] == $n['id']) ? "selected" : "") . ">" . ajaxEncode($n['name']) . " (" . $n['id'] . ")</option>";
        } while ($n = sql_result($ss1)); ?>
        jsLogin("<? echo $json1['visuId']; ?>","<? echo $html; ?>");
    <? } else { ?>
        serverNotReady("<? echo $json1['visuId']; ?>","");
    <? }
}
function cmd($cmd, $visuCmd)
{
    global $json1, $json2, $sid, $vid;
    if ($cmd == 'login') {
        $sid = loginVisu($json2[0], $json2[1], $json2[2]);
        if ($sid !== false) { ?>
            sid="<? echo $sid; ?>";
            document.getElementById("login").style.display="none";
            document.getElementById("login").innerHTML="";
            initVisu("<? echo $json2[0]; ?>");
        <? } else { ?>
            document.getElementById("login-loginform-fd1").value="";
            document.getElementById("login-loginform-fd2").value="";
            document.getElementById("login-loginform-fd1").focus();
            shakeObj("loginform");
        <? }
    }
    if ($cmd == 'initVisu') {
        if ($vid != global_version) { ?>
            self.location.reload();
            <? return;
        } ?>
        document.getElementById("windowContainer").style.display="none";
        <? if (getEdomiStatus() == 3) {
            $ss1 = sql_call("SELECT * FROM edomiLive.visu WHERE (id=" . $json1['visuId'] . ")");
            if ($visu = sql_result($ss1)) {
                $visuUser = sql_getValues('edomiLive.visuUser', '*', 'id=' . $visuCmd['targetid']); ?>
                setInputMode("<? echo $visuUser['touch']; ?>","<? echo $visuUser['click']; ?>","<? echo $visuUser['touchscroll']; ?>");
                visu_flagSounds=<? echo(($visuUser['noacksounds'] == 1) ? 'false' : 'true'); ?>;
                visu_flagErrors=<? echo(($visuUser['noerrors'] == 1) ? 'false' : 'true'); ?>;
                visu_longclickSize=<? echo $visuUser['longclick']; ?>;
                <? $cssIndiColor1 = sql_getValue('edomiLive.visuFGcol', 'color', "id='" . $visu['indicolor'] . "'");
                if (isEmpty($cssIndiColor1)) {
                    $cssIndiColor1 = '#80e000';
                }
                $cssIndiColor2 = sql_getValue('edomiLive.visuFGcol', 'color', "id='" . $visu['indicolor2'] . "'");
                if (isEmpty($cssIndiColor2)) {
                    $cssIndiColor2 = 'inherit';
                } ?>
                preload_imagesFile=new Array();
                preload_imagesObj=new Array();
                var tmp_fonts="";
                var tmp_anims=".indicateClick{background:<? ajaxValue($cssIndiColor1); ?> !important;}\n";
                <? $ss2 = sql_call("SELECT * FROM edomiLive.visuMeta WHERE (" . (($visuUser['preload'] == 1) ? 'typ=28 OR ' : '') . "typ=150 OR typ=27) AND visuid=" . $json1['visuId']);
                while ($meta = sql_result($ss2)) {
                    if ($meta['typ'] == 28) {
                        if ($n = sql_getValues('edomiLive.visuImg', '*', 'id=' . $meta['id'])) {
                            $fn = 'img-' . $n['id'] . '.' . $n['suffix']; ?>
                            preload_imagesFile.push("../data/liveproject/visu/img/<? ajaxEcho($fn); ?>?<? echo $n['ts']; ?>");
                        <? }
                    } else if ($meta['typ'] == 150) {
                        if ($n = sql_getValues('edomiLive.visuFont', '*', 'id=' . $meta['id'])) {
                            if ($n['fonttyp'] == 0) { ?>
                                tmp_fonts+="@font-face {font-family:font<? echo $n['id']; ?>; src:local(<? ajaxValue($n['fontname']); ?>);}\n";
                            <? } else {
                                $fn = 'font-' . $n['id'] . '.ttf'; ?>
                                tmp_fonts+="@font-face {font-family:font<? echo $n['id']; ?>; font-style:<? if ($n['fontstyle'] == 0) {
                                    echo 'normal';
                                } else {
                                    echo 'italic';
                                } ?>; font-weight:<? if ($n['fontweight'] == 0) {
                                    echo 'normal';
                                } else {
                                    echo 'bold';
                                } ?>; src:url('../../data/liveproject/visu/etc/<? ajaxEcho($fn); ?>?<? echo $n['ts']; ?>') format('truetype');}\n";
                            <? }
                        }
                    } else if ($meta['typ'] == 27) {
                        if ($n = sql_getValues('edomiLive.visuAnim', '*', 'id=' . $meta['id'])) { ?>
                            tmp_anims+="@-webkit-keyframes anim<? echo $n['id']; ?> {<? ajaxValue(str_replace(chr(10), ' ', $n['keyframes'])); ?>}\n";
                        <? }
                    }
                }
                sql_close($ss2); ?>
                document.getElementById("cssFonts").innerHTML=tmp_fonts;
                document.getElementById("cssAnims").innerHTML=tmp_anims;
                <? ?>
                openWindowPage(<? echo $visu['xsize']; ?>,<? echo $visu['ysize']; ?>);
                <? ?>
                var d=document.getElementById("windowContainer");
                visu_indiColor='<? ajaxValue($cssIndiColor1); ?>';
                visu_indiColorText='<? ajaxValue($cssIndiColor2); ?>';
                visu_visuid=<? echo $visu['id']; ?>;
                visu_sspageid=<? echo $visuCmd['sspageid']; ?>;
                visu_sstimeout=<? echo $visu['sstimeout']; ?>;
                d.style.width="<? echo $visu['xsize']; ?>px";
                d.style.height="<? echo $visu['ysize']; ?>px";
                <? if ($visu['hassound'] == 1 || $visuCmd['hassound'] == 1) { ?>
                    visu_soundEnabled=1;
                    visuSoundInit();
                <? }
                if ($visu['hasspeech'] == 1 || $visuCmd['hasspeech'] == 1) { ?>
                    visu_textToSpeechEnabled=1;
                    visuTextToSpeechInit();
                <? }
                sql_call("DELETE FROM edomiLive.visuQueue WHERE (targetid='" . $visuCmd['id'] . "')"); ?>
                visu_preview=<? echo(($visu['preview'] == 1) ? 'true' : 'false'); ?>;
                <? if ($visuCmd['defaultpageid'] >= 1) { ?>
                    document.getElementById("meta-viewport").setAttribute('content','user-scalable=no, width=<? echo $visu['xsize']; ?>');
                    websocket_init("<? echo $visuCmd['defaultpageid']; ?>",<? echo global_visuWebsocketPort; ?>);
                <? } else { ?>
                    serverNotReady(<? echo $json1['visuId']; ?>,"KEINE STARTSEITE KONFIGURIERT");
                <? }
            } else { ?>
                serverNotReady(<? echo $json1['visuId']; ?>,"VISUALISIERUNG IST NICHT VERFÜGBAR");
            <? }
        } else { ?>
            serverNotReady(<? echo $json1['visuId']; ?>,"EDOMI IST NICHT BEREIT");
        <? }
    }
} ?>
