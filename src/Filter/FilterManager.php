<?php

namespace Wasabi\Core\Filter;

use Cake\Utility\Hash;
use Wasabi\Core\Controller\Component\FilterComponent;

class FilterManager
{
    /**
     * Holds all setup user filters.
     * [$param => $value]
     *
     * @var array
     */
    private $filters = [];

    /**
     * Holds all sort criteria.
     * [$field => $direction]
     *
     * @var array
     */
    private $sort = [];

    /**
     * Holds the sort string that the sort array is created with.
     *
     * @var string
     */
    private $sortString = '';

    /**
     * Holds the item limit per page.
     *
     * @var int
     */
    private $limit = 10;

    /**
     * Holds the requested page.
     *
     * @var int
     */
    private $page = 1;

    /**
     * Holds an array of parameters that should be handled in the filter process.
     *
     * @var array
     */
    private $paramsToHandle = [];

    /**
     * Holds an array of parameters that should be ignored in the filter process.
     *
     * @var array
     */
    private $paramsToIgnore = [];

    /**
     * Holds the page query parameter name.
     *
     * @var string
     */
    private $pageParamName;

    /**
     * Holds the limit parameter name.
     *
     * @var string
     */
    private $limitParamName;

    /**
     * Holds if limitation/pagination is on/off
     *
     * @var bool
     */
    private $paginationActive = true;

    /**
     * Holds the sort parameter name.
     *
     * @var string
     */
    private $sortParamName;

    /**
     * Holds the type of strategy used for filtered associations.
     *
     * Possible values are:
     *
     * 'contain' (default) -> filter query contains filterable associations.
     * 'subquery' -> filter query adds filterable associations by subqueries.
     *
     * @var string
     */
    private $strategy;

    /**
     * Constructor
     *
     * @param array $params The params to be filtered on.
     * @param array $config Provides configuration options for the filter manager.
     */
    public function __construct($params, array $config = [])
    {
        $this->pageParamName = (string)Hash::get($config, 'pageParamName', 'page');
        $this->limitParamName = (string)Hash::get($config, 'limitParamName', 'limit');
        $this->sortParamName = (string)Hash::get($config, 'sortParamName', 'sort');
        $this->paramsToHandle = (array)Hash::get($config, 'handledParams', []);
        $this->paramsToIgnore = (array)Hash::get($config, 'ignoredParams', []);
        $this->strategy = (string)Hash::get($config, 'strategy', FilterComponent::FILTER_STRATEGY_CONTAIN);

        foreach ($params as $param => $value) {
            if ($this->shouldSkipParam($param)) {
                continue;
            }

            $this->setupFilter($param, $value);
        }
    }

    /**
     * Setup a filter for the given $param and $value.
     *
     * @param string $param
     * @param mixed $value
     */
    protected function setupFilter($param, $value)
    {
        switch ($param) {
            case $this->limitParamName:
                $this->limit = (int)$value;
                break;

            case $this->pageParamName:
                $this->page = (int)$value;
                break;

            case $this->sortParamName:
                $this->setupSort($value);
                break;

            default:
                $this->addFilter($param, $value);
        }
    }

    /**
     * Setup the sort options.
     *
     * @param string $value
     * @return void
     */
    protected function setupSort($value)
    {
        $parts = explode(',', $value);

        foreach ($parts as $part) {
            if (preg_match_all('/^(\-)?([\w]+)$/', $part, $matches) > 0) {
                $name = $matches[2][0];
                $order = $matches[1][0];
                $this->sort[$name] = $order === '-' ? 'desc' : 'asc';
            }
        }

        $sortStrings = [];
        foreach ($this->sort as $name => $order) {
            $sortStrings[] = ($order === 'asc' ? '' : '-') . $name;
        }

        $this->sortString = join(',', $sortStrings);
    }

    /**
     * Add a filter for the given $param and $value.
     *
     * @param string $param
     * @param mixed $value
     * @return void
     */
    public function addFilter($param, $value)
    {
        $this->filters[$param] = $value;
    }

    /**
     * Get the limit.
     *
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Is limitation/pagination on?
     *
     * @return bool
     */
    public function isPaginationActive(): bool
    {
        return $this->paginationActive;
    }

    /**
     * Turns on/off limitation/pagination.
     *
     * @param bool $activeLimit
     */
    public function setPaginationActive(bool $paginationActive)
    {
        $this->paginationActive = $paginationActive;
    }

    /**
     * The the page.
     *
     * @return int
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Get all user filters and system filters.
     *
     * @return array
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * Get a specific filter for the given $param.
     *
     * @param string $param
     * @return mixed
     */
    public function getFilter($param)
    {
        return $this->filters[$param] ?? null;
    }

    /**
     * Remove the filter for the given $param.
     *
     * @param string $param
     * @return void
     */
    public function removeFilter($param)
    {
        if (isset($this->filters[$param])) {
            unset($this->filters[$param]);
        }
    }

    /**
     * Get all sort options.
     *
     * @return array
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * Get the active sort definition for the given $field.
     *
     * @param string $field
     * @return null|string
     */
    public function getSortByField(string $field)
    {
        return Hash::get($this->sort, $field, null);
    }

    /**
     * Get the sort string for all applied sort options.
     *
     * @return string
     */
    public function getSortString()
    {
        return $this->sortString;
    }

    /**
     * Gets the type of strategy used for filtered associations.
     *
     * Possible values are:
     * 'contain' -> filter query contains filterable associations
     * 'subquery' (default) -> filter query adds filterable associations by subqueries.
     *
     * @return string
     */
    public function getStrategy(): string
    {
        return $this->strategy;
    }

    /**
     * Check if the given $param should be skipped from filter processing.
     *
     * @param string $param
     * @return bool
     */
    protected function shouldSkipParam($param)
    {
        $allowedParams = [
            $this->pageParamName,
            $this->limitParamName,
            $this->sortParamName
        ];

        $allowedParams = array_merge($allowedParams, $this->paramsToHandle);

        foreach ($this->paramsToIgnore as $ignoreParam) {
            unset($allowedParams[$ignoreParam]);
        }

        return !in_array($param, $allowedParams);
    }
}
