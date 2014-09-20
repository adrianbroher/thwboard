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

define('SEARCH', 0);
define('REPLACE', 1);

$a_thwbcode = array();
$a_smilies = array();

function get_smilies()
{
    $a_smilies = array(
        ':)'     => 'smile',
        '&gt;:(' => 'angry',
        ':('    => 'frown',
        ':D'    => 'biggrin',
        ';)'    => 'wink',
        ':?'    => 'question',
        ':|'    => 'strange',
        ':\\'    => 'prefect',
        '=)'    => 'gumble',
        ':oah:'    => 'oah',
        ':rolleyes:' => 'rolleyes'
    );

    define( 'THWB_SMILIES', true );

    return $a_smilies;
}


function get_thwb_tags()
{
    global $style, $a_thwbcode, $a_thwbcode2nd;

    $a_thwbcode = $a_thwbcode2nd = array();

    //ttt: please keep an eye on the order the tags appear;
    //       e.g. noparse /must/ come after [php] and [code]

    /**
     * Achtung!
     * Wenn hier ein Code-Tag hinzugefügt wird,
     * muss auch in inc/functions.inc.php
     * ca. Zeile 291:
     *     $a_tags = array("php", "code", "mail", "url", "noparse", "color", "b", "i", "u", "-", "quote");
     * angepasst werden.
     *
     * Attention!
     * When adding a code tag, please keep in
     * mind that you must also add it in
     * inc/functions.inc.php ~ line 291:
     *     $a_tags = array("php", "code", "mail", "url", "noparse", "color", "b", "i", "u", "-", "quote");
     **/

    // [php]
    $a_thwbcode[SEARCH][] = '/\[php\](.*)\[\/php\]/Uesi';
    $a_thwbcode[REPLACE][] = 'format_phpsource(\'\1\')';
    // [code]
    $a_thwbcode[SEARCH][] = '/\[code\](.*)\[\/code\]/Uesi';
    $a_thwbcode[REPLACE][] = 'format_source(\'\1\')';

    // [url]http://www.thwboard.de[/url]
    $a_thwbcode[SEARCH][] = "/\[url\]([a-zA-Z0-9.\-+]+):\/\/([^ \"\n]+)\[\/url\]/Ui";
    $a_thwbcode[REPLACE][] = '[noparse]<a href="\1://\2" target="_blank">\1://\2</a>[/noparse]';

    // [url]www.thwboard.de[/url]
    $a_thwbcode[SEARCH][] = "/\[url\]([^ ,\"\n]+)\.([^ ,\"\n]+)\[\/url\]/Ui";
    $a_thwbcode[REPLACE][] = '[noparse]<a href="http://\1.\2" target="_blank">\1.\2</a>[/noparse]';

    // http://www.thwboard.de
    $a_thwbcode[SEARCH][] = "/(^|[\]\( \n])([a-zA-Z0-9.\-+]+):\/\/([^ \"\n]+?)([\?!,\.\)]*)(?=[ \"\n\[]|$)/";
    $a_thwbcode[REPLACE][] = '\1[noparse]<a href="\2://\3" target="_blank">[/noparse]\2://\3</a>\4';
    // www.thwboard.de
    $a_thwbcode[SEARCH][] = "/(^|[\]\( \n])www\.([^ \"\n]+?)([\?!,\.\)]*)(?=[ \"\n\[]|$)/i";
    $a_thwbcode[REPLACE][] = '\1[noparse]<a href="http://www.\2" target="_blank">[/noparse]www.\2</a>\3';
    // [mail]
    $a_thwbcode[SEARCH][] = '/\[mail\]([._0-9a-zA-Z-]+)@([._0-9a-zA-Z-]+)\[\/mail\]/Ui';
    $a_thwbcode[REPLACE][] = '[noparse]<a href="mailto:\1@\2">\1@\2</a>[/noparse]';
    // [mail=""]
    $a_thwbcode[SEARCH][] = '/\[mail="([._0-9a-zA-Z-]+)@([._0-9a-zA-Z-]+)"\](.*)\[\/mail\]/Ui';
    $a_thwbcode[REPLACE][] = '[noparse]<a href="mailto:\1@\2">\3</a>[/noparse]';

    // [url=""]
    $a_thwbcode[SEARCH][] = "/\[url=\"([a-zA-Z0-9.\-+]+):\/\/([^ \"\n]+)\"\](.*)\[\/url\]/Usi";
    $a_thwbcode[REPLACE][] = '[noparse]<a href="\1://\2" target="_blank">[/noparse]\3</a>';
    // [url="www.thwboard.de"]
    $a_thwbcode[SEARCH][] = "/\[url=\"www\.([^ \"\n]+)\"\](.*)\[\/url\]/Usi";
    $a_thwbcode[REPLACE][] = '[noparse]<a href="http://www.\1" target="_blank">[/noparse]\2</a>';
    // [noparse] - these are extracted before further parsing
    $a_thwbcode[SEARCH][] = '/\[noparse\](.*)\[\/noparse\]/Uesi';
    $a_thwbcode[REPLACE][] = 'noparse(\'\1\')';
    // [color]
     $a_thwbcode2nd[SEARCH][] = '/\[color="([a-zA-Z0-9# ]+)"\](.*)\[\/color\]/Uis';
    $a_thwbcode2nd[REPLACE][] = '<span style="color: \1 !important;">\2</span>';
    // [b]
    $a_thwbcode2nd[SEARCH][] = '/\[b\](.*)\[\/b\]/Uis';
    $a_thwbcode2nd[REPLACE][] = '<b>\1</b>';
    // [i]
    $a_thwbcode2nd[SEARCH][] = '/\[i\](.*)\[\/i\]/Uis';
    $a_thwbcode2nd[REPLACE][] = '<i>\1</i>';
    // [u]
    $a_thwbcode2nd[SEARCH][] = '/\[u\](.*)\[\/u\]/Uis';
    $a_thwbcode2nd[REPLACE][] = '<u>\1</u>';
    // [-]
    $a_thwbcode2nd[SEARCH][] = '/\[-\](.*)\[\/-\]/Uis';
    $a_thwbcode2nd[REPLACE][] = '<s>\1</s>';
    // [quote]
    $a_thwbcode2nd[SEARCH][] = '/\[quote\]/';
    $a_thwbcode2nd[REPLACE][] = '<br><table border="0" align="center" width="95%" cellpadding="3" cellspacing="1" bgcolor="' . $style['color4'] . '"><tr><td><span class="smallfont" style="color: '.$style['col_he_fo_font'].'"><b>Zitat:</b></span></td></tr><tr><td bgcolor="' . $style['CellA'] . '"><font size="2">';
    // [/quote]
    $a_thwbcode2nd[SEARCH][] = '/\[\/quote\]/';
    $a_thwbcode2nd[REPLACE][] = '</font></td></tr></table><br>';

    define( 'THWB_TAGS', true );

    return $a_thwbcode;
}


