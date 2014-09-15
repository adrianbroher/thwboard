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

include "common.inc.php";

$style_file_version = '1.0';

if( $action == 'ExportStyle' )
{
    $r_style = query("SELECT * FROM ".$pref."style WHERE styleid=$styleid");
    $style = mysql_fetch_array($r_style);

    $filename = strtolower($style['stylename']);
    for( $i = 0; $i < strlen($filename); $i++ )
    {
        if( !ereg('([a-z0-9])', $filename[$i]) )
        {
            $filename[$i] = '_';
        }         
    }    
    
    header('Content-Type: application/octetstream');
    header('Content-Disposition: filename="'.$filename.'.style"');
    header('Pragma: no-cache');
    header('Expires: 0');

    echo 'styleversion='.$style_file_version."\n";
    while( list($k, $v) = each($style) )
    {
        if( $k != 'styleid' && $k != 'styleispublic' && $k != 'styleisdefault' && gettype($k) != 'integer')
        {
            echo $k.'='.$v."\n";
        }
    }
    exit;
}


tb_header();

/*
 * =======================================
 *                <functions>
 * =======================================
 */
function EditboxEncode($string)
{
    $string = str_replace('&', '&amp;', $string);
    $string = str_replace('"', '&quot;', $string);
    $string = str_replace('<', '&lt;', $string);
    $string = str_replace('>', '&gt;', $string);
    
    return $string;
}

function EditboxDecode($string)
{
    $string = str_replace('&amp;', '&', $string);
    $string = str_replace('&quot;', '"', $string);
    $string = str_replace('&lt;', '<', $string);
    $string = str_replace('&gt;', '>', $string);

    return $string;
}

