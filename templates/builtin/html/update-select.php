<?php $this->layout('install-frame', [
    'about_handler' => $about_handler,
    'step' => $step
]) ?>
<b>Updates</b><br>
<br>
<?php if (!empty($updates)): ?>
<p><?= $this->_('selectupdate') ?></p>
<label for="update-selected"><?= $this->_('availableupdates') ?></label>
<select id="update-selected" class="inst_button" name="update-selected" size="6">
<?php foreach ($updates as $file => $update): ?>
    <option value="<?= $this->e($file) ?>"><?= $this->e($update) ?></option>
<?php endforeach ?>
</select>
<?php else: ?>
<?= $this->_('noupdates') ?>
<?php endif ?>
