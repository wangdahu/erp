<?php

class AppMigrate extends Migrate {
    
    private $_tables;
    
    public function __construct(){
        parent::__construct();
        $this->_tables = require_once  __DIR__ . '/tables.php';
    }
    
    public function install() {
        $this->createTables();
        $this->initData();
    }

    public function uninstall() {
        $this->dropTables();
    }

    /**
     * 创建数据库表
     */
    protected function createTables() {
        foreach ($this->_tables as $tablename => $tabledata) {
            $this->db->createCommand()->createTable($tablename, $tabledata['fields'], empty($tabledata['comments']) ? null : "comment '{$tabledata['comments']}'");
        }
    }

    /**
     * 删除数据库表
     */
    protected function dropTables() {
        foreach ($this->_tables as $tablename => $tabledata) {
            $this->db->createCommand()->dropTable($tablename);
        }
    }

    /**
     * 初始化数据
     */
    protected function initData() {

    }

}
