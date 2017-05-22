<?php
/**
 * @var \Wasabi\Core\View\AppView $this
 * @var array $class
 * @var boolean $render
 * @var string $iconClass
 * @var string $title
 * @var string $unit
 * @var string $link
 * @var string $linkTitle
 * @var mixed $value
 */

$tag = !empty($link) ? 'a' : 'div';
$href = !empty($link) ? ' href="' . $link . '"' : '';
$linkTitle = (!empty($link) && !empty($linkTitle)) ? ' title="' . $linkTitle . '"' : '';

if (empty($render) || $render !== false ):
?>
<<?= $tag . $href ?> class="summary-box<?= !empty($class) ? ' ' . join(' ', $class) : '' ?>"<?= $linkTitle ?>>
    <div class="summary-box-content">
        <?php if (!empty($iconClass)): ?>
        <div class="summary-box-icon"><i class="<?= $iconClass ?>"></i></div>
        <?php endif; ?>
        <?php if (!empty($title)): ?>
        <div class="summary-box-title"><?= $title ?></div>
        <?php endif; ?>
        <?php if (!empty($unit)): ?>
        <div class="summary-box-unit"><?= $unit ?></div>
        <?php endif; ?>
        <?php if (!empty($value)): ?>
        <div class="summary-box-value"><?= $value ?></div>
        <?php endif; ?>
    </div>
</<?= $tag ?>>
<?php
endif;
