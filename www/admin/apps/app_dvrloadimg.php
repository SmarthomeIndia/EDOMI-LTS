<?
/*
*/
?><? ?><? require("../../shared/php/config.php");
require(MAIN_PATH . "/www/shared/php/base.php");
require(MAIN_PATH . "/www/shared/php/incl_http.php");
require(MAIN_PATH . "/www/admin/include/php/config.php");
require(MAIN_PATH . "/www/admin/include/php/base.php");
$fname = httpGetVar('n1');
$pos = httpGetVar('n2');
$len = httpGetVar('n3');
if (!isEmpty($fname) && $pos >= 0 && $len > 0) {
    $f1 = fopen(global_dvrPath . '/cam-' . $fname . '-2.edomidvr', 'rb');
    if ($f1) {
        fseek($f1, $pos);
        $img = fread($f1, $len);
        header('Content-Type:image/jpeg');
        header('Content-Length:' . $len);
        echo $img;
    }
    fclose($f1);
} ?>
