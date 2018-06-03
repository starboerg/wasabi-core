<?php
/**
 * Wasabi Core
 * Copyright (c) Frank Förster (http://frankfoerster.com)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Frank Förster (http://frankfoerster.com)
 * @link          https://github.com/wasabi-cms/core Wasabi Project
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace Wasabi\Core\Model\Behavior;

use Cake\ORM\Behavior;
use Cake\ORM\Entity;
use Cake\ORM\ResultSet;
use Cake\Utility\Hash;
use Exception;
use Wasabi\Core\Model\Entity\Setting;

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
     * Returns all keys and their values for all scopes.
     *
     * @return array
     */
    public function getAllKeyValues()
    {
        $query = $this->_table->query();

        $query->formatResults(function (ResultSet $results) {
            return $results->map(function ($row) {
                if ($row['serialized'] === true) {
                    $row['value'] = unserialize($row['value']);
                }
                return $row;
            });
        });

        $settings = [];

        foreach ($query->all() as $row) {
            $key = $row['scope'] . '__' . $row[$this->getConfig('fields.key')];
            $value = $row[$this->getConfig('fields.value')];
            $settings[$key] = $value;
        }

        return Hash::expand($settings, '__');
    }

    /**
     * Generate a custom entity with all keys as properties and
     * their corresponding values
     *
     * Returns a computed entity that can be used by FormHelper.
     *
     *
     * @param Entity $entity The base entity to customize.
     * @param array $keys The keys to set on the entity.
     * @return Entity
     */
    public function getKeyValues(Entity $entity, array $keys)
    {
        $scope = $this->getConfig('scope');
        $query = $this->_table->query();

        $query
            ->where([
                'scope' => $scope,
                $this->getConfig('fields.key') . ' IN' => $keys
            ])
            ->enableHydration(false);

        // unserialize serialized fields
        $query->formatResults(function (ResultSet $results) {
            return $results->map(function ($row) {
                if ($row['serialized'] === true) {
                    $row['value'] = unserialize($row['value']);
                }
                return $row;
            });
        });

        foreach ($query->all() as $row) {
            $entity->{$row[$this->getConfig('fields.key')]} = $row[$this->getConfig('fields.value')];
        }

        return $entity;
    }

    /**
     * Save key value pairs.
     *
     * @param Entity $entity The entity to save key values on.
     * @param array $keys The keys to apply.
     * @return bool|mixed
     */
    public function saveKeyValues(Entity $entity, array $keys)
    {
        $event = $this->_table->dispatchEvent('Model.beforeSaveKeyValues', compact('entity'));

        if ($event->isStopped()) {
            return $event->result;
        }

        if (!empty($event->result)) {
            $entity = $event->result;
        }

        $fields = $this->getConfig('fields');

        if ($entity->has($fields['key']) && $entity->has($fields['value'])) {
            return true;
        }

        $mappedIds = $this->_getFieldToIdMapping($keys);

        $entities = [];
        foreach ($entity->toArray() as $key => $value) {
            if ($this->_table->hasField($key)) {
                continue;
            }

            $serialized = false;
            if (in_array($key, $this->getConfig('serializeFields'))) {
                $value = serialize($value);
                $serialized = true;
            }

            /** @var Setting $kvEntity */
            $kvEntity = $this->_table->newEntity([
                'scope' => $this->getConfig('scope'),
                $this->getConfig('fields.key') => $key,
                $this->getConfig('fields.value') => $value,
                'serialized' => $serialized
            ]);

            if (($existingId = Hash::get($mappedIds, $key)) !== null) {
                $kvEntity->id = $existingId;
            }

            $entities[] = $kvEntity;
        }

        $result = false;

        try {
            $result = $this->_table->getConnection()->transactional(function () use ($entities) {
                foreach ($entities as $entity) {
                    $this->_table->save($entity);
                }
            });
        } catch (Exception $e) {}

        return ($result !== false);
    }

    /**
     * Returns an array of existing keys and
     * their corresponding ids
     *
     * @param array $keys The keys to map.
     * @return array
     */
    protected function _getFieldToIdMapping(array $keys)
    {
        return $this->_table
            ->find('list', [
                'keyField' => $this->getConfig('fields.key'),
                'valueField' => 'id'
            ])
            ->where([
                $this->_table->aliasField('scope') => $this->getConfig('scope'),
                $this->_table->getAlias() . '.' . $this->getConfig('fields.key') . ' IN' => $keys
            ])
            ->toArray();
    }
}
