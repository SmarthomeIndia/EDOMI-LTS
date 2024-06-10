<?
/* 
*/ 
?><? function launchProcess($process) { $r=trim(shell_exec($process.' > /dev/null 2>&1 & echo $!')); if (is_numeric($r)) { return intval($r); } else { return false; } } function checkProcess($pid) { if (is_numeric($pid)) { return file_exists('/proc/'.$pid); } else { return false; } } function terminateProcess($pid) { if (checkProcess($pid)) { exec('kill -2 '.$pid.' > /dev/null 2>&1',$n,$err); $t=0; while (checkProcess($pid) && $t<(10*10)) { usleep(100*1000); $t++; } } return (!checkProcess($pid)); } function countProcesses($name) { $n=explode(' ',trim(shell_exec('pidof '.$name))); return count($n); } ?>
