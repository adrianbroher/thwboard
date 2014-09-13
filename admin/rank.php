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
tb_header();

if( $config['enable_ranks'] )
{
	print '<b>Ranks</b><br><br>';

	if( $action == 'UpdateRanks' && is_array($rankid) )
	{
		while( list(, $rank['rankid']) = @each($rankid) )
		{
			if( $rankdelete[$rank['rankid']] == 'yes' )
			{
			query("DELETE FROM ".$pref."rank WHERE rankid=$rank[rankid]");
			}
			else
			{
				query("UPDATE ".$pref."rank SET
					rankposts='" . $rankposts[$rank['rankid']] . "',
					ranktitle='" . addslashes($ranktitle[$rank['rankid']]) . "',
					rankimage='" . addslashes($rankimage[$rank['rankid']]) . "'
				WHERE rankid=$rank[rankid]");
			}
		}
		
		print 'Ranks have been updated.<br><br>';
	}
	elseif( $action == 'InsertRank' )
	{
		if( $ranktitle && $rankposts )
		{
			query("INSERT INTO ".$pref."rank (ranktitle, rankimage, rankposts)
				VALUES ('" . addslashes($ranktitle) . "', '" . addslashes($rankimage) . "', '$rankposts');");
		}
		
		print 'Rank has been added.<br><br>';
	}


	print '<form name="theform" method="post" action="rank.php">
  <table border="0" cellspacing="1" cellpadding="8">
    <tr> 
      <td>Title</td>
      <td>Image (optional)</td>
      <td>Required posts</td>
      <td>Remove</td>
    </tr>';
    
    $r_rank = query("SELECT rankid, ranktitle, rankimage, rankposts FROM ".$pref."rank ORDER BY rankposts DESC");
    while( $rank = mysql_fetch_array($r_rank) )
    {
    	print '    <tr> 
      <td> 
        <input class="tbinput" type="hidden" name="rankid[' . $rank['rankid'] . ']" value="' . $rank['rankid'] . '">
        <input class="tbinput" type="text" name="ranktitle[' . $rank['rankid'] . ']" value="' . htmlspecialchars($rank['ranktitle']) . '">
      </td>
      <td> 
        <input class="tbinput" type="text" name="rankimage[' . $rank['rankid'] . ']" value="' . htmlspecialchars($rank['rankimage']) . '">
      </td>
      <td align="center"> 
        <input class="tbinput" type="text" name="rankposts[' . $rank['rankid'] . ']" size="5" value="' . $rank['rankposts'] . '">
      </td>
      <td align="center"> 
        <input type="checkbox" name="rankdelete[' . $rank['rankid'] . ']" value="yes">
      </td>
    </tr>';
    }
    
    print '  </table>
  <p>
    <input type="hidden" name="action" value="UpdateRanks">
    <input type="hidden" name="session" value="' . $session . '">
    <input type="submit" name="update" value="Update &gt;&gt;">
  </p>
</form>
';
	print '<br><hr><br><b>Add rank</b><br><br>';
	
	print '
<form name="theform" method="post" action="rank.php">  
  <table border="0" cellspacing="1" cellpadding="8">
    <tr>
      <td>Title</td>
      <td>
        <input class="tbinput" type="text" name="ranktitle">
      </td>
    </tr>
    <tr>
      <td>Image (optional)</td>
      <td>
        <input class="tbinput" type="text" name="rankimage">
      </td>
    </tr>
    <tr>
      <td>Required posts</td>
      <td>
        <input class="tbinput" type="text" name="rankposts" size="5">
      </td>
    </tr>
  </table>
  <p>
    <input type="hidden" name="action" value="InsertRank">
    <input type="hidden" name="session" value="' . $session . '">
    <input type="submit" name="Abschicken" value="Insert &gt;&gt;">
  </p>
 </form>
';
}
else
{
	print 'Ranks are currently disabled. You can enable them <a href="index.php?session=' . $session . '&action=EditSettings">here</a>.';
}

tb_footer();
?>
