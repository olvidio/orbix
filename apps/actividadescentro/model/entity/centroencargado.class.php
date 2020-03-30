<?php
namespace actividadescentro\model\entity;
use cambios\model\gestorAvisoCambios;
use core;
/**
 * Fitxer amb la Classe que accedeix a la taula da_ctr_encargados
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 07/01/2019
 */
/**
 * Classe que implementa l'entitat da_ctr_encargados
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 07/01/2019
 */
class CentroEncargado Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de CentroEncargado
	 *
	 * @var array
	 */
	 private $aPrimary_key;

	/**
	 * aDades de CentroEncargado
	 *
	 * @var array
	 */
	 private $aDades;

	/**
	 * aDades de ActividadProcesoTarea abans dels canvis.
	 *
	 * @var array
	 */
	 private $aDadesActuals;
	 
	/**
	 * Id_activ de CentroEncargado
	 *
	 * @var integer
	 */
	 private $iid_activ;
	/**
	 * Id_ubi de CentroEncargado
	 *
	 * @var integer
	 */
	 private $iid_ubi;
	/**
	 * Num_orden de CentroEncargado
	 *
	 * @var integer
	 */
	 private $inum_orden;
	/**
	 * Encargo de CentroEncargado
	 *
	 * @var string
	 */
	 private $sencargo;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/**
	 * oDbl de CentroEncargado
	 *
	 * @var object
	 */
	 protected $oDbl;
	/**
	 * NomTabla de CentroEncargado
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
	 * @param integer|array iid_activ,iid_ubi
	 * 						$a_id. Un array con los nombres=>valores de las claves primarias.
	 */
	function __construct($a_id='') {
		$oDbl = $GLOBALS['oDBC'];
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				if (($nom_id == 'id_activ') && $val_id !== '') $this->iid_activ = (int)$val_id; // evitem SQL injection fent cast a integer
				if (($nom_id == 'id_ubi') && $val_id !== '') $this->iid_ubi = (int)$val_id; // evitem SQL injection fent cast a integer
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('da_ctr_encargados');
	}

	/* METODES PUBLICS ----------------------------------------------------------*/

	/**
	 * Desa els atributs de l'objecte a la base de dades.
	 * Si no hi ha el registre, fa el insert, si hi es fa el update.
	 *
	 *@param bool optional $quiet : true per que no apunti els canvis. 0 (per defecte) apunta els canvis.
	 */
	public function DBGuardar($quiet=0) {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		if ($this->DBCarregar('guardar') === FALSE) { $bInsert=TRUE; } else { $bInsert=FALSE; }
		$aDades=array();
		$aDades['num_orden'] = $this->inum_orden;
		$aDades['encargo'] = $this->sencargo;
		array_walk($aDades, 'core\poner_null');

		if ($bInsert === FALSE) {
			//UPDATE
			$update="
					num_orden                = :num_orden,
					encargo                  = :encargo";
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_activ='$this->iid_activ' AND id_ubi='$this->iid_ubi'")) === FALSE) {
				$sClauError = 'CentroEncargado.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return FALSE;
			} else {
				try {
					$oDblSt->execute($aDades);
				}
				catch ( \PDOException $e) {
					$err_txt=$e->errorInfo[2];
					$this->setErrorTxt($err_txt);
					$sClauError = 'CentroEncargado.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return FALSE;
				}
			}
			// Anoto el cambio
			if (empty($quiet) && core\ConfigGlobal::is_app_installed('cambios')) {
			    $oGestorCanvis = new gestorAvisoCambios();
			    $shortClassName = (new \ReflectionClass($this))->getShortName();
			    $oGestorCanvis->addCanvi($shortClassName, 'UPDATE', $this->iid_activ, $aDades, $this->aDadesActuals);
			}
            $this->setAllAtributes($aDades);
		} else {
			// INSERT
			array_unshift($aDades, $this->iid_activ, $this->iid_ubi);
			$campos="(id_activ,id_ubi,num_orden,encargo)";
			$valores="(:id_activ,:id_ubi,:num_orden,:encargo)";		
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === FALSE) {
				$sClauError = 'CentroEncargado.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return FALSE;
			} else {
				try {
					$oDblSt->execute($aDades);
				}
				catch ( \PDOException $e) {
					$err_txt=$e->errorInfo[2];
					$this->setErrorTxt($err_txt);
					$sClauError = 'CentroEncargado.insertar.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return FALSE;
				}
			}
			if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_activ='$this->iid_activ' AND id_ubi='$this->iid_ubi'")) === false) {
			    $sClauError = get_class($this).'.carregar.Last';
			    $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			    return false;
			}
			$aDadesLast = $oDblSt->fetch(\PDO::FETCH_ASSOC);
			$this->aDades=$aDadesLast;
			$this->setAllAtributes($aDadesLast);
			// Anoto el cambio
			if (empty($quiet) && core\ConfigGlobal::is_app_installed('cambios')) {
			    $oGestorCanvis = new gestorAvisoCambios();
			    $shortClassName = (new \ReflectionClass($this))->getShortName();
			    $oGestorCanvis->addCanvi($shortClassName, 'INSERT', $aDadesLast['id_activ'], $this->aDades, array());
			}
		}
		return TRUE;
	}

	/**
	 * Carrega els camps de la base de dades com atributs de l'objecte.
	 *
	 */
	public function DBCarregar($que=null) {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		if (isset($this->iid_activ) && isset($this->iid_ubi)) {
			if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_activ='$this->iid_activ' AND id_ubi='$this->iid_ubi'")) === FALSE) {
				$sClauError = 'CentroEncargado.carregar';
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
					$this->aDadesActuals=$aDades;
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
		// que tenga el módulo de 'cambios'
		if (core\ConfigGlobal::is_app_installed('cambios')) {
		    // per carregar les dades a $this->aDadesActuals i poder posar-les als canvis.
		    $this->DBCarregar('guardar');
		    // ho poso abans d'esborrar perque sino no trova cap valor. En el cas d'error s'hauria d'esborrar l'apunt.
		    $oGestorCanvis = new gestorAvisoCambios();
		    $shortClassName = (new \ReflectionClass($this))->getShortName();
		    $oGestorCanvis->addCanvi($shortClassName, 'DELETE', $this->iid_activ, [], $this->aDadesActuals);
		}
		if (($oDbl->exec("DELETE FROM $nom_tabla WHERE id_activ='$this->iid_activ' AND id_ubi='$this->iid_ubi'")) === FALSE) {
			$sClauError = 'CentroEncargado.eliminar';
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
		if (array_key_exists('id_activ',$aDades)) $this->setId_activ($aDades['id_activ']);
		if (array_key_exists('id_ubi',$aDades)) $this->setId_ubi($aDades['id_ubi']);
		if (array_key_exists('num_orden',$aDades)) $this->setNum_orden($aDades['num_orden']);
		if (array_key_exists('encargo',$aDades)) $this->setEncargo($aDades['encargo']);
	}

	/**
	 * Estableix a empty el valor de tots els atributs
	 *
	 */
	function setNullAllAtributes() {
		$aPK = $this->getPrimary_key();
		$this->setId_activ('');
		$this->setId_ubi('');
		$this->setNum_orden('');
		$this->setEncargo('');
		$this->setPrimary_key($aPK);
	}


	/* METODES GET i SET --------------------------------------------------------*/

	/**
	 * Recupera tots els atributs de CentroEncargado en un array
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
	 * Recupera las claus primàries de CentroEncargado en un array
	 *
	 * @return array aPrimary_key
	 */
	function getPrimary_key() {
		if (!isset($this->aPrimary_key )) {
			$this->aPrimary_key = array('id_activ' => $this->iid_activ,'id_ubi' => $this->iid_ubi);
		}
		return $this->aPrimary_key;
	}
	
	/**
	 * Estableix las claus primàries de CentroEncargado en un array
	 *
	 * @return array aPrimary_key
	 */
	public function setPrimary_key($a_id='') {
	    if (is_array($a_id)) {
	        $this->aPrimary_key = $a_id;
	        foreach($a_id as $nom_id=>$val_id) {
	            if (($nom_id == 'id_activ') && $val_id !== '') $this->iid_activ = (int)$val_id; // evitem SQL injection fent cast a integer
	            if (($nom_id == 'id_ubi') && $val_id !== '') $this->iid_ubi = (int)$val_id; // evitem SQL injection fent cast a integer
	        }
	    }
	}
	
	/**
	 * Recupera l'atribut iid_activ de CentroEncargado
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
	 * estableix el valor de l'atribut iid_activ de CentroEncargado
	 *
	 * @param integer iid_activ
	 */
	function setId_activ($iid_activ) {
		$this->iid_activ = $iid_activ;
	}
	/**
	 * Recupera l'atribut iid_ubi de CentroEncargado
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
	 * estableix el valor de l'atribut iid_ubi de CentroEncargado
	 *
	 * @param integer iid_ubi
	 */
	function setId_ubi($iid_ubi) {
		$this->iid_ubi = $iid_ubi;
	}
	/**
	 * Recupera l'atribut inum_orden de CentroEncargado
	 *
	 * @return integer inum_orden
	 */
	function getNum_orden() {
		if (!isset($this->inum_orden)) {
			$this->DBCarregar();
		}
		return $this->inum_orden;
	}
	/**
	 * estableix el valor de l'atribut inum_orden de CentroEncargado
	 *
	 * @param integer inum_orden='' optional
	 */
	function setNum_orden($inum_orden='') {
		$this->inum_orden = $inum_orden;
	}
	/**
	 * Recupera l'atribut sencargo de CentroEncargado
	 *
	 * @return string sencargo
	 */
	function getEncargo() {
		if (!isset($this->sencargo)) {
			$this->DBCarregar();
		}
		return $this->sencargo;
	}
	/**
	 * estableix el valor de l'atribut sencargo de CentroEncargado
	 *
	 * @param string sencargo='' optional
	 */
	function setEncargo($sencargo='') {
		$this->sencargo = $sencargo;
	}
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$oCentroEncargadoSet = new core\Set();
		
		$oCentroEncargadoSet->add($this->getDatosId_ubi());
		$oCentroEncargadoSet->add($this->getDatosNum_orden());
		$oCentroEncargadoSet->add($this->getDatosEncargo());
		return $oCentroEncargadoSet->getTot();
	}


	
	/**
	 * Recupera les propietats de l'atribut iid_ubi de CentroEncargado
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosId_ubi() {
		$nom_tabla = $this->getNomTabla();
	    $oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_ubi'));
	    $oDatosCampo->setEtiqueta(_("centro"));
	    $oDatosCampo->setTipo('opciones');
	    $oDatosCampo->setArgument('ubis\model\entity\CentroDl'); // nombre del objeto relacionado
	    $oDatosCampo->setArgument2('getNombre_ubi'); // método para obtener el valor a mostrar del objeto relacionado.
	    $oDatosCampo->setArgument3('getListaCentros'); // método con que crear la lista de opciones del Gestor objeto relacionado.
	    
	    return $oDatosCampo;
	}
	
	/**
	 * Recupera les propietats de l'atribut inum_orden de CentroEncargado
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosNum_orden() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'num_orden'));
		$oDatosCampo->setEtiqueta(_("número de orden"));
		$oDatosCampo->setTipo('texto');
		$oDatosCampo->setArgument(3);
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut sencargo de CentroEncargado
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosEncargo() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'encargo'));
		$oDatosCampo->setEtiqueta(_("encargo"));
		$oDatosCampo->setTipo('texto');
		$oDatosCampo->setArgument(30);
		return $oDatosCampo;
	}
}
