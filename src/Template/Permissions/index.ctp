<?php
/**
 * @var \Wasabi\Core\View\AppView $this
 * @var array $permissions
 * @var \Cake\ORM\ResultSet $groups
 * @var \Wasabi\Core\Model\Entity\Group $superAdminGroup
 */

use Wasabi\Core\Permission\Permission;
use Wasabi\Core\Permission\PermissionGroup;

$this->Html->setTitle(__d('wasabi_core', 'Permissions'));
$this->Html->setSubTitle(__d('wasabi_core', 'N: No | O: Own | Y: Yes'));
$this->assign('title', __d('wasabi_core', 'Permissions'));
$groupCount = count($groups->toArray());
?>

<?= $this->Form->create(null, ['url' => $this->Route->permissionsUpdate()]) ?>

<div class="datatable-wrapper">
    <table class="datatable valign-middle">
        <thead>
        <tr>
            <th class="section" colspan="2"><?= __d('wasabi_core', 'Section/Action') ?></th>
            <th class="groups" colspan="<?= $groupCount + 1 ?>"><?= __d('wasabi_core', 'User Groups') ?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($permissions as $permissionGroup) { /** @var PermissionGroup $permissionGroup */?>
            <tr>
                <td class="section" colspan="2"><?= $permissionGroup->getName() ?></td>
                <td class="section group"><?= $superAdminGroup->name ?></td>
                <?php foreach ($groups as $group) { ?>
                    <td class="section group"><?= $group ?></td>
                <?php } ?>
            </tr>
            <?php foreach ($permissionGroup->getPermissions() as $permission) { /** @var Permission $permission */ ?>
                <tr>
                    <td>&nbsp;</td>
                    <td><?= $permission->getName() ?></td>
                    <td class="permission">
                        <div class="btn-group" data-toggle="buttons">
                            <?= $this->Form->control('permissions.1.' . $permission->getId(), [
                                'label' => false,
                                'div' => false,
                                'options' => $permission->getHighestPermissionOption(),
                                'type' => 'radio',
                                'templates' => 'Wasabi/Core.FormTemplates/btn_radio',
                                'value' => (string)$permission->getHighestPermissionOption(true),
                                'checked' => true
                            ]); ?>
                        </div>
                    </td>
                    <?php foreach ($groups as $groupId => $group) {
                        $field = 'permissions.' . $groupId . '.' . $permission->getId(); ?>
                        <td class="permission">
                            <div class="btn-group" data-toggle="buttons">
                                <?= $this->Form->control($field, [
                                    'label' => false,
                                    'div' => false,
                                    'options' => $permission->getSelectOptions(),
                                    'type' => 'radio',
                                    'templates' => 'Wasabi/Core.FormTemplates/btn_radio',
                                    'value' => (string)$this->request->getData($field)
                                ]); ?>
                            </div>
                        </td>
                    <?php } ?>
                </tr>
            <?php } ?>
        <?php } ?>
        </tbody>
    </table>
</div>

<div class="form-controls">
    <?php
    echo $this->Form->button(__d('wasabi_core', 'Save'), ['class' => 'button', 'data-toggle' => 'btn-loading']);
    echo $this->Guardian->protectedLink(
        __d('wasabi_core', 'Cancel'),
        $this->Route->permissionsIndex()
    );
    ?>
</div>

<?= $this->Form->end(); ?>
