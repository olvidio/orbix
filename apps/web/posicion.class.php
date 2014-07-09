<?php
namespace web;
use core;

class Posicion {
	
	/**
	 * id_div de Posicion
	 *
	 * @var string
	 */
	private $sid_div;
	/**
	 * Url de Posicion
	 *
	 * @var string
	 */
	private $surl;
	/**
	 * Bloque de Posicion
	 *
	 * @var string
	 */
	private $sbloque;
	/**
	 * parametros de Posicion
	 *
	 * @var array
	 */
	private $aParametros = array ();

	/* CONSTRUCTOR ------------------------------ */
	function __construct($php_self,$vars) {
		$this->surl = $php_self;
		$this->sbloque = 'main';
		$this->setParametros($vars);
	}
	
	/**
	 * coloca el cursor de posicion n posiciones atras.
	 *
	 * @var n número de posiciones a retroceder.
	 */
	public function go($n=0) {
		$aPosition = end($_SESSION['position']);
		for ($i=0; $i < $n; $i++) {
			$aPosition = prev($_SESSION['position']);
		}
		//print_r($_SESSION['position']);
		$this->surl = $aPosition['url'];
		$this->sbloque = $aPosition['bloque'];
		$this->aParametros = $aPosition['parametros'];
	}

	public function recordar() {
		//echo "<script>history.pushState({state:'new'},'New State','?new');</script>";
		// evitar que sea muy grande
		$this->limitar(20);

		$aPosition = array('url'=>$this->surl,'bloque'=>$this->sbloque,'parametros'=>$this->aParametros);
		$_SESSION['position'][] = $aPosition;
	}

	public function atras() {
		// puede ser que no haya donde volver
		if (empty($_SESSION['position'])) {
			return '';
		}
		$id_div = $this->getId_div();
		$id_div = empty($id_div)? 'ir_atras' : $id_div;
		$aPosition = end($_SESSION['position']);
		$aParam = $aPosition['parametros'];
		$url = $aPosition['url'];
		$sparametros = Hash::add_hash($aParam,$url);

		$html = '<div id="'.$id_div.'">';
		$html .= '	<form id="go">';
		$html .= '	url: <input id="url" type="text" value="' . $url .'" size=70><br>';
		$html .= '	parametros: <input id="parametros" type="text" value="' . $sparametros . '" size=70><br>';
		$html .= '	bloque: <input id="id_div" type="text" value="' . $aPosition['bloque'] . '" size=70>';
		$html .= '</form>';
		$html .= '</div>';
		return $html;
	}
	public function atras2() {
		$aPosition = end($_SESSION['position']);
		$aParam = $aPosition['parametros'];
		$url = $aPosition['url'];
		$sparametros = Hash::add_hash($aParam,$url);

		$html = '<div style="display: none;">';
		$html .= '<form id="go">';
		$html .= '	<input id="url" type="hidden" value="' . $url .'" size=70>';
		$html .= '	<input id="parametros" type="hidden" value="' . $sparametros . '" size=70>';
		$html .= '	<input id="id_div" type="hidden" value="' . $aPosition['bloque'] . '" size=70>';
		$html .= '</form>';
		$html .= '</div>';
		$html .= "<img onclick=fnjs_ir_a() src=".core\ConfigGlobal::$web_icons.'/flechas/left.gif border=0 height=40>';
		return $html;
	}

	private function limitar($n=10) {
		//if (isset($_SESSION['position']) & is_array($_SESSION['position']) & (count($_SESSION['position']) > 2*$n)) {
		if (isset($_SESSION['position'])) { // No sé poruqe no deja poner todo junto
			if(is_array($_SESSION['position']) & (count($_SESSION['position']) > 2*$n)) {
				array_splice($_SESSION['position'], -$n); // negativo empieza por el final.
			}
		}
	}

	/**
	 * estableix el valor de l'atribut id_div de Posicion
	 *
	 * @param string id_div
	 */
	function setId_div($id_div) {
		$this->sid_div = $id_div;
	}
	/**
	 * Recupera l'atribut id_div de Posicion
	 *
	 * @return string id_div
	 */
	function getId_div() {
		return $this->sid_div;
	}
	/**
	 * estableix el valor de l'atribut url de Posicion
	 *
	 * @param string url
	 */
	function setUrl($url) {
		$this->surl = $url;
	}
	/**
	 * Recupera l'atribut url de Posicion
	 *
	 * @return string url
	 */
	function getUrl() {
		return $this->surl;
	}
	/**
	 * estableix el valor de l'atribut bloque de Posicion
	 *
	 * @param string sbloque
	 */
	function setBloque($bloque) {
		$this->sbloque = $bloque;
	}
	/**
	 * Recupera l'atribut bloque de Posicion
	 *
	 * @return string sbloque
	 */
	function getBloque() {
		return $this->sbloque;
	}

	/**
	 * estableix el valor de tots els atributs parametros de Posicion que se li passen en un array
	 *
	 * @param array aVars
	 */
	public function setParametros($aVars) {

		foreach ($aVars as $key=>$value) {
			$this->aParametros[$key] = $value;
		}
	}
	/**
	 * recupera el valor de tots del parametre
	 *
	 * @param string nomParametre
	 */
	public function getParametro($nomParametre) {
		$valParametre = empty($this->aParametros[$nomParametre])? '' : $this->aParametros[$nomParametre];
		return $valParametre;
	}
}
?>
