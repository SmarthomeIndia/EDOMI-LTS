<?
/*
*/
?><? require(dirname(__FILE__) . "/../../www/shared/php/config.php");
require(MAIN_PATH . "/www/shared/php/base.php");
require(MAIN_PATH . "/www/shared/php/incl_dbinit.php");
require(MAIN_PATH . "/www/admin/include/php/base.php");
require(MAIN_PATH . "/main/include/php/config.php");
require(MAIN_PATH . "/main/include/php/base.php");
require(MAIN_PATH . "/main/include/php/update.php");
require(MAIN_PATH . "/main/include/php/incl_cmd.php");
require(MAIN_PATH . "/main/include/php/incl_log.php");
require(MAIN_PATH . "/main/include/php/incl_ga.php");
require(MAIN_PATH . "/main/include/php/incl_telnet.php");
require(MAIN_PATH . "/main/include/php/incl_fritzbox.php");
sql_connect();
if ($argv[4] == 1) {
    backup(1, $argv[5]);
}
if ($argv[4] == 2) {
    backup(2, '');
}
if ($argv[4] == 3) {
    dbAndLogRotate();
}
if ($argv[4] == 4) {
    saveServerIp();
}
if ($argv[4] == 5) {
    saveProject($argv[5]);
}
if ($argv[4] == 6) {
    loadProject($argv[5]);
}
if ($argv[4] == 7) {
    restore($argv[5]);
}
if ($argv[4] == 8) {
    callServerHeartbeatURL();
}
if ($argv[4] == 9) {
    systemupdate($argv[5]);
}
if ($argv[4] == 10) {
    autoupdate($argv[5]);
}
if ($argv[4] == 11) {
    visuPreviewActivation();
}
if ($argv[4] == 12) {
    deleteProject();
}
if ($argv[4] == 20) {
    importVse($argv[5]);
}
if ($argv[4] == 21) {
    importLbs($argv[5]);
}
sql_call("DELETE FROM edomiLive.RAMcmdQueue WHERE (id=" . $argv[1] . " AND status=1)");
sql_disconnect();
function backup($mode, $fn)
{
    exec('mkdir ' . BACKUP_PATH);
    if ($mode == 1) {
        writeToLog(0, true, 'Download-Backup erstellen: ' . $fn);
        deleteFiles(BACKUP_PATH . '/' . $fn);
        deleteFiles(MAIN_PATH . '/www/data/tmp/' . $fn);
        exec('tar -cf ' . BACKUP_PATH . '/' . $fn . ' ' . MAIN_PATH . '/ --exclude=' . MAIN_PATH . '/www/data/tmp/*');
        sql_call("FLUSH TABLES " . sql_getAllTables(false) . " WITH READ LOCK");
        exec('tar -rf ' . BACKUP_PATH . '/' . $fn . ' ' . MYSQL_PATH . '/edomiProject');
        exec('tar -rf ' . BACKUP_PATH . '/' . $fn . ' ' . MYSQL_PATH . '/edomiAdmin');
        exec('tar -rf ' . BACKUP_PATH . '/' . $fn . ' ' . MYSQL_PATH . '/edomiLive --exclude=' . MYSQL_PATH . '/edomiLive/RAM*.*');
        sql_call("UNLOCK TABLES");
        exec('cp ' . BACKUP_PATH . '/' . $fn . ' ' . MAIN_PATH . '/www/data/tmp');
        createInfoFile(MAIN_PATH . '/www/data/tmp/backupready.txt', array('ok'));
    } else if ($mode == 2) {
        $fn = date('Y-m-d-His') . '.edomibackup';
        writeToLog(0, true, 'Autobackup erstellen: ' . $fn);
        exec('tar -cf ' . BACKUP_PATH . '/' . $fn . ' ' . MAIN_PATH . '/ --exclude=' . MAIN_PATH . '/www/data/tmp/*');
        sql_call("FLUSH TABLES " . sql_getAllTables(false) . " WITH READ LOCK");
        exec('tar -rf ' . BACKUP_PATH . '/' . $fn . ' ' . MYSQL_PATH . '/edomiProject');
        exec('tar -rf ' . BACKUP_PATH . '/' . $fn . ' ' . MYSQL_PATH . '/edomiAdmin');
        exec('tar -rf ' . BACKUP_PATH . '/' . $fn . ' ' . MYSQL_PATH . '/edomiLive --exclude=' . MYSQL_PATH . '/edomiLive/RAM*.*');
        sql_call("UNLOCK TABLES");
    }
}

function restore($fn)
{
    if (file_exists(BACKUP_PATH . '/' . $fn)) {
        writeToLog(0, true, 'Restore einspielen: ' . $fn);
        exec('cp "' . BACKUP_PATH . '/' . $fn . '" /tmp/edomirestore.data');
        $fh = fopen('/tmp/edomirestore.sh', 'w');
        fwrite($fh, 'echo ""' . "\n");
        fwrite($fh, 'echo "--------------------------------------------------------------------------------"' . "\n");
        fwrite($fh, 'echo "edomirestore.sh"' . "\n");
        fwrite($fh, 'echo "Restore wird gestartet"' . "\n");
        fwrite($fh, 'echo "--------------------------------------------------------------------------------"' . "\n");
        fwrite($fh, 'sleep 3s' . "\n");
        fwrite($fh, 'clear' . "\n");
        fwrite($fh, 'service mysqld stop' . "\n");
        fwrite($fh, 'service httpd stop' . "\n");
        fwrite($fh, 'sleep 1s' . "\n");
        fwrite($fh, 'rm -rf ' . MAIN_PATH . "\n");
        fwrite($fh, 'rm -f ' . MYSQL_PATH . '/mysql.sock' . "\n");
        fwrite($fh, 'rm -rf ' . MYSQL_PATH . '/edomiAdmin' . "\n");
        fwrite($fh, 'rm -rf ' . MYSQL_PATH . '/edomiProject' . "\n");
        fwrite($fh, 'rm -rf ' . MYSQL_PATH . '/edomiLive' . "\n");
        fwrite($fh, 'sleep 1s' . "\n");
        fwrite($fh, 'tar -xf /tmp/edomirestore.data -C /' . "\n");
        fwrite($fh, 'chmod 777 -R ' . MAIN_PATH . "\n");
        fwrite($fh, 'rm -f /tmp/edomirestore.data' . "\n");
        fwrite($fh, 'rm -rf ' . MAIN_PATH . '/clientid.edomi' . "\n");
        fwrite($fh, 'service mysqld start' . "\n");
        fwrite($fh, 'service httpd start' . "\n");
        fwrite($fh, 'echo "--------------------------------------------------------------------------------"' . "\n");
        fwrite($fh, 'echo "Restore abgeschlossen. Reboot in 5 Sekunden..."' . "\n");
        fwrite($fh, 'echo "--------------------------------------------------------------------------------"' . "\n");
        fwrite($fh, 'sleep 5s' . "\n");
        fwrite($fh, 'reboot' . "\n");
        fwrite($fh, 'exit' . "\n");
        fclose($fh);
        createInfoFile(MAIN_PATH . '/www/data/tmp/restoreready.txt', array('ok'));
    }
}

