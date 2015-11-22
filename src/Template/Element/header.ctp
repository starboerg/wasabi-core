<?php
/**
 * @var \Wasabi\Core\View\AppView $this
 */
use Wasabi\Core\Config;

?><header id="page-header" class="row">
    <ul class="row">
        <li><a class="nav-toggle" href="javascript:void(0)"><i class="icon-nav"></i></a></li>
        <li><?= $this->Html->link(
            Config::getInstanceName(),
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
