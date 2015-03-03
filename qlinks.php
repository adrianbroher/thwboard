<?php
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
// quicklinks by Morpheus

if (!@include('./inc/config.inc.php')) {
    print '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
    ';
    print 'Das Forum ist noch nicht eingerichtet! Bitte <a href="./admin/install.php">installieren</a> Sie zuerst die Forensoftware um das Forum nutzen zu k&ouml;nnen.';
    exit;
}

$mysql = @mysql_connect($mysql_h, $mysql_u, $mysql_p);
$db = @mysql_select_db($mysql_db);

$mysql_h = ''; $mysql_u = ''; $mysql_p = ''; $mysql_db = '';

if( !$mysql || !$db )
{
    print '<b>Sorry</b><br><br>Es gibt momentan leider ein kleines Datenbank-Problem, bitte versuche es sp&#xE4;ter noch einmal.';
    exit;
}

$id = ((isset($_REQUEST['id'])) ? intval($_REQUEST['id']) : 0);

if( $id ) {
    $r_qlink = mysql_query( "SELECT
            linkid,
            linkhttp
        FROM
            $pref"."qlink
        WHERE linkid = '$id'"
    );

    if( mysql_num_rows( $r_qlink ) == 1 ) {
        $a_qlink = mysql_fetch_array( $r_qlink );

        $r_qlink = mysql_query( "UPDATE
                $pref"."qlink
            SET
                linkcounter = linkcounter+1
            WHERE
                linkid = '$id'"
        );
        header( 'Location: '. $a_qlink['linkhttp'] );
        exit;
    }
}

?>

<html>
<head>
    <title>Quicklink nicht gefunden</title>
</head>

<body>
<h2>Quicklink nicht gefunden</h2>
<p>Der angeforderte Quicklink mit der ID <b><?php echo $id; ?></b> wurde nicht gefunden.
Wenn Sie die URL nicht per Hand ver&auml;ndert haben, benachrichtigen Sie bitte den
Administrator des Forums &uuml;ber diesen Fehler.</p>
</body>

</html>
