<?php $this->layout('install-frame', [
    'about_handler' => $about_handler,
    'step' => $step
]) ?>
<b><?= $this->_('completing') ?></b><br>
<br>
<?= sprintf($this->_('completingtxt'), $download_url) ?>
