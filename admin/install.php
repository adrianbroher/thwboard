<?php
/*
          phpInstaller - PHP Script Installer
        ==============================================
          (c) 2000-2004 by ThWboard Development Group


          This  program is  free  software;  you can
          redistribute it and/or modify it under the
          terms of the GNU General Public License as
          published by the Free Software Foundation;
          either  version 2 of  the License,  or (at
          your option) any later version.

        ==============================================
*/
function check_php_version ($version)
{
    $testSplit = explode ('.', $version);
    $currentSplit = explode ('.', phpversion ());

    if( $testSplit[0] < $currentSplit[0] )
    {
        return true;
    }
    if( $testSplit[0] == $currentSplit[0] )
    {
        if( $testSplit[1] < $currentSplit[1] )
        {
            return true;
        }
        if( $testSplit[1] == $currentSplit[1] )
        {
            if( $testSplit[2] <= $currentSplit[2] )
            {
                return true;
            }
        }
    }

    return false;
}

if( !check_php_version('4.0.6') )
{
    die('ThWboard install can not be performed because the PHP version used is to old ( < 4.0.6 ).<br>Please ask your webmaster or server administrator for updating the PHP version.');
}

require('install_functions.php');

if( !isset($action) )
{
    $action = '';
}

if( !install_allowed() && $action != 'about' && $action != '' )
{
    $action = 'deny';
}

switch( $action )
{
    case 'generate_config':
        header('Content-Type: application/octetstream');
        header('Content-Disposition: filename="config.inc.php"');
        header('Pragma: no-cache');
        header('Expires: 0');

        p_configuration(STDOUT, [
            'database-hostname' => $HTTP_POST_VARS['hostname'],
            'database-username' => $HTTP_POST_VARS['user'],
            'database-password' => $HTTP_POST_VARS['pass'],
            'database-name' => $HTTP_POST_VARS['db'],
            'table-prefix' => $HTTP_POST_VARS['prefix']
        ]);
        break;

    case 'createadmin':
        if( strlen($HTTP_POST_VARS['admin_pass']) < 5 )
        {
            p_errormsg(lng('error'), lng('adminpwtooshort'));
        }

        mysql_connect($HTTP_POST_VARS['hostname'], $HTTP_POST_VARS['user'], $HTTP_POST_VARS['pass']);
        mysql_select_db($HTTP_POST_VARS['db']);

        thwb_query("INSERT INTO $HTTP_POST_VARS[prefix]"."user (username, useremail, userpassword, userisadmin, userjoin, groupids, usernodelete) VALUES
            ('$admin_user', '$admin_email', '".md5($HTTP_POST_VARS['admin_pass'])."', '1', ".time().", ',3,', '1')");

        p_header();
        p_prewrite($HTTP_POST_VARS['hostname'], $HTTP_POST_VARS['user'], $HTTP_POST_VARS['pass'], $HTTP_POST_VARS['db'], $HTTP_POST_VARS['prefix']);
        p_footer('writeconfig', array(
            'hostname' => $HTTP_POST_VARS['hostname'],
            'user' => $HTTP_POST_VARS['user'],
            'pass' => $HTTP_POST_VARS['pass'],
            'db' => $HTTP_POST_VARS['db'],
            'prefix' => $HTTP_POST_VARS['prefix']
        ));
        break;

    case 'createtables':
        mysql_connect($HTTP_POST_VARS['hostname'], $HTTP_POST_VARS['user'], $HTTP_POST_VARS['pass']);
        mysql_select_db($HTTP_POST_VARS['db']);

        create_tables($delete_existing);

        p_header();
        p_adminprofile();
        p_footer('createadmin', array(
            'hostname' => $HTTP_POST_VARS['hostname'],
            'user' => $HTTP_POST_VARS['user'],
            'pass' => $HTTP_POST_VARS['pass'],
            'db' => $HTTP_POST_VARS['db'],
            'prefix' => $HTTP_POST_VARS['prefix']
        ));
        break;

    case 'writeconfig':
        if( !WriteAccess('../inc/config.inc.php') )
        {
            p_errormsg(lng('error'), lng('chmoderror'));
        }
        else
        {
            $fp = @fopen('../inc/config.inc.php', 'w');
            p_configuration($fp, [
                'database-hostname' => $HTTP_POST_VARS['hostname'],
                'database-username' => $HTTP_POST_VARS['user'],
                'database-password' => $HTTP_POST_VARS['pass'],
                'database-name' => $HTTP_POST_VARS['db'],
                'table-prefix' => $HTTP_POST_VARS['prefix']
            ]);
            fclose($fp);

            p_header();
            p_done();
            p_footer();
        }
        break;

    case 'setprefix':
        mysql_connect($HTTP_POST_VARS['hostname'], $HTTP_POST_VARS['user'], $HTTP_POST_VARS['pass']);

        $db = '';
        if( $HTTP_POST_VARS['name_db'] && $HTTP_POST_VARS['selected_db'] == '_usefield' )
        {
            $db = $HTTP_POST_VARS['name_db'];
        }
        else
        {
            $db = $HTTP_POST_VARS['selected_db'];
        }

        if( !db_exists($db) )
        {
          thwb_query("CREATE DATABASE ".$db);
          if( !db_exists($db) )
            {
              p_errormsg(lng('error'), sprintf(lng('mysqlerror'), $db, mysql_error()));
            }
        }

        mysql_select_db($db);

        $r_table = mysql_list_tables($db);
        $a_tables = array();
        $i = 0;
        while( $i < mysql_num_rows($r_table) )
        {
            $tables[] = mysql_tablename($r_table, $i);
            $i++;
        }

        p_header();
        p_chooseprefix($db, $tables);
        p_footer('createtables', array(
            'hostname' => $HTTP_POST_VARS['hostname'],
            'user' => $HTTP_POST_VARS['user'],
            'pass' => $HTTP_POST_VARS['pass'],
            'db' => $db
        ));
        break;

    case 'selectdb':
        $dbhandle = @mysql_connect($HTTP_POST_VARS['hostname'], $HTTP_POST_VARS['user'], $HTTP_POST_VARS['pass']);
        if( !$dbhandle )
        {
            p_errormsg(lng('error'), sprintf(lng('connecterror'), mysql_error()));
        }

        $r_database = mysql_listdbs();

        $databases = '';
        $i = 0;
        while( $i < mysql_num_rows($r_database) )
        {
            $databases .= '<option value="'.mysql_tablename($r_database, $i).'">'.lng('existingdb').': '.mysql_tablename($r_database, $i).'</option>';
            $i++;
        }

        p_header();
        p_selectdb($databases);
        p_footer('setprefix', array(
            'hostname' => $HTTP_POST_VARS['hostname'],
            'user' => $HTTP_POST_VARS['user'],
            'pass' => $HTTP_POST_VARS['pass']
        ));
        break;

    case 'mysqldata':
        if( $HTTP_POST_VARS['accept'] != 'yes' )
        {
            p_errormsg(lng('error'), lng('licaccept'));
        }
        else
        {
            p_header();
            p_mysqldata();
            p_footer('selectdb');
        }
        break;

    case 'license':
        p_header();
        p_license();
        p_footer('mysqldata');
        break;

    case 'about':
        p_header();
        p_about();
        p_footer();
        break;

    case 'deny':
        p_header();
        p_deny_install();
        p_footer();
        break;

    case 'welcome':
        p_header();
        p_welcome();
        p_footer('license');
        break;

    default:
        p_header();
        p_selectlang();
        p_footer('welcome');

}
