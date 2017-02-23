<?php
namespace Application\Api;

use Application\Api\Controllers\TimeController;
use ManaPHP\Mvc\Router\Group;

class RouteGroup extends Group
{
    public function __construct($path)
    {
        parent::__construct($path);
        $this->addGet('/time/current', [TimeController::class, 'current']);
    }
}