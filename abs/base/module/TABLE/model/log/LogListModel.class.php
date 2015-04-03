<?php

namespace ABS;

class LogListModel extends BaseModel {
    
    private static $instance = null;
    
    public static function obj() {
        
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    protected $mServer   = array();
    protected $sServer   = array();
    protected $dbName    = 'quickback_blog';
    protected $tableName = 'log_list';
    protected $indexes   = array('log_id');
    protected $charset   = 'utf8';
    
    protected $fieldTypes = array(
        'log_id'      => 'INT',
        'type'        => 'TINYINT',
        'create_time' => 'INT',
        'level'       => 'TINYINT',
        'pid'         => 'INT',
        'remote_ip'   => 'BIGINT',
        'remote_port' => 'INT',
        'loc'         => 'VARCHAR',
        'url'         => 'VARCHAR',
        'request'     => 'TEXT',
        'message'     => 'TEXT',
    );
    
    protected function __construct() {
        
        $this->mServer = DbConfig::$WEB_MASTER;
        $this->sServer = DbConfig::$WEB_SLAVE;
        
        parent::__construct();
    }
}