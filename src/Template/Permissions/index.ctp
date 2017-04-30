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
$this->Html->setSubTitle(__d('wasabi_core', 'N: No | O: Own | A: All'));
$this->assign('title', __d('wasabi_core', 'Permissions'));
$permissionOptions = Permission::getSelect();
$groupCount = count($groups->toArray());
?>

<?= $this->Form->create(null, ['url' => ['action' => 'update']]) ?>

<div class="datatable-wrapper">
    <table class="datatable valign-middle">
        <thead>
        <tr>
            <th class="section" colspan="2" rowspan="2"><?= __d('wasabi_core', 'Section/Action') ?></th>
            <th class="groups" colspan="<?= $groupCount + 1 ?>"><?= __d('wasabi_core', 'User Groups') ?></th>
        </tr>
        <tr>
            <th class="group"><div class="rotate"><span><?= $superAdminGroup->name ?></span></div></th>
            <?php foreach ($groups as $group) { ?>
                <th class="group"><div class="rotate"><span><?= $group ?></span></div></th>
            <?php } ?>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($permissions as $permissionGroup) { /** @var PermissionGroup $permissionGroup */?>
            <tr><td class="section" colspan="<?= 3 + $groupCount ?>"><?= $permissionGroup->getName() ?></td></tr>
            <?php foreach ($permissionGroup->getPermissions() as $permission) { /** @var Permission $permission */ ?>
                <tr>
                    <td>&nbsp;</td>
                    <td><?= $permission->getName() ?></td>
                    <td class="permission">
                        <div class="btn-group" data-toggle="buttons">
                            <?= $this->Form->input('permissions.1.' . $permission->getId(), [
                                'label' => false,
                                'div' => false,
                                'options' => array(end($permissionOptions)),
                                'type' => 'radio',
                                'templates' => 'Wasabi/Core.FormTemplates/btn_radio',
                                'value' => (string)Permission::getValueForName(Permission::ALL['name']),
                                'checked' => true
                            ]); ?>
                        </div>
                    </td>
                    <?php foreach ($groups as $groupId => $group) {
                        $field = 'permissions.' . $groupId . '.' . $permission->getId(); ?>
                        <td class="permission">
                            <div class="btn-group" data-toggle="buttons">
                                <?= $this->Form->input($field, [
                                    'label' => false,
                                    'div' => false,
                                    'options' => $permissionOptions,
                                    'type' => 'radio',
                                    'templates' => 'Wasabi/Core.FormTemplates/btn_radio',
                                    'value' => (string)$this->request->data($field)
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
    echo $this->Form->button(__d('wasabi_core', 'Save'), ['div' => false, 'class' => 'button']);
    echo $this->Guardian->protectedLink(__d('wasabi_core', 'Cancel'), [
        'plugin' => 'Wasabi/Core',
        'controller' => 'GroupPermissions',
        'action' => 'index'
    ]);
    ?>
</div>

<?= $this->Form->end(); ?>
