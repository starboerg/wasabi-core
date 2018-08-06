<?php

namespace Wasabi\Core\Form;

use Cake\Form\Form;

class FilterForm extends Form
{
    /**
     * Set the form data.
     *
     * @param $data
     */
    public function setData($data)
    {
        foreach ($data as $field => $value) {
            $this->schema()->addField($field, [
                'type' => gettype($value),
                'default' => $value
            ]);
        }
    }
}
