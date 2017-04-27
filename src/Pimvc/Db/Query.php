<?php

/**
 * Class query
 * 
 * @author Pierre Fromager <pf@pier-infor.fr>
 * 
 */
namespace Pimvc\Db;

class Query {

    const select = 'select';
    const error = 'There is a technical problem at this moment please contact live support :';

    protected $db;
    protected $error = null;

    protected function doQuery($sql, $bindParams = []) {
        $dsn = new Dsn();
        $db = Lib_db_connect::getConnection($dsn->dsn);
        $results = [];
        try {
            $sth = $db->prepare($sql);
            if ($db instanceof PDO) {
                $this->bindArray($sth, $bindParams);
            }
            $sth->execute();
            if (strtolower(substr($sql, 0, strlen(self::select))) === self::select) {
                $results = $sth->fetchAll(PDO::FETCH_ASSOC);
                $sth->closeCursor();
            }
        } catch (Exception $e) {
            die('Error');
            $this->error = self::error . $e->getMessage();
            echo $e->getMessage();
            $results[] = $this->error;
        }
        return $results;
    }

    protected function bindArray(&$pdoStatement, &$paArray) {
        foreach ($paArray as $k => $v) {
            @$pdoStatement->bindValue(':' . $k, $v);
        }
    }

    protected function doMultiQuery($queryPack) {
        if (is_array($queryPack)) {
            foreach ($queryPack as $query) {
                $resultArray[] = $this->doQuery($query);
            }
        }
        return $resultArray;
    }

}