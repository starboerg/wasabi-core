<?php
/**
 * @var \Wasabi\Core\View\AppView $this
 * @var \Wasabi\Core\Model\Entity\Language $lang language
 * @var integer $key
 */
?><tr>
    <td class="col-id center" data-title="<?= __d('wasabi_core', 'ID') ?>">
        <?= $this->Form->control($key . '.id', ['type' => 'hidden', 'value' => $lang->id]) ?>
        <?= $this->Form->control($key . '.position', ['type' => 'hidden', 'value' => $lang->position, 'class' => 'position']) ?>
        <?= $lang['id'] ?>
    </td>
    <td class="col-pos center" data-title="<?= __d('wasabi_core', 'Sort') ?>"><?= $this->Html->link('<i class="icon-grab"></i>', 'javascript:void(0)', ['title' => __d('wasabi_core', 'Change the position of this Language'), 'class' => 'action-sort', 'escapeTitle' => false]) ?></td>
    <td class="col-name" data-title="<?= __d('wasabi_core', 'Language') ?>">
        <?= $this->Guardian->protectedLink(
            $lang->name,
            $this->Route->languagesEdit($lang->id),
            [
                'title' => __d('wasabi_core', 'Edit language "{0}"', $lang->name)
            ]
        ) ?>
    </td>
    <td class="col-iso2" data-title="<?= __d('wasabi_core', 'ISO 639-1') ?>"><?= $lang->iso2 ?></td>
    <td class="col-iso3" data-title="<?= __d('wasabi_core', 'ISO 639-2/T') ?>"><?= $lang->iso3 ?></td>
    <td class="col-lang" data-title="<?= __d('wasabi_core', 'HTML lang') ?>"><?= $lang->lang ?></td>
    <td data-title="<?= __d('wasabi_core', 'Availablitiy') ?>">
        <?php
        $cls = '';
        if ($lang->available_at_frontend === true) {
            $cls = ' label--info';
        }
        ?>
        <span class="label<?php echo $cls; ?>">Frontend</span>
        <?php
        $cls = '';
        if ($lang->available_at_backend === true) {
            $cls = ' label--info';
        }
        ?>
        <span class="label<?php echo $cls; ?>">Backend</span>
    </td>
    <td class="col-actions center" data-title="<?= __d('wasabi_core', 'Actions') ?>">
        <?php
        if (!in_array($lang->id, [1, 2])) {
            echo $this->Guardian->protectedConfirmationLink(
                $this->Icon->delete(),
                $this->Route->languagesDelete($lang->id),
                [
                    'title' => __d('wasabi_core', 'Delete language "{0}"', $lang->name),
                    'confirm-message' => __d('wasabi_core', 'Delete language <strong>{0}</strong> ?', $lang->name),
                    'confirm-title' => __d('wasabi_core', 'Deletion Confirmation'),
                    'escapeTitle' => false
                ]
            );
        } else {
            echo '-';
        }
        ?>
    </td>
</tr>
