<?
/*
*/
?><? require(dirname(__FILE__) . "/../www/shared/php/config.php");
require(MAIN_PATH . "/www/shared/php/base.php");
require(MAIN_PATH . "/www/shared/php/incl_dbinit.php");
require(MAIN_PATH . "/www/shared/php/incl_camera.php");
require(MAIN_PATH . "/main/include/php/config.php");
require(MAIN_PATH . "/main/include/php/base.php");
require(MAIN_PATH . "/main/include/php/incl_log.php");
require(MAIN_PATH . "/main/include/php/incl_ga.php");
require(MAIN_PATH . "/main/include/php/incl_cmd.php");
require(MAIN_PATH . "/main/include/php/incl_process.php");
require(MAIN_PATH . "/www/admin/include/php/base.php");
require(MAIN_PATH . "/www/admin/include/php/incl_items.php");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

sql_connect();
exit();

sql_connect();

$exchange = new class_exchange();
if (1 == 1) {
    $exchange->exportType = true;
    $exchange->exportAddItem('editVisu', 6);
    $exchange->exportStart();
    $exchange->exportSave('test');
} else {
    if ($exchange->importLoad('test.json')) {
        $exchange->import_items();
    } else {
        echo "Fehler in der JSON-Datei\n";
    }
    $exchange->importCleanup();
}

$exchange = null;

class class_exchange
{
    private $exportFolders = array();
    private $exportItems = array();
    private $sysFolders = array();
    private $exportTree = array();
    private $dbMeta = array(
        'editRoot' => array('folders', 1000),
        'editLogicPage' => array('logicPages', 1),
        'editLogicElement' => array('logicItems', 1),
        'editLogicElementDef' => array('logicUnits', 19000000),
        'editVisu' => array('visus', 1),
        'editVisuPage' => array('visuPages', 1),
        'editVisuElement' => array('visuItems', 1),
        'editVisuUser' => array('visuAccounts', 1),
        'editVisuElementDef' => array('visuDesignTemplates', 1),
        'editVisuFGcol' => array('visuFgColors', 1),
        'editVisuBGcol' => array('visuBgColors', 1),
        'editVisuImg' => array('visuImages', 1),
        'editVisuFont' => array('visuFonts', 1),
        'editVisuSnd' => array('visuSounds', 1),
        'editVisuAnim' => array('visuAnimations', 1),
        'editKo' => array('comObjects', 100),
        'editTimer' => array('timers', 1),
        'editAgenda' => array('agendas', 1),
        'editScene' => array('scenes', 1),
        'editSequence' => array('sequences', 1),
        'editCam' => array('cameras', 1),
        'editAws' => array('presenceSimulations', 1),
        'editIp' => array('ipCommands', 1),
        'editEmail' => array('emails', 10),
        'editIr' => array('irCommands', 1),
        'editPhoneBook' => array('phoneNumbers', 1),
        'editPhoneCall' => array('phoneCalls', 1),
        'editChart' => array('diagrams', 1),
        'editHttpKo' => array('remoteAccesses', 1),
        'editArchivKo' => array('dataArchives', 1),
        'editArchivMsg' => array('messageArchives', 1),
        'editArchivCam' => array('cameraArchives', 1),
        'editArchivPhone' => array('phoneArchives', 1)
    );
    private $visuelementTypes = array(
        '1' => array('universal', 'clickMode', 'clickIndicator'),
        '2' => array('image', 'size', 'repeat', 'width', 'height'),
        '10' => array('keyboard', 'type', 'preselect', '', 'behavior', 'min', 'max', 'step', 'digits'),
        '16' => array('notes', 'type', 'lineWidth', 'optimize', '', '', '', '', '', '', 'headerSize'),
        '11' => array('wheel', 'type', '', '', 'behavior', 'min', 'max', 'step', 'digits'),
        '13' => array('slider', 'type', 'size', 'direction', 'behavior', 'min', 'max', 'step', 'digits'),
        '12' => array('dimmer', 'type', 'brightnessIndicator', 'appearance', 'behavior'),
        '15' => array('colorPicker', 'mode', 'clickIndicator'),
        '24' => array('code', 'code', 'attempts', 'type'),
        '14' => array('table', 'rowSeparator', 'columnSeparator', 'sort', 'rowHeight', 'padding', 'grid', 'rowTitle', 'scrolling', '', 'headerSize'),
        '27' => array('gauge', 'type', 'pointerWidth', 'min', 'max', 'scaleLength', 'rangeType', 'scaleOpacity', 'rangeOpacity', 'angleFrom', 'angleTo'),
        '17' => array('clock', 'type', 'pointerWidth', 'scaleType', 'scaleWidth', 'scaleLength', 'scaleOpacity', '24hOpacity', 'pointerMinuteLength', 'pointerHourLength'),
        '21' => array('diagram', 'diagram_id', 'refresh', 'titleCaption', 'xaxisCaption', 'xaxisTicks', 'captionsOpacity'),
        '29' => array('url', 'websiteBackground', 'type', 'refresh', 'imageRatio'),
        '30' => array('urlSound', 'repeat'),
        '31' => array('speak', 'language', 'speed', 'pitch'),
        '22' => array('timer', 'timer_id', 'valueType', 'controls', '', '', '', '', '', '', 'headerSize'),
        '32' => array('agenda', 'agenda_id', 'valueType', 'controls', '', 'showExpired', 'showDate', 'rangeFrom', 'rangeTo', 'refresh', 'headerSize'),
        '23' => array('presenceSimulation', 'presenceSimulation_id', '', 'controls', '', '', '', '', '', '', 'headerSize'),
        '26' => array('dataArchive', 'dataArchive_id', 'load', '', 'timestamp', '', '', '', '', '', 'headerSize'),
        '25' => array('messageArchive', 'messageArchive_id', 'load', 'type', 'timestamp', '', '', '', '', '', 'headerSize'),
        '20' => array('camera', 'camera_id', 'refresh', 'cameraArchive_id', 'default', '', '', '', '', '', 'headerSize'),
        '28' => array('phoneArchive', 'phoneArchive_id', 'type', 'load', 'timestamp', '', '', '', '', '', 'headerSize'),
        '255' => array('group')
    );
    private $commandTypes = array('1' => array('comObject_setInput', 'comObject_id'),
        '13' => array('dataArchive_addInput', 'dataArchive_id', 'timestamp'),
        '14' => array('messageArchive_addInput', 'messageArchive_id', 'timestamp'),
        '2' => array('comObject_setValue', 'comObject_id', 'value'),
        '7' => array('comObject_addValue', 'comObject_id', 'value'),
        '3' => array('comObject_setComObjectValue', 'comObject_id', 'comObjectSource_id'),
        '4' => array('comObject_toggle', 'comObject_id', 'value'),
        '6' => array('comObject_toggleWithStatus', 'comObject_id', 'comObjectStatus_id'),
        '5' => array('comObject_step', 'comObject_id', 'direction'),
        '9' => array('comObject_list', 'comObject_id', 'direction'),
        '8' => array('comObject_request', 'comObject_id'),
        '10' => array('scene', 'scene_id', 'mode'),
        '11' => array('sequence', 'sequence_id', 'mode'),
        '40' => array('dataArchive_addValue', 'dataArchive_id', 'value'),
        '41' => array('messageArchive_addValue', 'messageArchive_id', 'value'),
        '12' => array('cameraArchive_addImage', 'cameraArchive_id'),
        '50' => array('dataArchive_delete', 'dataArchive_id', 'mode'),
        '51' => array('messageArchive_delete', 'messageArchive_id', 'mode'),
        '52' => array('cameraArchive_delete', 'cameraArchive_id', 'mode'),
        '53' => array('phoneArchive_delete', 'phoneArchive_id', 'mode'),
        '15' => array('ipCommand', 'ipCommand_id'),
        '16' => array('irCommand', 'irCommand_id', 'channel'),
        '20' => array('email', 'email_id'),
        '22' => array('phoneNumber', 'phoneNumber_id', 'duration'),
        '21' => array('visu_openPage', 'page_id', 'account_id'),
        '28' => array('visu_closePopups', 'visu_id', 'account_id'),
        '24' => array('visu_playSound', 'visu_id', 'sound_id'),
        '25' => array('visuAccount_playSound', 'account_id', 'sound_id'),
        '26' => array('visu_speak', 'visu_id', 'value'),
        '27' => array('visuAccount_speak', 'account_id', 'value'),
        '23' => array('visu_logout', 'visu_id', 'account_id'),
        '30' => array('edomi_control', 'command')
    );