function StyleForm($action, $style)
{
    global $session;
    
    print '<form method="post" action="style.php">
  <table width="100%" border="0" cellspacing="1" cellpadding="3">
    <tr> 
      <td>Stylename</td>
      <td width="1">&nbsp; </td>
      <td> 
        <input class="tbinput" type="text" name="style[stylename]" value="' . htmlspecialchars($style[stylename]) . '">
      </td>
    </tr>
    <tr>
      <td colspan="3">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="3"><b>Layout</b><br>
        <font size="1">The general layout of this style.</font></td>
    </tr>
    <tr> 
      <td>Template set</td>
      <td width="1">
        &nbsp; 
      </td>
      <td> 
        <select class="tbinput" name="style[styletemplate]" size="1">';
        
        $a_templateset = get_templatesetarray();
        while( list(, $file) = each($a_templateset) )
        {
            print '<option value="'.$file.'"'.($file == $style['styletemplate'] ? ' selected' : '').'>'.$file.'</option>';
        }
    
    print'
        </select>
      </td>
    </tr>
    <tr>
      <td colspan="3">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="3"><b>Main (Background)</b><br>
        <font size="1">This applies to the background of your forum, i.e. the copyright notice and some notes above and under tables.</font></td>
    </tr>
    <tr> 
      <td>Background color</td>
      <td width="1"> 
        <table width="64" border="0" cellspacing="1" cellpadding="1" bgcolor="#000000">
          <tr> 
            <td bgcolor="' . $style[colorbg] . '">&nbsp;</td>
          </tr>
        </table>
      </td>
      <td> 
        <input class="tbinput" type="text" name="style[colorbg]" size="9" maxlength="7" value="' . $style[colorbg] . '">
      </td>
    </tr>
    <tr> 
      <td>Background text color</td>
      <td width="1"> 
        <table width="64" border="0" cellspacing="1" cellpadding="1" bgcolor="#000000">
          <tr> 
            <td bgcolor="' . $style[colorbgfont] . '">&nbsp;</td>
          </tr>
        </table>
      </td>
      <td> 
        <input class="tbinput" type="text" name="style[colorbgfont]" size="9" maxlength="7" value="' . $style[colorbgfont] . '">
      </td>
    </tr>
    <tr>
      <td colspan="3">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="3"><b>Table Headers / Footers</b><br>
        <font size="1">These settings are used for the &quot;header&quot; and &quot;footer&quot; rows in tables.</font></td>
    </tr>
    <tr>
      <td>Background color</td>
      <td width="1">
        <table width="64" border="0" cellspacing="1" cellpadding="1" bgcolor="#000000">
          <tr> 
            <td bgcolor="' . $style[color4] . '">&nbsp;</td>
          </tr>
        </table>
      </td>
      <td>
        <input class="tbinput" type="text" name="style[color4]" size="9" maxlength="7" value="' . $style[color4] . '">
      </td>
    </tr>
    <tr>
      <td>Text color</td>
      <td width="1">
        <table width="64" border="0" cellspacing="1" cellpadding="1" bgcolor="#000000">
          <tr> 
            <td bgcolor="' . $style[col_he_fo_font] . '">&nbsp;</td>
          </tr>
        </table>
      </td>
      <td>
        <input class="tbinput" type="text" name="style[col_he_fo_font]" size="9" maxlength="7" value="' . $style[col_he_fo_font] . '">
      </td>
    </tr>
    <tr>
      <td colspan="3">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="3"><b>Tables</b><br>
        <font size="1">The main part of your forum, all tables. i.e. threads, posts, private messages, ...</font></td>
    </tr>
    <tr> 
      <td>Text color</td>
      <td width="1"> 
        <table width="64" border="0" cellspacing="1" cellpadding="1" bgcolor="#000000">
          <tr> 
            <td bgcolor="' . $style[color1] . '">&nbsp;</td>
          </tr>
        </table>
      </td>
      <td> 
        <input class="tbinput" type="text" name="style[color1]" size="9" maxlength="7" value="' . $style[color1] . '">
      </td>
    </tr>
    <tr> 
      <td>Cell background color</td>
      <td width="1"> 
        <table width="64" border="0" cellspacing="1" cellpadding="1" bgcolor="#000000">
          <tr> 
            <td bgcolor="' . $style[CellA] . '">&nbsp;</td>
          </tr>
        </table>
      </td>
      <td> 
        <input class="tbinput" type="text" name="style[CellA]" size="9" maxlength="7" value="' . $style[CellA] . '">
      </td>
    </tr>
    <tr> 
      <td>Alternative cell background color</td>
      <td width="1"> 
        <table width="64" border="0" cellspacing="1" cellpadding="1" bgcolor="#000000">
          <tr> 
            <td bgcolor="' . $style[CellB] . '">&nbsp;</td>
          </tr>
        </table>
      </td>
      <td> 
        <input class="tbinput" type="text" name="style[CellB]" size="9" maxlength="7" value="' . $style[CellB] . '">
      </td>
    </tr>
    <tr> 
      <td>Table bordercolor<br>
        <font size="1">The color between the cells</font></td>
      <td width="1"> 
        <table width="64" border="0" cellspacing="1" cellpadding="1" bgcolor="#000000">
          <tr> 
            <td bgcolor="' . $style[border_col] . '">&nbsp;</td>
          </tr>
        </table>
      </td>
      <td> 
        <input class="tbinput" type="text" name="style[border_col]" size="9" maxlength="7" value="' . $style[border_col] . '">
      </td>
    </tr>
    <tr> 
      <td>Error color<br>
        <font size="1">Used for error messages and important notes
</font>      </td>
      <td width="1"> 
        <table width="64" border="0" cellspacing="1" cellpadding="1" bgcolor="#000000">
          <tr> 
            <td bgcolor="' . $style[color_err] . '">&nbsp;</td>
          </tr>
        </table>
      </td>
      <td> 
        <input class="tbinput" type="text" name="style[color_err]" size="9" maxlength="7" value="' . $style[color_err] . '">
      </td>
    </tr>
    <tr> 
      <td>Link</td>
      <td width="1"> 
        <table width="64" border="0" cellspacing="1" cellpadding="1" bgcolor="#000000">
          <tr> 
            <td bgcolor="' . $style[col_link] . '">&nbsp;</td>
          </tr>
        </table>
      </td>
      <td> 
        <input class="tbinput" type="text" name="style[col_link]" size="9" maxlength="7" value="' . $style[col_link] . '">
      </td>
    </tr>
    <tr> 
      <td>Link (visited)</td>
      <td width="1"> 
        <table width="64" border="0" cellspacing="1" cellpadding="1" bgcolor="#000000">
          <tr> 
            <td bgcolor="' . $style[col_link_v] . '">&nbsp;</td>
          </tr>
        </table>
      </td>
      <td> 
        <input class="tbinput" type="text" name="style[col_link_v]" size="9" maxlength="7" value="' . $style[col_link_v] . '">
      </td>
    </tr>
    <tr> 
      <td>Link hover</td>
      <td width="1"> 
        <table width="64" border="0" cellspacing="1" cellpadding="1" bgcolor="#000000">
          <tr> 
            <td bgcolor="' . $style[col_link_hover] . '">&nbsp;</td>
          </tr>
        </table>
      </td>
      <td> 
        <input class="tbinput" type="text" name="style[col_link_hover]" size="9" maxlength="7" value="' . $style[col_link_hover] . '">
      </td>
    </tr>
    <tr> 
      <td>Default font</td>
      <td width="1">&nbsp;</td>
      <td> 
        <input class="tbinput" type="text" name="style[stdfont]" value="' . EditboxEncode($style[stdfont]) . '">
      </td>
    </tr>
    <tr>
      <td>Board image-path</td>
      <td width="1">&nbsp;</td>
      <td> 
        <input class="tbinput" type="text" name="style[boardimage]" value="' . $style[boardimage] . '">
      </td>
    </tr>
<!--
    <tr> 
      <td>"New Topic"-image-path</td>
      <td width="1">&nbsp;</td>
      <td> 
        <input class="tbinput" type="text" name="style[newtopicimage]" value="' . $style[newtopicimage] . '">
      </td>
    </tr>
-->
    <tr> 
      <td>Public style?<br>
        <font size="1">Allow Users to select this style in their profile?</font></td>
      <td width="1">&nbsp;</td>
      <td>
        <input type="radio" name="style[styleispublic]" value="1"' . ($style['styleispublic'] == 1 ? ' checked' : '') . '>
        Yes
        <input type="radio" name="style[styleispublic]" value="0"' . ($style['styleispublic'] == 0 ? ' checked' : '') . '>
        No 
      </td>
    </tr>
    <tr> 
      <td>&nbsp;</td>
      <td width="1">&nbsp;</td>
      <td> 
        <input type="hidden" name="style[styleid]" value="' . $style[styleid] . '">
        <input type="hidden" name="action" value="' . $action . '">
        <input type="hidden" name="session" value="' . $session . '">
        <input type="submit" name="Abschicken" value="Submit">
      </td>
    </tr>
  </table>
</form>';
}

