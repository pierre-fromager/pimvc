<?php
namespace Pimvc\Helper\Model;

class Pgsql implements IHelper
{

    protected $tableId;
    private $modelConfig;

    /**
     * __construct
     *
     * @param string $tableId
     * @return $this
     */
    public function __construct($tableId)
    {
        $this->tableId = $tableId;
        $this->modelConfig = \Pimvc\App::getInstance()->getConfig()->getSettings('dbPool');
        return $this;
    }

    /**
     * setColumnsPgsql
     *
     * @param int $tableId
     * @param boolean $withKey
     */
    public function getColumns($withKey = false)
    {
        // Colonnes
        //$columnsList = array();
        //$columnsData = array();
        $columnModel = new \Pimvc\Model\Pgsql\Columns($this->modelConfig);
        //$cols = $columnModel->getByTableName($tableId);
        //$c = -1;
        return $columnModel->getByTableName($this->tableId);
        /*
          var_dump($cols);
          die;
          foreach ($cols as $column) {
          $c++;
          $type = trim(preg_replace("/\([^)]+\)/", "", $column[self::PARAM_TYPE]));
          preg_match('#\((.*?)\)#', $column[self::PARAM_TYPE], $match);
          $length = !empty($match[1]) ? $match[1] : 12;
          $typePdo = (preg_match('/int/', $type)) ? PDO::PARAM_INT : PDO::PARAM_STR;
          $pdoLabel = \Pimvc\Db\Pdo\Types::getPdoLabel($typePdo);
          $columnsData[] = array(
          $c
          , $column[self::PARAM_FIELD]
          , $type
          , $pdoLabel
          , $length
          );
          $columnsList[] = array(
          self::PARAM_NAME => $column[self::PARAM_FIELD]
          , self::PARAM_TYPE => $pdoLabel
          , self::PARAM_LENGTH => $length
          );
          }
          unset($indexModel);
          $this->columns = ($withKey) ? $columnsList : $columnsData;
          unset($columnsList);
          unset($columnsData); */
    }

    /**
     * getIndexes
     *
     * @param string $tableId
     */
    public function getIndexes()
    {
        //$indexes = array();
        $pgsqlSchema = new \Pimvc\Model\Pgsql\Pclass($this->modelConfig);
        $resultModel = $pgsqlSchema->indexes($this->tableId);
        return $resultModel;
        /*
          var_dump($resultModel);
          die;
          $infosFields = $pgsqlSchema->_getInfoFields($tableId);
          $infosFields = \Pimvc\Tools\Arrayproto::array_column($infosFields, null, 'column');
          var_dump($resultModel, '<hr>', $infosFields);
          die;
          unset($pgsqlSchema);
          $c = -1;
          foreach ($resultModel as $index) {
          $c++;
          $indexId = $index[self::PARAM_KEY];
          $ai = $index[self::PARAM_EXTRA];
          if (!empty($indexId)) {
          $name = $index[self::PARAM_FIELD];
          $indexes[] = array(
          $c
          , $name
          , self::PARAM_INDEX_TYPE => $index[self::PARAM_KEY]
          , self::PARAM_UNIQNESS => ($ai == 'auto_increment') ? self::PARAM_YES : self::PARAM_NO
          );
          }
          }
          $this->indexes = $indexes;
          unset($indexes);
          unset($resultModel);
          unset($pgsqlSchema); */
    }
}
