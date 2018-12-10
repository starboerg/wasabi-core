<?php

namespace Wasabi\Core\Controller\Component;

use Cake\Controller\Component;
use Cake\Datasource\ModelAwareTrait;
use Cake\Http\ServerRequest;
use Cake\ORM\Query;
use Wasabi\Core\Filter\Exception\FilterableTraitNotAppliedException;
use Wasabi\Core\Filter\FilterManager;
use Wasabi\Core\Filter\FilterResult;
use Wasabi\Core\Model\Table\FiltersTable;

/**
 * Class FilterComponent
 *
 * @property FiltersTable Filters
 */
class FilterComponent extends Component
{
    use ModelAwareTrait;

    /**
     * Holds the filter manager instance as soon as the filter method is called.
     *
     * @var FilterManager
     */
    protected $filterManager;

    /**
     * Holds the default sort option.
     *
     * @var bool|string
     */
    protected $defaultSort = false;

    /**
     * Holds the default limit option.
     *
     * @var int
     */
    protected $defaultLimit = 10;

    /**
     * Holds the pagination data after a successful filter operation.
     *
     * @var array
     */
    protected $paginationData = [];

    /**
     * Holds the configured limit parameter name.
     *
     * @var string
     */
    protected $limitParam;

    /**
     * Holds the configured page parameter name.
     *
     * @var string
     */
    protected $pageParam;

    /**
     * Holds the configured slug parameter name.
     *
     * @var string
     */
    protected $slugParam;

    /**
     * Holds the configured sort parameter name.
     *
     * @var string
     */
    protected $sortParam;

    /**
     * Holds the configured association strategy.
     *
     * @see FilterManager::$strategy
     * @var string
     */
    protected $associationStrategy;

    /**
     * Filter query contains filterable associations
     */
    const FILTER_STRATEGY_CONTAIN = 'contain';

    /**
     * Filter query adds filterable associations by subqueries.
     */
    const FILTER_STRATEGY_SUBQUERY = 'subquery';

    /**
     * Initialization hook method.
     *
     * @param array $config
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->loadModel('Wasabi/Core.Filters');

        $this->limitParam = $this->getConfig($this->getAction() . '.limit.param');
        $this->pageParam = $this->getConfig($this->getAction() . '.pagination.param');
        $this->sortParam = $this->getConfig($this->getAction() . '.sort.param');
        $this->associationStrategy = $this->getConfig($this->getAction() . '.association.strategy');
        $this->defaultSort = $this->getDefaultSort();
        $this->defaultLimit = $this->getDefaultLimit();
        $this->slugParam = $this->getConfig($this->getAction() . '.params.slug');

        $slug = $this->getRequest()->getParam($this->slugParam, '');
        $queryParams = empty($slug) ? [] : $this->Filters->findFilterDataForSlug($slug);
        $queryParams = $queryParams + $this->getRequest()->getQueryParams();

        if (!isset($queryParams[$this->sortParam])) {
            $queryParams[$this->sortParam] = $this->defaultSort;
        }

        if (!isset($queryParams[$this->limitParam])) {
            $lastLimit = $this->getRequest()->getSession()->read(join('.', [
                'LIMIT_' . $this->getPluginName(),
                $this->getPrefix(),
                $this->getControllerName(),
                $this->getAction()
            ]));
            if ($lastLimit) {
                $queryParams[$this->limitParam] = $lastLimit;
            } else {
                $queryParams[$this->limitParam] = $this->defaultLimit;
            }
        }

        $this->filterManager = new FilterManager($queryParams, [
            'limitParamName' => $this->limitParam,
            'pageParamName' => $this->pageParam,
            'sortParamName' => $this->sortParam,
            'strategy' => $this->associationStrategy,
            'handledParams' => $this->getFilterFields(),
            'ignoredParams' => $this->getConfig($this->getAction() . '.params.ignore'),
            'allowedSortFields' => $this->getSortFields()
        ]);
    }

    /**
     * Called after Controller::beforeFilter() and before the controller action.
     *
     * @return void
     */
    public function startup()
    {
        if (!$this->isFilterEnabled()) {
            return;
        }

        $filters = $this->getFiltersFromPostRequest();
        if (empty($filters)) {
            return;
        }

        $redirectUrl = $this->createSluggedUrl($filters);
        $this->getController()->redirect($redirectUrl);
    }

