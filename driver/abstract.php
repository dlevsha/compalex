<?php

abstract class BaseDriver
{
    protected static $_instance = null;
    protected $_dsn = [];

    public function getCompareTables()
    {
        throw new Exception(__METHOD__ . ' Not work');
    }

    public function getCompareIndex()
    {
        throw new Exception(__METHOD__ . ' Not work');
    }

    public function getCompareProcedures()
    {
        throw new Exception(__METHOD__ . ' Not work');
    }

    public function getCompareFunctions()
    {
        throw new Exception(__METHOD__ . ' Not work');
    }

    public function getCompareViews()
    {
        throw new Exception(__METHOD__ . ' Not work');
    }

    public function getCompareKeys()
    {
        throw new Exception(__METHOD__ . ' Not work');
    }

    /**
     * @param $baseName
     * @param $tableName
     * @param int $rowCount
     * @return array
     * @throws Exception
     */
    public function getTableRows($baseName, $tableName, $rowCount = SAMPLE_DATA_LENGTH)
    {
        if (!$baseName) {
            throw new Exception('$baseName is not set');
        }
        if (!$tableName) {
            throw new Exception('$tableName is not set');
        }
        $rowCount = (int)$rowCount;
        $tableName = preg_replace("$[^A-z0-9.,-_]$", '', $tableName);
        switch (DRIVER) {
            case "mssql":
            case "dblib":
                $query = 'SELECT TOP ' . $rowCount . ' * FROM ' . $baseName . '..' . $tableName;
                break;
            case "pgsql":
            case "mysql":
                $query = 'SELECT * FROM ' . $tableName . ' LIMIT ' . $rowCount;
                break;

        }
        if ($baseName === FIRST_BASE_NAME) {
            $result = $this->_select($query, $this->_getFirstConnect(), FIRST_BASE_NAME);
        } else {
            $result = $this->_select($query, $this->_getSecondConnect(), SECOND_BASE_NAME);
        }

        if ($result) {
            $firstRow = array_shift($result);

            $out[] = array_keys($firstRow);
            $out[] = array_values($firstRow);

            foreach ($result as $row) {
                $out[] = array_values($row);
            }
        } else {
            $out = [];
        }

        if (DATABASE_ENCODING != 'utf-8' && $out) {
            // $out = array_map(function($item){ return array_map(function($itm){ return iconv(DATABASE_ENCODING, 'utf-8', $itm); }, $item); }, $out);
        }

        return $out;
    }

    /**
     * @param $query
     * @param bool|false $diffMode
     * @return array
     */
    protected function _getCompareArray($query, $diffMode = false)
    {

        $out = [];
        $fArray = $this->_prepareOutArray($this->_select($query, $this->_getFirstConnect(), FIRST_BASE_NAME), $diffMode);
        $sArray = $this->_prepareOutArray($this->_select($query, $this->_getSecondConnect(), SECOND_BASE_NAME), $diffMode);

        $allTables = array_unique(array_merge(array_keys($fArray), array_keys($sArray)));
        sort($allTables);

        foreach ($allTables as $v) {
            $allFields = array_unique(array_merge(array_keys((array)@$fArray[$v]), array_keys((array)@$sArray[$v])));
            foreach ($allFields as $f) {
                if (!isset($fArray[$v][$f])) {
                    $sArray[$v][$f]['isNew'] = true;
                }
                if (!isset($sArray[$v][$f])) {
                    $fArray[$v][$f]['isNew'] = true;
                }
            }
            $out[$v] = array(
                'fArray' => @$fArray[$v],
                'sArray' => @$sArray[$v]
            );
        }
        return $out;
    }

    /**
     * @param $result
     * @param $diffMode
     * @return array
     */
    private function _prepareOutArray($result, $diffMode)
    {
        $mArray = array();
        foreach ($result as $r) {
            if ($diffMode) {
                foreach (explode("\n", $r['ARRAY_KEY_2']) as $pr) {
                    $mArray[$r['ARRAY_KEY_1']][$pr] = $r;
                }

            } else {
                $mArray[$r['ARRAY_KEY_1']][$r['ARRAY_KEY_2']] = $r;
            }
        }
        return $mArray;
    }

    /**
     * @param $query
     * @param $connect
     * @param $baseName
     * @return array
     */
    protected function _select($query, $connect, $baseName)
    {
        $out = array();

        $query = str_replace('<<BASENAME>>', $baseName, $query);

        $stmt = $connect->prepare($query);
        $stmt->execute();

        while ($row = $stmt->fetch()) {
            $out[] = $row;
        }
        return $out;
    }

    protected function _getFirstConnect()
    {
        return $this->_getConnect(FIRST_DSN, FIRST_BASE_NAME);
    }

    /**
     * @param $dsn
     * @return mixed
     */
    protected function _getConnect($dsn)
    {
        if (!isset($this->_dsn[$dsn])) {
            $pdsn = parse_url($dsn);

            $dsn = DRIVER . ':host=' . $pdsn['host'] . ';dbname=' . substr($pdsn['path'], 1, 1000) . (DRIVER !== 'pgsql' ? ';charset=' . DATABASE_ENCODING : '');
            $this->_dsn[$dsn] = new PDO($dsn, $pdsn['user'], isset($pdsn['pass']) ? $pdsn['pass'] : '', array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ));
        }
        return $this->_dsn[$dsn];
    }

    protected function _getSecondConnect()
    {
        return $this->_getConnect(SECOND_DSN, SECOND_BASE_NAME);
    }

}