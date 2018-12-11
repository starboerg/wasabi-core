<?php
/**
 * Copyright (c) Frank Förster (http://frankfoerster.com)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Frank Förster (http://frankfoerster.com)
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace Wasabi\Core\View\Helper;

use Cake\Form\Form;
use Cake\Routing\Router;
use Cake\Utility\Hash;
use Cake\View\Helper;
use Cake\View\View;
use Cake\View\Helper\HtmlHelper;
use Wasabi\Core\Controller\Component\FilterComponent;
use Wasabi\Core\Form\FilterForm;

/**
 * FilterHelper
 *
 * @property View _View
 * @property HtmlHelper Html
 */
class FilterHelper extends Helper
{
    /**
     * Helpers used by this helper.
     *
     * @var array
     */
    public $helpers = [
        'Html'
    ];

    /**
     * Holds the instantiated filter component.
     *
     * @var FilterComponent
     */
    protected $filterComponent;

    /**
     * Constructor
     *
     * @param View $View The view initializing the Filter helper instance.
     * @param array $config Configuration options passed to the constructor.
     */
    public function __construct(View $View, array $config = [])
    {
        $filterComponent = Hash::get($View->viewVars, 'filter', false);

        if ($filterComponent !== false) {
            $this->filterComponent = $filterComponent;
        }

        parent::__construct($View, $config);
    }

    /**
     * Get the filter form context.
     *
     * @return Form
     */
    public function getContext()
    {
        $filterForm = new FilterForm();
        $filterForm->setData($this->filterComponent->getFilterManager()->getFilters());

        return $filterForm;
    }

    /**
     * Render a sort link for the given $field and $options, with $name as link text/content.
     *
     * @param string $name
     * @param string $field
     * @param array $options
     * @return string
     */
    public function sortLink($name, $field, $options = [])
    {
        $url = $this->getSortUrl($field);

        $iClass = 'icon-sortable';
        if ($this->filterComponent->sortIsActiveOn($field)) {
            $iClass .= '__' . $this->filterComponent->getFilterManager()->getSortByField($field);
        }

        if (isset($options['icon-theme'])) {
            $iClass .= ' ' . $options['icon-theme'];
            unset($options['icon-theme']);
        }

        $name = '<span>' . $name . '</span><i class="' . $iClass . '"></i>';
        $options['escape'] = false;

        return $this->Html->link($name, $url, $options);
    }

    /**
     * Render the pagination.
     *
     * @param int $maxPageNumbers The maximum number of pages to show in the link list.
     * @param string $itemType The item type that is paginated.
     * @param string $class An optional css class for the pagination.
     * @param string $element The pagination element to render.
     * @param array $passParams Optional params passed to the filter urls.
     * @return string
     */
    public function pagination($maxPageNumbers = 10, $itemType = 'Items', $class = '', $element = 'Filter/pagination')
    {
        $paginationData = $this->filterComponent->getPaginationDate();

        if (empty($paginationData)) {
            return '';
        }

        $page = (integer)$paginationData['page'];
        $pages = (integer)$paginationData['pages'];
        $pagesOnLeft = floor($maxPageNumbers / 2);
        $pagesOnRight = $maxPageNumbers - $pagesOnLeft - 1;
        $minPage = $page - $pagesOnLeft;
        $maxPage = $page + $pagesOnRight;
        $firstUrl = false;
        $prevUrl = false;
        $pageLinks = [];
        $nextUrl = false;
        $lastUrl = false;

        if ($page > 1) {
            $firstUrl = $this->getPaginatedUrl(1);
            $prevUrl = $this->getPaginatedUrl($page - 1);
        }
        if ($minPage < 1) {
            $minPage = 1;
            $maxPage = $maxPageNumbers;
        }
        if ($maxPage > $pages) {
            $maxPage = $pages;
            $minPage = $maxPage + 1 - $maxPageNumbers;
        }
        if ($minPage < 1) {
            $minPage = 1;
        }
        if ($minPage > $maxPage) {
            $minPage = $maxPage;
        }
        if ($minPage !== 0 && $maxPage !== 0) {
            foreach (range($minPage, $maxPage) as $p) {
                $link = [
                    'page' => (int)$p,
                    'url' => $this->getPaginatedUrl($p),
                    'active' => ((int)$p === $page)
                ];
                $pageLinks[] = $link;
            }
        }
        if ($page < $pages) {
            $nextUrl = $this->getPaginatedUrl($page + 1);
            $lastUrl = $this->getPaginatedUrl($pages);
        }

        return $this->_View->element($element, [
            'total' => $paginationData['totalHits'],
            'itemType' => $itemType,
            'first' => $firstUrl,
            'prev' => $prevUrl,
            'pages' => $pageLinks,
            'next' => $nextUrl,
            'last' => $lastUrl,
            'class' => $class,
            'currentPage' => $page,
            'baseUrl' => Router::url($this->getFilterUrl(false)),
            'from' => $paginationData['from'],
            'to' => $paginationData['to'],
            'nrOfPages' => $pages,
            'limitParamName' => $this->filterComponent->getLimitParam(),
            'limit' => $this->filterComponent->getFilterManager()->getLimit(),
            'limitOptions' => array_combine($this->filterComponent->getLimitOptions(), $this->filterComponent->getLimitOptions())
        ]);
    }

    /**
     * Try to retrieve a filtered backlink from the session.
     *
     * @param array $url proper route array
     * @return array
     */
    public function getBacklink($url)
    {
        return FilterComponent::getBacklink($url, $this->request);
    }

    /**
     * Get a paginated url for the given $page number.
     *
     * @param int $page
     * @return array
     */
    protected function getPaginatedUrl($page)
    {
        $url = $this->getSortUrl();

        if ($page > 1) {
            if (!isset($url['?'])) {
                $url['?'] = [];
            }
            $url['?'][$this->filterComponent->getPageParam()] = $page;
        }

        return $url;
    }

    /**
     * Get a sort url for the given $field.
     *
     * @param string $field The field to sort on.
     * @return array
     */
    protected function getSortUrl(string $field = '')
    {
        $url = $this->getFilterUrl();

        $activeSort = $this->filterComponent->getFilterManager()->getSort();

        if (empty($field)) {
            return $url;
        }

        if (!isset($url['?'])) {
            $url['?'] = [];
        }

        if (isset($activeSort[$field])) {
            $sortString = $activeSort[$field] === 'asc' ? '-' . $field : $field;
        } else {
            $sortString = $field;
        }

        if ($sortString !== $this->filterComponent->getDefaultSort()) {
            $url['?'][$this->filterComponent->getSortParam()] = $sortString;
        }

        return $url;
    }

    /**
     * Get the filter url.
     *
     * @param boolean $withLimit
     * @return array
     */
    protected function getFilterUrl($withLimit = true)
    {
        $url = [
            'plugin' => $this->request->getParam('plugin'),
            'controller' => $this->request->getParam('controller'),
            'action' => $this->request->getParam('action'),
            'prefix' => $this->request->getParam('prefix')
        ];

        foreach($this->filterComponent->getPassedParams() as $name => $value) {
            $url[$name] = $value;
        }

        if (!empty($this->request->getParam('filterSlug'))) {
            $url['filterSlug'] = $this->request->getParam('filterSlug');
        };

        $limit = $this->filterComponent->getFilterManager()->getLimit();

        if ($withLimit && !empty($limit) && $limit !== $this->filterComponent->getDefaultLimit() &&
            $limit !== $this->filterComponent->getFilterManager()->getLimit()
        ) {
            $url['?'][$this->filterComponent->getLimitParam()] = $limit;
        }

        return $url;
    }
}
