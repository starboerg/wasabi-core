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
use Cake\View\Widget\WidgetInterface;

/**
 * Class SectionWidget
 *
 * Form 'widget' for creating labels.
 *
 * Generally this element is used by other widgets,
 * and FormHelper itself.
 */
class SectionWidget implements WidgetInterface
{
    /**
     * Templates
     *
     * @var \Cake\View\StringTemplate
     */
    protected $_templates;

    /**
     * The template to use.
     *
     * @var string
     */
    protected $_sectionTemplate = 'section';

    /**
     * Constructor.
     *
     * This class uses the following template:
     *
     * - `label` Used to generate the label for a radio button.
     *   Can use the following variables `attrs`, `text` and `input`.
     *
     * @param \Cake\View\StringTemplate $templates Templates list.
     */
    public function __construct($templates)
    {
        $this->_templates = $templates;
    }

    /**
     * Render a label widget.
     *
     * Accepts the following keys in $data:
     *
     * - `text` The text for the label.
     * - `input` The input that can be formatted into the label if the template allows it.
     * - `escape` Set to false to disable HTML escaping.
     *
     * All other attributes will be converted into HTML attributes.
     *
     * @param array $data Data array.
     * @param \Cake\View\Form\ContextInterface $context The current form context.
     * @return string
     */
    public function render(array $data, ContextInterface $context)
    {
        $data += [
            'class' => '',
            'title' => '',
            'description' => ''
        ];

        $class = !empty($data['class']) ? ' ' . $data['class'] : '';

        return $this->_templates->format($this->_sectionTemplate, [
            'title' => $data['title'],
            'description' => $data['description'],
            'class' => $class,
            'attrs' => $this->_templates->formatAttributes(
                $data,
                ['class', 'title', 'description']
            )
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function secureFields(array $data)
    {
        return [];
    }
}
