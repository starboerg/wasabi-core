<?php
/**
 * @var \Wasabi\Core\View\AppView $this
 * @var \Wasabi\Core\Model\Entity\Group $group
 */
use Wasabi\Core\Wasabi;

?><tr>
    <td class="col-id center" data-title="<?= __d('wasabi_core', 'ID') ?>"><?= $group->id ?></td>
    <td class="col-name" data-title="<?= __d('wasabi_core', 'Group') ?>">
        <?= $this->Guardian->protectedLink(
            $group->name,
            $this->Route->groupsEdit($group->id),
            ['title' => __d('wasabi_core', 'Edit Group "{0}"', $group->name)],
            true
        ) ?>
    </td>
    <td class="col-description" data-title="<?= __d('wasabi_core', 'Description') ?>"><?= $group->description ?></td>
    <td class="col-user-count" data-title="<?= __d('wasabi_core', '# Users') ?>"><?= $group->user_count ?></td>
    <td class="col-actions center" data-title="<?= __d('wasabi_core', 'Actions') ?>">
        <?php
        echo $this->Guardian->protectedLink(
            $this->Icon->edit(),
            $this->Route->groupsEdit($group->id),
            [
                'title' => __d('wasabi_core', 'Edit Group "{0}"', $group->name),
                'escapeTitle' => false
            ]
        );
        if (Wasabi::user()->can('delete', $group)) {
            echo $this->Guardian->protectedConfirmationLink(
                $this->Icon->delete(),
                $this->Route->groupsDelete($group->id),
                [
                    'title' => __d('wasabi_core', 'Delete Group'),
                    'confirm-message' => __d('wasabi_core', 'Do you really want to delete group <strong>{0}</strong>?', $group->name),
                    'confirm-title' => __d('wasabi_core', 'Confirm Deletion'),
                    'escapeTitle' => false
                ]
            );
        }
        ?>
    </td>
</tr>