    public function __construct()
    {
        $this->sysFolders = array(
            '/10/11/' => $this->dbMeta['editLogicPage'][0],
            '/20/21/' => $this->dbMeta['editVisu'][0],
            '/20/22/' => $this->dbMeta['editVisuPage'][0],
            '/20/23/' => $this->dbMeta['editVisuUser'][0],
            '/20/24/' => $this->dbMeta['editVisuElementDef'][0],
            '/20/25/' => $this->dbMeta['editVisuBGcol'][0],
            '/20/26/' => $this->dbMeta['editVisuFGcol'][0],
            '/20/27/' => $this->dbMeta['editVisuAnim'][0],
            '/20/28/' => $this->dbMeta['editVisuImg'][0],
            '/20/29/' => $this->dbMeta['editVisuSnd'][0],
            '/20/150/' => $this->dbMeta['editVisuFont'][0],
            '/30/31/' => $this->dbMeta['editKo'][0] . '_internal',
            '/30/32/' => $this->dbMeta['editKo'][0] . '_knx',
            '/30/33/' => $this->dbMeta['editKo'][0] . '_system',
            '/40/' => $this->dbMeta['editScene'][0],
            '/50/' => $this->dbMeta['editArchivKo'][0],
            '/60/' => $this->dbMeta['editArchivMsg'][0],
            '/70/' => $this->dbMeta['editIp'][0],
            '/75/' => $this->dbMeta['editIr'][0],
            '/80/81/' => $this->dbMeta['editCam'][0],
            '/80/82/' => $this->dbMeta['editArchivCam'][0],
            '/90/' => $this->dbMeta['editSequence'][0],
            '/100/' => $this->dbMeta['editTimer'][0],
            '/101/' => $this->dbMeta['editAgenda'][0],
            '/110/' => $this->dbMeta['editAws'][0],
            '/120/' => $this->dbMeta['editEmail'][0],
            '/120/121/' => $this->dbMeta['editEmail'][0] . '_system',
            '/124/125/' => $this->dbMeta['editPhoneBook'][0],
            '/124/126/' => $this->dbMeta['editPhoneCall'][0],
            '/124/127/' => $this->dbMeta['editArchivPhone'][0],
            '/130/' => $this->dbMeta['editChart'][0],
            '/140/' => $this->dbMeta['editHttpKo'][0]
        );
        $this->exportTree = array(
            'version' => global_version,
            $this->dbMeta['editRoot'][0] => null,
            $this->dbMeta['editLogicPage'][0] => null,
            $this->dbMeta['editLogicElement'][0] => null,
            $this->dbMeta['editLogicElementDef'][0] => null,
            $this->dbMeta['editVisu'][0] => null,
            $this->dbMeta['editVisuPage'][0] => null,
            $this->dbMeta['editVisuElement'][0] => null,
            $this->dbMeta['editVisuUser'][0] => null,
            $this->dbMeta['editVisuElementDef'][0] => null,
            $this->dbMeta['editVisuBGcol'][0] => null,
            $this->dbMeta['editVisuFGcol'][0] => null,
            $this->dbMeta['editVisuAnim'][0] => null,
            $this->dbMeta['editVisuImg'][0] => null,
            $this->dbMeta['editVisuSnd'][0] => null,
            $this->dbMeta['editVisuFont'][0] => null,
            $this->dbMeta['editKo'][0] => null,
            $this->dbMeta['editScene'][0] => null,
            $this->dbMeta['editArchivKo'][0] => null,
            $this->dbMeta['editArchivMsg'][0] => null,
            $this->dbMeta['editIp'][0] => null,
            $this->dbMeta['editIr'][0] => null,
            $this->dbMeta['editCam'][0] => null,
            $this->dbMeta['editArchivCam'][0] => null,
            $this->dbMeta['editSequence'][0] => null,
            $this->dbMeta['editTimer'][0] => null,
            $this->dbMeta['editAgenda'][0] => null,
            $this->dbMeta['editAws'][0] => null,
            $this->dbMeta['editEmail'][0] => null,
            $this->dbMeta['editPhoneBook'][0] => null,
            $this->dbMeta['editPhoneCall'][0] => null,
            $this->dbMeta['editArchivPhone'][0] => null,
            $this->dbMeta['editChart'][0] => null,
            $this->dbMeta['editHttpKo'][0] => null
        );
    }

    public function importLoad($fileName)
    {
        exec('rm -rf ' . MAIN_PATH . '/www/data/tmp/import');
        exec('mkdir ' . MAIN_PATH . '/www/data/tmp/import');
        $fn = MAIN_PATH . '/www/data/tmp/' . $fileName;
        $suffix = strtoupper(pathinfo($fileName, PATHINFO_EXTENSION));

        if ($suffix == 'TAR' && file_exists($fn)) {
            exec('tar -xf "' . $fn . '" -C "' . MAIN_PATH . '/www/data/tmp/import"');
        } else if ($suffix == 'JSON' && file_exists($fn)) {
            exec('cp ' . $fn . ' ' . MAIN_PATH . '/www/data/tmp/import/data.json');
        } else {
            return false;
        }
        $fn_json = MAIN_PATH . '/www/data/tmp/import/data.json';

        if (file_exists($fn_json)) {
            $this->importData = $this->import_jsonDecode(file_get_contents($fn_json));
            if (is_array($this->importData)) {
                return true;
            }
        }

        return false;
    }

    public function importCleanup()
    {
        exec('rm -rf ' . MAIN_PATH . '/www/data/tmp/import');
    }

    public function import_items()
    {
        foreach ($this->importData as $k1 => $v1) {
            echo $k1 . "\n";
            foreach ($v1 as $k2 => $v2) {
                if ($k1 == '#folders') {
                    $this->import_add_folder($v2['id']);
                }
                if ($k1 == '#kos') {
                    $this->import_add_ko($v2['id']);
                }
                if ($k1 == '#logicElements') {
                    $this->import_add_logicLBS($v2['id']);
                }
                if ($k1 == '#dataArchives') {
                    $this->import_add_archive_data($v2['id']);
                }
                if ($k1 == '#sequences') {
                    $this->import_add_sequence($v2['id']);
                }
                if ($k1 == '#scenes') {
                    $this->import_add_scene($v2['id']);
                }
                if ($k1 == '#timers') {
                    $this->import_add_timer($v2['id']);
                }
                if ($k1 == '#agendas') {
                    $this->import_add_agenda($v2['id']);
                }
                if ($k1 == '#cameras') {
                    $this->import_add_camera($v2['id']);
                }
                if ($k1 == '#ipCommands') {
                    $this->import_add_ipCommand($v2['id']);
                }
                if ($k1 == '#irCommands') {
                    $this->import_add_irCommand($v2['id']);
                }
                if ($k1 == '#emails') {
                    $this->import_add_email($v2['id']);
                }
                if ($k1 == '#diagrams') {
                    $this->import_add_diagram($v2['id']);
                }
                if ($k1 == '#presenceSimulations') {
                    $this->import_add_presenceSimulation($v2['id']);
                }
                if ($k1 == '#remoteAccesses') {
                    $this->import_add_remoteAccess($v2['id']);
                }
                if ($k1 == '#phoneNumbers') {
                    $this->import_add_phoneNumber($v2['id']);
                }
                if ($k1 == '#phoneCallTriggers') {
                    $this->import_add_phoneCallTrigger($v2['id']);
                }
                if ($k1 == '#visus') {
                    $this->import_add_visu($v2['id']);
                }
                if ($k1 == '#visuAccounts') {
                    $this->import_add_visuAccount($v2['id']);
                }
                if ($k1 == '#logicPages') {
                    $this->import_add_logicPage($this->importData['#logicPages'], $v2['id']);
                }
            }
        }
        sort($this->import_errors);
        var_dump($this->import_errors);
    }

    public $exportType = false;

    public function exportAddFolder($id)
    {
        if ($id == 0) {
            $ss1 = sql_call("SELECT id FROM edomiProject.editRoot WHERE (id<1000 AND parentid=0) ORDER BY id ASC");
            while ($n = sql_result($ss1)) {
                $this->exportFolders[] = array($n['id']);
            }
            sql_close($ss1);
        } else {
            $this->exportFolders[] = array($id);
        }
    }

    public function exportAddItem($db, $id)
    {
        $this->exportItems[] = array($db, $id);
    }

    public function exportStart()
    {
        foreach ($this->exportFolders as $k => $v) {
            $this->export_add_folder($v[0]);
        }
        foreach ($this->exportItems as $k => $v) {
            $this->export_add_item($v[0], $v[1], true, true);
        }
    }

    public function exportSave($fileName)
    {
        foreach ($this->exportTree as $k => $v) {
            if (is_null($v)) {
                unset($this->exportTree[$k]);
            }
        }
        if (count($this->exportTree) > 0) {
            $jsonData = $this->export_jsonEncode($this->exportTree, true);
            $containsFiles = false;
            foreach ($this->exportTree as $k1 => $v1) {
                foreach ($v1 as $k2 => $v2) {
                    if (!isEmpty($v2['fileName'])) {
                        $containsFiles = true;
                        break;
                    }
                }
            }
            if ($containsFiles) {
                file_put_contents(MAIN_PATH . '/www/data/tmp/data.json', $jsonData);
                $fn = MAIN_PATH . '/www/data/tmp/' . $fileName . '.tar';
                exec('tar -cf "' . $fn . '" -C "' . MAIN_PATH . '/www/data/tmp/" "data.json"');
                foreach ($this->exportTree as $k1 => $v1) {
                    foreach ($v1 as $k2 => $v2) {
                        if (!isEmpty($v2['fileName'])) {
                            if ($k1 == $this->dbMeta['editLogicElementDef'][0]) {
                                exec('tar -rf "' . $fn . '" -C "' . MAIN_PATH . '/www/admin/lbs/" "' . $v2['fileName'] . '"');
                            }
                            if ($k1 == $this->dbMeta['editVisuImg'][0]) {
                                exec('tar -rf "' . $fn . '" -C "' . MAIN_PATH . '/www/data/project/visu/img/" "' . $v2['fileName'] . '"');
                            }
                            if ($k1 == $this->dbMeta['editVisuSnd'][0]) {
                                exec('tar -rf "' . $fn . '" -C "' . MAIN_PATH . '/www/data/project/visu/etc/" "' . $v2['fileName'] . '"');
                            }
                            if ($k1 == $this->dbMeta['editVisuFont'][0]) {
                                exec('tar -rf "' . $fn . '" -C "' . MAIN_PATH . '/www/data/project/visu/etc/" "' . $v2['fileName'] . '"');
                            }
                        }
                    }
                }
                deleteFiles(MAIN_PATH . '/www/data/tmp/data.json');
            } else {
                file_put_contents(MAIN_PATH . '/www/data/tmp/' . $fileName . '.json', $jsonData);
            }
            return true;
        }
        return false;
    }

