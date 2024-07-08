<?
/*
*/
?><? require(dirname(__FILE__) . "/../www/shared/php/config.php");
require(MAIN_PATH . "/www/shared/php/base.php");
sql_connect();
$ok = false;
if ($argv[1] == 'reboot') {
    if (getEdomiStatus() > 0) {
        setSysInfo(2, 13);
        echo "OK;REBOOT\n";
        $ok = true;
    }
} else if ($argv[1] == 'shutdown') {
    if (getEdomiStatus() > 0) {
        setSysInfo(2, 23);
        echo "OK;SHUTDOWN\n";
        $ok = true;
    }
} else if ($argv[1] == 'activate') {
    if (getEdomiStatus() > 0) {
        setSysInfo(2, 14);
        echo "OK;ACTIVATE\n";
        $ok = true;
    }
} else if ($argv[1] == 'start') {
    if (getEdomiStatus() == 1) {
        setSysInfo(2, 11);
        echo "OK;START\n";
        $ok = true;
    }
} else if ($argv[1] == 'pause') {
    if (getEdomiStatus() >= 2) {
        setSysInfo(2, 10);
        createInfoFile(MAIN_PATH . '/www/data/tmp/restartadmin.txt', array('10'));
        echo "OK;PAUSE\n";
        $ok = true;
    }
} else if ($argv[1] == 'restart') {
    if (getEdomiStatus() >= 1) {
        setSysInfo(2, 12);
        createInfoFile(MAIN_PATH . '/www/data/tmp/restartadmin.txt', array('12'));
        echo "OK;RESTART\n";
        $ok = true;
    }
} else if ($argv[1] == 'stop' || $argv[1] == 'quit') {
    if (getEdomiStatus() > 0) {
        setSysInfo(2, 22);
        echo "OK;STOP/QUIT;WAITING...\n";
        $ok = true;
        $t = getMicrotime();
        while (getSysInfo(1) != 0) {
            usleep(1000);
            if (getMicrotime() >= ($t + 30)) {
                $t = -1;
                break;
            }
        }
        if ($t == -1) {
            echo "ERROR;STOP/QUIT;TIMEOUT\n";
        } else {
            echo "OK;STOP/QUIT;READY\n";
        }
    }
}
sql_disconnect();
if (!$ok) {
    echo "ERROR\n";
} ?>
