<?php
$class = ['flash-message'];
if (!empty($params['type'])) {
    $class[] = 'flash-message--' . $params['type'];
}
if (!empty($params['class'])) {
    $class[] = $params['class'];
}
?><div class="<?= h(join(' ', $class)) ?>"><?= $message ?><a href="javascript:void(0)" class="dismiss-flash" title="<?= __d('wasabi_core', 'Dismiss this Message') ?>"><i class="icon-cross"></i></a></div>