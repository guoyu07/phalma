<?php
namespace ManaPHP\Mvc;

use Phalcon\DiInterface;
use Phalcon\Mvc\ModuleDefinitionInterface;
use Phalcon\Mvc\User\Component;

class Module extends Component implements ModuleDefinitionInterface
{
    public function registerAutoloaders(DiInterface $dependencyInjector = null)
    {

    }

    public function registerServices(DiInterface $dependencyInjector = null)
    {

    }
}