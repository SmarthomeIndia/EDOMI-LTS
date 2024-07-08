<?
/*
*/
?><? ?><? function loginAdmin($login, $pass)
{
    if ($n = checkAdminLoginPass($login, $pass)) {
        $sid = createAdminSid();
        sql_call("UPDATE edomiAdmin.user SET sid='" . $sid . "',logindate=" . sql_getNow() . ",loginip='" . $_SERVER['REMOTE_ADDR'] . "' WHERE (id=" . $n['id'] . ")");
        return array($sid, $n['typ'], $n['id']);
    }
    return false;
}

function logoutAdmin($sid)
{
    if (checkAdminSid($sid)) {
        sql_call("UPDATE edomiAdmin.user SET sid=NULL,logoutdate=" . sql_getNow() . ",actiondate=NULL WHERE (sid='" . $sid . "')"); ?>
        jsLogout();
        <? return true;
    } else { ?>
        jsLogout();
        <? return false;
    }
}

function checkAdminLoginPass($login, $pass)
{
    $ss1 = sql_call("SELECT * FROM edomiAdmin.user WHERE (typ<10 AND login='" . sql_encodeValue($login) . "' AND pass='" . sql_encodeValue($pass) . "')");
    if ($n = sql_result($ss1)) {
        return $n;
    }
    return false;
}

function checkAdmin($sid, $logout = true)
{
    if (checkAdminSid($sid)) {
        return true;
    } else {
        if ($logout) {
            logoutAdmin($sid);
        }
        return false;
    }
}

function checkAdminSid($sid, $mode = false)
{
    if (!isEmpty($sid)) {
        $ss1 = sql_call("SELECT * FROM edomiAdmin.user WHERE ((sid IS NOT NULL) AND sid='" . $sid . "')");
        if ($n = sql_result($ss1)) {
            sql_call("UPDATE edomiAdmin.user SET actiondate=" . sql_getNow() . " WHERE (id=" . $n['id'] . ")");
            if ($mode) {
                return $n;
            } else {
                return $n['id'];
            }
        }
    }
    return false;
}

function createAdminSid()
{
    do {
        $sid = substr(strtoupper(dechex(intval(rand(1000000, 1000000000))) . dechex(intval(strtotime('NOW') * rand(1, 1000000))) . dechex(intval(rand(1000000, 1000000000)))), 0, 30);
        $ss1 = sql_call("SELECT id FROM edomiAdmin.user WHERE (sid='" . $sid . "')");
    } while (sql_result($ss1));
    return $sid;
}

function checkLiveProjectData()
{
    $ss1 = sql_call("SELECT id FROM edomiAdmin.project WHERE (edit=1 AND live=1)");
    if ($n = sql_result($ss1)) {
        if (getEdomiStatus() >= 2) {
            return true;
        }
    }
    return false;
}

function getEditProjektId()
{
    $ss1 = sql_call("SELECT id FROM edomiAdmin.project WHERE (edit=1)");
    if ($n = sql_result($ss1)) {
        return $n['id'];
    }
    return false;
}

function getLiveProjektData()
{
    $n = sql_getValues('edomiAdmin.project', '*', 'live=1');
    if ($n !== false) {
        return $n;
    } else {
        $tmp = readInfoFile(MAIN_PATH . '/www/data/liveproject/liveprojectname.txt');
        if ($tmp !== false) {
            $n['id'] = $tmp[0];
            $n['name'] = $tmp[1];
            $n['livedate'] = $tmp[2];
            return $n;
        }
    }
    return false;
}

function getLiveProjektId()
{
    $ss1 = sql_call("SELECT id FROM edomiAdmin.project WHERE (live=1)");
    if ($n = sql_result($ss1)) {
        return $n['id'];
    } else {
        $n = readInfoFile(MAIN_PATH . '/www/data/liveproject/liveprojectname.txt');
        if ($n !== false) {
            return $n[0];
        }
    }
    return false;
}

function checkLiveProjektValid()
{
    if (sql_tableExists('edomiLive.logicElement')) {
        return true;
    }
    return false;
}

function getLiveProjectName()
{
    $ss1 = sql_call("SELECT * FROM edomiAdmin.project WHERE (live=1)");
    if ($n = sql_result($ss1)) {
        return ajaxEncode($n['name']) . ' <span class="id" style="color:#343434;">' . $n['id'] . '</span>';
    } else {
        $n = readInfoFile(MAIN_PATH . '/www/data/liveproject/liveprojectname.txt');
        if ($n !== false) {
            return ajaxEncode($n[1]) . ' <span class="id" style="color:#343434;">' . $n[0] . '</span>';
        }
    }
    return '<span style="color:#ff0000;">kein Live-Projekt vorhanden</span>';
}

function showEditProjectName()
{
    $ss1 = sql_call("SELECT * FROM edomiAdmin.project WHERE (edit=1)");
    if ($n = sql_result($ss1)) {
        if ($n['live'] == 1) {
            $r = ajaxEncode($n['name']) . ' <span class="id">' . $n['id'] . '</span> <span style="color:#ffffff; border-radius:3px; background:#80e000;">&nbsp;LIVE&nbsp;</span>';
        } else {
            $r = ajaxEncode($n['name']) . ' <span class="id">' . $n['id'] . '</span>';
        }
    } else {
        $r = '<span style="color:#ff0000;">kein Projekt vorhanden</span>';
    } ?>
    document.getElementById("desktopProject").innerHTML='<? echo $r; ?>';
<? }

function lbs_import($id, $echo = false, $lbsUserFolderId = null)
{
    if (isEmpty($lbsUserFolderId)) {
        $lbsUserFolderId = sql_getValue('edomiProject.editLogicElementDef', 'folderid', 'id=' . $id . ' AND folderid>=1000');
    }
    lbs_deleteAllData($id);
    if (file_exists(MAIN_PATH . '/www/admin/lbs/' . $id . '_lbs.php')) {
        $lbsID = substr($id, 0, 8);
        $lbsFolderID = substr($id, 0, 2);
        if (!isEmpty($lbsUserFolderId) && $lbsUserFolderId >= 1000 && $lbsFolderID == 19) {
            $lbsFolderID = $lbsUserFolderId;
        }
        if ($r = lbs_parse($lbsID)) {
            $ss1 = sql_call("SELECT * FROM edomiProject.editLogicElement WHERE (functionid=" . $lbsID . ")");
            while ($n = sql_result($ss1)) {
                $t = 0;
                $curIn = array();
                $ss2 = sql_call("SELECT DISTINCT(eingang) FROM edomiProject.editLogicLink WHERE (elementid=" . $n['id'] . ") ORDER BY eingang ASC");
                while ($nn = sql_result($ss2)) {
                    $curIn[$t] = $nn['eingang'];
                    $t++;
                }
                sql_close($ss2);
                foreach ($r[4] as $newIn) {
                    if (!in_array($newIn[0], $curIn)) {
                        sql_save('edomiProject.editLogicLink', null, array('elementid' => $n['id'], 'linktyp' => 2, 'eingang' => $newIn[0], 'value' => sql_encodeValue($newIn[2], true)));
                    }
                }
                for ($t = 0; $t < count($curIn); $t++) {
                    $exist = false;
                    foreach ($r[4] as $newIn) {
                        if ($curIn[$t] == $newIn[0]) {
                            $exist = true;
                            break;
                        }
                    }
                    if (!$exist) {
                        sql_call("DELETE FROM edomiProject.editLogicLink WHERE (elementid=" . $n['id'] . " AND eingang=" . $curIn[$t] . ")");
                    }
                }
                $t = 0;
                $curOut = array();
                $ss2 = sql_call("SELECT ausgang FROM edomiProject.editLogicLink WHERE (linkid=" . $n['id'] . " AND linktyp=1) GROUP BY ausgang ORDER BY ausgang ASC");
                while ($nn = sql_result($ss2)) {
                    $curOut[$t] = $nn['ausgang'];
                    $t++;
                }
                sql_close($ss2);
                for ($t = 0; $t < count($curOut); $t++) {
                    $exist = false;
                    foreach ($r[5] as $newOut) {
                        if ($curOut[$t] == $newOut[0]) {
                            $exist = true;
                            break;
                        }
                    }
                    if (!$exist) {
                        sql_call("UPDATE edomiProject.editLogicLink SET linktyp=2,linkid=null,ausgang=null WHERE (linkid=" . $n['id'] . " AND ausgang=" . $curOut[$t] . ")");
                    }
                }
                sql_call("DELETE FROM edomiProject.editLogicElementVar WHERE (elementid=" . $n['id'] . ")");
                foreach ($r[6] as $newVar) {
                    sql_save('edomiProject.editLogicElementVar', null, array('elementid' => $n['id'], 'varid' => $newVar[0], 'value' => sql_encodeValue($newVar[1], true), 'remanent' => $newVar[2]));
                }
            }
            sql_close($ss1);
            sql_save('edomiProject.editLogicElementDef', null, array('id' => $lbsID, 'folderid' => $lbsFolderID, 'name' => sql_encodeValue($r[3], true), 'title' => sql_encodeValue($r[10], true), 'defin' => count($r[4]), 'defout' => count($r[5]), 'defvar' => count($r[6]), 'errcount' => $r[0], 'errmsg' => sql_encodeValue($r[1], true), 'exec' => (($r[2]) ? 1 : 0)));
            foreach ($r[4] as $newIn) {
                sql_save('edomiProject.editLogicElementDefIn', null, array('targetid' => $lbsID, 'id' => $newIn[0], 'name' => sql_encodeValue($newIn[1], true), 'value' => sql_encodeValue($newIn[2], true), 'color' => $newIn[3]));
            }
            foreach ($r[5] as $newOut) {
                sql_save('edomiProject.editLogicElementDefOut', null, array('targetid' => $lbsID, 'id' => $newOut[0], 'name' => sql_encodeValue($newOut[1], true)));
            }
            foreach ($r[6] as $newVar) {
                sql_save('edomiProject.editLogicElementDefVar', null, array('targetid' => $lbsID, 'id' => $newVar[0], 'value' => sql_encodeValue($newVar[1], true), 'remanent' => $newVar[2]));
            }
            if ($r[0] == 0) {
                $r[9] = "<lbs-titel>" . $r[3] . " <span class='id'>" . $lbsID . "</span></lbs-titel>\n<div style='float:right; margin:0px 3px 10px 10px;'>" . lbs_preview($lbsID) . "</div>" . $r[9];
                $f = fopen(MAIN_PATH . '/www/admin/help/lbs_' . $lbsID . '.htm', 'w');
                fwrite($f, $r[9]);
                fclose($f);
                if ($echo === true) {
                    echo 'LBS ' . $lbsID . ": Ok \n";
                } else if ($echo !== false) {
                    writeToTmpLog($echo, ajaxValueHTML($r[3]) . " <span class='id'>" . $lbsID . "</span>: Ok", false);
                }
            } else {
                if ($echo === true) {
                    echo 'LBS ' . $lbsID . ": Fehler \n";
                } else if ($echo !== false) {
                    writeToTmpLog($echo, ajaxValueHTML($r[3]) . " <span class='id'>" . $lbsID . "</span>: " . $r[0] . " Fehler", true);
                }
            }
            return $r;
        }
    }
    return false;
}

function lbs_importAll($echo = false, $activation = false)
{
    $r = array(0, 0);
    $tmp = glob(MAIN_PATH . '/www/admin/lbs/????????_lbs.php');
    foreach ($tmp as $pathFn) {
        if (is_file($pathFn)) {
            $id = substr(basename($pathFn), 0, 8);
            $n = lbs_import($id, $echo, null);
            if ($n !== false) {
                if ($n[0] == 0) {
                    $r[0]++;
                } else {
                    $r[1]++;
                }
            }
        }
    }
    lbs_optimizeTables();
    return $r;
}

function lbs_deleteAllData($id)
{
    sql_call("DELETE FROM edomiProject.editLogicElementDef WHERE (id=" . $id . ")");
    sql_call("DELETE FROM edomiProject.editLogicElementDefIn WHERE (targetid=" . $id . ")");
    sql_call("DELETE FROM edomiProject.editLogicElementDefOut WHERE (targetid=" . $id . ")");
    sql_call("DELETE FROM edomiProject.editLogicElementDefVar WHERE (targetid=" . $id . ")");
    deleteFiles(MAIN_PATH . '/www/admin/help/lbs_' . $id . '.htm');
}

function lbs_optimizeTables()
{
    $ss1 = sql_call("SELECT id FROM edomiProject.editLogicElementDef");
    while ($n = sql_result($ss1)) {
        if (!file_exists(MAIN_PATH . '/www/admin/lbs/' . $n['id'] . '_lbs.php')) {
            lbs_deleteAllData($n['id']);
        }
    }
    sql_close($ss1);
    sql_call("OPTIMIZE TABLE edomiProject.editLogicElementDef");
    sql_call("OPTIMIZE TABLE edomiProject.editLogicElementDefIn");
    sql_call("OPTIMIZE TABLE edomiProject.editLogicElementDefOut");
    sql_call("OPTIMIZE TABLE edomiProject.editLogicElementDefVar");
    sql_call("OPTIMIZE TABLE edomiProject.editLogicElement");
    sql_call("OPTIMIZE TABLE edomiProject.editLogicElementVar");
    sql_call("OPTIMIZE TABLE edomiProject.editLogicLink");
}

function lbs_checkSyntax($pathFn)
{
    clearstatcache();
    exec('php -d display_errors=1 -l -n ' . $pathFn, $n);
    $tmp = implode($n);
    if (strpos(strtoupper($tmp), 'NO SYNTAX ERRORS') !== false) {
        return false;
    } else {
        foreach ($n as $tmp) {
            $tmp = trim($tmp);
            if (!isEmpty($tmp) && strpos(strtoupper($tmp), 'ERRORS PARSING') === false) {
                return $tmp;
            }
        }
        return 'unbekannter Syntaxfehler';
    }
}

function lbs_parse($lbsID)
{
    $lbsFilename = MAIN_PATH . '/www/admin/lbs/' . $lbsID . '_lbs.php';
    $lbsFolderID = substr($lbsID, 0, 2);
    if (is_numeric($lbsID) && strlen($lbsID) == 8 && file_exists($lbsFilename) && $lbsFolderID >= 12 && $lbsFolderID <= 19) {
        $lbsRAW = file_get_contents($lbsFilename);
        if (getFileEncoding($lbsFilename) != 'utf-8') {
            $lbsRAW = utf8_encode($lbsRAW);
            file_put_contents($lbsFilename, $lbsRAW);
        }
        $lbsDEF = '';
        $lbsDEFname = '';
        $lbsDEFtitle = '';
        $lbsDEFin = array();
        $lbsDEFout = array();
        $lbsDEFvar = array();
        $lbsHELP = '';
        $lbsLBS = '';
        $lbsEXEC = '';
        $lbsEXECcall = false;
        $err = 0;
        $errInfo = '';
        $tmp = string_cutout($lbsRAW, '###[DEF]###', '###[/DEF]###');
        if ($tmp !== false) {
            $lbsDEF = trim($tmp);
        } else {
            $err++;
            $errInfo .= 'kein Abschnitt [DEF] vorhanden' . "\n";
        }
        $tmp = string_cutout($lbsRAW, '###[HELP]###', '###[/HELP]###');
        if ($tmp !== false) {
            $lbsHELP = trim($tmp);
        } else {
            $err++;
            $errInfo .= 'kein Abschnitt [HELP] vorhanden' . "\n";
        }
        $tmp = string_cutout($lbsRAW, '###[LBS]###', '###[/LBS]###');
        if ($tmp !== false) {
            $lbsLBS = trim($tmp);
        } else {
            $err++;
            $errInfo .= 'kein Abschnitt [LBS] vorhanden' . "\n";
        }
        $tmp = string_cutout($lbsRAW, '###[EXEC]###', '###[/EXEC]###');
        if ($tmp !== false) {
            $lbsEXEC = trim($tmp);
        } else {
            $err++;
            $errInfo .= 'kein Abschnitt [EXEC] vorhanden' . "\n";
        }
        if (!isEmpty($lbsDEF)) {
            if (preg_match_all("'\[(.*?)\]'s", $lbsDEF, $tmp) > 0) {
                for ($t = 0; $t < count($tmp[0]); $t++) {
                    $def = explode('=', $tmp[1][$t], 2);
                    if (count($def) == 2) {
                        $vName = strtoupper(trim($def[0]));
                        $vValue = trim($def[1]);
                        if ($vName == 'NAME') {
                            $lbsDEFname = substr(trim($vValue), 0, 100);
                        }
                        if ($vName == 'TITEL') {
                            $lbsDEFtitle = substr(trim($vValue), 0, 100);
                        }
                        if (substr($vName, 0, 2) == 'E#') {
                            $tmp0 = preg_replace("/[^0-9]/", '', substr($vName, 2, strlen($vName)));
                            $vTmp = explode('#init=', $vValue);
                            $tmp1 = substr(trim($vTmp[0]), 0, 100);
                            if (count($vTmp) > 1) {
                                $tmp2 = trim($vTmp[1]);
                            } else {
                                $tmp2 = null;
                            }
                            $tmp3 = 0;
                            if (strpos($vName, 'TRIGGER') !== false) {
                                $tmp3 = 1;
                            }
                            if (strpos($vName, 'OPTION') !== false) {
                                $tmp3 = 2;
                            }
                            if (strpos($vName, 'IMPORTANT') !== false) {
                                $tmp3 = 3;
                            }
                            $exist = false;
                            foreach ($lbsDEFin as $n) {
                                if ($tmp0 == $n[0]) {
                                    $exist = true;
                                    break;
                                }
                            }
                            if (!$exist) {
                                if ($tmp0 > 0) {
                                    $lbsDEFin[] = array($tmp0, $tmp1, $tmp2, $tmp3);
                                } else {
                                    $err++;
                                    $errInfo .= 'E#' . $tmp0 . ': ungültige ID' . "\n";
                                }
                            } else {
                                $err++;
                                $errInfo .= 'E#' . $tmp0 . ': bereits deklariert' . "\n";
                            }
                        }
                        if (substr($vName, 0, 2) == 'A#') {
                            $tmp0 = preg_replace("/[^0-9]/", '', substr($vName, 2, strlen($vName)));
                            $tmp1 = substr(trim($vValue), 0, 100);
                            $exist = false;
                            foreach ($lbsDEFout as $n) {
                                if ($tmp0 == $n[0]) {
                                    $exist = true;
                                    break;
                                }
                            }
                            if (!$exist) {
                                if ($tmp0 > 0) {
                                    $lbsDEFout[] = array($tmp0, $tmp1);
                                } else {
                                    $err++;
                                    $errInfo .= 'A#' . $tmp0 . ': ungültige ID' . "\n";
                                }
                            } else {
                                $err++;
                                $errInfo .= 'A#' . $tmp0 . ': bereits deklariert' . "\n";
                            }
                        }
                        if (substr($vName, 0, 2) == 'V#') {
                            $tmp0 = preg_replace("/[^0-9]/", '', substr($vName, 2, strlen($vName)));
                            $tmp1 = trim($vValue);
                            $tmp2 = 0;
                            if (strpos($vName, 'REMANENT') !== false) {
                                $tmp2 = 1;
                            }
                            $exist = false;
                            foreach ($lbsDEFvar as $n) {
                                if ($tmp0 == $n[0]) {
                                    $exist = true;
                                    break;
                                }
                            }
                            if (!$exist) {
                                if ($tmp0 > 0) {
                                    $lbsDEFvar[] = array($tmp0, $tmp1, $tmp2);
                                } else {
                                    $err++;
                                    $errInfo .= 'V#' . $tmp0 . ': ungültige ID' . "\n";
                                }
                            } else {
                                $err++;
                                $errInfo .= 'V#' . $tmp0 . ': bereits deklariert' . "\n";
                            }
                        }
                    }
                }
            }
        }
        if (isEmpty($lbsDEFname)) {
            $lbsDEFname = 'ohne Name';
        }
        if (count($lbsDEFin) == 0 && $lbsID != "12000000") {
            $err++;
            $errInfo .= 'kein Eingang deklariert' . "\n";
        }
        if (strpos($lbsLBS, 'function LB_LBSID(') === false) {
            $err++;
            $errInfo .= 'Funktion LB_LBSID() nicht vorhanden' . "\n";
        }
        if (strpos($lbsLBS, "callLogicFunctionExec(") !== false || strpos($lbsLBS, "logic_callExec(") !== false) {
            $lbsEXECcall = true;
            if (isEmpty($lbsEXEC)) {
                $err++;
                $errInfo .= 'Abschnitt [EXEC] ist leer, jedoch erforderlich' . "\n";
            }
        }
        $lbsLBS = str_replace('LBSID', $lbsID, $lbsLBS);
        $lbsEXEC = str_replace('LBSID', $lbsID, $lbsEXEC);
        $tmp = lbs_checkSyntax($lbsFilename);
        if ($tmp !== false) {
            $err++;
            $errInfo .= 'PHP-Syntaxfehler: ' . $tmp . "\n";
        }
        if (!$lbsEXECcall) {
            $lbsEXEC = null;
        }
        return array($err, $errInfo, $lbsEXECcall, $lbsDEFname, $lbsDEFin, $lbsDEFout, $lbsDEFvar, $lbsLBS, $lbsEXEC, $lbsHELP, $lbsDEFtitle);
    } else {
        return false;
    }
}

