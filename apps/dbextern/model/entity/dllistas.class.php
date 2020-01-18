<?php
namespace dbextern\model\entity;
use core;
use web;

class DlListas Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/*
	Dl vachar(5)
	nombre_dl varchar(30)
	numero_dl tinyinteger
	abr_r varchar(10)
	numero_r tinyinteger
	 */
	
	/**
	 * aPrimary_key de Listas
	 *
	 * @var array
	 */
	 private $aPrimary_key;

	/**
	 * aDades de Listas
	 *
	 * @var array
	 */
	 private $aDades;

	/**
	 * Numero_dl de Listas
	 *
	 * @var integer
	 */
	 private $iNumero_dl;
	/**
	 * Numero_r de Listas
	 *
	 * @var integer
	 */
	 private $iNumero_r;
	/**
	 * Dl de Listas
	 *
	 * @var string
	 */
	 private $sDl;
	/**
	 * Nombre_dl de Listas
	 *
	 * @var string
	 */
	 private $sNombre_dl;
	/**
	 * Abr_r de Listas
	 *
	 * @var string
	 */
	 private $sAbr_r;
	 
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */


    /* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 * Si només necessita un valor, se li pot passar un integer.
	 * En general se li passa un array amb les claus primàries.
	 *
	 * @param integer|array sDl
	 * 						$a_id. Un array con los nombres=>valores de las claves primarias.
	 */
	function __construct($a_id='') {
		if (!empty($_SESSION['oDBListas']) && $_SESSION['oDBListas'] == 'error') {
			exit(_("no se puede conectar con la base de datos de Listas")); 
		}
		$oDbl = $GLOBALS['oDBListas'];
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				if (($nom_id == 'dl') && $val_id !== '') $this->sDl = (string)$val_id; // evitem SQL injection fent cast a integer
			}
		} else {
			if (isset($a_id) && $a_id !== '') {
				$this->sDl = $a_id;
				$this->aPrimary_key = array('dl' => $this->sDl);
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('dbo.q_Aux_Dl');
	}

	/* METODES PUBLICS ----------------------------------------------------------*/

	
	/**
	 * Desa els atributs de l'objecte a la base de dades.
	 * Si no hi ha el registre, fa el insert, si hi es fa el update.
	 *
	 */
	public function DBGuardar() {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		return false;
	}

	/**
	 * Carrega els camps de la base de dades com atributs de l'objecte.
	 *
	 */
	public function DBCarregar($que=null) {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		if (isset($this->iIdentif)) {
			if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE Identif='$this->iIdentif'")) === false) {
				$sClauError = 'Listas.carregar';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			}
			$aDades = $oDblSt->fetch(\PDO::FETCH_ASSOC);
			switch ($que) {
				case 'tot':
					$this->aDades=$aDades;
					break;
				case 'guardar':
					if (!$oDblSt->rowCount()) return false;
					break;
				default:
					// En el caso de no existir esta fila, $aDades = FALSE:
					if ($aDades === FALSE) {
						$this->setNullAllAtributes();
					} else {
						$this->setAllAtributes($aDades);
					}
			}
			return true;
		} else {
		   	return false;
		}
	}

	/**
	 * Elimina el registre de la base de dades corresponent a l'objecte.
	 *
	 */
	public function DBEliminar() {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		return FALSE;
	}
	
	/* METODES ALTRES  ----------------------------------------------------------*/
	
	/* METODES PRIVATS ----------------------------------------------------------*/

	/**
	 * Estableix el valor de tots els atributs
	 *
	 * @param array $aDades
	 */
	function setAllAtributes($aDades) {
		if (!is_array($aDades)) return;
		if (array_key_exists('dl',$aDades)) $this->setDl($aDades['dl']);
		if (array_key_exists('nombre_dl',$aDades)) $this->setNombre_dl($aDades['nombre_dl']);
		if (array_key_exists('numero_dl',$aDades)) $this->setNumero_dl($aDades['numero_dl']);
		if (array_key_exists('abr_r',$aDades)) $this->setAbr_r($aDades['abr_r']);
		if (array_key_exists('numero_r',$aDades)) $this->setNumero_r($aDades['numero_r']);
	}

	/**
	 * Estableix a empty el valor de tots els atributs
	 *
	 */
	function setNullAllAtributes() {
		$this->setDl('');
		$this->setNombre_dl('');
		$this->setNumero_dl('');
		$this->setAbr_r('');
		$this->setNumero_r('');
	}



	/* METODES GET i SET --------------------------------------------------------*/
	/**
	 * Recupera tots els atributs de Listas en un array
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
	 * Recupera las claus primàries de Listas en un array
	 *
	 * @return array aPrimary_key
	 */
	function getPrimary_key() {
		if (!isset($this->aPrimary_key )) {
			$this->aPrimary_key = array('iIdentif' => $this->iIdentif);
		}
		return $this->aPrimary_key;
	}

	/**
	 * Recupera l'atribut sDl de Listas
	 *
	 * @return string sDl
	 */
	function getDl() {
		if (!isset($this->sDl)) {
			$this->DBCarregar();
		}
		return $this->sDl;
	}
	/**
	 * estableix el valor de l'atribut sDl de Listas
	 *
	 * @param string sDl
	 */
	function setDl($sDl) {
		$this->sDl = $sDl;
	}
	/**
	 * Recupera l'atribut sNombre_dl de Listas
	 *
	 * @return string sNombre_dl
	 */
	function getNombre_dl() {
		if (!isset($this->sNombre_dl)) {
			$this->DBCarregar();
		}
		return $this->sNombre_dl;
	}
	/**
	 * estableix el valor de l'atribut sNombre_dl de Listas
	 *
	 * @param string sNombre_dl
	 */
	function setNombre_dl($sNombre_dl) {
		$this->sNombre_dl = $sNombre_dl;
	}
	/**
	 * Recupera l'atribut sAbr_r de Listas
	 *
	 * @return string sAbr_r
	 */
	function getAbr_r() {
		if (!isset($this->sAbr_r)) {
			$this->DBCarregar();
		}
		return $this->sAbr_r;
	}
	/**
	 * estableix el valor de l'atribut sAbr_r de Listas
	 *
	 * @param string sAbr_r
	 */
	function setAbr_r($sAbr_r) {
		$this->sAbr_r = $sAbr_r;
	}
	/**
	 * Recupera l'atribut iNumero_dl de Listas
	 *
	 * @return string iNumero_dl
	 */
	function getNumero_dl() {
		if (!isset($this->iNumero_dl)) {
			$this->DBCarregar();
		}
		return $this->iNumero_dl;
	}
	/**
	 * estableix el valor de l'atribut iNumero_dl de Listas
	 *
	 * @param string iNumero_dl
	 */
	function setNumero_dl($iNumero_dl) {
		$this->iNumero_dl = $iNumero_dl;
	}
	/**
	 * Recupera l'atribut iNumero_r de Listas
	 *
	 * @return string iNumero_r
	 */
	function getNumero_r() {
		if (!isset($this->iNumero_r)) {
			$this->DBCarregar();
		}
		return $this->iNumero_r;
	}
	/**
	 * estableix el valor de l'atribut iNumero_r de Listas
	 *
	 * @param string iNumero_r
	 */
	function setNumero_r($iNumero_r) {
		$this->iNumero_r = $iNumero_r;
	}
	
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$oListasSet = new core\Set();

		$oListasSet->add($this->getDatosDl());
		$oListasSet->add($this->getDatosNombre_dl());
		$oListasSet->add($this->getDatoiNumero_dl());
		$oListasSet->add($this->getDatosAbr_r());
		$oListasSet->add($this->getDatoiNumero_r());
		return $oListasSet->getTot();
	}

	/**
	 * Recupera les propietats de l'atribut sDl de Listas
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
	 * Recupera les propietats de l'atribut sNombre_dl de Listas
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosNombre_dl() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'nombre_dl'));
		$oDatosCampo->setEtiqueta(_("Nombre dl"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut iNumero_dl de Listas
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatoiNumero_dl() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'numero_dl'));
		$oDatosCampo->setEtiqueta(_("Número dl"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut sAbr_r de Listas
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosAbr_r() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'abr_r'));
		$oDatosCampo->setEtiqueta(_("Abreviatura región"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut iNumero_r de Listas
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatoiNumero_r() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'numero_r'));
		$oDatosCampo->setEtiqueta(_("Número región"));
		return $oDatosCampo;
	}
}

