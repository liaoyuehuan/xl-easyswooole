<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/15/015
 * Time: 10:07
 */

namespace App\Model\Impl;

use App\Http\Pagination;
use App\Model\IBaseModel;
use App\Vendor\Db\DbFactory;
use Core\Component\Spl\SplBean;
use Core\Http\Response;

abstract class AbstractBaseModel implements IBaseModel
{

    protected $pk = 'id';

    protected $expectUpdatePro = [];

    /**
     * @var SplBean
     */
    private $splBean;

    /**
     * @var \MysqliDb
     */
    protected $db;

    /**
     * @var array
     */
    protected $columns;

    /**
     * @var string
     */
    protected $tableName;

    /**
     * @var object[]
     */
    protected static $instance;

    public function __construct()
    {
        $this->db = DbFactory::getDbConnect();
        $this->splBean = $this->getSqlBean();
        $this->columns = $this->getColumns();
        $this->tableName = $this->getTableName();
    }

    /**
     * return static
     */
    public static function getInstance()
    {
        if (!isset(static::$instance[static::class])) {
            static::$instance[static::class] = new static;
        }
        return static::$instance[static::class];
    }


    /**
     * @param $id
     * @return $this->SplBeanClassName()
     */
    function get($id)
    {
        $data = $this->db->where($this->pk, $id)->getOne($this->tableName, $this->columns);
        return empty($data) ? null : $this->toBean($data);
    }

    /**
     * @param SplBean $bean
     * @return bool
     */
    function insert($bean)
    {
        return $this->db->insert($this->tableName, $bean->toArray(SplBean::FILTER_TYPE_NOT_NULL));
    }

    function insertMulti(array $data)
    {
        $mulInsertData = array_map(function (SplBean $value) {
            return $value->toArray(SplBean::FILTER_TYPE_NOT_NULL);
        }, $data);
        return $this->db->insertMulti($this->tableName, $mulInsertData);
    }


    /**
     * $@param $bean SplBean
     * @return bool
     */
    function merge($bean)
    {
        $get = 'get' . $this->propertyToHump($this->pk);
        $id = $bean->$get();
        if (empty($this->get($id))) {
            return $this->insert($bean);
        } else {
            return $this->update($id, $bean);
        }
    }


    /**
     * @param $id string
     * @param $bean SplBean
     * @return bool
     */
    function update($id, $bean)
    {
        $set = 'set' . $this->propertyToHump($this->pk);
        $bean->$set(null);
        $this->handleExpectUpdatePro($bean);
        return $this->db->where($this->pk, $id)->update($this->tableName, $bean->toArray(SplBean::FILTER_TYPE_NOT_NULL));
    }

    function getOne(callable $callable)
    {
        $callable !== null && $callable($this->db);
        $beanArray = $this->db->getOne($this->tableName, $this->columns);
        return empty($beanArray) ? null : $this->toBean($beanArray);
    }


    /**
     * @param int|array $numRows Array to define SQL limit in format Array ($offset, $count)
     *                  or only $count
     * @param callable $callable
     * @return array
     */
    function select($numRows = null, callable $callable = null)
    {
        $callable !== null && $callable($this->db);
        return $this->toBeanArray($this->db->get($this->tableName, $numRows, $this->columns));
    }

    /**
     * @param $page
     * @param $limit
     * @param callable|null $callable
     * @return Pagination
     */
    function pagination($page, $limit, callable $callable = null)
    {
        $callable !== null && $callable($this->db);
        $this->db->pageLimit = $limit;
        $data = $this->db->paginate($this->tableName, $page, $this->columns);;
        return new Pagination($this->db->totalCount, $this->toBeanArray($data));
    }

    /**\
     * @param SplBean $bean
     * @param int $numRows Limit on the number of rows that can be updated.
     * @param callable|null $callable
     */
    function updateByWhere($bean = null, $numRows = null, callable $callable = null)
    {
        $callable !== null && $callable($this->db);
        $this->db->update($this->tableName, $bean->toArray(), $numRows);
    }

    function insertGetInsertId($bean): string
    {
        if ($this->insert($bean)) {
            return $this->db->getInsertId();
        } else {
            return null;
        }
    }


    function createSplBeanFromData(array $data)
    {

        $filter_data = array_filter($data, function ($value) {
            if (in_array($value, $this->columns)) {
                return true;
            } else {
                return false;
            }
        }, ARRAY_FILTER_USE_KEY);
        return $this->getSplBeanInstance($filter_data);
    }


    protected function getTableName()
    {
        $shortName = (new \ReflectionClass($this->splBean))->getShortName();
        return $this->getTableNameFromShortName($shortName);
    }

    private function getTableNameFromShortName($shortName)
    {
        return strtolower(preg_replace('/(?<=[a-z])(?=[A-Z])/', '_', $shortName));
    }

    protected function getColumns()
    {
        return $this->getSplBeanInstance()->getVarList();
    }

    /**
     * @param array $args
     * @return SplBean
     */
    protected function getSplBeanInstance($args = [])
    {
        return (new \ReflectionClass($this->splBean))->newInstance($args);
    }

    protected function toBeanArray(array $dataArr)
    {
        return array_map(function ($value) {
            return $this->toBean($value);
        }, $dataArr
        );
    }

    protected function toBean(array $data)
    {
        return $this->getSplBeanInstance($data);
    }

    public function propertyToHump($property)
    {
        return str_replace('_', '', ucwords(ucfirst($property), '_'));
    }

    /**
     * @return string SplBeanClassName
     */
    abstract public function getSqlBean();

    protected function handleExpectUpdatePro(&$bean)
    {
        foreach ($this->expectUpdatePro as &$value) {
            $set = 'set' . $this->propertyToHump($value);
            $bean->$set(null);
        }
    }

}