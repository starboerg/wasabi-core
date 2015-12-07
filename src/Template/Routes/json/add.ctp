<?php
/**
 * @var \Wasabi\Core\Model\Entity\Route[] $routes
 * @var array $routeTypes
 * @var string $model
 * @var string $element
 */
ob_start();
echo $this->element($element, [
    'routes' => $routes,
    'routeTypes' => $routeTypes,
    'model' => $model,
    'element' => $element
]);
$out = ob_get_clean();

echo json_encode(['content' => $out]);
