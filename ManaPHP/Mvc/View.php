<?php
namespace ManaPHP\Mvc;

use Phalcon\Cache\BackendInterface;
use Phalcon\Mvc\View\Exception;
use Phalcon\Mvc\View\Exception as ViewException;

class View extends \Phalcon\Mvc\View
{
    /**
     * @var array
     */
    protected $_sections = [];

    /**
     * @var array
     */
    protected $_sectionStack = [];

    public function __construct($options = null)
    {
        parent::__construct($options);

        $this->registerEngines(['.phtml' => 'Phalcon\Mvc\View\Engine\Php', '.sword' => 'ManaPHP\Mvc\View\Engine\Sword']);
        $this->setRenderLevel(self::LEVEL_LAYOUT);
    }

    protected function _engineRender($engines, $viewPath, $silence, $mustClean, BackendInterface $cache = null)
    {
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        return parent::_engineRender($engines, $viewPath, false, $mustClean, $cache);
    }

    /**
     * Get the string contents of a section.
     *
     * @param  string $section
     * @param  string $default
     *
     * @return string
     */
    public function getSection($section, $default = '')
    {
        if (isset($this->_sections[$section])) {
            return $this->_sections[$section];
        } else {
            return $default;
        }
    }

    /**
     * Start injecting content into a section.
     *
     * @param  string $section
     * @param string  $default
     *
     * @return void
     */
    public function startSection($section, $default = null)
    {
        if ($default === null) {
            ob_start();
            $this->_sectionStack[] = $section;
        } else {
            $this->_sections[$section] = $default;
        }
    }

    /**
     * Stop injecting content into a section.
     *
     * @param  bool $overwrite
     *
     * @return void
     * @throws \Phalcon\Exception
     */
    public function stopSection($overwrite = false)
    {
        if (count($this->_sectionStack) === 0) {
            throw new Exception('cannot stop a section without first starting session'/**m0005e5105f6b924c8*/);
        }

        $last = array_pop($this->_sectionStack);
        if ($overwrite || !isset($this->_sections[$last])) {
            $this->_sections[$last] = ob_get_clean();
        } else {
            $this->_sections[$last] .= ob_get_clean();
        }
    }

    /**
     * @return void
     * @throws \Phalcon\Exception
     */
    public function appendSection()
    {
        if (count($this->_sectionStack) === 0) {
            throw new Exception('Cannot append a section without first starting one:'/**m0612bf4d28a6f9d36*/);
        }

        $last = array_pop($this->_sectionStack);
        if (isset($this->_sections[$last])) {
            $this->_sections[$last] .= ob_get_clean();
        } else {
            $this->_sections[$last] = ob_get_clean();
        }
    }

    /**
     * @param string $v
     *
     * @return string
     */
    public function escape($v)
    {
        return htmlentities($v, ENT_QUOTES, 'UTF-8', false);
    }

    /**
     * @param string    $widget
     * @param array     $options
     * @param int|array $cacheOptions
     *
     * @throws \Phalcon\Mvc\View\Exception
     */
    public function widget($widget, $options = [], $cacheOptions = null)
    {
        $widgetClassName = basename(APP_PATH) . '\\' . $this->_dependencyInjector->getShared('dispatcher')->getModuleName() . "\\Widgets\\{$widget}Widget";

        if (!class_exists($widgetClassName)) {
            throw new ViewException("`$widget` widget is invalid: `$widgetClassName` class is not exists"/**m020db278f144382d6*/);
        }

        /**
         * @var \ManaPHP\Mvc\WidgetInterface $widgetInstance
         */
        $widgetInstance = $this->_dependencyInjector->get($widgetClassName);
        $vars = $widgetInstance->run($options);

        if (is_string($vars)) {
            echo $vars;
        } else {
            $viewParams = $this->_viewParams;
            $this->_viewParams = $vars;
            $this->_engineRender($this->_loadTemplateEngines(), 'Widgets/' . $widget, false, false);
            $this->_viewParams = $viewParams;
        }
    }

    public function render($controllerName, $actionName, $params = null)
    {
        /**
         * @var \ManaPHP\Mvc\Dispatcher $dispatcher
         */
        $dispatcher = $this->_dependencyInjector->getShared('dispatcher');

        $viewsDir = APP_PATH . '/' . $dispatcher->getModuleName() . '/Views';
        $this->setViewsDir($viewsDir);
        $this->setLayoutsDir($viewsDir . '/Layouts/');

        parent::render($controllerName, ucfirst($actionName), $params);
    }
}