<?php

namespace G;

final class Loader
{

    protected static $instance = null;

    public function __construct()
    {
        spl_autoload_register(array($this, 'autoload'));
    }

    /**
     * 单例
     *
     * @return \G\Loader
     */
    public static function getInstance()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function autoload($className)
    {
        return $this->import($className);
    }

    /**
     * 引入文件
     *
     * @staticvar array $_imported
     * @param string $filename
     * @return boolean
     */
    public function import($filename)
    {
        static $_imported = [];

        $filename = $this->filePath($filename);
        $filekey  = str_replace('/', '_', $filename);

        if (!isset($_imported[$filekey])) {
            if (!file_exists($filename)) {
                throw new Exception($filename . ': No such file or directory');
            }

            require($filename);
            $_imported[$filekey] = 1;

            return true;
        }

        return (isset($_imported[$filekey]) && $_imported[$filekey] === 1) ? true : false;
    }

    /**
     * 文件路径
     *
     * @param string $path
     * @return string
     */
    public function filePath($filename, $ext = '.php')
    {
        $prevPath = APPLICATION_PATH . '/library/';

        return $prevPath . str_replace('\\', '/', str_replace('_', '/', $filename)) . $ext;
    }

}
