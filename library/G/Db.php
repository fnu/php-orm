<?php

namespace G;

/**
 * 仅支持 MySql
 *
 * @author ghost
 */
class Db
{

    protected static $instance = null;

    /**
     * 数据库地址
     *
     * @var string
     */
    protected $dsn      = '';
    protected $dbName   = '';
    protected $username = '';
    protected $password = '';
    protected $host     = 'localhost';
    protected $port     = 3306;

    /**
     *
     * @var \PDO
     */
    protected $pdo = null;

    public function __construct()
    {
        $this->host     = Conf::get('database.host', 'localhost');
        $this->port     = Conf::get('database.port', 3306);
        $this->username = Conf::get('database.username', 'root');
        $this->dbName   = Conf::get('database.dbname', 'test');
        $this->password = Conf::get('database.password');

        $this->dsn = "mysql:host={$this->host};dbname={$this->dbName};port={$this->port}";


        $pdo = new \PDO($this->dsn, $this->username, $this->password, array(\PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES "UTF8"'));
        $pdo->setAttribute(\PDO::CASE_LOWER, true);
        $pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);

        $this->pdo = $pdo;
    }

    /**
     * 单例
     *
     * @return \G\Db
     */
    public static function getInstance()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * 取得PDO
     *
     * @return \PDO
     */
    public function getPdo()
    {
        return $this->pdo;
    }

    /**
     * 获取所有的表名
     *
     * @return array|null
     */
    public function getTables()
    {
        $sql = 'SELECT * FROM `information_schema`.`tables` '
                . ' WHERE '
                . " `table_schema` = '{$this->dbName}'";

        return $this->pdo->query($sql)->fetchAll();
    }

    /**
     * 获取表中的所有字段
     *
     * @param string $tabname
     * @return array|null
     */
    public function getColumns($tabname)
    {
        $tabname = trim($tabname);

        $sql = 'SELECT * FROM `information_schema`.`columns` '
                . ' WHERE '
                . " `table_schema` = '{$this->dbName}'"
                . " AND "
                . " `table_name` = '{$tabname}' ";

        return $this->pdo->query($sql)->fetchAll();
    }

}
