<?php $this->layout('install-frame', [
    'about_handler' => $about_handler,
    'step' => $step
]) ?>
<b><?= $this->_('updateinfo') ?></b><br>
<br>
<table summary="Attributes of the update to be installed" width="100%" border="0" cellspacing="0" cellpadding="3">
    <tr>
        <th><?= $this->_('reqver') ?></th>
        <td><?= $this->e($update->fromVersion) ?></td>
    </tr>
    <tr>
        <th><?= $this->_('newver') ?></th>
        <td><?= $this->e($update->toVersion) ?></td>
    </tr>
    <tr>
        <th><?= $this->_('author') ?></th>
        <td><?= $this->e($update->author) ?></td>
    </tr>
    <tr>
        <th><?= $this->_('executable') ?></th>
        <td><?= (($update->fromVersion == $schema_version) ? $this->_('yes') : $this->_('no')) ?></td>
    </tr>
    <tr>
        <th><?= $this->_('notes') ?></th>
        <td><?= ($update->notes ? $update->notes : $this->_('na')) ?></td>
    </tr>
</table>
