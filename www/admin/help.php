<?
/*
*/
?><? ?><? require("../shared/php/config.php");
require(MAIN_PATH . "/www/shared/php/base.php");
require(MAIN_PATH . "/www/shared/php/incl_http.php");
require(MAIN_PATH . "/www/admin/include/php/config.php");
require(MAIN_PATH . "/www/admin/include/php/base.php");
sql_connect();
$helpFn = httpGetVar('file');
if (isEmpty($helpFn)) {
    $helpFn = 'start';
}
$search = httpGetVar('search');
if (!isEmpty($search)) {
    helpSearchRequest($search, 'result');
    $helpFn = 'result';
} ?>
    <!DOCTYPE HTML>
    <html>
    <head>
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="viewport" content="user-scalable=yes, width=1024">
        <meta name="apple-mobile-web-app-status-bar-style" content="black">
        <link rel="icon" href="../shared/img/favicon-admin.png">
        <link rel="apple-touch-icon" href="../shared/img/favicon-admin.png">
        <title>EDOMI &middot; Hilfe</title>
        <link rel="stylesheet" type="text/css" href="../shared/css/global.css">
        <link rel="stylesheet" type="text/css" href="include/css/main.css">
        <link rel="stylesheet" type="text/css" href="include/css/controls.css">
        <link rel="stylesheet" type="text/css" href="include/css/app1_<? echo global_logicStyleTheme; ?>.css">
        <script type="text/javascript">
            function showHelp() {
                <? if ($menu = ajaxValueHTML(helpToHtml(0, 'navigation', true))) { ?>
                document.getElementById("menuRoot").innerHTML = '<?echo $menu;?>';
                <? } ?>
                document.getElementById("search").value = "<?ajaxValue($search);?>";
                <? if ($help = ajaxValueHTML(helpToHtml(0, $helpFn, true))) { ?>
                document.getElementById("filename").innerHTML = '<?echo $helpFn;?>.htm';
                document.getElementById("help").innerHTML = '<?echo $help;?>';
                <? } else { ?>
                document.getElementById("filename").innerHTML = '<span style="color:#e00000;"><?echo $helpFn;?>.htm</span>';
                document.getElementById("help").innerHTML = 'Es ist keine Hilfe für dieses Thema verfügbar.';
                <? } ?>
            }

            function search() {
                n = document.getElementById("search").value;
                if (n != "") {
                    window.location = "help.php?search=" + encodeURIComponent(n);
                }
            }
        </script>
    </head>

    <body onLoad='showHelp();'
          style="font-family:<? echo global_adminFont; ?>; background:-webkit-linear-gradient(top, #ffffff 0px,#f0f0e9 80px,#ffffff 80px); text-align:left; background-repeat:no-repeat; -webkit-user-select:auto; overflow:auto;">
    <div class='appTitel' style='margin:0; padding:5px 0 5px 0;'>EDOMI <? echo global_version; ?> &middot; &copy; Long Term Evolution
        <div id='filename' style='float:right;'></div>
    </div>
    <div class='appMenu' style='margin:5px 0 0 0; padding:0;'>
        <table id='<? echo $winId; ?>-form1' width='100%' border='0' cellpadding='0' cellspacing='0' style='margin-top:5px; table-layout:auto;'>
            <tr>
                <td width='30px'>
                    <div class='cmdButton' onClick='history.back();'>&lt;</div>
                </td>
                <td style='padding-left:2px;'><input type='text' id='search' data-type='1' autofocus value='' onkeydown='if (event.keyCode==13) {search();}'
                                                     class='control1' style='width:100%; height:27px; margin:0; outline:none;'></input></td>
            </tr>
        </table>
    </div>
    <table width='100%' border='0' cellpadding='0' cellspacing='0' style='padding-top:15px; table-layout:auto;'>
        <tr valign='top'>
            <td width='1' style='zoom:115%;'>
                <div id='menuRoot' class='columnMenu' style='height:100%;'></div>
            </td>
            <td>
                <div id='help' class='columnContent'
                     style='zoom:115%; line-height:1.5; font-family:<? echo global_adminFont; ?>; font-size:10px; padding-left:10px; box-sizing:border-box;'></div>
            </td>
        </tr>
    </table>
    </body>
    </html>
<? sql_disconnect(); ?>