    /**
     * beforeRender callback
     *
     * Is called after the controller executes the requested action’s logic, but before the controller renders the view and layout.
     *
     * - Save the filter, sort and pagination params to the session.
     * - Can be later retrieved via FilterHelper::getBacklink($url)
     *
     * @return void
     */
    public function beforeRender()
    {
        $this->storeFilterOptionsInSession();

        $this->getController()->set('filter', $this);
    }

    /**
     * Filter the given $query with the filter data represented by the $slug.
     *
     * @param Query $query
     * @param string $slug
     * @return Query
     * @throws FilterableTraitNotAppliedException
     */
    public function filter(Query $query)
    {
        if (!method_exists($query->getRepository(), 'getFilterResult')) {
            throw new FilterableTraitNotAppliedException('Table "' . get_class($query->getRepository()) . '" must use the trait "Filterable" to be filtered.');
        }

        /** @var FilterResult $filterResult */
        $filterResult = $query->getRepository()->getFilterResult($this->filterManager, $query);

        $this->paginationData = $filterResult->getPaginationData();

        return $filterResult->getQuery();
    }

    /**
     * Get the pagination data.
     *
     * @return array
     */
    public function getPaginationDate(): array
    {
        return $this->paginationData;
    }

    /**
     * Try to retrieve a filtered backlink from the session.
     *
     * @param array $url
     * @param ServerRequest $request
     * @return array
     */
    public static function getBacklink($url, ServerRequest $request)
    {
        if (!isset($url['plugin'])) {
            $url['plugin'] = $request->getParam('plugin');
        }
        if (!isset($url['controller'])) {
            $url['controller'] = $request->getParam('controller');
        }
        if (!isset($url['prefix'])) {
            $url['prefix'] = $request->getParam('prefix');
        }

        $filterPath = join('.', [
            'FILTER_' . ($url['plugin'] ? $url['plugin'] : ''),
            $url['prefix'] ? $url['prefix'] : '',
            $url['controller'],
            $url['action']
        ]);

        if (($filterOptions = $request->getSession()->read($filterPath))) {
            if (isset($filterOptions['filterSlug'])) {
                $url['filterSlug'] = $filterOptions['filterSlug'];
                unset($filterOptions['filterSlug']);
            }
            if (!empty($filterOptions)) {
                if (!isset($url['?'])) {
                    $url['?'] = [];
                }
                $url['?'] = array_merge($url['?'], $filterOptions);
            }
        }

        return $url;
    }

    /**
     * Get all active filters for the current post request.
     *
     * @return array
     */
    protected function getFiltersFromPostRequest(): array
    {
        if (!$this->getRequest()->is('post')) {
            return [];
        }

        $filters = [];

        foreach ($this->getFilterFields() as $field) {
            $filterValue = $this->getRequest()->getData($field);
            if ($filterValue !== '') {
                $filters[$field] = $filterValue;
            }
        }

        return $filters;
    }

    /**
     * Create a slugged url from the current request and the given $filter array.
     *
     * @param array $filters
     * @return array
     */
    protected function createSluggedUrl(array $filters): array
    {
        $url = ['action' => $this->getAction()];

        if (empty($filters)) {
            return $url;
        }

        $url = $this->_applyPassedParams($url);

        return $url + [
            'filterSlug' => $this->Filters->findOrCreateSlugForFilterData($this->getRequest(), $filters),
            '?' => $this->getRequest()->getQueryParams()
        ];
    }

    /**
     * Apply configured pass params to the url array.
     *
     * @param array $url
     * @return array modified url array
     */
    protected function _applyPassedParams($url)
    {
        $passParams = $this->getPassedParams();

        if (empty($passParams)) {
            return $url;
        }

        return array_merge($url, $passParams);
    }

    /**
     * Get the passed params.
     *
     * @return array
     */
    public function getPassedParams() {
        $passParams = [];
        foreach ($this->getConfig($this->getAction() . '.params.pass', []) as $key) {
            if (!empty($this->getRequest()->getParam($key))) {
                $passParams[$key] = $this->getRequest()->getParam($key);
            }
        }

        return $passParams;
    }

