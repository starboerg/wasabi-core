<?php
/**
 * Wasabi CMS
 * Copyright (c) Frank Förster (http://frankfoerster.com)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Frank Förster (http://frankfoerster.com)
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace Wasabi\Core\View\Widget;

use Cake\View\Form\ContextInterface;

class TextareaWidget extends \Cake\View\Widget\TextareaWidget
{
    /**
     * @inheritdoc
     */
    public function render(array $data, ContextInterface $context)
    {
        $data += [
            'val' => '',
            'name' => '',
            'escape' => true,
            'rows' => 5,
            'templateVars' => [],
            'info' => false,
            'labelInfo' => false
        ];
        return $this->_templates->format('textarea', [
            'name' => $data['name'],
            'value' => $data['escape'] ? h($data['val']) : $data['val'],
            'templateVars' => $data['templateVars'],
            'info' => $data['info'] ? '<small>' . $data['info'] . '</small>' : '',
            'labelInfo' => $data['labelInfo'] ? '<small>' . $data['labelInfo'] . '</small>' : '',
            'attrs' => $this->_templates->formatAttributes(
                $data,
                ['name', 'val']
            )
        ]);
    }
}
