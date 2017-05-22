<?php
/**
 * @var \Wasabi\Core\View\AppView $this
 */
use Cake\Core\Configure;

?>
<!doctype html>
<html class="no-js" lang="<?= Configure::read('backendLanguage')->iso2 ?>">
<head>
    <?= $this->element('Wasabi/Core.Layout/head') ?>
</head>
<body class="<?= trim(join(' ', [$this->get('sectionCssClass', ''), join(' ', $this->get('bodyCssClass', ''))])) ?>">
<div class="support-wrapper">
    <?= $this->fetch('content'); ?>
</div>
</body>
</html>
