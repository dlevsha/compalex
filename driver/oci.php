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
        $query = "SELECT 
                    utc.TABLE_NAME AS ARRAY_KEY_1, 
                    utc.COLUMN_NAME AS ARRAY_KEY_2, 
                    utc.DATA_TYPE || '(' || utc.DATA_LENGTH || '), NULL: ' || utc.NULLABLE AS dtype
                FROM 
                    USER_TAB_COLUMNS utc, 
                    USER_TABLES av 
                WHERE 
                    utc.TABLE_NAME = av.TABLE_NAME
                ORDER BY 
                    utc.TABLE_NAME, 
                    utc.COLUMN_NAME, 
                    utc.COLUMN_ID";
        return $this->_getCompareArray($query);
    }

    public function getCompareProcedures()
    {
        $query = 'SELECT 
              NAME AS ARRAY_KEY_1,  
              LISTAGG(TEXT, \' \' on overflow truncate) WITHIN GROUP (ORDER BY LINE) AS ARRAY_KEY_2, 
              \'\' AS dtype
        FROM 
             user_source 
        WHERE 
            TYPE = \'PROCEDURE\'     
        GROUP BY 
             NAME, TYPE';

        return $this->_getCompareArray($query);
    }

    public function getCompareFunctions()
    {
        $query = 'SELECT 
              NAME AS ARRAY_KEY_1,  
              LISTAGG(TEXT, \' \' on overflow truncate) WITHIN GROUP (ORDER BY LINE) AS ARRAY_KEY_2, 
              TYPE AS dtype
        FROM 
             user_source
        WHERE 
            TYPE = \'FUNCTION\'     
        GROUP BY 
             NAME, TYPE';

        return $this->_getCompareArray($query);
    }

    public function getCompareTriggers()
    {
        $query = "SELECT
                    TABLE_NAME || '::' || TRIGGER_NAME || ' [' || TRIGGERING_EVENT || ' / ' || TRIGGER_TYPE || ' / ' || STATUS || ']' AS ARRAY_KEY_1,
                    TRIGGER_BODY AS ARRAY_KEY_2,
                    '' dtype
                  FROM 
                    USER_TRIGGERS";
        return $this->_getCompareArray($query);
    }

    public function getCompareViews()
    {
        $query = "SELECT 
                    utc.TABLE_NAME AS ARRAY_KEY_1, 
                    utc.COLUMN_NAME AS ARRAY_KEY_2, 
                    utc.DATA_TYPE || '(' || utc.DATA_LENGTH || '), NULL: ' || utc.NULLABLE AS dtype
                FROM 
                    USER_TAB_COLUMNS utc, 
                    USER_VIEWS av
                WHERE 
                    utc.TABLE_NAME = av.VIEW_NAME
                ORDER BY 
                    utc.TABLE_NAME, 
                    utc.COLUMN_NAME, 
                    utc.COLUMN_ID";
        return $this->_getCompareArray($query);
    }

    public function getCompareKeys()
    {
        $query = "
                SELECT
                   uic.TABLE_NAME || ' [' || uic.INDEX_NAME || '] ' AS ARRAY_KEY_1,
                   uic.COLUMN_NAME AS ARRAY_KEY_2,
                   'POS:' || uic.COLUMN_POSITION || ' / LEN: ' || uic.COLUMN_LENGTH || ' / SORT: ' || uic.DESCEND AS dtype
                FROM
                     USER_IND_COLUMNS uic,
                     USER_TABLES av
                WHERE
                      av.TABLE_NAME = uic.TABLE_NAME
                ORDER BY
                      uic.TABLE_NAME,
                      uic.INDEX_NAME,
                      uic.COLUMN_POSITION ";

        return $this->_getCompareArray($query);
    }


}