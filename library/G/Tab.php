<?php

namespace G;

/**
 * 表
 *
 * @author ghost
 */
class Tab
{

    protected $tableCatalog   = null;
    protected $tableSchema    = null;
    protected $tableName      = null;
    protected $tableType      = null;
    protected $engine         = null;
    protected $version        = null;
    protected $rowFormat      = null;
    protected $tableRows      = null;
    protected $avgRowLength   = null;
    protected $dataLength     = null;
    protected $maxDataLength  = null;
    protected $indexLength    = null;
    protected $dataFree       = null;
    protected $autoIncrement  = null;
    protected $createTime     = null;
    protected $updateTime     = null;
    protected $checkTime      = null;
    protected $tableCollation = null;
    protected $checksum       = null;
    protected $createOptions  = null;
    protected $tableComment   = null;

    /**
     * 保存这个表所有的字段对象
     *
     * @var array
     */
    protected $fieldArr = array();

    /**
     * 不同的字段类型与生成类的对应关系
     *
     * @var array
     */
    protected $datatypeMap = array(
        'int'       => 'Int',
        'bigint'    => 'Int',
        'timestamp' => 'Int',
        'tinyint'   => 'Int',
        'smallint'  => 'Int',
        'char'      => 'String',
        'varchar'   => 'String',
        'datetime'  => 'Datetime',
        'text'      => 'String',
        'enum'      => 'Enum',
        'decimal'   => 'Float',
    );

    public function __construct($options = null)
    {
        if ($options) {
            $this->setOptions($options);
        }

        if ($this->tableName) {
            $this->loadField();
        }
    }

    public function loadField()
    {
        $fields = \G\Db::getInstance()->getColumns($this->getTableName());

        foreach ($fields as $field) {
            $this->pushField($field);
        }
    }

