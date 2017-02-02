<?php
/**
 * @var \Wasabi\Core\View\AppView $this
 * @var \Wasabi\Core\Model\Entity\User $user
 */

use Wasabi\Core\Wasabi;

$this->set('bodyCssClass', ['support', 'register']);
?>
<?= $this->element('Wasabi/Core.support-image') ?>
<?= $this->Form->create($user, ['novalidate' => 'novalidate']) ?>
    <div class="support-content">
        <h1><?= __d('wasabi_core', 'Register') ?></h1>
        <?php
        echo $this->Flash->render();
        if (Wasabi::setting('Core.User.has_username')) {
            echo $this->Form->input('username', ['label' => __d('wasabi_core', 'Username') . ':']);
        }
        if (Wasabi::setting('Core.User.has_firstname_lastname')) {
            echo $this->Form->input('firstname', ['label' => __d('wasabi_core', 'First Name') . ':']);
            echo $this->Form->input('lastname', ['label' => __d('wasabi_core', 'Last Name') . ':']);
        }
        echo $this->Form->input('email', ['label' => __d('wasabi_core', 'Email') . ':']);
        echo $this->Form->input('password', ['label' => __d('wasabi_core', 'Password') . ':']);
        echo $this->Form->input('password_confirmation', ['label' => __d('wasabi_core', 'Password Confirmation') . ':']);
        if (Wasabi::setting('Core.User.allow_timezone_change')) {
            echo $this->Form->timeZoneSelect('timezone', ['label' => __d('wasabi_core', 'Timezone')]);
        }
        ?>
    </div>
    <div class="form-controls">
        <ul>
            <li><?= $this->Html->link(__d('wasabi_core', 'Or log in.'), [
                'plugin' => 'Wasabi/Core',
                'controller' => 'Users',
                'action' => 'login'
            ]) ?></li>
        </ul>
        <?= $this->Form->button(__d('wasabi_core', 'Submit'), ['class' => 'button blue']) ?>
    </div>
<?= $this->Form->end() ?>
