<?php
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
            'input' => $options['input'],
            'label' => $options['label'],
            'error' => $options['error'],
            'templateVars' => $this->_processTemplateVars($options)
        ]);
    }

    /**
     * Process templateVars to wrap info elements in a small tag and
     * apply the "for" attribute to form row labels.
     *
     * @param array $options
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
     * @param string $text
     * @param null|string $info
     * @return array
     */
    public function getLabel($text, $info = null)
    {
        $options = [
            'text' => $text,
            'templateVars' => []
        ];

        if ($info) {
            $options['templateVars']['labelInfo'] = '<small>' . $info . '</small>';
        }

        return $options;
    }

    /**
     * Render a grouped time zone select box.
     *
     * @param string $field
     * @param array $options
     * @return string
     */
    public function timeZoneSelect($field, array $options = [])
    {
        $regions = array(
            'Europe' => DateTimeZone::EUROPE,
            'America' => DateTimeZone::AMERICA,
            'Africa' => DateTimeZone::AFRICA,
            'Antarctica' => DateTimeZone::ANTARCTICA,
            'Asia' => DateTimeZone::ASIA,
            'Atlantic' => DateTimeZone::ATLANTIC,
            'Indian' => DateTimeZone::INDIAN,
            'Pacific' => DateTimeZone::PACIFIC
        );
        $timezones = array();
        foreach ($regions as $name => $mask)
        {
            $zones = DateTimeZone::listIdentifiers($mask);
            foreach($zones as $timezone)
            {
                // Lets sample the time there right now
                $time = new DateTime(NULL, new DateTimeZone($timezone));
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
}
