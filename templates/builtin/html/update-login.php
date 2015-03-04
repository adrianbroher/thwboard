<?php $this->layout('install-frame', [
    'about_handler' => $about_handler,
    'step' => $step,
]) ?>
<b><?= $this->_('login') ?></b><br>
<br>
<div>
    <label for="login-username"><?= $this->_('username') ?></label>
    <input id="login-username" type="text" name="login-username">
</div>
<div>
    <label for="login-password"><?= $this->_('password') ?></label>
    <input id="login-password" type="password" name="login-password">
</div>
<div>
    <label for="language">Language</label>
    <select id="language" name="lang">
<?php foreach ($languages as $key => $language): ?>
        <option<?= (($key == 'en') ? ' selected="selected"' : '') ?> value="<?= $this->e($key) ?>"><?= $this->e($language['desc']) ?></option>
<?php endforeach ?>
    </select>
</div>
