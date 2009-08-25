<?php

/**
 * overview.php
 *
 * @version 1.0
 * @copyright 2008 by ??????? for XNova
 */

define('INSIDE'  , true);
define('INSTALL' , false);
define('IN_ADMIN', true);

$ugamela_root_path = '../';
include($ugamela_root_path . 'extension.inc');
include($ugamela_root_path . 'common.' . $phpEx);

if ($user['authlevel'] < 1) {
	AdminMessage ($lang['sys_noalloaw'], $lang['sys_noaccess']);
}

includeLang('admin');

if ($_GET['cmd'] == 'sort') {
	$TypeSort = $_GET['type'];
} else {
	$TypeSort = 'id';
}

$PageTPL  = gettemplate('admin/overview_body');
$RowsTPL  = gettemplate('admin/overview_rows');

$parse                      = $lang;
$parse['dpath']             = $dpath;
$parse['mf']                = $mf;
$parse['adm_ov_data_yourv'] = colorRed(VERSION);

// we get the last version
$raw = @file_get_contents('http://trac.assembla.com/projectxnovaes/browser/trunk/install/version.txt?format=raw');

if (!empty($raw) && strlen($raw) < 10 && version_compare(VERSION, $raw, '<')) {
	// mayor
	$parse['upgrade_tip'] = '<div style="background-color:#bb0000;color:white;cursor:pointer;" onclick="location=\'http://project.xnova.es/viewforum.php?f=6\';">There is a new version! Please UPDATE NOW!</div>';
} else {
	// minor
	$parse['upgrade_tip'] = '';
}

$queryuser="u.id, u.username, u.user_agent, u.user_lastip, u.ally_name, u.onlinetime";
$querystat="s.total_points,s.id_owner,s.stat_type";

$Last15Mins = doquery("SELECT ". $queryuser .", ". $querystat ." FROM  {{table}}users as u, {{table}}statpoints as s
WHERE u.onlinetime >= '". (time() - 15 * 60) ."' AND u.id=s.id_owner AND s.stat_type=1 ORDER BY `". mysql_escape_string($TypeSort) ."` ASC;", '');

$Count      = 0;
$Color      = "lime";

while ($TheUser = mysql_fetch_array($Last15Mins)) {
	
	if ($PrevIP != "") {
		if ($PrevIP == $TheUser['user_lastip']) {
			$Color = "red";
		} else {
			$Color = "lime";
		}
	}

	
   	$Bloc['dpath']               = $dpath;
	$Bloc['adm_ov_altpm']        = $lang['adm_ov_altpm'];
   	$Bloc['adm_ov_wrtpm']        = $lang['adm_ov_wrtpm'];
   	$Bloc['adm_ov_data_id']      = $TheUser['id'];
   	$Bloc['adm_ov_data_name']    = $TheUser['username'];
   	$Bloc['adm_ov_data_agen']    = $TheUser['user_agent'];
   	$Bloc['adm_ov_data_clip']    = $Color;
   	$Bloc['adm_ov_data_adip']    = $TheUser['user_lastip'];
   	$Bloc['adm_ov_data_ally']    = $TheUser['ally_name'];
   	$Bloc['adm_ov_data_point']   = pretty_number($TheUser['total_points']);
   	$Bloc['adm_ov_data_activ']   = pretty_time(time() - $TheUser['onlinetime']);
   	$Bloc['adm_ov_data_pict']    = 'm.gif';
   	$PrevIP                      = $TheUser['user_lastip'];

	$parse['adm_ov_data_table'] .= parsetemplate($RowsTPL, $Bloc);
	$Count++;
}

$parse['adm_ov_data_count']  = $Count;
$Page = parsetemplate($PageTPL, $parse);

display($Page, $lang['sys_overview'], false, '', true);

?>