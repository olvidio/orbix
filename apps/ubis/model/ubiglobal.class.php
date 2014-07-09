<?php
namespace ubis\model;
use core;
/**
 * Classe que implementa l'entitat ubis
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 01/10/2010
 */

Abstract class UbiGlobal Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */
	/**
	 * aPrimary_key de Ubi
	 *
	 * @var array
	 */
	 protected $aPrimary_key;

	/**
	 * aDades de Ubi
	 *
	 * @var array
	 */
	 protected $aDades;

	/**
	 * Tipo_ubi de Ubi
	 *
	 * @var string
	 */
	 protected $stipo_ubi;
	/**
	 * Id_ubi de Ubi
	 *
	 * @var integer
	 */
	 protected $iid_ubi;
	/**
	 * Nombre_ubi de Ubi
	 *
	 * @var string
	 */
	 protected $snombre_ubi;
	/**
	 * Dl de Ubi
	 *
	 * @var string
	 */
	 protected $sdl;
	/**
	 * Pais de Ubi
	 *
	 * @var string
	 */
	 protected $spais;
	/**
	 * Region de Ubi
	 *
	 * @var string
	 */
	 protected $sregion;
	/**
	 * Status de Ubi
	 *
	 * @var boolean
	 */
	 protected $bstatus;
	/**
	 * F_status de Ubi
	 *
	 * @var date
	 */
	 protected $df_status;
	/**
	 * Sv de Ubi
	 *
	 * @var boolean
	 */
	 protected $bsv;
	/**
	 * Sf de Ubi
	 *
	 * @var boolean
	 */
	 protected $bsf;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe vuit. 
	 */
	function __construct($a_id='') {
	}

	/* METODES PUBLICS ----------------------------------------------------------*/

	/* METODES ALTRES  ----------------------------------------------------------*/
	/* METODES PRIVATS ----------------------------------------------------------*/


	/* METODES GET i SET --------------------------------------------------------*/
	/**
	* Devuelve las direcciones de un ubi especificados por
	*
	* @return array de objetos Direccion
	*
	*/
	function getDirecciones($ordre='principal DESC') {
		$aClassName = explode('\\',get_called_class());
		$childClassName = end($aClassName);
		switch ($childClassName) {
			case 'Centro':
				$obj = 'ubis\model\GestorCtrxDireccion';
				$obj_dir = 'ubis\model\DireccionCtr';
			break;
			case 'CentroDl':
				$obj = 'ubis\model\GestorCtrDlxDireccion';
				$obj_dir = 'ubis\model\DireccionCtrDl';
			break;
			case 'CentroEx':
				$obj = 'ubis\model\GestorCtrExxDireccion';
				$obj_dir = 'ubis\model\DireccionCtrEx';
			break;
			case 'Casa':
				$obj = 'ubis\model\GestorCdcxDireccion';
				$obj_dir = 'ubis\model\DireccionCdc';
			break;
			case 'CasaDl':
				$obj = 'ubis\model\GestorCdcDlxDireccion';
				$obj_dir = 'ubis\model\DireccionCdcDl';
			break;
			case 'CasaEx':
				$obj = 'ubis\model\GestorCdcExxDireccion';
				$obj_dir = 'ubis\model\DireccionCdcEx';
			break;
		}
		$aWhere['id_ubi'] = $this->getId_ubi();
		$aWhere['_ordre'] = $ordre;
		$GesUbixDireccion = new $obj();
		$cUbixDireccion = $GesUbixDireccion->getUbixDirecciones($aWhere);
		$dirs = array();
		if ($cUbixDireccion !== false) {
			foreach ($cUbixDireccion as $oUbixDireccion) {
				$id_direccion = $oUbixDireccion->getId_direccion();
				$propietario = $oUbixDireccion->getPropietario();
				$Direccion = new $obj_dir($id_direccion);
				$dirs[] = $Direccion;
			}
		}
		return $dirs;
	}
	/**
	* Devuelve los teleco de un ubi especificados por
	*
	*	 parámetros $id_ubi,$tipo_teleco,$desc_teleco,$separador
	*		
	*	Si $desc_teleco es '*', entonces se añade la descripción entre paréntesis
	*      al final del número...
	*/
	function getTeleco($tipo_teleco,$desc_teleco,$separador) {
		$aClassName = explode('\\',get_called_class());
		$childClassName = end($aClassName);
		switch ($childClassName) {
			case 'Centro':
				$obj = 'ubis\model\GestorTelecoCtr';
			break;
			case 'CentroDl':
				$obj = 'ubis\model\GestorTelecoCtrDl';
			break;
			case 'CentroEx':
				$obj = 'ubis\model\GestorTelecoCtrEx';
			break;
			case 'Casa':
				$obj = 'ubis\model\GestorTelecoCdc';
			break;
			case 'CasaDl':
				$obj = 'ubis\model\GestorTelecoCdcDl';
			break;
			case 'CasaEx':
				$obj = 'ubis\model\GestorTelecoCdcEx';
			break;
		}
		$aWhere['id_ubi'] = $this->getId_ubi();
		$aWhere['tipo_teleco'] = $tipo_teleco;
		if ($desc_teleco != '*' && !empty($desc_teleco)) {
			$aWhere['desc_teleco'] = $desc_teleco;
		}
		$GesTelecoUbis = new $obj();
		$cTelecos = $GesTelecoUbis->getTelecos($aWhere);
		$tels='';
		$separador=empty($separador)? ".-<br>": $separador;
		if ($cTelecos !== false) {
		foreach ($cTelecos as $oTelecoUbi) {
			$iDescTel = $oTelecoUbi->getDesc_teleco();
			$num_teleco = trim ($oTelecoUbi->getNum_teleco());
			if ($desc_teleco=="*" && !empty($iDescTel)) {
				//$tels.=$num_teleco." (".$DescTel.")".$separador;
				$oDescTel = new DescTeleco($iDescTel);
				$tels.=$num_teleco."(".$oDescTel->getDesc_teleco().")".$separador;
			} else {
				$tels.=$num_teleco.$separador;
			}
		}
		}
		$tels=substr($tels,0,-(strlen($separador)));
		return $tels;
	}


	/**
	 * Recupera tots els atributs de Ubi en un array
	 *
	 * @return array aDades
	 */
	function getTot() {
		if (!is_array($this->aDades)) {
			$this->DBCarregar('tot');
		}
		return $this->aDades;
	}

	/**
	 * Recupera las claus primàries de Ubi en un array
	 *
	 * @return array aPrimary_key
	 */
	function getPrimary_key() {
		if (!isset($this->aPrimary_key )) {
			$this->aPrimary_key = array('iid_ubi' => $this->iid_ubi);
		}
		return $this->aPrimary_key;
	}

	/**
	 * Recupera l'atribut stipo_ubi de Ubi
	 *
	 * @return string stipo_ubi
	 */
	function getTipo_ubi() {
		if (!isset($this->stipo_ubi)) {
			$this->DBCarregar();
		}
		return $this->stipo_ubi;
	}
	/**
	 * estableix el valor de l'atribut stipo_ubi de Ubi
	 *
	 * @param string stipo_ubi='' optional
	 */
	function setTipo_ubi($stipo_ubi='') {
		$this->stipo_ubi = $stipo_ubi;
	}
	/**
	 * Recupera l'atribut iid_ubi de Ubi
	 *
	 * @return integer iid_ubi
	 */
	function getId_ubi() {
		if (!isset($this->iid_ubi)) {
			$this->DBCarregar();
		}
		return $this->iid_ubi;
	}
	/**
	 * estableix el valor de l'atribut iid_ubi de Ubi
	 *
	 * @param integer iid_ubi
	 */
	function setId_ubi($iid_ubi) {
		$this->iid_ubi = $iid_ubi;
	}
	/**
	 * Recupera l'atribut snombre_ubi de Ubi
	 *
	 * @return string snombre_ubi
	 */
	function getNombre_ubi() {
		if (!isset($this->snombre_ubi)) {
			$this->DBCarregar();
		}
		return $this->snombre_ubi;
	}
	/**
	 * estableix el valor de l'atribut snombre_ubi de Ubi
	 *
	 * @param string snombre_ubi='' optional
	 */
	function setNombre_ubi($snombre_ubi='') {
		$this->snombre_ubi = $snombre_ubi;
	}
	/**
	 * Recupera l'atribut sdl de Ubi
	 *
	 * @return string sdl
	 */
	function getDl() {
		if (!isset($this->sdl)) {
			$this->DBCarregar();
		}
		return $this->sdl;
	}
	/**
	 * estableix el valor de l'atribut sdl de Ubi
	 *
	 * @param string sdl='' optional
	 */
	function setDl($sdl='') {
		$this->sdl = $sdl;
	}
	/**
	 * Recupera l'atribut spais de Ubi
	 *
	 * @return string spais
	 */
	function getPais() {
		if (!isset($this->spais)) {
			$this->DBCarregar();
		}
		return $this->spais;
	}
	/**
	 * estableix el valor de l'atribut spais de Ubi
	 *
	 * @param string spais='' optional
	 */
	function setPais($spais='') {
		$this->spais = $spais;
	}
	/**
	 * Recupera l'atribut sregion de Ubi
	 *
	 * @return string sregion
	 */
	function getRegion() {
		if (!isset($this->sregion)) {
			$this->DBCarregar();
		}
		return $this->sregion;
	}
	/**
	 * estableix el valor de l'atribut sregion de Ubi
	 *
	 * @param string sregion='' optional
	 */
	function setRegion($sregion='') {
		$this->sregion = $sregion;
	}
	/**
	 * Recupera l'atribut bstatus de Ubi
	 *
	 * @return boolean bstatus
	 */
	function getStatus() {
		if (!isset($this->bstatus)) {
			$this->DBCarregar();
		}
		return $this->bstatus;
	}
	/**
	 * estableix el valor de l'atribut bstatus de Ubi
	 *
	 * @param boolean bstatus='f' optional
	 */
	function setStatus($bstatus='f') {
		$this->bstatus = $bstatus;
	}
	/**
	 * Recupera l'atribut df_status de Ubi
	 *
	 * @return date df_status
	 */
	function getF_status() {
		if (!isset($this->df_status)) {
			$this->DBCarregar();
		}
		return $this->df_status;
	}
	/**
	 * estableix el valor de l'atribut df_status de Ubi
	 *
	 * @param date df_status='' optional
	 */
	function setF_status($df_status='') {
		$this->df_status = $df_status;
	}
	/**
	 * Recupera l'atribut bsv de Ubi
	 *
	 * @return boolean bsv
	 */
	function getSv() {
		if (!isset($this->bsv)) {
			$this->DBCarregar();
		}
		return $this->bsv;
	}
	/**
	 * estableix el valor de l'atribut bsv de Ubi
	 *
	 * @param boolean bsv='f' optional
	 */
	function setSv($bsv='f') {
		$this->bsv = $bsv;
	}
	/**
	 * Recupera l'atribut bsf de Ubi
	 *
	 * @return boolean bsf
	 */
	function getSf() {
		if (!isset($this->bsf)) {
			$this->DBCarregar();
		}
		return $this->bsf;
	}
	/**
	 * estableix el valor de l'atribut bsf de Ubi
	 *
	 * @param boolean bsf='f' optional
	 */
	function setSf($bsf='f') {
		$this->bsf = $bsf;
	}
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$oUbiSet = new core\Set();

		$oUbiSet->add($this->getDatosTipo_ubi());
		$oUbiSet->add($this->getDatosNombre_ubi());
		$oUbiSet->add($this->getDatosDl());
		$oUbiSet->add($this->getDatosPais());
		$oUbiSet->add($this->getDatosRegion());
		$oUbiSet->add($this->getDatosStatus());
		$oUbiSet->add($this->getDatosF_status());
		$oUbiSet->add($this->getDatosSv());
		$oUbiSet->add($this->getDatosSf());
		return $oUbiSet->getTot();
	}



	/**
	 * Recupera les propietats de l'atribut stipo_ubi de Ubi
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosTipo_ubi() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'tipo_ubi'));
		$oDatosCampo->setEtiqueta(_("tipo_ubi"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut snombre_ubi de Ubi
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosNombre_ubi() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'nombre_ubi'));
		$oDatosCampo->setEtiqueta(_("nombre_ubi"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut sdl de Ubi
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosDl() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'dl'));
		$oDatosCampo->setEtiqueta(_("dl"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut spais de Ubi
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosPais() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'pais'));
		$oDatosCampo->setEtiqueta(_("pais"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut sregion de Ubi
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosRegion() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'region'));
		$oDatosCampo->setEtiqueta(_("region"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut bstatus de Ubi
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosStatus() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'status'));
		$oDatosCampo->setEtiqueta(_("status"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut df_status de Ubi
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosF_status() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'f_status'));
		$oDatosCampo->setEtiqueta(_("f_status"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut bsv de Ubi
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosSv() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'sv'));
		$oDatosCampo->setEtiqueta(_("sv"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut bsf de Ubi
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosSf() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'sf'));
		$oDatosCampo->setEtiqueta(_("sf"));
		return $oDatosCampo;
	}
}
?>
