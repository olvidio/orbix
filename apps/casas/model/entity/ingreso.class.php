<?php
namespace casas\model\entity;
use core;
/**
 * Fitxer amb la Classe que accedeix a la taula da_ingresos_dl
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 26/6/2019
 */
/**
 * Classe que implementa l'entitat da_ingresos_dl
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 26/6/2019
 */
class Ingreso Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de Ingreso
	 *
	 * @var array
	 */
	 private $aPrimary_key;

	/**
	 * aDades de Ingreso
	 *
	 * @var array
	 */
	 private $aDades;

	/**
	 * Id_activ de Ingreso
	 *
	 * @var integer
	 */
	 private $iid_activ;
	/**
	 * Ingresos de Ingreso
	 *
	 * @var float
	 */
	 private $iingresos;
	/**
	 * Num_asistentes de Ingreso
	 *
	 * @var integer
	 */
	 private $inum_asistentes;
	/**
	 * Ingresos_previstos de Ingreso
	 *
	 * @var float
	 */
	 private $iingresos_previstos;
	/**
	 * Num_asistentes_previstos de Ingreso
	 *
	 * @var integer
	 */
	 private $inum_asistentes_previstos;
	/**
	 * Observ de Ingreso
	 *
	 * @var string
	 */
	 private $sobserv;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/**
	 * oDbl de Ingreso
	 *
	 * @var object
	 */
	 protected $oDbl;
	/**
	 * NomTabla de Ingreso
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
	 * @param integer|array iid_activ
	 * 						$a_id. Un array con los nombres=>valores de las claves primarias.
	 */
	function __construct($a_id='') {
		$oDbl = $GLOBALS['oDBC'];
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				if (($nom_id == 'id_activ') && $val_id !== '') $this->iid_activ = (int)$val_id; // evitem SQL injection fent cast a integer
			}
	   } else {
			if (isset($a_id) && $a_id !== '') {
				$this->iid_activ = intval($a_id); // evitem SQL injection fent cast a integer
				$this->aPrimary_key = array('iid_activ' => $this->iid_activ);
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('da_ingresos_dl');
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
		$aDades['ingresos'] = $this->iingresos;
		$aDades['num_asistentes'] = $this->inum_asistentes;
		$aDades['ingresos_previstos'] = $this->iingresos_previstos;
		$aDades['num_asistentes_previstos'] = $this->inum_asistentes_previstos;
		$aDades['observ'] = $this->sobserv;
		array_walk($aDades, 'core\poner_null');

		if ($bInsert === FALSE) {
			//UPDATE
			$update="
					ingresos                 = :ingresos,
					num_asistentes           = :num_asistentes,
					ingresos_previstos       = :ingresos_previstos,
					num_asistentes_previstos = :num_asistentes_previstos,
					observ                   = :observ";
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_activ='$this->iid_activ'")) === FALSE) {
				$sClauError = 'Ingreso.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return FALSE;
			} else {
				if ($oDblSt->execute($aDades) === FALSE) {
					$sClauError = 'Ingreso.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return FALSE;
				}
			}
		} else {
			// INSERT
			array_unshift($aDades, $this->iid_activ);
			$campos="(id_activ,ingresos,num_asistentes,ingresos_previstos,num_asistentes_previstos,observ)";
			$valores="(:id_activ,:ingresos,:num_asistentes,:ingresos_previstos,:num_asistentes_previstos,:observ)";		
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === FALSE) {
				$sClauError = 'Ingreso.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return FALSE;
			} else {
				if ($oDblSt->execute($aDades) === FALSE) {
					$sClauError = 'Ingreso.insertar.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return FALSE;
				}
			}
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
		if (isset($this->iid_activ)) {
			if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_activ='$this->iid_activ'")) === FALSE) {
				$sClauError = 'Ingreso.carregar';
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
		if (($oDbl->exec("DELETE FROM $nom_tabla WHERE id_activ='$this->iid_activ'")) === FALSE) {
			$sClauError = 'Ingreso.eliminar';
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
		if (array_key_exists('ingresos',$aDades)) $this->setIngresos($aDades['ingresos']);
		if (array_key_exists('num_asistentes',$aDades)) $this->setNum_asistentes($aDades['num_asistentes']);
		if (array_key_exists('ingresos_previstos',$aDades)) $this->setIngresos_previstos($aDades['ingresos_previstos']);
		if (array_key_exists('num_asistentes_previstos',$aDades)) $this->setNum_asistentes_previstos($aDades['num_asistentes_previstos']);
		if (array_key_exists('observ',$aDades)) $this->setObserv($aDades['observ']);
	}

	/* METODES GET i SET --------------------------------------------------------*/

	/**
	 * Recupera tots els atributs de Ingreso en un array
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
	 * Recupera las claus primàries de Ingreso en un array
	 *
	 * @return array aPrimary_key
	 */
	function getPrimary_key() {
		if (!isset($this->aPrimary_key )) {
			$this->aPrimary_key = array('id_activ' => $this->iid_activ);
		}
		return $this->aPrimary_key;
	}

	/**
	 * Recupera l'atribut iid_activ de Ingreso
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
	 * estableix el valor de l'atribut iid_activ de Ingreso
	 *
	 * @param integer iid_activ
	 */
	function setId_activ($iid_activ) {
		$this->iid_activ = $iid_activ;
	}
	/**
	 * Recupera l'atribut iingresos de Ingreso
	 *
	 * @return float iingresos
	 */
	function getIngresos() {
		if (!isset($this->iingresos)) {
			$this->DBCarregar();
		}
		return $this->iingresos;
	}
	/**
	 * estableix el valor de l'atribut iingresos de Ingreso
	 *
	 * @param float iingresos='' optional
	 */
	function setIngresos($iingresos='') {
		$this->iingresos = $iingresos;
	}
	/**
	 * Recupera l'atribut inum_asistentes de Ingreso
	 *
	 * @return integer inum_asistentes
	 */
	function getNum_asistentes() {
		if (!isset($this->inum_asistentes)) {
			$this->DBCarregar();
		}
		return $this->inum_asistentes;
	}
	/**
	 * estableix el valor de l'atribut inum_asistentes de Ingreso
	 *
	 * @param integer inum_asistentes='' optional
	 */
	function setNum_asistentes($inum_asistentes='') {
		$this->inum_asistentes = $inum_asistentes;
	}
	/**
	 * Recupera l'atribut iingresos_previstos de Ingreso
	 *
	 * @return float iingresos_previstos
	 */
	function getIngresos_previstos() {
		if (!isset($this->iingresos_previstos)) {
			$this->DBCarregar();
		}
		return $this->iingresos_previstos;
	}
	/**
	 * estableix el valor de l'atribut iingresos_previstos de Ingreso
	 *
	 * @param float iingresos_previstos='' optional
	 */
	function setIngresos_previstos($iingresos_previstos='') {
		$this->iingresos_previstos = $iingresos_previstos;
	}
	/**
	 * Recupera l'atribut inum_asistentes_previstos de Ingreso
	 *
	 * @return integer inum_asistentes_previstos
	 */
	function getNum_asistentes_previstos() {
		if (!isset($this->inum_asistentes_previstos)) {
			$this->DBCarregar();
		}
		return $this->inum_asistentes_previstos;
	}
	/**
	 * estableix el valor de l'atribut inum_asistentes_previstos de Ingreso
	 *
	 * @param integer inum_asistentes_previstos='' optional
	 */
	function setNum_asistentes_previstos($inum_asistentes_previstos='') {
		$this->inum_asistentes_previstos = $inum_asistentes_previstos;
	}
	/**
	 * Recupera l'atribut sobserv de Ingreso
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
	 * estableix el valor de l'atribut sobserv de Ingreso
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
		$oIngresoSet = new core\Set();

		$oIngresoSet->add($this->getDatosIngresos());
		$oIngresoSet->add($this->getDatosNum_asistentes());
		$oIngresoSet->add($this->getDatosIngresos_previstos());
		$oIngresoSet->add($this->getDatosNum_asistentes_previstos());
		$oIngresoSet->add($this->getDatosObserv());
		return $oIngresoSet->getTot();
	}



	/**
	 * Recupera les propietats de l'atribut iingresos de Ingreso
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosIngresos() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'ingresos'));
		$oDatosCampo->setEtiqueta(_("ingresos"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut inum_asistentes de Ingreso
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosNum_asistentes() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'num_asistentes'));
		$oDatosCampo->setEtiqueta(_("num_asistentes"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut iingresos_previstos de Ingreso
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosIngresos_previstos() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'ingresos_previstos'));
		$oDatosCampo->setEtiqueta(_("ingresos_previstos"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut inum_asistentes_previstos de Ingreso
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosNum_asistentes_previstos() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'num_asistentes_previstos'));
		$oDatosCampo->setEtiqueta(_("num_asistentes_previstos"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut sobserv de Ingreso
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
}
