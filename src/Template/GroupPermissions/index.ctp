<?php
/**
 * @var \Wasabi\Core\View\AppView $this
 * @var array $plugins
 * @var \Wasabi\Core\Model\Entity\GroupPermission $permission
 */

$this->Html->setTitle(__d('wasabi_core', 'Permissions'));
$this->Html->setSubTitle(__d('wasabi_core', 'Management'));
$this->Html->addAction(
    $this->Guardian->protectedLink(
        '<i class="icon-sync"></i>',
        [
            'plugin' => 'Wasabi/Core',
            'controller' => 'GroupPermissions',
            'action' => 'sync'
        ],
        [
            'title' => __d('wasabi_core', 'Synchronize Permissions'),
            'class' => 'sync',
            'escape' => false
        ])
);
?>
<?= $this->Form->create($permission, [
    'url' => [
        'plugin' => 'Wasabi/Core',
        'controller' => 'GroupPermissions',
        'action' => 'update'
    ],
    'class' => 'permissions-update-form'
]) ?>
    <table class="list permissions valign-middle">
        <thead>
        <tr>
            <th class="t-5-16"><?= __d('wasabi_core', 'Controller') ?></th>
            <th class="t-5-16"><?= __d('wasabi_core', 'Action') ?></th>
            <th class="t-4-16"><?= __d('wasabi_core', 'Permissions') ?></th>
            <th class="t-2-16 center"><?= __d('wasabi_core', 'Update') ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($plugins as $plugin => $controllers) {
            echo $this->element('../GroupPermissions/__groupPermission-row', [
                'plugin' => $plugin,
                'controllers' => $controllers
            ]);
        }
        ?>
        </tbody>
    </table>
    <div class="form-controls">
        <?php
        echo $this->Form->button(__d('wasabi_core', 'Update all'), array('div' => false, 'class' => 'button'));
        echo $this->Guardian->backendLink(__d('wasabi_core', 'Cancel'), [
            'plugin' => 'Wasabi/Core',
            'controller' => 'GroupPermissions',
            'action' => 'index'
        ]);
        ?>
    </div>
<?= $this->Form->end(); ?>