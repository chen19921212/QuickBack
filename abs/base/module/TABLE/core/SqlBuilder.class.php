<?php

/**
 * @brief   用于构建单表SQL
 *          原属于BaseModel的一部分，为了方便维护，从BaseModel拆分出来；
 */

namespace ABS;

class SqlBuilder {

    // 不需要界定符的数据类型
    private static $NO_GLUE_TYPES = array(
        'TINYINT',     // 8
        'SMALLINT',    // 16
        'MEDIUMINT',   // 24
        'INT',         // 32
        'BIGINT',      // 64
        'DECIMAL',
    );

    // mysqli连接对象，用于escape输入数据，防止sql注入
    private $mysqli     = null;

    // 由于table中的mysqli连接都是基于数据库的，所以这里只需要表名
    private $tableName  = '';
    private $fieldTypes = array();

    public function __construct($mysqli, $tableName, $fieldTypes) {
        
        $this->mysqli     = $mysqli;
        $this->tableName  = $tableName;
        $this->fieldTypes = $fieldTypes;
    }

    public function createSelectSql($field = '*', $where = '', $order = '', $limit = '', $offset = '') {
        
        // 验证$field合法性
        if (empty($field) || !is_string($field)) {
            $msg = 'Field不合法，Field必须是字符串！ ' . __FILE__ . ':' . __LINE__;
            SLOG('mysql')->warn($msg);
            return false;
        }
        
        // 解析$where
        $where = $this->getWhereStr($where);
        if (false === $where) {
            $msg = '构建查询SQL，解析where失败！ ' . __FILE__ . ':' . __LINE__;
            SLOG('mysql')->warn($msg);
            return false;
        }
        
        $order = $this->getOrderStr($order);
        
        // 解析$limit
        $limit = $this->getLimitStr($limit, $offset);
        if (false === $limit) {
            $msg = '构建查询SQL，解析limit失败！ ' . __FILE__ . ':' . __LINE__;
            SLOG('mysql')->warn($msg);
            return false;
        }
        
        $where = empty($where) ? '' : ' ' . $where;
        $order = empty($order) ? '' : ' ' . $order;
        $limit = empty($limit) ? '' : ' ' . $limit;
        
        return "SELECT {$field} FROM {$this->tableName}{$where}{$order}{$limit}";
    }

    public function createDeleteSql($where) {
        
        // 删除操作不允许$where为空
        if (empty($where)) {
            $message = '删除操作时，where条件不允许为空！ ' . __FILE__ . ':' . __LINE__;
            SLOG('mysql')->warn($msg);
            return false;
        }
        
        $where = $this->getWhereStr($where);
        if ($where === false) {
            $msg = '构建删除SQL，解析where失败！ ' . __FILE__ . ':' . __LINE__;
            SLOG('mysql')->warn($msg);
            return false;
        }
        
        $where = empty($where) ? '' : ' ' . $where;
        return "DELETE FROM {$this->tableName}{$where}";
    }

    public function createUpdateSql($data, $where) {
        
        // 更新操作不允许$where为空
        if (empty($where)) {
            $message = '更新操作时，where条件不允许为空！ ' . __FILE__ . ':' . __LINE__;
            SLOG('mysql')->warn($msg);
            return false;
        }
        
        $data = $this->data2str($data);
        if ($data === false) {
            $msg = '构建更新SQL，解析data失败！ ' . __FILE__ . ':' . __LINE__;
            SLOG('mysql')->warn($msg);
            return false;
        }
        
        $where = $this->getWhereStr($where);
        if (empty($where) || $where === false) {
            $msg = '构建更新SQL，解析where失败！ ' . __FILE__ . ':' . __LINE__;
            SLOG('mysql')->warn($msg);
            return false;
        }
        
        return "UPDATE {$this->tableName} SET {$data} {$where}";
    }

    public function createInsertSql($data) {
        
        $data = $this->data2str($data);
        if ($data === false) {
            $msg = '构建插入SQL，解析data失败！ ' . __FILE__ . ':' . __LINE__;
            SLOG('mysql')->warn($msg);
            return false;
        }
        return "INSERT INTO {$this->tableName} SET {$data}";
    }

