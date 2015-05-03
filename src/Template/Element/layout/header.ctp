<?php
/**
 * @var \Wasabi\Core\View\AppView $this
 */
?><header class="row">
    <ul class="row">
        <li><a class="toggle-nav" href="javascript:void(0)"><i class="icon-nav"></i></a></li>
        <li><?= $this->Html->backendUnprotectedLink('wasabi', '/backend', array('class' => 'brand')) ?></li>
        <?php #$this->element('Wasabi/Core.menus/top_nav') ?>
        <?= $this->element('Wasabi/Core.menu/user') ?>
        <?= $this->element('Wasabi/Core.menu/language') ?>
    </ul>
</header>