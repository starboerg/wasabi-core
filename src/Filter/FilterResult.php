<?php

namespace Wasabi\Core\Filter;

use Cake\ORM\Query;

class FilterResult
{
    /**
     * Holds the query instance with all applied filter and sort operations.
     *
     * @var Query
     */
    private $query;

    /**
     * Holds the pagination data that is represented by the $query and the total number of available records.
     *
     * @var array
     */
    private $paginationData;

    /**
     * Constructor.
     *
     * @param Query $query
     * @param array $paginationData
     */
    public function __construct(Query $query, array $paginationData)
    {
        $this->query = $query;
        $this->paginationData = $paginationData;
    }

    /**
     * Get the query.
     *
     * @return Query
     */
    public function getQuery() : Query
    {
        return $this->query;
    }

    /**
     * Get the pagination data.
     *
     * @return array
     */
    public function getPaginationData() : array
    {
        return $this->paginationData;
    }
}
