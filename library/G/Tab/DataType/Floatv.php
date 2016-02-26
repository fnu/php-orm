<?php

namespace G\Tab\DataType;

use \G\Tab\DataType_Abstract;

/**
 * 数据类型
 *
 * @author ghost
 */
class Floatv extends DataType_Abstract
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

    public function toPhpValue()
    {
        if (null === $this->getDefault()) {
            return parent::toPhpValue();
        }

        return floatval($this->getDefault());
    }

}
