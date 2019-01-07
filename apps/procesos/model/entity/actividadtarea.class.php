<?php
namespace procesos\model\entity;
use core;
/**
 * Fitxer amb la Classe que accedeix a la taula a_tareas
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 07/12/2018
 */
/**
 * Classe que implementa l'entitat a_tareas
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 07/12/2018
 */
class ActividadTarea Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de ActividadTarea
	 *
	 * @var array
	 */
	 private $aPrimary_key;

	/**
	 * aDades de ActividadTarea
	 *
	 * @var array
	 */
	 private $aDades;

	/**
	 * Id_fase de ActividadTarea
	 *
	 * @var integer
	 */
	 private $iid_fase;
	/**
	 * Id_tarea de ActividadTarea
	 *
	 * @var integer
	 */
	 private $iid_tarea;
	/**
	 * Desc_tarea de ActividadTarea
	 *
	 * @var string
	 */
	 private $sdesc_tarea;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/**
	 * oDbl de ActividadTarea
	 *
	 * @var object
	 */
	 protected $oDbl;
	/**
	 * NomTabla de ActividadTarea
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
	 * @param integer|array iid_tarea
	 * 						$a_id. Un array con los nombres=>valores de las claves primarias.
	 */
	function __construct($a_id='') {
		$oDbl = $GLOBALS['oDBC'];
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				if (($nom_id == 'id_tarea') && $val_id !== '') $this->iid_tarea = (int)$val_id; // evitem SQL injection fent cast a integer
			}	} else {
			if (isset($a_id) && $a_id !== '') {
				$this->iid_tarea = intval($a_id); // evitem SQL injection fent cast a integer
				$this->aPrimary_key = array('iid_tarea' => $this->iid_tarea);
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('a_tareas');
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
		$aDades['id_fase'] = $this->iid_fase;
		$aDades['desc_tarea'] = $this->sdesc_tarea;
		array_walk($aDades, 'core\poner_null');

		if ($bInsert === FALSE) {
			//UPDATE
			$update="
					id_fase                  = :id_fase,
					desc_tarea               = :desc_tarea";
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_tarea='$this->iid_tarea'")) === FALSE) {
				$sClauError = 'ActividadTarea.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return FALSE;
			} else {
				if ($oDblSt->execute($aDades) === FALSE) {
					$sClauError = 'ActividadTarea.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return FALSE;
				}
			}
		} else {
			// INSERT
			$campos="(id_fase,desc_tarea)";
			$valores="(:id_fase,:desc_tarea)";		
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === FALSE) {
				$sClauError = 'ActividadTarea.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return FALSE;
			} else {
				if ($oDblSt->execute($aDades) === FALSE) {
					$sClauError = 'ActividadTarea.insertar.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return FALSE;
				}
			}
			$this->id_tarea = $oDbl->lastInsertId('a_tareas_id_tarea_seq');
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
		if (isset($this->iid_tarea)) {
			if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_tarea='$this->iid_tarea'")) === FALSE) {
				$sClauError = 'ActividadTarea.carregar';
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
		if (($oDbl->exec("DELETE FROM $nom_tabla WHERE id_tarea='$this->iid_tarea'")) === FALSE) {
			$sClauError = 'ActividadTarea.eliminar';
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
		if (array_key_exists('id_fase',$aDades)) $this->setId_fase($aDades['id_fase']);
		if (array_key_exists('id_tarea',$aDades)) $this->setId_tarea($aDades['id_tarea']);
		if (array_key_exists('desc_tarea',$aDades)) $this->setDesc_tarea($aDades['desc_tarea']);
	}

	/* METODES GET i SET --------------------------------------------------------*/

	/**
	 * Recupera tots els atributs de ActividadTarea en un array
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
	 * Recupera las claus primàries de ActividadTarea en un array
	 *
	 * @return array aPrimary_key
	 */
	function getPrimary_key() {
		if (!isset($this->aPrimary_key )) {
			$this->aPrimary_key = array('id_tarea' => $this->iid_tarea);
		}
		return $this->aPrimary_key;
	}

	/**
	 * Recupera l'atribut iid_fase de ActividadTarea
	 *
	 * @return integer iid_fase
	 */
	function getId_fase() {
		if (!isset($this->iid_fase)) {
			$this->DBCarregar();
		}
		return $this->iid_fase;
	}
	/**
	 * estableix el valor de l'atribut iid_fase de ActividadTarea
	 *
	 * @param integer iid_fase='' optional
	 */
	function setId_fase($iid_fase='') {
		$this->iid_fase = $iid_fase;
	}
	/**
	 * Recupera l'atribut iid_tarea de ActividadTarea
	 *
	 * @return integer iid_tarea
	 */
	function getId_tarea() {
		if (!isset($this->iid_tarea)) {
			$this->DBCarregar();
		}
		return $this->iid_tarea;
	}
	/**
	 * estableix el valor de l'atribut iid_tarea de ActividadTarea
	 *
	 * @param integer iid_tarea
	 */
	function setId_tarea($iid_tarea) {
		$this->iid_tarea = $iid_tarea;
	}
	/**
	 * Recupera l'atribut sdesc_tarea de ActividadTarea
	 *
	 * @return string sdesc_tarea
	 */
	function getDesc_tarea() {
		if (!isset($this->sdesc_tarea)) {
			$this->DBCarregar();
		}
		return $this->sdesc_tarea;
	}
	/**
	 * estableix el valor de l'atribut sdesc_tarea de ActividadTarea
	 *
	 * @param string sdesc_tarea='' optional
	 */
	function setDesc_tarea($sdesc_tarea='') {
		$this->sdesc_tarea = $sdesc_tarea;
	}
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$oActividadTareaSet = new core\Set();

		$oActividadTareaSet->add($this->getDatosId_fase());
		$oActividadTareaSet->add($this->getDatosDesc_tarea());
		return $oActividadTareaSet->getTot();
	}



	/**
	 * Recupera les propietats de l'atribut iid_fase de ActividadTarea
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosId_fase() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_fase'));
		$oDatosCampo->setEtiqueta(_("fase"));
		$oDatosCampo->setTipo('opciones');
		$oDatosCampo->setArgument('procesos\model\entity\ActividadFase'); // nombre del objeto relacionado
		$oDatosCampo->setArgument2('desc_fase'); // clave con la que crear el objeto relacionado
		$oDatosCampo->setArgument3('getListaActividadFases'); // método con que crear la lista de opciones del Gestor objeto relacionado.
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut sdesc_tarea de ActividadTarea
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosDesc_tarea() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'desc_tarea'));
		$oDatosCampo->setEtiqueta(_("descripción"));
		$oDatosCampo->setTipo('texto');
		$oDatosCampo->setArgument('30');
		return $oDatosCampo;
	}
}