function writestyle($id)
{
  global $pref;

  $r_style = query("SELECT colorbg, colorbgfont, color4, col_he_fo_font, color1, CellA, CellB, border_col, color_err, col_link, col_link_v, col_link_hover, stdfont AS font FROM $pref"."style WHERE styleid=$id");

  $style = mysql_fetch_array($r_style);

  $str = "a:link { color: $style[col_link]; text-decoration: none; }\n".
         "a:visited { color: $style[col_link_v]; text-decoration: none; }\n".
         "a:hover { color: $style[col_link_hover]; text-decoration: none; }\n".
         "a:active { color: $style[col_link]; text-decoration: none; }\n".
         "a.bglink:link { color: $style[colorbgfont]; text-decoration: underline; }\n".
         "a.bglink:visited { color: $style[colorbgfont]; text-decoration: underline; }\n".
         "a.bglink:hover { color: $style[colorbgfont]; text-decoration: none; }\n".
         "a.bglink:active { color: $style[colorbgfont]; text-decoration: underline; }\n".
         "a.hefo:link { color: $style[col_he_fo_font]; text-decoration: underline; }\n".
         "a.hefo:visited { color: $style[col_he_fo_font]; text-decoration: underline; }\n".
         "a.hefo:hover { color: $style[col_he_fo_font]; text-decoration: none; }\n".
         "a.hefo:active { color: $style[col_he_fo_font]; text-decoration: underline; }\n".
         "body { background-color: $style[colorbg]; color: $style[color1]; font-family: $style[font]; }\n".
         "h1 { color: $style[col_he_fo_font]; font-size: 18px; padding: 0px; margin: 0px; font-weight: bold; font-family: $style[font] }\n".
         ".border-col { background-color: $style[border_col]; }\n".
         ".cella { background-color: $style[CellA]; }\n".
         ".cellb { background-color: $style[CellB]; }\n".
         ".color4 { background-color: $style[color4]; }\n".
         ".tbbutton { font-family: Verdana; font-size: 8pt; }\n".
         ".tbinput { background-color: #EEEEEE; font-family: Verdana; font-size: 8pt; }\n".
         ".tbselect { background-color: #EEEEEE; font-family: Verdana; font-size: 8pt; }\n".
         ".tbtextarea { background-color: #EEEEEE; font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 10pt; }\n".
         ".smallfont { font-family: $style[font]; font-size:10px; }\n".
         ".stdfont { font-family: $style[font]; font-size:12px; }";

  $f = fopen("../templates/css/$id.css", "w");
  
  fwrite($f, $str);

  fclose($f);
}

