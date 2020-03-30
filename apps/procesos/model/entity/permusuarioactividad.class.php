<?php
namespace procesos\model\entity;
use core;
/**
 * Fitxer amb la Classe que accedeix a la taula aux_usuarios_perm
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 02/01/2019
 */
/**
 * Classe que implementa l'entitat aux_usuarios_perm
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 02/01/2019
 */
class PermUsuarioActividad Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de PermUsuarioActividad
	 *
	 * @var array
	 */
	 private $aPrimary_key;

	/**
	 * aDades de PermUsuarioActividad
	 *
	 * @var array
	 */
	 private $aDades;

	/**
	 * Id_item de PermUsuarioActividad
	 *
	 * @var integer
	 */
	 private $iid_item;
	/**
	 * Id_usuario de PermUsuarioActividad
	 *
	 * @var integer
	 */
	 private $iid_usuario;
	/**
	 * Id_tipo_activ_txt de PermUsuarioActividad
	 *
	 * @var string
	 */
	 private $sid_tipo_activ_txt;
	/**
	 * fases_csv de PermUsuarioActividad
	 *
	 * @var string
	 */
	 private $sfases_csv;
	/**
	 * Accion de PermUsuarioActividad
	 *
	 * @var integer
	 */
	 private $iaccion;
	/**
	 * Afecta_a de PermUsuarioActividad
	 *
	 * @var integer
	 */
	 private $iafecta_a;
	/**
	 * Dl_propia de PermUsuarioActividad
	 *
	 * @var boolean
	 */
	 private $bdl_propia;
	/**
	 * JSON fase-accion de PermUsuarioActividad
	 *
	 * @var object JSON
	 */
	 private $json_fase_accion;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/**
	 * oDbl de PermUsuarioActividad
	 *
	 * @var object
	 */
	 protected $oDbl;
	/**
	 * NomTabla de PermUsuarioActividad
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
		$this->setNomTabla('aux_usuarios_perm');
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
		$aDades['id_usuario'] = $this->iid_usuario;
		$aDades['id_tipo_activ_txt'] = $this->sid_tipo_activ_txt;
		$aDades['fases_csv'] = $this->sfases_csv;
		$aDades['accion'] = $this->iaccion;
		$aDades['afecta_a'] = $this->iafecta_a;
		$aDades['dl_propia'] = $this->bdl_propia;
		$aDades['json_fase_accion'] = $this->json_fase_accion;
		array_walk($aDades, 'core\poner_null');
		//para el caso de los boolean FALSE, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
		if ( core\is_true($aDades['dl_propia']) ) { $aDades['dl_propia']='true'; } else { $aDades['dl_propia']='false'; }

		if ($bInsert === FALSE) {
			//UPDATE
			$update="
					id_usuario               = :id_usuario,
					id_tipo_activ_txt        = :id_tipo_activ_txt,
					fases_csv                = :fases_csv,
					accion                   = :accion,
					afecta_a                 = :afecta_a,
					dl_propia                = :dl_propia,
					json_fase_accion         = :json_fase_accion";
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_item='$this->iid_item'")) === FALSE) {
				$sClauError = 'PermUsuarioActividad.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return FALSE;
			} else {
				try {
					$oDblSt->execute($aDades);
				}
				catch ( \PDOException $e) {
					$err_txt=$e->errorInfo[2];
					$this->setErrorTxt($err_txt);
					$sClauError = 'PermUsuarioActividad.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return FALSE;
				}
			}
		} else {
			// INSERT
			$campos="(id_usuario,id_tipo_activ_txt,fases_csv,accion,afecta_a,dl_propia,json_fase_accion)";
			$valores="(:id_usuario,:id_tipo_activ_txt,:fases_csv,:accion,:afecta_a,:dl_propia,:json_fase_accion)";		
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === FALSE) {
				$sClauError = 'PermUsuarioActividad.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return FALSE;
			} else {
				try {
					$oDblSt->execute($aDades);
				}
				catch ( \PDOException $e) {
					$err_txt=$e->errorInfo[2];
					$this->setErrorTxt($err_txt);
					$sClauError = 'PermUsuarioActividad.insertar.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return FALSE;
				}
			}
			$this->id_item = $oDbl->lastInsertId('aux_usuarios_perm_id_item_seq');
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
				$sClauError = 'PermUsuarioActividad.carregar';
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
			$sClauError = 'PermUsuarioActividad.eliminar';
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
		if (array_key_exists('id_usuario',$aDades)) $this->setId_usuario($aDades['id_usuario']);
		if (array_key_exists('id_tipo_activ_txt',$aDades)) $this->setId_tipo_activ_txt($aDades['id_tipo_activ_txt']);
		if (array_key_exists('fases_csv',$aDades)) $this->setFases_csv($aDades['fases_csv']);
		if (array_key_exists('accion',$aDades)) $this->setAccion($aDades['accion']);
		if (array_key_exists('afecta_a',$aDades)) $this->setAfecta_a($aDades['afecta_a']);
		if (array_key_exists('dl_propia',$aDades)) $this->setDl_propia($aDades['dl_propia']);
		if (array_key_exists('json_fase_accion',$aDades)) $this->setJsonFaseAccion($aDades['json_fase_accion']);
	}

	/**
	 * Estableix a empty el valor de tots els atributs
	 *
	 */
	function setNullAllAtributes() {
		$aPK = $this->getPrimary_key();
		$this->setId_item('');
		$this->setId_usuario('');
		$this->setId_tipo_activ_txt('');
		$this->setFases_csv('');
		$this->setAccion('');
		$this->setAfecta_a('');
		$this->setDl_propia('');
		$this->setJsonFaseAccion('');
		$this->setPrimary_key($aPK);
	}


	/* METODES GET i SET --------------------------------------------------------*/

	/**
	 * Recupera tots els atributs de PermUsuarioActividad en un array
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
	 * Recupera las claus primàries de PermUsuarioActividad en un array
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
	 * Estableix las claus primàries de PermUsuarioActividad en un array
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
	 * Recupera l'atribut iid_item de PermUsuarioActividad
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
	 * estableix el valor de l'atribut iid_item de PermUsuarioActividad
	 *
	 * @param integer iid_item
	 */
	function setId_item($iid_item) {
		$this->iid_item = $iid_item;
	}
	/**
	 * Recupera l'atribut iid_usuario de PermUsuarioActividad
	 *
	 * @return integer iid_usuario
	 */
	function getId_usuario() {
		if (!isset($this->iid_usuario)) {
			$this->DBCarregar();
		}
		return $this->iid_usuario;
	}
	/**
	 * estableix el valor de l'atribut iid_usuario de PermUsuarioActividad
	 *
	 * @param integer iid_usuario='' optional
	 */
	function setId_usuario($iid_usuario='') {
		$this->iid_usuario = $iid_usuario;
	}
	/**
	 * Recupera l'atribut sid_tipo_activ_txt de PermUsuarioActividad
	 *
	 * @return string sid_tipo_activ_txt
	 */
	function getId_tipo_activ_txt() {
		if (!isset($this->sid_tipo_activ_txt)) {
			$this->DBCarregar();
		}
		return $this->sid_tipo_activ_txt;
	}
	/**
	 * estableix el valor de l'atribut sid_tipo_activ_txt de PermUsuarioActividad
	 *
	 * @param string sid_tipo_activ_txt='' optional
	 */
	function setId_tipo_activ_txt($sid_tipo_activ_txt='') {
		$this->sid_tipo_activ_txt = $sid_tipo_activ_txt;
	}
	/**
	 * Recupera l'atribut sfases_csv de PermUsuarioActividad
	 *
	 * @return string sfases_csv
	 */
	function getFases_csv() {
		if (!isset($this->sfases_csv)) {
			$this->DBCarregar();
		}
		return $this->sfases_csv;
	}
	/**
	 * estableix el valor de l'atribut sfases_csv de PermUsuarioActividad
	 *
	 * @param string sfases_csv='' optional
	 */
	function setFases_csv($sfases_csv='') {
		$this->sfases_csv = $sfases_csv;
	}
	/**
	 * Recupera l'atribut iaccion de PermUsuarioActividad
	 *
	 * @return integer iaccion
	 */
	function getAccion() {
		if (!isset($this->iaccion)) {
			$this->DBCarregar();
		}
		return $this->iaccion;
	}
	/**
	 * estableix el valor de l'atribut iaccion de PermUsuarioActividad
	 *
	 * @param integer iaccion='' optional
	 */
	function setAccion($iaccion='') {
		$this->iaccion = $iaccion;
	}
	/**
	 * Recupera l'atribut iafecta_a de PermUsuarioActividad
	 *
	 * @return integer iafecta_a
	 */
	function getAfecta_a() {
		if (!isset($this->iafecta_a)) {
			$this->DBCarregar();
		}
		return $this->iafecta_a;
	}
	/**
	 * estableix el valor de l'atribut iafecta_a de PermUsuarioActividad
	 *
	 * @param integer iafecta_a='' optional
	 */
	function setAfecta_a($iafecta_a='') {
		$this->iafecta_a = $iafecta_a;
	}
	/**
	 * Recupera l'atribut bdl_propia de PermUsuarioActividad
	 *
	 * @return boolean bdl_propia
	 */
	function getDl_propia() {
		if (!isset($this->bdl_propia)) {
			$this->DBCarregar();
		}
		return $this->bdl_propia;
	}
	/**
	 * estableix el valor de l'atribut bdl_propia de PermUsuarioActividad
	 *
	 * @param boolean bdl_propia='f' optional
	 */
	function setDl_propia($bdl_propia='f') {
		$this->bdl_propia = $bdl_propia;
	}
	/**
	 * Recupera l'atribut json_fase_accion de PermUsuarioActividad
	 *
	 * @return string json_fase_accion
	 */
	function getJsonFaseAccion() {
		if (!isset($this->json_fase_accion)) {
			$this->DBCarregar();
		}
		return $this->json_fase_accion;
	}
	/**
	 * estableix el valor de l'atribut json_fase_accion de PermUsuarioActividad
	 *
	 * @param string json_fase_accion='' optional
	 */
	function setJsonFaseAccion($json_fase_accion='') {
		$this->json_fase_accion = $json_fase_accion;
	}
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$oPermUsuarioActividadSet = new core\Set();

		$oPermUsuarioActividadSet->add($this->getDatosId_usuario());
		$oPermUsuarioActividadSet->add($this->getDatosId_tipo_activ_txt());
		$oPermUsuarioActividadSet->add($this->getDatosFases_csv());
		$oPermUsuarioActividadSet->add($this->getDatosAccion());
		$oPermUsuarioActividadSet->add($this->getDatosAfecta_a());
		$oPermUsuarioActividadSet->add($this->getDatosDl_propia());
		$oPermUsuarioActividadSet->add($this->getDatosJsonFaseAccion());
		return $oPermUsuarioActividadSet->getTot();
	}



	/**
	 * Recupera les propietats de l'atribut iid_usuario de PermUsuarioActividad
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosId_usuario() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_usuario'));
		$oDatosCampo->setEtiqueta(_("id_usuario"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut sid_tipo_activ_txt de PermUsuarioActividad
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosId_tipo_activ_txt() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_tipo_activ_txt'));
		$oDatosCampo->setEtiqueta(_("id_tipo_activ_txt"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut sfases_csv de PermUsuarioActividad
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosFases_csv() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'fases_csv'));
		$oDatosCampo->setEtiqueta(_("fases_csv"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut iaccion de PermUsuarioActividad
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosAccion() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'accion'));
		$oDatosCampo->setEtiqueta(_("accion"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut iafecta_a de PermUsuarioActividad
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosAfecta_a() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'afecta_a'));
		$oDatosCampo->setEtiqueta(_("afecta_a"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut bdl_propia de PermUsuarioActividad
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosDl_propia() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'dl_propia'));
		$oDatosCampo->setEtiqueta(_("dl_propia"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut json_fase_accion de PermUsuarioActividad
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosJsonFaseAccion() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'json_fase_accion'));
		$oDatosCampo->setEtiqueta(_("json_fase_accion"));
		return $oDatosCampo;
	}
}
