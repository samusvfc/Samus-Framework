<?php

/**
 * Classe Samus_CRUD_ObjectSql
 *
 * @author Vinicius
 */
class Samus_CRUD_ObjectSql {

    private $object;
    private static $attrArray = array();
    /**
     * @var ReflectionClass
     */
    private $ref;
    public static $nestingControl = array();

    const NESTING_LIMIT = 1;

    public function __construct($object) {
        $this->setObject($object);
        $this->ref = new ReflectionClass($object);
    }

    public function buildParamsArray($className=null, $baseClassName="", $baseAttr="") {

        $ref = new ReflectionClass($className);
        self::$attrArray[$ref->getName()] = array();

        if (empty($baseClassName)) {
            $baseClassName = $this->ref->getName();
        }

        foreach ($this->getPropertiesFromObject($className) as $propertie) {
            /* @var $propertie ReflectionPropertie */
            $refMethod = new ReflectionMethod($className, Samus_CRUD_MethodSintaxe::buildSetterName($propertie->getName()));

            $paramArray = $refMethod->getParameters();
            $param = $paramArray[0];
            /* @var $param ReflectionParameter */

            $paramClass = $param->getClass();


            if ($paramClass != null) {
                $this->buildParamsArray($paramClass->getName(), $this->ref->getName(), $propertie->getName());
                
            } else {

                if (empty($baseAttr)) {
                    self::$attrArray[$baseClassName][$propertie->getName()] = $propertie->getName();
                } else {
                    self::$attrArray[$baseClassName][$baseAttr][$ref->getName()][] = $propertie->getName();
                }
            }

        }
    }

    public function getAttrArray() {
        return self::$attrArray;
    }

    public function getPropertiesFromObject($className) {

        $ref = new ReflectionClass($className);
        $attrArray = array();

        foreach ($ref->getProperties() as $key => $p) {
            /* @var $p ReflectionProperty */

            $name = $p->getName();

            if ($name{0} != "_") {
                $attrArray[] = $p;
            }
        }
        return $attrArray;
    }

    public function getObject() {
        return $this->object;
    }

    public function setObject($object) {
        $this->object = $object;
    }

}