<?php

/**
 *  路径分隔符
 */
define('DS', DIRECTORY_SEPARATOR);

/**
 * 应用的根目录
 */
define('APPLICATION_PATH', realpath('.'));

include APPLICATION_PATH . '/library/G/Loader.php';

\G\Loader::getInstance();
\G\Conf::getInstance();

$db = \G\Db::getInstance();

foreach ($db->getTables() as $tab) {
    $tabObj = new \G\Tab($tab);

    var_dump($tabObj->getModelFilePath());

    /*
     * 创建输出的目录
     */
    if (!file_exists(dirname($tabObj->getModelFilePath()))) {
        mkdir(dirname($tabObj->getModelFilePath()), 0777, true);
    }

    file_put_contents($tabObj->getModelFilePath(), $tabObj->toModelCode());
}