function lbs_preview($id)
{
    $preview = '';
    $lbs = sql_getValues('edomiProject.editLogicElementDef', 'id,name,title,errcount', 'id=' . $id);
    if ($lbs !== false && $lbs['errcount'] == 0) {
        if (!isEmpty($lbs['title'])) {
            $lbs['name'] = $lbs['title'];
        }
        if ($lbs['id'] == 12000000) {
            $preview = '<div style="border-radius:3px; overflow:hidden; box-shadow:0 0 5px #404040;">';
            $preview .= '<table border="0" cellspacing="0" cellpadding="1" class="app1_elContainer" style="position:relative; box-shadow:none; line-height:1; pointer-events:none;">';
            $preview .= '<tr><td class="app1_elTextboxTitel">' . $lbs['name'] . '</td></tr>';
            $preview .= '<tr><td class="app1_divTextboxInfo" style="height:100px;">&nbsp;</td></tr>';
            $preview .= '</table>';
            $preview .= '</div>';
            return $preview;
        }
        $tmp = array();
        $countIn = 0;
        $countOut = 0;
        $ss1 = sql_call("SELECT * FROM edomiProject.editLogicElementDefIn WHERE targetid=" . $lbs['id'] . " ORDER BY ID ASC");
        while ($n = sql_result($ss1)) {
            if ($lbs['id'] <= 12000005) {
                $tmp[$n['id']][0] = '<td colspan="3" class="app1_elEingangsboxInput">' . $n['name'] . '</td>';
            } else {
                $tmp[$n['id']][0] = '<td class="app1_elInputNum col' . $n['color'] . '">&#x25B8;&nbsp;E' . $n['id'] . '</td><td class="app1_elInput">' . $n['name'] . '</td><td class="app1_elInputValue">' . $n['value'] . '</td>';
            }
            $countIn++;
        }
        sql_close($ss1);
        $ss1 = sql_call("SELECT * FROM edomiProject.editLogicElementDefOut WHERE targetid=" . $lbs['id'] . " ORDER BY ID ASC");
        while ($n = sql_result($ss1)) {
            if ($lbs['id'] <= 12000005) {
                $tmp[$n['id']][1] = '<td colspan="2" class="app1_elOutputNum">&nbsp;A' . $n['id'] . '&nbsp;&#x25B8;</td>';
            } else {
                $tmp[$n['id']][1] = '<td class="app1_elOutput">' . $n['name'] . '</td><td class="app1_elOutputNum">A' . $n['id'] . '&nbsp;&#x25B8;</td>';
            }
            $countOut++;
        }
        sql_close($ss1);
        if ($lbs['id'] >= 12000010 && $lbs['id'] <= 12000019) {
            $tmp[1][1] = '<td colspan="2" class="app1_elAusgangsboxOutput0">0 Befehle</td>';
            $countOut = 1;
        }
        $preview = '<div style="border-radius:3px; overflow:hidden; box-shadow:0 0 5px #404040;">';
        $preview .= '<table border="0" cellspacing="0" cellpadding="1" class="app1_elContainer" style="position:relative; box-shadow:none; line-height:1; pointer-events:none;">';
        $preview .= '<tr><td colspan="5" class="app1_elTitel">' . $lbs['name'] . '</td></tr>';
        for ($t = 1; $t <= count($tmp); $t++) {
            $preview .= '<tr>';
            if (!isset($tmp[$t][0])) {
                $tmp[$t][0] = '<td colspan="3" style="background:transparent;">&nbsp;</td>';
            }
            if (!isset($tmp[$t][1])) {
                $tmp[$t][1] = '<td colspan="2" style="background:transparent;">&nbsp;</td>';
            }
            if ($countIn == 0) {
                $tmp[$t][0] = '';
            }
            if ($countOut == 0) {
                $tmp[$t][1] = '';
            }
            $preview .= $tmp[$t][0] . $tmp[$t][1];
            $preview .= '</tr>';
        }
        $preview .= '</table>';
        $preview .= '</div>';
    }
    return $preview;
}

function vse_import($vseID, $echo = false)
{
    vse_delete($vseID);
    if (file_exists(MAIN_PATH . '/www/admin/vse/' . $vseID . '_vse.php')) {
        $tmp = vse_importHelper($vseID, $echo, false);
        sql_call("OPTIMIZE TABLE edomiProject.editVisuElementDef");
        return $tmp;
    }
    return false;
}

function vse_importAll($echo = false, $activation = false)
{
    $r = array(0, 0);
    sql_call("DELETE FROM edomiProject.editVisuElementDef");
    deleteFiles(MAIN_PATH . '/www/admin/help/1002-*.htm');
    deleteFiles(MAIN_PATH . '/www/admin/vse/vse_include_*.*');
    $vseIds = array();
    $tmp = glob(MAIN_PATH . '/www/admin/vse/*_vse.php');
    foreach ($tmp as $pathFn) {
        if (is_file($pathFn)) {
            $id = explode('_', basename($pathFn));
            if (isset($id[0]) && intVal($id[0]) > 0) {
                $vseIds[] = intVal($id[0]);
            }
        }
    }
    if (count($vseIds) > 0) {
        file_put_contents(MAIN_PATH . '/www/admin/vse/vse_include_admin.js', "\n");
        file_put_contents(MAIN_PATH . '/www/admin/vse/vse_include_admin.php', "\n");
        if ($activation) {
            deleteFiles(MAIN_PATH . '/www/data/liveproject/vse/vse_include_visu*.*');
            $tmp = "<?\n";
            $tmp .= 'require("../../../shared/php/config.php");' . "\n";
            $tmp .= 'require(MAIN_PATH."/www/shared/php/base.php");' . "\n";
            $tmp .= 'require(MAIN_PATH."/www/shared/php/incl_http.php");' . "\n";
            $tmp .= 'require(MAIN_PATH."/www/visu/include/php/config.php");' . "\n";
            $tmp .= 'require(MAIN_PATH."/www/visu/include/php/base.php");' . "\n";
            $tmp .= 'sql_connect();' . "\n";
            $tmp .= '$vseFUNCTION=\'PHP_VSE_\'.$vseId;' . "\n";
            $tmp .= 'if (function_exists($vseFUNCTION)) {' . "\n";
            $tmp .= '	if (checkVisuSid($visuId,$sid)) {$vseFUNCTION($cmd,$json1,$json2);}' . "\n";
            $tmp .= '}' . "\n";
            $tmp .= 'sql_disconnect();' . "\n";
            $tmp .= "?>\n";
            $ss1 = sql_call("SELECT id FROM edomiProject.editVisu ORDER BY id ASC");
            while ($visu = sql_result($ss1)) {
                file_put_contents(MAIN_PATH . '/www/data/liveproject/vse/vse_include_visu' . $visu['id'] . '.js', "\n");
                file_put_contents(MAIN_PATH . '/www/data/liveproject/vse/vse_include_visu' . $visu['id'] . '.php', "\n" . $tmp);
            }
            sql_close($ss1);
        }
    }
    sort($vseIds, SORT_NUMERIC);
    foreach ($vseIds as $vseID) {
        $tmp = vse_importHelper($vseID, $echo, $activation);
        if ($tmp !== false) {
            if ($tmp[0] == 0) {
                $r[0]++;
            } else {
                $r[1]++;
            }
        } else {
            $r[1]++;
        }
    }
    sql_call("OPTIMIZE TABLE edomiProject.editVisuElementDef");
    return $r;
}

function vse_delete($vseID)
{
    sql_call("DELETE FROM edomiProject.editVisuElementDef WHERE id=" . $vseID);
    deleteFiles(MAIN_PATH . '/www/admin/help/1002-' . $vseID . '.htm');
    $tmp = string_cutoutInverse(file_get_contents(MAIN_PATH . '/www/admin/vse/vse_include_admin.js'), "/*###[VSEID:" . $vseID . "]###*/\n", "\n/*###[/VSEID:" . $vseID . "]###*/\n");
    deleteFiles(MAIN_PATH . '/www/admin/vse/vse_include_admin.js');
    file_put_contents(MAIN_PATH . '/www/admin/vse/vse_include_admin.js', $tmp);
    $tmp = string_cutoutInverse(file_get_contents(MAIN_PATH . '/www/admin/vse/vse_include_admin.php'), "<?/*###[VSEID:" . $vseID . "]###*/\n", "/*###[/VSEID:" . $vseID . "]###*/?>\n");
    deleteFiles(MAIN_PATH . '/www/admin/vse/vse_include_admin.php');
    file_put_contents(MAIN_PATH . '/www/admin/vse/vse_include_admin.php', $tmp);
}

