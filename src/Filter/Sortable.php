<?php

namespace Wasabi\Core\Filter;

use Cake\Database\Expression\QueryExpression;
use Cake\ORM\Query;

trait Sortable
{
    /**
     * Apply natural asc sorting to the given $field on the $query.
     *
     * @param Query $query
     * @param string $field
     * @return Query
     */
    protected function naturalSortAsc(Query $query, string $field): Query
    {
        $query->orderAsc(new QueryExpression($field . '+00'));
        $query->orderAsc(new QueryExpression($field . '+0'));
        $query->orderAsc($field);

        return $query;
    }

    /**
     * Apply natural desc sorting to the given $field on the $query.
     *
     * @param Query $query
     * @param string $field
     * @return Query
     */
    protected function naturalSortDesc(Query $query, string $field): Query
    {
        $query->orderDesc(new QueryExpression($field . '+00'));
        $query->orderDesc(new QueryExpression($field . '+0'));
        $query->orderDesc($field);

        return $query;
    }
}
