<?
/*
*/
?><? ?><? require("../../shared/php/config.php");
require(MAIN_PATH . "/www/shared/php/base.php");
require(MAIN_PATH . "/www/shared/php/incl_http.php");
require(MAIN_PATH . "/www/shared/php/incl_camera.php");
require(MAIN_PATH . "/www/admin/include/php/config.php");
require(MAIN_PATH . "/www/admin/include/php/base.php");
require(MAIN_PATH . "/www/admin/include/php/incl_items.php");
sql_connect();
if (checkAdmin($sid)) {
    cmd($cmd);
    initControls($cmd);
}
sql_disconnect();
function cmd($cmd)
{
    global $appId, $winId, $data, $dataArr, $phpdata, $phpdataArr, $sid;
    global $global_dpt;
    $options = parseOptions();
    $rootFolder = parseFolderId($dataArr[1]);
    $currentFolder = parseFolderId($dataArr[6]);
    if (!(intval($currentFolder[0]) > 0)) {
        $currentFolder = $rootFolder;
    }
    $currentDbName = sql_getValue('edomiProject.editRoot', 'namedb', 'id=' . $currentFolder[0]);
    if ($cmd == 'initApp') {
        if (getEditProjektId() === false) { ?>
            closeWindow("<? echo $winId; ?>");
            jsConfirm("Es ist kein Arbeitsprojekt vorhanden.","","none");
            <? return;
        }
        if ($options['typ'] == 6) {
            cmd('directItemEdit');
            return;
        } ?>
        var n="";
        <? if ($options['typ'] == 0) { ?>
            n+="<table width='100%' height='100%' cellpadding='0' cellspacing='0' border='0'><tr valign='middle'><td align='center' onClick='flashWindow(this);'>";
                                                                                                                                                                 n+="<div class='appWindowNormal' id='<? echo $winId; ?>-global' data-markedfolder='' data-jsdata='' data-optiontyp='' data-cursor='' data-sysfolderid='' data-searchresult='' data-searchcursor=''>";
                                                                                                                                                                                                                                                                                                                                                                    n+="
            <div class='appTitel'><? ajaxEcho($options['title']); ?>
                <div class='cmdClose' onClick='app1000_cancel(\"<? echo $winId; ?>\");'></div>
                <div id='<? echo $winId; ?>-help' class='cmdHelp' data-helpid='<? echo $appId; ?>' onClick='openWindow(9999,this.dataset.helpid);'></div>
            </div>";
        <? } else { ?>
            n+="<div class='appWindowDrag' id='<? echo $winId; ?>-global' data-markedfolder='' data-jsdata='' data-optiontyp='' data-cursor='' data-sysfolderid='' data-searchresult='' data-searchcursor=''>";
                                                                                                                                                                                                             n+="
            <div class='appTitelDrag' onMouseDown='dragWindowStart(\"<? echo $appId; ?>\",\"<? echo $winId; ?>-global\");'><? ajaxEcho($options['title']); ?>
                <div class='cmdClose' onClick='app1000_cancel(\"<? echo $winId; ?>\");'></div>
                <div id='<? echo $winId; ?>-help' class='cmdHelp' data-helpid='<? echo $appId; ?>' onClick='openWindow(9999,this.dataset.helpid);'></div>
            </div>";
        <? } ?>
        n+="
        <div id='<? echo $winId; ?>-main' style='width:850px;'>";

            n+="
            <div class='appMenu' style='margin-bottom:5px;'>";
                <? if ($options['typ'] == 1 || $options['typ'] == 2 || $options['typ'] == 3 || $options['typ'] == 4) { ?>
                    n+="
                    <div class='cmdButton cmdButtonL' onClick='app1000_cancel(\"<? echo $winId; ?>\");'>Abbrechen</div>";
                                                                                                                       n+="
                    <div class='cmdButton cmdButtonM' onClick='app1000_buttonReturnClick(\"<? echo $winId; ?>\");'><b>Übernehmen</b></div>";
                    <? if ($options['reset'] == 1) { ?>
                        n+="
                        <div class='cmdButton cmdButtonR' onClick='app1000_itemReturnReset(\"<? echo $winId; ?>\");'>Zurücksetzen</div>";
                    <? } else { ?>
                        n+="
                        <div class='cmdButton cmdButtonR cmdButtonDisabled'>Zurücksetzen</div>";
                    <? } ?>
                    n+="&nbsp;&nbsp;&nbsp;";
                <? }
                if ($options['typ'] != 4) { ?>
                    n+="
                    <div class='cmdButton cmdButtonL' onClick='app1000_buttonEditClick(\"<? echo $winId; ?>\")'>Bearbeiten</div>";
                                                                                                                                n+="
                    <div class='cmdButton cmdButtonR' onClick='app1000_buttonNewClick(\"<? echo $winId; ?>\")'>Neu</div>";
                <? } ?>
                n+="
            </div>
            ";

            n+="
            <div class='appContentBlank'>";
                n+="
                <table width='100%' border='0' cellpadding='0' cellspacing='0' style='table-layout:auto;'>";
                    n+="
                    <tr>";
                        n+="
                        <td><input type='text' id='<? echo $winId; ?>-searchInput' data-type='1' value=''
                                   onInput='app1000_itemSearchReset(\"<? echo $winId; ?>\",false);' onkeydown='app1000_itemSearchKey(\"<? echo $winId; ?>\");'
                                   autofocus class='control1' style='width:100%; height:27px; margin:0; outline:none;'></input></td>
                        ";
                        n+="
                        <td width='30' style='padding-left:2px;'>
                            <div class='cmdButton cmdButtonL' onClick='app1000_itemSearchMove(\"<? echo $winId; ?>\",-1,false);'
                                 style='min-width:30px; width:30px;'>&lt;
                            </div>
                        </td>
                        ";
                        n+="
                        <td width='30'>
                            <div class='cmdButton cmdButtonM' onClick='app1000_itemSearchMove(\"<? echo $winId; ?>\",0,false);'
                                 style='min-width:30px; width:30px;'>&equiv;
                            </div>
                        </td>
                        ";
                        n+="
                        <td width='30'>
                            <div class='cmdButton cmdButtonR' onClick='app1000_itemSearchMove(\"<? echo $winId; ?>\",1,false);'
                                 style='min-width:30px; width:30px;'>&gt;
                            </div>
                        </td>
                        ";
                        n+="
                    </tr>
                    ";
                    n+="
                </table>
                ";

                n+="
                <table width='100%' border='0' cellpadding='0' cellspacing='0' style='margin-top:5px; table-layout:auto;'>";
                    n+="
                    <tr>";
                        n+="
                        <td width='165'>
                            <div id='<? echo $winId; ?>-menuRoot' class='columnMenu' style='height:550px;'></div>
                        </td>
                        ";
                        n+="
                        <td style='padding-left:5px;'>
                            <div id='<? echo $winId; ?>-folderRoot' class='columnContent' onMouseDown='app1000_columnContentClick(\"<? echo $winId; ?>\");'
                                 style='display:block; max-width:670px; height:550px;'></div>
                        </td>
                        ";
                        n+="
                    </tr>
                    ";
                    n+="
                </table>
                ";
                n+="
            </div>
            ";

            n+="
        </div>";
        n+="
        <div id='<? echo $winId; ?>-edit' class='appWindowOverlay' data-drag='<? echo(($options['typ'] == 0) ? '0' : '1'); ?>'></div>";
        n+="</div>";
        <? if ($options['typ'] == 0) { ?>
            n+="</td></tr></table>";
        <? } ?>
        document.getElementById("<? echo $winId; ?>").innerHTML=n;
        <? if ($options['typ'] != 0) { ?>
            dragWindowRestore("<? echo $appId; ?>","<? echo $winId; ?>-global");
        <? }
        $ss1 = sql_call("SELECT id,name FROM edomiProject.editRoot WHERE id<1000 AND parentid=0 ORDER by id ASC");
        while ($folder = sql_result($ss1)) { ?>
            var folder=createNewDiv('<? echo $winId; ?>-menuRoot','<? echo $winId; ?>-m-<? echo $folder['id']; ?>');
            folder.className='columnMenuItem';
            folder.innerHTML="<? ajaxEcho($folder['name']); ?>";
            <? if ($options['typ'] == 0 || $options['typ'] == 3) { ?>
                folder.setAttribute('onClick','app1000_menuClick("<? echo $winId; ?>","'+folder.id+'","<? echo $folder['id']; ?>");');
            <? } else { ?>
                folder.style.color="#a0a0a0";
            <? }
        }
        sql_close($ss1); ?>
        app1000_resetMarkedFolder("<? echo $winId; ?>");
        <? cmd('initFolders');
        if ($options['typ'] == 1 || $options['typ'] == 2 || $options['typ'] == 3 || $options['typ'] == 4 || $options['typ'] == 5) {
            if ($dataArr[2] > 0) {
                $folderId = sql_getValue('edomiProject.' . $currentDbName, 'folderid', "id='" . $dataArr[2] . "'"); ?>
                app1000_itemExpandToItem("<? echo $winId; ?>","<? echo $winId; ?>-i-<? echo $folderId; ?>-<? echo $dataArr[2]; ?>");
            <? } else { ?>
                app1000_folderRestoreHistory("<? echo $winId; ?>");
            <? }
        }
    }
    if ($cmd == 'directItemEdit') {
        $ok = false;
        if ($dataArr[2] > 0) {
            $folderId = sql_getValue('edomiProject.' . $currentDbName, 'folderid', "id='" . $dataArr[2] . "'");
            if (!isEmpty($currentDbName) && !isEmpty($folderId)) { ?>
                var n="";
                n+="
                <div class='appWindowDrag' id='<? echo $winId; ?>-global' data-markedfolder='' data-jsdata='' data-optiontyp=''>";
                    n+="
                    <div class='appTitelDrag'
                         onMouseDown='dragWindowStart(\"<? echo $appId; ?>\",\"<? echo $winId; ?>-global\");'><? ajaxEcho($options['title']); ?>
                        <div class='cmdClose cmdCloseDisabled'></div>
                        <div id='<? echo $winId; ?>-help' class='cmdHelp' data-helpid='<? echo $appId; ?>-<? echo $currentFolder[0]; ?>'
                             onClick='openWindow(9999,this.dataset.helpid);'></div>
                    </div>
                    ";
                    n+="
                    <div id='<? echo $winId; ?>-main' style='width:850px; height:630px;'></div>
                    ";
                    n+="
                    <div id='<? echo $winId; ?>-edit' class='appWindowOverlay' data-drag='1'></div>
                    ";
                    n+="
                </div>";
                n+="</td></tr>";
                document.getElementById("<? echo $winId; ?>").innerHTML=n;
                dragWindowRestore("<? echo $appId; ?>","<? echo $winId; ?>-global");
                <? ?>
                document.getElementById("<? echo $winId; ?>-global").dataset.jsdata="<? ajaxEcho($data); ?>";
                document.getElementById("<? echo $winId; ?>-global").dataset.optiontyp="<? ajaxEcho($options['typ']); ?>";
                <? $phpdataArr[0] = $folderId;
                $phpdataArr[1] = $dataArr[2];
                editItem($currentDbName);
                $ok = true;
            }
        }
        if (!$ok) { ?>
            closeWindow("<? echo $winId; ?>");
            jsConfirm("Das Element existiert nicht und kann daher nicht bearbeitet werden.","","none");
        <? }
    }
    if ($cmd == 'searchItem') {
        $result = '';
        $folder = sql_getValues('edomiProject.editRoot', 'id,namedb', "id='" . $currentFolder[0] . "' AND id<1000");
        if ($folder !== false) {
            if (!isEmpty($folder['namedb'])) {
                $result = app1000_searchItems($currentFolder[0], $currentFolder[1], $phpdataArr[0]);
            } else {
                $ss1 = sql_call("SELECT id FROM edomiProject.editRoot WHERE (parentid=" . $folder['id'] . " AND id<1000) ORDER BY id ASC");
                while ($n = sql_result($ss1)) {
                    $result .= app1000_searchItems($n['id'], 0, $phpdataArr[0]);
                }
                sql_close($ss1);
            }
        } ?>
        document.getElementById("<? echo $winId; ?>-global").dataset.searchresult="<? echo $result; ?>";
        app1000_itemSearchMove("<? echo $winId; ?>",1,true);
    <? }
    if ($cmd == 'newItem') {
        $selectedFolder = parseFolderId($phpdataArr[0]);
        $folder = sql_getValues('edomiProject.editRoot', 'id,namedb,linkid', 'id=' . $selectedFolder[0]);
        $phpdataArr[0] = $folder['id'];
        $phpdataArr[1] = -1;
        if ($selectedFolder[0] == 22) {
            if ($selectedFolder[1] !== false) {
                editItem($folder['namedb'], $selectedFolder[1]);
            }
        } else {
            editItem($folder['namedb'], $folder['linkid']);
        }
    }
    if ($cmd == 'editItem') {
        $itemMeta = getItemMetaFromObjId($phpdataArr[0]);
        if ($itemMeta !== false) {
            $phpdataArr[0] = $itemMeta[1];
            $phpdataArr[1] = $itemMeta[0];
            editItem($itemMeta[2]);
        }
    }
    if ($cmd == 'saveItem') {
        saveItem($phpdataArr[0]);
    }
    if ($cmd == 'showItemLinks') {
        $itemMeta = getItemMetaFromObjId($phpdataArr[0]);
        if ($itemMeta !== false) {
            $n = db_itemLinks($itemMeta[2], $itemMeta[0]);
            if (!isEmpty($n)) { ?>
                ajaxConfirm('Verweise auf dieses Element:<br>
                <br><? ajaxValue($n); ?>','showItemLinks2','1000','<? echo $winId ?>','<? echo $data ?>','<? echo $itemMeta[2]; ?><? echo AJAX_SEPARATOR1; ?><? echo $itemMeta[1]; ?><? echo AJAX_SEPARATOR1; ?><? echo $itemMeta[0]; ?><? echo AJAX_SEPARATOR1; ?><? ajaxValue($n); ?>','Abbrechen','&gt; Merkliste');
            <? } else { ?>
                jsConfirm("Es sind keine Verweise auf dieses Element verhanden.","","none");
            <? }
        }
    }
    if ($cmd == 'showItemLinks2') {
        if ($phpdataArr[0] == 'editVisuUser') {
            $tmp = 'login';
        } else {
            $tmp = 'name';
        } ?>
        pushDesktopNotes(1,"Verweise auf: <? ajaxValue(dbRoot_getFullPath($phpdataArr[1])); ?><? ajaxValue(sql_getValue('edomiProject.' . $phpdataArr[0], $tmp, 'id=' . $phpdataArr[2])); ?> (<? ajaxValue($phpdataArr[2]); ?>)
        <br><? ajaxValue($phpdataArr[3]); ?>");
    <? }
    if ($cmd == 'duplicateItem') {
        $itemMeta = getItemMetaFromObjId($phpdataArr[0]);
        if ($itemMeta !== false) {
            db_itemDuplicate($itemMeta[2], $itemMeta[0], 0, '-KOPIE');
            cmd('refreshFolders');
        }
    }
    if ($cmd == 'deleteItem') {
        $itemMeta = getItemMetaFromObjId($phpdataArr[0]);
        if ($itemMeta !== false) {
            $n = db_itemLinks($itemMeta[2], $itemMeta[0]);
            $msg = 'Soll dieses Element wirklich gelöscht werden?';
            $msgSecure = false;
            if ($itemMeta[2] == 'editLogicElementDef') {
                $msg = 'Soll dieser Logikbaustein einschließlich sämtlicher Instanzen wirklich gelöscht werden?';
                $msgSecure = true;
            }
            if ($itemMeta[2] == 'editVisuElementDef') {
                $msg = 'Soll dieses Visuelement einschließlich sämtlicher Instanzen wirklich gelöscht werden?';
                $msgSecure = true;
            }
            if ($itemMeta[2] == 'editVisu') {
                $msg = 'Soll diese Visualisierung einschließlich sämtlicher Visuseiten wirklich gelöscht werden?';
                $msgSecure = true;
            }
            if (isEmpty($n)) {
                if ($msgSecure) { ?>
                    ajaxConfirmSecure('<? echo $msg; ?>','deleteItem2','1000','<? echo $winId ?>','<? echo $data ?>','<? echo $phpdataArr[0] ?>','','Löschen');
                <? } else { ?>
                    ajaxConfirm('<? echo $msg; ?>','deleteItem2','1000','<? echo $winId ?>','<? echo $data ?>','<? echo $phpdataArr[0] ?>','','Löschen');
                <? }
            } else { ?>
                ajaxConfirmSecure('<? echo $msg; ?><br><br>Es sind Verweise auf dieses Element vorhanden:<br>
                <br><? ajaxValue($n); ?>','deleteItem2','1000','<? echo $winId ?>','<? echo $data ?>','<? echo $phpdataArr[0] ?>','','Löschen');
            <? }
        }
    }
    if ($cmd == 'deleteItem2') {
        $itemMeta = getItemMetaFromObjId($phpdataArr[0]);
        if ($itemMeta !== false) {
            db_itemDelete($itemMeta[2], $itemMeta[0]);
            cmd('refreshFolders');
        }
    }
    if ($cmd == 'itemContextMenu') {
        $itemMeta = getItemMetaFromObjId($phpdataArr[0]);
        if ($itemMeta !== false) {
            $item = sql_getValues('edomiProject.' . $itemMeta[2], '*', 'id=' . $itemMeta[0]);
            if ($item !== false) {
                $folder = sql_getValues('edomiProject.editRoot', '*', 'id=' . $item['folderid']);
                $folderRoot = sql_getValues('edomiProject.editRoot', '*', 'id=' . $folder['rootid']);
                if ($options['typ'] != 4 && $folderRoot['allow'] & 8) { ?>
                    apps_contextMenu.addItem("Element bearbeiten <span
                        class='id'><? echo $item['id']; ?></span>","ajax('editItem','1000','<? echo $winId; ?>','<? ajaxEcho($data); ?>','<? echo $phpdataArr[0]; ?>');");
                <? }
                if ($options['typ'] != 4 && $folderRoot['allow'] & 32) { ?>
                    apps_contextMenu.addItem("Element duplizieren","ajax('duplicateItem','1000','<? echo $winId; ?>','<? ajaxEcho($data); ?>','<? echo $phpdataArr[0]; ?>');");
                <? }
                if ($options['typ'] != 4 && $folderRoot['allow'] & 64) { ?>
                    apps_contextMenu.addItem("Element löschen","ajax('deleteItem','1000','<? echo $winId; ?>','<? ajaxEcho($data); ?>','<? echo $phpdataArr[0]; ?>');");
                <? }
                if ($options['typ'] != 4) { ?>
                    apps_contextMenu.addHr();
                    apps_contextMenu.addItem("Verweise anzeigen","ajax('showItemLinks','1000','<? echo $winId; ?>','<? ajaxEcho($data); ?>','<? echo $phpdataArr[0]; ?>');");
                <? }
                if ($options['typ'] != 4) {
                    app1000_contextMenuSelection();
                }
                if ($itemMeta[2] == 'editLogicElementDef') { ?>
                    apps_contextMenu.addHr();
                    apps_contextMenu.addItem("Hilfe","openWindow(9999,'lbs_<? echo $item['id']; ?>','');");
                <? }
                if ($itemMeta[2] == 'editVisuElementDef') { ?>
                    apps_contextMenu.addHr();
                    apps_contextMenu.addItem("Hilfe","openWindow(9999,'1002-<? echo $item['id']; ?>','');");
                <? }
                if ($itemMeta[2] == 'editVisuImg') {
                    $tmp = '../data/project/visu/img/img-' . $item['id'] . '.' . $item['suffix'] . '?' . date('YmdHis'); ?>
                    apps_contextMenu.addHr();
                    apps_contextMenu.addText("
                    <div
                        style='width:100%; height:120px; margin-top:5px; background:url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAQAAAAECAYAAACp8Z5+AAAAJUlEQVQIHWNsaGj4zwAFCgoKDEzInAcPHkAEQDIgDggwIXNAAgCU4wvnT6Hq5QAAAABJRU5ErkJggg==) repeat;'>
                        <div
                            style='width:100%; height:100%; background-size:contain; background-repeat:no-repeat; background-image:url(\"<? echo $tmp; ?>\");'></div>
                    </div>");
                <? }
                if ($itemMeta[2] == 'editHttpKo') {
                    $tmp = sql_getValue('edomiProject.editHttpKo', 'gaid', 'id=' . $item['id']);
                    if (!isEmpty($tmp)) { ?>
                        apps_contextMenu.addHr();
                        apps_contextMenu.addText("KO-ID für Fernzugriff: <span class='id'><? echo $tmp; ?></span>");
                    <? }
                }
            }
        } ?>
        apps_contextMenu.show();
    <? }
    if ($cmd == 'sortFolder') {
        $folder = parseFolderId($phpdataArr[0]);
        $tmp = sql_getValue('edomiProject.editRoot', 'sortid', "id=" . $folder[0] . " AND sortid<>0 AND sortcolumns<>''");
        if (abs($tmp) > 0) {
            if (abs($tmp) == $phpdataArr[1]) {
                $phpdataArr[1] = -$tmp;
            }
            sql_call("UPDATE edomiProject.editRoot SET sortid=" . $phpdataArr[1] . " WHERE id=" . $folder[0] . " AND sortid<>0 AND sortcolumns<>''"); ?>
            app1000_itemSearchReset("<? echo $winId; ?>",false);
            <? cmd('refreshFolders');
        }
    }
    if ($cmd == 'folderNew') {
        $folder = parseFolderId($phpdataArr[0]);
        $dbId = db_itemSave('editRoot', array(1 => -1, 2 => 'Neuer Ordner', 3 => $folder[0], 4 => $folder[1]));
        if ($dbId > 0) {
            cmd('refreshFolders'); ?>
            app1000_folderExpandToFolder("<? echo $winId; ?>","<? echo $winId; ?>-f-<? echo $dbId; ?>");
            <? $phpdataArr[0] = $winId + '-f-' + $dbId;
            $phpdataArr[1] = 1;
            cmd('folderRename');
        } else { ?>
            jsConfirm("Fehler: Beim Erstellen des Ordners ist ein Problem aufgetreten!","","none");
        <? }
    }
    if ($cmd == 'folderRename') {
        $folder = parseFolderId($phpdataArr[0]);
        $n = sql_getValues('edomiProject.editRoot', 'name', 'id=' . $folder[0] . ' AND id>=1000');
        if ($n !== false) { ?>
            var obj=document.getElementById("<? echo $winId . '-cap-' . $folder[0]; ?>");
            obj.innerHTML="
            <div id='<? echo $winId; ?>-formRename' style='display:inline;'><input type='text' id='<? echo $winId; ?>-fd0' autofocus data-type='1' value=''
                                                                                   maxlength='100' onMouseDown='clickCancel();'
                                                                                   onkeydown='if (event.keyCode==13){this.blur();}'
                                                                                   onBlur='ajax(\"folderRenameSave\",\"<? echo $appId; ?>\",\"<? echo $winId; ?>\",\"<? echo $data; ?>\",\"<? echo $phpdataArr[0] . AJAX_SEPARATOR1; ?>\"+controlGetFormData(\"<? echo $winId; ?>-formRename\"));'
                                                                                   class='control1' style='width:100%; margin:2px; color:#0000ff;'></input>
            </div>";
            document.getElementById("<? echo $winId; ?>-fd0").value='<? ajaxValue($n['name']); ?>';
            appAll_setAutofocus("<? echo $winId; ?>-formRename");
            <? if ($phpdataArr[1] == '1') { ?>
                document.getElementById("<? echo $winId; ?>-fd0").select();
            <? }
        }
    }
    if ($cmd == 'folderRenameSave') {
        $folder = parseFolderId($phpdataArr[0]);
        if (!isEmpty($phpdataArr[1])) {
            $dbId = db_itemSave('editRoot', array(1 => $folder[0], 2 => $phpdataArr[1]));
        }
        cmd('refreshFolders');
    }
    if ($cmd == 'folderDuplicate') {
        $dbId = db_folderDuplicate($phpdataArr[0], 0, '-KOPIE');
        cmd('refreshFolders');
    }
    if ($cmd == 'folderDelete') { ?>
        ajaxConfirmSecure('Soll dieser Ordner wirklich gelöscht werden?<br>
        <br>Alle Unterordner und Elemente in diesem Ordner werden gelöscht und alle Verweise werden zurückgesetzt!','folderDelete2','1000','<? echo $winId ?>','<? echo $data ?>','<? echo $phpdataArr[0]; ?>','','Löschen');
    <? }
    if ($cmd == 'folderDelete2') {
        $r = db_folderDelete($phpdataArr[0]);
        if (!$r) { ?> jsConfirm("Fehler: Der Ordner konnte nicht vollständig gelöscht werden!","","none"); <? }
        cmd('refreshFolders');
    }
    if ($cmd == 'duplicateSelected') {
        $selected = app1000_parseSelectedElements($phpdataArr[0]);
        if ($selected !== false) {
            $newSelection = array();
            $err = 0;
            foreach ($selected[2] as $folderId) {
                $dbId = db_folderDuplicate($folderId, 0, '-KOPIE');
                if ($dbId > 0) {
                    $newSelection[] = $winId . '-f-' . $dbId;
                } else {
                    $err++;
                }
            }
            foreach ($selected[3] as $itemId) {
                $dbId = db_itemDuplicate($selected[1], $itemId, 0, '-KOPIE');
                $tmp = sql_getValue();
                if ($dbId > 0) {
                    $newSelection[] = $winId . '-i-' . sql_getValue('edomiProject.' . $selected[1], 'folderid', 'id=' . $dbId) . '-' . $dbId;
                } else {
                    $err++;
                }
            }
            cmd('refreshFolders'); ?>
            app1000_elementSelectFromList("<? echo $winId; ?>","<? echo implode(';', $newSelection); ?>");
            <? if ($err > 0) { ?>
                jsConfirm("Fehler: <? echo $err; ?> Elemente/Ordner konnten nicht dupliziert werden!","","none");
            <? }
        } else { ?>
            shakeObj("<? echo $winId; ?>");
        <? }
    }
    if ($cmd == 'deleteSelected') {
        $selected = app1000_parseSelectedElements($phpdataArr[0]);
        if ($selected !== false) {
            $err = 0;
            foreach ($selected[2] as $folderId) {
                $r = db_folderDelete($folderId);
                if (!$r) {
                    $err++;
                }
            }
            foreach ($selected[3] as $itemId) {
                $dbId = db_itemDelete($selected[1], $itemId);
            }
            cmd('refreshFolders');
            if ($err > 0) { ?>
                jsConfirm("Fehler: <? echo $err; ?> Ordner konnten nicht vollständig gelöscht werden!","","none");
            <? }
        } else { ?>
            shakeObj("<? echo $winId; ?>");
        <? }
    }
    if ($cmd == 'copySelected') {
        $targetFolder = parseFolderId($phpdataArr[1]);
        $selected = app1000_parseSelectedElements($phpdataArr[0], $targetFolder[0]);
        if ($selected !== false) {
            $newSelection = array();
            $err = 0;
            foreach ($selected[2] as $folderId) {
                $dbId = db_folderDuplicate($folderId, $targetFolder[0], '-KOPIE');
                if ($dbId > 0) {
                    $newSelection[] = $winId . '-f-' . $dbId;
                } else {
                    $err++;
                }
            }
            foreach ($selected[3] as $itemId) {
                $dbId = db_itemDuplicate($selected[1], $itemId, $targetFolder[0], '-KOPIE');
                if ($dbId > 0) {
                    $newSelection[] = $winId . '-i-' . sql_getValue('edomiProject.' . $selected[1], 'folderid', 'id=' . $dbId) . '-' . $dbId;
                } else {
                    $err++;
                }
            }
            cmd('refreshFolders'); ?>
            app1000_elementSelectFromList("<? echo $winId; ?>","<? echo implode(';', $newSelection); ?>");
            <? if ($err > 0) { ?>
                jsConfirm("Fehler: <? echo $err; ?> Elemente/Ordner konnten nicht dupliziert werden!","","none");
            <? }
        } else { ?>
            shakeObj("<? echo $winId; ?>");
        <? }
    }
    if ($cmd == 'moveSelected') {
        $targetFolder = parseFolderId($phpdataArr[1]);
        $selected = app1000_parseSelectedElements($phpdataArr[0], $targetFolder[0]);
        if ($targetFolder[0] > 0 && $selected !== false) {
            $newSelection = array();
            $err = 0;
            foreach ($selected[2] as $folderId) {
                $dbId = db_folderMove($folderId, $targetFolder[0]);
                if ($dbId > 0) {
                    $newSelection[] = $winId . '-f-' . $dbId;
                } else {
                    $err++;
                }
            }
            foreach ($selected[3] as $itemId) {
                $tmp = sql_call("UPDATE edomiProject." . $selected[1] . " SET folderid=" . $targetFolder[0] . " WHERE id=" . $itemId);
                if ($tmp) {
                    $dbId = $itemId;
                } else {
                    $dbId = false;
                }
                if ($dbId > 0) {
                    $newSelection[] = $winId . '-i-' . sql_getValue('edomiProject.' . $selected[1], 'folderid', 'id=' . $dbId) . '-' . $dbId;
                } else {
                    $err++;
                }
            }
            cmd('refreshFolders'); ?>
            app1000_elementSelectFromList("<? echo $winId; ?>","<? echo implode(';', $newSelection); ?>");
            <? if ($err > 0) { ?>
                jsConfirm("Fehler: <? echo $err; ?> Elemente/Ordner konnten nicht verschoben werden!","","none");
            <? }
        } else { ?>
            shakeObj("<? echo $winId; ?>");
        <? }
    }
    if ($cmd == 'folderCreateElement') {
        $folderMeta = getFolderMetaFromObjId($phpdataArr[0]);
        if ($folderMeta !== false) {
            $selectedFolderId = $folderMeta[0];
            $selectedFolder = parseFolderId($selectedFolderId);
            $folder = sql_getValues('edomiProject.editRoot', '*', 'id=' . $selectedFolder[0]);
            if ($folder !== false) {
                $folderRoot = sql_getValues('edomiProject.editRoot', '*', 'id=' . $folder['rootid']);
                if ($options['typ'] != 4 && $folderRoot['allow'] & 2 && (!($folderRoot['allow'] & 1) || ($folderRoot['allow'] & 1 && ($folder['id'] >= 1000 || $selectedFolder[1] !== false)))) {
                    $phpdataArr[0] = $selectedFolderId;
                    cmd('newItem');
                    return;
                }
            }
        } ?>
        shakeObj("<? echo $winId; ?>");
    <? }
    if ($cmd == 'folderContextMenu') {
        $folderMeta = getFolderMetaFromObjId($phpdataArr[0]);
        if ($folderMeta !== false) {
            $selectedFolderId = $folderMeta[0];
            $selectedFolder = parseFolderId($selectedFolderId);
            $folder = sql_getValues('edomiProject.editRoot', '*', 'id=' . $selectedFolder[0]);
            if ($folder !== false) {
                $folderRoot = sql_getValues('edomiProject.editRoot', '*', 'id=' . $folder['rootid']);
                if ($options['typ'] != 4 && $folderRoot['allow'] & 2 && (!($folderRoot['allow'] & 1) || ($folderRoot['allow'] & 1 && ($folder['id'] >= 1000 || $selectedFolder[1] !== false)))) { ?>
                    apps_contextMenu.addItem("Element erstellen","ajax('newItem','1000','<? echo $winId; ?>','<? ajaxEcho($data); ?>','<? echo $selectedFolderId; ?>');");
                <? }
                if ($options['typ'] != 4 && $folderRoot['allow'] & 4 && (!($folderRoot['allow'] & 1) || ($folderRoot['allow'] & 1 && ($folder['id'] >= 1000 || $selectedFolder[1] !== false)))) { ?>
                    apps_contextMenu.addItem("Ordner erstellen","ajax('folderNew','1000','<? echo $winId; ?>','<? ajaxEcho($data); ?>','<? echo $selectedFolderId; ?>');");
                <? } ?>
                apps_contextMenu.addHr();
                <? if ($options['typ'] != 4 && $folder['id'] >= 1000 && $folderRoot['allow'] & 8) { ?>
                    apps_contextMenu.addItem("Ordner umbennenen <span
                        class='id'><? echo $folder['id']; ?></span>","ajax('folderRename','1000','<? echo $winId; ?>','<? ajaxEcho($data); ?>','<? echo $selectedFolderId; ?>');");
                <? }
                if ($options['typ'] != 4 && $folder['id'] >= 1000 && $folderRoot['allow'] & 32) { ?>
                    apps_contextMenu.addItem("Ordner duplizieren","ajax('folderDuplicate','1000','<? echo $winId; ?>','<? ajaxEcho($data); ?>','<? echo $selectedFolderId; ?>');");
                <? }
                if ($options['typ'] != 4 && $folder['id'] >= 1000 && $folderRoot['allow'] & 64) { ?>
                    apps_contextMenu.addItem("Ordner löschen","ajax('folderDelete','1000','<? echo $winId; ?>','<? ajaxEcho($data); ?>','<? echo $selectedFolderId; ?>');");
                <? }
                if ($options['typ'] != 4) {
                    app1000_contextMenuSelection();
                }
                $sort = dbRoot_getOrdnerString($folder['id'], true);
                if ($sort !== false) { ?>
                    apps_contextMenu.addHr();
                    apps_contextMenu.addText("Sortierung:",1);
                    <? $n = explode(',', $sort[1]);
                    for ($t = 0; $t < count($n); $t++) {
                        $nn = explode('/', $n[$t]); ?>
                        apps_contextMenu.addItem("<span
                            style='<? echo(($t == abs($sort[2]) - 1) ? "color:#00a000; font-weight:bold;" : ""); ?>'><? echo $nn[1]; ?><? echo(($t == abs($sort[2]) - 1) ? (($sort[2] > 0) ? "&#x25BD;" : "&#x25B3;") : ""); ?></span>","ajax('sortFolder','1000','<? echo $winId; ?>','<? ajaxEcho($data); ?>','<? echo $sort[0] . AJAX_SEPARATOR1 . ($t + 1); ?>');",1);
                        <? if ($t < count($n) - 1) { ?>
                            apps_contextMenu.addVr();
                        <? }
                    }
                }
                if ($options['typ'] != 4 && $selectedFolder[0] == 32) { ?>
                    apps_contextMenu.addHr();
                    apps_contextMenu.addItem("Genutzte GAs","ajax('showUsedItems','1000','<? echo $winId; ?>','<? ajaxEcho($data); ?>','<? echo $selectedFolderId; ?>');",1);
                    apps_contextMenu.addVr();
                    apps_contextMenu.addItem("Ungenutzte GAs","ajax('showUnusedItems','1000','<? echo $winId; ?>','<? ajaxEcho($data); ?>','<? echo $selectedFolderId; ?>');",1);
                <? }
            }
        } ?>
        apps_contextMenu.show();
    <? }
    if ($cmd == 'initFolders' || $cmd == 'refreshFolders') { ?>
        document.getElementById("<? echo $winId; ?>-global").dataset.jsdata="<? ajaxEcho($data); ?>";
        document.getElementById("<? echo $winId; ?>-global").dataset.optiontyp="<? ajaxEcho($options['typ']); ?>";
        appAll_setAutofocus("<? echo $winId; ?>-main");
        <? if ($cmd != 'initFolders') { ?>
            app1000_folderSaveExpanded("<? echo $winId; ?>");
        <? } ?>
        clearObject("<? echo $winId; ?>-folderRoot");
        <? if ($currentFolder[0] > 0) { ?>
            document.getElementById('<? echo $winId; ?>-help').dataset.helpid="<? echo $appId; ?>-<? echo $currentFolder[0]; ?>";
            <? $backLink = 0;
            $tmp = sql_getValues('edomiProject.editRoot', 'path,parentid', 'id=' . $currentFolder[0]);
            if ($tmp !== false) {
                $tmp2 = explode('/', $tmp['path']);
                $menuFolderId = ((isEmpty($tmp2[1])) ? $currentFolder[0] : $tmp2[1]);
                if ($options['typ'] == 0 || $options['typ'] == 3) { ?>
                    appAll_menuSelect("<? echo $winId; ?>","<? echo $winId; ?>-m-<? echo $menuFolderId; ?>");
                <? } else { ?>
                    appAll_menuSelect("<? echo $winId; ?>","<? echo $winId; ?>-m-<? echo $menuFolderId; ?>",true);
                <? }
                if ($tmp['parentid'] > 0) {
                    $tmp_ok = false;
                    if ($currentFolder[1] !== false) {
                        $tmp['parentid'] = $currentFolder[0];
                    }
                    if (($options['typ'] == 1 || $options['typ'] == 2 || $options['typ'] == 4 || $options['typ'] == 5) && $rootFolder[0] > 0) {
                        $tmp2 = sql_getValue('edomiProject.editRoot', 'id', "id=" . $tmp['parentid'] . " AND (id=" . $rootFolder[0] . " OR path LIKE '%/" . $rootFolder[0] . "/%')");
                        if (!isEmpty($tmp2) && $rootFolder[1] === false) {
                            $tmp_ok = true;
                        }
                    } else {
                        $tmp_ok = true;
                    }
                    if ($tmp_ok) {
                        $backLink = $tmp['parentid'];
                    }
                }
            }
            $rootFolderId = null;
            if ($currentFolder[1] === false) {
                $ss1 = sql_call("SELECT * FROM edomiProject.editRoot WHERE (id=" . $currentFolder[0] . " OR path LIKE '%/" . $currentFolder[0] . "/%') ORDER BY path ASC,id ASC,name ASC");
                while ($folder = sql_result($ss1)) {
                    if ($folder['link'] == 0) { ?>
                        app1000_folderCreate("<? echo $winId; ?>","<? echo $folder['id']; ?>","<? echo $folder['parentid']; ?>","<? ajaxEcho($folder['name']); ?>",false,"<? if ($backLink > 0) {
                            echo $backLink;
                        } ?>","<? if ($backLink > 0) {
                            ajaxEcho(dbRoot_getFullPath($backLink));
                        } ?>");
                        <? if (isEmpty($rootFolderId)) {
                            $rootFolderId = $folder['id'];
                            $backLink = 0;
                        }
                        if ($folder['id'] == 22) {
                            $ss2 = sql_call("SELECT id,name FROM edomiProject.editVisu ORDER BY " . dbRoot_getOrdnerString(21));
                            while ($visu = sql_result($ss2)) { ?>
                                app1000_folderCreate("<? echo $winId; ?>","<? echo $folder['id']; ?>_<? echo $visu['id']; ?>","<? echo $folder['id']; ?>","<? ajaxEcho($visu['name']); ?>
                                <span class='id' style='background:#00a000;'><? echo $visu['id']; ?></span>");
                            <? }
                            sql_close($ss2);
                        }
                    }
                    if ($folder['link'] == 22) {
                        if ($folder['parentid'] == $folder['link']) { ?>
                            app1000_folderCreate("<? echo $winId; ?>","<? echo $folder['id']; ?>","<? echo $folder['link']; ?>_<? echo $folder['linkid']; ?>","<? ajaxEcho($folder['name']); ?>",true);
                        <? } else { ?>
                            app1000_folderCreate("<? echo $winId; ?>","<? echo $folder['id']; ?>","<? echo $folder['parentid']; ?>","<? ajaxEcho($folder['name']); ?>",true);
                        <? }
                    }
                }
                sql_close($ss1);
            } else {
                if ($currentFolder[0] == 22) {
                    $visu = sql_getValues('edomiProject.editVisu', 'id,name', 'id=' . $currentFolder[1]);
                    if ($visu !== false) { ?>
                        app1000_folderCreate("<? echo $winId; ?>","<? echo $currentFolder[0]; ?>_<? echo $visu['id']; ?>","<? echo $currentFolder[0]; ?>","<? ajaxEcho($visu['name']); ?>
                        <span class='id' style='background:#00a000;'><? echo $visu['id']; ?></span>",false,"<? if ($backLink > 0) {
                            echo $backLink;
                        } ?>","<? if ($backLink > 0) {
                            ajaxEcho(dbRoot_getFullPath($backLink));
                        } ?>");
                        <? if (isEmpty($rootFolderId)) {
                            $rootFolderId = $currentFolder[0] . '_' . $currentFolder[1];
                            $backLink = 0;
                        }
                    }
                }
                $ss1 = sql_call("SELECT * FROM edomiProject.editRoot WHERE (link=" . $currentFolder[0] . " AND linkid=" . $currentFolder[1] . ") ORDER BY path ASC,id ASC,name ASC");
                while ($folder = sql_result($ss1)) {
                    if ($folder['parentid'] == $folder['link']) { ?>
                        app1000_folderCreate("<? echo $winId; ?>","<? echo $folder['id']; ?>","<? echo $folder['link']; ?>_<? echo $folder['linkid']; ?>","<? ajaxEcho($folder['name']); ?>",false);
                    <? } else { ?>
                        //Unterordner
                        app1000_folderCreate("<? echo $winId; ?>","<? echo $folder['id']; ?>","<? echo $folder['parentid']; ?>","<? ajaxEcho($folder['name']); ?>",false);
                    <? }
                }
                sql_close($ss1);
            }
        }
        if (!isEmpty($rootFolderId)) {
            $ss1 = sql_call("SELECT id,namedb FROM edomiProject.editRoot WHERE (id=" . $currentFolder[0] . " OR path LIKE '%/" . $currentFolder[0] . "/%') AND namedb<>''");
            while ($folder = sql_result($ss1)) {
                $ss2 = sql_call("SELECT * FROM edomiProject." . $folder['namedb'] . " WHERE (folderid=" . $folder['id'] . ") ORDER BY " . dbRoot_getOrdnerString($folder['id']));
                while ($item = sql_result($ss2)) {
                    if ($folder['namedb'] == 'editKo') {
                        $tmp2 = explode('(', $global_dpt[$item['valuetyp']]);
                        if (count($tmp2) > 0) {
                            $tmp = trim($tmp2[0]);
                        } else {
                            $tmp = $global_dpt[$item['valuetyp']];
                        }
                        if (!isEmpty($item['defaultvalue'])) {
                            $tmp .= ' &gt; <span style="color:#50b000;">' . ajaxEncode(substr($item['defaultvalue'], 0, 20)) . ((strlen($item['defaultvalue']) > 20) ? '...' : '') . '</span>';
                        }
                        if ($item['remanent'] == 1) {
                            $tmp .= ' / Remanent';
                        }
                        if ($item['initscan'] == 1) {
                            $tmp .= ' / InitScan';
                        }
                        if ($item['initsend'] == 1) {
                            $tmp .= ' / InitSend';
                        }
                        if ($item['endsend'] == 1) {
                            $tmp .= ' / EndSend';
                        }
                        if ($item['requestable'] >= 1) {
                            $tmp .= ' / Abfragbar';
                        }
                        if ($item['prio'] == 1) {
                            $tmp .= ' / Priorisiert';
                        }
                        if (!isEmpty($item['text'])) {
                            $tmp3 = '&nbsp;<span class="varItem">Notiz</span>';
                        } else {
                            $tmp3 = '';
                        } ?>
                        app1000_itemCreate("<? echo $winId; ?>","<? echo $item['id']; ?>","<? echo $folder['id']; ?>",'<? ajaxEcho($item['name']); ?> <span
                            class="idGa<? echo $item['gatyp']; ?>"><? ajaxEcho($item['ga']); ?></span> <span
                            class="varItem"><? echo $tmp; ?></span><? echo $tmp3; ?>');
                    <? } else if ($folder['namedb'] == 'editVisuFGcol' || $folder['namedb'] == 'editVisuBGcol') {
                        if (strpos($item['color'], '{') === false) { ?>
                            app1000_itemCreate("<? echo $winId; ?>","<? echo $item['id']; ?>","<? echo $folder['id']; ?>",'<span class="colorPreview1"
                                                                                                                                 style="background:<? ajaxEcho($item['color']); ?>;"></span> <? ajaxEcho($item['name']); ?>
                            <span class="id"><? echo $item['id']; ?></span>');
                        <? } else { ?>
                            app1000_itemCreate("<? echo $winId; ?>","<? echo $item['id']; ?>","<? echo $folder['id']; ?>",'<span
                                class="colorPreview2"></span> <? ajaxEcho($item['name']); ?> <span class="id"><? echo $item['id']; ?></span> <span
                                class="varItem" style="color:#ff0000;">dynamisch</span>');
                        <? }
                    } else if ($folder['namedb'] == 'editIp') {
                        if ($item['iptyp'] == 1) {
                            $tmp = 'HTTP-GET';
                        }
                        if ($item['iptyp'] == 2) {
                            $tmp = 'SHELL';
                        }
                        if ($item['iptyp'] == 3) {
                            $tmp = 'UDP';
                        } ?>
                        app1000_itemCreate("<? echo $winId; ?>","<? echo $item['id']; ?>","<? echo $folder['id']; ?>",'<? ajaxEcho($item['name']); ?> <span
                            class="id"><? echo $item['id']; ?></span> <span class="varItem"><? echo $tmp; ?></span>');
                    <? } else if ($folder['namedb'] == 'editLogicPage') {
                        if (!isEmpty($item['text'])) {
                            $tmp = '&nbsp;<span class="varItem">Notiz</span>';
                        } else {
                            $tmp = '';
                        }
                        if ($item['pagestatus'] == 1) { ?>
                            app1000_itemCreate("<? echo $winId; ?>","<? echo $item['id']; ?>","<? echo $folder['id']; ?>",'<? ajaxEcho($item['name']); ?> <span
                                class="id"><? echo $item['id']; ?></span><? echo $tmp; ?>');
                        <? } else { ?>
                            app1000_itemCreate("<? echo $winId; ?>","<? echo $item['id']; ?>","<? echo $folder['id']; ?>",'<span
                                style="color:#a0a0a0;"><? ajaxEcho($item['name']); ?></span> <span class="id"><? echo $item['id']; ?></span><? echo $tmp; ?>
                            <span style="color:#ff0000;">&gt; Deaktiviert</span>');
                        <? }
                    } else if ($folder['namedb'] == 'editVisuElementDesignDef') {
                        if ($item['styletyp'] == 0) { ?>
                            app1000_itemCreate("<? echo $winId; ?>","<? echo $item['id']; ?>","<? echo $folder['id']; ?>",'<? ajaxEcho($item['name']); ?> <span
                                class="id"><? echo $item['id']; ?></span>');
                        <? } else { ?>
                            app1000_itemCreate("<? echo $winId; ?>","<? echo $item['id']; ?>","<? echo $folder['id']; ?>",'<? ajaxEcho($item['name']); ?> <span
                                class="id"><? echo $item['id']; ?></span> <span class="varItem"
                                                                                style="color:#ff0000;"><? ajaxEcho($item['s1']); ?> &gt; <? ajaxEcho($item['s2']); ?></span>');
                        <? }
                    } else if ($folder['namedb'] == 'editVisu') { ?>
                        app1000_itemCreate("<? echo $winId; ?>","<? echo $item['id']; ?>","<? echo $folder['id']; ?>",'<? ajaxEcho($item['name']); ?> <span
                            class="id"><? echo $item['id']; ?></span> <span class="varItem"><? echo $item['xsize']; ?> x <? echo $item['ysize']; ?> px</span>');
                    <? } else if ($folder['namedb'] == 'editVisuPage') {
                        if ($folder['id'] == 22) {
                            $folder['id'] = '22_' . $item['visuid'];
                        }
                        if ($item['pagetyp'] == 1) { ?>
                            app1000_itemCreate("<? echo $winId; ?>","<? echo $item['id']; ?>","<? echo $folder['id']; ?>",'<? ajaxEcho($item['name']); ?> <span
                                class="id"><? echo $item['id']; ?></span> <span class="varItem">Popup: <? echo $item['xsize']; ?> x <? echo $item['ysize']; ?> px</span>');
                        <? } else if ($item['pagetyp'] == 2) { ?>
                            app1000_itemCreate("<? echo $winId; ?>","<? echo $item['id']; ?>","<? echo $folder['id']; ?>",'<? ajaxEcho($item['name']); ?> <span
                                class="id"><? echo $item['id']; ?></span> <span class="varItem" style="color:#ff0000;">globale Inkludeseite</span>');
                        <? } else { ?>
                            app1000_itemCreate("<? echo $winId; ?>","<? echo $item['id']; ?>","<? echo $folder['id']; ?>",'<? ajaxEcho($item['name']); ?> <span
                                class="id"><? echo $item['id']; ?></span>');
                        <? }
                    } else if ($folder['namedb'] == 'editVisuImg') {
                        if ($item['xsize'] == 0 && $item['ysize'] == 0) { ?>
                            app1000_itemCreate("<? echo $winId; ?>","<? echo $item['id']; ?>","<? echo $folder['id']; ?>",'<? ajaxEcho($item['name']); ?> <span
                                class="id"><? echo $item['id']; ?></span> <span class="varItem">Vektorgrafik</span>');
                        <? } else { ?>
                            app1000_itemCreate("<? echo $winId; ?>","<? echo $item['id']; ?>","<? echo $folder['id']; ?>",'<? ajaxEcho($item['name']); ?> <span
                                class="id"><? echo $item['id']; ?></span> <span
                                class="varItem"><? echo $item['xsize']; ?> x <? echo $item['ysize']; ?> px</span>');
                        <? }
                    } else if ($folder['namedb'] == 'editVisuUser') { ?>
                        app1000_itemCreate("<? echo $winId; ?>","<? echo $item['id']; ?>","<? echo $folder['id']; ?>",'<? ajaxEcho($item['login']); ?> <span
                            class="id"><? echo $item['id']; ?></span>');
                    <? } else if ($folder['namedb'] == 'editLogicElementDef') {
                        if ($item['errcount'] == 0) { ?>
                            app1000_itemCreate("<? echo $winId; ?>","<? echo $item['id']; ?>","<? echo $folder['id']; ?>",'<? ajaxValue($item['name']); ?> <span
                                class="id"><? echo $item['id']; ?></span> <? if ($item['exec'] == 1) {
                                echo '<span class="varItem">mit EXEC-Script</span>';
                            } ?>');
                        <? } else { ?>
                            app1000_itemCreate("<? echo $winId; ?>","<? echo $item['id']; ?>","<? echo $folder['id']; ?>",'<span
                                style="color:#ff0000;"><? ajaxValue($item['name']); ?></span> <span
                                class="id"><? echo $item['id']; ?></span> <? if ($item['exec'] == 1) {
                                echo '<span class="varItem">mit EXEC-Script</span>';
                            } ?> <span style="color:#ff0000;">&gt; <? echo $item['errcount']; ?> Fehler</span>');
                        <? }
                    } else if ($folder['namedb'] == 'editVisuElementDef') {
                        if ($item['errcount'] == 0) { ?>
                            app1000_itemCreate("<? echo $winId; ?>","<? echo $item['id']; ?>","<? echo $folder['id']; ?>",'<? ajaxValue($item['name']); ?> <span
                                class="id"><? echo $item['id']; ?></span>');
                        <? } else { ?>
                            app1000_itemCreate("<? echo $winId; ?>","<? echo $item['id']; ?>","<? echo $folder['id']; ?>",'<span
                                style="color:#ff0000;"><? ajaxValue($item['name']); ?></span> <span class="id"><? echo $item['id']; ?></span> <span
                                style="color:#ff0000;">&gt; <? echo $item['errcount']; ?> Fehler</span>');
                        <? }
                    } else if ($folder['namedb'] == 'editCam') {
                        if ($item['dvr'] == 0) { ?>
                            app1000_itemCreate("<? echo $winId; ?>","<? echo $item['id']; ?>","<? echo $folder['id']; ?>",'<? ajaxEcho($item['name']); ?> <span
                                class="id"><? echo $item['id']; ?></span>');
                        <? } else { ?>
                            app1000_itemCreate("<? echo $winId; ?>","<? echo $item['id']; ?>","<? echo $folder['id']; ?>",'<? ajaxEcho($item['name']); ?> <span
                                class="id"><? echo $item['id']; ?></span> <span class="varItem" style="color:#ff0000;">DVR aktiviert</span>');
                        <? }
                    } else { ?>
                        app1000_itemCreate("<? echo $winId; ?>","<? echo $item['id']; ?>","<? echo $folder['id']; ?>",'<? ajaxEcho($item['name']); ?> <span
                            class="id"><? echo $item['id']; ?></span>');
                    <? }
                }
                sql_close($ss2);
            }
            sql_close($ss1);
        }
        if ($cmd != 'initFolders') { ?>
            app1000_folderRestoreExpanded("<? echo $winId; ?>");
        <? } ?>
        app1000_itemShowCursor("<? echo $winId; ?>",1);
        app1000_refreshMarkedFolder("<? echo $winId; ?>");
        <? if (!isEmpty($rootFolderId)) { ?>
            app1000_rootFolderShowHint("<? echo $winId; ?>","<? ajaxEcho(app1000_getRootFolderInfo($rootFolderId)); ?>");
        <? }
    }
    if ($cmd == 'showUsedItems') {
        if ($phpdataArr[0] == 32) {
            $tmp = '';
            $ss1 = sql_call("SELECT id,name,ga FROM edomiProject.editKo WHERE gatyp=1 ORDER BY id ASC");
            while ($n = sql_result($ss1)) {
                if (!isEmpty(db_itemLinks('editKo', $n['id']))) {
                    $tmp .= $n['ga'] . ': ' . ajaxEncode($n['name']) . ' (' . $n['id'] . ')<br>';
                }
            }
            sql_close($ss1); ?>
            ajaxConfirm('Genutzte KNX-GAs:<br>
            <br><? echo $tmp; ?>','showUsedItems2','1000','<? echo $winId ?>','<? echo $data ?>','<? echo $phpdataArr[0]; ?><? echo AJAX_SEPARATOR1; ?><? ajaxValue(str_replace("'", '', $tmp)); ?>','Abbrechen','&gt; Merkliste');
        <? }
    }
    if ($cmd == 'showUsedItems2') { ?>
        pushDesktopNotes(1,"Genutzte KNX-GAs:<br><? ajaxValue($phpdataArr[1]); ?>");
    <? }
    if ($cmd == 'showUnusedItems') {
        if ($phpdataArr[0] == 32) {
            $tmp = '';
            $ss1 = sql_call("SELECT id,name,ga FROM edomiProject.editKo WHERE gatyp=1 ORDER BY id ASC");
            while ($n = sql_result($ss1)) {
                if (isEmpty(db_itemLinks('editKo', $n['id']))) {
                    $tmp .= $n['ga'] . ': ' . ajaxEncode($n['name']) . ' (' . $n['id'] . ')<br>';
                }
            }
            sql_close($ss1); ?>
            ajaxConfirm('Ungenutzte KNX-GAs:<br>
            <br><? echo $tmp; ?>','showUnusedItems2','1000','<? echo $winId ?>','<? echo $data ?>','<? echo $phpdataArr[0]; ?><? echo AJAX_SEPARATOR1; ?><? ajaxValue(str_replace("'", '', $tmp)); ?>','Abbrechen','&gt; Merkliste');
        <? }
    }
    if ($cmd == 'showUnusedItems2') { ?>
        pushDesktopNotes(1,"Ungenutzte KNX-GAs:<br><? ajaxValue($phpdataArr[1]); ?>");
    <? }
    if ($cmd == 'camPreview') { ?>
        document.getElementById("<? echo $winId; ?>-campreviewinfo").innerHTML="";
        document.getElementById("<? echo $winId; ?>-campreview").style.backgroundImage="none";
        <? $imgFn = getLiveCamImgPreview($phpdataArr[4], $phpdataArr[5]);
        if ($imgFn !== false) {
            $tmp = getimagesize(MAIN_PATH . '/www/data/tmp/' . $imgFn); ?>
            document.getElementById("<? echo $winId; ?>-campreviewinfo").innerHTML="<? echo $tmp[0]; ?> x <? echo $tmp[1]; ?> Pixel";
            document.getElementById("<? echo $winId; ?>-campreview").style.backgroundImage="url(../data/tmp/<? echo $imgFn; ?>?&<? echo date('YmdHis'); ?>)";
        <? }
    }
    if ($cmd == 'visuSndUploadOk') {
        $fileName = MAIN_PATH . '/www/data/tmp/' . $phpdataArr[0];
        if (file_exists($fileName)) {
            $fileSuffix = pathinfo($fileName, PATHINFO_EXTENSION);
            $fileSize = filesize($fileName); ?>
            if (document.getElementById("<? echo $winId; ?>-fd3").value=='') {
            document.getElementById("<? echo $winId; ?>-fd3").value="<? echo $phpdataArr[1]; ?>";
            }

            document.getElementById("<? echo $winId; ?>-fd10").value="1";

            document.getElementById("<? echo $winId; ?>-preview").pause();
            document.getElementById("<? echo $winId; ?>-preview").src="../data/tmp/<? echo $phpdataArr[0]; ?>";
            document.getElementById("<? echo $winId; ?>-preview").load();
        <? } else { ?>
            shakeObj("<? echo $winId; ?>");
        <? }
    }
    if ($cmd == 'visuFontUploadOk') {
        $fileName = MAIN_PATH . '/www/data/tmp/' . $phpdataArr[0];
        if (file_exists($fileName)) {
            $fileSuffix = pathinfo($fileName, PATHINFO_EXTENSION);
            $fileSize = filesize($fileName); ?>
            if (document.getElementById("<? echo $winId; ?>-fd3").value=='') {
            document.getElementById("<? echo $winId; ?>-fd3").value="<? echo $phpdataArr[1]; ?>";
            }

            document.getElementById("<? echo $winId; ?>-fd10").value="1";

            app1000_fontPreview("<? echo $winId; ?>");
        <? } else { ?>
            shakeObj("<? echo $winId; ?>");
        <? }
    }
    if ($cmd == 'visuImgUploadOk') {
        $fileName = MAIN_PATH . '/www/data/tmp/' . $phpdataArr[0];
        if (file_exists($fileName)) {
            $fileSuffix = pathinfo($fileName, PATHINFO_EXTENSION);
            $fileSize = filesize($fileName);
            $imgSize = getimagesize($fileName);
            if (!$imgSize && strToUpper($fileSuffix) == 'SVG') {
                $imgSize[0] = 0;
                $imgSize[1] = 0;
            } ?>
            if (document.getElementById("<? echo $winId; ?>-fd3").value=='') {
            document.getElementById("<? echo $winId; ?>-fd3").value="<? echo $phpdataArr[1]; ?>";
            }
            document.getElementById("<? echo $winId; ?>-fd4").value="<? echo $imgSize[0]; ?>";
            document.getElementById("<? echo $winId; ?>-fd5").value="<? echo $imgSize[1]; ?>";
            document.getElementById("<? echo $winId; ?>-fd6").value="<? echo $fileSuffix; ?>";
            document.getElementById("<? echo $winId; ?>-fd10").value="1";

            document.getElementById("<? echo $winId; ?>-preview").style.backgroundImage="url(../data/tmp/<? echo $phpdataArr[0]; ?>?<? echo date('YmdHis'); ?>)";
            <? if ($imgSize[0] == 0 && $imgSize[1] == 0) { ?>
                document.getElementById("<? echo $winId; ?>-imginfo").innerHTML="Vektorgrafik (<? echo strToUpper($fileSuffix); ?>)";
            <? } else { ?>
                document.getElementById("<? echo $winId; ?>-imginfo").innerHTML="<? echo $imgSize[0]; ?> x <? echo $imgSize[1]; ?> Pixel (<? echo strToUpper($fileSuffix); ?>)";
            <? }
        } else { ?>
            shakeObj("<? echo $winId; ?>");
        <? }
    }
    if ($cmd == 'visuImgMultiuploadOk') { ?>
        var n="";
        <? $sizeCount = 0;
        $errCount = 0;
        $okCount = 0;
        $fileData = explode('|', $phpdataArr[0], -1);
        for ($t = 0; $t < count($fileData); $t++) {
            $files = explode(';', $fileData[$t]);
            $tmp = explode('-', $files[1], 2);
            $folderid = $tmp[0];
            $tmp = explode('_', $files[1], 2);
            $titel = $tmp[1];
            $fileSuffix = pathinfo($files[1], PATHINFO_EXTENSION);
            if (isEmpty($files[0])) {
                $tmpArr = array(null, -1, $folderid, $titel, 0, 0, $fileSuffix);
                $id = db_itemSave('editVisuImg', $tmpArr);
                if ($id > 0) {
                    exec('mv "' . MAIN_PATH . '/www/data/tmp/' . $files[1] . '" "' . MAIN_PATH . '/www/data/project/visu/img/img-' . $id . '.' . $fileSuffix . '"');
                    $fileName = MAIN_PATH . '/www/data/project/visu/img/img-' . $id . '.' . $fileSuffix;
                    if (file_exists($fileName)) {
                        $fileSize = filesize($fileName);
                        $imgSize = getimagesize($fileName);
                        if (!$imgSize && strToUpper($fileSuffix) == 'SVG') {
                            $imgSize[0] = 0;
                            $imgSize[1] = 0;
                        }
                        sql_call("UPDATE edomiProject.editVisuImg SET xsize='" . $imgSize[0] . "',ysize='" . $imgSize[1] . "' WHERE (id=" . $id . ")");
                        $okCount++;
                        $sizeCount += $fileSize / 1024 / 1024;
                        if ($imgSize[0] == 0 && $imgSize[1] == 0) { ?>
                            n+="<? echo $titel; ?>: Vektorgrafik (<? echo strToUpper($fileSuffix); ?>) / <? echo round($fileSize / 1024, 2); ?> KB<br>";
                        <? } else { ?>
                            n+="<? echo $titel; ?>: <? echo $imgSize[0]; ?> x <? echo $imgSize[1]; ?> px (<? echo strToUpper($fileSuffix); ?>) / <? echo round($fileSize / 1024, 2); ?> KB
                            <br>";
                        <? }
                    } else {
                        $errCount++;
                        sql_call("DELETE FROM edomiProject.editVisuImg WHERE (id=" . $id . ")"); ?>
                        n+="<? echo $titel; ?>: Fehler beim Verschieben der Datei<br>";
                    <? }
                } else {
                    $errCount++;
                    deleteFiles(MAIN_PATH . '/www/data/tmp/' . $files[1]); ?>
                    n+="><? echo $titel; ?>: Fehler beim Hinzufügen in die Datenbank<br>";
                <? }
            } else {
                $errCount++; ?>
                n+="<? echo $titel; ?>: <? echo $files[0]; ?><br>";
            <? }
        } ?>
        jsConfirm("Mehrere Bilddateien hochgeladen (<? echo $okCount; ?> Bilddateien (<? echo round($sizeCount, 2); ?> MB / <? echo $errCount; ?> Fehler):<br>
        <br>"+n,"app1000_editCancel($<? echo $winId; ?>$);","none","Ok","auto");
    <? }
    if ($cmd == 'lbsRefresh') {
        $id = $phpdataArr[0];
        if (file_exists(MAIN_PATH . '/www/admin/lbs/' . $id . '_lbs.php')) {
            lbs_import($id);
        }
        $phpdataArr[1] = $id;
        editItem('editLogicElementDef');
    }
    if ($cmd == 'vseRefresh') {
        $id = $phpdataArr[0];
        if (file_exists(MAIN_PATH . '/www/admin/vse/' . $id . '_vse.php')) {
            vse_import($id);
        }
        $phpdataArr[1] = $id;
        editItem('editVisuElementDef');
    }
}

function parseOptions()
{
    global $dataArr;
    $options = array();
    $tmp = explode(';', $dataArr[3]);
    for ($t = 0; $t < count($tmp); $t++) {
        $tmp2 = explode('=', $tmp[$t]);
        if (!isEmpty(trim($tmp2[0]))) {
            $options[trim($tmp2[0])] = $tmp2[1];
        }
    }
    if (isEmpty($options['typ'])) {
        $options['typ'] = 0;
    }
    if (isEmpty($options['reset'])) {
        $options['reset'] = 1;
    }
    if (isEmpty($options['return'])) {
        $options['return'] = 1;
    }
    if (isEmpty($options['title'])) {
        if ($options['typ'] == 1 || $options['typ'] == 2 || $options['typ'] == 4 || $options['typ'] == 5 || $options['typ'] == 6) {
            $tmp = parseFolderId($dataArr[1]);
            $tmp2 = sql_getValue('edomiProject.editRoot', 'name', 'id=' . $tmp[0]);
            if ($options['typ'] == 6) {
                if (!isEmpty($tmp2)) {
                    $options['title'] = 'Bearbeiten: ' . $tmp2;
                }
            } else if ($options['typ'] == 5) {
                if (!isEmpty($tmp2)) {
                    $options['title'] = 'Konfiguration: ' . $tmp2;
                }
            } else {
                if (!isEmpty($tmp2)) {
                    $options['title'] = 'Auswahl: ' . $tmp2;
                }
            }
        }
        if ($options['typ'] == 3) {
            $options['title'] = 'Mehrfachauswahl von Elementen und Ordnern';
        }
        if (isEmpty($options['title'])) {
            $options['title'] = 'Konfiguration';
        }
    }
    return $options;
}

function parseFolderId($id)
{
    $r = explode('_', $id);
    if (!($r[0] > 0)) {
        $r[0] = 0;
    }
    if (!($r[1] > 0)) {
        $r[1] = false;
    }
    return $r;
}

function getItemMetaFromObjId($objId)
{
    $tmp = explode('-', $objId);
    if ($tmp[1] == 'i') {
        $folder = sql_getValues('edomiProject.editRoot', 'id,namedb', 'id=' . $tmp[2]);
        if ($folder !== false && $tmp[3] > 0) {
            return array($tmp[3], $folder['id'], $folder['namedb'], $tmp[0]);
        }
    }
    return false;
}

function getFolderMetaFromObjId($objId)
{
    $tmp = explode('-', $objId);
    if ($tmp[1] == 'f') {
        $folderMeta = parseFolderId($tmp[2]);
        $folder = sql_getValues('edomiProject.editRoot', 'id,parentid', 'id=' . $folderMeta[0]);
        if ($folder !== false) {
            return array($tmp[2], $folder['parentid'], $folder['id'], $tmp[0]);
        }
    }
    return false;
}

function app1000_searchItems($folderId, $linkId, $searchValue)
{
    $result = '';
    $db = sql_getValue('edomiProject.editRoot', 'namedb', 'id=' . $folderId);
    if (!isEmpty($db)) {
        $ss1 = false;
        $value = str_replace("%", "\%", trim($searchValue));
        if (substr($value, 0, 1) == '*') {
            $value = '%' . ltrim($value, '*');
        }
        $valueId = ((is_numeric($value)) ? $value : 0);
        $valueGa = str_replace(' ', '/', $value);
        if (!isEmpty(sql_getValue('edomiProject.editRoot', 'id', 'id<1000 AND parentid=' . $folderId))) {
            $checkRoot = 'b.rootid>=' . $folderId;
        } else {
            $checkRoot = 'b.rootid=' . $folderId;
        }
        if ($db == 'editVisuUser') {
            $ss1 = sql_call("SELECT a.id,a.folderid FROM edomiProject." . $db . " AS a,edomiProject.editRoot AS b WHERE (a.folderid=b.id AND " . $checkRoot . ") AND (a.id='" . sql_encodeValue($valueId) . "' OR a.login LIKE '" . sql_encodeValue($value) . "%')");
        } else if ($db == 'editVisuPage' && $linkId > 0) {
            $ss1 = sql_call("SELECT a.id,a.folderid FROM edomiProject." . $db . " AS a,edomiProject.editRoot AS b WHERE (a.folderid=b.id AND " . $checkRoot . ") AND a.visuid='" . $linkId . "' AND (a.id='" . sql_encodeValue($valueId) . "' OR a.name LIKE '" . sql_encodeValue($value) . "%')");
        } else if ($db == 'editKo') {
            $ss1 = sql_call("SELECT a.id,a.folderid FROM edomiProject." . $db . " AS a,edomiProject.editRoot AS b WHERE (a.folderid=b.id AND " . $checkRoot . ") AND (a.ga='" . sql_encodeValue($valueGa) . "' OR a.id='" . sql_encodeValue($valueId) . "' OR a.name LIKE '" . sql_encodeValue($value) . "%')");
        } else {
            $ss1 = sql_call("SELECT a.id,a.folderid FROM edomiProject." . $db . " AS a,edomiProject.editRoot AS b WHERE (a.folderid=b.id AND " . $checkRoot . ") AND (a.id='" . sql_encodeValue($valueId) . "' OR a.name LIKE '" . sql_encodeValue($value) . "%')");
        }
        if ($ss1 !== false) {
            while ($n = sql_result($ss1)) {
                $result .= $n['folderid'] . '-' . $n['id'] . ';';
            }
        }
        sql_close($ss1);
    }
    return $result;
}

function app1000_getRootFolderInfo($folderIdFull)
{
    $n = '';
    $folderId = parseFolderId($folderIdFull);
    $folder = sql_getValues('edomiProject.editRoot', 'id,allow', 'id=' . $folderId[0]);
    if ($folder !== false) {
        if ($folderId[0] == 22 && !($folderId[1] > 0)) {
            $n = 'Inhalte können erst nach dem Erstellen einer Visualisierung hinzugefügt werden.';
        } else {
            if ($folder['allow'] & 2 || $folder['allow'] & 4) {
                $n = 'Inhalte können ggf. per Rechtsklick auf den Ordnernamen oder der Schaltfläche [Neu] hinzugefügt werden.';
            } else {
                $n = 'Das Hinzufügen von Inhalten ist in diesem Systemordner nicht möglich.';
            }
        }
    }
    return $n;
}

function app1000_parseSelectedElements($jsElements, $targetFolderId = 0)
{
    $rItems = array();
    $rFolder = array();
    $elements = explode(';', $jsElements, -1);
    if (!(count($elements) > 0)) {
        return false;
    }
    $rootid = null;
    foreach ($elements as $element) {
        $tmp = null;
        $item = getItemMetaFromObjId($element);
        if ($item !== false) {
            $tmp = sql_getValues('edomiProject.editRoot', 'id,rootid,path', 'id=' . $item[1]);
            if ($tmp === false) {
                return false;
            }
            $rItems[] = array(intval($item[0]), $tmp['path'] . $tmp['id'] . '/');
        } else {
            $folder = getFolderMetaFromObjId($element);
            if ($folder !== false) {
                $tmp = sql_getValues('edomiProject.editRoot', 'rootid,path', 'id=' . $folder[0]);
                if ($tmp === false) {
                    return false;
                }
                $rFolder[] = array(intval($folder[2]), $tmp['path']);
            }
        }
        if (!isEmpty($tmp['rootid'])) {
            if (isEmpty($rootid)) {
                $rootid = $tmp['rootid'];
            } else if ($rootid != $tmp['rootid']) {
                return false;
            }
        } else {
            return false;
        }
    }
    if (isEmpty($rootid)) {
        return false;
    }
    if (count($rItems) == 0 && count($rFolder) == 0) {
        return false;
    }
    for ($t = 0; $t < count($rFolder); $t++) {
        if ($rFolder[$t][0] > 0) {
            for ($tt = 0; $tt < count($rItems); $tt++) {
                if ($rItems[$tt][0] > 0 && strpos($rItems[$tt][1], '/' . $rFolder[$t][0] . '/') !== false) {
                    $rItems[$tt][0] = 0;
                    $rItems[$tt][1] = null;
                }
            }
        }
    }
    for ($t = 0; $t < count($rFolder); $t++) {
        if ($rFolder[$t][0] > 0) {
            for ($tt = 0; $tt < count($rFolder); $tt++) {
                if ($rFolder[$tt][0] > 0 && strpos($rFolder[$tt][1], '/' . $rFolder[$t][0] . '/') !== false) {
                    $rFolder[$tt][0] = 0;
                    $rFolder[$tt][1] = null;
                }
            }
        }
    }
    if ($targetFolderId > 0) {
        if (isEmpty(sql_getValue('edomiProject.editRoot', 'id', 'id=' . $targetFolderId . ' AND rootid=' . $rootid))) {
            return false;
        }
        for ($t = 0; $t < count($rFolder); $t++) {
            if ($rFolder[$t][0] > 0) {
                if ($targetFolderId == $rFolder[$t][0]) {
                    return false;
                }
                $tmp = sql_getValue('edomiProject.editRoot', 'id', "id=" . $targetFolderId . " AND path LIKE '" . $rFolder[$t][1] . $rFolder[$t][0] . "/%' LIMIT 0,1");
                if (!isEmpty($tmp)) {
                    return false;
                }
            }
        }
    }
    $db = sql_getValue('edomiProject.editRoot', 'namedb', 'id=' . $rootid);
    if (isEmpty($db)) {
        return false;
    }
    $tmp1 = array();
    foreach ($rFolder as $n) {
        if ($n[0] > 0) {
            $tmp1[] = $n[0];
        }
    }
    sort($tmp1);
    $tmp2 = array();
    foreach ($rItems as $n) {
        if ($n[0] > 0) {
            $tmp2[] = $n[0];
        }
    }
    sort($tmp2);
    if (count($tmp1) == 0 && count($tmp2) == 0) {
        return false;
    } else {
        $tmp = array($rootid, $db, $tmp1, $tmp2);
        return $tmp;
    }
}

function app1000_contextMenuSelection()
{
    global $appId, $winId, $data, $dataArr, $phpdata, $phpdataArr, $sid;
    $targetFolder = parseFolderId($phpdataArr[1]);
    $targetFolderAllow = dbRoot_getAllow($phpdataArr[1]);
    if ($options['typ'] != 4 && ((!($targetFolderAllow & 1) && ($targetFolderAllow & 16 || $targetFolderAllow & 32 || $targetFolderAllow & 64)) || ($targetFolder[1] !== false && $targetFolderAllow & 1 && ($targetFolderAllow & 16 || $targetFolderAllow & 32 || $targetFolderAllow & 64)))) { ?>
        apps_contextMenu.addHr();
        apps_contextMenu.addItem("Alles in diesem Ordner auswählen","app1000_elementSelectAllInFolder('<? echo $winId; ?>','<? echo $winId; ?>-f-<? echo $phpdataArr[3]; ?>');");
        <? if ($phpdataArr[4] > 0 && $phpdataArr[2] == $phpdataArr[1]) { ?>
            apps_contextMenu.addItem("Auswahl in diesem Ordner aufheben","app1000_elementSelectNoneInFolder('<? echo $winId; ?>','<? echo $winId; ?>-f-<? echo $phpdataArr[3]; ?>');");
            apps_contextMenu.addItem("Auswahl aufheben","app1000_elementSelectNone('<? echo $winId; ?>');");
            apps_contextMenu.addHr();
            apps_contextMenu.addText("Ausgewählte Elemente/Ordner (<? echo $phpdataArr[4]; ?>):");
            <? if ($targetFolderAllow & 16 && dbRoot_getAllow($phpdataArr[1]) & 32) { ?>
                apps_contextMenu.addItem("&gt; In diesen Ordner duplizieren","app1000_elementPasteSelected('<? echo $winId; ?>','<? echo $phpdataArr[3]; ?>',0);");
            <? }
            if ($targetFolderAllow & 16) { ?>
                apps_contextMenu.addItem("&gt; In diesen Ordner verschieben","app1000_elementPasteSelected('<? echo $winId; ?>','<? echo $phpdataArr[3]; ?>',1);");
            <? }
            if (dbRoot_getAllow($phpdataArr[2]) & 32) { ?>
                apps_contextMenu.addItem("&gt; Duplizieren","app1000_elementDuplicateSelected('<? echo $winId; ?>');");
            <? }
            if (dbRoot_getAllow($phpdataArr[2]) & 64) { ?>
                apps_contextMenu.addItem("&gt; Löschen","app1000_elementDeleteSelected('<? echo $winId; ?>');");
            <? }
        }
    }
}

function editItem($db, $linkId = 0)
{
    global $appId, $winId, $data, $dataArr, $phpdata, $phpdataArr, $sid;
    global $global_dpt; ?>
    var n="";
    var app1000_edittitle="";
    var app1000_edithelp="";
    var app1000_editpath="";

    if (document.getElementById("<? echo $winId; ?>-edit").dataset.drag=="1") {
    n+="
    <div class='appTitelDrag' onMouseDown='dragWindowStart(\"<? echo $appId; ?>\",\"<? echo $winId; ?>-global\");'><span
            id='<? echo $winId; ?>-edittitle'></span>
        <div class='cmdClose cmdCloseDisabled'></div>
        <div id='<? echo $winId; ?>-edithelp' class='cmdHelp' data-helpid='<? echo $appId; ?>' onClick='openWindow(9999,this.dataset.helpid);'></div>
    </div>";
    } else {
    n+="
    <div class='appTitel'><span id='<? echo $winId; ?>-edittitle'></span>
        <div class='cmdClose cmdCloseDisabled'></div>
        <div id='<? echo $winId; ?>-edithelp' class='cmdHelp' data-helpid='<? echo $appId; ?>' onClick='openWindow(9999,this.dataset.helpid);'></div>
    </div>";
    }
    <? if ($db == 'editLogicElementDef') { ?>
    n+="
    <div class='appMenu'>";
        <? $ss1 = sql_call("SELECT * FROM edomiProject.editLogicElementDef WHERE (id=" . $phpdataArr[1] . ")");
        if ($n = sql_result($ss1)) {
            $fd[0] = $db;
            $fd[1] = $n['id'];
            $fd[2] = $n['folderid'];
            $fd[4] = substr($n['id'], 2, 6);
            $fd[10] = $n['name'];
            $fd[11] = $n['errcount'];
            $fd[12] = $n['errmsg'];
            $fd[13] = $n['exec'];
            $fd[14] = $n['defin'];
            $fd[15] = $n['defout'];
            $fd[16] = $n['defvar']; ?>
            n+="
            <div class='cmdButton cmdButtonL cmdButtonDisabled'>Abbrechen</div>";
                                                                               n+="
            <div class='cmdButton cmdButtonR'
                 onClick='ajax(\"saveItem\",\"<? echo $appId; ?>\",\"<? echo $winId; ?>\",\"<? echo $data; ?>\",controlGetFormData(\"<? echo $winId; ?>-form1\"));'>
                <b>Übernehmen</b></div>";
                                       n+="
            <div class='cmdButton' onClick='ajax(\"lbsRefresh\",\"<? echo $appId; ?>\",\"<? echo $winId; ?>\",\"<? echo $data; ?>\",\"<? echo $fd[1]; ?>\");'
                 style='float:right; margin-left:5px;'>LBS-Datei neu einlesen
            </div>";
            <? if (dbRoot_getRootId($fd[2]) == 19) { ?>
                n+="
                <div class='cmdButton'
                     onClick='openWindow(1018,\"<? echo $data; ?>\",\"<? echo $winId; ?><? echo AJAX_SEPARATOR1; ?><? echo $fd[1]; ?><? echo AJAX_SEPARATOR1; ?><? echo $fd[2]; ?>\");'
                     style='float:right;'>Quelltext bearbeiten
                </div>";
            <? } else { ?>
                n+="
                <div class='cmdButton'
                     onClick='openWindow(1018,\"<? echo $data; ?>\",\"<? echo $winId; ?><? echo AJAX_SEPARATOR1; ?><? echo $fd[1]; ?><? echo AJAX_SEPARATOR1; ?><? echo $fd[2]; ?>\");'
                     style='float:right;'>Quelltext einsehen
                </div>";
            <? }
        } else {
            $fd[0] = $db;
            $fd[1] = -1;
            $fd[2] = $phpdataArr[0];
            $fd[4] = ''; ?>
            n+="
            <div class='cmdButton cmdButtonL' onClick='app1000_editCancel(\"<? echo $winId; ?>\");'>Abbrechen</div>";
                                                                                                                   n+="
            <div class='cmdButton cmdButtonR'
                 onClick='ajax(\"saveItem\",\"<? echo $appId; ?>\",\"<? echo $winId; ?>\",\"<? echo $data; ?>\",controlGetFormData(\"<? echo $winId; ?>-form1\"));'>
                <b>Logikbaustein erstellen</b></div>";
        <? }
        editItem_setMeta('Logikbaustein', $fd[1], $fd[2], '12'); ?>
        n+="
    </div>";
    n+="
    <div id='<? echo $winId; ?>-form1' class='appContentBlank'>";
        n+="<input type='hidden' id='<? echo $winId; ?>-fd0' data-type='1' value='<? echo $fd[0]; ?>'></input>";
        n+="<input type='hidden' id='<? echo $winId; ?>-fd1' data-type='1' value='<? echo $fd[1]; ?>'></input>";
        n+="<input type='hidden' id='<? echo $winId; ?>-fd2' data-type='1' value='<? echo $fd[2]; ?>'></input>";
        n+="
        <table width='100%' border='0' cellpadding='5' cellspacing='0' style='table-layout:auto;'>";
            n+="
            <tr>
                <td colspan='2'>"+app1000_editpath+"</td>
            </tr>
            ";
            <? if ($fd[1] > 0) { ?>
                n+="<input type='hidden' id='<? echo $winId; ?>-fd4' data-type='1' value='<? echo $fd[4]; ?>'></input>";
                <? if (file_exists(MAIN_PATH . '/www/admin/lbs/' . $fd[1] . '_lbs.php')) { ?>
                    n+="
                    <tr>";
                        n+="
                        <td>Name<br>
                            <div id='<? echo $winId; ?>-info1' class='controlEditInline' style='box-sizing:border-box; width:100%; background:#ffffff;'></div>
                        </td>
                        ";
                        n+="
                    </tr>";
                         n+="
                    <tr>";
                        n+="
                        <td>Eigenschaften<br>
                            <div class='controlEditInline' style='box-sizing:border-box; width:100%; background:#ffffff;'><? echo $fd[14]; ?>
                                Eingänge, <? echo $fd[15]; ?> Ausgänge, <? echo $fd[16]; ?> Variablen<? echo(($fd[13] == 1) ? ', mit EXEC-Script' : '') ?></div>
                        </td>
                        ";
                        n+="
                    </tr>";
                         n+="
                    <tr>";
                        n+="
                        <td>Instanzen<br>
                            <div class='controlEditInline'
                                 style='box-sizing:border-box; width:100%; background:#ffffff;'><? echo sql_getCount('edomiProject.editLogicElement', 'functionid=' . $fd[1]); ?>
                                Instanzen insgesamt
                            </div>
                        </td>
                        ";
                        n+="
                    </tr>";
                    <? if ($fd[11] > 0) {
                        $tmp = explode("\n", $fd[12], -1);
                        for ($t = 0; $t < count($tmp); $t++) {
                            $tmp[$t] = '&bull; ' . $tmp[$t];
                        }
                        $tmp2 = implode('<br>', $tmp); ?>
                        n+="
                        <tr>";
                            n+="
                            <td>
                                <div class='controlEditInline'
                                     style='box-sizing:border-box; width:100%; max-height:250px; line-height:1.5; background:#ffffff; border:1px solid #ff0000;'>
                                    <span
                                        style='color:#ff0000;'><b>Dieser Logikbaustein enthält <? echo $fd[11]; ?> Fehler:</b></span><br><? ajaxValue($tmp2); ?>
                                </div>
                            </td>
                            ";
                            n+="
                        </tr>";
                    <? }
                } else { ?>
                    n+="
                    <tr>";
                        n+="
                        <td>
                            <div class='controlEditInline'
                                 style='box-sizing:border-box; width:100%; max-height:250px; line-height:1.5; background:#ffffff; border:1px solid #ff0000;'>
                                <span style='color:#ff0000;'><b>Es existiert keine Logikbaustein-Datei für diesen Logikbaustein.</b></span></div>
                        </td>
                        ";
                        n+="
                    </tr>";
                <? }
            } else { ?>
                n+="
                <tr>";
                    n+="
                    <td>Logikbaustein-ID<br>
                        <div class='idBig'
                             style='display:inline-block; height:20; padding:2px; margin:1px 0px 5px 0px; border-top-right-radius:0; border-bottom-right-radius:0;'>
                            19
                        </div>
                        <input type='text' id='<? echo $winId; ?>-fd4' data-type='1' value='<? echo $fd[4]; ?>' maxlength='6' class='control1' autofocus
                               style='width:100px; border-radius:0 10px 10px 0;'></input></td>
                    ";
                    n+="
                </tr>";
                     n+="
                <tr>";
                    n+="
                    <td>Vorlage<br>
                        <div id='<? echo $winId; ?>-fd5' data-type='6' data-value='1' data-list='1|Standard-LBS;2|LBS mit EXEC-Script;3|Dämon-LBS;'
                             class='control6' style='width:100%;'>&nbsp;
                        </div>
                    </td>
                    ";
                    n+="
                </tr>";
            <? } ?>
            n+="
            <tr>
                <td>&nbsp;</td>
            </tr>
            ";
            n+="
            <tr>";
                n+="
                <td>
                    <div style='height:380px; padding:5px; overflow:auto;'>
                        <div id='<? echo $winId; ?>-lbspreview' style='display:inline-block; width:auto;'></div>
                    </div>
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
    document.getElementById("<? echo $winId; ?>-edit").innerHTML=n;
    document.getElementById("<? echo $winId; ?>-lbspreview").innerHTML='<? ajaxValue(lbs_preview($fd[1])); ?>';
    if (document.getElementById("<? echo $winId; ?>-info1")) {document.getElementById("<? echo $winId; ?>-info1").innerHTML='<? ajaxValue($fd[10]); ?>';}
<? }
    if ($db == 'editVisuElementDef') { ?>
        n+="
        <div class='appMenu'>";
            <? $ss1 = sql_call("SELECT * FROM edomiProject.editVisuElementDef WHERE (id=" . $phpdataArr[1] . ")");
            if ($n = sql_result($ss1)) {
                $fd[0] = $db;
                $fd[1] = $n['id'];
                $fd[2] = $n['folderid'];
                $fd[4] = $n['id'];
                $fd[10] = $n['name'];
                $fd[11] = $n['errcount'];
                $fd[12] = $n['errmsg']; ?>
                n+="
                <div class='cmdButton cmdButtonL cmdButtonDisabled'>Abbrechen</div>";
                                                                                   n+="
                <div class='cmdButton cmdButtonR'
                     onClick='ajax(\"saveItem\",\"<? echo $appId; ?>\",\"<? echo $winId; ?>\",\"<? echo $data; ?>\",controlGetFormData(\"<? echo $winId; ?>-form1\"));'>
                    <b>Übernehmen</b></div>";
                                           n+="
                <div class='cmdButton'
                     onClick='ajax(\"vseRefresh\",\"<? echo $appId; ?>\",\"<? echo $winId; ?>\",\"<? echo $data; ?>\",\"<? echo $fd[1]; ?>\");'
                     style='float:right; margin-left:5px;'>VSE-Datei neu einlesen
                </div>";
                <? if (dbRoot_getRootId($fd[2]) == 170) { ?>
                    n+="
                    <div class='cmdButton'
                         onClick='openWindow(1016,\"<? echo $data; ?>\",\"<? echo $winId; ?><? echo AJAX_SEPARATOR1; ?><? echo $fd[1]; ?><? echo AJAX_SEPARATOR1; ?><? echo $fd[2]; ?>\");'
                         style='float:right;'>Quelltext bearbeiten
                    </div>";
                <? } else { ?>
                    n+="
                    <div class='cmdButton'
                         onClick='openWindow(1016,\"<? echo $data; ?>\",\"<? echo $winId; ?><? echo AJAX_SEPARATOR1; ?><? echo $fd[1]; ?><? echo AJAX_SEPARATOR1; ?><? echo $fd[2]; ?>\");'
                         style='float:right;'>Quelltext einsehen
                    </div>";
                <? }
            } else {
                $fd[0] = $db;
                $fd[1] = -1;
                $fd[2] = $phpdataArr[0];
                $fd[4] = ''; ?>
                n+="
                <div class='cmdButton cmdButtonL' onClick='app1000_editCancel(\"<? echo $winId; ?>\");'>Abbrechen</div>";
                                                                                                                       n+="
                <div class='cmdButton cmdButtonR'
                     onClick='ajax(\"saveItem\",\"<? echo $appId; ?>\",\"<? echo $winId; ?>\",\"<? echo $data; ?>\",controlGetFormData(\"<? echo $winId; ?>-form1\"));'>
                    <b>Visuelement erstellen</b></div>";
            <? }
            editItem_setMeta('Visuelement', $fd[1], $fd[2], '160'); ?>
            n+="
        </div>";
        n+="
        <div id='<? echo $winId; ?>-form1' class='appContentBlank'>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd0' data-type='1' value='<? echo $fd[0]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd1' data-type='1' value='<? echo $fd[1]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd2' data-type='1' value='<? echo $fd[2]; ?>'></input>";
            n+="
            <table width='100%' border='0' cellpadding='5' cellspacing='0' style='table-layout:auto;'>";
                n+="
                <tr>
                    <td colspan='2'>"+app1000_editpath+"</td>
                </tr>
                ";
                <? if ($fd[1] > 0) { ?>
                    n+="<input type='hidden' id='<? echo $winId; ?>-fd4' data-type='1' value='<? echo $fd[4]; ?>'></input>";
                    <? if (file_exists(MAIN_PATH . '/www/admin/vse/' . $fd[1] . '_vse.php')) { ?>
                        n+="
                        <tr>";
                            n+="
                            <td>Name<br>
                                <div id='<? echo $winId; ?>-info1' class='controlEditInline'
                                     style='box-sizing:border-box; width:100%; background:#ffffff;'></div>
                            </td>
                            ";
                            n+="
                        </tr>";
                             n+="
                        <tr>";
                            n+="
                            <td>Instanzen<br>
                                <div class='controlEditInline'
                                     style='box-sizing:border-box; width:100%; background:#ffffff;'><? echo sql_getCount('edomiProject.editVisuElement', 'controltyp=' . $fd[1]); ?>
                                    Instanzen insgesamt
                                </div>
                            </td>
                            ";
                            n+="
                        </tr>";
                        <? if ($fd[11] > 0) {
                            $tmp = explode("\n", $fd[12], -1);
                            for ($t = 0; $t < count($tmp); $t++) {
                                $tmp[$t] = '&bull; ' . $tmp[$t];
                            }
                            $tmp2 = implode('<br>', $tmp); ?>
                            n+="
                            <tr>";
                                n+="
                                <td>
                                    <div class='controlEditInline'
                                         style='box-sizing:border-box; width:100%; max-height:250px; line-height:1.5; background:#ffffff; border:1px solid #ff0000;'>
                                        <span
                                            style='color:#ff0000;'><b>Dieses Visuelement enthält <? echo $fd[11]; ?> Fehler:</b></span><br><? ajaxValue($tmp2); ?>
                                    </div>
                                </td>
                                ";
                                n+="
                            </tr>";
                        <? }
                    } else { ?>
                        n+="
                        <tr>";
                            n+="
                            <td>
                                <div class='controlEditInline'
                                     style='box-sizing:border-box; width:100%; max-height:250px; line-height:1.5; background:#ffffff; border:1px solid #ff0000;'>
                                    <span style='color:#ff0000;'><b>Es existiert keine Visuelement-Datei für dieses Visuelement.</b></span></div>
                            </td>
                            ";
                            n+="
                        </tr>";
                    <? }
                } else { ?>
                    n+="
                    <tr>";
                        n+="
                        <td>Visuelement-ID<br><input type='text' id='<? echo $winId; ?>-fd4' data-type='1' value='<? echo $fd[4]; ?>' maxlength='8'
                                                     class='control1' autofocus style='width:100px;'></input></td>
                        ";
                        n+="
                    </tr>";
                         n+="
                    <tr>";
                        n+="
                        <td>Vorlage<br>
                            <div id='<? echo $winId; ?>-fd5' data-type='6' data-value='1' data-list='1|Standard-VSE;' class='control6' style='width:100%;'>
                                &nbsp;
                            </div>
                        </td>
                        ";
                        n+="
                    </tr>";
                <? } ?>
                n+="
            </table>
            ";
            n+="
        </div>";
        document.getElementById("<? echo $winId; ?>-edit").innerHTML=n;

        if (document.getElementById("<? echo $winId; ?>-info1")) {document.getElementById("<? echo $winId; ?>-info1").innerHTML='<? ajaxValue($fd[10]); ?>';}
    <? }
    if ($db == 'editVisuUser') {
        $ss1 = sql_call("SELECT * FROM edomiProject.editVisuUser WHERE (id=" . $phpdataArr[1] . ")");
        if ($n = sql_result($ss1)) {
            $fd[0] = $db;
            $fd[1] = $n['id'];
            $fd[2] = $n['folderid'];
            $fd[3] = $n['login'];
            $fd[4] = $n['pass'];
            $fd[9] = $n['touch'];
            $fd[10] = $n['touchscroll'];
            $fd[11] = $n['gaid'];
            $fd[12] = $n['autologout'];
            $fd[13] = $n['gaid2'];
            $fd[14] = $n['gaid3'];
            $fd[15] = $n['click'];
            $fd[16] = $n['noacksounds'];
            $fd[17] = $n['noerrors'];
            $fd[18] = $n['preload'];
            $fd[19] = $n['longclick'];
        } else {
            $fd[0] = $db;
            $fd[1] = -1;
            $fd[2] = $phpdataArr[0];
            $fd[3] = '';
            $fd[4] = '';
            $fd[9] = 0;
            $fd[10] = 2;
            $fd[11] = 0;
            $fd[12] = 0;
            $fd[13] = 0;
            $fd[14] = 0;
            $fd[15] = 0;
            $fd[16] = 0;
            $fd[17] = 0;
            $fd[18] = 1;
            $fd[19] = 0;
        }
        editItem_setMeta('Visuaccount', $fd[1], $fd[2], '23'); ?>
        n+="
        <div class='appMenu'>";
            n+="
            <div class='cmdButton cmdButtonL' onClick='app1000_editCancel(\"<? echo $winId; ?>\");'>Abbrechen</div>
            ";
            n+="
            <div class='cmdButton cmdButtonR'
                 onClick='ajax(\"saveItem\",\"<? echo $appId; ?>\",\"<? echo $winId; ?>\",\"<? echo $data; ?>\",controlGetFormData(\"<? echo $winId; ?>-form1\"));'>
                <b>Übernehmen</b></div>
            ";
            n+="
        </div>";
        n+="
        <div id='<? echo $winId; ?>-form1' class='appContentBlank'>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd0' data-type='1' value='<? echo $fd[0]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd1' data-type='1' value='<? echo $fd[1]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd2' data-type='1' value='<? echo $fd[2]; ?>'></input>";
            n+="
            <table width='100%' border='0' cellpadding='5' cellspacing='0'>";
                n+="
                <colgroup>";
                    n+="
                    <col width='25%'>
                    ";
                    n+="
                    <col width='25%'>
                    ";
                    n+="
                    <col width='25%'>
                    ";
                    n+="
                    <col width='25%'>
                    ";
                    n+="
                </colgroup>
                ";
                n+="
                <tr>
                    <td colspan='4'>"+app1000_editpath+"</td>
                </tr>
                ";
                n+="
                <tr>";
                    n+="
                    <td colspan='3'>Login/Gerät<br><input type='text' id='<? echo $winId; ?>-fd3' data-type='1' value='' class='control1' autofocus
                                                          style='width:100%;'></input></td>
                    ";
                    n+="
                    <td style='border-left:1px solid #c0c0c0;'>Eingabegerät<br>
                        <div id='<? echo $winId; ?>-fd9' data-type='6' data-value='<? echo $fd[9]; ?>'
                             data-list='0|automatisch;3|automatisch (ggf. ohne Mauspfeil);1|Maus-Bedienung;2|Touch-Bedienung;4|Touch-Bedienung (ohne Mauspfeil);'
                             class='control6' style='width:100%;'>&nbsp;
                        </div>
                    </td>
                    ";
                    n+="
                </tr>
                ";
                n+="
                <tr>";
                    n+="
                    <td colspan='3'>Passwort<br><input type='text' id='<? echo $winId; ?>-fd4' data-type='1' value='' class='control1'
                                                       style='width:100%;'></input></td>
                    ";
                    n+="
                    <td style='border-left:1px solid #c0c0c0;'>Klickmodus<br>
                        <div id='<? echo $winId; ?>-fd15' data-type='6' data-value='<? echo $fd[15]; ?>' data-list='0|beim Betätigen;1|beim Loslassen;'
                             class='control6' style='width:100%;'>&nbsp;
                        </div>
                    </td>
                    ";
                    n+="
                </tr>
                ";
                n+="
                <tr>";
                    n+="
                    <td colspan='3'>&nbsp;</td>
                    ";
                    n+="
                    <td style='border-left:1px solid #c0c0c0;'>Scrollen<br>
                        <div id='<? echo $winId; ?>-fd10' data-type='6' data-value='<? echo $fd[10]; ?>'
                             data-list='0|deaktiviert;1|aktiviert;2|nur bei Maus-Bedienung;3|nur bei Touch-Bedienung;' class='control6' style='width:100%;'>
                            &nbsp;
                        </div>
                    </td>
                    ";
                    n+="
                </tr>
                ";
                n+="
                <tr>";
                    n+="
                    <td colspan='3'>&nbsp;</td>
                    ";
                    n+="
                    <td style='border-left:1px solid #c0c0c0;'>Größe der Langklick-Animation<br>
                        <div id='<? echo $winId; ?>-fd19' data-type='6' data-value='<? echo $fd[19]; ?>'
                             data-list='-50|-50%;-40|-40%;-30|-30%;-20|-20%;-10|-10%;0|0% (wie das Visuelement);25|25%;50|50%;75|75%;100|100%;150|150%;'
                             class='control6' style='width:100%;'>&nbsp;
                        </div>
                    </td>
                    ";
                    n+="
                </tr>
                ";
                n+="
                <tr>
                    <td colspan='4' class='formSubTitel'>Weitere Optionen
                        <hr>
                    </td>
                </tr>
                ";
                n+="
                <tr>";
                    n+="
                    <td>Automatisch ausloggen<br>
                        <div id='<? echo $winId; ?>-fd12' data-type='6' data-value='<? echo $fd[12]; ?>'
                             data-list='0|deaktiviert;1|aktiviert (nach ausbleibender Rückmeldung);' class='control6' style='width:100%;'>&nbsp;
                        </div>
                    </td>
                    ";
                    n+="
                    <td>Bilddateien<br>
                        <div id='<? echo $winId; ?>-fd18' data-type='6' data-value='<? echo $fd[18]; ?>'
                             data-list='0|Bilder erst bei Verwendung laden;1|alle Bilder beim Start laden;' class='control6' style='width:100%;'>&nbsp;
                        </div>
                    </td>
                    ";
                    n+="
                    <td>Fehler/Warnungen<br>
                        <div id='<? echo $winId; ?>-fd17' data-type='6' data-value='<? echo $fd[17]; ?>' data-list='0|anzeigen;1|unterdrücken;' class='control6'
                             style='width:100%;'>&nbsp;
                        </div>
                    </td>
                    ";
                    n+="
                    <td>Hinweis \"Tonausgabe aktivieren\"<br>
                        <div id='<? echo $winId; ?>-fd16' data-type='6' data-value='<? echo $fd[16]; ?>' data-list='0|anzeigen;1|unterdrücken;' class='control6'
                             style='width:100%;'>&nbsp;
                        </div>
                    </td>
                    ";
                    n+="
                <tr>
                    <td colspan='4' class='formSubTitel'>Kommunikationsobjekte
                        <hr>
                    </td>
                </tr>
                ";
                n+="
                <tr>";
                    n+="
                    <td colspan='4'>KO: Status (0=offline, 1..&infin;=online (VisuID))<br>
                        <div id='<? echo $winId; ?>-fd11' data-type='1000' data-root='30' data-value='<? echo $fd[11]; ?>' data-options='typ=1'
                             class='control10' style='width:100%;'>&nbsp;
                        </div>
                    </td>
                    ";
                    n+="
                </tr>
                ";
                n+="
                <tr>";
                    n+="
                    <td colspan='4'>KO: Metadaten<br>
                        <div id='<? echo $winId; ?>-fd13' data-type='1000' data-root='30' data-value='<? echo $fd[13]; ?>' data-options='typ=1'
                             class='control10' style='width:100%;'>&nbsp;
                        </div>
                    </td>
                    ";
                    n+="
                </tr>
                ";
                n+="
                <tr>";
                    n+="
                    <td colspan='4'>KO: Nutzerinteraktion<br>
                        <div id='<? echo $winId; ?>-fd14' data-type='1000' data-root='30' data-value='<? echo $fd[14]; ?>' data-options='typ=1'
                             class='control10' style='width:100%;'>&nbsp;
                        </div>
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
        document.getElementById("<? echo $winId; ?>-edit").innerHTML=n;

        document.getElementById("<? echo $winId; ?>-fd3").value='<? ajaxValue($fd[3]); ?>';
        document.getElementById("<? echo $winId; ?>-fd4").value='<? ajaxValue($fd[4]); ?>';
    <? }
    if ($db == 'editVisuSnd') {
        $ss1 = sql_call("SELECT * FROM edomiProject.editVisuSnd WHERE (id=" . $phpdataArr[1] . ")");
        if ($n = sql_result($ss1)) {
            $fd[0] = $db;
            $fd[1] = $n['id'];
            $fd[2] = $n['folderid'];
            $fd[3] = $n['name'];
        } else {
            $fd[0] = $db;
            $fd[1] = -1;
            $fd[2] = $phpdataArr[0];
            $fd[3] = '';
        }
        editItem_setMeta('Ton', $fd[1], $fd[2], '29'); ?>
        n+="
        <div class='appMenu'>";
            n+="
            <div class='cmdButton cmdButtonL' onClick='app1000_editCancel(\"<? echo $winId; ?>\");'>Abbrechen</div>
            ";
            n+="
            <div class='cmdButton cmdButtonR'
                 onClick='ajax(\"saveItem\",\"<? echo $appId; ?>\",\"<? echo $winId; ?>\",\"<? echo $data; ?>\",controlGetFormData(\"<? echo $winId; ?>-form1\"));'>
                <b>Übernehmen</b></div>
            ";

            n+="
            <iframe id='<? echo $winId; ?>-iframe' name='<? echo $winId; ?>-iframe' style='width:1px; height:1px; display:none;'></iframe>
            ";
            n+="
            <form id='<? echo $winId; ?>-formupload1'
                  action='apps/app_upload.php?filename=snd-tmp&suffixes=mp3;&ajaxok=visuSndUploadOk&ajaxappid=<? echo $appId; ?>&ajaxwinid=<? echo $winId; ?>&ajaxdata=<? echo $fd[1]; ?>&sid=<? echo $sid; ?>'
                  target='<? echo $winId; ?>-iframe' method='post' enctype='multipart/form-data' style='display:inline; float:right;'>";
                n+="
                <div class='cmdUpload'><b>Tondatei hochladen (mp3)</b><input type='file' name='file' id='file' accept='audio/*'
                                                                             onChange='openBusyWindow(); document.getElementById(\"<? echo $winId; ?>-formupload1\").submit();'>
                </div>
                <br>";
                n+="
            </form>
            ";
            n+="
        </div>";
        n+="
        <div id='<? echo $winId; ?>-form1' class='appContentBlank'>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd0' data-type='1' value='<? echo $fd[0]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd1' data-type='1' value='<? echo $fd[1]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd2' data-type='1' value='<? echo $fd[2]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd10' data-type='1' value='0'></input>";
            n+="
            <table width='100%' border='0' cellpadding='5' cellspacing='0'>";
                n+="
                <tr>
                    <td>"+app1000_editpath+"</td>
                </tr>
                ";
                n+="
                <tr>
                    <td>Name<br><input type='text' id='<? echo $winId; ?>-fd3' data-type='1' value='' class='control1' autofocus style='width:100%;'></input>
                    </td>
                </tr>
                ";

                n+="
                <tr valign='top'>";
                    <? if ($fd[1] > 0 && file_exists(MAIN_PATH . '/www/data/project/visu/etc/snd-' . $fd[1] . '.mp3')) { ?>
                        n+="
                        <td>
                            <audio id='<? echo $winId; ?>-preview' src='../data/project/visu/etc/snd-<? echo $fd[1]; ?>.mp3' controls
                                   style='width:100%;'></audio>
                        </td>";
                    <? } else { ?>
                        n+="
                        <td>
                            <audio id='<? echo $winId; ?>-preview' src='../shared/etc/snd-empty.mp3' controls style='width:100%;'></audio>
                        </td>";
                    <? } ?>
                    n+="
                </tr>
                ";
                n+="
            </table>
            ";
            n+="
        </div>";
        document.getElementById("<? echo $winId; ?>-edit").innerHTML=n;

        document.getElementById("<? echo $winId; ?>-fd3").value='<? ajaxValue($fd[3]); ?>';
    <? }
    if ($db == 'editVisuFont') {
        $ss1 = sql_call("SELECT * FROM edomiProject.editVisuFont WHERE (id=" . $phpdataArr[1] . ")");
        if ($n = sql_result($ss1)) {
            $fd[0] = $db;
            $fd[1] = $n['id'];
            $fd[2] = $n['folderid'];
            $fd[3] = $n['name'];
            $fd[4] = $n['fonttyp'];
            if ($n['fonttyp'] == 0) {
                $fd[5] = $n['fontname'];
            } else {
                $fd[5] = '';
            }
            $fd[6] = $n['fontstyle'];
            $fd[7] = $n['fontweight'];
        } else {
            $fd[0] = $db;
            $fd[1] = -1;
            $fd[2] = $phpdataArr[0];
            $fd[3] = '';
            $fd[4] = 0;
            $fd[5] = '';
            $fd[6] = 0;
            $fd[7] = 0;
        }
        editItem_setMeta('Schriftart', $fd[1], $fd[2], '150'); ?>
        n+="
        <div class='appMenu'>";
            n+="
            <div class='cmdButton cmdButtonL' onClick='app1000_editCancel(\"<? echo $winId; ?>\");'>Abbrechen</div>
            ";
            n+="
            <div class='cmdButton cmdButtonR'
                 onClick='ajax(\"saveItem\",\"<? echo $appId; ?>\",\"<? echo $winId; ?>\",\"<? echo $data; ?>\",controlGetFormData(\"<? echo $winId; ?>-form1\"));'>
                <b>Übernehmen</b></div>
            ";
            n+="
            <iframe id='<? echo $winId; ?>-iframe' name='<? echo $winId; ?>-iframe' style='width:1px; height:1px; display:none;'></iframe>
            ";
            n+="
            <div id='<? echo $winId; ?>-btnpreview' class='cmdButton' onClick='app1000_fontPreview(\"<? echo $winId; ?>\");' style='display:none; float:right;'>
                Vorschau
            </div>
            ";
            n+="
            <form id='<? echo $winId; ?>-formupload1'
                  action='apps/app_upload.php?filename=font-tmp&suffixes=ttf;&ajaxok=visuFontUploadOk&ajaxappid=<? echo $appId; ?>&ajaxwinid=<? echo $winId; ?>&ajaxdata=<? echo $fd[1]; ?>&sid=<? echo $sid; ?>'
                  target='<? echo $winId; ?>-iframe' method='post' enctype='multipart/form-data' style='display:none; float:right;'>";
                n+="
                <div class='cmdUpload' style='width:100%;'><b>Truetype-Datei hochladen (ttf)</b><input type='file' name='file' id='file' accept='.ttf'
                                                                                                       onChange='openBusyWindow(); document.getElementById(\"<? echo $winId; ?>-formupload1\").submit();'>
                </div>
                ";
                n+="
            </form>
            ";
            n+="
        </div>";
        n+="
        <div id='<? echo $winId; ?>-form1' class='appContentBlank'>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd0' data-type='1' value='<? echo $fd[0]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd1' data-type='1' value='<? echo $fd[1]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd2' data-type='1' value='<? echo $fd[2]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd10' data-type='1' value='0'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd11' data-type='1'
                       value='<? echo(file_exists(MAIN_PATH . '/www/data/project/visu/etc/font-' . $fd[1] . '.ttf') ? '1' : '0'); ?>'></input>";
            n+="
            <table width='100%' border='0' cellpadding='5' cellspacing='0'>";
                n+="
                <colgroup>";
                    n+="
                    <col width='50%'>
                    ";
                    n+="
                    <col width='50%'>
                    ";
                    n+="
                </colgroup>
                ";
                n+="
                <tr>
                    <td colspan='2'>"+app1000_editpath+"</td>
                </tr>
                ";
                n+="
                <tr>
                    <td colspan='2'>Name<br><input type='text' id='<? echo $winId; ?>-fd3' data-type='1' value='' class='control1' autofocus
                                                   style='width:100%;'></input></td>
                </tr>
                ";
                n+="
                <tr>";
                    n+="
                    <td colspan='2'>Typ<br>
                        <div id='<? echo $winId; ?>-fd4' onChange='app1000_fontPreview(\"<? echo $winId; ?>\");' data-type='6' data-value='<? echo $fd[4]; ?>'
                             data-list='0|System-Schriftart|<? echo $winId; ?>-radiotr1;1|Individuelle Schriftart (Truetype)|<? echo $winId; ?>-radiotr2;'
                             class='control6' style='width:100%;'>&nbsp;
                        </div>
                    </td>
                    ";
                    n+="
                </tr>
                ";

                n+="
                <tr id='<? echo $winId; ?>-radiotr1'>";
                    n+="
                    <td colspan='2'>Schriftart<br><input type='text' id='<? echo $winId; ?>-fd5' data-type='1' value='' class='control1'
                                                         style='width:100%;'></input></td>
                    ";
                    n+="
                </tr>
                ";
                n+="
                <tr id='<? echo $winId; ?>-radiotr2'>";
                    n+="
                    <td>Gültigkeit (Stil)<br>
                        <div id='<? echo $winId; ?>-fd6' data-type='6' data-value='<? echo $fd[6]; ?>' data-list='0|immer anwenden;1|nur für kursive Texte;'
                             class='control6' style='width:100%;'>&nbsp;
                        </div>
                    </td>
                    ";
                    n+="
                    <td>Gültigkeit (Stärke)<br>
                        <div id='<? echo $winId; ?>-fd7' data-type='6' data-value='<? echo $fd[7]; ?>' data-list='0|immer anwenden;1|nur für fette Texte;'
                             class='control6' style='width:100%;'>&nbsp;
                        </div>
                    </td>
                    ";
                    n+="
                </tr>
                ";

                n+="
                <tr valign='top'>";
                    n+="
                    <td colspan='2'>";
                        n+="
                        <div style='width:100%; height:380px; border:1px solid #c0c0c0; overflow:hidden; box-sizing:border-box;'>";
                            n+="
                            <table width='100%' height='100%' border='0' cellpadding='0' cellspacing='0'>";
                                n+="
                                <tr valign='middle'>
                                    <td align='center'>";
                                        n+="
                                        <div id='<? echo $winId; ?>-preview'>";
                                            n+="<span style='font-size:50px;'>abc ABC 123</span><br><br>";
                                            n+="<span style='font-size:20px;'>(){}[]#;:,.!?%&/+-*&deg;&euro;&lt;&gt;</span><br><br>";
                                            n+="<span
                                                style='font-size:20px;'>abcdefghijklmnopqrstuvwxyzäöüß<br>ABCDEFGHIJKLMNOPQRSTUVWXYZäöü<br>1234567890</span><br><br>";
                                            n+="<span
                                                style='font-size:12px;'>abcdefghijklmnopqrstuvwxyzäöüß<br>ABCDEFGHIJKLMNOPQRSTUVWXYZäöü<br>1234567890</span><br><br>";
                                            n+="<span
                                                style='font-size:8px;'>abcdefghijklmnopqrstuvwxyzäöüß<br>ABCDEFGHIJKLMNOPQRSTUVWXYZäöü<br>1234567890</span><br><br>";
                                            n+="<textarea rows='10' wrap='soft' class='control1' placeholder='Testeingabe'
                                                          style='width:90%; height:50px; font-size:20px; resize:none;'></textarea>";
                                            n+="
                                        </div>
                                        ";
                                        n+="
                                    </td>
                                </tr>
                                ";
                                n+="
                            </table>
                            ";
                            n+="
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
        document.getElementById("<? echo $winId; ?>-edit").innerHTML=n;

        document.getElementById("<? echo $winId; ?>-fd3").value='<? ajaxValue($fd[3]); ?>';
        document.getElementById("<? echo $winId; ?>-fd5").value='<? ajaxValue($fd[5]); ?>';

        app1000_fontPreview("<? echo $winId; ?>");
    <? }
    if ($db == 'editVisuFormat') {
        $ss1 = sql_call("SELECT * FROM edomiProject.editVisuFormat WHERE (id=" . $phpdataArr[1] . ")");
        if ($n = sql_result($ss1)) {
            $fd[0] = $db;
            $fd[1] = $n['id'];
            $fd[2] = $n['folderid'];
            $fd[3] = $n['name'];
            $fd[4] = $n['fgid'];
            $fd[5] = $n['bgid'];
            $fd[6] = $n['imgid'];
        } else {
            $fd[0] = $db;
            $fd[1] = -1;
            $fd[2] = $phpdataArr[0];
            $fd[3] = '';
            $fd[4] = '';
            $fd[5] = '';
            $fd[6] = '';
        }
        editItem_setMeta('Formatierung (Meldungsarchive)', $fd[1], $fd[2], dbRoot_getRootId($fd[2])); ?>
        n+="
        <div class='appMenu'>";
            n+="
            <div class='cmdButton cmdButtonL' onClick='app1000_editCancel(\"<? echo $winId; ?>\");'>Abbrechen</div>
            ";
            n+="
            <div class='cmdButton cmdButtonR'
                 onClick='ajax(\"saveItem\",\"<? echo $appId; ?>\",\"<? echo $winId; ?>\",\"<? echo $data; ?>\",controlGetFormData(\"<? echo $winId; ?>-form1\"));'>
                <b>Übernehmen</b></div>
            ";
            n+="
        </div>";
        n+="
        <div id='<? echo $winId; ?>-form1' class='appContentBlank'>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd0' data-type='1' value='<? echo $fd[0]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd1' data-type='1' value='<? echo $fd[1]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd2' data-type='1' value='<? echo $fd[2]; ?>'></input>";
            n+="
            <table width='100%' border='0' cellpadding='5' cellspacing='0'>";
                n+="
                <tr>
                    <td colspan='2'>"+app1000_editpath+"</td>
                </tr>
                ";
                n+="
                <tr>
                    <td colspan='2'>Name<br><input type='text' id='<? echo $winId; ?>-fd3' data-type='1' value='' class='control1' autofocus
                                                   style='width:100%;'></input></td>
                </tr>
                ";
                n+="
                <tr>
                    <td colspan='2'>Symbol<br>
                        <div id='<? echo $winId; ?>-fd6' data-type='1000' data-root='28' data-value='<? echo $fd[6]; ?>' data-options='typ=1' class='control10'
                             style='width:100%;'>&nbsp;
                        </div>
                    </td>
                </tr>
                ";
                n+="
                <tr>";
                    n+="
                    <td>Farbe<br>
                        <div id='<? echo $winId; ?>-fd4' data-type='1000' data-root='26' data-value='<? echo $fd[4]; ?>' data-options='typ=1' class='control10'
                             style='width:100%;'>&nbsp;
                        </div>
                    </td>
                    ";
                    n+="
                    <td>Hintergrundfarbe<br>
                        <div id='<? echo $winId; ?>-fd5' data-type='1000' data-root='25' data-value='<? echo $fd[5]; ?>' data-options='typ=1' class='control10'
                             style='width:100%;'>&nbsp;
                        </div>
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
        document.getElementById("<? echo $winId; ?>-edit").innerHTML=n;

        document.getElementById("<? echo $winId; ?>-fd3").value='<? ajaxValue($fd[3]); ?>';
        document.getElementById("<? echo $winId; ?>-fd4").value='<? ajaxValue($fd[4]); ?>';
        document.getElementById("<? echo $winId; ?>-fd5").value='<? ajaxValue($fd[5]); ?>';
        document.getElementById("<? echo $winId; ?>-fd6").value='<? ajaxValue($fd[6]); ?>';
    <? }
    if ($db == 'editVisuImg') {
        $ss1 = sql_call("SELECT * FROM edomiProject.editVisuImg WHERE (id=" . $phpdataArr[1] . ")");
        if ($n = sql_result($ss1)) {
            $fd[0] = $db;
            $fd[1] = $n['id'];
            $fd[2] = $n['folderid'];
            $fd[3] = $n['name'];
            $fd[4] = $n['xsize'];
            $fd[5] = $n['ysize'];
            $fd[6] = $n['suffix'];
        } else {
            $fd[0] = $db;
            $fd[1] = -1;
            $fd[2] = $phpdataArr[0];
            $fd[3] = '';
            $fd[4] = '';
            $fd[5] = '';
            $fd[6] = '';
        }
        editItem_setMeta('Bild', $fd[1], $fd[2], '28'); ?>
        n+="
        <div class='appMenu'>";
            n+="
            <div class='cmdButton cmdButtonL' onClick='app1000_editCancel(\"<? echo $winId; ?>\");'>Abbrechen</div>
            ";
            n+="
            <div class='cmdButton cmdButtonR'
                 onClick='ajax(\"saveItem\",\"<? echo $appId; ?>\",\"<? echo $winId; ?>\",\"<? echo $data; ?>\",controlGetFormData(\"<? echo $winId; ?>-form1\"));'>
                <b>Übernehmen</b></div>
            ";
            <? if ($fd[1] < 0) { ?>
                n+="
                <form id='<? echo $winId; ?>-formupload2'
                      action='apps/app_upload.php?multiple=1&folderid=<? echo $phpdataArr[0]; ?>&suffixes=jpg;jpeg;gif;png;svg;&ajaxok=visuImgMultiuploadOk&ajaxappid=<? echo $appId; ?>&ajaxwinid=<? echo $winId; ?>&ajaxdata=<? echo $data; ?>&sid=<? echo $sid; ?>'
                      target='<? echo $winId; ?>-iframe' method='post' enctype='multipart/form-data' style='display:inline; float:right; margin-left:5px;'>";
                    n+="
                    <div class='cmdUpload'>Mehrere Bilddateien hochladen<input type='file' name='files[]' id='files' multiple='' accept='image/*'
                                                                               onChange='openBusyWindow(); document.getElementById(\"<? echo $winId; ?>-formupload2\").submit();'>
                    </div>
                    <br>";
                    n+="
                </form>";
            <? } ?>
            n+="
            <iframe id='<? echo $winId; ?>-iframe' name='<? echo $winId; ?>-iframe' style='width:1px; height:1px; display:none;'></iframe>
            ";
            n+="
            <form id='<? echo $winId; ?>-formupload1'
                  action='apps/app_upload.php?filename=img-tmp&suffixes=jpg;jpeg;gif;png;svg;&ajaxok=visuImgUploadOk&ajaxappid=<? echo $appId; ?>&ajaxwinid=<? echo $winId; ?>&ajaxdata=<? echo $fd[1]; ?>&sid=<? echo $sid; ?>'
                  target='<? echo $winId; ?>-iframe' method='post' enctype='multipart/form-data' style='display:inline; float:right;'>";
                n+="
                <div class='cmdUpload'><b>Bilddatei hochladen</b><input type='file' name='file' id='file' accept='image/*'
                                                                        onChange='openBusyWindow(); document.getElementById(\"<? echo $winId; ?>-formupload1\").submit();'>
                </div>
                ";
                n+="
            </form>
            ";
            n+="
        </div>";
        n+="
        <div id='<? echo $winId; ?>-form1' class='appContentBlank'>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd0' data-type='1' value='<? echo $fd[0]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd1' data-type='1' value='<? echo $fd[1]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd2' data-type='1' value='<? echo $fd[2]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd4' data-type='1' value='<? echo $fd[4]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd5' data-type='1' value='<? echo $fd[5]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd6' data-type='1' value='<? echo $fd[6]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd10' data-type='1' value='0'></input>";
            n+="
            <table width='100%' border='0' cellpadding='5' cellspacing='0'>";
                n+="
                <tr>
                    <td>"+app1000_editpath+"</td>
                </tr>
                ";
                n+="
                <tr>
                    <td>Name<br><input type='text' id='<? echo $winId; ?>-fd3' data-type='1' value='' class='control1' autofocus style='width:100%;'></input>
                    </td>
                </tr>
                ";

                n+="
                <tr valign='top'>";
                    <? if ($fd[1] > 0 && file_exists(MAIN_PATH . '/www/data/project/visu/img/img-' . $fd[1] . '.' . $fd[6])) {
                        $imgFilename = '../data/project/visu/img/img-' . $fd[1] . '.' . $fd[6] . '?' . date('YmdHis'); ?>
                        n+="
                        <td>";
                            n+="
                            <div
                                style='width:100%; height:470px; background:url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAQAAAAECAYAAACp8Z5+AAAAJUlEQVQIHWNsaGj4zwAFCgoKDEzInAcPHkAEQDIgDggwIXNAAgCU4wvnT6Hq5QAAAABJRU5ErkJggg==) repeat;'>
                                ";
                                n+="
                                <div id='<? echo $winId; ?>-preview'
                                     style='width:100%; height:100%; background-image:url(<? echo $imgFilename; ?>); background-size:contain; background-repeat:no-repeat;'></div>
                                ";
                                n+="
                            </div>
                            ";
                            <? if ($fd[4] == 0 && $fd[5] == 0) { ?>
                                n+="
                                <div id='<? echo $winId; ?>-imginfo' style='padding-left:3px; padding-top:5px;'>Vektorgrafik (<? echo strToUpper($fd[6]); ?>)
                                </div>";
                            <? } else { ?>
                                n+="
                                <div id='<? echo $winId; ?>-imginfo' style='padding-left:3px; padding-top:5px;'><? echo $fd[4]; ?> x <? echo $fd[5]; ?> Pixel
                                    (<? echo strToUpper($fd[6]); ?>)
                                </div>";
                            <? } ?>
                            n+="
                        </td>";
                    <? } else { ?>
                        n+="
                        <td>";
                            n+="
                            <div
                                style='width:100%; height:470px; background:url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAQAAAAECAYAAACp8Z5+AAAAJUlEQVQIHWNsaGj4zwAFCgoKDEzInAcPHkAEQDIgDggwIXNAAgCU4wvnT6Hq5QAAAABJRU5ErkJggg==) repeat;'>
                                ";
                                n+="
                                <div id='<? echo $winId; ?>-preview'
                                     style='width:100%; height:100%; background-size:contain; background-repeat:no-repeat;'></div>
                                ";
                                n+="
                            </div>
                            ";
                            n+="
                            <div id='<? echo $winId; ?>-imginfo' style='padding-left:3px; padding-top:5px;'></div>
                            ";
                            n+="
                        </td>";
                    <? } ?>
                    n+="
                </tr>
                ";
                n+="
            </table>
            ";
            n+="
        </div>";
        document.getElementById("<? echo $winId; ?>-edit").innerHTML=n;

        document.getElementById("<? echo $winId; ?>-fd3").value='<? ajaxValue($fd[3]); ?>';
    <? }
    if ($db == 'editVisuBGcol') {
        $ss1 = sql_call("SELECT * FROM edomiProject.editVisuBGcol WHERE (id=" . $phpdataArr[1] . ")");
        if ($n = sql_result($ss1)) {
            $fd[0] = $db;
            $fd[1] = $n['id'];
            $fd[2] = $n['folderid'];
            $fd[3] = $n['name'];
            $fd[4] = $n['color'];
        } else {
            $fd[0] = $db;
            $fd[1] = -1;
            $fd[2] = $phpdataArr[0];
            $fd[3] = '';
            $fd[4] = '';
        }
        editItem_setMeta('Farbe (Hintergrund)', $fd[1], $fd[2], '25'); ?>
        n+="
        <div class='appMenu'>";
            n+="
            <div class='cmdButton cmdButtonL' onClick='app1000_editCancel(\"<? echo $winId; ?>\");'>Abbrechen</div>
            ";
            n+="
            <div class='cmdButton cmdButtonR'
                 onClick='ajax(\"saveItem\",\"<? echo $appId; ?>\",\"<? echo $winId; ?>\",\"<? echo $data; ?>\",controlGetFormData(\"<? echo $winId; ?>-form1\"));'>
                <b>Übernehmen</b></div>
            ";
            n+="<input type='text' id='<? echo $winId; ?>-ko' data-type='1' value='0' class='control1 cmdButtonR' placeholder='KO-Wert'
                       style='width:100px; height:27px; border-color:#c0c0c0; float:right;'></input>";
            n+="
            <div class='cmdButton cmdButtonL'
                 onClick='app1000_colorBGpreview(\"<? echo $winId; ?>-colorpreview\",document.getElementById(\"<? echo $winId; ?>-fd4\").value,document.getElementById(\"<? echo $winId; ?>-ko\").value);'
                 style='width:100px; float:right;'>Vorschau
            </div>
            ";
            n+="
        </div>";
        n+="
        <div id='<? echo $winId; ?>-form1' class='appContentBlank'>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd0' data-type='1' value='<? echo $fd[0]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd1' data-type='1' value='<? echo $fd[1]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd2' data-type='1' value='<? echo $fd[2]; ?>'></input>";
            n+="
            <table width='100%' border='0' cellpadding='5' cellspacing='0'>";
                n+="
                <tr>
                    <td>"+app1000_editpath+"</td>
                </tr>
                ";
                n+="
                <tr>
                    <td>Name<br><input type='text' id='<? echo $winId; ?>-fd3' data-type='1' value='' class='control1' autofocus style='width:100%;'></input>
                    </td>
                </tr>
                ";
                n+="
                <tr>
                    <td>Farb-Definition<br><textarea id='<? echo $winId; ?>-fd4' data-type='1' maxlength='1000' rows='10' wrap='soft' class='control1'
                                                     style='width:100%; height:220px; background:#e0ffe0; resize:none;'></textarea></td>
                </tr>
                ";
                n+="
                <tr valign='top'>";
                    n+="
                    <td>";
                        n+="
                        <div id='<? echo $winId; ?>-colorpreview'
                             style='position:relative; left:0; top:0; width:100%; padding-top:10px; padding-bottom:10px; text-align:center; background:#e0e0e0; border:1px solid #c0c0c0; box-sizing:border-box;'>
                            ";
                            n+="<span style='font-size:30px; color:#ffffff;'>Testtext 123</span><br>";
                            n+="<span style='font-size:25px; color:#eeeeee;'>Testtext 123</span><br>";
                            n+="<span style='font-size:20px; color:#cccccc;'>Testtext 123</span><br>";
                            n+="<span style='font-size:15px; color:#aaaaaa;'>Testtext 123</span><br>";
                            n+="<span style='font-size:10px; color:#888888;'>Testtext 123</span><br>";
                            n+="<span style='font-size:15px; color:#666666;'>Testtext 123</span><br>";
                            n+="<span style='font-size:20px; color:#444444;'>Testtext 123</span><br>";
                            n+="<span style='font-size:25px; color:#222222;'>Testtext 123</span><br>";
                            n+="<span style='font-size:30px; color:#000000;'>Testtext 123</span><br>";
                            n+="
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
        document.getElementById("<? echo $winId; ?>-edit").innerHTML=n;
        app1000_colorBGpreview("<? echo $winId; ?>-colorpreview","<? ajaxValue($fd[4]); ?>","0");

        document.getElementById("<? echo $winId; ?>-fd3").value='<? ajaxValue($fd[3]); ?>';
        document.getElementById("<? echo $winId; ?>-fd4").value='<? ajaxValue($fd[4]); ?>';
    <? }
    if ($db == 'editVisuFGcol') {
        $ss1 = sql_call("SELECT * FROM edomiProject.editVisuFGcol WHERE (id=" . $phpdataArr[1] . ")");
        if ($n = sql_result($ss1)) {
            $fd[0] = $db;
            $fd[1] = $n['id'];
            $fd[2] = $n['folderid'];
            $fd[3] = $n['name'];
            $fd[4] = $n['color'];
        } else {
            $fd[0] = $db;
            $fd[1] = -1;
            $fd[2] = $phpdataArr[0];
            $fd[3] = '';
            $fd[4] = '';
        }
        editItem_setMeta('Farbe (Vordergrund)', $fd[1], $fd[2], '26'); ?>
        n+="
        <div class='appMenu'>";
            n+="
            <div class='cmdButton cmdButtonL' onClick='app1000_editCancel(\"<? echo $winId; ?>\");'>Abbrechen</div>
            ";
            n+="
            <div class='cmdButton cmdButtonR'
                 onClick='ajax(\"saveItem\",\"<? echo $appId; ?>\",\"<? echo $winId; ?>\",\"<? echo $data; ?>\",controlGetFormData(\"<? echo $winId; ?>-form1\"));'>
                <b>Übernehmen</b></div>
            ";
            n+="<input type='text' id='<? echo $winId; ?>-ko' data-type='1' value='0' class='control1 cmdButtonR' placeholder='KO-Wert'
                       style='width:100px; height:27px; border-color:#c0c0c0; float:right;'></input>";
            n+="
            <div class='cmdButton cmdButtonL'
                 onClick='app1000_colorFGpreview(\"<? echo $winId; ?>-colorpreview\",document.getElementById(\"<? echo $winId; ?>-fd4\").value,document.getElementById(\"<? echo $winId; ?>-ko\").value);'
                 style='width:100px; float:right;'>Vorschau
            </div>
            ";
            n+="
        </div>";
        n+="
        <div id='<? echo $winId; ?>-form1' class='appContentBlank'>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd0' data-type='1' value='<? echo $fd[0]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd1' data-type='1' value='<? echo $fd[1]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd2' data-type='1' value='<? echo $fd[2]; ?>'></input>";
            n+="
            <table width='100%' border='0' cellpadding='5' cellspacing='0'>";
                n+="
                <tr>
                    <td>"+app1000_editpath+"</td>
                </tr>
                ";
                n+="
                <tr>
                    <td>Name<br><input type='text' id='<? echo $winId; ?>-fd3' data-type='1' value='' class='control1' autofocus style='width:100%;'></input>
                    </td>
                </tr>
                ";
                n+="
                <tr>
                    <td>Farb-Definition<br><textarea id='<? echo $winId; ?>-fd4' data-type='1' maxlength='1000' rows='10' wrap='soft' class='control1'
                                                     style='width:100%; height:220px; background:#e0ffe0; resize:none;'></textarea></td>
                </tr>
                ";
                n+="
                <tr valign='top'>";
                    n+="
                    <td>";
                        n+="
                        <div id='<? echo $winId; ?>-colorpreview'
                             style='position:relative; left:0; top:0; width:100%; padding-top:10px; padding-bottom:10px; text-align:center; color:transparent; background:-webkit-linear-gradient(top,#ffffff 0%,#000000 100%); border:1px solid #c0c0c0; box-sizing:border-box;'>
                            ";
                            n+="<span style='font-size:30px;'>Testtext 123</span><br>";
                            n+="<span style='font-size:25px;'>Testtext 123</span><br>";
                            n+="<span style='font-size:20px;'>Testtext 123</span><br>";
                            n+="<span style='font-size:15px;'>Testtext 123</span><br>";
                            n+="<span style='font-size:10px;'>Testtext 123</span><br>";
                            n+="<span style='font-size:15px;'>Testtext 123</span><br>";
                            n+="<span style='font-size:20px;'>Testtext 123</span><br>";
                            n+="<span style='font-size:25px;'>Testtext 123</span><br>";
                            n+="<span style='font-size:30px;'>Testtext 123</span><br>";
                            n+="
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
        document.getElementById("<? echo $winId; ?>-edit").innerHTML=n;
        app1000_colorFGpreview("<? echo $winId; ?>-colorpreview","<? ajaxValue($fd[4]); ?>","0");

        document.getElementById("<? echo $winId; ?>-fd3").value='<? ajaxValue($fd[3]); ?>';
        document.getElementById("<? echo $winId; ?>-fd4").value='<? ajaxValue($fd[4]); ?>';
    <? }
    if ($db == 'editVisuAnim') {
        $ss1 = sql_call("SELECT * FROM edomiProject.editVisuAnim WHERE (id=" . $phpdataArr[1] . ")");
        if ($n = sql_result($ss1)) {
            $fd[0] = $db;
            $fd[1] = $n['id'];
            $fd[2] = $n['folderid'];
            $fd[3] = $n['name'];
            $fd[4] = $n['keyframes'];
            $fd[5] = $n['timing'];
            $fd[6] = $n['delay'];
            $fd[7] = $n['direction'];
            $fd[8] = $n['fillmode'];
        } else {
            $fd[0] = $db;
            $fd[1] = -1;
            $fd[2] = $phpdataArr[0];
            $fd[3] = '';
            $fd[4] = '';
            $fd[5] = '0';
            $fd[6] = '0';
            $fd[7] = '0';
            $fd[8] = '0';
        }
        editItem_setMeta('Animation', $fd[1], $fd[2], '27'); ?>
        n+="
        <div class='appMenu'>";
            n+="
            <table width='100%' border='0' cellpadding='0' cellspacing='0' style='white-space: nowrap; table-layout:auto;'>";
                n+="
                <tr>";
                    n+="
                    <td>";
                        n+="
                        <div class='cmdButton cmdButtonL' onClick='app1000_editCancel(\"<? echo $winId; ?>\");'>Abbrechen</div>
                        ";
                        n+="
                        <div class='cmdButton cmdButtonR'
                             onClick='ajax(\"saveItem\",\"<? echo $appId; ?>\",\"<? echo $winId; ?>\",\"<? echo $data; ?>\",controlGetFormData(\"<? echo $winId; ?>-form1\"));'>
                            <b>Übernehmen</b></div>
                        ";
                        n+="
                    </td>
                    ";
                    n+="
                    <td align='right'>";
                        n+="
                        <div id='<? echo $winId; ?>-duration' style='display:inline;'>1s</div>&nbsp;";
                        n+="<input type='range' class='controlSlider' value='1' min='1' max='10' step='1'
                                   onInput='app1000_animationDuration(\"<? echo $winId; ?>\",this.value);' style='width:80px; vertical-align:middle;'></input>";
                        n+="&nbsp;&nbsp;&nbsp;";
                        n+="
                        <div id='<? echo $winId; ?>-count' style='display:inline;'>1x</div>&nbsp;";
                        n+="<input type='range' class='controlSlider' value='1' min='1' max='10' step='1'
                                   onInput='app1000_animationCount(\"<? echo $winId; ?>\",this.value);' style='width:80px; vertical-align:middle;'></input>";
                        n+="&nbsp;&nbsp;&nbsp;";
                        n+="
                        <div class='cmdButton cmdButtonL' onClick='app1000_animationPreview(\"<? echo $winId; ?>\");' style='min-width:50px;'>&gt;</div>
                        ";
                        n+="
                        <div class='cmdButton cmdButtonR' onClick='app1000_animationStop(\"<? echo $winId; ?>\");' style='min-width:50px;'>
                            <div style='display:inline-block; width:5px; height:5px; border:1px solid #606060;'></div>
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

        n+="
        <div id='<? echo $winId; ?>-form1' class='appContentBlank'>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd0' data-type='1' value='<? echo $fd[0]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd1' data-type='1' value='<? echo $fd[1]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd2' data-type='1' value='<? echo $fd[2]; ?>'></input>";
            n+="
            <table width='100%' border='0' cellpadding='5' cellspacing='0'>";
                n+="
                <colgroup>";
                    n+="
                    <col width='70%'>
                    ";
                    n+="
                    <col width='30%'>
                    ";
                    n+="
                </colgroup>
                ";
                n+="
                <tr>
                    <td colspan='2'>"+app1000_editpath+"</td>
                </tr>
                ";
                n+="
                <tr>
                    <td colspan='2'>Name<br><input type='text' id='<? echo $winId; ?>-fd3' data-type='1' value='' class='control1' autofocus
                                                   style='width:100%;'></input></td>
                </tr>
                ";
                n+="
                <tr>
                    <td rowspan='5'>Keyframes<br><textarea id='<? echo $winId; ?>-fd4' data-type='1' maxlength='10000' rows='10' wrap='soft' class='control1'
                                                           style='width:100%; height:220px; resize:none;'></textarea></td>
                </tr>
                ";

                n+="
                <tr valign='top'>";
                    n+="
                    <td>Geschwindigkeit<br>
                        <div id='<? echo $winId; ?>-fd5' data-type='6' data-value='<? echo $fd[5]; ?>'
                             data-list='0|gleichförmig;1|schnell beschleunigen und verlangsamen;4|gleichmäßig beschleunigen und verlangsamen;2|beschleunigen;3|verlangsamen;'
                             class='control6' style='width:100%;'>&nbsp;
                        </div>
                    </td>
                    ";
                    n+="
                </tr>
                ";
                n+="
                <tr valign='top'>";
                    n+="
                    <td>Verzögerung (Sekunden, 0=ohne)<br><input type='text' id='<? echo $winId; ?>-fd6' data-type='1' value='' class='control1' autofocus
                                                                 style='width:100%;'></input></td>
                    ";
                    n+="
                </tr>
                ";
                n+="
                <tr valign='top'>";
                    n+="
                    <td>Abspielrichtung<br>
                        <div id='<? echo $winId; ?>-fd7' data-type='6' data-value='<? echo $fd[7]; ?>'
                             data-list='0|vorwärts;1|rückwärts;2|alternierend;3|rückwärts alternierend;' class='control6' style='width:100%;'>&nbsp;
                        </div>
                    </td>
                    ";
                    n+="
                </tr>
                ";
                n+="
                <tr valign='top'>";
                    n+="
                    <td>Füllmodus<br>
                        <div id='<? echo $winId; ?>-fd8' data-type='6' data-value='<? echo $fd[8]; ?>' data-list='0|ohne;1|vorwärts;2|rückwärts;3|beides;'
                             class='control6' style='width:100%;'>&nbsp;
                        </div>
                    </td>
                    ";
                    n+="
                </tr>
                ";

                n+="
                <tr valign='top'>";
                    n+="
                    <td colspan='2'>";
                        n+="
                        <div
                            style='position:relative; left:0; top:0; width:100%; height:240px; border:1px solid #c0c0c0; overflow:hidden; box-sizing:border-box;'>
                            ";
                            n+="
                            <table width='100%' height='100%' border='0' cellpadding='0' cellspacing='0'>";
                                n+="
                                <tr valign='middle'>
                                    <td align='center'>
                                        <div id='<? echo $winId; ?>-animpreview'
                                             style='width:30px; height:30px; border:3px solid #000000; border-radius:3px; background:#80e000;'></div>
                                    </td>
                                </tr>
                                ";
                                n+="
                            </table>
                            ";
                            n+="
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
        document.getElementById("<? echo $winId; ?>-edit").innerHTML=n;

        document.getElementById("<? echo $winId; ?>-fd3").value='<? ajaxValue($fd[3]); ?>';
        document.getElementById("<? echo $winId; ?>-fd4").value='<? ajaxValue($fd[4]); ?>';
        document.getElementById("<? echo $winId; ?>-fd6").value='<? ajaxValue($fd[6]); ?>';
    <? }
    if ($db == 'editVisuElementDesignDef') { ?>
        n+="
        <div class='appMenu'>";
            <? $ss1 = sql_call("SELECT * FROM edomiProject.editVisuElementDesignDef WHERE (id=" . $phpdataArr[1] . ")");
            if ($n = sql_result($ss1)) {
                $fd[0] = $db;
                $fd[1] = $n['id'];
                $fd[2] = $n['folderid'];
                $fd[3] = $n['name']; ?>
                n+="
                <div class='cmdButton cmdButtonL cmdButtonDisabled'>Abbrechen</div>";
                                                                                   n+="
                <div class='cmdButton cmdButtonR'
                     onClick='ajax(\"saveItem\",\"<? echo $appId; ?>\",\"<? echo $winId; ?>\",\"<? echo $data; ?>\",controlGetFormData(\"<? echo $winId; ?>-form1\"));'>
                    <b>Übernehmen</b></div>";
            <? } else {
                $fd[0] = $db;
                $fd[1] = -1;
                $fd[2] = $phpdataArr[0];
                $fd[3] = ''; ?>
                n+="
                <div class='cmdButton cmdButtonL' onClick='app1000_editCancel(\"<? echo $winId; ?>\");'>Abbrechen</div>";
                                                                                                                       n+="
                <div class='cmdButton cmdButtonR'
                     onClick='ajax(\"saveItem\",\"<? echo $appId; ?>\",\"<? echo $winId; ?>\",\"<? echo $data; ?>\",controlGetFormData(\"<? echo $winId; ?>-form1\"));'>
                    <b>Designvorlage erstellen</b></div>";
            <? }
            editItem_setMeta('Designvorlage', $fd[1], $fd[2], '24'); ?>
            n+="
        </div>";
        n+="
        <div id='<? echo $winId; ?>-form1' class='appContentBlank'>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd0' data-type='1' value='<? echo $fd[0]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd1' data-type='1' value='<? echo $fd[1]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd2' data-type='1' value='<? echo $fd[2]; ?>'></input>";
            n+="
            <table width='100%' border='0' cellpadding='5' cellspacing='0'>";
                n+="
                <tr>
                    <td>"+app1000_editpath+"</td>
                </tr>
                ";
                n+="
                <tr>
                    <td>Name<br><input type='text' id='<? echo $winId; ?>-fd3' data-type='1' value='' class='control1' autofocus style='width:100%;'></input>
                    </td>
                </tr>
                ";
                <? if ($fd[1] > 0) { ?>
                    n+="
                    <tr>
                        <td>Design<br>
                            <div id='<? echo $winId; ?>-list1' data-type='1003' data-value='<? echo $fd[1]; ?>' data-itemid='0' data-options='-editSheet'
                                 class='control10' style='width:100%;'>&nbsp;
                            </div>
                        </td>
                    </tr>";
                <? } ?>
                n+="
            </table>
            ";
            n+="
        </div>";
        document.getElementById("<? echo $winId; ?>-edit").innerHTML=n;

        document.getElementById("<? echo $winId; ?>-fd3").value='<? ajaxValue($fd[3]); ?>';
    <? }
    if ($db == 'editChart') { ?>
        n+="
        <div class='appMenu'>";
            <? $ss1 = sql_call("SELECT * FROM edomiProject.editChart WHERE (id=" . $phpdataArr[1] . ")");
            if ($n = sql_result($ss1)) {
                $fd[0] = $db;
                $fd[1] = $n['id'];
                $fd[2] = $n['folderid'];
                $fd[3] = $n['name'];
                $fd[4] = $n['titel'];
                $fd[5] = $n['datefrom'];
                $fd[6] = $n['dateto'];
                $fd[7] = $n['mode'];
                $fd[8] = $n['xunit'];
                $fd[9] = $n['xinterval'];
                $fd[10] = $n['ymin'];
                $fd[11] = $n['ymax'];
                $fd[12] = $n['ynice'];
                $fd[13] = $n['yticks']; ?>
                n+="
                <div class='cmdButton cmdButtonL cmdButtonDisabled'>Abbrechen</div>";
                                                                                   n+="
                <div class='cmdButton cmdButtonR'
                     onClick='ajax(\"saveItem\",\"<? echo $appId; ?>\",\"<? echo $winId; ?>\",\"<? echo $data; ?>\",controlGetFormData(\"<? echo $winId; ?>-form1\"));'>
                    <b>Übernehmen</b></div>";
            <? } else {
                $fd[0] = $db;
                $fd[1] = -1;
                $fd[2] = $phpdataArr[0];
                $fd[3] = '';
                $fd[4] = '';
                $fd[5] = '';
                $fd[6] = '';
                $fd[7] = 0;
                $fd[8] = -1;
                $fd[9] = '';
                $fd[10] = '';
                $fd[11] = '';
                $fd[12] = 1;
                $fd[13] = 0; ?>
                n+="
                <div class='cmdButton cmdButtonL' onClick='app1000_editCancel(\"<? echo $winId; ?>\");'>Abbrechen</div>";
                                                                                                                       n+="
                <div class='cmdButton cmdButtonR'
                     onClick='ajax(\"saveItem\",\"<? echo $appId; ?>\",\"<? echo $winId; ?>\",\"<? echo $data; ?>\",controlGetFormData(\"<? echo $winId; ?>-form1\"));'>
                    <b>Diagramm erstellen</b></div>";
            <? }
            editItem_setMeta('Diagramm', $fd[1], $fd[2], '130'); ?>
            n+="
        </div>";
        n+="
        <div id='<? echo $winId; ?>-form1' class='appContentBlank'>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd0' data-type='1' value='<? echo $fd[0]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd1' data-type='1' value='<? echo $fd[1]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd2' data-type='1' value='<? echo $fd[2]; ?>'></input>";
            n+="
            <table width='100%' border='0' cellpadding='5' cellspacing='0'>";
                n+="
                <colgroup>";
                    n+="
                    <col width='25%'>
                    ";
                    n+="
                    <col width='25%'>
                    ";
                    n+="
                    <col width='25%'>
                    ";
                    n+="
                    <col width='25%'>
                    ";
                    n+="
                </colgroup>
                ";
                n+="
                <tr>
                    <td colspan='4'>"+app1000_editpath+"</td>
                </tr>
                ";
                n+="
                <tr>";
                    n+="
                    <td colspan='4'>Name<br><input type='text' id='<? echo $winId; ?>-fd3' data-type='1' value='' class='control1' autofocus
                                                   style='width:100%;'></input></td>
                    ";
                    n+="
                </tr>
                ";
                n+="
                <tr>";
                    n+="
                    <td colspan='2'>Diagrammtitel<br><input type='text' id='<? echo $winId; ?>-fd4' data-type='1' value='' class='control1'
                                                            style='width:100%;'></input></td>
                    ";
                    n+="
                    <td colspan='2'>Kumulation<br>
                        <div id='<? echo $winId; ?>-fd7' data-type='6' data-value='<? echo $fd[7]; ?>'
                             data-list='0|ohne;1|Sekunde;2|Minute;3|Stunde;4|Wochentag;5|Tag;6|Woche;7|Monat;8|Jahr;9|gesamter Zeitraum;' class='control6'
                             style='width:100%;'>&nbsp;
                        </div>
                    </td>
                    ";
                    n+="
                </tr>
                ";

                n+="
                <tr>";
                    n+="
                    <td colspan='2' class='formSubTitel'>Diagrammintervall
                        <hr>
                    </td>
                    ";
                    n+="
                    <td colspan='2' class='formSubTitel'>X-Achse
                        <hr>
                    </td>
                    ";
                    n+="
                </tr>
                ";
                n+="
                <tr>";
                    n+="
                    <td>Startdatum (links)<br><input type='text' id='<? echo $winId; ?>-fd5' data-type='1' value='' class='control1'
                                                     style='width:100%;'></input></td>
                    ";
                    n+="
                    <td>Enddatum (rechts)<br><input type='text' id='<? echo $winId; ?>-fd6' data-type='1' value='' class='control1' style='width:100%;'></input>
                    </td>
                    ";
                    n+="
                    <td>Intervall (Einheit)<br>
                        <div id='<? echo $winId; ?>-fd8' data-type='6' data-value='<? echo $fd[8]; ?>'
                             data-list='-1|automatisch skalieren;0|Sekunden;1|Minuten;2|Stunden;3|Tage;4|Monate;5|Jahre;' class='control6' style='width:100%;'>
                            &nbsp;
                        </div>
                    </td>
                    ";
                    n+="
                    <td>Tick-Abstand (in der gewählten Einheit)<br><input type='text' id='<? echo $winId; ?>-fd9' data-type='1' value='' class='control1'
                                                                          style='width:100%;'></input></td>
                    ";
                    n+="
                </tr>
                ";

                n+="
                <tr>
                    <td colspan='4' class='formSubTitel'>Y-Achsen
                        <hr>
                    </td>
                </tr>
                ";
                n+="
                <tr valign='top'>";
                    n+="
                    <td>Min-Wert (leer=automatisch)<br><input type='text' id='<? echo $winId; ?>-fd10' data-type='1' value='' class='control1'
                                                              style='width:100%;'></input></td>
                    ";
                    n+="
                    <td>Max-Wert (leer=automatisch)<br><input type='text' id='<? echo $winId; ?>-fd11' data-type='1' value='' class='control1'
                                                              style='width:100%;'></input></td>
                    ";
                    n+="
                    <td>Gesamtintervall optimieren<br>
                        <div id='<? echo $winId; ?>-fd12' data-type='6' data-value='<? echo $fd[12]; ?>' data-list='0|nein;1|ja (Algorithmus 1);'
                             class='control6' style='width:100%;'>&nbsp;
                        </div>
                    </td>
                    ";
                    n+="
                    <td>Y-Intervalle (0=automatisch)<br><input type='text' id='<? echo $winId; ?>-fd13' data-type='1' value='<? echo $fd[13]; ?>'
                                                               class='control1' style='width:100%;'></input></td>
                    ";
                    n+="
                </tr>
                ";

                <? if ($fd[1] > 0) { ?>
                    n+="
                    <tr>
                        <td colspan='4' class='formSubTitel'>Datenquellen
                            <hr>
                        </td>
                    </tr>";
                         n+="
                    <tr>
                        <td colspan='4'>
                            <div id='<? echo $winId; ?>-list1' data-type='1008' data-value='<? echo $fd[1]; ?>' data-itemid='0' class='controlList'
                                 style='height:200px;'>&nbsp;
                            </div>
                        </td>
                    </tr>";
                <? } ?>
                n+="
            </table>
            ";
            n+="
        </div>";
        document.getElementById("<? echo $winId; ?>-edit").innerHTML=n;

        document.getElementById("<? echo $winId; ?>-fd3").value='<? ajaxValue($fd[3]); ?>';
        document.getElementById("<? echo $winId; ?>-fd4").value='<? ajaxValue($fd[4]); ?>';
        document.getElementById("<? echo $winId; ?>-fd5").value='<? ajaxValue($fd[5]); ?>';
        document.getElementById("<? echo $winId; ?>-fd6").value='<? ajaxValue($fd[6]); ?>';
        document.getElementById("<? echo $winId; ?>-fd9").value='<? ajaxValue($fd[9]); ?>';
        document.getElementById("<? echo $winId; ?>-fd10").value='<? ajaxValue($fd[10]); ?>';
        document.getElementById("<? echo $winId; ?>-fd11").value='<? ajaxValue($fd[11]); ?>';
    <? }
    if ($db == 'editEmail') {
        $ss1 = sql_call("SELECT * FROM edomiProject.editEmail WHERE (id=" . $phpdataArr[1] . ")");
        if ($n = sql_result($ss1)) {
            $fd[0] = $db;
            $fd[1] = $n['id'];
            $fd[2] = $n['folderid'];
            $fd[3] = $n['name'];
            $fd[4] = $n['mailaddr'];
            $fd[5] = $n['subject'];
            $fd[6] = $n['body'];
        } else {
            $fd[0] = $db;
            $fd[1] = -1;
            $fd[2] = $phpdataArr[0];
            $fd[3] = '';
            $fd[4] = '';
            $fd[5] = '';
            $fd[6] = '';
        }
        editItem_setMeta('Email', $fd[1], $fd[2], dbRoot_getRootId($fd[2])); ?>
        n+="
        <div class='appMenu'>";
            n+="
            <div class='cmdButton cmdButtonL' onClick='app1000_editCancel(\"<? echo $winId; ?>\");'>Abbrechen</div>
            ";
            n+="
            <div class='cmdButton cmdButtonR'
                 onClick='ajax(\"saveItem\",\"<? echo $appId; ?>\",\"<? echo $winId; ?>\",\"<? echo $data; ?>\",controlGetFormData(\"<? echo $winId; ?>-form1\"));'>
                <b>Übernehmen</b></div>
            ";
            n+="
        </div>";
        n+="
        <div id='<? echo $winId; ?>-form1' class='appContentBlank'>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd0' data-type='1' value='<? echo $fd[0]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd1' data-type='1' value='<? echo $fd[1]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd2' data-type='1' value='<? echo $fd[2]; ?>'></input>";
            n+="
            <table width='100%' border='0' cellpadding='5' cellspacing='0'>";
                n+="
                <tr>
                    <td>"+app1000_editpath+"</td>
                </tr>
                ";
                n+="
                <tr>
                    <td>Name<br><input type='text' id='<? echo $winId; ?>-fd3' data-type='1' value='' class='control1' autofocus style='width:100%;'></input>
                    </td>
                </tr>
                ";
                n+="
                <tr>
                    <td>Empfänger (leer=Defaultadresse)<br><input type='text' id='<? echo $winId; ?>-fd4' data-type='1' value='' class='control1'
                                                                  style='width:100%;'></input></td>
                </tr>
                ";
                n+="
                <tr>
                    <td>Betreff<br><input type='text' id='<? echo $winId; ?>-fd5' data-type='1' value='' class='control1' style='width:100%;'></input></td>
                </tr>
                ";
                n+="
                <tr>
                    <td>Inhalt<br><textarea id='<? echo $winId; ?>-fd6' data-type='1' maxlength='10000' rows='20' wrap='soft' class='control1'
                                            onkeydown='if (event.keyCode==9) {appAll_enableTabKey(this);}'
                                            style='width:100%; height:200px; resize:none; tab-size:4;'></textarea></td>
                </tr>
                ";
                n+="
            </table>
            ";
            n+="
        </div>";
        document.getElementById("<? echo $winId; ?>-edit").innerHTML=n;

        document.getElementById("<? echo $winId; ?>-fd3").value='<? ajaxValue($fd[3]); ?>';
        document.getElementById("<? echo $winId; ?>-fd4").value='<? ajaxValue($fd[4]); ?>';
        document.getElementById("<? echo $winId; ?>-fd5").value='<? ajaxValue($fd[5]); ?>';
        document.getElementById("<? echo $winId; ?>-fd6").value='<? ajaxValue($fd[6]); ?>';
    <? }
    if ($db == 'editHttpKo') {
        $ss1 = sql_call("SELECT * FROM edomiProject.editHttpKo WHERE (id=" . $phpdataArr[1] . ")");
        if ($n = sql_result($ss1)) {
            $fd[0] = $db;
            $fd[1] = $n['id'];
            $fd[2] = $n['folderid'];
            $fd[3] = $n['name'];
            $fd[4] = $n['gaid'];
        } else {
            $fd[0] = $db;
            $fd[1] = -1;
            $fd[2] = $phpdataArr[0];
            $fd[3] = '';
            $fd[4] = 0;
        }
        editItem_setMeta('Fernzugriff', $fd[1], $fd[2], '140'); ?>
        n+="
        <div class='appMenu'>";
            n+="
            <div class='cmdButton cmdButtonL' onClick='app1000_editCancel(\"<? echo $winId; ?>\");'>Abbrechen</div>
            ";
            n+="
            <div class='cmdButton cmdButtonR'
                 onClick='ajax(\"saveItem\",\"<? echo $appId; ?>\",\"<? echo $winId; ?>\",\"<? echo $data; ?>\",controlGetFormData(\"<? echo $winId; ?>-form1\"));'>
                <b>Übernehmen</b></div>
            ";
            n+="
        </div>";
        n+="
        <div id='<? echo $winId; ?>-form1' class='appContentBlank'>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd0' data-type='1' value='<? echo $fd[0]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd1' data-type='1' value='<? echo $fd[1]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd2' data-type='1' value='<? echo $fd[2]; ?>'></input>";
            n+="
            <table width='100%' border='0' cellpadding='5' cellspacing='0'>";
                n+="
                <tr>
                    <td>"+app1000_editpath+"</td>
                </tr>
                ";
                n+="
                <tr>
                    <td>Name<br><input type='text' id='<? echo $winId; ?>-fd3' data-type='1' value='' class='control1' autofocus style='width:100%;'></input>
                    </td>
                </tr>
                ";
                n+="
                <tr>
                    <td>KO<br>
                        <div id='<? echo $winId; ?>-fd4' data-type='1000' data-root='30' data-value='<? echo $fd[4]; ?>' data-options='typ=1;reset=0'
                             class='control10' style='width:100%;'>&nbsp;
                        </div>
                    </td>
                </tr>
                ";
                n+="
            </table>
            ";
            n+="
        </div>";
        document.getElementById("<? echo $winId; ?>-edit").innerHTML=n;

        document.getElementById("<? echo $winId; ?>-fd3").value='<? ajaxValue($fd[3]); ?>';
    <? }
    if ($db == 'editPhoneBook') {
        $ss1 = sql_call("SELECT * FROM edomiProject.editPhoneBook WHERE (id=" . $phpdataArr[1] . ")");
        if ($n = sql_result($ss1)) {
            $fd[0] = $db;
            $fd[1] = $n['id'];
            $fd[2] = $n['folderid'];
            $fd[3] = $n['name'];
            $fd[4] = $n['phone1'];
            $fd[5] = $n['phone2'];
        } else {
            $fd[0] = $db;
            $fd[1] = -1;
            $fd[2] = $phpdataArr[0];
            $fd[3] = '';
            $fd[4] = '';
            $fd[5] = '';
        }
        editItem_setMeta('Telefonbucheintrag', $fd[1], $fd[2], '125'); ?>
        n+="
        <div class='appMenu'>";
            n+="
            <div class='cmdButton cmdButtonL' onClick='app1000_editCancel(\"<? echo $winId; ?>\");'>Abbrechen</div>
            ";
            n+="
            <div class='cmdButton cmdButtonR'
                 onClick='ajax(\"saveItem\",\"<? echo $appId; ?>\",\"<? echo $winId; ?>\",\"<? echo $data; ?>\",controlGetFormData(\"<? echo $winId; ?>-form1\"));'>
                <b>Übernehmen</b></div>
            ";
            n+="
        </div>";
        n+="
        <div id='<? echo $winId; ?>-form1' class='appContentBlank'>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd0' data-type='1' value='<? echo $fd[0]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd1' data-type='1' value='<? echo $fd[1]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd2' data-type='1' value='<? echo $fd[2]; ?>'></input>";
            n+="
            <table width='100%' border='0' cellpadding='5' cellspacing='0'>";
                n+="
                <colgroup>";
                    n+="
                    <col width='30%'>
                    ";
                    n+="
                    <col width='70%'>
                    ";
                    n+="
                </colgroup>
                ";
                n+="
                <tr>
                    <td colspan='2'>"+app1000_editpath+"</td>
                </tr>
                ";
                n+="
                <tr>
                    <td colspan='2'>Name<br><input type='text' id='<? echo $winId; ?>-fd3' data-type='1' value='' class='control1' autofocus
                                                   style='width:100%;'></input></td>
                </tr>
                ";
                n+="
                <tr>";
                    n+="
                    <td>Vorwahl<br><input type='text' id='<? echo $winId; ?>-fd4' data-type='1' value='' class='control1' style='width:100%;'></input></td>
                    ";
                    n+="
                    <td>Rufnummer<br><input type='text' id='<? echo $winId; ?>-fd5' data-type='1' value='' class='control1' style='width:100%;'></input></td>
                    ";
                    n+="
                </tr>
                ";
                n+="
            </table>
            ";
            n+="
        </div>";
        document.getElementById("<? echo $winId; ?>-edit").innerHTML=n;

        document.getElementById("<? echo $winId; ?>-fd3").value='<? ajaxValue($fd[3]); ?>';
        document.getElementById("<? echo $winId; ?>-fd4").value='<? ajaxValue($fd[4]); ?>';
        document.getElementById("<? echo $winId; ?>-fd5").value='<? ajaxValue($fd[5]); ?>';
    <? }
    if ($db == 'editPhoneCall') {
        $ss1 = sql_call("SELECT * FROM edomiProject.editPhoneCall WHERE (id=" . $phpdataArr[1] . ")");
        if ($n = sql_result($ss1)) {
            $fd[0] = $db;
            $fd[1] = $n['id'];
            $fd[2] = $n['folderid'];
            $fd[3] = $n['name'];
            $fd[4] = $n['phoneid1'];
            $fd[5] = $n['phoneid2'];
            $fd[6] = $n['gaid1'];
            $fd[7] = $n['gaid2'];
            $fd[8] = $n['gaid3'];
            $fd[9] = $n['typ'];
        } else {
            $fd[0] = $db;
            $fd[1] = -1;
            $fd[2] = $phpdataArr[0];
            $fd[3] = '';
            $fd[4] = 0;
            $fd[5] = 0;
            $fd[6] = 0;
            $fd[7] = 0;
            $fd[8] = 0;
            $fd[9] = 0;
        }
        editItem_setMeta('Anruftrigger', $fd[1], $fd[2], '126'); ?>
        n+="
        <div class='appMenu'>";
            n+="
            <div class='cmdButton cmdButtonL' onClick='app1000_editCancel(\"<? echo $winId; ?>\");'>Abbrechen</div>
            ";
            n+="
            <div class='cmdButton cmdButtonR'
                 onClick='ajax(\"saveItem\",\"<? echo $appId; ?>\",\"<? echo $winId; ?>\",\"<? echo $data; ?>\",controlGetFormData(\"<? echo $winId; ?>-form1\"));'>
                <b>Übernehmen</b></div>
            ";
            n+="
        </div>";
        n+="
        <div id='<? echo $winId; ?>-form1' class='appContentBlank'>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd0' data-type='1' value='<? echo $fd[0]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd1' data-type='1' value='<? echo $fd[1]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd2' data-type='1' value='<? echo $fd[2]; ?>'></input>";
            n+="
            <table width='100%' border='0' cellpadding='5' cellspacing='0'>";
                n+="
                <colgroup>";
                    n+="
                    <col width='70%'>
                    ";
                    n+="
                    <col width='30%'>
                    ";
                    n+="
                </colgroup>
                ";
                n+="
                <tr>
                    <td colspan='2'>"+app1000_editpath+"</td>
                </tr>
                ";
                n+="
                <tr>";
                    n+="
                    <td>Name<br><input type='text' id='<? echo $winId; ?>-fd3' data-type='1' value='' class='control1' autofocus style='width:100%;'></input>
                    </td>
                    ";
                    n+="
                    <td>Anruftyp<br>
                        <div id='<? echo $winId; ?>-fd9' data-type='6' data-value='<? echo $fd[9]; ?>' data-list='0|Eingehender Anruf;1|Ausgehender Anruf;'
                             class='control6' style='width:100%;'>&nbsp;
                        </div>
                    </td>
                    ";
                    n+="
                </tr>
                ";
                n+="
                <tr>
                    <td colspan='2'>Quell-Rufnummer (leer=alle Nummern)<br>
                        <div id='<? echo $winId; ?>-fd4' data-type='1000' data-root='125' data-value='<? echo $fd[4]; ?>' data-options='typ=1' class='control10'
                             style='width:100%;'>&nbsp;
                        </div>
                    </td>
                </tr>
                ";
                n+="
                <tr>
                    <td colspan='2'>Ziel-Rufnummer (leer=alle Nummern)<br>
                        <div id='<? echo $winId; ?>-fd5' data-type='1000' data-root='125' data-value='<? echo $fd[5]; ?>' data-options='typ=1' class='control10'
                             style='width:100%;'>&nbsp;
                        </div>
                    </td>
                </tr>
                ";

                n+="
                <tr>
                    <td colspan='2' class='formSubTitel'>Kommunikationsobjekte
                        <hr>
                    </td>
                </tr>
                ";
                n+="
                <tr>
                    <td colspan='2'>KO: Status (wird bei Anruf auf 1 gesetzt)<br>
                        <div id='<? echo $winId; ?>-fd6' data-type='1000' data-root='30' data-value='<? echo $fd[6]; ?>' data-options='typ=1' class='control10'
                             style='width:100%;'>&nbsp;
                        </div>
                    </td>
                </tr>
                ";
                n+="
                <tr>
                    <td colspan='2'>KO: Quell-Rufnummer/Name<br>
                        <div id='<? echo $winId; ?>-fd7' data-type='1000' data-root='30' data-value='<? echo $fd[7]; ?>' data-options='typ=1' class='control10'
                             style='width:100%;'>&nbsp;
                        </div>
                    </td>
                </tr>
                ";
                n+="
                <tr>
                    <td colspan='2'>KO: Ziel-Rufnummer/Name<br>
                        <div id='<? echo $winId; ?>-fd8' data-type='1000' data-root='30' data-value='<? echo $fd[8]; ?>' data-options='typ=1' class='control10'
                             style='width:100%;'>&nbsp;
                        </div>
                    </td>
                </tr>
                ";
                n+="
            </table>
            ";
            n+="
        </div>";
        document.getElementById("<? echo $winId; ?>-edit").innerHTML=n;

        document.getElementById("<? echo $winId; ?>-fd3").value='<? ajaxValue($fd[3]); ?>';
    <? }
    if ($db == 'editArchivPhone') {
        $ss1 = sql_call("SELECT * FROM edomiProject.editArchivPhone WHERE (id=" . $phpdataArr[1] . ")");
        if ($n = sql_result($ss1)) {
            $fd[0] = $db;
            $fd[1] = $n['id'];
            $fd[2] = $n['folderid'];
            $fd[3] = $n['name'];
            $fd[4] = $n['keep'];
            $fd[5] = $n['outgaid'];
        } else {
            $fd[0] = $db;
            $fd[1] = -1;
            $fd[2] = $phpdataArr[0];
            $fd[3] = '';
            $fd[4] = 31;
            $fd[5] = 0;
        }
        editItem_setMeta('Anrufarchiv', $fd[1], $fd[2], '127'); ?>
        n+="
        <div class='appMenu'>";
            n+="
            <div class='cmdButton cmdButtonL' onClick='app1000_editCancel(\"<? echo $winId; ?>\");'>Abbrechen</div>
            ";
            n+="
            <div class='cmdButton cmdButtonR'
                 onClick='ajax(\"saveItem\",\"<? echo $appId; ?>\",\"<? echo $winId; ?>\",\"<? echo $data; ?>\",controlGetFormData(\"<? echo $winId; ?>-form1\"));'>
                <b>Übernehmen</b></div>
            ";
            n+="
        </div>";
        n+="
        <div id='<? echo $winId; ?>-form1' class='appContentBlank'>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd0' data-type='1' value='<? echo $fd[0]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd1' data-type='1' value='<? echo $fd[1]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd2' data-type='1' value='<? echo $fd[2]; ?>'></input>";
            n+="
            <table width='100%' border='0' cellpadding='5' cellspacing='0'>";
                n+="
                <tr>
                    <td colspan='2'>"+app1000_editpath+"</td>
                </tr>
                ";
                n+="
                <tr>
                    <td colspan='2'>Name<br><input type='text' id='<? echo $winId; ?>-fd3' data-type='1' value='' class='control1' autofocus
                                                   style='width:100%;'></input></td>
                </tr>
                ";
                n+="
                <tr>
                    <td colspan='2'>KO: Status (erhält bei jeder Änderung die Gesamtanzahl der Einträge)<br>
                        <div id='<? echo $winId; ?>-fd5' data-type='1000' data-root='30' data-value='<? echo $fd[5]; ?>' data-options='typ=1' class='control10'
                             style='width:100%;'>&nbsp;
                        </div>
                    </td>
                </tr>
                ";
                n+="
                <tr>";
                    n+="
                    <td>Speicherdauer<br>
                        <div id='<? echo $winId; ?>-fd4' data-type='6' data-value='<? echo $fd[4]; ?>'
                             data-list='0|unendlich;1|1 Tag;2|2 Tage;3|3 Tage;7|1 Woche;14|2 Wochen;31|1 Monat;90|3 Monate;180|6 Monate;365|1 Jahr;730|2 Jahre;1095|3 Jahre;'
                             class='control6' style='width:100%;'>&nbsp;
                        </div>
                    </td>
                    ";
                    n+="
                    <td>&nbsp;</td>
                    ";
                    n+="
                </tr>
                ";
                n+="
            </table>
            ";
            n+="
        </div>";
        document.getElementById("<? echo $winId; ?>-edit").innerHTML=n;

        document.getElementById("<? echo $winId; ?>-fd3").value='<? ajaxValue($fd[3]); ?>';
    <? }
    if ($db == 'editIr') {
        $ss1 = sql_call("SELECT * FROM edomiProject.editIr WHERE (id=" . $phpdataArr[1] . ")");
        if ($n = sql_result($ss1)) {
            $fd[0] = $db;
            $fd[1] = $n['id'];
            $fd[2] = $n['folderid'];
            $fd[3] = $n['name'];
            $fd[4] = $n['data'];
        } else {
            $fd[0] = $db;
            $fd[1] = -1;
            $fd[2] = $phpdataArr[0];
            $fd[3] = '';
            $fd[4] = '';
        }
        editItem_setMeta('IR-Befehl', $fd[1], $fd[2], '75'); ?>
        n+="
        <div class='appMenu'>";
            n+="
            <div class='cmdButton cmdButtonL' onClick='app1000_editCancel(\"<? echo $winId; ?>\");'>Abbrechen</div>
            ";
            n+="
            <div class='cmdButton cmdButtonR'
                 onClick='ajax(\"saveItem\",\"<? echo $appId; ?>\",\"<? echo $winId; ?>\",\"<? echo $data; ?>\",controlGetFormData(\"<? echo $winId; ?>-form1\"));'>
                <b>Übernehmen</b></div>
            ";
            <? if (global_irGatewayActive) { ?>
                n+="
                <div class='cmdButton'
                     onClick='ajax(\"irtransReset\",\"1015\",\"<? echo $winId; ?>\",\"<? echo $data; ?>\",controlGetFormData(\"<? echo $winId; ?>-form1\"));'
                     style='float:right; margin-left:5px;'>IRtrans neustarten
                </div>";
                      n+="
                <div class='cmdButton cmdButtonR'
                     onClick='ajax(\"irtransSend\",\"1015\",\"<? echo $winId; ?>\",\"<? echo $data; ?>\",controlGetFormData(\"<? echo $winId; ?>-form1\"));'
                     style='float:right;'>Senden
                </div>";
                      n+="
                <div class='cmdButton cmdButtonL'
                     onClick='ajax(\"irtransLearn\",\"1015\",\"<? echo $winId; ?>\",\"<? echo $data; ?>\",controlGetFormData(\"<? echo $winId; ?>-form1\"));'
                     style='float:right;'><b>Lernen</b></div>";
            <? } ?>
            n+="
        </div>";
        n+="
        <div id='<? echo $winId; ?>-form1' class='appContentBlank'>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd0' data-type='1' value='<? echo $fd[0]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd1' data-type='1' value='<? echo $fd[1]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd2' data-type='1' value='<? echo $fd[2]; ?>'></input>";
            n+="
            <table width='100%' border='0' cellpadding='5' cellspacing='0'>";
                n+="
                <colgroup>";
                    n+="
                    <col width='70%'>
                    ";
                    n+="
                    <col width='30%'>
                    ";
                    n+="
                </colgroup>
                ";
                n+="
                <tr>
                    <td colspan='2'>"+app1000_editpath+"</td>
                </tr>
                ";
                n+="
                <tr>
                    <td colspan='2'>Name<br><input type='text' id='<? echo $winId; ?>-fd3' data-type='1' value='' class='control1' autofocus
                                                   style='width:100%;'></input></td>
                </tr>
                ";
                n+="
                <tr>
                    <td colspan='2'>IR-Code<br><textarea id='<? echo $winId; ?>-fd4' data-type='1' maxlength='10000' rows='13' wrap='soft' class='control1'
                                                         style='width:100%; height:240px; resize:none;'></textarea></td>
                </tr>
                ";
                <? if (global_irGatewayActive) { ?>
                    n+="
                    <tr>
                        <td colspan='2' class='formSubTitel'>IR-Befehl anlernen
                            <hr>
                        </td>
                    </tr>";
                         n+="
                    <tr valign='top'>";
                        n+="
                        <td>";
                            n+="
                            <table width='100%' height='170' border='0' cellpadding='5' cellspacing='0' style='border:1px solid #c0c0c0;'>";
                                n+="
                                <tr>
                                    <td align='center' valign='middle'>
                                        <div id='<? echo $winId; ?>-irinfo'><span style='color:#909090'>Statusanzeige</span></div>
                                    </td>
                                </tr>
                                ";
                                n+="
                            </table>
                            ";
                            n+="
                        </td>
                        ";
                        n+="
                        <td>";
                            n+="
                            <div id='<? echo $winId; ?>-fd6' data-type='5' data-value='0' class='control5' style='width:100%; margin-bottom:0;'>Long-IR-Code
                            </div>
                            <br>";
                            n+="
                            <div id='<? echo $winId; ?>-fd7' data-type='5' data-value='0' class='control5' style='width:100%; margin-bottom:0;'>Repeat-IR-Code
                            </div>
                            <br>";
                            n+="
                            <div id='<? echo $winId; ?>-fd8' data-type='5' data-value='0' class='control5' style='width:100%; margin-bottom:0;'>
                                RAW/Binär-Modus
                            </div>
                            ";
                            n+="
                        </td>
                        ";
                        n+="
                    </tr>";
                <? } ?>
                n+="
            </table>
            ";
            n+="
        </div>";
        document.getElementById("<? echo $winId; ?>-edit").innerHTML=n;

        document.getElementById("<? echo $winId; ?>-fd3").value='<? ajaxValue($fd[3]); ?>';
        document.getElementById("<? echo $winId; ?>-fd4").value='<? ajaxValue($fd[4]); ?>';
    <? }
    if ($db == 'editAws') { ?>
        n+="
        <div class='appMenu'>";
            <? $ss1 = sql_call("SELECT * FROM edomiProject.editAws WHERE (id=" . $phpdataArr[1] . ")");
            if ($n = sql_result($ss1)) {
                $fd[0] = $db;
                $fd[1] = $n['id'];
                $fd[2] = $n['folderid'];
                $fd[3] = $n['name'];
                $fd[4] = $n['gaid']; ?>
                n+="
                <div class='cmdButton cmdButtonL cmdButtonDisabled'>Abbrechen</div>";
                                                                                   n+="
                <div class='cmdButton cmdButtonR'
                     onClick='ajax(\"saveItem\",\"<? echo $appId; ?>\",\"<? echo $winId; ?>\",\"<? echo $data; ?>\",controlGetFormData(\"<? echo $winId; ?>-form1\"));'>
                    <b>Übernehmen</b></div>";
            <? } else {
                $fd[0] = $db;
                $fd[1] = -1;
                $fd[2] = $phpdataArr[0];
                $fd[3] = '';
                $fd[4] = 0; ?>
                n+="
                <div class='cmdButton cmdButtonL' onClick='app1000_editCancel(\"<? echo $winId; ?>\");'>Abbrechen</div>";
                                                                                                                       n+="
                <div class='cmdButton cmdButtonR'
                     onClick='ajax(\"saveItem\",\"<? echo $appId; ?>\",\"<? echo $winId; ?>\",\"<? echo $data; ?>\",controlGetFormData(\"<? echo $winId; ?>-form1\"));'>
                    <b>Anwesenheitssimulation erstellen</b></div>";
            <? }
            editItem_setMeta('Anwesenheitssimulation', $fd[1], $fd[2], '110'); ?>
            n+="
        </div>";
        n+="
        <div id='<? echo $winId; ?>-form1' class='appContentBlank'>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd0' data-type='1' value='<? echo $fd[0]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd1' data-type='1' value='<? echo $fd[1]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd2' data-type='1' value='<? echo $fd[2]; ?>'></input>";
            n+="
            <table width='100%' border='0' cellpadding='5' cellspacing='0'>";
                n+="
                <tr>
                    <td>"+app1000_editpath+"</td>
                </tr>
                ";
                n+="
                <tr>
                    <td>Name<br><input type='text' id='<? echo $winId; ?>-fd3' data-type='1' value='' class='control1' autofocus style='width:100%;'></input>
                    </td>
                </tr>
                ";
                n+="
                <tr>
                    <td>KO: Steuerung der Anwesenheitssimulation (0=Deaktiviert, 1=Abspielen, 2=Aufnehmen)<br>
                        <div id='<? echo $winId; ?>-fd4' data-type='1000' data-root='30' data-value='<? echo $fd[4]; ?>' data-options='typ=1;reset=0'
                             class='control10' style='width:100%;'>&nbsp;
                        </div>
                    </td>
                </tr>
                ";
                <? if ($fd[1] > 0) { ?>
                    n+="
                    <tr>
                        <td class='formSubTitel'>Kommunikationsobjekte
                            <hr>
                        </td>
                    </tr>";
                         n+="
                    <tr>
                        <td>
                            <div id='<? echo $winId; ?>-list1' data-type='1009' data-value='<? echo $fd[1]; ?>' data-itemid='0' class='controlList'
                                 style='height:400px;'>&nbsp;
                            </div>
                        </td>
                    </tr>";
                <? } ?>
                n+="
            </table>
            ";
            n+="
        </div>";
        document.getElementById("<? echo $winId; ?>-edit").innerHTML=n;

        document.getElementById("<? echo $winId; ?>-fd3").value='<? ajaxValue($fd[3]); ?>';
    <? }
    if ($db == 'editTimer') { ?>
        n+="
        <div class='appMenu'>";
            <? $ss1 = sql_call("SELECT * FROM edomiProject.editTimer WHERE (id=" . $phpdataArr[1] . ")");
            if ($n = sql_result($ss1)) {
                $fd[0] = $db;
                $fd[1] = $n['id'];
                $fd[2] = $n['folderid'];
                $fd[3] = $n['name'];
                $fd[4] = $n['gaid'];
                $fd[5] = $n['gaid2']; ?>
                n+="
                <div class='cmdButton cmdButtonL cmdButtonDisabled'>Abbrechen</div>";
                                                                                   n+="
                <div class='cmdButton cmdButtonR'
                     onClick='ajax(\"saveItem\",\"<? echo $appId; ?>\",\"<? echo $winId; ?>\",\"<? echo $data; ?>\",controlGetFormData(\"<? echo $winId; ?>-form1\"));'>
                    <b>Übernehmen</b></div>";
                                           n+="
                <div class='cmdButton'
                     onClick='openWindow(1021,\"<? echo $data; ?>\",\"<? echo date('Y'); ?><? echo AJAX_SEPARATOR1; ?><? echo $fd[1]; ?><? echo AJAX_SEPARATOR1; ?>1\");'
                     style='float:right;'>Vorschau
                </div>";
            <? } else {
                $fd[0] = $db;
                $fd[1] = -1;
                $fd[2] = $phpdataArr[0];
                $fd[3] = '';
                $fd[4] = 0;
                $fd[5] = 0; ?>
                n+="
                <div class='cmdButton cmdButtonL' onClick='app1000_editCancel(\"<? echo $winId; ?>\");'>Abbrechen</div>";
                                                                                                                       n+="
                <div class='cmdButton cmdButtonR'
                     onClick='ajax(\"saveItem\",\"<? echo $appId; ?>\",\"<? echo $winId; ?>\",\"<? echo $data; ?>\",controlGetFormData(\"<? echo $winId; ?>-form1\"));'>
                    <b>Zeitschaltuhr erstellen</b></div>";
            <? }
            editItem_setMeta('Zeitschaltuhr', $fd[1], $fd[2], '100'); ?>
            n+="
        </div>";
        n+="
        <div id='<? echo $winId; ?>-form1' class='appContentBlank'>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd0' data-type='1' value='<? echo $fd[0]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd1' data-type='1' value='<? echo $fd[1]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd2' data-type='1' value='<? echo $fd[2]; ?>'></input>";
            n+="
            <table width='100%' border='0' cellpadding='5' cellspacing='0'>";
                n+="
                <colgroup>";
                    n+="
                    <col width='60%'>
                    ";
                    n+="
                    <col width='40%'>
                    ";
                    n+="
                </colgroup>
                ";
                n+="
                <tr>
                    <td colspan='2'>"+app1000_editpath+"</td>
                </tr>
                ";
                n+="
                <tr>
                    <td colspan='2'>Name<br><input type='text' id='<? echo $winId; ?>-fd3' data-type='1' value='' class='control1' autofocus
                                                   style='width:100%;'></input></td>
                </tr>
                ";
                n+="
                <tr>
                    <td colspan='2'>KO: Steuerung der Zeitschaltuhr (0=Aus, 1=Ein)<br>
                        <div id='<? echo $winId; ?>-fd4' data-type='1000' data-root='30' data-value='<? echo $fd[4]; ?>' data-options='typ=1;reset=0'
                             class='control10' style='width:100%;'>&nbsp;
                        </div>
                    </td>
                </tr>
                ";
                n+="
                <tr>
                    <td colspan='2'>KO: Zusatzbedingung<br>
                        <div id='<? echo $winId; ?>-fd5' data-type='1000' data-root='30' data-value='<? echo $fd[5]; ?>' data-options='typ=1;reset=1'
                             class='control10' style='width:100%;'>&nbsp;
                        </div>
                    </td>
                </tr>
                ";
                <? if ($fd[1] > 0) { ?>
                    n+="
                    <tr>";
                        n+="
                        <td class='formSubTitel'>Schaltzeiten
                            <hr>
                        </td>
                        ";
                        n+="
                        <td class='formSubTitel'>Makro-Vorgaben für Visualisierungen
                            <hr>
                        </td>
                        ";
                        n+="
                    </tr>";
                         n+="
                    <tr>";
                        n+="
                        <td>
                            <div id='<? echo $winId; ?>-list1' data-type='1019' data-value='<? echo $fd[1]; ?>' data-itemid='0' data-db='editTimerData'
                                 class='controlList' style='height:350px;'>&nbsp;
                            </div>
                        </td>
                        ";
                        n+="
                        <td>
                            <div id='<? echo $winId; ?>-list2' data-type='1023' data-value='<? echo $fd[1]; ?>' data-itemid='0' data-db='editTimerMacroList'
                                 class='controlList' style='height:350px;'>&nbsp;
                            </div>
                        </td>
                        ";
                        n+="
                    </tr>";
                <? } ?>
                n+="
            </table>
            ";
            n+="
        </div>";
        document.getElementById("<? echo $winId; ?>-edit").innerHTML=n;

        document.getElementById("<? echo $winId; ?>-fd3").value='<? ajaxValue($fd[3]); ?>';
    <? }
    if ($db == 'editAgenda') { ?>
        n+="
        <div class='appMenu'>";
            <? $ss1 = sql_call("SELECT * FROM edomiProject.editAgenda WHERE (id=" . $phpdataArr[1] . ")");
            if ($n = sql_result($ss1)) {
                $fd[0] = $db;
                $fd[1] = $n['id'];
                $fd[2] = $n['folderid'];
                $fd[3] = $n['name'];
                $fd[4] = $n['gaid'];
                $fd[5] = $n['gaid2']; ?>
                n+="
                <div class='cmdButton cmdButtonL cmdButtonDisabled'>Abbrechen</div>";
                                                                                   n+="
                <div class='cmdButton cmdButtonR'
                     onClick='ajax(\"saveItem\",\"<? echo $appId; ?>\",\"<? echo $winId; ?>\",\"<? echo $data; ?>\",controlGetFormData(\"<? echo $winId; ?>-form1\"));'>
                    <b>Übernehmen</b></div>";
                                           n+="
                <div class='cmdButton'
                     onClick='openWindow(1021,\"<? echo $data; ?>\",\"<? echo date('Y'); ?><? echo AJAX_SEPARATOR1; ?><? echo $fd[1]; ?><? echo AJAX_SEPARATOR1; ?>2\");'
                     style='float:right;'>Vorschau
                </div>";
            <? } else {
                $fd[0] = $db;
                $fd[1] = -1;
                $fd[2] = $phpdataArr[0];
                $fd[3] = '';
                $fd[4] = 0;
                $fd[5] = 0; ?>
                n+="
                <div class='cmdButton cmdButtonL' onClick='app1000_editCancel(\"<? echo $winId; ?>\");'>Abbrechen</div>";
                                                                                                                       n+="
                <div class='cmdButton cmdButtonR'
                     onClick='ajax(\"saveItem\",\"<? echo $appId; ?>\",\"<? echo $winId; ?>\",\"<? echo $data; ?>\",controlGetFormData(\"<? echo $winId; ?>-form1\"));'>
                    <b>Terminschaltuhr erstellen</b></div>";
            <? }
            editItem_setMeta('Terminschaltuhr', $fd[1], $fd[2], '101'); ?>
            n+="
        </div>";
        n+="
        <div id='<? echo $winId; ?>-form1' class='appContentBlank'>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd0' data-type='1' value='<? echo $fd[0]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd1' data-type='1' value='<? echo $fd[1]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd2' data-type='1' value='<? echo $fd[2]; ?>'></input>";
            n+="
            <table width='100%' border='0' cellpadding='5' cellspacing='0'>";
                n+="
                <colgroup>";
                    n+="
                    <col width='60%'>
                    ";
                    n+="
                    <col width='40%'>
                    ";
                    n+="
                </colgroup>
                ";
                n+="
                <tr>
                    <td colspan='2'>"+app1000_editpath+"</td>
                </tr>
                ";
                n+="
                <tr>
                    <td colspan='2'>Name<br><input type='text' id='<? echo $winId; ?>-fd3' data-type='1' value='' class='control1' autofocus
                                                   style='width:100%;'></input></td>
                </tr>
                ";
                n+="
                <tr>
                    <td colspan='2'>KO: Steuerung der Terminschaltuhr (0=Aus, 1=Ein)<br>
                        <div id='<? echo $winId; ?>-fd4' data-type='1000' data-root='30' data-value='<? echo $fd[4]; ?>' data-options='typ=1;reset=0'
                             class='control10' style='width:100%;'>&nbsp;
                        </div>
                    </td>
                </tr>
                ";
                n+="
                <tr>
                    <td colspan='2'>KO: Zusatzbedingung<br>
                        <div id='<? echo $winId; ?>-fd5' data-type='1000' data-root='30' data-value='<? echo $fd[5]; ?>' data-options='typ=1;reset=1'
                             class='control10' style='width:100%;'>&nbsp;
                        </div>
                    </td>
                </tr>
                ";
                <? if ($fd[1] > 0) { ?>
                    n+="
                    <tr>";
                        n+="
                        <td class='formSubTitel'>Termine
                            <hr>
                        </td>
                        ";
                        n+="
                        <td class='formSubTitel'>Makro-Vorgaben für Visualisierungen
                            <hr>
                        </td>
                        ";
                        n+="
                    </tr>";
                         n+="
                    <tr>";
                        n+="
                        <td>
                            <div id='<? echo $winId; ?>-list1' data-type='1022' data-value='<? echo $fd[1]; ?>' data-itemid='0' data-db='editAgendaData'
                                 class='controlList' style='height:350px;'>&nbsp;
                            </div>
                        </td>
                        ";
                        n+="
                        <td>
                            <div id='<? echo $winId; ?>-list2' data-type='1024' data-value='<? echo $fd[1]; ?>' data-itemid='0' data-db='editAgendaMacroList'
                                 class='controlList' style='height:350px;'>&nbsp;
                            </div>
                        </td>
                        ";
                        n+="
                    </tr>";
                <? } ?>
                n+="
            </table>
            ";
            n+="
        </div>";
        document.getElementById("<? echo $winId; ?>-edit").innerHTML=n;

        document.getElementById("<? echo $winId; ?>-fd3").value='<? ajaxValue($fd[3]); ?>';
    <? }
    if ($db == 'editSequence') { ?>
        n+="
        <div class='appMenu'>";
            <? $ss1 = sql_call("SELECT * FROM edomiProject.editSequence WHERE (id=" . $phpdataArr[1] . ")");
            if ($n = sql_result($ss1)) {
                $fd[0] = $db;
                $fd[1] = $n['id'];
                $fd[2] = $n['folderid'];
                $fd[3] = $n['name']; ?>
                n+="
                <div class='cmdButton cmdButtonL cmdButtonDisabled'>Abbrechen</div>";
                                                                                   n+="
                <div class='cmdButton cmdButtonR'
                     onClick='ajax(\"saveItem\",\"<? echo $appId; ?>\",\"<? echo $winId; ?>\",\"<? echo $data; ?>\",controlGetFormData(\"<? echo $winId; ?>-form1\"));'>
                    <b>Übernehmen</b></div>";
            <? } else {
                $fd[0] = $db;
                $fd[1] = -1;
                $fd[2] = $phpdataArr[0];
                $fd[3] = ''; ?>
                n+="
                <div class='cmdButton cmdButtonL' onClick='app1000_editCancel(\"<? echo $winId; ?>\");'>Abbrechen</div>";
                                                                                                                       n+="
                <div class='cmdButton cmdButtonR'
                     onClick='ajax(\"saveItem\",\"<? echo $appId; ?>\",\"<? echo $winId; ?>\",\"<? echo $data; ?>\",controlGetFormData(\"<? echo $winId; ?>-form1\"));'>
                    <b>Sequenz erstellen</b></div>";
            <? }
            editItem_setMeta('Sequenz', $fd[1], $fd[2], '90'); ?>
            n+="
        </div>";
        n+="
        <div id='<? echo $winId; ?>-form1' class='appContentBlank'>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd0' data-type='1' value='<? echo $fd[0]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd1' data-type='1' value='<? echo $fd[1]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd2' data-type='1' value='<? echo $fd[2]; ?>'></input>";
            n+="
            <table width='100%' border='0' cellpadding='5' cellspacing='0'>";
                n+="
                <tr>
                    <td>"+app1000_editpath+"</td>
                </tr>
                ";
                n+="
                <tr valign='top'>";
                    n+="
                    <td>Name<br><input type='text' id='<? echo $winId; ?>-fd3' data-type='1' value='' class='control1' autofocus style='width:100%;'></input>
                    </td>
                    ";
                    n+="
                </tr>
                ";
                <? if ($fd[1] > 0) { ?>
                    n+="
                    <tr>
                        <td class='formSubTitel'>Befehle
                            <hr>
                        </td>
                    </tr>";
                         n+="
                    <tr>
                        <td>
                            <div id='<? echo $winId; ?>-list1' data-type='1007' data-value='<? echo $fd[1]; ?>' data-itemid='0' data-db='editSequenceCmdList'
                                 class='controlList' style='height:440px;'>&nbsp;
                            </div>
                        </td>
                    </tr>";
                <? } ?>
                n+="
            </table>
            ";
            n+="
        </div>";
        document.getElementById("<? echo $winId; ?>-edit").innerHTML=n;

        document.getElementById("<? echo $winId; ?>-fd3").value='<? ajaxValue($fd[3]); ?>';
    <? }
    if ($db == 'editMacro') { ?>
        n+="
        <div class='appMenu'>";
            <? $ss1 = sql_call("SELECT * FROM edomiProject.editMacro WHERE (id=" . $phpdataArr[1] . ")");
            if ($n = sql_result($ss1)) {
                $fd[0] = $db;
                $fd[1] = $n['id'];
                $fd[2] = $n['folderid'];
                $fd[3] = $n['name']; ?>
                n+="
                <div class='cmdButton cmdButtonL cmdButtonDisabled'>Abbrechen</div>";
                                                                                   n+="
                <div class='cmdButton cmdButtonR'
                     onClick='ajax(\"saveItem\",\"<? echo $appId; ?>\",\"<? echo $winId; ?>\",\"<? echo $data; ?>\",controlGetFormData(\"<? echo $winId; ?>-form1\"));'>
                    <b>Übernehmen</b></div>";
            <? } else {
                $fd[0] = $db;
                $fd[1] = -1;
                $fd[2] = $phpdataArr[0];
                $fd[3] = ''; ?>
                n+="
                <div class='cmdButton cmdButtonL' onClick='app1000_editCancel(\"<? echo $winId; ?>\");'>Abbrechen</div>";
                                                                                                                       n+="
                <div class='cmdButton cmdButtonR'
                     onClick='ajax(\"saveItem\",\"<? echo $appId; ?>\",\"<? echo $winId; ?>\",\"<? echo $data; ?>\",controlGetFormData(\"<? echo $winId; ?>-form1\"));'>
                    <b>Makro erstellen</b></div>";
            <? }
            editItem_setMeta('Makro', $fd[1], $fd[2], '95'); ?>
            n+="
        </div>";
        n+="
        <div id='<? echo $winId; ?>-form1' class='appContentBlank'>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd0' data-type='1' value='<? echo $fd[0]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd1' data-type='1' value='<? echo $fd[1]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd2' data-type='1' value='<? echo $fd[2]; ?>'></input>";
            n+="
            <table width='100%' border='0' cellpadding='5' cellspacing='0'>";
                n+="
                <tr>
                    <td>"+app1000_editpath+"</td>
                </tr>
                ";
                n+="
                <tr valign='top'>";
                    n+="
                    <td>Name<br><input type='text' id='<? echo $winId; ?>-fd3' data-type='1' value='' class='control1' autofocus style='width:100%;'></input>
                    </td>
                    ";
                    n+="
                </tr>
                ";
                <? if ($fd[1] > 0) { ?>
                    n+="
                    <tr>
                        <td class='formSubTitel'>Befehle
                            <hr>
                        </td>
                    </tr>";
                         n+="
                    <tr>
                        <td>
                            <div id='<? echo $winId; ?>-list1' data-type='1007' data-value='<? echo $fd[1]; ?>' data-itemid='0' data-db='editMacroCmdList'
                                 class='controlList' style='height:440px;'>&nbsp;
                            </div>
                        </td>
                    </tr>";
                <? } ?>
                n+="
            </table>
            ";
            n+="
        </div>";
        document.getElementById("<? echo $winId; ?>-edit").innerHTML=n;

        document.getElementById("<? echo $winId; ?>-fd3").value='<? ajaxValue($fd[3]); ?>';
    <? }
    if ($db == 'editCam') {
        $ss1 = sql_call("SELECT * FROM edomiProject.editCam WHERE (id=" . $phpdataArr[1] . ")");
        if ($n = sql_result($ss1)) {
            $fd[0] = $db;
            $fd[1] = $n['id'];
            $fd[2] = $n['folderid'];
            $fd[3] = $n['name'];
            $fd[4] = $n['url'];
            $fd[5] = $n['mjpeg'];
            $fd[7] = $n['dvr'];
            $fd[8] = $n['dvrrate'];
            $fd[9] = $n['dvrkeep'];
            $fd[10] = $n['dvrgaid'];
            $fd[11] = $n['dvrgaid2'];
        } else {
            $fd[0] = $db;
            $fd[1] = -1;
            $fd[2] = $phpdataArr[0];
            $fd[3] = '';
            $fd[4] = '';
            $fd[5] = 1;
            $fd[7] = 0;
            $fd[8] = 5;
            $fd[9] = 0;
            $fd[10] = 0;
            $fd[11] = 0;
        }
        editItem_setMeta('Kameraeinstellung', $fd[1], $fd[2], '81'); ?>
        n+="
        <div class='appMenu'>";
            n+="
            <div class='cmdButton cmdButtonL' onClick='app1000_editCancel(\"<? echo $winId; ?>\");'>Abbrechen</div>
            ";
            n+="
            <div class='cmdButton cmdButtonR'
                 onClick='ajax(\"saveItem\",\"<? echo $appId; ?>\",\"<? echo $winId; ?>\",\"<? echo $data; ?>\",controlGetFormData(\"<? echo $winId; ?>-form1\"));'>
                <b>Übernehmen</b></div>
            ";
            n+="
            <div class='cmdButton'
                 onClick='ajax(\"camPreview\",\"<? echo $appId; ?>\",\"<? echo $winId; ?>\",\"<? echo $data; ?>\",controlGetFormData(\"<? echo $winId; ?>-form1\"));'
                 style='float:right;'>Vorschau
            </div>
            <br><br>";
            n+="
        </div>";
        n+="
        <div id='<? echo $winId; ?>-form1' class='appContentBlank'>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd0' data-type='1' value='<? echo $fd[0]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd1' data-type='1' value='<? echo $fd[1]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd2' data-type='1' value='<? echo $fd[2]; ?>'></input>";
            n+="
            <table width='100%' border='0' cellpadding='5' cellspacing='0'>";
                n+="
                <colgroup>";
                    n+="
                    <col width='70%'>
                    ";
                    n+="
                    <col width='30%'>
                    ";
                    n+="
                </colgroup>
                ";
                n+="
                <tr>
                    <td colspan='2'>"+app1000_editpath+"</td>
                </tr>
                ";
                n+="
                <tr>
                    <td colspan='2'>Name<br><input type='text' id='<? echo $winId; ?>-fd3' data-type='1' value='' class='control1' autofocus
                                                   style='width:100%;'></input></td>
                </tr>
                ";
                n+="
                <tr valign='bottom'>";
                    n+="
                    <td>URL<br><input type='text' id='<? echo $winId; ?>-fd4' data-type='1' value='' class='control1' style='width:100%;'></input></td>
                    ";
                    n+="
                    <td>Typ<br>
                        <div id='<? echo $winId; ?>-fd5' data-type='6' data-value='<? echo $fd[5]; ?>' data-list='0|JPG;1|MJPEG;' class='control6'
                             style='width:100%;'>&nbsp;
                        </div>
                    </td>
                    ";
                    n+="
                </tr>
                ";

                n+="
                <tr>";
                    n+="
                    <td colspan='2'>";
                        n+="
                        <div id='<? echo $winId; ?>-campreview'
                             style='position:relative; left:0; top:0; width:100%; height:250px; text-align:center; color:#ffff00; border:1px solid #c0c0c0; background-size:contain; background-repeat:no-repeat; box-sizing:border-box;'></div>
                        ";
                        n+="
                        <div id='<? echo $winId; ?>-campreviewinfo' style='padding-left:3px; padding-top:5px;'></div>
                        ";
                        n+="
                    </td>
                    ";
                    n+="
                </tr>
                ";

                n+="
                <tr>
                    <td colspan='2' class='formSubTitel'>Digitaler Videorekorder (DVR)
                        <hr>
                    </td>
                </tr>
                ";
                n+="
                <tr valign='top'>
                    <td colspan='3'>
                        <div id='<? echo $winId; ?>-fd7' data-type='6' data-value='<? echo $fd[7]; ?>'
                             data-list='0|deaktiviert;1|Aufzeichnung aktiviert|<? echo $winId; ?>-radiotr1;' class='control6' style='width:100%;'>&nbsp;
                        </div>
                    </td>
                </tr>
                ";
                n+="
                <tr valign='top' id='<? echo $winId; ?>-radiotr1'>";
                    n+="
                    <td colspan='2'>";
                        n+="
                        <table width='100%' border='0' cellpadding='0' cellspacing='0' style='table-layout:auto;'>";
                            n+="
                            <tr>";
                                n+="
                                <td width='49%'>Aufnahmeintervall (Einzelbild)<br>
                                    <div id='<? echo $winId; ?>-fd8' data-type='6' data-value='<? echo $fd[8]; ?>'
                                         data-list='1|jede Sekunde;2|alle 2 Sekunden;3|alle 3 Sekunden;4|alle 4 Sekunden;5|alle 5 Sekunden;10|alle 10 Sekunden;15|alle 15 Sekunden;20|alle 20 Sekunden;30|alle 30 Sekunden;60|alle 60 Sekunden;'
                                         class='control6' style='width:100%;'>&nbsp;
                                    </div>
                                </td>
                                ";
                                n+="
                                <td>&nbsp;</td>
                                ";
                                n+="
                                <td width='49%'>KO: Steuerung der Aufnahme (0=deaktiviert, &ge;1=aktiviert/Aufnahmeintervall)<br>
                                    <div id='<? echo $winId; ?>-fd10' data-type='1000' data-root='30' data-value='<? echo $fd[10]; ?>' data-options='typ=1'
                                         class='control10' style='width:100%;'>&nbsp;
                                    </div>
                                </td>
                                ";
                                n+="
                            </tr>
                            ";
                            n+="
                            <tr>";
                                n+="
                                <td>Speicherdauer<br>
                                    <div id='<? echo $winId; ?>-fd9' data-type='6' data-value='<? echo $fd[9]; ?>'
                                         data-list='0|unendlich;1|1 Tag;2|2 Tage;3|3 Tage;4|4 Tage;5|5 Tage;6|6 Tage;7|1 Woche;14|2 Wochen;21|3 Wochen;28|4 Wochen;'
                                         class='control6' style='width:100%;'>&nbsp;
                                    </div>
                                </td>
                                ";
                                n+="
                                <td>&nbsp;</td>
                                ";
                                n+="
                                <td>KO: Ereignis<br>
                                    <div id='<? echo $winId; ?>-fd11' data-type='1000' data-root='30' data-value='<? echo $fd[11]; ?>' data-options='typ=1'
                                         class='control10' style='width:100%;'>&nbsp;
                                    </div>
                                </td>
                                ";
                                n+="
                            </tr>
                            ";
                            n+="
                        </table>
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
        document.getElementById("<? echo $winId; ?>-edit").innerHTML=n;

        document.getElementById("<? echo $winId; ?>-fd3").value='<? ajaxValue($fd[3]); ?>';
        document.getElementById("<? echo $winId; ?>-fd4").value='<? ajaxValue($fd[4]); ?>';
    <? }
    if ($db == 'editCamView') {
        $ss1 = sql_call("SELECT * FROM edomiProject.editCamView WHERE (id=" . $phpdataArr[1] . ")");
        if ($n = sql_result($ss1)) {
            $fd[0] = $db;
            $fd[1] = $n['id'];
            $fd[2] = $n['folderid'];
            $fd[3] = $n['name'];
            $fd[4] = $n['camid'];
            $fd[5] = $n['srctyp'];
            $fd[10] = $n['zoom'];
            $fd[11] = $n['a1'];
            $fd[12] = $n['a2'];
            $fd[13] = $n['x'];
            $fd[14] = $n['y'];
            $fd[15] = $n['dstw'];
            $fd[16] = $n['dsth'];
            $fd[17] = $n['dsts'];
            $fd[18] = $n['srcr'];
            $fd[19] = $n['srcd'];
            $fd[20] = $n['srcs'];
        } else {
            $fd[0] = $db;
            $fd[1] = -1;
            $fd[2] = $phpdataArr[0];
            $fd[3] = '';
            $fd[4] = 0;
            $fd[5] = 0;
            $fd[10] = 250;
            $fd[11] = 0;
            $fd[12] = 0;
            $fd[13] = 0;
            $fd[14] = 0;
            $fd[15] = 4;
            $fd[16] = 3;
            $fd[17] = 0;
            $fd[18] = 0;
            $fd[19] = 0;
            $fd[20] = 50;
        }
        editItem_setMeta('Kameraansicht', $fd[1], $fd[2], '83'); ?>
        n+="
        <div class='appMenu'>";
            n+="
            <div class='cmdButton cmdButtonL' onClick='app1000_editCancel(\"<? echo $winId; ?>\");'>Abbrechen</div>
            ";
            n+="
            <div class='cmdButton cmdButtonR'
                 onClick='ajax(\"saveItem\",\"<? echo $appId; ?>\",\"<? echo $winId; ?>\",\"<? echo $data; ?>\",controlGetFormData(\"<? echo $winId; ?>-form1\"));'>
                <b>Übernehmen</b></div>
            ";
            n+="
            <div id='<? echo $winId; ?>-radio1' class='cmdButton' onClick='app1020_open(\"<? echo $winId; ?>\");' style='float:right;'><b>Einstellungen</b>
            </div>
            ";
            n+="
        </div>";
        n+="
        <div id='<? echo $winId; ?>-form1' class='appContentBlank'>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd0' data-type='1' value='<? echo $fd[0]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd1' data-type='1' value='<? echo $fd[1]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd2' data-type='1' value='<? echo $fd[2]; ?>'></input>";

            n+="<input type='hidden' id='<? echo $winId; ?>-fd10' data-type='1' value='<? echo $fd[10]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd11' data-type='1' value='<? echo $fd[11]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd12' data-type='1' value='<? echo $fd[12]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd13' data-type='1' value='<? echo $fd[13]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd14' data-type='1' value='<? echo $fd[14]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd15' data-type='1' value='<? echo $fd[15]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd16' data-type='1' value='<? echo $fd[16]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd17' data-type='1' value='<? echo $fd[17]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd18' data-type='1' value='<? echo $fd[18]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd19' data-type='1' value='<? echo $fd[19]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd20' data-type='1' value='<? echo $fd[20]; ?>'></input>";

            n+="
            <table width='100%' border='0' cellpadding='5' cellspacing='0'>";
                n+="
                <tr>
                    <td>"+app1000_editpath+"</td>
                </tr>
                ";
                n+="
                <tr>
                    <td>Name<br><input type='text' id='<? echo $winId; ?>-fd3' data-type='1' value='' class='control1' autofocus style='width:100%;'></input>
                    </td>
                </tr>
                ";
                n+="
                <tr>";
                    n+="
                    <td>Kamera<br>
                        <div id='<? echo $winId; ?>-fd4' data-type='1000' data-root='81' data-value='<? echo $fd[4]; ?>' data-options='typ=1' class='control10'
                             style='width:100%;'>&nbsp;
                        </div>
                    </td>
                    ";
                    n+="
                </tr>
                ";

                n+="
                <tr>
                    <td class='formSubTitel'>Bildbearbeitung
                        <hr>
                    </td>
                </tr>
                ";
                n+="
                <tr>
                    <td>
                        <div id='<? echo $winId; ?>-fd5' data-type='6' data-value=''
                             data-list='0|deaktiviert (Originalbild);1|Ausschnitt|<? echo $winId; ?>-radio1;2|360-Grad-Entzerrung (Kreis)|<? echo $winId; ?>-radio1;3|360-Grad-Entzerrung (Ellipse)|<? echo $winId; ?>-radio1;'
                             class='control6' style='width:100%;'>&nbsp;
                        </div>
                    </td>
                </tr>
                ";
                n+="
            </table>
            ";
            n+="
        </div>";
        document.getElementById("<? echo $winId; ?>-edit").innerHTML=n;

        document.getElementById("<? echo $winId; ?>-fd3").value='<? ajaxValue($fd[3]); ?>';
        document.getElementById("<? echo $winId; ?>-fd4").value='<? ajaxValue($fd[4]); ?>';
        document.getElementById("<? echo $winId; ?>-fd5").dataset.value='<? ajaxValue($fd[5]); ?>';
    <? }
    if ($db == 'editIp') {
        $ss1 = sql_call("SELECT * FROM edomiProject.editIp WHERE (id=" . $phpdataArr[1] . ")");
        if ($n = sql_result($ss1)) {
            $fd[0] = $db;
            $fd[1] = $n['id'];
            $fd[2] = $n['folderid'];
            $fd[3] = $n['name'];
            $fd[4] = $n['url'];
            $fd[5] = $n['iptyp'];
            $fd[6] = $n['data'];
            $fd[7] = $n['httperrlog'];
            $fd[8] = $n['udpraw'];
            $fd[9] = $n['outgaid'];
            $fd[10] = $n['outgaid2'];
            $fd[11] = $n['httptimeout'];
        } else {
            $fd[0] = $db;
            $fd[1] = -1;
            $fd[2] = $phpdataArr[0];
            $fd[3] = '';
            $fd[4] = '';
            $fd[5] = 1;
            $fd[6] = '';
            $fd[7] = 1;
            $fd[8] = 0;
            $fd[9] = 0;
            $fd[10] = 0;
            $fd[11] = 10;
        }
        editItem_setMeta('HTTP/UDP/SHELL', $fd[1], $fd[2], '70'); ?>
        n+="
        <div class='appMenu'>";
            n+="
            <div class='cmdButton cmdButtonL' onClick='app1000_editCancel(\"<? echo $winId; ?>\");'>Abbrechen</div>
            ";
            n+="
            <div class='cmdButton cmdButtonR'
                 onClick='ajax(\"saveItem\",\"<? echo $appId; ?>\",\"<? echo $winId; ?>\",\"<? echo $data; ?>\",controlGetFormData(\"<? echo $winId; ?>-form1\"));'>
                <b>Übernehmen</b></div>
            ";
            n+="
        </div>";
        n+="
        <div id='<? echo $winId; ?>-form1' class='appContentBlank'>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd0' data-type='1' value='<? echo $fd[0]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd1' data-type='1' value='<? echo $fd[1]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd2' data-type='1' value='<? echo $fd[2]; ?>'></input>";
            n+="
            <table width='100%' border='0' cellpadding='5' cellspacing='0'>";
                n+="
                <colgroup>";
                    n+="
                    <col width='70%'>
                    ";
                    n+="
                    <col width='30%'>
                    ";
                    n+="
                </colgroup>
                ";
                n+="
                <tr>
                    <td colspan='2'>"+app1000_editpath+"</td>
                </tr>
                ";
                n+="
                <tr>
                    <td colspan='2'>Name<br><input type='text' id='<? echo $winId; ?>-fd3' data-type='1' value='' class='control1' autofocus
                                                   style='width:100%;'></input></td>
                </tr>
                ";
                n+="
                <tr valign='top'>";
                    n+="
                    <td>URL / Shell-Befehl mit Pfad<br><textarea id='<? echo $winId; ?>-fd4' data-type='1' maxlength='10000' rows='5' wrap='soft'
                                                                 class='control1' style='width:100%; height:50px; resize:none;'></textarea></td>
                    ";
                    n+="
                    <td>Typ<br>
                        <div id='<? echo $winId; ?>-fd5' data-type='6' data-value='<? echo $fd[5]; ?>'
                             data-list='1|HTTP-GET|<? echo $winId; ?>-radiotr1;3|UDP|<? echo $winId; ?>-radiotr3;2|SHELL|<? echo $winId; ?>-radiotr2;'
                             class='control6' style='width:100%;'>&nbsp;
                        </div>
                    </td>
                    ";
                    n+="
                </tr>
                ";

                n+="
                <tr>
                    <td colspan='2' class='formSubTitel'>Optionen
                        <hr>
                    </td>
                </tr>
                ";
                n+="
                <tr id='<? echo $winId; ?>-radiotr1'>";
                    n+="
                    <td>";
                        n+="KO: Antwort<br>
                        <div id='<? echo $winId; ?>-fd9' data-type='1000' data-root='30' data-value='<? echo $fd[9]; ?>' data-options='typ=1' class='control10'
                             style='width:100%;'>&nbsp;
                        </div>
                        <br><br>";
                        n+="KO: Fehler<br>
                        <div id='<? echo $winId; ?>-fd10' data-type='1000' data-root='30' data-value='<? echo $fd[10]; ?>' data-options='typ=1'
                             class='control10' style='width:100%;'>&nbsp;
                        </div>
                        ";
                        n+="
                    </td>
                    ";
                    n+="
                    <td valign='top'>";
                        n+="Timeout (1..&infin; Sekunden)<br><input type='text' id='<? echo $winId; ?>-fd11' data-type='1' value='' class='control1' autofocus
                                                                    style='width:100%;'></input><br><br>";
                        n+="&nbsp;<br>
                        <div id='<? echo $winId; ?>-fd7' data-type='5' data-value='<? echo $fd[7]; ?>' class='control5' style='width:100%;'>Fehler
                            protokollieren
                        </div>
                        ";
                        n+="
                    </td>
                    ";
                    n+="
                </tr>
                ";
                n+="
                <tr id='<? echo $winId; ?>-radiotr2'>";
                    n+="
                    <td colspan='2'>Für Shell-Befehle sind keine weiteren Optionen verfügbar.</td>
                    ";
                    n+="
                </tr>
                ";
                n+="
                <tr id='<? echo $winId; ?>-radiotr3'>";
                    n+="
                    <td>UDP-Daten<br><textarea id='<? echo $winId; ?>-fd6' data-type='1' maxlength='10000' rows='10' wrap='soft' class='control1'
                                               style='width:100%; height:100px; resize:none;'></textarea></td>
                    ";
                    n+="
                    <td valign='top'>Datentyp<br>
                        <div id='<? echo $winId; ?>-fd8' data-type='6' data-value='<? echo $fd[8]; ?>' data-list='0|String;1|HEX;' class='control6'
                             style='width:100%;'>&nbsp;
                        </div>
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
        document.getElementById("<? echo $winId; ?>-edit").innerHTML=n;

        document.getElementById("<? echo $winId; ?>-fd3").value='<? ajaxValue($fd[3]); ?>';
        document.getElementById("<? echo $winId; ?>-fd4").value='<? ajaxValue($fd[4]); ?>';
        document.getElementById("<? echo $winId; ?>-fd6").value='<? ajaxValue($fd[6]); ?>';
        document.getElementById("<? echo $winId; ?>-fd11").value='<? ajaxValue($fd[11]); ?>';
    <? }
    if ($db == 'editArchivMsg') {
        $ss1 = sql_call("SELECT * FROM edomiProject.editArchivMsg WHERE (id=" . $phpdataArr[1] . ")");
        if ($n = sql_result($ss1)) {
            $fd[0] = $db;
            $fd[1] = $n['id'];
            $fd[2] = $n['folderid'];
            $fd[3] = $n['name'];
            $fd[4] = $n['keep'];
            $fd[5] = $n['outgaid'];
            $fd[6] = $n['delay'];
        } else {
            $fd[0] = $db;
            $fd[1] = -1;
            $fd[2] = $phpdataArr[0];
            $fd[3] = '';
            $fd[4] = 31;
            $fd[5] = 0;
            $fd[6] = 0;
        }
        editItem_setMeta('Meldungsarchiv', $fd[1], $fd[2], '60'); ?>
        n+="
        <div class='appMenu'>";
            n+="
            <div class='cmdButton cmdButtonL' onClick='app1000_editCancel(\"<? echo $winId; ?>\");'>Abbrechen</div>
            ";
            n+="
            <div class='cmdButton cmdButtonR'
                 onClick='ajax(\"saveItem\",\"<? echo $appId; ?>\",\"<? echo $winId; ?>\",\"<? echo $data; ?>\",controlGetFormData(\"<? echo $winId; ?>-form1\"));'>
                <b>Übernehmen</b></div>
            ";
            n+="
        </div>";
        n+="
        <div id='<? echo $winId; ?>-form1' class='appContentBlank'>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd0' data-type='1' value='<? echo $fd[0]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd1' data-type='1' value='<? echo $fd[1]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd2' data-type='1' value='<? echo $fd[2]; ?>'></input>";
            n+="
            <table width='100%' border='0' cellpadding='5' cellspacing='0'>";
                n+="
                <tr>
                    <td colspan='2'>"+app1000_editpath+"</td>
                </tr>
                ";
                n+="
                <tr>
                    <td colspan='2'>Name<br><input type='text' id='<? echo $winId; ?>-fd3' data-type='1' value='' class='control1' autofocus
                                                   style='width:100%;'></input></td>
                </tr>
                ";
                n+="
                <tr>
                    <td colspan='2'>KO: Status (erhält bei jeder Änderung die Gesamtanzahl der Einträge)<br>
                        <div id='<? echo $winId; ?>-fd5' data-type='1000' data-root='30' data-value='<? echo $fd[5]; ?>' data-options='typ=1' class='control10'
                             style='width:100%;'>&nbsp;
                        </div>
                    </td>
                </tr>
                ";
                n+="
                <tr>";
                    n+="
                    <td>Totzeit (Sekunden, 0=ohne)<br><input type='text' id='<? echo $winId; ?>-fd6' data-type='1' value='<? echo $fd[6]; ?>' class='control1'
                                                             style='width:100%;'></input></td>
                    ";
                    n+="
                    <td>Speicherdauer<br>
                        <div id='<? echo $winId; ?>-fd4' data-type='6' data-value='<? echo $fd[4]; ?>'
                             data-list='0|unendlich;1|1 Tag;2|2 Tage;3|3 Tage;7|1 Woche;14|2 Wochen;31|1 Monat;90|3 Monate;180|6 Monate;365|1 Jahr;730|2 Jahre;1095|3 Jahre;'
                             class='control6' style='width:100%;'>&nbsp;
                        </div>
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
        document.getElementById("<? echo $winId; ?>-edit").innerHTML=n;

        document.getElementById("<? echo $winId; ?>-fd3").value='<? ajaxValue($fd[3]); ?>';
    <? }
    if ($db == 'editArchivKo') {
        $ss1 = sql_call("SELECT * FROM edomiProject.editArchivKo WHERE (id=" . $phpdataArr[1] . ")");
        if ($n = sql_result($ss1)) {
            $fd[0] = $db;
            $fd[1] = $n['id'];
            $fd[2] = $n['folderid'];
            $fd[3] = $n['name'];
            $fd[4] = $n['keep'];
            $fd[5] = $n['outgaid'];
            $fd[6] = $n['delay'];
            $fd[7] = $n['cmode'];
            $fd[8] = $n['cinterval'];
            $fd[9] = $n['cts'];
            $fd[10] = $n['clist'];
            $fd[11] = $n['coffset'];
            $fd[12] = $n['cunit'];
        } else {
            $fd[0] = $db;
            $fd[1] = -1;
            $fd[2] = $phpdataArr[0];
            $fd[3] = '';
            $fd[4] = 0;
            $fd[5] = 0;
            $fd[6] = 0;
            $fd[7] = 0;
            $fd[8] = 10;
            $fd[9] = 0;
            $fd[10] = '';
            $fd[11] = 1;
            $fd[12] = 10;
        }
        editItem_setMeta('Datenarchiv', $fd[1], $fd[2], '50'); ?>
        n+="
        <div class='appMenu'>";
            n+="
            <div class='cmdButton cmdButtonL' onClick='app1000_editCancel(\"<? echo $winId; ?>\");'>Abbrechen</div>
            ";
            n+="
            <div class='cmdButton cmdButtonR'
                 onClick='ajax(\"saveItem\",\"<? echo $appId; ?>\",\"<? echo $winId; ?>\",\"<? echo $data; ?>\",controlGetFormData(\"<? echo $winId; ?>-form1\"));'>
                <b>Übernehmen</b></div>
            ";
            n+="
        </div>";
        n+="
        <div id='<? echo $winId; ?>-form1' class='appContentBlank'>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd0' data-type='1' value='<? echo $fd[0]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd1' data-type='1' value='<? echo $fd[1]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd2' data-type='1' value='<? echo $fd[2]; ?>'></input>";
            n+="
            <table width='100%' border='0' cellpadding='5' cellspacing='0'>";
                n+="
                <tr>
                    <td colspan='2'>"+app1000_editpath+"</td>
                </tr>
                ";
                n+="
                <tr>
                    <td colspan='2'>Name<br><input type='text' id='<? echo $winId; ?>-fd3' data-type='1' value='' class='control1' autofocus
                                                   style='width:100%;'></input></td>
                </tr>
                ";
                n+="
                <tr>
                    <td colspan='2'>KO: Status (erhält bei jeder Änderung die Gesamtanzahl der Einträge)<br>
                        <div id='<? echo $winId; ?>-fd5' data-type='1000' data-root='30' data-value='<? echo $fd[5]; ?>' data-options='typ=1' class='control10'
                             style='width:100%;'>&nbsp;
                        </div>
                    </td>
                </tr>
                ";
                n+="
                <tr>";
                    n+="
                    <td>Totzeit (Sekunden, 0=ohne)<br><input type='text' id='<? echo $winId; ?>-fd6' data-type='1' value='<? echo $fd[6]; ?>' class='control1'
                                                             style='width:100%;'></input></td>
                    ";
                    n+="
                    <td>Speicherdauer<br>
                        <div id='<? echo $winId; ?>-fd4' data-type='6' data-value='<? echo $fd[4]; ?>'
                             data-list='0|unendlich;1|1 Tag;2|2 Tage;3|3 Tage;7|1 Woche;14|2 Wochen;31|1 Monat;90|3 Monate;180|6 Monate;365|1 Jahr;730|2 Jahre;1095|3 Jahre;'
                             class='control6' style='width:100%;'>&nbsp;
                        </div>
                    </td>
                    ";
                    n+="
                </tr>
                ";

                n+="
                <tr>
                    <td colspan='2' class='formSubTitel'>Automatische Datenverdichtung
                        <hr>
                    </td>
                </tr>
                ";
                n+="
                <tr>
                    <td colspan='2'>
                        <div id='<? echo $winId; ?>-fd7' data-type='6' data-value='<? echo $fd[7]; ?>'
                             data-list='0|deaktiviert;1|Mittelwert berechnen|<? echo $winId; ?>-radiotr1;2|Minimum berechnen|<? echo $winId; ?>-radiotr1;3|Maximum berechnen|<? echo $winId; ?>-radiotr1;'
                             class='control6' style='width:100%;'>&nbsp;
                        </div>
                    </td>
                </tr>
                ";
                n+="
                <tr id='<? echo $winId; ?>-radiotr1'>";
                    n+="
                    <td colspan='2'>";
                        n+="
                        <table width='100%' border='0' cellpadding='0' cellspacing='0' style='table-layout:auto;'>";
                            n+="
                            <tr>";
                                n+="
                                <td colspan='7'>Berechnungsintervall<br>
                                    <div id='<? echo $winId; ?>-fd8' data-type='6' data-value='<? echo $fd[8]; ?>'
                                         data-list='5|stündlich;10|täglich;21|wöchentlich;22|monatlich;23|jährlich;' class='control6' style='width:100%;'>&nbsp;
                                    </div>
                                </td>
                                ";
                                n+="
                            </tr>
                            ";
                            n+="
                            <tr>";
                                n+="
                                <td>Mindestalter (&ge;1)<br><input type='text' id='<? echo $winId; ?>-fd11' data-type='1' value='<? echo $fd[11]; ?>'
                                                                   class='control1' style='width:100%;'></input></td>
                                ";
                                n+="
                                <td>&nbsp;</td>
                                ";
                                n+="
                                <td>Einheit<br>
                                    <div id='<? echo $winId; ?>-fd12' data-type='6' data-value='<? echo $fd[12]; ?>'
                                         data-list='9|Stunden (gleitend);10|Tage (gleitend);11|Wochen (gleitend);12|Monate (gleitend);13|Jahre (gleitend);19|Stunden (kalendarisch);20|Tage (kalendarisch);21|Wochen (kalendarisch);22|Monate (kalendarisch);23|Jahre (kalendarisch);'
                                         class='control6' style='width:100%;'>&nbsp;
                                    </div>
                                </td>
                                ";
                                n+="
                                <td>&nbsp;</td>
                                ";
                                n+="
                                <td>Zeitstempel<br>
                                    <div id='<? echo $winId; ?>-fd9' data-type='6' data-value='<? echo $fd[9]; ?>'
                                         data-list='0|Anfang (Quelldaten);1|Mitte (Quelldaten);2|Ende (Quelldaten);10|Anfang (Intervall);11|Mitte (Intervall);12|Ende (Intervall);'
                                         class='control6' style='width:100%;'>&nbsp;
                                    </div>
                                </td>
                                ";
                                n+="
                                <td>&nbsp;</td>
                                ";
                                n+="
                                <td>Nachkommastellen<br>
                                    <div id='<? echo $winId; ?>-fd10' data-type='6' data-value='<? echo $fd[10]; ?>'
                                         data-list='|beliebig;0|0 (x);1|1 (x.y);2|2 (x.yy);3|3 (x.yyy);4|4 (x.yyyy);5|5 (x.yyyyy);' class='control6'
                                         style='width:100%;'>&nbsp;
                                    </div>
                                </td>
                                ";
                                n+="
                            </tr>
                            ";
                            n+="
                        </table>
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
        document.getElementById("<? echo $winId; ?>-edit").innerHTML=n;

        document.getElementById("<? echo $winId; ?>-fd3").value='<? ajaxValue($fd[3]); ?>';
    <? }
    if ($db == 'editArchivCam') {
        $ss1 = sql_call("SELECT * FROM edomiProject.editArchivCam WHERE (id=" . $phpdataArr[1] . ")");
        if ($n = sql_result($ss1)) {
            $fd[0] = $db;
            $fd[1] = $n['id'];
            $fd[2] = $n['folderid'];
            $fd[3] = $n['camid'];
            $fd[4] = $n['keep'];
            $fd[5] = $n['name'];
            $fd[6] = $n['outgaid'];
            $fd[7] = $n['outgaid2'];
            $fd[8] = $n['delay'];
        } else {
            $fd[0] = $db;
            $fd[1] = -1;
            $fd[2] = $phpdataArr[0];
            $fd[3] = 0;
            $fd[4] = 0;
            $fd[5] = '';
            $fd[6] = 0;
            $fd[7] = 0;
            $fd[8] = 0;
        }
        editItem_setMeta('Kameraarchiv', $fd[1], $fd[2], '82'); ?>
        n+="
        <div class='appMenu'>";
            n+="
            <div class='cmdButton cmdButtonL' onClick='app1000_editCancel(\"<? echo $winId; ?>\");'>Abbrechen</div>
            ";
            n+="
            <div class='cmdButton cmdButtonR'
                 onClick='ajax(\"saveItem\",\"<? echo $appId; ?>\",\"<? echo $winId; ?>\",\"<? echo $data; ?>\",controlGetFormData(\"<? echo $winId; ?>-form1\"));'>
                <b>Übernehmen</b></div>
            ";
            n+="
        </div>";
        n+="
        <div id='<? echo $winId; ?>-form1' class='appContentBlank'>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd0' data-type='1' value='<? echo $fd[0]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd1' data-type='1' value='<? echo $fd[1]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd2' data-type='1' value='<? echo $fd[2]; ?>'></input>";
            n+="
            <table width='100%' border='0' cellpadding='5' cellspacing='0'>";
                n+="
                <tr>
                    <td colspan='2'>"+app1000_editpath+"</td>
                </tr>
                ";
                n+="
                <tr>
                    <td colspan='2'>Name<br><input type='text' id='<? echo $winId; ?>-fd5' data-type='1' value='' class='control1' autofocus
                                                   style='width:100%;'></input></td>
                </tr>
                ";
                n+="
                <tr>
                    <td colspan='2'>Kamera<br>
                        <div id='<? echo $winId; ?>-fd3' data-type='1000' data-root='81' data-value='<? echo $fd[3]; ?>' data-options='typ=1' class='control10'
                             style='width:100%;'>&nbsp;
                        </div>
                    </td>
                </tr>
                ";
                n+="
                <tr>
                    <td colspan='2'>&nbsp;</td>
                </tr>
                ";
                n+="
                <tr>
                    <td colspan='2'>KO: Status (erhält bei jeder Änderung die Gesamtanzahl der Einträge)<br>
                        <div id='<? echo $winId; ?>-fd6' data-type='1000' data-root='30' data-value='<? echo $fd[6]; ?>' data-options='typ=1' class='control10'
                             style='width:100%;'>&nbsp;
                        </div>
                    </td>
                </tr>
                ";
                n+="
                <tr>
                    <td colspan='2'>KO: letzte Archivierung (erhält bei jeder Archivierung diverse Metadaten)<br>
                        <div id='<? echo $winId; ?>-fd7' data-type='1000' data-root='30' data-value='<? echo $fd[7]; ?>' data-options='typ=1' class='control10'
                             style='width:100%;'>&nbsp;
                        </div>
                    </td>
                </tr>
                ";
                n+="
                <tr>";
                    n+="
                    <td>Totzeit (Sekunden, 0=ohne)<br><input type='text' id='<? echo $winId; ?>-fd8' data-type='1' value='<? echo $fd[8]; ?>' class='control1'
                                                             style='width:100%;'></input></td>
                    ";
                    n+="
                    <td>Speicherdauer<br>
                        <div id='<? echo $winId; ?>-fd4' data-type='6' data-value='<? echo $fd[4]; ?>'
                             data-list='0|unendlich;1|1 Tag;2|2 Tage;3|3 Tage;7|1 Woche;14|2 Wochen;31|1 Monat;90|3 Monate;180|6 Monate;365|1 Jahr;730|2 Jahre;1095|3 Jahre;'
                             class='control6' style='width:100%;'>&nbsp;
                        </div>
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
        document.getElementById("<? echo $winId; ?>-edit").innerHTML=n;

        document.getElementById("<? echo $winId; ?>-fd5").value='<? ajaxValue($fd[5]); ?>';
    <? }
    if ($db == 'editScene') { ?>
        n+="
        <div class='appMenu'>";
            <? $ss1 = sql_call("SELECT * FROM edomiProject.editScene WHERE (id=" . $phpdataArr[1] . ")");
            if ($n = sql_result($ss1)) {
                $fd[0] = $db;
                $fd[1] = $n['id'];
                $fd[2] = $n['folderid'];
                $fd[3] = $n['name']; ?>
                n+="
                <div class='cmdButton cmdButtonL cmdButtonDisabled'>Abbrechen</div>";
                                                                                   n+="
                <div class='cmdButton cmdButtonR'
                     onClick='ajax(\"saveItem\",\"<? echo $appId; ?>\",\"<? echo $winId; ?>\",\"<? echo $data; ?>\",controlGetFormData(\"<? echo $winId; ?>-form1\"));'>
                    <b>Übernehmen</b></div>";
            <? } else {
                $fd[0] = $db;
                $fd[1] = -1;
                $fd[2] = $phpdataArr[0];
                $fd[3] = ''; ?>
                n+="
                <div class='cmdButton cmdButtonL' onClick='app1000_editCancel(\"<? echo $winId; ?>\");'>Abbrechen</div>";
                                                                                                                       n+="
                <div class='cmdButton cmdButtonR'
                     onClick='ajax(\"saveItem\",\"<? echo $appId; ?>\",\"<? echo $winId; ?>\",\"<? echo $data; ?>\",controlGetFormData(\"<? echo $winId; ?>-form1\"));'>
                    <b>Szene erstellen</b></div>";
            <? }
            editItem_setMeta('Szene', $fd[1], $fd[2], '40'); ?>
            n+="
        </div>";
        n+="
        <div id='<? echo $winId; ?>-form1' class='appContentBlank'>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd0' data-type='1' value='<? echo $fd[0]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd1' data-type='1' value='<? echo $fd[1]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd2' data-type='1' value='<? echo $fd[2]; ?>'></input>";
            n+="
            <table width='100%' border='0' cellpadding='5' cellspacing='0'>";
                n+="
                <tr>
                    <td>"+app1000_editpath+"</td>
                </tr>
                ";
                n+="
                <tr valign='top'>";
                    n+="
                    <td>Name<br><input type='text' id='<? echo $winId; ?>-fd3' data-type='1' value='' class='control1' autofocus style='width:100%;'></input>
                    </td>
                    ";
                    n+="
                </tr>
                ";
                <? if ($fd[1] > 0) { ?>
                    n+="
                    <tr>
                        <td class='formSubTitel'>Kommunikationsobjekte
                            <hr>
                        </td>
                    </tr>";
                         n+="
                    <tr>
                        <td>
                            <div id='<? echo $winId; ?>-list1' data-type='1001' data-value='<? echo $fd[1]; ?>' data-itemid='0' class='controlList'
                                 style='height:440px;'>&nbsp;
                            </div>
                        </td>
                    </tr>";
                <? } ?>
                n+="
            </table>
            ";
            n+="
        </div>";
        document.getElementById("<? echo $winId; ?>-edit").innerHTML=n;

        document.getElementById("<? echo $winId; ?>-fd3").value='<? ajaxValue($fd[3]); ?>';
    <? }
    if ($db == 'editVisu') { ?>
        n+="
        <div class='appMenu'>";
            <? $ss1 = sql_call("SELECT * FROM edomiProject.editVisu WHERE (id=" . $phpdataArr[1] . ")");
            if ($n = sql_result($ss1)) {
                $fd[0] = $db;
                $fd[1] = $n['id'];
                $fd[2] = $n['folderid'];
                $fd[10] = $n['name'];
                $fd[4] = $n['xsize'];
                $fd[5] = $n['ysize'];
                $fd[6] = $n['defaultpageid'];
                $fd[7] = $n['sspageid'];
                $fd[8] = $n['sstimeout'];
                $fd[13] = $n['indicolor'];
                $fd[14] = $n['indicolor2']; ?>
                n+="
                <div class='cmdButton cmdButtonL cmdButtonDisabled'>Abbrechen</div>";
                                                                                   n+="
                <div class='cmdButton cmdButtonR'
                     onClick='ajax(\"saveItem\",\"<? echo $appId; ?>\",\"<? echo $winId; ?>\",\"<? echo $data; ?>\",controlGetFormData(\"<? echo $winId; ?>-form1\"));'>
                    <b>Übernehmen</b></div>";
            <? } else {
                $fd[0] = $db;
                $fd[1] = -1;
                $fd[2] = $phpdataArr[0];
                $fd[10] = '';
                $fd[4] = '';
                $fd[5] = '';
                $fd[6] = 0;
                $fd[7] = 0;
                $fd[8] = 0;
                $fd[13] = 0;
                $fd[14] = 0; ?>
                n+="
                <div class='cmdButton cmdButtonL' onClick='app1000_editCancel(\"<? echo $winId; ?>\");'>Abbrechen</div>";
                                                                                                                       n+="
                <div class='cmdButton cmdButtonR'
                     onClick='ajax(\"saveItem\",\"<? echo $appId; ?>\",\"<? echo $winId; ?>\",\"<? echo $data; ?>\",controlGetFormData(\"<? echo $winId; ?>-form1\"));'>
                    <b>Visualisierung erstellen</b></div>";
            <? }
            editItem_setMeta('Visualisierung', $fd[1], $fd[2], '21'); ?>
            n+="
        </div>";
        n+="
        <div id='<? echo $winId; ?>-form1' class='appContentBlank'>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd0' data-type='1' value='<? echo $fd[0]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd1' data-type='1' value='<? echo $fd[1]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd2' data-type='1' value='<? echo $fd[2]; ?>'></input>";
            n+="
            <table width='100%' border='0' cellpadding='5' cellspacing='0'>";
                n+="
                <colgroup>";
                    n+="
                    <col width='50%'>
                    ";
                    n+="
                    <col width='25%'>
                    ";
                    n+="
                    <col width='25%'>
                    ";
                    n+="
                </colgroup>
                ";
                n+="
                <tr>
                    <td colspan='3'>"+app1000_editpath+"</td>
                </tr>
                ";
                n+="
                <tr>";
                    n+="
                    <td>Name<br><input type='text' id='<? echo $winId; ?>-fd10' data-type='1' value='' autofocus class='control1' style='width:100%;'></input>
                    </td>
                    ";
                    n+="
                    <td>Breite (px)<br><input type='text' id='<? echo $winId; ?>-fd4' data-type='1' value='' class='control1' style='width:100%;'></input></td>
                    ";
                    n+="
                    <td>Höhe (px)<br><input type='text' id='<? echo $winId; ?>-fd5' data-type='1' value='' class='control1' style='width:100%;'></input></td>
                    ";
                    n+="
                </tr>
                ";
                <? if ($fd[1] > 0) { ?>
                    n+="
                    <tr>";
                        n+="
                        <td colspan='3'>Startseite<br>
                            <div id='<? echo $winId; ?>-fd6' data-type='1000' data-root='22_<? echo $fd[1]; ?>' data-value='<? echo $fd[6]; ?>'
                                 data-options='typ=1' class='control10' style='width:100%;'>&nbsp;
                            </div>
                        </td>
                        ";
                        n+="
                    </tr>";
                         n+="
                    <tr>";
                        n+="
                        <td>Bildschirmschoner<br>
                            <div id='<? echo $winId; ?>-fd7' data-type='1000' data-root='22_<? echo $fd[1]; ?>' data-value='<? echo $fd[7]; ?>'
                                 data-options='typ=1' class='control10' style='width:100%;'>&nbsp;
                            </div>
                        </td>
                        ";
                        n+="
                        <td colspan='2'>Aktivierung<br>
                            <div id='<? echo $winId; ?>-fd8' data-type='6' data-value='<? echo $fd[8]; ?>'
                                 data-list='0|niemals (deaktiviert);1|nach 1 Minute;2|nach 2 Minuten;3|nach 3 Minuten;5|nach 5 Minuten;10|nach 10 Minuten;15|nach 15 Minuten;20|nach 20 Minuten;30|nach 30 Minuten;45|nach 45 Minuten;60|nach 1 Stunde;120|nach 2 Stunden;180|nach 3 Stunden;300|nach 5 Stunden;'
                                 class='control6' style='width:100%;'>&nbsp;
                            </div>
                        </td>
                        ";
                        n+="
                    </tr>";
                         n+="
                    <tr>";
                        n+="
                        <td>Indikatorfarbe (leer=Standard)
                            <div id='<? echo $winId; ?>-fd13' data-type='1000' data-root='26' data-value='<? echo $fd[13]; ?>' data-options='typ=1'
                                 class='control10' style='min-width:0; width:100%;'>&nbsp;
                            </div>
                        </td>
                        ";
                        n+="
                        <td colspan='2'>Eingabefarbe (leer=Standard)
                            <div id='<? echo $winId; ?>-fd14' data-type='1000' data-root='26' data-value='<? echo $fd[14]; ?>' data-options='typ=1'
                                 class='control10' style='min-width:0; width:100%;'>&nbsp;
                            </div>
                        </td>
                        ";
                        n+="
                    </tr>";

                         n+="
                    <tr>
                        <td colspan='3' class='formSubTitel'>Zugriff für diese Visuaccounts erlauben
                            <hr>
                        </td>
                    </tr>";
                         n+="
                    <tr>
                        <td colspan='5'>
                            <div id='<? echo $winId; ?>-list1' data-type='1017' data-value='<? echo $fd[1]; ?>' data-itemid='0' class='controlList'
                                 style='height:200px;'>&nbsp;
                            </div>
                        </td>
                    </tr>";
                <? } else { ?>
                    n+="<input type='hidden' id='<? echo $winId; ?>-fd6' data-type='1' value='<? echo $fd[6]; ?>'></input>";
                                                                                                                          n+="<input type='hidden'
                                                                                                                                     id='<? echo $winId; ?>-fd7'
                                                                                                                                     data-type='1'
                                                                                                                                     value='<? echo $fd[7]; ?>'></input>";
                                                                                                                                                                        n+="
                    <input type='hidden' id='<? echo $winId; ?>-fd8' data-type='1' value='<? echo $fd[8]; ?>'></input>";
                <? } ?>
                n+="
            </table>
            ";
            n+="
        </div>";
        document.getElementById("<? echo $winId; ?>-edit").innerHTML=n;

        document.getElementById("<? echo $winId; ?>-fd10").value='<? ajaxValue($fd[10]); ?>';
        document.getElementById("<? echo $winId; ?>-fd4").value='<? ajaxValue($fd[4]); ?>';
        document.getElementById("<? echo $winId; ?>-fd5").value='<? ajaxValue($fd[5]); ?>';
    <? }
    if ($db == 'editVisuPage') {
        $ss1 = sql_call("SELECT * FROM edomiProject.editVisuPage WHERE (id=" . $phpdataArr[1] . ")");
        if ($n = sql_result($ss1)) {
            $fd[0] = $db;
            $fd[1] = $n['id'];
            $fd[2] = $n['folderid'];
            $fd[3] = $n['visuid'];
            $fd[4] = $n['name'];
            $fd[5] = $n['autoclose'];
            $fd[6] = $n['pagetyp'];
            $fd[7] = $n['xsize'];
            $fd[8] = $n['ysize'];
            $fd[9] = $n['includeid'];
            $fd[10] = $n['bgcolorid'];
            $fd[11] = $n['bgimg'];
            $fd[12] = $n['xgrid'];
            $fd[13] = $n['ygrid'];
            $fd[14] = $n['globalinclude'];
            $fd[15] = $n['xpos'];
            $fd[16] = $n['ypos'];
            $fd[17] = $n['outlinecolorid'];
            $fd[18] = $n['bgmodal'];
            $fd[19] = $n['bganim'];
            $fd[20] = $n['bgdark'];
            $fd[21] = $n['bgshadow'];
        } else {
            $fd[0] = $db;
            $fd[1] = -1;
            $fd[2] = $phpdataArr[0];
            $fd[3] = $linkId;
            $fd[4] = '';
            $fd[5] = 0;
            $fd[6] = 0;
            $fd[7] = '';
            $fd[8] = '';
            $fd[9] = 0;
            $fd[10] = '';
            $fd[11] = 0;
            $fd[12] = 1;
            $fd[13] = 1;
            $fd[14] = 1;
            $fd[15] = '';
            $fd[16] = '';
            $fd[17] = '';
            $fd[18] = 1;
            $fd[19] = 1;
            $fd[20] = 1;
            $fd[21] = 1;
        }
        editItem_setMeta('Visuseite', $fd[1], $fd[2], '22', $fd[3]); ?>
        n+="
        <div class='appMenu'>";
            n+="
            <div class='cmdButton cmdButtonL' onClick='app1000_editCancel(\"<? echo $winId; ?>\");'>Abbrechen</div>
            ";
            n+="
            <div class='cmdButton cmdButtonR'
                 onClick='ajax(\"saveItem\",\"<? echo $appId; ?>\",\"<? echo $winId; ?>\",\"<? echo $data; ?>\",controlGetFormData(\"<? echo $winId; ?>-form1\"));'>
                <b>Übernehmen</b></div>
            ";
            n+="
        </div>";
        n+="
        <div id='<? echo $winId; ?>-form1' class='appContentBlank'>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd0' data-type='1' value='<? echo $fd[0]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd1' data-type='1' value='<? echo $fd[1]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd2' data-type='1' value='<? echo $fd[2]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd3' data-type='1' value='<? echo $fd[3]; ?>'></input>";
            n+="
            <table width='100%' border='0' cellpadding='5' cellspacing='0'>";
                n+="
                <colgroup>";
                    n+="
                    <col width='25%'>
                    ";
                    n+="
                    <col width='25%'>
                    ";
                    n+="
                    <col width='25%'>
                    ";
                    n+="
                    <col width='25%'>
                    ";
                    n+="
                </colgroup>
                ";
                n+="
                <tr>
                    <td colspan='4'>"+app1000_editpath+"</td>
                </tr>
                ";
                n+="
                <tr>
                    <td colspan='4'>Name<br><input type='text' id='<? echo $winId; ?>-fd4' data-type='1' value='' autofocus class='control1'
                                                   style='width:100%;'></input></td>
                </tr>
                ";

                n+="
                <tr>
                    <td colspan='4' class='formSubTitel'>Seitentyp
                        <hr>
                    </td>
                </tr>
                ";
                n+="
                <tr>
                    <td colspan='4'>
                        <div id='<? echo $winId; ?>-fd6' data-type='6' data-value='<? echo $fd[6]; ?>'
                             data-list='0|Normale Visuseite|<? echo $winId; ?>-radiotr1;1|Popup|<? echo $winId; ?>-radiotr2;2|Globale Inkludeseite;'
                             class='control6' style='width:100%;'>&nbsp;
                        </div>
                    </td>
                </tr>
                ";
                n+="
                <tr valign='top'>";
                    n+="
                <tr id='<? echo $winId; ?>-radiotr1'>";
                    n+="
                    <td colspan='4'>";
                        n+="
                        <table width='100%' border='0' cellpadding='0' cellspacing='0'>";
                            n+="
                            <tr>";
                                n+="
                                <td width='70%'>Visuseite einbinden (Inkludeseite)<br>
                                    <div id='<? echo $winId; ?>-fd9' data-type='1000' data-root='22_<? echo $fd[3]; ?>' data-value='<? echo $fd[9]; ?>'
                                         data-options='typ=1' class='control10' style='width:100%;'>&nbsp;
                                    </div>
                                </td>
                                ";
                                n+="
                                <td width='10'>&nbsp;</td>
                                ";
                                n+="
                                <td>&nbsp;<br>
                                    <div id='<? echo $winId; ?>-fd14' data-type='4' data-value='<? echo $fd[14]; ?>'
                                         data-list='Globale Inkludeseiten ignorieren|Globale Inkludeseiten einbinden|' class='control5'
                                         style='width:100%;'></div>
                                </td>
                                ";
                                n+="
                            </tr>
                            ";
                            n+="
                        </table>
                        ";
                        n+="
                    </td>
                    ";
                    n+="
                </tr>
                ";
                n+="
                <tr id='<? echo $winId; ?>-radiotr2'>";
                    n+="
                    <td colspan='4'>";
                        n+="
                        <table width='100%' border='0' cellpadding='0' cellspacing='0'>";
                            n+="
                            <tr>";
                                n+="
                                <td>X-Position (px)<br><input type='text' id='<? echo $winId; ?>-fd15' data-type='1' value='' class='control1'
                                                              style='width:100%;'></input></td>
                                ";
                                n+="
                                <td width='10'>&nbsp;</td>
                                ";
                                n+="
                                <td>Y-Position (px)<br><input type='text' id='<? echo $winId; ?>-fd16' data-type='1' value='' class='control1'
                                                              style='width:100%;'></input></td>
                                ";

                                n+="
                                <td width='5'>&nbsp;</td>
                                ";
                                n+="
                                <td width='5' style='border-left:1px solid #c0c0c0;'>&nbsp;</td>
                                ";

                                n+="
                                <td><br>
                                    <div id='<? echo $winId; ?>-fd18' data-type='5' data-value='<? echo $fd[18]; ?>' class='control5' style='width:100%;'>
                                        Exklusiv öffnen
                                    </div>
                                </td>
                                ";
                                n+="
                                <td width='10'>&nbsp;</td>
                                ";
                                n+="
                                <td><br>
                                    <div id='<? echo $winId; ?>-fd19' data-type='5' data-value='<? echo $fd[19]; ?>' class='control5' style='width:100%;'>
                                        Animation beim Öffnen
                                    </div>
                                </td>
                                ";
                                n+="
                            </tr>
                            ";
                            n+="
                            <tr>";
                                n+="
                                <td>Breite (px)<br><input type='text' id='<? echo $winId; ?>-fd7' data-type='1' value='' class='control1'
                                                          style='width:100%;'></input></td>
                                ";
                                n+="
                                <td width='10'>&nbsp;</td>
                                ";
                                n+="
                                <td>Höhe (px)<br><input type='text' id='<? echo $winId; ?>-fd8' data-type='1' value='' class='control1'
                                                        style='width:100%;'></input></td>
                                ";

                                n+="
                                <td width='5'>&nbsp;</td>
                                ";
                                n+="
                                <td width='5' style='border-left:1px solid #c0c0c0;'>&nbsp;</td>
                                ";

                                n+="
                                <td><br>
                                    <div id='<? echo $winId; ?>-fd21' data-type='5' data-value='<? echo $fd[21]; ?>' class='control5' style='width:100%;'>
                                        Schlagschatten
                                    </div>
                                </td>
                                ";
                                n+="
                                <td width='10'>&nbsp;</td>
                                ";
                                n+="
                                <td><br>
                                    <div id='<? echo $winId; ?>-fd20' data-type='5' data-value='<? echo $fd[20]; ?>' class='control5' style='width:100%;'>
                                        Hintergrund abdunkeln
                                    </div>
                                </td>
                                ";
                                n+="
                            </tr>
                            ";
                            n+="
                            <tr>";
                                n+="
                                <td colspan='3'>Automatisch schließen<br>
                                    <div id='<? echo $winId; ?>-fd5' data-type='6' data-value='<? echo $fd[5]; ?>'
                                         data-list='0|niemals;5|nach 5 Sekunden;10|nach 10 Sekunden;20|nach 20 Sekunden;30|nach 30 Sekunden;60|nach 1 Minute;120|nach 2 Minuten;180|nach 3 Minuten;300|nach 5 Minuten;600|nach 10 Minuten;900|nach 15 Minuten;1800|nach 30 Minuten;3600|nach 1 Stunde;7200|nach 2 Stunden;10800|nach 3 Stunden;'
                                         class='control6' style='width:100%;'>&nbsp;
                                    </div>
                                </td>
                                ";

                                n+="
                                <td width='5'>&nbsp;</td>
                                ";
                                n+="
                                <td width='5' style='border-left:1px solid #c0c0c0;'>&nbsp;</td>
                                ";

                                n+="
                                <td colspan='3'>&nbsp;</td>
                                ";
                                n+="
                            </tr>
                            ";
                            n+="
                        </table>
                        ";
                        n+="
                    </td>
                    ";
                    n+="
                </tr>
                ";

                n+="
                <tr>
                    <td colspan='4' class='formSubTitel'>Hintergrund
                        <hr>
                    </td>
                </tr>
                ";
                n+="
                <tr>";
                    n+="
                    <td colspan='2'>Hintergrundfarbe<br>
                        <div id='<? echo $winId; ?>-fd10' data-type='1000' data-root='25' data-value='<? echo $fd[10]; ?>' data-options='typ=1'
                             class='control10' style='width:100%;'>&nbsp;
                        </div>
                    </td>
                    ";
                    n+="
                    <td colspan='2'>Hintergrundbild<br>
                        <div id='<? echo $winId; ?>-fd11' data-type='1000' data-root='28' data-value='<? echo $fd[11]; ?>' data-options='typ=1'
                             class='control10' style='width:100%;'>&nbsp;
                        </div>
                    </td>
                    ";
                    n+="
                </tr>
                ";

                n+="
                <tr>
                    <td colspan='4' class='formSubTitel'>Visueditor-Einstellungen
                        <hr>
                    </td>
                </tr>
                ";
                n+="
                <tr valign='top'>";
                    n+="
                    <td>Raster X (px)<br><input type='text' id='<? echo $winId; ?>-fd12' data-type='1' value='' class='control1' style='width:100%;'></input>
                    </td>
                    ";
                    n+="
                    <td>Raster Y (px)<br><input type='text' id='<? echo $winId; ?>-fd13' data-type='1' value='' class='control1' style='width:100%;'></input>
                    </td>
                    ";
                    n+="
                    <td colspan='2'>Rahmenfarbe (leer=Standard)<br>
                        <div id='<? echo $winId; ?>-fd17' data-type='1000' data-root='26' data-value='<? echo $fd[17]; ?>' data-options='typ=1'
                             class='control10' style='width:100%;'>&nbsp;
                        </div>
                    </td>
                    ";
                    n+="
                </tr>
                ";
                n+="
            </table";
                n+="
        </div>";
        document.getElementById("<? echo $winId; ?>-edit").innerHTML=n;

        document.getElementById("<? echo $winId; ?>-fd4").value='<? ajaxValue($fd[4]); ?>';
        document.getElementById("<? echo $winId; ?>-fd7").value='<? ajaxValue($fd[7]); ?>';
        document.getElementById("<? echo $winId; ?>-fd8").value='<? ajaxValue($fd[8]); ?>';
        document.getElementById("<? echo $winId; ?>-fd12").value='<? ajaxValue($fd[12]); ?>';
        document.getElementById("<? echo $winId; ?>-fd13").value='<? ajaxValue($fd[13]); ?>';
        document.getElementById("<? echo $winId; ?>-fd15").value='<? ajaxValue($fd[15]); ?>';
        document.getElementById("<? echo $winId; ?>-fd16").value='<? ajaxValue($fd[16]); ?>';
    <? }
    if ($db == 'editLogicPage') {
        $ss1 = sql_call("SELECT * FROM edomiProject.editLogicPage WHERE (id=" . $phpdataArr[1] . ")");
        if ($n = sql_result($ss1)) {
            $fd[0] = $db;
            $fd[1] = $n['id'];
            $fd[2] = $n['folderid'];
            $fd[3] = $n['name'];
            $fd[4] = $n['pagestatus'];
            $fd[5] = $n['text'];
        } else {
            $fd[0] = $db;
            $fd[1] = -1;
            $fd[2] = $phpdataArr[0];
            $fd[3] = '';
            $fd[4] = 1;
            $fd[5] = '';
        }
        editItem_setMeta('Logikseite', $fd[1], $fd[2], '11'); ?>
        n+="
        <div class='appMenu'>";
            n+="
            <div class='cmdButton cmdButtonL' onClick='app1000_editCancel(\"<? echo $winId; ?>\");'>Abbrechen</div>
            ";
            n+="
            <div class='cmdButton cmdButtonR'
                 onClick='ajax(\"saveItem\",\"<? echo $appId; ?>\",\"<? echo $winId; ?>\",\"<? echo $data; ?>\",controlGetFormData(\"<? echo $winId; ?>-form1\"));'>
                <b>Übernehmen</b></div>
            ";
            n+="
        </div>";
        n+="
        <div id='<? echo $winId; ?>-form1' class='appContentBlank'>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd0' data-type='1' value='<? echo $fd[0]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd1' data-type='1' value='<? echo $fd[1]; ?>'></input>";
            n+="<input type='hidden' id='<? echo $winId; ?>-fd2' data-type='1' value='<? echo $fd[2]; ?>'></input>";
            n+="
            <table width='100%' border='0' cellpadding='5' cellspacing='0'>";
                n+="
                <colgroup>";
                    n+="
                    <col width='70%'>
                    ";
                    n+="
                    <col width='30%'>
                    ";
                    n+="
                </colgroup>
                ";
                n+="
                <tr>
                    <td colspan='2'>"+app1000_editpath+"</td>
                </tr>
                ";
                n+="
                <tr valign='top'>";
                    n+="
                    <td>Name<br><input type='text' id='<? echo $winId; ?>-fd3' data-type='1' value='' class='control1' autofocus style='width:100%;'></input>
                    </td>
                    ";
                    n+="
                    <td>Status<br>
                        <div id='<? echo $winId; ?>-fd4' data-type='4' data-value='<? echo $fd[4]; ?>' data-list='Deaktiviert|Aktiviert|' class='control5'
                             style='width:100%;'></div>
                    </td>
                    ";
                    n+="
                </tr>
                ";

                n+="
                <tr>
                    <td colspan='2' class='formSubTitel'>Notizen
                        <hr>
                    </td>
                </tr>
                ";
                n+="
                <tr valign='top'>";
                    n+="
                    <td colspan='2'><textarea id='<? echo $winId; ?>-fd5' data-type='1' maxlength='10000' rows='5' wrap='soft' class='control1'
                                              style='width:100%; height:300px; resize:none;'></textarea></td>
                    ";
                    n+="
                </tr>
                ";
                n+="
            </table>
            ";
            n+="
        </div>";
        document.getElementById("<? echo $winId; ?>-edit").innerHTML=n;

        document.getElementById("<? echo $winId; ?>-fd3").value='<? ajaxValue($fd[3]); ?>';
        document.getElementById("<? echo $winId; ?>-fd5").value='<? ajaxValue($fd[5]); ?>';
    <? }
    if ($db == 'editKo') {
        $ss1 = sql_call("SELECT * FROM edomiProject.editKo WHERE (id=" . $phpdataArr[1] . ")");
        if ($n = sql_result($ss1)) {
            $fd[0] = $db;
            $fd[1] = $n['id'];
            $fd[2] = $n['folderid'];
            $fd[16] = $n['name'];
            $fd[3] = $n['ga'];
            $fd[4] = $n['gatyp'];
            $fd[5] = $n['valuetyp'];
            $fd[6] = $n['defaultvalue'];
            $fd[7] = $n['vmin'];
            $fd[8] = $n['vmax'];
            $fd[9] = $n['vstep'];
            $fd[10] = $n['vlist'];
            $fd[11] = $n['initscan'];
            $fd[12] = $n['initsend'];
            $fd[13] = $n['requestable'];
            $fd[14] = $n['remanent'];
            $fd[17] = $n['text'];
            $fd[18] = $n['vcsv'];
            $fd[19] = $n['prio'];
            $fd[20] = $n['endvalue'];
            $fd[21] = $n['endsend'];
        } else {
            $fd[0] = $db;
            $fd[1] = -1;
            $fd[2] = $phpdataArr[0];
            $fd[16] = '';
            $fd[3] = '';
            $fd[4] = 0;
            $fd[5] = 0;
            $fd[6] = '';
            $fd[7] = '';
            $fd[8] = '';
            $fd[9] = '';
            $fd[10] = '';
            $fd[11] = 0;
            $fd[12] = 0;
            $fd[13] = 0;
            $fd[14] = 0;
            $fd[17] = '';
            $fd[18] = '';
            $fd[19] = 0;
            $fd[20] = '';
            $fd[21] = 0;
        }
        $rootFolderId = dbRoot_getRootId($fd[2]);
        if (!($fd[1] > 0)) {
            if ($rootFolderId == 31) {
                $fd[4] = 2;
                $fd[5] = 0;
            }
            if ($rootFolderId == 32) {
                $fd[4] = 1;
                $fd[5] = 1;
            }
            if ($rootFolderId == 33) {
                $fd[4] = 2;
                $fd[5] = 0;
            }
        }
        if ($rootFolderId == 31) {
            editItem_setMeta('Internes KO', $fd[1], $fd[2], 31);
        }
        if ($rootFolderId == 32) {
            editItem_setMeta('KNX-Gruppenadresse', $fd[1], $fd[2], 32);
        }
        if ($rootFolderId == 33) {
            editItem_setMeta('System KO', $fd[1], $fd[2], 33);
        } ?>
        n+="
        <div class='appMenu'>";
            n+="
            <div class='cmdButton cmdButtonL' onClick='app1000_editCancel(\"<? echo $winId; ?>\");'>Abbrechen</div>
            ";
            n+="
            <div class='cmdButton cmdButtonR'
                 onClick='ajax(\"saveItem\",\"<? echo $appId; ?>\",\"<? echo $winId; ?>\",\"<? echo $data; ?>\",controlGetFormData(\"<? echo $winId; ?>-form1\"));'>
                <b>Übernehmen</b></div>&nbsp;&nbsp;&nbsp;";
            <? if ($fd[1] > 0 && checkLiveProjectData()) { ?>
                n+="
                <div class='cmdButton' onClick='openWindow(1010,\"\",\"<? echo $fd[1]; ?><? echo AJAX_SEPARATOR1; ?><? echo $fd[1]; ?>\");'
                     style='background:#e8e800; float:right;'>Live
                </div>";
            <? } ?>
            n+="
        </div>";
        n+="
        <div id='<? echo $winId; ?>-form1' class='appContentBlank'>";
            <? if ($rootFolderId == 32) {
                $dpts = '';
                foreach ($global_dpt as $i => $v) {
                    if ($i > 0) {
                        $dpts .= $i . '|' . $v . ';';
                    }
                } ?>
                n+="<input type='hidden' id='<? echo $winId; ?>-fd0' data-type='1' value='<? echo $fd[0]; ?>'></input>";
                                                                                                                      n+="<input type='hidden'
                                                                                                                                 id='<? echo $winId; ?>-fd1'
                                                                                                                                 data-type='1'
                                                                                                                                 value='<? echo $fd[1]; ?>'></input>";
                                                                                                                                                                    n+="
                <input type='hidden' id='<? echo $winId; ?>-fd2' data-type='1' value='<? echo $fd[2]; ?>'></input>";
                                                                                                                  n+="<input type='hidden'
                                                                                                                             id='<? echo $winId; ?>-fd4'
                                                                                                                             data-type='1'
                                                                                                                             value='<? echo $fd[4]; ?>'></input>";
                                                                                                                                                                n+="
                <input type='hidden' id='<? echo $winId; ?>-fd14' data-type='1' value='<? echo $fd[14]; ?>'></input>";

                                                                                                                    n+="
                <table width='100%' border='0' cellpadding='5' cellspacing='0'>";
                    n+="
                    <colgroup>";
                        n+="
                        <col width='25%'>
                        ";
                        n+="
                        <col width='25%'>
                        ";
                        n+="
                        <col width='25%'>
                        ";
                        n+="
                        <col width='25%'>
                        ";
                        n+="
                    </colgroup>
                    ";
                    n+="
                    <tr>
                        <td colspan='4'>"+app1000_editpath+"</td>
                    </tr>
                    ";
                    n+="
                    <tr>";
                        n+="
                        <td colspan='3'>Name<br><input type='text' id='<? echo $winId; ?>-fd16' data-type='1' value='' autofocus class='control1'
                                                       style='width:100%;'></input></td>
                        ";
                        n+="
                        <td style='border-left:1px solid #c0c0c0;'>Senden / Read-Request<br>
                            <div id='<? echo $winId; ?>-fd13' data-type='6' data-value='<? echo $fd[13]; ?>'
                                 data-list='0|WRITE / keine Reaktion;1|RESPONSE / Logik triggern;2|RESPONSE / Automatisch antworten;3|RESPONSE / Logik triggern und automatisch antworten;4|WRITE / Logik triggern;5|WRITE / Automatisch antworten;6|WRITE / Logik triggern und automatisch antworten;'
                                 class='control6' style='width:100%;'>&nbsp;
                            </div>
                        </td>
                        ";
                        n+="
                    </tr>
                    ";
                    n+="
                    <tr>";
                        n+="
                        <td colspan='3'>Datentyp<br>
                            <div id='<? echo $winId; ?>-fd5' data-type='6' data-value='<? echo $fd[5]; ?>' data-list='<? echo $dpts; ?>' class='control6'
                                 style='width:100%;'>&nbsp;
                            </div>
                        </td>
                        ";
                        n+="
                        <td style='border-left:1px solid #c0c0c0;'><br>
                            <div id='<? echo $winId; ?>-fd19' data-type='4' data-value='<? echo $fd[19]; ?>' data-list='keine Priorisierung|Priorisierung|'
                                 class='control5' style='width:100%;'></div>
                        </td>
                        ";
                        n+="
                    </tr>
                    ";
                    n+="
                    <tr>";
                        n+="
                        <td colspan='3'>Gruppenadresse<br><input type='text' id='<? echo $winId; ?>-fd3' data-type='1' value='' class='control1'
                                                                 style='width:100%;'></input></td>
                        ";
                        n+="
                        <td style='border-left:1px solid #c0c0c0;'><br>
                            <div id='<? echo $winId; ?>-fd11' data-type='4' data-value='<? echo $fd[11]; ?>' data-list='kein InitScan|InitScan|'
                                 class='control5' style='width:100%;'></div>
                        </td>
                        ";
                        n+="
                    </tr>
                    ";
                    n+="
                    <tr>";
                        n+="
                        <td colspan='3'>Initialwert<br><input type='text' id='<? echo $winId; ?>-fd6' data-type='1' value='' class='control1'
                                                              style='width:100%;'></input></td>
                        ";
                        n+="
                        <td style='border-left:1px solid #c0c0c0;'><br>
                            <div id='<? echo $winId; ?>-fd12' data-type='4' data-value='<? echo $fd[12]; ?>' data-list='kein InitSend|InitSend|'
                                 class='control5' style='width:100%;'></div>
                        </td>
                        ";
                        n+="
                    </tr>
                    ";
                    n+="
                    <tr>";
                        n+="
                        <td colspan='3'>Endwert<br><input type='text' id='<? echo $winId; ?>-fd20' data-type='1' value='' class='control1'
                                                          style='width:100%;'></input></td>
                        ";
                        n+="
                        <td style='border-left:1px solid #c0c0c0;'><br>
                            <div id='<? echo $winId; ?>-fd21' data-type='4' data-value='<? echo $fd[21]; ?>' data-list='kein EndSend|EndSend|' class='control5'
                                 style='width:100%;'></div>
                        </td>
                        ";
                        n+="
                    </tr>
                    ";
                    n+="
                    <tr>
                        <td colspan='4' class='formSubTitel'>Filtereinstellungen
                            <hr>
                        </td>
                    </tr>
                    ";
                    n+="
                    <tr>";
                        n+="
                        <td>Minimum<br><input type='text' id='<? echo $winId; ?>-fd7' data-type='1' value='' class='control1' style='width:100%;'></input></td>
                        ";
                        n+="
                        <td>Maximum<br><input type='text' id='<? echo $winId; ?>-fd8' data-type='1' value='' class='control1' style='width:100%;'></input></td>
                        ";
                        n+="
                        <td>Raster (Quantisierung)<br><input type='text' id='<? echo $winId; ?>-fd9' data-type='1' value='' class='control1'
                                                             style='width:100%;'></input></td>
                        ";
                        n+="
                        <td>Nachkommastellen<br>
                            <div id='<? echo $winId; ?>-fd10' data-type='6' data-value='<? echo $fd[10]; ?>'
                                 data-list='|beliebig;0|0 (x);1|1 (x.y);2|2 (x.yy);3|3 (x.yyy);4|4 (x.yyyy);5|5 (x.yyyyy);' class='control6'
                                 style='width:100%;'>&nbsp;
                            </div>
                        </td>
                        ";
                        n+="
                    </tr>
                    ";
                    n+="
                    <tr>";
                        n+="
                        <td colspan='4'>Wertliste<br><input type='text' id='<? echo $winId; ?>-fd18' maxlength='1000' data-type='1' value='' class='control1'
                                                            style='width:100%;'></input></td>
                        ";
                        n+="
                    </tr>
                    ";

                    n+="
                    <tr>
                        <td colspan='4' class='formSubTitel'>Notizen
                            <hr>
                        </td>
                    </tr>
                    ";
                    n+="
                    <tr>";
                        n+="
                        <td colspan='4'><textarea id='<? echo $winId; ?>-fd17' data-type='1' maxlength='1000' rows='6' wrap='soft' class='control1'
                                                  style='width:100%; height:160px; resize:none;'></textarea></td>
                        ";
                        n+="
                    </tr>
                    ";

                    n+="
                </table>";
            <? }
            if ($rootFolderId == 31) {
                $dpts = '';
                foreach ($global_dpt as $i => $v) {
                    $dpts .= $i . '|' . $v . ';';
                } ?>
                n+="<input type='hidden' id='<? echo $winId; ?>-fd0' data-type='1' value='<? echo $fd[0]; ?>'></input>";
                                                                                                                      n+="<input type='hidden'
                                                                                                                                 id='<? echo $winId; ?>-fd1'
                                                                                                                                 data-type='1'
                                                                                                                                 value='<? echo $fd[1]; ?>'></input>";
                                                                                                                                                                    n+="
                <input type='hidden' id='<? echo $winId; ?>-fd2' data-type='1' value='<? echo $fd[2]; ?>'></input>";
                                                                                                                  n+="<input type='hidden'
                                                                                                                             id='<? echo $winId; ?>-fd3'
                                                                                                                             data-type='1' value=''></input>";
                                                                                                                                                            n+="
                <input type='hidden' id='<? echo $winId; ?>-fd4' data-type='1' value='<? echo $fd[4]; ?>'></input>";
                                                                                                                  n+="<input type='hidden'
                                                                                                                             id='<? echo $winId; ?>-fd11'
                                                                                                                             data-type='1'
                                                                                                                             value='<? echo $fd[11]; ?>'></input>";
                                                                                                                                                                 n+="
                <input type='hidden' id='<? echo $winId; ?>-fd12' data-type='1' value='<? echo $fd[12]; ?>'></input>";
                                                                                                                    n+="<input type='hidden'
                                                                                                                               id='<? echo $winId; ?>-fd13'
                                                                                                                               data-type='1'
                                                                                                                               value='<? echo $fd[13]; ?>'></input>";
                                                                                                                                                                   n+="
                <input type='hidden' id='<? echo $winId; ?>-fd19' data-type='1' value='<? echo $fd[19]; ?>'></input>";
                                                                                                                    n+="<input type='hidden'
                                                                                                                               id='<? echo $winId; ?>-fd20'
                                                                                                                               data-type='1' value=''></input>";
                                                                                                                                                              n+="
                <input type='hidden' id='<? echo $winId; ?>-fd21' data-type='1' value='<? echo $fd[21]; ?>'></input>";

                                                                                                                    n+="
                <table width='100%' border='0' cellpadding='5' cellspacing='0'>";
                    n+="
                    <colgroup>";
                        n+="
                        <col width='25%'>
                        ";
                        n+="
                        <col width='25%'>
                        ";
                        n+="
                        <col width='25%'>
                        ";
                        n+="
                        <col width='25%'>
                        ";
                        n+="
                    </colgroup>
                    ";
                    n+="
                    <tr>
                        <td colspan='4'>"+app1000_editpath+"</td>
                    </tr>
                    ";
                    n+="
                    <tr>";
                        n+="
                        <td colspan='3'>Name<br><input type='text' id='<? echo $winId; ?>-fd16' data-type='1' value='' autofocus class='control1'
                                                       style='width:100%;'></input><br></td>
                        ";
                        n+="
                        <td style='border-left:1px solid #c0c0c0;'><br>
                            <div id='<? echo $winId; ?>-fd14' data-type='4' data-value='<? echo $fd[14]; ?>' data-list='nicht Remanent|Remanent|'
                                 class='control5' style='width:100%;'>Remanent
                            </div>
                        </td>
                        ";
                        n+="
                    </tr>
                    ";
                    n+="
                    <tr>";
                        n+="
                        <td colspan='3'>Datentyp<br>
                            <div id='<? echo $winId; ?>-fd5' data-type='6' data-value='<? echo $fd[5]; ?>' data-list='<? echo $dpts; ?>' class='control6'
                                 style='width:100%;'>&nbsp;
                            </div>
                        </td>
                        ";
                        n+="
                        <td style='border-left:1px solid #c0c0c0;'>&nbsp;</td>
                        ";
                        n+="
                    </tr>
                    ";
                    n+="
                    <tr>";
                        n+="
                        <td colspan='3'>Initialwert<br><input type='text' id='<? echo $winId; ?>-fd6' data-type='1' value='' class='control1'
                                                              style='width:100%;'></input></td>
                        ";
                        n+="
                        <td style='border-left:1px solid #c0c0c0;'>&nbsp;</td>
                        ";
                        n+="
                    </tr>
                    ";

                    n+="
                    <tr>
                        <td colspan='4' class='formSubTitel'>Filtereinstellungen
                            <hr>
                        </td>
                    </tr>
                    ";
                    n+="
                    <tr>";
                        n+="
                        <td>Minimum<br><input type='text' id='<? echo $winId; ?>-fd7' data-type='1' value='' class='control1' style='width:100%;'></input></td>
                        ";
                        n+="
                        <td>Maximum<br><input type='text' id='<? echo $winId; ?>-fd8' data-type='1' value='' class='control1' style='width:100%;'></input></td>
                        ";
                        n+="
                        <td>Raster (Quantisierung)<br><input type='text' id='<? echo $winId; ?>-fd9' data-type='1' value='' class='control1'
                                                             style='width:100%;'></input></td>
                        ";
                        n+="
                        <td>Nachkommastellen<br>
                            <div id='<? echo $winId; ?>-fd10' data-type='6' data-value='<? echo $fd[10]; ?>'
                                 data-list='|beliebig;0|0 (x);1|1 (x.y);2|2 (x.yy);3|3 (x.yyy);4|4 (x.yyyy);5|5 (x.yyyyy);' class='control6'
                                 style='width:100%;'>&nbsp;
                            </div>
                        </td>
                        ";
                        n+="
                    </tr>
                    ";
                    n+="
                    <tr>";
                        n+="
                        <td colspan='4'>Wertliste<br><input type='text' id='<? echo $winId; ?>-fd18' maxlength='1000' data-type='1' value='' class='control1'
                                                            style='width:100%;'></input></td>
                        ";
                        n+="
                    </tr>
                    ";

                    n+="
                    <tr>
                        <td colspan='4' class='formSubTitel'>Notizen
                            <hr>
                        </td>
                    </tr>
                    ";
                    n+="
                    <tr>";
                        n+="
                        <td colspan='4'><textarea id='<? echo $winId; ?>-fd17' data-type='1' maxlength='1000' rows='6' wrap='soft' class='control1'
                                                  style='width:100%; height:210px; resize:none;'></textarea></td>
                        ";
                        n+="
                    </tr>
                    ";
                    n+="
                </table>";
            <? }
            if ($rootFolderId == 33) { ?>
                n+="<input type='hidden' id='<? echo $winId; ?>-fd0' data-type='1' value='<? echo $fd[0]; ?>'></input>";
                                                                                                                      n+="<input type='hidden'
                                                                                                                                 id='<? echo $winId; ?>-fd1'
                                                                                                                                 data-type='1'
                                                                                                                                 value='<? echo $fd[1]; ?>'></input>";
                                                                                                                                                                    n+="
                <input type='hidden' id='<? echo $winId; ?>-fd2' data-type='1' value='<? echo $fd[2]; ?>'></input>";
                                                                                                                  n+="<input type='hidden'
                                                                                                                             id='<? echo $winId; ?>-fd3'
                                                                                                                             data-type='1' value=''></input>";
                                                                                                                                                            n+="
                <input type='hidden' id='<? echo $winId; ?>-fd4' data-type='1' value='<? echo $fd[4]; ?>'></input>";
                                                                                                                  n+="<input type='hidden'
                                                                                                                             id='<? echo $winId; ?>-fd5'
                                                                                                                             data-type='1'
                                                                                                                             value='<? echo $fd[5]; ?>'></input>";
                                                                                                                                                                n+="
                <input type='hidden' id='<? echo $winId; ?>-fd7' data-type='1' value=''></input>";
                                                                                                n+="<input type='hidden' id='<? echo $winId; ?>-fd8'
                                                                                                           data-type='1' value=''></input>";
                                                                                                                                          n+="<input
                    type='hidden' id='<? echo $winId; ?>-fd9' data-type='1' value=''></input>";
                                                                                             n+="<input type='hidden' id='<? echo $winId; ?>-fd10' data-type='1'
                                                                                                        value='<? echo $fd[10]; ?>'></input>";
                                                                                                                                            n+="<input
                    type='hidden' id='<? echo $winId; ?>-fd18' data-type='1' value='<? echo $fd[18]; ?>'></input>";
                                                                                                                 n+="<input type='hidden'
                                                                                                                            id='<? echo $winId; ?>-fd11'
                                                                                                                            data-type='1'
                                                                                                                            value='<? echo $fd[11]; ?>'></input>";
                                                                                                                                                                n+="
                <input type='hidden' id='<? echo $winId; ?>-fd12' data-type='1' value='<? echo $fd[12]; ?>'></input>";
                                                                                                                    n+="<input type='hidden'
                                                                                                                               id='<? echo $winId; ?>-fd13'
                                                                                                                               data-type='1'
                                                                                                                               value='<? echo $fd[13]; ?>'></input>";
                                                                                                                                                                   n+="
                <input type='hidden' id='<? echo $winId; ?>-fd14' data-type='1' value='<? echo $fd[14]; ?>'></input>";
                                                                                                                    n+="<input type='hidden'
                                                                                                                               id='<? echo $winId; ?>-fd19'
                                                                                                                               data-type='1'
                                                                                                                               value='<? echo $fd[19]; ?>'></input>";
                                                                                                                                                                   n+="
                <input type='hidden' id='<? echo $winId; ?>-fd20' data-type='1' value=''></input>";
                                                                                                 n+="<input type='hidden' id='<? echo $winId; ?>-fd21'
                                                                                                            data-type='1' value='<? echo $fd[21]; ?>'></input>";

                                                                                                                                                              n+="
                <table width='100%' border='0' cellpadding='5' cellspacing='0'>";
                    n+="
                    <colgroup>";
                        n+="
                        <col width='50%'>
                        ";
                        n+="
                        <col width='50%'>
                        ";
                        n+="
                    </colgroup>
                    ";
                    n+="
                    <tr>
                        <td colspan='2'>"+app1000_editpath+"</td>
                    </tr>
                    ";
                    n+="
                    <tr>";
                        n+="
                        <td colspan='2'>Name<br><input type='text' disabled id='<? echo $winId; ?>-fd16' data-type='1' value='' autofocus class='control1'
                                                       style='width:100%; background:#f0f0f0;'></input><br></td>
                        ";
                        n+="
                    </tr>
                    ";
                    <? if ($fd[1] == 6) { ?>
                        n+="
                        <tr>";
                            n+="
                            <td colspan='2'>Initialwert<br>
                                <div id='<? echo $winId; ?>-fd6' data-type='6' data-value='<? echo $fd[6]; ?>'
                                     data-list='0|0: deaktiviert;1|1: nur KNX-GAs aufzeichnen;2|2: nur interne KOs aufzeichnen;3|3: KNX-GAs und interne KOs aufzeichnen;'
                                     class='control6' style='width:100%;'>&nbsp;
                                </div>
                            </td>
                            ";
                            n+="
                        </tr>";
                    <? } else { ?>
                        n+="<input type='hidden' id='<? echo $winId; ?>-fd6' data-type='1' value=''></input>";
                    <? } ?>
                    n+="
                    <tr>
                        <td colspan='2' class='formSubTitel'>Notizen
                            <hr>
                        </td>
                    </tr>
                    ";
                    n+="
                    <tr>";
                        n+="
                        <td colspan='2'><textarea id='<? echo $winId; ?>-fd17' data-type='1' maxlength='1000' rows='6' wrap='soft' class='control1'
                                                  style='width:100%; height:390px; resize:none;'></textarea></td>
                        ";
                        n+="
                    </tr>
                    ";
                    n+="
                </table>";
            <? } ?>
            n+="
        </div>";
        document.getElementById("<? echo $winId; ?>-edit").innerHTML=n;

        document.getElementById("<? echo $winId; ?>-fd16").value='<? ajaxValue($fd[16]); ?>';
        document.getElementById("<? echo $winId; ?>-fd3").value='<? ajaxValue($fd[3]); ?>';
        document.getElementById("<? echo $winId; ?>-fd6").value='<? ajaxValue($fd[6]); ?>';
        document.getElementById("<? echo $winId; ?>-fd7").value='<? ajaxValue($fd[7]); ?>';
        document.getElementById("<? echo $winId; ?>-fd8").value='<? ajaxValue($fd[8]); ?>';
        document.getElementById("<? echo $winId; ?>-fd9").value='<? ajaxValue($fd[9]); ?>';
        document.getElementById("<? echo $winId; ?>-fd17").value='<? ajaxValue($fd[17]); ?>';
        document.getElementById("<? echo $winId; ?>-fd18").value='<? ajaxValue($fd[18]); ?>';
        document.getElementById("<? echo $winId; ?>-fd20").value='<? ajaxValue($fd[20]); ?>';
    <? } ?>
    controlInitAll("<? echo $winId; ?>-form1");
    document.getElementById("<? echo $winId; ?>-edittitle").innerHTML=app1000_edittitle;
    document.getElementById("<? echo $winId; ?>-edithelp").dataset.helpid=app1000_edithelp;
    document.getElementById("<? echo $winId; ?>-edit").style.display="inline";
    appAll_setAutofocus("<? echo $winId; ?>-form1");
<? }

function editItem_setMeta($n, $itemId, $folderId, $helpId, $visuId = 0)
{
    global $appId; ?>
    app1000_edittitle="<? echo $n; ?><? echo(($itemId > 0) ? "&nbsp;<span class='idBig'>" . $itemId . "</span>" : ""); ?>";
    app1000_edithelp="<? echo $appId; ?>-<? echo $helpId; ?>";
    app1000_editpath="<span style='color:#808080; word-break:break-all;'><img src='../shared/img/folder1b.png' width='12' height='12' valign='middle'
                                                                              style='margin:0; display:inline;'
                                                                              draggable='false'> <? ajaxEcho(rtrim(dbRoot_getFullPath($folderId, $visuId), '/')); ?></span>";
<? }

function saveItem($db)
{
    global $appId, $winId, $data, $dataArr, $phpdata, $phpdataArr, $sid;
    $editNewRecord = false;
    $err = false;
    if ($db == 'editLogicElementDef') {
        if ($phpdataArr[1] > 0) {
            $dbId = $phpdataArr[1];
        } else {
            $dbId = db_itemSave($db, $phpdataArr);
            if (!($phpdataArr[1] > 0) && $dbId > 0) {
                $editNewRecord = true;
            }
        }
    }
    if ($db == 'editVisuElementDef') {
        if ($phpdataArr[1] > 0) {
            $dbId = $phpdataArr[1];
        } else {
            $dbId = db_itemSave($db, $phpdataArr);
            if (!($phpdataArr[1] > 0) && $dbId > 0) {
                $editNewRecord = true;
            }
        }
    }
    if ($db == 'editVisuUser') {
        $dbId = db_itemSave($db, $phpdataArr);
    }
    if ($db == 'editVisuSnd') {
        if ($phpdataArr[10] == 1) {
            if (file_exists(MAIN_PATH . '/www/data/tmp/snd-tmp.mp3')) {
                $dbId = db_itemSave($db, $phpdataArr);
                if ($dbId > 0) {
                    deleteFiles(MAIN_PATH . '/www/data/project/visu/etc/snd-' . $dbId . '.*');
                    exec('mv "' . MAIN_PATH . '/www/data/tmp/snd-tmp.mp3" "' . MAIN_PATH . '/www/data/project/visu/etc/snd-' . $dbId . '.mp3"');
                }
            } else { ?>
                shakeObj("<? echo $winId; ?>");
            <? }
        } else {
            if (file_exists(MAIN_PATH . '/www/data/project/visu/etc/snd-' . $phpdataArr[1] . '.mp3')) {
                $dbId = db_itemSave($db, $phpdataArr);
            } else { ?>
                shakeObj("<? echo $winId; ?>");
            <? }
        }
    }
    if ($db == 'editVisuFont') {
        if ($phpdataArr[4] == 1) {
            if ($phpdataArr[10] == 1) {
                if (file_exists(MAIN_PATH . '/www/data/tmp/font-tmp.ttf')) {
                    $dbId = db_itemSave($db, $phpdataArr);
                    if ($dbId > 0) {
                        deleteFiles(MAIN_PATH . '/www/data/project/visu/etc/font-' . $dbId . '.*');
                        exec('mv "' . MAIN_PATH . '/www/data/tmp/font-tmp.ttf" "' . MAIN_PATH . '/www/data/project/visu/etc/font-' . $dbId . '.ttf"');
                    }
                } else { ?>
                    shakeObj("<? echo $winId; ?>");
                <? }
            } else {
                if (file_exists(MAIN_PATH . '/www/data/project/visu/etc/font-' . $phpdataArr[1] . '.ttf')) {
                    $dbId = db_itemSave($db, $phpdataArr);
                } else { ?>
                    shakeObj("<? echo $winId; ?>");
                <? }
            }
        } else {
            $dbId = db_itemSave($db, $phpdataArr);
        }
    }
    if ($db == 'editVisuImg') {
        if ($phpdataArr[10] == 1) {
            if (file_exists(MAIN_PATH . '/www/data/tmp/img-tmp.' . $phpdataArr[6])) {
                $dbId = db_itemSave($db, $phpdataArr);
                if ($dbId > 0) {
                    deleteFiles(MAIN_PATH . '/www/data/project/visu/img/img-' . $dbId . '.*');
                    exec('mv "' . MAIN_PATH . '/www/data/tmp/img-tmp.' . $phpdataArr[6] . '" "' . MAIN_PATH . '/www/data/project/visu/img/img-' . $dbId . '.' . $phpdataArr[6] . '"');
                }
            } else { ?>
                shakeObj("<? echo $winId; ?>");
            <? }
        } else {
            if (file_exists(MAIN_PATH . '/www/data/project/visu/img/img-' . $phpdataArr[1] . '.' . $phpdataArr[6])) {
                $dbId = db_itemSave($db, $phpdataArr);
            } else { ?>
                shakeObj("<? echo $winId; ?>");
            <? }
        }
    }
    if ($db == 'editVisuFormat') {
        $dbId = db_itemSave($db, $phpdataArr);
    }
    if ($db == 'editVisuBGcol') {
        $dbId = db_itemSave($db, $phpdataArr);
    }
    if ($db == 'editVisuFGcol') {
        $dbId = db_itemSave($db, $phpdataArr);
    }
    if ($db == 'editVisuAnim') {
        $dbId = db_itemSave($db, $phpdataArr);
    }
    if ($db == 'editVisuElementDesignDef') {
        $dbId = db_itemSave($db, $phpdataArr, 3);
        if (!($phpdataArr[1] > 0) && $dbId > 0) {
            $editNewRecord = true;
        }
    }
    if ($db == 'editChart') {
        $dbId = db_itemSave($db, $phpdataArr);
        if (!($phpdataArr[1] > 0) && $dbId > 0) {
            $editNewRecord = true;
        }
    }
    if ($db == 'editEmail') {
        $dbId = db_itemSave($db, $phpdataArr);
    }
    if ($db == 'editHttpKo') {
        $dbId = db_itemSave($db, $phpdataArr);
    }
    if ($db == 'editPhoneBook') {
        $dbId = db_itemSave($db, $phpdataArr);
    }
    if ($db == 'editPhoneCall') {
        $dbId = db_itemSave($db, $phpdataArr);
    }
    if ($db == 'editArchivPhone') {
        $dbId = db_itemSave($db, $phpdataArr);
    }
    if ($db == 'editIr') {
        $dbId = db_itemSave($db, $phpdataArr);
    }
    if ($db == 'editAws') {
        $dbId = db_itemSave($db, $phpdataArr);
        if (!($phpdataArr[1] > 0) && $dbId > 0) {
            $editNewRecord = true;
        }
    }
    if ($db == 'editTimer') {
        $dbId = db_itemSave($db, $phpdataArr);
        if (!($phpdataArr[1] > 0) && $dbId > 0) {
            $editNewRecord = true;
        }
    }
    if ($db == 'editAgenda') {
        $dbId = db_itemSave($db, $phpdataArr);
        if (!($phpdataArr[1] > 0) && $dbId > 0) {
            $editNewRecord = true;
        }
    }
    if ($db == 'editSequence') {
        $dbId = db_itemSave($db, $phpdataArr);
        if (!($phpdataArr[1] > 0) && $dbId > 0) {
            $editNewRecord = true;
        }
    }
    if ($db == 'editMacro') {
        $dbId = db_itemSave($db, $phpdataArr);
        if (!($phpdataArr[1] > 0) && $dbId > 0) {
            $editNewRecord = true;
        }
    }
    if ($db == 'editCam') {
        $dbId = db_itemSave($db, $phpdataArr);
    }
    if ($db == 'editCamView') {
        $dbId = db_itemSave($db, $phpdataArr);
    }
    if ($db == 'editIp') {
        $dbId = db_itemSave($db, $phpdataArr);
    }
    if ($db == 'editArchivMsg') {
        $dbId = db_itemSave($db, $phpdataArr);
    }
    if ($db == 'editArchivKo') {
        $dbId = db_itemSave($db, $phpdataArr);
    }
    if ($db == 'editArchivCam') {
        $dbId = db_itemSave($db, $phpdataArr);
    }
    if ($db == 'editScene') {
        $dbId = db_itemSave($db, $phpdataArr);
        if (!($phpdataArr[1] > 0) && $dbId > 0) {
            $editNewRecord = true;
        }
    }
    if ($db == 'editVisu') {
        $dbId = db_itemSave($db, $phpdataArr);
        if (!($phpdataArr[1] > 0) && $dbId > 0) {
            $editNewRecord = true;
        }
    }
    if ($db == 'editVisuPage') {
        $dbId = db_itemSave($db, $phpdataArr);
    }
    if ($db == 'editLogicPage') {
        $dbId = db_itemSave($db, $phpdataArr);
    }
    if ($db == 'editKo') {
        $dbId = db_itemSave($db, $phpdataArr);
        if ($dbId == -1) { ?> jsConfirm("Die Gruppenadresse ist ungültig (Gruppenadressen müssen dem Schema 0..31/0..7/0..255 folgen).","","none"); <? }
        if ($dbId == -2) { ?> jsConfirm("Diese Gruppenadresse existiert bereits in diesem Projekt.","","none"); <? }
    }
    if ($editNewRecord) {
        $phpdataArr[0] = $phpdataArr[2];
        $phpdataArr[1] = $dbId;
        editItem($db);
    } else {
        if ($dbId > 0) {
            $options = parseOptions();
            if ($options['typ'] == 6) { ?>
                app1000_itemReturnValue("<? echo $winId; ?>");
            <? } else {
                cmd('refreshFolders'); ?>
                clearObject("<? echo $winId; ?>-edit",1);
                app1000_itemExpandToItem("<? echo $winId; ?>","<? echo $winId; ?>-i-<? echo $phpdataArr[2]; ?>-<? echo $dbId; ?>");
            <? }
        } else if ($dbId == 0) { ?>
            shakeObj("<? echo $winId; ?>");
        <? }
    }
} ?>
