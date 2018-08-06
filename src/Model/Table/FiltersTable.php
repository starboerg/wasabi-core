<?php

namespace Wasabi\Core\Model\Table;

use Cake\Http\ServerRequest;
use Cake\ORM\Table;
use Wasabi\Core\Model\Entity\Filter;

class FiltersTable extends Table
{
    /**
     * Find an existing or create a new slug for the given $request and $filterData.
     *
     * @param ServerRequest $request
     * @param array $filterData
     * @return string
     */
    public function findOrCreateSlugForFilterData(ServerRequest $request, array $filterData)
    {
        $conditions = $this->getFilterFindConditions($request, $filterData);
        $existingFilter = $this->findExistingFilterForConditions($conditions)->first();
        if ($existingFilter) {
            return $existingFilter->slug;
        }

        return $this->createSlugFor($request, $filterData);
    }

    /**
     * Find an existing filter entry for the given $conditions.
     *
     * @param array $conditions
     * @return \Cake\ORM\Query|Filter
     */
    public function findExistingFilterForConditions(array $conditions)
    {
        return $this->find()
            ->select($this->aliasField('slug'))
            ->where($conditions);
    }

    /**
     * Find the filter data for the given $slug.
     *
     * @param string $slug
     * @return array
     */
    public function findFilterDataForSlug(string $slug): array
    {
        $filter = $this
            ->findExistingFilterForConditions([$this->aliasField('slug') => $slug])
            ->select([$this->aliasField('filter_data')], true)
            ->first();

        if ($filter) {
            return $this->decodeFilterData($filter->filter_data);
        }

        return [];
    }

    /**
     * Create a slug for the given $request and the provided $filterData.
     *
     * @param ServerRequest $request
     * @param array $filterData
     * @return string
     */
    protected function createSlugFor(ServerRequest $request, array $filterData)
    {
        $charlist = 'abcdefghikmnopqrstuvwxyz';

        do {
            $slug = '';
            for ($i = 0; $i < 14; $i++) {
                $slug .= substr($charlist, rand(0, 31), 1);
            }
        } while ($this->slugExists($request, $slug));

        $this->save($this->newEntity([
            'plugin' => $request->getParam('plugin'),
            'controller' => $request->getParam('controller'),
            'action' => $request->getParam('action'),
            'slug' => $slug,
            'filter_data' => $this->encodeFilterData($filterData)
        ]));

        return $slug;
    }

    /**
     * Check if the given $slug exists for the $request.
     *
     * @param ServerRequest $request
     * @param string $slug
     * @return bool
     */
    protected function slugExists(ServerRequest $request, $slug)
    {
        $uniqueSlugConditions = $this->getFilterFindConditions($request, [], $slug);

        $existingSlug = $this->find('all')
            ->select($this->aliasField('slug'))
            ->where($uniqueSlugConditions)
            ->enableHydration(false)
            ->first();

        return $existingSlug !== null;
    }

    /**
     * Get the filter find conditions for the given request and optional $filterDate or $slug.
     *
     * @param ServerRequest $request
     * @param array $filterData
     * @param string $slug
     * @return array
     */
    protected function getFilterFindConditions(ServerRequest $request, array $filterData = [], string $slug = '')
    {
        $conditions = [
            $this->aliasField('controller') => $request->getParam('controller'),
            $this->aliasField('action') => $request->getParam('action'),
            $this->aliasField('plugin') . ' IS' => $request->getParam('plugin'),
        ];

        if (!empty($filterData)) {
            $conditions[$this->aliasField('filter_data')] = $this->encodeFilterData($filterData);
        }

        if (!empty($slug)) {
            $conditions[$this->aliasField('slug')] = $slug;
        }

        return $conditions;
    }

    /**
     * Encode the provided $filterData array to a json string.
     *
     * @param array $filterData
     * @return string
     */
    protected function encodeFilterData(array $filterData)
    {
        return json_encode($filterData);
    }

    /**
     * Decode the given json string and return the filter data array.
     *
     * @param string $filterDataString
     * @return array
     */
    protected function decodeFilterData(string $filterDataString): array
    {
        $filterData = json_decode($filterDataString, true);
        if (!is_array($filterData)) {
            return [];
        }

        return $filterData;
    }
}
