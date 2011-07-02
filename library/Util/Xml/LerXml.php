<?php

class Util_LerXml {

	/**
	 * Array dos elementos xml
	 *
	 * @var array
	 */
	private $xmlArray = array();

	/**
	 *
	 * @param SimpleXMLElement $xmlElement
	 */
	public function read(SimpleXMLElement $xmlElement) {
		if($xmlElement->children()) {
			$this->read($xmlElement->children());
		}
		$this->xmlArray[] = $xmlElement;
	}
	

	/**
	 * @return array
	 */
	public function getXmlArray() {
		return $this->xmlArray;
	}

	/**
	 * @param array $xmlArray
	 */
	public function setXmlArray($xmlArray) {
		$this->xmlArray = $xmlArray;
	}


}


?>