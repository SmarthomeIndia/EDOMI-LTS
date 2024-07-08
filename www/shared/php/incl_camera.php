<?
/*
*/
?><? ?><? function getLiveCamImgPreview($camUrl, $camTyp)
{
    if (!isEmpty($camUrl)) {
        if ($camTyp == 0) {
            $img = getLiveCamImgFromSnapshot($camUrl);
        } else {
            $img = getLiveCamImgFromStream($camUrl);
        }
        if ($img) {
            $f = fopen(MAIN_PATH . '/www/data/tmp/campreview.jpg', 'w');
            fwrite($f, $img);
            fclose($f);
            if (file_exists(MAIN_PATH . '/www/data/tmp/campreview.jpg')) {
                return 'campreview.jpg';
            }
        }
    }
    return false;
}

function getLiveCamImg($camId, $returnTyp, $url = null, $urlTyp = null)
{
    if ($camId > 0) {
        if (isEmpty($url)) {
            $n = sql_getValues('edomiLive.cam ', 'id,url,mjpeg', 'id=' . $camId);
            if ($n !== false) {
                $url = $n['url'];
                $urlTyp = $n['mjpeg'];
            }
        }
        if (!isEmpty($url)) {
            if (global_camLiveMaxRefresh > 0) {
                $ts = getMicrotimeInt();
                $tmp = sql_getValue('edomiLive.cam', 'cachets', 'id=' . $camId);
                if (isEmpty($tmp) || ($tmp + (global_camLiveMaxRefresh * 1000000)) < $ts) {
                    sql_call("UPDATE edomiLive.cam SET cachets=" . $ts . " WHERE id=" . $camId);
                    if ($urlTyp == 0) {
                        $img = getLiveCamImgFromSnapshot($url);
                    } else {
                        $img = getLiveCamImgFromStream($url);
                    }
                    if ($img) {
                        $f = fopen(MAIN_PATH . '/www/data/tmp/livecam-' . $camId . '-' . $ts . '.jpg', 'w');
                        fwrite($f, $img);
                        fclose($f);
                        rename(MAIN_PATH . '/www/data/tmp/livecam-' . $camId . '-' . $ts . '.jpg', MAIN_PATH . '/www/data/liveproject/cam/live/cam' . $camId . '.jpg');
                        if ($returnTyp == 0) {
                            return $img;
                        } else if (file_exists(MAIN_PATH . '/www/data/liveproject/cam/live/cam' . $camId . '.jpg')) {
                            return 'cam' . $camId . '.jpg';
                        }
                    }
                } else if (file_exists(MAIN_PATH . '/www/data/liveproject/cam/live/cam' . $camId . '.jpg')) {
                    if ($returnTyp == 0) {
                        return file_get_contents(MAIN_PATH . '/www/data/liveproject/cam/live/cam' . $camId . '.jpg');
                    } else {
                        return 'cam' . $camId . '.jpg';
                    }
                }
            } else {
                if ($urlTyp == 0) {
                    $img = getLiveCamImgFromSnapshot($url);
                } else {
                    $img = getLiveCamImgFromStream($url);
                }
                if ($img) {
                    if ($returnTyp == 0) {
                        return $img;
                    } else {
                        $ts = getMicrotimeInt();
                        $f = fopen(MAIN_PATH . '/www/data/tmp/livecam-' . $camId . '-' . $ts . '.jpg', 'w');
                        fwrite($f, $img);
                        fclose($f);
                        rename(MAIN_PATH . '/www/data/tmp/livecam-' . $camId . '-' . $ts . '.jpg', MAIN_PATH . '/www/data/liveproject/cam/live/cam' . $camId . '.jpg');
                        if (file_exists(MAIN_PATH . '/www/data/liveproject/cam/live/cam' . $camId . '.jpg')) {
                            return 'cam' . $camId . '.jpg';
                        }
                    }
                }
            }
        }
    }
    return false;
}

function getLiveCamImgFromSnapshot($url)
{
    $tmp = explode('***', $url, 5);
    if (strtoupper(trim($tmp[0])) == 'CURL') {
        if (strtoupper(trim($tmp[1])) == 'ANY') {
            $auth = CURLAUTH_ANY;
        } else if (strtoupper(trim($tmp[1])) == 'BASIC') {
            $auth = CURLAUTH_BASIC;
        } else if (strtoupper(trim($tmp[1])) == 'DIGEST') {
            $auth = CURLAUTH_DIGEST;
        }
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, trim($tmp[4]));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_USERPWD, trim($tmp[2]) . ':' . trim($tmp[3]));
        curl_setopt($curl, CURLOPT_HTTPAUTH, $auth);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        $r = curl_exec($curl);
        curl_close($curl);
        return $r;
    } else {
        $ctx = stream_context_create(array('http' => array('timeout' => 10)));
        return file_get_contents($url, false, $ctx);
    }
}

function getLiveCamImgFromStream($url)
{
    $timeout = 10;
    $img = false;
    $frameBoundary = false;
    $n = get_headers($url);
    if ($n !== false) {
        for ($t = 0; $t < count($n); $t++) {
            if (preg_match('/boundary=(.*)/i', $n[$t], $m)) {
                $frameBoundary = trim($m[1]);
                break;
            }
        }
    }
    if ($frameBoundary !== false) {
        $data = '';
        $ctx = stream_context_create(array('http' => array('timeout' => 10)));
        $f = fopen($url, 'r', false, $ctx);
        if ($f) {
            $t1 = microtime(true);
            do {
                $n = fgets($f);
            } while (strpos($n, $frameBoundary) === false && ((microtime(true) - $t1) < $timeout));
            if (strpos($n, $frameBoundary) !== false) {
                do {
                    $n = fgets($f);
                    if (strpos($n, $frameBoundary) === false) {
                        $data .= $n;
                    }
                } while (strpos($n, $frameBoundary) === false && ((microtime(true) - $t1) < $timeout));
                if (strpos($n, $frameBoundary) !== false) {
                    $x = strpos($data, "\r\n\r\n");
                    if ($x === false) {
                        $x = strpos($data, "\n\n");
                    }
                    if ($x === false) {
                        $x = strpos($data, "\r\r");
                    }
                    if ($x === false) {
                        $x = strpos($data, chr(0xff) . chr(0xd8));
                        if ($x !== false) {
                            $img = substr($data, $x);
                        }
                    } else {
                        $img = substr($data, $x + 4);
                    }
                }
            }
            fclose($f);
        }
    }
    return $img;
} ?>
