<?php

use Cake\Core\Configure;

echo $this->Html->charset();
echo $this->Html->meta('viewport', 'width=device-width, initial-scale=1.0');
echo $this->Html->tag('title', $this->fetch('title'));
echo $this->Html->meta('icon');
echo $this->Asset->css('core' . (!Configure::read('debug') ? '.min' : ''), 'Wasabi/Core');
echo $this->fetch('head_css'); ?>
<link rel='stylesheet' id='open-sans-css'  href='https://fonts.googleapis.com/css?family=Open+Sans%3A300italic%2C400italic%2C600italic%2C300%2C400%2C600&#038;subset=latin%2Clatin-ext&#038;ver=4.3.1' type='text/css' media='all' />
<script type="text/javascript">
    document.documentElement.className = document.documentElement.className.replace('no-js','js');
</script>
