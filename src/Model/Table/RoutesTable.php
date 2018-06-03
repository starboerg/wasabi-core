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
namespace Wasabi\Core\Model\Table;

use ArrayObject;
use Cake\Cache\Cache;
use Cake\Datasource\EntityInterface;
use Cake\Datasource\ResultSetInterface;
use Cake\Event\Event;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Wasabi\Core\Model\Entity\Route;

/**
 * Class RoutesTable
 */
class RoutesTable extends Table
{
    /**
     * Initialize a table instance. Called after the constructor.
     *
     * @param array $config Configuration options passed to the constructor.
     * @return void
     */
    public function initialize(array $config)
    {
        $this->addBehavior('Timestamp');
    }

    /**
     * Default validation rules.
     *
     * @param Validator $validator The validator to customize.
     * @return Validator
     * @throws \Aura\Intl\Exception
     */
    public function validationDefault(Validator $validator)
    {
        return $validator
            ->notEmpty('url', __d('wasabi_core', 'Please enter a url for this route.'));
    }

    /**
     * Build Rules.
     *
     * @param RulesChecker $rules The rules checker to customize.
     * @return RulesChecker
     * @throws \Aura\Intl\Exception
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->isUnique(['url'], __d('wasabi_core', 'This url is already taken.')));
        return $rules;
    }

    /**
     * Called after an entity is saved.
     *
     * @param Event $event An event instance.
     * @param Route $entity The entity that triggered the event.
     * @param ArrayObject $options Additional options passed to the save call.
     * @return void
     */
    public function afterSave(Event $event, Route $entity, ArrayObject $options)
    {
        Cache::clear(false, 'wasabi/core/routes');
        $this->getEventManager()->dispatch(new Event('Wasabi.Routes.changed'));
    }

    /**
     * {@inheritdoc}
     *
     * @return Route
     */
    public function newEntity($data = null, array $options = [])
    {
        return parent::newEntity($data, $options);
    }

    /**
     * {@inheritdoc}
     *
     * @return Route
     */
    public function patchEntity(EntityInterface $entity, array $data, array $options = [])
    {
        return parent::patchEntity($entity, $data, $options);
    }

    /**
     * {@inheritDoc}
     *
     * @return array|Route
     */
    public function get($primaryKey, $options = [])
    {
        return parent::get($primaryKey, $options);
    }

    /**
     * Check wheter a route with the given $url already exists.
     *
     * @param string $url The url to check.
     * @return bool
     */
    public function routeAlreadyExists($url)
    {
        $route = $this->find()
            ->select([$this->aliasField('id')])
            ->where([$this->aliasField('url') => $url])
            ->enableHydration(false)
            ->first();

        return !empty($route);
    }

    /**
     * Get the default route for an entity $table + $foreignId for the given language $languageId.
     *
     * @param string $model The model name.
     * @param int $foreignKey The foreign key of the model instance.
     * @param int $languageId The language id.
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
            ->enableHydration(false)
            ->first();

        return $defaultRoute;
    }

    /**
     * Get all other routes for the given $table and $foreignId except the route with id = $id.
     *
     * @param int $id The route id to exclude.
     * @param string $model The model name.
     * @param int $foreignKey The foreign key of the model instance.
     * @param int $languageId The language id.
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
     * @param Route[]|Query|ResultSetInterface $routes The routes to redirect.
     * @param int $id The route id to redirect the routes to.
     * @return void
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
     * @param string $model The model name.
     * @param int $foreignKey The foreign key of the model instance.
     * @param int $languageId The language id.
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
