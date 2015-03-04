<?php $this->layout('install-frame', [
    'about_handler' => $about_handler,
    'step' => $step
]) ?>
<b>Welcome to phpInstaller!</b><br>
<br>
<p>Please select your language.</p>
<div>
    <label for="language">Language</label>
    <select id="language" name="lang">
<?php foreach ($languages as $key => $language): ?>
        <option <?= (($key === 'en') ? 'selected="selected" ': '') ?>value="<?= $this->e($key) ?>"><?= $this->e($language['desc']) ?></option>
<?php endforeach ?>
    </select>
</div>
