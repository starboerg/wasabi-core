<?php
/**
 * @var \Wasabi\Core\View\AppView $this
 * @var \Wasabi\Core\Model\Entity\CacheSetting $settings
 * @var array $cacheDurations
 */

$this->Html->setTitle(__d('wasabi_core', 'Cache Settings'));
echo $this->Form->create($settings, ['context' => ['table' => 'Wasabi/Core.CacheSettings'], 'class' => 'no-top-section']);
echo $this->Form->control('enable_caching', [
    'label' => $this->Form->getLabel(
        __d('wasabi_core', 'Enable Caching'),
        __d('wasabi_core', 'Enable or disable View caching for the entire CMS instance.')
    ),
    'options' => [
        '0' => __d('wasabi_core', 'No'),
        '1' => __d('wasabi_core', 'Yes')
    ]
]);
echo $this->Form->control('cache_duration', [
    'label' => $this->Form->getLabel(
        __d('wasabi_core', 'Cache Duration'),
        __d('wasabi_core', 'This is used as a default setting and can be overriden by individual plugins.')
    ),
    'options' => $cacheDurations
]);
echo $this->Html->div('form-controls');
echo $this->Form->button(__d('wasabi_core', 'Save'), ['class' => 'button', 'data-toggle' => 'btn-loading']);
echo $this->Guardian->protectedLink(
    __d('wasabi_core', 'Cancel'),
    $this->Route->settingsCache()
);
echo $this->Html->tag('/div');
echo $this->Form->end();