    /**
     * @brief  获取界定符
     * @param  $field 合法的字段名
     * @return 界定符 | false
     */
    private function getGlue($field) {
        
        $type = $this->fieldTypes[$field];
        if (in_array(strtoupper($type), self::$NO_GLUE_TYPES)) {
            return '';
        }
        return "'";
    }

    /**
     * @brief 是否是数值，字符串类型
     */
    private function isValid($value, $glue) {
        
        if (!is_numeric($value) && !is_string($value)) {
            return false;
        }
        
        // 如果当前是数值
        if ($glue === '') {
            return is_numeric($value);
        }
        return true;
    }

    /**
     * @brief   构建sql的where部分
     * @param   $where string|array 查询条件
     * @return  false | string
     */
    public function getWhereStr($where) {
        
        // 如果是空值，返回空串
        if (empty($where)) {
            return '';
        }
        
        // 验证$where合法性
        if (!is_string($where) && !is_array($where)) {
            $msg = 'Where不合法，Where必须是数组或者字符串！ ' . __FILE__ . ':' . __LINE__;
            SLOG('mysql')->warn($msg);
            return false;
        }
        
        // 如果是字符串，处理后返回
        if (is_string($where)) {
            return "WHERE {$where}";
        }
        
        // 判断是否有GROUP BY和HAVING，然后从$where中分离
        $groupBy = $having = '';
        if (isset($where['group_by'])) {
            $groupBy = $where['group_by'];
            if (isset($where['having'])) {
                $having = $where['having'];
                unset($where['having']);
            }
            unset($where['group_by']);
        }
        
        // 转换where条件
        $where = $this->dfsWhere($where);
        if ($where === false) {
            return false;
        }
        
        // 拼接
        $str = empty($where) ? '' : "WHERE {$where}";
        if (!empty($groupBy)) {
            $str .= " GROUP BY {$groupBy}";
            if (!empty($having)) {
                $str .= " HAVING {$having}";
            }
        }
        return $str;
    }

