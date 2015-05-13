<?php
/**
 * @var \Wasabi\Core\View\AppView $this
 * @var string $plugin plugin
 * @var array $controllers controllers
 */
?>
<tr class="plugin">
    <td colspan="4"><?= $plugin ?></td>
</tr>
<?php
$rowCount = $i = 1;
foreach ($controllers as $controller => $actions):
    $actionCount = 1;
    foreach ($actions as $action => $groups):
        $classes = array();
        if (($rowCount % 2) == 0) {
            $classes[] = 'even';
        }
        if ($actionCount === count($actions)) {
            $classes[] = 'last-action';
        }
        $classes = !empty($classes) ? ' class="' . implode(' ', $classes) . '"' : '';
        ?>
        <tr<?= $classes ?>>
            <?php if ($actionCount === 1): ?>
                <td class="controller" rowspan="<?= count($actions) ?>"><?= $controller ?></td>
            <?php endif; ?>
            <td class="action"><?= $action ?></td>
            <td>
                <?php foreach ($groups as $groupId => $group): ?>
                    <div>
                        <input id="<?= $i ?>Id" type="hidden" value="<?= $group['permission_id'] ?>"name="<?= $i ?>[id]">
                        <input id="<?= $i ?>Allowed_" type="hidden" value="0" name="<?= $i ?>[allowed]">
                        <input id="<?= $i ?>Allowed" type="checkbox" value="1" name="<?= $i ?>[allowed]"<?= $group['allowed'] ? 'checked="checked"' : '' ?>>
                        <label for="<?= $i ?>Allowed"><?= $group['name'] ?></label>
                    </div>
                    <?php $i++; endforeach; ?>
            </td>
            <td class="center valign-middle">
                <button class="single-submit button small blue" type="submit">
                    <span><?= __d('core', 'Update') ?></span></button>
            </td>
        </tr>
        <?php
        $actionCount++;
        $rowCount++;
    endforeach;
endforeach;
?>