    public function pushField($field)
    {
        $field = array_change_key_case($field);

        if (!isset($field['data_type'])) {
            throw new \Exception('未来声明 data_type');
        }

        if (!isset($this->datatypeMap[$field['data_type']])) {
            throw new \Exception('未知的 data_type:' . $field['data_type'] . ', tabName:' . $this->getTableName());
        }

        $objName = '\\G\\Tab\\DataType\\' . $this->datatypeMap[$field['data_type']];

        /* @var $obj \G\Tab\DataType_Abstract */
        $obj = new $objName($field);

        $this->fieldArr[$field['column_name']] = $obj;
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

    public function getModelFilePath()
    {
        $path = APPLICATION_PATH . '/out/Model/Orm/';
        $temp = explode('_', strtolower($this->tableName));
        $name = ucfirst(end($temp));

        return $path . $name . '.php';
    }

    /**
     * Model的名称, 带有全名空间
     *
     * @return string
     */
    public function getBaseModelName()
    {
        $temp = explode('_', strtolower($this->tableName));
        $name = ucfirst(end($temp));

        return $name . 'Model';
    }

    /**
     * 生成源代码
     *
     * @return string  Model的源代码
     */
    public function toModelCode()
    {
        $code = "<?php\n\n";

        $code .= "namespace Orm;\n\n";

        $code .= "use " . \G\Conf::get('orm.model.use') . ";\n";
        $code .= "\n";
        $code .= "/**\n";
        $code .= " * 数据模型\n";
        $code .= " * {$this->getTableComment()}\n";
        $code .= " * Table: {$this->getTableName()}\n";
        $code .= " */\n";
        $code .= "class {$this->getBaseModelName()} extends " . \G\Conf::get('orm.model.extend') . "\n";
        $code .="{\n";
        $code .= $this->toAttributes();
        $code .= "\n";
        $code .= $this->toMethod();
        $code .="}\n";

        return $code;
    }

    /**
     * 生成 属性 部分的代码
     *
     * @return string
     */
    public function toAttributes()
    {
        $str = '';

        foreach ($this->fieldArr as $field) {
            $str .= $this->toAttributeItem($field);
        }

        return $str;
    }

    /**
     *
     * @param \G\Tab\DataType_Abstract $field
     */
    protected function toAttributeItem($field)
    {
        $str = "\n"
                . $field->toAttributeComment()
                . '    protected $' . $field->toAttributeName() . " = null;\n";

        return $str;
    }

    public function toGetMethod()
    {
        $str = '';

        foreach ($this->fieldArr as $field) {
            $str .= $field->toGetComment();
            $str .= $field->toGet() . "\n";
        }

        return $str;
    }

    public function toSetMethod()
    {
        $str = '';

        foreach ($this->fieldArr as $field) {
            $str .= $field->toSetComment();
            $str .= $field->toSet() . "\n";
        }

        return $str;
    }

    /**
     * 生成 method 部分的代码
     *
     * @return string
     */
    public function toMethod()
    {
        $str = '';

        foreach ($this->fieldArr as $field) {
            $str .= $field->toGetComment();
            $str .= $field->toGet();

            $str .= $field->toSetComment();
            $str .= $field->toSet();
        }

        $str .= "\n"
                . "    /**\n"
                . "     * @return array\n"
                . "     */\n"
                . "    public function toArray()\n"
                . "    {\n"
                . "        return array(\n";

        foreach ($this->fieldArr as $field) {
            $str .= "            '"
                    . $field->getName()
                    . "' => \$this->"
                    . $field->toAttributeName()
                    . ",\n";
        }

        $str .= "         );\n";
        $str .= "    }\n";

        return $str;
    }

    /**
     *
     * @return String
     */
    public function getTableCatalog()
    {
        return $this->tableCatalog;
    }

    /**
     * @param string $tableCatalog
     *
     * @return \G\Tab
     */
    public function setTableCatalog($tableCatalog)
    {
        $this->tableCatalog = trim($tableCatalog);
        return $this;
    }

    /**
     *
     * @return String
     */
    public function getTableSchema()
    {
        return $this->tableSchema;
    }

    /**
     * @param string $tableSchema
     *
     * @return \G\Tab
     */
    public function setTableSchema($tableSchema)
    {
        $this->tableSchema = trim($tableSchema);
        return $this;
    }

    /**
     *
     * @return String
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * @param string $tableName
     *
     * @return \G\Tab
     */
    public function setTableName($tableName)
    {
        $this->tableName = trim($tableName);
        return $this;
    }

    /**
     *
     * @return String
     */
    public function getTableType()
    {
        return $this->tableType;
    }

    /**
     * @param string $tableType
     *
     * @return \G\Tab
     */
    public function setTableType($tableType)
    {
        $this->tableType = trim($tableType);
        return $this;
    }

    /**
     *
     * @return String
     */
    public function getEngine()
    {
        return $this->engine;
    }

    /**
     * @param string $engine
     *
     * @return \G\Tab
     */
    public function setEngine($engine)
    {
        $this->engine = trim($engine);
        return $this;
    }

    /**
     *
     * @return String
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param string $version
     *
     * @return \G\Tab
     */
    public function setVersion($version)
    {
        $this->version = trim($version);
        return $this;
    }

    /**
     *
     * @return String
     */
    public function getRowFormat()
    {
        return $this->rowFormat;
    }

    /**
     * @param string $rowFormat
     *
     * @return \G\Tab
     */
    public function setRowFormat($rowFormat)
    {
        $this->rowFormat = trim($rowFormat);
        return $this;
    }

    /**
     *
     * @return String
     */
    public function getTableRows()
    {
        return $this->tableRows;
    }

    /**
     * @param string $tableRows
     *
     * @return \G\Tab
     */
    public function setTableRows($tableRows)
    {
        $this->tableRows = trim($tableRows);
        return $this;
    }

    /**
     *
     * @return String
     */
    public function getAvgRowLength()
    {
        return $this->avgRowLength;
    }

    /**
     * @param string $avgRowLength
     *
     * @return \G\Tab
     */
    public function setAvgRowLength($avgRowLength)
    {
        $this->avgRowLength = trim($avgRowLength);
        return $this;
    }

    /**
     *
     * @return String
     */
    public function getDataLength()
    {
        return $this->dataLength;
    }

    /**
     * @param string $dataLength
     *
     * @return \G\Tab
     */
    public function setDataLength($dataLength)
    {
        $this->dataLength = trim($dataLength);
        return $this;
    }

    /**
     *
     * @return String
     */
    public function getMaxDataLength()
    {
        return $this->maxDataLength;
    }

    /**
     * @param string $maxDataLength
     *
     * @return \G\Tab
     */
    public function setMaxDataLength($maxDataLength)
    {
        $this->maxDataLength = trim($maxDataLength);
        return $this;
    }

    /**
     *
     * @return String
     */
    public function getIndexLength()
    {
        return $this->indexLength;
    }

    /**
     * @param string $indexLength
     *
     * @return \G\Tab
     */
    public function setIndexLength($indexLength)
    {
        $this->indexLength = trim($indexLength);
        return $this;
    }

    /**
     *
     * @return String
     */
    public function getDataFree()
    {
        return $this->dataFree;
    }

    /**
     * @param string $dataFree
     *
     * @return \G\Tab
     */
    public function setDataFree($dataFree)
    {
        $this->dataFree = trim($dataFree);
        return $this;
    }

    /**
     *
     * @return String
     */
    public function getAutoIncrement()
    {
        return $this->autoIncrement;
    }

    /**
     * @param string $autoIncrement
     *
     * @return \G\Tab
     */
    public function setAutoIncrement($autoIncrement)
    {
        $this->autoIncrement = trim($autoIncrement);
        return $this;
    }

    /**
     *
     * @return String
     */
    public function getCreateTime()
    {
        return $this->createTime;
    }

    /**
     * @param string $createTime
     *
     * @return \G\Tab
     */
    public function setCreateTime($createTime)
    {
        $this->createTime = trim($createTime);
        return $this;
    }

    /**
     *
     * @return String
     */
    public function getUpdateTime()
    {
        return $this->updateTime;
    }

    /**
     * @param string $updateTime
     *
     * @return \G\Tab
     */
    public function setUpdateTime($updateTime)
    {
        $this->updateTime = trim($updateTime);
        return $this;
    }

    /**
     *
     * @return String
     */
    public function getCheckTime()
    {
        return $this->checkTime;
    }

    /**
     * @param string $checkTime
     *
     * @return \G\Tab
     */
    public function setCheckTime($checkTime)
    {
        $this->checkTime = trim($checkTime);
        return $this;
    }

    /**
     *
     * @return String
     */
    public function getTableCollation()
    {
        return $this->tableCollation;
    }

    /**
     * @param string $tableCollation
     *
     * @return \G\Tab
     */
    public function setTableCollation($tableCollation)
    {
        $this->tableCollation = trim($tableCollation);
        return $this;
    }

    /**
     *
     * @return String
     */
    public function getChecksum()
    {
        return $this->checksum;
    }

    /**
     * @param string $checksum
     *
     * @return \G\Tab
     */
    public function setChecksum($checksum)
    {
        $this->checksum = trim($checksum);
        return $this;
    }

    /**
     *
     * @return String
     */
    public function getCreateOptions()
    {
        return $this->createOptions;
    }

    /**
     * @param string $createOptions
     *
     * @return \G\Tab
     */
    public function setCreateOptions($createOptions)
    {
        $this->createOptions = trim($createOptions);
        return $this;
    }

    /**
     *
     * @return String
     */
    public function getTableComment()
    {
        return $this->tableComment;
    }

    /**
     * @param string $tableComment
     *
     * @return \G\Tab
     */
    public function setTableComment($tableComment)
    {
        $this->tableComment = trim($tableComment);
        return $this;
    }

    /**
     * 通用设置方法
     *
     * @param array $options    参数. 如果是类, 必需实现了toArray(), 或者Traversabl接口的类.
     * @return \Base\Model\AbstractModel
     */
    public function setOptions($options)
    {
        if (is_object($options)) {
            if (method_exists($options, 'toArray')) {
                $options = $options->toArray();
            }
            else if (!($options instanceof \Traversable)) {
                return $this;
            }
        }
        else if (!is_array($options)) {
            return $this;
        }

        foreach ($options as $key => $value) {
            // 所有的Key都转成 "驼峰命名法"
            $key = \G\Fun::toCamelCase(strtolower($key));

            $method = 'set' . ucfirst($key);
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }

        return $this;
    }

}
