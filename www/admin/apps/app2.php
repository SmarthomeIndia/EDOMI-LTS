<?
/*
*/
?><? ?><? require("../../shared/php/config.php");
require(MAIN_PATH . "/www/shared/php/base.php");
require(MAIN_PATH . "/www/shared/php/incl_http.php");
require(MAIN_PATH . "/www/admin/include/php/config.php");
require(MAIN_PATH . "/www/admin/include/php/base.php");
require(MAIN_PATH . "/www/admin/include/php/incl_items.php");
if (file_exists(MAIN_PATH . '/www/admin/vse/vse_include_admin.php')) {
    require(MAIN_PATH . '/www/admin/vse/vse_include_admin.php');
} else { ?>
    closeWindow("<? echo $winId; ?>");
    jsConfirm("<b>Es sind keine Visuelemente verfügbar!</b><br><br>Die Nutzung des Visueditors ist daher nicht möglich.","","none");
    <? exit();
}
sql_connect();
if (checkAdmin($sid)) {
    cmd($cmd);
}
function cmd($cmd)
{
    global $appId, $winId, $data, $dataArr, $phpdata, $phpdataArr, $sid;
    if ($cmd == 'initApp') {
        if (getEditProjektId() === false) { ?>
            closeWindow("<? echo $winId; ?>");
            jsConfirm("Es ist kein Arbeitsprojekt vorhanden.","","none");
            <? return;
        } ?>
        var n="
        <div id='<? echo $winId; ?>-global' class='appWindowFullscreen' onMouseUp='app2_itemPageUnclick();' data-copybuffer=''>";
            n+="
            <div class='appTitel'>Visueditor
                <div class='cmdClose' onClick='app2_quit(\"<? echo $winId; ?>\");'></div>
                <div class='cmdHelp' onClick='openWindow(9999,\"<? echo $appId; ?>\");'></div>
            </div>
            ";
            n+="
            <div id='<? echo $winId; ?>-main'></div>
            ";
            n+="
            <div id='<? echo $winId; ?>-menu' class='controlEditInline'
                 style='position:absolute; background:#ffffff; margin:0 5px 5px 5px; padding:0px; left:0px; width:230px; top:80px; bottom:0px; border-radius:0; border:none;'></div>
            ";
            n+="
            <div id='<? echo $winId; ?>-pagecontainer' class='app2_pageContainer' style='left:242px;'>";
                n+="
                <div id='<? echo $winId; ?>-page' class='app2_page' onMouseDown='app2_itemPageClick();' onMouseMove='app2_itemPageMouseMove();'></div>
                ";
                n+="
            </div>
            ";
            n+="
        </div>";
        document.getElementById("<? echo $winId; ?>").innerHTML=n;
        <? ?>
        var n="
        <div class='appMenu'>";
            n+="
            <table width='100%' border='0' cellpadding='0' cellspacing='0' style='white-space: nowrap; table-layout:auto;'>";
                n+="
                <tr>";
                    n+="
                    <td>";
                        n+="
                        <div id='<? echo $winId; ?>-fd1' data-type='1000' data-root='22' data-value='0' data-options='typ=1;reset=0;caption=Visuseite öffnen'
                             data-callback='app2_pickPage_callback(\"<? echo $winId; ?>-fd1\");'
                             data-callback2='app2_pickPage_callback(\"<? echo $winId; ?>-fd1\");' class='cmdButton'
                             style='min-width:100px; max-width:500px; white-space:nowrap; overflow:hidden; vertical-align:top;'>&nbsp;
                        </div>
                        ";
                        n+="&nbsp;&nbsp;&nbsp;";
                        n+="
                        <div id='<? echo $winId; ?>-pagebound' class='cmdButton' value='1' onClick='app2_pageboundToggle();'
                             style='min-width:30px; background:#80e000;'>
                            <div style='display:inline-block; width:6px; height:6px; border:1px dotted #000000;'></div>
                        </div>
                        ";
                        n+="&nbsp;&nbsp;&nbsp;";
                        n+="
                        <div id='<? echo $winId; ?>-unlockx' class='cmdButton cmdButtonL' value='1' onClick='app2_unlockXtoggle();'
                             style='min-width:20px; background:#80e000;'>X
                        </div>
                        ";
                        n+="
                        <div id='<? echo $winId; ?>-rasterx' class='cmdButton cmdButtonM' value='1' onClick='app2_gridXtoggle();'
                             style='min-width:20px; background:#80e000;'>1
                        </div>
                        ";
                        n+="
                        <div id='<? echo $winId; ?>-position' class='cmdButton cmdButtonM' onClick='app2_moveElementsByKeyboard();' style='min-width:80px;'>
                            (X/Y)
                        </div>
                        ";
                        n+="
                        <div id='<? echo $winId; ?>-unlocky' class='cmdButton cmdButtonM' value='1' onClick='app2_unlockYtoggle();'
                             style='min-width:20px; background:#80e000;'>Y
                        </div>
                        ";
                        n+="
                        <div id='<? echo $winId; ?>-rastery' class='cmdButton cmdButtonR' value='1' onClick='app2_gridYtoggle();'
                             style='min-width:20px; background:#80e000;'>1
                        </div>
                        ";
                        n+="
                        <div id='<? echo $winId; ?>-fd3' data-type='1000' data-root='160' data-value='0' data-options='typ=4;reset=0'
                             data-callback='app2_pickElement_callback(\"<? echo $winId; ?>-fd3\");' data-callback2='app2_refreshAll(0);'
                             style='display:none;'></div>
                        ";
                        n+="&nbsp;&nbsp;&nbsp;";
                        n+="
                        <div onClick='openWindow(103,\"menu15"+AJAX_SEPARATOR1+"noMenu"+AJAX_SEPARATOR1+"app2_refreshIncludeJS();\");'
                             class='cmdButton cmdButtonL'>Visuaktivierung
                        </div>
                        ";
                        n+="
                        <div id='<? echo $winId; ?>-preview' class='cmdButton cmdButtonM' onClick='app2_previewModeToggle();'>Vorschau</div>
                        ";
                        n+="<input type='text' id='<? echo $winId; ?>-previewValue' class='control1 cmdButtonM' onChange='app2_previewChangeValue(this);'
                                   placeholder='KO1' value='' style='width:150px; height:27px; border-color:#c0c0c0;'></input>";
                        n+="<input type='text' id='<? echo $winId; ?>-previewValue3' class='control1 cmdButtonR' onChange='app2_previewChangeValue3(this);'
                                   placeholder='KO3' value='' style='width:100px; height:27px; border-color:#c0c0c0; border-left:none;'></input>";
                        n+="
                    </td>
                    ";
                    n+="
                    <td align='right'>";
                        n+="
                        <div id='<? echo $winId; ?>-zoom' style='display:inline;'>100%</div>&nbsp;";
                        n+="<input type='range' class='controlSlider' value='1' min='0.5' max='2' step='0.1' onInput='app2_zoom(this.value);'
                                   onDblClick='app2_zoom(1); this.value=1;' style='width:120px; vertical-align:middle;'></input>";
                        n+="&nbsp;&nbsp;&nbsp;";
                        n+="
                        <div id='<? echo $winId; ?>-layer' class='cmdButton' onClick='app2_setLayerMode();'><img src='../shared/img/lock1.png' width='16'
                                                                                                                 height='16' valign='middle'
                                                                                                                 style='margin:0; padding-left:2px;'
                                                                                                                 draggable='false'></div>
                        ";
                        n+="&nbsp;&nbsp;&nbsp;";
                        n+="
                        <div id='<? echo $winId; ?>-fd2' data-type='1000' data-root='20' data-value='0' data-options='typ=0;caption=Konfiguration'
                             data-callback2='app2_refreshIncludeJS();' class='cmdButton'>&nbsp;
                        </div>
                        ";
                        n+="
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
        controlInitAll("<? echo $winId; ?>-main");
        controlClickLeft("<? echo $winId; ?>-fd1"); //Visuseite auswählen

        //Visuelemente inkludieren und app2_init() aufrufen
        includeVisuelements('vse/vse_include_admin.js',function(){app2_init("<? echo $winId; ?>");},true);
    <? }
    if ($cmd == 'start') {
        if ($dataArr[0] > 0) {
            $dataArr[1] = (($dataArr[1] == 1) ? true : false); ?>
            clearObject(app2_winId+"-page",0);
            clearObject(app2_winId+"-menu",0);
            clearObject("cssAnims",0);
            clearObject("cssFonts",0);
            app2_links=new Array();
            <? $page = sql_getValues('edomiProject.editVisuPage', '*', 'id=' . $dataArr[0]);
            if ($page !== false) {
                $visu = sql_getValues('edomiProject.editVisu', '*', 'id=' . $page['visuid']);
            }
            if ($visu['id'] > 0 && $page['id'] > 0) {
                $cssIndiColor1 = sql_getValue('edomiProject.editVisuFGcol', 'color', "id='" . $visu['indicolor'] . "'");
                if (isEmpty($cssIndiColor1)) {
                    $cssIndiColor1 = '#80e000';
                }
                $cssIndiColor2 = sql_getValue('edomiProject.editVisuFGcol', 'color', "id='" . $visu['indicolor2'] . "'");
                if (isEmpty($cssIndiColor2)) {
                    $cssIndiColor2 = 'inherit';
                } ?>
                visu_indiColor='<? ajaxValue($cssIndiColor1); ?>';
                visu_indiColorText='<? ajaxValue($cssIndiColor2); ?>';

                document.getElementById("<? echo $winId; ?>-rasterx").value="<? echo $page['xgrid']; ?>";
                document.getElementById("<? echo $winId; ?>-rasterx").innerHTML="<? echo $page['xgrid']; ?>";
                document.getElementById("<? echo $winId; ?>-rastery").value="<? echo $page['ygrid']; ?>";
                document.getElementById("<? echo $winId; ?>-rastery").innerHTML="<? echo $page['ygrid']; ?>";
                <? if ($page['outlinecolorid'] >= 1) {
                    $tmp = sql_getValue('edomiProject.editVisuFGcol', 'color', "id='" . $page['outlinecolorid'] . "'"); ?>
                    app2_elementOutlineColor="<? ajaxEcho($tmp); ?>";
                <? } else { ?>
                    app2_elementOutlineColor=apps_colorSelected;
                <? } ?>
                var pdiv=document.getElementById("<? echo $winId; ?>-page");
                var vp=app2_newDiv(pdiv,"<? echo $winId; ?>-visupage");
                vp.style.overflow="none";
                vp.style.border="5px solid #808080";
                <? if ($dataArr[1]) { ?>
                    vp.style.boxShadow="0 0 0 1px transparent inset";
                <? } else { ?>
                    vp.style.boxShadow="0 0 0 1px "+app2_elementOutlineColor+" inset";
                <? }
                if ($page['pagetyp'] == 1) { ?>
                    vp.style.width="<? echo $page['xsize']; ?>px";
                    vp.style.height="<? echo $page['ysize']; ?>px";
                <? } else { ?>
                    vp.style.width="<? echo $visu['xsize']; ?>px";
                    vp.style.height="<? echo $visu['ysize']; ?>px";
                <? }
                $pageIncludeIDs = array($page['id']);
                if ($page['pagetyp'] == 0) {
                    $tmp = $page['includeid'];
                    while (!isEmpty($tmp) && is_numeric($tmp) && $tmp > 0 && !in_array($tmp, $pageIncludeIDs)) {
                        array_unshift($pageIncludeIDs, $tmp);
                        $ss1 = sql_call("SELECT includeid FROM edomiProject.editVisuPage WHERE id=" . $tmp);
                        $tmp = 0;
                        if ($n = sql_result($ss1)) {
                            if (!isEmpty($n['includeid']) && is_numeric($n['includeid']) && $n['includeid'] > 0) {
                                $tmp = $n['includeid'];
                            } else {
                                $tmp = 0;
                            }
                        }
                        sql_close($ss1);
                    }
                    if ($page['globalinclude'] == 1) {
                        $ss1 = sql_call("SELECT id FROM edomiProject.editVisuPage WHERE (visuid=" . $visu['id'] . " AND pagetyp=2 AND id<>" . $page['id'] . ") ORDER BY id DESC");
                        while ($n = sql_result($ss1)) {
                            array_unshift($pageIncludeIDs, $n['id']);
                        }
                        sql_close($ss1);
                    }
                }
                $currentPageIDs = implode(',', $pageIncludeIDs);
                $tmp1 = '';
                $tmp2 = '';
                $ss1 = sql_call("SELECT bgimg,bgcolorid FROM edomiProject.editVisuPage WHERE (id IN (" . $currentPageIDs . ")) ORDER BY FIELD(id," . $currentPageIDs . ") DESC");
                while ($n = sql_result($ss1)) {
                    if (isEmpty($tmp1) && $n['bgimg'] > 0 && ($tmp = sql_getValues('edomiProject.editVisuImg', '*', 'id=' . $n['bgimg']))) {
                        $tmp1 = "url('../data/project/visu/img/img-" . $tmp['id'] . "." . $tmp['suffix'] . "?" . $tmp['ts'] . "')";
                    }
                    if (isEmpty($tmp2) && $n['bgcolorid'] > 0 && ($tmp = sql_getValues('edomiProject.editVisuBGcol', '*', 'id=' . $n['bgcolorid']))) {
                        $tmp2 = $tmp['color'];
                    }
                    if (!isEmpty($tmp1) && !isEmpty($tmp2)) {
                        break;
                    }
                }
                sql_close($ss1);
                $tmp = trim(($tmp1 . ',' . $tmp2), ',');
                if (!isEmpty($tmp)) { ?>
                    vp.style.background="<? ajaxEcho($tmp); ?>";
                    vp.style.backgroundSize="100% 100%";
                    vp.style.backgroundRepeat="no-repeat";
                <? }
                $head = array(array());
                $ss1 = sql_call("SELECT * FROM edomiProject.editVisuElement WHERE (pageid IN (" . $currentPageIDs . ")) ORDER BY FIELD(pageid," . $currentPageIDs . ") ASC,(controltyp=0) DESC,id ASC");
                while ($item = sql_result($ss1)) {
                    if ($item['controltyp'] == 0) {
                        if ($item['pageid'] == $page['id']) { ?>
                            var ve=new class_app2_VE();
                            ve.element={id:"<? echo $item['id']; ?>",pageid:"<? echo $item['pageid']; ?>",controltyp:"<? echo $item['controltyp']; ?>",groupid:"<? echo $item['groupid']; ?>",layer:"<? echo $item['layer']; ?>",name:"<? echo escapeString($item['name'], 1); ?>"};
                            ve.addGroup();
                        <? }
                    } else {
                        $vseDef = sql_getValues('edomiProject.editVisuElementDef', '*', 'id=' . $item['controltyp']);
                        $vseEDITOR_PHP = 'PHP_VSE_' . $item['controltyp'] . '_EDITOR_PHP';
                        if ($vseDef !== false && $vseDef['errcount'] == 0 && function_exists($vseEDITOR_PHP)) {
                            $property = $vseEDITOR_PHP($item);
                        }
                        $style = app2_getElementStyle($item, $vseDef, $head, $dataArr[1], $dataArr[2], $dataArr[3]);
                        if ($dataArr[1]) { ?>
                            var itemCss=pS("<? ajaxEcho($style[0]); ?>",app2_previewValue);
                            var itemText=pS("<? echo escapeString($style[1], 4); ?>",app2_previewValue);
                        <? } else { ?>
                            var itemCss="<? ajaxEcho($style[0]); ?>";
                            var itemText="<? echo escapeString($style[1], 4); ?>";
                        <? } ?>
                        var ve=new class_app2_VE();
                        ve.element={id:"<? echo $item['id']; ?>",defname:"<? ajaxEcho((($vseDef !== false) ? $vseDef['name'] : '(unbekannter Typ)')); ?>",error:"<? echo((($vseDef !== false && $vseDef['errcount'] == 0)) ? '0' : '1'); ?>",pageid:"<? echo $item['pageid']; ?>",controltyp:"<? echo $item['controltyp']; ?>",groupid:"<? echo $item['groupid']; ?>",layer:"<? echo $item['layer']; ?>",name:"<? echo escapeString($item['name'], 1); ?>",menutext:"<? echo escapeString($item['text'], 2); ?>"};
                        ve.addElement(itemCss,itemText,[<? for ($t = 0; $t < count($property); $t++) {
                            echo '"' . escapeString($property[$t], 1) . '"';
                            echo(($t < count($property) - 1) ? ',' : '');
                        } ?>],{<? for ($t = 1; $t <= 20; $t++) {
                            echo 'var' . $t . ':"' . escapeString($item['var' . $t], 0) . '"';
                            echo(($t < 20) ? ',' : '');
                        } ?>});
                        <? if ($item['linkid'] > 0) { ?>
                            app2_addLink("<? echo $item['id']; ?>","<? echo $item['linkid']; ?>");
                        <? }
                    }
                }
                sql_close($ss1); ?>
                app2_drawLinks();
                app2_groupBoundingBoxes();
                app2_restoreState("<? echo $phpdataArr[0]; ?>");
            <? }
        }
    }
    if ($cmd == 'newVisuElement') {
        $vseDef = sql_getValues('edomiProject.editVisuElementDef', '*', 'id=' . $phpdataArr[0] . ' AND errcount=0');
        if ($vseDef !== false) {
            $tmp = sql_getValues('edomiProject.editVisuPage', 'visuid', 'id=' . $dataArr[0]);
            if ($tmp !== false) {
                $dbId = db_itemSave('editVisuElement', array(1 => -1, 97 => $tmp['visuid'], 98 => $dataArr[0], 6 => $phpdataArr[0], 2 => $phpdataArr[1], 3 => $phpdataArr[2], 8 => global_adminVEzindex, 96 => 0, 22 => 0, 23 => 0, 15 => $vseDef['xsize'], 16 => $vseDef['ysize'], 18 => $vseDef['text'], 31 => $vseDef['var1'], 32 => $vseDef['var2'], 33 => $vseDef['var3'], 34 => $vseDef['var4'], 35 => $vseDef['var5'], 36 => $vseDef['var6'], 37 => $vseDef['var7'], 38 => $vseDef['var8'], 39 => $vseDef['var9'], 40 => $vseDef['var10'], 41 => $vseDef['var11'], 42 => $vseDef['var12'], 43 => $vseDef['var13'], 44 => $vseDef['var14'], 45 => $vseDef['var15'], 46 => $vseDef['var16'], 47 => $vseDef['var17'], 48 => $vseDef['var18'], 49 => $vseDef['var19'], 50 => $vseDef['var20'])); ?>
                app2_refreshAll(0);
            <? }
        }
    }
    if ($cmd == 'saveElementsPosition') {
        $tmp = explode(';', $phpdataArr[0]);
        foreach ($tmp as $n) {
            $element = explode(',', $n);
            if ($element[0] > 0) {
                sql_call("UPDATE edomiProject.editVisuElement SET xpos='" . $element[1] . "',ypos='" . $element[2] . "' WHERE (id=" . $element[0] . " AND controltyp<>0)");
            }
        }
    }
    if ($cmd == 'saveElementsPositionAndSize') {
        $tmp = explode(';', $phpdataArr[0]);
        foreach ($tmp as $n) {
            $element = explode(',', $n);
            if ($element[0] > 0) {
                sql_call("UPDATE edomiProject.editVisuElement SET xpos='" . $element[1] . "',ypos='" . $element[2] . "',xsize='" . $element[3] . "',ysize='" . $element[4] . "' WHERE (id=" . $element[0] . " AND controltyp<>0)");
            }
        } ?>
        app2_refreshAll(0);
    <? }
    if ($cmd == 'linkElement') {
        $tmp = explode(';', $phpdataArr[0]);
        if (count($tmp) >= 2 && $tmp[0] > 0 && $tmp[1] > 0 && $tmp[0] != $tmp[1]) {
            if (sql_getValue('edomiProject.editVisuElement', 'controltyp', 'id=' . $tmp[0]) > 0 && sql_getValue('edomiProject.editVisuElement', 'controltyp', 'id=' . $tmp[1]) > 0) {
                sql_call("UPDATE edomiProject.editVisuElement SET linkid=0 WHERE id=" . $tmp[0] . " OR id=" . $tmp[1] . " OR linkid=" . $tmp[0] . " OR linkid=" . $tmp[1]);
                sql_call("UPDATE edomiProject.editVisuElement SET linkid=" . $tmp[1] . " WHERE id=" . $tmp[0] . " AND controltyp<>0");
            }
        } ?>
        app2_refreshAll(0,"0");    //Selektion aufheben (ist bequemer für den Nutzer)
    <? }
    if ($cmd == 'unlinkElements') {
        sql_call("UPDATE edomiProject.editVisuElement SET linkid=0 WHERE id=" . $phpdataArr[0] . " OR linkid=" . $phpdataArr[0]); ?>
        app2_refreshAll(0);
    <? }
    if ($cmd == 'layerElements') {
        $tmp = explode(';', $phpdataArr[0]);
        foreach ($tmp as $element) {
            if ($element > 0) {
                sql_call("UPDATE edomiProject.editVisuElement SET layer='" . $phpdataArr[1] . "' WHERE id=" . $element);
            }
        } ?>
        app2_refreshAll(0);
    <? }
    if ($cmd == 'bulkeditElements' || $cmd == 'bulkeditElementsSnapToGrid') {
        if ($cmd == 'bulkeditElementsSnapToGrid') {
            $phpdataArr[1] = -1;
            $phpdataArr[2] = '';
            $phpdataArr[3] = 0;
            $phpdataArr[4] = 0;
            $phpdataArr[5] = 0;
            $phpdataArr[6] = 0;
            $phpdataArr[7] = 0;
            $phpdataArr[8] = 0;
            $phpdataArr[9] = 0;
            $phpdataArr[10] = 0;
            $phpdataArr[11] = 0;
            $phpdataArr[12] = 0;
            $phpdataArr[13] = 0;
        }
        $gridX = 1;
        $gridY = 1;
        if ($phpdataArr[14] == 1 || $phpdataArr[15] == 1) {
            $grid = sql_getValues('edomiProject.editVisuPage', 'xgrid,ygrid', 'id=' . $dataArr[0]);
            if ($grid !== false) {
                if ($phpdataArr[14] == 1) {
                    $gridX = $grid['xgrid'];
                }
                if ($phpdataArr[15] == 1) {
                    $gridY = $grid['ygrid'];
                }
            }
        }
        $n = '';
        if ($phpdataArr[1] >= 0) {
            $n .= 'groupid=' . intVal($phpdataArr[1]) . ',';
        }
        if (!isEmpty($phpdataArr[2])) {
            if ($phpdataArr[3] == 0) {
                $n .= 'zindex=(CASE WHEN ((CAST(zindex AS SIGNED INTEGER)+' . intVal($phpdataArr[2]) . ')>=0) THEN zindex+' . intVal($phpdataArr[2]) . ' ELSE 0 END),';
            } else if (intVal($phpdataArr[2]) >= 0) {
                $n .= 'zindex=' . intVal($phpdataArr[2]) . ',';
            }
        }
        if (!isEmpty($phpdataArr[4])) {
            if ($phpdataArr[5] == 0) {
                if (strpos($phpdataArr[4], '%') === false) {
                    $n .= 'xpos=(ROUND((xpos+' . intVal($phpdataArr[4]) . ')/' . $gridX . ')*' . $gridX . '),';
                } else {
                    $n .= 'xpos=(ROUND((xpos+(xpos/100*' . floatVal($phpdataArr[4]) . '))/' . $gridX . ')*' . $gridX . '),';
                }
            } else {
                $n .= 'xpos=(ROUND(' . intVal($phpdataArr[4]) . '/' . $gridX . ')*' . $gridX . '),';
            }
        }
        if (!isEmpty($phpdataArr[6])) {
            if ($phpdataArr[7] == 0) {
                if (strpos($phpdataArr[6], '%') === false) {
                    $n .= 'ypos=(ROUND((ypos+' . intVal($phpdataArr[6]) . ')/' . $gridY . ')*' . $gridY . '),';
                } else {
                    $n .= 'ypos=(ROUND((ypos+(ypos/100*' . floatVal($phpdataArr[6]) . '))/' . $gridY . ')*' . $gridY . '),';
                }
            } else {
                $n .= 'ypos=(ROUND(' . intVal($phpdataArr[6]) . '/' . $gridY . ')*' . $gridY . '),';
            }
        }
        if (!isEmpty($phpdataArr[8])) {
            if ($phpdataArr[9] == 0) {
                if (strpos($phpdataArr[8], '%') === false) {
                    $n .= 'xsize=(CASE WHEN ((ROUND((CAST(xsize AS SIGNED INTEGER)+' . intVal($phpdataArr[8]) . ')/' . $gridX . ')*' . $gridX . ')>0) THEN (ROUND((CAST(xsize AS SIGNED INTEGER)+' . intVal($phpdataArr[8]) . ')/' . $gridX . ')*' . $gridX . ') ELSE ' . $gridX . ' END),';
                } else {
                    $n .= 'xsize=(CASE WHEN ((ROUND((CAST(xsize AS SIGNED INTEGER)+(CAST(xsize AS SIGNED INTEGER)/100*' . floatVal($phpdataArr[8]) . '))/' . $gridX . ')*' . $gridX . ')>0) THEN (ROUND((CAST(xsize AS SIGNED INTEGER)+(CAST(xsize AS SIGNED INTEGER)/100*' . floatVal($phpdataArr[8]) . '))/' . $gridX . ')*' . $gridX . ') ELSE ' . $gridX . ' END),';
                }
            } else if (intVal($phpdataArr[8]) > 0) {
                $n .= 'xsize=(ROUND(' . intVal($phpdataArr[8]) . '/' . $gridX . ')*' . $gridX . '),';
            }
        }
        if (!isEmpty($phpdataArr[10])) {
            if ($phpdataArr[11] == 0) {
                if (strpos($phpdataArr[10], '%') === false) {
                    $n .= 'ysize=(CASE WHEN ((ROUND((CAST(ysize AS SIGNED INTEGER)+' . intVal($phpdataArr[10]) . ')/' . $gridY . ')*' . $gridY . ')>0) THEN (ROUND((CAST(ysize AS SIGNED INTEGER)+' . intVal($phpdataArr[10]) . ')/' . $gridY . ')*' . $gridY . ') ELSE ' . $gridY . ' END),';
                } else {
                    $n .= 'ysize=(CASE WHEN ((ROUND((CAST(ysize AS SIGNED INTEGER)+(CAST(ysize AS SIGNED INTEGER)/100*' . floatVal($phpdataArr[10]) . '))/' . $gridY . ')*' . $gridY . ')>0) THEN (ROUND((CAST(ysize AS SIGNED INTEGER)+(CAST(ysize AS SIGNED INTEGER)/100*' . floatVal($phpdataArr[10]) . '))/' . $gridY . ')*' . $gridY . ') ELSE ' . $gridY . ' END),';
                }
            } else if (intVal($phpdataArr[10]) > 0) {
                $n .= 'ysize=(ROUND(' . intVal($phpdataArr[10]) . '/' . $gridY . ')*' . $gridY . '),';
            }
        }
        $n = rtrim($n, ',');
        if (!isEmpty($n)) {
            $tmp = explode(';', $phpdataArr[0]);
            foreach ($tmp as $element) {
                if ($element > 0) {
                    sql_call("UPDATE edomiProject.editVisuElement SET " . $n . " WHERE (id=" . $element . " AND controltyp<>0)");
                }
            }
        }
        if ($phpdataArr[12] > 0) {
            $tmp = explode(';', $phpdataArr[0]);
            foreach ($tmp as $element) {
                if ($element > 0) {
                    if (!isEmpty(sql_getValue('edomiProject.editVisuElement', 'id', 'id=' . $element . ' AND controltyp<>0'))) {
                        sql_call("UPDATE edomiProject.editVisuElementDesign SET defid=" . $phpdataArr[12] . " WHERE (targetid=" . $element . " AND styletyp=0)");
                        if ($phpdataArr[13] == 1) {
                            if (isEmpty(sql_getValue('edomiProject.editVisuElementDesign', 'id', 'targetid=' . $element . ' AND styletyp=0'))) {
                                $n = array(1 => -1, 2 => $element, 3 => $phpdataArr[12], 4 => 0);
                                db_itemSave('editVisuElementDesign', $n, false);
                            }
                        }
                    }
                }
            }
        } ?>
        app2_refreshAll(0);
    <? }
    if ($cmd == 'deleteElements') {
        $tmp = explode(';', $phpdataArr[0]);
        foreach ($tmp as $element) {
            if ($element > 0) {
                db_itemDelete('editVisuElement', $element);
            }
        } ?>
        app2_refreshAll(0);
    <? }
    if ($cmd == 'pasteElements') {
        $visuId = sql_getValue('edomiProject.editVisuPage', 'visuid', 'id=' . $dataArr[0]);
        if ($visuId > 0) {
            $tmp = explode(';', $phpdataArr[0]);
            $newElements = db_itemDuplicate_editVisuElement($visuId, $dataArr[0], $tmp, '-KOPIE'); ?>
            app2_refreshAll(0,"<? echo implode(';', $newElements); ?>");
        <? }
    }
    if ($cmd == 'moveElements') {
        $visuId = sql_getValue('edomiProject.editVisuPage', 'visuid', 'id=' . $dataArr[0]);
        if ($visuId > 0) {
            $newElements = array();
            $tmp = explode(';', $phpdataArr[0]);
            foreach ($tmp as $element) {
                if ($element > 0) {
                    $groupId = sql_getValue('edomiProject.editVisuElement', 'groupid', 'id=' . $element . ' AND controltyp<>0');
                    if ($groupId > 0) {
                        if (array_search($groupId, $tmp) === false) {
                            $groupId = 0;
                        }
                    }
                    sql_call("UPDATE edomiProject.editVisuElement SET visuid='" . $visuId . "',pageid='" . $dataArr[0] . "',groupid=" . sql_encodeValue($groupId, true) . " WHERE id=" . $element);
                    $newElements[] = $element;
                }
            }
            $ss1 = sql_call("SELECT id,pageid FROM edomiProject.editVisuElement WHERE controltyp=0");
            while ($n = sql_result($ss1)) {
                sql_call("UPDATE edomiProject.editVisuElement SET groupid=0 WHERE groupid=" . $n['id'] . " AND pageid<>" . $n['pageid']);
            }
            sql_close($ss1);
            $ss1 = sql_call("SELECT id,linkid,pageid FROM edomiProject.editVisuElement AS a WHERE a.linkid>0 HAVING a.linkid NOT IN (SELECT id FROM edomiProject.editVisuElement WHERE id=a.linkid AND pageid=a.pageid)");
            while ($n = sql_result($ss1)) {
                sql_call("UPDATE edomiProject.editVisuElement SET linkid=0 WHERE id=" . $n['id']);
            }
            sql_close($ss1); ?>
            app2_refreshAll(0,"<? echo implode(';', $newElements); ?>");
        <? }
    }
    if ($cmd == 'createGroup') {
        $visuId = sql_getValue('edomiProject.editVisuPage', 'visuid', 'id=' . $dataArr[0]);
        if ($visuId > 0) {
            $tmp = explode(';', $phpdataArr[0]);
            $err = false;
            foreach ($tmp as $element) {
                if ($element > 0) {
                    $n = sql_getValue('edomiProject.editVisuElement', 'id', 'id=' . $element . ' AND (groupid>0 OR controltyp=0)');
                    if ($n > 0) {
                        $err = true;
                        break;
                    }
                }
            }
            if (!$err) {
                $newElements = array();
                $dbId = db_itemSave('editVisuElement', array(1 => -1, 6 => 0, 20 => 'Neue Gruppe', 97 => $visuId, 98 => $dataArr[0]));
                if ($dbId > 0) {
                    $newElements[] = $dbId;
                    foreach ($tmp as $element) {
                        if ($element > 0) {
                            sql_call("UPDATE edomiProject.editVisuElement SET groupid=" . $dbId . " WHERE id=" . $element);
                            $newElements[] = $element;
                        }
                    }
                } ?>
                app2_refreshAll(0,"<? echo implode(';', $newElements); ?>");
            <? } else { ?>
                jsConfirm("Gruppen oder Elemente einer Gruppe können nicht erneut gruppiert werden.","","none");
            <? }
        }
    }
    if ($cmd == 'uncreateGroup') {
        sql_call("UPDATE edomiProject.editVisuElement SET groupid=0 WHERE (groupid='" . $phpdataArr[0] . "')");
        sql_call("DELETE FROM edomiProject.editVisuElement WHERE (id='" . $phpdataArr[0] . "')"); ?>
        app2_refreshAll(0);
    <? }
}

