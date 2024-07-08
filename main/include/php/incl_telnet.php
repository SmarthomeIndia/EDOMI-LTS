<?
/*
*/
?><? function telnetOpen($ip, $port, $timeout)
{
    $con = fsockopen($ip, $port, $x, $xx, $timeout);
    if ($con) {
        stream_set_blocking($con, 0);
        return $con;
    } else {
        return false;
    }
}

function telnetClose($con)
{
    return fclose($con);
}

function telnetSend($con, $n)
{
    return fwrite($con, $n . "\r\n");
}

function telnetWait($con, $waitFor, $readBytes, $timeout)
{
    $ok = false;
    $n = '';
    $t0 = microtime(true);
    while (!feof($con)) {
        $n .= fread($con, $readBytes);
        if (strpos(strtoupper($n), strtoupper($waitFor)) !== false) {
            $ok = true;
            break;
        }
        if ((microtime(true) - $t0) > $timeout) {
            break;
        }
        usleep(100 * 1000);
    }
    return $ok;
}

function telnetWait2($con, $waitFor, $readBytes, $timeout)
{
    $ok = false;
    $n = '';
    $t0 = microtime(true);
    while (!feof($con)) {
        $n .= fread($con, $readBytes);
        if (strpos(strtoupper($n), strtoupper($waitFor)) !== false) {
            $ok = $n;
            break;
        }
        if ((microtime(true) - $t0) > $timeout) {
            break;
        }
        usleep(100 * 1000);
    }
    return $ok;
} ?>