    private function export_add_folder($id)
    {
        foreach ($this->dbMeta as $db => $void) {
            $ss1 = sql_call("SELECT id FROM edomiProject." . $db . " WHERE folderid=" . $id . " ORDER BY id ASC");
            while ($item = sql_result($ss1)) {
                $this->export_add_item($db, $item['id'], true, true);
            }
            sql_close($ss1);
        }
        $ss2 = sql_call("SELECT id,parentid FROM edomiProject.editRoot WHERE path LIKE '%/" . $id . "/%' ORDER BY id ASC");
        while ($folder = sql_result($ss2)) {
            $emptyFolder = true;
            foreach ($this->dbMeta as $db => $void) {
                $ss1 = sql_call("SELECT id FROM edomiProject." . $db . " WHERE folderid=" . $folder['id'] . " ORDER BY id ASC");
                while ($item = sql_result($ss1)) {
                    $emptyFolder = false;
                    $this->export_add_item($db, $item['id'], true, true);
                }
                sql_close($ss1);
            }
            if ($emptyFolder) {
                $this->export_add_item('editRoot', $folder['id'], true, true);
            }
        }

        sql_close($ss2);
    }

    private function export_add_item($db, $id, $returnSysId = true, $noLink, &$tree = false)
    {
        if ($tree === false) {
            $tree =& $this->exportTree[$this->dbMeta[$db][0]];
        }
        if ($id >= $this->dbMeta[$db][1]) {
            $r = $this->export_id_Exists($tree, $id);
            if (!$r) {
                if ($id > 0) {
                    $item = sql_getValues('edomiProject.' . $db, '*', 'id=' . $id);
                    if ($item !== false) {
                        $r = (($this->exportType) ? '+' : '') . $item['id'];
                        $fn = 'export_add_item_' . $db;
                        $this->$fn($tree, $r, $item, $this->export_is_inFolders($item['folderid']), $noLink);
                    }
                }
            } else if ($noLink) {
                echo $this->dbMeta[$db][0] . ' / ' . $id . ' / ' . $r . "\n";
                foreach ($tree as $k => &$v) {
                    if ($v['id'] == $r) {
                        $v['#noLink'] = '1';
                        break;
                    }
                }
            }
            return $r;
        } else if ($id > 0) {
            if ($returnSysId) {
                return $id;
            } else {
                return null;
            }
        }
    }

    private function export_add_item_editKo(&$tree, $id, $tmp, $addFolder = false, $noLink = false)
    {
        if ($tmp['gatyp'] == 1) {
            $tree[] = array(
                '#noLink' => (($noLink) ? '1' : '0'),
                'id' => $id, 'name' => $tmp['name'],
                'folder_id' => (($addFolder || !$this->exportType) ? $this->export_add_item('editRoot', $tmp['folderid'], false) : null),
                'type' => 'knx',
                'description' => $tmp['text'],
                'groupAddress' => $tmp['ga'],
                'dpt' => $tmp['valuetyp'],
                'defaultValue' => $tmp['defaultvalue'],
                'initScan' => (($tmp['initscan'] > 0) ? $tmp['initscan'] : null),
                'initSend' => (($tmp['initsend'] > 0) ? $tmp['initsend'] : null),
                'requestType' => $tmp['requestable'], 'min' => $tmp['vmin'],
                'max' => $tmp['vmax'],
                'step' => $tmp['vstep'],
                'digits' => $tmp['vlist'],
                'csvList' => $tmp['vcsv']);
        } else {
            $tree[] = array('#noLink' => (($noLink) ? '1' : '0'),
                'id' => $id, 'name' => $tmp['name'],
                'folder_id' => (($addFolder || !$this->exportType) ? $this->export_add_item('editRoot', $tmp['folderid'], false) : null),
                'type' => 'internal',
                'description' => $tmp['text'],
                'dpt' => $tmp['valuetyp'],
                'defaultValue' => $tmp['defaultvalue'],
                'remanent' => (($tmp['remanent'] > 0) ? $tmp['remanent'] : null),
                'min' => $tmp['vmin'],
                'max' => $tmp['vmax'],
                'step' => $tmp['vstep'],
                'digits' => $tmp['vlist'],
                'csvList' => $tmp['vcsv']);
        }
    }

    private function export_add_item_editLogicPage(&$tree, $id, $tmp, $addFolder = false, $noLink = false)
    {
        $tree[] = array(
            '#noLink' => (($noLink) ? '1' : '0'),
            'id' => $id,
            'name' => $tmp['name'],
            'folder_id' => (($addFolder || !$this->exportType) ? $this->export_add_item('editRoot', $tmp['folderid'], false) : null),
            'enabled' => $tmp['pagestatus'],
            'description' => $tmp['text'],
            'elements' => null
        );
        $treeThis =& $tree[count($tree) - 1];
        $ss1 = sql_call("SELECT id FROM edomiProject.editLogicElement WHERE pageid=" . $tmp['id'] . " ORDER BY id ASC");

        while ($n = sql_result($ss1)) {
            $this->export_add_item('editLogicElement', $n['id'], true, $noLink, $treeThis['elements']);
        }

        sql_close($ss1);
    }

    private function export_add_item_editLogicElement(&$tree, $id, $tmp, $addFolder = false, $noLink = false)
    {
        $tree[] = array('id' => (($this->exportType) ? '+' : '') . $tmp['id'],
            'logicUnit_id' => $this->export_add_item('editLogicElementDef', $tmp['functionid'], true, $noLink),
            'x' => $tmp['xpos'],
            'y' => $tmp['ypos'],
            'description' => $tmp['name'],
            'inputs' => null,
            'commands' => null
        );
        $treeThis =& $tree[count($tree) - 1];
        $ss1 = sql_call("SELECT * FROM edomiProject.editLogicLink WHERE elementid=" . $tmp['id'] . " ORDER BY eingang ASC");
        while ($n = sql_result($ss1)) {
            if ($n['linktyp'] == 0) {
                $treeThis['inputs'][] = array(
                    'e' . $n['eingang'] => array(
                        'initvalue' => $n['value'],
                        'comObject_id' => $this->export_add_item('editKo', $n['linkid'])
                    )
                );
            }
            if ($n['linktyp'] == 1) {
                $treeThis['inputs'][] = array(
                    'e' . $n['eingang'] => array(
                        'initvalue' => $n['value'],
                        'element_id' => (($this->exportType) ? '+' : '') . $n['linkid'],
                        'element_output' => 'a' . $n['ausgang']
                    )
                );
            }

            if ($n['linktyp'] == 2) {
                $treeThis['inputs'][] = array(
                    'e' . $n['eingang'] => array(
                        'initvalue' => $n['value']
                    )
                );
            }
        }

        sql_close($ss1);
        $ss1 = sql_call("SELECT id FROM edomiProject.editLogicCmdList WHERE targetid=" . $tmp['id'] . " ORDER BY id ASC");

        while ($n = sql_result($ss1)) {
            $this->export_add_item('editLogicCmdList', $n['id'], true, $noLink, $treeThis['commands']);
        }
        sql_close($ss1);
    }

    private function export_add_item_editLogicCmdList(&$tree, $id, $tmp, $addFolder = false, $noLink = false)
    {
        $tree[] = $this->export_parse_command($tmp);
    }

    private function export_add_item_editLogicElementDef(&$tree, $id, $tmp, $addFolder = false, $noLink = false)
    {
        $tree[] = array(
            '#noLink' => (($noLink) ? '1' : '0'),
            'id' => $id,
            'fileName' => $tmp['id'] . '_lbs.php'
        );
    }

