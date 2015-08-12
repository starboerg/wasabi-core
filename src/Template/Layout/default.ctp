<?php
/**
 * @var \Wasabi\Core\View\AppView $this
 */

use Cake\Core\Configure;

?><!DOCTYPE html>
<html class="no-js" lang="en-US">
<head>
    <?= $this->element('Wasabi/Core.Layout/head') ?>
</head>
<body class="<?= $this->get('sectionCssClass', '') ?>">
<?= $this->element('Wasabi/Core.header') ?>
<div id="wrapper">
    <div id="asidebg"></div>
    <aside>
        <nav id="backend-menu">
            <ul>
                <?= $this->cell('Wasabi/Core.Menu', ['backend.main']) ?>
            </ul>
        </nav>
    </aside>
    <div id="content">
        <?php
        echo $this->Flash->render('auth');
        echo $this->Html->titlePad();
        echo $this->Flash->render('flash');
        echo $this->fetch('content');
        ?>
    </div>
</div>
<?= $this->element('Wasabi/Core.JavaScript/templates.hbs') ?>
<?= $this->element('Wasabi/Core.JavaScript/setup') ?>
</body>
</html>