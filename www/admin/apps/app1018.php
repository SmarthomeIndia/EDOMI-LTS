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
        $fd[0] = $phpdataArr[0];
        $fd[1] = $phpdataArr[1];
        $fd[2] = $phpdataArr[2];
        $fd[3] = file_get_contents(MAIN_PATH . '/www/admin/lbs/' . $phpdataArr[1] . '_lbs.php'); ?>
        var n="
        <div class='appWindowFullscreen'>";
            n+="
            <div class='appTitel'>Logikbaustein <span class='idBig'><? echo $fd[1]; ?></span>
                <div class='cmdClose' onClick='closeWindow(\"<? echo $winId; ?>\");'></div>
                <div class='cmdHelp' onClick='openWindow(9999,\"<? echo $appId; ?>\");'></div>
            </div>
            ";
            n+="
            <div id='<? echo $winId; ?>-main' style='width:800px;'>"
                n+="
                <div class='appMenu'>";
                    <? if (dbRoot_getRootId($fd[2]) == 19) { ?>
                        n+="
                        <div class='cmdButton cmdButtonL' onClick='closeWindow(\"<? echo $winId; ?>\");'>Abbrechen</div>";
                                                                                                                        n+="
                        <div class='cmdButton cmdButtonR'
                             onClick='ajax(\"checkLbs\",\"<? echo $appId; ?>\",\"<? echo $winId; ?>\",\"<? echo $data; ?>\",controlGetFormData(\"<? echo $winId; ?>-form1\"));'>
                            <b>Übernehmen</b></div>";
                    <? } else { ?>
                        n+="
                        <div class='cmdButton' onClick='closeWindow(\"<? echo $winId; ?>\");'>Abbrechen</div>";
                    <? } ?>
                    n+="
                </div>
                ";
                n+="
                <div id='<? echo $winId; ?>-form1' style='position:absolute; left:5px; top:80px; right:5px; bottom:5px; overflow:hidden;'>";
                    n+="<input type='hidden' id='<? echo $winId; ?>-fd0' data-type='1' value='<? echo $fd[0]; ?>'></input>";
                    n+="<input type='hidden' id='<? echo $winId; ?>-fd1' data-type='1' value='<? echo $fd[1]; ?>'></input>";
                    n+="<input type='hidden' id='<? echo $winId; ?>-fd2' data-type='1' value='<? echo $fd[2]; ?>'></input>";
                    n+="<textarea id='<? echo $winId; ?>-fd3' data-type='1' wrap='off' class='control1' autofocus
                                  onkeydown='if (event.keyCode==9) {appAll_enableTabKey(this);}'
                                  style='position:absolute; left:0; top:0; width:100%; height:100%; font-family:EDOMIfontMono,Menlo,Courier,monospace; font-size:11px; line-height:1.5; resize:none; margin:0; padding:5px; background:#ffffff; outline:none; tab-size:4; border-radius:0; border:none;'></textarea>";
                    n+="
                </div>
                ";
                n+="
            </div>
            ";
            n+="
        </div>";
        document.getElementById("<? echo $winId; ?>").innerHTML=n;
        document.getElementById("<? echo $winId; ?>-fd3").value='<? ajaxValue($fd[3]); ?>';
        controlInitAll("<? echo $winId; ?>-form1");
    <? }
    if ($cmd == 'checkLbs') {
        $lbsFn = MAIN_PATH . '/www/admin/lbs/' . $phpdataArr[1] . '_lbs.php';
        $f = fopen($lbsFn, 'w');
        fwrite($f, $phpdataArr[3]);
        fclose($f); ?>
        ajax("lbsRefresh","1000","<? echo $phpdataArr[0]; ?>","<? echo $data; ?>","<? echo $phpdataArr[1]; ?>");
        closeWindow("<? echo $winId; ?>");
    <? }
}

sql_disconnect(); ?>
