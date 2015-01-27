<?php $this->layout('install-frame', [
    'about_handler' => $about_handler,
    'language' => $language,
    'step' => $step,
    'variables' => $variables
]) ?>
<b><?= $this->_('licagreement') ?></b><br>
<br>
<textarea cols="67" wrap="OFF" rows="12" readonly><?= $this->e($license) ?></textarea><br>
<br>
<input id="license-accept"  type="checkbox" name="license-accept" value="true"<?= ($accept ? ' checked="checked"' : '') ?>>
<label for="license-accept"><?= $this->_('licread') ?></label>
