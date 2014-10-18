<?php

namespace G\Tab\DataType;

use \G\Tab\DataType_Abstract;

/**
 * 数据类型
 *
 * @author ghost
 */
class Datetime extends DataType_Abstract
{

    public function getTypeName()
    {
        return DataType_Abstract::DATETIME;
    }

    public function getPhpType()
    {
        return 'String';
    }

    protected function toSetFunc()
    {
        $name = $this->toAttributeName();
        return 'trim($' . $name . ')';
    }

    public function parseColumnType($colunType)
    {
        $this->columnType = $colunType;
    }

}