function vse_importHelper($vseID, $echo, $activation)
{
    if ($vseData = vse_parse($vseID)) {
        $vseData[2]['DEF_CONTENT']['name'] = substr($vseData[2]['DEF_CONTENT']['name'], 0, 40);
        if (!isset($vseData[2]['DEF_CONTENT']['flagko1']) || $vseData[2]['DEF_CONTENT']['flagko1'] != 1) {
            sql_call("UPDATE edomiProject.editVisuElement SET gaid=null WHERE controltyp=" . $vseID);
        }
        if (!isset($vseData[2]['DEF_CONTENT']['flagko2']) || $vseData[2]['DEF_CONTENT']['flagko2'] != 1) {
            sql_call("UPDATE edomiProject.editVisuElement SET gaid2=null WHERE controltyp=" . $vseID);
        }
        if (!isset($vseData[2]['DEF_CONTENT']['flagko3']) || $vseData[2]['DEF_CONTENT']['flagko3'] != 1) {
            sql_call("UPDATE edomiProject.editVisuElement SET gaid3=null WHERE controltyp=" . $vseID);
        }
        if (!isset($vseData[2]['DEF_CONTENT']['flagtext']) || $vseData[2]['DEF_CONTENT']['flagtext'] != 1) {
            sql_call("UPDATE edomiProject.editVisuElement SET text=null WHERE controltyp=" . $vseID);
        } else if (isset($vseData[2]['DEF_CONTENT']['flagtext']) && $vseData[2]['DEF_CONTENT']['flagtext'] == 1 && isset($vseData[2]['DEF_CONTENT']['text'])) {
            sql_call("UPDATE edomiProject.editVisuElement SET text='" . sql_encodeValue($vseData[2]['DEF_CONTENT']['text']) . "' WHERE controltyp=" . $vseID . " AND text IS NULL");
        }
        if (!isset($vseData[2]['DEF_CONTENT']['flagdesign']) || $vseData[2]['DEF_CONTENT']['flagdesign'] != 1) {
            $ss1 = sql_call("SELECT DISTINCT(b.targetid) AS anz1 FROM edomiProject.editVisuElement AS a, edomiProject.editVisuElementDesign AS b WHERE a.controltyp=" . $vseID . " AND b.targetid=a.id AND b.styletyp=0");
            while ($n = sql_result($ss1)) {
                sql_call("DELETE FROM edomiProject.editVisuElementDesign WHERE targetid=" . $n['anz1'] . " AND styletyp=0");
            }
            sql_close($ss1);
        }
        if (!isset($vseData[2]['DEF_CONTENT']['flagdyndesign']) || $vseData[2]['DEF_CONTENT']['flagdyndesign'] != 1) {
            $ss1 = sql_call("SELECT DISTINCT(b.targetid) AS anz1 FROM edomiProject.editVisuElement AS a, edomiProject.editVisuElementDesign AS b WHERE a.controltyp=" . $vseID . " AND b.targetid=a.id AND b.styletyp=1");
            while ($n = sql_result($ss1)) {
                sql_call("DELETE FROM edomiProject.editVisuElementDesign WHERE targetid=" . $n['anz1'] . " AND styletyp=1");
            }
            sql_close($ss1);
        }
        if (!isset($vseData[2]['DEF_CONTENT']['flagpage']) || $vseData[2]['DEF_CONTENT']['flagpage'] != 1) {
            sql_call("UPDATE edomiProject.editVisuElement SET gotopageid=null,closepopupid=null,closepopup=0 WHERE controltyp=" . $vseID);
        }
        if (!isset($vseData[2]['DEF_CONTENT']['flagcmd']) || $vseData[2]['DEF_CONTENT']['flagcmd'] != 1) {
            $ss1 = sql_call("SELECT DISTINCT(b.targetid) AS anz1 FROM edomiProject.editVisuElement AS a, edomiProject.editVisuCmdList AS b WHERE a.controltyp=" . $vseID . " AND b.targetid=a.id");
            while ($n = sql_result($ss1)) {
                sql_call("DELETE FROM edomiProject.editVisuCmdList WHERE targetid=" . $n['anz1']);
            }
            sql_close($ss1);
        }
        for ($t = 1; $t <= 20; $t++) {
            if (isset($vseData[2]['DEF_CONTENT']['var' . $t])) {
                sql_call("UPDATE edomiProject.editVisuElement SET var" . $t . "='" . sql_encodeValue($vseData[2]['DEF_CONTENT']['var' . $t]) . "' WHERE controltyp=" . $vseID . " AND var" . $t . " IS NULL");
            } else {
                sql_call("UPDATE edomiProject.editVisuElement SET var" . $t . "=null WHERE controltyp=" . $vseID);
            }
        }
        $hasActivation = trim(string_cutout($vseData[2]['ACTIVATION.PHP'], '<?', '?>'));
        $hasEditorPhp = trim(string_cutout($vseData[2]['EDITOR.PHP'], '<?', '?>'));
        $hasControlPhp = trim(string_cutout($vseData[2]['VISU.PHP'], '<?', '?>'));
        sql_save('edomiProject.editVisuElementDef', null, array('id' => $vseID, 'folderid' => ((isset($vseData[2]['DEF_CONTENT']['folderid']) && $vseData[2]['DEF_CONTENT']['folderid'] >= 160 && $vseData[2]['DEF_CONTENT']['folderid'] <= 170 && $vseID < 1000) ? intVal($vseData[2]['DEF_CONTENT']['folderid']) : 170), 'name' => sql_encodeValue($vseData[2]['DEF_CONTENT']['name'], true), 'errcount' => $vseData[0], 'errmsg' => sql_encodeValue($vseData[1], true), 'activationphp' => ((!isEmpty($hasActivation)) ? 1 : 0), 'editorphp' => ((!isEmpty($hasEditorPhp)) ? 1 : 0), 'controlphp' => ((!isEmpty($hasControlPhp)) ? 1 : 0), 'xsize' => ((isset($vseData[2]['DEF_CONTENT']['xsize']) && $vseData[2]['DEF_CONTENT']['xsize'] >= 1) ? intVal($vseData[2]['DEF_CONTENT']['xsize']) : 100), 'ysize' => ((isset($vseData[2]['DEF_CONTENT']['ysize']) && $vseData[2]['DEF_CONTENT']['ysize'] >= 1) ? intVal($vseData[2]['DEF_CONTENT']['ysize']) : 100), 'text' => ((isset($vseData[2]['DEF_CONTENT']['flagtext']) && $vseData[2]['DEF_CONTENT']['flagtext'] == 1 && isset($vseData[2]['DEF_CONTENT']['text'])) ? sql_encodeValue($vseData[2]['DEF_CONTENT']['text'], true) : 'null'), 'var1' => ((isset($vseData[2]['DEF_CONTENT']['var1'])) ? "'" . sql_encodeValue($vseData[2]['DEF_CONTENT']['var1']) . "'" : 'null'), 'var2' => ((isset($vseData[2]['DEF_CONTENT']['var2'])) ? "'" . sql_encodeValue($vseData[2]['DEF_CONTENT']['var2']) . "'" : 'null'), 'var3' => ((isset($vseData[2]['DEF_CONTENT']['var3'])) ? "'" . sql_encodeValue($vseData[2]['DEF_CONTENT']['var3']) . "'" : 'null'), 'var4' => ((isset($vseData[2]['DEF_CONTENT']['var4'])) ? "'" . sql_encodeValue($vseData[2]['DEF_CONTENT']['var4']) . "'" : 'null'), 'var5' => ((isset($vseData[2]['DEF_CONTENT']['var5'])) ? "'" . sql_encodeValue($vseData[2]['DEF_CONTENT']['var5']) . "'" : 'null'), 'var6' => ((isset($vseData[2]['DEF_CONTENT']['var6'])) ? "'" . sql_encodeValue($vseData[2]['DEF_CONTENT']['var6']) . "'" : 'null'), 'var7' => ((isset($vseData[2]['DEF_CONTENT']['var7'])) ? "'" . sql_encodeValue($vseData[2]['DEF_CONTENT']['var7']) . "'" : 'null'), 'var8' => ((isset($vseData[2]['DEF_CONTENT']['var8'])) ? "'" . sql_encodeValue($vseData[2]['DEF_CONTENT']['var8']) . "'" : 'null'), 'var9' => ((isset($vseData[2]['DEF_CONTENT']['var9'])) ? "'" . sql_encodeValue($vseData[2]['DEF_CONTENT']['var9']) . "'" : 'null'), 'var10' => ((isset($vseData[2]['DEF_CONTENT']['var10'])) ? "'" . sql_encodeValue($vseData[2]['DEF_CONTENT']['var10']) . "'" : 'null'), 'var11' => ((isset($vseData[2]['DEF_CONTENT']['var11'])) ? "'" . sql_encodeValue($vseData[2]['DEF_CONTENT']['var11']) . "'" : 'null'), 'var12' => ((isset($vseData[2]['DEF_CONTENT']['var12'])) ? "'" . sql_encodeValue($vseData[2]['DEF_CONTENT']['var12']) . "'" : 'null'), 'var13' => ((isset($vseData[2]['DEF_CONTENT']['var13'])) ? "'" . sql_encodeValue($vseData[2]['DEF_CONTENT']['var13']) . "'" : 'null'), 'var14' => ((isset($vseData[2]['DEF_CONTENT']['var14'])) ? "'" . sql_encodeValue($vseData[2]['DEF_CONTENT']['var14']) . "'" : 'null'), 'var15' => ((isset($vseData[2]['DEF_CONTENT']['var15'])) ? "'" . sql_encodeValue($vseData[2]['DEF_CONTENT']['var15']) . "'" : 'null'), 'var16' => ((isset($vseData[2]['DEF_CONTENT']['var16'])) ? "'" . sql_encodeValue($vseData[2]['DEF_CONTENT']['var16']) . "'" : 'null'), 'var17' => ((isset($vseData[2]['DEF_CONTENT']['var17'])) ? "'" . sql_encodeValue($vseData[2]['DEF_CONTENT']['var17']) . "'" : 'null'), 'var18' => ((isset($vseData[2]['DEF_CONTENT']['var18'])) ? "'" . sql_encodeValue($vseData[2]['DEF_CONTENT']['var18']) . "'" : 'null'), 'var19' => ((isset($vseData[2]['DEF_CONTENT']['var19'])) ? "'" . sql_encodeValue($vseData[2]['DEF_CONTENT']['var19']) . "'" : 'null'), 'var20' => ((isset($vseData[2]['DEF_CONTENT']['var20'])) ? "'" . sql_encodeValue($vseData[2]['DEF_CONTENT']['var20']) . "'" : 'null'), 'var1root' => ((isset($vseData[2]['DEF_CONTENT']['var1root'])) ? sql_encodeValue($vseData[2]['DEF_CONTENT']['var1root'], true) : 'null'), 'var2root' => ((isset($vseData[2]['DEF_CONTENT']['var2root'])) ? sql_encodeValue($vseData[2]['DEF_CONTENT']['var2root'], true) : 'null'), 'var3root' => ((isset($vseData[2]['DEF_CONTENT']['var3root'])) ? sql_encodeValue($vseData[2]['DEF_CONTENT']['var3root'], true) : 'null'), 'var4root' => ((isset($vseData[2]['DEF_CONTENT']['var4root'])) ? sql_encodeValue($vseData[2]['DEF_CONTENT']['var4root'], true) : 'null'), 'var5root' => ((isset($vseData[2]['DEF_CONTENT']['var5root'])) ? sql_encodeValue($vseData[2]['DEF_CONTENT']['var5root'], true) : 'null'), 'var6root' => ((isset($vseData[2]['DEF_CONTENT']['var6root'])) ? sql_encodeValue($vseData[2]['DEF_CONTENT']['var6root'], true) : 'null'), 'var7root' => ((isset($vseData[2]['DEF_CONTENT']['var7root'])) ? sql_encodeValue($vseData[2]['DEF_CONTENT']['var7root'], true) : 'null'), 'var8root' => ((isset($vseData[2]['DEF_CONTENT']['var8root'])) ? sql_encodeValue($vseData[2]['DEF_CONTENT']['var8root'], true) : 'null'), 'var9root' => ((isset($vseData[2]['DEF_CONTENT']['var9root'])) ? sql_encodeValue($vseData[2]['DEF_CONTENT']['var9root'], true) : 'null'), 'var10root' => ((isset($vseData[2]['DEF_CONTENT']['var10root'])) ? sql_encodeValue($vseData[2]['DEF_CONTENT']['var10root'], true) : 'null'), 'var11root' => ((isset($vseData[2]['DEF_CONTENT']['var11root'])) ? sql_encodeValue($vseData[2]['DEF_CONTENT']['var11root'], true) : 'null'), 'var12root' => ((isset($vseData[2]['DEF_CONTENT']['var12root'])) ? sql_encodeValue($vseData[2]['DEF_CONTENT']['var12root'], true) : 'null'), 'var13root' => ((isset($vseData[2]['DEF_CONTENT']['var13root'])) ? sql_encodeValue($vseData[2]['DEF_CONTENT']['var13root'], true) : 'null'), 'var14root' => ((isset($vseData[2]['DEF_CONTENT']['var14root'])) ? sql_encodeValue($vseData[2]['DEF_CONTENT']['var14root'], true) : 'null'), 'var15root' => ((isset($vseData[2]['DEF_CONTENT']['var15root'])) ? sql_encodeValue($vseData[2]['DEF_CONTENT']['var15root'], true) : 'null'), 'var16root' => ((isset($vseData[2]['DEF_CONTENT']['var16root'])) ? sql_encodeValue($vseData[2]['DEF_CONTENT']['var16root'], true) : 'null'), 'var17root' => ((isset($vseData[2]['DEF_CONTENT']['var17root'])) ? sql_encodeValue($vseData[2]['DEF_CONTENT']['var17root'], true) : 'null'), 'var18root' => ((isset($vseData[2]['DEF_CONTENT']['var18root'])) ? sql_encodeValue($vseData[2]['DEF_CONTENT']['var18root'], true) : 'null'), 'var19root' => ((isset($vseData[2]['DEF_CONTENT']['var19root'])) ? sql_encodeValue($vseData[2]['DEF_CONTENT']['var19root'], true) : 'null'), 'var20root' => ((isset($vseData[2]['DEF_CONTENT']['var20root'])) ? sql_encodeValue($vseData[2]['DEF_CONTENT']['var20root'], true) : 'null'), 'flagtext' => ((isset($vseData[2]['DEF_CONTENT']['flagtext'])) ? getNearestNumericValue($vseData[2]['DEF_CONTENT']['flagtext'], '0,1') : 0), 'flagko1' => ((isset($vseData[2]['DEF_CONTENT']['flagko1'])) ? getNearestNumericValue($vseData[2]['DEF_CONTENT']['flagko1'], '0,1,2') : 0), 'flagko2' => ((isset($vseData[2]['DEF_CONTENT']['flagko2'])) ? getNearestNumericValue($vseData[2]['DEF_CONTENT']['flagko2'], '0,1') : 0), 'flagko3' => ((isset($vseData[2]['DEF_CONTENT']['flagko3'])) ? getNearestNumericValue($vseData[2]['DEF_CONTENT']['flagko3'], '0,1') : 0), 'flagpage' => ((isset($vseData[2]['DEF_CONTENT']['flagpage'])) ? getNearestNumericValue($vseData[2]['DEF_CONTENT']['flagpage'], '0,1') : 0), 'flagcmd' => ((isset($vseData[2]['DEF_CONTENT']['flagcmd'])) ? getNearestNumericValue($vseData[2]['DEF_CONTENT']['flagcmd'], '0,1') : 0), 'flagdesign' => ((isset($vseData[2]['DEF_CONTENT']['flagdesign'])) ? getNearestNumericValue($vseData[2]['DEF_CONTENT']['flagdesign'], '0,1') : 0), 'flagdyndesign' => ((isset($vseData[2]['DEF_CONTENT']['flagdyndesign'])) ? getNearestNumericValue($vseData[2]['DEF_CONTENT']['flagdyndesign'], '0,1') : 0), 'flagsound' => ((isset($vseData[2]['DEF_CONTENT']['flagsound'])) ? getNearestNumericValue($vseData[2]['DEF_CONTENT']['flagsound'], '0,1') : 0), 'flagspeech' => ((isset($vseData[2]['DEF_CONTENT']['flagspeech'])) ? getNearestNumericValue($vseData[2]['DEF_CONTENT']['flagspeech'], '0,1') : 0), 'captiontext' => ((isset($vseData[2]['DEF_CONTENT']['captiontext'])) ? sql_encodeValue($vseData[2]['DEF_CONTENT']['captiontext'], true) : 'null'), 'captionko1' => ((isset($vseData[2]['DEF_CONTENT']['captionko1'])) ? sql_encodeValue($vseData[2]['DEF_CONTENT']['captionko1'], true) : "'Steuerung'"), 'captionko2' => ((isset($vseData[2]['DEF_CONTENT']['captionko2'])) ? sql_encodeValue($vseData[2]['DEF_CONTENT']['captionko2'], true) : "'Wert setzen'"), 'captionko3' => 'null'));
        if ($vseData[0] == 0) {
            $tmp = "/*###[VSEID:" . $vseID . "]###*/\n";
            $tmp .= $vseData[2]['EDITOR.JS'];
            if (!isEmpty(trim($vseData[2]['SHARED.JS']))) {
                $tmp .= "\n" . $vseData[2]['SHARED.JS'];
            }
            $tmp .= "\n/*###[/VSEID:" . $vseID . "]###*/\n";
            file_put_contents(MAIN_PATH . '/www/admin/vse/vse_include_admin.js', $tmp, FILE_APPEND);
            $tmp = "<?/*###[VSEID:" . $vseID . "]###*/\n";
            $tmp .= "function PHP_VSE_" . $vseID . "_PROPERTIES() {\nglobal \$winId;\n?>\n" . $vseData[2]['PROPERTIES'] . "\n<?\n}\n";
            $tmp .= "function PHP_VSE_" . $vseID . "_EDITOR_PHP(\$item) {\n\$property=array();\n?>" . $vseData[2]['EDITOR.PHP'] . "<?\nreturn \$property;\n}\n";
            $tmp .= "function PHP_VSE_" . $vseID . "_ACTIVATION_PHP(\$item) {\nglobal \$global_dptData;\n?>" . $vseData[2]['ACTIVATION.PHP'] . "<?\n}\n";
            $tmp .= "/*###[/VSEID:" . $vseID . "]###*/?>\n";
            file_put_contents(MAIN_PATH . '/www/admin/vse/vse_include_admin.php', $tmp, FILE_APPEND);
            $vseData[2]['HELP'] = "<h1><path>Administrationsseite***0-1</path><path>Visueditor***2</path><path>Visuelemente***1002</path>" . $vseData[2]['DEF_CONTENT']['name'] . " <span class=\"id\">" . $vseID . "</span></h1>\n" . $vseData[2]['HELP'];
            $f = fopen(MAIN_PATH . '/www/admin/help/1002-' . $vseID . '.htm', 'w');
            fwrite($f, $vseData[2]['HELP']);
            fclose($f);
            if ($activation) {
                $ss1 = sql_call("SELECT distinct(a.visuid) FROM edomiProject.editVisuElement AS a,edomiProject.editVisu AS b WHERE a.controltyp=" . $vseID . " AND a.visuid=b.id");
                while ($n = sql_result($ss1)) {
                    $tmp = $vseData[2]['VISU.JS'] . "\n";
                    if (!isEmpty(trim($vseData[2]['SHARED.JS']))) {
                        $tmp .= "\n" . $vseData[2]['SHARED.JS'] . "\n";
                    }
                    file_put_contents(MAIN_PATH . '/www/data/liveproject/vse/vse_include_visu' . $n['visuid'] . '.js', $tmp, FILE_APPEND);
                    file_put_contents(MAIN_PATH . '/www/data/liveproject/vse/vse_include_visu' . $n['visuid'] . '.php', $vseData[2]['VISU.PHP'], FILE_APPEND);
                }
                sql_close($ss1);
            }
            if ($echo === true) {
                echo 'VSE ' . $vseID . ": Ok \n";
            } else if ($echo !== false) {
                writeToTmpLog($echo, ajaxValueHTML($vseData[2]['DEF_CONTENT']['name']) . " <span class='id'>" . $vseID . "</span>: Ok", false);
            }
        } else {
            if ($echo === true) {
                echo 'VSE ' . $vseID . ": Fehler \n";
            } else if ($echo !== false) {
                writeToTmpLog($echo, ajaxValueHTML($vseData[2]['DEF_CONTENT']['name']) . " <span class='id'>" . $vseID . "</span>: " . $vseData[0] . " Fehler", true);
            }
        }
    }
    return $vseData;
}

