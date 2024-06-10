<?
/* 
*/ 
?><? ?><? require("../shared/php/config.php"); require(MAIN_PATH."/www/shared/php/base.php"); require(MAIN_PATH."/www/shared/php/incl_http.php"); require(MAIN_PATH."/www/visu/include/php/config.php"); require(MAIN_PATH."/www/visu/include/php/base.php"); sql_connect(); $visuId=preg_replace("/[^0-9]/",'',httpGetVar('visu')); if (!is_numeric($visuId) || $visuId<1) {$visuId=0;} $loginSid=loginVisu($visuId,httpGetVar('login'),httpGetVar('pass')); ?>
<!DOCTYPE HTML>
<html>
	<head>
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="format-detection" content="telephone=no">
		<meta id="meta-viewport" name="viewport" content="user-scalable=no, width=device-width, initial-scale=1.0">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">
		<link rel="icon" href="../shared/img/favicon-visu.png?<?echo global_version;?>">
		<link rel="apple-touch-icon" href="../shared/img/favicon-visu.png?<?echo global_version;?>">
		<title>EDOMI &middot; Visualisierung</title>
		<link rel="stylesheet" type="text/css" href="../shared/css/global.css?<?echo global_version;?>">
		<link rel="stylesheet" type="text/css" href="include/css/main.css?<?echo global_version;?>">
		<script type="text/javascript" src="../shared/js/main.js?<?echo global_version;?>"></script>
		<script type="text/javascript" src="../shared/js/camview.js?<?echo global_version;?>"></script>
		<script type="text/javascript" src="../shared/js/camview_global.js?<?echo global_version;?>"></script>
		<script type="text/javascript" src="../shared/js/graphics.js?<?echo global_version;?>"></script>
		<script type="text/javascript" src="include/js/main.js?<?echo global_version;?>"></script>
		<style id="cssAnims"></style>
		<style id="cssFonts"></style>
	</head>
<? if (strpos(strtoupper($_SERVER['HTTP_USER_AGENT']),'WEBKIT')===false) { ?>
	<body style="font-family:<?echo global_visuFont;?>;">
		<div style="position:absolute; overflow:auto; top:0; left:0; bottom:0; right:0; display:inline; background:#343434;">
			<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
				<tr valign="middle" height="100%">
					<td align="center">
						<span style="font-size:20px; color:#fffff9;"><img src="../shared/img/edomi-visu.svg" width="192" height="64" valign="middle" style="margin:0;" draggable="false"><br><br>Webkit-Browser erforderlich</span><br><br>
						<span style="font-size:11px; color:#a9a9a0;">Visualisierungen können nur mit einem Webkit-Browser<br>dargestellt werden, z.B. Apple/Safari oder Google/Chrome.</span>
					</td>
				</tr>
			</table>
		</div>
	</body>
<? } else { ?>
	<body onLoad="firstinit(<?echo $visuId;?>,'<?echo $loginSid;?>','<?echo global_version;?>');" onContextMenu="return false;" style="font-family:<?echo global_visuFont;?>; background:<?echo global_visuBgColor;?>;">

		<!-- Container für die Visu -->
		<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
			<tr valign="top">
				<td align="center">
					<div id="windowContainer" style="position:relative; left:0; top:0; display:none; margin:0; padding:0; background:#808080;"></div>
				</td>
			</tr>
		</table>

		<!-- Logo/Warteanimation/Preloadbalken -->
		<div id="wait" class="appWindow" style="-webkit-animation:none; color:#c0c0c0; background:#343434;">
			<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0" style="table-layout:fixed; ">
				<tr valign="middle" height="60" style="font-size:10px;">
					<td align="center">&nbsp;</td>
				</tr>
				<tr valign="middle">
					<td align="center">
						<div style="position:relative; width:184px; height:184px; border-radius:100%; background:#343434;">
							<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0" style="padding:2px;">
								<tr valign="middle" height="33%">
									<td align="center">&nbsp;</td>
								</tr>
								<tr valign="middle">
									<td align="center"><img src="../shared/img/edomi-visu.svg" width="96" height="32" valign="middle" style="margin:0;" draggable="false"></td>
								</tr>
								<tr valign="top" height="33%">
									<td align="center">
										<div id="preload" style="display:none; width:100px; height:3px; border-radius:3px; background:#606060;"></div>
									</td>
								</tr>
							</table>
							<div id="waitanim" class="connectAnim"></div>
						</div>
					</td>
				</tr>
				<tr valign="middle" height="60" style="font-size:10px; color:#595950;">
					<td align="center"><span onClick="self.location.reload();" style="padding:3px; cursor:pointer;">EDOMI <?echo global_version;?></span> &middot; <span style="padding:3px;">&copy; Long Term Evolution</span></td>
				</tr>
			</table>
		</div>

		<!-- Warnungs-Overlay (CPU/RAM/...) -->
		<div id="warn" style="display:none; position:absolute; text-align:center; overflow:hidden; top:0; left:0; right:0; pointer-events:none; background:transparent; z-index:99999;"></div>

		<!-- Container für Longclick-Indikator -->
		<div id="longclick" class="indicateLongclick"></div>

		<!-- Container für Login-Dialog -->
		<div id="login" class="appWindow" style="display:none; background:url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAUAAAAFCAYAAACNbyblAAAAAXNSR0IArs4c6QAAAChJREFUCB1jZGBgMAZiZODDhMwDsn2AeAuyIFgApAgmCBeACaIIgAQBzpEEiaAV3YQAAAAASUVORK5CYII=');"></div>

		<!-- Warnungs-Overlay (Visuaktivierung) -->
		<div id="preview" onClick="ackWarningPreview();" class="visuPreview">&#9651;<br>VORSCHAU</div>

		<!-- Fehlermeldung (vor dem Start) -->
		<div id="error" style="display:none; position:absolute; text-align:center; overflow:hidden; top:0; left:0; right:0; pointer-events:none; background:transparent; z-index:99999;"></div>
	</body>
<? } ?>
</html>
<? sql_disconnect(); ?>

