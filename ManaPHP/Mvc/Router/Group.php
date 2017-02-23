<?php
namespace ManaPHP\Mvc\Router;

use Phalcon\Text;

class Group extends \Phalcon\Mvc\Router\Group
{
    /** @noinspection MagicMethodsValidityInspection */

    /**
     * Group constructor.
     *
     * @param string $path
     * @param bool   $useDefaultRoutes
     */
    public function __construct($path, $useDefaultRoutes = true)
    {
        $parts = explode('\\', get_class($this));
        $module = $parts[1];
        $this->setPaths(['module' => $module, 'namespace' => "$parts[0]\\$parts[1]\\Controllers"]);
        if ($path !== '/') {
            $this->setPrefix($path ? strtolower($path) : ('/' . strtolower($module)));
        }

        if ($useDefaultRoutes) {
            $this->add('/', ['controller' => 'index', 'action' => 'index']);
            $this->add('/:controller', ['controller' => 1, 'action' => 'index']);
            $this->add('/:controller/:action', ['controller' => 1, 'action' => 2]);
            $this->add('/:controller/:action/:params', ['controller' => 1, 'action' => 2, 'params' => 3]);
        }
    }

    protected function _addRoute($pattern, $paths = null, $httpMethods = null)
    {
        $routePaths = [];

        if (is_string($paths)) {
            $parts = explode('::', $paths);
            if (count($parts) === 2) {
                $routePaths['controller'] = $parts[0];
                /** @noinspection MultiAssignmentUsageInspection */
                $routePaths['action'] = $parts[1];
            } else {
                $routePaths['controller'] = $parts[0];
            }
        } elseif (is_array($paths)) {
            if (isset($paths[0])) {
                if (strpos($paths[0], '::')) {
                    $parts = explode('::', $paths[0]);
                    $routePaths['controller'] = $parts[0];
                    $routePaths['action'] = $parts[1];
                } else {
                    $routePaths['controller'] = $paths[0];
                }
            }

            if (isset($paths[1])) {
                $routePaths['action'] = $paths[1];
            }

            /** @noinspection ForeachSourceInspection */
            foreach ($paths as $k => $v) {
                if (is_string($k)) {
                    $routePaths[$k] = $v;
                }
            }
        }

        if (isset($routePaths['controller']) && is_string($routePaths['controller'])) {
            $parts = explode('\\', $routePaths['controller']);
            $routePaths['controller'] = basename(end($parts), 'Controller');

            $routePaths['controller'] = Text::uncamelize($routePaths['controller']);
        }

        parent::_addRoute($pattern, $routePaths, $httpMethods);
    }
}