function vse_parse($vseID)
{
    $vseFilename = MAIN_PATH . '/www/admin/vse/' . $vseID . '_vse.php';
    if (is_numeric($vseID) && file_exists($vseFilename)) {
        $vseRAW = file_get_contents($vseFilename);
        if (getFileEncoding($vseFilename) != 'utf-8') {
            $vseRAW = utf8_encode($vseRAW);
            file_put_contents($vseFilename, $vseRAW);
        }
        $vseRAW = str_replace('VSEID', $vseID, $vseRAW);
        $err = 0;
        $errInfo = '';
        $section = array();
        $section['DEF'] = '';
        $section['DEF_CONTENT'] = array();
        $tmp = string_cutout($vseRAW, '###[DEF]###', '###[/DEF]###');
        if ($tmp !== false) {
            $section['DEF'] = trim($tmp);
            if (preg_match_all("'\[(.*?)\]'s", $section['DEF'], $tmp) > 0) {
                for ($t = 0; $t < count($tmp[0]); $t++) {
                    $def = explode('=', $tmp[1][$t], 2);
                    if (count($def) == 2) {
                        $vPara = strtolower(trim($def[0]));
                        $vValue = explode('#root=', trim($def[1]));
                        if (!isEmpty($vPara)) {
                            $section['DEF_CONTENT'][$vPara] = trim($vValue[0]);
                            if (isset($vValue[1])) {
                                $section['DEF_CONTENT'][$vPara . 'root'] = trim($vValue[1]);
                            }
                        }
                    }
                }
            }
            if (!isset($section['DEF_CONTENT']['name']) || isEmpty($section['DEF_CONTENT']['name'])) {
                $err++;
                $errInfo .= 'kein Name deklariert' . "\n";
            }
        } else {
            $err++;
            $errInfo .= 'kein Abschnitt [DEF] vorhanden' . "\n";
        }
        if (!isset($section['DEF_CONTENT']['name']) || isEmpty($section['DEF_CONTENT']['name'])) {
            $section['DEF_CONTENT']['name'] = 'ohne Name';
        }
        $section['PROPERTIES'] = '';
        $tmp = string_cutout($vseRAW, '###[PROPERTIES]###', '###[/PROPERTIES]###');
        if ($tmp !== false) {
            $maxColumns = 1;
            $rowCount = 0;
            $n = "n+=\"<table width='100%' border='0' cellpadding='2' cellspacing='0'>\";\n";
            if (preg_match_all("'\[(.*?)\]'s", $tmp, $raw) > 0) {
                for ($t = 0; $t < count($raw[0]); $t++) {
                    $def = explode('=', $raw[1][$t], 2);
                    if (!isset($def[1])) {
                        $def[1] = '';
                    }
                    if (count($def) == 2) {
                        $vPara = strtolower(trim($def[0]));
                        $vValue = trim($def[1]);
                        if (!isEmpty($vPara)) {
                            if ($vPara == 'columns') {
                                $n .= "n+=\"<colgroup>\";\n";
                                $col = explode(',', $vValue);
                                for ($tt = 0; $tt < count($col); $tt++) {
                                    $n .= "n+=\"<col width='" . trim($col[$tt]) . "%'>\";\n";
                                }
                                $n .= "n+=\"</colgroup>\";\n";
                                $maxColumns = count($col);
                            }
                            if ($vPara == 'row') {
                                if ($rowCount > 0) {
                                    $n .= "n+=\"</tr>\";\n";
                                }
                                if (isEmpty($vValue)) {
                                    $n .= "n+=\"<tr>\";\n";
                                } else {
                                    $n .= "n+=\"<tr><td colspan='" . $maxColumns . "' class='formSubTitel2'>" . escapeString($vValue, 1) . "<hr></td></tr>\";\n";
                                    $n .= "n+=\"<tr>\";\n";
                                }
                                $rowCount++;
                            }
                            if (substr($vPara, 0, 3) == 'var') {
                                $varId = intVal(preg_replace("/[^0-9]/", '', $vPara));
                                if ($varId >= 1 && $varId <= 20) {
                                    $csv = str_getcsv($vValue, ',', "'");
                                    if (count($csv) == 4) {
                                        $csv[0] = escapeString($csv[0]);
                                        $csv[1] = escapeString($csv[1]);
                                        $csv[2] = escapeString($csv[2], 1);
                                        if (trim(strToLower($csv[0])) == 'root') {
                                            $n .= "n+=\"<td colspan='" . trim($csv[1]) . "'>" . ((!isEmpty(trim($csv[2]))) ? trim($csv[2]) . '<br>' : '') . "<div id='<?echo \$winId;?>-fd" . ($varId + 30) . "' data-type='1000' data-value='' data-root='" . trim($csv[3]) . "' data-options='typ=1' class='control10' style='width:100%;'></div></td>\";\n";
                                        }
                                        if (trim(strToLower($csv[0])) == 'text') {
                                            $n .= "n+=\"<td colspan='" . trim($csv[1]) . "'>" . ((!isEmpty(trim($csv[2]))) ? trim($csv[2]) . '<br>' : '') . "<input id='<?echo \$winId;?>-fd" . ($varId + 30) . "' data-type='10' value='' maxlength='1000' type='text' placeholder='' class='control1' style='width:100%;'></input></td>\";\n";
                                            $n .= "option[" . ($varId + 30) . "]=\"" . escapeString($csv[3]) . "\";\n";
                                        }
                                        if (trim(strToLower($csv[0])) == 'checkmulti') {
                                            $n .= "n+=\"<td colspan='" . trim($csv[1]) . "'>" . ((!isEmpty(trim($csv[2]))) ? trim($csv[2]) . '<br>' : '') . "<div id='<?echo \$winId;?>-fd" . ($varId + 30) . "' data-type='11' data-value='' data-list='' class='control5' style='width:100%; border-color:#c9c9c9;'></div></td>\";\n";
                                            $n .= "option[" . ($varId + 30) . "]=\"" . escapeString($csv[3], 1) . "\";\n";
                                        }
                                        if (trim(strToLower($csv[0])) == 'check') {
                                            $n .= "n+=\"<td colspan='" . trim($csv[1]) . "'>" . ((!isEmpty(trim($csv[2]))) ? trim($csv[2]) . '<br>' : '') . "<div id='<?echo \$winId;?>-fd" . ($varId + 30) . "' data-type='12' data-value='' class='control5' style='width:100%; border-color:#c9c9c9;'></div></td>\";\n";
                                            $n .= "option[" . ($varId + 30) . "]=\"" . escapeString($csv[3], 1) . "\";\n";
                                        }
                                        if (trim(strToLower($csv[0])) == 'select') {
                                            $n .= "n+=\"<td colspan='" . trim($csv[1]) . "'>" . ((!isEmpty(trim($csv[2]))) ? trim($csv[2]) . '<br>' : '') . "<div id='<?echo \$winId;?>-fd" . ($varId + 30) . "' data-type='13' data-value='' data-list='' class='control6' style='width:100%;'></div></td>\";\n";
                                            $n .= "option[" . ($varId + 30) . "]=\"" . escapeString($csv[3], 1) . "\";\n";
                                        }
                                    }
                                } else {
                                    $err++;
                                    $errInfo .= 'Abschnitt [PROPERTIES] fehlerhaft: ungültige var-ID' . "\n";
                                }
                            }
                        }
                    }
                }
            }
            if ($rowCount > 0) {
                $n .= "n+=\"</tr>\";\n";
            }
            $n .= "n+=\"</table>\";\n";
            $section['PROPERTIES'] = trim($n);
        } else {
            $err++;
            $errInfo .= 'kein Abschnitt [PROPERTIES] vorhanden' . "\n";
        }
        $section['ACTIVATION.PHP'] = '';
        $tmp = string_cutout($vseRAW, '###[ACTIVATION.PHP]###', '###[/ACTIVATION.PHP]###');
        if ($tmp !== false) {
            $section['ACTIVATION.PHP'] = trim($tmp);
        }
        $section['SHARED.JS'] = '';
        $tmp = string_cutout($vseRAW, '###[SHARED.JS]###', '###[/SHARED.JS]###');
        if ($tmp !== false) {
            $section['SHARED.JS'] = trim($tmp);
        }
        $section['EDITOR.PHP'] = '';
        $tmp = string_cutout($vseRAW, '###[EDITOR.PHP]###', '###[/EDITOR.PHP]###');
        if ($tmp !== false) {
            $section['EDITOR.PHP'] = trim($tmp);
        }
        $section['EDITOR.JS'] = '';
        $tmp = string_cutout($vseRAW, '###[EDITOR.JS]###', '###[/EDITOR.JS]###');
        if ($tmp !== false) {
            $section['EDITOR.JS'] = trim($tmp);
        } else {
            $err++;
            $errInfo .= 'kein Abschnitt [EDITOR.JS] vorhanden' . "\n";
        }
        $section['VISU.PHP'] = '';
        $tmp = string_cutout($vseRAW, '###[VISU.PHP]###', '###[/VISU.PHP]###');
        if ($tmp !== false) {
            $tmp = preg_replace("/visuElement_callPhp\s*\(/", 'visuElement_callPhp(' . $vseID . ',', $tmp);
            $section['VISU.PHP'] = trim($tmp);
        }
        $section['VISU.JS'] = '';
        $tmp = string_cutout($vseRAW, '###[VISU.JS]###', '###[/VISU.JS]###');
        if ($tmp !== false) {
            $tmp = preg_replace("/visuElement_callPhp\s*\(/", 'visuElement_callPhp(' . $vseID . ',', $tmp);
            $section['VISU.JS'] = trim($tmp);
        } else {
            $err++;
            $errInfo .= 'kein Abschnitt [VISU.JS] vorhanden' . "\n";
        }
        $section['HELP'] = '';
        $tmp = string_cutout($vseRAW, '###[HELP]###', '###[/HELP]###');
        if ($tmp !== false) {
            $section['HELP'] = trim($tmp);
        } else {
            $err++;
            $errInfo .= 'kein Abschnitt [HELP] vorhanden' . "\n";
        }
        return array($err, $errInfo, $section);
    } else {
        return false;
    }
}

