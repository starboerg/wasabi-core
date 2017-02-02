<?php

namespace Wasabi\Core\Model\Enum;

use Wasabi\Core\Enum\EnumTrait;

class Permission
{
    use EnumTrait;

    const NO = [
        'name' => 'N',
        'value' => 0
    ];

    const OWN = [
        'name' => 'O',
        'value' => 1
    ];

    const ALL = [
        'name' => 'A',
        'value' => 2
    ];
}
