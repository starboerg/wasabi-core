<?php
/**
 * @var \Wasabi\Core\View\AppView $this
 */
?>
<!doctype html>
<!--[if lt IE 7]>     <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en-US"><![endif]-->
<!--[if IE 7]>        <html class="no-js lt-ie9 lt-ie8" lang="en-US"><![endif]-->
<!--[if IE 8]>        <html class="no-js lt-ie9" lang="en-US"><![endif]-->
<!--[if gt IE 8]><!--><html class="no-js" lang="en-US"><!--<![endif]-->
<head>
    <?= $this->element('layout/head') ?>
</head>
<body<?= $this->get('bodyCssClass') ? ' class="' . implode(' ', $this->get('bodyCssClass')) . '"' : '' ?>>
<div class="support-wrapper">
    <?= $this->fetch('content'); ?>
</div>
</body>
</html>