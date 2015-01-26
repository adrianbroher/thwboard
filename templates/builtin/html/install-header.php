<html>
    <head>
        <title>ThWboard - phpInstaller v1.1</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <style type="text/css">
.inst_button {  font-family: Tahoma, Verdana, Arial, Helvetica, sans-serif; font-size: 8pt}
td {  font-family: Tahoma, Verdana, Arial, Helvetica, sans-serif; font-size: 8pt}
        </style>
    </head>
    <body bgcolor="#3A6EA5" text="#000000" link="#0000FF" vlink="#0033FF" alink="#0000FF">
        <form name="theform" method="post" action="<?= (isset($step) ? ('?step=' . $step) : '') ?>">
            <table width="600" border="0" cellspacing="0" cellpadding="0" align="center">
                <tr>
                    <td bgcolor="#D4D0C8" height="1" width="1"><img src="./images/space.gif" width="1" height="1"></td>
                    <td bgcolor="#D4D0C8" height="1" width="1"></td>
                    <td bgcolor="#D4D0C8" height="1"></td>
                    <td bgcolor="#D4D0C8" height="1" width="1"></td>
                    <td bgcolor="#000000" height="1" width="1"></td>
                </tr>
                <tr>
                    <td bgcolor="#D4D0C8" height="1" width="1"></td>
                    <td bgcolor="#FFFFFF" height="1" width="1"><img src="./images/space.gif" width="1" height="1"></td>
                    <td bgcolor="#FFFFFF" height="1"></td>
                    <td bgcolor="#FFFFFF" height="1" width="1"></td>
                    <td bgcolor="#000000" height="1" width="1"></td>
                </tr>
                <tr>
                    <td bgcolor="#D4D0C8" width="1"></td>
                    <td bgcolor="#FFFFFF" width="1"></td>
                    <td bgcolor="#D4D0C8">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td>
                                    <table width="100%" border="0" cellspacing="0" cellpadding="6">
                                        <tr>
                                            <td>
                                                <b>ThWboard <?= $this->_('installation') ?></b><br>
                                                <a href="<?= $about_handler ?>">About phpInstaller</a> v1.1
                                            </td>
                                            <td align="right"><img src="./images/thwboard_logo.gif"></td>
                                        </tr>
                                    </table>
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td bgcolor="#808080" height="1"><img src="./images/space.gif" width="1" height="1"></td>
                                        </tr>
                                        <tr>
                                            <td bgcolor="#FFFFFF" height="1"><img src="./images/space.gif" width="1" height="1"></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <table width="100%" border="0" cellspacing="0" cellpadding="16">
                                        <tr>
                                            <td>
