<?php
namespace actividadestudios\model;
use core;
/**
 * Fitxer amb la Classe que accedeix a la taula d_matriculas_activ
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 18/11/2014
 */
/**
 * Classe que implementa l'entitat d_matriculas_activ
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 18/11/2014
 */
class Matricula Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de Matricula
	 *
	 * @var array
	 */
	 protected $aPrimary_key;

	/**
	 * aDades de Matricula
	 *
	 * @var array
	 */
	 protected $aDades;

	/**
	 * Id_schema de Matricula
	 *
	 * @var integer
	 */
	 protected $iid_schema;
	/**
	 * Id_activ de Matricula
	 *
	 * @var integer
	 */
	 protected $iid_activ;
	/**
	 * Id_asignatura de Matricula
	 *
	 * @var integer
	 */
	 protected $iid_asignatura;
	/**
	 * Id_nivel de Matricula
	 *
	 * @var integer
	 */
	 protected $iid_nivel;
	/**
	 * Id_nom de Matricula
	 *
	 * @var integer
	 */
	 protected $iid_nom;
	/**
	 * Id_situacion de Matricula
	 *
	 * @var integer
	 */
	 protected $iid_situacion;
	/**
	 * Preceptor de Matricula
	 *
	 * @var boolean
	 */
	 protected $bpreceptor;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/**
	 * oDbl de Matricula
	 *
	 * @var object
	 */
	 protected $oDbl;
	/**
	 * NomTabla de Matricula
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
	 * @param integer|array iid_activ,iid_asignatura,iid_nom
	 * 						$a_id. Un array con los nombres=>valores de las claves primarias.
	 */
	function __construct($a_id='') {
		$oDbl = $GLOBALS['oDBP'];
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				if (($nom_id == 'id_activ') && $val_id !== '') $this->iid_activ = (int)$val_id; // evitem SQL injection fent cast a integer
				if (($nom_id == 'id_asignatura') && $val_id !== '') $this->iid_asignatura = (int)$val_id; // evitem SQL injection fent cast a integer
				if (($nom_id == 'id_nom') && $val_id !== '') $this->iid_nom = (int)$val_id; // evitem SQL injection fent cast a integer
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('d_matriculas_activ');
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
		//$aDades['id_schema'] = $this->iid_schema;
		$aDades['id_nivel'] = $this->iid_nivel;
		$aDades['id_situacion'] = $this->iid_situacion;
		$aDades['preceptor'] = $this->bpreceptor;
		array_walk($aDades, 'core\poner_null');
		//para el caso de los boolean false, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
		if (empty($aDades['preceptor']) || ($aDades['preceptor'] === 'off') || ($aDades['preceptor'] === false) || ($aDades['preceptor'] === 'f')) { $aDades['preceptor']='f'; } else { $aDades['preceptor']='t'; }

		if ($bInsert === false) {
			//UPDATE
			$update="
					id_nivel             	 = :id_nivel,
					id_situacion             = :id_situacion,
					preceptor                = :preceptor";
			if (($qRs = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_activ='$this->iid_activ' AND id_asignatura='$this->iid_asignatura' AND id_nom='$this->iid_nom'")) === false) {
				$sClauError = 'Matricula.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($qRs->execute($aDades) === false) {
					$sClauError = 'Matricula.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
		} else {
			// INSERT
			array_unshift($aDades, $this->iid_activ, $this->iid_asignatura, $this->iid_nom);
			$campos="(id_activ,id_asignatura,id_nom,id_nivel,id_situacion,preceptor)";
			$valores="(:id_activ,:id_asignatura,:id_nom,:id_nivel,:id_situacion,:preceptor)";		
			if (($qRs = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
				$sClauError = 'Matricula.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($qRs->execute($aDades) === false) {
					$sClauError = 'Matricula.insertar.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
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
		if (isset($this->iid_activ) && isset($this->iid_asignatura) && isset($this->iid_nom)) {
			if (($qRs = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_activ='$this->iid_activ' AND id_asignatura='$this->iid_asignatura' AND id_nom='$this->iid_nom'")) === false) {
				$sClauError = 'Matricula.carregar';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			}
			$aDades = $qRs->fetch(\PDO::FETCH_ASSOC);
			switch ($que) {
				case 'tot':
					$this->aDades=$aDades;
					break;
				case 'guardar':
					if (!$qRs->rowCount()) return false;
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
		if (($qRs = $oDbl->exec("DELETE FROM $nom_tabla WHERE id_activ='$this->iid_activ' AND id_asignatura='$this->iid_asignatura' AND id_nom='$this->iid_nom'")) === false) {
			$sClauError = 'Matricula.eliminar';
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
		if (array_key_exists('id_schema',$aDades)) $this->setId_schema($aDades['id_schema']);
		if (array_key_exists('id_activ',$aDades)) $this->setId_activ($aDades['id_activ']);
		if (array_key_exists('id_asignatura',$aDades)) $this->setId_asignatura($aDades['id_asignatura']);
		if (array_key_exists('id_nom',$aDades)) $this->setId_nom($aDades['id_nom']);
		if (array_key_exists('id_nivel',$aDades)) $this->setId_nivel($aDades['id_nivel']);
		if (array_key_exists('id_situacion',$aDades)) $this->setId_situacion($aDades['id_situacion']);
		if (array_key_exists('preceptor',$aDades)) $this->setPreceptor($aDades['preceptor']);
	}

	/* METODES GET i SET --------------------------------------------------------*/

	/**
	 * Recupera tots els atributs de Matricula en un array
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
	 * Recupera las claus primàries de Matricula en un array
	 *
	 * @return array aPrimary_key
	 */
	function getPrimary_key() {
		if (!isset($this->aPrimary_key )) {
			$this->aPrimary_key = array('id_activ' => $this->iid_activ,'id_asignatura' => $this->iid_asignatura,'id_nom' => $this->iid_nom);
		}
		return $this->aPrimary_key;
	}

	/**
	 * Recupera l'atribut iid_schema de Matricula
	 *
	 * @return integer iid_schema
	 */
	function getId_schema() {
		if (!isset($this->iid_schema)) {
			$this->DBCarregar();
		}
		return $this->iid_schema;
	}
	/**
	 * estableix el valor de l'atribut iid_schema de Matricula
	 *
	 * @param integer iid_schema='' optional
	 */
	function setId_schema($iid_schema='') {
		$this->iid_schema = $iid_schema;
	}
	/**
	 * Recupera l'atribut iid_activ de Matricula
	 *
	 * @return integer iid_activ
	 */
	function getId_activ() {
		if (!isset($this->iid_activ)) {
			$this->DBCarregar();
		}
		return $this->iid_activ;
	}
	/**
	 * estableix el valor de l'atribut iid_activ de Matricula
	 *
	 * @param integer iid_activ
	 */
	function setId_activ($iid_activ) {
		$this->iid_activ = $iid_activ;
	}
	/**
	 * Recupera l'atribut iid_asignatura de Matricula
	 *
	 * @return integer iid_asignatura
	 */
	function getId_asignatura() {
		if (!isset($this->iid_asignatura)) {
			$this->DBCarregar();
		}
		return $this->iid_asignatura;
	}
	/**
	 * estableix el valor de l'atribut iid_asignatura de Matricula
	 *
	 * @param integer iid_asignatura
	 */
	function setId_asignatura($iid_asignatura) {
		$this->iid_asignatura = $iid_asignatura;
	}
	/**
	 * Recupera l'atribut iid_nom de Matricula
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
	 * estableix el valor de l'atribut iid_nom de Matricula
	 *
	 * @param integer iid_nom
	 */
	function setId_nom($iid_nom) {
		$this->iid_nom = $iid_nom;
	}
	/**
	 * Recupera l'atribut iid_nivel de Matricula
	 *
	 * @return integer iid_nivel
	 */
	function getId_nivel() {
		if (!isset($this->iid_nivel)) {
			$this->DBCarregar();
		}
		return $this->iid_nivel;
	}
	/**
	 * estableix el valor de l'atribut iid_nivel de Matricula
	 *
	 * @param integer iid_nivel='' optional
	 */
	function setId_nivel($iid_nivel='') {
		$this->iid_nivel = $iid_nivel;
	}
	/**
	 * Recupera l'atribut iid_situacion de Matricula
	 *
	 * @return integer iid_situacion
	 */
	function getId_situacion() {
		if (!isset($this->iid_situacion)) {
			$this->DBCarregar();
		}
		return $this->iid_situacion;
	}
	/**
	 * estableix el valor de l'atribut iid_situacion de Matricula
	 *
	 * @param integer iid_situacion='' optional
	 */
	function setId_situacion($iid_situacion='') {
		$this->iid_situacion = $iid_situacion;
	}
	/**
	 * Recupera l'atribut bpreceptor de Matricula
	 *
	 * @return boolean bpreceptor
	 */
	function getPreceptor() {
		if (!isset($this->bpreceptor)) {
			$this->DBCarregar();
		}
		return $this->bpreceptor;
	}
	/**
	 * estableix el valor de l'atribut bpreceptor de Matricula
	 *
	 * @param boolean bpreceptor='f' optional
	 */
	function setPreceptor($bpreceptor='f') {
		$this->bpreceptor = $bpreceptor;
	}
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$oMatriculaSet = new core\Set();

		$oMatriculaSet->add($this->getDatosId_schema());
		$oMatriculaSet->add($this->getDatosId_nivel());
		$oMatriculaSet->add($this->getDatosId_situacion());
		$oMatriculaSet->add($this->getDatosPreceptor());
		return $oMatriculaSet->getTot();
	}



	/**
	 * Recupera les propietats de l'atribut iid_schema de Matricula
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosId_schema() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_schema'));
		$oDatosCampo->setEtiqueta(_("id_schema"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut iid_nivel de Matricula
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosId_nivel() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_nivel'));
		$oDatosCampo->setEtiqueta(_("id_nivel"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut iid_situacion de Matricula
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosId_situacion() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_situacion'));
		$oDatosCampo->setEtiqueta(_("id_situacion"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut bpreceptor de Matricula
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosPreceptor() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'preceptor'));
		$oDatosCampo->setEtiqueta(_("preceptor"));
		return $oDatosCampo;
	}
}
?>
