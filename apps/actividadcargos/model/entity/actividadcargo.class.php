<?php
namespace actividadcargos\model\entity;
use core;
/**
 * Fitxer amb la Classe que accedeix a la taula d_cargos_activ_dl
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 19/11/2014
 */
/**
 * Classe que implementa l'entitat d_cargos_activ_dl
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 19/11/2014
 */
class ActividadCargo Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de ActividadCargo
	 *
	 * @var array
	 */
	 private $aPrimary_key;

	/**
	 * aDades de ActividadCargo
	 *
	 * @var array
	 */
	 private $aDades;

	/**
	 * Id_schema de ActividadCargo
	 *
	 * @var integer
	 */
	 private $iid_schema;
	/**
	 * Id_item de ActividadCargo
	 *
	 * @var integer
	 */
	 private $iid_item;
	/**
	 * Id_activ de ActividadCargo
	 *
	 * @var integer
	 */
	 private $iid_activ;
	/**
	 * Id_cargo de ActividadCargo
	 *
	 * @var integer
	 */
	 private $iid_cargo;
	/**
	 * Id_nom de ActividadCargo
	 *
	 * @var integer
	 */
	 private $iid_nom;
	/**
	 * Puede_agd de ActividadCargo
	 *
	 * @var boolean
	 */
	 private $bpuede_agd;
	/**
	 * Observ de ActividadCargo
	 *
	 * @var string
	 */
	 private $sobserv;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/**
	 * oDbl de ActividadCargo
	 *
	 * @var object
	 */
	 protected $oDbl;
	/**
	 * NomTabla de ActividadCargo
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
		$oDbl = $GLOBALS['oDB'];
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				//if (($nom_id == 'id_item') && $val_id !== '') $this->iid_item = (int)$val_id; // evitem SQL injection fent cast a integer
				$this->$nom_id = (int)$val_id; // evitem SQL injection fent cast a integer
			}	} else {
			if (isset($a_id) && $a_id !== '') {
				$this->iid_item = intval($a_id); // evitem SQL injection fent cast a integer
				$this->aPrimary_key = array('iid_item' => $this->iid_item);
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('d_cargos_activ_dl');
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
		$aDades['id_activ'] = $this->iid_activ;
		$aDades['id_cargo'] = $this->iid_cargo;
		$aDades['id_nom'] = $this->iid_nom;
		$aDades['puede_agd'] = $this->bpuede_agd;
		$aDades['observ'] = $this->sobserv;
		array_walk($aDades, 'core\poner_null');
		//para el caso de los boolean false, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
		$aDades['puede_agd'] = ($aDades['puede_agd'] === 't')? 'true' : '';
		if ( filter_var( $aDades['puede_agd'], FILTER_VALIDATE_BOOLEAN)) { $aDades['puede_agd']='t'; } else { $aDades['puede_agd']='f'; }

		if ($bInsert === false) {
			//UPDATE
			$update="
					id_activ                 = :id_activ,
					id_cargo                 = :id_cargo,
					id_nom                   = :id_nom,
					puede_agd                = :puede_agd,
					observ                   = :observ";
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_item=$this->iid_item ")) === false) {
				$sClauError = 'ActividadCargo.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($oDblSt->execute($aDades) === false) {
					$sClauError = 'ActividadCargo.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
		} else {
			// INSERT
			$campos="(id_activ,id_cargo,id_nom,puede_agd,observ)";
			$valores="(:id_activ,:id_cargo,:id_nom,:puede_agd,:observ)";		
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
				$sClauError = 'ActividadCargo.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($oDblSt->execute($aDades) === false) {
					$sClauError = 'ActividadCargo.insertar.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
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
		if (isset($this->iid_item)) {
			if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_item=$this->iid_item")) === false) {
				$sClauError = 'ActividadCargo.carregar';
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
			return TRUE;
		} elseif (!empty($this->aPrimary_key)) {
		    if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla
                    WHERE id_activ=$this->iid_activ AND id_cargo=$this->iid_cargo")) === FALSE) {
                    $sClauError = 'Proceso.carregar';
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
		if (($oDbl->exec("DELETE FROM $nom_tabla WHERE id_item=$this->iid_item")) === false) {
			$sClauError = 'ActividadCargo.eliminar';
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
		if (array_key_exists('id_item',$aDades)) $this->setId_item($aDades['id_item']);
		if (array_key_exists('id_activ',$aDades)) $this->setId_activ($aDades['id_activ']);
		if (array_key_exists('id_cargo',$aDades)) $this->setId_cargo($aDades['id_cargo']);
		if (array_key_exists('id_nom',$aDades)) $this->setId_nom($aDades['id_nom']);
		if (array_key_exists('puede_agd',$aDades)) $this->setPuede_agd($aDades['puede_agd']);
		if (array_key_exists('observ',$aDades)) $this->setObserv($aDades['observ']);
	}

	/* METODES GET i SET --------------------------------------------------------*/

	/**
	 * Recupera tots els atributs de ActividadCargo en un array
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
	 * Recupera las claus primàries de ActividadCargo en un array
	 *
	 * @return array aPrimary_key
	 */
	function getPrimary_key() {
		if (!isset($this->aPrimary_key )) {
			$this->aPrimary_key = array('iid_item' => $this->iid_item);
		}
		return $this->aPrimary_key;
	}

	/**
	 * Recupera l'atribut iid_item de ActividadCargo
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
	 * estableix el valor de l'atribut iid_item de ActividadCargo
	 *
	 * @param integer iid_item
	 */
	function setId_item($iid_item) {
		$this->iid_item = $iid_item;
	}

	/**
	 * Recupera l'atribut iid_activ de ActividadCargo
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
	 * estableix el valor de l'atribut iid_activ de ActividadCargo
	 *
	 * @param integer iid_activ
	 */
	function setId_activ($iid_activ) {
		$this->iid_activ = $iid_activ;
	}
	/**
	 * Recupera l'atribut iid_cargo de ActividadCargo
	 *
	 * @return integer iid_cargo
	 */
	function getId_cargo() {
		if (!isset($this->iid_cargo)) {
			$this->DBCarregar();
		}
		return $this->iid_cargo;
	}
	/**
	 * estableix el valor de l'atribut iid_cargo de ActividadCargo
	 *
	 * @param integer iid_cargo
	 */
	function setId_cargo($iid_cargo) {
		$this->iid_cargo = $iid_cargo;
	}
	/**
	 * Recupera l'atribut iid_nom de ActividadCargo
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
	 * estableix el valor de l'atribut iid_nom de ActividadCargo
	 *
	 * @param integer iid_nom='' optional
	 */
	function setId_nom($iid_nom='') {
		$this->iid_nom = $iid_nom;
	}
	/**
	 * Recupera l'atribut bpuede_agd de ActividadCargo
	 *
	 * @return boolean bpuede_agd
	 */
	function getPuede_agd() {
		if (!isset($this->bpuede_agd)) {
			$this->DBCarregar();
		}
		return $this->bpuede_agd;
	}
	/**
	 * estableix el valor de l'atribut bpuede_agd de ActividadCargo
	 *
	 * @param boolean bpuede_agd='f' optional
	 */
	function setPuede_agd($bpuede_agd='f') {
		$this->bpuede_agd = $bpuede_agd;
	}
	/**
	 * Recupera l'atribut sobserv de ActividadCargo
	 *
	 * @return string sobserv
	 */
	function getObserv() {
		if (!isset($this->sobserv)) {
			$this->DBCarregar();
		}
		return $this->sobserv;
	}
	/**
	 * estableix el valor de l'atribut sobserv de ActividadCargo
	 *
	 * @param string sobserv='' optional
	 */
	function setObserv($sobserv='') {
		$this->sobserv = $sobserv;
	}
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$oActividadCargoSet = new core\Set();

		$oActividadCargoSet->add($this->getDatosId_schema());
		$oActividadCargoSet->add($this->getDatosId_nom());
		$oActividadCargoSet->add($this->getDatosPuede_agd());
		$oActividadCargoSet->add($this->getDatosObserv());
		return $oActividadCargoSet->getTot();
	}



	/**
	 * Recupera les propietats de l'atribut iid_schema de ActividadCargo
	 * en una clase del tipus DatosCampo
	 *
	 * @return object DatosCampo
	 */
	function getDatosId_schema() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_schema'));
		$oDatosCampo->setEtiqueta(_("id_schema"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut iid_nom de ActividadCargo
	 * en una clase del tipus DatosCampo
	 *
	 * @return object DatosCampo
	 */
	function getDatosId_nom() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_nom'));
		$oDatosCampo->setEtiqueta(_("id_nom"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut bpuede_agd de ActividadCargo
	 * en una clase del tipus DatosCampo
	 *
	 * @return object DatosCampo
	 */
	function getDatosPuede_agd() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'puede_agd'));
		$oDatosCampo->setEtiqueta(_("¿puede ser agd?"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut sobserv de ActividadCargo
	 * en una clase del tipus DatosCampo
	 *
	 * @return object DatosCampo
	 */
	function getDatosObserv() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'observ'));
		$oDatosCampo->setEtiqueta(_("observaciones"));
		return $oDatosCampo;
	}
}