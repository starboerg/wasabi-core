<?php

namespace Wasabi\Core\Controller;

use Cake\Database\Connection;
use Cake\Network\Exception\BadRequestException;
use Cake\Network\Exception\MethodNotAllowedException;
use Cake\ORM\ResultSet;
use Wasabi\Core\Model\Entity\Route;
use Wasabi\Core\Model\Table\RoutesTable;
use Wasabi\Core\Routing\RouteTypes;
use Wasabi\Core\Wasabi;

/**
 * Class RoutesController
 *
 * @property RoutesTable Routes
 */
class RoutesController extends BackendAppController
{
    /**
     * Initialization hook method.
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();

        $this->loadComponent('RequestHandler');
        $this->loadModel('Wasabi/Core.Routes');
    }

    /**
     * Add action
     * AJAX POST
     *
     * @throws MethodNotAllowedException
     * @throws BadRequestException
     * @return void
     */
    public function add()
    {
        if (!$this->request->isAll(['ajax', 'post'])) {
            throw new MethodNotAllowedException();
        }

        $model = $this->request->data('model');
        $foreignKey = (int)$this->request->data('foreign_key');
        $languageId = Wasabi::contentLanguage()->id;
        /** @var string|null $url */
        $url = $this->request->data('url');
        $routeType = (int)$this->request->data('route_type');
        $element = $this->request->data('element');

        if (empty($model) || !$foreignKey || !RouteTypes::get($routeType) || empty($element)) {
            throw new BadRequestException();
        }

        if (!empty($url) && substr($url, 0, 1) !== '/') {
            $url = '/' . $url;
        }

        $routeData = [
            'url' => $url,
            'model' => $model,
            'foreign_key' => $foreignKey,
            'language_id' => $languageId,
        ];

        switch ($routeType) {
            case RouteTypes::TYPE_DEFAULT_ROUTE:
                $route = $this->_addDefaultRoute($routeData);
                break;
            case RouteTypes::TYPE_REDIRECT_ROUTE:
                $route = $this->_addRedirectRoute($routeData);
                break;
            default:
                throw new BadRequestException();
        }

        $this->_render($model, $foreignKey, $languageId, $element, $route ?? null);
    }

    /**
     * MakeDefault action
     * AJAX POST
     *
     * @param int|string $id The route id.
     * @return void
     */
    public function makeDefault($id)
    {
        if (!$this->request->isAll(['ajax', 'post'])) {
            throw new MethodNotAllowedException();
        }

        /** @var Route $route */
        $route = $this->Routes->get($id);

        /** @var Connection $connection */
        $connection = $this->Routes->connection();
        $connection->begin();

        $route->set([
            'redirect_to' => null,
            'status_code' => null
        ]);
        if ($this->Routes->save($route)) {
            $otherRoutes = $this->Routes->getOtherRoutesExcept($route->id, $route->model, $route->foreign_key, $route->language_id);
            $this->Routes->redirectRoutesToId($otherRoutes, $route->id);

            $connection->commit();
            $this->Flash->success(__d('wasabi_core', '<strong>{0}</strong> is now the new Default Route.', $route->url), 'routes');
        } else {
            $connection->rollback();
            $this->Flash->error($this->dbErrorMessage, 'routes');
        }

        $this->_render($route->model, $route->foreign_key, $route->language_id, $this->request->query('element'));
    }