function initControls($cmd)
{
    global $appId, $winId, $data, $dataArr, $phpdata, $phpdataArr, $sid;
    global $global_weekdays, $global_charttyp;
    if ($cmd == 'initControls') {
        $n = explode(AJAX_SEPARATOR2, $data, -1);
        for ($t = 0; $t < count($n); $t++) {
            $dataArr = explode(AJAX_SEPARATOR1, $n[$t]);
            $tmp = array_shift($dataArr);
            $data = implode(AJAX_SEPARATOR1, $dataArr);
            initControls($tmp);
        }
    } else if ($cmd == 'initControl-1000') {
        $root = explode('_', $dataArr[1]);
        $options = array();
        $tmp = explode(';', $dataArr[3]);
        for ($t = 0; $t < count($tmp); $t++) {
            $tmp2 = explode('=', $tmp[$t]);
            if (!isEmpty(trim($tmp2[0]))) {
                $options[trim($tmp2[0])] = $tmp2[1];
            }
        }
        $n = false;
        if ($root[0] == 11) {
            $n = sql_getValues('edomiProject.editLogicPage', 'id,name,pagestatus', 'id=' . $dataArr[2]);
            if ($n !== false) {
                if ($n['pagestatus'] == 0) {
                    echo 'document.getElementById("' . $dataArr[0] . '").innerHTML="<span style=\"color:#a0a0a0;\">' . ajaxEncode($n['name']) . '</span> <span class=\"id\">' . $n['id'] . '</span>";';
                } else {
                    echo 'document.getElementById("' . $dataArr[0] . '").innerHTML="<span style=\"color:#409000;\">' . ajaxEncode($n['name']) . '</span> <span class=\"id\">' . $n['id'] . '</span>";';
                }
            }
        } else if ($root[0] == 12) {
            $n = sql_getValues('edomiProject.editLogicElementDef', 'id,name,errcount', 'id=' . $dataArr[2]);
            if ($n !== false) {
                if (isEmpty($options['caption'])) {
                    if ($n['errcount'] == 0) {
                        echo 'document.getElementById("' . $dataArr[0] . '").innerHTML="' . ajaxEncode($n['name']) . ' <span class=\"id\">' . $n['id'] . '</span>";';
                    } else {
                        echo 'document.getElementById("' . $dataArr[0] . '").innerHTML="<span style=\"color:#ff0000;\">' . ajaxEncode($n['name']) . '</span> <span class=\"id\">' . $n['id'] . '</span>";';
                    }
                }
            }
        } else if ($root[0] == 21) {
            $n = sql_getValues('edomiProject.editVisu', 'id,name', 'id=' . $dataArr[2]);
            if ($n !== false) {
                echo 'document.getElementById("' . $dataArr[0] . '").innerHTML="' . ajaxEncode($n['name']) . ' <span class=\"id\">' . $n['id'] . '</span>";';
            }
        } else if ($root[0] == 22) {
            $n = sql_getValues('edomiProject.editVisuPage', 'id,name', 'id=' . $dataArr[2]);
            if ($n !== false) {
                echo 'document.getElementById("' . $dataArr[0] . '").innerHTML="' . ajaxEncode($n['name']) . ' <span class=\"id\">' . $n['id'] . '</span>";';
            }
        } else if ($root[0] == 23) {
            $n = sql_getValues('edomiProject.editVisuUser', 'id,login', 'id=' . $dataArr[2]);
            if ($n !== false) {
                echo 'document.getElementById("' . $dataArr[0] . '").innerHTML="' . ajaxEncode($n['login']) . ' <span class=\"id\">' . $n['id'] . '</span>";';
            }
        } else if ($root[0] == 24) {
            $n = sql_getValues('edomiProject.editVisuElementDesignDef', 'id,name,styletyp,s1,s2', 'id=' . $dataArr[2]);
            if ($n !== false) {
                if ($n['styletyp'] == 1) {
                    $tmp = ' <span class=\"varItem\" style=\"color:#ff0000;\">' . ajaxEncode($n['s1']) . ' &gt; ' . ajaxEncode($n['s2']) . '</span>';
                } else {
                    $tmp = '';
                }
                echo 'document.getElementById("' . $dataArr[0] . '").innerHTML="' . ajaxEncode($n['name']) . ' <span class=\"id\">' . $n['id'] . '</span>' . $tmp . '";';
            }
        } else if ($root[0] == 25) {
            $n = sql_getValues('edomiProject.editVisuBGcol', 'id,name,color', 'id=' . $dataArr[2]);
            if ($n !== false) {
                if (strpos($n['color'], '{') === false) {
                    echo 'document.getElementById("' . $dataArr[0] . '").innerHTML="<span class=\"colorPreview1\" style=\"background:' . ajaxEncode($n['color']) . ';\"></span> ' . ajaxEncode($n['name']) . ' <span class=\"id\">' . $n['id'] . '</span>";';
                } else {
                    echo 'document.getElementById("' . $dataArr[0] . '").innerHTML="<span class=\"colorPreview2\"></span> ' . ajaxEncode($n['name']) . ' <span class=\"id\">' . $n['id'] . '</span>";';
                }
            }
        } else if ($root[0] == 26) {
            $n = sql_getValues('edomiProject.editVisuFGcol', 'id,name,color', 'id=' . $dataArr[2]);
            if ($n !== false) {
                if (strpos($n['color'], '{') === false) {
                    echo 'document.getElementById("' . $dataArr[0] . '").innerHTML="<span class=\"colorPreview1\" style=\"background:' . ajaxEncode($n['color']) . ';\"></span> ' . ajaxEncode($n['name']) . ' <span class=\"id\">' . $n['id'] . '</span>";';
                } else {
                    echo 'document.getElementById("' . $dataArr[0] . '").innerHTML="<span class=\"colorPreview2\"></span> ' . ajaxEncode($n['name']) . ' <span class=\"id\">' . $n['id'] . '</span>";';
                }
            }
        } else if ($root[0] == 27) {
            $n = sql_getValues('edomiProject.editVisuAnim', 'id,name', 'id=' . $dataArr[2]);
            if ($n !== false) {
                echo 'document.getElementById("' . $dataArr[0] . '").innerHTML="' . ajaxEncode($n['name']) . ' <span class=\"id\">' . $n['id'] . '</span>";';
            }
        } else if ($root[0] == 28) {
            $n = sql_getValues('edomiProject.editVisuImg', 'id,name', 'id=' . $dataArr[2]);
            if ($n !== false) {
                echo 'document.getElementById("' . $dataArr[0] . '").innerHTML="' . ajaxEncode($n['name']) . ' <span class=\"id\">' . $n['id'] . '</span>";';
            }
        } else if ($root[0] == 29) {
            $n = sql_getValues('edomiProject.editVisuSnd', 'id,name', 'id=' . $dataArr[2]);
            if ($n !== false) {
                echo 'document.getElementById("' . $dataArr[0] . '").innerHTML="' . ajaxEncode($n['name']) . ' <span class=\"id\">' . $n['id'] . '</span>";';
            }
        } else if ($root[0] == 30) {
            $n = sql_getValues('edomiProject.editKo', 'id,ga,name,gatyp', 'id=' . $dataArr[2]);
            if ($n !== false) {
                echo 'document.getElementById("' . $dataArr[0] . '").innerHTML="' . ajaxEncode($n['name']) . ' <span class=\"idGa' . $n['gatyp'] . '\">' . ajaxEncode($n['ga']) . '</span>";';
            }
        } else if ($root[0] == 40) {
            $n = sql_getValues('edomiProject.editScene', 'id,name', 'id=' . $dataArr[2]);
            if ($n !== false) {
                echo 'document.getElementById("' . $dataArr[0] . '").innerHTML="' . ajaxEncode($n['name']) . ' <span class=\"id\">' . $n['id'] . '</span>";';
            }
        } else if ($root[0] == 50) {
            $n = sql_getValues('edomiProject.editArchivKo', 'id,name', 'id=' . $dataArr[2]);
            if ($n !== false) {
                echo 'document.getElementById("' . $dataArr[0] . '").innerHTML="' . ajaxEncode($n['name']) . ' <span class=\"id\">' . $n['id'] . '</span>";';
            }
        } else if ($root[0] == 60) {
            $n = sql_getValues('edomiProject.editArchivMsg', 'id,name', 'id=' . $dataArr[2]);
            if ($n !== false) {
                echo 'document.getElementById("' . $dataArr[0] . '").innerHTML="' . ajaxEncode($n['name']) . ' <span class=\"id\">' . $n['id'] . '</span>";';
            }
        } else if ($root[0] == 70) {
            $n = sql_getValues('edomiProject.editIp', 'id,name', 'id=' . $dataArr[2]);
            if ($n !== false) {
                echo 'document.getElementById("' . $dataArr[0] . '").innerHTML="' . ajaxEncode($n['name']) . ' <span class=\"id\">' . $n['id'] . '</span>";';
            }
        } else if ($root[0] == 75) {
            $n = sql_getValues('edomiProject.editIr', 'id,name', 'id=' . $dataArr[2]);
            if ($n !== false) {
                echo 'document.getElementById("' . $dataArr[0] . '").innerHTML="' . ajaxEncode($n['name']) . ' <span class=\"id\">' . $n['id'] . '</span>";';
            }
        } else if ($root[0] == 81) {
            $n = sql_getValues('edomiProject.editCam', 'id,name', 'id=' . $dataArr[2]);
            if ($n !== false) {
                echo 'document.getElementById("' . $dataArr[0] . '").innerHTML="' . ajaxEncode($n['name']) . ' <span class=\"id\">' . $n['id'] . '</span>";';
            }
        } else if ($root[0] == 82) {
            $n = sql_getValues('edomiProject.editArchivCam', 'id,camid,name', 'id=' . $dataArr[2]);
            if ($n !== false) {
                echo 'document.getElementById("' . $dataArr[0] . '").innerHTML="' . ajaxEncode($n['name']) . ' <span class=\"id\">' . $n['id'] . '</span>";';
            }
        } else if ($root[0] == 83) {
            $n = sql_getValues('edomiProject.editCamView', 'id,name', 'id=' . $dataArr[2]);
            if ($n !== false) {
                echo 'document.getElementById("' . $dataArr[0] . '").innerHTML="' . ajaxEncode($n['name']) . ' <span class=\"id\">' . $n['id'] . '</span>";';
            }
        } else if ($root[0] == 90) {
            $n = sql_getValues('edomiProject.editSequence', 'id,name', 'id=' . $dataArr[2]);
            if ($n !== false) {
                echo 'document.getElementById("' . $dataArr[0] . '").innerHTML="' . ajaxEncode($n['name']) . ' <span class=\"id\">' . $n['id'] . '</span>";';
            }
        } else if ($root[0] == 95) {
            $n = sql_getValues('edomiProject.editMacro', 'id,name', 'id=' . $dataArr[2]);
            if ($n !== false) {
                echo 'document.getElementById("' . $dataArr[0] . '").innerHTML="' . ajaxEncode($n['name']) . ' <span class=\"id\">' . $n['id'] . '</span>";';
            }
        } else if ($root[0] == 100) {
            $n = sql_getValues('edomiProject.editTimer', 'id,name', 'id=' . $dataArr[2]);
            if ($n !== false) {
                echo 'document.getElementById("' . $dataArr[0] . '").innerHTML="' . ajaxEncode($n['name']) . ' <span class=\"id\">' . $n['id'] . '</span>";';
            }
        } else if ($root[0] == 101) {
            $n = sql_getValues('edomiProject.editAgenda', 'id,name', 'id=' . $dataArr[2]);
            if ($n !== false) {
                echo 'document.getElementById("' . $dataArr[0] . '").innerHTML="' . ajaxEncode($n['name']) . ' <span class=\"id\">' . $n['id'] . '</span>";';
            }
        } else if ($root[0] == 110) {
            $n = sql_getValues('edomiProject.editAws', 'id,name', 'id=' . $dataArr[2]);
            if ($n !== false) {
                echo 'document.getElementById("' . $dataArr[0] . '").innerHTML="' . ajaxEncode($n['name']) . ' <span class=\"id\">' . $n['id'] . '</span>";';
            }
        } else if ($root[0] == 120) {
            $n = sql_getValues('edomiProject.editEmail', 'id,name', 'id=' . $dataArr[2]);
            if ($n !== false) {
                echo 'document.getElementById("' . $dataArr[0] . '").innerHTML="' . ajaxEncode($n['name']) . ' <span class=\"id\">' . $n['id'] . '</span>";';
            }
        } else if ($root[0] == 125) {
            $n = sql_getValues('edomiProject.editPhoneBook', 'id,name', 'id=' . $dataArr[2]);
            if ($n !== false) {
                echo 'document.getElementById("' . $dataArr[0] . '").innerHTML="' . ajaxEncode($n['name']) . ' <span class=\"id\">' . $n['id'] . '</span>";';
            }
        } else if ($root[0] == 127) {
            $n = sql_getValues('edomiProject.editArchivPhone', 'id,name', 'id=' . $dataArr[2]);
            if ($n !== false) {
                echo 'document.getElementById("' . $dataArr[0] . '").innerHTML="' . ajaxEncode($n['name']) . ' <span class=\"id\">' . $n['id'] . '</span>";';
            }
        } else if ($root[0] == 130) {
            $n = sql_getValues('edomiProject.editChart', 'id,name', 'id=' . $dataArr[2]);
            if ($n !== false) {
                echo 'document.getElementById("' . $dataArr[0] . '").innerHTML="' . ajaxEncode($n['name']) . ' <span class=\"id\">' . $n['id'] . '</span>";';
            }
        } else if ($root[0] == 140) {
            $n = sql_getValues('edomiProject.editHttpKo', 'id,name', 'id=' . $dataArr[2]);
            if ($n !== false) {
                echo 'document.getElementById("' . $dataArr[0] . '").innerHTML="' . ajaxEncode($n['name']) . ' <span class=\"id\">' . $n['id'] . '</span>";';
            }
        } else if ($root[0] == 150) {
            $n = sql_getValues('edomiProject.editVisuFont', 'id,name', 'id=' . $dataArr[2]);
            if ($n !== false) {
                echo 'document.getElementById("' . $dataArr[0] . '").innerHTML="' . ajaxEncode($n['name']) . ' <span class=\"id\">' . $n['id'] . '</span>";';
            }
        } else if ($root[0] == 155) {
            $n = sql_getValues('edomiProject.editVisuFormat', 'id,name', 'id=' . $dataArr[2]);
            if ($n !== false) {
                echo 'document.getElementById("' . $dataArr[0] . '").innerHTML="' . ajaxEncode($n['name']) . ' <span class=\"id\">' . $n['id'] . '</span>";';
            }
        }
        if ($n === false) {
            if (isEmpty($options['caption'])) {
                echo 'document.getElementById("' . $dataArr[0] . '").innerHTML="<span style=\"color:#a0a0a0;\">&lt;leer&gt;</span>";';
            } else {
                echo 'document.getElementById("' . $dataArr[0] . '").innerHTML="' . $options['caption'] . '";';
            }
        }
    } else if ($cmd == 'initControl-1001') {
        echo 'clearObject("' . $dataArr[0] . '",0);';
        $ss1 = sql_call("SELECT * FROM edomiProject.editSceneList WHERE (targetid=" . $dataArr[2] . ") ORDER BY id ASC");
        while ($item = sql_result($ss1)) { ?>
            var item=createNewDiv("<? echo $dataArr[0]; ?>","<? echo $dataArr[0]; ?>-<? echo $item['id']; ?>");
            item.className="controlListItem";
            item.setAttribute('onMouseDown','document.getElementById("<? echo $dataArr[0]; ?>").dataset.itemid=<? echo $item['id']; ?>; controlClick("<? echo $dataArr[0]; ?>");');

            var tmp="&bull; <span class='varItem'><? echo getGaInfo(1, $item['gaid']); ?></span> &gt; <span
                class='varItem'><? ajaxEcho($item['gavalue']); ?></span>";
            <? if ($item['learngaid'] > 0) { ?>
                tmp+=" &middot; Lern-KO <span class='varItem'><? echo getGaInfo(1, $item['learngaid']); ?></span>";
            <? }
            if ($item['valuegaid'] > 0) { ?>
                tmp+=" &gt; <span class='varItem'><? echo getGaInfo(1, $item['valuegaid']); ?></span>";
            <? } ?>
            item.innerHTML=tmp;
            item.dataset.cm0='ajax("deleteItem",1001,"","<? echo $data; ?>","<? echo $item['id']; ?>");';
        <? } ?>
        var item=createNewDiv("<? echo $dataArr[0]; ?>","<? echo $dataArr[0]; ?>-newitem");
        item.className="controlListItem";
        item.setAttribute('onMouseDown','document.getElementById("<? echo $dataArr[0]; ?>").dataset.itemid=-1; controlClick("<? echo $dataArr[0]; ?>");');
        item.innerHTML="
        <div style='padding:2px;'><b>+ Eintrag hinzufügen...</b></div>";
    <? } else if ($cmd == 'initControl-1003') {
        echo 'clearObject("' . $dataArr[0] . '",0);';
        if (strpos($dataArr[3], '-dynDesigns') === false) {
            $dynDesigns = false;
        } else {
            $dynDesigns = true;
        }
        if (strpos($dataArr[3], '-editSheet') === false) {
            $editSheet = false;
        } else {
            $editSheet = true;
        }
        $empty = true;
        if ($editSheet) {
            $ss1 = sql_call("SELECT * FROM edomiProject.editVisuElementDesignDef WHERE (id=" . $dataArr[2] . ")");
            while ($item = sql_result($ss1)) {
                $empty = false; ?>
                var item=createNewDiv("<? echo $dataArr[0]; ?>","<? echo $dataArr[0]; ?>-<? echo $item['id']; ?>");
                item.setAttribute('onMouseDown','document.getElementById("<? echo $dataArr[0]; ?>").dataset.itemid=<? echo $item['id']; ?>; controlClick("<? echo $dataArr[0]; ?>");');
                <? if ($item['styletyp'] == 0) { ?>
                    item.innerHTML="&bull; individuell";
                <? } else { ?>
                    item.innerHTML="&bull; <span class='varItem'><? ajaxEcho($item['s1']); ?> &gt; <? ajaxEcho($item['s2']); ?></span> individuell";
                <? }
            }
        } else {
            if ($dynDesigns) {
                $ss1 = sql_call("SELECT * FROM edomiProject.editVisuElementDesign WHERE (targetid=" . $dataArr[2] . " AND styletyp=1) ORDER BY id ASC");
                while ($item = sql_result($ss1)) {
                    $empty = false; ?>
                    var item=createNewDiv("<? echo $dataArr[0]; ?>","<? echo $dataArr[0]; ?>-<? echo $item['id']; ?>");
                    item.className="controlListItem";
                    item.setAttribute('onMouseDown','document.getElementById("<? echo $dataArr[0]; ?>").dataset.itemid=<? echo $item['id']; ?>; controlClick("<? echo $dataArr[0]; ?>");');
                    <? if ($item['defid'] > 0) {
                        $ss2 = sql_call("SELECT * FROM edomiProject.editVisuElementDesignDef WHERE (id=" . $item['defid'] . ")");
                        if ($itemDef = sql_result($ss2)) {
                            if (!isEmpty($item['s1'])) {
                                $itemDef['s1'] = $item['s1'];
                            }
                            if (!isEmpty($item['s2'])) {
                                $itemDef['s2'] = $item['s2'];
                            } ?>
                            item.innerHTML="&bull; <span class='varItem'><? ajaxEcho($itemDef['s1']); ?> &gt; <? ajaxEcho($itemDef['s2']); ?></span> <span
                                class='varItem'><? ajaxEcho($itemDef['name']); ?> <span class='id'><? echo $itemDef['id']; ?></span></span>";
                        <? } else { ?>
                            item.innerHTML="&bull; <span style='color:#ffffff; background:#ff0000; border:1px solid #ff0000;'>[Designvorlage ungültig!]</span>";
                        <? }
                    } else { ?>
                        item.innerHTML="&bull; <span class='varItem'><? ajaxEcho($item['s1']); ?> &gt; <? ajaxEcho($item['s2']); ?></span> individuell";
                    <? }
                    if ($dynDesigns) { ?>
                        item.dataset.cm3='ajax("duplicateItem",1003,"","<? echo $data; ?>","<? echo $item['id']; ?>");';
                        item.dataset.cm0='ajax("deleteItem",1003,"","<? echo $data; ?>","<? echo $item['id']; ?>");';
                    <? }
                }
            } else {
                $ss1 = sql_call("SELECT * FROM edomiProject.editVisuElementDesign WHERE (targetid=" . $dataArr[2] . " AND styletyp=0)");
                if ($item = sql_result($ss1)) {
                    $empty = false; ?>
                    var item=createNewDiv("<? echo $dataArr[0]; ?>","<? echo $dataArr[0]; ?>-<? echo $item['id']; ?>");
                    item.setAttribute('onMouseDown','document.getElementById("<? echo $dataArr[0]; ?>").dataset.itemid=<? echo $item['id']; ?>; controlClick("<? echo $dataArr[0]; ?>");');
                    <? if ($item['defid'] > 0) {
                        $ss2 = sql_call("SELECT * FROM edomiProject.editVisuElementDesignDef WHERE (id=" . $item['defid'] . ")");
                        if ($itemDef = sql_result($ss2)) { ?>
                            item.innerHTML="&bull; <span class='varItem'><? ajaxEcho($itemDef['name']); ?> <span
                                    class='id'><? echo $itemDef['id']; ?></span></span>";
                        <? } else { ?>
                            item.innerHTML="&bull; <span style='color:#ffffff; background:#ff0000; border:1px solid #ff0000;'>[Designvorlage ungültig!]</span>";
                        <? }
                    } else { ?>
                        item.innerHTML="&bull; individuell";
                    <? }
                }
            }
        }
        if ($dynDesigns && (!$editSheet)) { ?>
            var item=createNewDiv("<? echo $dataArr[0]; ?>","<? echo $dataArr[0]; ?>-newitem");
            item.className="controlListItem";
            item.setAttribute('onMouseDown','document.getElementById("<? echo $dataArr[0]; ?>").dataset.itemid=-1; controlClick("<? echo $dataArr[0]; ?>");');
            item.innerHTML="
            <div style='padding:2px;'><b>+ Eintrag hinzufügen...</b></div>";
        <? } else if ($empty) { ?>
            var item=createNewDiv("<? echo $dataArr[0]; ?>","<? echo $dataArr[0]; ?>-newitem");
            item.setAttribute('onMouseDown','document.getElementById("<? echo $dataArr[0]; ?>").dataset.itemid=-1; controlClick("<? echo $dataArr[0]; ?>");');
            item.innerHTML="<span style='color:#a0a0a0;'>&lt;leer&gt;</span>";
        <? }
    } else if ($cmd == 'initControl-1007') {
        echo 'clearObject("' . $dataArr[0] . '",0);';
        if ($dataArr[5] == 'editLogicCmdList') {
            $ss1 = sql_call("SELECT * FROM edomiProject." . $dataArr[5] . " WHERE (targetid=" . $dataArr[2] . ") ORDER BY id ASC");
        }
        if ($dataArr[5] == 'editVisuCmdList') {
            $ss1 = sql_call("SELECT * FROM edomiProject." . $dataArr[5] . " WHERE (targetid=" . $dataArr[2] . ") ORDER BY id ASC");
        }
        if ($dataArr[5] == 'editSequenceCmdList') {
            $ss1 = sql_call("SELECT * FROM edomiProject." . $dataArr[5] . " WHERE (targetid=" . $dataArr[2] . ") ORDER BY sort ASC, id ASC");
        }
        if ($dataArr[5] == 'editMacroCmdList') {
            $ss1 = sql_call("SELECT * FROM edomiProject." . $dataArr[5] . " WHERE (targetid=" . $dataArr[2] . ") ORDER BY id ASC");
        }
        while ($item = sql_result($ss1)) { ?>
            var item=createNewDiv("<? echo $dataArr[0]; ?>","<? echo $dataArr[0]; ?>-<? echo $item['id']; ?>");
            item.className="controlListItem";
            item.setAttribute('onMouseDown','document.getElementById("<? echo $dataArr[0]; ?>").dataset.itemid=<? echo $item['id']; ?>; controlClick("<? echo $dataArr[0]; ?>");');
            <? if ($dataArr[5] == 'editSequenceCmdList') { ?>
                item.innerHTML="&bull; <? echo getCommandData($dataArr[5], $item['id']); ?> + <span class='varItem'><? echo $item['delay']; ?>s</span> warten";
            <? } else { ?>
                item.innerHTML="&bull; <? echo getCommandData($dataArr[5], $item['id']); ?>";
            <? } ?>
            item.dataset.cm0='ajax("deleteItem",1007,"","<? echo $data; ?>","<? echo $item['id']; ?>");';
            <? if ($dataArr[5] == 'editSequenceCmdList') { ?>
                item.dataset.cm1='ajax("sortDecItem",1007,"","<? echo $data; ?>","<? echo $item['id']; ?>");';
                item.dataset.cm2='ajax("sortIncItem",1007,"","<? echo $data; ?>","<? echo $item['id']; ?>");';
            <? } ?>
        <? } ?>
        var item=createNewDiv("<? echo $dataArr[0]; ?>","<? echo $dataArr[0]; ?>-newitem");
        item.className="controlListItem";
        item.setAttribute('onMouseDown','document.getElementById("<? echo $dataArr[0]; ?>").dataset.itemid=-1; controlClick("<? echo $dataArr[0]; ?>");');
        item.innerHTML="
        <div style='padding:2px;'><b>+ Eintrag hinzufügen...</b></div>";
    <? } else if ($cmd == 'initControl-1008') {
        echo 'clearObject("' . $dataArr[0] . '",0);';
        $ss1 = sql_call("SELECT * FROM edomiProject.editChartList WHERE (targetid=" . $dataArr[2] . ") ORDER BY sort ASC, id ASC");
        while ($item = sql_result($ss1)) {
            $col1 = '';
            $tmp = sql_getValue('edomiProject.editVisuFGcol', 'color', 'id=' . $item['s1']);
            if (strpos($tmp, '{') === false) {
                $col1 = "<span class='colorPreview1' style='background:" . ajaxEncode($tmp) . ";'></span>";
            } else {
                $col1 = "<span class='colorPreview2'></span>";
            } ?>
            var item=createNewDiv("<? echo $dataArr[0]; ?>","<? echo $dataArr[0]; ?>-<? echo $item['id']; ?>");
            item.className="controlListItem";
            item.setAttribute('onMouseDown','document.getElementById("<? echo $dataArr[0]; ?>").dataset.itemid=<? echo $item['id']; ?>; controlClick("<? echo $dataArr[0]; ?>");');
            item.innerHTML="&bull; <? echo $col1; ?> Datenarchiv <span class='varItem'><? echo getArchivInfo('editArchivKo', $item['archivkoid']); ?></span> /
            <span class='varItem'><? if ($item['ystyle'] == 0) {
                    echo "<span style='color:#0000ff;'>";
                    ajaxEcho($item['titel']);
                    echo "</span>, ";
                } ?> <? echo $global_charttyp[$item['charttyp']]; ?>, <? ajaxEcho($item['s3']); ?> px</span>";
            item.dataset.cm0='ajax("deleteItem",1008,"","<? echo $data; ?>","<? echo $item['id']; ?>");';
            item.dataset.cm1='ajax("sortDecItem",1008,"","<? echo $data; ?>","<? echo $item['id']; ?>");';
            item.dataset.cm2='ajax("sortIncItem",1008,"","<? echo $data; ?>","<? echo $item['id']; ?>");';
        <? } ?>
        var item=createNewDiv("<? echo $dataArr[0]; ?>","<? echo $dataArr[0]; ?>-newitem");
        item.className="controlListItem";
        item.setAttribute('onMouseDown','document.getElementById("<? echo $dataArr[0]; ?>").dataset.itemid=-1; controlClick("<? echo $dataArr[0]; ?>");');
        item.innerHTML="
        <div style='padding:2px;'><b>+ Eintrag hinzufügen...</b></div>";
    <? } else if ($cmd == 'initControl-1009') {
        echo 'clearObject("' . $dataArr[0] . '",0);';
        $ss1 = sql_call("SELECT * FROM edomiProject.editAwsList WHERE (targetid=" . $dataArr[2] . ") ORDER BY id ASC");
        while ($item = sql_result($ss1)) { ?>
            var item=createNewDiv("<? echo $dataArr[0]; ?>","<? echo $dataArr[0]; ?>-<? echo $item['id']; ?>");
            item.className="controlListItem";
            item.setAttribute('onMouseDown','document.getElementById("<? echo $dataArr[0]; ?>").dataset.itemid=<? echo $item['id']; ?>; controlClick("<? echo $dataArr[0]; ?>");');
            var tmp="&bull; <span class='varItem'><? echo getGaInfo(1, $item['gaid']); ?></span>";
            <? if (!isEmpty($item['gavalue1'])) { ?>
                tmp+=" &gt; Start <span class='varItem'><? ajaxEcho($item['gavalue1']); ?></span>";
            <? }
            if (!isEmpty($item['gavalue2'])) { ?>
                tmp+=" &gt; Stopp <span class='varItem'><? ajaxEcho($item['gavalue2']); ?></span>";
            <? }
            if ($item['gaid2'] > 0) { ?>
                tmp+=" &middot; Status-KO <span class='varItem'><? echo getGaInfo(1, $item['gaid2']); ?></span>";
            <? } ?>
            item.innerHTML=tmp;
            item.dataset.cm0='ajax("deleteItem",1009,"","<? echo $data; ?>","<? echo $item['id']; ?>");';
        <? } ?>
        var item=createNewDiv("<? echo $dataArr[0]; ?>","<? echo $dataArr[0]; ?>-newitem");
        item.className="controlListItem";
        item.setAttribute('onMouseDown','document.getElementById("<? echo $dataArr[0]; ?>").dataset.itemid=-1; controlClick("<? echo $dataArr[0]; ?>");');
        item.innerHTML="
        <div style='padding:2px;'><b>+ Eintrag hinzufügen...</b></div>";
    <? } else if ($cmd == 'initControl-1017') {
        echo 'clearObject("' . $dataArr[0] . '",0);';
        $ss1 = sql_call("SELECT * FROM edomiProject.editVisuUserList WHERE (visuid=" . $dataArr[2] . ") ORDER BY id ASC");
        while ($item = sql_result($ss1)) { ?>
            var item=createNewDiv("<? echo $dataArr[0]; ?>","<? echo $dataArr[0]; ?>-<? echo $item['id']; ?>");
            item.className="controlListItem";
            item.setAttribute('onMouseDown','document.getElementById("<? echo $dataArr[0]; ?>").dataset.itemid=<? echo $item['id']; ?>; controlClick("<? echo $dataArr[0]; ?>");');
            var tmp="&bull; <span class='varItem'><? echo getVisuUserInfo($item['targetid']); ?></span>";
            <? if ($item['defaultpageid'] >= 1) { ?>
                tmp+=" &middot; Startseite: <span class='varItem'><? echo getVisuPageInfo($item['defaultpageid']); ?></span>";
            <? }
            if ($item['sspageid'] >= 1) { ?>
                tmp+=" &middot; Bildschirmschoner: <span class='varItem'><? echo getVisuPageInfo($item['sspageid']); ?></span>";
            <? } ?>
            item.innerHTML=tmp;
            item.dataset.cm0='ajax("deleteItem",1017,"","<? echo $data; ?>","<? echo $item['id']; ?>");';
        <? } ?>
        var item=createNewDiv("<? echo $dataArr[0]; ?>","<? echo $dataArr[0]; ?>-newitem");
        item.className="controlListItem";
        item.setAttribute('onMouseDown','document.getElementById("<? echo $dataArr[0]; ?>").dataset.itemid=-1; controlClick("<? echo $dataArr[0]; ?>");');
        item.innerHTML="Visuaccount hinzufügen...";
    <? } else if ($cmd == 'initControl-1019') {
        echo 'clearObject("' . $dataArr[0] . '",0);';
        $ss1 = sql_call("SELECT * FROM edomiProject.editTimerData WHERE (targetid=" . $dataArr[2] . ") ORDER BY id ASC");
        while ($item = sql_result($ss1)) {
            $days = '';
            for ($t = 0; $t < 7; $t++) {
                if ($item['d' . $t] == 1) {
                    $days .= "<span style='color:#40a000;'>" . substr($global_weekdays[$t], 0, 2) . "</span> ";
                } else {
                    $days .= "<span style='color:#c0c0c0;'>" . substr($global_weekdays[$t], 0, 2) . "</span> ";
                }
            }
            if ($item['d7'] == 1) {
                $days .= " &nbsp;&middot;&nbsp; <span style='color:#40a000;'>KO</span> ";
            } else if ($item['d7'] == 2) {
                $days .= " &nbsp;&middot;&nbsp; <span style='color:#40a000;'><s>&nbsp;KO&nbsp;</s></span> ";
            }
            $noDate = true;
            if (isEmpty($item['day1'])) {
                $item['day1'] = "<span style='color:#c0c0c0;'>&middot;&middot;</span>";
            } else {
                $item['day1'] = sprintf("%02d", $item['day1']);
                $noDate = false;
            }
            if (isEmpty($item['month1'])) {
                $item['month1'] = "<span style='color:#c0c0c0;'>&middot;&middot;</span>";
            } else {
                $item['month1'] = sprintf("%02d", $item['month1']);
                $noDate = false;
            }
            if (isEmpty($item['year1'])) {
                $item['year1'] = "<span style='color:#c0c0c0;'>&middot;&middot;&middot;&middot;</span>";
            } else {
                $item['year1'] = sprintf("%04d", $item['year1']);
                $noDate = false;
            }
            if (isEmpty($item['day2'])) {
                $item['day2'] = "<span style='color:#c0c0c0;'>&middot;&middot;</span>";
            } else {
                $item['day2'] = sprintf("%02d", $item['day2']);
                $noDate = false;
            }
            if (isEmpty($item['month2'])) {
                $item['month2'] = "<span style='color:#c0c0c0;'>&middot;&middot;</span>";
            } else {
                $item['month2'] = sprintf("%02d", $item['month2']);
                $noDate = false;
            }
            if (isEmpty($item['year2'])) {
                $item['year2'] = "<span style='color:#c0c0c0;'>&middot;&middot;&middot;&middot;</span>";
            } else {
                $item['year2'] = sprintf("%04d", $item['year2']);
                $noDate = false;
            }
            if ($noDate) {
                $tmp = '';
            } else {
                $tmp = ' &nbsp;&middot;&nbsp; ' . $item['day1'] . '.' . $item['month1'] . '.' . $item['year1'] . ' ' . (($item['mode'] == 0) ? '&middot;' : '&gt;') . ' ' . $item['day2'] . '.' . $item['month2'] . '.' . $item['year2'];
            } ?>
            var item=createNewDiv("<? echo $dataArr[0]; ?>","<? echo $dataArr[0]; ?>-<? echo $item['id']; ?>");
            item.className="controlListItem";
            item.setAttribute('onMouseDown','document.getElementById("<? echo $dataArr[0]; ?>").dataset.itemid=<? echo $item['id']; ?>; controlClick("<? echo $dataArr[0]; ?>");');
            item.innerHTML="&bull; <span
                class='varItem'><? echo sprintf("%02d", $item['hour']) . ':' . sprintf("%02d", $item['minute']) . ' Uhr &nbsp;&middot;&nbsp; ' . trim($days) . $tmp; ?></span> &gt;
            <span class='varItem'><? echo getMacroInfo($item['cmdid']); ?></span>";
            item.dataset.cm0='ajax("deleteItem",1019,"","<? echo $data; ?>","<? echo $item['id']; ?>");';
            item.dataset.cm3='ajax("duplicateItem",1019,"","<? echo $data; ?>","<? echo $item['id']; ?>");';
        <? } ?>
        var item=createNewDiv("<? echo $dataArr[0]; ?>","<? echo $dataArr[0]; ?>-newitem");
        item.className="controlListItem";
        item.setAttribute('onMouseDown','document.getElementById("<? echo $dataArr[0]; ?>").dataset.itemid=-1; controlClick("<? echo $dataArr[0]; ?>");');
        item.innerHTML="
        <div style='padding:2px;'><b>+ Eintrag hinzufügen...</b></div>";
    <? } else if ($cmd == 'initControl-1022') {
        echo 'clearObject("' . $dataArr[0] . '",0);';
        $ss1 = sql_call("SELECT * FROM edomiProject.editAgendaData WHERE (targetid=" . $dataArr[2] . ") ORDER BY id ASC");
        while ($item = sql_result($ss1)) {
            $info1 = '';
            if ($item['d7'] == 1) {
                $info1 .= "&middot;&nbsp;<span style='color:#40a000;'>KO</span>";
            } else if ($item['d7'] == 2) {
                $info1 .= "&middot;&nbsp;<span style='color:#40a000;'><s>&nbsp;KO&nbsp;</s></span>";
            }
            if ($item['step'] > 0) {
                $info1 .= ' &middot; alle ' . $item['step'];
                if ($item['unit'] == 0) {
                    $info1 .= ' Tage';
                }
                if ($item['unit'] == 1) {
                    $info1 .= ' Wochen';
                }
                if ($item['unit'] == 2) {
                    $info1 .= ' Monate';
                }
                if ($item['unit'] == 3) {
                    $info1 .= ' Jahre';
                }
                if (!isEmpty($item['date2'])) {
                    $info1 .= ' bis ' . sql_getDate($item['date2']);
                }
            } ?>
            var item=createNewDiv("<? echo $dataArr[0]; ?>","<? echo $dataArr[0]; ?>-<? echo $item['id']; ?>");
            item.className="controlListItem";
            item.setAttribute('onMouseDown','document.getElementById("<? echo $dataArr[0]; ?>").dataset.itemid=<? echo $item['id']; ?>; controlClick("<? echo $dataArr[0]; ?>");');
            item.innerHTML="&bull; <span
                class='varItem'><? echo sql_getDate($item['date1']); ?> &middot; <? echo sprintf("%02d", $item['hour']) . ':' . sprintf("%02d", $item['minute']); ?> Uhr <span
                    style='color:#40a000;'><? echo $info1; ?></span></span>&nbsp;&gt; <span
                class='varItem'><? echo getMacroInfo($item['cmdid']); ?></span> &middot;&nbsp;<span class='varItem'
                                                                                                    style='color:#0000ff;'><? ajaxEcho($item['name']); ?></span>";
            item.dataset.cm0='ajax("deleteItem",1022,"","<? echo $data; ?>","<? echo $item['id']; ?>");';
            item.dataset.cm3='ajax("duplicateItem",1022,"","<? echo $data; ?>","<? echo $item['id']; ?>");';
        <? } ?>
        var item=createNewDiv("<? echo $dataArr[0]; ?>","<? echo $dataArr[0]; ?>-newitem");
        item.className="controlListItem";
        item.setAttribute('onMouseDown','document.getElementById("<? echo $dataArr[0]; ?>").dataset.itemid=-1; controlClick("<? echo $dataArr[0]; ?>");');
        item.innerHTML="
        <div style='padding:2px;'><b>+ Eintrag hinzufügen...</b></div>";
    <? } else if ($cmd == 'initControl-1023') {
        echo 'clearObject("' . $dataArr[0] . '",0);';
        $ss1 = sql_call("SELECT * FROM edomiProject.editTimerMacroList WHERE (timerid=" . $dataArr[2] . ") ORDER BY sort ASC, id ASC");
        while ($item = sql_result($ss1)) { ?>
            var item=createNewDiv("<? echo $dataArr[0]; ?>","<? echo $dataArr[0]; ?>-<? echo $item['id']; ?>");
            item.className="controlListItem";
            item.setAttribute('onMouseDown','document.getElementById("<? echo $dataArr[0]; ?>").dataset.itemid=<? echo $item['id']; ?>; controlClick("<? echo $dataArr[0]; ?>");');
            item.innerHTML="&bull; <span class='varItem'><? echo getMacroInfo($item['targetid']); ?></span>";
            item.dataset.cm0='ajax("deleteItem",1023,"","<? echo $data; ?>","<? echo $item['id']; ?>");';
            item.dataset.cm1='ajax("sortDecItem",1023,"","<? echo $data; ?>","<? echo $item['id']; ?>");';
            item.dataset.cm2='ajax("sortIncItem",1023,"","<? echo $data; ?>","<? echo $item['id']; ?>");';
        <? } ?>
        var item=createNewDiv("<? echo $dataArr[0]; ?>","<? echo $dataArr[0]; ?>-newitem");
        item.className="controlListItem";
        item.setAttribute('onMouseDown','document.getElementById("<? echo $dataArr[0]; ?>").dataset.itemid=-1; controlClick("<? echo $dataArr[0]; ?>");');
        item.innerHTML="
        <div style='padding:2px;'><b>+ Eintrag hinzufügen...</b></div>";
    <? } else if ($cmd == 'initControl-1024') {
        echo 'clearObject("' . $dataArr[0] . '",0);';
        $ss1 = sql_call("SELECT * FROM edomiProject.editAgendaMacroList WHERE (agendaid=" . $dataArr[2] . ") ORDER BY sort ASC, id ASC");
        while ($item = sql_result($ss1)) { ?>
            var item=createNewDiv("<? echo $dataArr[0]; ?>","<? echo $dataArr[0]; ?>-<? echo $item['id']; ?>");
            item.className="controlListItem";
            item.setAttribute('onMouseDown','document.getElementById("<? echo $dataArr[0]; ?>").dataset.itemid=<? echo $item['id']; ?>; controlClick("<? echo $dataArr[0]; ?>");');
            item.innerHTML="&bull; <span class='varItem'><? echo getMacroInfo($item['targetid']); ?></span>";
            item.dataset.cm0='ajax("deleteItem",1024,"","<? echo $data; ?>","<? echo $item['id']; ?>");';
            item.dataset.cm1='ajax("sortDecItem",1024,"","<? echo $data; ?>","<? echo $item['id']; ?>");';
            item.dataset.cm2='ajax("sortIncItem",1024,"","<? echo $data; ?>","<? echo $item['id']; ?>");';
        <? } ?>
        var item=createNewDiv("<? echo $dataArr[0]; ?>","<? echo $dataArr[0]; ?>-newitem");
        item.className="controlListItem";
        item.setAttribute('onMouseDown','document.getElementById("<? echo $dataArr[0]; ?>").dataset.itemid=-1; controlClick("<? echo $dataArr[0]; ?>");');
        item.innerHTML="
        <div style='padding:2px;'><b>+ Eintrag hinzufügen...</b></div>";
    <? }
}

