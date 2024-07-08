<?
/*
*/
?><? ?><? require("../../shared/php/config.php");
require(MAIN_PATH . "/www/shared/php/base.php");
require(MAIN_PATH . "/www/shared/php/incl_http.php");
require(MAIN_PATH . "/www/admin/include/php/config.php");
require(MAIN_PATH . "/www/admin/include/php/base.php");
$maxFileSize = 100000000;
sql_connect(); ?>
<script type="text/javascript">
    <? if (checkAdmin($sid, false)) { if (httpGetVar('multiple') == 1) { $folderid = httpGetVar('folderid'); $suffixes = httpGetVar('suffixes'); $ajaxOk = httpGetVar('ajaxok'); $ajaxErr = httpGetVar('ajaxerr'); $ajaxAppId = httpGetVar('ajaxappid'); $ajaxWinId = httpGetVar('ajaxwinid'); $ajaxData = httpGetVar('ajaxdata'); $allowedSuffixes = explode(';', strtolower($suffixes)); $filenames = ''; $sumSize = 0; for ($t = 0; $t < count($_FILES['files']['name']); $t++) {
        $sumSize += $_FILES["files"]["size"][$t];
    } if ($sumSize < $maxFileSize) { for ($t = 0; $t < count($_FILES['files']['name']); $t++) {
        $uploadSuffix = pathinfo($_FILES["files"]["name"][$t], PATHINFO_EXTENSION);
        $filename = $folderid . '-' . $t . '_' . $_FILES["files"]["name"][$t];
        if ($_FILES["files"]["size"][$t] < $maxFileSize && in_array(strtolower($uploadSuffix), $allowedSuffixes)) {
            if ($_FILES["files"]["error"][$t] == 0) {
                if (move_uploaded_file($_FILES["files"]["tmp_name"][$t], MAIN_PATH . '/www/data/tmp/' . $filename)) {
                    $filenames .= ';' . $filename . '|';
                } else {
                    $filenames .= 'Fehler beim Hochladen der Datei;' . $filename . '|';
                }
            } else {
                $filenames .= 'Fehler beim Hochladen der Datei;' . $filename . '|';
            }
        } else {
            $filenames .= 'Falsches Format oder Bilddatei zu groß;' . $filename . '|';
        }
    } if ($ajaxOk) { ?>
    parent.ajax("<?echo $ajaxOk;?>", "<?echo $ajaxAppId;?>", "<?echo $ajaxWinId;?>", "<?echo $ajaxData;?>", "<?echo $filenames;?>");
    <? } } else { ?>
    parent.jsConfirm("Die Gesamtgröße der ausgewählten Dateien übersteigt das Limit von <?echo($maxFileSize / 1000000);?> MB.", "", "none");
    <? } ?>
    parent.hideBusyWindow();
    <? } else { $filename = httpGetVar('filename'); $suffixes = httpGetVar('suffixes'); $ajaxOk = httpGetVar('ajaxok'); $ajaxErr = httpGetVar('ajaxerr'); $ajaxAppId = httpGetVar('ajaxappid'); $ajaxWinId = httpGetVar('ajaxwinid'); $ajaxData = httpGetVar('ajaxdata'); $desiredSuffix = pathinfo($filename, PATHINFO_EXTENSION); $allowedSuffixes = explode(';', strtolower($suffixes)); $uploadSuffix = pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION); $uploadOk = false; if (count($_FILES["file"]) > 0 && $_FILES["file"]["size"] < $maxFileSize && in_array(strtolower($uploadSuffix), $allowedSuffixes)) { if ($_FILES["file"]["error"] == 0) { if (isEmpty($filename)) {
        $filename = basename($_FILES["file"]["name"]);
    } else {
        if (isEmpty($desiredSuffix)) {
            $filename .= '.' . $uploadSuffix;
        }
    } if (move_uploaded_file($_FILES["file"]["tmp_name"], MAIN_PATH . '/www/data/tmp/' . $filename)) { $uploadOk = true; if ($ajaxOk) { ?>
    parent.ajax("<?echo $ajaxOk;?>", "<?echo $ajaxAppId;?>", "<?echo $ajaxWinId;?>", "<?echo $ajaxData;?>", "<?echo $filename;?><?echo AJAX_SEPARATOR1;?><?echo basename($_FILES["file"]["name"]);?>");
    <? } } } } if (!$uploadOk) { if ($ajaxWinId) { ?>
    parent.shakeObj("<?echo $ajaxWinId;?>");
    <? } if ($ajaxErr) { ?>
    parent.ajax("<?echo $ajaxErr;?>", "<?echo $ajaxAppId;?>", "<?echo $ajaxWinId;?>", "<?echo $ajaxData;?>", "");
    <? } } ?>
    parent.hideBusyWindow();
    <? } } else { ?>
    parent.jsLogout();
    <? } ?>
</script>
<? sql_disconnect(); ?>
