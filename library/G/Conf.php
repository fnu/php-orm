<?php

namespace G;

/**
 * @author ghost
 */
class Conf
{

    protected static $instance = null;

    /**
     * 从ini配置文件读取的.
     *
     * @var array
     */
    protected $conf = array();

    public function __construct()
    {
        $this->conf = parse_ini_file(APPLICATION_PATH . '/conf/orm.ini');
    }

    public function getKey($key, $def = null)
    {
        return isset($this->conf[$key]) ? $this->conf[$key] : $def;
    }

    /**
     * 单例
     *
     * @return \G\Conf
     */
    public static function getInstance()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     *
     * 读取配置
     *
     * @param string $key
     * @return mixed
     */
    public static function get($key, $def = null)
    {
        return self::getInstance()->getKey($key, $def);
    }

}