function getGaInfo($mode, $id)
{
    $ss1 = sql_call("SELECT id,name,ga,gatyp FROM edomiProject.editKo WHERE (id=" . $id . ")");
    if ($n = sql_result($ss1)) {
        if ($mode == 0) {
            return '<span class=\"idGa' . $n['gatyp'] . '\">' . ajaxEncode($n['ga']) . '</span>' . SEPARATOR1 . ajaxEncode($n['name']);
        }
        if ($mode == 1) {
            return ajaxEncode($n['name']) . ' <span class=\"idGa' . $n['gatyp'] . '\">' . ajaxEncode($n['ga']) . '</span>';
        }
    } else {
        if ($mode == 0) {
            return '<span class=\"id\">-?-</span>' . SEPARATOR1 . '-?-';
        }
        if ($mode == 1) {
            return '<span class=\"id\">-?-</span>';
        }
    }
}

function getLbsInstanceInfo($id, $item = 0, $mode = -1)
{
    $n = sql_getValues('edomiProject.editLogicElement', 'id,functionid', 'id=' . $id);
    if ($n !== false) {
        $itemInfo = '';
        $lbs = sql_getValues('edomiProject.editLogicElementDef', 'id,name', 'id=' . $n['functionid']);
        if ($lbs !== false) {
            $itemInfo = ajaxValue($lbs['name'], false) . ' <span class=\"id\">' . $n['id'] . '</span>';
            if ($item > 0 && $mode >= 0) {
                if ($mode == 0) {
                    $tmp = sql_getValues('edomiProject.editLogicElementDefIn', 'id,name', 'targetid=' . $lbs['id'] . ' AND id=' . $item);
                    $itemInfo .= ' &gt; ' . ajaxValue($tmp['name'], false) . ' <span class=\"id\">E' . $tmp['id'] . '</span>';
                } else {
                    $tmp = sql_getValues('edomiProject.editLogicElementDefOut', 'id,name', 'targetid=' . $lbs['id'] . ' AND id=' . $item);
                    $itemInfo .= ' &gt; ' . ajaxValue($tmp['name'], false) . ' <span class=\"id\">A' . $tmp['id'] . '</span>';
                }
            }
        } else {
            $itemInfo = '-?-' . ' <span class=\"id\">' . $n['id'] . '</span>';
        }
        return $itemInfo;
    }
    return '-?-';
}

function getCamInfo($id)
{
    $ss1 = sql_call("SELECT id,name FROM edomiProject.editCam WHERE (id=" . $id . ")");
    if ($n = sql_result($ss1)) {
        return ajaxEncode($n['name']) . ' <span class=\"id\">' . $n['id'] . '</span>';
    } else {
        return '-?-';
    }
}

function getCamViewInfo($id)
{
    $ss1 = sql_call("SELECT id,name FROM edomiProject.editCamView WHERE (id=" . $id . ")");
    if ($n = sql_result($ss1)) {
        return ajaxEncode($n['name']) . ' <span class=\"id\">' . $n['id'] . '</span>';
    } else {
        return '-?-';
    }
}

function getSceneInfo($id)
{
    $ss1 = sql_call("SELECT id,name FROM edomiProject.editScene WHERE (id=" . $id . ")");
    if ($n = sql_result($ss1)) {
        return ajaxEncode($n['name']) . ' <span class=\"id\">' . $n['id'] . '</span>';
    } else {
        return '-?-';
    }
}

function getArchivInfo($archivDb, $id)
{
    $ss1 = sql_call("SELECT id,name FROM edomiProject." . $archivDb . " WHERE (id=" . $id . ")");
    if ($n = sql_result($ss1)) {
        return ajaxEncode($n['name']) . ' <span class=\"id\">' . $n['id'] . '</span>';
    } else {
        return '-?-';
    }
}

function getSequenceInfo($id)
{
    $ss1 = sql_call("SELECT id,name FROM edomiProject.editSequence WHERE (id=" . $id . ")");
    if ($n = sql_result($ss1)) {
        return ajaxEncode($n['name']) . ' <span class=\"id\">' . $n['id'] . '</span>';
    } else {
        return '-?-';
    }
}

function getMacroInfo($id)
{
    $ss1 = sql_call("SELECT id,name FROM edomiProject.editMacro WHERE (id=" . $id . ")");
    if ($n = sql_result($ss1)) {
        return ajaxEncode($n['name']) . ' <span class=\"id\">' . $n['id'] . '</span>';
    } else {
        return '-?-';
    }
}

