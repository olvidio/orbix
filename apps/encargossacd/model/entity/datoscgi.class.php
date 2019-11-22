<?php
namespace encargossacd\model\entity;
use core;
/**
 * Fitxer amb la Classe que accedeix a la taula encargo_datos_cgi
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 11/01/2019
 */
/**
 * Classe que implementa l'entitat encargo_datos_cgi
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 11/01/2019
 */
class DatosCgi Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de DatosCgi
	 *
	 * @var array
	 */
	 private $aPrimary_key;

	/**
	 * aDades de DatosCgi
	 *
	 * @var array
	 */
	 private $aDades;

	/**
	 * Id_item de DatosCgi
	 *
	 * @var integer
	 */
	 private $iid_item;
	/**
	 * Id_ubi de DatosCgi
	 *
	 * @var integer
	 */
	 private $iid_ubi;
	/**
	 * Curso_ini_any de DatosCgi
	 *
	 * @var integer
	 */
	 private $icurso_ini_any;
	/**
	 * Curso_fin_any de DatosCgi
	 *
	 * @var integer
	 */
	 private $icurso_fin_any;
	/**
	 * Num_alum de DatosCgi
	 *
	 * @var integer
	 */
	 private $inum_alum;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/**
	 * oDbl de DatosCgi
	 *
	 * @var object
	 */
	 protected $oDbl;
	/**
	 * NomTabla de DatosCgi
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
		$oDbl = $GLOBALS['oDBC'];
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				if (($nom_id == 'id_item') && $val_id !== '') $this->iid_item = (int)$val_id; // evitem SQL injection fent cast a integer
			}
		} else {
			if (isset($a_id) && $a_id !== '') {
				$this->iid_item = intval($a_id); // evitem SQL injection fent cast a integer
				$this->aPrimary_key = array('iid_item' => $this->iid_item);
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('encargo_datos_cgi');
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
		$aDades['id_ubi'] = $this->iid_ubi;
		$aDades['curso_ini_any'] = $this->icurso_ini_any;
		$aDades['curso_fin_any'] = $this->icurso_fin_any;
		$aDades['num_alum'] = $this->inum_alum;
		array_walk($aDades, 'core\poner_null');

		if ($bInsert === FALSE) {
			//UPDATE
			$update="
					id_ubi                   = :id_ubi,
					curso_ini_any            = :curso_ini_any,
					curso_fin_any            = :curso_fin_any,
					num_alum                 = :num_alum";
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_item='$this->iid_item'")) === FALSE) {
				$sClauError = 'DatosCgi.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return FALSE;
			} else {
				if ($oDblSt->execute($aDades) === FALSE) {
					$sClauError = 'DatosCgi.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return FALSE;
				}
			}
		} else {
			// INSERT
			$campos="(id_ubi,curso_ini_any,curso_fin_any,num_alum)";
			$valores="(:id_ubi,:curso_ini_any,:curso_fin_any,:num_alum)";		
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === FALSE) {
				$sClauError = 'DatosCgi.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return FALSE;
			} else {
				if ($oDblSt->execute($aDades) === FALSE) {
					$sClauError = 'DatosCgi.insertar.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return FALSE;
				}
			}
			$this->id_item = $oDbl->lastInsertId('encargo_datos_cgi_id_item_seq');
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
				$sClauError = 'DatosCgi.carregar';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return FALSE;
			}
			$aDades = $oDblSt->fetch(\PDO::FETCH_ASSOC);
			switch ($que) {
				case 'tot':
					$this->aDades=$aDades;
					break;
				case 'guardar':
					if (!$oDblSt->rowCount()) return FALSE;
					break;
				default:
					$this->setAllAtributes($aDades);
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
			$sClauError = 'DatosCgi.eliminar';
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
		if (array_key_exists('id_ubi',$aDades)) $this->setId_ubi($aDades['id_ubi']);
		if (array_key_exists('curso_ini_any',$aDades)) $this->setCurso_ini_any($aDades['curso_ini_any']);
		if (array_key_exists('curso_fin_any',$aDades)) $this->setCurso_fin_any($aDades['curso_fin_any']);
		if (array_key_exists('num_alum',$aDades)) $this->setNum_alum($aDades['num_alum']);
	}

	/* METODES GET i SET --------------------------------------------------------*/

	/**
	 * Recupera tots els atributs de DatosCgi en un array
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
	 * Recupera las claus primàries de DatosCgi en un array
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
	 * Recupera l'atribut iid_item de DatosCgi
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
	 * estableix el valor de l'atribut iid_item de DatosCgi
	 *
	 * @param integer iid_item
	 */
	function setId_item($iid_item) {
		$this->iid_item = $iid_item;
	}
	/**
	 * Recupera l'atribut iid_ubi de DatosCgi
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
	 * estableix el valor de l'atribut iid_ubi de DatosCgi
	 *
	 * @param integer iid_ubi='' optional
	 */
	function setId_ubi($iid_ubi='') {
		$this->iid_ubi = $iid_ubi;
	}
	/**
	 * Recupera l'atribut icurso_ini_any de DatosCgi
	 *
	 * @return integer icurso_ini_any
	 */
	function getCurso_ini_any() {
		if (!isset($this->icurso_ini_any)) {
			$this->DBCarregar();
		}
		return $this->icurso_ini_any;
	}
	/**
	 * estableix el valor de l'atribut icurso_ini_any de DatosCgi
	 *
	 * @param integer icurso_ini_any='' optional
	 */
	function setCurso_ini_any($icurso_ini_any='') {
		$this->icurso_ini_any = $icurso_ini_any;
	}
	/**
	 * Recupera l'atribut icurso_fin_any de DatosCgi
	 *
	 * @return integer icurso_fin_any
	 */
	function getCurso_fin_any() {
		if (!isset($this->icurso_fin_any)) {
			$this->DBCarregar();
		}
		return $this->icurso_fin_any;
	}
	/**
	 * estableix el valor de l'atribut icurso_fin_any de DatosCgi
	 *
	 * @param integer icurso_fin_any='' optional
	 */
	function setCurso_fin_any($icurso_fin_any='') {
		$this->icurso_fin_any = $icurso_fin_any;
	}
	/**
	 * Recupera l'atribut inum_alum de DatosCgi
	 *
	 * @return integer inum_alum
	 */
	function getNum_alum() {
		if (!isset($this->inum_alum)) {
			$this->DBCarregar();
		}
		return $this->inum_alum;
	}
	/**
	 * estableix el valor de l'atribut inum_alum de DatosCgi
	 *
	 * @param integer inum_alum='' optional
	 */
	function setNum_alum($inum_alum='') {
		$this->inum_alum = $inum_alum;
	}
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$oDatosCgiSet = new core\Set();

		$oDatosCgiSet->add($this->getDatosId_ubi());
		$oDatosCgiSet->add($this->getDatosCurso_ini_any());
		$oDatosCgiSet->add($this->getDatosCurso_fin_any());
		$oDatosCgiSet->add($this->getDatosNum_alum());
		return $oDatosCgiSet->getTot();
	}



	/**
	 * Recupera les propietats de l'atribut iid_ubi de DatosCgi
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosId_ubi() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_ubi'));
		$oDatosCampo->setEtiqueta(_("id_ubi"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut icurso_ini_any de DatosCgi
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosCurso_ini_any() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'curso_ini_any'));
		$oDatosCampo->setEtiqueta(_("año inicio curso"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut icurso_fin_any de DatosCgi
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosCurso_fin_any() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'curso_fin_any'));
		$oDatosCampo->setEtiqueta(_("año fin curso"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut inum_alum de DatosCgi
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosNum_alum() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'num_alum'));
		$oDatosCampo->setEtiqueta(_("número de alumnos"));
		return $oDatosCampo;
	}
}
