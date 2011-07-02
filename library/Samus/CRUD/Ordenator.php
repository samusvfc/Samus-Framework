<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Samus_CRUD_Ordenator
 *
 * @author samus
 */
class Samus_CRUD_Ordenator {

	/**
	 * Ordena um array de Objetos a partir de uma propriedade qualquer, é possível
	 * ordenar por uma propriedade de uma propriedade que seja também um objeto
	 *
	 * Ex.:
	 * DAO::orderBy($array , 'name');
	 * DAO::orderBy($array , 'property->property->property->name');
	 *
	 * @param array $objectArray
	 * @param string $propertyName
	 * @param boolean $desc se será em ordem inversa
	 * @return void;
	 */
	public static function orderBy(&$objectArray, $propertyName, $desc = false) {

		$isObj = false;
		if ($objectArray instanceof Samus_CRUD_Matrix) {
			$objectArray = $objectArray->getArrayCopy ();
			$isObj = true;
		}

		if(!function_exists("_daoObjSort")) {


			function _daoObjSort(&$objArray, $indexFunction, $propertyName, $desc, $sort_flags = 0) {
				$indices = array ();
				$copyOfArray = array();
				foreach ( $objArray as $obj ) {
					$indeces[] = _daoGetIndex ( $obj, $propertyName );
					$copyOfArray[] = $obj;
				}

				if($desc) {
				    asort($indeces , SORT_DESC);	
				} else {
					asort($indeces , SORT_ASC);
				}
				

				$auxArray = array();

				foreach ($indeces as $key => $a) {
					$auxArray[] = $copyOfArray[$key];
				}


				$objArray = $auxArray;

				if ($desc) {
					$objArray = array_reverse ( $objArray );
				}
			}

		}

		if(!function_exists("_daoGetIndex")) {

			function _daoGetIndex($obj, $propertyName) {
				$val = null;
				$strEval = '$val = $obj->' . Samus_CRUD_MethodSintaxe::buildGetterName($propertyName) . '(); ';
				eval ( $strEval );
				return $val;
			}

		}

		_daoObjSort ( $objectArray, '_daoGetIndex', $propertyName, $desc );

		if ($isObj) {
			return new Samus_CRUD_Matrix ( $objectArray );
		} else {
			return $objectArray;
		}

	}

}
