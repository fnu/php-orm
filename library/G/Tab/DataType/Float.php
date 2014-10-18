<?php

namespace G\Tab\DataType;

use \G\Tab\DataType_Abstract;

/**
 * 数据类型
 *
 * @author ghost
 */
class Float extends DataType_Abstract
{

    public function getTypeName()
    {
        return DataType_Abstract::INT;
    }

    public function getPhpType()
    {
        return 'Float';
    }

    protected function toSetFunc()
    {
        $name = $this->toAttributeName();
        return 'floatval($' . $name . ')';
    }

    public function parseColumnType($colunType)
    {
        $this->columnType = $colunType;
    }

}
