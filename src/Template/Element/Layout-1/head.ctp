<?php
/**
 * @var \Wasabi\Core\View\AppView $this
 */

use Cake\Core\Configure;

echo $this->Html->charset();
echo $this->Html->meta('viewport', 'width=device-width, initial-scale=1.0');
echo $this->Html->tag('title', $this->fetch('title'));
echo $this->Html->meta('icon');
echo $this->Asset->css('core' . (!Configure::read('debug') ? '.min' : ''), 'Wasabi/Core');
