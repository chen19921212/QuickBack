<?php

namespace ABS;

class ArticleListModel extends BaseModel {
    
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
    protected $tableName = 'article_list';
    protected $indexes   = array('article_id');
    protected $charset   = 'utf8';
    
    protected $fieldTypes = array(
        'article_id'  => 'INT',
        'category'    => 'TINYINT',
        'title'       => 'VARCHAR',
        'tags'        => 'VARCHAR',
        'content'     => 'TEXT',
        'pv'          => 'INT',
        'create_time' => 'INT',
        'hidden'      => 'TINYINT',
    );
    
    protected function __construct() {
        
        $this->mServer = DbConfig::$WEB_MASTER;
        $this->sServer = DbConfig::$WEB_SLAVE;
        
        parent::__construct();
    }
}