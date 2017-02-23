<?php
namespace ManaPHP\Db;

use Phalcon\Mvc\User\Component;

class SqlDePreparer extends Component
{
    /**
     * @param mixed $value
     * @param int   $preservedStrLength
     *
     * @return int|string
     */
    protected function _parseBindValue($value, $preservedStrLength)
    {
        if (is_string($value)) {
            return $value;
        } elseif (is_int($value)) {
            return $value;
        } elseif ($value === null) {
            return 'NULL';
        } elseif (is_bool($value)) {
            return (int)$value;
        } else {
            return $value;
        }
    }

    /**
     * Active SQL statement in the object with replace the bind with value
     *
     * @param string $sql
     * @param array  $bind
     * @param int    $preservedStrLength
     *
     * @return string
     */
    public function dePrepare($sql, $bind, $preservedStrLength = -1)
    {
        if (count($bind) === 0) {
            return (string)$sql;
        }

        if (isset($bind[0])) {
            return (string)$sql;
        } else {
            $replaces = [];
            foreach ($bind as $key => $value) {
                $replaces[':' . $key] = $this->_parseBindValue($value, $preservedStrLength);
            }

            return (string)strtr($sql, $replaces);
        }
    }
}