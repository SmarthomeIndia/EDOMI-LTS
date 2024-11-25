<?
require(dirname(__FILE__) . "/../../../www/shared/php/config.php");
require(MAIN_PATH . "/www/shared/php/base.php");
require(MAIN_PATH . "/www/shared/php/incl_http.php");
require(MAIN_PATH . "/main/include/php/config.php");
require(MAIN_PATH . "/main/include/php/base.php");
require(MAIN_PATH . "/main/include/php/incl_log.php");
require(MAIN_PATH . "/main/include/php/incl_ga.php");
require(MAIN_PATH . "/main/include/php/incl_cmd.php");
require(MAIN_PATH . "/main/include/php/incl_logic.php");
set_time_limit(30);
$id = $argv[1];
$logic_globalStartExec = true;
logicMonitor_init(false);
register_shutdown_function('mainLogicExec_onExit', $id);
mainLogicExec_onStart($id);
?>