function systemupdate($fn)
{
    if (file_exists(MAIN_PATH . '/www/data/tmp/' . $fn)) {
        if (preg_match('/\_(.*?)\./s', $fn, $v) > 0) {
            $vCurrent = sprintf("%01.2f", floatVal(global_version));
            $vUpdate = sprintf("%01.2f", floatVal($v[1]) / 100);
            $vValid = sprintf("%01.2f", floatVal(global_version) + 0.01);
            $ok = false;
            if ($vUpdate == $vValid) {
                $ok = true;
            }
            if ($vCurrent == '1.64' && $vUpdate == '2.00') {
                $ok = true;
            }
            if ($ok) {
                writeToLog(0, true, 'EDOMI-Update installieren: ' . $fn);
                exec('mv "' . MAIN_PATH . '/www/data/tmp/' . $fn . '" /tmp/edomiupdate.data');
                $fh = fopen('/tmp/edomiupdate.sh', 'w');
                fwrite($fh, 'echo ""' . "\n");
                fwrite($fh, 'echo "--------------------------------------------------------------------------------"' . "\n");
                fwrite($fh, 'echo "edomiupdate.sh"' . "\n");
                fwrite($fh, 'echo "Update wird installiert"' . "\n");
                fwrite($fh, 'echo "--------------------------------------------------------------------------------"' . "\n");
                fwrite($fh, 'sleep 3s' . "\n");
                fwrite($fh, 'clear' . "\n");
                fwrite($fh, 'service httpd stop' . "\n");
                fwrite($fh, 'sleep 1s' . "\n");
                if (floatVal($vUpdate) >= 2.01) {
                    fwrite($fh, 'tar -xf /tmp/edomiupdate.data -C ' . MAIN_PATH . "\n");
                } else {
                    fwrite($fh, 'tar -xf /tmp/edomiupdate.data -C ' . MAIN_PATH . ' --strip-components=3' . "\n");
                }
                fwrite($fh, 'chmod 777 -R ' . MAIN_PATH . "\n");
                fwrite($fh, 'rm -f /tmp/edomiupdate.data' . "\n");
                fwrite($fh, 'service httpd start' . "\n");
                fwrite($fh, 'sleep 1s' . "\n");
                fwrite($fh, 'if [ -f ' . MAIN_PATH . '/main/_edomiupdate.php ]; then' . "\n");
                fwrite($fh, 'php ' . MAIN_PATH . '/main/_edomiupdate.php' . "\n");
                fwrite($fh, 'rm -f ' . MAIN_PATH . '/main/_edomiupdate.php' . "\n");
                fwrite($fh, 'fi' . "\n");
                fwrite($fh, 'echo "--------------------------------------------------------------------------------"' . "\n");
                fwrite($fh, 'echo "Update abgeschlossen. Reboot in 5 Sekunden..."' . "\n");
                fwrite($fh, 'echo "--------------------------------------------------------------------------------"' . "\n");
                fwrite($fh, 'sleep 5s' . "\n");
                fwrite($fh, 'reboot' . "\n");
                fwrite($fh, 'exit' . "\n");
                fclose($fh);
                createInfoFile(MAIN_PATH . '/www/data/tmp/updateready.txt', array('ok'));
            } else {
                deleteFiles(MAIN_PATH . '/www/data/tmp/' . $fn);
                writeToLog(0, false, 'EDOMI-Update installieren (' . $fn . ') gescheitert: Falsche Version!');
                return false;
            }
        }
    }
}

