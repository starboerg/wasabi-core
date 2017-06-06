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

if (empty($render) || $render !== false ): ?>
<div class="summary-box<?= !empty($class) ? ' ' . join(' ', $class) : '' ?>">
    <?= empty($link) ?: '<a class="summary-box-link" href="' .  $link . '"' . (empty($linkTitle) ?: ' title="' . $linkTitle . '"') . '>' ?>
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
    <?= empty($link) ?: '</a>' ?>
</div>
<?php
endif;
