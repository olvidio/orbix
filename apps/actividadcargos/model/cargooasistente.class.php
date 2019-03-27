<?php
namespace actividadcargos\model;

/**
 * Classe que implementa l'entitat d_asistentes_activ
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 01/10/2010
 */
class CargoOAsistente {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de ActividadAsistente
	 *
	 * @var array
	 */
	 private $aPrimary_key;

	/**
	 * aDades de ActividadAsistente
	 *
	 * @var array
	 */
	 private $aDades;

	/**
	 * Id_activ de ActividadAsistente
	 *
	 * @var integer
	 */
	 private $iid_activ;
	/**
	 * Id_nom de ActividadAsistente
	 *
	 * @var integer
	 */
	 private $iid_nom;
	/**
	 * Propio de ActividadAsistente
	 *
	 * @var boolean
	 */
	 private $bpropio;
	/**
	 * Id_cargo de ActividadAsistente
	 *
	 * @var integer
	 */
	 private $iid_cargo;
	/* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 * Si només necessita un valor, se li pot passar un integer.
	 * En general se li passa un array amb les claus primàries.
	 *
	 * @param integer|array iid_activ,iid_nom
	 * 						$a_id. Un array con los nombres=>valores de las claves primarias.
	 */
	function __construct($a_id='') {
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				$nom_id='i'.$nom_id; //imagino que es un integer
				if ($val_id !== '') $this->$nom_id = intval($val_id); // evitem SQL injection fent cast a integer
			}
		} else {
			if ($a_id !== '') $this->iid_activ = intval($a_id); // evitem SQL injection fent cast a integer
		}
	}

	/* METODES PUBLICS ----------------------------------------------------------*/

	/**
	 * Recupera l'atribut iid_activ de ActividadAsistente
	 *
	 * @return integer iid_activ
	 */
	function getId_activ() {
		return $this->iid_activ;
	}
	/**
	 * estableix el valor de l'atribut iid_activ de ActividadAsistente
	 *
	 * @param integer iid_activ
	 */
	function setId_activ($iid_activ) {
		$this->iid_activ = $iid_activ;
	}
	/**
	 * Recupera l'atribut iid_nom de ActividadAsistente
	 *
	 * @return integer iid_nom
	 */
	function getId_nom() {
		return $this->iid_nom;
	}
	/**
	 * estableix el valor de l'atribut iid_nom de ActividadAsistente
	 *
	 * @param integer iid_nom
	 */
	function setId_nom($iid_nom) {
		$this->iid_nom = $iid_nom;
	}
	/**
	 * Recupera l'atribut bpropio de ActividadAsistente
	 *
	 * @return boolean bpropio
	 */
	function getPropio() {
		return $this->bpropio;
	}
	/**
	 * estableix el valor de l'atribut bpropio de ActividadAsistente
	 *
	 * @param boolean bpropio='f' optional
	 */
	function setPropio($bpropio='f') {
		$this->bpropio = $bpropio;
	}
	/**
	 * Recupera l'atribut iid_cargo de ActividadAsistente
	 *
	 * @return integer iid_cargo
	 */
	function getId_cargo() {
		return $this->iid_cargo;
	}
	/**
	 * estableix el valor de l'atribut iid_cargo de ActividadAsistente
	 *
	 * @param integer iid_nom
	 */
	function setId_cargo($iid_cargo) {
		$this->iid_cargo = $iid_cargo;
	}

}