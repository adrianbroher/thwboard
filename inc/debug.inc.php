<?php
/* $Id: debug.inc.php 87 2004-11-07 00:19:15Z td $ */
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

/**
 * thwb debugging package.
 * 
 * extended error reporting (including backtraces)
 */

define('THWB_ERR_NONE', 0);     //!< no error
define('THWB_ERR_PHP', 1 << 0); //!< PHP error
define('THWB_ERR_SQL', 1 << 1); //!< SQL error

/**
 * returns a human-readable description for the given error type.
 * 
 * @param $err  error type.
 * @return string containing the description-
 **/

function thwb_error_type_to_string($err)
{
    switch ($err) 
    {
    case THWB_ERR_PHP:
        return 'PHP';
    case THWB_ERR_SQL:
        return 'SQL';
    default:
        return 'none';
    }
}

global $_thwb_error_cfg; //!< global error reporting config

//! default settings

$_thwb_error_cfg = array
    (
        'sql' => 0,
        'php' => 0,
        'log' => '',
        'mail' => '',
        'date' => 0
    );

/**
 * php error handler
 * 
 * @param $errno    error number
 * @param $errmsg   error message
 * @param $filename errornous file
 * @param $linenum  line number
 * @param $vars     error context stack variables dump
 **/

function thwb_php_error_handler($errno, $errmsg, $filename, $linenum, $vars)
{
    $msg = 'PHP Error: `'.$errmsg.'\' (errno '.$errno.")\n"
        .'in file: `'.$filename.'\' (line ' .$linenum.")\n";
        
    
    if(function_exists('debug_backtrace'))
    {
        $bt = debug_backtrace();
        $msg .= "Backtrace: \n".print_r($bt, true)."\n";
    }

    thwb_handled_error($msg, THWB_ERR_PHP);
}

/**
 * sql error handler
 * 
 * @param $query    sql query
 * @param $bt       backtrace
 **/

function thwb_sql_error_handler($query, $bt)
{
    $msg = 'SQL Error: `'.mysql_error()."'\n"
        .'in query: `'.$query."'\n";

    if(count($bt))
    {
        $msg .= "Backtrace: \n".print_r($bt, true)."\n";
    }

    thwb_handled_error($msg, THWB_ERR_SQL);
}

/**
 * handles the error message.
 * 
 * prints, logs and/or mails error (depending on config).
 * 
 * @param $msg  error message
 * @param $type error type
 **/

function thwb_handled_error($msg, $type)
{
    global $HTTP_SERVER_VARS, $_thwb_error_cfg, $g_user, $config;

    static $a_server_vars = array(
        'Script' => 'SCRIPT_FILENAME',
        'Query String' => 'QUERY_STRING',
        'Referer' => 'HTTP_REFERER',
        'Browser' => 'HTTP_USER_AGENT'
        );

    $head = 'ThWB Error: '.thwb_error_type_to_string($type)."\n"
        .'Date: '.date('Y-m-d H:i:s')."\n"
        .'User: `'.$g_user['username'].'\' ('.$g_user['userid'].")\n";
                                                                      
    foreach($a_server_vars as $k => $v)
    {
        if(!empty($HTTP_SERVER_VARS[$v]))
        {
            $head .= $k.': `'.$HTTP_SERVER_VARS[$v]."'\n";
        }
        else
        {
            $head .= "$k is empty.\n";
        }
    }

    if(!empty($g_user['userisadmin']) && $g_user['userisadmin'])
    {
        print '<pre>'.htmlentities($head.$msg."\n").'</pre>';
    }
    
    if(!empty($_thwb_error_cfg['mail']) && !empty($config['use_email']))
    {
        @mail($_thwb_error_cfg['mail'], 'ThWB Error: '.thwb_error_type_to_string($type), ($head.$msg), "From: $config[board_admin]");
    }

    if(!empty($_thwb_error_cfg['log']))
    {
        $h = fopen($_thwb_error_cfg['log'].'_'.strtolower(thwb_error_type_to_string($type)).'.log', 'a');
        fwrite($h, $head.$msg);
        fclose($h);
    }
}

/**
 * $what    array, contains none, one or both of THWB_ERR_PHP and THWB_ERR_SQL.
 * $mail    if non-zero, the error is mailed to that address.
 **/

function thwb_setup_error_handling($what, $mail, $log)
{
    global $_thwb_error_cfg;

    $_thwb_error_cfg['sql'] = ($what & THWB_ERR_SQL);
    $_thwb_error_cfg['php'] = ($what & THWB_ERR_PHP);
    $_thwb_error_cfg['mail'] = $mail;
    $_thwb_error_cfg['log'] = $log;

    if($_thwb_error_cfg['php'])
    {
        set_error_handler('thwb_php_error_handler');
    }
}

?>
