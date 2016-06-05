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
namespace Wasabi\Core\Routing\Route;

use Cake\Cache\Cache;
use Cake\Event\Event;
use Cake\Event\EventManager;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\Routing\Route\Route;

class WasabiRoute extends Route
{
    const PAGE_PART = 'p';

    /**
     * Check if a URL array matches this route instance.
     *
     * If the URL matches the route parameters and settings, then
     * return a generated string URL. If the URL doesn't match the route parameters, false will be returned.
     * This method handles the reverse routing or conversion of URL arrays into string URLs.
     *
     * @param array $url An array of parameters to check matching with.
     * @param array $context An array of the current request context.
     *   Contains information such as the current host, scheme, port, base
     *   directory and other url params.
     * @return string|false Either a string URL for the parameters if they match or false.
     */
    public function match(array $url, array $context = [])
    {
        unset($url['action'], $url['plugin']);

        $cacheKey = md5(serialize($url));

        $route = Cache::remember($cacheKey, function () use ($url, $context) {
            $routesMatchEvent = new Event('Wasabi.Routes.match', $this, [$url, $context]);
            EventManager::instance()->dispatch($routesMatchEvent);

            if (empty($routesMatchEvent->result)) {
                return false;
            }

            return $routesMatchEvent->result;
        }, 'wasabi/core/routes');

        if ($route === false) {
            return false;
        }

        return $context['_base'] . $route;
    }

    /**
     * Parse a requested url and create routing parameters from the routes table.
     *
     * @param string $url The url to parse.
     * @return array|bool|mixed
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
            /** @var \Wasabi\Core\Model\Entity\Route $redirectRoute */
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

    /**
     * Redirect to the given url.
     *
     * @param string $redirectUrl The url to redirect to.
     * @param int $statusCode The http status code.
     * @return void
     */
    protected function _redirect($redirectUrl, $statusCode = 301)
    {
        header('HTTP/1.1 ' . $statusCode);
        header('Location: ' . Router::url($redirectUrl, true));
        exit(0);
    }
}
