<?php
namespace Wasabi\Core\View\Helper;

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
}
