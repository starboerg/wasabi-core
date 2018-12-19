<?php

namespace Wasabi\Core\Filter;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\Utility\Inflector;
use Dsc\Core\Controller\Component\FilterComponent;
use Wasabi\Core\Filter\Exception\MissingFilterMethodException;
use Wasabi\Core\Filter\Exception\MissingSortMethodException;

class FilterHandler
{
    /**
     * Holds the table instance no which filter and sort operations are performed.
     *
     * @var Table
     */
    private $table;

    /**
     * Holds the filter manager instance.
     *
     * @var FilterManager
     */
    private $filterManager;

    /**
     * Constructor.
     *
     * @param Table $table
     * @param FilterManager $filterManager
     */
    public function __construct(Table $table, FilterManager $filterManager)
    {
        $this->table = $table;
        $this->filterManager = $filterManager;
    }

    /**
     * Apply all filters of the filter manager to the given $query instance based on the
     * {@link FilterManager::getStrategy}.
     *
     * @param Query $query
     * @return int count Count of filtered results
     * @throws MissingFilterMethodException
     */
    public function applyFilters(Query $query)
    {
        if ($this->filterManager->getStrategy() == FilterComponent::FILTER_STRATEGY_CONTAIN) {
            $this->_applyFilters($query);

            return $query->count();
        } else {
            $idsQuery = $this->table->find()->select([$this->table->aliasField('id')]);

            $idsQuery->applyOptions($query->getOptions());

            $this->_applyFilters($idsQuery);

            $query->where([$this->table->aliasField('id') . ' IN' => $idsQuery]);

            return $idsQuery->count();
        }
    }

    /**
     * Apply all filters of the filter manager to the given $query instance.
     *
     * @param Query $query
     * @throws MissingFilterMethodException
     */
    protected function _applyFilters(Query $query)
    {
        foreach ($this->filterManager->getFilters() as $name => $value) {
            if ($value === '') {
                continue;
            }

            $filterMethod = 'filterBy' . Inflector::camelize($name);
            if (!method_exists($this->table, $filterMethod)) {
                throw new MissingFilterMethodException('Filter method "' . $filterMethod . '" on table "' . get_class($this->table) . '" is not implemented.');
            }

            $this->table->{$filterMethod}($query, $value);
        }
    }

    /**
     * Apply all sort options defined in the filter manager to the given $query instance.
     *
     * @param Query $query
     * @return void
     * @throws MissingSortMethodException
     */
    public function applySort(Query $query)
    {
        foreach ($this->filterManager->getSort() as $name => $direction) {
            $sortMethod = 'sortBy' . Inflector::camelize($name) . ucfirst($direction);
            if (!method_exists($this->table, $sortMethod)) {
                throw new MissingSortMethodException('Sort method "' . $sortMethod . '" on table "' . get_class($this->table) . '" is not implemented.');
            }

            $this->table->{$sortMethod}($query);
        }
    }

    /**
     * Paginate the given $query.
     *
     * @param Query $query
     */
    public function applyPagination(Query $query)
    {
        if ($this->filterManager->isPaginationActive()) {
            $query->limit($this->filterManager->getLimit());
            $query->page($this->filterManager->getPage());
        }
    }
}
