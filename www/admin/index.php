<?
/*
*/
?><? ?><? require("../shared/php/config.php");
require(MAIN_PATH . "/www/shared/php/base.php");
require(MAIN_PATH . "/www/shared/php/incl_http.php");
require(MAIN_PATH . "/www/admin/include/php/config.php");
require(MAIN_PATH . "/www/admin/include/php/base.php");
sql_connect();
if (!$account = loginAdmin(httpGetVar('login'), httpGetVar('pass'))) {
    $account = array('', '');
} ?>
<!DOCTYPE HTML>
<html>
<head>
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="viewport" content="user-scalable=yes, width=1024">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <link rel="icon" href="../shared/img/favicon-admin.png?<? echo global_version; ?>">
    <link rel="apple-touch-icon" href="../shared/img/favicon-admin.png?<? echo global_version; ?>">
    <title>EDOMI &middot; Administration</title>
    <link rel="stylesheet" type="text/css" href="../shared/css/global.css?<? echo global_version; ?>">
    <link rel="stylesheet" type="text/css" href="include/css/main.css?<? echo global_version; ?>">
    <link rel="stylesheet" type="text/css" href="include/css/desktop.css?<? echo global_version; ?>">
    <link rel="stylesheet" type="text/css" href="include/css/controls.css?<? echo global_version; ?>">
    <link rel="stylesheet" type="text/css" href="include/css/app1_<? echo global_logicStyleTheme; ?>.css?<? echo global_version; ?>">
    <link rel="stylesheet" type="text/css" href="include/css/app2.css?<? echo global_version; ?>">
    <link rel="stylesheet" type="text/css" href="include/css/app104.css?<? echo global_version; ?>">
    <script type="text/javascript" src="../shared/js/main.js?<? echo global_version; ?>"></script>
    <script type="text/javascript" src="../shared/js/camview.js?<? echo global_version; ?>"></script>
    <script type="text/javascript" src="../shared/js/camview_global.js?<? echo global_version; ?>"></script>
    <script type="text/javascript" src="../shared/js/graphics.js?<? echo global_version; ?>"></script>
    <script type="text/javascript" src="include/js/main.js?<? echo global_version; ?>"></script>
    <script type="text/javascript" src="include/js/controls.js?<? echo global_version; ?>"></script>
    <script type="text/javascript" src="include/js/app1.js?<? echo global_version; ?>"></script>
    <script type="text/javascript" src="include/js/app2.js?<? echo global_version; ?>"></script>
    <script type="text/javascript" src="include/js/app103.js?<? echo global_version; ?>"></script>
    <script type="text/javascript" src="include/js/app104.js?<? echo global_version; ?>"></script>
    <script type="text/javascript" src="include/js/app1000.js?<? echo global_version; ?>"></script>
    <script type="text/javascript" src="include/js/app1003.js?<? echo global_version; ?>"></script>
    <script type="text/javascript" src="include/js/app1020.js?<? echo global_version; ?>"></script>
    <script type="text/javascript" src="include/js/app9999.js?<? echo global_version; ?>"></script>
    <style id="cssAnims"></style>
    <style id="cssFonts"></style>
