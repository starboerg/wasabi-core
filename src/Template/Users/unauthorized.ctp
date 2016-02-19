<?php
/**
 * @var \Wasabi\Core\View\AppView $this
 */
$this->Html->setTitle(__d('wasabi_core', 'Access Denied'));

$flashMsg = $this->Flash->render('auth');

if (!empty($flashMsg)) {
    echo $flashMsg;
} else {
    echo __d('wasabi_core', 'You are not authorized to access that location.');
}
