<?php
/**
 * @var \Wasabi\Core\View\AppView $this
 * @var \Wasabi\Core\Model\Entity\Language $lang language
 * @var integer $key
 */
?><tr>
    <td class="col-id center">
        <?= $this->Form->input($key . '.id', ['type' => 'hidden', 'value' => $lang->id]) ?>
        <?= $this->Form->input($key . '.position', ['type' => 'hidden', 'value' => $lang->position, 'class' => 'position']) ?>
        <?= $lang['id'] ?>
    </td>
    <td class="col-pos center"><?= $this->Html->link('<i class="icon-grab"></i>', 'javascript:void(0)', ['title' => __d('wasabi_core', 'Change the position of this Language'), 'class' => 'action-sort', 'escapeTitle' => false]) ?></td>
    <td class="col-name">
        <?= $this->Guardian->protectedLink(
            $lang->name,
            $this->Route->languagesEdit($lang->id),
            [
                'title' => __d('wasabi_core', 'Edit language "{0}"', $lang->name)
            ]
        ) ?>
    </td>
    <td class="col-iso2"><?= $lang->iso2 ?></td>
    <td class="col-iso3"><?= $lang->iso3 ?></td>
    <td class="col-lang"><?= $lang->lang ?></td>
    <td>
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
    <td class="col-actions center">
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
