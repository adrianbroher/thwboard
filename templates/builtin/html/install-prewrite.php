<?php $this->layout('install-frame', [
    'about_handler' => $about_handler,
    'language' => $language,
    'step' => $step,
    'variables' => $variables
]) ?>
<b><?= $this->_('completing') ?></b><br>
<br>
<?= sprintf($this->_('completingtxt'), $download_url) ?>