function autoupdate($download)
{
    if (!isEmpty(global_urlAutoupdate)) {
        $clientId = get_clientId();
        $clientId_encrypt = strToUpper(hash('sha256', $clientId));
        $url = global_urlAutoupdate . '/checkupdate.php?clientid=' . $clientId . '&version=' . global_version . '&' . date('YmdHis');
        if ($download == 2) {
            writeToLog(0, true, 'EDOMI-Autoupdate: Update-Verfügbarkeit prüfen');
        }
        $ctx = stream_context_create(array('http' => array('timeout' => 10)));
        $r = file_get_contents($url, false, $ctx);
        $response = explode('/', $r);
        if (count($response) == 6 && $response[0] = 'OK' && $response[1] == $clientId_encrypt && is_numeric($response[2]) && !isEmpty($response[5])) {
            $n = getGADataFromID(12, 2, 'value');
            if ($n['value'] != 1) {
                writeGA(12, 1);
            }
            if ($download == 1) {
                $url = global_urlAutoupdate . '/' . $response[4] . '?' . date('YmdHis');
                $tmpFn = 'TMP_' . $response[4];
                deleteFiles(MAIN_PATH . '/www/data/tmp/' . $tmpFn);
                if (urlDownload($url, $tmpFn, 'autoupdatedownload.txt', $response[3])) {
                    if (getFileSize(MAIN_PATH . '/www/data/tmp/' . $tmpFn) == $response[3] && getFileMd5(MAIN_PATH . '/www/data/tmp/' . $tmpFn) == $response[5]) {
                        rename(MAIN_PATH . '/www/data/tmp/' . $tmpFn, MAIN_PATH . '/www/data/tmp/' . $response[4]);
                        writeToLog(0, true, 'EDOMI-Autoupdate: Download von Update-Version ' . $response[2] . ' erfolgreich');
                        createInfoFile(MAIN_PATH . '/www/data/tmp/autoupdateinfo.txt', array('DOWNLOADED', $response[2], $response[3], $response[4]));
                    } else {
                        deleteFiles(MAIN_PATH . '/www/data/tmp/' . $tmpFn);
                        writeToLog(0, false, 'EDOMI-Autoupdate: Ungültige Prüfsumme');
                        createInfoFile(MAIN_PATH . '/www/data/tmp/autoupdateinfo.txt', array('ERROR', $response[2], $response[3]));
                    }
                } else {
                    deleteFiles(MAIN_PATH . '/www/data/tmp/' . $tmpFn);
                    writeToLog(0, false, 'EDOMI-Autoupdate: Download gescheitert');
                    createInfoFile(MAIN_PATH . '/www/data/tmp/autoupdateinfo.txt', array('ERROR', $response[2], $response[3]));
                }
            } else {
                if ($download == 0) {
                    createInfoFile(MAIN_PATH . '/www/data/tmp/autoupdateinfo.txt', array('CHECKED', $response[2], $response[3]));
                }
            }
        } else {
            if ($r == 'NOUPDATE') {
                if ($download == 0) {
                    createInfoFile(MAIN_PATH . '/www/data/tmp/autoupdateinfo.txt', array('NOUPDATE'));
                }
            } else {
                if ($download == 0) {
                    createInfoFile(MAIN_PATH . '/www/data/tmp/autoupdateinfo.txt', array('ERROR'));
                }
            }
        }
    } else {
        if ($download == 0) {
            createInfoFile(MAIN_PATH . '/www/data/tmp/autoupdateinfo.txt', array('DISABLED'));
        }
    }
}

function dbAndLogRotate()
{
    writeToLog(0, true, 'Logs/Archive/Autobackups aufräumen');
    $keep = global_logSysKeep - 1;
    if ($keep < 1) {
        $keep = 0;
    }
    exec("find " . MAIN_PATH . "/www/data/log -mindepth 1 -maxdepth 1 -type f \( -name 'SYSLOG_*.*' \) -ctime +" . $keep . " -delete");
    $keep = global_logErrKeep - 1;
    if ($keep < 1) {
        $keep = 0;
    }
    exec("find " . MAIN_PATH . "/www/data/log -mindepth 1 -maxdepth 1 -type f \( -name 'ERRLOG_*.*' \) -ctime +" . $keep . " -delete");
    $keep = global_logVisuKeep - 1;
    if ($keep < 1) {
        $keep = 0;
    }
    exec("find " . MAIN_PATH . "/www/data/log -mindepth 1 -maxdepth 1 -type f \( -name 'VISULOG_*.*' \) -ctime +" . $keep . " -delete");
    $keep = global_logLogicKeep - 1;
    if ($keep < 1) {
        $keep = 0;
    }
    exec("find " . MAIN_PATH . "/www/data/log -mindepth 1 -maxdepth 1 -type f \( -name 'LOGICLOG_*.*' \) -ctime +" . $keep . " -delete");
    $keep = global_logMonKeep - 1;
    if ($keep < 1) {
        $keep = 0;
    }
    exec("find " . MAIN_PATH . "/www/data/log -mindepth 1 -maxdepth 1 -type f \( -name 'MONLOG_*.*' \) -ctime +" . $keep . " -delete");
    $keep = global_logCustomKeep - 1;
    if ($keep < 1) {
        $keep = 0;
    }
    exec("find " . MAIN_PATH . "/www/data/log -mindepth 1 -maxdepth 1 -type f \( -name 'CUSTOMLOG_*.*' \) -ctime +" . $keep . " -delete");
    $keep = global_backupKeep - 1;
    if ($keep < 1) {
        $keep = 0;
    }
    exec("find " . BACKUP_PATH . " -mindepth 1 -maxdepth 1 -type f \( -name '*.edomibackup' \) -ctime +" . $keep . " -delete");
    dbRotate_archivKo();
    dbRotate_archivMsg();
    dbRotate_archivPhone();
    dbRotate_archivCam();
    exec('rm -f /var/log/boot.log');
    exec('cat /dev/null > /var/log/lastlog');
    exec('cat /dev/null > /var/log/wtmp');
    exec('cat /dev/null > /var/log/log_http');
    exec('cat /dev/null > /var/log/log_mysql');
    exec('cat /dev/null > /var/log/log_mysql.err');
}

