<?php
namespace encargossacd\model\entity;
use core;
/**
 * Fitxer amb la Classe que accedeix a la taula encargo_textos
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 05/03/2019
 */
/**
 * Classe que implementa l'entitat encargo_textos
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 05/03/2019
 */
class EncargoTexto Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de EncargoTexto
	 *
	 * @var array
	 */
	 private $aPrimary_key;

	/**
	 * aDades de EncargoTexto
	 *
	 * @var array
	 */
	 private $aDades;

	/**
	 * bLoaded
	 *
	 * @var boolean
	 */
	 private $bLoaded = FALSE;

	/**
	 * Id_item de EncargoTexto
	 *
	 * @var integer
	 */
	 private $iid_item;
	/**
	 * Idioma de EncargoTexto
	 *
	 * @var string
	 */
	 private $sidioma;
	/**
	 * Clave de EncargoTexto
	 *
	 * @var string
	 */
	 private $sclave;
	/**
	 * Texto de EncargoTexto
	 *
	 * @var string
	 */
	 private $stexto;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/**
	 * oDbl de EncargoTexto
	 *
	 * @var object
	 */
	 protected $oDbl;
	/**
	 * NomTabla de EncargoTexto
	 *
	 * @var string
	 */
	 protected $sNomTabla;
	/* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 * Si només necessita un valor, se li pot passar un integer.
	 * En general se li passa un array amb les claus primàries.
	 *
	 * @param integer|array iid_item
	 * 						$a_id. Un array con los nombres=>valores de las claves primarias.
	 */
	function __construct($a_id='') {
		$oDbl = $GLOBALS['oDBE'];
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				if (($nom_id == 'id_item') && $val_id !== '') $this->iid_item = (int)$val_id; // evitem SQL injection fent cast a integer
			}
		} else {
			if (isset($a_id) && $a_id !== '') {
				$this->iid_item = intval($a_id); // evitem SQL injection fent cast a integer
				$this->aPrimary_key = array('id_item' => $this->iid_item);
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('encargo_textos');
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
		if ($this->DBCarregar('guardar') === FALSE) { $bInsert=TRUE; } else { $bInsert=FALSE; }
		$aDades=array();
		$aDades['idioma'] = $this->sidioma;
		$aDades['clave'] = $this->sclave;
		$aDades['texto'] = $this->stexto;
		array_walk($aDades, 'core\poner_null');

		if ($bInsert === FALSE) {
			//UPDATE
			$update="
					idioma                   = :idioma,
					clave                    = :clave,
					texto                    = :texto";
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_item='$this->iid_item'")) === FALSE) {
				$sClauError = 'EncargoTexto.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return FALSE;
			} else {
				try {
					$oDblSt->execute($aDades);
				}
				catch ( \PDOException $e) {
					$err_txt=$e->errorInfo[2];
					$this->setErrorTxt($err_txt);
					$sClauError = 'EncargoTexto.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return FALSE;
				}
			}
		} else {
			// INSERT
			$campos="(idioma,clave,texto)";
			$valores="(:idioma,:clave,:texto)";		
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === FALSE) {
				$sClauError = 'EncargoTexto.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return FALSE;
			} else {
				try {
					$oDblSt->execute($aDades);
				}
				catch ( \PDOException $e) {
					$err_txt=$e->errorInfo[2];
					$this->setErrorTxt($err_txt);
					$sClauError = 'EncargoTexto.insertar.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return FALSE;
				}
			}
			$this->id_item = $oDbl->lastInsertId('encargo_textos_id_item_seq');
		}
		$this->setAllAtributes($aDades);
		return TRUE;
	}

	/**
	 * Carrega els camps de la base de dades com atributs de l'objecte.
	 *
	 */
	public function DBCarregar($que=null) {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		if (isset($this->iid_item)) {
			if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_item='$this->iid_item'")) === FALSE) {
				$sClauError = 'EncargoTexto.carregar';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return FALSE;
			}
			$aDades = $oDblSt->fetch(\PDO::FETCH_ASSOC);
			// Para evitar posteriores cargas
			$this->bLoaded = TRUE;
			switch ($que) {
				case 'tot':
					$this->aDades=$aDades;
					break;
				case 'guardar':
					if (!$oDblSt->rowCount()) return FALSE;
					break;
				default:
					// En el caso de no existir esta fila, $aDades = FALSE:
					if ($aDades === FALSE) {
						$this->setNullAllAtributes();
					} else {
						$this->setAllAtributes($aDades);
					}
			}
			return TRUE;
		} else {
		   	return FALSE;
		}
	}

	/**
	 * Elimina el registre de la base de dades corresponent a l'objecte.
	 *
	 */
	public function DBEliminar() {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		if (($oDbl->exec("DELETE FROM $nom_tabla WHERE id_item='$this->iid_item'")) === FALSE) {
			$sClauError = 'EncargoTexto.eliminar';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return FALSE;
		}
		return TRUE;
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
		if (array_key_exists('id_item',$aDades)) $this->setId_item($aDades['id_item']);
		if (array_key_exists('idioma',$aDades)) $this->setIdioma($aDades['idioma']);
		if (array_key_exists('clave',$aDades)) $this->setClave($aDades['clave']);
		if (array_key_exists('texto',$aDades)) $this->setTexto($aDades['texto']);
	}

	/**
	 * Estableix a empty el valor de tots els atributs
	 *
	 */
	function setNullAllAtributes() {
		$aPK = $this->getPrimary_key();
		$this->setId_item('');
		$this->setIdioma('');
		$this->setClave('');
		$this->setTexto('');
		$this->setPrimary_key($aPK);
	}


	/* METODES GET i SET --------------------------------------------------------*/

	/**
	 * Recupera tots els atributs de EncargoTexto en un array
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
	 * Recupera las claus primàries de EncargoTexto en un array
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
	 * Estableix las claus primàries de EncargoTexto en un array
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
	 * Recupera l'atribut iid_item de EncargoTexto
	 *
	 * @return integer iid_item
	 */
	function getId_item() {
		if (!isset($this->iid_item) && !$this->bLoaded) {
			$this->DBCarregar();
		}
		return $this->iid_item;
	}
	/**
	 * estableix el valor de l'atribut iid_item de EncargoTexto
	 *
	 * @param integer iid_item
	 */
	function setId_item($iid_item) {
		$this->iid_item = $iid_item;
	}
	/**
	 * Recupera l'atribut sidioma de EncargoTexto
	 *
	 * @return string sidioma
	 */
	function getIdioma() {
		if (!isset($this->sidioma) && !$this->bLoaded) {
			$this->DBCarregar();
		}
		return $this->sidioma;
	}
	/**
	 * estableix el valor de l'atribut sidioma de EncargoTexto
	 *
	 * @param string sidioma='' optional
	 */
	function setIdioma($sidioma='') {
		$this->sidioma = $sidioma;
	}
	/**
	 * Recupera l'atribut sclave de EncargoTexto
	 *
	 * @return string sclave
	 */
	function getClave() {
		if (!isset($this->sclave) && !$this->bLoaded) {
			$this->DBCarregar();
		}
		return $this->sclave;
	}
	/**
	 * estableix el valor de l'atribut sclave de EncargoTexto
	 *
	 * @param string sclave='' optional
	 */
	function setClave($sclave='') {
		$this->sclave = $sclave;
	}
	/**
	 * Recupera l'atribut stexto de EncargoTexto
	 *
	 * @return string stexto
	 */
	function getTexto() {
		if (!isset($this->stexto) && !$this->bLoaded) {
			$this->DBCarregar();
		}
		return $this->stexto;
	}
	/**
	 * estableix el valor de l'atribut stexto de EncargoTexto
	 *
	 * @param string stexto='' optional
	 */
	function setTexto($stexto='') {
		$this->stexto = $stexto;
	}
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$oEncargoTextoSet = new core\Set();

		$oEncargoTextoSet->add($this->getDatosIdioma());
		$oEncargoTextoSet->add($this->getDatosClave());
		$oEncargoTextoSet->add($this->getDatosTexto());
		return $oEncargoTextoSet->getTot();
	}



	/**
	 * Recupera les propietats de l'atribut sidioma de EncargoTexto
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosIdioma() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'idioma'));
		$oDatosCampo->setEtiqueta(_("idioma"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut sclave de EncargoTexto
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosClave() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'clave'));
		$oDatosCampo->setEtiqueta(_("clave"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut stexto de EncargoTexto
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosTexto() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'texto'));
		$oDatosCampo->setEtiqueta(_("texto"));
		return $oDatosCampo;
	}
}
