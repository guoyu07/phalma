<?php
namespace ManaPHP\Mvc;

use Phalcon\Text;

class Dispatcher extends \Phalcon\Mvc\Dispatcher
{
    /**
     * @param array|string $forward
     */
    public function forward($forward)
    {
        if (is_string($forward)) {
            $parts = explode('/', $forward);
            if (count($parts) === 1) {
                $forward = ['action' => $parts[0]];
            } elseif (count($parts) === 2) {
                $forward = ['controller' => $parts[0], 'action' => $parts[1]];
            }
        }

        return parent::forward($forward);
    }

    public function setControllerName($controllerName)
    {
        parent::setControllerName(ucfirst(strpos($controllerName, '_') ? Text::camelize($controllerName) : $controllerName));
    }

    public function setActionName($actionName)
    {
        parent::setActionName($actionName = lcfirst(strpos($actionName, '_') ? Text::camelize($actionName) : $actionName));
    }
}