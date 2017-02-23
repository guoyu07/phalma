<?php
namespace ManaPHP\Mvc\Model\Query;

class Builder extends \Phalcon\Mvc\Model\Query\Builder
{
    public function andWhere($conditions, $bindParams = null, $bindTypes = null)
    {
        if (is_scalar($bindParams)) {
            $conditions = trim($conditions);

            if (!strpos($conditions, ' ')) {
                $conditions .= ' =';
            }

            $parts = explode(' ', $conditions, 2);
            $conditions = preg_replace('#[a-z_][a-z0-9_]*#i', '[\\0]', $parts[0]) . ' ' . $parts[1];
            $column = str_replace('.', '_', $parts[0]);
            /** @noinspection CascadeStringReplacementInspection */
            $from = ['`', '[', ']'];
            $column = str_replace($from, '', $column);

            $conditions = $conditions . ' :' . $column . ':';
            $bindParams = [$column => $bindParams];
        }

        return parent::andWhere($conditions, $bindParams, $bindTypes);
    }

    /**
     * @param int $size
     * @param int $current
     *
     * @return static
     */
    public function page($size, $current = 1)
    {
        $current = (int)max(1, $current);

        $this->_limit = (int)$size;
        $this->_offset = (int)($current - 1) * $size;

        return $this;
    }

    /**
     * @return [][]|[]
     */
    public function execute()
    {
        $result = $this->getQuery()->execute();
        if ($result === false) {
            return [];
        } else {
            return $result->toArray();
        }
    }

    /**
     * @return int
     */
    protected function _getTotalRows()
    {
        $this->_columns = 'COUNT(*) as [row_count]';
        $this->_limit = null;
        $this->_offset = null;
        $this->_order = null;

        if ($this->_group === null) {
            $result = $this->execute();
            return $result[0]['row_count'];
        } else {
            $result = $this->execute();
            return count($result);
        }
    }

    public function executeEx(&$totalRows)
    {
        $copy = clone $this;

        $result = $this->execute();
        if (!$this->_limit) {
            $totalRows = count($result);
        } else {
            if (count($result) % $this->_limit === 0) {
                $totalRows = $copy->_getTotalRows();
            } else {
                $totalRows = $this->_offset + count($result);
            }
        }

        return $result;
    }
}