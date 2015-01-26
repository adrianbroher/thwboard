<b>Updates</b><br>
<br>
<?php if (!empty($updates)): ?>
<?= $this->_('selectupdate') ?><br>
<label for="update-selected"><?= $this->_('availableupdates') ?></label><br>
<select id="update-selected" class="inst_button" name="update-selected" size="6">
<?php foreach ($updates as $update): ?>
    <option value="<?= $this->e($update) ?>"><?= $this->e($update) ?></option>
<?php endforeach ?>
</select>
<?php else: ?>
<?= $this->_('noupdates') ?>
<?php endif ?>
