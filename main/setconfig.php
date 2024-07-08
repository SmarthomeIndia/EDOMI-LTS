<?
/*
*/
?><? $ipAuto = "'127.0.0.1'";
$ips = exec('hostname -I');
$ip = explode(' ', $ips);
if (isset($ip[0])) {
    $ipAuto = "'" . $ip[0] . "'";
}
$ipServer = $ipAuto;
$fnIni = $argv[1];
$fnCnf = $argv[2];
if (file_exists($fnIni) && file_exists($fnCnf)) {
    $ini = file($fnIni);
    $config = file($fnCnf);
    for ($t = 0; $t < count($ini); $t++) {
        if (trim($ini[$t]) != '' && substr(trim($ini[$t]), 0, 1) != '#') {
            $var = explode('=', trim($ini[$t]), 2);
            if (count($var) == 2) {
                $var[0] = trim($var[0]);
                $var[1] = trim($var[1]);
                $config = findrreplace($config, "define('" . $var[0] . "'", "define('" . trim($var[0]) . "'," . trim($var[1]) . ");");
                if ($var[0] == 'set_timezone') {
                    $config = findrreplace($config, "date_default_timezone_set(", "date_default_timezone_set(" . $var[1] . ");");
                }
                if ($var[0] == 'global_serverIP') {
                    if ($var[1] == "''" || strtoupper($var[1]) == "'AUTO'") {
                        $config = findrreplace($config, "define('" . $var[0] . "'", "define('" . trim($var[0]) . "'," . trim($ipAuto) . ");");
                        $ipServer = $ipAuto;
                    } else {
                        $ipServer = $var[1];
                    }
                }
                if ($var[0] == 'global_visuIP') {
                    if ($var[1] == "''") {
                        $config = findrreplace($config, "define('" . $var[0] . "'", "define('" . trim($var[0]) . "'," . trim($ipServer) . ");");
                    } else if (strtoupper($var[1]) == "'AUTO'") {
                        $config = findrreplace($config, "define('" . $var[0] . "'", "define('" . trim($var[0]) . "'," . trim($ipAuto) . ");");
                    }
                }
                if ($var[0] == 'global_knxIP') {
                    if ($var[1] == "''") {
                        $config = findrreplace($config, "define('" . $var[0] . "'", "define('" . trim($var[0]) . "'," . trim($ipServer) . ");");
                    } else if (strtoupper($var[1]) == "'AUTO'") {
                        $config = findrreplace($config, "define('" . $var[0] . "'", "define('" . trim($var[0]) . "'," . trim($ipAuto) . ");");
                    }
                }
            }
        }
    }
    $tmp = implode('', $config);
    file_put_contents($fnCnf, $tmp);
    echo $fnIni . " eingelesen\n";
}
function findrreplace($config, $search, $replace)
{
    for ($t = 0; $t < count($config); $t++) {
        if (strpos($config[$t], $search) !== false) {
            $config[$t] = $replace . "\n";
            break;
        }
    }
    return $config;
} ?>

