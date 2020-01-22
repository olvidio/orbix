<?php
namespace devel\model\entity;
use core;
/**
 * Fitxer amb la Classe que accedeix a la taula m0_apps
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 15/12/2014
 */
/**
 * Classe que implementa l'entitat m0_apps
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 15/12/2014
 */
class App Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de App
	 *
	 * @var array
	 */
	 private $aPrimary_key;

	/**
	 * aDades de App
	 *
	 * @var array
	 */
	 private $aDades;

	/**
	 * Id_app de App
	 *
	 * @var integer
	 */
	 private $iid_app;
	/**
	 * Nom de App
	 *
	 * @var string
	 */
	 private $snom;
	/**
	 * Db_prefix de App
	 *
	 * @var string
	 */
	 private $sdb_prefix;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/**
	 * oDbl de App
	 *
	 * @var object
	 */
	 protected $oDbl;
	/**
	 * NomTabla de App
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
	 * @param integer|array iid_app
	 * 						$a_id. Un array con los nombres=>valores de las claves primarias.
	 */
	function __construct($a_id='') {
		$oDbl = $GLOBALS['oDBPC'];
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				if (($nom_id == 'id_app') && $val_id !== '') $this->iid_app = (int)$val_id; // evitem SQL injection fent cast a integer
			}
		} else {
			if (isset($a_id) && $a_id !== '') {
				$this->iid_app = intval($a_id); // evitem SQL injection fent cast a integer
				$this->aPrimary_key = array('iid_app' => $this->iid_app);
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('m0_apps');
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
		$aDades['nom'] = $this->snom;
		$aDades['db_prefix'] = $this->sdb_prefix;
		array_walk($aDades, 'core\poner_null');

		if ($bInsert === false) {
			//UPDATE
			$update="
					nom                      = :nom,
					db_prefix                = :db_prefix";
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_app='$this->iid_app'")) === false) {
				$sClauError = 'App.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($oDblSt->execute($aDades) === false) {
					$sClauError = 'App.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
		} else {
			// INSERT
			$campos="(nom,db_prefix)";
			$valores="(:nom,:db_prefix)";		
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
				$sClauError = 'App.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($oDblSt->execute($aDades) === false) {
					$sClauError = 'App.insertar.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
			$this->id_app = $oDbl->lastInsertId('m0_apps_id_app_seq');
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
		if (isset($this->iid_app)) {
			if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_app='$this->iid_app'")) === false) {
				$sClauError = 'App.carregar';
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
		if (($oDblSt = $oDbl->exec("DELETE FROM $nom_tabla WHERE id_app='$this->iid_app'")) === false) {
			$sClauError = 'App.eliminar';
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
		if (array_key_exists('id_app',$aDades)) $this->setId_app($aDades['id_app']);
		if (array_key_exists('nom',$aDades)) $this->setNom($aDades['nom']);
		if (array_key_exists('db_prefix',$aDades)) $this->setDb_prefix($aDades['db_prefix']);
	}

	/**
	 * Estableix a empty el valor de tots els atributs
	 *
	 */
	function setNullAllAtributes() {
		$aPK = $this->getPrimary_key();
		$this->setId_schema('');
		$this->setId_app('');
		$this->setNom('');
		$this->setDb_prefix('');
		$this->setPrimary_key($aPK);
	}


	/* METODES GET i SET --------------------------------------------------------*/

	/**
	 * Recupera tots els atributs de App en un array
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
	 * Recupera las claus primàries de App en un array
	 *
	 * @return array aPrimary_key
	 */
	function getPrimary_key() {
		if (!isset($this->aPrimary_key )) {
			$this->aPrimary_key = array('id_app' => $this->iid_app);
		}
		return $this->aPrimary_key;
	}

	/**
	 * Recupera l'atribut iid_app de App
	 *
	 * @return integer iid_app
	 */
	function getId_app() {
		if (!isset($this->iid_app)) {
			$this->DBCarregar();
		}
		return $this->iid_app;
	}
	/**
	 * estableix el valor de l'atribut iid_app de App
	 *
	 * @param integer iid_app
	 */
	function setId_app($iid_app) {
		$this->iid_app = $iid_app;
	}
	/**
	 * Recupera l'atribut snom de App
	 *
	 * @return string snom
	 */
	function getNom() {
		if (!isset($this->snom)) {
			$this->DBCarregar();
		}
		return $this->snom;
	}
	/**
	 * estableix el valor de l'atribut snom de App
	 *
	 * @param string snom='' optional
	 */
	function setNom($snom='') {
		$this->snom = $snom;
	}
	/**
	 * Recupera l'atribut sdb_prefix de App
	 *
	 * @return string sdb_prefix
	 */
	function getDb_prefix() {
		if (!isset($this->sdb_prefix)) {
			$this->DBCarregar();
		}
		return $this->sdb_prefix;
	}
	/**
	 * estableix el valor de l'atribut sdb_prefix de App
	 *
	 * @param string sdb_prefix='' optional
	 */
	function setDb_prefix($sdb_prefix='') {
		$this->sdb_prefix = $sdb_prefix;
	}
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$oAppSet = new core\Set();

		$oAppSet->add($this->getDatosNom());
		$oAppSet->add($this->getDatosDb_prefix());
		return $oAppSet->getTot();
	}



	/**
	 * Recupera les propietats de l'atribut snom de App
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosNom() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'nom'));
		$oDatosCampo->setEtiqueta(_("nombre"));
		$oDatosCampo->setTipo('texto');
		$oDatosCampo->setArgument(30);
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut sdb_prefix de App
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosDb_prefix() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'db_prefix'));
		$oDatosCampo->setEtiqueta(_("db prefix"));
		$oDatosCampo->setTipo('texto');
		$oDatosCampo->setArgument(30);
		return $oDatosCampo;
	}
}
?>
