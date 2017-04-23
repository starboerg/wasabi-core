<?php
/**
 * @var \Wasabi\Core\View\AppView $this
 */
use Wasabi\Core\Wasabi;

?><header id="page-header" class="row">
    <ul class="row">
        <li><?= $this->Html->link(
            Wasabi::getInstanceName(),
            [
                'plugin' => 'Wasabi/Core',
                'controller' => 'Dashboard',
                'action' => 'index'
            ],
            [
                'class' => 'brand'
            ]
        ) ?></li>
        <?= $this->element('Wasabi/Core.Menu/user') ?>
        <?= $this->element('Wasabi/Core.Menu/language') ?>
    </ul>
</header>
