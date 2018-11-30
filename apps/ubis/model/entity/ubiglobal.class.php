<?php
namespace ubis\model\entity;
use core;
use web;
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
	 * @var web\DateTimeLocal
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
				$obj = 'ubis\model\entity\GestorCtrxDireccion';
				$obj_dir = 'ubis\model\entity\DireccionCtr';
			break;
			case 'CentroDl':
				$obj = 'ubis\model\entity\GestorCtrDlxDireccion';
				$obj_dir = 'ubis\model\entity\DireccionCtrDl';
			break;
			case 'CentroEx':
				$obj = 'ubis\model\entity\GestorCtrExxDireccion';
				$obj_dir = 'ubis\model\entity\DireccionCtrEx';
			break;
			case 'Casa':
				$obj = 'ubis\model\entity\GestorCdcxDireccion';
				$obj_dir = 'ubis\model\entity\DireccionCdc';
			break;
			case 'CasaDl':
				$obj = 'ubis\model\entity\GestorCdcDlxDireccion';
				$obj_dir = 'ubis\model\entity\DireccionCdcDl';
			break;
			case 'CasaEx':
				$obj = 'ubis\model\entity\GestorCdcExxDireccion';
				$obj_dir = 'ubis\model\entity\DireccionCdcEx';
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
				$obj = 'ubis\model\entity\GestorTelecoCtr';
			break;
			case 'CentroDl':
				$obj = 'ubis\model\entity\GestorTelecoCtrDl';
			break;
			case 'CentroEx':
				$obj = 'ubis\model\entity\GestorTelecoCtrEx';
			break;
			case 'Casa':
				$obj = 'ubis\model\entity\GestorTelecoCdc';
			break;
			case 'CasaDl':
				$obj = 'ubis\model\entity\GestorTelecoCdcDl';
			break;
			case 'CasaEx':
				$obj = 'ubis\model\entity\GestorTelecoCdcEx';
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
	 * @return web\DateTimeLocal df_status
	 */
	function getF_status() {
	    if (!isset($this->df_status)) {
	        $this->DBCarregar();
	    }
	    if (empty($this->df_status)) {	    	return new web\NullDateTimeLocal();	    }	    $oConverter = new core\Converter('date', $this->df_status);
	    return $oConverter->fromPg();
	}
	/**
	 * estableix el valor de l'atribut df_status de Ubi	* Si df_status es string, y convert=true se convierte usando el formato webDateTimeLocal->getFormat().	* Si convert es false, df_status debe ser un string en formato ISO (Y-m-d). Corresponde al pgstyle de la base de datos.	*	* @param date|string df_status='' optional.	* @param boolean convert=true optional. Si es false, df_status debe ser un string en formato ISO (Y-m-d).	 */
	function setF_status($df_status='',$convert=true) {
		if ($convert === true && !empty($df_status)) {
	        $oConverter = new core\Converter('date', $df_status);
	        $this->df_status =$oConverter->toPg();
	    } else {
	        $this->df_status = $df_status;
	    }
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
	 * @return core\DatosCampo
	 */
	function getDatosTipo_ubi() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'tipo_ubi'));
		$oDatosCampo->setEtiqueta(_("tipo de ubi"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut snombre_ubi de Ubi
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosNombre_ubi() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'nombre_ubi'));
		$oDatosCampo->setEtiqueta(_("nombre ubi"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut sdl de Ubi
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
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
	 * @return core\DatosCampo
	 */
	function getDatosPais() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'pais'));
		$oDatosCampo->setEtiqueta(_("país"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut sregion de Ubi
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosRegion() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'region'));
		$oDatosCampo->setEtiqueta(_("región"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut bstatus de Ubi
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
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
	 * @return core\DatosCampo
	 */
	function getDatosF_status() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'f_status'));
		$oDatosCampo->setEtiqueta(_("fecha status"));
        $oDatosCampo->setTipo('fecha');
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut bsv de Ubi
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
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
	 * @return core\DatosCampo
	 */
	function getDatosSf() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'sf'));
		$oDatosCampo->setEtiqueta(_("sf"));
		return $oDatosCampo;
	}
}