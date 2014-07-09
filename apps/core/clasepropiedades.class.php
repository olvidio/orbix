<?php
namespace core;
abstract class ClasePropiedades {
	/**
	 * oDbl de Grupo
	 *
	 * @var object
	 */
	 protected $oDbl;
	/**
	 * NomTabla de Grupo
	 *
	 * @var string
	 */
	 protected $sNomTabla;
	/* METODES GET i SET --------------------------------------------------------*/
	/**
	 * Recupera l'atribut oDbl de Grupo
	 *
	 * @return object oDbl
	 */
	public function getoDbl() {
		return $this->oDbl;
	}
	/**
	 * estableix el valor de l'atribut oDbl de Grupo
	 *
	 * @param object oDbl
	 */
	protected function setoDbl($oDbl) {
		$this->oDbl = $oDbl;
	}
	/**
	 * Recupera l'atribut sNomTabla de Grupo
	 *
	 * @return string sNomTabla
	 */
	public function getNomTabla() {
		return $this->sNomTabla;
	}
	/**
	 * estableix el valor de l'atribut sNomTabla de Grupo
	 *
	 * @param string sNomTabla
	 */
	protected function setNomTabla($sNomTabla) {
		$this->sNomTabla = $sNomTabla;
	}



    public function __get($nombre) {
    	$metodo = 'get' . ucfirst($nombre);
    	if (method_exists($this, $metodo))  return $this->$metodo();
    }
    public function __set($nombre, $valor) {
      $metodo = 'set' . ucfirst($nombre);
      if (method_exists($this, $metodo)) $this->$metodo($valor);
	}
}
?>
