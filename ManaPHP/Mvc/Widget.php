<?php
namespace ManaPHP\Mvc;

use Phalcon\Mvc\User\Component;

/**
 * Class ManaPHP\Mvc\Widget
 *
 * @package widget
 *
 * @property \Phalcon\Mvc\UrlInterface           $url
 * @property \Phalcon\Mvc\Model\ManagerInterface $modelsManager
 * @property \Phalcon\Db\AdapterInterface        $db
 */
abstract class Widget extends Component implements WidgetInterface
{

}