    private function export_add_item_editVisu(&$tree, $id, $tmp, $addFolder = false, $noLink = false)
    {
        $folderIdPageroot = sql_getValue('edomiProject.editRoot', 'id', "linkdb='editVisu' AND linkid=" . $tmp['id'] . " AND parentid=22");
        if ($folderIdPageroot > 0) {
            $ss1 = sql_call("SELECT id FROM edomiProject.editRoot WHERE linkdb='editVisu' AND linkid=" . $tmp['id'] . " ORDER BY id ASC");
            while ($n = sql_result($ss1)) {
                $this->export_add_item('editRoot', $n['id'], false, $noLink);
            }
            sql_close($ss1);
            $tree[] = array(
                '#noLink' => (($noLink) ? '1' : '0'),
                'id' => $id,
                'name' => $tmp['name'],
                'width' => $tmp['xsize'],
                'height' => $tmp['ysize'],
                'defaultPage_id' => (($tmp['defaultpageid'] > 0) ? (($this->exportType) ? '+' : '') . $tmp['defaultpageid'] : null),
                'refresh' => $tmp['refresh'],
                'clickDelay' => $tmp['clickrefresh'],
                'queueDelay' => $tmp['queuelatency'],
                'indicatorFgColor_id' => $this->export_add_item('editVisuFGcol', $tmp['indicolor']),
                'inputFgColor_id' => $this->export_add_item('editVisuFGcol', $tmp['indicolor2']),
                'screensaverPage_id' => (($tmp['sspageid'] > 0) ? (($this->exportType) ? '+' : '') . $tmp['sspageid'] : null),
                'screensaverDelay' => $tmp['sstimeout'],
                'screensaverRefresh' => $tmp['ssrefresh'],
                'accounts' => null);

            $treeThis =& $tree[count($tree) - 1];

            $ss1 = sql_call("SELECT targetid FROM edomiProject.editVisuUserList WHERE visuid=" . $tmp['id'] . " ORDER BY id ASC");
            while ($n = sql_result($ss1)) {
                $treeThis['accounts'][] = array(
                    'visuAccount_id' => $this->export_add_item('editVisuUser', $n['targetid'])
                );
            }

            sql_close($ss1);
            $ss1 = sql_call("SELECT id FROM edomiProject.editVisuPage WHERE visuid=" . $tmp['id'] . " ORDER BY folderid ASC,id ASC");
            while ($n = sql_result($ss1)) {
                $this->export_add_item('editVisuPage', $n['id'], true, $noLink);
            }
            sql_close($ss1);
        }
    }

    private function export_add_item_editVisuUser(&$tree, $id, $tmp, $addFolder = false, $noLink = false)
    {
        $tree[] = array(
            '#noLink' => (($noLink) ? '1' : '0'),
            'id' => $id, 'login' => $tmp['login'],
            'password' => $tmp['pass'],
            'folder_id' => (($addFolder || !$this->exportType) ? $this->export_add_item('editRoot', $tmp['folderid'], false) : null),
            'refresh' => $tmp['refresh'],
            'clickDelay' => $tmp['clickrefresh'],
            'queueDelay' => $tmp['queuelatency'],
            'screensaverRefresh' => $tmp['ssrefresh'],
            'inputDevice' => $tmp['touch'],
            'touchScrolling' => $tmp['touchscroll'],
            'autoLogout' => $tmp['autologout'],
            'comObjectStatus_id' => $this->export_add_item('editKo', $tmp['gaid']),
            'comObjectInfo_id' => $this->export_add_item('editKo', $tmp['gaid2'])
        );
    }

    private function export_add_item_editVisuPage(&$tree, $id, $tmp, $addFolder = false, $noLink = false)
    {
        $addGlobalInkludes = false;
        if ($this->export_is_inItems('editVisu', $tmp['visuid']) || $this->export_is_inFolders(21)) {
            $addFolder = true;
            $addGlobalInkludes = true;
        }
        if ($tmp['pagetyp'] == 0) {
            $tree[] = array(
                '#noLink' => (($noLink) ? '1' : '0'),
                'id' => $id,
                'name' => $tmp['name'],
                'folder_id' => (($addFolder || !$this->exportType) ? $this->export_add_item('editRoot', $tmp['folderid'], false) : null),
                'type' => 'page',
                'includePage_id' => null,
                'globalIncludeEnabled' => $tmp['globalinclude'],
                'bgColor_id' => $this->export_add_item('editVisuBGcol', $tmp['bgcolorid']),
                'bgImage_id' => $this->export_add_item('editVisuImg', $tmp['bgimg']),
                'gridX' => $tmp['xgrid'],
                'gridY' => $tmp['ygrid'],
                'elements' => null);
        } else if ($tmp['pagetyp'] == 1) {
            $tree[] = array(
                '#noLink' => (($noLink) ? '1' : '0'),
                'id' => $id, 'name' => $tmp['name'],
                'folder_id' => (($addFolder || !$this->exportType) ? $this->export_add_item('editRoot', $tmp['folderid'], false) : null),
                'type' => 'popup',
                'autoClose' => $tmp['autoclose'],
                'width' => $tmp['xsize'],
                'height' => $tmp['ysize'],
                'bgColor_id' => $this->export_add_item('editVisuBGcol', $tmp['bgcolorid']),
                'bgImage_id' => $this->export_add_item('editVisuImg', $tmp['bgimg']),
                'gridX' => $tmp['xgrid'], 'gridY' => $tmp['ygrid'], 'elements' => null);
        } else if ($tmp['pagetyp'] == 2) {
            $tree[] = array('#noLink' => (($noLink) ? '1' : '0'),
                'id' => $id, 'name' => $tmp['name'],
                'folder_id' => (($addFolder || !$this->exportType) ? $this->export_add_item('editRoot', $tmp['folderid'], false) : null),
                'type' => 'global',
                'bgColor_id' => $this->export_add_item('editVisuBGcol', $tmp['bgcolorid']),
                'bgImage_id' => $this->export_add_item('editVisuImg', $tmp['bgimg']),
                'gridX' => $tmp['xgrid'],
                'gridY' => $tmp['ygrid'],
                'elements' => null
            );
        }
        $treeThis =& $tree[count($tree) - 1];
        $ss1 = sql_call("SELECT id FROM edomiProject.editVisuElement WHERE pageid=" . $tmp['id'] . " ORDER BY (controltyp=255) DESC,id ASC");
        while ($n = sql_result($ss1)) {
            $this->export_add_item('editVisuElement', $n['id'], true, $noLink, $treeThis['elements']);
        }

        sql_close($ss1);
        $treeThis['includePage_id'] = $this->export_add_item('editVisuPage', $tmp['includeid']);

        if ($addGlobalInkludes || ($tmp['pagetyp'] == 0 && $tmp['globalinclude'] == 1)) {
            $ss1 = sql_call("SELECT id FROM edomiProject.editVisuPage WHERE (visuid=" . $tmp['visuid'] . " AND pagetyp=2) ORDER BY id ASC");
            while ($n = sql_result($ss1)) {
                $this->export_add_item('editVisuPage', $n['id']);
            }
            sql_close($ss1);
        }
    }

    private function export_add_item_editVisuElement(&$tree, $id, $tmp, $addFolder = false, $noLink = false)
    {
        if ($tmp['controltyp'] == 255) {
            $tree[] = array(
                'id' => $id, 'type' => $this->visuelementTypes[$tmp['controltyp']][0],
                'name' => $tmp['name']
            );
        } else {
            if ($tmp['controltyp'] == 20) {
                $tmp['var1'] = $this->export_add_item('editCam', $tmp['var1']);
                $tmp['var3'] = $this->export_add_item('editArchivCam', $tmp['var3']);
            }
            if ($tmp['controltyp'] == 21) {
                $tmp['var1'] = $this->export_add_item('editChart', $tmp['var1']);
            }
            if ($tmp['controltyp'] == 22) {
                $tmp['var1'] = $this->export_add_item('editTimer', $tmp['var1']);
            }
            if ($tmp['controltyp'] == 23) {
                $tmp['var1'] = $this->export_add_item('editAws', $tmp['var1']);
            }
            if ($tmp['controltyp'] == 25) {
                $tmp['var1'] = $this->export_add_item('editArchivMsg', $tmp['var1']);
            }
            if ($tmp['controltyp'] == 26) {
                $tmp['var1'] = $this->export_add_item('editArchivKo', $tmp['var1']);
            }
            if ($tmp['controltyp'] == 28) {
                $tmp['var1'] = $this->export_add_item('editArchivPhone', $tmp['var1']);
            }
            if ($tmp['controltyp'] == 32) {
                $tmp['var1'] = $this->export_add_item('editAgenda', $tmp['var1']);
            }
            $tree[] = array(
                'id' => $id, 'type' => $this->visuelementTypes[$tmp['controltyp']][0],
                'name' => $tmp['name'],
                'group_id' => (($tmp['groupid'] > 0) ? (($this->exportType) ? '+' : '') . $tmp['groupid'] : null),
                'zindex' => $tmp['zindex'],
                'x' => $tmp['xpos'],
                'y' => $tmp['ypos'],
                'width' => $tmp['xsize'],
                'height' => $tmp['ysize'],
                'options' => array(
                    ((!isEmpty($this->visuelementTypes[$tmp['controltyp']][1])) ? $this->visuelementTypes[$tmp['controltyp']][1] : 'option1') => $tmp['var1'],
                    ((!isEmpty($this->visuelementTypes[$tmp['controltyp']][2])) ? $this->visuelementTypes[$tmp['controltyp']][2] : 'option2') => $tmp['var2'],
                    ((!isEmpty($this->visuelementTypes[$tmp['controltyp']][3])) ? $this->visuelementTypes[$tmp['controltyp']][3] : 'option3') => $tmp['var3'],
                    ((!isEmpty($this->visuelementTypes[$tmp['controltyp']][4])) ? $this->visuelementTypes[$tmp['controltyp']][4] : 'option4') => $tmp['var4'],
                    ((!isEmpty($this->visuelementTypes[$tmp['controltyp']][5])) ? $this->visuelementTypes[$tmp['controltyp']][5] : 'option5') => $tmp['var5'],
                    ((!isEmpty($this->visuelementTypes[$tmp['controltyp']][6])) ? $this->visuelementTypes[$tmp['controltyp']][6] : 'option6') => $tmp['var6'],
                    ((!isEmpty($this->visuelementTypes[$tmp['controltyp']][7])) ? $this->visuelementTypes[$tmp['controltyp']][7] : 'option7') => $tmp['var7'],
                    ((!isEmpty($this->visuelementTypes[$tmp['controltyp']][8])) ? $this->visuelementTypes[$tmp['controltyp']][8] : 'option8') => $tmp['var8'],
                    ((!isEmpty($this->visuelementTypes[$tmp['controltyp']][9])) ? $this->visuelementTypes[$tmp['controltyp']][9] : 'option9') => $tmp['var9'],
                    ((!isEmpty($this->visuelementTypes[$tmp['controltyp']][10])) ? $this->visuelementTypes[$tmp['controltyp']][10] : 'option10') => $tmp['var10']),
                'caption' => $tmp['text'],
                'design' => null, 'initonly' => (($tmp['initonly'] > 0) ? '1' : null),
                'dynamicDesigns' => null,
                'comObject1_id' => $this->export_add_item('editKo', $tmp['gaid']),
                'comObject2_id' => $this->export_add_item('editKo', $tmp['gaid2']),
                'pageLink_id' => null, 'popupClose' => (($tmp['closepopup'] > 0) ? '1' : null), 'commands' => null
            );
            $treeThis =& $tree[count($tree) - 1];

            $ss1 = sql_call("SELECT * FROM edomiProject.editVisuElementDesign WHERE (targetid=" . $tmp['id'] . " AND styletyp=0) ORDER BY id ASC");
            if ($n = sql_result($ss1)) {
                $treeThis['design'] = array(
                        'designTemplate_id' => $this->export_add_item('editVisuElementDef', $n['defid'])) + $this->export_parse_visuelementDesign($n);
            }

            sql_close($ss1);
            $ss1 = sql_call("SELECT * FROM edomiProject.editVisuElementDesign WHERE (targetid=" . $tmp['id'] . " AND styletyp=1) ORDER BY id ASC");
            while ($n = sql_result($ss1)) {
                $treeThis['dynamicDesigns'][] = array('designTemplate_id' => $this->export_add_item('editVisuElementDef', $n['defid'])) + $this->export_parse_visuelementDesign($n);
            }

            sql_close($ss1);

            $treeThis['pageLink_id'] = $this->export_add_item('editVisuPage', $tmp['gotopageid']);
            $ss1 = sql_call("SELECT id FROM edomiProject.editVisuCmdList WHERE targetid=" . $tmp['id'] . " ORDER BY id ASC");
            while ($n = sql_result($ss1)) {
                $this->export_add_item('editVisuCmdList', $n['id'], true, $noLink, $treeThis['commands']);
            }

            sql_close($ss1);
        }
    }

