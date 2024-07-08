<?
/*
*/
?><? set_time_limit(0);
function php_errorHandler($errCode, $errText, $errFile, $errRow)
{
    $log = true;
    if (strpos($errFile, '/incl_camera.php') !== false) {
        $fh = fopen(MAIN_PATH . '/www/data/tmp/camerror.txt', 'w');
        fwrite($fh, date('d.m.Y H:i:s'));
        fclose($fh);
        if (global_logLevelCam == 1) {
            $log = false;
            $n = date('d.m.Y');
            $tmp = readInfoFile(MAIN_PATH . '/www/data/tmp/camerror.txt');
            if ($tmp === false || $tmp[0] != $n) {
                createInfoFile(MAIN_PATH . '/www/data/tmp/camerror.txt', array($n));
                writeToLog(-1, false, 'Kamerafehler (Logging erfolgt nur max. 1x täglich) / Datei: ' . $errFile . ' | Fehlercode: ' . $errCode . ' | Zeile: ' . $errRow . ' | ' . $errText);
            }
        } else if (global_logLevelCam == 0) {
            $log = false;
        }
    }
    if ($log) {
        writeToLog(-1, false, 'Datei: ' . $errFile . ' | Fehlercode: ' . $errCode . ' | Zeile: ' . $errRow . ' | ' . $errText);
    }
    return true;
}

function php_exceptionHandler($exception)
{
    writeToLog(-1, false, 'Datei: ' . $exception->getFile() . ' | Fehlercode: ' . $exception->getCode() . ' | Zeile: ' . $exception->getLine() . ' | ' . $exception->getMessage(), 'EXCEPTION');
    if (preg_match('/liveproject\/lbs\/LBS(.*?).php/s', $exception->getFile(), $tmp) > 0) {
        createInfoFile(MAIN_PATH . '/www/data/tmp/lbserror.txt', array($tmp[1], '1'));
    }
}

function php_fatalerrorHandler()
{
    $error = error_get_last();
    if ($error['type'] === E_ERROR) {
        writeToLog(-1, false, 'Datei: ' . $error['file'] . ' | Fehlercode: ' . $error['type'] . ' | Zeile: ' . $error['line'] . ' | ' . $error['message'], 'FATALERROR');
        if (preg_match('/liveproject\/lbs\/LBS(.*?).php/s', $error['file'], $tmp) > 0) {
            createInfoFile(MAIN_PATH . '/www/data/tmp/lbserror.txt', array($tmp[1], '1'));
        }
    }
}

set_error_handler('php_errorHandler');
set_exception_handler('php_exceptionHandler');
register_shutdown_function('php_fatalerrorHandler'); ?>
