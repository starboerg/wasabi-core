<?php
/**
 * @var \Wasabi\Core\View\AppView $this
 * @var array $lang language
 * @var integer $key
 */
?><tr<?= $class ?>>
    <td class="center">
        <?= $this->Form->input('Language.' . $key . '.id', ['type' => 'hidden', 'value' => $lang['id']]) ?>
        <?= $this->Form->input('Language.' . $key . '.position', ['type' => 'hidden', 'value' => $lang['position'], 'class' => 'position']) ?>
        <?= $lang['id'] ?>
    </td>
    <td><?= $this->Html->backendLink($lang['name'], '/backend/languages/edit/' . $lang['id'], ['title' => __d('wasabi_core', 'Edit language "{0}"', $lang['name'])]) ?></td>
    <td><?= $lang['iso2'] ?></td>
    <td><?= $lang['iso3'] ?></td>
    <td><?= $lang['lang'] ?></td>
    <td>
        <?php
        $cls = '';
        if ($lang['available_at_frontend'] === true) {
            $cls = ' label--info';
        }
        ?>
        <span class="label<?php echo $cls; ?>">Frontend</span>
        <?php
        $cls = '';
        if ($lang['available_at_backend'] === true) {
            $cls = ' label--info';
        }
        ?>
        <span class="label<?php echo $cls; ?>">Backend</span>
    </td>
    <td class="actions center">
        <?php
        echo $this->Html->link('<i class="icon-move-vertical"></i>', 'javascript:void(0)', ['title' => __d('wasabi_core', 'Change the position of this Language'), 'class' => 'action-sort', 'escapeTitle' => false]);
        if (!in_array($lang['id'], [1, 2])) {
            echo $this->Html->backendConfirmationLink(
                '<i class="icon-delete"></i>',
                '/backend/languages/delete/' . $lang['id'],
                [
                    'escapeTitle' => false,
                    'title' => __d('wasabi_core', 'Delete language "{0}"', $lang['name']),
                    'confirm-message' => __d('wasabi_core', 'Delete language <strong>{0}</strong> ?', $lang['name']),
                    'confirm-title' => __d('wasabi_core', 'Deletion Confirmation')
                ]
            );
        }
        ?>
    </td>
</tr>