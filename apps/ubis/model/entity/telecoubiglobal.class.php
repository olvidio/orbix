<?php
namespace ubis\model\entity;
use core;
/**
 * Classe que implementa l'entitat d_teleco_ubis
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 01/10/2010
 */

Abstract class TelecoUbiGlobal Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de TelecoUbi
	 *
	 * @var array
	 */
	 protected $aPrimary_key;

	/**
	 * aDades de TelecoUbi
	 *
	 * @var array
	 */
	 protected $aDades;

	/**
	 * Id_ubi de TelecoUbi
	 *
	 * @var integer
	 */
	 protected $iid_ubi;
	/**
	 * Id_item de TelecoUbi
	 *
	 * @var integer
	 */
	 protected $iid_item;
	/**
	 * Tipo_teleco de TelecoUbi
	 *
	 * @var string
	 */
	 protected $stipo_teleco;
	/**
	 * Desc_teleco de TelecoUbi
	 *
	 * @var string
	 */
	 protected $sdesc_teleco;
	/**
	 * Num_teleco de TelecoUbi
	 *
	 * @var string
	 */
	 protected $snum_teleco;
	/**
	 * Observ de TelecoUbi
	 *
	 * @var string
	 */
	 protected $sobserv;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe vuit.
	 *
	 */
	function __construct($a_id='') {
	}

	/* METODES PUBLICS ----------------------------------------------------------*/
	
	/* METODES ALTRES  ----------------------------------------------------------*/
	/* METODES PRIVATS ----------------------------------------------------------*/

	/* METODES GET i SET --------------------------------------------------------*/
	/**
	 * Recupera tots els atributs de TelecoUbi en un array
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
	 * Recupera las claus primàries de TelecoUbi en un array
	 *
	 * @return array aPrimary_key
	 */
	function getPrimary_key() {
		if (!isset($this->aPrimary_key )) {
			$this->aPrimary_key = array('iid_item' => $this->iid_item);
		}
		return $this->aPrimary_key;
	}

	/**
	 * Recupera l'atribut iid_ubi de TelecoUbi
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
	 * estableix el valor de l'atribut iid_ubi de TelecoUbi
	 *
	 * @param integer iid_ubi
	 */
	function setId_ubi($iid_ubi) {
		$this->iid_ubi = $iid_ubi;
	}
	/**
	 * Recupera l'atribut iid_item de TelecoUbi
	 *
	 * @return integer iid_item
	 */
	function getId_item() {
		if (!isset($this->iid_item)) {
			$this->DBCarregar();
		}
		return $this->iid_item;
	}
	/**
	 * estableix el valor de l'atribut iid_item de TelecoUbi
	 *
	 * @param integer iid_item
	 */
	function setId_item($iid_item) {
		$this->iid_item = $iid_item;
	}
	/**
	 * Recupera l'atribut stipo_teleco de TelecoUbi
	 *
	 * @return string stipo_teleco
	 */
	function getTipo_teleco() {
		if (!isset($this->stipo_teleco)) {
			$this->DBCarregar();
		}
		return $this->stipo_teleco;
	}
	/**
	 * estableix el valor de l'atribut stipo_teleco de TelecoUbi
	 *
	 * @param string stipo_teleco='' optional
	 */
	function setTipo_teleco($stipo_teleco='') {
		$this->stipo_teleco = $stipo_teleco;
	}
	/**
	 * Recupera l'atribut sdesc_teleco de TelecoUbi
	 *
	 * @return string sdesc_teleco
	 */
	function getDesc_teleco() {
		if (!isset($this->sdesc_teleco)) {
			$this->DBCarregar();
		}
		return $this->sdesc_teleco;
	}
	/**
	 * estableix el valor de l'atribut sdesc_teleco de TelecoUbi
	 *
	 * @param string sdesc_teleco='' optional
	 */
	function setDesc_teleco($sdesc_teleco='') {
		$this->sdesc_teleco = $sdesc_teleco;
	}
	/**
	 * Recupera l'atribut snum_teleco de TelecoUbi
	 *
	 * @return string snum_teleco
	 */
	function getNum_teleco() {
		if (!isset($this->snum_teleco)) {
			$this->DBCarregar();
		}
		return $this->snum_teleco;
	}
	/**
	 * estableix el valor de l'atribut snum_teleco de TelecoUbi
	 *
	 * @param string snum_teleco='' optional
	 */
	function setNum_teleco($snum_teleco='') {
		$this->snum_teleco = $snum_teleco;
	}
	/**
	 * Recupera l'atribut sobserv de TelecoUbi
	 *
	 * @return string sobserv
	 */
	function getObserv() {
		if (!isset($this->sobserv)) {
			$this->DBCarregar();
		}
		return $this->sobserv;
	}
	/**
	 * estableix el valor de l'atribut sobserv de TelecoUbi
	 *
	 * @param string sobserv='' optional
	 */
	function setObserv($sobserv='') {
		$this->sobserv = $sobserv;
	}
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$oTelecoUbiSet = new core\Set();

		$oTelecoUbiSet->add($this->getDatosTipo_teleco());
		$oTelecoUbiSet->add($this->getDatosDesc_teleco());
		$oTelecoUbiSet->add($this->getDatosNum_teleco());
		$oTelecoUbiSet->add($this->getDatosObserv());
		return $oTelecoUbiSet->getTot();
	}


	function getDatosTipo_teleco() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'tipo_teleco'));
		$oDatosCampo->setEtiqueta(_("nombre teleco"));
		$oDatosCampo->setTipo('opciones');
		$oDatosCampo->setArgument('ubis\model\entity\TipoTeleco'); // nombre del objeto relacionado
		$oDatosCampo->setArgument2('nombre_teleco'); // clave con la que crear el objeto relacionado
		$oDatosCampo->setArgument3('getListaTiposTelecoUbi'); // método con que crear la lista de opciones del Gestor objeto relacionado.
		$oDatosCampo->setAccion('desc_teleco'); // campo que hay que actualizar al cambiar este.
		return $oDatosCampo;
	}
	function getDatosDesc_teleco() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'desc_teleco'));
		$oDatosCampo->setEtiqueta(_("descripción"));
		$oDatosCampo->setTipo('depende');
		$oDatosCampo->setArgument('ubis\model\entity\DescTeleco');
		$oDatosCampo->setArgument2('desc_teleco');
		$oDatosCampo->setArgument3('getListaDescTelecoUbis');
		$oDatosCampo->setDepende('tipo_teleco');
		return $oDatosCampo;
	}
	function getDatosNum_teleco() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'num_teleco'));
		$oDatosCampo->setEtiqueta(_("número o siglas"));
		$oDatosCampo->setTipo('texto');
		$oDatosCampo->setArgument('50');
		return $oDatosCampo;
	}
	function getDatosObserv() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'observ'));
		$oDatosCampo->setEtiqueta(_("observaciones"));
		$oDatosCampo->setTipo('texto');
		$oDatosCampo->setArgument('50');
		return $oDatosCampo;
	}

}
?>
