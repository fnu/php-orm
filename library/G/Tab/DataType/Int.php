<?php

namespace G\Tab\DataType;

use \G\Tab\DataType_Abstract;

/**
 * 数据类型
 *
 * @author ghost
 */
class Int extends DataType_Abstract
{

    public function getTypeName()
    {
        return DataType_Abstract::INT;
    }

    public function getPhpType()
    {
        return 'Int';
    }

    protected function toSetFunc()
    {
        $name = $this->toAttributeName();
        return 'intval($' . $name . ')';
    }

    /**
     * 目前只是简单的记录一下而已
     *
     * @param string $colunType
     */
    public function parseColumnType($colunType)
    {
        $this->columnType = $colunType;
    }

}
