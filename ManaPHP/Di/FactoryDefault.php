<?php
namespace ManaPHP\Di;

use Phalcon\Events\EventsAwareInterface;
use Phalcon\Mvc\Router;

class FactoryDefault extends \Phalcon\DI\FactoryDefault
{
    /**
     * @var \ManaPHP\Events\Manager
     */
    protected $_eventManagerEx;

    /**
     * FactoryDefault constructor.
     *
     * @param bool $peekEvents
     */
    public function __construct($peekEvents = true)
    {
        parent::__construct();

        $this->setShared('view', 'ManaPHP\Mvc\View');
        $this->setShared('response', 'ManaPHP\Http\Response');
        $this->setShared('debugger', 'ManaPHP\Debugger');
        $this->setShared('dispatcher', 'ManaPHP\Mvc\Dispatcher');
        $this->setShared('random', 'Phalcon\Security\Random');
        $this->setShared('sqlDePreparer', 'ManaPHP\Db\SqlDePreparer');

        $this->setShared('router', function () {
            $router = new Router(false);
            $router->removeExtraSlashes(true);
            $router->notFound(['module' => 'Home', 'controller' => 'Exception', 'action' => 'router']);

            return $router;
        });

        $this->set('Phalcon\Mvc\Model\Query\Builder', 'ManaPHP\Mvc\Model\Query\Builder');
        $this->setShared('eventsManager', 'ManaPHP\Events\Manager');
        if ($peekEvents) {
            $this->_eventManagerEx = $this->getShared('eventsManager');
        }
    }

    public function get($name, $parameters = null)
    {
        $instance = parent::get($name, $parameters);

        if ($this->_eventManagerEx && $instance instanceof EventsAwareInterface) {
            $instance->setEventsManager($this->_eventManagerEx);
        }

        return $instance;
    }
}