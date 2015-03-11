<?php

namespace G\Tab;

abstract class DataType_Abstract
{

    const INT       = 'int';
    const BIGINT    = 'bigint';
    const CHAR      = 'char';
    const VARCHAR   = 'varchar';
    const TEXT      = 'text';
    const DATETIME  = 'datatime';
    const TIME      = 'time';
    const TIMESTAMP = 'timestamp';
    const FLOAT     = 'float';
    const TINYINT   = 'tinyint';
    const SMALLINT  = 'smallint';
    const LONGTEXT  = 'longtext';

    /**
     * 备注
     *
     * @var string
     */
    protected $comments = '';

    /**
     * 字段名称
     *
     * @var string
     */
    protected $name = '';

    /**
     * 字段默认值
     *
     * @var mixed
     */
    protected $default = null;

    /**
     * 列定义, 来自数据库
     *
     * @var string
     */
    protected $columnType = '';

    /**
     * 字段大小
     *
     * @var int
     */
    protected $len = 0;

    /**
     * 所要生成的Model名称
     *
     * @var string
     */
    protected $modelName = '';

    /**
     *
     * @var 所属的表名
     */
    protected $tableName = '';

    /**
     * 来自数据的原始数据
     *
     * @var array
     */
    protected $columnData = array();

    /**
     * @return string
     */
    abstract public function getPhpType();

    /**
     * @return string
     */
    abstract protected function toSetFunc();

    /**
     * 解析字段的定义
     * @param string $colunType
     */
    abstract public function parseColumnType($colunType);

    public function __construct($columnData = array())
    {
        if (!empty($columnData)) {
            $this->columnData = $columnData;
            $this->parseData($columnData);
        }
    }

    public function parseData($columnData)
    {
        $this->setName($columnData['column_name']);
        $this->setComments($columnData['column_comment']);
        $this->setTableName($columnData['table_name']);
        $this->parseColumnType($columnData['column_type']);
        $this->setDefault($columnData['column_default']);
    }

    /**
     * @return int
     */
    public function getLen()
    {
        return $this->len;
    }

    public function setLen($len)
    {
        $this->len = intval($len);
        return $this;
    }

    /**
     * 字段的注释
     *
     * @return string
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * 为了保持良好的兼容性和可阅读性,
     * 尽量在控制在70个汉字以内, 并在一行中显示
     *
     * @param string $comments  字段注释
     * @return \G\Tab\DataType_Abstract
     */
    public function setComments($comments)
    {
        $this->comments = trim($comments);
        return $this;
    }

    /**
     * 字段名称, 原始值, 没有处理大小写, 下划线之类的事务
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * 设置字段名称, 此处不会处理大小写, 下划线之类的事务
     *
     * @param string $name  字段名称
     * @return \G\Tab\DataType_Abstract
     */
    public function setName($name)
    {
        $this->name = trim($name);
        return $this;
    }

    /**
     * 字段的默认值
     *
     * @return mixed
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * 设置 字段的默认值
     *
     * @param mixed $default    字段默认值
     * @return \G\Tab\DataType_Abstract
     */
    public function setDefault($default)
    {
        $this->default = $default;
        return $this;
    }

    /**
     * 设置 表名
     *
     * @param string $tableName
     * @return \G\Tab\DataType_Abstract
     */
    public function setTableName($tableName)
    {
        $this->tableName = trim($tableName);
        return $this;
    }

    /**
     * 表名
     */
    public function getTableName()
    {
        $this->tableName;
    }

    /**
     * Model的名称, 带有全名空间
     *
     * @return string
     */
    public function getModelName()
    {
        $name = '';

        foreach (explode('_', strtolower($this->tableName)) as $item) {
            $name .= '\\' . ucfirst($item);
        }

        return '\\Orm' . $name . 'Model';
    }

    /**
     * 获取 符合驼峰写法的字段名
     *
     * @return string
     */
    public function toAttributeName()
    {
        return preg_replace_callback(
                '/(_[a-z])/i', function($matches) {
            return strtoupper(trim($matches[1], '_'));
        }, trim(strtolower($this->name), '_'));
    }

    /**
     * 生成 setXXX 代码
     *
     * @return string
     */
    public function toSet()
    {
        $name = $this->toAttributeName();

        $str = ''
                . '    public function set' . ucfirst($name) . '($' . $name . ')' . "\n"
                . '    {' . "\n"
                . '        $this->' . $name . ' = ' . $this->toSetFunc() . ';' . "\n"
                . '        return $this;' . "\n"
                . '    }' . "\n";

        return $str;
    }

    public function toGet()
    {
        $name = $this->toAttributeName();

        $str = ''
                . '    public function get' . ucfirst($name) . "()\n"
                . '    {' . "\n"
                . '        return $this->' . $name . ";\n"
                . '    }' . "\n";

        return $str;
    }

    public function toGetComment()
    {
        $str = ''
                . '    /**' . "\n"
                . '     * 获取 ' . $this->getComments() . "\n"
                . '     *' . "\n"
                . '     * @return ' . $this->getPhpType() . "\n"
                . '     */' . "\n";
        return $str;
    }

    public function toSetComment()
    {
        $str = ''
                . '    /**' . "\n"
                . '     * 设置 ' . $this->getComments() . "\n"
                . '     *' . "\n"
                . '     * database: ' . $this->columnType . "\n"
                . '     * @param ' . $this->getPhpType() . ' $' . $this->toAttributeName()
                . ' ' . $this->getComments() . "\n"
                . "     * @return {$this->getModelName()}\n"
                . '     */' . "\n";

        return $str;
    }

    public function toAttributeComment()
    {
        $str = ''
                . '    /**' . "\n"
                . '     * ' . $this->getComments() . "\n"
                . '     *' . "\n"
                . '     * @var ' . $this->getPhpType() . ' $' . $this->toAttributeName() . "\n"
                . '     */' . "\n";

        return $str;
    }

    /**
     * 解析成PHP格式的值
     * @return null|string|float|int
     */
    public function toPhpValue()
    {
        return 'null';
    }

}