function dbRotate_archivKo()
{
    $ss1 = sql_call("SELECT * FROM edomiLive.archivKo WHERE (keep>0)");
    while ($n = sql_result($ss1)) {
        $cntBefore = sql_getCount('edomiLive.archivKoData', 'targetid=' . $n['id']);
        sql_call("DELETE FROM edomiLive.archivKoData WHERE (targetid=" . $n['id'] . " AND DATE_ADD(datetime,INTERVAL " . $n['keep'] . " DAY)<=" . sql_getNow() . ")");
        if (sql_affectedRows() > 0) {
            $cntAfter = sql_getCount('edomiLive.archivKoData', 'targetid=' . $n['id']);
            writeToLog(0, true, 'Datenarchiv (' . $n['id'] . '): ' . ($cntBefore - $cntAfter) . ' alte Einträge entfernt, ' . $cntAfter . ' Einträge verbleibend');
            if ($n['outgaid'] > 0) {
                writeGA($n['outgaid'], $cntAfter);
            }
        }
    }
    sql_close($ss1);
    $ss1 = sql_call("SELECT * FROM edomiLive.archivKo WHERE cmode>0 AND coffset>=1");
    while ($archiv = sql_result($ss1)) {
        if ($archiv['cinterval'] == 5) {
            $tmp_interval = '%Y %m %d %H';
        } else if ($archiv['cinterval'] == 10) {
            $tmp_interval = '%Y %m %d';
        } else if ($archiv['cinterval'] == 21) {
            $tmp_interval = '%Y %u';
        } else if ($archiv['cinterval'] == 22) {
            $tmp_interval = '%Y %m';
        } else if ($archiv['cinterval'] == 23) {
            $tmp_interval = '%Y';
        }
        if ($archiv['cunit'] == 9) {
            $tmp = date('Y-m-d H:i:s', strtotime('now -' . $archiv['coffset'] . ' hour'));
        } else if ($archiv['cunit'] == 10) {
            $tmp = date('Y-m-d H:i:s', strtotime('now -' . $archiv['coffset'] . ' day'));
        } else if ($archiv['cunit'] == 11) {
            $tmp = date('Y-m-d H:i:s', strtotime('now -' . $archiv['coffset'] . ' week'));
        } else if ($archiv['cunit'] == 12) {
            $tmp = date('Y-m-d H:i:s', strtotime('now -' . $archiv['coffset'] . ' month'));
        } else if ($archiv['cunit'] == 13) {
            $tmp = date('Y-m-d H:i:s', strtotime('now -' . $archiv['coffset'] . ' year'));
        } else if ($archiv['cunit'] == 19) {
            $tmp = date('Y-m-d H:00:00', strtotime('now -' . ($archiv['coffset'] - 1) . ' hour'));
        } else if ($archiv['cunit'] == 20) {
            $tmp = date('Y-m-d 00:00:00', strtotime('now -' . ($archiv['coffset'] - 1) . ' day'));
        } else if ($archiv['cunit'] == 21) {
            $tmp = date('Y-m-d 00:00:00', strtotime('now -' . (date('N') - 1) . ' day'));
            $tmp = date('Y-m-d 00:00:00', strtotime($tmp . ' -' . ($archiv['coffset'] - 1) . ' week'));
        } else if ($archiv['cunit'] == 22) {
            $tmp = date('Y-m-01 00:00:00', strtotime('now -' . ($archiv['coffset'] - 1) . ' month'));
        } else if ($archiv['cunit'] == 23) {
            $tmp = date('Y-01-01 00:00:00', strtotime('now -' . ($archiv['coffset'] - 1) . ' year'));
        } else {
            $tmp = false;
        }
        if ($tmp !== false && $archiv['coffset'] >= 1) {
            $changed = false;
            $cntBefore = sql_getCount('edomiLive.archivKoData', 'targetid=' . $archiv['id']);
            if ($archiv['cmode'] == 1) {
                $ss2 = sql_call("SELECT COUNT(*) AS anz1,AVG(gavalue) AS r_value,MIN(datetime) AS r_mindate,MAX(datetime) AS r_maxdate FROM edomiLive.archivKoData WHERE targetid=" . $archiv['id'] . " AND datetime<'" . $tmp . "' GROUP BY date_format(datetime, '" . $tmp_interval . "') HAVING anz1>1");
            } else if ($archiv['cmode'] == 2) {
                $ss2 = sql_call("SELECT COUNT(*) AS anz1,MIN(gavalue) AS r_value,MIN(datetime) AS r_mindate,MAX(datetime) AS r_maxdate FROM edomiLive.archivKoData WHERE targetid=" . $archiv['id'] . " AND datetime<'" . $tmp . "' GROUP BY date_format(datetime, '" . $tmp_interval . "') HAVING anz1>1");
            } else if ($archiv['cmode'] == 3) {
                $ss2 = sql_call("SELECT COUNT(*) AS anz1,MAX(gavalue) AS r_value,MIN(datetime) AS r_mindate,MAX(datetime) AS r_maxdate FROM edomiLive.archivKoData WHERE targetid=" . $archiv['id'] . " AND datetime<'" . $tmp . "' GROUP BY date_format(datetime, '" . $tmp_interval . "') HAVING anz1>1");
            }
            while ($n = sql_result($ss2)) {
                if ($archiv['cts'] == 0) {
                    $ts[0] = $n['r_mindate'];
                    $ts[1] = '000000';
                } else if ($archiv['cts'] == 1) {
                    $ts[0] = date('Y-m-d H:i:s', intVal((strtotime($n['r_mindate']) + strtotime($n['r_maxdate'])) / 2));
                    $ts[1] = '000000';
                } else if ($archiv['cts'] == 2) {
                    $ts[0] = $n['r_maxdate'];
                    $ts[1] = '999999';
                } else {
                    if ($archiv['cinterval'] == 5) {
                        if ($archiv['cts'] == 10) {
                            $ts[0] = date('Y-m-d H:00:00', strtotime($n['r_mindate']));
                            $ts[1] = '000000';
                        }
                        if ($archiv['cts'] == 11) {
                            $ts[0] = date('Y-m-d H:i:s', intVal((strtotime(date('Y-m-d H:00:00', strtotime($n['r_mindate']))) + strtotime(date('Y-m-d H:59:59', strtotime($n['r_mindate'])))) / 2));
                            $ts[1] = '000000';
                        }
                        if ($archiv['cts'] == 12) {
                            $ts[0] = date('Y-m-d H:59:59', strtotime($n['r_mindate']));
                            $ts[1] = '999999';
                        }
                    } else if ($archiv['cinterval'] == 10) {
                        if ($archiv['cts'] == 10) {
                            $ts[0] = date('Y-m-d 00:00:00', strtotime($n['r_mindate']));
                            $ts[1] = '000000';
                        }
                        if ($archiv['cts'] == 11) {
                            $ts[0] = date('Y-m-d H:i:s', intVal((strtotime(date('Y-m-d 00:00:00', strtotime($n['r_mindate']))) + strtotime(date('Y-m-d 23:59:59', strtotime($n['r_mindate'])))) / 2));
                            $ts[1] = '000000';
                        }
                        if ($archiv['cts'] == 12) {
                            $ts[0] = date('Y-m-d 23:59:59', strtotime($n['r_mindate']));
                            $ts[1] = '999999';
                        }
                    } else if ($archiv['cinterval'] == 21) {
                        if ($archiv['cts'] == 10) {
                            $ts[0] = date('Y-m-d 00:00:00', strtotime($n['r_mindate'] . ' -' . (date('N', strtotime($n['r_mindate'])) - 1) . ' days'));
                            $ts[1] = '000000';
                        }
                        if ($archiv['cts'] == 11) {
                            $ts[0] = date('Y-m-d H:i:s', intVal((strtotime(date('Y-m-d 00:00:00', strtotime($n['r_mindate'] . ' -' . (date('N', strtotime($n['r_mindate'])) - 1) . ' days'))) + strtotime(date('Y-m-d 23:59:59', strtotime($n['r_mindate'] . ' +' . (7 - date('N', strtotime($n['r_mindate']))) . ' days')))) / 2));
                            $ts[1] = '000000';
                        }
                        if ($archiv['cts'] == 12) {
                            $ts[0] = date('Y-m-d 23:59:59', strtotime($n['r_mindate'] . ' +' . (7 - date('N', strtotime($n['r_mindate']))) . ' days'));
                            $ts[1] = '999999';
                        }
                    } else if ($archiv['cinterval'] == 22) {
                        if ($archiv['cts'] == 10) {
                            $ts[0] = date('Y-m-01 00:00:00', strtotime($n['r_mindate']));
                            $ts[1] = '000000';
                        }
                        if ($archiv['cts'] == 11) {
                            $ts[0] = date('Y-m-d H:i:s', intVal((strtotime(date('Y-m-01 00:00:00', strtotime($n['r_mindate']))) + strtotime(date('Y-m-' . date('t', strtotime($n['r_mindate'])) . ' 23:59:59', strtotime($n['r_mindate'])))) / 2));
                            $ts[1] = '000000';
                        }
                        if ($archiv['cts'] == 12) {
                            $ts[0] = date('Y-m-' . date('t', strtotime($n['r_mindate'])) . ' 23:59:59', strtotime($n['r_mindate']));
                            $ts[1] = '999999';
                        }
                    } else if ($archiv['cinterval'] == 23) {
                        if ($archiv['cts'] == 10) {
                            $ts[0] = date('Y-01-01 00:00:00', strtotime($n['r_mindate']));
                            $ts[1] = '000000';
                        }
                        if ($archiv['cts'] == 11) {
                            $ts[0] = date('Y-m-d H:i:s', intVal((strtotime(date('Y-01-01 00:00:00', strtotime($n['r_mindate']))) + strtotime(date('Y-12-31 23:59:59', strtotime($n['r_maxdate'])))) / 2));
                            $ts[1] = '000000';
                        }
                        if ($archiv['cts'] == 12) {
                            $ts[0] = date('Y-12-31 23:59:59', strtotime($n['r_mindate']));
                            $ts[1] = '999999';
                        }
                    }
                }
                if (!isEmpty($archiv['clist'])) {
                    $n['r_value'] = round($n['r_value'], $archiv['clist']);
                }
                sql_call("DELETE FROM edomiLive.archivKoData WHERE targetid=" . $archiv['id'] . " AND datetime>='" . $n['r_mindate'] . "' AND datetime<='" . $n['r_maxdate'] . "'");
                sql_call("INSERT INTO edomiLive.archivKoData (datetime,ms,targetid,gavalue) VALUES ('" . $ts[0] . "','" . $ts[1] . "'," . $archiv['id'] . ",'" . sql_encodeValue($n['r_value']) . "')");
                $changed = true;
            }
            sql_close($ss2);
            if ($changed) {
                $cntAfter = sql_getCount('edomiLive.archivKoData', 'targetid=' . $archiv['id']);
                writeToLog(0, true, 'Datenarchiv (' . $archiv['id'] . ') verdichtet: ' . ($cntBefore - $cntAfter) . ' Einträge von ' . $cntBefore . ' verrechnet und entfernt, ' . $cntAfter . ' Einträge verbleibend');
                if ($archiv['outgaid'] > 0) {
                    writeGA($archiv['outgaid'], $cntAfter);
                }
            }
        }
    }
    sql_close($ss1);
    sql_call("OPTIMIZE TABLE edomiLive.archivKoData");
}

