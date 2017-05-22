<?php
/**
 * @var \Wasabi\Core\View\AppView $this
 * @var array $summaryBoxes
 */
$this->Html->setTitle(__d('wasabi_core', 'Dashboard'));
?>
<?php if (!empty($summaryBoxes)): ?>
    <div class="summary-boxes row">
        <?php
        foreach ($summaryBoxes as $id => $summaryBox) {
            $cell = $this->cell($summaryBox['cell'], ['options' => $summaryBox]);
            echo $cell->render();
        }
        ?>
    </div>
<?php endif; ?>
