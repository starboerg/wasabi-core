<?php
/**
 * @var \Wasabi\Core\View\AppView $this
 * @var int $heartBeatFrequency
 */

use Cake\Core\Configure;

$options = [
    'baseUrl' => $this->Url->build('/backend', true),
    'translations' => [
        'confirmYes' => __d('wasabi_core', 'Yes'),
        'confirmNo' => __d('wasabi_core', 'No')
    ],
    'heartbeat' => $heartBeatFrequency,
    'heartbeatEndpoint' => $this->Url->build('/backend/heartbeat'),
    'cookiePath' => $this->request->getAttribute('base')
];
?>
<?= $this->Asset->js('js/wasabi' . ((Configure::read('debug') === false) ? '.min' : '') .'.js', 'Wasabi/Core') ?>
<?= $this->fetch('backend-js-assets') ?>
<script>
    window.WS.configureModule('Wasabi/Core', <?= json_encode($options) ?>);
<?= $this->fetch('backend-js'); ?>
    window.WS.boot();
</script>
