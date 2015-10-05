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
                    cl.TABLE_NAME \"ARRAY_KEY_1\",
                    cl.COLUMN_NAME \"ARRAY_KEY_2\",
                    cl.UDT_NAME dtype
                  FROM information_schema.columns cl,  information_schema.TABLES ss
                  WHERE
                    cl.TABLE_NAME = ss.TABLE_NAME AND
                    cl.TABLE_SCHEMA = 'public' AND
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
                    ROUTINE_NAME \"ARRAY_KEY_1\",
                    ROUTINE_DEFINITION \"ARRAY_KEY_2\",
                    '' dtype
                  FROM
                    information_schema.ROUTINES
                  WHERE
                    ROUTINE_SCHEMA = 'public' AND
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
        $query = 'select
                        CONCAT(t.relname , \' [\', i.relname, \'] \') AS "ARRAY_KEY_1",
                        a.attname  AS "ARRAY_KEY_2",
                        \'\' AS dtype
                    from
                        pg_class t,
                        pg_class i,
                        pg_index ix,
                        pg_attribute a
                    where
                        t.oid = ix.indrelid
                        and i.oid = ix.indexrelid
                        and a.attrelid = t.oid
                        and a.attnum = ANY(ix.indkey)
                        and t.relkind = \'r\'
                    order by
                        t.relname,
                        i.relname';


        return $this->_getCompareArray($query);
    }
}