function dbRotate_archivMsg()
{
    $ss1 = sql_call("SELECT * FROM edomiLive.archivMsg WHERE (keep>0)");
    while ($n = sql_result($ss1)) {
        $cntBefore = sql_getCount('edomiLive.archivMsgData', 'targetid=' . $n['id']);
        sql_call("DELETE FROM edomiLive.archivMsgData WHERE (targetid=" . $n['id'] . " AND DATE_ADD(datetime,INTERVAL " . $n['keep'] . " DAY)<=" . sql_getNow() . ")");
        if (sql_affectedRows() > 0) {
            $cntAfter = sql_getCount('edomiLive.archivMsgData', 'targetid=' . $n['id']);
            writeToLog(0, true, 'Meldungsarchiv (' . $n['id'] . '): ' . ($cntBefore - $cntAfter) . ' alte Einträge entfernt, ' . $cntAfter . ' Einträge verbleibend');
            if ($n['outgaid'] > 0) {
                writeGA($n['outgaid'], $cntAfter);
            }
        }
    }
    sql_close($ss1);
    sql_call("OPTIMIZE TABLE edomiLive.archivMsgData");
}

function dbRotate_archivPhone()
{
    $ss1 = sql_call("SELECT * FROM edomiLive.archivPhone WHERE (keep>0)");
    while ($n = sql_result($ss1)) {
        $cntBefore = sql_getCount('edomiLive.archivPhoneData', 'targetid=' . $n['id']);
        sql_call("DELETE FROM edomiLive.archivPhoneData WHERE (targetid=" . $n['id'] . " AND DATE_ADD(datetime,INTERVAL " . $n['keep'] . " DAY)<=" . sql_getNow() . ")");
        if (sql_affectedRows() > 0) {
            $cntAfter = sql_getCount('edomiLive.archivPhoneData', 'targetid=' . $n['id']);
            writeToLog(0, true, 'Anrufarchiv (' . $n['id'] . '): ' . ($cntBefore - $cntAfter) . ' alte Einträge entfernt, ' . $cntAfter . ' Einträge verbleibend');
            if ($n['outgaid'] > 0) {
                writeGA($n['outgaid'], $cntAfter);
            }
        }
    }
    sql_close($ss1);
    sql_call("OPTIMIZE TABLE edomiLive.archivPhoneData");
}

