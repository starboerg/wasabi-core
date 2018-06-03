<?php
/**
 * @var \Wasabi\Core\View\AppView $this
 * @var \Wasabi\Core\Model\Entity\Group $group
 * @var \Cake\ORM\Query $groups
 * @var \Cake\ORM\Query $users
 */

$this->Html->setTitle(__d('wasabi_core', 'Move existing Member(s)'));
$this->Html->setSubTitle($group->name);
$this->assign('title', __d('wasabi_core', 'Move existing Member(s)'));

echo $this->Form->create($group, ['class' => 'no-top-section', 'type' => 'post']);
    /** @var \Wasabi\Core\Model\Entity\User $user */
    foreach ($users as $user) {
        echo $this->Form->control('alternative_group_id.' . $user->id, [
            'label' => $user->fullName(),
            'options' => $groups,
            'empty' => __d('wasabi_core', 'Please choose...'),
            'templateVars' => [
                'info' => __d('wasabi_core', 'Please select a group <strong>{0}</strong> should be assigned to.', $user->fullName())
            ]
        ]);
    }
    echo $this->Html->div('form-controls');
        echo $this->Form->button(__d('wasabi_core', 'Move Members & Delete Group'), ['class' => 'button red', 'data-toggle' => 'btn-loading']);
        echo $this->Guardian->protectedLink(
            __d('wasabi_core', 'Cancel'),
            $this->Filter->getBacklink($this->Route->groupsIndex())
        );
    echo $this->Html->tag('/div');
echo $this->Form->end();
