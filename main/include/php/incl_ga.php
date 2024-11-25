<?
function writeGA($gaid, $value, $log = true, $queueprio = -1)
{
    if ($gaData = getGADataFromID($gaid, 0)) {
        $setValue = verifyGaValue($value, $gaData['valuetyp'], $gaData['vmin'], $gaData['vmax'], $gaData['vstep'], $gaData['vlist'], $gaData['vcsv']);
        if ($setValue !== false) {
            if (global_knxGatewayActive && $gaData['gatyp'] == 1) {
                if ($gaData['requestable'] == 1 || $gaData['requestable'] == 2 || $gaData['requestable'] == 3) {
                    $mode = 1;
                } else {
                    $mode = 2;
                }
                sql_call("INSERT INTO edomiLive.RAMknxWrite (prio,mode,gaid,value) VALUES ('" . (($queueprio == 1 || ($queueprio < 0 && $gaData['prio'] == 1)) ? 1 : 0) . "'," . $mode . "," . $gaid . ",'" . sql_encodeValue($setValue) . "')");
                return true;
            }
            if ($gaData['gatyp'] == 2) {
                sql_call("INSERT INTO edomiLive.RAMknxRead (mode,gatyp,gaid,value,remanent) VALUES (2,2," . $gaid . ",'" . sql_encodeValue($setValue) . "'," . $gaData['remanent'] . ")");
                if ($log) {
                    writeToMonLog(1, 2, 2, $gaid, '', '', $gaData['name'], $setValue);
                }
                return true;
            }
        } else {
            sql_call("UPDATE edomiLive.RAMko SET visuts='" . getTimestampVisu() . "' WHERE (id=" . $gaid . ")");
            if ($log) {
                writeToMonLog(1, -1, $gaData['gatyp'], $gaid, '', $gaData['ga'], $gaData['name'], $value);
            }
        }
    }
    return false;
}

function requestGA($gaid, $queueprio = -1)
{
    if ($gaData = getGADataFromID($gaid, 0)) {
        if (global_knxGatewayActive && $gaData['gatyp'] == 1) {
            sql_call("INSERT INTO edomiLive.RAMknxWrite (prio,mode,gaid) VALUES ('" . (($queueprio == 1 || ($queueprio < 0 && $gaData['prio'] == 1)) ? 1 : 0) . "',0," . $gaid . ")");
        }
    }
}

function getGADataFromID($gaid, $gatyp, $cols = '*')
{
    if ($gatyp > 0) {
        $ss1 = sql_call("SELECT " . $cols . " FROM edomiLive.RAMko WHERE (id=" . $gaid . " AND gatyp=" . $gatyp . ")");
    } else {
        $ss1 = sql_call("SELECT " . $cols . " FROM edomiLive.RAMko WHERE (id=" . $gaid . ")");
    }
    if ($ss1 === false) {
        return false;
    }
    if ($n = sql_result($ss1)) {
        sql_close($ss1);
        return $n;
    } else {
        sql_close($ss1);
        return false;
    }
}

function getGADataFromGA($ga, $gatyp, $cols = '*')
{
    if ($gatyp > 0) {
        $ss1 = sql_call("SELECT " . $cols . " FROM edomiLive.RAMko WHERE (ga='" . $ga . "' AND gatyp=" . $gatyp . ")");
    } else {
        $ss1 = sql_call("SELECT " . $cols . " FROM edomiLive.RAMko WHERE (ga='" . $ga . "')");
    }
    if ($ss1 === false) {
        return false;
    }
    if ($n = sql_result($ss1)) {
        sql_close($ss1);
        return $n;
    } else {
        sql_close($ss1);
        return false;
    }
}

