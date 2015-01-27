<?php $this->layout('install-frame', [
    'about_handler' => $about_handler,
    'language' => $language,
    'step' => $step,
    'variables' => $variables
]) ?>
<b><?= $this->_('createadmin') ?></b><br>
<br>
<table width="100%" border="0" cellspacing="0" cellpadding="2">
    <tr>
        <td><label for="administrator-username"><?= $this->_('username') ?></label></td>
        <td width="10">&nbsp;</td>
        <td><input id="administrator-username" type="text" name="administrator-username" value="<?= $this->e($username) ?>" class="inst_button"></td>
    </tr>
    <tr>
        <td><label for="administrator-email"><?= $this->_('email') ?></label></td>
        <td width="10">&nbsp;</td>
        <td><input id="administrator-email" type="text" name="administrator-email" class="inst_button" value="<?= $this->e($email) ?>"></td>
    </tr>
    <tr>
        <td><label for="administrator-password"><?= $this->_('password') ?></label></td>
        <td width="10">&nbsp;</td>
        <td><input id="administrator-password" type="password" name="administrator-password" class="inst_button"></td>
    </tr>
</table>
