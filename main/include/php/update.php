<? function edomi_update($version, $update)
{
    $version = floatVal(preg_replace("/[^\.0-9]+/i", '', $version));
    $logUpdate = '';
    if ($version < 1.10) {
        $logUpdate .= '1.10/';
        sql_call("UPDATE edomiProject.editRoot SET name='Grundfunktionen & Auslöser (13)' WHERE id=13");
        sql_call("UPDATE edomiProject.editRoot SET name='Gatter & Logik (14)' WHERE id=14");
        sql_call("UPDATE edomiProject.editRoot SET name='Mathematik, Vergleicher & Filter (15)' WHERE id=15");
        sql_call("UPDATE edomiProject.editRoot SET name='Timer & Zeitfunktionen (16)' WHERE id=16");
        sql_call("UPDATE edomiProject.editRoot SET name='Funktionale Einheiten (17)' WHERE id=17");
        sql_call("UPDATE edomiProject.editRoot SET name='Sonstige (18)' WHERE id=18");
        sql_call("UPDATE edomiProject.editRoot SET name='Community-Bausteine (19)' WHERE id=19");
    }
    if ($version < 1.12) {
        $logUpdate .= '1.12/';
        sql_call("UPDATE edomiProject.editRoot SET allow=CONCAT(SUBSTRING(allow,1,10),'xx') WHERE SUBSTRING(path,1,7)='/20/22/'");
        sql_call("UPDATE edomiProject.editRoot SET allow=CONCAT(SUBSTRING(allow,1,10),'xx') WHERE id=21");
        if (!sql_columnExists('edomiProject.editRoot', 'tmp')) {
            sql_call("ALTER TABLE edomiProject.editRoot ADD COLUMN tmp BIGINT UNSIGNED AFTER linkid");
        }
        if (!sql_columnExists('edomiProject.editVisuPage', 'tmp')) {
            sql_call("ALTER TABLE edomiProject.editVisuPage ADD COLUMN tmp BIGINT UNSIGNED AFTER ygrid");
        }
    }
    if ($version < 1.15) {
        $logUpdate .= '1.15/';
        sql_call("UPDATE edomiProject.editRoot SET allow=CONCAT(SUBSTRING(allow,1,10),'xx') WHERE id=31 OR SUBSTRING(path,1,7)='/30/31/'");
        sql_call("UPDATE edomiProject.editRoot SET allow=CONCAT(SUBSTRING(allow,1,10),'xx') WHERE id=81 OR SUBSTRING(path,1,7)='/80/81/'");
        sql_call("UPDATE edomiProject.editRoot SET allow=CONCAT(SUBSTRING(allow,1,10),'xx') WHERE id=82 OR SUBSTRING(path,1,7)='/80/82/'");
        sql_call("UPDATE edomiProject.editRoot SET allow=CONCAT(SUBSTRING(allow,1,10),'xx') WHERE id=50 OR SUBSTRING(path,1,4)='/50/'");
        sql_call("UPDATE edomiProject.editRoot SET allow=CONCAT(SUBSTRING(allow,1,10),'xx') WHERE id=60 OR SUBSTRING(path,1,4)='/60/'");
        sql_call("UPDATE edomiProject.editRoot SET allow=CONCAT(SUBSTRING(allow,1,10),'xx') WHERE id=70 OR SUBSTRING(path,1,4)='/70/'");
        sql_call("UPDATE edomiProject.editRoot SET allow=CONCAT(SUBSTRING(allow,1,10),'xx') WHERE id=75 OR SUBSTRING(path,1,4)='/75/'");
        sql_call("UPDATE edomiProject.editRoot SET allow=CONCAT(SUBSTRING(allow,1,10),'xx') WHERE id=90 OR SUBSTRING(path,1,4)='/90/'");
        sql_call("UPDATE edomiProject.editRoot SET allow=CONCAT(SUBSTRING(allow,1,10),'xx') WHERE id=23 OR SUBSTRING(path,1,7)='/20/23/'");
        sql_call("UPDATE edomiProject.editRoot SET allow=CONCAT(SUBSTRING(allow,1,10),'xx') WHERE id=24 OR SUBSTRING(path,1,7)='/20/24/'");
        sql_call("UPDATE edomiProject.editRoot SET allow=CONCAT(SUBSTRING(allow,1,10),'xx') WHERE id=25 OR SUBSTRING(path,1,7)='/20/25/'");
        sql_call("UPDATE edomiProject.editRoot SET allow=CONCAT(SUBSTRING(allow,1,10),'xx') WHERE id=26 OR SUBSTRING(path,1,7)='/20/26/'");
        sql_call("UPDATE edomiProject.editRoot SET allow=CONCAT(SUBSTRING(allow,1,10),'xx') WHERE id=27 OR SUBSTRING(path,1,7)='/20/27/'");
        sql_call("UPDATE edomiProject.editRoot SET allow=CONCAT(SUBSTRING(allow,1,10),'xx') WHERE id=100 OR SUBSTRING(path,1,5)='/100/'");
        sql_call("UPDATE edomiProject.editRoot SET allow=CONCAT(SUBSTRING(allow,1,10),'xx') WHERE id=110 OR SUBSTRING(path,1,5)='/110/'");
        sql_call("UPDATE edomiProject.editRoot SET allow=CONCAT(SUBSTRING(allow,1,10),'xx') WHERE id=120 OR SUBSTRING(path,1,5)='/120/'");
        sql_call("UPDATE edomiProject.editRoot SET allow=CONCAT(SUBSTRING(allow,1,10),'-x') WHERE id=121");
        sql_call("UPDATE edomiProject.editRoot SET allow=CONCAT(SUBSTRING(allow,1,10),'xx') WHERE id=126 OR SUBSTRING(path,1,9)='/125/126/'");
        sql_call("UPDATE edomiProject.editRoot SET allow=CONCAT(SUBSTRING(allow,1,10),'xx') WHERE id=127 OR SUBSTRING(path,1,9)='/125/127/'");
        sql_call("UPDATE edomiProject.editRoot SET allow=CONCAT(SUBSTRING(allow,1,10),'xx') WHERE id=130 OR SUBSTRING(path,1,5)='/130/'");
    }
    if ($version < 1.16) {
        $logUpdate .= '1.16/';
        sql_call("UPDATE edomiProject.editRoot SET path='/10/12/' WHERE id=13");
        sql_call("UPDATE edomiProject.editRoot SET path='/10/12/' WHERE id=14");
        sql_call("UPDATE edomiProject.editRoot SET path='/10/12/' WHERE id=15");
        sql_call("UPDATE edomiProject.editRoot SET path='/10/12/' WHERE id=16");
        sql_call("UPDATE edomiProject.editRoot SET path='/10/12/' WHERE id=17");
        sql_call("UPDATE edomiProject.editRoot SET path='/10/12/' WHERE id=18");
        sql_call("UPDATE edomiProject.editRoot SET path='/10/12/' WHERE id=19");
        if (!sql_columnExists('edomiProject.editVisu', 'indicolor')) {
            sql_call("ALTER TABLE edomiProject.editVisu ADD COLUMN indicolor BIGINT UNSIGNED DEFAULT 0 AFTER queuelatency");
        }
    }
    if ($version < 1.17) {
        $logUpdate .= '1.17/';
        if (!sql_columnExists('edomiProject.editVisuElement', 'name')) {
            sql_call("ALTER TABLE edomiProject.editVisuElement ADD COLUMN name VARCHAR(200) DEFAULT NULL AFTER hascmd");
        }
        if (!sql_columnExists('edomiProject.editVisuElement', 'groupid')) {
            sql_call("ALTER TABLE edomiProject.editVisuElement ADD COLUMN groupid BIGINT UNSIGNED DEFAULT NULL AFTER name");
        }
        if (!sql_columnExists('edomiProject.editVisuElement', 'tmp')) {
            sql_call("ALTER TABLE edomiProject.editVisuElement ADD COLUMN tmp BIGINT UNSIGNED DEFAULT NULL AFTER groupid");
        }
        sql_call("ALTER TABLE edomiProject.editKo MODIFY vmin FLOAT DEFAULT NULL");
        sql_call("ALTER TABLE edomiProject.editKo MODIFY vmax FLOAT DEFAULT NULL");
        sql_call("ALTER TABLE edomiLive.ko MODIFY vmin FLOAT DEFAULT NULL");
        sql_call("ALTER TABLE edomiLive.ko MODIFY vmax FLOAT DEFAULT NULL");
    }
    if ($version < 1.18) {
        $logUpdate .= '1.18/';
        if (!sql_columnExists('edomiProject.editChartList', 'ystyle')) {
            sql_call("ALTER TABLE edomiProject.editChartList ADD COLUMN ystyle TINYINT DEFAULT 0 AFTER style");
        }
    }
    if ($version < 1.19) {
        $logUpdate .= '1.19/';
        sql_call("UPDATE edomiProject.editLogicElementDef SET defin=REPLACE(defin,'|','" . SEPARATOR1 . "')");
        sql_call("UPDATE edomiProject.editLogicElementDef SET defin=REPLACE(defin,'#','" . SEPARATOR2 . "')");
        sql_call("UPDATE edomiProject.editLogicElementDef SET defout=REPLACE(defout,'|','" . SEPARATOR1 . "')");
        sql_call("UPDATE edomiProject.editLogicElementDef SET defout=REPLACE(defout,'#','" . SEPARATOR2 . "')");
        sql_call("UPDATE edomiProject.editLogicElementDef SET defvar=REPLACE(defvar,'|','" . SEPARATOR1 . "')");
        sql_call("UPDATE edomiProject.editLogicElementDef SET defvar=REPLACE(defvar,'#','" . SEPARATOR2 . "')");
        sql_call("ALTER TABLE edomiProject.editArchivKoData MODIFY gavalue VARCHAR(10000) DEFAULT NULL");
        sql_call("ALTER TABLE edomiProject.editAwsData MODIFY gavalue VARCHAR(10000) DEFAULT NULL");
        sql_call("ALTER TABLE edomiProject.editKo MODIFY value VARCHAR(10000) DEFAULT NULL");
        sql_call("ALTER TABLE edomiProject.editKo MODIFY defaultvalue VARCHAR(10000) DEFAULT NULL");
        sql_call("ALTER TABLE edomiProject.editLogicCmdList MODIFY outvalue VARCHAR(10000) DEFAULT NULL");
        sql_call("ALTER TABLE edomiProject.editLogicElementVar MODIFY value VARCHAR(10000) DEFAULT NULL");
        sql_call("ALTER TABLE edomiProject.editLogicLink MODIFY value VARCHAR(10000) DEFAULT NULL");
        sql_call("ALTER TABLE edomiProject.editLogicLink MODIFY valueold VARCHAR(10000) DEFAULT NULL");
        sql_call("ALTER TABLE edomiProject.editSceneList MODIFY gavalue VARCHAR(10000) DEFAULT NULL");
        sql_call("ALTER TABLE edomiProject.editSequenceList MODIFY outvalue VARCHAR(10000) DEFAULT NULL");
        sql_call("ALTER TABLE edomiProject.editTimerData MODIFY status VARCHAR(10000) DEFAULT NULL");
        sql_call("ALTER TABLE edomiProject.editVisuCmdList MODIFY outvalue VARCHAR(10000) DEFAULT NULL");
        sql_call("ALTER TABLE edomiProject.editVisuElement MODIFY var1 VARCHAR(20) DEFAULT NULL");
        sql_call("ALTER TABLE edomiProject.editVisuElement MODIFY style1 VARCHAR(10000) DEFAULT NULL");
        sql_call("ALTER TABLE edomiProject.editVisuElement MODIFY text VARCHAR(10000) DEFAULT NULL");
        sql_call("ALTER TABLE edomiProject.editLogicElementDef MODIFY defin VARCHAR(10000) DEFAULT NULL");
        sql_call("ALTER TABLE edomiProject.editLogicElementDef MODIFY defout VARCHAR(10000) DEFAULT NULL");
        sql_call("ALTER TABLE edomiProject.editLogicElementDef MODIFY defvar VARCHAR(10000) DEFAULT NULL");
        sql_call("ALTER TABLE edomiProject.editArchivMsgData MODIFY msg VARCHAR(10000) DEFAULT NULL");
        sql_call("ALTER TABLE edomiProject.editCam MODIFY url VARCHAR(10000) DEFAULT NULL");
        sql_call("ALTER TABLE edomiProject.editChart MODIFY datefrom VARCHAR(10000) DEFAULT NULL");
        sql_call("ALTER TABLE edomiProject.editChart MODIFY dateto VARCHAR(10000) DEFAULT NULL");
        sql_call("ALTER TABLE edomiProject.editEmail MODIFY mailaddr VARCHAR(1000) DEFAULT NULL");
        sql_call("ALTER TABLE edomiProject.editEmail MODIFY subject VARCHAR(1000) DEFAULT NULL");
        sql_call("ALTER TABLE edomiProject.editEmail MODIFY body VARCHAR(10000) DEFAULT NULL");
        sql_call("ALTER TABLE edomiProject.editIp MODIFY url VARCHAR(10000) DEFAULT NULL");
        sql_call("ALTER TABLE edomiProject.editIp MODIFY data VARCHAR(10000) DEFAULT NULL");
        sql_call("ALTER TABLE edomiProject.editIRtrans MODIFY data VARCHAR(10000) DEFAULT NULL");
        sql_call("ALTER TABLE edomiProject.editRoot MODIFY path VARCHAR(10000) DEFAULT NULL");
        sql_call("ALTER TABLE edomiProject.editVisuAnim MODIFY keyframes VARCHAR(10000) DEFAULT NULL");
        sql_call("ALTER TABLE edomiProject.editVisuElementDef MODIFY style VARCHAR(10000) DEFAULT NULL");
        sql_call("ALTER TABLE edomiLive.archivKoData MODIFY gavalue VARCHAR(10000) DEFAULT NULL");
        sql_call("ALTER TABLE edomiLive.archivMsgData MODIFY msg VARCHAR(10000) DEFAULT NULL");
        sql_call("ALTER TABLE edomiLive.awsData MODIFY gavalue VARCHAR(10000) DEFAULT NULL");
        sql_call("ALTER TABLE edomiLive.ko MODIFY value VARCHAR(10000) DEFAULT NULL");
        sql_call("ALTER TABLE edomiLive.ko MODIFY defaultvalue VARCHAR(10000) DEFAULT NULL");
        sql_call("ALTER TABLE edomiLive.sceneList MODIFY gavalue VARCHAR(10000) DEFAULT NULL");
        sql_call("ALTER TABLE edomiLive.timerData MODIFY status VARCHAR(10000) DEFAULT NULL");
        $r = sql_call("CREATE TABLE edomiProject.editVisuElementDefTMP (
					id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
					folderid BIGINT UNSIGNED DEFAULT NULL,
					name VARCHAR(200) DEFAULT NULL,
					styletyp TINYINT UNSIGNED DEFAULT 0,
					s1 VARCHAR(500) DEFAULT NULL,
					s2 VARCHAR(500) DEFAULT NULL,
					s3 VARCHAR(500) DEFAULT NULL,
					s4 VARCHAR(500) DEFAULT NULL,
					s5 VARCHAR(500) DEFAULT NULL,
					s6 VARCHAR(500) DEFAULT NULL,
					s7 VARCHAR(500) DEFAULT NULL,
					s8 VARCHAR(500) DEFAULT NULL,
					s9 BIGINT UNSIGNED DEFAULT NULL,
					s10 BIGINT UNSIGNED DEFAULT NULL,
					s11 VARCHAR(10000) DEFAULT NULL,
					s12 VARCHAR(500) DEFAULT NULL,
					s13 VARCHAR(500) DEFAULT NULL,
					s14 VARCHAR(500) DEFAULT NULL,
					s15 BIGINT UNSIGNED DEFAULT NULL,
					s16 TINYINT UNSIGNED DEFAULT NULL,
					s17 TINYINT UNSIGNED DEFAULT NULL,
					s18 TINYINT UNSIGNED DEFAULT NULL,
					s19 VARCHAR(500) DEFAULT NULL,
					s20 VARCHAR(500) DEFAULT NULL,
					s21 VARCHAR(500) DEFAULT NULL,
					s22 BIGINT UNSIGNED DEFAULT NULL,
					s23 VARCHAR(500) DEFAULT NULL,
					s24 VARCHAR(500) DEFAULT NULL,
					s25 VARCHAR(500) DEFAULT NULL,
					s26 VARCHAR(500) DEFAULT NULL,
					s27 BIGINT UNSIGNED DEFAULT NULL,
					s28 BIGINT UNSIGNED DEFAULT NULL,
					s29 BIGINT UNSIGNED DEFAULT NULL,
					s30 BIGINT UNSIGNED DEFAULT NULL,
					s31 VARCHAR(500) DEFAULT NULL,
					s32 TINYINT UNSIGNED DEFAULT NULL,
					s33 VARCHAR(500) DEFAULT NULL,
					s34 VARCHAR(500) DEFAULT NULL,
					s35 VARCHAR(500) DEFAULT NULL,
					s36 VARCHAR(500) DEFAULT NULL,
					s37 BIGINT UNSIGNED DEFAULT NULL,
					s38 TINYINT UNSIGNED DEFAULT NULL,
					s39 BIGINT UNSIGNED DEFAULT NULL,
					s40 VARCHAR(500) DEFAULT NULL,
					s41 VARCHAR(500) DEFAULT NULL,
					s42 BIGINT UNSIGNED DEFAULT NULL,
					s43 BIGINT UNSIGNED DEFAULT NULL,
					s44 BIGINT UNSIGNED DEFAULT NULL,
					s45 BIGINT UNSIGNED DEFAULT NULL,
					s46 BIGINT UNSIGNED DEFAULT NULL,
					s47 BIGINT UNSIGNED DEFAULT NULL,
					PRIMARY KEY (id),
					KEY (folderid),
					KEY (styletyp),
					KEY (s1),
					KEY (s2)
				) ENGINE=MyISAM DEFAULT CHARSET=latin1");
        if ($r) {
            $ss1 = sql_call("SELECT * FROM edomiProject.editVisuElementDef ORDER BY id ASC");
            while ($n = sql_result($ss1)) {
                $tmp1 = '';
                $tmp2 = '';
                $style = explode(SEPARATOR1, $n['style'], -1);
                for ($t = 0; $t < count($style); $t++) {
                    $tmp1 .= "s" . ($t + 1) . ",";
                    $tmp2 .= sql_encodeValue($style[$t], true) . ',';
                }
                sql_call("INSERT INTO edomiProject.editVisuElementDefTMP (id,folderid,name,styletyp," . rtrim($tmp1, ',') . ") VALUES ('" . $n['id'] . "','" . $n['folderid'] . "','" . $n['name'] . "','" . $n['styletyp'] . "'," . rtrim($tmp2, ',') . ")");
            }
            sql_close($ss1);
            sql_call("ALTER TABLE edomiProject.editVisuElementDefTMP AUTO_INCREMENT=0");
            sql_call("DROP TABLE IF EXISTS edomiProject.editVisuElementDef");
            sql_call("ALTER TABLE edomiProject.editVisuElementDefTMP RENAME edomiProject.editVisuElementDef");
            sql_call("UPDATE edomiProject.editVisuElementDef SET s11=REPLACE(s11,'//','\n')");
        }
        $r = sql_call("CREATE TABLE edomiProject.editVisuElementDesign (
					id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
					targetid BIGINT UNSIGNED NOT NULL,
					defid BIGINT UNSIGNED DEFAULT NULL,
					styletyp TINYINT UNSIGNED DEFAULT 0,
					s1 VARCHAR(500) DEFAULT NULL,
					s2 VARCHAR(500) DEFAULT NULL,
					s3 VARCHAR(500) DEFAULT NULL,
					s4 VARCHAR(500) DEFAULT NULL,
					s5 VARCHAR(500) DEFAULT NULL,
					s6 VARCHAR(500) DEFAULT NULL,
					s7 VARCHAR(500) DEFAULT NULL,
					s8 VARCHAR(500) DEFAULT NULL,
					s9 BIGINT UNSIGNED DEFAULT NULL,
					s10 BIGINT UNSIGNED DEFAULT NULL,
					s11 VARCHAR(10000) DEFAULT NULL,
					s12 VARCHAR(500) DEFAULT NULL,
					s13 VARCHAR(500) DEFAULT NULL,
					s14 VARCHAR(500) DEFAULT NULL,
					s15 BIGINT UNSIGNED DEFAULT NULL,
					s16 TINYINT UNSIGNED DEFAULT NULL,
					s17 TINYINT UNSIGNED DEFAULT NULL,
					s18 TINYINT UNSIGNED DEFAULT NULL,
					s19 VARCHAR(500) DEFAULT NULL,
					s20 VARCHAR(500) DEFAULT NULL,
					s21 VARCHAR(500) DEFAULT NULL,
					s22 BIGINT UNSIGNED DEFAULT NULL,
					s23 VARCHAR(500) DEFAULT NULL,
					s24 VARCHAR(500) DEFAULT NULL,
					s25 VARCHAR(500) DEFAULT NULL,
					s26 VARCHAR(500) DEFAULT NULL,
					s27 BIGINT UNSIGNED DEFAULT NULL,
					s28 BIGINT UNSIGNED DEFAULT NULL,
					s29 BIGINT UNSIGNED DEFAULT NULL,
					s30 BIGINT UNSIGNED DEFAULT NULL,
					s31 VARCHAR(500) DEFAULT NULL,
					s32 TINYINT UNSIGNED DEFAULT NULL,
					s33 VARCHAR(500) DEFAULT NULL,
					s34 VARCHAR(500) DEFAULT NULL,
					s35 VARCHAR(500) DEFAULT NULL,
					s36 VARCHAR(500) DEFAULT NULL,
					s37 BIGINT UNSIGNED DEFAULT NULL,
					s38 TINYINT UNSIGNED DEFAULT NULL,
					s39 BIGINT UNSIGNED DEFAULT NULL,
					s40 VARCHAR(500) DEFAULT NULL,
					s41 VARCHAR(500) DEFAULT NULL,
					s42 BIGINT UNSIGNED DEFAULT NULL,
					s43 BIGINT UNSIGNED DEFAULT NULL,
					s44 BIGINT UNSIGNED DEFAULT NULL,
					s45 BIGINT UNSIGNED DEFAULT NULL,
					s46 BIGINT UNSIGNED DEFAULT NULL,
					s47 BIGINT UNSIGNED DEFAULT NULL,
					PRIMARY KEY (id),
					KEY (targetid),
					KEY (defid),
					KEY (styletyp),
					KEY (s1),
					KEY (s2)
				) ENGINE=MyISAM DEFAULT CHARSET=latin1");
        if ($r) {
            $ss1 = sql_call("SELECT * FROM edomiProject.editVisuElement ORDER BY id ASC");
            while ($n = sql_result($ss1)) {
                if (!isEmpty($n['style1'])) {
                    $tmp1 = '';
                    $tmp2 = '';
                    $tmp3 = 0;
                    $style = explode(SEPARATOR1, $n['style1'], -1);
                    for ($t = 0; $t < count($style); $t++) {
                        if ($t == 41) {
                            $tmp3 = $style[$t];
                        } else {
                            $tmp1 .= "s" . ($t + 1) . ",";
                            $tmp2 .= sql_encodeValue($style[$t], true) . ',';
                        }
                    }
                    if ($tmp3 > 0) {
                        sql_call("INSERT INTO edomiProject.editVisuElementDesign (targetid,defid,styletyp) VALUES ('" . $n['id'] . "'," . sql_encodeValue($tmp3, true) . ",0)");
                    } else {
                        sql_call("INSERT INTO edomiProject.editVisuElementDesign (targetid,defid,styletyp," . rtrim($tmp1, ',') . ") VALUES ('" . $n['id'] . "',0,0," . rtrim($tmp2, ',') . ")");
                    }
                }
                if (!isEmpty($n['style2'])) {
                    $styles = explode(SEPARATOR2, $n['style2'], -1);
                    for ($t = 0; $t < count($styles); $t++) {
                        $tmp1 = '';
                        $tmp2 = '';
                        $tmp3 = 0;
                        $style = explode(SEPARATOR1, $styles[$t], -1);
                        for ($tt = 0; $tt < count($style); $tt++) {
                            if ($tt == 41) {
                                $tmp3 = $style[$tt];
                            } else {
                                $tmp1 .= "s" . ($tt + 1) . ",";
                                $tmp2 .= sql_encodeValue($style[$tt], true) . ',';
                            }
                        }
                        if ($tmp3 > 0) {
                            sql_call("INSERT INTO edomiProject.editVisuElementDesign (targetid,defid,styletyp) VALUES ('" . $n['id'] . "'," . sql_encodeValue($tmp3, true) . ",1)");
                        } else {
                            sql_call("INSERT INTO edomiProject.editVisuElementDesign (targetid,defid,styletyp," . rtrim($tmp1, ',') . ") VALUES ('" . $n['id'] . "',0,1," . rtrim($tmp2, ',') . ")");
                        }
                    }
                }
            }
            sql_close($ss1);
            sql_call("ALTER TABLE edomiProject.editVisuElement DROP style1");
            sql_call("ALTER TABLE edomiProject.editVisuElement DROP style2");
            sql_call("UPDATE edomiProject.editVisuElement SET text=REPLACE(text,'//','\n')");
            sql_call("UPDATE edomiProject.editVisuElementDesign SET s11=REPLACE(s11,'//','\n')");
        }
        lbs_importAll();
        sql_call("ALTER TABLE edomiProject.editChartList ADD COLUMN s1 BIGINT UNSIGNED DEFAULT NULL");
        sql_call("ALTER TABLE edomiProject.editChartList ADD COLUMN s2 INT DEFAULT NULL");
        sql_call("ALTER TABLE edomiProject.editChartList ADD COLUMN s3 INT DEFAULT NULL");
        sql_call("ALTER TABLE edomiProject.editChartList ADD COLUMN s4 INT DEFAULT NULL");
        $ss1 = sql_call("SELECT * FROM edomiProject.editChartList");
        while ($n = sql_result($ss1)) {
            if (!isEmpty($n['style'])) {
                $style = explode(SEPARATOR1, $n['style'], -1);
                sql_call("UPDATE edomiProject.editChartList SET s1='" . sql_encodeValue($style[0]) . "',s2='" . sql_encodeValue(intVal($style[1] * 100)) . "',s3='" . sql_encodeValue($style[2]) . "',s4='" . sql_encodeValue($style[3]) . "' WHERE (id=" . $n['id'] . ")");
            }
        }
        sql_close($ss1);
        sql_call("ALTER TABLE edomiProject.editChartList DROP style");
        sql_call("ALTER TABLE edomiProject.editLogicPage ADD COLUMN text VARCHAR(10000) DEFAULT NULL");
        sql_call("ALTER TABLE edomiProject.editLogicElement MODIFY name VARCHAR(10000) DEFAULT NULL");
        sql_call("UPDATE edomiProject.editRoot SET allow='-------x----' WHERE id=13");
        sql_call("UPDATE edomiProject.editRoot SET allow='-------x----' WHERE id=14");
        sql_call("UPDATE edomiProject.editRoot SET allow='-------x----' WHERE id=15");
        sql_call("UPDATE edomiProject.editRoot SET allow='-------x----' WHERE id=16");
        sql_call("UPDATE edomiProject.editRoot SET allow='-------x----' WHERE id=17");
        sql_call("UPDATE edomiProject.editRoot SET allow='-------x----' WHERE id=18");
        sql_call("UPDATE edomiProject.editVisuElement SET text=var1 WHERE controltyp=29");
        sql_call("UPDATE edomiProject.editVisuElement SET var1=null WHERE controltyp=29");
    }
    if ($version < 1.20) {
        $logUpdate .= '1.20/';
        sql_call("ALTER TABLE edomiProject.editLogicElement DROP status");
        sql_call("ALTER TABLE edomiProject.editLogicLink DROP valueold");
        if (!sql_columnExists('edomiProject.editVisuElement', 'tmp')) {
            sql_call("ALTER TABLE edomiProject.editVisuElement ADD COLUMN tmp BIGINT UNSIGNED DEFAULT NULL");
        }
        lbs_importAll();
    }
    if ($version < 1.21) {
        $logUpdate .= '1.21/';
        lbs_importAll();
    }
    if ($version < 1.22) {
        $logUpdate .= '1.22/';
        sql_call("ALTER TABLE edomiProject.editVisuElement MODIFY xpos INT DEFAULT NULL");
        sql_call("ALTER TABLE edomiProject.editVisuElement MODIFY ypos INT DEFAULT NULL");
        if (!sql_columnExists('edomiProject.editVisuElement', 'var5')) {
            sql_call("ALTER TABLE edomiProject.editVisuElement ADD COLUMN var5 VARCHAR(20) DEFAULT NULL AFTER var4");
            sql_call("ALTER TABLE edomiProject.editVisuElement ADD COLUMN var6 VARCHAR(20) DEFAULT NULL AFTER var5");
            sql_call("ALTER TABLE edomiProject.editVisuElement ADD COLUMN var7 VARCHAR(20) DEFAULT NULL AFTER var6");
            sql_call("ALTER TABLE edomiProject.editVisuElement ADD COLUMN var8 VARCHAR(20) DEFAULT NULL AFTER var7");
            sql_call("ALTER TABLE edomiProject.editVisuElement ADD COLUMN var9 VARCHAR(20) DEFAULT NULL AFTER var8");
            sql_call("ALTER TABLE edomiProject.editVisuElement ADD COLUMN var10 VARCHAR(20) DEFAULT NULL AFTER var9");
        }
    }
    if ($version < 1.23) {
        $logUpdate .= '1.23/';
        $r = sql_call("INSERT INTO edomiProject.editKo (id,name,folderid,ga,gatyp,valuetyp,value,defaultvalue,initscan,initsend,requestable,remanent,vmin,vmax,vstep,vlist) VALUES
			(24, 'Trigger: Zehnminütlich --:(00/10/20/.../50):00', 33, '24', 2, 0, NULL, NULL, 0, 0, 0, 0, NULL, NULL, NULL, NULL),
			(25, 'Trigger: Fünfminütlich --:(00/05/10/15/../55):00', 33, '25', 2, 0, NULL, NULL, 0, 0, 0, 0, NULL, NULL, NULL, NULL),
			(26, 'Trigger: Minütlich --:(00/01/02/03/../59):00', 33, '26', 2, 0, NULL, NULL, 0, 0, 0, 0, NULL, NULL, NULL, NULL)
		");
    }
    if ($version < 1.24) {
        $logUpdate .= '1.24/';
        if (!sql_columnExists('edomiProject.editArchivCam', 'outgaid2')) {
            sql_call("ALTER TABLE edomiProject.editArchivCam ADD COLUMN outgaid2 BIGINT UNSIGNED DEFAULT NULL");
        }
        sql_call("ALTER TABLE edomiProject.editChartList MODIFY ymin FLOAT DEFAULT NULL");
        sql_call("ALTER TABLE edomiProject.editChartList MODIFY ymax FLOAT DEFAULT NULL");
        if (!sql_columnExists('edomiProject.editChartList', 'yticks')) {
            sql_call("ALTER TABLE edomiProject.editChartList ADD COLUMN ygrid3 TINYINT UNSIGNED DEFAULT NULL AFTER ymax");
            sql_call("ALTER TABLE edomiProject.editChartList ADD COLUMN ygrid2 TINYINT UNSIGNED DEFAULT NULL AFTER ymax");
            sql_call("ALTER TABLE edomiProject.editChartList ADD COLUMN ygrid1 TINYINT UNSIGNED DEFAULT NULL AFTER ymax");
            sql_call("ALTER TABLE edomiProject.editChartList ADD COLUMN yshow TINYINT UNSIGNED DEFAULT NULL AFTER ymax");
            sql_call("ALTER TABLE edomiProject.editChartList ADD COLUMN ynice TINYINT UNSIGNED DEFAULT NULL AFTER ymax");
            sql_call("ALTER TABLE edomiProject.editChartList ADD COLUMN yticks INT UNSIGNED DEFAULT NULL AFTER ymax");
            sql_call("UPDATE edomiProject.editChartList SET ygrid1=1,ygrid2=20,ygrid3=0,yshow=0,yticks=10,ynice=1");
        }
    }
    if ($version < 1.26) {
        $logUpdate .= '1.26/';
        if (!sql_columnExists('edomiProject.editChartList', 'charttyp2')) {
            sql_call("ALTER TABLE edomiProject.editChartList ADD COLUMN charttyp2 TINYINT UNSIGNED DEFAULT NULL");
            sql_call("ALTER TABLE edomiProject.editChartList ADD COLUMN ss1 BIGINT UNSIGNED DEFAULT NULL");
            sql_call("ALTER TABLE edomiProject.editChartList ADD COLUMN ss2 BIGINT UNSIGNED DEFAULT NULL");
            sql_call("ALTER TABLE edomiProject.editChartList ADD COLUMN ss3 BIGINT UNSIGNED DEFAULT NULL");
            sql_call("ALTER TABLE edomiProject.editChartList ADD COLUMN ss4 BIGINT UNSIGNED DEFAULT NULL");
            sql_call("ALTER TABLE edomiProject.editChartList ADD COLUMN xinterval BIGINT UNSIGNED DEFAULT NULL");
            sql_call("ALTER TABLE edomiProject.editChartList ADD COLUMN yminmax TINYINT UNSIGNED DEFAULT NULL");
            sql_call("UPDATE edomiProject.editChartList SET charttyp2=0,xinterval=0,yminmax=1");
        }
    }
    if ($version < 1.28) {
        $logUpdate .= '1.28/';
        if (!sql_columnExists('edomiProject.editChartList', 'extend1')) {
            sql_call("ALTER TABLE edomiProject.editChartList ADD COLUMN extend1 TINYINT UNSIGNED DEFAULT NULL");
            sql_call("ALTER TABLE edomiProject.editChartList ADD COLUMN extend2 TINYINT UNSIGNED DEFAULT NULL");
            sql_call("ALTER TABLE edomiProject.editChartList ADD COLUMN yshowvalue TINYINT UNSIGNED DEFAULT NULL");
            sql_call("UPDATE edomiProject.editChartList SET extend1=0,extend2=0,yshowvalue=0");
        }
    }
    if ($version < 1.29) {
        $logUpdate .= '1.29/';
        sql_call("UPDATE edomiProject.editChartList SET s2=100 WHERE s2=0 OR s2 IS NULL");
        sql_call("UPDATE edomiProject.editChartList SET s3=1 WHERE s3=0 OR s3 IS NULL");
        sql_call("UPDATE edomiProject.editChartList SET ss2=100 WHERE ss2=0 OR ss2 IS NULL");
        sql_call("UPDATE edomiProject.editChartList SET ss3=1 WHERE ss3=0 OR ss3 IS NULL");
        if (!sql_columnExists('edomiProject.editArchivKo', 'delay')) {
            sql_call("ALTER TABLE edomiProject.editArchivKo ADD COLUMN delay BIGINT UNSIGNED NOT NULL DEFAULT 0 AFTER keep");
            sql_call("ALTER TABLE edomiProject.editArchivMsg ADD COLUMN delay BIGINT UNSIGNED NOT NULL DEFAULT 0 AFTER keep");
            sql_call("ALTER TABLE edomiProject.editArchivCam ADD COLUMN delay BIGINT UNSIGNED NOT NULL DEFAULT 0 AFTER keep");
        }
    }
    if ($version < 1.30) {
        $logUpdate .= '1.30/';
        sql_call("DELETE FROM edomiProject.editVisuElementDesign WHERE NOT EXISTS (SELECT id FROM edomiProject.editVisuElement WHERE edomiProject.editVisuElement.id=edomiProject.editVisuElementDesign.targetid)");
        if (!sql_columnExists('edomiProject.editProjectInfo', 'text')) {
            sql_call("ALTER TABLE edomiProject.editProjectInfo ADD COLUMN text VARCHAR(10000) DEFAULT NULL");
        }
    }
    if ($version < 1.31) {
        $logUpdate .= '1.31/';
        if (!sql_columnExists('edomiProject.editProjectInfo', 'text')) {
            sql_call("ALTER TABLE edomiProject.editProjectInfo ADD COLUMN text VARCHAR(10000) DEFAULT NULL");
        }
    }
    if ($version < 1.32) {
        $logUpdate .= '1.32/';
        sql_call("ALTER TABLE edomiProject.editKo MODIFY valuetyp INT UNSIGNED NOT NULL DEFAULT 0");
        sql_call("ALTER TABLE edomiLive.ko MODIFY valuetyp INT UNSIGNED NOT NULL DEFAULT 0");
    }
    if ($version < 1.34) {
        $logUpdate .= '1.34/';
        sql_call("UPDATE edomiProject.editCam SET mask=REPLACE(mask,',ff0000,',',!000000,')");
    }
    if ($version < 1.35) {
        $logUpdate .= '1.35/';
        sql_call("UPDATE edomiProject.editRoot SET name='Emails' WHERE id=120");
        sql_call("UPDATE edomiProject.editRoot SET name='System-Emails' WHERE id=121");
        sql_call("UPDATE edomiProject.editRoot SET name='Fernzugriff' WHERE id=140");
    }
    if ($version < 1.36) {
        $logUpdate .= '1.36/';
        if (!sql_columnExists('edomiProject.editVisuElementDef', 's48')) {
            sql_call("ALTER TABLE edomiProject.editVisuElementDef ADD COLUMN s48 VARCHAR(1000) DEFAULT NULL AFTER s47");
            sql_call("ALTER TABLE edomiProject.editVisuElementDesign ADD COLUMN s48 VARCHAR(1000) DEFAULT NULL AFTER s47");
        }
        sql_call("UPDATE edomiProject.editVisuElement SET var3=3,var4=7,var5=3,var6=70 WHERE controltyp=21");
        if (!sql_columnExists('edomiProject.editChart', 'xunit')) {
            sql_call("ALTER TABLE edomiProject.editChart ADD COLUMN xinterval BIGINT DEFAULT NULL AFTER mode");
            sql_call("ALTER TABLE edomiProject.editChart ADD COLUMN xunit TINYINT DEFAULT -1 AFTER mode");
        }
    }
    if ($version < 1.37) {
        $logUpdate .= '1.37/';
    }
    if ($version < 1.38) {
        $logUpdate .= '1.38/';
        if (!sql_columnExists('edomiProject.editVisu', 'indicolor2')) {
            sql_call("ALTER TABLE edomiProject.editVisu ADD COLUMN indicolor2 BIGINT UNSIGNED DEFAULT 0 AFTER indicolor");
        }
        sql_call("UPDATE edomiProject.editVisuElement SET var3=3 WHERE controltyp=22");
        sql_call("UPDATE edomiProject.editVisuElement SET var3=1 WHERE controltyp=23");
    }
    if ($version < 1.39) {
        $logUpdate .= '1.39/';
        sql_call("ALTER TABLE edomiProject.editLogicCmdList ADD KEY (outmode), ADD KEY (outid), ADD KEY (outvalue)");
        sql_call("ALTER TABLE edomiProject.editVisuCmdList ADD KEY (outmode), ADD KEY (outid), ADD KEY (outvalue)");
        sql_call("ALTER TABLE edomiProject.editSequenceList ADD KEY (outmode), ADD KEY (outid), ADD KEY (outvalue)");
        if (!sql_columnExists('edomiProject.editVisuUserList', 'visuspeech')) {
            sql_call("ALTER TABLE edomiProject.editVisuUserList ADD COLUMN visuspeech VARCHAR(10000) DEFAULT NULL AFTER visusoundid");
        }
        if (!sql_columnExists('edomiLive.visuUserList', 'visuspeech')) {
            sql_call("ALTER TABLE edomiLive.visuUserList ADD COLUMN visuspeech VARCHAR(10000) DEFAULT NULL AFTER visusoundid");
        }
        if (!sql_columnExists('edomiProject.editVisuUser', 'gaid')) {
            sql_call("ALTER TABLE edomiProject.editVisuUser ADD COLUMN gaid BIGINT UNSIGNED DEFAULT NULL AFTER pass");
        }
        if (!sql_columnExists('edomiLive.visuUser', 'gaid')) {
            sql_call("ALTER TABLE edomiLive.visuUser ADD COLUMN gaid BIGINT UNSIGNED DEFAULT NULL AFTER pass");
        }
        if (!sql_columnExists('edomiProject.editKo', 'text')) {
            sql_call("ALTER TABLE edomiProject.editKo ADD COLUMN text VARCHAR(1000) DEFAULT NULL AFTER vlist");
        }
        if (!sql_columnExists('edomiLive.ko', 'text')) {
            sql_call("ALTER TABLE edomiLive.ko ADD COLUMN text VARCHAR(1000) DEFAULT NULL AFTER vlist");
        }
        sql_call("INSERT INTO edomiProject.editKo (id,name,folderid,ga,gatyp,valuetyp,value,defaultvalue,initscan,initsend,requestable,remanent,vmin,vmax,vstep,vlist) VALUES
			(17, 'Trigger: Jährlich 00:00:00', 33, '17', 2, 0, NULL, NULL, 0, 0, 0, 0, NULL, NULL, NULL, NULL),
			(18, 'Trigger: Monatlich 00:00:00', 33, '18', 2, 0, NULL, NULL, 0, 0, 0, 0, NULL, NULL, NULL, NULL),
			(19, 'Trigger: Wöchentlich (Montags) 00:00:00', 33, '19', 2, 0, NULL, NULL, 0, 0, 0, 0, NULL, NULL, NULL, NULL)
		");
    }
    if ($version < 1.40) {
        $logUpdate .= '1.40/';
        if (!sql_columnExists('edomiLive.ko', 'text')) {
            sql_call("ALTER TABLE edomiLive.ko ADD COLUMN text VARCHAR(1000) DEFAULT NULL AFTER vlist");
        }
    }
    if ($version < 1.41) {
        $logUpdate .= '1.41/';
        if (!sql_columnExists('edomiProject.editVisuUser', 'autologout')) {
            sql_call("ALTER TABLE edomiProject.editVisuUser ADD COLUMN autologout TINYINT UNSIGNED DEFAULT 0 AFTER touchscroll");
        }
        if (!sql_columnExists('edomiLive.visuUser', 'autologout')) {
            sql_call("ALTER TABLE edomiLive.visuUser ADD COLUMN autologout TINYINT UNSIGNED DEFAULT 0 AFTER touchscroll");
        }
        sql_call("INSERT INTO edomiProject.editKo (id,name,folderid,ga,gatyp,valuetyp,value,defaultvalue,initscan,initsend,requestable,remanent,vmin,vmax,vstep,vlist) VALUES
			(11, 'Zeitumstellung', 33, '11', 2, 0, NULL, NULL, 0, 0, 0, 1, NULL, NULL, NULL, NULL)
		");
    }
    if ($version < 1.42) {
        $logUpdate .= '1.42/';
        if (!sql_columnExists('edomiProject.editKo', 'vcsv')) {
            sql_call("ALTER TABLE edomiProject.editKo ADD COLUMN vcsv VARCHAR(1000) DEFAULT NULL AFTER vlist");
        }
        if (!sql_columnExists('edomiLive.ko', 'vcsv')) {
            sql_call("ALTER TABLE edomiLive.ko ADD COLUMN vcsv VARCHAR(1000) DEFAULT NULL AFTER vlist");
        }
        sql_call("CREATE TABLE IF NOT EXISTS edomiProject.editVisuFont (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			folderid BIGINT UNSIGNED DEFAULT NULL,
			name VARCHAR(200) DEFAULT NULL,
			fonttyp TINYINT UNSIGNED DEFAULT 0,
			fontname VARCHAR(200) DEFAULT NULL,
			fontstyle TINYINT UNSIGNED DEFAULT 0,
			fontweight TINYINT UNSIGNED DEFAULT 0,
			PRIMARY KEY (id),
			KEY (folderid)
			) ENGINE=MyISAM DEFAULT CHARSET=latin1");
        sql_call("INSERT INTO edomiProject.editRoot (id,allow,path,parentid,name,namedb,linkdb,linkid) VALUES
				(150, 'X--X--xxxx-x', '/20/', 20, 'Schriftarten', 'editVisuFont', NULL, NULL)
			");
        sql_call("ALTER TABLE edomiProject.editVisuElementDef MODIFY s13 BIGINT UNSIGNED DEFAULT NULL");
        sql_call("ALTER TABLE edomiProject.editVisuElementDesign MODIFY s13 BIGINT UNSIGNED DEFAULT NULL");
        sql_call("UPDATE edomiProject.editVisuElementDef SET s13=NULL");
        sql_call("UPDATE edomiProject.editVisuElementDesign SET s13=NULL");
        sql_call("INSERT INTO edomiProject.editKo (id,name,folderid,ga,gatyp,valuetyp,value,defaultvalue,initscan,initsend,requestable,remanent,vmin,vmax,vstep,vlist) VALUES
			(12, 'EDOMI-Update', 33, '12', 2, 0, NULL, NULL, 0, 0, 0, 0, NULL, NULL, NULL, NULL)
		");
    }
    if ($version < 1.43) {
        $logUpdate .= '1.43/';
        sql_call("ALTER TABLE edomiProject.editChart ADD COLUMN ymin FLOAT DEFAULT NULL");
        sql_call("ALTER TABLE edomiProject.editChart ADD COLUMN ymax FLOAT DEFAULT NULL");
        sql_call("ALTER TABLE edomiProject.editChart ADD COLUMN yticks INT UNSIGNED DEFAULT NULL");
        sql_call("ALTER TABLE edomiProject.editChart ADD COLUMN ynice TINYINT UNSIGNED DEFAULT NULL");
        sql_call("UPDATE edomiProject.editChart SET yticks=0,ynice=1");
        sql_call("ALTER TABLE edomiProject.editChartList ADD COLUMN yscale TINYINT UNSIGNED DEFAULT NULL");
        sql_call("UPDATE edomiProject.editChartList SET yscale=0");
    }
    if ($version < 1.45) {
        $logUpdate .= '1.45/';
        sql_call("ALTER TABLE edomiProject.editVisuPage ADD COLUMN globalinclude TINYINT UNSIGNED DEFAULT NULL AFTER includeid");
        sql_call("UPDATE edomiProject.editVisuPage SET globalinclude=1");
        $ss1 = sql_call("SELECT * FROM edomiProject.editVisuElement WHERE controltyp=27");
        while ($n = sql_result($ss1)) {
            $ctyp = 0;
            if ($n['var1'] == 0 || $n['var1'] == 5) {
                $ctyp = 17;
                $dest['var1'] = 2;
                $dest['var2'] = $n['var6'];
                $dest['var3'] = 1;
                $dest['var4'] = 0;
                $dest['var5'] = $n['var5'];
                $dest['var6'] = $n['var7'];
                if ($n['var2'] == 0) {
                    $dest['var6'] = 0;
                }
                $dest['var7'] = 30;
                if ($n['var1'] == 0) {
                    $dest['var7'] = 0;
                }
                $dest['var8'] = 0;
                $dest['var9'] = 0;
            } else {
                if ($n['var1'] == 1 || $n['var1'] == 2) {
                    $dest['var1'] = 2;
                    $ctyp = 27;
                }
                if ($n['var1'] == 3 || $n['var1'] == 6) {
                    $dest['var1'] = 5;
                    $ctyp = 27;
                }
                if ($n['var1'] == 4 || $n['var1'] == 7) {
                    $dest['var1'] = 6;
                    $ctyp = 27;
                }
                if ($n['var1'] == 8 || $n['var1'] == 9) {
                    $dest['var1'] = 3;
                    $ctyp = 27;
                }
                $dest['var2'] = $n['var6'];
                $dest['var3'] = $n['var3'];
                $dest['var4'] = $n['var4'];
                $dest['var5'] = $n['var5'];
                $dest['var6'] = 0;
                $dest['var7'] = $n['var7'];
                if ($n['var2'] == 0) {
                    $dest['var7'] = 0;
                }
                $dest['var8'] = 0;
                if ($n['var1'] == 1 || $n['var1'] == 6 || $n['var1'] == 7 || $n['var1'] == 8) {
                    $dest['var9'] = 45;
                    $dest['var10'] = 315;
                } else {
                    $dest['var9'] = 0;
                    $dest['var10'] = 360;
                }
            }
            if ($ctyp > 0) {
                sql_call("UPDATE edomiProject.editVisuElement SET var1='" . $dest['var1'] . "',var2='" . $dest['var2'] . "',var3='" . $dest['var3'] . "',var4='" . $dest['var4'] . "',var5='" . $dest['var5'] . "',var6='" . $dest['var6'] . "',var7='" . $dest['var7'] . "',var8='" . $dest['var8'] . "',var9='" . $dest['var9'] . "',var10='" . $dest['var10'] . "',controltyp=" . $ctyp . " WHERE (controltyp=27 AND id=" . $n['id'] . ")");
            }
        }
        sql_call("INSERT INTO edomiProject.editKo (id,name,folderid,ga,gatyp,valuetyp,value,defaultvalue,initscan,initsend,requestable,remanent,vmin,vmax,vstep,vlist) VALUES (15, 'Anrufmonitor: Rohdaten', 33, '15', 2, 0, NULL, NULL, 0, 0, 0, 0, NULL, NULL, NULL, NULL)");
        sql_call("ALTER TABLE edomiProject.editVisuUser ADD COLUMN gaid2 BIGINT UNSIGNED DEFAULT NULL AFTER gaid");
        sql_call("ALTER TABLE edomiLive.visuUser ADD COLUMN gaid2 BIGINT UNSIGNED DEFAULT NULL AFTER gaid");
    }
    if ($version < 1.47) {
        $logUpdate .= '1.47/';
        sql_call("UPDATE edomiProject.editKo SET name='Version',ga=1,gatyp=2,valuetyp=0,value=NULL,defaultvalue=NULL WHERE id=1");
        sql_call("UPDATE edomiLive.ko SET name='Version',ga=1,gatyp=2,valuetyp=0,value=NULL,defaultvalue=NULL WHERE id=1");
    }
    if ($version < 1.48) {
        $logUpdate .= '1.48/';
        $ss1 = sql_call("SELECT * FROM edomiProject.editRoot WHERE path='/20/22/'");
        while ($n = sql_result($ss1)) {
            $tmp = rtrim(substr($n['name'], 0, strrpos($n['name'], '(')));
            if (!isEmpty($tmp)) {
                sql_call("UPDATE edomiProject.editRoot SET name='" . sql_encodeValue($tmp) . "' WHERE id=" . $n['id']);
            }
        }
        sql_close($ss1);
    }
    if ($version < 1.49) {
        $logUpdate .= '1.49/';
        sql_call("INSERT INTO edomiProject.editRoot (id,allow,path,parentid,name,namedb,linkdb,linkid) VALUES (101, 'X--X--xxxxxx', '/', 0, 'Terminschaltuhren', 'editAgenda', NULL, NULL)");
        sql_call("CREATE TABLE IF NOT EXISTS edomiProject.editAgenda (id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,folderid BIGINT UNSIGNED DEFAULT NULL,name VARCHAR(100) DEFAULT NULL,gaid BIGINT UNSIGNED DEFAULT NULL,outgaid BIGINT UNSIGNED DEFAULT NULL,PRIMARY KEY (id),KEY (gaid)) ENGINE=MyISAM DEFAULT CHARSET=latin1");
        sql_call("CREATE TABLE IF NOT EXISTS edomiProject.editAgendaData (id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,targetid BIGINT UNSIGNED DEFAULT 0,name VARCHAR(100) DEFAULT NULL,fixed TINYINT UNSIGNED NOT NULL DEFAULT 0,status VARCHAR(10000) DEFAULT NULL,hour SMALLINT UNSIGNED DEFAULT NULL,minute SMALLINT UNSIGNED DEFAULT NULL,date1 DATE DEFAULT NULL,date2 DATE DEFAULT NULL,step BIGINT UNSIGNED DEFAULT 0,unit SMALLINT UNSIGNED DEFAULT 0,PRIMARY KEY (id),KEY (targetid),KEY (hour),KEY (minute),KEY (date1),KEY (date2),KEY (step),KEY (unit)) ENGINE=MyISAM DEFAULT CHARSET=latin1");
        sql_call("UPDATE edomiProject.editTimerData SET day1=NULL WHERE day1=0");
        sql_call("UPDATE edomiProject.editTimerData SET day2=NULL WHERE day2=9999");
        sql_call("UPDATE edomiProject.editTimerData SET month1=NULL WHERE month1=0");
        sql_call("UPDATE edomiProject.editTimerData SET month2=NULL WHERE month2=9999");
        sql_call("UPDATE edomiProject.editTimerData SET year1=NULL WHERE year1=0");
        sql_call("UPDATE edomiProject.editTimerData SET year2=NULL WHERE year2=9999");
        sql_call("UPDATE edomiLive.timerData SET day1=NULL WHERE day1=0");
        sql_call("UPDATE edomiLive.timerData SET day2=NULL WHERE day2=9999");
        sql_call("UPDATE edomiLive.timerData SET month1=NULL WHERE month1=0");
        sql_call("UPDATE edomiLive.timerData SET month2=NULL WHERE month2=9999");
        sql_call("UPDATE edomiLive.timerData SET year1=NULL WHERE year1=0");
        sql_call("UPDATE edomiLive.timerData SET year2=NULL WHERE year2=9999");
        sql_call("ALTER TABLE edomiProject.editTimerData DROP COLUMN pointer");
        sql_call("ALTER TABLE edomiLive.timerData DROP COLUMN pointer");
    }
    if ($version < 1.50) {
        $logUpdate .= '1.50/';
        $ss1 = sql_call("SELECT * FROM edomiProject.editRoot WHERE path='/20/22/'");
        while ($n = sql_result($ss1)) {
            $ss2 = sql_call("SELECT * FROM edomiProject.editRoot WHERE path LIKE '%/" . $n['id'] . "/%'");
            while ($nn = sql_result($ss2)) {
                $tmp = explode('/', $nn['path']);
                $tmp2 = array();
                $tmp3 = 0;
                for ($t = 0; $t < count($tmp); $t++) {
                    if ($tmp[$t] > 0 && $tmp[$t] != $n['id']) {
                        $tmp2[] = $tmp[$t];
                        $tmp3 = $tmp[$t];
                    }
                }
                $tmp = implode($tmp2, '/');
                sql_call("UPDATE edomiProject.editRoot SET path='/" . $tmp . "/',parentid=" . $tmp3 . " WHERE id=" . $nn['id']);
            }
            sql_close($ss2);
            sql_call("UPDATE edomiProject.editVisuPage SET folderid=22 WHERE folderid=" . $n['id']);
            sql_call("DELETE FROM edomiProject.editRoot WHERE id=" . $n['id']);
        }
        sql_close($ss1);
        sql_call("UPDATE edomiProject.editRoot SET linkdb='22' WHERE linkdb='editVisu'");
        sql_call("ALTER TABLE edomiProject.editRoot CHANGE linkdb link BIGINT UNSIGNED NOT NULL DEFAULT 0");
        sql_call("ALTER TABLE edomiProject.editRoot CHANGE allow allow TINYINT UNSIGNED DEFAULT NULL");
        sql_call("UPDATE edomiProject.editRoot SET allow=NULL WHERE id>=1000");
        sql_call("ALTER TABLE edomiProject.editRoot ADD COLUMN rootid BIGINT UNSIGNED DEFAULT NULL AFTER path");
        sql_call("ALTER TABLE edomiProject.editRoot ADD COLUMN sortcolumns VARCHAR(100) DEFAULT NULL AFTER linkid");
        sql_call("ALTER TABLE edomiProject.editRoot ADD COLUMN sortid TINYINT DEFAULT 0 AFTER linkid");
        $ss1 = sql_call("SELECT * FROM edomiProject.editRoot WHERE id>=1000");
        while ($n = sql_result($ss1)) {
            $rootid = dbRoot_getRootId($n['id']);
            if (!($rootid > 0)) {
                $rootid = 0;
            }
            sql_call("UPDATE edomiProject.editRoot SET rootid='" . $rootid . "' WHERE id=" . $n['id']);
        }
        sql_close($ss1);
        sql_call("INSERT INTO edomiProject.editRoot (id,allow,path,rootid,parentid,name,namedb,link,linkid,sortcolumns,sortid) VALUES (122,8,'/120/',122,120,'System-Emails','editEmail',0,NULL,'id/ID,name/Name',1)");
        sql_call("UPDATE edomiProject.editEmail SET folderid=122 WHERE folderid=121");
        sql_call("UPDATE edomiProject.editEmail SET folderid=121 WHERE folderid=120");
        $ss1 = sql_call("SELECT * FROM edomiProject.editRoot WHERE id>=1000 AND path LIKE '/120/%'");
        while ($n = sql_result($ss1)) {
            $newpath = str_replace('/120/', '/120/121/', $n['path']);
            $tmp = explode('/', $newpath);
            $parentid = 0;
            for ($t = 0; $t < count($tmp); $t++) {
                if ($tmp[$t] > 0) {
                    $parentid = $tmp[$t];
                }
            }
            sql_call("UPDATE edomiProject.editRoot SET rootid=121,path='" . $newpath . "',parentid=" . $parentid . " WHERE id=" . $n['id']);
        }
        sql_close($ss1);
        sql_call("UPDATE edomiProject.editRoot SET allow=0,rootid=id WHERE id=10");
        sql_call("UPDATE edomiProject.editRoot SET allow=94,rootid=id,sortcolumns='id/ID,name/Name',sortid=1 WHERE id=11");
        sql_call("UPDATE edomiProject.editRoot SET allow=8,rootid=id,sortcolumns='id/ID,name/Name',sortid=1 WHERE id=12");
        sql_call("UPDATE edomiProject.editRoot SET allow=8,rootid=id WHERE id=13");
        sql_call("UPDATE edomiProject.editRoot SET allow=8,rootid=id WHERE id=14");
        sql_call("UPDATE edomiProject.editRoot SET allow=8,rootid=id WHERE id=15");
        sql_call("UPDATE edomiProject.editRoot SET allow=8,rootid=id WHERE id=16");
        sql_call("UPDATE edomiProject.editRoot SET allow=8,rootid=id WHERE id=17");
        sql_call("UPDATE edomiProject.editRoot SET allow=8,rootid=id WHERE id=18");
        sql_call("UPDATE edomiProject.editRoot SET allow=94,name='Eigene Logikbausteine (19)',rootid=id WHERE id=19");
        sql_call("UPDATE edomiProject.editRoot SET allow=0,rootid=id WHERE id=20");
        sql_call("UPDATE edomiProject.editRoot SET allow=106,rootid=id,sortcolumns='id/ID,name/Name',sortid=1 WHERE id=21");
        sql_call("UPDATE edomiProject.editRoot SET allow=127,rootid=id,sortcolumns='id/ID,name/Name',sortid=1 WHERE id=22");
        sql_call("UPDATE edomiProject.editRoot SET allow=126,rootid=id,sortcolumns='id/ID,login/Login',sortid=1 WHERE id=23");
        sql_call("UPDATE edomiProject.editRoot SET allow=126,rootid=id,sortcolumns='id/ID,name/Name',sortid=1 WHERE id=24");
        sql_call("UPDATE edomiProject.editRoot SET allow=126,rootid=id,sortcolumns='id/ID,name/Name',sortid=1 WHERE id=25");
        sql_call("UPDATE edomiProject.editRoot SET allow=126,rootid=id,sortcolumns='id/ID,name/Name',sortid=1 WHERE id=26");
        sql_call("UPDATE edomiProject.editRoot SET allow=126,rootid=id,sortcolumns='id/ID,name/Name',sortid=1 WHERE id=27");
        sql_call("UPDATE edomiProject.editRoot SET allow=94,rootid=id,sortcolumns='id/ID,name/Name',sortid=1 WHERE id=28");
        sql_call("UPDATE edomiProject.editRoot SET allow=94,rootid=id,sortcolumns='id/ID,name/Name',sortid=1 WHERE id=29");
        sql_call("UPDATE edomiProject.editRoot SET allow=0,rootid=id,namedb='editKo' WHERE id=30");
        sql_call("UPDATE edomiProject.editRoot SET allow=126,rootid=id,sortcolumns='id/ID,name/Name',sortid=1 WHERE id=31");
        sql_call("UPDATE edomiProject.editRoot SET allow=94,rootid=id,sortcolumns='id/ID,name/Name,ga/GA',sortid=1 WHERE id=32");
        sql_call("UPDATE edomiProject.editRoot SET allow=8,rootid=id,sortcolumns='id/ID,name/Name',sortid=1 WHERE id=33");
        sql_call("UPDATE edomiProject.editRoot SET allow=126,rootid=id,sortcolumns='id/ID,name/Name',sortid=1 WHERE id=40");
        sql_call("UPDATE edomiProject.editRoot SET allow=126,rootid=id,sortcolumns='id/ID,name/Name',sortid=1 WHERE id=50");
        sql_call("UPDATE edomiProject.editRoot SET allow=126,rootid=id,sortcolumns='id/ID,name/Name',sortid=1 WHERE id=60");
        sql_call("UPDATE edomiProject.editRoot SET allow=126,rootid=id,sortcolumns='id/ID,name/Name',sortid=1 WHERE id=70");
        sql_call("UPDATE edomiProject.editRoot SET allow=126,rootid=id,sortcolumns='id/ID,name/Name',sortid=1,namedb='editIr' WHERE id=75");
        sql_call("UPDATE edomiProject.editRoot SET namedb='editIr' WHERE namedb='editIRtrans'");
        sql_call("RENAME TABLE edomiProject.editIRtrans TO edomiProject.editIr");
        sql_call("RENAME TABLE edomiLive.IRtrans TO edomiLive.ir");
        sql_call("UPDATE edomiProject.editRoot SET allow=0,rootid=id WHERE id=80");
        sql_call("UPDATE edomiProject.editRoot SET allow=126,rootid=id,sortcolumns='id/ID,name/Name',sortid=1 WHERE id=81");
        sql_call("UPDATE edomiProject.editRoot SET allow=126,rootid=id,sortcolumns='id/ID,name/Name',sortid=1 WHERE id=82");
        sql_call("UPDATE edomiProject.editRoot SET allow=126,rootid=id,sortcolumns='id/ID,name/Name',sortid=1 WHERE id=90");
        sql_call("UPDATE edomiProject.editRoot SET allow=126,rootid=id,sortcolumns='id/ID,name/Name',sortid=1 WHERE id=100");
        sql_call("UPDATE edomiProject.editRoot SET allow=126,rootid=id,sortcolumns='id/ID,name/Name',sortid=1 WHERE id=101");
        sql_call("UPDATE edomiProject.editRoot SET allow=126,rootid=id,sortcolumns='id/ID,name/Name',sortid=1 WHERE id=110");
        sql_call("UPDATE edomiProject.editRoot SET allow=0,rootid=id,namedb='editEmail' WHERE id=120");
        sql_call("UPDATE edomiProject.editRoot SET allow=126,rootid=id,path='/120/',name='Eigene Emails',namedb='editEmail',sortcolumns='id/ID,name/Name',sortid=1 WHERE id=121");
        sql_call("UPDATE edomiProject.editRoot SET allow=0,rootid=id WHERE id=124");
        sql_call("UPDATE edomiProject.editRoot SET allow=94,rootid=id,sortcolumns='id/ID,name/Name',sortid=1 WHERE id=125");
        sql_call("UPDATE edomiProject.editRoot SET allow=126,rootid=id,sortcolumns='id/ID,name/Name',sortid=1 WHERE id=126");
        sql_call("UPDATE edomiProject.editRoot SET allow=126,rootid=id,sortcolumns='id/ID,name/Name',sortid=1 WHERE id=127");
        sql_call("UPDATE edomiProject.editRoot SET allow=126,rootid=id,sortcolumns='id/ID,name/Name',sortid=1 WHERE id=130");
        sql_call("UPDATE edomiProject.editRoot SET allow=94,rootid=id,sortcolumns='id/ID,name/Name',sortid=1 WHERE id=140");
        sql_call("UPDATE edomiProject.editRoot SET allow=94,rootid=id,sortcolumns='id/ID,name/Name',sortid=1 WHERE id=150");
        sql_call("UPDATE edomiProject.editVisuElement SET groupid=0 WHERE groupid IS NULL");
        sql_call("ALTER TABLE edomiProject.editVisuElement CHANGE groupid groupid BIGINT UNSIGNED NOT NULL DEFAULT 0");
        sql_call("ALTER TABLE edomiProject.editVisuElement ADD COLUMN layer INT UNSIGNED DEFAULT 0 AFTER groupid");
        sql_call("ALTER TABLE edomiProject.editLogicElement ADD COLUMN layer INT UNSIGNED DEFAULT 0 AFTER name");
        sql_call("DROP TABLE IF EXISTS edomiProject.editLogicElementDef");
        sql_call("CREATE TABLE IF NOT EXISTS edomiProject.editLogicElementDef (
				id BIGINT UNSIGNED NOT NULL,
				name VARCHAR(100) DEFAULT NULL,
				folderid BIGINT UNSIGNED DEFAULT NULL,
				defin BIGINT UNSIGNED DEFAULT 0,
				defout BIGINT UNSIGNED DEFAULT 0,
				defvar BIGINT UNSIGNED DEFAULT 0,
				errcount BIGINT UNSIGNED NOT NULL DEFAULT 0,
				errmsg VARCHAR(10000) DEFAULT NULL,
				exec TINYINT UNSIGNED DEFAULT 0,
				KEY (id)
				) ENGINE=MyISAM DEFAULT CHARSET=latin1");
        sql_call("CREATE TABLE IF NOT EXISTS edomiProject.editLogicElementDefIn (
				targetid BIGINT UNSIGNED DEFAULT NULL,
				id BIGINT UNSIGNED DEFAULT NULL,
				name VARCHAR(100) DEFAULT NULL,
				value VARCHAR(10000) DEFAULT NULL,
				color TINYINT UNSIGNED DEFAULT 0,
				KEY (targetid),
				KEY (id)
				) ENGINE=MyISAM DEFAULT CHARSET=latin1");
        sql_call("CREATE TABLE IF NOT EXISTS edomiProject.editLogicElementDefOut (
				targetid BIGINT UNSIGNED DEFAULT NULL,
				id BIGINT UNSIGNED DEFAULT NULL,
				name VARCHAR(100) DEFAULT NULL,
				KEY (targetid),
				KEY (id)
				) ENGINE=MyISAM DEFAULT CHARSET=latin1");
        sql_call("CREATE TABLE IF NOT EXISTS edomiProject.editLogicElementDefVar (
				targetid BIGINT UNSIGNED DEFAULT NULL,
				id BIGINT UNSIGNED DEFAULT NULL,
				value VARCHAR(10000) DEFAULT NULL,
				KEY (targetid),
				KEY (id)
				) ENGINE=MyISAM DEFAULT CHARSET=latin1");
        sql_call("ALTER TABLE edomiProject.editVisuElement ADD KEY (name),ADD KEY (groupid),ADD KEY (layer),ADD KEY (tmp)");
        sql_call("ALTER TABLE edomiProject.editAgenda ADD KEY (folderid),ADD KEY (name)");
        sql_call("ALTER TABLE edomiProject.editArchivCam ADD KEY (folderid),ADD KEY (name)");
        sql_call("ALTER TABLE edomiProject.editArchivKo ADD KEY (folderid),ADD KEY (name)");
        sql_call("ALTER TABLE edomiProject.editArchivMsg ADD KEY (folderid),ADD KEY (name)");
        sql_call("ALTER TABLE edomiProject.editArchivPhone ADD KEY (folderid),ADD KEY (name)");
        sql_call("ALTER TABLE edomiProject.editAws ADD KEY (folderid),ADD KEY (name)");
        sql_call("ALTER TABLE edomiProject.editCam ADD KEY (folderid),ADD KEY (name)");
        sql_call("ALTER TABLE edomiProject.editChart ADD KEY (name)");
        sql_call("ALTER TABLE edomiProject.editEmail ADD KEY (name)");
        sql_call("ALTER TABLE edomiProject.editHttpKo ADD KEY (name)");
        sql_call("ALTER TABLE edomiProject.editLogicPage ADD KEY (name)");
        sql_call("ALTER TABLE edomiProject.editPhoneBook ADD KEY (name)");
        sql_call("ALTER TABLE edomiProject.editPhoneCall ADD KEY (name)");
        sql_call("ALTER TABLE edomiProject.editVisu ADD KEY (name)");
        sql_call("ALTER TABLE edomiProject.editVisuAnim ADD KEY (name)");
        sql_call("ALTER TABLE edomiProject.editVisuBGcol ADD KEY (name)");
        sql_call("ALTER TABLE edomiProject.editVisuElementDef ADD KEY (name)");
        sql_call("ALTER TABLE edomiProject.editVisuFGcol ADD KEY (name)");
        sql_call("ALTER TABLE edomiProject.editVisuFont ADD KEY (name)");
        sql_call("ALTER TABLE edomiProject.editVisuImg ADD KEY (name)");
        sql_call("ALTER TABLE edomiProject.editVisuPage ADD KEY (name)");
        sql_call("ALTER TABLE edomiProject.editVisuSnd ADD KEY (name)");
        sql_call("ALTER TABLE edomiProject.editIp ADD KEY (folderid),ADD KEY (name)");
        sql_call("ALTER TABLE edomiProject.editIr ADD KEY (folderid),ADD KEY (name)");
        sql_call("ALTER TABLE edomiProject.editKo ADD KEY (folderid),ADD KEY (name)");
        sql_call("ALTER TABLE edomiProject.editLogicElementDef ADD KEY (folderid),ADD KEY (name)");
        sql_call("ALTER TABLE edomiProject.editScene ADD KEY (folderid),ADD KEY (name)");
        sql_call("ALTER TABLE edomiProject.editSequence ADD KEY (folderid),ADD KEY (name)");
        sql_call("ALTER TABLE edomiProject.editTimer ADD KEY (folderid),ADD KEY (name)");
        sql_call("ALTER TABLE edomiProject.editRoot ADD KEY (tmp)");
    }
    if ($version < 1.51) {
        $logUpdate .= '1.51/';
        sql_call("ALTER TABLE edomiProject.editVisuPage CHANGE xsize xsize INT UNSIGNED DEFAULT NULL");
        sql_call("ALTER TABLE edomiProject.editVisuPage CHANGE ysize ysize INT UNSIGNED DEFAULT NULL");
        sql_call("ALTER TABLE edomiProject.editVisuPage ADD COLUMN ypos INT DEFAULT NULL AFTER pagetyp");
        sql_call("ALTER TABLE edomiProject.editVisuPage ADD COLUMN xpos INT DEFAULT NULL AFTER pagetyp");
        sql_call("ALTER TABLE edomiProject.editIp ADD COLUMN outgaid2 BIGINT UNSIGNED DEFAULT NULL AFTER iptyp");
        sql_call("ALTER TABLE edomiProject.editIp ADD COLUMN outgaid BIGINT UNSIGNED DEFAULT NULL AFTER iptyp");
        sql_call("ALTER TABLE edomiProject.editIp ADD COLUMN udpraw TINYINT UNSIGNED DEFAULT 0 AFTER iptyp");
        sql_call("ALTER TABLE edomiProject.editIp ADD COLUMN httptimeout INT UNSIGNED DEFAULT 10 AFTER iptyp");
        sql_call("ALTER TABLE edomiProject.editIp ADD COLUMN httperrlog TINYINT UNSIGNED DEFAULT 1 AFTER iptyp");
        sql_call("UPDATE edomiProject.editIp SET iptyp=1 WHERE iptyp=4");
    }
    if ($version < 1.52) {
        $logUpdate .= '1.52/';
        sql_call("ALTER TABLE edomiProject.editVisu DROP COLUMN refresh");
        sql_call("ALTER TABLE edomiProject.editVisu DROP COLUMN ssrefresh");
        sql_call("ALTER TABLE edomiProject.editVisu DROP COLUMN clickrefresh");
        sql_call("ALTER TABLE edomiProject.editVisu DROP COLUMN queuelatency");
        sql_call("ALTER TABLE edomiProject.editVisuUser DROP COLUMN refresh");
        sql_call("ALTER TABLE edomiProject.editVisuUser DROP COLUMN ssrefresh");
        sql_call("ALTER TABLE edomiProject.editVisuUser DROP COLUMN clickrefresh");
        sql_call("ALTER TABLE edomiProject.editVisuUser DROP COLUMN queuelatency");
        sql_call("ALTER TABLE edomiProject.editVisuUserList DROP COLUMN visutimeout");
        sql_call("ALTER TABLE edomiLive.visuUserList DROP COLUMN visutimeout");
        sql_call("UPDATE edomiProject.editVisuElement SET var5=2 WHERE controltyp=20");
        sql_call("UPDATE edomiProject.editVisuElement SET var7=1 WHERE controltyp=21");
        sql_call("UPDATE edomiProject.editVisuElement SET var5=1 WHERE controltyp=25");
        sql_call("UPDATE edomiProject.editVisuElement SET var5=1 WHERE controltyp=26");
        sql_call("UPDATE edomiProject.editVisuElement SET var5=1 WHERE controltyp=28");
        sql_call("UPDATE edomiProject.editVisuElement SET var5=1 WHERE controltyp=29");
        sql_call("UPDATE edomiProject.editRoot SET path='/20/',parentid=20 WHERE id=130");
        sql_call("UPDATE edomiProject.editRoot SET path=CONCAT('/20',path) WHERE path LIKE '/130/%'");
        sql_call("UPDATE edomiProject.editKo SET remanent=0 WHERE id=6");
    }
    if ($version < 1.53) {
        $logUpdate .= '1.53/';
        if ($update) {
        } else {
        }
        sql_call("ALTER TABLE edomiProject.editVisuUser ADD COLUMN gaid3 BIGINT UNSIGNED DEFAULT NULL AFTER gaid2");
        sql_call("ALTER TABLE edomiProject.editVisuElement ADD COLUMN galive TINYINT UNSIGNED DEFAULT 0 AFTER gaid");
        sql_call("UPDATE edomiProject.editVisuElement SET var6=3 WHERE controltyp=20");
        sql_call("UPDATE edomiProject.editVisuElement SET var2=1,var3=255,var4=-1 WHERE controltyp=15");
        sql_call("UPDATE edomiProject.editVisuElement SET var4=-1 WHERE controltyp=10");
        sql_call("UPDATE edomiProject.editVisuElement SET var3=20 WHERE controltyp=16 AND var3=25");
        sql_call("UPDATE edomiProject.editVisuElement SET var2=6 WHERE controltyp=11 AND var1<>1");
        sql_call("UPDATE edomiProject.editVisuElement SET var2=7 WHERE controltyp=11 AND var1=1");
        sql_call("UPDATE edomiProject.editVisuElement SET var1=1,var4=-1 WHERE controltyp=11");
        sql_call("UPDATE edomiProject.editVisuElement SET var5=((var3&4)>0)|((var2>0)*2) WHERE controltyp=12");
        sql_call("UPDATE edomiProject.editVisuElement SET var2=((var3&2)>0)|2|4 WHERE controltyp=12");
        sql_call("UPDATE edomiProject.editVisuElement SET var3=3,var4=-1 WHERE controltyp=12");
        sql_call("UPDATE edomiProject.editVisuElement SET var1=0,var2=7,var4=-1 WHERE controltyp=13");
        sql_call("UPDATE edomiProject.editVisuElement SET var3=(var3&1)<>1 WHERE controltyp=13 AND ysize>xsize");
    }
    if ($version < 1.54) {
        $logUpdate .= '1.54/';
        sql_call("ALTER TABLE edomiProject.editVisuElement CHANGE galive galive INT DEFAULT 0");
        sql_call("UPDATE edomiProject.editVisuElement SET galive=-1 WHERE galive=1");
        sql_call("ALTER TABLE edomiProject.editTimerData ADD COLUMN mode TINYINT UNSIGNED DEFAULT 0 AFTER year2");
        sql_call("ALTER TABLE edomiLive.timerData ADD COLUMN mode TINYINT UNSIGNED DEFAULT 0 AFTER year2");
    }
    if ($version < 1.55) {
        $logUpdate .= '1.55/';
        $r = sql_call("CREATE TABLE IF NOT EXISTS edomiProject.editMacro (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			folderid BIGINT UNSIGNED DEFAULT NULL,
			name VARCHAR(100) DEFAULT NULL,
			PRIMARY KEY (id),
			KEY (folderid),
			KEY (name)
			) ENGINE=MyISAM DEFAULT CHARSET=latin1
		");
        $r = sql_call("CREATE TABLE IF NOT EXISTS edomiProject.editMacroCmdList (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			targetid BIGINT UNSIGNED DEFAULT 0,
			outmode TINYINT UNSIGNED DEFAULT NULL,
			outid BIGINT UNSIGNED DEFAULT NULL,
			outvalue VARCHAR(10000) DEFAULT NULL,
			PRIMARY KEY (id),
			KEY (targetid),
			KEY (outmode),
			KEY (outid),
			KEY (outvalue)
			) ENGINE=MyISAM DEFAULT CHARSET=latin1
		");
        sql_call("INSERT INTO edomiProject.editRoot (id,allow,path,rootid,parentid,name,namedb,link,linkid,sortcolumns,sortid) VALUES (95,126,'/',95,0,'Makros','editMacro',0,NULL,'id/ID,name/Name',1)");
        if ($update) {
            sql_call("DROP TABLE IF EXISTS edomiLive.timerData");
            sql_call("DROP TABLE IF EXISTS edomiLive.agendaData");
        }
        sql_call("CREATE TABLE IF NOT EXISTS edomiProject.editTimerMacroList (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			targetid BIGINT UNSIGNED DEFAULT NULL,
			timerid BIGINT UNSIGNED DEFAULT NULL,
			sort BIGINT UNSIGNED DEFAULT NULL,
			PRIMARY KEY (id),
			KEY (targetid),
			KEY (timerid),
			KEY (sort)
			) ENGINE=MyISAM DEFAULT CHARSET=latin1");
        $ss1 = sql_call("SELECT * FROM edomiProject.editTimer");
        while ($zsu = sql_result($ss1)) {
            $ss2 = sql_call("SELECT * FROM edomiProject.editTimerData WHERE targetid=" . $zsu['id']);
            while ($data = sql_result($ss2)) {
                $tmp = sql_getValues('edomiProject.editMacroCmdList', '*', "outmode=2 AND outid='" . $zsu['outgaid'] . "' AND outvalue='" . sql_encodeValue($data['status']) . "'");
                if ($tmp !== false) {
                    sql_call("UPDATE edomiProject.editTimerData SET status=" . $tmp['targetid'] . " WHERE id=" . $data['id']);
                } else {
                    sql_call("INSERT INTO edomiProject.editMacro (folderid,name) VALUES (95,'KO [" . $zsu['outgaid'] . "] = " . sql_encodeValue($data['status']) . "')");
                    $macroId = sql_insertId();
                    if ($macroId > 0) {
                        sql_call("INSERT INTO edomiProject.editMacroCmdList (targetid,outmode,outid,outvalue) VALUES (" . $macroId . ",2,'" . $zsu['outgaid'] . "','" . sql_encodeValue($data['status']) . "')");
                        $macroCmdId = sql_insertId();
                        if ($macroCmdId > 0) {
                            $sort = sql_getValue('edomiProject.editTimerMacroList', 'MAX(sort)', 'timerid=' . $zsu['id']);
                            if ($sort > 0) {
                                $sort++;
                            } else {
                                $sort = 1;
                            }
                            sql_call("INSERT INTO edomiProject.editTimerMacroList (targetid,timerid,sort) VALUES (" . $macroId . "," . $zsu['id'] . ",'" . $sort . "')");
                            sql_call("UPDATE edomiProject.editTimerData SET status=" . $macroId . " WHERE id=" . $data['id']);
                        }
                    }
                }
            }
            sql_close($ss2);
        }
        sql_close($ss1);
        sql_call("ALTER TABLE edomiProject.editTimerData CHANGE status cmdid BIGINT UNSIGNED DEFAULT 0");
        sql_call("ALTER TABLE edomiProject.editTimer DROP COLUMN outgaid");
        sql_call("UPDATE edomiProject.editVisuElement SET var2=NULL WHERE controltyp=22");
        sql_call("CREATE TABLE IF NOT EXISTS edomiProject.editAgendaMacroList (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			targetid BIGINT UNSIGNED DEFAULT NULL,
			agendaid BIGINT UNSIGNED DEFAULT NULL,
			sort BIGINT UNSIGNED DEFAULT NULL,
			PRIMARY KEY (id),
			KEY (targetid),
			KEY (agendaid),
			KEY (sort)
			) ENGINE=MyISAM DEFAULT CHARSET=latin1");
        $ss1 = sql_call("SELECT * FROM edomiProject.editAgenda");
        while ($zsu = sql_result($ss1)) {
            $ss2 = sql_call("SELECT * FROM edomiProject.editAgendaData WHERE targetid=" . $zsu['id']);
            while ($data = sql_result($ss2)) {
                $tmp = sql_getValues('edomiProject.editMacroCmdList', '*', "outmode=2 AND outid='" . $zsu['outgaid'] . "' AND outvalue='" . sql_encodeValue($data['status']) . "'");
                if ($tmp !== false) {
                    sql_call("UPDATE edomiProject.editAgendaData SET status=" . $tmp['targetid'] . " WHERE id=" . $data['id']);
                } else {
                    sql_call("INSERT INTO edomiProject.editMacro (folderid,name) VALUES (95,'KO [" . $zsu['outgaid'] . "] = " . sql_encodeValue($data['status']) . "')");
                    $macroId = sql_insertId();
                    if ($macroId > 0) {
                        sql_call("INSERT INTO edomiProject.editMacroCmdList (targetid,outmode,outid,outvalue) VALUES (" . $macroId . ",2,'" . $zsu['outgaid'] . "','" . sql_encodeValue($data['status']) . "')");
                        $macroCmdId = sql_insertId();
                        if ($macroCmdId > 0) {
                            $sort = sql_getValue('edomiProject.editAgendaMacroList', 'MAX(sort)', 'agendaid=' . $zsu['id']);
                            if ($sort > 0) {
                                $sort++;
                            } else {
                                $sort = 1;
                            }
                            sql_call("INSERT INTO edomiProject.editAgendaMacroList (targetid,agendaid,sort) VALUES (" . $macroId . "," . $zsu['id'] . ",'" . $sort . "')");
                            sql_call("UPDATE edomiProject.editAgendaData SET status=" . $macroId . " WHERE id=" . $data['id']);
                        }
                    }
                }
            }
            sql_close($ss2);
        }
        sql_close($ss1);
        sql_call("ALTER TABLE edomiProject.editAgendaData CHANGE status cmdid BIGINT UNSIGNED DEFAULT 0");
        sql_call("ALTER TABLE edomiProject.editAgenda DROP COLUMN outgaid");
        sql_call("UPDATE edomiProject.editVisuElement SET var2=NULL WHERE controltyp=32");
        sql_call("ALTER TABLE edomiProject.editVisuElement ADD COLUMN dynstylemode INT UNSIGNED DEFAULT 0 AFTER controltyp");
        sql_call("UPDATE edomiProject.editVisuElement SET var7=2,var8=0,var9=NULL,var10=NULL WHERE controltyp=1");
        sql_call("UPDATE edomiProject.editVisuElement SET var7=var1,var8=var2,var9=var3,var10=var4 WHERE controltyp=2");
        sql_call("UPDATE edomiProject.editVisuElement SET var1=0,var2=0,controltyp=1 WHERE controltyp=2");
        if ($update) {
            deleteFiles(MAIN_PATH . '/www/visu/include/js/control2.js');
            deleteFiles(MAIN_PATH . '/www/visu/include/js/preview2.js');
            deleteFiles(MAIN_PATH . '/www/admin/help/1002-2.htm');
        }
    }
    if ($version < 1.56) {
        $logUpdate .= '1.56/';
        sql_call("ALTER TABLE edomiProject.editAws DROP COLUMN checkpointer");
        sql_call("ALTER TABLE edomiProject.editAwsList ADD COLUMN gavalue2 VARCHAR(10000) DEFAULT NULL AFTER gaid");
        sql_call("ALTER TABLE edomiProject.editAwsList ADD COLUMN gavalue1 VARCHAR(10000) DEFAULT NULL AFTER gaid");
    }
    if ($version < 1.57) {
        $logUpdate .= '1.57/';
        sql_call("ALTER TABLE edomiProject.editVisuPage ADD COLUMN outlinecolorid BIGINT UNSIGNED DEFAULT 0 AFTER ygrid");
    }
    if ($version < 1.58) {
        $logUpdate .= '1.58/';
        sql_call("ALTER TABLE edomiProject.editLogicElementDef ADD COLUMN title VARCHAR(100) DEFAULT NULL AFTER name");
    }
    if ($version < 1.59) {
        $logUpdate .= '1.59/';
        $r = sql_call("INSERT INTO edomiProject.editRoot (id,allow,path,rootid,parentid,name,namedb,link,linkid,sortcolumns,sortid) VALUES (83,126,'/80/',83,80,'Kameraansichten','editCamView',0,NULL,'id/ID,name/Name',1)");
        $r = sql_call("CREATE TABLE IF NOT EXISTS edomiProject.editCamView (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			folderid BIGINT UNSIGNED DEFAULT NULL,
			name VARCHAR(100) DEFAULT NULL,
			camid BIGINT UNSIGNED DEFAULT NULL,
			srctyp SMALLINT UNSIGNED DEFAULT 0,
			zoom INT DEFAULT 0,
			a1 INT DEFAULT 0,
			a2 INT DEFAULT 0,
			x INT DEFAULT 0,
			y INT DEFAULT 0,
			dstw INT DEFAULT 0,
			dsth INT DEFAULT 0,
			dsts INT DEFAULT 0,
			srcr INT DEFAULT 0,
			srcd INT DEFAULT 0,
			srcs INT DEFAULT 0,
			PRIMARY KEY (id),
			KEY (folderid),
			KEY (name)
			) ENGINE=MyISAM DEFAULT CHARSET=latin1
		");
        sql_call("ALTER TABLE edomiProject.editCam ADD COLUMN dvrgaid BIGINT UNSIGNED DEFAULT NULL AFTER mask");
        sql_call("ALTER TABLE edomiProject.editCam ADD COLUMN dvrkeep INT DEFAULT 0 AFTER mask");
        sql_call("ALTER TABLE edomiProject.editCam ADD COLUMN dvrrate INT DEFAULT 5 AFTER mask");
        sql_call("ALTER TABLE edomiProject.editCam ADD COLUMN dvr TINYINT DEFAULT 0 AFTER mask");
        sql_call("ALTER TABLE edomiProject.editCam DROP COLUMN datetime");
        sql_call("ALTER TABLE edomiProject.editCam ADD COLUMN cachets BIGINT UNSIGNED DEFAULT NULL AFTER id");
        sql_call("ALTER TABLE edomiProject.editCam DROP COLUMN mask");
        $ss1 = sql_call("SELECT var1 FROM edomiProject.editVisuElement WHERE controltyp=20 AND var1>=1 GROUP BY var1 ORDER BY CAST(var1 AS UNSIGNED) ASC");
        while ($n = sql_result($ss1)) {
            sql_call("INSERT INTO edomiProject.editCamView (folderid,name,camid,srctyp,zoom,a1,a2,x,y,dstw,dsth,dsts,srcr,srcd,srcs) VALUES (83,'Ansicht für Kamera [" . $n['var1'] . "]'," . $n['var1'] . ",0,250,0,0,0,0,4,3,0,0,0,50)");
        }
        sql_close($ss1);
        $ss1 = sql_call("SELECT id,var1 FROM edomiProject.editVisuElement WHERE controltyp=20 AND var1>=1");
        while ($n = sql_result($ss1)) {
            $viewId = sql_getValue('edomiProject.editCamView', 'id', 'camid=' . $n['var1']);
            if (!isEmpty($viewId)) {
                sql_call("UPDATE edomiProject.editVisuElement SET var1='" . $viewId . "' WHERE id=" . $n['id']);
            }
        }
        sql_close($ss1);
        sql_call("ALTER TABLE edomiProject.editArchivCamData DROP COLUMN width0");
        sql_call("ALTER TABLE edomiProject.editArchivCamData DROP COLUMN height0");
        sql_call("ALTER TABLE edomiProject.editArchivCamData DROP COLUMN width1");
        sql_call("ALTER TABLE edomiProject.editArchivCamData DROP COLUMN height1");
        if ($update) {
            sql_call("ALTER TABLE edomiLive.archivCamData DROP COLUMN width0");
            sql_call("ALTER TABLE edomiLive.archivCamData DROP COLUMN height0");
            sql_call("ALTER TABLE edomiLive.archivCamData DROP COLUMN width1");
            sql_call("ALTER TABLE edomiLive.archivCamData DROP COLUMN height1");
            deleteFiles(MAIN_PATH . '/www/data/liveproject/cam/archiv/archiv*-1.jpg');
        }
    }
    if ($version < 1.60) {
        $logUpdate .= '1.60/';
        if ($update) {
            edomi_update_log($update, true, 'Überflüssige Dateien löschen...');
            deleteFiles(MAIN_PATH . '/main/include/php/incl_knx.php');
        }
    }
    if ($version < 1.61) {
        $logUpdate .= '1.61/';
        edomi_update_log($update, sql_call("ALTER TABLE edomiProject.editKo ADD COLUMN prio TINYINT UNSIGNED NOT NULL DEFAULT 0 AFTER remanent"), 'Datenbank edomiProject.editKo modifizieren...');
        if (!sql_columnExists('edomiLive.ko', 'prio')) {
            edomi_update_log($update, sql_call("ALTER TABLE edomiLive.ko ADD COLUMN prio TINYINT UNSIGNED NOT NULL DEFAULT 0 AFTER remanent"), 'Datenbank edomiLive.ko modifizieren...');
        }
    }
    if ($version < 1.62) {
        $logUpdate .= '1.62/';
        edomi_update_log($update, sql_call("RENAME TABLE edomiProject.editSequenceList TO edomiProject.editSequenceCmdList"), 'Datenbank edomiProject.editSequenceList umbenennen: editSequenceCmdList');
        if ($update) {
            edomi_update_log($update, sql_call("RENAME TABLE edomiLive.sequenceList TO edomiLive.sequenceCmdList"), 'Datenbank edomiLive.sequenceList umbenennen: sequenceCmdList');
        }
        $dbs = array('editLogicCmdList', 'editMacroCmdList', 'editVisuCmdList', 'editSequenceCmdList');
        foreach ($dbs as $tmp) {
            edomi_update_log($update, sql_call("ALTER TABLE edomiProject." . $tmp . " CHANGE outmode cmd TINYINT UNSIGNED DEFAULT NULL"), 'Datenbank edomiProject.' . $tmp . ' modifizieren: CHANGE cmd');
            edomi_update_log($update, sql_call("ALTER TABLE edomiProject." . $tmp . " CHANGE outid cmdid1 BIGINT UNSIGNED DEFAULT NULL"), 'Datenbank edomiProject.' . $tmp . ' modifizieren: CHANGE cmdid1');
            edomi_update_log($update, sql_call("ALTER TABLE edomiProject." . $tmp . " CHANGE outvalue cmdvalue1 VARCHAR(10000) DEFAULT NULL"), 'Datenbank edomiProject.' . $tmp . ' modifizieren: CHANGE cmdvalue1');
            edomi_update_log($update, sql_call("ALTER TABLE edomiProject." . $tmp . " ADD COLUMN cmdid2 BIGINT UNSIGNED DEFAULT NULL AFTER cmdid1"), 'Datenbank edomiProject.' . $tmp . ' modifizieren: ADD cmdid2');
            edomi_update_log($update, sql_call("ALTER TABLE edomiProject." . $tmp . " ADD COLUMN cmdvalue2 VARCHAR(10000) DEFAULT NULL AFTER cmdvalue1"), 'Datenbank edomiProject.' . $tmp . ' modifizieren: ADD cmdvalue2');
            edomi_update_log($update, sql_call("ALTER TABLE edomiProject." . $tmp . " ADD COLUMN cmdoption1 INT SIGNED DEFAULT 0 AFTER cmdid2"), 'Datenbank edomiProject.' . $tmp . ' modifizieren: ADD cmdoption1');
            edomi_update_log($update, sql_call("ALTER TABLE edomiProject." . $tmp . " ADD COLUMN cmdoption2 INT SIGNED DEFAULT 0 AFTER cmdoption1"), 'Datenbank edomiProject.' . $tmp . ' modifizieren: ADD cmdoption2');
            edomi_update_log($update, sql_call("ALTER TABLE edomiProject." . $tmp . " DROP KEY outmode"), 'Datenbank edomiProject.' . $tmp . ' modifizieren: DROP KEY outmode');
            edomi_update_log($update, sql_call("ALTER TABLE edomiProject." . $tmp . " ADD KEY (cmd)"), 'Datenbank edomiProject.' . $tmp . ' modifizieren: ADD KEY cmd');
            edomi_update_log($update, sql_call("ALTER TABLE edomiProject." . $tmp . " DROP KEY outid"), 'Datenbank edomiProject.' . $tmp . ' modifizieren: DROP KEY outid');
            edomi_update_log($update, sql_call("ALTER TABLE edomiProject." . $tmp . " ADD KEY (cmdid1)"), 'Datenbank edomiProject.' . $tmp . ' modifizieren: ADD KEY cmdid1');
            edomi_update_log($update, sql_call("ALTER TABLE edomiProject." . $tmp . " ADD KEY (cmdid2)"), 'Datenbank edomiProject.' . $tmp . ' modifizieren: ADD KEY cmdid2');
            edomi_update_log($update, sql_call("ALTER TABLE edomiProject." . $tmp . " DROP KEY outvalue"), 'Datenbank edomiProject.' . $tmp . ' modifizieren: DROP KEY outvalue');
            edomi_update_log($update, sql_call("ALTER TABLE edomiProject." . $tmp . " ADD KEY (cmdvalue1)"), 'Datenbank edomiProject.' . $tmp . ' modifizieren: ADD KEY cmdvalue1');
            edomi_update_log($update, sql_call("ALTER TABLE edomiProject." . $tmp . " ADD KEY (cmdvalue2)"), 'Datenbank edomiProject.' . $tmp . ' modifizieren: ADD KEY cmdvalue2');
            edomi_update_log($update, sql_call("UPDATE edomiProject." . $tmp . " SET cmdoption1=cmdvalue1 WHERE cmd=13"), 'Datenbank edomiProject.' . $tmp . ' modifizieren: cmdoption1=cmdvalue1');
            edomi_update_log($update, sql_call("UPDATE edomiProject." . $tmp . " SET cmdoption1=0 WHERE cmd=13 AND cmdoption1 IS NULL"), 'Datenbank edomiProject.' . $tmp . ' modifizieren: cmdoption1=0');
            edomi_update_log($update, sql_call("UPDATE edomiProject." . $tmp . " SET cmdvalue1=NULL WHERE cmd=13"), 'Datenbank edomiProject.' . $tmp . ' modifizieren: cmdvalue1=NULL');
            edomi_update_log($update, sql_call("UPDATE edomiProject." . $tmp . " SET cmdoption1=cmdvalue1 WHERE cmd=14"), 'Datenbank edomiProject.' . $tmp . ' modifizieren: cmdoption1=cmdvalue1');
            edomi_update_log($update, sql_call("UPDATE edomiProject." . $tmp . " SET cmdoption1=0 WHERE cmd=14 AND cmdoption1 IS NULL"), 'Datenbank edomiProject.' . $tmp . ' modifizieren: cmdoption1=0');
            edomi_update_log($update, sql_call("UPDATE edomiProject." . $tmp . " SET cmdvalue1=NULL WHERE cmd=14"), 'Datenbank edomiProject.' . $tmp . ' modifizieren: cmdvalue1=NULL');
            edomi_update_log($update, sql_call("UPDATE edomiProject." . $tmp . " SET cmdid2=cmdvalue1 WHERE cmd=3"), 'Datenbank edomiProject.' . $tmp . ' modifizieren: cmdid2=cmdvalue1');
            edomi_update_log($update, sql_call("UPDATE edomiProject." . $tmp . " SET cmdvalue1=NULL WHERE cmd=3"), 'Datenbank edomiProject.' . $tmp . ' modifizieren: cmdvalue1=NULL');
            edomi_update_log($update, sql_call("UPDATE edomiProject." . $tmp . " SET cmdid2=cmdvalue1 WHERE cmd=6"), 'Datenbank edomiProject.' . $tmp . ' modifizieren: cmdid2=cmdvalue1');
            edomi_update_log($update, sql_call("UPDATE edomiProject." . $tmp . " SET cmdvalue1=1 WHERE cmd=6"), 'Datenbank edomiProject.' . $tmp . ' modifizieren: cmdvalue1=1');
            edomi_update_log($update, sql_call("UPDATE edomiProject." . $tmp . " SET cmdoption1=cmdvalue1 WHERE cmd=5"), 'Datenbank edomiProject.' . $tmp . ' modifizieren: cmdoption1=cmdvalue1');
            edomi_update_log($update, sql_call("UPDATE edomiProject." . $tmp . " SET cmdoption1=1 WHERE cmd=5 AND cmdoption1 IS NULL"), 'Datenbank edomiProject.' . $tmp . ' modifizieren: cmdoption1=0');
            edomi_update_log($update, sql_call("UPDATE edomiProject." . $tmp . " SET cmdvalue1=NULL WHERE cmd=5"), 'Datenbank edomiProject.' . $tmp . ' modifizieren: cmdvalue1=NULL');
            edomi_update_log($update, sql_call("UPDATE edomiProject." . $tmp . " SET cmdoption1=cmdvalue1 WHERE cmd=10"), 'Datenbank edomiProject.' . $tmp . ' modifizieren: cmdoption1=cmdvalue1');
            edomi_update_log($update, sql_call("UPDATE edomiProject." . $tmp . " SET cmdoption1=0 WHERE cmd=10 AND cmdoption1 IS NULL"), 'Datenbank edomiProject.' . $tmp . ' modifizieren: cmdoption1=0');
            edomi_update_log($update, sql_call("UPDATE edomiProject." . $tmp . " SET cmdvalue1=NULL WHERE cmd=10"), 'Datenbank edomiProject.' . $tmp . ' modifizieren: cmdvalue1=NULL');
            edomi_update_log($update, sql_call("UPDATE edomiProject." . $tmp . " SET cmdoption1=cmdvalue1 WHERE cmd=11"), 'Datenbank edomiProject.' . $tmp . ' modifizieren: cmdoption1=cmdvalue1');
            edomi_update_log($update, sql_call("UPDATE edomiProject." . $tmp . " SET cmdoption1=0 WHERE cmd=11 AND cmdoption1 IS NULL"), 'Datenbank edomiProject.' . $tmp . ' modifizieren: cmdoption1=0');
            edomi_update_log($update, sql_call("UPDATE edomiProject." . $tmp . " SET cmdvalue1=NULL WHERE cmd=11"), 'Datenbank edomiProject.' . $tmp . ' modifizieren: cmdvalue1=NULL');
            edomi_update_log($update, sql_call("UPDATE edomiProject." . $tmp . " SET cmdoption1=cmdvalue1 WHERE cmd=50"), 'Datenbank edomiProject.' . $tmp . ' modifizieren: cmdoption1=cmdvalue1');
            edomi_update_log($update, sql_call("UPDATE edomiProject." . $tmp . " SET cmdoption1=0 WHERE cmd=50 AND cmdoption1 IS NULL"), 'Datenbank edomiProject.' . $tmp . ' modifizieren: cmdoption1=0');
            edomi_update_log($update, sql_call("UPDATE edomiProject." . $tmp . " SET cmdvalue1=NULL WHERE cmd=50"), 'Datenbank edomiProject.' . $tmp . ' modifizieren: cmdvalue1=NULL');
            edomi_update_log($update, sql_call("UPDATE edomiProject." . $tmp . " SET cmdoption1=cmdvalue1 WHERE cmd=51"), 'Datenbank edomiProject.' . $tmp . ' modifizieren: cmdoption1=cmdvalue1');
            edomi_update_log($update, sql_call("UPDATE edomiProject." . $tmp . " SET cmdoption1=0 WHERE cmd=51 AND cmdoption1 IS NULL"), 'Datenbank edomiProject.' . $tmp . ' modifizieren: cmdoption1=0');
            edomi_update_log($update, sql_call("UPDATE edomiProject." . $tmp . " SET cmdvalue1=NULL WHERE cmd=51"), 'Datenbank edomiProject.' . $tmp . ' modifizieren: cmdvalue1=NULL');
            edomi_update_log($update, sql_call("UPDATE edomiProject." . $tmp . " SET cmdoption1=cmdvalue1 WHERE cmd=52"), 'Datenbank edomiProject.' . $tmp . ' modifizieren: cmdoption1=cmdvalue1');
            edomi_update_log($update, sql_call("UPDATE edomiProject." . $tmp . " SET cmdoption1=0 WHERE cmd=52 AND cmdoption1 IS NULL"), 'Datenbank edomiProject.' . $tmp . ' modifizieren: cmdoption1=0');
            edomi_update_log($update, sql_call("UPDATE edomiProject." . $tmp . " SET cmdvalue1=NULL WHERE cmd=52"), 'Datenbank edomiProject.' . $tmp . ' modifizieren: cmdvalue1=NULL');
            edomi_update_log($update, sql_call("UPDATE edomiProject." . $tmp . " SET cmdoption1=cmdvalue1 WHERE cmd=53"), 'Datenbank edomiProject.' . $tmp . ' modifizieren: cmdoption1=cmdvalue1');
            edomi_update_log($update, sql_call("UPDATE edomiProject." . $tmp . " SET cmdoption1=0 WHERE cmd=53 AND cmdoption1 IS NULL"), 'Datenbank edomiProject.' . $tmp . ' modifizieren: cmdoption1=0');
            edomi_update_log($update, sql_call("UPDATE edomiProject." . $tmp . " SET cmdvalue1=NULL WHERE cmd=53"), 'Datenbank edomiProject.' . $tmp . ' modifizieren: cmdvalue1=NULL');
            edomi_update_log($update, sql_call("UPDATE edomiProject." . $tmp . " SET cmdoption1=cmdvalue1 WHERE cmd=16"), 'Datenbank edomiProject.' . $tmp . ' modifizieren: cmdoption1=cmdvalue1');
            edomi_update_log($update, sql_call("UPDATE edomiProject." . $tmp . " SET cmdoption1=1 WHERE cmd=16 AND cmdoption1 IS NULL"), 'Datenbank edomiProject.' . $tmp . ' modifizieren: cmdoption1=0');
            edomi_update_log($update, sql_call("UPDATE edomiProject." . $tmp . " SET cmdvalue1=NULL WHERE cmd=16"), 'Datenbank edomiProject.' . $tmp . ' modifizieren: cmdvalue1=NULL');
            edomi_update_log($update, sql_call("UPDATE edomiProject." . $tmp . " SET cmdoption1=cmdvalue1 WHERE cmd=22"), 'Datenbank edomiProject.' . $tmp . ' modifizieren: cmdoption1=cmdvalue1');
            edomi_update_log($update, sql_call("UPDATE edomiProject." . $tmp . " SET cmdoption1=0 WHERE cmd=22 AND cmdoption1 IS NULL"), 'Datenbank edomiProject.' . $tmp . ' modifizieren: cmdoption1=0');
            edomi_update_log($update, sql_call("UPDATE edomiProject." . $tmp . " SET cmdvalue1=NULL WHERE cmd=22"), 'Datenbank edomiProject.' . $tmp . ' modifizieren: cmdvalue1=NULL');
            edomi_update_log($update, sql_call("UPDATE edomiProject." . $tmp . " SET cmdid2=cmdvalue1 WHERE cmd=21"), 'Datenbank edomiProject.' . $tmp . ' modifizieren: cmdid2=cmdvalue1');
            edomi_update_log($update, sql_call("UPDATE edomiProject." . $tmp . " SET cmdvalue1=NULL WHERE cmd=21"), 'Datenbank edomiProject.' . $tmp . ' modifizieren: cmdvalue1=NULL');
            edomi_update_log($update, sql_call("UPDATE edomiProject." . $tmp . " SET cmdid2=cmdvalue1 WHERE cmd=28"), 'Datenbank edomiProject.' . $tmp . ' modifizieren: cmdid2=cmdvalue1');
            edomi_update_log($update, sql_call("UPDATE edomiProject." . $tmp . " SET cmdvalue1=NULL WHERE cmd=28"), 'Datenbank edomiProject.' . $tmp . ' modifizieren: cmdvalue1=NULL');
            edomi_update_log($update, sql_call("UPDATE edomiProject." . $tmp . " SET cmdid2=cmdvalue1 WHERE cmd=24"), 'Datenbank edomiProject.' . $tmp . ' modifizieren: cmdid2=cmdvalue1');
            edomi_update_log($update, sql_call("UPDATE edomiProject." . $tmp . " SET cmdvalue1=NULL WHERE cmd=24"), 'Datenbank edomiProject.' . $tmp . ' modifizieren: cmdvalue1=NULL');
            edomi_update_log($update, sql_call("UPDATE edomiProject." . $tmp . " SET cmdid2=cmdvalue1 WHERE cmd=25"), 'Datenbank edomiProject.' . $tmp . ' modifizieren: cmdid2=cmdvalue1');
            edomi_update_log($update, sql_call("UPDATE edomiProject." . $tmp . " SET cmdvalue1=NULL WHERE cmd=25"), 'Datenbank edomiProject.' . $tmp . ' modifizieren: cmdvalue1=NULL');
            edomi_update_log($update, sql_call("UPDATE edomiProject." . $tmp . " SET cmdid2=cmdvalue1 WHERE cmd=23"), 'Datenbank edomiProject.' . $tmp . ' modifizieren: cmdid2=cmdvalue1');
            edomi_update_log($update, sql_call("UPDATE edomiProject." . $tmp . " SET cmdvalue1=NULL WHERE cmd=23"), 'Datenbank edomiProject.' . $tmp . ' modifizieren: cmdvalue1=NULL');
            edomi_update_log($update, sql_call("UPDATE edomiProject." . $tmp . " SET cmdoption1=cmdvalue1 WHERE cmd=30"), 'Datenbank edomiProject.' . $tmp . ' modifizieren: cmdoption1=cmdvalue1');
            edomi_update_log($update, sql_call("UPDATE edomiProject." . $tmp . " SET cmdoption1=1 WHERE cmd=30 AND cmdoption1 IS NULL"), 'Datenbank edomiProject.' . $tmp . ' modifizieren: cmdoption1=0');
            edomi_update_log($update, sql_call("UPDATE edomiProject." . $tmp . " SET cmdvalue1=NULL WHERE cmd=30"), 'Datenbank edomiProject.' . $tmp . ' modifizieren: cmdvalue1=NULL');
        }
        edomi_update_log($update, sql_call("ALTER TABLE edomiProject.editVisuUserList ADD COLUMN defaultpageid BIGINT UNSIGNED DEFAULT NULL AFTER visuid"), 'Datenbank edomiProject.editVisuUserList modifizieren: ADD defaultpageid');
        if (!sql_columnExists('edomiLive.visuUserList', 'defaultpageid')) {
            edomi_update_log($update, sql_call("ALTER TABLE edomiLive.visuUserList ADD COLUMN defaultpageid BIGINT UNSIGNED DEFAULT NULL AFTER visuid"), 'Datenbank edomiLive.visuUserList modifizieren: ADD defaultpageid');
        }
        edomi_update_log($update, sql_call("ALTER TABLE edomiProject.editChartList ADD COLUMN sort BIGINT UNSIGNED DEFAULT NULL AFTER yscale"), 'Datenbank edomiProject.editChartList modifizieren: ADD sort');
        edomi_update_log($update, sql_call("ALTER TABLE edomiProject.editChartList ADD KEY (sort)"), 'Datenbank edomiProject.editChartList modifizieren: ADD KEY sort');
        $tmp = 0;
        $sort = 1;
        $ss1 = sql_call("SELECT * FROM edomiProject.editChartList ORDER BY targetid ASC,id ASC");
        while ($n = sql_result($ss1)) {
            if ($tmp != $n['targetid']) {
                $tmp = $n['targetid'];
                $sort = 1;
            }
            edomi_update_log($update, sql_call("UPDATE edomiProject.editChartList SET sort=" . $sort . " WHERE id=" . $n['id']), 'Datenbank edomiProject.editChartList modifizieren: SET sort');
            $sort++;
        }
        sql_close($ss1);
        edomi_update_log($update, sql_call("ALTER TABLE edomiProject.editVisuPage ADD COLUMN bgdark TINYINT UNSIGNED DEFAULT 0 AFTER autoclose"), 'Datenbank edomiProject.editVisuPage modifizieren: ADD bgdark');
        edomi_update_log($update, sql_call("ALTER TABLE edomiProject.editVisuPage ADD COLUMN bganim TINYINT UNSIGNED DEFAULT 0 AFTER autoclose"), 'Datenbank edomiProject.editVisuPage modifizieren: ADD bganim');
        edomi_update_log($update, sql_call("ALTER TABLE edomiProject.editVisuPage ADD COLUMN bgmodal TINYINT UNSIGNED DEFAULT 0 AFTER autoclose"), 'Datenbank edomiProject.editVisuPage modifizieren: ADD bgmodal');
        edomi_update_log($update, sql_call("UPDATE edomiProject.editVisuPage SET bgmodal=1,bganim=1,bgdark=1"), 'Datenbank edomiProject.editVisuPage modifizieren: SET bgmodal,bganim,bgdark');
        edomi_update_log($update, sql_call("ALTER TABLE edomiProject.editVisuUserList DROP COLUMN visuinit"), 'Datenbank edomiProject.editVisuUserList modifizieren: DROP visuinit');
        edomi_update_log($update, sql_call("ALTER TABLE edomiProject.editVisuUserList DROP COLUMN visupageid"), 'Datenbank edomiProject.editVisuUserList modifizieren: DROP visupageid');
        edomi_update_log($update, sql_call("ALTER TABLE edomiProject.editVisuUserList DROP COLUMN visusoundid"), 'Datenbank edomiProject.editVisuUserList modifizieren: DROP visusoundid');
        edomi_update_log($update, sql_call("ALTER TABLE edomiProject.editVisuUserList DROP COLUMN visuspeech"), 'Datenbank edomiProject.editVisuUserList modifizieren: DROP visuspeech');
        edomi_update_log($update, sql_call("ALTER TABLE edomiLive.visuUserList DROP COLUMN visuinit"), 'Datenbank edomiLive.visuUserList modifizieren: DROP visuinit');
        edomi_update_log($update, sql_call("ALTER TABLE edomiLive.visuUserList DROP COLUMN visupageid"), 'Datenbank edomiLive.visuUserList modifizieren: DROP visupageid');
        edomi_update_log($update, sql_call("ALTER TABLE edomiLive.visuUserList DROP COLUMN visusoundid"), 'Datenbank edomiLive.visuUserList modifizieren: DROP visusoundid');
        edomi_update_log($update, sql_call("ALTER TABLE edomiLive.visuUserList DROP COLUMN visuspeech"), 'Datenbank edomiLive.visuUserList modifizieren: DROP visuspeech');
        edomi_update_log($update, sql_call("ALTER TABLE edomiProject.editVisuElement ADD COLUMN closepopupid BIGINT UNSIGNED DEFAULT NULL AFTER gotopageid"), 'Datenbank edomiProject.editVisuElement modifizieren: ADD closepopupid');
        edomi_update_log($update, sql_call("ALTER TABLE edomiProject.editVisu ADD COLUMN hasspeech TINYINT UNSIGNED DEFAULT 0 AFTER defaultpageid"), 'Datenbank edomiProject.editVisu modifizieren: ADD hasspeech');
        edomi_update_log($update, sql_call("ALTER TABLE edomiProject.editVisu ADD COLUMN hassound TINYINT UNSIGNED DEFAULT 0 AFTER defaultpageid"), 'Datenbank edomiProject.editVisu modifizieren: ADD hassound');
        edomi_update_log($update, sql_call("ALTER TABLE edomiProject.editVisuUserList ADD COLUMN hasspeech TINYINT UNSIGNED DEFAULT 0 AFTER defaultpageid"), 'Datenbank edomiProject.editVisuUserList modifizieren: ADD hasspeech');
        edomi_update_log($update, sql_call("ALTER TABLE edomiProject.editVisuUserList ADD COLUMN hassound TINYINT UNSIGNED DEFAULT 0 AFTER defaultpageid"), 'Datenbank edomiProject.editVisuUserList modifizieren: ADD hassound');
        if (!sql_columnExists('edomiLive.visuUserList', 'hasspeech')) {
            edomi_update_log($update, sql_call("ALTER TABLE edomiLive.visuUserList ADD COLUMN hasspeech TINYINT UNSIGNED DEFAULT 0 AFTER defaultpageid"), 'Datenbank edomiLive.visuUserList modifizieren: ADD hasspeech');
        }
        if (!sql_columnExists('edomiLive.visuUserList', 'hassound')) {
            edomi_update_log($update, sql_call("ALTER TABLE edomiLive.visuUserList ADD COLUMN hassound TINYINT UNSIGNED DEFAULT 0 AFTER defaultpageid"), 'Datenbank edomiLive.visuUserList modifizieren: ADD hassound');
        }
        edomi_update_log($update, sql_call("ALTER TABLE edomiProject.editVisuUserList ADD COLUMN online TINYINT UNSIGNED DEFAULT 0 AFTER loginip"), 'Datenbank edomiProject.editVisuUserList modifizieren: ADD online');
        edomi_update_log($update, sql_call("ALTER TABLE edomiProject.editVisuUserList ADD KEY (online)"), 'Datenbank edomiProject.editVisuUserList modifizieren: ADD KEY online');
        if (!sql_columnExists('edomiLive.visuUserList', 'online')) {
            edomi_update_log($update, sql_call("ALTER TABLE edomiLive.visuUserList ADD COLUMN online TINYINT UNSIGNED DEFAULT 0 AFTER loginip"), 'Datenbank edomiLive.visuUserList modifizieren: ADD online');
            edomi_update_log($update, sql_call("ALTER TABLE edomiLive.visuUserList ADD KEY (online)"), 'Datenbank edomiLive.visuUserList modifizieren: ADD KEY online');
        }
        edomi_update_log($update, sql_call("ALTER TABLE edomiProject.editVisuUserList ADD COLUMN logout TINYINT UNSIGNED DEFAULT 0 AFTER loginip"), 'Datenbank edomiProject.editVisuUserList modifizieren: ADD logout');
        if (!sql_columnExists('edomiLive.visuUserList', 'logout')) {
            edomi_update_log($update, sql_call("ALTER TABLE edomiLive.visuUserList ADD COLUMN logout TINYINT UNSIGNED DEFAULT 0 AFTER loginip"), 'Datenbank edomiLive.visuUserList modifizieren: ADD logout');
        }
        edomi_update_log($update, sql_call("INSERT INTO edomiProject.editKo (id,name,folderid,ga,gatyp,valuetyp,value,defaultvalue,initscan,initsend,requestable,remanent,vmin,vmax,vstep,vlist) VALUES (13, 'unerwarteter Neustart', 33, '13', 2, 0, NULL, NULL, 0, 0, 0, 0, NULL, NULL, NULL, NULL)"), 'System-KO 13 hinzufügen');
        edomi_update_log($update, sql_call("ALTER TABLE edomiProject.editLogicElementDefVar ADD COLUMN remanent TINYINT UNSIGNED DEFAULT 0 AFTER value"), 'Datenbank edomiProject.editLogicElementDefVar modifizieren: ADD remanent');
        edomi_update_log($update, sql_call("ALTER TABLE edomiProject.editLogicElementVar ADD COLUMN remanent TINYINT UNSIGNED DEFAULT 0 AFTER value"), 'Datenbank edomiProject.editLogicElementVar modifizieren: ADD remanent');
        edomi_update_log($update, sql_call("ALTER TABLE edomiProject.editLogicElementVar ADD KEY (remanent)"), 'Datenbank edomiProject.editLogicElementVar modifizieren: ADD KEY remanent');
    }
    if ($version < 1.63) {
        $logUpdate .= '1.63/';
        edomi_update_log($update, sql_call("UPDATE edomiProject.editVisuElement SET var2=0,var4='' WHERE controltyp=22"), 'Datenbank edomiProject.editVisuElement: ZSU anpassen');
        edomi_update_log($update, sql_call("ALTER TABLE edomiProject.editArchivKo ADD COLUMN cunit TINYINT UNSIGNED NOT NULL DEFAULT 10 AFTER outgaid"), 'Datenbank edomiProject.editArchivKo modifizieren: ADD cunit');
        edomi_update_log($update, sql_call("ALTER TABLE edomiProject.editArchivKo ADD COLUMN coffset INT UNSIGNED NOT NULL DEFAULT 1 AFTER outgaid"), 'Datenbank edomiProject.editArchivKo modifizieren: ADD coffset');
        edomi_update_log($update, sql_call("ALTER TABLE edomiProject.editArchivKo ADD COLUMN clist TINYINT UNSIGNED DEFAULT NULL AFTER outgaid"), 'Datenbank edomiProject.editArchivKo modifizieren: ADD clist');
        edomi_update_log($update, sql_call("ALTER TABLE edomiProject.editArchivKo ADD COLUMN cts TINYINT UNSIGNED NOT NULL DEFAULT 0 AFTER outgaid"), 'Datenbank edomiProject.editArchivKo modifizieren: ADD cts');
        edomi_update_log($update, sql_call("ALTER TABLE edomiProject.editArchivKo ADD COLUMN cinterval TINYINT UNSIGNED NOT NULL DEFAULT 10 AFTER outgaid"), 'Datenbank edomiProject.editArchivKo modifizieren: ADD cinterval');
        edomi_update_log($update, sql_call("ALTER TABLE edomiProject.editArchivKo ADD COLUMN cmode TINYINT UNSIGNED NOT NULL DEFAULT 0 AFTER outgaid"), 'Datenbank edomiProject.editArchivKo modifizieren: ADD cmode');
        edomi_update_log($update, sql_call("ALTER TABLE edomiProject.editTimer ADD COLUMN gaid2 BIGINT UNSIGNED DEFAULT NULL AFTER gaid"), 'Datenbank edomiProject.editTimer modifizieren: ADD gaid2');
        edomi_update_log($update, sql_call("ALTER TABLE edomiProject.editTimerData ADD COLUMN d7 TINYINT UNSIGNED DEFAULT 0 AFTER d6"), 'Datenbank edomiProject.editTimerData modifizieren: ADD d7');
        if (!sql_columnExists('edomiLive.timerData', 'd7')) {
            edomi_update_log($update, sql_call("ALTER TABLE edomiLive.timerData ADD COLUMN d7 TINYINT UNSIGNED DEFAULT 0 AFTER d6"), 'Datenbank edomiLive.timerData modifizieren: ADD d7');
        }
        edomi_update_log($update, sql_call("ALTER TABLE edomiProject.editAgenda ADD COLUMN gaid2 BIGINT UNSIGNED DEFAULT NULL AFTER gaid"), 'Datenbank edomiProject.editAgenda modifizieren: ADD gaid2');
        edomi_update_log($update, sql_call("ALTER TABLE edomiProject.editAgendaData ADD COLUMN d7 TINYINT UNSIGNED DEFAULT 0 AFTER unit"), 'Datenbank edomiProject.editAgendaData modifizieren: ADD d7');
        if (!sql_columnExists('edomiLive.agendaData', 'd7')) {
            edomi_update_log($update, sql_call("ALTER TABLE edomiLive.agendaData ADD COLUMN d7 TINYINT UNSIGNED DEFAULT 0 AFTER unit"), 'Datenbank edomiLive.agendaData modifizieren: ADD d7');
        }
        edomi_update_log($update, sql_call("ALTER TABLE edomiProject.editVisu ADD COLUMN preview TINYINT UNSIGNED DEFAULT 0 AFTER indicolor2"), 'Datenbank edomiProject.editVisu modifizieren: ADD preview');
    }
    if ($version < 1.64) {
        $logUpdate .= '1.64/';
    }
    if ($version < 2.00) {
        $logUpdate .= '2.00/';
        edomi_update_log($update, sql_call("ALTER TABLE edomiProject.editVisuElement ADD COLUMN var20 VARCHAR(20) DEFAULT NULL AFTER var10"), 'Datenbank edomiProject.editVisuElement modifizieren: ADD var20');
        edomi_update_log($update, sql_call("ALTER TABLE edomiProject.editVisuElement ADD COLUMN var19 VARCHAR(20) DEFAULT NULL AFTER var10"), 'Datenbank edomiProject.editVisuElement modifizieren: ADD var19');
        edomi_update_log($update, sql_call("ALTER TABLE edomiProject.editVisuElement ADD COLUMN var18 VARCHAR(20) DEFAULT NULL AFTER var10"), 'Datenbank edomiProject.editVisuElement modifizieren: ADD var18');
        edomi_update_log($update, sql_call("ALTER TABLE edomiProject.editVisuElement ADD COLUMN var17 VARCHAR(20) DEFAULT NULL AFTER var10"), 'Datenbank edomiProject.editVisuElement modifizieren: ADD var17');
        edomi_update_log($update, sql_call("ALTER TABLE edomiProject.editVisuElement ADD COLUMN var16 VARCHAR(20) DEFAULT NULL AFTER var10"), 'Datenbank edomiProject.editVisuElement modifizieren: ADD var16');
        edomi_update_log($update, sql_call("ALTER TABLE edomiProject.editVisuElement ADD COLUMN var15 VARCHAR(20) DEFAULT NULL AFTER var10"), 'Datenbank edomiProject.editVisuElement modifizieren: ADD var15');
        edomi_update_log($update, sql_call("ALTER TABLE edomiProject.editVisuElement ADD COLUMN var14 VARCHAR(20) DEFAULT NULL AFTER var10"), 'Datenbank edomiProject.editVisuElement modifizieren: ADD var14');
        edomi_update_log($update, sql_call("ALTER TABLE edomiProject.editVisuElement ADD COLUMN var13 VARCHAR(20) DEFAULT NULL AFTER var10"), 'Datenbank edomiProject.editVisuElement modifizieren: ADD var13');
        edomi_update_log($update, sql_call("ALTER TABLE edomiProject.editVisuElement ADD COLUMN var12 VARCHAR(20) DEFAULT NULL AFTER var10"), 'Datenbank edomiProject.editVisuElement modifizieren: ADD var12');
        edomi_update_log($update, sql_call("ALTER TABLE edomiProject.editVisuElement ADD COLUMN var11 VARCHAR(20) DEFAULT NULL AFTER var10"), 'Datenbank edomiProject.editVisuElement modifizieren: ADD var11');
        edomi_update_log($update, sql_call("RENAME TABLE edomiProject.editVisuElementDef TO edomiProject.editVisuElementDesignDef"), 'Datenbank edomiProject.editVisuElementDef umbenennen: editVisuElementDesignDef');
        edomi_update_log($update, sql_call("UPDATE edomiProject.editRoot SET namedb='editVisuElementDesignDef' WHERE namedb='editVisuElementDef'"), 'Datenbank edomiProject.editRoot modifizieren: CHANGE namedb (editVisuElementDesignDef)');
        edomi_update_log($update, sql_call("CREATE TABLE IF NOT EXISTS edomiProject.editVisuElementDef (
			id BIGINT UNSIGNED NOT NULL,
			folderid BIGINT UNSIGNED DEFAULT NULL,
			name VARCHAR(100) DEFAULT NULL,
			errcount BIGINT UNSIGNED NOT NULL DEFAULT 0,
			errmsg VARCHAR(10000) DEFAULT NULL,
			activationphp TINYINT UNSIGNED DEFAULT 0,
			editorphp TINYINT UNSIGNED DEFAULT 0,
			controlphp TINYINT UNSIGNED DEFAULT 0,
			xsize INT UNSIGNED DEFAULT NULL,
			ysize INT UNSIGNED DEFAULT NULL,
			text VARCHAR(10000) DEFAULT NULL,
			var1 VARCHAR(20) DEFAULT NULL,
			var2 VARCHAR(20) DEFAULT NULL,
			var3 VARCHAR(20) DEFAULT NULL,
			var4 VARCHAR(20) DEFAULT NULL,
			var5 VARCHAR(20) DEFAULT NULL,
			var6 VARCHAR(20) DEFAULT NULL,
			var7 VARCHAR(20) DEFAULT NULL,
			var8 VARCHAR(20) DEFAULT NULL,
			var9 VARCHAR(20) DEFAULT NULL,
			var10 VARCHAR(20) DEFAULT NULL,
			var11 VARCHAR(20) DEFAULT NULL,
			var12 VARCHAR(20) DEFAULT NULL,
			var13 VARCHAR(20) DEFAULT NULL,
			var14 VARCHAR(20) DEFAULT NULL,
			var15 VARCHAR(20) DEFAULT NULL,
			var16 VARCHAR(20) DEFAULT NULL,
			var17 VARCHAR(20) DEFAULT NULL,
			var18 VARCHAR(20) DEFAULT NULL,
			var19 VARCHAR(20) DEFAULT NULL,
			var20 VARCHAR(20) DEFAULT NULL,
			var1root BIGINT UNSIGNED DEFAULT NULL,
			var2root BIGINT UNSIGNED DEFAULT NULL,
			var3root BIGINT UNSIGNED DEFAULT NULL,
			var4root BIGINT UNSIGNED DEFAULT NULL,
			var5root BIGINT UNSIGNED DEFAULT NULL,
			var6root BIGINT UNSIGNED DEFAULT NULL,
			var7root BIGINT UNSIGNED DEFAULT NULL,
			var8root BIGINT UNSIGNED DEFAULT NULL,
			var9root BIGINT UNSIGNED DEFAULT NULL,
			var10root BIGINT UNSIGNED DEFAULT NULL,
			var11root BIGINT UNSIGNED DEFAULT NULL,
			var12root BIGINT UNSIGNED DEFAULT NULL,
			var13root BIGINT UNSIGNED DEFAULT NULL,
			var14root BIGINT UNSIGNED DEFAULT NULL,
			var15root BIGINT UNSIGNED DEFAULT NULL,
			var16root BIGINT UNSIGNED DEFAULT NULL,
			var17root BIGINT UNSIGNED DEFAULT NULL,
			var18root BIGINT UNSIGNED DEFAULT NULL,
			var19root BIGINT UNSIGNED DEFAULT NULL,
			var20root BIGINT UNSIGNED DEFAULT NULL,
			flagtext TINYINT UNSIGNED DEFAULT 0,
			flagko1 TINYINT UNSIGNED DEFAULT 0,
			flagko2 TINYINT UNSIGNED DEFAULT 0,
			flagko3 TINYINT UNSIGNED DEFAULT 0,
			flagpage TINYINT UNSIGNED DEFAULT 0,
			flagcmd TINYINT UNSIGNED DEFAULT 0,
			flagdesign TINYINT UNSIGNED DEFAULT 0,
			flagdyndesign TINYINT UNSIGNED DEFAULT 0,
			flagsound TINYINT UNSIGNED DEFAULT 0,
			flagspeech TINYINT UNSIGNED DEFAULT 0,
			captiontext VARCHAR(100) DEFAULT NULL,
			captionko1 VARCHAR(100) DEFAULT NULL,
			captionko2 VARCHAR(100) DEFAULT NULL,
			captionko3 VARCHAR(100) DEFAULT NULL,
			KEY (id),
			KEY (errcount)
			) ENGINE=MyISAM DEFAULT CHARSET=latin1
		"), 'Datenbank edomiProject.editVisuElementDef erstellen');
        edomi_update_log($update, sql_call("ALTER TABLE edomiProject.editVisuElement CHANGE controltyp controltyp BIGINT UNSIGNED DEFAULT NULL"), 'Datenbank edomiProject.editVisuElement modifizieren: CHANGE controltyp');
        edomi_update_log($update, sql_call("UPDATE edomiProject.editVisuElement SET controltyp=0 WHERE controltyp=255"), 'Datenbank edomiProject.editVisuElement modifizieren: CHANGE controltyp for Gruppe');
        edomi_update_log($update, sql_call("INSERT INTO edomiProject.editRoot (id,allow,path,rootid,parentid,name,namedb,link,linkid,sortcolumns,sortid) VALUES
			(160,	8,		'/20/', 	160,20,		'Visuelemente', 		'editVisuElementDef', 0, NULL,'id/ID,name/Name',1),
			(161,	8,		'/20/160/', 161,160,	'Allgemein', 			'editVisuElementDef', 0, NULL,NULL,0),
			(162,	8,		'/20/160/', 162,160,	'Eingabe', 				'editVisuElementDef', 0, NULL,NULL,0),
			(163,	8,		'/20/160/', 163,160,	'Archive',			 	'editVisuElementDef', 0, NULL,NULL,0),
			(164,	8,		'/20/160/', 164,160,	'Sonstige', 			'editVisuElementDef', 0, NULL,NULL,0),
			(170,	90,		'/20/160/', 170,160,	'Eigene Visuelemente', 	'editVisuElementDef', 0, NULL,NULL,0)
		"), 'Datenbank edomiProject.editRoot modifizieren: ADD Visuelemente');
        if ($update) {
            edomi_update_log($update, true, 'Überflüssige Dateien löschen...');
            deleteFiles(MAIN_PATH . '/www/visu/apps/app10*.php');
            deleteFiles(MAIN_PATH . '/www/admin/help/1002-255.htm');
            deleteFiles(MAIN_PATH . '/www/visu/include/js/con*.js');
            deleteFiles(MAIN_PATH . '/www/visu/include/js/pre*.js');
        }
        edomi_update_log($update, sql_call("UPDATE edomiProject.editVisuUser SET touchscroll=2 WHERE touchscroll=0"), 'Datenbank edomiProject.editVisuUser modifizieren: SET touchscroll');
        edomi_update_log($update, sql_call("ALTER TABLE edomiProject.editVisuUser ADD COLUMN click SMALLINT UNSIGNED DEFAULT 0 AFTER touch"), 'Datenbank edomiProject.editVisuUser modifizieren: ADD click');
        edomi_update_log($update, sql_call("ALTER TABLE edomiProject.editVisuUser ADD COLUMN noacksounds SMALLINT UNSIGNED DEFAULT 0 AFTER touch"), 'Datenbank edomiProject.editVisuUser modifizieren: ADD noacksounds');
        edomi_update_log($update, sql_call("ALTER TABLE edomiProject.editVisuUser ADD COLUMN noerrors SMALLINT UNSIGNED DEFAULT 0 AFTER touch"), 'Datenbank edomiProject.editVisuUser modifizieren: ADD noerrors');
        edomi_update_log($update, sql_call("ALTER TABLE edomiProject.editVisuUser ADD COLUMN preload SMALLINT UNSIGNED DEFAULT 0 AFTER touch"), 'Datenbank edomiProject.editVisuUser modifizieren: ADD preload');
        edomi_update_log($update, sql_call("ALTER TABLE edomiProject.editVisuPage ADD COLUMN bgshadow TINYINT UNSIGNED DEFAULT 0 AFTER bgdark"), 'Datenbank edomiProject.editVisuPage modifizieren: ADD bgshadow');
        edomi_update_log($update, sql_call("UPDATE edomiProject.editVisuPage SET bgshadow=1"), 'Datenbank edomiProject.editVisuPage modifizieren: SET bgshadow');
        edomi_update_log($update, sql_call("UPDATE edomiProject.editVisuElement SET controltyp=18,var1=0 WHERE controltyp=16 AND var1=1"), 'Datenbank edomiProject.editVisuElement modifizieren: CHANGE controltyp=16 to 18 (Skizze)');
        edomi_update_log($update, sql_call("ALTER TABLE edomiProject.editVisuElement ADD COLUMN gaid3 BIGINT UNSIGNED DEFAULT NULL AFTER gaid2"), 'Datenbank edomiProject.editVisuElement modifizieren: ADD gaid3');
        edomi_update_log($update, sql_call("ALTER TABLE edomiProject.editVisuElement ADD KEY (gaid3)"), 'Datenbank edomiProject.editVisuElement modifizieren: ADD KEY gaid3');
        edomi_update_log($update, sql_call("ALTER TABLE edomiProject.editVisuElement ADD COLUMN linkid BIGINT UNSIGNED NOT NULL DEFAULT 0 AFTER groupid"), 'Datenbank edomiProject.editVisuElement modifizieren: ADD linkid');
        edomi_update_log($update, sql_call("ALTER TABLE edomiProject.editVisuElement ADD KEY (linkid)"), 'Datenbank edomiProject.editVisuElement modifizieren: ADD KEY linkid');
        edomi_update_log($update, sql_call("ALTER TABLE edomiProject.editVisuElement ADD COLUMN tmp2 BIGINT UNSIGNED DEFAULT NULL AFTER tmp"), 'Datenbank edomiProject.editVisuElement modifizieren: ADD tmp2');
        edomi_update_log($update, sql_call("ALTER TABLE edomiProject.editVisuElement ADD KEY (tmp2)"), 'Datenbank edomiProject.editVisuElement modifizieren: ADD KEY tmp2');
        edomi_update_log($update, sql_call("UPDATE edomiProject.editVisuElement SET var3=0,var4=0,var5=0,var6=0,var11=0,var12=0,var13=0,var14=0 WHERE controltyp=1"), 'Datenbank edomiProject.editVisuElement modifizieren: SET defaultvalues (Universalelement)');
        edomi_update_log($update, sql_call("UPDATE edomiProject.editVisuElement SET var3=1 WHERE controltyp=1 AND var1=1"), 'Datenbank edomiProject.editVisuElement modifizieren: SET defaultvalues (Universalelement)');
        edomi_update_log($update, sql_call("UPDATE edomiProject.editVisuElement SET var7=100,var11=0 WHERE controltyp=27"), 'Datenbank edomiProject.editVisuElement modifizieren: SET defaultvalues (Rundinstrument)');
        edomi_update_log($update, sql_call("UPDATE edomiProject.editVisuElement SET var6=1 WHERE controltyp=29"), 'Datenbank edomiProject.editVisuElement modifizieren: SET defaultvalues (Bild-URL/Webseite)');
        edomi_update_log($update, sql_call("UPDATE edomiProject.editVisuElement SET var5=90,var6=5 WHERE controltyp=15"), 'Datenbank edomiProject.editVisuElement modifizieren: SET defaultvalues (Farbauswahl)');
        edomi_update_log($update, sql_call("UPDATE edomiProject.editVisuElement SET var11=1 WHERE controltyp=22"), 'Datenbank edomiProject.editVisuElement modifizieren: SET defaultvalues (ZSU)');
        edomi_update_log($update, sql_call("UPDATE edomiProject.editVisuElement SET var11=1 WHERE controltyp=23"), 'Datenbank edomiProject.editVisuElement modifizieren: SET defaultvalues (AWS)');
        edomi_update_log($update, sql_call("UPDATE edomiProject.editVisuElement SET var2=NULL,var11=1 WHERE controltyp=32"), 'Datenbank edomiProject.editVisuElement modifizieren: SET defaultvalues (TSU)');
        edomi_update_log($update, sql_call("UPDATE edomiProject.editVisuElement SET var4=3 WHERE controltyp=24"), 'Datenbank edomiProject.editVisuElement modifizieren: SET defaultvalues (Codeschloss)');
        edomi_update_log($update, sql_call("UPDATE edomiProject.editVisuElement SET var9=100,var10=70,var11=100,var12=0,var13=360,var18=100,var19=1 WHERE controltyp=11"), 'Datenbank edomiProject.editVisuElement modifizieren: SET defaultvalues (Drehregler)');
        edomi_update_log($update, sql_call("UPDATE edomiProject.editVisuElement SET var12=45,var13=315 WHERE controltyp=11 AND var1&4"), 'Datenbank edomiProject.editVisuElement modifizieren: SET defaultvalues (Drehregler)');
        edomi_update_log($update, sql_call("UPDATE edomiProject.editVisuElement SET var1=0 WHERE controltyp=11 AND var1=4"), 'Datenbank edomiProject.editVisuElement modifizieren: SET defaultvalues (Drehregler)');
        edomi_update_log($update, sql_call("UPDATE edomiProject.editVisuElement SET var1=2 WHERE controltyp=11 AND var1=6"), 'Datenbank edomiProject.editVisuElement modifizieren: SET defaultvalues (Drehregler)');
        edomi_update_log($update, sql_call("UPDATE edomiProject.editVisuElement SET var6=3,var9=90,var10=70,var11=100,var18=100,var19=1 WHERE controltyp=12"), 'Datenbank edomiProject.editVisuElement modifizieren: SET defaultvalues (Dimmer)');
        edomi_update_log($update, sql_call("UPDATE edomiProject.editVisuElement SET var9=100,var10=50,var19=0 WHERE controltyp=13"), 'Datenbank edomiProject.editVisuElement modifizieren: SET defaultvalues (Schieberegler)');
        edomi_update_log($update, sql_call("UPDATE edomiProject.editVisuElement SET var10='' WHERE controltyp=17"), 'Datenbank edomiProject.editVisuElement modifizieren: SET defaultvalues (Analoguhr)');
        edomi_update_log($update, sql_call("UPDATE edomiProject.editVisuElement SET controltyp=19,var3=0,var4=0,var5=null,var6=0,var7=null WHERE controltyp=20 AND var1>0"), 'Datenbank edomiProject.editVisuElement modifizieren: SET controltyp=20 to 19 (Kamera)');
        edomi_update_log($update, sql_call("UPDATE edomiProject.editVisuElement SET var1=var3,var2=var7 WHERE controltyp=20"), 'Datenbank edomiProject.editVisuElement modifizieren: SET defaultvalues (Kameraarchiv)');
        edomi_update_log($update, sql_call("UPDATE edomiProject.editVisuElement SET var3=var6,var4=var5 WHERE controltyp=20"), 'Datenbank edomiProject.editVisuElement modifizieren: SET defaultvalues (Kameraarchiv)');
        edomi_update_log($update, sql_call("UPDATE edomiProject.editVisuElement SET var5=0,var6=1,var7=null WHERE controltyp=20"), 'Datenbank edomiProject.editVisuElement modifizieren: SET defaultvalues (Kameraarchiv)');
        edomi_update_log($update, sql_call("ALTER TABLE edomiProject.editVisuAnim ADD COLUMN fillmode TINYINT UNSIGNED DEFAULT 0 AFTER keyframes"), 'Datenbank edomiProject.editVisuAnim modifizieren: ADD fillmode');
        edomi_update_log($update, sql_call("ALTER TABLE edomiProject.editVisuAnim ADD COLUMN direction TINYINT UNSIGNED DEFAULT 0 AFTER keyframes"), 'Datenbank edomiProject.editVisuAnim modifizieren: ADD direction');
        edomi_update_log($update, sql_call("ALTER TABLE edomiProject.editVisuAnim ADD COLUMN delay FLOAT UNSIGNED DEFAULT 0 AFTER keyframes"), 'Datenbank edomiProject.editVisuAnim modifizieren: ADD delay');
        edomi_update_log($update, sql_call("ALTER TABLE edomiProject.editVisuAnim ADD COLUMN timing TINYINT UNSIGNED DEFAULT 0 AFTER keyframes"), 'Datenbank edomiProject.editVisuAnim modifizieren: ADD timing');
        edomi_update_log($update, sql_call("ALTER TABLE edomiProject.editVisuImg ADD COLUMN ts VARCHAR(20) DEFAULT NULL AFTER name"), 'Datenbank edomiProject.editVisuImg modifizieren: ADD ts');
        edomi_update_log($update, sql_call("ALTER TABLE edomiProject.editVisuFont ADD COLUMN ts VARCHAR(20) DEFAULT NULL AFTER name"), 'Datenbank edomiProject.editVisuFont modifizieren: ADD ts');
        edomi_update_log($update, sql_call("ALTER TABLE edomiProject.editVisuSnd ADD COLUMN ts VARCHAR(20) DEFAULT NULL AFTER name"), 'Datenbank edomiProject.editVisuSnd modifizieren: ADD ts');
        edomi_update_log($update, sql_call("UPDATE edomiProject.editVisuImg SET ts='" . date('YmdHis') . "'"), 'Datenbank edomiProject.editVisuImg modifizieren: SET ts');
        edomi_update_log($update, sql_call("UPDATE edomiProject.editVisuFont SET ts='" . date('YmdHis') . "'"), 'Datenbank edomiProject.editVisuFont modifizieren: SET ts');
        edomi_update_log($update, sql_call("UPDATE edomiProject.editVisuFont SET fontname='' WHERE fonttyp=1"), 'Datenbank edomiProject.editVisuFont modifizieren: SET fontname');
        edomi_update_log($update, sql_call("UPDATE edomiProject.editVisuSnd SET ts='" . date('YmdHis') . "'"), 'Datenbank edomiProject.editVisuSnd modifizieren: SET ts');
        edomi_update_log($update, sql_call("ALTER TABLE edomiProject.editCam ADD COLUMN dvrgaid2 BIGINT UNSIGNED DEFAULT NULL AFTER dvrgaid"), 'Datenbank edomiProject.editCam modifizieren: ADD dvrgaid2');
        edomi_update_log($update, sql_call("ALTER TABLE edomiProject.editVisuUserList ADD COLUMN sspageid BIGINT UNSIGNED DEFAULT NULL AFTER defaultpageid"), 'Datenbank edomiProject.editVisuUserList modifizieren: ADD sspageid');
        if (!sql_columnExists('edomiLive.visuUserList', 'sspageid')) {
            edomi_update_log($update, sql_call("ALTER TABLE edomiLive.visuUserList ADD COLUMN sspageid BIGINT UNSIGNED DEFAULT NULL AFTER defaultpageid"), 'Datenbank edomiLive.visuUserList modifizieren: ADD sspageid');
        }
    }
    if ($version < 2.01) {
        $logUpdate .= '2.01/';
        edomi_update_log($update, sql_call("UPDATE edomiProject.editRoot SET name='Experimentell (17)' WHERE id=17"), 'Datenbank edomiProject.editRoot modifizieren: SET name');
        edomi_update_log($update, sql_call("INSERT INTO edomiProject.editRoot (id,allow,path,rootid,parentid,name,namedb,link,linkid,sortcolumns,sortid) VALUES
			(155,	126,	'/20/', 	155,20,		'Formatierung (Meldungsarchive)', 'editVisuFormat', 0, NULL,'id/ID,name/Name',1)
		"), 'Datenbank edomiProject.editRoot modifizieren: ADD Formatierung (Meldungsarchive)');
        edomi_update_log($update, sql_call("CREATE TABLE IF NOT EXISTS edomiProject.editVisuFormat (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			folderid BIGINT UNSIGNED DEFAULT NULL,
			name VARCHAR(200) DEFAULT NULL,
			fgid BIGINT UNSIGNED DEFAULT NULL,
			bgid BIGINT UNSIGNED DEFAULT NULL,
			imgid BIGINT UNSIGNED DEFAULT NULL,
			PRIMARY KEY (id),
			KEY (folderid),
			KEY (name)
			) ENGINE=MyISAM DEFAULT CHARSET=latin1
		"), 'Datenbank edomiProject.editVisuFormat erstellen');
        edomi_update_log($update, sql_call("ALTER TABLE edomiProject.editArchivMsgData ADD COLUMN formatid BIGINT UNSIGNED DEFAULT NULL AFTER msg"), 'Datenbank edomiProject.editArchivMsgData modifizieren: ADD formatid');
        if (!sql_columnExists('edomiLive.archivMsgData', 'formatid')) {
            edomi_update_log($update, sql_call("ALTER TABLE edomiLive.archivMsgData ADD COLUMN formatid BIGINT UNSIGNED DEFAULT NULL AFTER msg"), 'Datenbank edomiLive.archivMsgData modifizieren: ADD formatid');
        }
        edomi_update_log($update, sql_call("UPDATE edomiProject.editVisuElement SET var3=7,var4=0 WHERE controltyp=1 AND var3=0 AND var4=0"), 'Datenbank edomiProject.editVisuElement modifizieren: SET defaultvalues (Universalelement)');
        edomi_update_log($update, sql_call("UPDATE edomiProject.editVisuElement SET var3=4,var4=3 WHERE controltyp=1 AND var3=1 AND var4=0"), 'Datenbank edomiProject.editVisuElement modifizieren: SET defaultvalues (Universalelement)');
        edomi_update_log($update, sql_call("UPDATE edomiProject.editVisuElement SET var3=7,var4=3 WHERE controltyp=1 AND var3=2 AND var4=0"), 'Datenbank edomiProject.editVisuElement modifizieren: SET defaultvalues (Universalelement)');
        edomi_update_log($update, sql_call("UPDATE edomiProject.editVisuElement SET var3=3,var4=4 WHERE controltyp=1 AND var3=0 AND var4=1"), 'Datenbank edomiProject.editVisuElement modifizieren: SET defaultvalues (Universalelement)');
        edomi_update_log($update, sql_call("UPDATE edomiProject.editVisuElement SET var3=0,var4=7 WHERE controltyp=1 AND var3=1 AND var4=1"), 'Datenbank edomiProject.editVisuElement modifizieren: SET defaultvalues (Universalelement)');
        edomi_update_log($update, sql_call("UPDATE edomiProject.editVisuElement SET var3=3,var4=7 WHERE controltyp=1 AND var3=2 AND var4=1"), 'Datenbank edomiProject.editVisuElement modifizieren: SET defaultvalues (Universalelement)');
        edomi_update_log($update, sql_call("UPDATE edomiProject.editVisuElement SET var3=7,var4=4 WHERE controltyp=1 AND var3=0 AND var4=2"), 'Datenbank edomiProject.editVisuElement modifizieren: SET defaultvalues (Universalelement)');
        edomi_update_log($update, sql_call("UPDATE edomiProject.editVisuElement SET var3=4,var4=7 WHERE controltyp=1 AND var3=1 AND var4=2"), 'Datenbank edomiProject.editVisuElement modifizieren: SET defaultvalues (Universalelement)');
        edomi_update_log($update, sql_call("UPDATE edomiProject.editVisuElement SET var3=7,var4=7 WHERE controltyp=1 AND var3=2 AND var4=2"), 'Datenbank edomiProject.editVisuElement modifizieren: SET defaultvalues (Universalelement)');
    }
    if ($version < 2.02) {
        $logUpdate .= '2.02/';
        if ($update) {
            edomi_update_log($update, true, 'Überflüssige Dateien löschen...');
            deleteFiles(MAIN_PATH . '/www/admin/lbs/17000211.php');
            deleteFiles(MAIN_PATH . '/www/admin/lbs/17900002.php');
        }
        edomi_update_log($update, sql_call("INSERT INTO edomiProject.editKo (id,name,folderid,ga,gatyp,valuetyp,value,defaultvalue,initscan,initsend,requestable,remanent,vmin,vmax,vstep,vlist) VALUES (16, 'Kamerafehler', 33, '16', 2, 0, NULL, NULL, 0, 0, 0, 0, NULL, NULL, NULL, NULL)"), 'System-KO 16 hinzufügen');
        for ($t = 1; $t <= 20; $t++) {
            edomi_update_log($update, sql_call("ALTER TABLE edomiProject.editVisuElement MODIFY var" . $t . " VARCHAR(1000) DEFAULT NULL"), 'Datenbank edomiProject.editVisuElement modifizieren: var' . $t . ' VARCHAR(1000)');
            edomi_update_log($update, sql_call("ALTER TABLE edomiProject.editVisuElementDef MODIFY var" . $t . " VARCHAR(1000) DEFAULT NULL"), 'Datenbank edomiProject.editVisuElementDef modifizieren: var' . $t . ' VARCHAR(1000)');
        }
        edomi_update_log($update, sql_call("ALTER TABLE edomiProject.editAwsList ADD COLUMN gaid2 BIGINT UNSIGNED DEFAULT NULL AFTER gaid"), 'Datenbank edomiProject.editAwsList modifizieren: ADD gaid2');
        edomi_update_log($update, sql_call("ALTER TABLE edomiProject.editAwsList ADD KEY (gaid2)"), 'Datenbank edomiProject.editAwsList modifizieren: ADD KEY gaid2');
        edomi_update_log($update, sql_call("ALTER TABLE edomiProject.editSceneList ADD COLUMN valuegaid BIGINT UNSIGNED DEFAULT NULL AFTER learngaid"), 'Datenbank edomiProject.editSceneList modifizieren: ADD valuegaid');
        edomi_update_log($update, sql_call("ALTER TABLE edomiProject.editSceneList ADD KEY (valuegaid)"), 'Datenbank edomiProject.editSceneList modifizieren: ADD KEY valuegaid');
        if ($update) {
            edomi_update_log($update, sql_call("ALTER TABLE edomiLive.sceneList ADD COLUMN valuegaid BIGINT UNSIGNED DEFAULT NULL AFTER learngaid"), 'Datenbank edomiLive.sceneList modifizieren: ADD valuegaid');
            edomi_update_log($update, sql_call("ALTER TABLE edomiLive.sceneList ADD KEY (valuegaid)"), 'Datenbank edomiLive.sceneList modifizieren: ADD KEY valuegaid');
        }
    }
    if ($version < 2.03) {
        $logUpdate .= '2.03/';
        if ($update) {
            edomi_update_log($update, true, 'Überflüssige Dateien löschen...');
            deleteFiles(MAIN_PATH . '/www/admin/help/edomibrowserapp.htm');
            deleteFiles(MAIN_PATH . '/www/edomibrowser.apk');
        }
        edomi_update_log($update, sql_call("DELETE FROM edomiProject.editVisuCmdList WHERE cmd=31"), 'Datenbank edomiProject.editVisuCmdList modifizieren: (EDOMI-Browser-App: Befehle löschen)');
        edomi_update_log($update, sql_call("DELETE FROM edomiProject.editLogicCmdList WHERE cmd=31"), 'Datenbank edomiProject.editLogicCmdList modifizieren: (EDOMI-Browser-App: Befehle löschen)');
        edomi_update_log($update, sql_call("DELETE FROM edomiProject.editSequenceCmdList WHERE cmd=31"), 'Datenbank edomiProject.editSequenceCmdList modifizieren: (EDOMI-Browser-App: Befehle löschen)');
        edomi_update_log($update, sql_call("DELETE FROM edomiProject.editMacroCmdList WHERE cmd=31"), 'Datenbank edomiProject.editMacroCmdList modifizieren: (EDOMI-Browser-App: Befehle löschen)');
        edomi_update_log($update, sql_call("ALTER TABLE edomiProject.editVisuUser ADD COLUMN longclick SMALLINT DEFAULT 0 AFTER autologout"), 'Datenbank edomiProject.editVisuUser modifizieren: ADD longclick');
        edomi_update_log($update, sql_call("ALTER TABLE edomiProject.editKo ADD COLUMN endvalue VARCHAR(10000) DEFAULT NULL AFTER defaultvalue"), 'Datenbank edomiProject.editKo modifizieren: ADD endvalue');
        edomi_update_log($update, sql_call("ALTER TABLE edomiProject.editKo ADD COLUMN endsend TINYINT UNSIGNED DEFAULT 0 AFTER initsend"), 'Datenbank edomiProject.editKo modifizieren: ADD endsend');
        if (!sql_columnExists('edomiLive.ko', 'endvalue')) {
            edomi_update_log($update, sql_call("ALTER TABLE edomiLive.ko ADD COLUMN endvalue VARCHAR(10000) DEFAULT NULL AFTER defaultvalue"), 'Datenbank edomiLive.ko modifizieren: ADD endvalue');
        }
        if (!sql_columnExists('edomiLive.ko', 'endsend')) {
            edomi_update_log($update, sql_call("ALTER TABLE edomiLive.ko ADD COLUMN endsend TINYINT UNSIGNED DEFAULT 0 AFTER initsend"), 'Datenbank edomiLive.ko modifizieren: ADD endsend');
        }
    }
    return $logUpdate;
}

function edomi_update_log($isUpdate, $ok, $logMsg)
{
    if ($isUpdate) {
        echo (($ok === false) ? 'FEHLER: ' : '') . $logMsg . "\n";
        writeToCustomLog('EDOMIUPDATE', (($ok === false) ? 'FEHLER' : ''), $logMsg);
    }
} ?>