sql_disconnect();
function app2_getElementStyle($item, $vseDef, &$head, $previewMode, $previewValue1, $previewValue3)
{
    if ($previewMode) {
        if ($item['gaid3'] > 0) {
            $koDPT = sql_getValue('edomiProject.editKo', 'valuetyp', 'id=' . $item['gaid3']);
            $design = visu_getElementDesignData(false, $item['dynstylemode'], $item['id'], $previewValue3, $koDPT);
        } else {
            if ($item['gaid'] > 0) {
                $koDPT = sql_getValue('edomiProject.editKo', 'valuetyp', 'id=' . $item['gaid']);
                $design = visu_getElementDesignData(false, $item['dynstylemode'], $item['id'], $previewValue1, $koDPT);
            } else {
                $design = visu_getElementDesignData(false, $item['dynstylemode'], $item['id'], null, null);
            }
        }
    } else {
        $design = visu_getElementDesignData(false, -1, $item['id'], null, null);
    }
    $n = visu_getElementStyleCss($item, $vseDef, $design, $previewMode, false); ?>
    var cssFonts=document.getElementById("cssFonts").innerHTML;
    var cssAnims=document.getElementById("cssAnims").innerHTML;
    <? foreach ($n[2][0] as $tmpId => $tmp) {
    if (!isset($head[0][$tmpId])) {
        $head[0][$tmpId] = true; ?>
        cssFonts+="<? ajaxValue($tmp); ?>\n";
    <? }
}
    if ($previewMode) {
        foreach ($n[2][1] as $tmpId => $tmp) {
            if (!isset($head[1][$tmpId])) {
                $head[1][$tmpId] = true; ?>
                cssAnims+="<? ajaxValue(str_replace(chr(10), ' ', $tmp)); ?>\n";
            <? }
        }
    } ?>
    document.getElementById("cssFonts").innerHTML=cssFonts;
    document.getElementById("cssAnims").innerHTML=cssAnims;
    <? if (!isEmpty($design['s11']) && $design['styletyp'] == 1) {
    return array($n[0] . $n[1], $design['s11']);
} else {
    return array($n[0] . $n[1], $item['text']);
}
} ?>