function verifyGaValue($value, $valueTyp, $min, $max, $step, $dec, $csv)
{
    if ($valueTyp == 0) {
        if (is_numeric($value)) {
            return formatGaValue($value, $valueTyp, $min, $max, $step, $dec, $csv);
        } else {
            if (is_numeric($min) || is_numeric($max) || is_numeric($step) || is_numeric($dec)) {
                return false;
            }
            return $value;
        }
    }
    if ($valueTyp == 1) {
        if (!is_numeric($value)) {
            return false;
        }
        if ($value != 0) {
            $value = 1;
        } else {
            $value = 0;
        }
        return truncFloat($value);
    }
    if ($valueTyp == 2) {
        if (!is_numeric($value)) {
            return false;
        }
        if ($value < 0) {
            $value = 0;
        }
        if ($value > 3) {
            $value = 3;
        }
        return truncFloat(abs($value));
    }
    if ($valueTyp == 3) {
        if (!is_numeric($value)) {
            return false;
        }
        if ($value < 0) {
            $value = 0;
        }
        if ($value > 15) {
            $value = 15;
        }
        return truncFloat(abs($value));
    }
    if ($valueTyp == 4) {
        if (strlen($value) != 1) {
            return false;
        }
        if (ord($value) < 0 || ord($value) > 255) {
            return false;
        }
        return chr(ord($value) & 0xFF);
    }
    if ($valueTyp == 5) {
        if (!is_numeric($value)) {
            return false;
        }
        $value = formatGaValue($value, $valueTyp, $min, $max, $step, 0, $csv);
        return truncFloat(abs($value));
    }
    if ($valueTyp == 6) {
        if (!is_numeric($value)) {
            return false;
        }
        $value = formatGaValue($value, $valueTyp, $min, $max, $step, 0, $csv);
        return truncFloat($value);
    }
    if ($valueTyp == 7) {
        if (!is_numeric($value)) {
            return false;
        }
        $value = formatGaValue($value, $valueTyp, $min, $max, $step, 0, $csv);
        return truncFloat(abs($value));
    }
    if ($valueTyp == 8) {
        if (!is_numeric($value)) {
            return false;
        }
        $value = formatGaValue($value, $valueTyp, $min, $max, $step, 0, $csv);
        return truncFloat($value);
    }
    if ($valueTyp == 9) {
        if (!is_numeric($value)) {
            return false;
        }
        $value = formatGaValue($value, $valueTyp, $min, $max, $step, $dec, $csv);
        return round($value, 2);
    }
    if ($valueTyp == 10) {
        if (strlen($value) != 10) {
            return false;
        }
        if (strtotime(substr($value, 2, 8)) === false) {
            return false;
        }
        if (substr($value, 1, 1) != '.') {
            return false;
        }
        if (substr($value, 4, 1) != ':') {
            return false;
        }
        if (substr($value, 7, 1) != ':') {
            return false;
        }
        return $value;
    }
    if ($valueTyp == 11) {
        if (strlen($value) != 10) {
            return false;
        }
        if (strtotime($value) === false) {
            return false;
        }
        if (substr($value, 4, 1) != '-') {
            return false;
        }
        if (substr($value, 7, 1) != '-') {
            return false;
        }
        return $value;
    }
    if ($valueTyp == 12) {
        if (!is_numeric($value)) {
            return false;
        }
        $value = formatGaValue($value, $valueTyp, $min, $max, $step, 0, $csv);
        return truncFloat($value);
    }
    if ($valueTyp == 13) {
        if (!is_numeric($value)) {
            return false;
        }
        $value = formatGaValue($value, $valueTyp, $min, $max, $step, 0, $csv);
        return truncFloat($value);
    }
    if ($valueTyp == 14) {
        if (!is_numeric($value)) {
            return false;
        }
        $value = formatGaValue($value, $valueTyp, $min, $max, $step, $dec, $csv);
        return floatval($value);
    }
    if ($valueTyp == 16) {
        return $value;
    }
    if ($valueTyp == 232) {
        $value = sprintf('%06s', dechex(hexdec($value)));
        return $value;
    }
    if ($valueTyp == 99999) {
        if (isEmpty($value)) {
            return false;
        }
        return $value;
    }
    return false;
}

function formatGaValue($value, $valueTyp, $min, $max, $step, $dec, $csv)
{
    global $global_dptData;
    if (is_numeric($step) && $step > 0) {
        $value = truncFloat($value / $step) * $step;
    }
    if (!isEmpty($csv)) {
        $value = formatGaValueFromCsv($value, $csv, 0);
    }
    if (is_numeric($dec) && $dec >= 0) {
        $value = round($value, $dec);
    }
    if (is_numeric($min) && $value < $min) {
        $value = $min;
    }
    if (is_numeric($max) && $value > $max) {
        $value = $max;
    }
    if (is_numeric($global_dptData[$valueTyp][0]) && $value < $global_dptData[$valueTyp][0]) {
        $value = $global_dptData[$valueTyp][0];
    }
    if (is_numeric($global_dptData[$valueTyp][1]) && $value > $global_dptData[$valueTyp][1]) {
        $value = $global_dptData[$valueTyp][1];
    }
    return $value;
}

function formatGaValueFromCsv($value, $csv, $step = 0)
{
    $r = $value;
    if (is_numeric($r)) {
        $max = null;
        $matchid = false;
        $csvarr = array();
        $tmp = explode(',', $csv);
        for ($t = 0; $t < count($tmp); $t++) {
            if (is_numeric(trim($tmp[$t]))) {
                array_push($csvarr, trim($tmp[$t]));
            }
        }
        for ($t = 0; $t < count($csvarr); $t++) {
            if (is_null($max) || abs($value - $csvarr[$t]) < $max) {
                $max = abs($value - $csvarr[$t]);
                $matchid = $t;
                $r = $csvarr[$t];
            }
        }
        if ($matchid !== false) {
            if ($step != 0) {
                $matchid += intval($step);
                if ($matchid < 0) {
                    $matchid = 0;
                }
                if ($matchid > (count($csvarr) - 1)) {
                    $matchid = count($csvarr) - 1;
                }
                $r = $csvarr[$matchid];
            }
        }
    }
    return $r;
}

function parseGAValues($n)
{
    if (preg_match_all('/\{(.*?)\}/s', $n, $ko) > 0) {
        for ($t = 0; $t < count($ko[0]); $t++) {
            if (is_numeric($ko[1][$t])) {
                if ($nn = getGADataFromID($ko[1][$t], 0)) {
                    $n = str_replace($ko[0][$t], $nn['value'], $n);
                }
            } else {
                if ($nn = getGADataFromGA($ko[1][$t], 0)) {
                    $n = str_replace($ko[0][$t], $nn['value'], $n);
                }
            }
        }
    }
    return $n;
} 
?>
