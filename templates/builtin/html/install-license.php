<?php $this->layout('install-frame', [
    'about_handler' => $about_handler,
    'step' => $step
]) ?>
<b><?= $this->_('licagreement') ?></b><br>
<br>
<textarea cols="67" rows="12" readonly="readonly" style="width: 100%"><?= $this->e($license) ?></textarea><br>
<br>
<input id="license-accept"  type="checkbox" name="license-accept" value="true"<?= ($accept ? ' checked="checked"' : '') ?>>
<label for="license-accept" style="vertical-align: baseline; width: auto"><?= $this->_('licread') ?></label>
