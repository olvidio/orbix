<?php
namespace zonassacd\model\entity;
use core;
/**
 * Fitxer amb la Classe que accedeix a la taula zonas_grupos
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 01/03/2019
 */
/**
 * Classe que implementa l'entitat zonas_grupos
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 01/03/2019
 */
class ZonaGrupo Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de ZonaGrupo
	 *
	 * @var array
	 */
	 private $aPrimary_key;

	/**
	 * aDades de ZonaGrupo
	 *
	 * @var array
	 */
	 private $aDades;

	/**
	 * Id_grupo de ZonaGrupo
	 *
	 * @var integer
	 */
	 private $iid_grupo;
	/**
	 * Nombre_grupo de ZonaGrupo
	 *
	 * @var string
	 */
	 private $snombre_grupo;
	/**
	 * Orden de ZonaGrupo
	 *
	 * @var integer
	 */
	 private $iorden;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/**
	 * oDbl de ZonaGrupo
	 *
	 * @var object
	 */
	 protected $oDbl;
	/**
	 * NomTabla de ZonaGrupo
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
	 * @param integer|array iid_grupo
	 * 						$a_id. Un array con los nombres=>valores de las claves primarias.
	 */
	function __construct($a_id='') {
		$oDbl = $GLOBALS['oDBE'];
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				if (($nom_id == 'id_grupo') && $val_id !== '') $this->iid_grupo = (int)$val_id; // evitem SQL injection fent cast a integer
			}
		} else {
			if (isset($a_id) && $a_id !== '') {
				$this->iid_grupo = intval($a_id); // evitem SQL injection fent cast a integer
				$this->aPrimary_key = array('iid_grupo' => $this->iid_grupo);
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('zonas_grupos');
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
		$aDades['nombre_grupo'] = $this->snombre_grupo;
		$aDades['orden'] = $this->iorden;
		array_walk($aDades, 'core\poner_null');

		if ($bInsert === FALSE) {
			//UPDATE
			$update="
					nombre_grupo             = :nombre_grupo,
					orden                    = :orden";
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_grupo='$this->iid_grupo'")) === FALSE) {
				$sClauError = 'ZonaGrupo.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return FALSE;
			} else {
				if ($oDblSt->execute($aDades) === FALSE) {
					$sClauError = 'ZonaGrupo.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return FALSE;
				}
			}
		} else {
			// INSERT
			$campos="(nombre_grupo,orden)";
			$valores="(:nombre_grupo,:orden)";		
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === FALSE) {
				$sClauError = 'ZonaGrupo.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return FALSE;
			} else {
				if ($oDblSt->execute($aDades) === FALSE) {
					$sClauError = 'ZonaGrupo.insertar.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return FALSE;
				}
			}
			$this->id_grupo = $oDbl->lastInsertId('zonas_grupos_id_grupo_seq');
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
		if (isset($this->iid_grupo)) {
			if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_grupo='$this->iid_grupo'")) === FALSE) {
				$sClauError = 'ZonaGrupo.carregar';
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
		if (($oDbl->exec("DELETE FROM $nom_tabla WHERE id_grupo='$this->iid_grupo'")) === FALSE) {
			$sClauError = 'ZonaGrupo.eliminar';
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
		if (array_key_exists('id_grupo',$aDades)) $this->setId_grupo($aDades['id_grupo']);
		if (array_key_exists('nombre_grupo',$aDades)) $this->setNombre_grupo($aDades['nombre_grupo']);
		if (array_key_exists('orden',$aDades)) $this->setOrden($aDades['orden']);
	}

	/**
	 * Estableix a empty el valor de tots els atributs
	 *
	 */
	function setNullAllAtributes() {
		$aPK = $this->getPrimary_key();
		$this->setId_grupo('');
		$this->setNombre_grupo('');
		$this->setOrden('');
		$this->setPrimary_key($aPK);
	}


	/* METODES GET i SET --------------------------------------------------------*/

	/**
	 * Recupera tots els atributs de ZonaGrupo en un array
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
	 * Recupera las claus primàries de ZonaGrupo en un array
	 *
	 * @return array aPrimary_key
	 */
	function getPrimary_key() {
		if (!isset($this->aPrimary_key )) {
			$this->aPrimary_key = array('id_grupo' => $this->iid_grupo);
		}
		return $this->aPrimary_key;
	}

	/**
	 * Recupera l'atribut iid_grupo de ZonaGrupo
	 *
	 * @return integer iid_grupo
	 */
	function getId_grupo() {
		if (!isset($this->iid_grupo)) {
			$this->DBCarregar();
		}
		return $this->iid_grupo;
	}
	/**
	 * estableix el valor de l'atribut iid_grupo de ZonaGrupo
	 *
	 * @param integer iid_grupo
	 */
	function setId_grupo($iid_grupo) {
		$this->iid_grupo = $iid_grupo;
	}
	/**
	 * Recupera l'atribut snombre_grupo de ZonaGrupo
	 *
	 * @return string snombre_grupo
	 */
	function getNombre_grupo() {
		if (!isset($this->snombre_grupo)) {
			$this->DBCarregar();
		}
		return $this->snombre_grupo;
	}
	/**
	 * estableix el valor de l'atribut snombre_grupo de ZonaGrupo
	 *
	 * @param string snombre_grupo='' optional
	 */
	function setNombre_grupo($snombre_grupo='') {
		$this->snombre_grupo = $snombre_grupo;
	}
	/**
	 * Recupera l'atribut iorden de ZonaGrupo
	 *
	 * @return integer iorden
	 */
	function getOrden() {
		if (!isset($this->iorden)) {
			$this->DBCarregar();
		}
		return $this->iorden;
	}
	/**
	 * estableix el valor de l'atribut iorden de ZonaGrupo
	 *
	 * @param integer iorden='' optional
	 */
	function setOrden($iorden='') {
		$this->iorden = $iorden;
	}
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$oZonaGrupoSet = new core\Set();

		$oZonaGrupoSet->add($this->getDatosNombre_grupo());
		$oZonaGrupoSet->add($this->getDatosOrden());
		return $oZonaGrupoSet->getTot();
	}



	/**
	 * Recupera les propietats de l'atribut snombre_grupo de ZonaGrupo
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosNombre_grupo() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'nombre_grupo'));
		$oDatosCampo->setEtiqueta(_("nombre del grupo"));
		$oDatosCampo->setTipo('texto');
		$oDatosCampo->setArgument('30');
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut iorden de ZonaGrupo
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosOrden() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'orden'));
		$oDatosCampo->setEtiqueta(_("orden"));
		$oDatosCampo->setTipo('texto');
		$oDatosCampo->setArgument('5');
		return $oDatosCampo;
	}
}