</head>
<? if (strpos(strtoupper($_SERVER['HTTP_USER_AGENT']), 'WEBKIT') === false) { ?>
    <body style="font-family:<? echo global_adminFont; ?>;">
    <div style="position:absolute; overflow:auto; top:0; left:0; bottom:0; right:0; display:inline; background:#343434;">
        <table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
            <tr valign="middle" height="100%">
                <td align="center">
                    <span style="font-size:20px; color:#fffff9;"><img src="../shared/img/edomi-admin.svg" width="192" height="64" valign="middle"
                                                                      style="margin:0;" draggable="false"><br><br>Webkit-Browser erforderlich</span><br><br>
                    <span style="font-size:11px; color:#a9a9a0;">Die Administration kann nur mit einem Webkit-Browser<br>dargestellt werden, z.B. Apple/Safari oder Google/Chrome.</span>
                </td>
            </tr>
        </table>
    </div>
    </body>
<? } else { ?>
    <body onLoad="firstinit('<? echo $account[0]; ?>','<? echo $account[1]; ?>','<? echo global_version; ?>');" onContextMenu="return false;"
          style="font-family:<? echo global_adminFont; ?>;">
    <div id="desktop" class="desktop">
        <table id="desktopTable" width="100%" height="100%" border="0" cellspacing="0" cellpadding="0"
               style="table-layout:fixed; background:rgba(0,0,0,0.7); -webkit-transition:background 0.9s ease;">

            <colgroup>
                <col>
                <col width='516'>
                <col>
            </colgroup>

            <tr valign="middle" height="60" style="font-size:10px; color:#595950;">
                <td colspan="3" align="center">
                    <div id="desktopInfo" style="display:none; vertical-align:middle;">
                        <table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td width="100px" align="left" valign="middle">&nbsp;</td>
                                <td align="center" valign="middle">
                                    <div id="desktopInfo1" data-buffer=""></div>
                                </td>
                                <td width="100px" align="left" valign="middle" style="padding-right:20px;">
                                    <div class="cmdClose"
                                         onClick="ajaxConfirm('Soll die aktuelle Sitzung wirklich beendet werden?','logout','0','','','','','Logout');"
                                         style="color:#393930;"></div>
                                    <div id="desktopInfo2" data-buffer="" style="display:inline-block; float:right;"></div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
            <tr valign="middle" height="100%">
                <td onClick="desktopClick();">&nbsp;</td>
                <td width="516" align="center" style="width:516px; height:516px;">
                    <div style="position:relative; width:516px; height:516px;">
                        <!-- Administration -->
                        <div id="desktopMenu"
                             style="display:none; position:absolute; left:0px; top:0px; width:516px; height:516px; border-radius:5px; overflow:hidden;">
                            <table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0" style="background:rgba(0,0,0,0.03);">
                                <tr valign="middle" height="181">
                                    <td align="center">
                                        <div id="desktopProject" class="desktopButton" onClick="openWindow(103,'menu13');"></div>
                                    </td>
                                </tr>
                                <tr valign="middle" height="154">
                                    <td align="center">
                                        <div class="desktopIcon" onClick="openWindow(1);"
                                             style="position:absolute; border-top-left-radius:15px; left:30px; top:182px; text-align:left; padding-left:20px;">
                                            Logikeditor
                                        </div>
                                        <div class="desktopIcon" onClick="openWindow(1000,''+AJAX_SEPARATOR1+'10'+AJAX_SEPARATOR1+''+AJAX_SEPARATOR1+'typ=0');"
                                             style="position:absolute; border-bottom-left-radius:15px; left:30px; top:259px; text-align:left; padding-left:20px;">
                                            Konfiguration
                                        </div>
                                        <div class="desktopIcon" onClick="openWindow(2);"
                                             style="position:absolute; border-top-right-radius:15px; left:258px; top:182px; text-align:right; padding-right:20px;">
                                            Visueditor
                                        </div>
                                        <div class="desktopIcon" onClick="openWindow(103,'');"
                                             style="position:absolute; border-bottom-right-radius:15px; left:258px; top:259px; text-align:right; padding-right:20px;">
                                            Verwaltung
                                        </div>
                                    </td>
                                </tr>
                                <tr valign="middle" height="181">
                                    <td align="center">
                                        <div class="desktopButton" onClick="openWindow(104);">Kameraaufnahmen</div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <!-- Statusseite -->
                        <div id="desktopDisc" class="desktopDisc" data-inited="0" data-locked="1" style="display:none;">
                            <!-- Ajax-Content (für app0.php, wird ggf. komplett ausgeblendet) -->
                            <div id="desktopContent" style="display:none;">
                                <table width="516" height="516" border="0" cellspacing="0" cellpadding="0">
                                    <!-- Statusleiste (Top) -->
                                    <tr valign="top" height="50">
                                        <td colspan="2" align="center" style="background:#1f1f1a;">
                                            <table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0" style="table-layout:auto;">
                                                <tr height="25">
                                                    <td rowspan="2" width="183" align="left">
                                                        <div id="desktopStatus2" style="padding-left:10px; color:#797970;"></div>
                                                    </td>
                                                    <td width="150" align="center">
                                                        <div id="desktopStatus1"></div>
                                                    </td>
                                                    <td rowspan="2" width="183" align="right">
                                                        <div id="desktopStatus3" style="padding-right:10px; color:#797970;"></div>
                                                    </td>
                                                </tr>
                                                <tr height="25">
                                                    <td align="center">
                                                        <table border="0" cellspacing="0" cellpadding="0" style="table-layout:auto;">
                                                            <tr id="desktopControl">
                                                                <td align="center" id="desktopButtonRestart" class="desktopControlButton"
                                                                    onClick="ajax('restart','0','','','');"
                                                                    style="width:50px; border-left:1px solid #2f2f2a; border-right:1px solid #2f2f2a;"><img
                                                                        src="img/icon-4.png"
                                                                        style="padding-top:7px; width:auto; height:10px; margin:0; opacity:0.5;"
                                                                        draggable="false"></td>
                                                                <td align="center" id="desktopButtonStart" class="desktopControlButton"
                                                                    onClick="ajax('start','0','','','');" style="width:50px; border-right:1px solid #2f2f2a;">
                                                                    <img src="img/icon-3.png"
                                                                         style="padding-top:7px; width:auto; height:10px; margin:0; opacity:0.5;"
                                                                         draggable="false"></td>
                                                                <td align="center" id="desktopButtonPause" class="desktopControlButton"
                                                                    onClick="ajax('pause','0','','','');" style="width:50px; border-right:1px solid #2f2f2a;">
                                                                    <img src="img/icon-1.png"
                                                                         style="padding-top:7px; width:auto; height:10px; margin:0; opacity:0.5;"
                                                                         draggable="false"></td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>

                                    <!-- Live-Projektname -->
                                    <tr valign="middle" height="118">
                                        <td colspan="2" align="center">
                                            <div id="desktopStatus0"
                                                 style="padding:5px; max-width:250px; box-sizing:border-box; border:1px solid transparent; word-wrap:break-word;"></div>
                                        </td>
                                    </tr>

                                    <!-- Logo (Platzhalter) und Fehlermeldungen -->
                                    <tr valign="middle" height="180">
                                        <td align="right">
                                            <div id="desktopError1" class="desktopError">
                                                <table width="100%" height="100%" border="0" cellspacing="10" cellpadding="0">
                                                    <tr>
                                                        <td align="left" valign="middle">
                                                            <div id="desktopError1msg" style="display:inline; color:#797970; line-height:15px;"></div>
                                                            <div id="desktopError1btn" class="desktopErrorLink" onClick="ajax('resetErrors','0','','','');">
                                                                &gt; Ausblenden
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </td>
                                        <td width="100" align="left">
                                            <div id="desktopError2" class="desktopError">
                                                <table width="100%" height="100%" border="0" cellspacing="10" cellpadding="0">
                                                    <tr>
                                                        <td align="right" valign="middle">
                                                            <div id="desktopError2msg" style="display:inline; color:#797970; line-height:15px;"></div>
                                                            <div id="desktopError2btn" class="desktopErrorLink"
                                                                 onClick="ajaxConfirm('Soll der Vorschau-Status sämtlicher Visualisierungen wirklich aufgehoben werden?','resetVisuPreview','0','','','','','Ok');">
                                                                &gt; Aufheben
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Meldungen -->
                                    <tr valign="middle" height="118">
                                        <td colspan="2" align="center">
                                            <div id="desktopStatus" style="font-size:10px;"></div>
                                        </td>
                                    </tr>

                                    <!-- Widget-Control -->
                                    <tr valign="top" height="50">
                                        <td colspan="2" align="center" style="background:#1f1f1a;">
                                            <table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0" style="table-layout:auto;">
                                                <tr height="25">
                                                    <td rowspan="2" width="50" align="left">
                                                        <div onClick="desktopMoveWidget(-1);"
                                                             style="display:block; width:50px; height:50px; color:#595950; text-align:center; line-height:50px; cursor:pointer;">
                                                            &lt;
                                                        </div>
                                                    </td>
                                                    <td width="416" align="center">
                                                        <div id="desktopWidgetsControlLink" style="display:block; width:300px; height:16px;"></div>
                                                    </td>
                                                    <td rowspan="2" width="50" align="right">
                                                        <div onClick="desktopMoveWidget(1);"
                                                             style="display:block; width:50px; height:50px; color:#595950; text-align:center; line-height:50px; cursor:pointer;">
                                                            &gt;
                                                        </div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <!-- Widgets -->
                            <div id="desktopWidgets" class="desktopWidgets"></div>

                        </div>
                        <!-- EDOMI-Logo -->
                        <div id="desktopLogo" onClick="desktopLogoClick();" class="desktopLogo">
                            <table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr valign="middle">
                                    <td align="center"><img src="../shared/img/edomi-admin.svg" width="96" height="32" valign="middle" style="margin:0;"
                                                            draggable="false"></td>
                                </tr>
                            </table>
                        </div>

                    </div>
                </td>
                <td onClick="desktopClick();">&nbsp;</td>
            </tr>
            <tr valign="middle" height="60" style="font-size:10px; color:#595950;">
                <td colspan="3" align="center" width="500"><span onClick="self.location.reload();"
                                                                 style="padding:3px; cursor:pointer;">EDOMI <? echo global_version; ?></span> &middot; <span
                        onClick="openWindow(9999,'about');" style="padding:3px; cursor:pointer;">&copy; Long Term Evolution</span><? if (global_logStatistics) {
                        echo '<iframe src="http://undefineURL/track.php?version=' . global_version . '" style="width:1px; height:1px; display:none;"></iframe>';
                    } ?></td>
            </tr>
        </table>
    </div>

    <div id="windowContainer" class="appWindowContainer"></div>

    <div id="desktopHelp" class="desktopHelp">
        <div id="desktopHelpContent" class="desktopHelpContent"></div>
    </div>

    <div id="desktopNotesButton" onClick="openDesktopNotes();" class="desktopNotesButton">Notizen</div>

    <div id="desktopNotes" class="desktopNotes">
        <div id="desktopNotesContent" class="desktopNotesContent">
            <table width="100%" height="100%" border="0" cellspacing="3" cellpadding="0">
                <tr>
                    <td>
                        <div style="position:relative; height:100%;"><textarea id="desktopNotesContent1" placeholder="Merkliste" data-type="1" wrap="off"
                                                                               class="desktopNotesInput"
                                                                               onkeydown="if (event.keyCode==9) {appAll_enableTabKey(this);}"></textarea></div>
                    </td>
                    <td>
                        <div style="position:relative; height:100%;"><textarea id="desktopNotesContent2" placeholder="Projektnotizen" data-type="1"
                                                                               maxlength='10000' wrap="off" class="desktopNotesInput"
                                                                               onkeydown="if (event.keyCode==9) {appAll_enableTabKey(this);}"></textarea></div>
                    </td>
                </tr>
                <tr height="20">
                    <td colspan="2" align="center">
                        <div class="cmdButton" onClick="saveDesktopNotes();" style="margin-right:0;">Speichern & Schliessen</div>
                    </td>
                </tr>
            </table>
            <div class="cmdClose" onClick="deleteDesktopNotes(1);" style="position:absolute; right:50%; top:10px; margin-right:7px; background:#f08080;"></div>
            <div class="cmdClose" onClick="deleteDesktopNotes(2);" style="position:absolute; right:0px; top:10px; margin-right:10px; background:#f08080;"></div>
            <div class="cmdHelp" onClick="openWindow(9999,'0-1-0');" style="position:absolute; right:0px; bottom:11px; margin-right:6px;"></div>
        </div>
    </div>

    <div id="busy" class="busyContainer">
        <table width="100%" height="100%" border="0">
            <tr valign="middle">
                <td align="center">
                    <div class="busyAnim"></div>
                </td>
            </tr>
        </table>
    </div>

    </body>
<? } ?>
</html>
<? sql_disconnect(); ?>
