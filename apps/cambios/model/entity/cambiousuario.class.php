<?php
namespace cambios\model\entity;
use core;
/**
 * Fitxer amb la Classe que accedeix a la taula av_cambios_usuario
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 17/4/2019
 */
/**
 * Classe que implementa l'entitat av_cambios_usuario
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 17/4/2019
 */
class CambioUsuario Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de CambioUsuario
	 *
	 * @var array
	 */
	 private $aPrimary_key;

	/**
	 * aDades de CambioUsuario
	 *
	 * @var array
	 */
	 private $aDades;

	/**
	 * Id_item de CambioUsuario
	 *
	 * @var integer
	 */
	 private $iid_item;
	/**
	 * Id_schema_cambio de CambioUsuario
	 *
	 * @var integer
	 */
	 private $iid_schema_cambio;
	/**
	 * Id_item_cambio de CambioUsuario
	 *
	 * @var integer
	 */
	 private $iid_item_cambio;
	/**
	 * Id_usuario de CambioUsuario
	 *
	 * @var integer
	 */
	 private $iid_usuario;
	/**
	 * Aviso_tipo de CambioUsuario
	 *
	 * @var integer
	 */
	 private $iaviso_tipo;
	/**
	 * Aviso_donde de CambioUsuario
	 *
	 * @var string
	 */
	 private $saviso_donde;
	/**
	 * Avisado de CambioUsuario
	 *
	 * @var boolean
	 */
	 private $bavisado;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/**
	 * oDbl de CambioUsuario
	 *
	 * @var object
	 */
	 protected $oDbl;
	/**
	 * NomTabla de CambioUsuario
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
				$this->aPrimary_key = array('iid_item' => $this->iid_item);
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('av_cambios_usuario');
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
		$aDades['id_usuario'] = $this->iid_usuario;
		$aDades['aviso_tipo'] = $this->iaviso_tipo;
		$aDades['aviso_donde'] = $this->saviso_donde;
		$aDades['avisado'] = $this->bavisado;
		array_walk($aDades, 'core\poner_null');
		//para el caso de los boolean FALSE, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
		$aDades['avisado'] = ($aDades['avisado'] === 't')? 'true' : $aDades['avisado'];
		if ( filter_var( $aDades['avisado'], FILTER_VALIDATE_BOOLEAN)) { $aDades['avisado']='t'; } else { $aDades['avisado']='f'; }

		if ($bInsert === FALSE) {
			//UPDATE
			$update="
					id_schema_cambio         = :id_schema_cambio,
					id_item_cambio           = :id_item_cambio,
					id_usuario               = :id_usuario,
					aviso_tipo               = :aviso_tipo,
					aviso_donde              = :aviso_donde,
					avisado                  = :avisado";
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_item='$this->iid_item'")) === FALSE) {
				$sClauError = 'CambioUsuario.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return FALSE;
			} else {
				if ($oDblSt->execute($aDades) === FALSE) {
					$sClauError = 'CambioUsuario.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return FALSE;
				}
			}
		} else {
			// INSERT
			$campos="(id_schema_cambio,id_item_cambio,id_usuario,aviso_tipo,aviso_donde,avisado)";
			$valores="(:id_schema_cambio,:id_item_cambio,:id_usuario,:aviso_tipo,:aviso_donde,:avisado)";		
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === FALSE) {
				$sClauError = 'CambioUsuario.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return FALSE;
			} else {
				if ($oDblSt->execute($aDades) === FALSE) {
					$sClauError = 'CambioUsuario.insertar.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return FALSE;
				}
			}
			$this->id_item = $oDbl->lastInsertId('av_cambios_usuario_id_item_seq');
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
				$sClauError = 'CambioUsuario.carregar';
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
		if (($oDbl->exec("DELETE FROM $nom_tabla WHERE id_item='$this->iid_item'")) === FALSE) {
			$sClauError = 'CambioUsuario.eliminar';
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
		if (array_key_exists('id_usuario',$aDades)) $this->setId_usuario($aDades['id_usuario']);
		if (array_key_exists('aviso_tipo',$aDades)) $this->setAviso_tipo($aDades['aviso_tipo']);
		if (array_key_exists('aviso_donde',$aDades)) $this->setAviso_donde($aDades['aviso_donde']);
		if (array_key_exists('avisado',$aDades)) $this->setAvisado($aDades['avisado']);
	}

	/* METODES GET i SET --------------------------------------------------------*/

	/**
	 * Recupera tots els atributs de CambioUsuario en un array
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
	 * Recupera las claus primàries de CambioUsuario en un array
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
	 * Recupera l'atribut iid_item de CambioUsuario
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
	 * estableix el valor de l'atribut iid_item de CambioUsuario
	 *
	 * @param integer iid_item
	 */
	function setId_item($iid_item) {
		$this->iid_item = $iid_item;
	}
	/**
	 * Recupera l'atribut iid_schema_cambio de CambioUsuario
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
	 * estableix el valor de l'atribut iid_schema_cambio de CambioUsuario
	 *
	 * @param integer iid_schema_cambio='' optional
	 */
	function setId_schema_cambio($iid_schema_cambio='') {
		$this->iid_schema_cambio = $iid_schema_cambio;
	}
	/**
	 * Recupera l'atribut iid_item_cambio de CambioUsuario
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
	 * estableix el valor de l'atribut iid_item_cambio de CambioUsuario
	 *
	 * @param integer iid_item_cambio='' optional
	 */
	function setId_item_cambio($iid_item_cambio='') {
		$this->iid_item_cambio = $iid_item_cambio;
	}
	/**
	 * Recupera l'atribut iid_usuario de CambioUsuario
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
	 * estableix el valor de l'atribut iid_usuario de CambioUsuario
	 *
	 * @param integer iid_usuario='' optional
	 */
	function setId_usuario($iid_usuario='') {
		$this->iid_usuario = $iid_usuario;
	}
	/**
	 * Recupera l'atribut iaviso_tipo de CambioUsuario
	 *
	 * @return integer iaviso_tipo
	 */
	function getAviso_tipo() {
		if (!isset($this->iaviso_tipo)) {
			$this->DBCarregar();
		}
		return $this->iaviso_tipo;
	}
	/**
	 * estableix el valor de l'atribut iaviso_tipo de CambioUsuario
	 *
	 * @param integer iaviso_tipo='' optional
	 */
	function setAviso_tipo($iaviso_tipo='') {
		$this->iaviso_tipo = $iaviso_tipo;
	}
	/**
	 * Recupera l'atribut saviso_donde de CambioUsuario
	 *
	 * @return string saviso_donde
	 */
	function getAviso_donde() {
		if (!isset($this->saviso_donde)) {
			$this->DBCarregar();
		}
		return $this->saviso_donde;
	}
	/**
	 * estableix el valor de l'atribut saviso_donde de CambioUsuario
	 *
	 * @param string saviso_donde='' optional
	 */
	function setAviso_donde($saviso_donde='') {
		$this->saviso_donde = $saviso_donde;
	}
	/**
	 * Recupera l'atribut bavisado de CambioUsuario
	 *
	 * @return boolean bavisado
	 */
	function getAvisado() {
		if (!isset($this->bavisado)) {
			$this->DBCarregar();
		}
		return $this->bavisado;
	}
	/**
	 * estableix el valor de l'atribut bavisado de CambioUsuario
	 *
	 * @param boolean bavisado='f' optional
	 */
	function setAvisado($bavisado='f') {
		$this->bavisado = $bavisado;
	}
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$oCambioUsuarioSet = new core\Set();

		$oCambioUsuarioSet->add($this->getDatosId_item_cambio());
		$oCambioUsuarioSet->add($this->getDatosId_schema_cambio());
		$oCambioUsuarioSet->add($this->getDatosId_usuario());
		$oCambioUsuarioSet->add($this->getDatosAviso_tipo());
		$oCambioUsuarioSet->add($this->getDatosAviso_donde());
		$oCambioUsuarioSet->add($this->getDatosAvisado());
		return $oCambioUsuarioSet->getTot();
	}



	/**
	 * Recupera les propietats de l'atribut iid_schema_cambio de CambioUsuario
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
	 * Recupera les propietats de l'atribut iid_item_cambio de CambioUsuario
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
	 * Recupera les propietats de l'atribut iid_usuario de CambioUsuario
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
	 * Recupera les propietats de l'atribut iaviso_tipo de CambioUsuario
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosAviso_tipo() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'aviso_tipo'));
		$oDatosCampo->setEtiqueta(_("aviso_tipo"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut saviso_donde de CambioUsuario
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosAviso_donde() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'aviso_donde'));
		$oDatosCampo->setEtiqueta(_("aviso_donde"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut bavisado de CambioUsuario
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosAvisado() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'avisado'));
		$oDatosCampo->setEtiqueta(_("avisado"));
		return $oDatosCampo;
	}
}