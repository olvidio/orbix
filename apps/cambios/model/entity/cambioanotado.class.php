<?php
namespace cambios\model\entity;
use core;
/**
 * Fitxer amb la Classe que accedeix a la taula av_cambios_anotados
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 2/5/2019
 */
/**
 * Classe que implementa l'entitat av_cambios_anotados
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 2/5/2019
 */
class CambioAnotado Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de CambioAnotado
	 *
	 * @var array
	 */
	 private $aPrimary_key;

	/**
	 * aDades de CambioAnotado
	 *
	 * @var array
	 */
	 private $aDades;

	/**
	 * Id_item de CambioAnotado
	 *
	 * @var integer
	 */
	 private $iid_item;
	/**
	 * Id_schema_cambio de CambioAnotado
	 *
	 * @var integer
	 */
	 private $iid_schema_cambio;
	/**
	 * Id_item_cambio de CambioAnotado
	 *
	 * @var integer
	 */
	 private $iid_item_cambio;
	/**
	 * Anotado_sv de CambioAnotado
	 *
	 * @var boolean
	 */
	 private $banotado_sv;
	/**
	 * Anotado_sf de CambioAnotado
	 *
	 * @var boolean
	 */
	 private $banotado_sf;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/**
	 * oDbl de CambioAnotado
	 *
	 * @var object
	 */
	 protected $oDbl;
	/**
	 * NomTabla de CambioAnotado
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
		$oDbl = $GLOBALS['oDBC'];
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
		$this->setNomTabla('av_cambios_anotados');
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
		$aDades['id_schema_cambio'] = $this->iid_schema_cambio;
		$aDades['id_item_cambio'] = $this->iid_item_cambio;
		$aDades['anotado_sv'] = $this->banotado_sv;
		$aDades['anotado_sf'] = $this->banotado_sf;
		array_walk($aDades, 'core\poner_null');
		//para el caso de los boolean FALSE, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
		$aDades['anotado_sv'] = ($aDades['anotado_sv'] === 't')? 'true' : $aDades['anotado_sv'];
		if ( filter_var( $aDades['anotado_sv'], FILTER_VALIDATE_BOOLEAN)) { $aDades['anotado_sv']='t'; } else { $aDades['anotado_sv']='f'; }
		$aDades['anotado_sf'] = ($aDades['anotado_sf'] === 't')? 'true' : $aDades['anotado_sf'];
		if ( filter_var( $aDades['anotado_sf'], FILTER_VALIDATE_BOOLEAN)) { $aDades['anotado_sf']='t'; } else { $aDades['anotado_sf']='f'; }

		if ($bInsert === FALSE) {
			//UPDATE
			$update="
					id_schema_cambio         = :id_schema_cambio,
					id_item_cambio           = :id_item_cambio,
					anotado_sv               = :anotado_sv,
					anotado_sf               = :anotado_sf";
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_item='$this->iid_item'")) === FALSE) {
				$sClauError = 'CambioAnotado.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return FALSE;
			} else {
				if ($oDblSt->execute($aDades) === FALSE) {
					$sClauError = 'CambioAnotado.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return FALSE;
				}
			}
		} else {
			// INSERT
			$campos="(id_schema_cambio,id_item_cambio,anotado_sv,anotado_sf)";
			$valores="(:id_schema_cambio,:id_item_cambio,:anotado_sv,:anotado_sf)";		
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === FALSE) {
				$sClauError = 'CambioAnotado.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return FALSE;
			} else {
				if ($oDblSt->execute($aDades) === FALSE) {
					$sClauError = 'CambioAnotado.insertar.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return FALSE;
				}
			}
			$this->id_item = $oDbl->lastInsertId('av_cambios_anotados_id_item_seq');
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
				$sClauError = 'CambioAnotado.carregar';
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
			$sClauError = 'CambioAnotado.eliminar';
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
		if (array_key_exists('id_schema_cambio',$aDades)) $this->setId_schema_cambio($aDades['id_schema_cambio']);
		if (array_key_exists('id_item_cambio',$aDades)) $this->setId_item_cambio($aDades['id_item_cambio']);
		if (array_key_exists('anotado_sv',$aDades)) $this->setAnotado_sv($aDades['anotado_sv']);
		if (array_key_exists('anotado_sf',$aDades)) $this->setAnotado_sf($aDades['anotado_sf']);
	}

	/**
	 * Estableix a empty el valor de tots els atributs
	 *
	 */
	function setNullAllAtributes() {
		$aPK = $this->getPrimary_key();
		$this->setId_item('');
		$this->setId_schema_cambio('');
		$this->setId_item_cambio('');
		$this->setAnotado_sv('');
		$this->setAnotado_sf('');
		$this->setPrimary_key($aPK);
	}


	/* METODES GET i SET --------------------------------------------------------*/

	/**
	 * Recupera tots els atributs de CambioAnotado en un array
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
	 * Recupera las claus primàries de CambioAnotado en un array
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
	 * Estableix las claus primàries de CambioAnotado en un array
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
	 * Recupera l'atribut iid_item de CambioAnotado
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
	 * estableix el valor de l'atribut iid_item de CambioAnotado
	 *
	 * @param integer iid_item
	 */
	function setId_item($iid_item) {
		$this->iid_item = $iid_item;
	}
	/**
	 * Recupera l'atribut iid_schema_cambio de CambioAnotado
	 *
	 * @return integer iid_schema_cambio
	 */
	function getId_schema_cambio() {
		if (!isset($this->iid_schema_cambio)) {
			$this->DBCarregar();
		}
		return $this->iid_schema_cambio;
	}
	/**
	 * estableix el valor de l'atribut iid_schema_cambio de CambioAnotado
	 *
	 * @param integer iid_schema_cambio='' optional
	 */
	function setId_schema_cambio($iid_schema_cambio='') {
		$this->iid_schema_cambio = $iid_schema_cambio;
	}
	/**
	 * Recupera l'atribut iid_item_cambio de CambioAnotado
	 *
	 * @return integer iid_item_cambio
	 */
	function getId_item_cambio() {
		if (!isset($this->iid_item_cambio)) {
			$this->DBCarregar();
		}
		return $this->iid_item_cambio;
	}
	/**
	 * estableix el valor de l'atribut iid_item_cambio de CambioAnotado
	 *
	 * @param integer iid_item_cambio='' optional
	 */
	function setId_item_cambio($iid_item_cambio='') {
		$this->iid_item_cambio = $iid_item_cambio;
	}
	/**
	 * Recupera l'atribut banotado_sv de CambioAnotado
	 *
	 * @return boolean banotado_sv
	 */
	function getAnotado_sv() {
		if (!isset($this->banotado_sv)) {
			$this->DBCarregar();
		}
		return $this->banotado_sv;
	}
	/**
	 * estableix el valor de l'atribut banotado_sv de CambioAnotado
	 *
	 * @param boolean banotado_sv='f' optional
	 */
	function setAnotado_sv($banotado_sv='f') {
		$this->banotado_sv = $banotado_sv;
	}
	/**
	 * Recupera l'atribut banotado_sf de CambioAnotado
	 *
	 * @return boolean banotado_sf
	 */
	function getAnotado_sf() {
		if (!isset($this->banotado_sf)) {
			$this->DBCarregar();
		}
		return $this->banotado_sf;
	}
	/**
	 * estableix el valor de l'atribut banotado_sf de CambioAnotado
	 *
	 * @param boolean banotado_sf='f' optional
	 */
	function setAnotado_sf($banotado_sf='f') {
		$this->banotado_sf = $banotado_sf;
	}
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$oCambioAnotadoSet = new core\Set();

		$oCambioAnotadoSet->add($this->getDatosId_schema_cambio());
		$oCambioAnotadoSet->add($this->getDatosId_item_cambio());
		$oCambioAnotadoSet->add($this->getDatosAnotado_sv());
		$oCambioAnotadoSet->add($this->getDatosAnotado_sf());
		return $oCambioAnotadoSet->getTot();
	}



	/**
	 * Recupera les propietats de l'atribut iid_schema_cambio de CambioAnotado
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosId_schema_cambio() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_schema_cambio'));
		$oDatosCampo->setEtiqueta(_("id_schema_cambio"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut iid_item_cambio de CambioAnotado
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosId_item_cambio() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_item_cambio'));
		$oDatosCampo->setEtiqueta(_("id_item_cambio"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut banotado_sv de CambioAnotado
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosAnotado_sv() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'anotado_sv'));
		$oDatosCampo->setEtiqueta(_("anotado sv"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut banotado_sf de CambioAnotado
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosAnotado_sf() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'anotado_sf'));
		$oDatosCampo->setEtiqueta(_("anotado sf"));
		return $oDatosCampo;
	}
}
