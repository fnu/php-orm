<?php

namespace G\Obj\Model;

/**
 * 生成Model类的类属于
 *
 * @author ghost
 */
class Attribute
{

    /**
     * 属性名称, 应用于PHP中
     *
     * @var string
     */
    protected $phpName = '';

    /**
     * 属性类型, 应用于PHP中
     *
     * @var string
     */
    protected $phpType = '';

    /**
     * 字段注释, 来自数据库
     *
     * @var string
     */
    protected $comment = '';

    /**
     * 字段名称, 来自数据库
     *
     * @var string
     */
    protected $dbName = '';

    /**
     * 属性类型, 来自数据库
     *
     * @var string
     */
    protected $dbType = '';

    public function getPhpName()
    {
        return $this->phpName;
    }

    public function getPhpType()
    {
        return $this->phpType;
    }

    public function getComment()
    {
        return $this->comment;
    }

    public function getDbType()
    {
        return $this->dbType;
    }

    /**
     * 属性的PHP名称
     *
     * @param string $phpName
     * @return \G\Obj\Model\Attribute
     */
    public function setPhpName($phpName)
    {
        $this->phpName = $phpName;
        return $this;
    }

    /**
     * 属性的PHP类型
     *
     * @param string $phpType
     * @return \G\Obj\Model\Attribute
     */
    public function setPhpType($phpType)
    {
        $this->phpType = $phpType;
        return $this;
    }

    /**
     * 属性的注释, 来自数据库
     *
     * @param string $comment
     * @return \G\Obj\Model\Attribute
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
        return $this;
    }

    /**
     * 属性的名称, 来自数据库
     *
     * @param string $dbName
     * @return \G\Obj\Model\Attribute
     */
    public function setDbName($dbName)
    {
        $this->dbName = $dbName;
        return $this;
    }

    /**
     * 属性类型, 来自数据库
     *
     * @param string $dbType 属性类型
     * @return \G\Obj\Model\Attribute
     */
    public function setDbType($dbType)
    {
        $this->dbType = $dbType;
        return $this;
    }

}