function getTimerInfo($id)
{
    $ss1 = sql_call("SELECT id,name FROM edomiProject.editTimer WHERE (id=" . $id . ")");
    if ($n = sql_result($ss1)) {
        return ajaxEncode($n['name']) . ' <span class=\"id\">' . $n['id'] . '</span>';
    } else {
        return '-?-';
    }
}

function getIrInfo($id)
{
    $ss1 = sql_call("SELECT id,folderid,name FROM edomiProject.editIr WHERE (id=" . $id . ")");
    if ($n = sql_result($ss1)) {
        return ajaxEncode($n['name']) . ' <span class=\"id\">' . $n['id'] . '</span>';
    } else {
        return '-?-';
    }
}

function getEmailInfo($id)
{
    $ss1 = sql_call("SELECT id,name FROM edomiProject.editEmail WHERE (id=" . $id . ")");
    if ($n = sql_result($ss1)) {
        return ajaxEncode($n['name']) . ' <span class=\"id\">' . $n['id'] . '</span>';
    } else {
        return '-?-';
    }
}

function getPhoneInfo($id)
{
    $ss1 = sql_call("SELECT id,name FROM edomiProject.editPhoneBook WHERE (id=" . $id . ")");
    if ($n = sql_result($ss1)) {
        return ajaxEncode($n['name']) . ' <span class=\"id\">' . $n['id'] . '</span>';
    } else {
        return '-?-';
    }
}

function getChartInfo($id)
{
    $ss1 = sql_call("SELECT id,name FROM edomiProject.editChart WHERE (id=" . $id . ")");
    if ($n = sql_result($ss1)) {
        return ajaxEncode($n['name']) . ' <span class=\"id\">' . $n['id'] . '</span>';
    } else {
        return '-?-';
    }
}

function getVisuInfo($id)
{
    $ss1 = sql_call("SELECT id,name FROM edomiProject.editVisu WHERE (id=" . $id . ")");
    if ($n = sql_result($ss1)) {
        return ajaxEncode($n['name']) . ' <span class=\"id\">' . $n['id'] . '</span>';
    } else {
        return '-?-';
    }
}

function getVisuSoundInfo($id)
{
    $ss1 = sql_call("SELECT id,name FROM edomiProject.editVisuSnd WHERE (id=" . $id . ")");
    if ($n = sql_result($ss1)) {
        return ajaxEncode($n['name']) . ' <span class=\"id\">' . $n['id'] . '</span>';
    } else {
        return '-?-';
    }
}

function getVisuPageInfo($id)
{
    $ss1 = sql_call("SELECT id,name FROM edomiProject.editVisuPage WHERE (id=" . $id . ")");
    if ($n = sql_result($ss1)) {
        return ajaxEncode($n['name']) . ' <span class=\"id\">' . $n['id'] . '</span>';
    } else {
        return '-?-';
    }
}

function getVisuUserInfo($id)
{
    $ss1 = sql_call("SELECT id,login FROM edomiProject.editVisuUser WHERE (id=" . $id . ")");
    if ($n = sql_result($ss1)) {
        return ajaxEncode($n['login']) . ' <span class=\"id\">' . $n['id'] . '</span>';
    } else {
        return '-?-';
    }
}

function getCommandData($archivDb, $id)
{
    $ss1 = sql_call("SELECT * FROM edomiProject." . $archivDb . " WHERE (id=" . $id . ")");
    if ($n = sql_result($ss1)) {
        if ($n['cmd'] == 1) {
            return 'KO <span class=\"varItem\">' . getGaInfo(1, $n['cmdid1']) . '</span> &gt; Eingangswert (Ausgangsbox) zuweisen';
        }
        if ($n['cmd'] == 2) {
            return 'KO <span class=\"varItem\">' . getGaInfo(1, $n['cmdid1']) . '</span> &gt; Wert <span class=\"varItem\">' . ajaxEncode($n['cmdvalue1']) . '</span> zuweisen';
        }
        if ($n['cmd'] == 3) {
            return 'KO <span class=\"varItem\">' . getGaInfo(1, $n['cmdid1']) . '</span> &gt; Wert von KO <span class=\"varItem\">' . getGaInfo(1, $n['cmdid2']) . '</span> zuweisen';
        }
        if ($n['cmd'] == 4) {
            return 'KO <span class=\"varItem\">' . getGaInfo(1, $n['cmdid1']) . '</span> &gt; Wechseln zwischen 0 und <span class=\"varItem\">' . ajaxEncode($n['cmdvalue1']) . '</span>';
        }
        if ($n['cmd'] == 5) {
            $tmp = array(1 => 'addieren', -1 => 'subtrahieren');
            return 'KO <span class=\"varItem\">' . getGaInfo(1, $n['cmdid1']) . '</span> &gt; Rasterwert <span class=\"varItem\">' . $tmp[$n['cmdoption1']] . '</span>';
        }
        if ($n['cmd'] == 6) {
            return 'KO <span class=\"varItem\">' . getGaInfo(1, $n['cmdid1']) . '</span> &gt; Wechseln zwischen 0 und <span class=\"varItem\">' . ajaxEncode($n['cmdvalue1']) . '</span> mit Status-KO <span class=\"varItem\">' . getGaInfo(1, $n['cmdid2']) . '</span>';
        }
        if ($n['cmd'] == 19) {
            return 'KO <span class=\"varItem\">' . getGaInfo(1, $n['cmdid1']) . '</span> &gt; Wechseln zwischen 1 und <span class=\"varItem\">' . ajaxEncode($n['cmdvalue1']) . '</span> mit Status-KO <span class=\"varItem\">' . getGaInfo(1, $n['cmdid2']) . '</span>';
        }
        if ($n['cmd'] == 7) {
            return 'KO <span class=\"varItem\">' . getGaInfo(1, $n['cmdid1']) . '</span> &gt; Wert <span class=\"varItem\">' . ajaxEncode($n['cmdvalue1']) . '</span> addieren';
        }
        if ($n['cmd'] == 8) {
            return 'KO <span class=\"varItem\">' . getGaInfo(1, $n['cmdid1']) . '</span> &gt; Abfragen (Read-Request)';
        }
        if ($n['cmd'] == 9) {
            return 'KO <span class=\"varItem\">' . getGaInfo(1, $n['cmdid1']) . '</span> &gt; Wertliste vor/zurück: <span class=\"varItem\">' . ajaxEncode($n['cmdvalue1']) . '</span>';
        }
        if ($n['cmd'] == 10) {
            $tmp = array(0 => 'Abrufen', 1 => 'Lernen');
            return 'Szene <span class=\"varItem\">' . getSceneInfo($n['cmdid1']) . '</span> &gt; <span class=\"varItem\">' . $tmp[$n['cmdoption1']] . '</span>';
        }
        if ($n['cmd'] == 11) {
            $tmp = array(0 => 'Abrufen', 1 => 'Stoppen');
            return 'Sequenz <span class=\"varItem\">' . getSequenceInfo($n['cmdid1']) . '</span> &gt; <span class=\"varItem\">' . $tmp[$n['cmdoption1']] . '</span>';
        }
        if ($n['cmd'] == 17) {
            return 'Makro <span class=\"varItem\">' . getMacroInfo($n['cmdid1']) . '</span> &gt; Ausführen';
        }
        if ($n['cmd'] == 12) {
            return 'Kameraarchiv <span class=\"varItem\">' . getArchivInfo('editArchivCam', $n['cmdid1']) . '</span> &gt; Kamerabild hinzufügen';
        }
        if ($n['cmd'] == 13) {
            return 'Datenarchiv <span class=\"varItem\">' . getArchivInfo('editArchivKo', $n['cmdid1']) . '</span> &gt; Eingangswert (Ausgangsbox) hinzufügen';
        }
        if ($n['cmd'] == 14) {
            return 'Meldungsarchiv <span class=\"varItem\">' . getArchivInfo('editArchivMsg', $n['cmdid1']) . '</span> &gt; Eingangswert (Ausgangsbox) hinzufügen';
        }
        if ($n['cmd'] == 40) {
            return 'Datenarchiv <span class=\"varItem\">' . getArchivInfo('editArchivKo', $n['cmdid1']) . '</span> &gt; Wert <span class=\"varItem\">' . ajaxEncode($n['cmdvalue1']) . '</span> hinzufügen';
        }
        if ($n['cmd'] == 41) {
            return 'Meldungsarchiv <span class=\"varItem\">' . getArchivInfo('editArchivMsg', $n['cmdid1']) . '</span> &gt; Meldung <span class=\"varItem\">' . ajaxEncode($n['cmdvalue1']) . '</span> hinzufügen';
        }
        if ($n['cmd'] == 42) {
            return 'Datenarchiv <span class=\"varItem\">' . getArchivInfo('editArchivKo', $n['cmdid1']) . '</span> &gt; KO-Wert <span class=\"varItem\">' . getGaInfo(1, $n['cmdid2']) . '</span> hinzufügen';
        }
        if ($n['cmd'] == 50) {
            if ($n['cmdoption1'] == 0) {
                return 'Datenarchiv <span class=\"varItem\">' . getArchivInfo('editArchivKo', $n['cmdid1']) . '</span> &gt; Neusten Eintrag entfernen';
            }
            if ($n['cmdoption1'] == 1) {
                return 'Datenarchiv <span class=\"varItem\">' . getArchivInfo('editArchivKo', $n['cmdid1']) . '</span> &gt; Ältesten Eintrag entfernen';
            }
            if ($n['cmdoption1'] == 2) {
                return 'Datenarchiv <span class=\"varItem\">' . getArchivInfo('editArchivKo', $n['cmdid1']) . '</span> &gt; Alle Einträge entfernen(!)';
            }
        }
        if ($n['cmd'] == 51) {
            if ($n['cmdoption1'] == 0) {
                return 'Meldungsarchiv <span class=\"varItem\">' . getArchivInfo('editArchivMsg', $n['cmdid1']) . '</span> &gt; Neuste Meldung entfernen';
            }
            if ($n['cmdoption1'] == 1) {
                return 'Meldungsarchiv <span class=\"varItem\">' . getArchivInfo('editArchivMsg', $n['cmdid1']) . '</span> &gt; Älteste Meldung entfernen';
            }
            if ($n['cmdoption1'] == 2) {
                return 'Meldungsarchiv <span class=\"varItem\">' . getArchivInfo('editArchivMsg', $n['cmdid1']) . '</span> &gt; Alle Meldungen entfernen(!)';
            }
        }
        if ($n['cmd'] == 52) {
            if ($n['cmdoption1'] == 0) {
                return 'Kameraarchiv <span class=\"varItem\">' . getArchivInfo('editArchivCam', $n['cmdid1']) . '</span> &gt; Neustes Kamerabild entfernen';
            }
            if ($n['cmdoption1'] == 1) {
                return 'Kameraarchiv <span class=\"varItem\">' . getArchivInfo('editArchivCam', $n['cmdid1']) . '</span> &gt; Ältestes Kamerabild entfernen';
            }
            if ($n['cmdoption1'] == 2) {
                return 'Kameraarchiv <span class=\"varItem\">' . getArchivInfo('editArchivCam', $n['cmdid1']) . '</span> &gt; Alle Kamerabilder entfernen(!)';
            }
        }
        if ($n['cmd'] == 53) {
            if ($n['cmdoption1'] == 0) {
                return 'Anrufarchiv <span class=\"varItem\">' . getArchivInfo('editArchivPhone', $n['cmdid1']) . '</span> &gt; Neusten Eintrag entfernen';
            }
            if ($n['cmdoption1'] == 1) {
                return 'Anrufarchiv <span class=\"varItem\">' . getArchivInfo('editArchivPhone', $n['cmdid1']) . '</span> &gt; Ältesten Eintrag entfernen';
            }
            if ($n['cmdoption1'] == 2) {
                return 'Anrufarchiv <span class=\"varItem\">' . getArchivInfo('editArchivPhone', $n['cmdid1']) . '</span> &gt; Alle Einträge entfernen(!)';
            }
        }
        if ($n['cmd'] == 15) {
            return 'HTTP/UDP/SHELL <span class=\"varItem\">' . getArchivInfo('editIp', $n['cmdid1']) . '</span> &gt; Ausführen';
        }
        if ($n['cmd'] == 16) {
            $tmp = array(1 => 'Kanal 1', 2 => 'Kanal 2', 3 => 'Kanal 1 und 2');
            return 'IR-Befehl <span class=\"varItem\">' . getIrInfo($n['cmdid1']) . '</span> &gt; Über <span class=\"varItem\">' . $tmp[$n['cmdoption1']] . '</span> senden';
        }
        if ($n['cmd'] == 20) {
            return 'Email <span class=\"varItem\">' . getEmailInfo($n['cmdid1']) . '</span> &gt; Senden';
        }
        if ($n['cmd'] == 22) {
            if ($n['cmdid1'] > 0) {
                if ($n['cmdoption1'] > 0) {
                    return 'Telefonbucheintrag <span class=\"varItem\">' . getPhoneInfo($n['cmdid1']) . '</span> &gt; Anrufen und nach <span class=\"varItem\">' . ajaxEncode($n['cmdoption1']) . ' Sekunden</span> auflegen';
                } else {
                    return 'Telefonbucheintrag <span class=\"varItem\">' . getPhoneInfo($n['cmdid1']) . '</span> &gt; Anrufen und nicht(!) auflegen';
                }
            }
            if ($n['cmdid1'] == 0) {
                return 'Telefonanruf beenden (auflegen)';
            }
        }
        if ($n['cmd'] == 18) {
            if ($n['cmdid2'] == 0) {
                return 'Visu <span class=\"varItem\">' . getVisuInfo($n['cmdid1']) . '</span> &gt; Eingangswert (Ausgangsbox) als Visuseite aufrufen (alle Accounts)';
            }
            if ($n['cmdid2'] > 0) {
                return 'Visu <span class=\"varItem\">' . getVisuInfo($n['cmdid1']) . '</span> &gt; Eingangswert (Ausgangsbox) als Visuseite aufrufen (Visuaccount <span class=\"varItem\">' . getVisuUserInfo($n['cmdid2']) . '</span>)';
            }
        }
        if ($n['cmd'] == 21) {
            if ($n['cmdid2'] == 0) {
                return 'Visuseite/Popup <span class=\"varItem\">' . getVisuPageInfo($n['cmdid1']) . '</span> &gt; Aufrufen (alle Accounts)';
            }
            if ($n['cmdid2'] > 0) {
                return 'Visuseite/Popup <span class=\"varItem\">' . getVisuPageInfo($n['cmdid1']) . '</span> &gt; Aufrufen (Visuaccount <span class=\"varItem\">' . getVisuUserInfo($n['cmdid2']) . '</span>)';
            }
        }
        if ($n['cmd'] == 28) {
            if ($n['cmdid2'] == 0) {
                return 'Visu <span class=\"varItem\">' . getVisuInfo($n['cmdid1']) . '</span> &gt; Alle Popups schließen (alle Accounts)';
            }
            if ($n['cmdid2'] > 0) {
                return 'Visu <span class=\"varItem\">' . getVisuInfo($n['cmdid1']) . '</span> &gt; Alle Popups schließen (Visuaccount <span class=\"varItem\">' . getVisuUserInfo($n['cmdid2']) . '</span>)';
            }
        }
        if ($n['cmd'] == 29) {
            if ($n['cmdid2'] == 0) {
                return 'Popup <span class=\"varItem\">' . getVisuPageInfo($n['cmdid1']) . '</span> &gt; Schließen (alle Accounts)';
            }
            if ($n['cmdid2'] > 0) {
                return 'Popup <span class=\"varItem\">' . getVisuPageInfo($n['cmdid1']) . '</span> &gt; Schließen (Visuaccount <span class=\"varItem\">' . getVisuUserInfo($n['cmdid2']) . '</span>)';
            }
        }
        if ($n['cmd'] == 23) {
            if ($n['cmdid1'] == 0 && $n['cmdid2'] == 0) {
                return 'Visu/Visuaccount &gt; Logout aller Accounts (für alle Visus)';
            }
            if ($n['cmdid1'] == 0 && $n['cmdid2'] > 0) {
                return 'Visu/Visuaccount &gt; Logout des Accounts <span class=\"varItem\">' . getVisuUserInfo($n['cmdid2']) . '</span> (für alle Visus)';
            }
            if ($n['cmdid1'] > 0 && $n['cmdid2'] == 0) {
                return 'Visu/Visuaccount &gt; Logout der Visu <span class=\"varItem\">' . getVisuInfo($n['cmdid1']) . '</span> (alle Accounts)';
            }
            if ($n['cmdid1'] > 0 && $n['cmdid2'] > 0) {
                return 'Visu/Visuaccount &gt; Logout der Visu <span class=\"varItem\">' . getVisuInfo($n['cmdid1']) . '</span> (Account <span class=\"varItem\">' . getVisuUserInfo($n['cmdid2']) . '</span>)';
            }
        }
        if ($n['cmd'] == 24) {
            if ($n['cmdid2'] == 0) {
                return 'Visu <span class=\"varItem\">' . getVisuInfo($n['cmdid1']) . '</span> &gt; Tonausgabe stoppen';
            }
            if ($n['cmdid2'] > 0) {
                return 'Visu <span class=\"varItem\">' . getVisuInfo($n['cmdid1']) . '</span> &gt; Ton <span class=\"varItem\">' . getVisuSoundInfo($n['cmdid2']) . '</span> abspielen';
            }
        }
        if ($n['cmd'] == 25) {
            if ($n['cmdid2'] == 0) {
                return 'Visuaccount <span class=\"varItem\">' . getVisuUserInfo($n['cmdid1']) . '</span> &gt; Tonausgabe stoppen';
            }
            if ($n['cmdid2'] > 0) {
                return 'Visuaccount <span class=\"varItem\">' . getVisuUserInfo($n['cmdid1']) . '</span> &gt; Ton <span class=\"varItem\">' . getVisuSoundInfo($n['cmdid2']) . '</span> abspielen';
            }
        }
        if ($n['cmd'] == 26) {
            return 'Visu <span class=\"varItem\">' . getVisuInfo($n['cmdid1']) . '</span> &gt; Sprachausgabe: <span class=\"varItem\">' . ajaxEncode($n['cmdvalue1']) . '</span>';
        }
        if ($n['cmd'] == 27) {
            return 'Visuaccount <span class=\"varItem\">' . getVisuUserInfo($n['cmdid1']) . '</span> &gt; Sprachausgabe: <span class=\"varItem\">' . ajaxEncode($n['cmdvalue1']) . '</span>';
        }
        if ($n['cmd'] == 30) {
            $tmp = array(1 => 'Neustarten', 2 => 'Server neustarten', 3 => 'Server herunterfahren', 4 => 'Pausieren', 9 => 'Autobackup erstellen');
            return 'EDOMI &gt; <span class=\"varItem\">' . $tmp[$n['cmdoption1']] . '</span>';
        }
    } else {
        return '-?-';
    }
}

