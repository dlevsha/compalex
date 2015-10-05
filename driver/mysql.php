<?php

class Driver extends BaseDriver
{

    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    public function getCompareTables()
    {
        return $this->_getTableAndViewResult('BASE TABLE');
    }

    private function _getTableAndViewResult($type)
    {
        $query = "SELECT
                    cl.TABLE_NAME ARRAY_KEY_1,
                    cl.COLUMN_NAME ARRAY_KEY_2,
                    cl.COLUMN_TYPE dtype
                  FROM information_schema.columns cl,  information_schema.TABLES ss
                  WHERE
                    cl.TABLE_NAME = ss.TABLE_NAME AND
                    cl.TABLE_SCHEMA = '<<BASENAME>>' AND
                    ss.TABLE_TYPE = '{$type}'
                  ORDER BY
                    cl.table_name ";
        return $this->_getCompareArray($query);
    }

    public function getCompareViews()
    {
        return $this->_getTableAndViewResult('VIEW');
    }

    public function getCompareProcedures()
    {
        return $this->_getRoutineResult('PROCEDURE');
    }

    private function _getRoutineResult($type)
    {
        $query = "SELECT
                    ROUTINE_NAME ARRAY_KEY_1,
                    ROUTINE_DEFINITION ARRAY_KEY_2,
                    '' dtype
                  FROM
                    information_schema.ROUTINES
                  WHERE
                    ROUTINE_SCHEMA = '<<BASENAME>>' AND
                    ROUTINE_TYPE = '{$type}'
                  ORDER BY
                    ROUTINE_NAME";
        return $this->_getCompareArray($query, true);
    }

    public function getCompareFunctions()
    {
        return $this->_getRoutineResult('FUNCTION');
    }

    public function getCompareKeys()
    {
        $query = 'SELECT
                    CONCAT(TABLE_NAME, \' [\', INDEX_NAME, \'] \') ARRAY_KEY_1,
                    COLUMN_NAME  ARRAY_KEY_2,
                    CONCAT(\'(\' , SEQ_IN_INDEX, \')\') dtype
                  FROM INFORMATION_SCHEMA.STATISTICS
                  WHERE
                    TABLE_SCHEMA = \'<<BASENAME>>\'
                  ORDER BY
                    TABLE_NAME, INDEX_NAME, SEQ_IN_INDEX';
        return $this->_getCompareArray($query);
    }

}