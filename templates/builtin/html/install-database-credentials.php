<?php $this->layout('install-frame', [
    'about_handler' => $about_handler,
    'step' => $step
]) ?>
<b><?= $this->_('mysqldata') ?></b><br>
<br>
<?= $this->_('entermysqldata') ?><br>
<br>
<table border="0" cellspacing="0" cellpadding="2">
    <tr>
        <td><label for="database-hostname"><?= $this->_('mysqlhost') ?></label></td>
        <td width="10">&nbsp;</td>
        <td><input id="database-hostname" type="text" name="database-hostname" class="inst_button" value="<?= $this->e($hostname) ?>"></td>
    </tr>
    <tr>
        <td><label for="database-username"><?= $this->_('mysqluser') ?></label></td>
        <td width="10">&nbsp;</td>
        <td><input id="database-username" type="text" name="database-username" class="inst_button" value="<?= $this->e($username) ?>"></td>
    </tr>
    <tr>
        <td><label for="database-password"><?= $this->_('mysqlpass') ?></label></td>
        <td width="10">&nbsp;</td>
        <td><input id="database-password" type="password" name="database-password" class="inst_button"></td>
    </tr>
</table>