function dbRotate_archivCam()
{
    $ss1 = sql_call("SELECT * FROM edomiLive.archivCam WHERE (keep>0)");
    while ($n = sql_result($ss1)) {
        $ss2 = sql_call("SELECT * FROM edomiLive.archivCamData WHERE (targetid=" . $n['id'] . " AND DATE_ADD(datetime,INTERVAL " . $n['keep'] . " DAY)<=" . sql_getNow() . ")");
        while ($nn = sql_result($ss2)) {
            $fn = getArchivCamFilename($nn['targetid'], $nn['camid'], $nn['datetime'], $nn['ms']);
            deleteFiles(MAIN_PATH . '/www/data/liveproject/cam/archiv/' . $fn . '.jpg');
        }
        sql_close($ss2);
        $cntBefore = sql_getCount('edomiLive.archivCamData', 'targetid=' . $n['id']);
        sql_call("DELETE FROM edomiLive.archivCamData WHERE (targetid=" . $n['id'] . " AND DATE_ADD(datetime,INTERVAL " . $n['keep'] . " DAY)<=" . sql_getNow() . ")");
        if (sql_affectedRows() > 0) {
            $cntAfter = sql_getCount('edomiLive.archivCamData', 'targetid=' . $n['id']);
            writeToLog(0, true, 'Kameraarchiv (' . $n['id'] . '): ' . ($cntBefore - $cntAfter) . ' alte Bilder entfernt, ' . $cntAfter . ' Bilder verbleibend');
            if ($n['outgaid'] > 0) {
                writeGA($n['outgaid'], $cntAfter);
            }
        }
    }
    sql_close($ss1);
    $files = glob(MAIN_PATH . '/www/data/liveproject/cam/archiv/*.jpg');
    foreach ($files as $pathFn) {
        if (is_file($pathFn)) {
            $n = explode('-', basename($pathFn));
            $nn[0] = substr($n[0], 6, 100);
            $nn[1] = substr($n[1], 3, 100);
            $nn[2] = date('Y-m-d', strtotime(substr($n[2], 0, 8)));
            $nn[3] = date('H:i:s', strtotime(substr($n[2], 8, 6)));
            $nn[4] = substr($n[2], 14, 6);
            $ss1 = sql_call("SELECT targetid FROM edomiLive.archivCamData WHERE (targetid='" . $nn[0] . "' AND camid='" . $nn[1] . "' AND datetime='" . date('Y-m-d H:i:s', strtotime($nn[2] . ' ' . $nn[3])) . "' AND ms='" . $nn[4] . "')");
            if (!sql_result($ss1)) {
                writeToLog(0, true, 'Kameraarchive: Dateileiche ' . basename($pathFn) . ' entfernt');
                deleteFiles($pathFn);
            }
            sql_close($ss1);
        }
    }
    sql_call("OPTIMIZE TABLE edomiLive.archivCamData");
}

function saveServerIp()
{
    if (global_serverWANIP == 1) {
        $ip = fritzbox_GetWanIP();
        if ($ip !== false) {
            $n = getGADataFromID(3, 2);
            if ($n['value'] != $ip) {
                writeGA(3, $ip);
            }
        }
    } else if (global_serverWANIP == 2) {
        $ctx = stream_context_create(array('http' => array('timeout' => 10)));
        $ip = file_get_contents('http://ipecho.net/plain', false, $ctx, 0, 15);
        if (filter_var($ip, FILTER_VALIDATE_IP) !== false) {
            $n = getGADataFromID(3, 2);
            if ($n['value'] != $ip) {
                writeGA(3, $ip);
            }
        }
    } else if (global_serverWANIP == 3) {
        $ctx = stream_context_create(array('http' => array('timeout' => 10)));
        $ip = file_get_contents('http://edomi.de/get_wanip.php', false, $ctx, 0, 15);
        if (filter_var($ip, FILTER_VALIDATE_IP) !== false) {
            $n = getGADataFromID(3, 2);
            if ($n['value'] != $ip) {
                writeGA(3, $ip);
            }
        }
    }
}

function callServerHeartbeatURL()
{
    if (!isEmpty(global_serverHeartbeat)) {
        $ctx = stream_context_create(array('http' => array('timeout' => 10)));
        $serverIP = file_get_contents(global_serverHeartbeat . '?date=' . date('d.m.Y') . '&time=' . date('H:i:s'), false, $ctx, 0, 1);
    }
}

