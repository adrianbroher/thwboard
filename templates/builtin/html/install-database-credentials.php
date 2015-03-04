<?php $this->layout('install-frame', [
    'about_handler' => $about_handler,
    'step' => $step
]) ?>
<b><?= $this->_('mysqldata') ?></b><br>
<br>
<p><?= $this->_('entermysqldata') ?></p>
<div>
    <label for="database-hostname"><?= $this->_('mysqlhost') ?></label>
    <input id="database-hostname" type="text" name="database-hostname" value="<?= $this->e($hostname) ?>">
</div>
<div>
    <label for="database-username"><?= $this->_('mysqluser') ?></label>
    <input id="database-username" type="text" name="database-username" value="<?= $this->e($username) ?>">
</div>
<div>
    <label for="database-password"><?= $this->_('mysqlpass') ?></label>
    <input id="database-password" type="password" name="database-password">
</div>
