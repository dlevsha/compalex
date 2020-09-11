<?php

class Driver extends BaseDriver
{
    const
        FUNCTIONS = 'TF',
        TABLES = 'U',
        VIEWS = 'V',
        PROCEDURE = 'P';


    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    public function getCompareTables()
    {
        return $this->_getCompareArray($this->_getSql(self::TABLES));
    }

    public function getCompareProcedures()
    {
        return $this->_getCompareArray($this->_getSql(self::PROCEDURE));
    }

    public function getCompareFunctions()
    {
        return $this->_getCompareArray($this->_getSql(self::FUNCTIONS));
    }

    public function getCompareViews()
    {
        return $this->_getCompareArray($this->_getSql(self::VIEWS));
    }

    public function getCompareKeys()
    {
        $query = "
                SELECT
                     t.name + ' [' + ind.name + '] '   ARRAY_KEY_1,
                     '' dtype,
                     col.name ARRAY_KEY_2
                FROM
                     sys.indexes ind
                INNER JOIN
                     sys.index_columns ic ON  ind.object_id = ic.object_id and ind.index_id = ic.index_id
                INNER JOIN
                     sys.columns col ON ic.object_id = col.object_id and ic.column_id = col.column_id
                INNER JOIN
                     sys.tables t ON ind.object_id = t.object_id
                WHERE
                     ind.is_primary_key = 0
                     AND ind.is_unique = 0
                     AND ind.is_unique_constraint = 0
                     AND t.is_ms_shipped = 0
                ORDER BY
                     t.name, ind.name, ind.index_id, ic.index_column_id ";

        return $this->_getCompareArray($query);
    }

    private function _getSql($type)
    {
        return "SELECT DISTINCT
                    sc.name AS ARRAY_KEY_2,
                    st.name  + '(' + CAST(sc.length AS varchar(10)) + ')' AS dtype ,
                    so.name AS ARRAY_KEY_1,
                    colorder
                FROM
                    <<BASENAME>>..syscolumns sc,
                    <<BASENAME>>..systypes st,
                    <<BASENAME>>..sysobjects so
                WHERE
                    sc.id = so.id AND
                    sc.xtype = st.xtype AND
                    so.xtype='{$type}'
		        ORDER BY
		            so.name,  colorder";
    }

}