function saveProject($mode)
{
    $r = array(0, 0, '');
    $fn = '';
    $project = sql_getValues('edomiAdmin.project', '*', 'edit=1');
    if ($mode == 1 && $project !== false) {
        writeToTmpLog(0, 'Arbeitsprojekt archivieren: ' . ajaxValueHTML($project['name']));
        $fn = MAIN_PATH . '/www/data/projectarchiv/prj-' . $project['id'] . '.edomiproject';
        sql_call("UPDATE edomiAdmin.project SET savedate=" . sql_getNow() . " WHERE (id=" . $project['id'] . ")");
    }
    if ($mode == 2 && $project !== false) {
        writeToTmpLog(0, 'Arbeitsprojekt als Duplikat archivieren: ' . ajaxValueHTML($project['name'] . ((global_duplicateSuffix) ? '-KOPIE' : '')));
        sql_call("INSERT INTO edomiAdmin.project (name,createdate,savedate,edit,live) VALUES ('" . sql_encodeValue($project['name']) . ((global_duplicateSuffix) ? '-KOPIE' : '') . "','" . $project['createdate'] . "'," . sql_getNow() . ",0,0)");
        if ($tmp = sql_insertId()) {
            $fn = MAIN_PATH . '/www/data/projectarchiv/prj-' . $tmp . '.edomiproject';
        } else {
            writeToTmpLog(1, 'Fehler: Eintrag in Datenbank fehlgeschlagen', true);
            $r[1]++;
        }
    }
    if ($mode == 3 && $project !== false) {
        writeToTmpLog(0, 'Arbeitsprojekt herunterladen: ' . ajaxValueHTML($project['name']));
        deleteFiles(MAIN_PATH . '/www/data/tmp/*.edomiproject');
        $tmp = substr(preg_replace("/[^a-zA-Z0-9\ \-\_äöüÄÖÜß]/", '', $project['name']), 0, 200);
        if (!isEmpty($tmp)) {
            deleteFiles(MAIN_PATH . '/www/data/tmp/projectsaveready.txt');
            $tmp .= '.edomiproject';
            $fn = MAIN_PATH . '/www/data/tmp/' . $tmp;
            writeToTmpLog(1, 'Download-Dateiname generiert: ' . ajaxValueHTML($tmp));
            $r[2] = $tmp;
        } else {
            writeToTmpLog(1, 'Fehler: Dateiname konnte nicht generiert werden', true);
            $r[1]++;
        }
    }
    createInfoFile(MAIN_PATH . '/www/data/tmp/projectsave_status.txt', array(0));
    if (!isEmpty($fn) && $project !== false) {
        writeToLog(0, true, 'Projekt archivieren/herunterladen: ' . basename($fn));
        if (sql_tableExists('edomiProject.editProjectInfo')) {
            sql_call("UPDATE edomiProject.editProjectInfo SET edomiversion='" . global_version . "' WHERE id=1");
        } else {
            sql_call("CREATE TABLE edomiProject.editProjectInfo (id BIGINT UNSIGNED DEFAULT NULL,edomiversion VARCHAR(30) DEFAULT NULL,projectversion VARCHAR(30) DEFAULT NULL,KEY (id)) ENGINE=MyISAM DEFAULT CHARSET=latin1");
            sql_call("INSERT INTO edomiProject.editProjectInfo (id,edomiversion) VALUES (1,'" . global_version . "')");
        }
        writeToTmpLog(1, 'Dateien packen');
        exec('tar -cf "' . $fn . '" ' . MAIN_PATH . '/www/data/project');
        createInfoFile(MAIN_PATH . '/www/data/tmp/projectsave_status.txt', array(50));
        writeToTmpLog(1, 'Datenbanken packen');
        sql_call("FLUSH TABLES " . sql_getAllTables(false, 'Project'));
        exec('tar -rf "' . $fn . '" ' . MYSQL_PATH . '/edomiProject');
        sql_call("UNLOCK TABLES");
        createInfoFile(MAIN_PATH . '/www/data/tmp/projectsave_report.txt', array(0));
    } else {
        writeToTmpLog(1, 'Kein Arbeitsprojekt verfügbar oder unbekannter Fehler', true);
        $r[1]++;
    }
    createInfoFile(MAIN_PATH . '/www/data/tmp/projectsave_status.txt', array(100));
    createInfoFile(MAIN_PATH . '/www/data/tmp/projectsave_report.txt', $r);
}

function loadProject($prjId)
{
    $fn = 'prj-' . $prjId . '.edomiproject';
    $errCount = array(0, 0);
    writeToTmpLog(0, 'Archiviertes Arbeitsprojekt öffen: ' . ajaxValueHTML($fn));
    createInfoFile(MAIN_PATH . '/www/data/tmp/projectload_status.txt', array(0));
    if (file_exists(MAIN_PATH . '/www/data/projectarchiv/' . $fn)) {
        writeToLog(0, true, 'Projekt öffnen: ' . $fn);
        deleteFiles(MAIN_PATH . '/www/data/project/*.*');
        deleteFiles(MAIN_PATH . '/www/data/project/visu/img/*.*');
        deleteFiles(MAIN_PATH . '/www/data/project/visu/etc/*.*');
        sql_call("FLUSH TABLES " . sql_getAllTables(false, 'Project'));
        exec('rm -rf ' . MYSQL_PATH . '/edomiProject');
        exec('tar -xf "' . MAIN_PATH . '/www/data/projectarchiv/' . $fn . '" -C /');
        sql_call("UNLOCK TABLES");
        $version = sql_getValue('edomiProject.editProjectInfo', 'edomiversion', 'id=1');
        if (!is_numeric($version)) {
            $version = '0';
        }
        writeToTmpLog(1, 'Projekt: EDOMI-Version ' . $version);
        $version = floatVal($version);
        $tmp = edomi_update($version, false);
        if (!isEmpty($tmp)) {
            writeToTmpLog(1, 'Projekt: Updates anwenden (' . $tmp . ')');
            writeToLog(0, true, 'Projekt ' . $fn . ': Update auf EDOMI-Versionen ' . $tmp);
        }
        sql_call("DELETE FROM edomiAdmin.project WHERE (savedate IS NULL)");
        sql_call("UPDATE edomiAdmin.project SET edit=0");
        sql_call("UPDATE edomiAdmin.project SET edit=1 WHERE (id=" . $prjId . ")");
        createInfoFile(MAIN_PATH . '/www/data/tmp/projectload_status.txt', array(33));
        writeToTmpLog(1, 'Logikbausteine einlesen');
        $tmp = lbs_importAll(2);
        $errCount[0] += $tmp[1];
        createInfoFile(MAIN_PATH . '/www/data/tmp/projectload_status.txt', array(66));
        writeToTmpLog(1, 'Visuelemente einlesen');
        $tmp = vse_importAll(2);
        $errCount[0] += $tmp[1];
    } else {
        writeToTmpLog(1, 'Datei nicht gefunden', true);
        $errCount[1]++;
    }
    createInfoFile(MAIN_PATH . '/www/data/tmp/projectload_status.txt', array(100));
    createInfoFile(MAIN_PATH . '/www/data/tmp/projectload_report.txt', $errCount);
}

