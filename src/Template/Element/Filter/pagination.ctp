<?php
/**
 * @var \App\View\AppView $this
 *
 * // Set via FilterHelper
 * @var integer $total Total number of available pages
 * @var array|boolean $first The url of the first page.
 * @var array|boolean $prev The url of the previous page.
 * @var array $pages The array of individual page links.
 * @var array $next The url of the next page.
 * @var array $last The url of the last page.
 * @var string $baseUrl The current url without query params.
 * @var integer $from
 * @var integer $to
 *
 * // Set via controller action
 * @var string $itemType The plural item name of the items which are paginated.
 * @var array $limitOptions
 */
?>
<span class="item-count"><?php echo $from . ' - ' . $to . ' ' . __d('wasabi_core', 'of') . ' ' . $total . ' ' . $itemType ?></span>
<nav>
    <ul class="row">
        <li class="first"><?php
            if ($first !== false) {
                $url = $first;
                $class = '';
                $title = __d('wasabi_core', 'go to first page');
            } else {
                $url = 'javascript:void(0)';
                $class = 'disabled';
                $title = '';
            }
            echo $this->Html->link('<i class="icon-first"></i>', $url, array('title' => $title, 'escapeTitle' => false, 'class' => $class));
        ?></li>
        <li class="prev"><?php
            if ($prev !== false) {
                $url = $prev;
                $class = '';
                $title = __d('wasabi_core', 'go to previous page');
            } else {
                $url = 'javascript:void(0)';
                $class = 'disabled';
                $title = '';
            }
            echo $this->Html->link('<i class="icon-previous"></i>', $url, array('title' => $title, 'escapeTitle' => false, 'class' => $class));
        ?></li>
        <li class="pages">
            <ul>
                <?php foreach ($pages as $p): ?>
                    <li<?php echo $p['active'] ? ' class="active"' : '' ?>><?php echo $this->Html->link($p['page'], $p['url'], array('title' => __d('wasabi_core', 'go to page {0}', $p['page']))) ?></li>
                <?php endforeach; ?>
            </ul>
        </li>
        <li class="next"><?php
            if ($next !== false) {
                $url = $next;
                $class = '';
                $title = __d('wasabi_core', 'go to next page');
            } else {
                $url = 'javascript:void(0)';
                $class = 'disabled';
                $title = '';
            }
            echo $this->Html->link('<i class="icon-next"></i>', $url, array('title' => $title, 'escapeTitle' => false, 'class' => $class));
        ?></li>
        <li class="last"><?php
            if ($last !== false) {
                $url = $last;
                $class = '';
                $title = __d('wasabi_core', 'go to last page');
            } else {
                $url = 'javascript:void(0)';
                $class = 'disabled';
                $title = '';
            }
            echo $this->Html->link('<i class="icon-last"></i>', $url, array('title' => $title, 'escapeTitle' => false, 'class' => $class));
        ?></li>
    </ul>
</nav>
<?= $this->Form->input('l', array(
    'id' => false,
    'label' => false,
    'options' => $this->paginationParams['limits'],
    'class' => 'limit',
    'data-page' => $this->paginationParams['page'],
    'data-url' => $baseUrl,
    'templates' => 'Wasabi/Core.form_templates_filter'
)) ?>