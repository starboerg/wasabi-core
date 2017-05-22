<?php
/**
 * @var \Wasabi\Core\View\AppView $this
 */
use Wasabi\Core\Wasabi;

?><header id="page-header" class="row">
    <ul class="row">
        <li>
            <button class="sidebar--navigation-toggle"><span class="sidebar--navigation-toggle-lines"></span></button>
            <?= $this->Html->link(
                Wasabi::getInstanceName(),
                $this->Route->dashboardIndex(),
                ['class' => 'brand']
            ) ?>
        </li>
        <?= $this->element('Wasabi/Core.Menu/user') ?>
        <?= $this->element('Wasabi/Core.Menu/language') ?>
    </ul>
</header>
