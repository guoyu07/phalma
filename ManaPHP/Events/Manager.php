<?php
namespace ManaPHP\Events;

class Manager extends \Phalcon\Events\Manager
{
    protected $_peeks = [];

    /**
     * @param callable $handler
     *
     * @return void
     */
    public function peekEvents($handler)
    {
        $this->_peeks[] = $handler;
    }

    public function fire($eventType, $source, $data = null, $cancelable = true)
    {
        foreach ($this->_peeks as $handler) {
            if ($handler instanceof \Closure) {
                $handler($source, $data, $eventType);
            } else {
                $handler[0]->{$handler[1]}($source, $data, $eventType);
            }
        }

        return parent::fire($eventType, $source, $data, $cancelable);
    }
}