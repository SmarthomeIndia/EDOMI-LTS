<?
require(dirname(__FILE__) . "/../../www/shared/php/config.php");
require(MAIN_PATH . "/www/shared/php/base.php");
require(MAIN_PATH . "/www/shared/php/incl_dbinit.php");
require(MAIN_PATH . "/www/shared/php/incl_camera.php");
require(MAIN_PATH . "/main/include/php/config.php");
require(MAIN_PATH . "/main/include/php/base.php");
require(MAIN_PATH . "/main/include/php/incl_log.php");
require(MAIN_PATH . "/main/include/php/incl_ga.php");
require(MAIN_PATH . "/main/include/php/incl_cmd.php");
set_time_limit(30);
sql_connect();
saveInArchive($argv[4]);
sql_call("DELETE FROM edomiLive.RAMcmdQueue WHERE (id=" . $argv[1] . " AND status=1)");
sql_disconnect();
function saveInArchive($archivId)
{
    $archiv = sql_getValues('edomiLive.archivCam', '*', 'id=' . $archivId);
    if ($archiv !== false) {
        if (checkArchivDelay('archivCamData', $archivId, $archiv['delay'])) {
            $img = getLiveCamImg($archiv['camid'], 0);
            if ($img) {
                $ts = getTimestamp();
                $fn = MAIN_PATH . '/www/data/liveproject/cam/archiv/' . getArchivCamFilename($archivId, $archiv['camid'], $ts[0], $ts[1]) . '.jpg';
                $f = fopen($fn, 'w');
                fwrite($f, $img);
                fclose($f);
                sql_call("INSERT INTO edomiLive.archivCamData (datetime,ms,camid,targetid) VALUES ('" . $ts[0] . "','" . $ts[1] . "'," . $archiv['camid'] . "," . $archivId . ")");
                if ($archiv['outgaid'] > 0) {
                    writeGA($archiv['outgaid'], sql_getCount('edomiLive.archivCamData', 'targetid=' . $archivId));
                }
                if ($archiv['outgaid2'] > 0) {
                    $tmp = $archiv['id'] . ';' . $archiv['camid'] . ';' . sql_getDate($ts[0]) . ';' . sql_getTime($ts[0]) . ';' . $ts[1] . ';' . $fn;
                    writeGA($archiv['outgaid2'], $tmp);
                }
                return true;
            }
        }
    }
    return false;
}
?>
