<?php

namespace Wasabi\Core\Filter;

use Cake\Database\Expression\QueryExpression;
use Cake\ORM\Query;
use Cake\ORM\Table;
use Wasabi\Core\Filter\Exception\MissingFilterMethodException;

trait Filterable
{
    /**
     * @param FilterManager $filterManager
     * @param Query $query
     * @return FilterResult
     * @throws Exception\MissingSortMethodException
     * @throws MissingFilterMethodException
     */
    public function getFilterResult(FilterManager $filterManager, Query $query)
    {
        /** @var Table $this */
        $filterHandler = new FilterHandler($this, $filterManager);

        $count = $filterHandler->applyFilters($query);

        if ($count === 0) {
            $paginationData = [
                'from' => 0,
                'to' => 0,
                'page' => 0,
                'totalHits' => 0,
                'pages' => 0
            ];

            /** @var Table $this */
            return new FilterResult($this->find()->select($this->aliasField('id'))->where(['1 <> 1']), $paginationData);
        }

        $filterHandler->applySort($query);
        $filterHandler->applyPagination($query);

        $from = ($filterManager->getPage() - 1) * $filterManager->getLimit() + 1;
        $to = $from + $filterManager->getLimit() - 1;
        $to = min($count, $to);

        $paginationData = [
            'from' => $from,
            'to' => $to,
            'page' => $filterManager->getPage(),
            'pages' => (int)ceil($count / $filterManager->getLimit()),
            'totalHits' => $count
        ];

        return new FilterResult($query, $paginationData);
    }

    /**
     * Get an id query that reflects the current filters.
     *
     * This method can be used singlehandedly whenever you only want to
     * generate an id query of the present filters.
     *
     * @param FilterManager $filterManager
     * @return Query
     * @throws MissingFilterMethodException
     */
    public function getIdsQuery(FilterManager $filterManager)
    {
        /** @var Table $this */
        $filterHandler = new FilterHandler($this, $filterManager);

        return $this->getFilteredIdsQuery($filterHandler);
    }

    /**
     * Get the filtered ids query.
     *
     * @param FilterHandler $filterHandler
     * @return Query
     * @throws MissingFilterMethodException
     */
    protected function getFilteredIdsQuery(FilterHandler $filterHandler)
    {
        /** @var Table $this */
        $query = $this->find()->select([$this->aliasField('id')]);

        $filterHandler->applyFilters($query);

        return $query;
    }

    /**
     * Create a `LIKE` filter expression for the given $field and $value.
     *
     * @param string $field
     * @param string $value
     * @return \Closure
     */
    protected function likeFilter(string $field, string $value): \Closure
    {
        return function (QueryExpression $expression) use ($field, $value) {
            return $expression->like($field, $value, 'string');
        };
    }

    /**
     * Create a `LIKE` filter expression for the given $field and '%' . $value . '%'.
     *
     * @param string $field
     * @param string $value
     * @return \Closure
     */
    protected function likeContainFilter(string $field, string $value): \Closure
    {
        return function (QueryExpression $expression) use ($field, $value) {
            return $expression->like($field, '%' . $value . '%', 'string');
        };
    }
}