    private function export_add_item_editVisuCmdList(&$tree, $id, $tmp, $addFolder = false, $noLink = false)
    {
        $tree[] = $this->export_parse_command($tmp);
    }

    private function export_add_item_editVisuElementDef(&$tree, $id, $tmp, $addFolder = false, $noLink = false)
    {
        $tree[] = array(
                '#noLink' => (($noLink) ? '1' : '0'),
                'id' => $id, 'name' => $tmp['name'],
                'folder_id' => (($addFolder || !$this->exportType) ? $this->export_add_item('editRoot', $tmp['folderid'], false) : null),
                'type' => (($tmp['styletyp'] == 0) ? 'static' : 'dynamic')) + $this->export_parse_visuelementDesign($tmp);
    }

    private function export_add_item_editVisuFGcol(&$tree, $id, $tmp, $addFolder = false, $noLink = false)
    {
        $tree[] = array(
            '#noLink' => (($noLink) ? '1' : '0'),
            'id' => $id,
            'name' => $tmp['name'],
            'folder_id' => (($addFolder || !$this->exportType) ? $this->export_add_item('editRoot', $tmp['folderid'], false) : null),
            'color' => $tmp['color']);
    }

    private function export_add_item_editVisuBGcol(&$tree, $id, $tmp, $addFolder = false, $noLink = false)
    {
        $tree[] = array('#noLink' => (($noLink) ? '1' : '0'), 'id' => $id, 'name' => $tmp['name'], 'folder_id' => (($addFolder || !$this->exportType) ? $this->export_add_item('editRoot', $tmp['folderid'], false) : null), 'color' => $tmp['color']);
    }

    private function export_add_item_editVisuImg(&$tree, $id, $tmp, $addFolder = false, $noLink = false)
    {
        $tree[] = array('#noLink' => (($noLink) ? '1' : '0'), 'id' => $id, 'name' => $tmp['name'], 'folder_id' => (($addFolder || !$this->exportType) ? $this->export_add_item('editRoot', $tmp['folderid'], false) : null), 'fileName' => 'img-' . $tmp['id'] . '.' . $tmp['suffix']);
    }

    private function export_add_item_editVisuFont(&$tree, $id, $tmp, $addFolder = false, $noLink = false)
    {
        if ($tmp['fonttyp'] == 0) {
            $tree[] = array(
                '#noLink' => (($noLink) ? '1' : '0'),
                'id' => $id,
                'name' => $tmp['name'],
                'folder_id' => (($addFolder || !$this->exportType) ? $this->export_add_item('editRoot', $tmp['folderid'], false) : null),
                'fontType' => $tmp['fonttyp'],
                'fontName' => $tmp['fontname']);
        } else {
            $tree[] = array(
                '#noLink' => (($noLink) ? '1' : '0'),
                'id' => $id, 'name' => $tmp['name'],
                'folder_id' => (($addFolder || !$this->exportType) ? $this->export_add_item('editRoot', $tmp['folderid'], false) : null),
                'fontType' => $tmp['fonttyp'],
                'fontStyle' => $tmp['fontstyle'],
                'fontWeight' => $tmp['fontweight'],
                'fileName' => 'font-' . $tmp['id'] . '.ttf');
        }
    }

    private function export_add_item_editVisuAnim(&$tree, $id, $tmp, $addFolder = false, $noLink = false)
    {
        $tree[] = array(
            '#noLink' => (($noLink) ? '1' : '0'),
            'id' => $id,
            'name' => $tmp['name'],
            'folder_id' => (($addFolder || !$this->exportType) ? $this->export_add_item('editRoot', $tmp['folderid'], false) : null),
            'keyframes' => $tmp['keyframes']);
    }

    private function export_add_item_editVisuSnd(&$tree, $id, $tmp, $addFolder = false, $noLink = false)
    {
        $tree[] = array(
            '#noLink' => (($noLink) ? '1' : '0'),
            'id' => $id,
            'name' => $tmp['name'],
            'folder_id' => (($addFolder || !$this->exportType) ? $this->export_add_item('editRoot', $tmp['folderid'], false) : null),
            'fileName' => 'snd-' . $tmp['id'] . '.mp3');
    }

    private function export_add_item_editRoot(&$tree, $id, $tmp, $addFolder = false, $noLink = false)
    {
        $root = '/';
        $path = '/';
        $arr = explode('/', $tmp['path'], -1);
        for ($t = 1; $t < count($arr); $t++) {
            $n = $this->export_add_item('editRoot', $arr[$t]);
            if ($this->export_isSysFolder($n)) {
                $root .= $n . '/';
            } else {
                $path .= $n . '/';
            }
        }
        if (!isEmpty($this->sysFolders[$root])) {
            $root = $this->sysFolders[$root];
        }
        $link = array();
        if ($root == $this->sysFolders['/20/22/'] && $path == '/') {
            $link = array('visu_id' => (($this->exportType) ? '+' : '') . $tmp['linkid']);
            $tmp['name'] = null;
        }
        $tree[] = array(
                '#noLink' => (($noLink) ? '1' : '0'),
                'id' => $id,
                'name' => $tmp['name'],
                'path' => $root . rtrim($path, '/')) + $link;
    }

    private function export_add_item_editAgenda(&$tree, $id, $tmp, $addFolder = false, $noLink = false)
    {
        $tree[] = array(
            '#noLink' => (($noLink) ? '1' : '0'),
            'id' => $id,
            'name' => $tmp['name'],
            'folder_id' => (($addFolder || !$this->exportType) ? $this->export_add_item('editRoot', $tmp['folderid'], false) : null),
            'comObject_id' => $this->export_add_item('editKo', $tmp['gaid']),
            'comObjectSwitch_id' => $this->export_add_item('editKo', $tmp['outgaid']),
            'items' => null
        );
        $treeThis =& $tree[count($tree) - 1];
        $ss1 = sql_call("SELECT * FROM edomiProject.editAgendaData WHERE (targetid=" . $tmp['id'] . " AND fixed=1) ORDER BY id ASC");
        while ($n = sql_result($ss1)) {
            $this->export_add_item('editAgendaData', $n['id'], true, $noLink, $treeThis['items']);
        }
        sql_close($ss1);
    }

