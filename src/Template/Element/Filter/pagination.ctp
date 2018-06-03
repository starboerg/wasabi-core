<?php
/**
 * @var Wasabi\Core\View\AppView $this
 *
 * // Set via FilterHelper
 * @var integer $total The toal number of items that are paginated.
 * @var string $itemType The plural item name of the items which are paginated.
 * @var array|boolean $first The url of the first page.
 * @var array|boolean $prev The url of the previous page.
 * @var array $pages The array of individual page links.
 * @var array $next The url of the next page.
 * @var array $last The url of the last page.
 * @var array $class An optional css class for the pagination.
 * @var int $currentPage The currently active page.
 * @var string $baseUrl The current url without query params.
 * @var integer $from The item number the current page starts with.
 * @var integer $to The item number the current page ends with.
 * @var integer $nrOfPages The total number of available pages.
 *
 * // Set via controller action
 * @var array $limitOptions
 */
?>
<span class="item-count"><?php echo $from . ' - ' . $to . ' ' . __d('wasabi_core', 'of') . ' ' . $total . ' ' . $itemType ?></span>
<nav>
    <ul class="row">
        <li class="first">
            <?php
                if ($first !== false) {
                    $url = $first;
                    $class = '';
                    $title = __d('wasabi_core', 'go to first page');
                } else {
                    $url = 'javascript:void(0)';
                    $class = 'disabled';
                    $title = '';
                }
                echo $this->Html->link(
                    $this->Icon->firstPage(),
                    $url,
                    [
                        'title' => $title,
                        'class' => $class,
                        'escapeTitle' => false
                    ]
                );
            ?>
        </li>
        <li class="prev">
            <?php
                if ($prev !== false) {
                    $url = $prev;
                    $class = '';
                    $title = __d('wasabi_core', 'go to previous page');
                } else {
                    $url = 'javascript:void(0)';
                    $class = 'disabled';
                    $title = '';
                }
                echo $this->Html->link(
                    $this->Icon->previousPage(),
                    $url,
                    [
                        'title' => $title,
                        'class' => $class,
                        'escapeTitle' => false
                    ]
                );
            ?>
        </li>
        <?php foreach ($pages as $p): ?>
            <?php if ($p['active']): ?>
                <li class="active"><?= $this->Html->link('<span class="current-page">' . $p['page'] . '</span> <span class="nr-of-pages">/ ' . $nrOfPages . '</span>', 'javascript:void(0)', ['escapeTitle' => false, 'title' => __d('wasabi_core', 'current page')]) ?></li>
            <?php else: ?>
                <li><?= $this->Html->link($p['page'], $p['url'], ['title' => __d('wasabi_core', 'go to page {0}', $p['page'])]) ?></li>
            <?php endif; ?>
        <?php endforeach; ?>
        <li class="next">
            <?php
                if ($next !== false) {
                    $url = $next;
                    $class = '';
                    $title = __d('wasabi_core', 'go to next page');
                } else {
                    $url = 'javascript:void(0)';
                    $class = 'disabled';
                    $title = '';
                }
                echo $this->Html->link(
                    $this->Icon->nextPage(),
                    $url,
                    [
                        'title' => $title,
                        'class' => $class,
                        'escapeTitle' => false
                    ]
                );
            ?>
        </li>
        <li class="last">
            <?php
                if ($last !== false) {
                    $url = $last;
                    $class = '';
                    $title = __d('wasabi_core', 'go to last page');
                } else {
                    $url = 'javascript:void(0)';
                    $class = 'disabled';
                    $title = '';
                }
                echo $this->Html->link(
                    $this->Icon->lastPage(),
                    $url,
                    [
                        'title' => $title,
                        'class' => $class,
                        'escapeTitle' => false
                    ]
                );
            ?>
        </li>
    </ul>
</nav>
<?= $this->Form->control('l', [
    'id' => false,
    'label' => false,
    'options' => $this->Filter->paginationParams['limits'],
    'class' => 'limit',
    'data-page' => $this->Filter->paginationParams['page'],
    'data-url' => $baseUrl,
    'templates' => 'Wasabi/Core.FormTemplates/filter'
]) ?>
