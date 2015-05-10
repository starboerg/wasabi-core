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
namespace Wasabi\Core\Model\Behavior;
use Cake\Collection\Collection;
use Cake\ORM\Behavior;
use Cake\ORM\Entity;
use Cake\ORM\Query;
use Cake\ORM\ResultSet;
use Cake\Utility\Hash;

/**
 * Class KeyValueBehavior
 */
class KeyValueBehavior extends Behavior
{
    /**
     * Default configuration
     *
     * These are merged with user-provided configuration when the behavior is used.
     *
     * @var array
     */
    protected $_defaultConfig = [
        'scope' => 'App',
        'fields' => [
            'key' => 'field',
            'value' => 'value'
        ],
        'serializeFields' => [],
        'implementedFinder' => [
            'keyValues' => 'findKeyValues',
            'allKeyValues' => 'findAllKeyValues'
        ]
    ];

    /**
     * Generate a custom entity with all keys as properties and
     * their corresponding values
     *
     * Returns a computed entity that can be used by FormHelper.
     *
     *
     * @param Entity $entity
     * @return Entity
     */
    public function getKeyValues(Entity $entity)
    {
        $scope = $this->config('scope');
        $query = $this->_table->query();

        $query->where(['scope' => $scope])->hydrate(false);

        // unserialize serialized fields
        $query->formatResults(function(ResultSet $results) {
            return $results->map(function($row) {
                if ($row['serialized'] === true) {
                    $row['value'] = unserialize($row['value']);
                }
                return $row;
            });
        });

        foreach ($query->all() as $row) {
            $entity->{$row[$this->config('fields.key')]} = $row[$this->config('fields.value')];
        }

        return $entity;
    }

    /**
     * Save key value pairs.
     *
     * @param Entity $entity
     * @return bool|mixed
     */
    public function saveKeyValues(Entity $entity)
    {
        $event = $this->_table->dispatchEvent('Model.beforeSaveKeyValues', compact('data'));

        if ($event->isStopped()) {
            return $event->result;
        }

        if (!empty($event->result)) {
            $data = $event->result;
        }

        $fields = $this->config('fields');

        if ($entity->has($fields['key']) && $entity->has($fields['value'])) {
            return true;
        }

        $mappedIds = $this->_getFieldToIdMapping();

        $entities = [];
        foreach ($entity->toArray() as $key => $value) {
            if ($this->_table->hasField($key)) {
                continue;
            }

            $serialized = false;
            if (in_array($key, $this->config('serializeFields'))) {
                $value = serialize($value);
                $serialized = true;
            }

            $kvEntity = $this->_table->newEntity([
                'scope' => $this->config('scope'),
                $this->config('fields.key') => $key,
                $this->config('fields.value') => $value,
                'serialized' => $serialized
            ]);

            if (($existingId = Hash::get($mappedIds, $this->config('scope') . '.' . $key)) !== null) {
                $kvEntity->id = $existingId;
            }

            $entities[] = $kvEntity;
        }

        $table = $this->_table;
        $result = $this->_table->connection()->transactional(function () use ($table, $entities) {
            foreach ($entities as $e) {
                $table->save($e);
            }
        });

        return ($result !== false);
    }

    /**
     * Returns an array of existing keys and
     * their corresponding ids
     *
     * @return array
     */
    protected function _getFieldToIdMapping()
    {
        return $this->_table->find('list', [
            'keyField' => $this->config('fields.key'),
            'valueField' => 'id',
            'groupField' => 'scope'
        ])->toArray();
    }
}