function print_Calendar($month, $year, $checkMode, $filter, $multi = false)
{
    global $global_weekdays, $global_months;
    $now = date('d.m.Y');
    $d0 = date('d.m.Y', strtotime('01.' . $month . '.' . $year));
    $r = "<table width='100%' border='0' cellpadding='2' cellspacing='1' bgcolor='#ffffff'>";
    $r .= "<tr align='center' bgcolor='#a0a0a0'><td style='color:#ffffff;' colspan='7'>" . $month . " &middot; " . $global_months[$month - 1] . "</td></tr>";
    $r .= "<tr align='center' bgcolor='#d9d9d9'>";
    for ($tt = 0; $tt < 7; $tt++) {
        $r .= "<td " . (($tt >= 5) ? "style='color:#d90000;'" : "") . ">" . substr($global_weekdays[$tt], 0, 2) . "</td>";
    }
    $r .= "</tr>";
    $row = 0;
    $r .= "<tr align='center'>";
    $wday = date('N', strtotime($d0));
    for ($t = 1; $t < $wday; $t++) {
        $r .= "<td " . (($t > 5) ? "style='color:#d90000;'" : "") . ">&nbsp;</td>";
    }
    do {
        $day = date('d', strtotime($d0));
        $wday = date('N', strtotime($d0));
        $highlight = 0;
        if ($checkMode == 1) {
            if ($multi) {
                for ($t = 0; $t < count($filter); $t++) {
                    if (checkZSU_date($d0, $filter[$t], false)) {
                        if ($filter[$t][14] == 0) {
                            $highlight |= 1;
                        } else {
                            $highlight |= 2;
                        }
                    }
                }
            } else if (checkZSU_date($d0, $filter, false)) {
                if ($filter[14] == 0) {
                    $highlight = 1;
                } else {
                    $highlight = 2;
                }
            }
        } else if ($checkMode == 2) {
            if ($multi) {
                for ($t = 0; $t < count($filter); $t++) {
                    if (previewTSU_date($d0, $filter[$t])) {
                        if ($filter[$t][4] == 0) {
                            $highlight |= 1;
                        } else {
                            $highlight |= 2;
                        }
                    }
                }
            } else if (previewTSU_date($d0, $filter)) {
                if ($filter[4] == 0) {
                    $highlight = 1;
                } else {
                    $highlight = 2;
                }
            }
        }
        if ($highlight == 1) {
            $bgcolor = "background:#e09090;";
        } else if ($highlight == 2) {
            $bgcolor = "background:#90e000;";
        } else if ($highlight == 3) {
            $bgcolor = "background:-webkit-repeating-linear-gradient(45deg,#90e000 0%,#90e000 50%,#e09090 50%,#e09090 100%);";
        } else {
            $bgcolor = "";
        }
        $r .= "<td  style='" . $bgcolor . " " . (($wday > 5) ? "color:#d90000;" : "") . "'>" . (($d0 == $now) ? "<div style='display:block; color:#ffffff; background:#595959; border-radius:3px;'>" . intval($day) . "</div>" : intval($day)) . "</td>";
        if ($wday == 7) {
            $r .= "</tr><tr align='center'>";
            $row++;
        }
        $d0 = date('d.m.Y', strtotime($d0 . ' +1 day'));
    } while ($month == intval(date('m', strtotime($d0))));
    for ($t = ($wday + 1); $t <= 7; $t++) {
        $r .= "<td " . (($t > 5) ? "style='color:#d90000;'" : "") . ">&nbsp;</td>";
    }
    if ($wday == 7) {
        $r .= "<td colspan='7'>&nbsp;</td></tr>";
    } else {
        $r .= "</tr>";
    }
    if ($row < 5) {
        $r .= "<tr><td colspan='7'>&nbsp;</td></tr>";
    }
    $r .= "</table>";
    return $r;
}

function helpToHtml($winId, $helpFn, $newWin = false)
{
    if (file_exists(MAIN_PATH . '/www/admin/help/' . $helpFn . '.htm')) {
        $help = file_get_contents(MAIN_PATH . '/www/admin/help/' . $helpFn . '.htm');
        if (preg_match_all("'<menucat>(.*?)</menucat>'s", $help, $link) > 0) {
            for ($t = 0; $t < count($link[0]); $t++) {
                $n = explode('***', $link[1][$t]);
                $l = "<span class='menuCatHelp'><b>" . $n[0] . "</b></span>";
                $help = str_replace($link[0][$t], $l, $help);
            }
        }
        if (preg_match_all("'<menulink>(.*?)</menulink>'s", $help, $link) > 0) {
            for ($t = 0; $t < count($link[0]); $t++) {
                $n = explode('***', $link[1][$t]);
                if ($newWin) {
                    $l = "<span class='menuLinkHelp' onClick='window.location=\"help.php?file=" . $n[1] . "\"'>" . $n[0] . "</span>";
                } else {
                    $l = "<span class='menuLinkHelp' onClick='ajax(\"showHelp\",\"9999\",\"" . $winId . "\",\"" . $n[1] . "\",\"\");'>" . $n[0] . "</span>";
                }
                $help = str_replace($link[0][$t], $l, $help);
            }
        }
        if (preg_match_all("'<path>(.*?)</path>'s", $help, $link) > 0) {
            for ($t = 0; $t < count($link[0]); $t++) {
                $n = explode('***', $link[1][$t]);
                if ($newWin) {
                    $l = "<span class='linkHelp' onClick='window.location=\"help.php?file=" . $n[1] . "\"'>" . $n[0] . "</span> / ";
                } else {
                    $l = "<span class='linkHelp' onClick='ajax(\"showHelp\",\"9999\",\"" . $winId . "\",\"" . $n[1] . "\",\"\");'>" . $n[0] . "</span> / ";
                }
                $help = str_replace($link[0][$t], "<span style='font-weight:normal;'>" . $l . "</span>", $help);
            }
        }
        if (preg_match_all("'<link>(.*?)</link>'s", $help, $link) > 0) {
            for ($t = 0; $t < count($link[0]); $t++) {
                $n = explode('***', $link[1][$t]);
                if ($newWin) {
                    $l = "<span class='linkHelp' onClick='window.location=\"help.php?file=" . $n[1] . "\"'>" . $n[0] . "</span>&nbsp;<span class='linkHelp' onClick='window.open(\"help.php?file=" . $n[1] . "\",\"_blank\");'>&#x21D7;</span>";
                } else {
                    $l = "<span class='linkHelp' onClick='ajax(\"showHelp\",\"9999\",\"" . $winId . "\",\"" . $n[1] . "\",\"\");'>" . $n[0] . "</span>&nbsp;<span class='linkHelp' onClick='window.open(\"help.php?file=" . $n[1] . "\",\"_blank\");'>&#x21D7;</span>";
                }
                $help = str_replace($link[0][$t], $l, $help);
            }
        }
        if (preg_match("'<lbs-titel>(.*?)</lbs-titel>'s", $help, $tmp) > 0) {
            $l = "<h1>" . $tmp[1] . "</h1>";
            $help = str_replace($tmp[0], $l, $help);
        }
        if (preg_match("'<lbs-list>'s", $help, $tmp) > 0) {
            $n = "";
            $folderId = 0;
            $ss1 = sql_call("SELECT id,name,folderid,errcount FROM edomiProject.editLogicElementDef ORDER BY id ASC");
            while ($lbs = sql_result($ss1)) {
                $lbs['folderid'] = dbRoot_getRootId($lbs['folderid']);
                if ($folderId != $lbs['folderid']) {
                    if ($lbs['folderid'] == 12) {
                        $tmp = 'Basis-Logikbausteine (12)';
                    } else {
                        $tmp = sql_getValue('edomiProject.editRoot', 'name', "id='" . $lbs['folderid'] . "'");
                    }
                    $folderId = $lbs['folderid'];
                    if (isEmpty($n)) {
                        $n = "<ul>";
                    } else {
                        $n .= '</ul></li><br>';
                    }
                    $n .= '<li><span style="color:#00a000;"><b>' . $tmp . '</b></span><ul>';
                }
                if ($newWin) {
                    $n .= "<li><span class='linkHelp' " . (($lbs['errcount']) ? "style='color:#ff0000;'" : "") . " onClick='window.location=\"help.php?file=lbs_" . $lbs['id'] . "\"'>" . $lbs['name'] . " <span class=\"id\">" . $lbs['id'] . "</span>" . (($lbs['errcount']) ? " &gt; " . $lbs['errcount'] . " Fehler" : "") . "</span>&nbsp;<span class='linkHelp' onClick='window.open(\"help.php?file=lbs_" . $lbs['id'] . "\",\"_blank\");'>&#x21D7;</span></li>";
                } else {
                    $n .= "<li><span class='linkHelp' " . (($lbs['errcount']) ? "style='color:#ff0000;'" : "") . " onClick='ajax(\"showHelp\",\"9999\",\"" . $winId . "\",\"lbs_" . $lbs['id'] . "\",\"\");'>" . $lbs['name'] . " <span class=\"id\">" . $lbs['id'] . "</span>" . (($lbs['errcount']) ? " &gt; " . $lbs['errcount'] . " Fehler" : "") . "</span>&nbsp;<span class='linkHelp' onClick='window.open(\"help.php?file=lbs_" . $lbs['id'] . "\",\"_blank\");'>&#x21D7;</span></li>";
                }
            }
            if (!isEmpty($n)) {
                $n .= "</ul></li></ul>";
            }
            $help = str_replace('<lbs-list>', $n, $help);
        }
        if (preg_match("'<vse-list>'s", $help, $tmp) > 0) {
            $n = "";
            $folderId = 0;
            $ss1 = sql_call("SELECT id,name,folderid,errcount FROM edomiProject.editVisuElementDef ORDER BY folderid ASC,id ASC");
            while ($vse = sql_result($ss1)) {
                if ($folderId != $vse['folderid']) {
                    $tmp = sql_getValue('edomiProject.editRoot', 'name', "id='" . $vse['folderid'] . "'");
                    $folderId = $vse['folderid'];
                    if (isEmpty($n)) {
                        $n = "<ul>";
                    } else {
                        $n .= '</ul></li><br>';
                    }
                    $n .= '<li><span style="color:#00a000;"><b>' . $tmp . '</b></span><ul>';
                }
                if ($newWin) {
                    $n .= "<li><span class='linkHelp' " . (($vse['errcount']) ? "style='color:#ff0000;'" : "") . " onClick='window.location=\"help.php?file=1002-" . $vse['id'] . "\"'>" . $vse['name'] . " <span class=\"id\">" . $vse['id'] . "</span>" . (($vse['errcount']) ? " &gt; " . $vse['errcount'] . " Fehler" : "") . "</span>&nbsp;<span class='linkHelp' onClick='window.open(\"help.php?file=1002-" . $vse['id'] . "\",\"_blank\");'>&#x21D7;</span></li>";
                } else {
                    $n .= "<li><span class='linkHelp' " . (($vse['errcount']) ? "style='color:#ff0000;'" : "") . " onClick='ajax(\"showHelp\",\"9999\",\"" . $winId . "\",\"1002-" . $vse['id'] . "\",\"\");'>" . $vse['name'] . " <span class=\"id\">" . $vse['id'] . "</span>" . (($vse['errcount']) ? " &gt; " . $vse['errcount'] . " Fehler" : "") . "</span>&nbsp;<span class='linkHelp' onClick='window.open(\"help.php?file=1002-" . $vse['id'] . "\",\"_blank\");'>&#x21D7;</span></li>";
                }
            }
            if (!isEmpty($n)) {
                $n .= "</ul></li></ul>";
            }
            $help = str_replace('<vse-list>', $n, $help);
        }
        if (preg_match("'<donate-edomi>'s", $help, $tmp) > 0) {
            $n = file_get_contents('http://undefinedURL/donate.php?clientid=' . get_clientId());
            if ($n !== false) {
                $n = openssl_decrypt($n, "AES-128-ECB", 'HOvcxIAIghjUD687DwerwJHGASD56asd4DLJKSAD1sdf23JHKAD');
                $n = openssl_decrypt($n, "AES-128-ECB", get_clientId());
            }
            if ($n !== false) {
                $help = str_replace('<donate-edomi>', $n, $help);
            } else {
                $nn = '<table class="tableHelp">';
                $nn .= '<tr><td colspan="2">Kontoinformationen</td></tr>';
                $nn .= '<tr><td colspan="2">Beim Abrufen der Kontoinformationen ist ein Fehler aufgetreten.<br></td></tr>';
                $nn .= '</table>';
                $help = str_replace('<donate-edomi>', $nn, $help);
            }
        }
        if (preg_match_all("'<pre>(.*?)</pre>'s", $help, $tmp) > 0) {
            for ($t = 0; $t < count($tmp[0]); $t++) {
                $n = $tmp[1][$t];
                $n = str_replace('<', '&lt;', $n);
                $n = str_replace('>', '&gt;', $n);
                $help = str_replace($tmp[0][$t], '<pre>' . $n . '</pre>', $help);
            }
        }
        $help = str_replace('<?', '&lt;?', $help);
        $help = str_replace('?>', '?&gt;', $help);
        $help = str_replace('<MAIN_PATH>', MAIN_PATH, $help);
        $help = str_replace('<BACKUP_PATH>', BACKUP_PATH, $help);
        $help = str_replace("<li>\n", '<li>', $help);
        $help = str_replace("<ul>\n", '<ul>', $help);
        $help = str_replace("</li>\n", '</li>', $help);
        $help = str_replace("</ul>\n", '</ul>', $help);
        $help = str_replace("<table>\n", '<table>', $help);
        $help = str_replace("<tr>\n", '<tr>', $help);
        $help = str_replace("<td>\n", '<td>', $help);
        $help = str_replace("</table>\n", '</table>', $help);
        $help = str_replace("</tr>\n", '</tr>', $help);
        $help = str_replace("</td>\n", '</td>', $help);
        $help = str_replace("<pre>\n", '<pre>', $help);
        $help = str_replace("</pre>\n", '</pre>', $help);
        $help = str_replace("<pre>", '<pre style="color:#000080; background:#f7f7f7; border:1px solid #e0e0e0; font-family:EDOMIfontMono,Menlo,Courier,monospace; padding:5px; tab-size:4; white-space:pre-wrap; word-wrap:break-word;">', $help);
        return $help;
    }
    return false;
}

function helpSearchRequest($search, $resultFn)
{
    $search = strtoupper($search);
    $search = str_replace('ä', 'Ä', $search);
    $search = str_replace('ö', 'Ö', $search);
    $search = str_replace('ü', 'Ü', $search);
    $hits = array();
    $n = glob(MAIN_PATH . "/www/admin/help/*.htm");
    foreach ($n as $pathFn) {
        if (is_file($pathFn)) {
            $fn = basename($pathFn, '.htm');
            if ($fn != 'result') {
                $help = file_get_contents($pathFn);
                if (strpos(strToUpper($help), $search) !== false) {
                    if (preg_match("'<h1>(.*?)</h1>'", $help, $tmp) > 0) {
                        if (preg_match_all("'<path>(.*?)</path>'s", $tmp[1], $link) > 0) {
                            for ($t = 0; $t < count($link[0]); $t++) {
                                $n = explode('***', $link[1][$t]);
                                $l = $n[0] . " / ";
                                $tmp[1] = str_replace($link[0][$t], $l, $tmp[1]);
                            }
                        }
                        if (strpos(strToUpper($tmp[1]), $search) !== false) {
                            array_unshift($hits, '&gt; <link>' . $tmp[1] . '***' . $fn . '</link> <span style="color:#909090;">' . $fn . '.htm</span>');
                        } else {
                            array_push($hits, '&gt; <link>' . $tmp[1] . '***' . $fn . '</link> <span style="color:#909090;">' . $fn . '.htm</span>');
                        }
                    } else {
                        if (preg_match("'<lbs-titel>(.*?)</lbs-titel>'", $help, $tmp) > 0) {
                            if (strpos(strToUpper($tmp[1]), $search) !== false) {
                                array_unshift($hits, '&gt; Logikbaustein: <link>' . $tmp[1] . '***' . $fn . '</link> <span style="color:#909090;">' . $fn . '.htm</span>');
                            } else {
                                array_push($hits, '&gt; Logikbaustein: <link>' . $tmp[1] . '***' . $fn . '</link> <span style="color:#909090;">' . $fn . '.htm</span>');
                            }
                        }
                    }
                }
                usleep(100);
            }
        }
    }
    $f = fopen(MAIN_PATH . '/www/admin/help/' . $resultFn . '.htm', 'w');
    fwrite($f, '<h1>Suchergebnisse</h1><br>');
    if (count($hits) > 0) {
        for ($t = 0; $t < count($hits); $t++) {
            fwrite($f, $hits[$t] . '<br>');
        }
    } else {
        fwrite($f, 'keine Treffer gefunden');
    }
    fclose($f);
    return count($hits);
} ?>
