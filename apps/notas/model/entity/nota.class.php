<?php
namespace notas\model\entity;
use core;
/**
 * Fitxer amb la Classe que accedeix a la taula e_notas_situacion
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 07/04/2014
 */
/**
 * Classe que implementa l'entitat e_notas_situacion
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 07/04/2014
 */
class Nota Extends core\ClasePropiedades {
    // tipo plaza constantes.
    // 2:cursada
    const CURSADA       = 2; // Cursada
    const ARRAY_STATUS_TXT = [
        self::CURSADA => "cursada",
    ];
    // NO se usan, son solo para asegurar que exista la traducción
    private function traduccion () {
        $p = _("cursada");
    }
    
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de Nota
	 *
	 * @var array
	 */
	 protected $aPrimary_key;

	/**
	 * aDades de Nota
	 *
	 * @var array
	 */
	 protected $aDades;

	/**
	 * Id_situacion de Nota
	 *
	 * @var integer
	 */
	 protected $iid_situacion;
	/**
	 * Descripcion de Nota
	 *
	 * @var string
	 */
	 protected $sdescripcion;
	/**
	 * Superada de Nota
	 *
	 * @var boolean
	 */
	 protected $bsuperada;
	/**
	 * Breve de Nota
	 *
	 * @var string
	 */
	 protected $sbreve;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/**
	 * oDbl de Nota
	 *
	 * @var object
	 */
	 protected $oDbl;
	/**
	 * NomTabla de Nota
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
	 * @param integer|array iid_situacion
	 * 						$a_id. Un array con los nombres=>valores de las claves primarias.
	 */
	function __construct($a_id='') {
		$oDbl = $GLOBALS['oDBPC'];
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				if (($nom_id == 'id_situacion') && $val_id !== '') $this->iid_situacion = (int)$val_id; // evitem SQL injection fent cast a integer
			}
		} else {
			if (isset($a_id) && $a_id !== '') {
				$this->iid_situacion = intval($a_id); // evitem SQL injection fent cast a integer
				$this->aPrimary_key = array('iid_situacion' => $this->iid_situacion);
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('e_notas_situacion');
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
		$aDades['descripcion'] = $this->sdescripcion;
		$aDades['superada'] = $this->bsuperada;
		$aDades['breve'] = $this->sbreve;
		array_walk($aDades, 'core\poner_null');
		//para el caso de los boolean false, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
		$aDades['superada'] = ($aDades['superada'] === 't')? 'true' : $aDades['superada'];
		if ( filter_var( $aDades['superada'], FILTER_VALIDATE_BOOLEAN)) { $aDades['superada']='t'; } else { $aDades['superada']='f'; }

		if ($bInsert === false) {
			//UPDATE
			$update="
					descripcion              = :descripcion,
					superada                 = :superada,
					breve                    = :breve";
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_situacion='$this->iid_situacion'")) === false) {
				$sClauError = 'Nota.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($oDblSt->execute($aDades) === false) {
					$sClauError = 'Nota.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
		} else {
			// INSERT
			array_unshift($aDades, $this->iid_situacion);
			$campos="(id_situacion,descripcion,superada,breve)";
			$valores="(:id_situacion,:descripcion,:superada,:breve)";		
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
				$sClauError = 'Nota.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($oDblSt->execute($aDades) === false) {
					$sClauError = 'Nota.insertar.execute';
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
		if (isset($this->iid_situacion)) {
			if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_situacion='$this->iid_situacion'")) === false) {
				$sClauError = 'Nota.carregar';
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
				default:					// En el caso de no existir esta fila, $aDades = FALSE:					if ($aDades === FALSE) {
						$this->setNullAllAtributes();					} else {						$this->setAllAtributes($aDades);					}			}
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
		if (($oDblSt = $oDbl->exec("DELETE FROM $nom_tabla WHERE id_situacion='$this->iid_situacion'")) === false) {
			$sClauError = 'Nota.eliminar';
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
		if (array_key_exists('id_situacion',$aDades)) $this->setId_situacion($aDades['id_situacion']);
		if (array_key_exists('descripcion',$aDades)) $this->setDescripcion($aDades['descripcion']);
		if (array_key_exists('superada',$aDades)) $this->setSuperada($aDades['superada']);
		if (array_key_exists('breve',$aDades)) $this->setBreve($aDades['breve']);
	}	/**	 * Estableix a empty el valor de tots els atributs	 *	 */	function setNullAllAtributes() {
		$this->setId_schema('');
		$this->setId_situacion('');
		$this->setDescripcion('');
		$this->setSuperada('');
		$this->setBreve('');
	}

	/* METODES GET i SET --------------------------------------------------------*/

	/**
	 * Recupera tots els atributs de Nota en un array
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
	 * Recupera las claus primàries de Nota en un array
	 *
	 * @return array aPrimary_key
	 */
	function getPrimary_key() {
		if (!isset($this->aPrimary_key )) {
			$this->aPrimary_key = array('iid_situacion' => $this->iid_situacion);
		}
		return $this->aPrimary_key;
	}

	/**
	 * Recupera l'atribut iid_situacion de Nota
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
	 * estableix el valor de l'atribut iid_situacion de Nota
	 *
	 * @param integer iid_situacion
	 */
	function setId_situacion($iid_situacion) {
		$this->iid_situacion = $iid_situacion;
	}
	/**
	 * Recupera l'atribut sdescripcion de Nota
	 *
	 * @return string sdescripcion
	 */
	function getDescripcion() {
		if (!isset($this->sdescripcion)) {
			$this->DBCarregar();
		}
		return $this->sdescripcion;
	}
	/**
	 * estableix el valor de l'atribut sdescripcion de Nota
	 *
	 * @param string sdescripcion='' optional
	 */
	function setDescripcion($sdescripcion='') {
		$this->sdescripcion = $sdescripcion;
	}
	/**
	 * Recupera l'atribut bsuperada de Nota
	 *
	 * @return boolean bsuperada
	 */
	function getSuperada() {
		if (!isset($this->bsuperada)) {
			$this->DBCarregar();
		}
		return $this->bsuperada;
	}
	/**
	 * estableix el valor de l'atribut bsuperada de Nota
	 *
	 * @param boolean bsuperada='f' optional
	 */
	function setSuperada($bsuperada='f') {
		$this->bsuperada = $bsuperada;
	}
	/**
	 * Recupera l'atribut sbreve de Nota
	 *
	 * @return string sbreve
	 */
	function getBreve() {
		if (!isset($this->sbreve)) {
			$this->DBCarregar();
		}
		return $this->sbreve;
	}
	/**
	 * estableix el valor de l'atribut sbreve de Nota
	 *
	 * @param string sbreve='' optional
	 */
	function setBreve($sbreve='') {
		$this->sbreve = $sbreve;
	}
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$oNotaSet = new core\Set();

		$oNotaSet->add($this->getDatosDescripcion());
		$oNotaSet->add($this->getDatosSuperada());
		$oNotaSet->add($this->getDatosBreve());
		return $oNotaSet->getTot();
	}



	/**
	 * Recupera les propietats de l'atribut sdescripcion de Nota
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosDescripcion() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'descripcion'));
		$oDatosCampo->setEtiqueta(_("descripción"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut bsuperada de Nota
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosSuperada() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'superada'));
		$oDatosCampo->setEtiqueta(_("superada"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut sbreve de Nota
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosBreve() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'breve'));
		$oDatosCampo->setEtiqueta(_("breve"));
		return $oDatosCampo;
	}
}
?>
