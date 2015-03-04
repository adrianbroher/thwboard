<?php $this->layout('install-frame', [
    'about_handler' => $about_handler,
    'step' => $step
]) ?>
<b><?= $this->_('selectdb') ?></b><br>
<br>
<?= $this->_('choosedb') ?><br>
<br>
<div>
    <input id="database-allocation-use" name="database-allocation" type="radio" value="use"<?= (($allocation == 'use') ? ' checked="checked"' : '') ?>>
    <label for="database-allocation-use" style="vertical-align: baseline; width: auto"><?= $this->_('use') ?></label>
    <label for="database-name-use" style="vertical-align: baseline;"><?= $this->_('existingdb') ?></label>
</div>
<div>
    <select id="database-name-use" name="database-name-use" size="6" class="inst_button">
<?php foreach ($databases as $database): ?>
        <option <?= (($allocation == 'use' && $database == $database_name) ? 'selected="selected" ' : '') ?>value="<?= $this->e($database) ?>"><?= $this->e($database) ?></option>
<?php endforeach ?>
    </select>
</div>
<div>
    <input id="database-allocation-new" name="database-allocation" type="radio" value="new"<?= (($allocation == 'new') ? ' checked="checked"' : '') ?>>
    <label for="database-allocation-new" style="vertical-align: baseline; width: auto"><?= $this->_('create') ?></label>
    <label for="database-name-new" style="vertical-align: baseline;"><?= $this->_('newdbname') ?></label>
</div>
<div>
    <input id="database-name-new" type="text" name="database-name-new" class="inst_button" value="<?= (($allocation == 'new') ? $this->e($database_name) : '') ?>">
</div>
