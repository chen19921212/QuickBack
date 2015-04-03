<?php

namespace ABS;

abstract class BaseModel {
    
    // 主、从MysqliExt对象
    protected $mMysqliExt = null;
    protected $sMysqliExt = null;
    
    // SqlBuilder对象
    protected $sqlBuilder = null;
    
    protected function __construct() {
        
        $this->mMysqliExt = MysqliExt::obj($this->mServer, $this->dbName, $this->charset);
        $this->sMysqliExt = MysqliExt::obj($this->sServer, $this->dbName, $this->charset);
        $this->sqlBuilder = new SqlBuilder($this->mMysqliExt->getMysqli(), $this->tableName, $this->fieldTypes);
    }
    
    /**
     * @brief 防止克隆
     */
    final private function __clone() {}
    
    /**
     * @brief 允许获取Model的属性
     */
    public function __get($name) {
        
        return $this->$name;
    }
    
    /**
     * @brief 供外部使用
     */
    public function getMysqliExt($fromMaster = false) {
        
        return $fromMaster ? $this->mMysqliExt : $this->sMysqliExt;
    }
    
    /**
     * @brief 供外部使用
     */
    public function getSqlBuilder() {
        
        return $this->sqlBuilder;
    }
    
    public function insert($data) {
        
        $sql = $this->sqlBuilder->createInsertSql($data);
        if ($sql) {
            return $this->mMysqliExt->insert($sql);
        }
        return false;
    }
    
    /**
     * @brief   insertAll将当作一个事务来处理，只有全部插入成功才提交
     */
    public function insertAll($dataList) {
        
        $f = $this->begin();
        if (!$f) {
            return false;
        }
        foreach ($dataList as $data) {
            $ret = $this->insert($data);
            if (false === $ret) {
                $this->rollback();
                return false;
            }
        }
        $this->commit();
        return true;
    }
    
    public function update($data, $where) {
        
        $sql = $this->sqlBuilder->createUpdateSql($data, $where);
        if ($sql) {
            return $this->mMysqliExt->update($sql);
        }
        return false;
    }
    
    public function delete($where) {
        
        $sql = $this->sqlBuilder->createDeleteSql($where);
        if ($sql) {
            return $this->mMysqliExt->execute($sql);
        }
        return false;
    }
    
    public function getList($field = '*', $where = '', $order = '', $limit = '', $offset = '') {
        
        return $this->getAssocList('', $field, $where, $order, $limit, $offset);
    }
    
    public function getAll($field = '*', $where = '', $order = '', $limit = '', $offset = '') {
        
        $sql = $this->sqlBuilder->createSelectSql($field, $where, $order, $limit, $offset);
        if (empty($sql)) {
            return false;
        }
        return $this->sMysqliExt->queryList($sql);
    }
    
    public function getAssocList($key = '', $field = '*', $where = '', $order = '', $limit = '', $offset = '') {
        
        if (!empty($key) && !in_array($key, $this->indexes)) {
            trigger_error("key {$key} 不是表 {$this->tableName} 的唯一索引！", E_USER_ERROR);
        }
        
        $sql = $this->sqlBuilder->createSelectSql($field, $where, $order, $limit, $offset);
        if (empty($sql)) {
            return false;
        }
        $tmpList = $this->sMysqliExt->queryList($sql);
        if (empty($tmpList) || empty($this->indexes)) {
            return $tmpList;
        }
        // 如果key为空，那么取index第一个
        if (empty($key)) {
            $key = $this->indexes[0];
        }
        $retList = array();
        foreach ($tmpList as $row) {
            $retList[$row[$key]] = $row;
        }
        return $retList;
    }
    
    public function getRow($field = '*', $where = '', $order = '', $offset = '') {
        
        $sql = $this->sqlBuilder->createSelectSql($field, $where, $order, 1, $offset);
        return $this->getRowBySql($sql);
    }
    
    public function getRowBySql($sql) {
        
        if ($sql) {
            return $this->sMysqliExt->queryRow($sql);
        }
        return false;
    }
    
    public function getCount($where = '') {
        
        if (!empty($where) && is_array($where) && array_key_exists('group_by', $where)) {
            $list = $this->getAll('count(1)', $where);
            if (false === $list) {
                return false;
            }
            return count($list);
        }
        $row = $this->getRow('count(1)', $where);
        if (false === $row) {
            return false;
        }
        return current($row);
    }
    
    /**
     * @brief   开启一个事务
     */
    public function begin() {
        
        return $this->mMysqliExt->begin();
    }
    
    /**
     * @brief   提交当前事务
     */
    public function commit() {
        
        return $this->mMysqliExt->commit();
    }
    
    /**
     * @brief   回滚一个事务
     */
    public function rollback() {
        
        return $this->mMysqliExt->rollback();
    }
    
    /**
     * @brief   判断当前事务状态
     */
    public function inTrans() {
        
        return $this->mMysqliExt->inTrans();
    }
}
