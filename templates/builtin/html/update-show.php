<b><?= $this->_('updateinfo') ?></b><br>
<br>
<table width="100%" border="0" cellspacing="0" cellpadding="3">
    <tr>
        <td><?= $this->_('reqver') ?></td>
        <td><b><?= $this->e($update->OldVersion) ?></b></td>
    </tr>
    <tr>
        <td><?= $this->_('newver') ?></td>
        <td><b><?= $this->e($update->NewVersion) ?></b></td>
    </tr>
    <tr>
        <td><?= $this->_('author') ?></td>
        <td><b><?= $this->e($update->Author) ?></b></td>
    </tr>
    <tr>
        <td><?= $this->_('date') ?></td>
        <td><b><?= $this->e($update->Date) ?></b></td>
    </tr>
    <tr>
        <td><?= $this->_('executable') ?></td>
        <td><b><?= ($update->AllowUpdate() ? $this->_('yes') : $this->_('no')) ?></b></td>
    </tr>
    <tr>
        <td><?= $this->_('notes') ?></td>
        <td><b><?= ($update->Notes ? $update->Notes : $this->_('na')) ?></b></td>
    </tr>
</table>
