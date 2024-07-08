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
    if ($cmd == 'initApp') { ?>
        var n="
        <div class='appWindowDrag' id='<? echo $winId; ?>-global'>";
            n+="
            <div class='appTitelDrag' onMouseDown='dragWindowStart(\"<? echo $appId; ?>\",\"<? echo $winId; ?>-global\");'>Initialwert/Fixwert
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
        <? cmd('start');
    }
    if ($cmd == 'start') { ?>
        var n="
        <div class='appMenu'>";
            n+="
            <div class='cmdButton cmdButtonL' onClick='closeWindow(\"<? echo $winId; ?>\");'>Abbrechen</div>
            ";
            n+="
            <div class='cmdButton cmdButtonR'
                 onClick='ajax(\"validateValue\",\"<? echo $appId; ?>\",\"<? echo $winId; ?>\",\"<? echo $dataArr[0]; ?>\",controlGetFormData(\"<? echo $winId; ?>-form1\"));'>
                <b>Ok</b></div>
            ";
            n+="
        </div>";
        n+="
        <div class='appContent' id='<? echo $winId; ?>-form1'
             onkeydown='if (event.keyCode==13) {ajax(\"validateValue\",\"<? echo $appId; ?>\",\"<? echo $winId; ?>\",\"<? echo $dataArr[0]; ?>\",controlGetFormData(\"<? echo $winId; ?>-form1\"));}'>
            ";
            n+="Wert<br><input type='text' id='<? echo $winId; ?>-fd0' data-type='1' value='' autofocus class='control1' style='width:100%;'></input>";
            n+="
        </div>";
        document.getElementById("<? echo $winId; ?>-main").innerHTML=n;

        document.getElementById("<? echo $winId; ?>-fd0").value='<? ajaxValue($dataArr[2]); ?>';
        appAll_setAutofocus("<? echo $winId; ?>-main");

        controlInitAll("<? echo $winId; ?>-form1");
    <? }
    if ($cmd == 'validateValue') { ?>
        controlReturn("<? echo $winId; ?>","<? echo $dataArr[0]; ?>","<? ajaxValue($phpdataArr[0]); ?>");
    <? }
}

sql_disconnect(); ?>

