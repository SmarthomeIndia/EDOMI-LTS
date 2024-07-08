<?
/*
*/
?><? ?><? function init_DB_Admin()
{
    sql_call("CREATE DATABASE IF NOT EXISTS edomiAdmin");
    if (!sql_dbExists('edomiAdmin')) {
        return false;
    }
    sql_call("DROP TABLE IF EXISTS edomiAdmin.project");
    $r = sql_call("CREATE TABLE edomiAdmin.project (
		id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
		name VARCHAR(200) DEFAULT NULL,
		createdate datetime DEFAULT NULL,
		savedate datetime DEFAULT NULL,
		livedate datetime DEFAULT NULL,
		edit TINYINT UNSIGNED DEFAULT 0,
		live TINYINT UNSIGNED DEFAULT 0,
		PRIMARY KEY (id)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1
	");
    if (!$r) {
        return false;
    }
    sql_call("DROP TABLE IF EXISTS edomiAdmin.user");
    $r = sql_call("CREATE TABLE edomiAdmin.user (
		id BIGINT UNSIGNED DEFAULT NULL,
		typ TINYINT UNSIGNED DEFAULT NULL,
		login VARCHAR(30) DEFAULT NULL,
		pass VARCHAR(30) DEFAULT NULL,
		logindate datetime DEFAULT NULL,
		logoutdate datetime DEFAULT NULL,
		actiondate datetime DEFAULT NULL,
		loginip VARCHAR(45) DEFAULT NULL,
		sid VARCHAR(30) DEFAULT NULL,
		KEY (id),
		KEY (typ),
		KEY (sid)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1
	");
    if (!$r) {
        return false;
    }
    $r = sql_call("INSERT INTO edomiAdmin.user (id,typ,login,pass) VALUES
		(1,0,'admin','admin'),
		(2,1,'status','status'),
		(3,10,'remote','remote')
	");
    if (!$r) {
        return false;
    }
    return true;
}

function init_DB_Project()
{
    sql_call("DROP DATABASE IF EXISTS edomiProject");
    sql_call("CREATE DATABASE edomiProject");
    if (!sql_dbExists('edomiProject')) {
        return false;
    }
    $r = sql_call("CREATE TABLE IF NOT EXISTS edomiProject.editProjectInfo (
		id BIGINT UNSIGNED DEFAULT NULL,
		edomiversion VARCHAR(30) DEFAULT NULL,
		projectversion VARCHAR(30) DEFAULT NULL,
		text VARCHAR(10000) DEFAULT NULL,
		KEY (id)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1
	");
    if (!$r) {
        return false;
    }
    $r = sql_call("INSERT INTO edomiProject.editProjectInfo (id,edomiversion) VALUES
		(1,'" . global_version . "')
	");
    if (!$r) {
        return false;
    }
    $r = sql_call("CREATE TABLE IF NOT EXISTS edomiProject.editRoot (
		id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
		allow TINYINT UNSIGNED DEFAULT NULL,
		path VARCHAR(10000) DEFAULT NULL,
		rootid BIGINT UNSIGNED DEFAULT NULL,
		parentid BIGINT UNSIGNED DEFAULT NULL,
		name VARCHAR(100) DEFAULT NULL,
		namedb VARCHAR(100) DEFAULT NULL,
		link BIGINT UNSIGNED NOT NULL DEFAULT 0,
		linkid BIGINT UNSIGNED DEFAULT NULL,
		sortcolumns VARCHAR(100) DEFAULT NULL,
		sortid TINYINT DEFAULT 0,
		tmp BIGINT UNSIGNED DEFAULT NULL,
		PRIMARY KEY (id),
		KEY (rootid),
		KEY (parentid),
		KEY (path),
		KEY (link),
		KEY (linkid),
		KEY (tmp)
		) ENGINE=MyISAM AUTO_INCREMENT=1000 DEFAULT CHARSET=latin1
	");
    if (!$r) {
        return false;
    }
    $r = sql_call("CREATE TABLE IF NOT EXISTS edomiProject.editChart (
		id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
		folderid BIGINT UNSIGNED DEFAULT NULL,
		name VARCHAR(200) DEFAULT NULL,
		titel VARCHAR(100) DEFAULT NULL,
		datefrom VARCHAR(10000) DEFAULT NULL,
		dateto VARCHAR(10000) DEFAULT NULL,
		mode TINYINT DEFAULT 0,
		xunit TINYINT DEFAULT 0,
		xinterval BIGINT DEFAULT NULL,
		ymin FLOAT DEFAULT NULL,
		ymax FLOAT DEFAULT NULL,
		yticks INT UNSIGNED DEFAULT NULL,
		ynice TINYINT UNSIGNED DEFAULT NULL,
		PRIMARY KEY (id),
		KEY (folderid),
		KEY (name)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1
	");
    if (!$r) {
        return false;
    }
    $r = sql_call("CREATE TABLE IF NOT EXISTS edomiProject.editChartList (
		id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
		targetid BIGINT UNSIGNED DEFAULT 0,
		archivkoid BIGINT UNSIGNED DEFAULT 0,
		titel VARCHAR(100) DEFAULT NULL,
		charttyp TINYINT UNSIGNED DEFAULT NULL,
		ymin FLOAT DEFAULT NULL,
		ymax FLOAT DEFAULT NULL,
		yticks INT UNSIGNED DEFAULT NULL,
		ynice TINYINT UNSIGNED DEFAULT NULL,
		yshow TINYINT UNSIGNED DEFAULT NULL,
		ygrid1 TINYINT UNSIGNED DEFAULT NULL,
		ygrid2 TINYINT UNSIGNED DEFAULT NULL,
		ygrid3 TINYINT UNSIGNED DEFAULT NULL,
		ystyle TINYINT DEFAULT 0,
		s1 BIGINT UNSIGNED DEFAULT NULL,
		s2 INT DEFAULT NULL,
		s3 INT DEFAULT NULL,
		s4 INT DEFAULT NULL,
		charttyp2 TINYINT UNSIGNED DEFAULT NULL,
		ss1 BIGINT UNSIGNED DEFAULT NULL,
		ss2 INT DEFAULT NULL,
		ss3 INT DEFAULT NULL,
		ss4 INT DEFAULT NULL,
		xinterval BIGINT UNSIGNED DEFAULT NULL,
		yminmax TINYINT UNSIGNED DEFAULT NULL,
		extend1 TINYINT UNSIGNED DEFAULT NULL,
		extend2 TINYINT UNSIGNED DEFAULT NULL,
		yshowvalue TINYINT UNSIGNED DEFAULT NULL,
		yscale TINYINT UNSIGNED DEFAULT NULL,
		sort BIGINT UNSIGNED DEFAULT NULL,
		PRIMARY KEY (id),
		KEY (targetid),
		KEY (sort)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1
	");
    if (!$r) {
        return false;
    }
    $r = sql_call("CREATE TABLE IF NOT EXISTS edomiProject.editEmail (
		id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
		folderid BIGINT UNSIGNED DEFAULT NULL,
		name VARCHAR(200) DEFAULT NULL,
		mailaddr VARCHAR(1000) DEFAULT NULL,
		subject VARCHAR(1000) DEFAULT NULL,
		body VARCHAR(10000) DEFAULT NULL,
		PRIMARY KEY (id),
		KEY (folderid),
		KEY (name)
		) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=latin1
	");
    if (!$r) {
        return false;
    }
    $r = sql_call("CREATE TABLE IF NOT EXISTS edomiProject.editHttpKo (
		id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
		folderid BIGINT UNSIGNED DEFAULT NULL,
		name VARCHAR(200) DEFAULT NULL,
		gaid BIGINT UNSIGNED DEFAULT NULL,
		PRIMARY KEY (id),
		KEY (folderid),
		KEY (name),
		KEY (gaid)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1
	");
    if (!$r) {
        return false;
    }
    $r = sql_call("CREATE TABLE IF NOT EXISTS edomiProject.editPhoneBook (
		id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
		folderid BIGINT UNSIGNED DEFAULT NULL,
		name VARCHAR(200) DEFAULT NULL,
		phone1 VARCHAR(30) DEFAULT NULL,
		phone2 VARCHAR(30) DEFAULT NULL,
		PRIMARY KEY (id),
		KEY (folderid),
		KEY (name),
		KEY (phone1),
		KEY (phone2)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1
	");
    if (!$r) {
        return false;
    }
    $r = sql_call("CREATE TABLE IF NOT EXISTS edomiProject.editPhoneCall (
		id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
		folderid BIGINT UNSIGNED DEFAULT NULL,
		name VARCHAR(200) DEFAULT NULL,
		phoneid1 BIGINT UNSIGNED DEFAULT NULL,
		phoneid2 BIGINT UNSIGNED DEFAULT NULL,
		gaid1 BIGINT UNSIGNED DEFAULT NULL,
		gaid2 BIGINT UNSIGNED DEFAULT NULL,
		gaid3 BIGINT UNSIGNED DEFAULT NULL,
		typ TINYINT UNSIGNED DEFAULT 0,
		PRIMARY KEY (id),
		KEY (folderid),
		KEY (name),
		KEY (phoneid1),
		KEY (phoneid2)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1
	");
    if (!$r) {
        return false;
    }
    $r = sql_call("CREATE TABLE IF NOT EXISTS edomiProject.editArchivPhone (
		id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
		folderid BIGINT UNSIGNED DEFAULT NULL,
		name VARCHAR(100) DEFAULT NULL,
		keep INT UNSIGNED NOT NULL DEFAULT 0,
		outgaid BIGINT UNSIGNED DEFAULT NULL,
		PRIMARY KEY (id),
		KEY (folderid),
		KEY (name)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1
	");
    if (!$r) {
        return false;
    }
    $r = sql_call("CREATE TABLE IF NOT EXISTS edomiProject.editArchivPhoneData (
		datetime datetime DEFAULT NULL,
		ms int(11) DEFAULT NULL,
		targetid BIGINT UNSIGNED DEFAULT 0,
		phone VARCHAR(60) DEFAULT NULL,
		phoneid BIGINT UNSIGNED DEFAULT NULL,
		typ TINYINT UNSIGNED DEFAULT 0,
		status TINYINT UNSIGNED DEFAULT 0,
		KEY (datetime,ms),
		KEY (targetid),
		KEY (phone),
		KEY (phoneid),
		KEY (status),
		KEY (typ)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1
	");
    if (!$r) {
        return false;
    }
    $r = sql_call("CREATE TABLE IF NOT EXISTS edomiProject.editAws (
		id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
		folderid BIGINT UNSIGNED DEFAULT NULL,
		name VARCHAR(100) DEFAULT NULL,
		gaid BIGINT UNSIGNED DEFAULT NULL,
		recordpointer BIGINT UNSIGNED DEFAULT NULL,
		playpointer BIGINT UNSIGNED DEFAULT NULL,
		PRIMARY KEY (id),
		KEY (folderid),
		KEY (name),
		KEY (recordpointer),
		KEY (playpointer),
		KEY (gaid)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1
	");
    if (!$r) {
        return false;
    }
    $r = sql_call("CREATE TABLE IF NOT EXISTS edomiProject.editAwsList (
		id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
		targetid BIGINT UNSIGNED DEFAULT 0,
		gaid BIGINT UNSIGNED DEFAULT NULL,
		gaid2 BIGINT UNSIGNED DEFAULT NULL,
		gavalue1 VARCHAR(10000) DEFAULT NULL,
		gavalue2 VARCHAR(10000) DEFAULT NULL,
		PRIMARY KEY (id),
		KEY (targetid),
		KEY (gaid),
		KEY (gaid2)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1
	");
    if (!$r) {
        return false;
    }
    $r = sql_call("CREATE TABLE IF NOT EXISTS edomiProject.editAwsData (
		timestamp BIGINT UNSIGNED DEFAULT NULL,
		targetid BIGINT UNSIGNED DEFAULT 0,
		gaid BIGINT UNSIGNED DEFAULT NULL,
		gavalue VARCHAR(10000) DEFAULT NULL,
		KEY (timestamp),
		KEY (targetid),
		KEY (gaid)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1
	");
    if (!$r) {
        return false;
    }
    $r = sql_call("CREATE TABLE IF NOT EXISTS edomiProject.editTimer (
		id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
		folderid BIGINT UNSIGNED DEFAULT NULL,
		name VARCHAR(100) DEFAULT NULL,
		gaid BIGINT UNSIGNED DEFAULT NULL,
		gaid2 BIGINT UNSIGNED DEFAULT NULL,
		PRIMARY KEY (id),
		KEY (folderid),
		KEY (name),
		KEY (gaid)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1
	");
    if (!$r) {
        return false;
    }
    $r = sql_call("CREATE TABLE IF NOT EXISTS edomiProject.editTimerData (
		id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
		targetid BIGINT UNSIGNED DEFAULT 0,
		fixed TINYINT UNSIGNED NOT NULL DEFAULT 0,
		cmdid BIGINT UNSIGNED DEFAULT 0,
		hour SMALLINT UNSIGNED DEFAULT NULL,
		minute SMALLINT UNSIGNED DEFAULT NULL,
		day1 SMALLINT UNSIGNED DEFAULT NULL,
		day2 SMALLINT UNSIGNED DEFAULT NULL,
		month1 SMALLINT UNSIGNED DEFAULT NULL,
		month2 SMALLINT UNSIGNED DEFAULT NULL,
		year1 SMALLINT UNSIGNED DEFAULT NULL,
		year2 SMALLINT UNSIGNED DEFAULT NULL,
		mode TINYINT UNSIGNED DEFAULT 0,
		d0 TINYINT UNSIGNED DEFAULT 0,
		d1 TINYINT UNSIGNED DEFAULT 0,
		d2 TINYINT UNSIGNED DEFAULT 0,
		d3 TINYINT UNSIGNED DEFAULT 0,
		d4 TINYINT UNSIGNED DEFAULT 0,
		d5 TINYINT UNSIGNED DEFAULT 0,
		d6 TINYINT UNSIGNED DEFAULT 0,
		d7 TINYINT UNSIGNED DEFAULT 0,
		PRIMARY KEY (id),
		KEY (targetid),
		KEY (hour),
		KEY (minute),
		KEY (day1),
		KEY (day2),
		KEY (month1),
		KEY (month2),
		KEY (year1),
		KEY (year2),
		KEY (d0),
		KEY (d1),
		KEY (d2),
		KEY (d3),
		KEY (d4),
		KEY (d5),
		KEY (d6)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1
	");
    if (!$r) {
        return false;
    }
    $r = sql_call("CREATE TABLE IF NOT EXISTS edomiProject.editTimerMacroList (
		id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
		targetid BIGINT UNSIGNED DEFAULT NULL,
		timerid BIGINT UNSIGNED DEFAULT NULL,
		sort BIGINT UNSIGNED DEFAULT NULL,
		PRIMARY KEY (id),
		KEY (targetid),
		KEY (timerid),
		KEY (sort)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1
	");
    if (!$r) {
        return false;
    }
    $r = sql_call("CREATE TABLE IF NOT EXISTS edomiProject.editAgenda (
		id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
		folderid BIGINT UNSIGNED DEFAULT NULL,
		name VARCHAR(100) DEFAULT NULL,
		gaid BIGINT UNSIGNED DEFAULT NULL,
		gaid2 BIGINT UNSIGNED DEFAULT NULL,
		PRIMARY KEY (id),
		KEY (folderid),
		KEY (name),
		KEY (gaid)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1
	");
    if (!$r) {
        return false;
    }
    $r = sql_call("CREATE TABLE IF NOT EXISTS edomiProject.editAgendaData (
		id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
		targetid BIGINT UNSIGNED DEFAULT 0,
		name VARCHAR(100) DEFAULT NULL,
		fixed TINYINT UNSIGNED NOT NULL DEFAULT 0,
		cmdid BIGINT UNSIGNED DEFAULT 0,
		hour SMALLINT UNSIGNED DEFAULT NULL,
		minute SMALLINT UNSIGNED DEFAULT NULL,
		date1 DATE DEFAULT NULL,
		date2 DATE DEFAULT NULL,
		step BIGINT UNSIGNED DEFAULT 0,
		unit SMALLINT UNSIGNED DEFAULT 0,
		d7 TINYINT UNSIGNED DEFAULT 0,
		PRIMARY KEY (id),
		KEY (targetid),
		KEY (hour),
		KEY (minute),
		KEY (date1),
		KEY (date2),
		KEY (step),
		KEY (unit)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1
	");
    if (!$r) {
        return false;
    }
    $r = sql_call("CREATE TABLE IF NOT EXISTS edomiProject.editAgendaMacroList (
		id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
		targetid BIGINT UNSIGNED DEFAULT NULL,
		agendaid BIGINT UNSIGNED DEFAULT NULL,
		sort BIGINT UNSIGNED DEFAULT NULL,
		PRIMARY KEY (id),
		KEY (targetid),
		KEY (agendaid),
		KEY (sort)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1
	");
    if (!$r) {
        return false;
    }
    $r = sql_call("CREATE TABLE IF NOT EXISTS edomiProject.editSequence (
		id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
		folderid BIGINT UNSIGNED DEFAULT NULL,
		name VARCHAR(100) DEFAULT NULL,
		datetime datetime DEFAULT NULL,
		ms INT DEFAULT NULL,
		playpointer BIGINT UNSIGNED DEFAULT 0,
		PRIMARY KEY (id),
		KEY (folderid),
		KEY (name),
		KEY (playpointer)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1
	");
    if (!$r) {
        return false;
    }
    $r = sql_call("CREATE TABLE IF NOT EXISTS edomiProject.editSequenceCmdList (
		id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
		targetid BIGINT UNSIGNED DEFAULT 0,
		cmd TINYINT UNSIGNED DEFAULT NULL,
		cmdid1 BIGINT UNSIGNED DEFAULT NULL,
		cmdid2 BIGINT UNSIGNED DEFAULT NULL,
		cmdoption1 INT SIGNED DEFAULT 0,
		cmdoption2 INT SIGNED DEFAULT 0,
		cmdvalue1 VARCHAR(10000) DEFAULT NULL,
		cmdvalue2 VARCHAR(10000) DEFAULT NULL,
		delay INT UNSIGNED DEFAULT NULL,
		sort BIGINT UNSIGNED DEFAULT NULL,
		PRIMARY KEY (id),
		KEY (targetid),
		KEY (cmd),
		KEY (cmdid1),
		KEY (cmdid2),
		KEY (cmdvalue1),
		KEY (cmdvalue2),
		KEY (sort)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1
	");
    if (!$r) {
        return false;
    }
    $r = sql_call("CREATE TABLE IF NOT EXISTS edomiProject.editMacro (
		id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
		folderid BIGINT UNSIGNED DEFAULT NULL,
		name VARCHAR(100) DEFAULT NULL,
		PRIMARY KEY (id),
		KEY (folderid),
		KEY (name)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1
	");
    if (!$r) {
        return false;
    }
    $r = sql_call("CREATE TABLE IF NOT EXISTS edomiProject.editMacroCmdList (
		id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
		targetid BIGINT UNSIGNED DEFAULT 0,
		cmd TINYINT UNSIGNED DEFAULT NULL,
		cmdid1 BIGINT UNSIGNED DEFAULT NULL,
		cmdid2 BIGINT UNSIGNED DEFAULT NULL,
		cmdoption1 INT SIGNED DEFAULT 0,
		cmdoption2 INT SIGNED DEFAULT 0,
		cmdvalue1 VARCHAR(10000) DEFAULT NULL,
		cmdvalue2 VARCHAR(10000) DEFAULT NULL,
		PRIMARY KEY (id),
		KEY (targetid),
		KEY (cmd),
		KEY (cmdid1),
		KEY (cmdid2),
		KEY (cmdvalue1),
		KEY (cmdvalue2)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1
	");
    if (!$r) {
        return false;
    }
    $r = sql_call("CREATE TABLE IF NOT EXISTS edomiProject.editIp (
		id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
		folderid BIGINT UNSIGNED DEFAULT NULL,
		name VARCHAR(100) DEFAULT NULL,
		url VARCHAR(10000) DEFAULT NULL,
		iptyp TINYINT UNSIGNED DEFAULT NULL,
		httperrlog TINYINT UNSIGNED DEFAULT 1,
		httptimeout INT UNSIGNED DEFAULT 10,
		udpraw TINYINT UNSIGNED DEFAULT 0,
		outgaid BIGINT UNSIGNED DEFAULT NULL,
		outgaid2 BIGINT UNSIGNED DEFAULT NULL,
		data VARCHAR(10000) DEFAULT NULL,
		PRIMARY KEY (id),
		KEY (folderid),
		KEY (name)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1
	");
    if (!$r) {
        return false;
    }
    $r = sql_call("CREATE TABLE IF NOT EXISTS edomiProject.editIr (
		id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
		folderid BIGINT UNSIGNED DEFAULT NULL,
		name VARCHAR(100) DEFAULT NULL,
		data VARCHAR(10000) DEFAULT NULL,
		info VARCHAR(50) DEFAULT NULL,
		PRIMARY KEY (id),
		KEY (folderid),
		KEY (name)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1
	");
    if (!$r) {
        return false;
    }
    $r = sql_call("CREATE TABLE IF NOT EXISTS edomiProject.editArchivMsg (
		id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
		folderid BIGINT UNSIGNED DEFAULT NULL,
		name VARCHAR(100) DEFAULT NULL,
		keep INT UNSIGNED NOT NULL DEFAULT 0,
		delay BIGINT UNSIGNED NOT NULL DEFAULT 0,
		outgaid BIGINT UNSIGNED DEFAULT NULL,
		PRIMARY KEY (id),
		KEY (folderid),
		KEY (name)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1
	");
    if (!$r) {
        return false;
    }
    $r = sql_call("CREATE TABLE IF NOT EXISTS edomiProject.editArchivMsgData (
		datetime datetime DEFAULT NULL,
		ms int(11) DEFAULT NULL,
		targetid BIGINT UNSIGNED DEFAULT 0,
		msg VARCHAR(10000) DEFAULT NULL,
		formatid BIGINT UNSIGNED DEFAULT NULL,
		KEY (datetime,ms),
		KEY (targetid)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1
	");
    if (!$r) {
        return false;
    }
    $r = sql_call("CREATE TABLE IF NOT EXISTS edomiProject.editArchivKo (
		id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
		folderid BIGINT UNSIGNED DEFAULT NULL,
		name VARCHAR(100) DEFAULT NULL,
		keep INT UNSIGNED NOT NULL DEFAULT 0,
		delay BIGINT UNSIGNED NOT NULL DEFAULT 0,
		outgaid BIGINT UNSIGNED DEFAULT NULL,
		cmode TINYINT UNSIGNED NOT NULL DEFAULT 0,
		cinterval TINYINT UNSIGNED NOT NULL DEFAULT 10,
		cts TINYINT UNSIGNED NOT NULL DEFAULT 0,
		clist TINYINT UNSIGNED DEFAULT NULL,
		coffset INT UNSIGNED NOT NULL DEFAULT 1,
		cunit TINYINT UNSIGNED NOT NULL DEFAULT 10,
		PRIMARY KEY (id),
		KEY (folderid),
		KEY (name)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1
	");
    if (!$r) {
        return false;
    }
    $r = sql_call("CREATE TABLE IF NOT EXISTS edomiProject.editArchivKoData (
		datetime datetime DEFAULT NULL,
		ms int(11) DEFAULT NULL,
		targetid BIGINT UNSIGNED DEFAULT 0,
		gavalue VARCHAR(10000) DEFAULT NULL,
		KEY (datetime,ms),
		KEY (targetid)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1
	");
    if (!$r) {
        return false;
    }
    $r = sql_call("CREATE TABLE IF NOT EXISTS edomiProject.editCam (
		id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
		cachets BIGINT UNSIGNED DEFAULT NULL,
		folderid BIGINT UNSIGNED DEFAULT NULL,
		name VARCHAR(100) DEFAULT NULL,
		url VARCHAR(10000) DEFAULT NULL,
		mjpeg TINYINT DEFAULT 0,
		dvr TINYINT DEFAULT 0,
		dvrrate INT DEFAULT 5,
		dvrkeep INT DEFAULT 0,
		dvrgaid BIGINT UNSIGNED DEFAULT NULL,
		dvrgaid2 BIGINT UNSIGNED DEFAULT NULL,
		PRIMARY KEY (id),
		KEY (folderid),
		KEY (name)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1
	");
    if (!$r) {
        return false;
    }
    $r = sql_call("CREATE TABLE IF NOT EXISTS edomiProject.editArchivCam (
		id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
		folderid BIGINT UNSIGNED DEFAULT NULL,
		camid BIGINT UNSIGNED DEFAULT NULL,
		name VARCHAR(100) DEFAULT NULL,
		keep INT UNSIGNED NOT NULL DEFAULT 0,
		delay BIGINT UNSIGNED NOT NULL DEFAULT 0,
		outgaid BIGINT UNSIGNED DEFAULT NULL,
		outgaid2 BIGINT UNSIGNED DEFAULT NULL,
		PRIMARY KEY (id),
		KEY (folderid),
		KEY (name)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1
	");
    if (!$r) {
        return false;
    }
    $r = sql_call("CREATE TABLE IF NOT EXISTS edomiProject.editArchivCamData (
		datetime datetime DEFAULT NULL,
		ms int(11) DEFAULT NULL,
		targetid BIGINT UNSIGNED DEFAULT 0,
		camid BIGINT UNSIGNED DEFAULT NULL,
		KEY (datetime,ms),
		KEY (targetid),
		KEY (camid)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1
	");
    if (!$r) {
        return false;
    }
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
    if (!$r) {
        return false;
    }
    $r = sql_call("CREATE TABLE IF NOT EXISTS edomiProject.editScene (
		id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
		folderid BIGINT UNSIGNED DEFAULT NULL,
		name VARCHAR(100) DEFAULT NULL,
		PRIMARY KEY (id),
		KEY (folderid),
		KEY (name)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1
	");
    if (!$r) {
        return false;
    }
    $r = sql_call("CREATE TABLE IF NOT EXISTS edomiProject.editSceneList (
		id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
		targetid BIGINT UNSIGNED DEFAULT 0,
		gaid BIGINT UNSIGNED DEFAULT NULL,
		gavalue VARCHAR(10000) DEFAULT NULL,
		learngaid BIGINT UNSIGNED DEFAULT NULL,
		valuegaid BIGINT UNSIGNED DEFAULT NULL,
		PRIMARY KEY (id),
		KEY (targetid),
		KEY (valuegaid)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1
	");
    if (!$r) {
        return false;
    }
    $r = sql_call("CREATE TABLE IF NOT EXISTS edomiProject.editKo (
		id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
		name VARCHAR(100) DEFAULT NULL,
		folderid BIGINT UNSIGNED DEFAULT NULL,
		ga VARCHAR(11) DEFAULT NULL,
		gatyp TINYINT UNSIGNED DEFAULT NULL,
		valuetyp INT UNSIGNED NOT NULL DEFAULT 0,
		value VARCHAR(10000) DEFAULT NULL,
		defaultvalue VARCHAR(10000) DEFAULT NULL,
		endvalue VARCHAR(10000) DEFAULT NULL,
		initscan TINYINT UNSIGNED DEFAULT 0,
		initsend TINYINT UNSIGNED DEFAULT 0,
		endsend TINYINT UNSIGNED DEFAULT 0,
		requestable TINYINT UNSIGNED DEFAULT 0,
		remanent TINYINT UNSIGNED DEFAULT 0,
		prio TINYINT UNSIGNED NOT NULL DEFAULT 0,
		vmin FLOAT DEFAULT NULL,
		vmax FLOAT DEFAULT NULL,
		vstep FLOAT DEFAULT NULL,
		vlist INT UNSIGNED DEFAULT NULL,
		vcsv VARCHAR(1000) DEFAULT NULL,
		text VARCHAR(1000) DEFAULT NULL,
		PRIMARY KEY (id),
		KEY (folderid),
		KEY (name),
		KEY (ga),
		KEY (gatyp),
		KEY (remanent)
		) ENGINE=MyISAM AUTO_INCREMENT=100 DEFAULT CHARSET=latin1
	");
    if (!$r) {
        return false;
    }
    $r = sql_call("CREATE TABLE IF NOT EXISTS edomiProject.editVisu (
		id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
		folderid BIGINT UNSIGNED DEFAULT NULL,
		name VARCHAR(200) DEFAULT NULL,
		xsize SMALLINT UNSIGNED DEFAULT 0,
		ysize SMALLINT UNSIGNED DEFAULT 0,
		defaultpageid BIGINT UNSIGNED DEFAULT NULL,
		hassound TINYINT UNSIGNED DEFAULT 0,
		hasspeech TINYINT UNSIGNED DEFAULT 0,
		sspageid BIGINT UNSIGNED DEFAULT NULL,
		sstimeout INT UNSIGNED DEFAULT 0,
		indicolor BIGINT UNSIGNED DEFAULT 0,
		indicolor2 BIGINT UNSIGNED DEFAULT 0,
		preview TINYINT UNSIGNED DEFAULT 0,
		PRIMARY KEY (id),
		KEY (folderid),
		KEY (name)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1
	");
    if (!$r) {
        return false;
    }
    $r = sql_call("CREATE TABLE IF NOT EXISTS edomiProject.editVisuUser (
		id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
		folderid BIGINT UNSIGNED DEFAULT NULL,
		login VARCHAR(30) DEFAULT NULL,
		pass VARCHAR(30) DEFAULT NULL,
		gaid BIGINT UNSIGNED DEFAULT NULL,
		gaid2 BIGINT UNSIGNED DEFAULT NULL,
		gaid3 BIGINT UNSIGNED DEFAULT NULL,
		touch SMALLINT UNSIGNED DEFAULT 0,
		preload SMALLINT UNSIGNED DEFAULT 0,
		noerrors SMALLINT UNSIGNED DEFAULT 0,
		noacksounds SMALLINT UNSIGNED DEFAULT 0,
		click SMALLINT UNSIGNED DEFAULT 0,
		touchscroll SMALLINT UNSIGNED DEFAULT 0,
		autologout TINYINT UNSIGNED DEFAULT 0,
		longclick SMALLINT DEFAULT 0,
		PRIMARY KEY (id),
		KEY (folderid),
		KEY (login),
		KEY (pass),
		KEY (gaid),
		KEY (gaid2),
		KEY (gaid3)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1
	");
    if (!$r) {
        return false;
    }
    $r = sql_call("CREATE TABLE IF NOT EXISTS edomiProject.editVisuUserList (
		id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
		targetid BIGINT UNSIGNED DEFAULT NULL,
		visuid BIGINT UNSIGNED DEFAULT NULL,
		defaultpageid BIGINT UNSIGNED DEFAULT NULL,
		sspageid BIGINT UNSIGNED DEFAULT NULL,
		hassound TINYINT UNSIGNED DEFAULT 0,
		hasspeech TINYINT UNSIGNED DEFAULT 0,
		logindate datetime DEFAULT NULL,
		logoutdate datetime DEFAULT NULL,
		actiondate datetime DEFAULT NULL,
		loginip VARCHAR(45) DEFAULT NULL,
		logout TINYINT UNSIGNED DEFAULT 0,
		online TINYINT UNSIGNED DEFAULT 0,
		sid VARCHAR(30) DEFAULT NULL,
		PRIMARY KEY (id),
		KEY (targetid),
		KEY (visuid),
		KEY (actiondate),
		KEY (online),
		KEY (sid)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1
	");
    if (!$r) {
        return false;
    }
    $r = sql_call("CREATE TABLE IF NOT EXISTS edomiProject.editVisuPage (
		id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
		folderid BIGINT UNSIGNED DEFAULT NULL,
		visuid BIGINT UNSIGNED DEFAULT NULL,
		includeid BIGINT UNSIGNED NOT NULL DEFAULT 0,
		globalinclude TINYINT UNSIGNED DEFAULT NULL,
		name VARCHAR(200) DEFAULT NULL,
		pagetyp TINYINT UNSIGNED DEFAULT 0,
		xpos INT DEFAULT NULL,
		ypos INT DEFAULT NULL,
		xsize INT UNSIGNED DEFAULT NULL,
		ysize INT UNSIGNED DEFAULT NULL,
		autoclose INT UNSIGNED DEFAULT 0,
		bgmodal TINYINT UNSIGNED DEFAULT 0,
		bganim TINYINT UNSIGNED DEFAULT 0,
		bgdark TINYINT UNSIGNED DEFAULT 0,
		bgshadow TINYINT UNSIGNED DEFAULT 0,
		bgcolorid BIGINT UNSIGNED DEFAULT 0,
		bgimg BIGINT UNSIGNED DEFAULT NULL,
		xgrid SMALLINT UNSIGNED DEFAULT 1,
		ygrid SMALLINT UNSIGNED DEFAULT 1,
		outlinecolorid BIGINT UNSIGNED DEFAULT 0,
		tmp BIGINT UNSIGNED DEFAULT NULL,
		PRIMARY KEY (id),
		KEY (folderid),
		KEY (name),
		KEY (visuid)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1
	");
    if (!$r) {
        return false;
    }
    $r = sql_call("CREATE TABLE IF NOT EXISTS edomiProject.editVisuElementDesignDef (
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
		s13 BIGINT UNSIGNED DEFAULT NULL,
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
		s48 VARCHAR(1000) DEFAULT NULL,
		PRIMARY KEY (id),
		KEY (folderid),
		KEY (name),
		KEY (styletyp),
		KEY (s1),
		KEY (s2)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1
	");
    if (!$r) {
        return false;
    }
    $r = sql_call("CREATE TABLE IF NOT EXISTS edomiProject.editVisuElement (
		id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
		controltyp BIGINT UNSIGNED DEFAULT NULL,
		dynstylemode INT UNSIGNED DEFAULT 0,
		visuid BIGINT UNSIGNED DEFAULT NULL,
		pageid BIGINT UNSIGNED DEFAULT NULL,
		gaid BIGINT UNSIGNED DEFAULT NULL,
		galive INT DEFAULT 0,
		gaid2 BIGINT UNSIGNED DEFAULT NULL,
		gaid3 BIGINT UNSIGNED DEFAULT NULL,
		zindex INT UNSIGNED DEFAULT 0,
		xpos INT DEFAULT NULL,
		ypos INT DEFAULT NULL,
		xsize INT UNSIGNED DEFAULT NULL,
		ysize INT UNSIGNED DEFAULT NULL,
		text VARCHAR(10000) DEFAULT NULL,
		initonly TINYINT UNSIGNED DEFAULT 0,
		var1 VARCHAR(1000) DEFAULT NULL,
		var2 VARCHAR(1000) DEFAULT NULL,
		var3 VARCHAR(1000) DEFAULT NULL,
		var4 VARCHAR(1000) DEFAULT NULL,
		var5 VARCHAR(1000) DEFAULT NULL,
		var6 VARCHAR(1000) DEFAULT NULL,
		var7 VARCHAR(1000) DEFAULT NULL,
		var8 VARCHAR(1000) DEFAULT NULL,
		var9 VARCHAR(1000) DEFAULT NULL,
		var10 VARCHAR(1000) DEFAULT NULL,
		var11 VARCHAR(1000) DEFAULT NULL,
		var12 VARCHAR(1000) DEFAULT NULL,
		var13 VARCHAR(1000) DEFAULT NULL,
		var14 VARCHAR(1000) DEFAULT NULL,
		var15 VARCHAR(1000) DEFAULT NULL,
		var16 VARCHAR(1000) DEFAULT NULL,
		var17 VARCHAR(1000) DEFAULT NULL,
		var18 VARCHAR(1000) DEFAULT NULL,
		var19 VARCHAR(1000) DEFAULT NULL,
		var20 VARCHAR(1000) DEFAULT NULL,
		gotopageid BIGINT UNSIGNED DEFAULT NULL,
		closepopupid BIGINT UNSIGNED DEFAULT NULL,
		closepopup TINYINT UNSIGNED DEFAULT 0,
		hascmd TINYINT UNSIGNED DEFAULT 0,
		name VARCHAR(200) DEFAULT NULL,
		groupid BIGINT UNSIGNED NOT NULL DEFAULT 0,
		linkid BIGINT UNSIGNED NOT NULL DEFAULT 0,
		layer INT UNSIGNED DEFAULT 0,
		tmp BIGINT UNSIGNED DEFAULT NULL,
		tmp2 BIGINT UNSIGNED DEFAULT NULL,
		PRIMARY KEY (id),
		KEY (controltyp),
		KEY (visuid),
		KEY (pageid),
		KEY (gaid),
		KEY (gaid3),
		KEY (name),
		KEY (groupid),
		KEY (linkid),
		KEY (layer),
		KEY (tmp),
		KEY (tmp2)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1
	");
    if (!$r) {
        return false;
    }
    $r = sql_call("CREATE TABLE IF NOT EXISTS edomiProject.editVisuElementDef (
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
		var1 VARCHAR(1000) DEFAULT NULL,
		var2 VARCHAR(1000) DEFAULT NULL,
		var3 VARCHAR(1000) DEFAULT NULL,
		var4 VARCHAR(1000) DEFAULT NULL,
		var5 VARCHAR(1000) DEFAULT NULL,
		var6 VARCHAR(1000) DEFAULT NULL,
		var7 VARCHAR(1000) DEFAULT NULL,
		var8 VARCHAR(1000) DEFAULT NULL,
		var9 VARCHAR(1000) DEFAULT NULL,
		var10 VARCHAR(1000) DEFAULT NULL,
		var11 VARCHAR(1000) DEFAULT NULL,
		var12 VARCHAR(1000) DEFAULT NULL,
		var13 VARCHAR(1000) DEFAULT NULL,
		var14 VARCHAR(1000) DEFAULT NULL,
		var15 VARCHAR(1000) DEFAULT NULL,
		var16 VARCHAR(1000) DEFAULT NULL,
		var17 VARCHAR(1000) DEFAULT NULL,
		var18 VARCHAR(1000) DEFAULT NULL,
		var19 VARCHAR(1000) DEFAULT NULL,
		var20 VARCHAR(1000) DEFAULT NULL,
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
	");
    if (!$r) {
        return false;
    }
    $r = sql_call("CREATE TABLE IF NOT EXISTS edomiProject.editVisuElementDesign (
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
		s13 BIGINT UNSIGNED DEFAULT NULL,
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
		s48 VARCHAR(1000) DEFAULT NULL,
		PRIMARY KEY (id),
		KEY (targetid),
		KEY (defid),
		KEY (styletyp),
		KEY (s1),
		KEY (s2)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1
	");
    if (!$r) {
        return false;
    }
    $r = sql_call("CREATE TABLE IF NOT EXISTS edomiProject.editVisuCmdList (
		id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
		targetid BIGINT UNSIGNED DEFAULT 0,
		cmd TINYINT UNSIGNED DEFAULT NULL,
		cmdid1 BIGINT UNSIGNED DEFAULT NULL,
		cmdid2 BIGINT UNSIGNED DEFAULT NULL,
		cmdoption1 INT SIGNED DEFAULT 0,
		cmdoption2 INT SIGNED DEFAULT 0,
		cmdvalue1 VARCHAR(10000) DEFAULT NULL,
		cmdvalue2 VARCHAR(10000) DEFAULT NULL,
		PRIMARY KEY (id),
		KEY (targetid),
		KEY (cmd),
		KEY (cmdid1),
		KEY (cmdid2),
		KEY (cmdvalue1),
		KEY (cmdvalue2)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1
	");
    if (!$r) {
        return false;
    }
    $r = sql_call("CREATE TABLE IF NOT EXISTS edomiProject.editVisuBGcol (
		id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
		folderid BIGINT UNSIGNED DEFAULT NULL,
		name VARCHAR(200) DEFAULT NULL,
		color VARCHAR(1000) DEFAULT NULL,
		PRIMARY KEY (id),
		KEY (folderid),
		KEY (name)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1
	");
    if (!$r) {
        return false;
    }
    $r = sql_call("CREATE TABLE IF NOT EXISTS edomiProject.editVisuFGcol (
		id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
		folderid BIGINT UNSIGNED DEFAULT NULL,
		name VARCHAR(200) DEFAULT NULL,
		color VARCHAR(1000) DEFAULT NULL,
		PRIMARY KEY (id),
		KEY (folderid),
		KEY (name)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1
	");
    if (!$r) {
        return false;
    }
    $r = sql_call("CREATE TABLE IF NOT EXISTS edomiProject.editVisuAnim (
		id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
		folderid BIGINT UNSIGNED DEFAULT NULL,
		name VARCHAR(200) DEFAULT NULL,
		keyframes VARCHAR(10000) DEFAULT NULL,
		timing TINYINT UNSIGNED DEFAULT 0,
		delay FLOAT UNSIGNED DEFAULT 0,
		direction TINYINT UNSIGNED DEFAULT 0,
		fillmode TINYINT UNSIGNED DEFAULT 0,
		PRIMARY KEY (id),
		KEY (folderid),
		KEY (name)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1
	");
    if (!$r) {
        return false;
    }
    $r = sql_call("CREATE TABLE IF NOT EXISTS edomiProject.editVisuImg (
		id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
		folderid BIGINT UNSIGNED DEFAULT NULL,
		name VARCHAR(200) DEFAULT NULL,
		ts VARCHAR(20) DEFAULT NULL,
		xsize INT UNSIGNED DEFAULT NULL,
		ysize INT UNSIGNED DEFAULT NULL,
		suffix VARCHAR(10) DEFAULT NULL,
		PRIMARY KEY (id),
		KEY (folderid),
		KEY (name)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1
	");
    if (!$r) {
        return false;
    }
    $r = sql_call("CREATE TABLE IF NOT EXISTS edomiProject.editVisuSnd (
		id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
		folderid BIGINT UNSIGNED DEFAULT NULL,
		name VARCHAR(200) DEFAULT NULL,
		ts VARCHAR(20) DEFAULT NULL,
		PRIMARY KEY (id),
		KEY (folderid),
		KEY (name)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1
	");
    if (!$r) {
        return false;
    }
    $r = sql_call("CREATE TABLE IF NOT EXISTS edomiProject.editVisuFont (
		id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
		folderid BIGINT UNSIGNED DEFAULT NULL,
		name VARCHAR(200) DEFAULT NULL,
		ts VARCHAR(20) DEFAULT NULL,
		fonttyp TINYINT UNSIGNED DEFAULT 0,
		fontname VARCHAR(200) DEFAULT NULL,
		fontstyle TINYINT UNSIGNED DEFAULT 0,
		fontweight TINYINT UNSIGNED DEFAULT 0,
		PRIMARY KEY (id),
		KEY (folderid),
		KEY (name)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1
	");
    if (!$r) {
        return false;
    }
    $r = sql_call("CREATE TABLE IF NOT EXISTS edomiProject.editVisuFormat (
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
	");
    if (!$r) {
        return false;
    }
    $r = sql_call("CREATE TABLE IF NOT EXISTS edomiProject.editLogicElementDef (
		id BIGINT UNSIGNED NOT NULL,
		name VARCHAR(100) DEFAULT NULL,
		title VARCHAR(100) DEFAULT NULL,
		folderid BIGINT UNSIGNED DEFAULT NULL,
		defin BIGINT UNSIGNED DEFAULT 0,
		defout BIGINT UNSIGNED DEFAULT 0,
		defvar BIGINT UNSIGNED DEFAULT 0,
		errcount BIGINT UNSIGNED NOT NULL DEFAULT 0,
		errmsg VARCHAR(10000) DEFAULT NULL,
		exec TINYINT UNSIGNED DEFAULT 0,
		KEY (id),
		KEY (folderid),
		KEY (name)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1
	");
    if (!$r) {
        return false;
    }
    $r = sql_call("CREATE TABLE IF NOT EXISTS edomiProject.editLogicElementDefIn (
		targetid BIGINT UNSIGNED DEFAULT NULL,
		id BIGINT UNSIGNED DEFAULT NULL,
		name VARCHAR(100) DEFAULT NULL,
		value VARCHAR(10000) DEFAULT NULL,
		color TINYINT UNSIGNED DEFAULT 0,
		KEY (targetid),
		KEY (id)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1
	");
    if (!$r) {
        return false;
    }
    $r = sql_call("CREATE TABLE IF NOT EXISTS edomiProject.editLogicElementDefOut (
		targetid BIGINT UNSIGNED DEFAULT NULL,
		id BIGINT UNSIGNED DEFAULT NULL,
		name VARCHAR(100) DEFAULT NULL,
		KEY (targetid),
		KEY (id)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1
	");
    if (!$r) {
        return false;
    }
    $r = sql_call("CREATE TABLE IF NOT EXISTS edomiProject.editLogicElementDefVar (
		targetid BIGINT UNSIGNED DEFAULT NULL,
		id BIGINT UNSIGNED DEFAULT NULL,
		value VARCHAR(10000) DEFAULT NULL,
		remanent TINYINT UNSIGNED DEFAULT 0,
		KEY (targetid),
		KEY (id)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1
	");
    if (!$r) {
        return false;
    }
    $r = sql_call("CREATE TABLE IF NOT EXISTS edomiProject.editLogicPage (
		id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
		folderid BIGINT UNSIGNED DEFAULT NULL,
		pagestatus TINYINT UNSIGNED DEFAULT 0,
		name VARCHAR(200) DEFAULT NULL,
		text VARCHAR(10000) DEFAULT NULL,
		PRIMARY KEY (id),
		KEY (folderid),
		KEY (name)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1
	");
    if (!$r) {
        return false;
    }
    $r = sql_call("CREATE TABLE IF NOT EXISTS edomiProject.editLogicCmdList (
		id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
		targetid BIGINT UNSIGNED DEFAULT 0,
		cmd TINYINT UNSIGNED DEFAULT NULL,
		cmdid1 BIGINT UNSIGNED DEFAULT NULL,
		cmdid2 BIGINT UNSIGNED DEFAULT NULL,
		cmdoption1 INT SIGNED DEFAULT 0,
		cmdoption2 INT SIGNED DEFAULT 0,
		cmdvalue1 VARCHAR(10000) DEFAULT NULL,
		cmdvalue2 VARCHAR(10000) DEFAULT NULL,
		PRIMARY KEY (id),
		KEY (targetid),
		KEY (cmd),
		KEY (cmdid1),
		KEY (cmdid2),
		KEY (cmdvalue1),
		KEY (cmdvalue2)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1
	");
    if (!$r) {
        return false;
    }
    $r = sql_call("CREATE TABLE IF NOT EXISTS edomiProject.editLogicLink (
		id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
		elementid BIGINT UNSIGNED NOT NULL,
		eingang SMALLINT UNSIGNED NOT NULL,
		linktyp TINYINT UNSIGNED DEFAULT NULL,
		linkid BIGINT UNSIGNED DEFAULT NULL,
		ausgang SMALLINT UNSIGNED DEFAULT NULL,
		refresh TINYINT UNSIGNED NOT NULL DEFAULT 0,
		value VARCHAR(10000) DEFAULT NULL,
		PRIMARY KEY (id),
		KEY (elementid),
		KEY (linktyp),
		KEY (linkid),
		KEY (refresh)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1
	");
    if (!$r) {
        return false;
    }
    $r = sql_call("CREATE TABLE IF NOT EXISTS edomiProject.editLogicElement (
		id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
		functionid BIGINT UNSIGNED NOT NULL,
		pageid BIGINT UNSIGNED DEFAULT NULL,
		xpos INT UNSIGNED DEFAULT NULL,
		ypos INT UNSIGNED DEFAULT NULL,
		name VARCHAR(10000) DEFAULT NULL,
		layer INT UNSIGNED DEFAULT 0,
		PRIMARY KEY (id),
		KEY (functionid)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1
	");
    if (!$r) {
        return false;
    }
    $r = sql_call("CREATE TABLE IF NOT EXISTS edomiProject.editLogicElementVar (
		elementid BIGINT UNSIGNED NOT NULL,
		varid SMALLINT UNSIGNED NOT NULL,
		value VARCHAR(10000) DEFAULT NULL,
		remanent TINYINT UNSIGNED DEFAULT 0,
		KEY (elementid),
		KEY (varid),
		KEY (remanent)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1
	");
    if (!$r) {
        return false;
    }
    $r = sql_call("INSERT INTO edomiProject.editRoot (id,allow,path,rootid,parentid,name,namedb,link,linkid,sortcolumns,sortid) VALUES
		(10,	0,		'/', 		10,0, 		'Logik', NULL, 0, NULL,NULL,0),
		(11,	94,		'/10/',		11,10, 		'Logikseiten', 'editLogicPage', 0, NULL,'id/ID,name/Name',1),
		(12,	8,		'/10/',		12,10, 		'Logikbausteine', 'editLogicElementDef', 0, NULL,'id/ID,name/Name',1),
		(13,	8,		'/10/12/', 	13,12, 		'Grundfunktionen & Auslöser (13)', 'editLogicElementDef', 0, NULL,NULL,0),
		(14,	8, 		'/10/12/', 	14,12, 		'Gatter & Logik (14)', 'editLogicElementDef', 0, NULL,NULL,0),
		(15,	8, 		'/10/12/', 	15,12, 		'Mathematik, Vergleicher & Filter (15)', 'editLogicElementDef', 0, NULL,NULL,0),
		(16,	8,		'/10/12/', 	16,12, 		'Timer & Zeitfunktionen (16)', 'editLogicElementDef', 0, NULL,NULL,0),
		(17,	8,		'/10/12/', 	17,12, 		'Experimentell (17)', 'editLogicElementDef', 0, NULL,NULL,0),
		(18,	8,		'/10/12/', 	18,12, 		'Sonstige (18)', 'editLogicElementDef', 0, NULL,NULL,0),
		(19,	94,		'/10/12/', 	19,12,	 	'Eigene Logikbausteine (19)', 'editLogicElementDef', 0, NULL,NULL,0),
		(20,	0,		'/', 		20,0, 		'Visualisierung', NULL, 0, NULL,NULL,0),
		(21,	106,	'/20/', 	21,20, 		'Visualisierungen', 'editVisu', 0, NULL,'id/ID,name/Name',1),
		(22,	127,	'/20/', 	22,20, 		'Visuseiten', 'editVisuPage', 0, NULL,'id/ID,name/Name',1),
		(23,	126,	'/20/', 	23,20, 		'Visuaccounts', 'editVisuUser', 0, NULL,'id/ID,login/Login',1),
		(24,	126,	'/20/', 	24,20, 		'Designvorlagen', 'editVisuElementDesignDef', 0, NULL,'id/ID,name/Name',1),
		(25,	126,	'/20/', 	25,20, 		'Farben (Hintergrund)', 'editVisuBGcol', 0, NULL,'id/ID,name/Name',1),
		(26,	126,	'/20/', 	26,20, 		'Farben (Vordergrund)', 'editVisuFGcol', 0, NULL,'id/ID,name/Name',1),
		(27,	126,	'/20/', 	27,20, 		'Animationen', 'editVisuAnim', 0, NULL,'id/ID,name/Name',1),
		(28,	94,		'/20/', 	28,20, 		'Bilder', 'editVisuImg', 0, NULL,'id/ID,name/Name',1),
		(29,	94,		'/20/', 	29,20, 		'Töne', 'editVisuSnd', 0, NULL,'id/ID,name/Name',1),
		(30,	0,		'/', 		30,0, 		'Kommunikationsobjekte', 'editKo', 0, NULL,NULL,0),
		(31,	126,	'/30/', 	31,30, 		'Interne KOs', 'editKo', 0, NULL,'id/ID,name/Name',1),
		(32,	94,		'/30/', 	32,30, 		'KNX-Gruppenadressen', 'editKo', 0, NULL,'id/ID,name/Name,ga/GA',1),
		(33,	8,		'/30/', 	33,30, 		'System KOs', 'editKo', 0, NULL,'id/ID,name/Name',1),
		(40,	126,	'/', 		40,0, 		'Szenen', 'editScene', 0, NULL,'id/ID,name/Name',1),
		(50,	126,	'/', 		50,0, 		'Datenarchive', 'editArchivKo', 0, NULL,'id/ID,name/Name',1),
		(60,	126,	'/', 		60,0,		'Meldungsarchive', 'editArchivMsg', 0, NULL,'id/ID,name/Name',1),
		(70,	126,	'/', 		70,0, 		'HTTP/UDP/SHELL', 'editIp', 0, NULL,'id/ID,name/Name',1),
		(75,	126,	'/', 		75,0,		'IR-Befehle', 'editIr', 0, NULL,'id/ID,name/Name',1),
		(80,	0,		'/', 		80,0, 		'Kameras', NULL, 0, NULL,NULL,0),
		(81,	126,	'/80/', 	81,80,		'Kameraeinstellungen', 'editCam', 0, NULL,'id/ID,name/Name',1),
		(82,	126,	'/80/', 	82,80,		'Kameraarchive', 'editArchivCam', 0, NULL,'id/ID,name/Name',1),
		(83,	126,	'/80/', 	83,80,		'Kameraansichten', 'editCamView', 0, NULL,'id/ID,name/Name',1),
		(90,	126,	'/', 		90,0,		'Sequenzen', 'editSequence', 0, NULL,'id/ID,name/Name',1),
		(95,	126,	'/', 		95,0,		'Makros', 'editMacro', 0, NULL,'id/ID,name/Name',1),
		(100,	126,	'/', 		100,0,		'Zeitschaltuhren', 'editTimer', 0, NULL,'id/ID,name/Name',1),
		(101,	126,	'/', 		101,0,		'Terminschaltuhren', 'editAgenda', 0, NULL,'id/ID,name/Name',1),
		(110,	126,	'/', 		110,0,		'Anwesenheitssimulationen', 'editAws', 0, NULL,'id/ID,name/Name',1),
		(120,	0,		'/', 		120,0,		'Emails', 'editEmail', 0, NULL,NULL,0),
		(121,	126,	'/120/', 	121,120,	'Eigene Emails', 'editEmail', 0, NULL,'id/ID,name/Name',1),
		(122,	8,		'/120/', 	122,120,	'System-Emails', 'editEmail', 0, NULL,'id/ID,name/Name',1),
		(124,	0,		'/', 		124,0,		'Telefon', NULL, 0, NULL,NULL,0),
		(125,	94,		'/124/', 	125,124,	'Telefonbuch', 'editPhoneBook', 0, NULL,'id/ID,name/Name',1),
		(126,	126,	'/124/', 	126,124,	'Anruftrigger', 'editPhoneCall', 0, NULL,'id/ID,name/Name',1),
		(127,	126,	'/124/', 	127,124,	'Anrufarchive', 'editArchivPhone', 0, NULL,'id/ID,name/Name',1),
		(130,	126,	'/20/', 	130,20,		'Diagramme', 'editChart', 0, NULL,'id/ID,name/Name',1),
		(140,	94,		'/', 		140,0,		'Fernzugriff', 'editHttpKo', 0, NULL,'id/ID,name/Name',1),
		(150,	94,		'/20/', 	150,20,		'Schriftarten', 'editVisuFont', 0, NULL,'id/ID,name/Name',1),
		(155,	126,	'/20/', 	155,20,		'Formatierung (Meldungsarchive)', 'editVisuFormat', 0, NULL,'id/ID,name/Name',1),
		(160,	8,		'/20/', 	160,20,		'Visuelemente', 		'editVisuElementDef', 0, NULL,'id/ID,name/Name',1),
		(161,	8,		'/20/160/', 161,160,	'Allgemein', 			'editVisuElementDef', 0, NULL,NULL,0),
		(162,	8,		'/20/160/', 162,160,	'Eingabe', 				'editVisuElementDef', 0, NULL,NULL,0),
		(163,	8,		'/20/160/', 163,160,	'Archive',			 	'editVisuElementDef', 0, NULL,NULL,0),
		(164,	8,		'/20/160/', 164,160,	'Sonstige', 			'editVisuElementDef', 0, NULL,NULL,0),
		(170,	90,		'/20/160/', 170,160,	'Eigene Visuelemente', 	'editVisuElementDef', 0, NULL,NULL,0)
	");
    if (!$r) {
        return false;
    }
    $r = sql_call("INSERT INTO edomiProject.editKo (id,name,folderid,ga,gatyp,valuetyp,value,defaultvalue,initscan,initsend,requestable,remanent,vmin,vmax,vstep,vlist) VALUES
		(1, 'Version', 33, '1', 2, 0, NULL, NULL, 0, 0, 0, 0, NULL, NULL, NULL, NULL),
		(2, 'Systemstart', 33, '2', 2, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL, NULL),
		(3, 'Server-IP', 33, '3', 2, 0, NULL, NULL, 0, 0, 0, 0, NULL, NULL, NULL, NULL),
		(4, 'Systemdatum', 33, '4', 2, 0, NULL, NULL, 0, 0, 0, 0, NULL, NULL, NULL, NULL),
		(5, 'Systemzeit', 33, '5', 2, 0, NULL, NULL, 0, 0, 0, 0, NULL, NULL, NULL, NULL),
		(6, 'GA/KO-Monitor', 33, '6', 2, 0, NULL, 3, 0, 0, 0, 0, NULL, NULL, NULL, NULL),
		(7, 'CPU-Warnung', 33, '7', 2, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL, NULL),
		(8, 'RAM-Warnung', 33, '8', 2, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL, NULL),
		(9, 'HDD-Warnung', 33, '9', 2, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL, NULL),
		(10, 'ERROR-Warnung', 33, '10', 2, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL, NULL),
		(11, 'Zeitumstellung', 33, '11', 2, 0, NULL, NULL, 0, 0, 0, 1, NULL, NULL, NULL, NULL),
		(12, 'EDOMI-Update', 33, '12', 2, 0, NULL, NULL, 0, 0, 0, 0, NULL, NULL, NULL, NULL),
		(13, 'unerwarteter Neustart', 33, '13', 2, 0, NULL, NULL, 0, 0, 0, 0, NULL, NULL, NULL, NULL),
		(15, 'Anrufmonitor: Rohdaten', 33, '15', 2, 0, NULL, NULL, 0, 0, 0, 0, NULL, NULL, NULL, NULL),
		(16, 'Kamerafehler', 33, '16', 2, 0, NULL, NULL, 0, 0, 0, 0, NULL, NULL, NULL, NULL),
		(17, 'Trigger: Jährlich 00:00:00', 33, '17', 2, 0, NULL, NULL, 0, 0, 0, 0, NULL, NULL, NULL, NULL),
		(18, 'Trigger: Monatlich 00:00:00', 33, '18', 2, 0, NULL, NULL, 0, 0, 0, 0, NULL, NULL, NULL, NULL),
		(19, 'Trigger: Wöchentlich (Montags) 00:00:00', 33, '19', 2, 0, NULL, NULL, 0, 0, 0, 0, NULL, NULL, NULL, NULL),
		(20, 'Trigger: Täglich 00:00:00', 33, '20', 2, 0, NULL, NULL, 0, 0, 0, 0, NULL, NULL, NULL, NULL),
		(21, 'Trigger: Stündlich (00..23):00:00', 33, '21', 2, 0, NULL, NULL, 0, 0, 0, 0, NULL, NULL, NULL, NULL),
		(22, 'Trigger: Halbstündlich --:(00/30):00', 33, '22', 2, 0, NULL, NULL, 0, 0, 0, 0, NULL, NULL, NULL, NULL),
		(23, 'Trigger: Viertelstündlich --:(00/15/30/45):00', 33, '23', 2, 0, NULL, NULL, 0, 0, 0, 0, NULL, NULL, NULL, NULL),
		(24, 'Trigger: Zehnminütlich --:(00/10/20/.../50):00', 33, '24', 2, 0, NULL, NULL, 0, 0, 0, 0, NULL, NULL, NULL, NULL),
		(25, 'Trigger: Fünfminütlich --:(00/05/10/15/../55):00', 33, '25', 2, 0, NULL, NULL, 0, 0, 0, 0, NULL, NULL, NULL, NULL),
		(26, 'Trigger: Minütlich --:(00/01/02/03/../59):00', 33, '26', 2, 0, NULL, NULL, 0, 0, 0, 0, NULL, NULL, NULL, NULL)
	");
    if (!$r) {
        return false;
    }
    $r = sql_call("INSERT INTO edomiProject.editEmail (id,folderid,name,mailaddr,subject,body) VALUES
		(1, 122, 'Warnung: Unerwarteter Reboot', '', 'WARNUNG: EDOMI', 'EDOMI wurde unerwartet rebooted! Die Ursache könnte ein Stromausfall oder ein Absturz gewesen sein. Bitte Log-Dateien überprüfen!'),
		(2, 122, 'Warnung: CPU/RAM/HDD', '', 'WARNUNG: EDOMI', 'Probleme bei CPU-Last, RAM- oder HDD-Kapazität erkannt! Bitte schnellstens überprüfen.')
	");
    if (!$r) {
        return false;
    }
    return true;
}

function init_DB_Live()
{
    sql_call("CREATE DATABASE IF NOT EXISTS edomiLive");
    if (!sql_dbExists('edomiLive')) {
        return false;
    }
    sql_call("DROP TABLE IF EXISTS edomiLive.RAMsysInfo");
    $r = sql_call("CREATE TABLE edomiLive.RAMsysInfo (
		id INT UNSIGNED NOT NULL,
		value INT NOT NULL,
		KEY (id)
		) ENGINE=MEMORY DEFAULT CHARSET=latin1
	");
    if (!$r) {
        return false;
    }
    $r = sql_call("INSERT INTO edomiLive.RAMsysInfo (id,value) VALUES
		(1,0),
		(2,0),
		(3,0),
		(4,0)
	");
    if (!$r) {
        return false;
    }
    sql_call("DROP TABLE IF EXISTS edomiLive.RAMsysProc");
    $r = sql_call("CREATE TABLE edomiLive.RAMsysProc (
		id INT UNSIGNED NOT NULL,
		s0 VARCHAR(20) DEFAULT NULL,
		s1 VARCHAR(20) DEFAULT NULL,
		s2 VARCHAR(20) DEFAULT NULL,
		s3 VARCHAR(20) DEFAULT NULL,
		s4 VARCHAR(20) DEFAULT NULL,
		s5 VARCHAR(20) DEFAULT NULL,
		s6 VARCHAR(20) DEFAULT NULL,
		s7 VARCHAR(20) DEFAULT NULL,
		s8 VARCHAR(20) DEFAULT NULL,
		s9 VARCHAR(20) DEFAULT NULL,
		s10 VARCHAR(20) DEFAULT NULL,
		s11 VARCHAR(20) DEFAULT NULL,
		s12 VARCHAR(20) DEFAULT NULL,
		s13 VARCHAR(20) DEFAULT NULL,
		s14 VARCHAR(20) DEFAULT NULL,
		s15 VARCHAR(20) DEFAULT NULL,
		s16 VARCHAR(20) DEFAULT NULL,
		s17 VARCHAR(20) DEFAULT NULL,
		s18 VARCHAR(20) DEFAULT NULL,
		s19 VARCHAR(20) DEFAULT NULL,
		s20 VARCHAR(20) DEFAULT NULL,
		KEY (id)
		) ENGINE=MEMORY DEFAULT CHARSET=latin1
	");
    if (!$r) {
        return false;
    }
    $r = sql_call("INSERT INTO edomiLive.RAMsysProc (id) VALUES (1),(2),(3),(4),(5),(6),(7),(8),(9)");
    if (!$r) {
        return false;
    }
    sql_call("DROP TABLE IF EXISTS edomiLive.RAMlivemon");
    $r = sql_call("CREATE TABLE edomiLive.RAMlivemon (
			ts VARCHAR(20) DEFAULT NULL,
			datetime datetime DEFAULT NULL,
			ms int(11) DEFAULT NULL,
			pa varchar(11) DEFAULT NULL,
			gamode TINYINT DEFAULT NULL,
			gatyp INT UNSIGNED DEFAULT NULL,
			gaid BIGINT UNSIGNED DEFAULT NULL,
			ga varchar(11) DEFAULT NULL,
			ganame varchar(200) DEFAULT NULL,
			gavalue varchar(200) DEFAULT NULL,
			KEY (ts),
			KEY (datetime)
			) ENGINE=MEMORY DEFAULT CHARSET=latin1");
    if (!$r) {
        return false;
    }
    $tmp = global_koMonMaxCount;
    if ($tmp < 1) {
        $tmp = 1;
    } else if ($tmp > 1000) {
        $tmp = 1000;
    }
    for ($t = 0; $t < $tmp; $t++) {
        $r = sql_call("INSERT INTO edomiLive.RAMlivemon (ts) VALUES ('00000000000000000000')");
    }
    if (!$r) {
        return false;
    }
    sql_call("DROP TABLE IF EXISTS edomiLive.logicExecQueue");
    $r = sql_call("CREATE TABLE edomiLive.logicExecQueue (
			ts VARCHAR(20) DEFAULT NULL,
			elementid BIGINT UNSIGNED DEFAULT NULL,
			inputid SMALLINT UNSIGNED NOT NULL,
			refresh TINYINT UNSIGNED NOT NULL DEFAULT 0,
			value VARCHAR(10000) DEFAULT NULL,
			KEY (ts),
			KEY (elementid),
			KEY (inputid)
			) ENGINE=MyISAM DEFAULT CHARSET=latin1");
    if (!$r) {
        return false;
    }
    return true;
} ?>