    /**
     * Store the current filter options in the session.
     *
     * @return void
     */
    protected function storeFilterOptionsInSession()
    {
        if ($this->filterManager === null) {
            return;
        }

        $filterOptions = [];

        $filterSlug = $this->getRequest()->getParam('filterSlug');
        if (!empty($filterSlug)) {
            $filterOptions['filterSlug'] = $filterSlug;
        }

        $sortString = $this->filterManager->getSortString();
        if (!empty($sortString) && $sortString !== $this->defaultSort) {
            $filterOptions[$this->sortParam] = $sortString;
        }

        $page = $this->filterManager->getPage();
        if ($page > 1) {
            $filterOptions[$this->pageParam] = $page;
        }

        $filterPath = join('.', [
            'FILTER_' . $this->getPluginName(),
            $this->getPrefix() ?? '',
            $this->getControllerName(),
            $this->getAction()
        ]);
        $limitPath = str_replace('FILTER_', 'LIMIT_', $filterPath);

        $limit = $this->filterManager->getLimit();
        if ($limit !== $this->defaultLimit) {
            $this->getRequest()->getSession()->write($limitPath, $limit);
        } else {
            $this->getRequest()->getSession()->delete($limitPath);
        }

        if (!empty($filterOptions)) {
            $this->getRequest()->getSession()->write($filterPath, $filterOptions);
        } else {
            $this->getRequest()->getSession()->delete($filterPath);
        }
    }

    /**
     * Get the default limit option for the current action.
     *
     * @return int
     */
    public function getDefaultLimit(): int
    {
        return $this->getConfig($this->getAction() . '.limit.default', 10);
    }

    /**
     * Get the default sort option for the current action.
     *
     * @return string
     */
    public function getDefaultSort(): string
    {
        return $this->getConfig($this->getAction() . '.sort.default', '');
    }

    /**
     * Get the filter manager instance.
     *
     * @return FilterManager
     */
    public function getFilterManager(): FilterManager
    {
        return $this->filterManager;
    }

    /**
     * Get the limit parameter name.
     *
     * @return string
     */
    public function getLimitParam(): string
    {
        return $this->limitParam;
    }

    /**
     * Get all available limit options.
     *
     * @return array
     */
    public function getLimitOptions(): array
    {
        return $this->getConfig($this->getAction() . '.limit.available', []);
    }

    /**
     * Get the page parameter name.
     *
     * @return string
     */
    public function getPageParam(): string
    {
        return $this->pageParam;
    }

    /**
     * Get the sort parameter name.
     *
     * @return string
     */
    public function getSortParam(): string
    {
        return $this->sortParam;
    }

    /**
     * @param string $field
     * @return bool
     */
    public function sortIsActiveOn(string $field): bool
    {
        return ($this->filterManager->getSortByField($field) !== null);
    }

    /**
     * Check if filtering for the current controller action is enabled.
     *
     * @return bool
     */
    protected function isFilterEnabled(): bool
    {
        return !empty($this->getFilterFields());
    }

    /**
     * Get all available filter fields for the current action.
     *
     * @return array
     */
    protected function getFilterFields(): array
    {
        return $this->getConfig($this->getAction() . '.filterFields', []);
    }

    /**
     * Get all available sort fields for the current action.
     *
     * @return array
     */
    protected function getSortFields(): array
    {
        return $this->getConfig($this->getAction() . '.sort.fields', []);
    }

    /**
     * Get the current request’s controller action.
     */
    protected function getAction(): string
    {
        return $this->getRequest()->getParam('action');
    }

    /**
     * Get the current request’s controller name.
     *
     * @return string
     */
    protected function getControllerName(): string
    {
        return $this->getRequest()->getParam('controller');
    }

    /**
     * Get the current request’s prefix.
     *
     * @return null|string
     */
    protected function getPrefix()
    {
        return $this->getRequest()->getParam('pefix');
    }

    /**
     * Get the current request’s plugin name.
     *
     * @return string
     */
    protected function getPluginName(): string
    {
        return $this->getRequest()->getParam('plugin', '');
    }

    /**
     * Get the current request instance.
     *
     * @return \Cake\Http\ServerRequest
     */
    protected function getRequest(): ServerRequest
    {
        return $this->getController()->getRequest();
    }
}
