<?php $this->layout('install-frame', [
    'about_handler' => $about_handler,
    'language' => $language,
    'variables' => $variables
]) ?>
<b><?= $title ?></b><br>
<br>
<?= $message ?><br>
<br>
<?php if (!empty($back_url)): ?>
<a href="<?= htmlspecialchars($back_url) ?>"><?= lng('back') ?></a>
<?php endif ?>
