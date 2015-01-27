<?php $this->layout('install-frame', [
    'about_handler' => $about_handler,
    'step' => $step
]) ?>
<b><?= $this->_('selectdb') ?></b><br>
<br>
<?= $this->_('choosedb') ?><br>
<br>
<input id="database-allocation-use" name="database-allocation" type="radio" value="use"<?= (($allocation == 'use') ? ' checked="checked"' : '') ?>>
<label for="database-allocation-use"><?= $this->_('use') ?></label>
<label for="database-name-use"><?= $this->_('existingdb') ?></label><br>
<select id="database-name-use" name="database-name-use" size="6" class="inst_button">
<?php foreach ($databases as $database): ?>
    <option <?= (($allocation == 'use' && $database == $database_name) ? 'selected="selected" ' : '') ?>value="<?= $this->e($database) ?>"><?= $this->e($database) ?></option>
<?php endforeach ?>
</select>
<br>
<br>
<input id="database-allocation-new" name="database-allocation" type="radio" value="new"<?= (($allocation == 'new') ? ' checked="checked"' : '') ?>>
<label for="database-allocation-new"><?= $this->_('create') ?></label>
<label for="database-name-new"><?= $this->_('newdbname') ?></label><br>
<input id="database-name-new" type="text" name="database-name-new" class="inst_button" value="<?= (($allocation == 'new') ? $this->e($database_name) : '') ?>">
