<?php
namespace documentos\model\entity;
use core;
/**
 * Fitxer amb la Classe que accedeix a la taula doc_tipo_documento
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 28/4/2022
 */
/**
 * Classe que implementa l'entitat doc_tipo_documento
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 28/4/2022
 */
class TipoDoc Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de TipoDoc
	 *
	 * @var array
	 */
	 private $aPrimary_key;

	/**
	 * aDades de TipoDoc
	 *
	 * @var array
	 */
	 private $aDades;

	/**
	 * bLoaded de TipoDoc
	 *
	 * @var boolean
	 */
	 private $bLoaded = FALSE;

	/**
	 * Id_schema de TipoDoc
	 *
	 * @var integer
	 */
	 private $iid_schema;

	/**
	 * Id_tipo_doc de TipoDoc
	 *
	 * @var integer
	 */
	 private $iid_tipo_doc;
	/**
	 * Nom_doc de TipoDoc
	 *
	 * @var string
	 */
	 private $snom_doc;
	/**
	 * Sigla de TipoDoc
	 *
	 * @var string
	 */
	 private $ssigla;
	/**
	 * Observ de TipoDoc
	 *
	 * @var string
	 */
	 private $sobserv;
	/**
	 * Id_coleccion de TipoDoc
	 *
	 * @var integer
	 */
	 private $iid_coleccion;
	/**
	 * Bajo_llave de TipoDoc
	 *
	 * @var boolean
	 */
	 private $bbajo_llave;
	/**
	 * Vigente de TipoDoc
	 *
	 * @var boolean
	 */
	 private $bvigente;
	/**
	 * Numerado de TipoDoc
	 *
	 * @var boolean
	 */
	 private $bnumerado;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/**
	 * oDbl de TipoDoc
	 *
	 * @var object
	 */
	 protected $oDbl;
	/**
	 * NomTabla de TipoDoc
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
	 * @param integer|array iid_tipo_doc
	 * 						$a_id. Un array con los nombres=>valores de las claves primarias.
	 */
	function __construct($a_id='') {
		$oDbl = $GLOBALS['oDB'];
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				if (($nom_id == 'id_tipo_doc') && $val_id !== '') $this->iid_tipo_doc = (int)$val_id; // evitem SQL injection fent cast a integer
			}
		} else {
			if (isset($a_id) && $a_id !== '') {
				$this->iid_tipo_doc = (integer) $a_id; // evitem SQL injection fent cast a integer
				$this->aPrimary_key = array('iid_tipo_doc' => $this->iid_tipo_doc);
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('doc_tipo_documento');
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
		$aDades['nom_doc'] = $this->snom_doc;
		$aDades['sigla'] = $this->ssigla;
		$aDades['observ'] = $this->sobserv;
		$aDades['id_coleccion'] = $this->iid_coleccion;
		$aDades['bajo_llave'] = $this->bbajo_llave;
		$aDades['vigente'] = $this->bvigente;
		$aDades['numerado'] = $this->bnumerado;
		array_walk($aDades, 'core\poner_null');
		//para el caso de los boolean FALSE, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
		if ( core\is_true($aDades['bajo_llave']) ) { $aDades['bajo_llave']='true'; } else { $aDades['bajo_llave']='false'; }
		if ( core\is_true($aDades['vigente']) ) { $aDades['vigente']='true'; } else { $aDades['vigente']='false'; }
		if ( core\is_true($aDades['numerado']) ) { $aDades['numerado']='true'; } else { $aDades['numerado']='false'; }

		if ($bInsert === FALSE) {
			//UPDATE
			$update="
					nom_doc                  = :nom_doc,
					sigla                    = :sigla,
					observ                   = :observ,
					id_coleccion             = :id_coleccion,
					bajo_llave               = :bajo_llave,
					vigente                  = :vigente,
					numerado                 = :numerado";
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_tipo_doc='$this->iid_tipo_doc'")) === FALSE) {
				$sClauError = 'TipoDoc.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return FALSE;
			} else {
				try {
					$oDblSt->execute($aDades);
				}
				catch ( \PDOException $e) {
					$err_txt=$e->errorInfo[2];
					$this->setErrorTxt($err_txt);
					$sClauError = 'TipoDoc.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return FALSE;
				}
			}
		} else {
			// INSERT
			$campos="(nom_doc,sigla,observ,id_coleccion,bajo_llave,vigente,numerado)";
			$valores="(:nom_doc,:sigla,:observ,:id_coleccion,:bajo_llave,:vigente,:numerado)";		
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === FALSE) {
				$sClauError = 'TipoDoc.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return FALSE;
			} else {
				try {
					$oDblSt->execute($aDades);
				}
				catch ( \PDOException $e) {
					$err_txt=$e->errorInfo[2];
					$this->setErrorTxt($err_txt);
					$sClauError = 'TipoDoc.insertar.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return FALSE;
				}
			}
			$this->id_tipo_doc = $oDbl->lastInsertId('doc_tipo_documento_id_tipo_doc_seq');
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
		if (isset($this->iid_tipo_doc)) {
			if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_tipo_doc='$this->iid_tipo_doc'")) === FALSE) {
				$sClauError = 'TipoDoc.carregar';
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
		if (($oDbl->exec("DELETE FROM $nom_tabla WHERE id_tipo_doc='$this->iid_tipo_doc'")) === FALSE) {
			$sClauError = 'TipoDoc.eliminar';
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
		if (array_key_exists('id_schema',$aDades)) $this->setId_schema($aDades['id_schema']);
		if (array_key_exists('id_tipo_doc',$aDades)) $this->setId_tipo_doc($aDades['id_tipo_doc']);
		if (array_key_exists('nom_doc',$aDades)) $this->setNom_doc($aDades['nom_doc']);
		if (array_key_exists('sigla',$aDades)) $this->setSigla($aDades['sigla']);
		if (array_key_exists('observ',$aDades)) $this->setObserv($aDades['observ']);
		if (array_key_exists('id_coleccion',$aDades)) $this->setId_coleccion($aDades['id_coleccion']);
		if (array_key_exists('bajo_llave',$aDades)) $this->setBajo_llave($aDades['bajo_llave']);
		if (array_key_exists('vigente',$aDades)) $this->setVigente($aDades['vigente']);
		if (array_key_exists('numerado',$aDades)) $this->setNumerado($aDades['numerado']);
	}	
	/**
	 * Estableix a empty el valor de tots els atributs
	 *
	 */
	function setNullAllAtributes() {
		$aPK = $this->getPrimary_key();
		$this->setId_schema('');
		$this->setId_tipo_doc('');
		$this->setNom_doc('');
		$this->setSigla('');
		$this->setObserv('');
		$this->setId_coleccion('');
		$this->setBajo_llave('');
		$this->setVigente('');
		$this->setNumerado('');
		$this->setPrimary_key($aPK);
	}

	/* METODES GET i SET --------------------------------------------------------*/

	/**
	 * Recupera tots els atributs de TipoDoc en un array
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
	 * Recupera las claus primàries de TipoDoc en un array
	 *
	 * @return array aPrimary_key
	 */
	function getPrimary_key() {
		if (!isset($this->aPrimary_key )) {
			$this->aPrimary_key = array('id_tipo_doc' => $this->iid_tipo_doc);
		}
		return $this->aPrimary_key;
	}
	/**
	 * Estableix las claus primàries de TipoDoc en un array
	 *
	 */
	public function setPrimary_key($a_id='') {
	    if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				if (($nom_id == 'id_tipo_doc') && $val_id !== '') $this->iid_tipo_doc = (int)$val_id; // evitem SQL injection fent cast a integer
			}
		} else {
			if (isset($a_id) && $a_id !== '') {
				$this->iid_tipo_doc = (integer) $a_id; // evitem SQL injection fent cast a integer
				$this->aPrimary_key = array('iid_tipo_doc' => $this->iid_tipo_doc);
			}
		}
	}
	

	/**
	 * Recupera l'atribut iid_tipo_doc de TipoDoc
	 *
	 * @return integer iid_tipo_doc
	 */
	function getId_tipo_doc() {
		if (!isset($this->iid_tipo_doc) && !$this->bLoaded) {
			$this->DBCarregar();
		}
		return $this->iid_tipo_doc;
	}
	/**
	 * estableix el valor de l'atribut iid_tipo_doc de TipoDoc
	 *
	 * @param integer iid_tipo_doc
	 */
	function setId_tipo_doc($iid_tipo_doc) {
		$this->iid_tipo_doc = $iid_tipo_doc;
	}
	/**
	 * Recupera l'atribut snom_doc de TipoDoc
	 *
	 * @return string snom_doc
	 */
	function getNom_doc() {
		if (!isset($this->snom_doc) && !$this->bLoaded) {
			$this->DBCarregar();
		}
		return $this->snom_doc;
	}
	/**
	 * estableix el valor de l'atribut snom_doc de TipoDoc
	 *
	 * @param string snom_doc='' optional
	 */
	function setNom_doc($snom_doc='') {
		$this->snom_doc = $snom_doc;
	}
	/**
	 * Recupera l'atribut ssigla de TipoDoc
	 *
	 * @return string ssigla
	 */
	function getSigla() {
		if (!isset($this->ssigla) && !$this->bLoaded) {
			$this->DBCarregar();
		}
		return $this->ssigla;
	}
	/**
	 * estableix el valor de l'atribut ssigla de TipoDoc
	 *
	 * @param string ssigla='' optional
	 */
	function setSigla($ssigla='') {
		$this->ssigla = $ssigla;
	}
	/**
	 * Recupera l'atribut sobserv de TipoDoc
	 *
	 * @return string sobserv
	 */
	function getObserv() {
		if (!isset($this->sobserv) && !$this->bLoaded) {
			$this->DBCarregar();
		}
		return $this->sobserv;
	}
	/**
	 * estableix el valor de l'atribut sobserv de TipoDoc
	 *
	 * @param string sobserv='' optional
	 */
	function setObserv($sobserv='') {
		$this->sobserv = $sobserv;
	}
	/**
	 * Recupera l'atribut iid_coleccion de TipoDoc
	 *
	 * @return integer iid_coleccion
	 */
	function getId_coleccion() {
		if (!isset($this->iid_coleccion) && !$this->bLoaded) {
			$this->DBCarregar();
		}
		return $this->iid_coleccion;
	}
	/**
	 * estableix el valor de l'atribut iid_coleccion de TipoDoc
	 *
	 * @param integer iid_coleccion='' optional
	 */
	function setId_coleccion($iid_coleccion='') {
		$this->iid_coleccion = $iid_coleccion;
	}
	/**
	 * Recupera l'atribut bbajo_llave de TipoDoc
	 *
	 * @return boolean bbajo_llave
	 */
	function getBajo_llave() {
		if (!isset($this->bbajo_llave) && !$this->bLoaded) {
			$this->DBCarregar();
		}
		return $this->bbajo_llave;
	}
	/**
	 * estableix el valor de l'atribut bbajo_llave de TipoDoc
	 *
	 * @param boolean bbajo_llave='f' optional
	 */
	function setBajo_llave($bbajo_llave='f') {
		$this->bbajo_llave = $bbajo_llave;
	}
	/**
	 * Recupera l'atribut bvigente de TipoDoc
	 *
	 * @return boolean bvigente
	 */
	function getVigente() {
		if (!isset($this->bvigente) && !$this->bLoaded) {
			$this->DBCarregar();
		}
		return $this->bvigente;
	}
	/**
	 * estableix el valor de l'atribut bvigente de TipoDoc
	 *
	 * @param boolean bvigente='f' optional
	 */
	function setVigente($bvigente='f') {
		$this->bvigente = $bvigente;
	}
	/**
	 * Recupera l'atribut bnumerado de TipoDoc
	 *
	 * @return boolean bnumerado
	 */
	function getNumerado() {
		if (!isset($this->bnumerado) && !$this->bLoaded) {
			$this->DBCarregar();
		}
		return $this->bnumerado;
	}
	/**
	 * estableix el valor de l'atribut bnumerado de TipoDoc
	 *
	 * @param boolean bnumerado='f' optional
	 */
	function setNumerado($bnumerado='f') {
		$this->bnumerado = $bnumerado;
	}
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$oTipoDocSet = new core\Set();

		$oTipoDocSet->add($this->getDatosNom_doc());
		$oTipoDocSet->add($this->getDatosSigla());
		$oTipoDocSet->add($this->getDatosObserv());
		$oTipoDocSet->add($this->getDatosId_coleccion());
		$oTipoDocSet->add($this->getDatosBajo_llave());
		$oTipoDocSet->add($this->getDatosVigente());
		$oTipoDocSet->add($this->getDatosNumerado());
		return $oTipoDocSet->getTot();
	}



	/**
	 * Recupera les propietats de l'atribut snom_doc de TipoDoc
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosNom_doc() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'nom_doc'));
		$oDatosCampo->setEtiqueta(_("nom_doc"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut ssigla de TipoDoc
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosSigla() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'sigla'));
		$oDatosCampo->setEtiqueta(_("sigla"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut sobserv de TipoDoc
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosObserv() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'observ'));
		$oDatosCampo->setEtiqueta(_("observ"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut iid_coleccion de TipoDoc
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosId_coleccion() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_coleccion'));
		$oDatosCampo->setEtiqueta(_("id_coleccion"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut bbajo_llave de TipoDoc
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosBajo_llave() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'bajo_llave'));
		$oDatosCampo->setEtiqueta(_("bajo_llave"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut bvigente de TipoDoc
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosVigente() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'vigente'));
		$oDatosCampo->setEtiqueta(_("vigente"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut bnumerado de TipoDoc
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosNumerado() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'numerado'));
		$oDatosCampo->setEtiqueta(_("numerado"));
		return $oDatosCampo;
	}
}