function deleteProject()
{
    $errCount = array(0, 0);
    writeToTmpLog(0, 'Arbeitsprojekt löschen und leeres Arbeitsprojekt erstellen');
    createInfoFile(MAIN_PATH . '/www/data/tmp/projectdelete_status.txt', array(0));
    createInfoFile(MAIN_PATH . '/www/data/tmp/projectdelete_status.txt', array(25));
    writeToTmpLog(1, 'Projekt-Datenbank initialisieren');
    if (init_DB_Project()) {
        writeToTmpLog(1, 'Dateien löschen');
        deleteFiles(MAIN_PATH . '/www/data/project/visu/img/*.*');
        deleteFiles(MAIN_PATH . '/www/data/project/visu/etc/*.*');
        deleteFiles(MAIN_PATH . '/www/data/project/*.*');
        writeToTmpLog(1, 'nicht archivierte Projekte löschen');
        sql_call("DELETE FROM edomiAdmin.project WHERE (savedate IS NULL)");
        writeToTmpLog(1, 'neues Arbeitsprojekt erstellen');
        sql_call("UPDATE edomiAdmin.project SET edit=0");
        sql_call("INSERT INTO edomiAdmin.project (name,createdate,edit,live) VALUES ('Neues Projekt'," . sql_getNow() . ",1,0)");
        createInfoFile(MAIN_PATH . '/www/data/tmp/projectdelete_status.txt', array(50));
        writeToTmpLog(1, 'Logikbausteine einlesen');
        $tmp = lbs_importAll(2);
        $errCount[0] += $tmp[1];
        createInfoFile(MAIN_PATH . '/www/data/tmp/projectdelete_status.txt', array(75));
        writeToTmpLog(1, 'Visuelemente einlesen');
        $tmp = vse_importAll(2);
        $errCount[0] += $tmp[1];
    } else {
        writeToTmpLog(1, 'Projekt-Datenbank konnte nicht initialisiert werden', true);
        $errCount[1]++;
    }
    createInfoFile(MAIN_PATH . '/www/data/tmp/projectdelete_status.txt', array(100));
    createInfoFile(MAIN_PATH . '/www/data/tmp/projectdelete_report.txt', $errCount);
}

function visuPreviewActivation()
{
    require(MAIN_PATH . "/main/include/php/incl_process.php");
    require(MAIN_PATH . "/main/include/php/incl_activation.php");
    $tmp = new class_projectActivation();
    $tmp->start_visuPreview();
}

function importVse($mode)
{
    $errCount = array(0, 0);
    deleteFiles(MAIN_PATH . '/www/data/tmp/importvse_status.txt');
    deleteFiles(MAIN_PATH . '/www/data/tmp/importvse_report.txt');
    writeToTmpLog(0, 'Visuelemente einlesen');
    createInfoFile(MAIN_PATH . '/www/data/tmp/importvse_status.txt', array(0));
    if ($mode == -1) {
        $tmp = vse_importAll(1);
        $errCount[0] += $tmp[1];
        writeToTmpLog(1, $tmp[0] . ' Visuelemente erfolgreich eingelesen / ' . $errCount[0] . ' fehlerhafte Visuelemente');
    } else if ($mode == -2) {
        $c = 0;
        $fns = glob(MAIN_PATH . '/www/admin/vse/*_vse.php');
        foreach ($fns as $pathFn) {
            if (is_file($pathFn)) {
                $id = explode('_', basename($pathFn));
                $vseID = $id[0];
                if ($vseID >= 1000 && $vseID <= 99999999) {
                    $tmp = vse_import($vseID, 1);
                    if ($tmp === false || $tmp[0] > 0) {
                        $errCount[0]++;
                    } else {
                        $c++;
                    }
                }
            }
        }
        writeToTmpLog(1, $c . ' Visuelemente erfolgreich eingelesen / ' . $errCount[0] . ' fehlerhafte Visuelemente');
    } else if ($mode >= 1000 && $mode <= 99999999) {
        $c = 0;
        $tmp = vse_import($mode, 1);
        if ($tmp === false || $tmp[0] > 0) {
            $errCount[0]++;
        } else {
            $c++;
        }
        writeToTmpLog(1, $c . ' Visuelemente erfolgreich eingelesen / ' . $errCount[0] . ' fehlerhafte Visuelemente');
    }
    createInfoFile(MAIN_PATH . '/www/data/tmp/importvse_status.txt', array(100));
    createInfoFile(MAIN_PATH . '/www/data/tmp/importvse_report.txt', $errCount);
}

function importLbs($mode)
{
    $errCount = array(0, 0);
    deleteFiles(MAIN_PATH . '/www/data/tmp/importlbs_status.txt');
    deleteFiles(MAIN_PATH . '/www/data/tmp/importlbs_report.txt');
    writeToTmpLog(0, 'Logikbausteine einlesen');
    createInfoFile(MAIN_PATH . '/www/data/tmp/importlbs_status.txt', array(0));
    if ($mode == -1) {
        $tmp = lbs_importAll(1);
        $errCount[0] += $tmp[1];
        writeToTmpLog(1, $tmp[0] . ' Logikbausteine erfolgreich eingelesen / ' . $errCount[0] . ' fehlerhafte Logikbausteine');
    } else if ($mode == -2) {
        $c = 0;
        $fns = glob(MAIN_PATH . '/www/admin/lbs/19??????_lbs.php');
        foreach ($fns as $pathFn) {
            if (is_file($pathFn)) {
                $lbsID = substr(basename($pathFn), 0, 8);
                if ($lbsID >= 19000000 && $lbsID <= 19999999) {
                    $tmp = lbs_import($lbsID, 1);
                    if ($tmp === false || $tmp[0] > 0) {
                        $errCount[0]++;
                    } else {
                        $c++;
                    }
                }
            }
        }
        writeToTmpLog(1, $c . ' Logikbausteine erfolgreich eingelesen / ' . $errCount[0] . ' fehlerhafte Logikbausteine');
    } else if ($mode >= 19000000 && $mode <= 19999999) {
        $c = 0;
        $tmp = lbs_import($mode, 1);
        if ($tmp === false || $tmp[0] > 0) {
            $errCount[0]++;
        } else {
            $c++;
        }
        writeToTmpLog(1, $c . ' Logikbausteine erfolgreich eingelesen / ' . $errCount[0] . ' fehlerhafte Logikbausteine');
    }
    createInfoFile(MAIN_PATH . '/www/data/tmp/importlbs_status.txt', array(100));
    createInfoFile(MAIN_PATH . '/www/data/tmp/importlbs_report.txt', $errCount);
} ?>
