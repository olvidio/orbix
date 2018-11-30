<?php
namespace actividades\model\entity;
use core;
/**
 * Fitxer amb la Classe que accedeix a la taula a_tipos_actividad
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 09/11/2018
 */
/**
 * Classe que implementa l'entitat a_tipos_actividad
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 09/11/2018
 */
class TipoDeActividad Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de TipoDeActividad
	 *
	 * @var array
	 */
	 private $aPrimary_key;

	/**
	 * aDades de TipoDeActividad
	 *
	 * @var array
	 */
	 private $aDades;

	/**
	 * Id_schema de TipoDeActividad
	 *
	 * @var integer
	 */
	 private $iid_schema;
	/**
	 * Id_tipo_activ de TipoDeActividad
	 *
	 * @var integer
	 */
	 private $iid_tipo_activ;
	/**
	 * Nombre de TipoDeActividad
	 *
	 * @var string
	 */
	 private $snombre;
	/**
	 * Id_tipo_proceso de TipoDeActividad
	 *
	 * @var integer
	 */
	 private $iid_tipo_proceso;
	/**
	 * Id_tipo_proceso_ex de TipoDeActividad
	 *
	 * @var integer
	 */
	 private $iid_tipo_proceso_ex;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/**
	 * oDbl de TipoDeActividad
	 *
	 * @var object
	 */
	 protected $oDbl;
	/**
	 * NomTabla de TipoDeActividad
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
	 * @param integer|array iid_tipo_activ
	 * 						$a_id. Un array con los nombres=>valores de las claves primarias.
	 */
	function __construct($a_id='') {
		$oDbl = $GLOBALS['oDBC'];
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				if (($nom_id == 'id_tipo_activ') && $val_id !== '') $this->iid_tipo_activ = (int)$val_id; // evitem SQL injection fent cast a integer
			}	} else {
			if (isset($a_id) && $a_id !== '') {
				$this->iid_tipo_activ = intval($a_id); // evitem SQL injection fent cast a integer
				$this->aPrimary_key = array('iid_tipo_activ' => $this->iid_tipo_activ);
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('a_tipos_actividad');
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
		$aDades['id_schema'] = $this->iid_schema;
		$aDades['nombre'] = $this->snombre;
		$aDades['id_tipo_proceso'] = $this->iid_tipo_proceso;
		$aDades['id_tipo_proceso_ex'] = $this->iid_tipo_proceso_ex;
		array_walk($aDades, 'core\poner_null');

		if ($bInsert === false) {
			//UPDATE
			$update="
					id_schema                = :id_schema,
					nombre                   = :nombre,
					id_tipo_proceso          = :id_tipo_proceso,
					id_tipo_proceso_ex       = :id_tipo_proceso_ex";
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_tipo_activ='$this->iid_tipo_activ'")) === false) {
				$sClauError = 'TipoDeActividad.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($oDblSt->execute($aDades) === false) {
					$sClauError = 'TipoDeActividad.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
		} else {
			// INSERT
			array_unshift($aDades, $this->iid_tipo_activ);
			$campos="(id_schema,id_tipo_activ,nombre,id_tipo_proceso,id_tipo_proceso_ex)";
			$valores="(:id_schema,:id_tipo_activ,:nombre,:id_tipo_proceso,:id_tipo_proceso_ex)";		
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
				$sClauError = 'TipoDeActividad.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($oDblSt->execute($aDades) === false) {
					$sClauError = 'TipoDeActividad.insertar.execute';
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
		if (isset($this->iid_tipo_activ)) {
			if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_tipo_activ='$this->iid_tipo_activ'")) === false) {
				$sClauError = 'TipoDeActividad.carregar';
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
		if (($oDbl->exec("DELETE FROM $nom_tabla WHERE id_tipo_activ='$this->iid_tipo_activ'")) === false) {
			$sClauError = 'TipoDeActividad.eliminar';
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
		if (array_key_exists('id_tipo_activ',$aDades)) $this->setId_tipo_activ($aDades['id_tipo_activ']);
		if (array_key_exists('nombre',$aDades)) $this->setNombre($aDades['nombre']);
		if (array_key_exists('id_tipo_proceso',$aDades)) $this->setId_tipo_proceso($aDades['id_tipo_proceso']);
		if (array_key_exists('id_tipo_proceso_ex',$aDades)) $this->setId_tipo_proceso_ex($aDades['id_tipo_proceso_ex']);
	}

	/* METODES GET i SET --------------------------------------------------------*/

	/**
	 * Recupera tots els atributs de TipoDeActividad en un array
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
	 * Recupera las claus primàries de TipoDeActividad en un array
	 *
	 * @return array aPrimary_key
	 */
	function getPrimary_key() {
		if (!isset($this->aPrimary_key )) {
			$this->aPrimary_key = array('id_tipo_activ' => $this->iid_tipo_activ);
		}
		return $this->aPrimary_key;
	}

	/**
	 * Recupera l'atribut iid_schema de TipoDeActividad
	 *
	 * @return integer iid_schema
	 */
	function getId_schema() {
		if (!isset($this->iid_schema)) {
			$this->DBCarregar();
		}
		return $this->iid_schema;
	}
	/**
	 * estableix el valor de l'atribut iid_schema de TipoDeActividad
	 *
	 * @param integer iid_schema='' optional
	 */
	function setId_schema($iid_schema='') {
		$this->iid_schema = $iid_schema;
	}
	/**
	 * Recupera l'atribut iid_tipo_activ de TipoDeActividad
	 *
	 * @return integer iid_tipo_activ
	 */
	function getId_tipo_activ() {
		if (!isset($this->iid_tipo_activ)) {
			$this->DBCarregar();
		}
		return $this->iid_tipo_activ;
	}
	/**
	 * estableix el valor de l'atribut iid_tipo_activ de TipoDeActividad
	 *
	 * @param integer iid_tipo_activ
	 */
	function setId_tipo_activ($iid_tipo_activ) {
		$this->iid_tipo_activ = $iid_tipo_activ;
	}
	/**
	 * Recupera l'atribut snombre de TipoDeActividad
	 *
	 * @return string snombre
	 */
	function getNombre() {
		if (!isset($this->snombre)) {
			$this->DBCarregar();
		}
		return $this->snombre;
	}
	/**
	 * estableix el valor de l'atribut snombre de TipoDeActividad
	 *
	 * @param string snombre='' optional
	 */
	function setNombre($snombre='') {
		$this->snombre = $snombre;
	}
	/**
	 * Recupera l'atribut iid_tipo_proceso de TipoDeActividad
	 *
	 * @return integer iid_tipo_proceso
	 */
	function getId_tipo_proceso() {
		if (!isset($this->iid_tipo_proceso)) {
			$this->DBCarregar();
		}
		return $this->iid_tipo_proceso;
	}
	/**
	 * estableix el valor de l'atribut iid_tipo_proceso de TipoDeActividad
	 *
	 * @param integer iid_tipo_proceso='' optional
	 */
	function setId_tipo_proceso($iid_tipo_proceso='') {
		$this->iid_tipo_proceso = $iid_tipo_proceso;
	}
	/**
	 * Recupera l'atribut iid_tipo_proceso_ex de TipoDeActividad
	 *
	 * @return integer iid_tipo_proceso_ex
	 */
	function getId_tipo_proceso_ex() {
		if (!isset($this->iid_tipo_proceso_ex)) {
			$this->DBCarregar();
		}
		return $this->iid_tipo_proceso_ex;
	}
	/**
	 * estableix el valor de l'atribut iid_tipo_proceso_ex de TipoDeActividad
	 *
	 * @param integer iid_tipo_proceso_ex='' optional
	 */
	function setId_tipo_proceso_ex($iid_tipo_proceso_ex='') {
		$this->iid_tipo_proceso_ex = $iid_tipo_proceso_ex;
	}
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$oTipoDeActividadSet = new core\Set();

		$oTipoDeActividadSet->add($this->getDatosId_schema());
		$oTipoDeActividadSet->add($this->getDatosNombre());
		$oTipoDeActividadSet->add($this->getDatosId_tipo_proceso());
		$oTipoDeActividadSet->add($this->getDatosId_tipo_proceso_ex());
		return $oTipoDeActividadSet->getTot();
	}



	/**
	 * Recupera les propietats de l'atribut iid_schema de TipoDeActividad
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosId_schema() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_schema'));
		$oDatosCampo->setEtiqueta(_("id_schema"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut snombre de TipoDeActividad
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosNombre() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'nombre'));
		$oDatosCampo->setEtiqueta(_("nombre"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut iid_tipo_proceso de TipoDeActividad
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosId_tipo_proceso() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_tipo_proceso'));
		$oDatosCampo->setEtiqueta(_("id_tipo_proceso"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut iid_tipo_proceso_ex de TipoDeActividad
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosId_tipo_proceso_ex() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_tipo_proceso_ex'));
		$oDatosCampo->setEtiqueta(_("id_tipo_proceso_ex"));
		return $oDatosCampo;
	}
}
