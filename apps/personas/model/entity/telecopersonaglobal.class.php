<?php
namespace personas\model\entity;
use core;
/**
 * Classe que implementa l'entitat d_teleco_persona
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 01/10/2010
 */

Abstract class TelecoPersonaGlobal Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de TelecoPersona
	 *
	 * @var array
	 */
	 protected $aPrimary_key;

	/**
	 * aDades de TelecoPersona
	 *
	 * @var array
	 */
	 protected $aDades;

	/**
	 * Id_nom de TelecoPersona
	 *
	 * @var integer
	 */
	 protected $iid_nom;
	/**
	 * Id_item de TelecoPersona
	 *
	 * @var integer
	 */
	 protected $iid_item;
	/**
	 * Tipo_teleco de TelecoPersona
	 *
	 * @var string
	 */
	 protected $stipo_teleco;
	/**
	 * Desc_teleco de TelecoPersona
	 *
	 * @var string
	 */
	 protected $sdesc_teleco;
	/**
	 * Num_teleco de TelecoPersona
	 *
	 * @var string
	 */
	 protected $snum_teleco;
	/**
	 * Observ de TelecoPersona
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
	 * Recupera tots els atributs de TelecoPersona en un array
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
	 * Recupera las claus primàries de TelecoPersona en un array
	 *
	 * @return array aPrimary_key
	 */
	function getPrimary_key() {
		if (!isset($this->aPrimary_key )) {
			$this->aPrimary_key = array('id_item' => $this->iid_item);
		}
		return $this->aPrimary_key;
	}
	
	/**
	 * Estableix las claus primàries de TelecoPersona en un array
	 *
	 * @return array aPrimary_key
	 */
	public function setPrimary_key($a_id='') {
	    if (is_array($a_id)) {
	        $this->aPrimary_key = $a_id;
	        foreach($a_id as $nom_id=>$val_id) {
	            if (($nom_id == 'id_item') && $val_id !== '') $this->iid_item = (int)$val_id; // evitem SQL injection fent cast a integer
	        }
	    }
	}
	
	/**
	 * Recupera l'atribut iid_nom de TelecoPersona
	 *
	 * @return integer iid_nom
	 */
	function getId_nom() {
		if (!isset($this->iid_nom)) {
			$this->DBCarregar();
		}
		return $this->iid_nom;
	}
	/**
	 * estableix el valor de l'atribut iid_nom de TelecoPersona
	 *
	 * @param integer iid_nom
	 */
	function setId_nom($iid_nom) {
		$this->iid_nom = $iid_nom;
	}
	/**
	 * Recupera l'atribut iid_item de TelecoPersona
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
	 * estableix el valor de l'atribut iid_item de TelecoPersona
	 *
	 * @param integer iid_item
	 */
	function setId_item($iid_item) {
		$this->iid_item = $iid_item;
	}
	/**
	 * Recupera l'atribut stipo_teleco de TelecoPersona
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
	 * estableix el valor de l'atribut stipo_teleco de TelecoPersona
	 *
	 * @param string stipo_teleco='' optional
	 */
	function setTipo_teleco($stipo_teleco='') {
		$this->stipo_teleco = $stipo_teleco;
	}
	/**
	 * Recupera l'atribut sdesc_teleco de TelecoPersona
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
	 * estableix el valor de l'atribut sdesc_teleco de TelecoPersona
	 *
	 * @param string sdesc_teleco='' optional
	 */
	function setDesc_teleco($sdesc_teleco='') {
		$this->sdesc_teleco = $sdesc_teleco;
	}
	/**
	 * Recupera l'atribut snum_teleco de TelecoPersona
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
	 * estableix el valor de l'atribut snum_teleco de TelecoPersona
	 *
	 * @param string snum_teleco='' optional
	 */
	function setNum_teleco($snum_teleco='') {
		$this->snum_teleco = $snum_teleco;
	}
	/**
	 * Recupera l'atribut sobserv de TelecoPersona
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
	 * estableix el valor de l'atribut sobserv de TelecoPersona
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
		$oTelecoPersonaSet = new core\Set();

		$oTelecoPersonaSet->add($this->getDatosTipo_teleco());
		$oTelecoPersonaSet->add($this->getDatosDesc_teleco());
		$oTelecoPersonaSet->add($this->getDatosNum_teleco());
		$oTelecoPersonaSet->add($this->getDatosObserv());
		return $oTelecoPersonaSet->getTot();
	}


	function getDatosTipo_teleco() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'tipo_teleco'));
		$oDatosCampo->setEtiqueta(_("nombre teleco"));
		$oDatosCampo->setTipo('opciones');
		$oDatosCampo->setArgument('ubis\model\entity\TipoTeleco'); // nombre del objeto relacionado
		$oDatosCampo->setArgument2('getNombre_teleco'); // método para obtener el valor a mostrar del objeto relacionado.
		$oDatosCampo->setArgument3('getListaTiposTelecoPersona'); // método con que crear la lista de opciones del Gestor objeto relacionado.
		$oDatosCampo->setAccion('desc_teleco'); // campo que hay que actualizar al cambiar este.
		return $oDatosCampo;
	}
	function getDatosDesc_teleco() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'desc_teleco'));
		$oDatosCampo->setEtiqueta(_("descripción"));
		$oDatosCampo->setTipo('depende');
		$oDatosCampo->setArgument('ubis\model\entity\DescTeleco');
		$oDatosCampo->setArgument2('getDesc_teleco'); // método para obtener el valor a mostrar del objeto relacionado.
		$oDatosCampo->setArgument3('getListaDescTelecoPersonas');
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
