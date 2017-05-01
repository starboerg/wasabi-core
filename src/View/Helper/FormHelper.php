<?php
/**
 * Wasabi Core
 * Copyright (c) Frank Förster (http://frankfoerster.com)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Frank Förster (http://frankfoerster.com)
 * @link          https://github.com/wasabi-cms/core Wasabi Project
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace Wasabi\Core\View\Helper;

use DateTime;
use DateTimeZone;

class FormHelper extends \Cake\View\Helper\FormHelper
{
    /**
     * Generates an group template element
     *
     * @param array $options The options for group template
     * @return string The generated group template
     */
    protected function _groupTemplate($options)
    {
        $groupTemplate = $options['options']['type'] . 'FormGroup';
        if (!$this->templater()->get($groupTemplate)) {
            $groupTemplate = 'formGroup';
        }

        return $this->formatTemplate($groupTemplate, [
            'input' => $options['input'] ?? '',
            'label' => $options['label'],
            'error' => $options['error'],
            'templateVars' => $this->_processTemplateVars($options)
        ]);
    }

    /**
     * Process templateVars to wrap info elements in a small tag and
     * apply the "for" attribute to form row labels.
     *
     * @param array $options The input options
     * @return array
     */
    protected function _processTemplateVars(array $options)
    {
        $templateVars = isset($options['options']['templateVars']) ? $options['options']['templateVars'] : [];

        if (empty($templateVars)) {
            return $templateVars;
        }

        if (isset($templateVars['formRowLabel']) && $options['options']['id'] !== false) {
            $templateVars['formRowFor'] = ' for="' . $options['options']['id'] . '"';
        }

        $wrapInSmall = [
            'formRowLabelInfo',
            'formRowInfo',
            'info'
        ];

        foreach ($wrapInSmall as $attr) {
            if (isset($templateVars[$attr])) {
                $templateVars[$attr] = '<small>' . $templateVars[$attr] . '</small>';
            }
        }

        return $templateVars;
    }

    /**
     * Get the options array for a label.
     * This handles the optional placement of an info text below the label.
     *
     * @param string $text The label text.
     * @param null|string $info The optional label info.
     * @return array
     */
    public function getLabel($text, $info = null)
    {
        $options = [
            'text' => $text,
            'templateVars' => []
        ];

        if ($info !== null) {
            $options['templateVars']['labelInfo'] = '<small>' . $info . '</small>';
        }

        return $options;
    }

    /**
     * Render a grouped time zone select box.
     *
     * @param string $field The input field name.
     * @param array $options Additional options for the generated select.
     * @return string
     */
    public function timeZoneSelect($field, array $options = [])
    {
        $regions = [
            'Europe' => DateTimeZone::EUROPE,
            'America' => DateTimeZone::AMERICA,
            'Africa' => DateTimeZone::AFRICA,
            'Antarctica' => DateTimeZone::ANTARCTICA,
            'Asia' => DateTimeZone::ASIA,
            'Atlantic' => DateTimeZone::ATLANTIC,
            'Indian' => DateTimeZone::INDIAN,
            'Pacific' => DateTimeZone::PACIFIC
        ];
        $timezones = [];
        foreach ($regions as $name => $mask) {
            $zones = DateTimeZone::listIdentifiers($mask);
            foreach ($zones as $timezone) {
                // Lets sample the time there right now
                $time = new DateTime(null, new DateTimeZone($timezone));
                // Us dumb Americans can't handle millitary time
                // Remove region name and add a sample time
                $timezones[$name][] = [
                    'value' => $timezone,
                    'text' => 'UTC ' . $time->format('P') . ' ' . substr($timezone, strlen($name) + 1)
                ];
            }
        }
        $options['options'] = $timezones;
        return $this->input($field, $options);
    }

    /**
     * Creates a toggleSwitch input widget.
     *
     * ### Options:
     *
     * - `value` - the value of the underlaying checkbox
     * - `checked` - boolean to indicate that this toggleSwitch is on.
     * - `hiddenField` - boolean to indicate if you want the results of toggleSwitch() to include
     *    a hidden input with a value of ''.
     * - `disabled` - create a disabled input.
     * - `default` - Set the default value for the toggleSwitch. This allows you to start toggleSwitches
     *    as checked, without having to check the POST data. A matching POST data value, will overwrite
     *    the default value.
     *
     * @param string $fieldName Name of a field, like this "modelname.fieldname"
     * @param array $options Array of HTML attributes.
     * @return string|array An HTML text input element.
     */
    public function toggleSwitch($fieldName, array $options = [])
    {
        $options += ['hiddenField' => true, 'value' => 1, 'id' => true];

        // Work around value=>val translations.
        $value = $options['value'];
        unset($options['value']);
        $options = $this->_initInputField($fieldName, $options);
        $options['value'] = $value;

        $output = '';
        if ($options['hiddenField']) {
            $hiddenOptions = [
                'name' => $options['name'],
                'value' => ($options['hiddenField'] !== true && $options['hiddenField'] !== '_split' ? $options['hiddenField'] : '0'),
                'form' => isset($options['form']) ? $options['form'] : null,
                'secure' => false
            ];
            if (isset($options['disabled']) && $options['disabled']) {
                $hiddenOptions['disabled'] = 'disabled';
            }
            $output = $this->hidden($fieldName, $hiddenOptions);
        }

        if ($options['hiddenField'] === '_split') {
            unset($options['hiddenField'], $options['type']);
            return ['hidden' => $output, 'input' => $this->widget('checkbox', $options)];
        }
        unset($options['hiddenField'], $options['type']);
        return $output . $this->widget('toggleSwitch', $options);
    }
}