    /**
     * Delete action
     * AJAX POST
     *
     * @param int|string $id The route id.
     * @return void
     */
    public function delete($id)
    {
        if (!$this->request->isAll(['ajax', 'post'])) {
            throw new MethodNotAllowedException();
        }

        /** @var Route $route */
        $route = $this->Routes->get($id);

        /** @var ResultSet $otherRoutes */
        $otherRoutes = $this->Routes->getOtherRoutesExcept($route->id, $route->model, $route->foreign_key, $route->language_id);

        if (!empty($otherRoutes) && $otherRoutes->count() >= 1) {
            /** @var Connection $connection */
            $connection = $this->Routes->connection();
            $connection->begin();

            $this->Routes->delete($route);
            if ($route->redirect_to === null) {
                /** @var Route $newDefaultRoute */
                $newDefaultRoute = $otherRoutes->first();
                $newDefaultRoute->set([
                    'redirect_to' => null,
                    'status_code' => null
                ]);
                $this->Routes->save($newDefaultRoute);

                $newRedirectRoutes = $otherRoutes->skip(1);
                foreach ($newRedirectRoutes as $r) {
                    $r->set([
                        'redirect_to' => $newDefaultRoute->id,
                        'status_code' => 301
                    ]);
                    $this->Routes->save($r);
                }
            }

            $connection->commit();
            $this->Flash->success(__d('wasabi_core', 'The URL <strong>{0}</strong> has been deleted.', $route->url), 'routes');
        } else {
            $this->Flash->error(__d('wasabi_core', 'The URL <strong>{0}</strong> cannot be deleted. Please create another URL first.', $route->url), 'routes');
        }

        $this->_render($route->model, $route->foreign_key, $route->language_id, $this->request->query('element'));
    }

    /**
     * Global render method for all actions of this controller.
     *
     * @param string $model The model name.
     * @param int $foreignKey The foreign key of the entity.
     * @param int $languageId The language id.
     * @param string $element The name of the view element.
     * @param Route|null $route The route.
     * @return void
     */
    protected function _render($model, $foreignKey, $languageId, $element, $route = null)
    {
        $routes = $this->Routes
            ->findAllFor($model, $foreignKey, $languageId)
            ->order([$this->Routes->aliasField('url') => 'asc']);

        $this->set([
            'routes' => $routes,
            'routeTypes' => RouteTypes::getForSelect(),
            'model' => $model,
            'element' => $element,
            'formRoute' => $route
        ]);

        $this->render('add');
    }

    /**
     * Add a new default route.
     *
     * @param array $routeData The route data.
     * @return Route
     */
    protected function _addDefaultRoute(array $routeData)
    {
        /** @var Connection $connection */
        $connection = $this->Routes->connection();
        $connection->begin();

        // Save the new default route.
        $defaultRoute = $this->Routes->newEntity($routeData);

        if ($this->Routes->save($defaultRoute)) {
            // Make all other routes for this model + foreignKey + languageId
            // redirect to the new default route.
            $otherRoutes = $this->Routes->getOtherRoutesExcept($defaultRoute->id, $routeData['model'], $routeData['foreign_key'], $routeData['language_id']);
            $this->Routes->redirectRoutesToId($otherRoutes, $defaultRoute->id);

            $connection->commit();
            $this->Flash->success(__d('wasabi_core', 'New default URL <strong>{0}</strong> has been added.', $routeData['url']), 'routes');
            $this->request->data = [];
        } else {
            $connection->rollback();
            $this->Flash->error($this->formErrorMessage, 'routes');
            $defaultRoute->set('type', RouteTypes::TYPE_DEFAULT_ROUTE);
        }

        return $defaultRoute;
    }

    /**
     * Add a new redirect route.
     *
     * @param array $routeData The route data.
     * @return Route
     */
    protected function _addRedirectRoute($routeData)
    {
        $defaultRoute = $this->Routes->getDefaultRoute($routeData['model'], $routeData['foreign_key'], $routeData['language_id']);

        $redirectRoute = $this->Routes->newEntity($routeData);

        if (!empty($defaultRoute)) {
            $redirectRoute->set('redirect_to', $defaultRoute['id']);
            $redirectRoute->set('status_code', 301);
            if ($this->Routes->save($redirectRoute)) {
                $this->Flash->success(__d('wasabi_core', 'New redirect URL <strong>{0}</strong> has been added.', $routeData['url']), 'routes');
                $this->request->data = [];
            } else {
                $this->Flash->error($this->formErrorMessage, 'routes');
                $redirectRoute->set('type', RouteTypes::TYPE_REDIRECT_ROUTE);
            }
        } else {
            $this->Flash->error(__d('wasabi_core', 'Please create a default route first.'), 'routes');
            $redirectRoute->set('type', RouteTypes::TYPE_REDIRECT_ROUTE);
        }

        return $redirectRoute;
    }
}
