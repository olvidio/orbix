<?php
namespace profesores\model\entity;
use core;
/**
 * Fitxer amb la Classe que accedeix a la taula xe_tipo_profesor_stgr
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 07/04/2014
 */
/**
 * Classe que implementa l'entitat xe_tipo_profesor_stgr
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 07/04/2014
 */
class ProfesorTipo Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de ProfesorTipo
	 *
	 * @var array
	 */
	 private $aPrimary_key;

	/**
	 * aDades de ProfesorTipo
	 *
	 * @var array
	 */
	 private $aDades;

	/**
	 * Id_tipo_profesor de ProfesorTipo
	 *
	 * @var integer
	 */
	 private $iid_tipo_profesor;
	/**
	 * Tipo_profesor de ProfesorTipo
	 *
	 * @var string
	 */
	 private $stipo_profesor;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/**
	 * oDbl de ProfesorTipo
	 *
	 * @var object
	 */
	 protected $oDbl;
	/**
	 * NomTabla de ProfesorTipo
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
	 * @param integer|array iid_tipo_profesor
	 * 						$a_id. Un array con los nombres=>valores de las claves primarias.
	 */
	function __construct($a_id='') {
		$oDbl = $GLOBALS['oDBPC'];
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				if (($nom_id == 'id_tipo_profesor') && $val_id !== '') $this->iid_tipo_profesor = (int)$val_id; // evitem SQL injection fent cast a integer
			}	} else {
			if (isset($a_id) && $a_id !== '') {
				$this->iid_tipo_profesor = intval($a_id); // evitem SQL injection fent cast a integer
				$this->aPrimary_key = array('iid_tipo_profesor' => $this->iid_tipo_profesor);
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('xe_tipo_profesor_stgr');
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
		if ($this->DBCarregar('guardar') === false) { $bInsert=true; } else { $bInsert=false; }
		$aDades=array();
		$aDades['tipo_profesor'] = $this->stipo_profesor;
		array_walk($aDades, 'core\poner_null');

		if ($bInsert === false) {
			//UPDATE
			$update="
					tipo_profesor            = :tipo_profesor";
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_tipo_profesor='$this->iid_tipo_profesor'")) === false) {
				$sClauError = 'ProfesorTipo.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($oDblSt->execute($aDades) === false) {
					$sClauError = 'ProfesorTipo.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
		} else {
			// INSERT
			$campos="(tipo_profesor)";
			$valores="(:tipo_profesor)";		
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
				$sClauError = 'ProfesorTipo.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($oDblSt->execute($aDades) === false) {
					$sClauError = 'ProfesorTipo.insertar.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
			$this->id_tipo_profesor = $oDbl->lastInsertId('xe_tipo_profe_id_tipo_profe_seq');
		}
		$this->setAllAtributes($aDades);
		return true;
	}

	/**
	 * Carrega els camps de la base de dades com atributs de l'objecte.
	 *
	 */
	public function DBCarregar($que=null) {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		if (isset($this->iid_tipo_profesor)) {
			if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_tipo_profesor='$this->iid_tipo_profesor'")) === false) {
				$sClauError = 'ProfesorTipo.carregar';
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
					$this->setAllAtributes($aDades);
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
		if (($oDblSt = $oDbl->exec("DELETE FROM $nom_tabla WHERE id_tipo_profesor='$this->iid_tipo_profesor'")) === false) {
			$sClauError = 'ProfesorTipo.eliminar';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		return true;
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
		if (array_key_exists('id_tipo_profesor',$aDades)) $this->setId_tipo_profesor($aDades['id_tipo_profesor']);
		if (array_key_exists('tipo_profesor',$aDades)) $this->setTipo_profesor($aDades['tipo_profesor']);
	}

	/* METODES GET i SET --------------------------------------------------------*/

	/**
	 * Recupera tots els atributs de ProfesorTipo en un array
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
	 * Recupera las claus primàries de ProfesorTipo en un array
	 *
	 * @return array aPrimary_key
	 */
	function getPrimary_key() {
		if (!isset($this->aPrimary_key )) {
			$this->aPrimary_key = array('iid_tipo_profesor' => $this->iid_tipo_profesor);
		}
		return $this->aPrimary_key;
	}

	/**
	 * Recupera l'atribut iid_tipo_profesor de ProfesorTipo
	 *
	 * @return integer iid_tipo_profesor
	 */
	function getId_tipo_profesor() {
		if (!isset($this->iid_tipo_profesor)) {
			$this->DBCarregar();
		}
		return $this->iid_tipo_profesor;
	}
	/**
	 * estableix el valor de l'atribut iid_tipo_profesor de ProfesorTipo
	 *
	 * @param integer iid_tipo_profesor
	 */
	function setId_tipo_profesor($iid_tipo_profesor) {
		$this->iid_tipo_profesor = $iid_tipo_profesor;
	}
	/**
	 * Recupera l'atribut stipo_profesor de ProfesorTipo
	 *
	 * @return string stipo_profesor
	 */
	function getTipo_profesor() {
		if (!isset($this->stipo_profesor)) {
			$this->DBCarregar();
		}
		return $this->stipo_profesor;
	}
	/**
	 * estableix el valor de l'atribut stipo_profesor de ProfesorTipo
	 *
	 * @param string stipo_profesor='' optional
	 */
	function setTipo_profesor($stipo_profesor='') {
		$this->stipo_profesor = $stipo_profesor;
	}
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$oProfesorTipoSet = new core\Set();

		$oProfesorTipoSet->add($this->getDatosTipo_profesor());
		return $oProfesorTipoSet->getTot();
	}



	/**
	 * Recupera les propietats de l'atribut stipo_profesor de ProfesorTipo
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosTipo_profesor() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'tipo_profesor'));
		$oDatosCampo->setEtiqueta(_("tipo de profesor"));
		return $oDatosCampo;
	}
}
?>
