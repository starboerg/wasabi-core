<?php

namespace Wasabi\Core\Routing\Route;

use Cake\Event\Event;
use Cake\Event\EventManager;
use Cake\ORM\TableRegistry;
use Cake\Routing\Route\Route;
use Cake\Routing\Router;

class WasabiRoute extends Route
{
    CONST PAGE_PART = 'p';

    /**
     * Reverse routing method to generate a url from given url array
     *
     * @param array $url
     * @return boolean|mixed
     */
//    public function match($url)
//    {
//        if (!is_array($url) || empty($url) || !isset($url[Route::PAGE_KEY]) || !isset($url[Route::LANG_KEY])) {
//            return false;
//        }
//
//        if (!isset($url['plugin']) || $url['plugin'] == false) {
//            $url['plugin'] = null;
//        }
//
//        $identifier = md5(serialize($url));
//
//        if ($result = Cache::read($identifier, 'core.routes')) {
//            return $result;
//        }
//
//        $conditions = array(
//            //'Route.plugin' => ($url['plugin'] == null) ? '' : $url['plugin'],
//            //'Route.controller' => $url['controller'],
//            //'Route.action' => $url['action'],
//            'Route.' . Route::PAGE_KEY => $url[Route::PAGE_KEY],
//            'Route.' . Route::LANG_KEY => $url[Route::LANG_KEY],
//            'Route.status_code' => null
//        );
//
//        unset($url['plugin'], $url['controller'], $url['action'], $url[Route::PAGE_KEY], $url[Route::LANG_KEY]);
//
//        $passedParams = array();
//        $namedParams = array();
//
//        foreach ($url as $key => $value) {
//            if (is_int($key)) {
//                $passedParams[] = $value;
//            }
//            if (is_string($key)) {
//                $namedParams[] = $key . ':' . $value;
//            }
//        }
//
////		$conditions['Route.params'] = implode('|', $passedParams);
//
//        $route = ClassRegistry::init('Core.Route')->find('first', array(
//            'conditions' => $conditions
//        ));
//
//        if (!$route) {
//            return false;
//        }
//
//        if (!empty($namedParams)) {
//            $route['Route']['url'] .= '/' . implode('/', $namedParams);
//        }
//
//        Cache::write($identifier, $route['Route']['url'], 'core.routes');
//
//        return $route['Route']['url'];
//    }

    /**
     * Parse a requested url and create routing parameters from the routes table.
     *
     * @param string $url
     * @return array|boolean|mixed
     */
    public function parse($url)
    {
        if (!$url) {
            $url = '/';
        }

        $Routes = TableRegistry::get('Wasabi/Core.Routes');

        $route = $Routes->find()
            ->where([
                $Routes->aliasField('page_type') => 'simple',
                $Routes->aliasField('url') => $url
            ])
            ->first();

        $pageNumber = false;

        $urlParts = explode('/', $url);

        // no direct route found -> try paged collection routes
        if (!$route) {
            $part = array_pop($urlParts);

            if (preg_match('/^[0-9]+$/', $part)) {
                $pageNumber = (int)$part;
                $part = array_pop($urlParts);
            }

            // paged url found so lets try to find a proper collection route
            if ($pageNumber !== false && $part === self::PAGE_PART) {
                $route = $Routes->find()->where([
                    $Routes->aliasField('page_type') => 'collection',
                    $Routes->aliasField('url') => '/' . join('/', $urlParts)
                ]);
            }
        }

        if (!$route) {
            return false;
        }

        if ($route->redirect_to !== null) {
            $redirectRoute = $Routes->get($route->redirect_to);

            $redirectUrl = $redirectRoute->url;
            $statusCode = 301;
            if ($pageNumber !== false && $pageNumber > 1) {
                $redirectUrl .= '/' . self::PAGE_PART . '/' . $pageNumber;
            }
            if ($route->status_code !== null) {
                $statusCode = (int)$route->status_code;
            }

            $this->_redirect($redirectUrl, $statusCode);
        }

        // Dispatch Wasabi.Routes.parse event to all plugin listeners to get params.
        $routesParseEvent = new Event('Wasabi.Routes.parse', $url, [
            'route' => $route,
            'pageNumber' => $pageNumber
        ]);

        EventManager::instance()->dispatch($routesParseEvent);

        if (!$routesParseEvent->isStopped() || empty($routesParseEvent->result['params'])) {
            return false;
        }

        $params = $routesParseEvent->result['params'];

        return $params;
    }

    protected function _redirect($redirectUrl, $statusCode = 301)
    {
        header('HTTP/1.1 ' . $statusCode);
        header('Location: ' . Router::url($redirectUrl, true));
        exit(0);
    }
}