    /**
     * @brief   将查询数组转换成sql的where部分
     * @param   $where array
     * @param   $relation string 连接符AND或者OR
     * @return  string
     */
    public function dfsWhere($where, $relation = 'AND') {
        
        $ret = array();
        foreach ($where as $filter) {
            
            // 合法性判断
            if (empty($filter) || !is_array($filter)) {
                $msg = "转换条件失败，子条件必须是非空数组！附加信息：\$filter = {$filter} " . __FILE__ . ':' . __LINE__;
                SLOG('mysql')->warn($msg);
                return false;
            }
            
            if (key_exists('OR', $filter) && is_array($filter['OR'])) {
                // 如果是或条件，修改连接符后继续递归
                
                $orRet = array();
                $orRet = $this->dfsWhere($filter['OR'], 'OR');
                if ($orRet === false) {
                    return false;
                }
                $ret[] = "({$orRet})";
                
            } else if (is_array($filter[0])) {
                // 如果是且条件，继续递归
                
                $andRet = $this->dfsWhere($filter);
                if ($andRet === false) {
                    return false;
                }
                $ret[] = $andRet;
                
            } else if (is_string($filter[0]) &&  count($filter) == 1) {
                // 如果当前是一维数组，并且只有一个字符串元素
                
                $ret[] = $filter[0];
                
            } else if (is_string($filter[0]) && count($filter) == 3) {
                // 如果当前是一维数组，并且数组元素为3个
                
                $field = $filter[0];
                $op    = strtoupper($filter[1]);
                $value = $filter[2];
                $glue  = $this->getGlue($field);
                
                // 校验字段是否存在
                if (!key_exists($field, $this->fieldTypes)) {
                    $msg = "转换条件失败，字段不存在！附加信息：\$field = {$field} " . __FILE__ . ':' . __LINE__;
                    SLOG('mysql')->warn($msg);
                    return false;
                }
                
                if ($op == 'IN' || $op == 'NOT IN') {
                    
                    // IN操作时，$value是数组
                    if (!is_array($value)) {
                        $msg = "转换条件失败，当操作符为IN时，value必须是数组！附加信息：\$field = {$field} " . __FILE__ . ':' . __LINE__;
                        SLOG('mysql')->warn($msg);
                        return false;
                    }
                    
                    // 空数组处理
                    if (empty($value)) {
                        $ret[] = 'FALSE';
                        continue;
                    }
                    
                    // 校验$value数组中的每个值，并且对每个值escape
                    $arr = array();
                    foreach ($value as $val) {
                        if (!$this->isValid($val, $glue)) {
                            $msg = "转换条件失败，当操作符为IN时，value数组中的存在非法元素！附加信息：\$field = {$field} " . __FILE__ . ':' . __LINE__;
                            SLOG('mysql')->warn($msg);
                            return false;
                        }
                        $arr[] = $glue . $this->mysqli->real_escape_string($val) . $glue;
                    }
                    $arr = implode(', ', $arr);
                    $ret[] = "{$field} {$op} ({$arr})";
                    
                } else {
                    
                    // 对$value进行校验
                    if (!$this->isValid($value, $glue)) {
                        $msg = "转换条件失败，value必须是数字或者字符串！附加信息：\$field = {$field} " . __FILE__ . ':' . __LINE__;
                        SLOG('mysql')->warn($msg);
                        return false;
                    }
                    $value = $glue . $this->mysqli->real_escape_string($value) . $glue;
                    $ret[] = $field . ' ' . $op . ' ' . $value;
                }
            } else {
                $msg = "转换条件失败，因格式不正确，无法转换where条件！ " . __FILE__ . ':' . __LINE__;
                SLOG('mysql')->warn($msg);
                return false;
            }
        }
        return implode(' ' . $relation . ' ', $ret);
    }

    public function getOrderStr($order) {
        
        // 如果是空值，返回空串
        if (empty($order)) {
            return '';
        }
        return 'ORDER BY ' . $order;
    }

    public function getLimitStr($limit, $offset) {
        
        // 如果是空值，返回空串
        if ('' === $limit && '' === $offset) {
            return '';
        }
        
        $limit = empty($limit) ? 0 : $limit;
        $offset = empty($offset) ? 0 : $offset;
        
        // 整数校验
        if (!is_numeric($limit) || !is_numeric($offset) || intval($limit) != $limit || intval($offset) != $offset) {
            return false;
        }
        
        // offset存在时，limit必须存在
        if ($offset > 0 && $limit == 0) {
            return false;
        }
        
        if ($offset > 0) {
            return 'LIMIT ' . $offset . ', ' . $limit;
        }
        return 'LIMIT ' . $limit;
    }

    /**
     * @brief   将插入或者更新数据解析成字符串，如果数据非法，则返回false
     * @return  string | false
     */
    public function data2str($data) {
        
        // 判断数据合法性
        if (empty($data) || !is_string($data) && !is_array($data)) {
            $msg = '插入或者更新时，data必须是数组或者字符串，而且非空！ ' . __FILE__ . ':' . __LINE__;
            SLOG('mysql')->warn($msg);
            return false;
        }
        
        // 如果是字符串，直接返回
        if (is_string($data)) {
            return $data;
        }
        
        // 将数组解析成字符串
        $ret = array();
        foreach ($data as $field => $value) {
            $glue = $this->getGlue($field);
            
            // 校验每个字段
            if (!key_exists($field, $this->fieldTypes) || !$this->isValid($value, $glue)) {
                $msg = "插入或者更新时，data中存在非法字段或者非法的值！附加信息：\$field = '{$field}' " . __FILE__ . ':' . __LINE__;
                SLOG('mysql')->warn($msg);
                return false;
            }
            
            $ret[] = $field . ' = ' . $glue . $this->mysqli->real_escape_string($value) . $glue;
        }
        $ret = implode(', ', $ret);
        return $ret;
    }
}
