<?php
namespace usuarios\model\entity;
use core;

/**
 * Fitxer amb la Classe que accedeix a la taula $nom_tabla
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 16/11/2010
 */
/**
 * Classe que implementa l'entitat $nom_tabla
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 16/11/2010
 */
class Preferencia Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de Preferencia
	 *
	 * @var array
	 */
	 private $aPrimary_key;

	/**
	 * aDades de Preferencia
	 *
	 * @var array
	 */
	 private $aDades;

	/**
	 * bLoaded
	 *
	 * @var boolean
	 */
	 private $bLoaded = FALSE;

	/**
	 * Tipo de Preferencia
	 *
	 * @var string
	 */
	 private $stipo;
	/**
	 * Preferencia de Preferencia
	 *
	 * @var string
	 */
	 private $spreferencia;
	/**
	 * id_usuario de Preferencia
	 *
	 * @var integer
	 */
	 private $iid_usuario;

	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 * Si només necessita un valor, se li pot passar un integer.
	 * En general se li passa un array amb les claus primàries.
	 *
	 * @param integer|array iid_usuario,stipo
	 * 						$a_id. Un array con los nombres=>valores de las claves primarias.
	 */
	function __construct($a_id='') {
		$oDbl = $GLOBALS['oDBE'];
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				if ($nom_id=='id_usuario') $nom_id='i'.$nom_id;
				if ($nom_id=='tipo') $nom_id='s'.$nom_id;
				if ($val_id !== '') $this->$nom_id = $val_id;
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('web_preferencias');
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
		$aDades['preferencia'] = $this->spreferencia;
		array_walk($aDades, 'core\poner_null');

		if ($bInsert === false) {
			//UPDATE
			$update="
					preferencia              = :preferencia";
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_usuario='$this->iid_usuario' AND tipo='$this->stipo'")) === false) {
				$sClauError = 'Preferencia.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				try {
					$oDblSt->execute($aDades);
				}
				catch ( \PDOException $e) {
					$err_txt=$e->errorInfo[2];
					$this->setErrorTxt($err_txt);
					$sClauError = 'Preferencia.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
		} else {
			// INSERT
			array_unshift($aDades, $this->iid_usuario, $this->stipo);
			$campos="(id_usuario,tipo,preferencia)";
			$valores="(:id_usuario,:tipo,:preferencia)";		
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
				$sClauError = 'Preferencia.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				try {
					$oDblSt->execute($aDades);
				}
				catch ( \PDOException $e) {
					$err_txt=$e->errorInfo[2];
					$this->setErrorTxt($err_txt);
					$sClauError = 'Preferencia.insertar.execute';
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
		if (isset($this->iid_usuario) && isset($this->stipo)) {
			if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_usuario='$this->iid_usuario' AND tipo='$this->stipo'")) === false) {
				$sClauError = 'Preferencia.carregar';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			}
			$aDades = $oDblSt->fetch(\PDO::FETCH_ASSOC);
			// Para evitar posteriores cargas
			$this->bLoaded = TRUE;
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
		if (($oDblSt = $oDbl->exec("DELETE FROM $nom_tabla WHERE id_usuario='$this->iid_usuario' AND tipo='$this->stipo'")) === false) {
			$sClauError = 'Preferencia.eliminar';
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
		if (array_key_exists('id_usuario',$aDades)) $this->setId_usuario($aDades['id_usuario']);
		if (array_key_exists('tipo',$aDades)) $this->setTipo($aDades['tipo']);
		if (array_key_exists('preferencia',$aDades)) $this->setPreferencia($aDades['preferencia']);
	}

	/**
	 * Estableix a empty el valor de tots els atributs
	 *
	 */
	function setNullAllAtributes() {
		$this->setId_schema('');
		$this->setId_usuario('');
		$this->setTipo('');
		$this->setPreferencia('');
	}


	/* METODES GET i SET --------------------------------------------------------*/

	/**
	 * Recupera tots els atributs de Preferencia en un array
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
	 * Recupera las claus primàries de Preferencia en un array
	 *
	 * @return array aPrimary_key
	 */
	function getPrimary_key() {
		if (!isset($this->aPrimary_key )) {
			$this->aPrimary_key = array('id_usuario' => $this->iid_usuario,'tipo' => $this->stipo);
		}
		return $this->aPrimary_key;
	}
	
	/**
	 * Estableix las claus primàries de Preferencia en un array
	 *
	 * @return array aPrimary_key
	 */
	public function setPrimary_key($a_id='') {
	    if (is_array($a_id)) {
	        $this->aPrimary_key = $a_id;
	        foreach($a_id as $nom_id=>$val_id) {
	            if (($nom_id == 'id_usuario') && $val_id !== '') $this->iid_usuario = (int)$val_id; // evitem SQL injection fent cast a integer
	            if (($nom_id == 'tipo') && $val_id !== '') $this->stipo = $val_id;
	        }
	    }
	}
	
	/**
	 * Recupera l'atribut iid_usuario de Preferencia
	 *
	 * @return integer iid_usuario
	 */
	function getId_usuario() {
		if (!isset($this->iid_usuario) && !$this->bLoaded) {
			$this->DBCarregar();
		}
		return $this->iid_usuario;
	}
	/**
	 * estableix el valor de l'atribut iid_usuario de Preferencia
	 *
	 * @param integer iid_usuario
	 */
	function setId_usuario($iid_usuario) {
		$this->iid_usuario = $iid_usuario;
	}
	/**
	 * Recupera l'atribut stipo de Preferencia
	 *
	 * @return string stipo
	 */
	function getTipo() {
		if (!isset($this->stipo) && !$this->bLoaded) {
			$this->DBCarregar();
		}
		return $this->stipo;
	}
	/**
	 * estableix el valor de l'atribut stipo de Preferencia
	 *
	 * @param string stipo
	 */
	function setTipo($stipo) {
		$this->stipo = $stipo;
	}
	/**
	 * Recupera l'atribut spreferencia de Preferencia
	 *
	 * @return string spreferencia
	 */
	function getPreferencia() {
		if (!isset($this->spreferencia) && !$this->bLoaded) {
			$this->DBCarregar();
		}
		return $this->spreferencia;
	}
	/**
	 * estableix el valor de l'atribut spreferencia de Preferencia
	 *
	 * @param string spreferencia='' optional
	 */
	function setPreferencia($spreferencia='') {
		$this->spreferencia = $spreferencia;
	}
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$oPreferenciaSet = new core\Set();

		$oPreferenciaSet->add($this->getDatosPreferencia());
		return $oPreferenciaSet->getTot();
	}



	/**
	 * Recupera les propietats de l'atribut spreferencia de Preferencia
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosPreferencia() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'preferencia'));
		$oDatosCampo->setEtiqueta(_("preferencia"));
		return $oDatosCampo;
	}
}
?>
