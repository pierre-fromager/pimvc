<?php
namespace Pimvc\Helper\Model;

class Mysql implements IHelper
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
     * getIndexes
     *
     * @param string $tableId
     */
    public function getIndexes()
    {
        $indexes = array();
        $indexModel = new \Pimvc\Model\Users($this->modelConfig);
        $resultModel = $indexModel->describeTable($this->tableId);
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
        return $indexes;
        /*
          $this->indexes = $indexes;
          unset($indexes);
          unset($resultModel);
          unset($indexModel); */
    }

    /**
     * getRelations
     *
     * @param boolean $withLink
     */
    public function getRelations($withLink = false)
    {
        $constraintsModel = new \Pimvc\Model\Mysql\Keycolumnusages($this->modelConfig);
        $constraints = $constraintsModel->getByTableName($this->tableId);
        unset($constraintsModel);
        $relationData = array();
        foreach ($constraints as $constraint) {
            if (isset($constraint[self::PARAM_REFRENCED_TABLE_NAME])) {
                $tableLink = '<a class="foreignTableName" href="'
                    . $this->baseUrl . 'database/tablesmysql/id/'
                    . $constraint[self::PARAM_REFRENCED_TABLE_NAME] . '">'
                    . ucfirst($constraint[self::PARAM_REFRENCED_TABLE_NAME])
                    . '</a>';
                $relatedColumn = $constraint[self::PARAM_REFRENCED_COLUMN_NAME];
                $relationData[] = array(
                    $constraint[self::PARAM_COLUMN_NAME]
                    , ($withLink) ? $tableLink : $constraint[self::PARAM_REFRENCED_TABLE_NAME]
                    , $relatedColumn
                    , self::PARAM_NO //($constraint['delete_rule'] == '') ? self::PARAM_NO : self::PARAM_YES
                );
            }
        }
        return $relationData;
        /*
          $this->relations = $relationData;
          unset($relationData);
          unset($constraints); */
    }

    /**
     * getColumns
     *
     * @param boolean $withKey
     */
    public function getColumns($withKey = false)
    {
        // Colonnes
        $columnsList = array();
        $columnsData = array();
        $indexModel = new \Pimvc\Model\Users($this->modelConfig);

        $cols = $indexModel->describeTable($this->tableId);
        $c = -1;
        foreach ($cols as $column) {
            $c++;
            $type = trim(preg_replace("/\([^)]+\)/", "", $column[self::PARAM_TYPE]));
            preg_match('#\((.*?)\)#', $column[self::PARAM_TYPE], $match);
            $length = !empty($match[1]) ? $match[1] : 12;
            $typePdo = (preg_match('/int/', $type)) ? \PDO::PARAM_INT : \PDO::PARAM_STR;
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
        return ($withKey) ? $columnsList : $columnsData;
        /*
          $this->columns = ($withKey) ? $columnsList : $columnsData;
          unset($columnsList);
          unset($columnsData); */
    }
}
