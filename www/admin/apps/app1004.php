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
        $tmp_groups = '-1|(keine Änderung);0|(keine Gruppe);';
        if ($dataArr[1] > 0) {
            $ss1 = sql_call("SELECT id,name FROM edomiProject.editVisuElement WHERE (pageid=" . $dataArr[1] . " AND controltyp=0) ORDER BY id ASC");
            while ($n = sql_result($ss1)) {
                $n['name'] = preg_replace("/[\|\;]/", '', $n['name']);
                $tmp_groups .= $n['id'] . '|' . $n['name'] . ' (' . $n['id'] . ');';
            }
            sql_close($ss1);
        } ?>
        var n="
        <div class='appWindowDrag' id='<? echo $winId; ?>-global'>";
            n+="
            <div class='appTitelDrag' onMouseDown='dragWindowStart(\"<? echo $appId; ?>\",\"<? echo $winId; ?>-global\");'>Visuelemente bearbeiten
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
            <div class='cmdButton cmdButtonM'
                 onClick='ajax(\"bulkeditElements\",\"2\",\"<? echo $dataArr[0]; ?>\",\"<? echo $dataArr[1]; ?>\",controlGetFormData(\"<? echo $winId; ?>-form1\")); closeWindow(\"<? echo $winId; ?>\");'>
                <b>Übernehmen</b></div>
            ";
            n+="
            <div class='cmdButton cmdButtonR'
                 onClick='ajax(\"bulkeditElements\",\"2\",\"<? echo $dataArr[0]; ?>\",\"<? echo $dataArr[1]; ?>\",controlGetFormData(\"<? echo $winId; ?>-form1\"));'>
                Anwenden
            </div>
            ";
            n+="
            <div class='cmdButton'
                 onClick='ajax(\"bulkeditElementsSnapToGrid\",\"2\",\"<? echo $dataArr[0]; ?>\",\"<? echo $dataArr[1]; ?>\",controlGetFormData(\"<? echo $winId; ?>-form1\")); closeWindow(\"<? echo $winId; ?>\");'
                 style='float:right;'>nur am Raster ausrichten
            </div>
            ";
            n+="
        </div>";

        n+="
        <div id='<? echo $winId; ?>-form1' class='appContent' style='padding:8px;'>";

            n+="<input type='hidden' id='<? echo $winId; ?>-fd0' data-type='1' value='<? echo $phpdataArr[0]; ?>'></input>";

            n+="
            <table width='100%' border='0' cellpadding='2' cellspacing='0' style='table-layout:auto;'>";
                n+="
                <tr>";
                    n+="
                    <td colspan='5'>Gruppe<br>
                        <div id='<? echo $winId; ?>-fd1' data-type='6' data-value='-1' data-list='' class='control6' style='width:100%;'>&nbsp;</div>
                    </td>
                    ";
                    n+="
                </tr>
                ";
                n+="
                <tr>";
                    n+="
                    <td colspan='4'>Basis-Designvorlage (leer=keine Änderung)<br>
                        <div id='<? echo $winId; ?>-fd12' data-type='1000' data-root='24' data-value='0' data-options='typ=1' class='control10'
                             style='width:100%;'>&nbsp;
                        </div>
                    </td>
                    ";
                    n+="
                    <td align='right'><br>
                        <div id='<? echo $winId; ?>-fd13' data-type='5' data-value='0' class='control5' style='width:20px;'>+</div>
                    </td>
                    ";
                    n+="
                </tr>
                ";
                n+="
                <tr>";
                    n+="
                    <td colspan='4'>Z-Index<br><input type='text' id='<? echo $winId; ?>-fd2' data-type='1' value='' class='control1'
                                                      style='width:100%;'></input></td>
                    ";
                    n+="
                    <td align='right'><br>
                        <div id='<? echo $winId; ?>-fd3' data-type='5' data-value='0' class='control5' style='width:20px;'>&bull;</div>
                    </td>
                    ";
                    n+="
                </tr>
                ";
                n+="
                <tr>";
                    n+="
                    <td colspan='5'>
                        <hr>
                    </td>
                    ";
                    n+="
                </tr>
                ";
                n+="
                <tr>";
                    n+="
                    <td>X-Position (px/%)<br><input type='text' id='<? echo $winId; ?>-fd4' data-type='1' value='' class='control1' style='width:100%;'></input>
                    </td>
                    ";
                    n+="
                    <td align='right'><br>
                        <div id='<? echo $winId; ?>-fd5' data-type='5' data-value='0' class='control5' style='width:20px;'>&bull;</div>
                    </td>
                    ";
                    n+="
                    <td>&nbsp;</td>
                    ";
                    n+="
                    <td>Y-Position (px/%)<br><input type='text' id='<? echo $winId; ?>-fd6' data-type='1' value='' class='control1' style='width:100%;'></input>
                    </td>
                    ";
                    n+="
                    <td align='right'><br>
                        <div id='<? echo $winId; ?>-fd7' data-type='5' data-value='0' class='control5' style='width:20px;'>&bull;</div>
                    </td>
                    ";
                    n+="
                </tr>
                ";
                n+="
                <tr>";
                    n+="
                    <td>Breite (px/%)<br><input type='text' id='<? echo $winId; ?>-fd8' data-type='1' value='' class='control1' style='width:100%;'></input>
                    </td>
                    ";
                    n+="
                    <td align='right'><br>
                        <div id='<? echo $winId; ?>-fd9' data-type='5' data-value='0' class='control5' style='width:20px;'>&bull;</div>
                    </td>
                    ";
                    n+="
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    ";
                    n+="
                    <td>Höhe (px/%)<br><input type='text' id='<? echo $winId; ?>-fd10' data-type='1' value='' class='control1' style='width:100%;'></input></td>
                    ";
                    n+="
                    <td align='right'><br>
                        <div id='<? echo $winId; ?>-fd11' data-type='5' data-value='0' class='control5' style='width:20px;'>&bull;</div>
                    </td>
                    ";
                    n+="
                </tr>
                ";
                n+="
                <tr>";
                    n+="
                    <td colspan='2'>
                        <div id='<? echo $winId; ?>-fd14' data-type='5' data-value='1' class='control5' style='width:100%;'>am Raster ausrichten</div>
                    </td>
                    ";
                    n+="
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    ";
                    n+="
                    <td colspan='2'>
                        <div id='<? echo $winId; ?>-fd15' data-type='5' data-value='1' class='control5' style='width:100%;'>am Raster ausrichten</div>
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

        document.getElementById("<? echo $winId; ?>-fd1").dataset.list='<? ajaxValue($tmp_groups); ?>';

        controlInitAll("<? echo $winId; ?>-form1");
    <? }
}

sql_disconnect(); ?>