class CStack {
    var $tos;
    var $stack;

    function CStack()
    {
        $this->stack = array();
        $this->tos = 0;
        $this->stack[$this->tos] = 0; // simplifies things a bit =)
    }
    function push( $var ) {    $this->stack[++$this->tos] = $var; }
    function peek()    { return $this->stack[$this->tos]; }
    function pop()
    {
        if( $this->tos )
            return $this->stack[$this->tos--];
        else
            return 0;
    }

    //ttt: now supports regexps
    function search( $var, $preg=0 )
    {
        for( $i = $this->tos; $i > 0; $i-- )
        {
            if( $preg )
            {
                if( preg_match( $this->stack[$i], $var ) )
                    return 1;
            }
            else
            {
                if( !strcmp($this->stack[$i], $var) )
                    return 1;
            }
        }
        return 0;
    }
}


function close_tags( &$tags, &$s_pos, &$string, $curtag='' )
{
    if( strlen($curtag) > 0 )
    {
        if( !$tags->search($curtag) )
        {
            // no corresponding start tag, remove this tag
            $string = substr($string, 0, $s_pos - 1) . substr($string, $s_pos + strlen($curtag)+2);
            $s_pos -= strlen($curtag)+3; //HACK: keep string positioning consistent
            return;
        }
    }
    else
    {
        $curtag = "###"; // just a dummy
    }

    $oldtag = $tags->peek();
    while( $oldtag && ($oldtag != $curtag)  )
    {
        // missing end tag, just insert one
        $string = substr($string, 0, $s_pos-1) .'[/'. $tags->pop() .']'. substr($string, $s_pos-1);
        $s_pos += strlen($oldtag) + 3; // skip over [/$oldtag]
        $oldtag = $tags->peek();
    }
    $tags->pop();
}


function fixup_quotes( $string )
{
    // skip the whole thing when there are no quotes
    if( !(strchr($string,'[quote]') || strchr($string,'[/quote]')) )
        return $string;

    $tmp = $string;
    $s_pos = 0; // $s_pos is position in $string
    $t_pos = 0; // $t_pos is position in $tmp
    $tags = new CStack();

    while( is_integer($t_pos = strpos($tmp, '[')) )
    {
        $s_pos += $t_pos+1;
        $tmp = substr( $tmp, $t_pos+1 );

        $endpos = strpos( $tmp, ']' );
        if( is_integer($endpos) ) {
            $curtag = substr( $tmp, 0, $endpos );
            switch( $curtag )
            {
                case 'quote':
                    $tags->push($curtag);
                    break;
                case '/quote':
                    close_tags( $tags, $s_pos, $string, substr($curtag, 1) );
                    break;
                default:
                    //ttt: don't be fooled by [[quote] stuff
                    $endpos = -1;
                    break;
            }
            $s_pos += $endpos+1;
            $tmp = substr( $string, $s_pos );
        }
    }

    // if there are still some endtags missing, add them at the end
    $s_pos = strlen( $string ) +1; // normally this should be -1, but close_tags moves back 2 chars
    close_tags( $tags, $s_pos, $string );

    return $string;
}


