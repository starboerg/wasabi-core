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
namespace Wasabi\Core\Model\Table;

use ArrayObject;
use Cake\Cache\Cache;
use Cake\Datasource\ResultSetInterface;
use Cake\Event\Event;
use Cake\ORM\Query;
use Cake\ORM\Table;
use Wasabi\Core\Model\Entity\Route;

/**
 * Class RoutesTable
 */
class RoutesTable extends Table
{
    /**
     * Initialize a table instance. Called after the constructor.
     *
     * @param array $config Configuration options passed to the constructor
     */
    public function initialize(array $config)
    {
        $this->addBehavior('Timestamp');
    }

    /**
     * Called after an entity is saved.
     *
     * @param Event $event
     * @param Route $entity
     * @param ArrayObject $options
     */
    public function afterSave(Event $event, Route $entity, ArrayObject $options)
    {
        Cache::clearGroup('wasabi/core/routes');
        $this->eventManager()->dispatch(new Event('Wasabi.Routes.changed'));
    }

    /**
     * Check wheter a route with the given $url already exists.
     *
     * @param string $url
     * @return bool
     */
    public function routeAlreadyExists($url)
    {
        $route = $this->find()
            ->select([$this->aliasField('id')])
            ->where([$this->aliasField('url') => $url])
            ->hydrate(false)
            ->first();

        return !empty($route);
    }

    /**
     * Get the default route for an entity $table + $foreignId for the given language $languageId.
     *
     * @param string $model
     * @param int $foreignKey
     * @param int $languageId
     * @return bool|array
     */
    public function getDefaultRoute($model, $foreignKey, $languageId)
    {
        $defaultRoute = $this->find()
            ->select([$this->aliasField('id')])
            ->where([
                $this->aliasField('model') => $model,
                $this->aliasField('foreign_key') => $foreignKey,
                $this->aliasField('language_id') => $languageId,
                $this->aliasField('redirect_to') . ' IS' => null
            ])
            ->hydrate(false)
            ->first();

        return $defaultRoute;
    }

    /**
     * Get all other routes for the given $table and $foreignId except the route with id = $id.
     *
     * @param int $id
     * @param string $model
     * @param int $foreignKey
     * @param int $languageId
     * @return Query
     */
    public function getOtherRoutesExcept($id, $model, $foreignKey, $languageId)
    {
        $otherRoutes = $this->find()
            ->where([
                $this->aliasField('id') . ' <>' => $id,
                $this->aliasField('model') => $model,
                $this->aliasField('foreign_key') => $foreignKey,
                $this->aliasField('language_id') => $languageId
            ]);

        return $otherRoutes;
    }

    /**
     * Redirect the given $routes to the route with id = $id.
     *
     * @param Route[]|Query|ResultSetInterface $routes
     * @param int $id
     */
    public function redirectRoutesToId($routes, $id)
    {
        foreach ($routes as $route) {
            $route->set([
                'redirect_to' => $id,
                'status_code' => 301
            ]);
            $this->save($route);
        }
    }

    /**
     * Find all routes that match the given $table, $foreignId and $languageId.
     *
     * @param string $model
     * @param int $foreignKey
     * @param int $languageId
     * @return Query
     */
    public function findAllFor($model, $foreignKey, $languageId)
    {
        return $this->find()
            ->where([
                $this->aliasField('model') => $model,
                $this->aliasField('foreign_key') => $foreignKey,
                $this->aliasField('language_id') => $languageId
            ]);
    }
}
