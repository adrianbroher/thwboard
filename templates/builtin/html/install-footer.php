                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td bgcolor="#808080" height="1"><img src="./images/space.gif" width="1" height="1"></td>
                                        </tr>
                                        <tr>
                                            <td bgcolor="#FFFFFF" height="1"><img src="./images/space.gif" width="1" height="1"></td>
                                        </tr>
                                    </table>
                                    <br>
                                    <table width="100%" border="0" cellspacing="0" cellpadding="16">
                                        <tr>
                                            <td align="right">
<?php if (!empty($action)): ?>
                                                <input type="submit" name="next" value="<?= $this->_('next') ?> &gt;" class="inst_button">
<?php endif ?>
<?php foreach ($variables as $name => $value): ?>
                                                <input type="hidden" name="<?= $this->e($name) ?>" value="<?= $this->e($value) ?>">
<?php endforeach ?>
<?php if (!empty($language)): ?>
                                                <input type="hidden" name="lang" value="<?= $this->e($language) ?>">
<?php endif ?>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td bgcolor="#808080" width="1"></td>
                    <td bgcolor="#000000" width="1"></td>
                </tr>
                <tr>
                    <td bgcolor="#D4D0C8" height="1" width="1"></td>
                    <td bgcolor="#FFFFFF" height="1" width="1"></td>
                    <td bgcolor="#808080" height="1"></td>
                    <td bgcolor="#808080" height="1" width="1"><img src="./images/space.gif" width="1" height="1"></td>
                    <td bgcolor="#000000" height="1" width="1"></td>
                </tr>
                <tr bgcolor="#000000">
                    <td height="1" width="1"></td>
                    <td height="1" width="1"></td>
                    <td height="1"></td>
                    <td height="1" width="1"></td>
                    <td height="1" width="1"><img src="./images/space.gif" width="1" height="1"></td>
                </tr>
            </table>
        </form>
    </body>
</html>