    private function export_add_item_editAgendaData(&$tree, $id, $tmp, $addFolder = false, $noLink = false)
    {
        $tree[] = array(
            'name' => $tmp['name'],
            'value' => $tmp['status'],
            'hour' => $tmp['hour'],
            'minute' => $tmp['minute'],
            'date' => ((isEmpty($tmp['date1'])) ? null : sql_getDate($tmp['date1'])),
            'interval' => (($tmp['step'] < 1) ? null : $tmp['step']),
            'unit' => $tmp['unit'],
            'dateExpire' => ((isEmpty($tmp['date2'])) ? null : sql_getDate($tmp['date2']))
        );
    }

    private function export_add_item_editScene(&$tree, $id, $tmp, $addFolder = false, $noLink = false)
    {
        $tree[] = array(
            '#noLink' => (($noLink) ? '1' : '0'),
            'id' => $id,
            'name' => $tmp['name'],
            'folder_id' => (($addFolder || !$this->exportType) ? $this->export_add_item('editRoot', $tmp['folderid'], false) : null),
            'items' => null
        );
        $treeThis =& $tree[count($tree) - 1];
        $ss1 = sql_call("SELECT * FROM edomiProject.editSceneList WHERE targetid=" . $tmp['id'] . " ORDER BY id ASC");
        while ($n = sql_result($ss1)) {
            $this->export_add_item('editSceneList', $n['id'], true, $noLink, $treeThis['items']);
        }
        sql_close($ss1);
    }

    private function export_add_item_editSceneList(&$tree, $id, $tmp, $addFolder = false, $noLink = false)
    {
        $tree[] = array(
            'comObject_id' => $this->export_add_item('editKo', $tmp['gaid']),
            'comObjectLearn_id' => $this->export_add_item('editKo', $tmp['learngaid']),
            'value' => $tmp['gavalue']
        );
    }

    private function export_add_item_editTimer(&$tree, $id, $tmp, $addFolder = false, $noLink = false)
    {
        $tree[] = array(
            '#noLink' => (($noLink) ? '1' : '0'),
            'id' => $id,
            'name' => $tmp['name'],
            'folder_id' => (($addFolder || !$this->exportType) ? $this->export_add_item('editRoot', $tmp['folderid'], false) : null),
            'comObject_id' => $this->export_add_item('editKo', $tmp['gaid']),
            'comObjectSwitch_id' => $this->export_add_item('editKo', $tmp['outgaid']),
            'items' => null
        );
        $treeThis =& $tree[count($tree) - 1];
        $ss1 = sql_call("SELECT * FROM edomiProject.editTimerData WHERE (targetid=" . $tmp['id'] . " AND fixed=1) ORDER BY id ASC");
        while ($n = sql_result($ss1)) {
            $this->export_add_item('editTimerData', $n['id'], true, $noLink, $treeThis['items']);
        }
        sql_close($ss1);
    }

    private function export_add_item_editTimerData(&$tree, $id, $tmp, $addFolder = false, $noLink = false)
    {
        $tree[] = array(
            'value' => $tmp['status'],
            'hour' => $tmp['hour'],
            'minute' => $tmp['minute'],
            'monday' => $tmp['d0'],
            'tuesday' => $tmp['d1'],
            'wednesday' => $tmp['d2'],
            'thursday' => $tmp['d3'],
            'friday' => $tmp['d4'],
            'saturday' => $tmp['d5'],
            'sunday' => $tmp['d6'],
            'dayFrom' => ((isEmpty($tmp['day1'])) ? null : $tmp['day1']),
            'dayTo' => ((isEmpty($tmp['day2'])) ? null : $tmp['day2']),
            'monthFrom' => ((isEmpty($tmp['month1'])) ? null : $tmp['month1']),
            'monthTo' => ((isEmpty($tmp['month2'])) ? null : $tmp['month2']),
            'yearFrom' => ((isEmpty($tmp['year1'])) ? null : $tmp['year1']),
            'yearTo' => ((isEmpty($tmp['year2'])) ? null : $tmp['year2'])
        );
    }

    private function export_add_item_editSequence(&$tree, $id, $tmp, $addFolder = false, $noLink = false)
    {
        $tree[] = array(
            '#noLink' => (($noLink) ? '1' : '0'),
            'id' => $id,
            'name' => $tmp['name'],
            'folder_id' => (($addFolder || !$this->exportType) ? $this->export_add_item('editRoot', $tmp['folderid'], false) : null),
            'commands' => null
        );
        $treeThis =& $tree[count($tree) - 1];
        $ss1 = sql_call("SELECT * FROM edomiProject.editSequenceList WHERE targetid=" . $tmp['id'] . " ORDER BY sort ASC");
        while ($n = sql_result($ss1)) {
            $this->export_add_item('editSequenceList', $n['id'], true, $noLink, $treeThis['commands']);
        }
        sql_close($ss1);
    }

    private function export_add_item_editSequenceList(&$tree, $id, $tmp, $addFolder = false, $noLink = false)
    {
        $tree[] = $this->export_parse_command($tmp);
    }

    private function export_add_item_editArchivKo(&$tree, $id, $tmp, $addFolder = false, $noLink = false)
    {
        $tree[] = array(
            '#noLink' => (($noLink) ? '1' : '0'),
            'id' => $id, 'name' => $tmp['name'],
            'folder_id' => (($addFolder || !$this->exportType) ? $this->export_add_item('editRoot', $tmp['folderid'], false) : null),
            'keep' => $tmp['keep'], 'delay' => $tmp['delay'], 'comObject_id' => $this->export_add_item('editKo', $tmp['outgaid'])
        );
    }

    private function export_add_item_editArchivMsg(&$tree, $id, $tmp, $addFolder = false, $noLink = false)
    {
        $tree[] = array(
            '#noLink' => (($noLink) ? '1' : '0'),
            'id' => $id,
            'name' => $tmp['name'],
            'folder_id' => (($addFolder || !$this->exportType) ? $this->export_add_item('editRoot', $tmp['folderid'], false) : null),
            'keep' => $tmp['keep'],
            'delay' => $tmp['delay'],
            'comObject_id' => $this->export_add_item('editKo', $tmp['outgaid'])
        );
    }

    private function export_add_item_editArchivCam(&$tree, $id, $tmp, $addFolder = false, $noLink = false)
    {
        $tree[] = array(
            '#noLink' => (($noLink) ? '1' : '0'),
            'id' => $id,
            'name' => $tmp['name'],
            'folder_id' => (($addFolder || !$this->exportType) ? $this->export_add_item('editRoot', $tmp['folderid'], false) : null),
            'camera_id' => $this->export_add_item('editCam', $tmp['camid']),
            'keep' => $tmp['keep'], 'delay' => $tmp['delay'],
            'comObject_id1' => $this->export_add_item('editKo', $tmp['outgaid']),
            'comObject_id2' => $this->export_add_item('editKo', $tmp['outgaid2'])
        );
    }

    private function export_add_item_editCam(&$tree, $id, $tmp, $addFolder = false, $noLink = false)
    {
        $tree[] = array(
            '#noLink' => (($noLink) ? '1' : '0'),
            'id' => $id,
            'name' => $tmp['name'],
            'folder_id' => (($addFolder || !$this->exportType) ? $this->export_add_item('editRoot', $tmp['folderid'], false) : null),
            'type' => $tmp['mjpeg'],
            'url' => $tmp['url'],
            'mask' => $tmp['mask']
        );
    }

    private function export_add_item_editArchivPhone(&$tree, $id, $tmp, $addFolder = false, $noLink = false)
    {
        $tree[] = array(
            '#noLink' => (($noLink) ? '1' : '0'),
            'id' => $id, 'name' => $tmp['name'],
            'folder_id' => (($addFolder || !$this->exportType) ? $this->export_add_item('editRoot', $tmp['folderid'], false) : null),
            'keep' => $tmp['keep'], 'comObject_id' => $this->export_add_item('editKo', $tmp['outgaid'])
        );
    }

    private function export_add_item_editAws(&$tree, $id, $tmp, $addFolder = false, $noLink = false)
    {
        $tree[] = array(
            '#noLink' => (($noLink) ? '1' : '0'),
            'id' => $id, 'name' => $tmp['name'],
            'folder_id' => (($addFolder || !$this->exportType) ? $this->export_add_item('editRoot', $tmp['folderid'], false) : null),
            'comObject_id' => $this->export_add_item('editKo', $tmp['gaid']),
            'items' => null
        );
        $treeThis =& $tree[count($tree) - 1];
        $ss1 = sql_call("SELECT * FROM edomiProject.editAwsList WHERE targetid=" . $tmp['id'] . " ORDER BY id ASC");
        while ($n = sql_result($ss1)) {
            $this->export_add_item('editAwsList', $n['id'], true, $noLink, $treeThis['items']);
        }
        sql_close($ss1);
    }

    private function export_add_item_editAwsList(&$tree, $id, $tmp, $addFolder = false, $noLink = false)
    {
        $tree[] = array('comObject_id' => $this->export_add_item('editKo', $tmp['gaid']));
    }

    private function export_add_item_editIp(&$tree, $id, $tmp, $addFolder = false, $noLink = false)
    {
        $tree[] = array(
            '#noLink' => (($noLink) ? '1' : '0'),
            'id' => $id,
            'name' => $tmp['name'],
            'folder_id' => (($addFolder || !$this->exportType) ? $this->export_add_item('editRoot', $tmp['folderid'], false) : null),
            'type' => $tmp['iptyp'], 'url' => $tmp['url'], 'data' => $tmp['data']
        );
    }

