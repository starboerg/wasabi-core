<?php
/**
 * @var \Wasabi\Core\View\AppView $this
 */

use Cake\Core\Configure;
use Wasabi\Core\Wasabi;

?><!DOCTYPE html>
<html class="no-js" lang="<?= Configure::read('backendLanguage')->iso2 ?>">
<head>
    <?= $this->element('Wasabi/Core.Layout/head') ?>
</head>
<body class="<?= trim(join(' ', [$this->get('sectionCssClass', ''), $this->get('sidebarCollapsed', '')])) ?>">
<?= $this->element('Wasabi/Core.header') ?>
<div id="wrapper">
    <nav class="sidebar">
        <a class="sidebar--open-handle" href="javascript:void(0)"><i class="sidebar--open-handle-icon icon-angle-double-left"></i></a>
        <div class="sidebar--logo">
            <button class="sidebar--navigation-toggle"><span class="sidebar--navigation-toggle-lines"></span></button>
            <span class="sidebar--logo-short"><?= Wasabi::getInstanceShortName() ?></span>
            <span class="sidebar--logo-full"><?= Wasabi::getInstanceName() ?></span>
        </div>
        <div class="sidebar--menu-wrapper" data-init="gm-scrollbar">
            <div class="gm-scrollbar -vertical"><div class="thumb"></div></div>
            <div class="gm-scrollbar -horizontal"><div class="thumb"></div></div>
            <div class="gm-scroll-view">
                <ul class="sidebar--menu menu">
                    <?= $this->cell('Wasabi/Core.Menu', ['backend.main']) ?>
                </ul>
            </div>
        </div>
    </nav>
    <div id="content">
        <?php if (in_array('content-area', $this->blocks())): ?>
            <?= $this->fetch('content-area') ?>
        <?php else: ?>
        <div class="content--wrapper" data-init="gm-scrollbar">
            <div class="gm-scrollbar -vertical"><div class="thumb"></div></div>
            <div class="gm-scrollbar -horizontal"><div class="thumb"></div></div>
            <div class="gm-scroll-view">
                <div class="content--padding">
                    <?= $this->Flash->render('auth') ?>
                    <?= $this->Html->titlePad() ?>
                    <?= $this->Flash->render('flash') ?>
                    <?= $this->fetch('content') ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->element('Wasabi/Core.JavaScript/templates.hbs') ?>
<?= $this->fetch('js-templates') ?>
<?= $this->element('Wasabi/Core.JavaScript/setup') ?>
</body>
</html>
