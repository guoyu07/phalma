<?php
namespace ManaPHP\Mvc;

use Phalcon\Di;

class Model extends \Phalcon\Mvc\Model
{
    public function initialize()
    {
        $this->useDynamicUpdate(true);
    }

    /**
     * @param string|array $parameters
     *
     * @return \Phalcon\Mvc\Model\ResultsetInterface|static[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * @param string|array $parameters
     *
     * @return \Phalcon\Mvc\Model\ResultsetInterface|static[]
     */
    public static function findAll($parameters = null)
    {
        return static::find($parameters);
    }

    /**
     * @param string|array $parameters
     *
     * @return static|false
     */
    public static function findFirst($parameters = null)
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return parent::findFirst($parameters);
    }

    /**
     * @param string|array $parameters
     *
     * @return bool
     */
    public static function exists($parameters)
    {
        /**
         * @var $modelsManager \Phalcon\Mvc\Model\Manager
         */
        $modelsManager = Di::getDefault()->getShared('modelsManager');
        $result = $modelsManager->createBuilder($parameters)
            ->columns('1 as stub')
            ->from(get_called_class())
            ->limit(1)->getQuery()->execute();

        return isset($result[0]);
    }

    /**
     * @param array        $columnValues
     * @param string|array $conditions
     * @param array        $bind
     *
     * @return int
     */
    public static function updateAll($columnValues, $conditions, $bind = [])
    {
        /**
         * @var $instance \Phalcon\Mvc\Model
         */
        $instance = new static();

        $instance->getWriteConnection()->update($instance->getSource(), $columnValues, $conditions, $bind);
        return $instance->getWriteConnection()->affectedRows();
    }

    /**
     * @param $conditions
     * @param $bind
     *
     * @return int
     */
    public static function deleteAll($conditions, $bind)
    {
        /**
         * @var $instance \Phalcon\Mvc\Model
         */
        $instance = new static();

        $instance->getWriteConnection()->delete($instance->getSource(), $conditions, $bind);
        return $instance->getWriteConnection()->affectedRows();
    }
}