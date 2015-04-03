<?php

namespace ABS;
require_once __DIR__ . '/../../../../bootstrap.php';
require_once ABS_PATH . '/base/config/DbConfig.class.php';
require_once dirname(__DIR__) . '/core/BaseModel.class.php';
require_once dirname(__DIR__) . '/core/MysqliExt.class.php';
require_once dirname(__DIR__) . '/core/SqlBuilder.class.php';

DbConfig::$SAVE_SQL = false;

class ModelBuilder {
    
    public $server      = array();
    public $dbName      = '';
    public $tableName   = '';
    public $indexes     = '';
    
    public $mysqlExt    = null;
    
    public function __construct($server, $dbName, $tableName, $indexes) {
        
        $this->server       = $server;
        $this->dbName       = $dbName;
        $this->tableName    = $tableName;
        $this->indexes      = $indexes;
        $this->mysqlExt     = MysqliExt::obj($server, $dbName);
        if (false === $this->mysqlExt) {
            $message = "数据库连接失败！";
            trigger_error($message, E_USER_ERROR);
        }
    }
    
    public function getFieldTypes() {
        
        $sql = 'DESC ' . $this->tableName;
        $allTypes = $this->mysqlExt->queryList($sql);
        if ($allTypes === false) {
            trigger_error('获取字段失败！', E_USER_ERROR);
        }
        $ret = array();
        $maxLength = 0;
        foreach ($allTypes as $type) {
            $fieldName = $type['Field'];
            $typeName = strtoupper($type['Type']);
            if (strlen($fieldName) > $maxLength) {
                $maxLength = strlen($fieldName);
            }
            $pos = strpos($typeName, '(');
            $ret[$fieldName] = $pos === false ? $typeName : substr($typeName, 0, $pos);
        }
        
        foreach ($this->indexes as $indexName) {
            if (!empty($indexName) && !array_key_exists($indexName, $ret)) {
                trigger_error("键{$indexName}不存在！", E_USER_ERROR);
            }
        }
        
        $str = "array(\n";
        foreach ($ret as $fieldName => $typeName) {
            $blank = ' ';
            for ($i = 1; $i <= $maxLength - strlen($fieldName); $i ++) {
                $blank .= ' ';
            }
            $str .= "        '{$fieldName}'{$blank}=> '{$typeName}',\n";
        }
        $str .= '    )';
        return $str;
    }
    
    public function getClassName() {
        
        $className = '';
        $arr = explode('_', $this->tableName);
        foreach ($arr as $str) {
            $className .= ucfirst($str);
        }
        $className .= 'Model';
        return $className;
    }
    
    public function getIndexStr() {
        
        $str = implode('\', \'', $this->indexes);
        return "array('{$str}')";
    }
    
    public function createModels() {
        
        $className  = $this->getClassName();
        $fieldTypes = $this->getFieldTypes();
        $indexStr   = $this->getIndexStr();
        
        $code = <<<modelCode
<?php

namespace ABS;

class {$className} extends BaseModel {
    
    private static \$instance = null;
    
    public static function obj() {
        
        if (!self::\$instance) {
            self::\$instance = new self();
        }
        return self::\$instance;
    }
    
    protected \$mServer   = array();
    protected \$sServer   = array();
    protected \$dbName    = '{$this->dbName}';
    protected \$tableName = '{$this->tableName}';
    protected \$indexes   = {$indexStr};
    protected \$charset   = 'utf8';
    
    protected \$fieldTypes = {$fieldTypes};
    
    protected function __construct() {
        
        //\$this->mServer = DbConfig::\$WEB_MASTER;
        //\$this->sServer = DbConfig::\$WEB_SLAVE;
        
        parent::__construct();
    }
}
modelCode;
        
        $filePath = __DIR__ . '/' . $className . '.class.php';
        file_put_contents($filePath, $code);
        return true;
    }
}

$obj = new ModelBuilder(DbConfig::$WEB_MASTER, 'test', 'test', array('id'));
$ret = $obj->createModels();
echo $ret ? '创建成功' : '创建失败';
exit;
