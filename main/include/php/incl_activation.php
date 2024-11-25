<? class class_projectActivation
{
    private $statusMax = 0;
    private $statusCurrent = 0;
    private $errCount = 0;
    private $errFatalCount = 0;

    public function start($options)
    {
        deleteFiles(MAIN_PATH . '/www/data/tmp/activation_status.txt');
        deleteFiles(MAIN_PATH . '/www/data/tmp/activation_report.txt');
        deleteFiles(MAIN_PATH . '/www/data/tmp/activation_options.txt');
        $this->setStatus(0);
        $prjData = sql_getValues('edomiAdmin.project', '*', 'edit=1');
        if ($prjData !== false) {
            deleteFiles(MAIN_PATH . '/www/data/liveproject/liveprojectname.txt');
            sql_call("UPDATE edomiAdmin.project SET live=0");
            $this->logAdd(0, "Projektaktivierung: " . ajaxValueHTML($prjData['name']) . " <span class='id'>" . $prjData['id'] . "<span>");
            $this->makeLiveProject($options);
            $this->setStatus(100);
            createInfoFile(MAIN_PATH . '/www/data/tmp/activation_report.txt', array($this->errCount, $this->errFatalCount));
            return $prjData['id'];
        } else {
            createInfoFile(MAIN_PATH . '/www/data/tmp/activation_report.txt', array(-1, -1));
            return false;
        }
    }

    public function start_visuPreview()
    {
        deleteFiles(MAIN_PATH . '/www/data/tmp/activation_status.txt');
        deleteFiles(MAIN_PATH . '/www/data/tmp/activation_report.txt');
        $this->setStatus(0);
        $prjData = sql_getValues('edomiAdmin.project', '*', 'edit=1');
        if ($prjData !== false && sql_getCount('edomiLive.visu', 'id>0') > 0) {
            procStatus_setControl(7, 3);
            if (procStatus_wait(7, 19, 1, 30, false)) {
                $this->logAdd(0, "Visuaktivierung: " . ajaxValueHTML($prjData['name']) . " <span class='id'>" . $prjData['id'] . "<span>");
                $this->makeVisu();
                $this->setStatus(100);
                createInfoFile(MAIN_PATH . '/www/data/tmp/activation_report.txt', array($this->errCount, $this->errFatalCount));
                procStatus_setControl(7, 2);
                return true;
            } else {
                createInfoFile(MAIN_PATH . '/www/data/tmp/activation_report.txt', array(-1, -1));
                return false;
            }
        } else {
            createInfoFile(MAIN_PATH . '/www/data/tmp/activation_report.txt', array(-1, -1));
            return false;
        }
    }

    private function setStatus($percent)
    {
        createInfoFile(MAIN_PATH . '/www/data/tmp/activation_status.txt', array($percent));
    }

    private function setStatusElements()
    {
        $this->setStatus(intval((50 / $this->statusMax) * $this->statusCurrent));
        $this->statusCurrent++;
    }

    private function logAdd($level, $n, $err = false)
    {
        writeToTmpLog($level, $n, $err);
    }

    private function makeLiveProject($options)
    {
        $this->logAdd(0, 'Vorbereitungen');
        if ($options[0] == 1) {
            $this->logAdd(1, 'Live-Projekt vollständig gelöscht');
        } else {
            if ($options[1] == 1) {
                $this->logAdd(1, 'Remanentdaten löschen: Kommunikationsobjekte');
                sql_call("DROP TABLE IF EXISTS edomiLive.ko");
            }
            if ($options[2] == 1) {
                $this->logAdd(1, 'Remanentdaten löschen: Szenen');
                sql_call("DROP TABLE IF EXISTS edomiLive.sceneList");
            }
            if ($options[3] == 1) {
                $this->logAdd(1, 'Remanentdaten löschen: Anwesenheitssimulationen');
                sql_call("DROP TABLE IF EXISTS edomiLive.awsData");
            }
            if ($options[4] == 1) {
                $this->logAdd(1, 'Remanentdaten löschen: Meldungsarchive');
                sql_call("DROP TABLE IF EXISTS edomiLive.archivMsgData");
            }
            if ($options[5] == 1) {
                $this->logAdd(1, 'Remanentdaten löschen: Datenarchive');
                sql_call("DROP TABLE IF EXISTS edomiLive.archivKoData");
            }
            if ($options[6] == 1) {
                $this->logAdd(1, 'Remanentdaten löschen: Kameraarchive');
                sql_call("DROP TABLE IF EXISTS edomiLive.archivCamData");
                deleteFiles(MAIN_PATH . '/www/data/liveproject/cam/archiv/*.*');
            }
            if ($options[7] == 1) {
                $this->logAdd(1, 'Remanentdaten löschen: Zeitschaltuhren');
                sql_call("DROP TABLE IF EXISTS edomiLive.timerData");
            }
            if ($options[8] == 1) {
                $this->logAdd(1, 'Remanentdaten löschen: Anrufarchive');
                sql_call("DROP TABLE IF EXISTS edomiLive.archivPhoneData");
            }
            if ($options[9] == 1) {
                $this->logAdd(1, 'Remanentdaten löschen: Terminschaltuhren');
                sql_call("DROP TABLE IF EXISTS edomiLive.agendaData");
            }
            if ($options[10] == 1) {
                if (!isEmpty(global_dvrPath)) {
                    $this->logAdd(1, 'Remanentdaten löschen: Digitaler Videorekorder');
                    deleteFiles(global_dvrPath . '/cam-*.edomidvr');
                }
            }
            if ($options[11] == 1) {
                $this->logAdd(1, 'Remanentdaten löschen: LBS-Variablen');
                sql_call("DROP TABLE IF EXISTS edomiLive.logicElementVar");
            }
        }
        $this->statusMax = 24 + 12;
        $this->makeLiveProject_base();
        $this->makeLiveProject_ko();
        $this->makeLiveProject_visu(false);
        $this->makeLiveProject_scene();
        $this->makeLiveProject_sequence();
        $this->makeLiveProject_macro();
        $this->makeLiveProject_timer();
        $this->makeLiveProject_agenda();
        $this->makeLiveProject_ip();
        $this->makeLiveProject_cam();
        $this->makeLiveProject_camview();
        $this->makeLiveProject_aws();
        $this->makeLiveProject_archivcam();
        $this->makeLiveProject_archivko();
        $this->makeLiveProject_archivmsg();
        $this->makeLiveProject_email();
        $this->makeLiveProject_httpko();
        $this->makeLiveProject_phonebook();
        $this->makeLiveProject_phonecall();
        $this->makeLiveProject_archivphone();
        $this->makeLiveProject_ir();
        $this->makeLiveProject_dvr();
        $this->makeLiveProject_lbs();
        $this->makeLiveProject_logic();
        if ($this->errFatalCount > 0) {
            sql_call("DROP TABLE IF EXISTS edomiLive.logicElement");
        } else {
            sql_call("UPDATE edomiAdmin.project SET livedate=" . sql_getNow() . ",live=1 WHERE (edit=1)");
            $tmp = sql_getValues('edomiAdmin.project', '*', 'live=1');
            if ($tmp !== false) {
                createInfoFile(MAIN_PATH . '/www/data/liveproject/liveprojectname.txt', array($tmp['id'], $tmp['name'], $tmp['livedate']));
            }
        }
    }

    private function makeVisu()
    {
        $this->statusMax = 12;
        $this->makeLiveProject_visu(true);
    }

    private function makeLiveProject_base()
    {
        $this->logAdd(0, 'Basisdaten');
        sql_call("DROP TABLE IF EXISTS edomiLive.root");
        sql_call("CREATE TABLE edomiLive.root LIKE edomiProject.editRoot");
        sql_call("INSERT INTO edomiLive.root SELECT * FROM edomiProject.editRoot");
        sql_call("OPTIMIZE TABLE edomiProject.editRoot");
        sql_call("OPTIMIZE TABLE edomiLive.root");
        $this->setStatusElements();
    }

    private function makeLiveProject_ko()
    {
        $this->logAdd(0, 'Kommunikationsobjekte');
        sql_call("CREATE TABLE IF NOT EXISTS edomiLive.ko LIKE edomiProject.editKo");
        $c1 = 0;
        $ss1 = sql_call("SELECT * FROM edomiProject.editKo ORDER BY id ASC");
        while ($src = sql_result($ss1)) {
            $ss2 = sql_call("SELECT * FROM edomiLive.ko WHERE (id=" . $src['id'] . ")");
            if ($dest = sql_result($ss2)) {
                if ($src['remanent'] == 1 && $dest['remanent'] == 1) {
                    sql_call("UPDATE edomiLive.ko SET 
						name='" . sql_encodeValue($src['name']) . "',
						folderid='" . $src['folderid'] . "',
						ga=" . sql_encodeValue($src['ga'], true) . ",
						gatyp='" . $src['gatyp'] . "',
						valuetyp='" . $src['valuetyp'] . "',
						defaultvalue=" . sql_encodeValue($src['defaultvalue'], true) . ",
						endvalue=" . sql_encodeValue($src['endvalue'], true) . ",
						initscan='" . $src['initscan'] . "',
						initsend='" . $src['initsend'] . "',
						endsend='" . $src['endsend'] . "',
						requestable='" . $src['requestable'] . "',
						remanent='" . $src['remanent'] . "',
						prio='" . $src['prio'] . "',
						vmin=" . sql_encodeValue($src['vmin'], true) . ",
						vmax=" . sql_encodeValue($src['vmax'], true) . ",
						vstep=" . sql_encodeValue($src['vstep'], true) . ",
						vlist=" . sql_encodeValue($src['vlist'], true) . ",
						vcsv=" . sql_encodeValue($src['vcsv'], true) . ",
						text=" . sql_encodeValue($src['text'], true) . "
					WHERE id=" . $dest['id']);
                } else {
                    sql_call("UPDATE edomiLive.ko SET 
						name='" . sql_encodeValue($src['name']) . "',
						folderid='" . $src['folderid'] . "',
						ga=" . sql_encodeValue($src['ga'], true) . ",
						gatyp='" . $src['gatyp'] . "',
						valuetyp='" . $src['valuetyp'] . "',
						value=" . sql_encodeValue($src['value'], true) . ",
						defaultvalue=" . sql_encodeValue($src['defaultvalue'], true) . ",
						endvalue=" . sql_encodeValue($src['endvalue'], true) . ",
						initscan='" . $src['initscan'] . "',
						initsend='" . $src['initsend'] . "',
						endsend='" . $src['endsend'] . "',
						requestable='" . $src['requestable'] . "',
						remanent='" . $src['remanent'] . "',
						prio='" . $src['prio'] . "',
						vmin=" . sql_encodeValue($src['vmin'], true) . ",
						vmax=" . sql_encodeValue($src['vmax'], true) . ",
						vstep=" . sql_encodeValue($src['vstep'], true) . ",
						vlist=" . sql_encodeValue($src['vlist'], true) . ",
						vcsv=" . sql_encodeValue($src['vcsv'], true) . ",
						text=" . sql_encodeValue($src['text'], true) . "
					WHERE id=" . $dest['id']);
                }
            } else {
                sql_call("INSERT INTO edomiLive.ko SELECT * FROM edomiProject.editKo WHERE id=" . $src['id']);
                $c1++;
            }
        }
        $c2 = 0;
        $ss1 = sql_call("SELECT id FROM edomiLive.ko ORDER BY id ASC");
        while ($src = sql_result($ss1)) {
            $ss2 = sql_call("SELECT id FROM edomiProject.editKo WHERE (id=" . $src['id'] . ")");
            if (!($dest = sql_result($ss2))) {
                sql_call("DELETE FROM edomiLive.ko WHERE id=" . $src['id']);
                $c2++;
            }
        }
        sql_call("OPTIMIZE TABLE edomiLive.ko");
        if ($c1 > 0 || $c2 > 0) {
            $this->logAdd(1, $c1 . ' eingefügt / ' . $c2 . ' entfernt');
        }
        $this->setStatusElements();
    }

    private function makeLiveProject_visu($visuPreview)
    {
        $this->logAdd(0, 'Visualisierungen');
        global $global_dptData;
        $this->logAdd(1, 'Visuelemente einlesen');
        $this->setStatusElements();
        $tmp = vse_importAll(2, true);
        $this->errCount += $tmp[1];
        if (file_exists(MAIN_PATH . '/www/admin/vse/vse_include_admin.php')) {
            require(MAIN_PATH . '/www/admin/vse/vse_include_admin.php');
        } else {
            $this->logAdd(1, 'keine Visuelement-Definitionen vorhanden!', true);
            $this->errCount++;
        }
        $this->logAdd(1, 'Visualisierungen');
        $this->setStatusElements();
        sql_call("DROP TABLE IF EXISTS edomiLive.visu");
        sql_call("CREATE TABLE edomiLive.visu LIKE edomiProject.editVisu");
        sql_call("INSERT INTO edomiLive.visu SELECT * FROM edomiProject.editVisu");
        sql_call("DROP TABLE IF EXISTS edomiLive.visuMeta");
        sql_call("CREATE TABLE edomiLive.visuMeta (
			visuid BIGINT UNSIGNED DEFAULT NULL,
			typ BIGINT UNSIGNED DEFAULT 0,
			id BIGINT UNSIGNED DEFAULT NULL,
			KEY (visuid),
			KEY (typ),
			KEY (id)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1");
        $this->logAdd(1, 'Visuaccounts');
        $this->setStatusElements();
        sql_call("DROP TABLE IF EXISTS edomiLive.visuUser");
        sql_call("CREATE TABLE edomiLive.visuUser LIKE edomiProject.editVisuUser");
        sql_call("INSERT INTO edomiLive.visuUser SELECT * FROM edomiProject.editVisuUser");
        sql_call("CREATE TABLE IF NOT EXISTS edomiLive.visuUserList LIKE edomiProject.editVisuUserList");
        $ss1 = sql_call("SELECT id FROM edomiProject.editVisuUserList ORDER BY id ASC");
        while ($src = sql_result($ss1)) {
            $ss2 = sql_call("SELECT id FROM edomiLive.visuUserList WHERE (id=" . $src['id'] . ")");
            if ($dest = sql_result($ss2)) {
                sql_call("UPDATE edomiLive.visuUserList AS a,edomiProject.editVisuUserList AS b	
					SET a.targetid=b.targetid,a.visuid=b.visuid,a.defaultpageid=b.defaultpageid,a.sspageid=b.sspageid,a.logindate=b.logindate,a.logoutdate=b.logoutdate,a.actiondate=b.actiondate,a.loginip=b.loginip,a.sid=b.sid 
					WHERE (a.id=" . $dest['id'] . " AND a.id=b.id) AND (a.targetid<>b.targetid OR a.visuid<>b.visuid)");
                sql_call("UPDATE edomiLive.visuUserList AS a,edomiProject.editVisuUserList AS b	SET a.defaultpageid=b.defaultpageid,a.sspageid=b.sspageid WHERE (a.id=" . $dest['id'] . " AND a.id=b.id)");
            } else {
                sql_call("INSERT INTO edomiLive.visuUserList SELECT * FROM edomiProject.editVisuUserList WHERE id=" . $src['id']);
            }
        }
        sql_close($ss1);
        $ss1 = sql_call("SELECT id FROM edomiLive.visuUserList ORDER BY id ASC");
        while ($src = sql_result($ss1)) {
            $ss2 = sql_call("SELECT id FROM edomiProject.editVisuUserList WHERE (id=" . $src['id'] . ")");
            if (!($dest = sql_result($ss2))) {
                sql_call("DELETE FROM edomiLive.visuUserList WHERE id=" . $src['id']);
            }
        }
        sql_close($ss1);
        $ss1 = sql_call("SELECT * FROM edomiLive.visu");
        while ($n = sql_result($ss1)) {
            sql_call("UPDATE edomiLive.visuUserList	SET defaultpageid=" . (($n['defaultpageid'] > 0) ? $n['defaultpageid'] : 0) . " WHERE visuid=" . $n['id'] . " AND (defaultpageid IS NULL OR NOT (defaultpageid>0))");
            sql_call("UPDATE edomiLive.visuUserList	SET sspageid=" . (($n['sspageid'] > 0) ? $n['sspageid'] : 0) . " WHERE visuid=" . $n['id'] . " AND (sspageid IS NULL OR NOT (sspageid>0))");
        }
        sql_close($ss1);
        $this->logAdd(1, 'Farben, Animationen, Schriftarten, Töne, Bilder, Formatierungen');
        $this->setStatusElements();
        sql_call("DROP TABLE IF EXISTS edomiLive.visuFGcol");
        sql_call("CREATE TABLE edomiLive.visuFGcol LIKE edomiProject.editVisuFGcol");
        sql_call("INSERT INTO edomiLive.visuFGcol SELECT * FROM edomiProject.editVisuFGcol");
        sql_call("DROP TABLE IF EXISTS edomiLive.visuBGcol");
        sql_call("CREATE TABLE edomiLive.visuBGcol LIKE edomiProject.editVisuBGcol");
        sql_call("INSERT INTO edomiLive.visuBGcol SELECT * FROM edomiProject.editVisuBGcol");
        sql_call("DROP TABLE IF EXISTS edomiLive.visuAnim");
        sql_call("CREATE TABLE edomiLive.visuAnim LIKE edomiProject.editVisuAnim");
        sql_call("INSERT INTO edomiLive.visuAnim SELECT * FROM edomiProject.editVisuAnim");
        sql_call("DROP TABLE IF EXISTS edomiLive.visuFont");
        sql_call("CREATE TABLE edomiLive.visuFont LIKE edomiProject.editVisuFont");
        sql_call("INSERT INTO edomiLive.visuFont SELECT * FROM edomiProject.editVisuFont");
        sql_call("DROP TABLE IF EXISTS edomiLive.visuSnd");
        sql_call("CREATE TABLE edomiLive.visuSnd LIKE edomiProject.editVisuSnd");
        sql_call("INSERT INTO edomiLive.visuSnd SELECT * FROM edomiProject.editVisuSnd");
        sql_call("DROP TABLE IF EXISTS edomiLive.visuImg");
        sql_call("CREATE TABLE edomiLive.visuImg LIKE edomiProject.editVisuImg");
        sql_call("INSERT INTO edomiLive.visuImg SELECT * FROM edomiProject.editVisuImg");
        sql_call("DROP TABLE IF EXISTS edomiLive.visuFormat");
        sql_call("CREATE TABLE edomiLive.visuFormat LIKE edomiProject.editVisuFormat");
        sql_call("INSERT INTO edomiLive.visuFormat SELECT * FROM edomiProject.editVisuFormat");
        $ss1 = sql_call("
			SELECT DISTINCT(cmdid2) FROM edomiProject.editLogicCmdList WHERE ((cmd=24 OR cmd=25) AND cmdid2>0) UNION 
			SELECT DISTINCT(cmdid2) FROM edomiProject.editVisuCmdList WHERE ((cmd=24 OR cmd=25) AND cmdid2>0) UNION 
			SELECT DISTINCT(cmdid2) FROM edomiProject.editMacroCmdList WHERE ((cmd=24 OR cmd=25) AND cmdid2>0) UNION 
			SELECT DISTINCT(cmdid2) FROM edomiProject.editSequenceCmdList WHERE ((cmd=24 OR cmd=25) AND cmdid2>0)
		");
        while ($tmp = sql_result($ss1)) {
            if ($tmp['cmdid2'] > 0 && ($n = sql_getValues('edomiProject.editVisuSnd', '*', 'id=' . $tmp['cmdid2']))) {
                class_projectActivation_visuAddMeta(0, 29, $n['id']);
            }
        }
        $ss1 = sql_call("SELECT * FROM edomiLive.visuFormat WHERE imgid>0");
        while ($n = sql_result($ss1)) {
            class_projectActivation_visuAddMeta(0, 28, $n['imgid']);
        }
        sql_close($ss1);
        $this->logAdd(1, 'Visuseiten und Visuelemente');
        $this->setStatusElements();
        sql_call("DROP TABLE IF EXISTS edomiLive.visuPage");
        sql_call("CREATE TABLE edomiLive.visuPage LIKE edomiProject.editVisuPage");
        sql_call("INSERT INTO edomiLive.visuPage SELECT * FROM edomiProject.editVisuPage");
        $ss1 = sql_call("SELECT * FROM edomiProject.editVisuPage WHERE bgimg>0");
        while ($n = sql_result($ss1)) {
            class_projectActivation_visuAddMeta($n['visuid'], 28, $n['bgimg']);
        }
        sql_close($ss1);
        sql_call("DROP TABLE IF EXISTS edomiLive.visuElement");
        sql_call("CREATE TABLE edomiLive.visuElement LIKE edomiProject.editVisuElement");
        sql_call("INSERT INTO edomiLive.visuElement SELECT * FROM edomiProject.editVisuElement");
        $tmp = sql_getCount('edomiLive.visuElement', 'controltyp<>0 AND controltyp NOT IN (SELECT id FROM edomiProject.editVisuElementDef WHERE errcount=0)');
        if ($tmp > 0) {
            $this->logAdd(2, $tmp . ' fehlerhafte Visuelemente entfernt', true);
            $this->errCount++;
        }
        sql_call("DELETE FROM edomiLive.visuElement WHERE controltyp<>0 AND controltyp NOT IN (SELECT id FROM edomiProject.editVisuElementDef WHERE errcount=0)");
        sql_call("DROP TABLE IF EXISTS edomiLive.visuCmdList");
        sql_call("CREATE TABLE edomiLive.visuCmdList LIKE edomiProject.editVisuCmdList");
        sql_call("INSERT INTO edomiLive.visuCmdList SELECT * FROM edomiProject.editVisuCmdList");
        $ss1 = sql_call("SELECT id FROM edomiLive.visuElement WHERE controltyp=0 AND (gaid IS NULL OR gaid=0 OR text IS NULL OR text='')");
        while ($n = sql_result($ss1)) {
            sql_call("UPDATE edomiLive.visuElement SET groupid=0 WHERE groupid=" . $n['id']);
        }
        sql_call("DELETE FROM edomiLive.visuElement WHERE controltyp=0 AND (gaid IS NULL OR gaid=0 OR text IS NULL OR text='')");
        sql_call("UPDATE edomiLive.visuPage SET includeid=0 WHERE pagetyp>0");
        $ss1 = sql_call("SELECT * FROM edomiLive.visuElement");
        while ($item = sql_result($ss1)) {
            $ss2 = sql_call("SELECT COUNT(*) AS anz1 FROM edomiLive.visuCmdList WHERE (targetid=" . $item['id'] . ")");
            if ($n = sql_result($ss2)) {
                if ($n['anz1'] > 0) {
                    sql_call("UPDATE edomiLive.visuElement SET hascmd=1 WHERE (id=" . $item['id'] . ")");
                }
            }
            if ($item['controltyp'] > 0) {
                $vseACTIVATION = 'PHP_VSE_' . $item['controltyp'] . '_ACTIVATION_PHP';
                if (function_exists($vseACTIVATION)) {
                    $vseACTIVATION($item);
                }
            }
        }
        $this->logAdd(1, 'Designs');
        $this->setStatusElements();
        sql_call("DROP TABLE IF EXISTS edomiLive.visuElementStyle");
        sql_call("CREATE TABLE edomiLive.visuElementStyle (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			targetid BIGINT UNSIGNED DEFAULT NULL,
			visuid BIGINT UNSIGNED DEFAULT NULL,
			controltyp BIGINT UNSIGNED DEFAULT NULL,
			fromvalue VARCHAR(500) DEFAULT NULL,
			tovalue VARCHAR(500) DEFAULT NULL,
			styletyp TINYINT UNSIGNED DEFAULT 0,
			css VARCHAR(20000) DEFAULT NULL,
			text VARCHAR(10000) DEFAULT NULL,
			initonly TINYINT UNSIGNED DEFAULT 0,
			PRIMARY KEY (id),
			KEY (targetid),
			KEY (visuid),
			KEY (fromvalue),
			KEY (tovalue),
			KEY (styletyp)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1");
        $ss1 = sql_call("SELECT * FROM edomiLive.visuElement");
        while ($item = sql_result($ss1)) {
            $ss2 = sql_call("SELECT * FROM edomiProject.editVisuElementDesign WHERE (targetid=" . $item['id'] . ") ORDER BY id ASC");
            while ($design = sql_result($ss2)) {
                if ($design['defid'] > 0) {
                    $tmp_styletyp = $design['styletyp'];
                    $ss3 = sql_call("SELECT * FROM edomiProject.editVisuElementDesignDef WHERE (id=" . $design['defid'] . ")");
                    if ($tmp = sql_result($ss3)) {
                        for ($t = 1; $t <= 48; $t++) {
                            if (!isEmpty($design['s' . $t])) {
                                $tmp['s' . $t] = $design['s' . $t];
                            }
                        }
                        $tmp['styletyp'] = $tmp_styletyp;
                        $design = $tmp;
                    }
                    sql_close($ss3);
                }
                $this->visuInsertElementStyle($item, $design);
            }
            sql_close($ss2);
            $ss2 = sql_call("SELECT COUNT(*) AS anz1 FROM edomiLive.visuElementStyle WHERE (targetid=" . $item['id'] . " AND (fromvalue IS NULL) AND (tovalue IS NULL))");
            if ($n = sql_result($ss2)) {
                if ($n['anz1'] == 0) {
                    $design['defid'] = 0;
                    $design['styletyp'] = 0;
                    for ($t = 1; $t <= 48; $t++) {
                        $design['s' . $t] = null;
                    }
                    $this->visuInsertElementStyle($item, $design);
                }
            }
            sql_close($ss2);
        }
        sql_close($ss1);
        sql_call("UPDATE edomiLive.visuElement SET text='' WHERE controltyp<>0");
        $this->logAdd(1, 'Bilddateien');
        $this->setStatusElements();
        deleteFiles(MAIN_PATH . '/www/data/liveproject/visu/img/img-*.*');
        $ss1 = sql_call("SELECT id FROM edomiLive.visuMeta WHERE typ=28 GROUP BY id");
        while ($meta = sql_result($ss1)) {
            if ($n = sql_getValues('edomiLive.visuImg', '*', 'id=' . $meta['id'])) {
                $fn = 'img-' . $n['id'] . '.' . $n['suffix'];
                if (file_exists(MAIN_PATH . '/www/data/project/visu/img/' . $fn)) {
                    copy(MAIN_PATH . '/www/data/project/visu/img/' . $fn, MAIN_PATH . '/www/data/liveproject/visu/img/' . $fn);
                }
            }
        }
        sql_close($ss1);
        $this->logAdd(1, 'Schriftartdateien');
        $this->setStatusElements();
        deleteFiles(MAIN_PATH . '/www/data/liveproject/visu/etc/font-*.*');
        $ss1 = sql_call("SELECT id FROM edomiLive.visuMeta WHERE typ=150 GROUP BY id");
        while ($meta = sql_result($ss1)) {
            if ($n = sql_getValues('edomiLive.visuFont', '*', 'id=' . $meta['id'])) {
                if ($n['fonttyp'] > 0) {
                    $fn = 'font-' . $n['id'] . '.ttf';
                    if (file_exists(MAIN_PATH . '/www/data/project/visu/etc/' . $fn)) {
                        copy(MAIN_PATH . '/www/data/project/visu/etc/' . $fn, MAIN_PATH . '/www/data/liveproject/visu/etc/' . $fn);
                    }
                }
            }
        }
        sql_close($ss1);
        $this->logAdd(1, 'Tondateien');
        $this->setStatusElements();
        deleteFiles(MAIN_PATH . '/www/data/liveproject/visu/etc/snd-*.*');
        $ss1 = sql_call("SELECT id FROM edomiLive.visuMeta WHERE typ=29 GROUP BY id");
        while ($meta = sql_result($ss1)) {
            if ($n = sql_getValues('edomiLive.visuSnd', '*', 'id=' . $meta['id'])) {
                $fn = 'snd-' . $n['id'] . '.mp3';
                if (file_exists(MAIN_PATH . '/www/data/project/visu/etc/' . $fn)) {
                    copy(MAIN_PATH . '/www/data/project/visu/etc/' . $fn, MAIN_PATH . '/www/data/liveproject/visu/etc/' . $fn);
                }
            }
        }
        sql_close($ss1);
        $this->logAdd(1, 'Ton- und Sprachausgabe aktivieren');
        $this->setStatusElements();
        sql_call("UPDATE edomiLive.visu SET hassound=0,hasspeech=0");
        sql_call("UPDATE edomiLive.visuUserList SET hassound=0,hasspeech=0");
        $ss1 = sql_call("SELECT id FROM edomiLive.visu");
        while ($n = sql_result($ss1)) {
            $tmp = sql_getCount('edomiLive.visuElement AS a,edomiProject.editVisuElementDef AS b', 'a.visuid=' . $n['id'] . ' AND a.controltyp=b.id AND b.flagsound=1');
            $tmp += sql_getCount('edomiProject.editLogicCmdList', "(cmd=24 AND cmdid1='" . $n['id'] . "')");
            $tmp += sql_getCount('edomiProject.editVisuCmdList', "(cmd=24 AND cmdid1='" . $n['id'] . "')");
            $tmp += sql_getCount('edomiProject.editMacroCmdList', "(cmd=24 AND cmdid1='" . $n['id'] . "')");
            $tmp += sql_getCount('edomiProject.editSequenceCmdList', "(cmd=24 AND cmdid1='" . $n['id'] . "')");
            if ($tmp > 0) {
                sql_call("UPDATE edomiLive.visu SET hassound=1 WHERE id=" . $n['id']);
            }
            $tmp = sql_getCount('edomiLive.visuElement AS a,edomiProject.editVisuElementDef AS b', 'a.visuid=' . $n['id'] . ' AND a.controltyp=b.id AND b.flagspeech=1');
            $tmp += sql_getCount('edomiProject.editLogicCmdList', "(cmd=26 AND cmdid1='" . $n['id'] . "')");
            $tmp += sql_getCount('edomiProject.editVisuCmdList', "(cmd=26 AND cmdid1='" . $n['id'] . "')");
            $tmp += sql_getCount('edomiProject.editMacroCmdList', "(cmd=26 AND cmdid1='" . $n['id'] . "')");
            $tmp += sql_getCount('edomiProject.editSequenceCmdList', "(cmd=26 AND cmdid1='" . $n['id'] . "')");
            if ($tmp > 0) {
                sql_call("UPDATE edomiLive.visu SET hasspeech=1 WHERE id=" . $n['id']);
            }
        }
        $ss1 = sql_call("SELECT id,targetid FROM edomiLive.visuUserList");
        while ($n = sql_result($ss1)) {
            $tmp = sql_getCount('edomiProject.editLogicCmdList', "(cmd=25 AND cmdid1='" . $n['targetid'] . "')");
            $tmp += sql_getCount('edomiProject.editVisuCmdList', "(cmd=25 AND cmdid1='" . $n['targetid'] . "')");
            $tmp += sql_getCount('edomiProject.editMacroCmdList', "(cmd=25 AND cmdid1='" . $n['targetid'] . "')");
            $tmp += sql_getCount('edomiProject.editSequenceCmdList', "OR (cmd=25 AND cmdid1='" . $n['targetid'] . "')");
            if ($tmp > 0) {
                sql_call("UPDATE edomiLive.visuUserList SET hassound=1 WHERE id=" . $n['id']);
            }
            $tmp = sql_getCount('edomiProject.editLogicCmdList', "(cmd=27 AND cmdid1='" . $n['targetid'] . "')");
            $tmp += sql_getCount('edomiProject.editVisuCmdList', "(cmd=27 AND cmdid1='" . $n['targetid'] . "')");
            $tmp += sql_getCount('edomiProject.editMacroCmdList', "(cmd=27 AND cmdid1='" . $n['targetid'] . "')");
            $tmp += sql_getCount('edomiProject.editSequenceCmdList', "(cmd=27 AND cmdid1='" . $n['targetid'] . "')");
            if ($tmp > 0) {
                sql_call("UPDATE edomiLive.visuUserList SET hasspeech=1 WHERE id=" . $n['id']);
            }
        }
        sql_call("UPDATE edomiLive.visu SET preview=" . (($visuPreview) ? 1 : 0));
        $this->logAdd(1, 'Diagramme');
        $this->setStatusElements();
        sql_call("DROP TABLE IF EXISTS edomiLive.chart");
        sql_call("CREATE TABLE edomiLive.chart LIKE edomiProject.editChart");
        sql_call("INSERT INTO edomiLive.chart SELECT * FROM edomiProject.editChart");
        sql_call("DROP TABLE IF EXISTS edomiLive.chartList");
        sql_call("CREATE TABLE edomiLive.chartList LIKE edomiProject.editChartList");
        sql_call("INSERT INTO edomiLive.chartList SELECT * FROM edomiProject.editChartList");
        $ss1 = sql_call("SELECT * FROM edomiLive.chartList");
        while ($item = sql_result($ss1)) {
            if ($item['ystyle'] == 1) {
                $item['titel'] = sql_getValue('edomiProject.editArchivKo', 'name', "id='" . $item['archivkoid'] . "'");
                sql_call("UPDATE edomiLive.chartList SET titel='" . sql_encodeValue($item['titel']) . "' WHERE (id=" . $item['id'] . ")");
            }
        }
        sql_call("ALTER TABLE edomiLive.visuFGcol DROP folderid");
        sql_call("ALTER TABLE edomiLive.visuFGcol DROP name");
        sql_call("ALTER TABLE edomiLive.visuBGcol DROP folderid");
        sql_call("ALTER TABLE edomiLive.visuBGcol DROP name");
        sql_call("ALTER TABLE edomiLive.visuAnim DROP name");
        sql_call("ALTER TABLE edomiLive.visuAnim DROP folderid");
        sql_call("ALTER TABLE edomiLive.visuFont DROP name");
        sql_call("ALTER TABLE edomiLive.visuFont DROP folderid");
        sql_call("ALTER TABLE edomiLive.visuSnd DROP name");
        sql_call("ALTER TABLE edomiLive.visuSnd DROP folderid");
        sql_call("ALTER TABLE edomiLive.visuImg DROP name");
        sql_call("ALTER TABLE edomiLive.visuImg DROP folderid");
        sql_call("ALTER TABLE edomiLive.visuFormat DROP name");
        sql_call("ALTER TABLE edomiLive.visuFormat DROP folderid");
        sql_call("ALTER TABLE edomiLive.visu DROP folderid");
        sql_call("ALTER TABLE edomiLive.visuPage DROP name");
        sql_call("ALTER TABLE edomiLive.visuPage DROP folderid");
        sql_call("ALTER TABLE edomiLive.visuPage DROP xgrid");
        sql_call("ALTER TABLE edomiLive.visuPage DROP ygrid");
        sql_call("ALTER TABLE edomiLive.visuPage DROP outlinecolorid");
        sql_call("ALTER TABLE edomiLive.visuElement DROP name");
        sql_call("ALTER TABLE edomiLive.visuElement DROP tmp");
        sql_call("ALTER TABLE edomiLive.visuElement DROP tmp2");
        sql_call("OPTIMIZE TABLE edomiLive.visuFGcol");
        sql_call("OPTIMIZE TABLE edomiLive.visuBGcol");
        sql_call("OPTIMIZE TABLE edomiLive.visuAnim");
        sql_call("OPTIMIZE TABLE edomiLive.visuFont");
        sql_call("OPTIMIZE TABLE edomiLive.visuSnd");
        sql_call("OPTIMIZE TABLE edomiLive.visuImg");
        sql_call("OPTIMIZE TABLE edomiLive.visuFormat");
        sql_call("OPTIMIZE TABLE edomiLive.visuUser");
        sql_call("OPTIMIZE TABLE edomiLive.visuUserList");
        sql_call("OPTIMIZE TABLE edomiLive.visuElementStyle");
        sql_call("OPTIMIZE TABLE edomiLive.visuElement");
        sql_call("OPTIMIZE TABLE edomiLive.visuCmdList");
        sql_call("OPTIMIZE TABLE edomiLive.visuPage");
        sql_call("OPTIMIZE TABLE edomiLive.visu");
        sql_call("OPTIMIZE TABLE edomiLive.chart");
        sql_call("OPTIMIZE TABLE edomiLive.chartList");
        $this->logAdd(1, 'Überprüfung');
        $this->setStatusElements();
        $ss1 = sql_call("SELECT * FROM edomiLive.visuUserList ORDER BY id ASC");
        while ($n = sql_result($ss1)) {
            if (!($n['defaultpageid'] >= 1)) {
                $this->logAdd(2, 'Hinweis: Visualisierung ' . $n['visuid'] . ', Visuaccount ' . $n['targetid'] . ': Keine Startseite zugewiesen', true);
                $this->errCount++;
            }
        }
        $ss1 = sql_call("SELECT id FROM edomiLive.visu");
        while ($n = sql_result($ss1)) {
            $ss2 = sql_call("SELECT id FROM edomiLive.visuUserList WHERE (visuid=" . $n['id'] . ")");
            if (!sql_result($ss2)) {
                $this->logAdd(2, 'Hinweis: Visualisierung ' . $n['id'] . ': Kein Visuaccount zugewiesen', true);
                $this->errCount++;
            }
        }
        $this->setStatusElements();
    }

    private function makeLiveProject_scene()
    {
        $this->logAdd(0, 'Szenen');
        sql_call("DROP TABLE IF EXISTS edomiLive.scene");
        sql_call("CREATE TABLE edomiLive.scene LIKE edomiProject.editScene");
        sql_call("INSERT INTO edomiLive.scene SELECT * FROM edomiProject.editScene");
        sql_call("CREATE TABLE IF NOT EXISTS edomiLive.sceneList LIKE edomiProject.editSceneList");
        $c1 = 0;
        $ss1 = sql_call("SELECT id FROM edomiProject.editSceneList ORDER BY id ASC");
        while ($src = sql_result($ss1)) {
            $ss2 = sql_call("SELECT id FROM edomiLive.sceneList WHERE (id=" . $src['id'] . ")");
            if ($dest = sql_result($ss2)) {
                sql_call("UPDATE edomiLive.sceneList AS a,edomiProject.editSceneList AS b
					SET a.gaid=b.gaid,a.gavalue=b.gavalue,a.learngaid=b.learngaid,a.valuegaid=b.valuegaid
					WHERE (a.id=" . $dest['id'] . " AND a.id=b.id)
					AND (a.gaid<>b.gaid OR a.learngaid<>b.learngaid OR b.learngaid=0 OR (b.learngaid IS NULL) OR (a.learngaid IS NULL AND b.learngaid>0))
					");
                sql_call("UPDATE edomiLive.sceneList AS a,edomiProject.editSceneList AS b
					SET a.valuegaid=b.valuegaid
					WHERE (a.id=" . $dest['id'] . " AND a.id=b.id)
					AND (a.valuegaid<>b.valuegaid OR (a.valuegaid IS NULL AND b.valuegaid>0) OR (b.valuegaid IS NULL AND a.valuegaid>0))
					");
            } else {
                sql_call("INSERT INTO edomiLive.sceneList SELECT * FROM edomiProject.editSceneList WHERE id=" . $src['id']);
                $c1++;
            }
        }
        $c2 = 0;
        $ss1 = sql_call("SELECT id FROM edomiLive.sceneList ORDER BY id ASC");
        while ($src = sql_result($ss1)) {
            $ss2 = sql_call("SELECT id FROM edomiProject.editSceneList WHERE (id=" . $src['id'] . ")");
            if (!($dest = sql_result($ss2))) {
                sql_call("DELETE FROM edomiLive.sceneList WHERE id=" . $src['id']);
                $c2++;
            }
        }
        sql_call("OPTIMIZE TABLE edomiLive.scene");
        sql_call("OPTIMIZE TABLE edomiLive.sceneList");
        if ($c1 > 0 || $c2 > 0) {
            $this->logAdd(1, $c1 . ' eingefügt / ' . $c2 . ' entfernt');
        }
        $this->setStatusElements();
    }

    private function makeLiveProject_sequence()
    {
        $this->logAdd(0, 'Sequenzen');
        sql_call("DROP TABLE IF EXISTS edomiLive.sequence");
        sql_call("CREATE TABLE edomiLive.sequence LIKE edomiProject.editSequence");
        sql_call("INSERT INTO edomiLive.sequence SELECT * FROM edomiProject.editSequence");
        sql_call("DROP TABLE IF EXISTS edomiLive.sequenceCmdList");
        sql_call("CREATE TABLE edomiLive.sequenceCmdList LIKE edomiProject.editSequenceCmdList");
        sql_call("INSERT INTO edomiLive.sequenceCmdList SELECT * FROM edomiProject.editSequenceCmdList");
        sql_call("OPTIMIZE TABLE edomiLive.sequence");
        sql_call("OPTIMIZE TABLE edomiLive.sequenceCmdList");
        $this->setStatusElements();
    }

    private function makeLiveProject_macro()
    {
        $this->logAdd(0, 'Makros');
        sql_call("DROP TABLE IF EXISTS edomiLive.macro");
        sql_call("CREATE TABLE edomiLive.macro LIKE edomiProject.editMacro");
        sql_call("INSERT INTO edomiLive.macro SELECT * FROM edomiProject.editMacro");
        sql_call("DROP TABLE IF EXISTS edomiLive.macroCmdList");
        sql_call("CREATE TABLE edomiLive.macroCmdList LIKE edomiProject.editMacroCmdList");
        sql_call("INSERT INTO edomiLive.macroCmdList SELECT * FROM edomiProject.editMacroCmdList");
        sql_call("OPTIMIZE TABLE edomiLive.macro");
        sql_call("OPTIMIZE TABLE edomiLive.macroCmdList");
        $this->setStatusElements();
    }

    private function makeLiveProject_timer()
    {
        $this->logAdd(0, 'Zeitschaltuhren');
        sql_call("DROP TABLE IF EXISTS edomiLive.timer");
        sql_call("CREATE TABLE edomiLive.timer LIKE edomiProject.editTimer");
        sql_call("INSERT INTO edomiLive.timer SELECT * FROM edomiProject.editTimer");
        sql_call("DROP TABLE IF EXISTS edomiLive.timerMacroList");
        sql_call("CREATE TABLE edomiLive.timerMacroList LIKE edomiProject.editTimerMacroList");
        sql_call("INSERT INTO edomiLive.timerMacroList SELECT * FROM edomiProject.editTimerMacroList");
        sql_call("CREATE TABLE IF NOT EXISTS edomiLive.timerData LIKE edomiProject.editTimerData");
        $ss1 = sql_call("SELECT * FROM edomiLive.timerData WHERE (fixed=0)");
        sql_call("DELETE FROM edomiLive.timerData");
        sql_call("INSERT INTO edomiLive.timerData SELECT * FROM edomiProject.editTimerData WHERE (fixed=1)");
        $maxId = sql_getValue("edomiLive.timerData", "MAX(id)", "1=1");
        sql_call("ALTER TABLE edomiLive.timerData AUTO_INCREMENT=" . ($maxId + 1));
        while ($n = sql_result($ss1)) {
            $tmp = sql_getValue('edomiProject.editMacro', 'id', "id='" . $n['cmdid'] . "'");
            if (isEmpty($tmp)) {
                $n['cmdid'] = 0;
            }
            sql_call("INSERT INTO edomiLive.timerData (targetid,fixed,d0,d1,d2,d3,d4,d5,d6,d7,hour,minute,day1,month1,year1,day2,month2,year2,mode,cmdid) VALUES (
				" . $n['targetid'] . ",
				" . $n['fixed'] . ",
				" . $n['d0'] . ",
				" . $n['d1'] . ",
				" . $n['d2'] . ",
				" . $n['d3'] . ",
				" . $n['d4'] . ",
				" . $n['d5'] . ",
				" . $n['d6'] . ",
				" . $n['d7'] . ",
				" . $n['hour'] . ",
				" . $n['minute'] . ",
				" . sql_encodeValue($n['day1'], true) . ",
				" . sql_encodeValue($n['month1'], true) . ",
				" . sql_encodeValue($n['year1'], true) . ",
				" . sql_encodeValue($n['day2'], true) . ",
				" . sql_encodeValue($n['month2'], true) . ",
				" . sql_encodeValue($n['year2'], true) . ",
				" . $n['mode'] . ",
				" . sql_encodeValue($n['cmdid'], true) . "
			)");
        }
        $ss1 = sql_call("SELECT id FROM edomiLive.timerData WHERE targetid NOT IN (SELECT id FROM edomiLive.timer)");
        while ($src = sql_result($ss1)) {
            sql_call("DELETE FROM edomiLive.timerData WHERE (id=" . $src['id'] . ")");
        }
        sql_call("OPTIMIZE TABLE edomiLive.timer");
        sql_call("OPTIMIZE TABLE edomiLive.timerData");
        $this->setStatusElements();
    }

    private function makeLiveProject_agenda()
    {
        $this->logAdd(0, 'Terminschaltuhren');
        sql_call("DROP TABLE IF EXISTS edomiLive.agenda");
        sql_call("CREATE TABLE edomiLive.agenda LIKE edomiProject.editAgenda");
        sql_call("INSERT INTO edomiLive.agenda SELECT * FROM edomiProject.editAgenda");
        sql_call("DROP TABLE IF EXISTS edomiLive.agendaMacroList");
        sql_call("CREATE TABLE edomiLive.agendaMacroList LIKE edomiProject.editAgendaMacroList");
        sql_call("INSERT INTO edomiLive.agendaMacroList SELECT * FROM edomiProject.editAgendaMacroList");
        sql_call("CREATE TABLE IF NOT EXISTS edomiLive.agendaData LIKE edomiProject.editAgendaData");
        $ss1 = sql_call("SELECT * FROM edomiLive.agendaData WHERE (fixed=0)");
        sql_call("DELETE FROM edomiLive.agendaData");
        sql_call("INSERT INTO edomiLive.agendaData SELECT * FROM edomiProject.editAgendaData WHERE (fixed=1)");
        $maxId = sql_getValue("edomiLive.agendaData", "MAX(id)", "1=1");
        sql_call("ALTER TABLE edomiLive.agendaData AUTO_INCREMENT=" . ($maxId + 1));
        while ($n = sql_result($ss1)) {
            $tmp = sql_getValue('edomiProject.editMacro', 'id', "id='" . $n['cmdid'] . "'");
            if (isEmpty($tmp)) {
                $n['cmdid'] = 0;
            }
            sql_call("INSERT INTO edomiLive.agendaData (targetid,fixed,name,cmdid,hour,minute,date1,date2,step,unit,d7) VALUES (
				" . $n['targetid'] . ",
				" . $n['fixed'] . ",
				'" . sql_encodeValue($n['name']) . "',
				" . sql_encodeValue($n['cmdid'], true) . ",
				" . $n['hour'] . ",
				" . $n['minute'] . ",
				" . sql_encodeValue($n['date1'], true) . ",
				" . sql_encodeValue($n['date2'], true) . ",
				" . $n['step'] . ",
				" . $n['unit'] . ",
				" . $n['d7'] . "
			)");
        }
        $ss1 = sql_call("SELECT id FROM edomiLive.agendaData WHERE targetid NOT IN (SELECT id FROM edomiLive.agenda)");
        while ($src = sql_result($ss1)) {
            sql_call("DELETE FROM edomiLive.agendaData WHERE (id=" . $src['id'] . ")");
        }
        sql_call("OPTIMIZE TABLE edomiLive.agenda");
        sql_call("OPTIMIZE TABLE edomiLive.agendaData");
        $this->setStatusElements();
    }

    private function makeLiveProject_ip()
    {
        $this->logAdd(0, 'HTTP/UDP/SHELL');
        sql_call("DROP TABLE IF EXISTS edomiLive.ip");
        sql_call("CREATE TABLE edomiLive.ip LIKE edomiProject.editIp");
        sql_call("INSERT INTO edomiLive.ip SELECT * FROM edomiProject.editIp");
        sql_call("OPTIMIZE TABLE edomiLive.ip");
        $this->setStatusElements();
    }

    private function makeLiveProject_cam()
    {
        $this->logAdd(0, 'Kameras');
        sql_call("DROP TABLE IF EXISTS edomiLive.cam");
        sql_call("CREATE TABLE edomiLive.cam LIKE edomiProject.editCam");
        sql_call("INSERT INTO edomiLive.cam SELECT * FROM edomiProject.editCam");
        deleteFiles(MAIN_PATH . '/www/data/liveproject/cam/live/cam*.*');
        sql_call("OPTIMIZE TABLE edomiLive.cam");
        $this->setStatusElements();
    }

    private function makeLiveProject_camview()
    {
        $this->logAdd(0, 'Kameraansichten');
        sql_call("DROP TABLE IF EXISTS edomiLive.camView");
        sql_call("CREATE TABLE edomiLive.camView LIKE edomiProject.editCamView");
        sql_call("INSERT INTO edomiLive.camView SELECT * FROM edomiProject.editCamView");
        sql_call("OPTIMIZE TABLE edomiLive.camView");
        $this->setStatusElements();
    }

    private function makeLiveProject_aws()
    {
        $this->logAdd(0, 'Anwesenheitssimulationen');
        sql_call("DROP TABLE IF EXISTS edomiLive.aws");
        sql_call("CREATE TABLE edomiLive.aws LIKE edomiProject.editAws");
        sql_call("INSERT INTO edomiLive.aws SELECT * FROM edomiProject.editAws");
        sql_call("DROP TABLE IF EXISTS edomiLive.awsList");
        sql_call("CREATE TABLE edomiLive.awsList LIKE edomiProject.editAwsList");
        sql_call("INSERT INTO edomiLive.awsList SELECT * FROM edomiProject.editAwsList");
        sql_call("CREATE TABLE IF NOT EXISTS edomiLive.awsData LIKE edomiProject.editAwsData");
        $c2 = 0;
        $ss1 = sql_call("SELECT * FROM edomiLive.awsData");
        while ($src = sql_result($ss1)) {
            $ss2 = sql_call("SELECT id FROM edomiProject.editAwsList WHERE (targetid=" . $src['targetid'] . " AND gaid=" . $src['gaid'] . ")");
            if (!($dest = sql_result($ss2))) {
                sql_call("DELETE FROM edomiLive.awsData WHERE (targetid=" . $src['targetid'] . " AND gaid=" . $src['gaid'] . ")");
                $c2++;
            }
        }
        sql_call("OPTIMIZE TABLE edomiLive.aws");
        sql_call("OPTIMIZE TABLE edomiLive.awsList");
        sql_call("OPTIMIZE TABLE edomiLive.awsData");
        if ($c2 > 0) {
            $this->logAdd(1, $c2 . ' entfernt');
        }
        $this->setStatusElements();
    }

    private function makeLiveProject_archivcam()
    {
        $this->logAdd(0, 'Kameraarchive');
        sql_call("DROP TABLE IF EXISTS edomiLive.archivCam");
        sql_call("CREATE TABLE edomiLive.archivCam LIKE edomiProject.editArchivCam");
        sql_call("INSERT INTO edomiLive.archivCam SELECT * FROM edomiProject.editArchivCam");
        sql_call("CREATE TABLE IF NOT EXISTS edomiLive.archivCamData LIKE edomiProject.editArchivCamData");
        $c2 = 0;
        $ss1 = sql_call("SELECT * FROM edomiLive.archivCamData WHERE targetid NOT IN (SELECT id FROM edomiLive.archivCam)");
        while ($src = sql_result($ss1)) {
            $fn = MAIN_PATH . '/www/data/liveproject/cam/archiv/' . getArchivCamFilename($src['targetid'], $src['camid'], $src['datetime'], $src['ms']) . '.jpg';
            deleteFiles($fn);
            sql_call("DELETE FROM edomiLive.archivCamData WHERE (datetime='" . $src['datetime'] . "' AND targetid=" . $src['targetid'] . ")");
            $c2++;
        }
        $c3 = 0;
        $files = glob(MAIN_PATH . '/www/data/liveproject/cam/archiv/*.jpg');
        foreach ($files as $pathFn) {
            if (is_file($pathFn)) {
                $n = explode('-', basename($pathFn));
                $nn[0] = substr($n[0], 6, 100);
                $nn[1] = substr($n[1], 3, 100);
                $nn[2] = date('Y-m-d', strtotime(substr($n[2], 0, 8)));
                $nn[3] = date('H:i:s', strtotime(substr($n[2], 8, 6)));
                $nn[4] = substr($n[2], 14, 6);
                $ss1 = sql_call("SELECT targetid FROM edomiLive.archivCamData WHERE (targetid='" . $nn[0] . "' AND camid='" . $nn[1] . "' AND datetime='" . date('Y-m-d H:i:s', strtotime($nn[2] . ' ' . $nn[3])) . "' AND ms='" . $nn[4] . "')");
                if (!sql_result($ss1)) {
                    deleteFiles($pathFn);
                    $c3++;
                }
                sql_close($ss1);
            }
        }
        sql_call("OPTIMIZE TABLE edomiLive.archivCam");
        sql_call("OPTIMIZE TABLE edomiLive.archivCamData");
        if ($c2 > 0 || $c3 > 0) {
            $this->logAdd(1, $c2 . ' entfernt / ' . $c3 . ' Bilddateien ohne Datenbank-Referenz entfernt');
        }
        $this->setStatusElements();
    }

    private function makeLiveProject_archivko()
    {
        $this->logAdd(0, 'Datenarchive');
        sql_call("DROP TABLE IF EXISTS edomiLive.archivKo");
        sql_call("CREATE TABLE edomiLive.archivKo LIKE edomiProject.editArchivKo");
        sql_call("INSERT INTO edomiLive.archivKo SELECT * FROM edomiProject.editArchivKo");
        sql_call("CREATE TABLE IF NOT EXISTS edomiLive.archivKoData LIKE edomiProject.editArchivKoData");
        $c2 = 0;
        $ss1 = sql_call("SELECT targetid FROM edomiLive.archivKoData GROUP BY targetid");
        while ($src = sql_result($ss1)) {
            $ss2 = sql_call("SELECT id FROM edomiProject.editArchivKo WHERE (id=" . $src['targetid'] . ")");
            if (!($dest = sql_result($ss2))) {
                sql_call("DELETE FROM edomiLive.archivKoData WHERE (targetid=" . $src['targetid'] . ")");
                $c2++;
            }
        }
        sql_call("OPTIMIZE TABLE edomiLive.archivKo");
        sql_call("OPTIMIZE TABLE edomiLive.archivKoData");
        if ($c2 > 0) {
            $this->logAdd(1, $c2 . ' entfernt');
        }
        $this->setStatusElements();
    }

    private function makeLiveProject_archivmsg()
    {
        $this->logAdd(0, 'Meldungsarchive');
        sql_call("DROP TABLE IF EXISTS edomiLive.archivMsg");
        sql_call("CREATE TABLE edomiLive.archivMsg LIKE edomiProject.editArchivMsg");
        sql_call("INSERT INTO edomiLive.archivMsg SELECT * FROM edomiProject.editArchivMsg");
        sql_call("CREATE TABLE IF NOT EXISTS edomiLive.archivMsgData LIKE edomiProject.editArchivMsgData");
        $c2 = 0;
        $ss1 = sql_call("SELECT targetid FROM edomiLive.archivMsgData GROUP BY targetid");
        while ($src = sql_result($ss1)) {
            $ss2 = sql_call("SELECT id FROM edomiProject.editArchivMsg WHERE (id=" . $src['targetid'] . ")");
            if (!($dest = sql_result($ss2))) {
                sql_call("DELETE FROM edomiLive.archivMsgData WHERE (targetid=" . $src['targetid'] . ")");
                $c2++;
            }
        }
        sql_call("OPTIMIZE TABLE edomiLive.archivMsg");
        sql_call("OPTIMIZE TABLE edomiLive.archivMsgData");
        if ($c2 > 0) {
            $this->logAdd(1, $c2 . ' entfernt');
        }
        $this->setStatusElements();
    }

    private function makeLiveProject_email()
    {
        $this->logAdd(0, 'Emails');
        sql_call("DROP TABLE IF EXISTS edomiLive.email");
        sql_call("CREATE TABLE edomiLive.email LIKE edomiProject.editEmail");
        sql_call("INSERT INTO edomiLive.email SELECT * FROM edomiProject.editEmail");
        sql_call("OPTIMIZE TABLE edomiLive.email");
        $this->setStatusElements();
    }

    private function makeLiveProject_httpko()
    {
        $this->logAdd(0, 'Fernzugriff');
        sql_call("DROP TABLE IF EXISTS edomiLive.httpKo");
        sql_call("CREATE TABLE edomiLive.httpKo LIKE edomiProject.editHttpKo");
        sql_call("INSERT INTO edomiLive.httpKo SELECT * FROM edomiProject.editHttpKo");
        sql_call("OPTIMIZE TABLE edomiLive.httpKo");
        $this->setStatusElements();
    }

    private function makeLiveProject_phonebook()
    {
        $this->logAdd(0, 'Telefonbuch');
        sql_call("DROP TABLE IF EXISTS edomiLive.phoneBook");
        sql_call("CREATE TABLE edomiLive.phoneBook LIKE edomiProject.editPhoneBook");
        sql_call("INSERT INTO edomiLive.phoneBook SELECT * FROM edomiProject.editPhoneBook");
        sql_call("OPTIMIZE TABLE edomiLive.phoneBook");
        $this->setStatusElements();
    }

    private function makeLiveProject_phonecall()
    {
        $this->logAdd(0, 'Anruftrigger');
        sql_call("DROP TABLE IF EXISTS edomiLive.phoneCall");
        sql_call("CREATE TABLE edomiLive.phoneCall LIKE edomiProject.editPhoneCall");
        sql_call("INSERT INTO edomiLive.phoneCall SELECT * FROM edomiProject.editPhoneCall");
        sql_call("OPTIMIZE TABLE edomiLive.phoneCall");
        $this->setStatusElements();
    }

    private function makeLiveProject_archivphone()
    {
        $this->logAdd(0, 'Anrufarchive');
        sql_call("DROP TABLE IF EXISTS edomiLive.archivPhone");
        sql_call("CREATE TABLE edomiLive.archivPhone LIKE edomiProject.editArchivPhone");
        sql_call("INSERT INTO edomiLive.archivPhone SELECT * FROM edomiProject.editArchivPhone");
        sql_call("CREATE TABLE IF NOT EXISTS edomiLive.archivPhoneData LIKE edomiProject.editArchivPhoneData");
        $c2 = 0;
        $ss1 = sql_call("SELECT targetid FROM edomiLive.archivPhoneData GROUP BY targetid");
        while ($src = sql_result($ss1)) {
            $ss2 = sql_call("SELECT id FROM edomiProject.editArchivPhone WHERE (id=" . $src['targetid'] . ")");
            if (!($dest = sql_result($ss2))) {
                sql_call("DELETE FROM edomiLive.archivPhoneData WHERE (targetid=" . $src['targetid'] . ")");
                $c2++;
            }
        }
        sql_call("OPTIMIZE TABLE edomiLive.archivPhone");
        sql_call("OPTIMIZE TABLE edomiLive.archivPhoneData");
        if ($c2 > 0) {
            $this->logAdd(1, $c2 . ' entfernt');
        }
        $this->setStatusElements();
    }

    private function makeLiveProject_ir()
    {
        $this->logAdd(0, 'IR-Befehle');
        sql_call("DROP TABLE IF EXISTS edomiLive.ir");
        sql_call("CREATE TABLE edomiLive.ir LIKE edomiProject.editIr");
        sql_call("INSERT INTO edomiLive.ir SELECT * FROM edomiProject.editIr");
        sql_call("OPTIMIZE TABLE edomiLive.ir");
        $this->setStatusElements();
    }

    private function makeLiveProject_dvr()
    {
        $this->logAdd(0, 'Digitaler Videorekorder');
        if (!isEmpty(global_dvrPath)) {
            $ss1 = sql_call("SELECT id,dvrkeep FROM edomiLive.cam WHERE dvr=1");
            while ($n = sql_result($ss1)) {
                if ($n['dvrkeep'] > 0) {
                    exec("find " . global_dvrPath . " -mindepth 1 -maxdepth 1 -type f \( -name 'cam-" . $n['id'] . "-*.edomidvr' \) -ctime +" . ($n['dvrkeep'] - 1) . " -delete");
                }
            }
            sql_close($ss1);
            $n = glob(global_dvrPath . '/cam-*.edomidvr');
            foreach ($n as $pathFn) {
                if (is_file($pathFn)) {
                    $tmpFn = basename($pathFn);
                    $tmp = explode('-', trim($tmpFn));
                    if ($tmp[1] > 0) {
                        $id = sql_getValue('edomiLive.cam', 'id', 'id=' . $tmp[1] . ' AND dvr=1');
                        if (isEmpty($id)) {
                            $this->logAdd(1, 'Kamera ' . $tmp[1] . ' ist nicht für DVR aktiviert oder existiert nicht mehr - Datei wird gelöscht: ' . ajaxValueHTML($tmpFn));
                            deleteFiles($pathFn);
                        }
                    }
                }
            }
        }
        $this->setStatusElements();
    }

    private function makeLiveProject_lbs()
    {
        $this->logAdd(0, 'Logikbausteine');
        $this->setStatus(50);
        deleteFiles(MAIN_PATH . '/www/data/liveproject/lbs/*.php');
        $lbsCount = 1;
        $ss1 = sql_call("SELECT COUNT(DISTINCT functionid) AS anz1 FROM edomiProject.editLogicElement");
        if ($n = sql_result($ss1)) {
            $lbsMax = $n['anz1'];
        } else {
            $lbsMax = 0;
        }
        sql_close($ss1);
        $ss1 = sql_call("SELECT functionid,COUNT(*) AS anz1 FROM edomiProject.editLogicElement GROUP BY functionid ORDER BY functionid ASC");
        while ($lbs = sql_result($ss1)) {
            $lbsPageCount = 0;
            $ss2 = sql_call("SELECT pageid FROM edomiProject.editLogicElement WHERE functionid=" . $lbs['functionid']);
            while ($n = sql_result($ss2)) {
                if (sql_getValue('edomiProject.editLogicPage', 'pagestatus', 'id=' . $n['pageid']) == 1) {
                    $lbsPageCount++;
                }
            }
            sql_close($ss2);
            if ($lbs['functionid'] > 12000005) {
                if ($lbsPageCount > 0) {
                    $this->setStatus(50 + intval((50 / $lbsMax) * $lbsCount));
                    $lbsCount++;
                    if ($r = lbs_import($lbs['functionid'])) {
                        if ($r[0] == 0) {
                            $f = fopen(MAIN_PATH . '/www/data/liveproject/lbs/LBS' . $lbs['functionid'] . '.php', 'w');
                            fwrite($f, $r[7]);
                            fclose($f);
                            if (isEmpty($r[8])) {
                                $this->logAdd(1, ajaxValueHTML($r[3]) . " <span class='id'>" . $lbs['functionid'] . '</span>: Ok (' . $lbs['anz1'] . ' Instanzen, ' . $lbsPageCount . ' aktivierte Logikseiten)');
                            } else {
                                $this->logAdd(1, ajaxValueHTML($r[3]) . " <span class='id'>" . $lbs['functionid'] . '</span> (mit EXEC-Script): Ok (' . $lbs['anz1'] . ' Instanzen, ' . $lbsPageCount . ' aktivierte Logikseiten)');
                                $f = fopen(MAIN_PATH . '/www/data/liveproject/lbs/EXE' . $lbs['functionid'] . '.php', 'w');
                                fwrite($f, $r[8]);
                                fclose($f);
                            }
                        } else {
                            if ($r[1]) {
                                $this->logAdd(1, ajaxValueHTML($r[3]) . " <span class='id'>" . $lbs['functionid'] . '</span>: ' . $r[0] . ' Fehler', true);
                            } else {
                                $this->logAdd(1, ajaxValueHTML($r[3]) . " <span class='id'>" . $lbs['functionid'] . '</span>: ' . $r[0] . ' Fehler / Syntaxfehler', true);
                            }
                            $this->errFatalCount++;
                        }
                    } else {
                        $this->logAdd(1, 'Logikbaustein ' . $lbs['functionid'] . ': Nicht vorhanden', true);
                        $this->errFatalCount++;
                    }
                } else {
                    $this->logAdd(11, ajaxValueHTML(sql_getValue('edomiProject.editLogicElementDef', 'name', 'id=' . $lbs['functionid'])) . " <span class='id'>" . $lbs['functionid'] . '</span>: nicht erforderlich (' . $lbs['anz1'] . ' Instanzen, alle Logikseiten deaktiviert)');
                }
            } else {
                $this->logAdd(11, ajaxValueHTML(sql_getValue('edomiProject.editLogicElementDef', 'name', 'id=' . $lbs['functionid'])) . " <span class='id'>" . $lbs['functionid'] . '</span>: nicht erforderlich (' . $lbs['anz1'] . ' Instanzen, ' . $lbsPageCount . ' aktivierte Logikseiten)');
            }
        }
        sql_close($ss1);
        lbs_optimizeTables();
    }

    private function makeLiveProject_logic()
    {
        $this->logAdd(0, 'Logiken');
        $c1 = 0;
        $ss1 = sql_call("SELECT COUNT(*) AS anz1 FROM edomiProject.editLogicPage WHERE (pagestatus=1)");
        if ($n = sql_result($ss1)) {
            $c1 = $n['anz1'];
        }
        $ss1 = sql_call("SELECT * FROM edomiProject.editLogicLink");
        while ($element = sql_result($ss1)) {
            $ss2 = sql_call("SELECT * FROM edomiProject.editLogicElement WHERE (id=" . $element['elementid'] . ")");
            if (!$n = sql_result($ss2)) {
                sql_call("DELETE FROM edomiProject.editLogicLink WHERE (id=" . $element['id'] . ")");
                $this->logAdd(1, 'Ungültiger Verweis in edomiProject.editLogicLink: elementid=' . $element['id'] . ' existiert nicht! (repariert)', true);
                $this->errCount++;
            }
        }
        sql_call("DROP TABLE IF EXISTS edomiLive.logicElement");
        sql_call("CREATE TABLE edomiLive.logicElement LIKE edomiProject.editLogicElement");
        sql_call("INSERT INTO edomiLive.logicElement SELECT * FROM edomiProject.editLogicElement");
        sql_call("DROP TABLE IF EXISTS edomiLive.logicLink");
        sql_call("CREATE TABLE edomiLive.logicLink LIKE edomiProject.editLogicLink");
        sql_call("INSERT INTO edomiLive.logicLink SELECT * FROM edomiProject.editLogicLink");
        sql_call("DROP TABLE IF EXISTS edomiLive.logicCmdList");
        sql_call("CREATE TABLE edomiLive.logicCmdList LIKE edomiProject.editLogicCmdList");
        sql_call("INSERT INTO edomiLive.logicCmdList SELECT * FROM edomiProject.editLogicCmdList");
        sql_call("DROP TABLE IF EXISTS edomiLive.logicElementVarTMP");
        sql_call("CREATE TABLE edomiLive.logicElementVarTMP LIKE edomiProject.editLogicElementVar");
        sql_call("INSERT INTO edomiLive.logicElementVarTMP SELECT * FROM edomiProject.editLogicElementVar");
        $ss1 = sql_call("SELECT * FROM edomiLive.logicElementVar WHERE remanent=1");
        while ($n = sql_result($ss1)) {
            sql_call("UPDATE edomiLive.logicElementVarTMP SET value=" . sql_encodeValue($n['value'], true) . " WHERE (elementid=" . $n['elementid'] . " AND varid=" . $n['varid'] . " AND remanent=1)");
        }
        sql_close($ss1);
        sql_call("DROP TABLE IF EXISTS edomiLive.logicElementVar");
        sql_call("RENAME TABLE edomiLive.logicElementVarTMP TO edomiLive.logicElementVar");
        sql_call("ALTER TABLE edomiLive.logicElement ADD COLUMN status BIGINT UNSIGNED DEFAULT 0");
        sql_call("ALTER TABLE edomiLive.logicElement ADD KEY (status)");
        sql_call("ALTER TABLE edomiLive.logicElement ADD COLUMN statusref BIGINT UNSIGNED DEFAULT 0");
        sql_call("ALTER TABLE edomiLive.logicElement ADD COLUMN statusint TINYINT UNSIGNED DEFAULT 0");
        sql_call("ALTER TABLE edomiLive.logicElement ADD COLUMN statusexec TINYINT UNSIGNED DEFAULT 0");
        $c2 = 0;
        $ss1 = sql_call("SELECT * FROM edomiProject.editLogicPage WHERE (pagestatus=0) ORDER BY id ASC");
        while ($page = sql_result($ss1)) {
            $c2++;
            $ss2 = sql_call("SELECT * FROM edomiLive.logicElement WHERE (pageid=" . $page['id'] . ")");
            while ($element = sql_result($ss2)) {
                sql_call("DELETE FROM edomiLive.logicLink WHERE (elementid=" . $element['id'] . ")");
                sql_call("DELETE FROM edomiLive.logicCmdList WHERE (targetid=" . $element['id'] . ")");
                sql_call("DELETE FROM edomiLive.logicElementVar WHERE (elementid=" . $element['id'] . ")");
            }
            sql_call("DELETE FROM edomiLive.logicElement WHERE (pageid=" . $page['id'] . ")");
        }
        sql_call("ALTER TABLE edomiLive.logicLink ADD COLUMN functionid BIGINT UNSIGNED NOT NULL AFTER elementid");
        $ss1 = sql_call("SELECT * FROM edomiLive.logicElement");
        while ($element = sql_result($ss1)) {
            sql_call("UPDATE edomiLive.logicLink SET functionid=" . $element['functionid'] . " WHERE (elementid=" . $element['id'] . ")");
        }
        sql_call("ALTER TABLE edomiLive.logicLink ADD COLUMN init TINYINT UNSIGNED NOT NULL DEFAULT 0 AFTER ausgang");
        sql_call("ALTER TABLE edomiLive.logicElement DROP xpos");
        sql_call("ALTER TABLE edomiLive.logicElement DROP ypos");
        sql_call("ALTER TABLE edomiLive.logicElement DROP name");
        sql_call("ALTER TABLE edomiLive.logicElement DROP layer");
        sql_call("ALTER TABLE edomiLive.logicCmdList DROP folderid");
        $ss1 = sql_call("SELECT * FROM edomiLive.logicLink WHERE (linktyp=1)");
        while ($el = sql_result($ss1)) {
            $ss2 = sql_call("SELECT * FROM edomiLive.logicLink WHERE (functionid<=12000005 AND elementid=" . $el['linkid'] . " AND eingang=" . $el['ausgang'] . ")");
            if ($n = sql_result($ss2)) {
                if ($n['linktyp'] == 0 && !isEmpty($n['linkid'])) {
                    sql_call("UPDATE edomiLive.logicLink SET linktyp=0,linkid=" . $n['linkid'] . ",ausgang=NULL WHERE (id=" . $el['id'] . ")");
                } else {
                    sql_call("UPDATE edomiLive.logicLink SET linktyp=2,linkid=NULL,ausgang=NULL WHERE (id=" . $el['id'] . ")");
                }
            }
        }
        $c3 = sql_getCount('edomiLive.logicElement', 'functionid<=12000005');
        sql_call("DELETE FROM edomiLive.logicLink WHERE (functionid<=12000005)");
        sql_call("DELETE FROM edomiLive.logicElement WHERE (functionid<=12000005)");
        sql_call("OPTIMIZE TABLE edomiLive.logicElement");
        sql_call("OPTIMIZE TABLE edomiLive.logicLink");
        sql_call("OPTIMIZE TABLE edomiLive.logicCmdList");
        sql_call("OPTIMIZE TABLE edomiLive.logicElementVar");
        if ($c1 > 0 || $c2 > 0) {
            $this->logAdd(1, $c1 . ' aktivierte Logikseiten / ' . $c2 . ' deaktivierte Logikseiten');
        }
        if ($c3 > 0) {
            $this->logAdd(1, $c3 . ' Textboxen bzw. Eingangsboxen entfernt');
        }
    }

    private function visuInsertElementStyle($item, $design)
    {
        if (!($item['gaid'] > 0)) {
            for ($t = 3; $t <= 48; $t++) {
                $design['s' . $t] = trim(preg_replace('/\{(.*?)\}/s', '', $design['s' . $t]));
            }
        }
        if ($design['styletyp'] == 0) {
            $v1 = null;
            $v2 = null;
            $styletyp = 0;
        } else {
            $v1 = $design['s1'];
            $v2 = $design['s2'];
            $styletyp = 1;
        }
        if (!isEmpty($design['s11']) && $design['styletyp'] == 1) {
            $item['text'] = $design['s11'];
        }
        $vseDef = sql_getValues('edomiProject.editVisuElementDef', '*', 'id=' . $item['controltyp']);
        $css = visu_getElementStyleCss($item, $vseDef, $design, false, true);
        if (strlen($css[0] . $css[1]) > 20000) {
            $this->logAdd(1, 'Visualisierung ' . $item['visuid'] . '/Seite ' . $item['pageid'] . '/Visuelement ' . $item['id'] . ': Design umfasst mehr als 20.000 Zeichen', true);
            $this->errCount++;
        }
        sql_call("INSERT INTO edomiLive.visuElementStyle (targetid,visuid,fromvalue,tovalue,styletyp,controltyp,css,text,initonly) VALUES (
			" . $item['id'] . ",
			" . $item['visuid'] . ",
			" . sql_encodeValue($v1, true) . ",
			" . sql_encodeValue($v2, true) . ",
			" . $styletyp . ",
			" . $item['controltyp'] . ",
			'" . sql_encodeValue($css[0] . $css[1]) . "',
			'" . sql_encodeValue($item['text']) . "',
			" . $item['initonly'] . "
		)");
    }
}

function class_projectActivation_visuAddMeta($visuId, $typ, $id)
{
    if (sql_getValues('edomiLive.visuMeta', '*', "visuid=" . $visuId . " AND typ=" . $typ . " AND id=" . $id) === false) {
        sql_call("INSERT INTO edomiLive.visuMeta (visuid,typ,id) VALUES (" . $visuId . "," . $typ . "," . $id . ")");
    }
}

?>
