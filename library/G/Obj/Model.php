<?php

namespace G\Obj;

/**
 * 用于生成 Model 类的类
 */
class Model
{

    /**
     * Model 的全名空间
     *
     * @var string
     */
    protected $namepace = '';

    /**
     * Model 所依赖的 use
     *
     * @var array
     */
    protected $uses = array();

    /**
     * Model 类注释
     *
     * @var string
     */
    protected $classComment = '';

    /**
     * Model 类名
     *
     * @var string
     */
    protected $className = '';

    /**
     * Model 所依赖的基类
     *
     * @var array
     */
    protected $classExtends = array();

    /**
     * 所对应的表名
     *
     * @var string
     */
    protected $tableName = '';

    /**
     * Model 的属性
     *
     * @var string
     */
    protected $attributes = array();

    /**
     * 对应 `information_schema`.`tables` 的数据
     *
     * @var array
     */
    protected $tableData = array();

    /**
     * 表对象
     *
     * @var \G\Tab
     */
    protected $tableObj = null;

    public function __construct($tableData = array())
    {
        if (empty($tableData)) {
            return;
        }

        $this->tableData = array_change_key_case($tableData);

        $this->parseTableData($this->tableData);
    }

    public function parseTableData($data)
    {
        /**
         * 表.注释
         */
        $this->setClassComment($data['table_comment']);

        /**
         * 表.名
         */
        $this->setClassName($data['table_name']);
        $this->setTableName($data['table_name']);

        $this->tableObj = new \G\Tab($data);
    }

    public function getNamepace()
    {
        return \G\Conf::get('orm.model.namepace') . $this->namepace;
    }

    public function getUses()
    {
        return $this->uses;
    }

    public function getClassComment()
    {
        return $this->classComment;
    }

    public function getClassName()
    {
        return $this->className . 'Model';
    }

    /**
     * 所依赖的基类
     *
     * @return array
     */
    public function getClassExtends()
    {
        return $this->classExtends;
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function getTableData()
    {
        return $this->tableData;
    }

    public function getTableName()
    {
        return $this->tableName;
    }

    public function setNamepace($namepace)
    {
        $this->namepace = $namepace;
        return $this;
    }

    public function setUses($uses)
    {
        $this->uses = $uses;
        return $this;
    }

    public function setClassComment($classComment)
    {
        $this->classComment = trim($classComment);
        return $this;
    }

    /**
     * 设置类的类名
     *
     * 会根据表名称来解析类名和命名空间;
     *
     * 例如:
     * 'user'       => 类名:'UserModel', 命名空间: ''
     * 'user_group' => 类名:'GroupModel', 命名空间: '/User'
     *
     *
     * @param string $tableName 表的名称
     * @return \G\Obj\Model
     */
    public function setClassName($tableName)
    {

        // 如果没有 '_'
        if (false === strpos($tableName, '_')) {
            $this->className = ucfirst(strtolower($tableName));
        }
        else {
            $temp = explode('_', trim(strtolower($tableName)));

            $this->className = ucfirst(array_pop($temp));

            $namepace = '';
            foreach ($temp as $item) {
                $namepace .= '\\' . ucfirst($item);
            }

            $this->setNamepace($namepace);
        }


        return $this;
    }

    public function setClassExtends($classExtends)
    {
        $this->classExtends = $classExtends;
        return $this;
    }

    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
        return $this;
    }

    public function setTableData($tableData)
    {
        $this->tableData = $tableData;
        return $this;
    }

    public function setTableName($tableName)
    {
        $this->tableName = $tableName;
        return $this;
    }

    /**
     * 文件所要保存的目录
     *
     * @return string
     */
    public function toFilePath()
    {
        $base = \G\Conf::get('orm.model.filepath');

        return $base . str_replace('\\', '/', $this->namepace) . '/';
    }

    /**
     * 文件所要保存的路径
     *
     * @return string
     */
    public function toFileName()
    {
        $base = \G\Conf::get('orm.model.filepath');

        return $base . str_replace('\\', '/', $this->namepace) . '/' . $this->className . '.php';
    }

    /**
     * Model 源代码
     *
     * @return string
     */
    public function toCode()
    {
        /**
         * to namespace
         * to use
         * to className (toExtends)
         * to Attributes
         * to getXX
         * to setXX
         * to toArray()
         * to mset()
         */
        $code = "<?php\n"
                . $this->toNamespace()
                . $this->toUses()
                . $this->toClassComment()
                . $this->toClassName()
                . "{\n"
                . $this->toArrtibutes()
                . "\n"
                . $this->toGetMethod()
                . $this->toSetMethod()
                . "}\n";


        return $code;
    }

    public function toNamespace()
    {
        $namespace = trim($this->getNamepace(), '\\');
        if (empty($namespace)) {
            return '';
        }

        $code = "\nnamespace {$namespace};\n";

        return $code;
    }

    public function toUses()
    {
        $code = '';
        $uses = $this->getUses();

        if (\G\Conf::get('orm.model.use')) {
            array_unshift($uses, \G\Conf::get('orm.model.use'));
        }

        foreach ($uses as $use) {
            $code .= "use {$use};\n";
        }

        if (!empty($code)) {
            $code = "\n" . $code . "\n";
        }

        return $code;
    }

    public function toClassComment()
    {
        $code = "/**\n"
                . ' * ' . $this->tableObj->getTableComment() . "\n"
                . " *\n"
                . " * Table:{$this->tableObj->getTableName()}\n"
                . " */\n";

        return $code;
    }

    public function toClassName()
    {
        return "class {$this->getClassName()}{$this->toExtends()}\n";
    }

    public function toExtends()
    {
        $extends = $this->getClassExtends();

        if (\G\Conf::get('orm.model.extend')) {
            array_unshift($extends, \G\Conf::get('orm.model.extend'));
        }

        return empty($extends) ? '' : (' extends ' . implode(', ', $extends));
    }

    public function toArrtibutes()
    {
        return $this->tableObj->toAttributes();
    }

    public function toGetMethod()
    {
        return $this->tableObj->toGetMethod();
    }

    public function toSetMethod()
    {
        return $this->tableObj->toSetMethod();
    }

    public function toToArray()
    {

    }

    public function toMset()
    {

    }

}
