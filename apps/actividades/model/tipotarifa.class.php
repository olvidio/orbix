<?php
namespace actividades\model;
use core;
/**
 * Classe que implementa l'entitat $nom_tabla
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 22/12/2010
 */
class TipoTarifa Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de TipoTarifa
	 *
	 * @var array
	 */
	 private $aPrimary_key;

	/**
	 * aDades de TipoTarifa
	 *
	 * @var array
	 */
	 private $aDades;

	/**
	 * Tarifa de TipoTarifa
	 *
	 * @var integer
	 */
	 private $itarifa;
	/**
	 * Modo de TipoTarifa
	 *
	 * @var integer
	 */
	 private $imodo;
	/**
	 * Letra de TipoTarifa
	 *
	 * @var string
	 */
	 private $sletra;
	/**
	 * Sfsv de TipoTarifa
	 *
	 * @var integer
	 */
	 private $isfsv;
	/**
	 * Observ de TipoTarifa
	 *
	 * @var string
	 */
	 private $sobserv;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
 
	/* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 * Si només necessita un valor, se li pot passar un integer.
	 * En general se li passa un array amb les claus primàries.
	 *
	 * @param integer|array itarifa
	 * 						$a_id. Un array con los nombres=>valores de las claves primarias.
	 */
	function __construct($a_id='') {
		$oDbl = $GLOBALS['oDBC'];
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				if (($nom_id === 'tarifa') && $val_id !== '') $this->itarifa = (int)$val_id; // evitem SQL injection fent cast a integer
			}	} else {
			if (isset($a_id) && $a_id !== '') {
				$this->itarifa = intval($a_id); // evitem SQL injection fent cast a integer
				$this->aPrimary_key = array('itarifa' => $this->itarifa);
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('xa_tipo_tarifa');
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
		$aDades['modo'] = $this->imodo;
		$aDades['letra'] = $this->sletra;
		$aDades['sfsv'] = $this->isfsv;
		$aDades['observ'] = $this->sobserv;
		// ho trec perque el modo pot se 0 i no null
		//array_walk($aDades, 'core\poner_null');


		if ($bInsert === false) {
			//UPDATE
			$update="
					modo                     = :modo,
					letra                    = :letra,
					sfsv                     = :sfsv,
					observ                   = :observ";
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE tarifa='$this->itarifa'")) === false) {
				$sClauError = 'TipoTarifa.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($oDblSt->execute($aDades) === false) {
					$sClauError = 'TipoTarifa.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
		} else {
			// INSERT
			$campos="(modo,letra,sfsv,observ)";
			$valores="(:modo,:letra,:sfsv,:observ)";		
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
				$sClauError = 'TipoTarifa.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($oDblSt->execute($aDades) === false) {
					$sClauError = 'TipoTarifa.insertar.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
			$aDades['tarifa'] = $oDbl->lastInsertId('$nom_tabla_tarifa_seq');
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
		if (isset($this->itarifa)) {
			if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE tarifa='$this->itarifa'")) === false) {
				$sClauError = 'TipoTarifa.carregar';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
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
		if (($oDblSt = $oDbl->exec("DELETE FROM $nom_tabla WHERE tarifa='$this->itarifa'")) === false) {
			$sClauError = 'TipoTarifa.eliminar';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
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
		if (array_key_exists('tarifa',$aDades)) $this->setTarifa($aDades['tarifa']);
		if (array_key_exists('modo',$aDades)) $this->setModo($aDades['modo']);
		if (array_key_exists('letra',$aDades)) $this->setLetra($aDades['letra']);
		if (array_key_exists('sfsv',$aDades)) $this->setSfsv($aDades['sfsv']);
		if (array_key_exists('observ',$aDades)) $this->setObserv($aDades['observ']);
	}

	/* METODES GET i SET --------------------------------------------------------*/

	/**
	 * Recupera tots els atributs de TipoTarifa en un array
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
	 * Recupera las claus primàries de TipoTarifa en un array
	 *
	 * @return array aPrimary_key
	 */
	function getPrimary_key() {
		if (!isset($this->aPrimary_key )) {
			$this->aPrimary_key = array('itarifa' => $this->itarifa);
		}
		return $this->aPrimary_key;
	}

	/**
	 * Recupera l'atribut itarifa de TipoTarifa
	 *
	 * @return integer itarifa
	 */
	function getTarifa() {
		if (!isset($this->itarifa)) {
			$this->DBCarregar();
		}
		return $this->itarifa;
	}
	/**
	 * estableix el valor de l'atribut itarifa de TipoTarifa
	 *
	 * @param integer itarifa
	 */
	function setTarifa($itarifa) {
		$this->itarifa = $itarifa;
	}
	/**
	 * Recupera l'atribut imodo de TipoTarifa
	 *
	 * @return integer imodo
	 */
	function getModo() {
		if (!isset($this->imodo)) {
			$this->DBCarregar();
		}
		return $this->imodo;
	}
	/**
	 * Recupera l'atribut imodo de TipoTarifa
	 *
	 * @return string imodo
	 */
	function getModoTxt() {
		if (!isset($this->imodo)) {
			$this->DBCarregar();
		}
		switch ($this->imodo) {
			case 0:
				$txt = _('por dia');
				break;
			case 1:
				$txt = _('total');
				break;
		}
		return $txt;
	}
	/**
	 * estableix el valor de l'atribut imodo de TipoTarifa
	 *
	 * @param integer imodo='' optional
	 */
	function setModo($imodo='') {
		$this->imodo = $imodo;
	}
	/**
	 * Recupera l'atribut sletra de TipoTarifa
	 *
	 * @return string sletra
	 */
	function getLetra() {
		if (!isset($this->sletra)) {
			$this->DBCarregar();
		}
		return $this->sletra;
	}
	/**
	 * estableix el valor de l'atribut sletra de TipoTarifa
	 *
	 * @param string sletra='' optional
	 */
	function setLetra($sletra='') {
		$this->sletra = $sletra;
	}
	/**
	 * Recupera l'atribut isfsv de TipoTarifa
	 *
	 * @return integer isfsv
	 */
	function getSfsv() {
		if (!isset($this->isfsv)) {
			$this->DBCarregar();
		}
		return $this->isfsv;
	}
	/**
	 * estableix el valor de l'atribut isfsv de TipoTarifa
	 *
	 * @param integer isfsv='' optional
	 */
	function setSfsv($isfsv='') {
		$this->isfsv = $isfsv;
	}
	/**
	 * Recupera l'atribut sobserv de TipoTarifa
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
	 * estableix el valor de l'atribut sobserv de TipoTarifa
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
		$oTipoTarifaSet = new core\Set();

		$oTipoTarifaSet->add($this->getDatosModo());
		$oTipoTarifaSet->add($this->getDatosLetra());
		$oTipoTarifaSet->add($this->getDatosSfsv());
		$oTipoTarifaSet->add($this->getDatosObserv());
		return $oTipoTarifaSet->getTot();
	}



	/**
	 * Recupera les propietats de l'atribut imodo de TipoTarifa
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosModo() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'modo'));
		$oDatosCampo->setEtiqueta(_("modo"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut sletra de TipoTarifa
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosLetra() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'letra'));
		$oDatosCampo->setEtiqueta(_("letra"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut isfsv de TipoTarifa
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosSfsv() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'sfsv'));
		$oDatosCampo->setEtiqueta(_("sfsv"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut iobserv de TipoTarifa
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosObserv() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'observ'));
		$oDatosCampo->setEtiqueta(_("observ"));
		return $oDatosCampo;
	}
}
?>
