<?php
/**
 * @var \Wasabi\Core\View\AppView $this
 */

use Cake\Core\Configure;

?><!DOCTYPE html>
<html class="no-js" lang="en-US">
<head>
    <?= $this->element('layout/head') ?>
</head>
<body class="<?= $this->get('sectionCssClass', '') ?>">
<?= $this->element('layout/header') ?>
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
<?= $this->Asset->js('require', 'Wasabi/Core') ?>
<?= $this->Asset->js('common', 'Wasabi/Core') ?>
<?= $this->Asset->js('wasabi', 'Wasabi/Core') ?>
<script>
    require(['wasabi', 'common'], function(WS) {
        WS.registerModule('wasabi.core', {
            baseUrl: '<?= $this->Url->build('/backend', true) ?>'
        });
        WS.boot();
    });
</script>
</body>
</html>