/*
 * =======================================
 *                ListStyles
 * =======================================
 */
if( $action == "ListStyles" )
{
    print '<b>Styles</b><br><br>';

    $r_style = query("SELECT styleid, stylename, styleispublic, styleisdefault FROM ".$pref."style");
    
    print '<table border="0" cellspacing="1" cellpadding="5">';

    while( $style = mysql_fetch_array($r_style) )
    {
        if( $style['styleispublic'] )
        {
            $ispublic = '<font color="darkblue">*</font>';
        }
        else
        {
            $ispublic = '';
        }

        if( $style['styleisdefault'] )
        {
            $isdefault = '<font color="red">*</font>';
        }
        else
        {
            $isdefault = '';
        }

        print '  <tr> 
    <td><font size="2">' . $style[stylename] . '</font></td>
    <td><font size="2" width="1">' . $isdefault . '</font></td>
    <td><font size="2" width="1">' . $ispublic . '</font></td>
    <td><font size="2"><a href="style.php?action=EditStyle&styleid=' . $style[styleid] . '&session=' . $session . '">edit</a></font></td>
    <td><font size="2"><a href="style.php?action=DeleteStyle&styleid=' . $style[styleid] . '&session=' . $session . '">delete</a></font></td>
    <td><font size="2"><a href="style.php?action=SetDefault&styleid=' . $style[styleid] . '&session=' . $session . '">set as default</a></font></td>
    <td><font size="2"><a href="style.php?action=ExportStyle&styleid=' . $style[styleid] . '&session=' . $session . '">export</a></font></td>
  </tr>';
    }

    print '</table>';
    
    echo '<br><br><font color="red">*</font> = Global default style: This style will be used in every board unless the board has its own style.<br>
    <font color="darkblue">*</font> = Public style: Users can select this style in their profile.<br><br>';

    echo '<b><font color="red">New!</font> Get more styles:</b> <a href="dynx.php" target="_blank">Download</a><br><br>';
}





/*
 * =======================================
 *                EditStyle
 * =======================================
 */
elseif( $action == "EditStyle" )
{
    print '<b>Edit styleset</b><br><br>';

    $r_style = query("SELECT * FROM ".$pref."style WHERE styleid=$styleid");
    $style = mysql_fetch_array($r_style);

    StyleForm("UpdateStyle", $style);
}



/*
 * =======================================
 *                UpdateStyle
 * =======================================
 */
