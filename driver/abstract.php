<?php

abstract class BaseDriver
{
    protected $_dsn = array();

    protected static $_instance = null;

    protected function _getFirstConnect()
    {
        return $this->_getConnect(FIRST_DSN, FIRST_BASE_NAME);
    }


    protected function _getSecondConnect()
    {
        return $this->_getConnect(SECOND_DSN, SECOND_BASE_NAME);
    }

    protected function _getConnect($dsn)
    {
        if (!isset($this->_dsn[$dsn])) {
            $pdsn = parse_url($dsn);

            if (in_array(DRIVER, array('sqlserv', 'dblib', 'mssql'))) {
                $dsn = DRIVER . ':host=' . $pdsn['host'] . ':' . $pdsn['port'] . ';dbname=' . substr($pdsn['path'], 1, 1000) . ';charset=' . DATABASE_ENCODING;
            } elseif (in_array(DRIVER, array('oci', 'oci8'))) {
                $dsn = 'oci:dbname=(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(HOST=' . $pdsn['host'] . ')(PORT=' . $pdsn['port'] . '))(CONNECT_DATA=(SERVICE_NAME=' . substr($pdsn['path'], 1, 1000) . ')));charset=' . DATABASE_ENCODING;
            } else {
                $dsn = DRIVER . ':host=' . $pdsn['host'] . ';port=' . $pdsn['port'] . ';dbname=' . substr($pdsn['path'], 1, 1000) . (DRIVER !== 'pgsql' ? ';charset=' . DATABASE_ENCODING : '');
            }

            $this->_dsn[$dsn] = new PDO($dsn, $pdsn['user'], isset($pdsn['pass']) ? $pdsn['pass'] : '', array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ));
        }
        return $this->_dsn[$dsn];
    }

    protected function _select($query, $connect, $baseName)
    {
        $out = array();

        $query = str_replace('<<BASENAME>>', $baseName, $query);

        $stmt = $connect->prepare($query);
        $stmt->execute();

        while ($row = @$stmt->fetch()) {
            if (!isset($row['dtype']) && isset($row['DTYPE'])) {
                $row['dtype'] = $row['DTYPE'];
            }
            $out[] = $row;
        }
        return $out;
    }


    protected function _getCompareArray($query, $diffMode = false, $ifOneLevelDiff = false)
    {

        $out = array();
        $fArray = $this->_prepareOutArray($this->_select($query, $this->_getFirstConnect(), FIRST_BASE_NAME), $diffMode, $ifOneLevelDiff);
        $sArray = $this->_prepareOutArray($this->_select($query, $this->_getSecondConnect(), SECOND_BASE_NAME), $diffMode, $ifOneLevelDiff);

        $allTables = array_unique(array_merge(array_keys($fArray), array_keys($sArray)));
        sort($allTables);

        foreach ($allTables as $v) {
            $allFields = array_unique(array_merge(array_keys((array)@$fArray[$v]), array_keys((array)@$sArray[$v])));
            foreach ($allFields as $f) {
                switch (true) {
                    case (!isset($fArray[$v][$f])):
                    {
                        if (is_array($sArray[$v][$f])) $sArray[$v][$f]['isNew'] = true;
                        break;
                    }
                    case (!isset($sArray[$v][$f])):
                    {
                        if (is_array($fArray[$v][$f])) $fArray[$v][$f]['isNew'] = true;
                        break;
                    }
                    case (isset($fArray[$v][$f]['dtype']) && isset($sArray[$v][$f]['dtype']) && ($fArray[$v][$f]['dtype'] != $sArray[$v][$f]['dtype'])) :
                    {
                        $fArray[$v][$f]['changeType'] = true;
                        $sArray[$v][$f]['changeType'] = true;
                        break;
                    }
                }
            }
            $out[$v] = array(
                'fArray' => @$fArray[$v],
                'sArray' => @$sArray[$v]
            );
        }
        return $out;
    }

    private function _prepareOutArray($result, $diffMode, $ifOneLevelDiff)
    {
        $mArray = array();
        foreach ($result as $r) {
            if ($diffMode) {
                foreach (explode("\n", $r['ARRAY_KEY_2']) as $pr) {
                    $mArray[$r['ARRAY_KEY_1']][$pr] = $r;
                }

            } else {
                if ($ifOneLevelDiff) {
                    $mArray[$r['ARRAY_KEY_1']] = $r;
                } else {
                    $mArray[$r['ARRAY_KEY_1']][$r['ARRAY_KEY_2']] = $r;
                }
            }
        }
        return $mArray;
    }

    public function getCompareTables()
    {
        throw new Exception(__METHOD__ . ' Not work');
    }

    public function getAdditionalTableInfo()
    {
        return array();
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

    public function getCompareTriggers()
    {
        throw new Exception(__METHOD__ . ' Not work');
    }

    public function getTableRows($baseName, $tableName, $rowCount = SAMPLE_DATA_LENGTH)
    {
        if (!$baseName) throw new Exception('$baseName is not set');
        if (!$tableName) throw new Exception('$tableName is not set');
        $rowCount = (int)$rowCount;
        $tableName = preg_replace("$[^A-z0-9.,-_]$", '', $tableName);
        switch (DRIVER) {
            case "mssql":
            case "dblib":
            case "mssql":
            case "sqlsrv":
                $query = 'SELECT TOP ' . $rowCount . ' * FROM ' . $baseName . '..' . $tableName;
                break;
            case "pgsql":
            case "mysql":
                $query = 'SELECT * FROM ' . $tableName . ' LIMIT ' . $rowCount;
                break;
            case "oci":
            case "oci8":
                $query = 'SELECT * FROM ' . $tableName . ' FETCH FIRST ' . $rowCount . ' ROWS ONLY ';
                break;
            default:
                throw new Exception('Select query not set');

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
            $out = array();
        }

        if (DATABASE_ENCODING != 'utf-8' && $out) {
            // $out = array_map(function($item){ return array_map(function($itm){ return iconv(DATABASE_ENCODING, 'utf-8', $itm); }, $item); }, $out);
        }

        return $out;
    }


}