<b><?= $this->_('login') ?></b><br>
<br>
<table cellspacing="0" cellpadding="2" border="0">
    <tr>
        <td><label for="login-username"><?= $this->_('username') ?></label></td>
        <td width="10">&nbsp;</td>
        <td><input id="login-username" type="text" name="login-username" class="inst_button"></td>
    </tr>
    <tr>
        <td><label for="login-password"><?= $this->_('password') ?></label></td>
        <td width="10">&nbsp;</td>
        <td><input id="login-password" type="password" name="login-password" class="inst_button"></td>
    </tr>
    <tr>
        <td><label for="language">Language</label></td>
        <td width="10">&nbsp;</td>
        <td>
            <select id="language" name="lang" class="inst_button">
<?php foreach ($languages as $key => $language): ?>
                <option<?= (($key == 'en') ? ' selected="selected"' : '') ?> value="<?= $this->e($key) ?>"><?= $this->e($language['desc']) ?></option>
<?php endforeach ?>
            </select>
        </td>
    </tr>
</table>
