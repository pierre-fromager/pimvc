<?php
namespace Pimvc\Helper\Model;

class Fourd implements IHelper
{

    protected $indexesType;
    protected $consColumns;
    protected $baseUrl;

    /**
     * __construct
     *
     * @param string $tableId
     * @return $this
     */
    public function __construct($tableId)
    {
        $app = \Pimvc\App::getInstance();
        $this->baseUrl = $app->getRequest()->getBaseUrl();
        $this->tableId = $tableId;
        $this->modelConfig = \Pimvc\App::getInstance()->getConfig()->getSettings('dbPool');
        return $this;
    }

    /**
     * getConscolumns
     *
     * @return array
     */
    public function getConscolumns()
    {
        $results = array();
        $indColumnModel = new \Pimvc\Model\Fourd\Conscolumns($this->modelConfig);
        $resultsCons = $indColumnModel->getByTableId($this->tableId);
        foreach ($resultsCons as $result) {
            $contraintName = strtolower($result[self::PARAM_CONSTRAINT_NAME]);
            $hasRelatedColum = (isset($result[self::PARAM_RELATED_COLUMN_NAME]));
            $results[$contraintName] = array(
                self::PARAM_COLUMN_NAME => strtolower(
                    $result[self::PARAM_COLUMN_NAME]
                )
                , self::PARAM_RELATED_COLUMN_NAME => ($hasRelatedColum) ? strtolower(
                    $result[self::PARAM_RELATED_COLUMN_NAME]
                ) : ''
            );
        }
        $this->consColumns = $results;
        return $results;
    }

    /**
     * getIndexes
     *
     * @return array
     */
    public function getIndexes()
    {
        $indColumnModel = new \Pimvc\Model\Fourd\Indcolumns($this->modelConfig);
        $resultModel = $indColumnModel->getByTableId($this->tableId);
        unset($indColumnModel);
        $indexesData = [];
        foreach ($resultModel as $index) {
            $type = $this->getIndexType($index[self::PARAM_INDEX_ID]);
            $indexesData[] = [
                $index[self::PARAM_COLUMN_ID]
                , strtolower($index[self::PARAM_COLUMN_NAME])
                , \Pimvc\Tools\Db\Fourd\Types::getIndexTypeLabel($type[self::PARAM_INDEX_TYPE])
                , ($type[self::PARAM_UNIQNESS] == 1) ? self::PARAM_YES : self::PARAM_NO
            ];
        }
        return $indexesData;
    }

    /**
     * getIndexesType
     *
     * @return array
     */
    public function getIndexesType()
    {
        $indexes = array();
        $indexModel = new \Pimvc\Model\Fourd\Indexes($this->modelConfig);
        $resultModel = $indexModel->getByTableId($this->tableId);
        foreach ($resultModel as $index) {
            $indexId = $index[self::PARAM_INDEX_ID];
            $indexes[$indexId] = [
                self::PARAM_INDEX_TYPE => $index[self::PARAM_INDEX_TYPE]
                , self::PARAM_UNIQNESS => $index[self::PARAM_UNIQNESS]
            ];
        }
        $this->indexesType = $indexes;
        return $indexes;
    }

    /**
     * getRelations
     *
     * @param boolean $withLink
     * @return array
     */
    public function getRelations($withLink = false)
    {
        $constraintsModel = new \Pimvc\Model\Fourd\Constraints($this->modelConfig);
        $resultModel = $constraintsModel->getByTableId($this->tableId);
        $relationData = array();
        $constraintInfo = array();
        unset($constraintsModel);
        foreach ($resultModel as $constraint) {
            $constraintName = strtolower($constraint[self::PARAM_CONSTRAINT_NAME]);
            $constraintInfo = $this->getConscolumn($constraintName);
            $relatedColumn = $constraintInfo[self::PARAM_RELATED_COLUMN_NAME];
            $columnName = $constraintInfo[self::PARAM_COLUMN_NAME];
            $hasRelation = isset($constraint[self::PARAM_RELATED_TABLE_ID]);
            $relationId = ($hasRelation) ? $constraint[self::PARAM_RELATED_TABLE_ID] : '';
            $relationName = ($hasRelation) ? $constraint[self::PARAM_RELATED_TABLE_NAME] : '';
            $tableLink = '<a class="foreignTableName" href="'
                . $this->baseUrl . '/database/tables4d/id/'
                . $relationId . '">'
                . $relationName . '</a>';
            $hasDeleteRule = isset($constraint['delete_rule']);
            $relationData[] = array(
                $columnName
                , ($withLink) ? $tableLink : $relationName
                , $relatedColumn
                , ($hasDeleteRule) ? self::PARAM_NO : self::PARAM_YES
            );
        }
        return $relationData;
    }

    /**
     * getColumns
     *
     * @param boolean $withKey
     * @return array
     */
    public function getColumns($withKey = false)
    {
        $columsModel = new \Pimvc\Model\Fourd\Columns($this->modelConfig);
        $resultModel = $columsModel->getByTableId($this->tableId);
        $columnsData = array();
        unset($columsModel);
        $columnsList = array();
        foreach ($resultModel as $column) {
            $type4d = $column['data_type'];
            $oldtype4d = $column['old_data_type'];
            $type4dLabel = \Pimvc\Tools\Db\Fourd\Types::getLabel($type4d);
            $typePdo = \Pimvc\Tools\Db\Fourd\Types::getPdo($type4d);
            $pdoLabel = \Pimvc\Tools\Db\Fourd\Types::getPdoLabel($typePdo);
            $columnsData[] = array(
                $column[self::PARAM_COLUMN_ID]
                , strtolower($column[self::PARAM_COLUMN_NAME])
                , $type4dLabel
                , $pdoLabel
                , $column['data_length']
            );
            $columnsList[] = array(
                self::PARAM_NAME => $column[self::PARAM_COLUMN_NAME]
                , 't4d' => $type4d
                , 'ot4d' => $oldtype4d
                , self::PARAM_TYPE => $pdoLabel
                , self::PARAM_LENGTH => $column['data_length']
            );
        }
        unset($resultModel);
        return ($withKey) ? $columnsList : $columnsData;
    }

    /**
     * getConscolumn
     *
     * @param string $contrainName
     * @return array
     */
    private function getConscolumn($contrainName)
    {
        return (isset($this->consColumns[$contrainName])) ? $this->consColumns[$contrainName] : '';
    }

    /**
     * getIndexType
     *
     * @param string $indexId
     * @return array
     */
    private function getIndexType($indexId)
    {
        return $this->indexesType[$indexId];
    }
}
