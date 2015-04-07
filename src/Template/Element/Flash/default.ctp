<?php
$class = 'flash-message';
if (!empty($params['class'])) {
    $class .= ' flash-message-' . $params['class'];
}
?>
<div class="<?= h($class) ?>"><?= $message ?></div>