function noparse($string, $insert = 0)
{
    static    $rpc, $a_replace;

    if( !$insert )
    {
        $string = str_replace('\"', '"', $string);

        $a_replace[++$rpc] = $string;

        return "<noparse $rpc>";
    }
    else
    {
        return $a_replace[$insert];
    }
}


function format_source($string)
{
    global $style;

    $string = str_replace('\"', '"', $string);
    $string = str_replace('  ', '&nbsp;&nbsp;', $string);
    $string = str_replace("\n", '<br>', $string);

    return '<br><table border="0" align="center" width="95%" cellpadding="3" cellspacing="1" bgcolor="' . $style['color4'] . '"><tr><td><span class="smallfont" style="color: '.$style['col_he_fo_font'].'"><b>Quellcode:</b></span></td></tr><tr><td bgcolor="' . $style['CellA'] . '"><code>' . noparse($string) . '</code></td></tr></table><br>';
}


function format_phpsource($string)
{
    global $style;

    $string = trim($string);
    $string = str_replace('\"', '"', $string);

    if( (float)(phpversion()) >= 4.2 )
    {
        $string = str_replace("<br>", "\n", $string);
        $string = str_replace('&lt;', '<', $string);
        $string = str_replace('&gt;', '>', $string);

        //ttt: automatically insert < ?php if necessary
        if( !preg_match( '/^\<\?(php)?/s', $string ) )
            $string = "<?php\n". $string;

        if( !preg_match( '/\?\>$/s', $string ) )
            $string .= "\n?>";

        $string = highlight_string($string, TRUE);
    }

    return '<br><table border="0" align="center" width="95%" cellpadding="3" cellspacing="1" bgcolor="' . $style['color4'] . '"><tr><td><span class="smallfont" style="color: '.$style['col_he_fo_font'].'"><b>PHP-Quellcode:</b></span></td></tr><tr><td bgcolor="' . $style['CellA'] . '"><code>' . noparse($string) . '</code></td></tr></table><br>';
}


function preparse_code($string)
{
    $string = str_replace("\r", '', $string);
    $string = str_replace(chr(160), '', $string);
    $string = trim($string);

    $string = str_replace("con\\con", '', $string);
    $string = str_replace("con/con", '', $string);

    // [code] tags.
    $string = str_replace("\t", '    ', $string);

    $string = ereg_replace("\[code\]([ \n]*)", '[code]', $string);
    $string = ereg_replace("\[/code\]([ \n]*)", '[/code] ', $string);
    $string = ereg_replace("\[php\]([ \n]*)", '[php]', $string);
    $string = ereg_replace("\[/php\]([ \n]*)", '[/php] ', $string);
    $string = ereg_replace("\[quote\]([ \n]*)", '[quote]', $string);
    $string = ereg_replace("\[/quote\]([ \n]*)", '[/quote] ', $string);

    return $string;
}


function parse_code($string, $do_br = 0, $do_img = 0, $do_code = 0, $do_smilies = 0)
{
    global $config, $style;
    static $smilies_fixed = 0;

    // HTML-Security & special characters

    $trans = get_html_translation_table(HTML_ENTITIES, ENT_QUOTES);

      foreach ($trans as $key => $value)
        {
          $trans[$key] = '&#'.ord($key).';';
        }

       strtr($string, $trans);

    $string = str_replace('<', '&lt;', $string);
    $string = str_replace('>', '&gt;', $string);

    if( $do_code )
    {
        global $a_thwbcode, $a_thwbcode2nd;

        if( !defined('THWB_TAGS') )
            get_thwb_tags();

        $string = preg_replace($a_thwbcode[SEARCH], $a_thwbcode[REPLACE], $string );
        // nested [quote] fixup
        $string = fixup_quotes($string);

        $string = preg_replace($a_thwbcode2nd[SEARCH], $a_thwbcode2nd[REPLACE], $string );
    }

    if( $do_smilies && $config['smilies'] )
    {
        global $a_smilies;

        if( !defined('THWB_SMILIES') )
            $a_smilies = get_smilies();

        if( !$smilies_fixed )
        {
            reset($a_smilies);
            $url_prepend = '<img src="templates/'.$style['styletemplate'].'/images/icon/';
            while( current( $a_smilies ) )
            {
                $a_smilies[key($a_smilies)] = $url_prepend.current($a_smilies).'_new.png" border="0">';
                next( $a_smilies );
            }
            $smilies_fixed = 1;
        }

        $string = strtr( $string, $a_smilies );
    }

    // reinsert extracted parts
    $string = preg_replace( '/\<noparse ([0-9]+)\>/e', 'noparse("",\1)', $string );

    if( $do_img && ($config['imageslevel'] != 2) )
      {
        $string = preg_replace('/\[img\](|[ \n]*<a href=\")([a-zA-Z]+):\/\/([^ \"\n]+)(|\" target=\"_blank\">\2:\/\/\3([ \n]*)<\/a>[ \n]*)\[\/img\]/Usi', '<img src="\2://\3" alt="" border="0">', $string);
      }


    if( $do_br )
        $string = str_replace("\n", '<br />', $string);
    else
        $string = str_replace("\n", '', $string);

    return $string;
}
