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

class SelectBoxWidget extends \Cake\View\Widget\SelectBoxWidget {

    /**
     * @inheritdoc
     */
    public function render(array $data, ContextInterface $context)
    {
        $data += [
            'name' => '',
            'empty' => false,
            'escape' => true,
            'options' => [],
            'disabled' => null,
            'val' => null,
            'info' => false,
            'labelInfo' => false,
        ];

        $options = $this->_renderContent($data);
        $name = $data['name'];
        unset($data['name'], $data['options'], $data['empty'], $data['val'], $data['escape']);
        if (isset($data['disabled']) && is_array($data['disabled'])) {
            unset($data['disabled']);
        }

        $template = 'select';
        if (!empty($data['multiple'])) {
            $template = 'selectMultiple';
            unset($data['multiple']);
        }
        $attrs = $this->_templates->formatAttributes($data);
        return $this->_templates->format($template, [
            'name' => $name,
            'attrs' => $attrs,
            'content' => implode('', $options),
            'info' => $data['info'] ? '<small>' . $data['info'] . '</small>' : '',
            'labelInfo' => $data['labelInfo'] ? '<small>' . $data['labelInfo'] . '</small>' : ''
        ]);
    }
}
