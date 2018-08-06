<?php
/**
 * @var \Wasabi\Core\View\AppView $this
 * @var \Wasabi\Core\Model\Entity\Language $language
 * @var \Cake\ORM\ResultSet $languages
 */

$this->Html->setTitle(__d('wasabi_core', 'Languages'));
$this->Html->setSubTitle(__d('wasabi_core', 'for Frontend & Backend'));
$this->Html->addAction(
    $this->Guardian->protectedLink(
        $this->Icon->addPlus(),
        $this->Route->languagesAdd(),
        [
            'title' => __d('wasabi_core', 'Create a new Language'),
            'class' => 'add',
            'escape' => false
        ]
    )
);
?>
<?= $this->Form->create($language, ['url' => $this->Route->apiLanguagesSort()]) ?>
<div class="datatable-wrapper">
    <table class="datatable languages valign-middle">
        <thead>
        <tr>
            <th class="t-1-16 center">ID</th>
            <th class="t-1-16 center">Pos.</th>
            <th class="t-3-16"><?= __d('wasabi_core', 'Language') ?></th>
            <th class="t-2-16"><?= __d('wasabi_core', 'ISO 639-1') ?></th>
            <th class="t-2-16"><?= __d('wasabi_core', 'ISO 639-2/T') ?></th>
            <th class="t-2-16"><?= __d('wasabi_core', 'HTML lang') ?></th>
            <th class="t-3-16"><?= __d('wasabi_core', 'Availability') ?></th>
            <th class="t-2-16 center"><?= __d('wasabi_core', 'Actions') ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($languages as $key => $lang) {
            echo $this->element('../Languages/__language-row', [
                'key' => $key,
                'lang' => $lang
            ]);
        }
        ?>
        </tbody>
    </table>
</div>
<?= $this->Form->end() ?>
<div class="bottom-links">
    <p><?= __d('wasabi_core', 'More ISO codes can be found <a href="http://en.wikipedia.org/wiki/List_of_ISO_639-1_codes">here</a>.') ?></p>
</div>
