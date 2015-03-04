<?php $this->layout('install-frame', [
    'about_handler' => $about_handler,
    'step' => $step
]) ?>
<b><?= $this->_('createadmin') ?></b><br>
<br>
<div>
    <label for="administrator-username"><?= $this->_('username') ?></label>
    <input id="administrator-username" type="text" name="administrator-username" value="<?= $this->e($username) ?>" class="inst_button">
</div>
<div>
    <label for="administrator-email"><?= $this->_('email') ?></label>
    <input id="administrator-email" type="text" name="administrator-email" class="inst_button" value="<?= $this->e($email) ?>">
</div>
<div>
    <label for="administrator-password"><?= $this->_('password') ?></label>
    <input id="administrator-password" type="password" name="administrator-password" class="inst_button">
</div>
