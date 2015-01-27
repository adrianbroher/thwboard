<?php $this->layout('install-frame', [
    'about_handler' => $about_handler,
    'language' => $language,
    'step' => $step,
    'variables' => $variables
]) ?>
<b><?= $this->_('chooseprefix') ?></b><br>
<br>
<?php if (!empty($tables)): ?>
<?= sprintf($this->_('tablelist'), $database_name) ?>
<ul>
<?php foreach ($tables as $table): ?>
    <li><?= $this->e($table) ?></li>
<?php endforeach ?>
</ul>
<?php else: ?>
<?= sprintf($this->_('emptytablelist'), $database_name) ?><br>
<?php endif ?>
<label for="table-prefix"><?= $this->_('enterprefix') ?></label><br>
<input id="table-prefix" type="text" name="table-prefix" value="<?= $this->e($table_prefix) ?>" class="inst_button"> (<?= $this->_('dontchange') ?>)<br>
<br>
<input id="database-clear" type="checkbox" name="database-clear" value="true"<?= ($database_overwrite ? ' checked="checked"': '') ?>>
<label for="database-clear"><?= $this->_('deleteexisting') ?></label>