    private function export_add_item_editEmail(&$tree, $id, $tmp, $addFolder = false, $noLink = false)
    {
        $tree[] = array(
            '#noLink' => (($noLink) ? '1' : '0'),
            'id' => $id, 'name' => $tmp['name'],
            'folder_id' => (($addFolder || !$this->exportType) ? $this->export_add_item('editRoot', $tmp['folderid'], false) : null),
            'address' => $tmp['mailaddr'],
            'subject' => $tmp['subject'],
            'body' => $tmp['body']
        );
    }

    private function export_add_item_editIr(&$tree, $id, $tmp, $addFolder = false, $noLink = false)
    {
        $tree[] = array(
            '#noLink' => (($noLink) ? '1' : '0'),
            'id' => $id,
            'name' => $tmp['name'],
            'folder_id' => (($addFolder || !$this->exportType) ? $this->export_add_item('editRoot', $tmp['folderid'], false) : null),
            'data' => $tmp['data']
        );
    }

    private function export_add_item_editPhoneBook(&$tree, $id, $tmp, $addFolder = false, $noLink = false)
    {
        $tree[] = array(
            '#noLink' => (($noLink) ? '1' : '0'),
            'id' => $id,
            'name' => $tmp['name'],
            'folder_id' => (($addFolder || !$this->exportType) ? $this->export_add_item('editRoot', $tmp['folderid'], false) : null),
            'number1' => $tmp['phone1'],
            'number2' => $tmp['phone2']
        );
    }

    private function export_add_item_editPhoneCall(&$tree, $id, $tmp, $addFolder = false, $noLink = false)
    {
        $tree[] = array(
            '#noLink' => (($noLink) ? '1' : '0'),
            'id' => $id,
            'name' => $tmp['name'],
            'folder_id' => (($addFolder || !$this->exportType) ? $this->export_add_item('editRoot', $tmp['folderid'], false) : null),
            'type' => $tmp['typ'],
            'sourcePhoneNumber_id' => $this->export_add_item('editPhoneBook', $tmp['phoneid1']),
            'destinationPhoneNumber_id' => $this->export_add_item('editPhoneBook', $tmp['phoneid2']),
            'comObject_id' => $this->export_add_item('editKo', $tmp['gaid1']),
            'comObjectSource_id' => $this->export_add_item('editKo', $tmp['gaid2']),
            'comObjectDestination_id' => $this->export_add_item('editKo', $tmp['gaid3'])
        );
    }

    private function export_add_item_editChart(&$tree, $id, $tmp, $addFolder = false, $noLink = false)
    {
        $tree[] = array(
            '#noLink' => (($noLink) ? '1' : '0'),
            'id' => $id,
            'name' => $tmp['name'],
            'folder_id' => (($addFolder || !$this->exportType) ? $this->export_add_item('editRoot', $tmp['folderid'], false) : null),
            'title' => $tmp['titel'],
            'dateFrom' => $tmp['datefrom'],
            'dateTo' => $tmp['dateto'],
            'accumulation' => $tmp['mode'],
            'xAxisUnit' => $tmp['xunit'],
            'xAxisTickInterval' => $tmp['xinterval'],
            'yAxisGlobalMin' => $tmp['ymin'],
            'yAxisGlobalMax' => $tmp['ymax'],
            'yAxisGlobalNice' => $tmp['ynice'],
            'yAxisGlobalTicks' => $tmp['yticks'],
            'charts' => null
        );
        $treeThis =& $tree[count($tree) - 1];
        $ss1 = sql_call("SELECT * FROM edomiProject.editChartList WHERE targetid=" . $tmp['id'] . " ORDER BY id ASC");
        while ($n = sql_result($ss1)) {
            $this->export_add_item('editChartList', $n['id'], true, $noLink, $treeThis['charts']);
        }
        sql_close($ss1);
    }

    private function export_add_item_editChartList(&$tree, $id, $tmp, $addFolder = false, $noLink = false)
    {
        $tree[] = array(
            'dataArchive_id' => $this->export_add_item('editArchivKo', $tmp['archivkoid']),
            'title' => $tmp['titel'],
            'chart1_type' => $tmp['charttyp'],
            'chart1_fgColor_id' => $this->export_add_item('editVisuFGcol', $tmp['s1']),
            'chart1_opacity' => $tmp['s2'],
            'chart1_lineWidth' => $tmp['s3'],
            'chart1_shadowSize' => $tmp['s4'],
            'chart1_showMinMax' => $tmp['yminmax'],
            'chart2_type' => $tmp['charttyp2'],
            'chart2_fgColor_id' => $this->export_add_item('editVisuFGcol', $tmp['ss1']),
            'chart2_opacity' => $tmp['ss2'],
            'chart2_lineWidth' => $tmp['ss3'],
            'chart2_shadowSize' => $tmp['ss4'],
            'avgInterval' => $tmp['xinterval'],
            'extentionLeft' => $tmp['extend1'],
            'extentionRight' => $tmp['extend2'],
            'yAxisStyle' => $tmp['ystyle'],
            'yAxisForceVisibility' => $tmp['yshow'],
            'yAxisShowCurrentValue' => $tmp['yshowvalue'],
            'yAxisScale' => $tmp['yscale'],
            'yAxisMin' => $tmp['ymin'],
            'yAxisMax' => $tmp['ymax'],
            'yAxisNice' => $tmp['ynice'],
            'yAxisTicks' => $tmp['yticks'],
            'yGridColor' => $tmp['ygrid1'],
            'yGridOpacity' => $tmp['ygrid2'],
            'yGridMultiple' => $tmp['ygrid3']);
    }

    private function export_add_item_editHttpKo(&$tree, $id, $tmp, $addFolder = false, $noLink = false)
    {
        $tree[] = array(
            '#noLink' => (($noLink) ? '1' : '0'),
            'id' => $id, 'name' => $tmp['name'],
            'folder_id' => (($addFolder || !$this->exportType) ? $this->export_add_item('editRoot', $tmp['folderid'], false) : null),
            'type' => $tmp['typ'], 'comObject_id' => $this->export_add_item('editKo', $tmp['gaid'])
        );
    }

    private function export_parse_command($tmp)
    {
        if ($tmp['outmode'] == 1) {
            $tmp['outid'] = $this->export_add_item('editKo', $tmp['outid']);
        }
        if ($tmp['outmode'] == 13) {
            $tmp['outid'] = $this->export_add_item('editArchivKo', $tmp['outid']);
        }
        if ($tmp['outmode'] == 14) {
            $tmp['outid'] = $this->export_add_item('editArchivMsg', $tmp['outid']);
        }
        if ($tmp['outmode'] == 2) {
            $tmp['outid'] = $this->export_add_item('editKo', $tmp['outid']);
        }
        if ($tmp['outmode'] == 3) {
            $tmp['outid'] = $this->export_add_item('editKo', $tmp['outid']);
            $tmp['outvalue'] = $this->export_add_item('editKo', $tmp['outvalue']);
        }
        if ($tmp['outmode'] == 4) {
            $tmp['outid'] = $this->export_add_item('editKo', $tmp['outid']);
        }
        if ($tmp['outmode'] == 5) {
            $tmp['outid'] = $this->export_add_item('editKo', $tmp['outid']);
        }
        if ($tmp['outmode'] == 6) {
            $tmp['outid'] = $this->export_add_item('editKo', $tmp['outid']);
            $tmp['outvalue'] = $this->export_add_item('editKo', $tmp['outvalue']);
        }
        if ($tmp['outmode'] == 7) {
            $tmp['outid'] = $this->export_add_item('editKo', $tmp['outid']);
        }
        if ($tmp['outmode'] == 8) {
            $tmp['outid'] = $this->export_add_item('editKo', $tmp['outid']);
        }
        if ($tmp['outmode'] == 9) {
            $tmp['outid'] = $this->export_add_item('editKo', $tmp['outid']);
        }
        if ($tmp['outmode'] == 10) {
            $tmp['outid'] = $this->export_add_item('editScene', $tmp['outid']);
        }
        if ($tmp['outmode'] == 11) {
            $tmp['outid'] = $this->export_add_item('editSequence', $tmp['outid']);
        }
        if ($tmp['outmode'] == 40) {
            $tmp['outid'] = $this->export_add_item('editArchivKo', $tmp['outid']);
        }
        if ($tmp['outmode'] == 41) {
            $tmp['outid'] = $this->export_add_item('editArchivMsg', $tmp['outid']);
        }
        if ($tmp['outmode'] == 12) {
            $tmp['outid'] = $this->export_add_item('editArchivCam', $tmp['outid']);
        }
        if ($tmp['outmode'] == 50) {
            $tmp['outid'] = $this->export_add_item('editArchivKo', $tmp['outid']);
        }
        if ($tmp['outmode'] == 51) {
            $tmp['outid'] = $this->export_add_item('editArchivMsg', $tmp['outid']);
        }
        if ($tmp['outmode'] == 52) {
            $tmp['outid'] = $this->export_add_item('editArchivCam', $tmp['outid']);
        }
        if ($tmp['outmode'] == 53) {
            $tmp['outid'] = $this->export_add_item('editArchivPhone', $tmp['outid']);
        }
        if ($tmp['outmode'] == 15) {
            $tmp['outid'] = $this->export_add_item('editIp', $tmp['outid']);
        }
        if ($tmp['outmode'] == 16) {
            $tmp['outid'] = $this->export_add_item('editIr', $tmp['outid']);
        }
        if ($tmp['outmode'] == 20) {
            $tmp['outid'] = $this->export_add_item('editEmail', $tmp['outid']);
        }
        if ($tmp['outmode'] == 22) {
            $tmp['outid'] = $this->export_add_item('editPhoneBook', $tmp['outid']);
        }
        if ($tmp['outmode'] == 21) {
            $tmp['outid'] = $this->export_add_item('editVisuPage', $tmp['outid']);
            $tmp['outvalue'] = $this->export_add_item('editVisuUser', $tmp['outvalue']);
        }
        if ($tmp['outmode'] == 28) {
            $tmp['outid'] = $this->export_add_item('editVisu', $tmp['outid']);
            $tmp['outvalue'] = $this->export_add_item('editVisuUser', $tmp['outvalue']);
        }
        if ($tmp['outmode'] == 24) {
            $tmp['outid'] = $this->export_add_item('editVisu', $tmp['outid']);
            $tmp['outvalue'] = $this->export_add_item('editVisuSnd', $tmp['outvalue']);
        }
        if ($tmp['outmode'] == 25) {
            $tmp['outid'] = $this->export_add_item('editVisuUser', $tmp['outid']);
            $tmp['outvalue'] = $this->export_add_item('editVisuSnd', $tmp['outvalue']);
        }
        if ($tmp['outmode'] == 26) {
            $tmp['outid'] = $this->export_add_item('editVisu', $tmp['outid']);
        }
        if ($tmp['outmode'] == 27) {
            $tmp['outid'] = $this->export_add_item('editVisuUser', $tmp['outid']);
        }
        if ($tmp['outmode'] == 23) {
            $tmp['outid'] = $this->export_add_item('editVisu', $tmp['outid']);
            $tmp['outvalue'] = $this->export_add_item('editVisuUser', $tmp['outvalue']);
        }
        if ($tmp['outmode'] == 30) {
        }
        $r = array('type' => $this->commandTypes[$tmp['outmode']][0]);
        if (!isEmpty($this->commandTypes[$tmp['outmode']][1])) {
            $r += array($this->commandTypes[$tmp['outmode']][1] => $tmp['outid']);
        }
        if (!isEmpty($this->commandTypes[$tmp['outmode']][2])) {
            $r += array($this->commandTypes[$tmp['outmode']][2] => $tmp['outvalue']);
        }
        if (!isEmpty($tmp['delay'])) {
            $r += array('wait' => $tmp['delay']);
        }
        return $r;
    }

