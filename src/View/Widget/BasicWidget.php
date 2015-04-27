<?php
namespace Wasabi\Core\View\Widget;

use Cake\View\Form\ContextInterface;
use Cake\View\Widget\WidgetInterface;

/**
 * Basic input class.
 *
 * This input class can be used to render basic simple
 * input elements like hidden, text, email, tel and other
 * types.
 */
class BasicWidget implements WidgetInterface
{

    /**
     * StringTemplate instance.
     *
     * @var \Cake\View\StringTemplate
     */
    protected $_templates;

    /**
     * Constructor.
     *
     * @param \Cake\View\StringTemplate $templates Templates list.
     */
    public function __construct($templates)
    {
        $this->_templates = $templates;
    }

    /**
     * Render a text widget or other simple widget like email/tel/number.
     *
     * This method accepts a number of keys:
     *
     * - `name` The name attribute.
     * - `val` The value attribute.
     * - `escape` Set to false to disable escaping on all attributes.
     *
     * Any other keys provided in $data will be converted into HTML attributes.
     *
     * @param array $data The data to build an input with.
     * @param \Cake\View\Form\ContextInterface $context The current form context.
     * @return string
     */
    public function render(array $data, ContextInterface $context)
    {
        $data += [
            'name' => '',
            'val' => null,
            'type' => 'text',
            'escape' => true,
            'info' => false,
            'labelInfo' => false,
        ];
        $data['value'] = $data['val'];
        unset($data['val']);

        return $this->_templates->format('input', [
            'name' => $data['name'],
            'type' => $data['type'],
            'attrs' => $this->_templates->formatAttributes(
                $data,
                ['name', 'type']
            ),
            'info' => $data['info'] ? '<small>' . $data['info'] . '</small>' : '',
            'labelInfo' => $data['labelInfo'] ? '<small>' . $data['labelInfo'] . '</small>' : ''
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function secureFields(array $data)
    {
        return [$data['name']];
    }
}
