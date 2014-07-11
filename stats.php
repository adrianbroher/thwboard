<?php
/* $Id: stats.php 87 2004-11-07 00:19:15Z td $ */
/*
          ThWboard - PHP/MySQL Bulletin Board System
        ==============================================
          (c) 2000-2004 by ThWboard Development Group



          download the latest version:
            http://www.thwboard.de

          This  program is  free  software;  you can
          redistribute it and/or modify it under the
          terms of the GNU General Public License as
          published by the Free Software Foundation;
          either  version 2 of  the License,  or (at
          your option) any later version.

        ==============================================

*/

define('THWB_NOSESSION_PAGE', true);

include('./inc/header.inc.php');
//include('./inc/statscode/chkgd.php');

// ******************************************************
//  Create available stats
// ******************************************************

$selectionpoints = array();
$dir = opendir("./inc/statscode");
$get_part = 'head';
$stats = '';

while (($subdir = readdir($dir)) !== false)
{
	if(!preg_match('/\.stats\.php$/i', $subdir))
		continue;
	
	include('./inc/statscode/' . $subdir);
}
closedir($dir);
$get_part = 'body';

// ******************************************************
//  Create stats config
// ******************************************************

$stats_config = array();
/*$stats_config['gdversion'] = CheckGDVersion();
if( $config['gdstats'] && $stats_config['gdversion'][0] )
{
	$stats_config['gd_enabled'] = true;
	switch( $stats_config['gdversion'][0] )
	{
		case 1:
			$stats_config['gd2'] = false;
			$stats_config['gd_ttf'] = true;
			break;
		case 2:
			$stats_config['gd2'] = true;
			$stats_config['gd_ttf'] = false;
			break;
	}
}
else
{
	$stats_config['gd_enabled'] = false;
}
if( !$config['gdstatsttf'] )
{
	$stats_config['gd_ttf'] = false;
}
*/
if( !isset($detail) || !isset($selectionpoints[$detail]) )
{
	$stats_config['detailpage'] = 'default';
}
else
{
	$stats_config['detailpage'] = $detail;
}

unset($detail);

// ******************************************************
//  Create navpath and pagetitle
// ******************************************************

$navpath .= 'Statistik (' . $selectionpoints[$stats_config['detailpage']]['title'] . ')';
$titleprepend = 'Statistik (' . $selectionpoints[$stats_config['detailpage']]['title'] . ') - ' . $titleprepend;

// ******************************************************
//  Create selection form
// ******************************************************

if( count($selectionpoints) < 1 )
{
	$selform = $style['stdfont'] . '<font color="' . $style['colorbgfont'] . '"><b>Keine Statistiken installiert!</b></font>' . $style['stdfontend'];
}
else
{
	$selform = '<form action="'.build_link("stats.php").'" method="post" name="statsselform"><select name="detail" size="1" class="tbselect" onChange="statsselform.submit()">';
	foreach( $selectionpoints as $singlepoint )
	{
		$selform .= '<option ' . ( $stats_config['detailpage'] == $singlepoint['base'] ? 'selected="selected"' : '' ) . ' value="' . $singlepoint['base'] . '">' . $singlepoint['title'] . '</option>';
	}
	$selform .= '</select> <input type="submit" value="Go" class="tbbutton">';
}

// ******************************************************
//  Initialize templates
// ******************************************************

$t_frame = new Template('./templates/' . $style['styletemplate'] . '/frame.html');
$t_stats_main = new Template('./templates/' . $style['styletemplate'] . '/stats_main.html');

$stats = '';
if( count($selectionpoints) >= 1 )
{
	include('./inc/statscode/' . $stats_config['detailpage'] . '.stats.php');
}

// ******************************************************
//  Evaluate templates
// ******************************************************

$CONTENT = '';
eval($t_stats_main->GetTemplate("CONTENT"));
eval($t_frame->GetTemplate());
?>