elseif( $action == "UpdateStyle" )
{
    $style['stdfont'] = EditboxDecode($style['stdfont']);

    if( $style['styleispublic'] != 1 )    
    {
        // the admin has decided that this style is not public.
        // in case it *was* public, make sure no one can use this style any longer
        query("UPDATE ".$pref."user SET styleid=0 WHERE styleid=$style[styleid]");
    }

    query("UPDATE ".$pref."style SET
        stylename='".addslashes($style['stylename'])."',
        colorbg='$style[colorbg]',
        colorbgfont='$style[colorbgfont]',
        color1='$style[color1]',
        CellA='$style[CellA]',
        CellB='$style[CellB]',
        color4='$style[color4]',
        col_he_fo_font='$style[col_he_fo_font]',
        border_col='$style[border_col]',
        color_err='$style[color_err]',
        col_link='$style[col_link]',
        col_link_v='$style[col_link_v]',
        col_link_hover='$style[col_link_hover]',
        stdfont='" . addslashes($style['stdfont']) . "',
        boardimage='$style[boardimage]',
        newtopicimage='$style[newtopicimage]',
        styleispublic='$style[styleispublic]',
        styletemplate='$style[styletemplate]'
        WHERE styleid=$style[styleid]");

    writestyle($style['styleid']);
        
    print 'Style has been updated!<br>click <a href="style.php?action=EditStyle&styleid=' . $style['styleid'] . '&session=' . $session . '">here</a> to edit this style once again.';
}
 
 


/*
 * =======================================
 *                NewStyle
 * =======================================
 */
elseif( $action == "NewStyle" )
{
    print '<b>Create new style</b><br><br>';

    StyleForm("InsertStyle", array());
}




/*
 * =======================================
 *                InsertStyle
 * =======================================
 */
elseif( $action == "InsertStyle" )
{
    $style['stdfont'] = EditboxDecode($style['stdfont']);
    $style['stdfontend'] = EditboxDecode($style['stdfontend']);
    $style['smallfont'] = EditboxDecode($style['smallfont']);
    $style['smallfontend'] = EditboxDecode($style['smallfontend']);
    
    query("INSERT INTO ".$pref."style (
            stylename,
            colorbg,
            colorbgfont,
            color1,
            CellA,
            CellB,
            col_he_fo_font,
            color4,
            border_col,
            color_err,
            col_link,
            col_link_v,
            col_link_hover,
            stdfont,
            boardimage,
            newtopicimage,
            styleispublic,
            styletemplate
        ) VALUES (
            '".addslashes($style['stylename'])."',
            '$style[colorbg]',
            '$style[colorbgfont]',
            '$style[color1]',
            '$style[CellA]',
            '$style[CellB]',
            '$style[col_he_fo_font]',
            '$style[color4]',
            '$style[border_col]',
            '$style[color_err]',
            '$style[col_link]',
            '$style[col_link_v]',
            '$style[col_link_hover]',
            '" . addslashes($style['stdfont']) . "',
            '$style[boardimage]',
            '$style[newtopicimage]',
            '$style[styleispublic]',
            '$style[styletemplate]'
        )");
        
    print 'Style has been added!';
}



/*
 * =======================================
 *                DeleteStyle
 * =======================================
 */
elseif( $action == "DeleteStyle" )
{
    $r_board = query("SELECT boardname FROM ".$pref."board WHERE styleid=$styleid");
    $r_style = query("SELECT styleisdefault FROM ".$pref."style WHERE styleid=$styleid");
    $style = mysql_fetch_array($r_style);
    
    if( mysql_num_rows($r_board) > 0 )
    {
        print 'Unable to delete style: this style is currently being used by the following board(s):<br><br><ul>';
        while( $board = mysql_fetch_array($r_board) )
        {
            print "<li><b>$board[boardname]</b></li>";
        }
        print '</ul>';
    }
    elseif( $style['styleisdefault'] == 1 )
    {
        print 'Unable to delete style: you cannot delete the default style!';
    }
    else
    {
        // admin wants to delete this style.
        // make sure no user is using this style any more ..
        query("UPDATE ".$pref."user SET styleid=0 WHERE styleid=$styleid");

        query("DELETE FROM ".$pref."style WHERE styleid=$styleid");
        print 'Style has been deleted.';

        if(file_exists('../templates/css/'.$styleid.'.css'))
          {
            unlink('../templates/css/'.$styleid.'.css');
          }
    }
}


/*
 * =======================================
 *                SetDefault
 * =======================================
 */
elseif( $action == 'SetDefault' )
{
    $r_style = query("UPDATE ".$pref."style SET styleisdefault=0");
    $r_style = query("UPDATE ".$pref."style SET styleisdefault=1 WHERE styleid=$styleid");

    writestyle($styleid);

    print 'Style has been set to default.';
}



/*
 * =======================================
 *                ExportStyle
 * =======================================
 */
elseif( $action == 'ExportStyle' )
{
    $r_style = query("SELECT * FROM ".$pref."style WHERE styleid=$styleid");
    $style = mysql_fetch_array($r_style);

    $glue = '~~~';

    $styledata = array();
    $styledata[] = 'thwb_2.7';
    while( list($k, $v) = each($style) )
    {
        if( $k != 'styleid' && $k != 'styleispublic' && $k != 'styleisdefault' && gettype($k) != 'integer')
        {
            $styledata[] = htmlspecialchars($v);
        }
    }

    $stylestring = implode($glue, $styledata);

    print '<b>Export style</b><br><br>';
    print 'The style has been exported:<br><br>';
    print '<textarea name="styledata" cols="60" rows="14">' . $stylestring . '</textarea>';
}



/*
 * =======================================
 *                ImportStyle'
 * =======================================
 */
elseif( $action == 'ImportStyle' )
{
    print '<b>Import style</b><br><br>';

    if( !$insert )
    {
        print '
Upload .style file:
<form ENCTYPE="multipart/form-data" name="theform" method="post" action="style.php">
  <input class="tbinput" type="file" name="style_file">
  <br>
  <br>
  <input type="submit" name="Abschicken" value="Import &gt;&gt;">
  <input type="hidden" name="action" value="ImportStyle">
  <input type="hidden" name="insert" value="1">
  <input type="hidden" name="session" value="' . $session . '">
</form><br>
<br>
OR choose a local file (templates/):
<br>
<form name="form1" method="post" action="style.php">
  <select class="tbinput" name="style_file" size="6">';
    $a_stylefile = array();
    
    $dp = opendir('../templates/');
    
    $files = 0;
    while( $filename = readdir($dp) )
    {
        if( substr($filename, -6) == '.style' )
        {
            print '<option value="'.$filename.'">'.$filename.'</option>';
            $files++;
        }
    }
    
    if( !$files )
    {
        print'
    <option value="_nofile">( No styles available. You need to</option>
    <option value="_nofile"> upload .style files into your
    <option value="_nofile"> templates/ directory. )</option>';
    }

    print'
  </select>
  <br><br>
  <input type="hidden" name="session" value="'.$session.'">
  <input type="hidden" name="action" value="ImportStyle">
  <input type="hidden" name="local" value="1">
  <input type="hidden" name="insert" value="1">
  <input type="submit" name="import" value="Import &gt;&gt;">
</form>';
    }
    else
    {
        if( $local )
            $styledata = file('../templates/'. basename($style_file));
        else
            $styledata = file($HTTP_POST_FILES['style_file']['tmp_name']);
        
        if(empty($styledata))
            print 'Style file is empty, please check.';
        else {
            $a_name = array();
            $a_value = array();

            while( list($nline, $line) = each($styledata) )
            {
                if( trim($line) )
                {
                    $pos = strpos($line, '=');
                    
                    $name = substr($line, 0, $pos);
                    $value = substr($line, $pos + 1);
            
                    if( $name == 'styleversion' )
                    {
                        $ver = trim($value);
                    }
                    else
                    {
                        $a_name[] = $name;
                        $a_value[] = "'".addslashes(trim($value))."'";
                    }
                }
            }
            
            if( $ver != $style_file_version )
                print 'Invalid style file version.';
            else
            {
                $query = "INSERT INTO $pref"."style
                    (".implode(',', $a_name).")
                    VALUES
                    (".implode(',', $a_value).")";
                    
                query($query);

                writestyle(mysql_insert_id());
                
                print 'Style has been imported.';
            }
        }
    }
}


tb_footer();