    private function export_parse_visuelementDesign($tmp)
    {
        $r = array(
            'from' => $tmp['s1'],
            'to' => $tmp['s2'],
            'dx' => $tmp['s3'],
            'dy' => $tmp['s4'],
            'dwidth' => $tmp['s5'],
            'dheight' => $tmp['s6'],
            'angle' => $tmp['s7'],
            'opacity' => $tmp['s8'],
            'bgColor_id' => $this->export_add_item('editVisuBGcol', $tmp['s9']),
            'bgImage_id' => $this->export_add_item('editVisuImg', $tmp['s10']),
            'caption' => $tmp['s11'],
            'padding' => $tmp['s12'],
            'font_id' => $this->export_add_item('editVisuFont', $tmp['s13']),
            'fontsize' => $tmp['s14'],
            'fgColor_id' => $this->export_add_item('editVisuFGcol', $tmp['s15']),
            'fontstyle' => $tmp['s16'],
            'fontweight' => $tmp['s17'],
            'align' => $tmp['s18'],
            'textShadowX' => $tmp['s19'],
            'textShadowY' => $tmp['s20'],
            'textShadowBlur' => $tmp['s21'],
            'textShadowColor_id' => $this->export_add_item('editVisuFGcol', $tmp['s22']),
            'borderRadiusTopLeft' => $tmp['s23'],
            'borderRadiusTopRight' => $tmp['s24'],
            'borderRadiusBottomRight' => $tmp['s25'],
            'borderRadiusBottomLeft' => $tmp['s26'],
            'borderColorLeft_id' => $this->export_add_item('editVisuFGcol', $tmp['s27']),
            'borderColorTop_id' => $this->export_add_item('editVisuFGcol', $tmp['s28']),
            'borderColorRight_id' => $this->export_add_item('editVisuFGcol', $tmp['s29']),
            'borderColorBottom_id' => $this->export_add_item('editVisuFGcol', $tmp['s30']),
            'borderWidth' => $tmp['s31'], 'borderPattern' => $tmp['s32'], 'shadowX' => $tmp['s33'],
            'shadowY' => $tmp['s34'],
            'shadowBlur' => $tmp['s35'],
            'shadowSize' => $tmp['s36'],
            'shadowColor_id' => $this->export_add_item('editVisuFGcol', $tmp['s37']),
            'shadowType' => $tmp['s38'],
            'animation_id' => $this->export_add_item('editVisuAnim', $tmp['s39']),
            'animationDuration' => $tmp['s40'],
            'animationCount' => $tmp['s41'],
            'auxFgcolor1_id' => $this->export_add_item('editVisuFGcol', $tmp['s42']),
            'auxFgcolor2_id' => $this->export_add_item('editVisuFGcol', $tmp['s43']),
            'auxBgcolor1_id' => $this->export_add_item('editVisuBGcol', $tmp['s44']),
            'auxBgcolor2_id' => $this->export_add_item('editVisuBGcol', $tmp['s45']),
            'auxBgImage1_id' => $this->export_add_item('editVisuImg', $tmp['s46']),
            'auxBgImage2_id' => $this->export_add_item('editVisuImg', $tmp['s47'])
        );
        return $r;
    }

    private function export_jsonEncode($arr, $reduce)
    {
        $r = $this->export_jsonEncode_rekursive(array($arr), 0, $reduce);
        if (!isEmpty($r)) {
            return $r;
        } else {
            return false;
        }
    }

    private function export_jsonEncode_rekursive($arr, $tabLevel, $reduce)
    {
        $r = '';
        foreach ($arr as $k => $v) {
            if (is_array($v)) {
                if (!is_numeric($k)) {
                    $r .= str_repeat("\t", $tabLevel) . '"' . $k . '": ' . "\n";
                }
                if ($this->isAssocArray($v)) {
                    $charOpen = '{';
                    $charClose = '}';
                } else {
                    $charOpen = '[';
                    $charClose = ']';
                }
                $r .= str_repeat("\t", $tabLevel) . $charOpen . "\n";
                $r .= $this->export_jsonEncode_rekursive($v, $tabLevel + 1, $reduce);
                $r .= str_repeat("\t", $tabLevel) . $charClose . ',' . "\n";
            } else if (!isEmpty($v) || !$reduce) {
                $v = str_replace(chr(92), chr(92) . chr(92), $v);
                $v = str_replace(chr(9), '\t', $v);
                $v = str_replace(chr(10), '\n', $v);
                $v = str_replace(chr(13), '\r', $v);
                $v = str_replace(chr(34), '\"', $v);
                $r .= str_repeat("\t", $tabLevel) . '"' . $k . '": "' . $v . '",' . "\n";
            }
        }
        $r = rtrim($r, ",\n") . "\n";
        return $r;
    }

    private function export_id_Exists($arr, $id)
    {
        foreach ($arr as $key => $value) {
            if ($value['id'] == (($this->exportType) ? '+' : '') . $id) {
                return (($this->exportType) ? '+' : '') . $id;
            }
        }
        return false;
    }

    private function export_isSysFolder($id)
    {
        $id = trim($id);
        $n = substr($id, 0, 1);
        if ($n != '+' && $n != '-' && $id >= 1 && $id < 1000) {
            return $id;
        }
        return false;
    }

    private function export_is_inFolders($id)
    {
        if (!($id > 0)) {
            return false;
        }
        $tmp = sql_getValues('edomiProject.editRoot', 'path', 'id=' . $id);
        if ($tmp !== false) {
            $folderIds = explode('/', trim($tmp['path'] . $id, '/'));
            foreach ($folderIds as $k1 => $v1) {
                foreach ($this->exportFolders as $k2 => $v2) {
                    if ($v1 == $v2[0]) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    private function export_is_inItems($db, $id)
    {
        if (!($id > 0)) {
            return false;
        }
        foreach ($this->exportItems as $k => $v) {
            if ($db == $v[0] && $id == $v[1]) {
                return true;
            }
        }
        return false;
    }

    private function export_is_inExportlists($db, $id)
    {
        if ($this->export_is_inItems($db, $id)) {
            return true;
        }
        $tmp = sql_getValues($db, 'folderid', 'id=' . $id);
        if ($tmp !== false) {
            if ($this->export_is_inFolders($tmp['folderid'])) {
                return true;
            }
        }
        if ($db == 'editVisuPage') {
        }
        return false;
    }

    private function isAssocArray($arr)
    {
        $arr = array_keys($arr);
        return ($arr !== array_keys($arr));
    }
}

sql_disconnect(); ?>
