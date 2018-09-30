<?php
namespace Pimvc\Helper\Model;

interface IHelper
{

    const MODEL_DOMAIN_PREFIX = 'Model_Domain_Proscope_';
    const PARAM_REQUEST = 'Request';
    const DEFAULT_ADAPTER = 'mysql';
    const ADAPTER_4D = '4d';
    const ADAPTER_PGSQL = 'pgsql';
    const ADAPTER_MYSQL = self::DEFAULT_ADAPTER;
    const PDO_ADPATER_4D = 'Pdo4d';
    const PDO_ADPATER_MYSQL = 'PdoMysql';
    const PARAM_ID = 'id';
    const PARAM_COLUMN_NAME = 'column_name';
    const PARAM_RELATED_COLUMN_NAME = 'related_column_name';
    const PARAM_INDEX_ID = 'index_id';
    const PARAM_INDEX_TYPE = 'index_type';
    const PARAM_COLUMN_ID = 'column_id';
    const PARAM_CONSTRAINT_NAME = 'constraint_name';
    const PARAM_RELATED_TABLE_NAME = 'related_table_name';
    const PARAM_REFRENCED_TABLE_NAME = 'referenced_table_name';
    const PARAM_REFRENCED_COLUMN_NAME = 'referenced_column_name';
    const PARAM_RELATED_TABLE_ID = 'related_table_id';
    const PARAM_UNIQNESS = 'uniqueness';
    const PARAM_4D = '4d';
    const PARAM_MYSQL = 'mysql';
    const PARAM_KEY = 'key';
    const PARAM_EXTRA = 'extra';
    const PARAM_FIELD = 'field';
    const PARAM_TYPE = 'type';
    const PARAM_NAME = 'name';
    const PARAM_YES = 'Oui';
    const PARAM_NO = 'Non';
    const PARAM_LENGTH = 'length';
    const PARAM_DATA_LENGTH = 'data_length';
    const PARAM_DATA_TYPE = 'data_type';
    const PARAM_TABLES_4D = 'tables-4d';
    const LABEL_GENERATE_CODE = 'Code';
    const PARAM_BUTTON = 'button';
    const LIST_ACTION = 'proscope/list';
    const LAYOUT_NAME = 'responsive';
    const PARAM_HTML = 'html';
    const PARAM_NAV = 'nav';
    const VIEW_DATABASE_PATH = 'Views/Database/';

}
