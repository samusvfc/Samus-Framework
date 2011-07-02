<?php


class Samus_Database_MysqlDataTypes {

    /**
     * Tipos Numéricos do Mysql
     * @var array
     */
    private static $numericTypes = array(
    'INTEGER' ,
    'TINYINT' ,
    'SMALLINT' , 
    'MEDIUMINT' , 
    'INT' , 
    'BIGINT' , 
    'DECIMAL' , 
    'FLOAT' , 
    'DOUBLE' , 
    'REAL' ,
    'BIT' ,
    'SERIAL'
    );

    /**
     * Tipos de dados booleanos
     * @var array
     */
    private static $booleanTypes = array(
    'BOOL' , 
    'BOOLEAN'
    );

    /**
     * Tipos de data do MySql
     * @var array
     */
    private static $dateTimeTypes = array(
    'DATE' , 
    'DATETIME' ,
    'TIMESTAMP' ,
    'TIME' , 
    'YEAR'
    );

    /**
     * Tipos String
     * @var array
     */
    private static $stringTypes = array(
    'CHAR' ,
    'TINYTEXT' ,
    'TEXT' ,
    'MEDIUMTEXT' ,
    'LONGTEXT' ,
    'BINARY' ,
    'VARBINARY' ,
    'TINYBLOB' ,
    'MEDIUMBLOB' ,
    'BLOB' ,
    'LONGBLOB' ,
    'ENUM' ,
    'SET'
    );

    /**
     * Tipos de dados VARCHAR
     * @var array
     */
    private static $varcharTypes = array(
    'VARCHAR' 
    );

    /**
     * Tipos geométricos
     * @var array
     */
    private static $geometryTypes = array(
    'POINT' ,
    'LINESTRING' ,
    'POLYGON' ,
    'MULTIPOINT' ,
    'MULTILINESTRING' ,
    'MULTIPOLYGON' ,
    'GEOMETRYCOLLECTION'
    );

    const STRING_TYPE = "string";

    const NUMERIC_TYPE = "numeric";

    const DATE_TYPE = "date";

    const GEOMETRY_TYPE = "geometry";

    const BOOLEANS_TYPE = "boolean";

    const VARCHAR_TYPE = "varchar";

    /**
     * Obtem um array com todos os tipos em um só array
     * @return array
     */
    public static function getAllTypesArray() {
        $array = array();

        foreach(self::$varcharTypes as $v) {
            $array[] = $v;
        }

        foreach(self::$booleanTypes as $b) {
            $array[] = $b;
        }

        foreach(self::$dateTimeTypes as $d) {
            $array[] = $d;
        }

        foreach(self::$geometryTypes as $g) {
            $array[] = $g;
        }

        foreach(self::$numericTypes as $n) {
            $array[] = $n;
        }

        foreach(self::$stringTypes as $s) {
            $array[] = $s;
        }

        return $array;
    }

    /**
     * Define se o tipo de dado é numérico
     * @param $type
     * @return boolean
     */
    public static function isNumeric($type) {
        $isNumeric = false;
        $type = strtoupper($type);
        foreach(self::$numericTypes as $n) {
            if($type == $n) {
                $isNumeric = true;
                break;
            }
        }
        return $isNumeric;
    }

    /**
     * Define se o tipo de dado é uma data
     * @param $type
     * @return boolean
     */
    public static function isDate($type) {
        $isDate = false;
        $type = strtoupper($type);
        foreach(self::$dateTimeTypes as $n) {
            if($type == $n) {
                $isDate = true;
                break;
            }
        }
        return $isDate;
    }

    /**
     * Define se o tipo de dado é uma string
     * @param $type
     * @return boolean
     */
    public static function isString($type) {
        $isString = false;
        $type = strtoupper($type);
        foreach(self::$stringTypes as $n) {
            if($type == $n) {
                $isString = true;
                break;
            }
        }
        return $isString;
    }

    /**
     * Define se o tipo de dado é uma VARCHAR
     * @param $type
     * @return boolean
     */
    public static function isVarchar($type) {
        $isVarchar = false;
        $type = strtoupper($type);
        foreach(self::$varcharTypes as $n) {
            if($type == $n) {
                $isVarchar = true;
                break;
            }
        }
        return $isVarchar;
    }

    /**
     * Define se o tipo de dado é um Boolean
     * @param $type
     * @return boolean
     */
    public static function isBoolean($type) {
        $isBoolean = false;
        $type = strtoupper($type);
        foreach(self::$booleanTypes as $n) {
            if($type == $n) {
                $isBoolean = true;
                break;
            }
        }
        return $isBoolean;
    }

    /**
     * Define se o tipo de dado esta no array de tipos geométricos
     * @param $type string
     * @return boolean
     */
    public static function isGeometry($type) {
        $isGeometry = false;
        $type = strtoupper($type);
        foreach(self::$geometryTypes as $n) {
            if($type == $n) {
                $isGeometry = true;
                break;
            }
        }
        return $isGeometry;
    }

    /**
     * Define se um tipo é numérico, string, data o geométrico
     * @param $type string
     * @return boolean
     */
    public static function getTypeFromType($type) {
        if(self::isBoolean($type)) {
            return self::BOOLEANS_TYPE;
        } elseif(self::isVarchar($type)) {
            return self::VARCHAR_TYPE;
        } elseif(self::isString($type)) {
            return self::STRING_TYPE;
        } elseif(self::isNumeric($type)) {
            return self::NUMERIC_TYPE;
        } elseif(self::isDate($type)) {
            return self::DATE_TYPE;
        } elseif(self::isGeometry($type)) {
            return self::GEOMETRY_TYPE;
        } else {
            return false;
        }
    }


    /**
     * @return array
     */
    public static function getNumericTypes() {
        return self::$numericTypes;
    }

    /**
     * @param $numericTypes array
     */
    public static function setNumericTypes($numericTypes) {
        self::$numericTypes = $numericTypes;
    }

    /**
     * @return array
     */
    public static function getDateTimeTypes() {
        return self::$dateTimeTypes;
    }

    /**
     * @param $dateTimeTypes array
     */
    public static function setDateTimeTypes($dateTimeTypes) {
        self::$dateTimeTypes = $dateTimeTypes;
    }

    /**
     *
     * @return array
     */
    public static function getStringTypes()  {
        return self::$stringTypes;
    }

    /**
     * @param $stringTypes
     */
    public static function setStringTypes($stringTypes) {
        self::$stringTypes = $stringTypes;
    }

    /**
     * @return array
     */
    public static function getGeometryTypes() {
        return self::$geometryTypes;
    }

    /**
     * @param $geometryTypes array
     */
    public static function setGeometryTypes($geometryTypes) {
        self::$geometryTypes = $geometryTypes;
    }
}