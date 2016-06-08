<?php

namespace Wasabi\Core\View\Widget;

use Cake\View\Form\ContextInterface;
use Cake\View\Widget\CheckboxWidget;

class ToggleSwitchWidget extends CheckboxWidget
{
    /**
     * The template to use for this widget.
     *
     * @var string
     */
    protected $_template = 'switchToggle';

    /**
     * Render a switchToggle element.
     *
     * Data supports the following keys:
     *
     * - `name` - The name of the input.
     * - `value` - The value attribute. Defaults to '1'.
     * - `val` - The current value. If it matches `value` the checkbox will be checked.
     *   You can also use the 'checked' attribute to make the checkbox checked.
     * - `disabled` - Whether or not the checkbox should be disabled.
     * - `onLabel` - The label for the "on" state.
     * - `offLabel` - The labe for the "off" state.
     *
     * Any other attributes passed in will be treated as HTML attributes.
     *
     * @param array $data The data to create a checkbox with.
     * @param \Cake\View\Form\ContextInterface $context The current form context.
     * @return string Generated HTML string.
     */
    public function render(array $data, ContextInterface $context)
    {
        $data += [
            'value' => 1,
            'val' => null,
            'disabled' => false,
            'onLabel' => 'on',
            'offLabel' => 'off',
            'class' => ''
        ];

        if ($this->_isChecked($data)) {
            $data['checked'] = true;
        }
        unset($data['val']);

        $attrs = $this->_templates->formatAttributes(
            $data,
            ['name', 'value', 'onLabel', 'offLabel', 'type', 'class']
        );

        $checkbox = $this->_templates->format('checkbox', [
            'name' => $data['name'],
            'value' => $data['value'],
            'templateVars' => $data['templateVars'],
            'attrs' => $attrs
        ]);

        $toggleSwitch = $this->_templates->format('toggleSwitch', [
            'checkbox' => $checkbox,
            'onLabel' => $data['onLabel'],
            'offLabel' => $data['offLabel'],
            'attrs' => $this->_templates->formatAttributes(
                $data,
                ['name', 'type', 'value', 'class']
            )
        ]);

        $data['class'] = empty($data['class']) ? 'toggle-switch' : 'toggle-switch ' .$data['class'];
        return $this->_templates->format('nestingLabel', [
            'hidden' => '',
            'input' => $toggleSwitch,
            'attrs' => $this->_templates->formatAttributes(
                $data,
                ['name', 'type', 'value', 'onLabel', 'offLabel', 'id']
            )
        ]);
    }
}
