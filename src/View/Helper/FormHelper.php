<?php
namespace Wasabi\Core\View\Helper;

use DateTime;
use DateTimeZone;

class FormHelper extends \Cake\View\Helper\FormHelper {

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
        return $this->templater()->format($groupTemplate, [
            'input' => $options['input'],
            'label' => $options['label'],
            'error' => $options['error'],
            'formRowLabel' => isset($options['options']['formRowLabel']) ? $options['options']['formRowLabel'] : ''
        ]);
    }

    public function timeZoneSelect($field, array $options = []) {
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
