<?php/** * Medoo数据库操作类* * @author XingShanghe<xingshanghe@gmail.com>* @date 2015-3-23 下午3:49:51 * @source Medoo.php* @version 2.0.0 * @copyright  Copyright 2015 sobey.com */ namespace Gaea;class Medoo extends \medoo{        /**     * 此方法获取sql语句，本做为memcached缓存键值使用，     * 现已废弃不建议再使用     * @param unknown $table     * @param unknown $join     * @param string $columns     * @param string $where     * @return string     */    public function select_sql($table, $join, $columns = null, $where = null)    {        return $this->select_context($table, $join, $columns, $where);    }        /**     * 重载medoo类的query方法，增加日志调试函数     * 正式环境中可删除     * (non-PHPdoc)     * @see medoo::query()     */    public function query($query)	{		if ($this->debug_mode)		{			echo $query;			$this->debug_mode = false;			return false;		}		array_push($this->logs, $query);		Core::log('SYSTEM', 'INFO', $query);				return $this->pdo->query($query);	}	/**	 * 重载medoo类的exec方法，增加日志调试函数	 * 正式环境中可删除	 * (non-PHPdoc)	 * @see medoo::exec()	 */	public function exec($query)	{		if ($this->debug_mode)		{			echo $query;			$this->debug_mode = false;			return false;		}		array_push($this->logs, $query);				Core::log('SYSTEM', 'INFO', $query);		return $this->pdo->exec($query);	}		public function where_clause($where){	    return parent::where_clause($where);	} 	}