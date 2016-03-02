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
$rowCount = 1;
foreach ($controllers as $controller => $actions):
    $actionCount = 1;
    foreach ($actions as $action => $groups):
        $classes = [];
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
                        <input id="<?= $group['permission_id'] ?>Id" type="hidden" value="<?= $group['permission_id'] ?>" name="<?= $group['permission_id'] ?>[id]">
                        <input id="<?= $group['permission_id'] ?>Allowed_" type="hidden" value="0" name="<?= $group['permission_id'] ?>[allowed]">
                        <input id="<?= $group['permission_id'] ?>Allowed" type="checkbox" value="1" name="<?= $group['permission_id'] ?>[allowed]"<?= $group['allowed'] ? 'checked="checked"' : '' ?>>
                        <label for="<?= $group['permission_id'] ?>Allowed"><?= $group['name'] ?></label>
                    </div>
                    <?php endforeach; ?>
            </td>
            <td class="center valign-middle">
                <button class="single-submit button small blue" type="submit">
                    <span><?= __d('wasabi_core', 'Update') ?></span></button>
            </td>
        </tr>
        <?php
        $actionCount++;
        $rowCount++;
    endforeach;
endforeach;
?>

