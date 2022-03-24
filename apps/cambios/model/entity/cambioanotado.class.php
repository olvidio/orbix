<?php
namespace cambios\model\entity;
use core;
/**
 * Fitxer amb la Classe que accedeix a la taula av_cambios_anotados_dl
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 2/5/2019
 */
/**
 * Classe que implementa l'entitat av_cambios_anotados_dl
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
	 * bLoaded
	 *
	 * @var boolean
	 */
	 private $bLoaded = FALSE;

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
	 * Anotado de CambioAnotado
	 *
	 * @var boolean
	 */
	 private $banotado;
	/**
	 * Server de CambioAnotado
	 * 
	 * Añado este campo para distinguir desde que servidor (Madrid-Barcelona) se anota el cambio.
	 * Aunque no debería hacer falta porque la tabla esta sincronizada, cuando se generan avisos
	 * masivamente, tarda un tiempo a sincronizarse, y puede suceder que desde el otro servidor
	 * también se generen avisos, generando nuevos registros, que en su momento impedirán la 
	 * sincronización porque la clave es la misma ($iid_schema_cambio, $iid_item_cambio) Ahora se
	 * añade $server a la clave.
	 * 
	 * @var integer
	 */
	 private $iserver;
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
				$this->iid_item = (integer) $a_id; // evitem SQL injection fent cast a integer
				$this->aPrimary_key = array('id_item' => $this->iid_item);
			}
		}
		$this->setoDbl($oDbl);
		//$this->setNomTabla('av_cambios_anotados_dl');
	}

	/* METODES PUBLICS ----------------------------------------------------------*/
	
	/**
	 * Se añade esta funcion para cambiar de tabla. Si se tienen una instalación en
	 * la dmz, hay desfases en la sincronización de la tabla y ocasiona algunos problemas.
	 * Se tiene una tabla distinta para sv y sf.
	 *
	 * @param integer $server
	 */
	public function setTabla($ubicacion){
	    if ($ubicacion === 'sv' ) {
	        $this->setNomTabla('av_cambios_anotados_dl');
	    }
	    if ($ubicacion === 'sf' ) {
	        $this->setNomTabla('av_cambios_anotados_dl_sf');
	    }
	}
	
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
		$aDades['anotado'] = $this->banotado;
		$aDades['server'] = $this->iserver;
		array_walk($aDades, 'core\poner_null');
		//para el caso de los boolean FALSE, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
		if ( core\is_true($aDades['anotado']) ) { $aDades['anotado']='true'; } else { $aDades['anotado']='false'; }

		if ($bInsert === FALSE) {
			//UPDATE
			$update="
					id_schema_cambio         = :id_schema_cambio,
					id_item_cambio           = :id_item_cambio,
					anotado                  = :anotado,
					server                   = :server";
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_item='$this->iid_item'")) === FALSE) {
				$sClauError = 'CambioAnotado.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return FALSE;
			} else {
				try {
					$oDblSt->execute($aDades);
				}
				catch ( \PDOException $e) {
					$err_txt=$e->errorInfo[2];
					$this->setErrorTxt($err_txt);
					$sClauError = 'CambioAnotado.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return FALSE;
				}
			}
		} else {
			// INSERT
			$campos="(id_schema_cambio,id_item_cambio,anotado,server)";
			$valores="(:id_schema_cambio,:id_item_cambio,:anotado,:server)";		
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === FALSE) {
				$sClauError = 'CambioAnotado.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return FALSE;
			} else {
				try {
					$oDblSt->execute($aDades);
				}
				catch ( \PDOException $e) {
					$err_txt=$e->errorInfo[2];
					$this->setErrorTxt($err_txt);
					$sClauError = 'CambioAnotado.insertar.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return FALSE;
				}
			}

	        $nom_seq = $this->getNomTabla() . '_id_item_seq';
	        
			$this->id_item = $oDbl->lastInsertId($nom_seq);
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
		if (array_key_exists('anotado',$aDades)) $this->setAnotado($aDades['anotado']);
		if (array_key_exists('server',$aDades)) $this->setServer($aDades['server']);
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
		$this->setAnotado('');
		$this->setServer('');
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
		if (!isset($this->iid_item) && !$this->bLoaded) {
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
		if (!isset($this->iid_schema_cambio) && !$this->bLoaded) {
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
		if (!isset($this->iid_item_cambio) && !$this->bLoaded) {
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
	 * Recupera l'atribut banotado de CambioAnotado
	 *
	 * @return boolean banotado
	 */
	function getAnotado() {
		if (!isset($this->banotado) && !$this->bLoaded) {
			$this->DBCarregar();
		}
		return $this->banotado;
	}
	/**
	 * estableix el valor de l'atribut banotado de CambioAnotado
	 *
	 * @param boolean banotado='f' optional
	 */
	function setAnotado($banotado='f') {
		$this->banotado = $banotado;
	}
	/**
	 * Recupera l'atribut iserver de CambioAnotado
	 *
	 * @return integer iserver
	 */
	function getServer() {
		if (!isset($this->iserver) && !$this->bLoaded) {
			$this->DBCarregar();
		}
		return $this->iserver;
	}
	/**
	 * estableix el valor de l'atribut iserver de CambioAnotado
	 *
	 * @param integer iserver=1 optional
	 */
	function setServer($iserver=1) {
		$this->iserver = $iserver;
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
		$oCambioAnotadoSet->add($this->getDatosAnotado());
		$oCambioAnotadoSet->add($this->getDatosServer());
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
	 * Recupera les propietats de l'atribut banotado de CambioAnotado
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosAnotado() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'anotado'));
		$oDatosCampo->setEtiqueta(_("anotado"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut iserver de CambioAnotado
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosServer() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'server'));
		$oDatosCampo->setEtiqueta(_("servidor"));
		return $oDatosCampo;
	}
}
