<?php
/*
          ThWboard - PHP/MySQL Bulletin Board System
        ==============================================
            (c) 2000-2004-2002 by



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

require('./inc/header.inc.php');

$a_pages = array(
         'index' => 'H&auml;ufig gestellte Fragen',
         'format' => 'Formatierung und Smilies',
          'page1' => 'Benutzerfunktionen',
         'page2' => 'Generelle Boardbenutzung',
         'page3' => 'Lesen und Schreiben von Nachrichten',
         );

if ( !isset($page) || !file_exists('./templates/'.$style['styletemplate'].'/faq_'.$page.'.html') || !isset($a_pages[$page]))
{
    $page = "index";
}

$Tframe = new Template('./templates/'.$style['styletemplate'].'/frame.html');
$Tfaq = new Template('templates/'.$style['styletemplate'].'/faq_'.$page.'.html');

$navpath .= '<a class="bglink" href="'.build_link("help.php").'">FAQ</a> &raquo; '.$a_pages[$page];

eval($Tfaq->GetTemplate("CONTENT"));
eval($Tframe->GetTemplate());
