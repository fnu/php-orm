<?php

namespace G\Tab\DataType;

use \G\Tab\DataType_Abstract;

/**
 * 数据类型
 *
 * @author ghost
 */
class String extends DataType_Abstract
{

    public function getTypeName()
    {
        return DataType_Abstract::VARCHAR;
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
