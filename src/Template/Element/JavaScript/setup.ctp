<?php
/**
 * @var \Wasabi\Core\View\AppView $this
 */

use Cake\Core\Configure;
use Cake\Routing\Router;
?>
<?= $this->Asset->js('require', 'Wasabi/Core') ?>
<?php if (!Configure::read('debug')): ?>
<?= $this->Asset->js('common', 'Wasabi/Core') ?>
<?= $this->Asset->js('wasabi', 'Wasabi/Core') ?>
<?php endif; ?>
<script>
    <?php
    if (Configure::read('debug')): ?>
    require.config(<?= json_encode([
        'baseUrl' => Router::url('/wasabi_core/js'),
        'urlArgs' => 't=' . time()
    ]) ?>);
    <?php endif; ?>
    require(['common'], function() {
        require(['wasabi'], function(WS) {
            WS.registerModule('wasabi.core', {
                baseUrl: '<?= $this->Url->build('/backend', true) ?>',
                translations: {
                    confirmYes: '<?= __d('wasabi_core', 'Yes') ?>',
                    confirmNo: '<?= __d('wasabi_core', 'No') ?>'
                }
            });
            WS.boot();
        });
    });
</script>