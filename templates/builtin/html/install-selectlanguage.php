<b>Welcome to phpInstaller!</b><br>
<br>
<label for="language">Please select your language:</label><br>
<br>
<select id="language" name="lang" class="inst_button">
<?php foreach ($languages as $key => $language): ?>
    <option <?= (($key === 'en') ? 'selected="selected" ': '') ?>value="<?= $this->e($key) ?>"><?= $this->e($language['desc']) ?></option>
<?php endforeach ?>
</select>
