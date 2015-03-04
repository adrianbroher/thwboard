<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <title>ThWboard - phpInstaller v1.1</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <style type="text/css">
body {
    background-color: #3A6EA5;
    color: #000;
    font-family: Tahoma, Verdana, Arial, Helvetica, sans-serif;
    font-size: 8pt;
    margin: 0;
    padding: 0;
}
h1 {
    font-size: 8pt;
    margin: .5em;
}
hr {
    border: none;
    border-top: 1px solid #808080;
    border-bottom: 1px solid #FFF;
    clear: both;
}
#content {
    background-color: #D4D0C8;
    border-left: 1px solid #FFF;
    border-top: 1px solid #FFF;
    border-right: 1px solid #808080;
    border-bottom: 1px solid #808080;
    margin-left: auto;
    margin-right: auto;
    margin-top: 2em;
    width: 600px;
}
#links {
    margin-left: .5em;
    margin-bottom: .5em;
}
#logo {
    margin: .5em;
    float: right;
}
#submit {
    float: right;
    margin: 1em;
}
.inst_button {  font-family: Tahoma, Verdana, Arial, Helvetica, sans-serif; font-size: 8pt}
        </style>
    </head>
    <body>
        <div id="content">
            <img alt="ThWboard Logo" id="logo" src="./images/thwboard_logo.gif">
            <h1>ThWboard <?= $this->_('installation') ?></h1>
            <div id="links"><a href="<?= $about_handler ?>">About phpInstaller</a> v1.1</div>
            <hr>
            <form name="theform" method="post" action="<?= (isset($step) ? ('?step=' . $step) : '') ?>">
                <div style="padding: 1em">
<?= $this->section('content') ?>
                </div>
<?php if (isset($step)): ?>
                <hr>
                <input id="submit" type="submit" name="submit" value="<?= $this->_('next') ?> &gt;" class="inst_button">
                <div style="clear:both"></div>
<?php endif ?>
            </form>
        </div>
    </body>
</html>
