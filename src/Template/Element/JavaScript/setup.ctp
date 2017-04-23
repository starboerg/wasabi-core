<?php
/**
 * @var \Wasabi\Core\View\AppView $this
 * @var int $heartBeatFrequency
 */

use Cake\Core\Configure;
use Cake\Routing\Router;

$debugJavascript = (Configure::read('debug') && Configure::read('debugJS'));
?>
<?= $this->Asset->js('js/require.js', 'Wasabi/Core') ?>
<?php if (!$debugJavascript): ?>
<?= $this->Asset->js('js/common.js', 'Wasabi/Core') ?>
<?= $this->Asset->js('js/wasabi.js', 'Wasabi/Core') ?>
<?= $this->fetch('js-files') ?>
<?php endif; ?>
<script>
    <?php
    if ($debugJavascript): ?>
    require.config(<?= json_encode([
        'baseUrl' => Router::url('/wasabi/core/ASSETS/js'),
        'urlArgs' => 't=' . time()
    ]) ?>);
    require(['common'], function() {
    <?php endif; ?>
        require(['wasabi'], function(WS) {
            WS.registerModule('wasabi.core', {
                baseUrl: '<?= $this->Url->build('/backend', true) ?>',
                translations: {
                    confirmYes: '<?= __d('wasabi_core', 'Yes') ?>',
                    confirmNo: '<?= __d('wasabi_core', 'No') ?>'
                },
                heartbeat: <?= $heartBeatFrequency ?>,
                heartbeatEndpoint: '<?= $this->Url->build('/backend/heartbeat') ?>',
                cookiePath: '<?= $this->request->getAttribute('base') ?>'
            });
<?= $this->fetch('requirejs') ?>
            WS.boot();
        });
    <?php if ($debugJavascript): ?>
    });
    <?php endif; ?>
</script>
