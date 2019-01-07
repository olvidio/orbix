<?php
namespace procesos\model\entity;
use core;
use actividades\model\entity\Actividad;
/**
 * Fitxer amb la Classe que accedeix a la taula a_procesos
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 07/12/2018
 */
/**
 * Classe que implementa l'entitat a_procesos
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 07/12/2018
 */
class Proceso Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de Proceso
	 *
	 * @var array
	 */
	 private $aPrimary_key;

	/**
	 * aDades de Proceso
	 *
	 * @var array
	 */
	 private $aDades;

	/**
	 * Id_item de Proceso
	 *
	 * @var integer
	 */
	 private $iid_item;
	/**
	 * Id_tipo_proceso de Proceso
	 *
	 * @var integer
	 */
	 private $iid_tipo_proceso;
	/**
	 * N_orden de Proceso
	 *
	 * @var integer
	 */
	 private $in_orden;
	/**
	 * Id_fase de Proceso
	 *
	 * @var integer
	 */
	 private $iid_fase;
	/**
	 * Id_tarea de Proceso
	 *
	 * @var integer
	 */
	 private $iid_tarea;
	/**
	 * Status de Proceso
	 *
	 * @var integer
	 */
	 private $istatus;
	/**
	 * Of_responsable de Proceso
	 *
	 * @var string
	 */
	 private $sof_responsable;
	/**
	 * Id_fase_previa de Proceso
	 *
	 * @var integer
	 */
	 private $iid_fase_previa;
	/**
	 * Id_tarea_previa de Proceso
	 *
	 * @var integer
	 */
	 private $iid_tarea_previa;
	/**
	 * Mensaje_requisito de Proceso
	 *
	 * @var string
	 */
	 private $smensaje_requisito;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/**
	 * oDbl de Proceso
	 *
	 * @var object
	 */
	 protected $oDbl;
	/**
	 * NomTabla de Proceso
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
				//if (($nom_id == 'id_item') && $val_id !== '') $this->iid_item = (int)$val_id; // evitem SQL injection fent cast a integer
				$this->$nom_id = (int)$val_id; // evitem SQL injection fent cast a integer
			}	
		} else {
			if (isset($a_id) && $a_id !== '') {
				$this->iid_item = intval($a_id); // evitem SQL injection fent cast a integer
				$this->aPrimary_key = array('iid_item' => $this->iid_item);
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('a_procesos');
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
		$aDades['id_tipo_proceso'] = $this->iid_tipo_proceso;
		$aDades['n_orden'] = $this->in_orden;
		$aDades['id_fase'] = $this->iid_fase;
		$aDades['id_tarea'] = $this->iid_tarea;
		$aDades['status'] = $this->istatus;
		$aDades['of_responsable'] = $this->sof_responsable;
		$aDades['id_fase_previa'] = $this->iid_fase_previa;
		$aDades['id_tarea_previa'] = $this->iid_tarea_previa;
		$aDades['mensaje_requisito'] = $this->smensaje_requisito;
		array_walk($aDades, 'core\poner_null');

		if ($bInsert === FALSE) {
			//UPDATE
			$update="
					id_tipo_proceso          = :id_tipo_proceso,
					n_orden                  = :n_orden,
					id_fase                  = :id_fase,
					id_tarea                 = :id_tarea,
					status                   = :status,
					of_responsable           = :of_responsable,
					id_fase_previa           = :id_fase_previa,
					id_tarea_previa          = :id_tarea_previa,
					mensaje_requisito        = :mensaje_requisito";
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_item='$this->iid_item'")) === FALSE) {
				$sClauError = 'Proceso.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return FALSE;
			} else {
				if ($oDblSt->execute($aDades) === FALSE) {
					$sClauError = 'Proceso.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return FALSE;
				}
			}
		} else {
			// INSERT
			$campos="(id_tipo_proceso,n_orden,id_fase,id_tarea,status,of_responsable,id_fase_previa,id_tarea_previa,mensaje_requisito)";
			$valores="(:id_tipo_proceso,:n_orden,:id_fase,:id_tarea,:status,:of_responsable,:id_fase_previa,:id_tarea_previa,:mensaje_requisito)";		
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === FALSE) {
				$sClauError = 'Proceso.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return FALSE;
			} else {
				if ($oDblSt->execute($aDades) === FALSE) {
					$sClauError = 'Proceso.insertar.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return FALSE;
				}
			}
			$this->id_item = $oDbl->lastInsertId('a_procesos_id_item_seq');
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
				$sClauError = 'Proceso.carregar';
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
		} elseif (!empty($this->aPrimary_key)) {
			if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla 
                    WHERE id_tipo_proceso=$this->iid_tipo_proceso AND id_fase=$this->iid_fase AND id_tarea=$this->iid_tarea")) === FALSE) {
				$sClauError = 'Proceso.carregar';
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
			$sClauError = 'Proceso.eliminar';
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
		if (array_key_exists('id_tipo_proceso',$aDades)) $this->setId_tipo_proceso($aDades['id_tipo_proceso']);
		if (array_key_exists('n_orden',$aDades)) $this->setN_orden($aDades['n_orden']);
		if (array_key_exists('id_fase',$aDades)) $this->setId_fase($aDades['id_fase']);
		if (array_key_exists('id_tarea',$aDades)) $this->setId_tarea($aDades['id_tarea']);
		if (array_key_exists('status',$aDades)) $this->setStatus($aDades['status']);
		if (array_key_exists('of_responsable',$aDades)) $this->setOf_responsable($aDades['of_responsable']);
		if (array_key_exists('id_fase_previa',$aDades)) $this->setId_fase_previa($aDades['id_fase_previa']);
		if (array_key_exists('id_tarea_previa',$aDades)) $this->setId_tarea_previa($aDades['id_tarea_previa']);
		if (array_key_exists('mensaje_requisito',$aDades)) $this->setMensaje_requisito($aDades['mensaje_requisito']);
	}

	/* METODES GET i SET --------------------------------------------------------*/

	/**
	 * Recupera tots els atributs de Proceso en un array
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
	 * Recupera las claus primàries de Proceso en un array
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
	 * Recupera l'atribut iid_item de Proceso
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
	 * estableix el valor de l'atribut iid_item de Proceso
	 *
	 * @param integer iid_item
	 */
	function setId_item($iid_item) {
		$this->iid_item = $iid_item;
	}
	/**
	 * Recupera l'atribut iid_tipo_proceso de Proceso
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
	 * estableix el valor de l'atribut iid_tipo_proceso de Proceso
	 *
	 * @param integer iid_tipo_proceso='' optional
	 */
	function setId_tipo_proceso($iid_tipo_proceso='') {
		$this->iid_tipo_proceso = $iid_tipo_proceso;
	}
	/**
	 * Recupera l'atribut in_orden de Proceso
	 *
	 * @return integer in_orden
	 */
	function getN_orden() {
		if (!isset($this->in_orden)) {
			$this->DBCarregar();
		}
		return $this->in_orden;
	}
	/**
	 * estableix el valor de l'atribut in_orden de Proceso
	 *
	 * @param integer in_orden='' optional
	 */
	function setN_orden($in_orden='') {
		$this->in_orden = $in_orden;
	}
	/**
	 * Recupera l'atribut iid_fase de Proceso
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
	 * estableix el valor de l'atribut iid_fase de Proceso
	 *
	 * @param integer iid_fase='' optional
	 */
	function setId_fase($iid_fase='') {
		$this->iid_fase = $iid_fase;
	}
	/**
	 * Recupera l'atribut iid_tarea de Proceso
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
	 * estableix el valor de l'atribut iid_tarea de Proceso
	 *
	 * @param integer iid_tarea='' optional
	 */
	function setId_tarea($iid_tarea='') {
		$this->iid_tarea = $iid_tarea;
	}
	/**
	 * Recupera l'atribut istatus de Proceso
	 *
	 * @return integer istatus
	 */
	function getStatus() {
		if (!isset($this->istatus)) {
			$this->DBCarregar();
		}
		return $this->istatus;
	}
	/**
	 * estableix el valor de l'atribut istatus de Proceso
	 *
	 * @param integer istatus='' optional
	 */
	function setStatus($istatus='') {
		$this->istatus = $istatus;
	}
	/**
	 * Recupera l'atribut sof_responsable de Proceso
	 *
	 * @return string sof_responsable
	 */
	function getOf_responsable() {
		if (!isset($this->sof_responsable)) {
			$this->DBCarregar();
		}
		return $this->sof_responsable;
	}
	/**
	 * estableix el valor de l'atribut sof_responsable de Proceso
	 *
	 * @param string sof_responsable='' optional
	 */
	function setOf_responsable($sof_responsable='') {
		$this->sof_responsable = $sof_responsable;
	}
	/**
	 * Recupera l'atribut iid_fase_previa de Proceso
	 *
	 * @return integer iid_fase_previa
	 */
	function getId_fase_previa() {
		if (!isset($this->iid_fase_previa)) {
			$this->DBCarregar();
		}
		return $this->iid_fase_previa;
	}
	/**
	 * estableix el valor de l'atribut iid_fase_previa de Proceso
	 *
	 * @param integer iid_fase_previa='' optional
	 */
	function setId_fase_previa($iid_fase_previa='') {
		$this->iid_fase_previa = $iid_fase_previa;
	}
	/**
	 * Recupera l'atribut iid_tarea_previa de Proceso
	 *
	 * @return integer iid_tarea_previa
	 */
	function getId_tarea_previa() {
		if (!isset($this->iid_tarea_previa)) {
			$this->DBCarregar();
		}
		return $this->iid_tarea_previa;
	}
	/**
	 * estableix el valor de l'atribut iid_tarea_previa de Proceso
	 *
	 * @param integer iid_tarea_previa='' optional
	 */
	function setId_tarea_previa($iid_tarea_previa='') {
		$this->iid_tarea_previa = $iid_tarea_previa;
	}
	/**
	 * Recupera l'atribut smensaje_requisito de Proceso
	 *
	 * @return string smensaje_requisito
	 */
	function getMensaje_requisito() {
		if (!isset($this->smensaje_requisito)) {
			$this->DBCarregar();
		}
		return $this->smensaje_requisito;
	}
	/**
	 * estableix el valor de l'atribut smensaje_requisito de Proceso
	 *
	 * @param string smensaje_requisito='' optional
	 */
	function setMensaje_requisito($smensaje_requisito='') {
		$this->smensaje_requisito = $smensaje_requisito;
	}
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$oProcesoSet = new core\Set();

		$oProcesoSet->add($this->getDatosId_tipo_proceso());
		$oProcesoSet->add($this->getDatosN_orden());
		$oProcesoSet->add($this->getDatosId_fase());
		$oProcesoSet->add($this->getDatosId_tarea());
		$oProcesoSet->add($this->getDatosStatus());
		$oProcesoSet->add($this->getDatosOf_responsable());
		$oProcesoSet->add($this->getDatosId_fase_previa());
		$oProcesoSet->add($this->getDatosId_tarea_previa());
		$oProcesoSet->add($this->getDatosMensaje_requisito());
		return $oProcesoSet->getTot();
	}



	/**
	 * Recupera les propietats de l'atribut iid_tipo_proceso de Proceso
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosId_tipo_proceso() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_tipo_proceso'));
		$oDatosCampo->setEtiqueta(_("tipo de proceso"));
		$oDatosCampo->setTipo('opciones');
		$oDatosCampo->setArgument('ProcesoTipo'); // nombre del objeto relacionado
		$oDatosCampo->setArgument2('nom_proceso'); // propiedad para ver en listados.
		$oDatosCampo->setArgument3('getListaProcesoTipos'); // método con que crear la lista de opciones del Gestor objeto relacionado.
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut in_orden de Proceso
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosN_orden() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'n_orden'));
		$oDatosCampo->setEtiqueta(_("orden"));
		$oDatosCampo->setTipo('texto');
		$oDatosCampo->setArgument('4');
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut iid_fase de Proceso
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosId_fase() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_fase'));
		$oDatosCampo->setEtiqueta(_("fase"));
		$oDatosCampo->setTipo('opciones');
		$oDatosCampo->setArgument('ActividadFase'); // nombre del objeto relacionado
		$oDatosCampo->setArgument2('desc_fase'); // propiedad para ver en listados.
		$oDatosCampo->setArgument3('getListaActividadFases'); // método con que crear la lista de opciones del Gestor objeto relacionado.
		$oDatosCampo->setAccion('id_tarea'); // campo que hay que actualizar al cambiar este.
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut iid_tarea de Proceso
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosId_tarea() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_tarea'));
		$oDatosCampo->setEtiqueta(_("tarea"));
		$oDatosCampo->setTipo('depende');
		$oDatosCampo->setArgument('ActividadTarea'); // nombre del objeto relacionado para ver en listados.
		$oDatosCampo->setArgument2('desc_tarea'); // propiedad para ver en listados.
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut istatus de Proceso
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosStatus() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'status'));
		$oDatosCampo->setEtiqueta(_("status"));
		$oDatosCampo->setTipo('array');
		$oDatosCampo->setLista(Actividad::ARRAY_STATUS_TXT);
		
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut sof_responsable de Proceso
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosOf_responsable() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'of_responsable'));
		$oDatosCampo->setEtiqueta(_("oficina responsable"));
		$oDatosCampo->setTipo('texto');
		$oDatosCampo->setArgument('7');
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut iid_fase_previa de Proceso
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosId_fase_previa() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_fase_previa'));
		$oDatosCampo->setEtiqueta(_("fase previa"));
		$oDatosCampo->setTipo('opciones');
		$oDatosCampo->setArgument('ActividadFase'); // nombre del objeto relacionado
		$oDatosCampo->setArgument2('desc_fase'); // propiedad para ver en listados.
		$oDatosCampo->setArgument3('getListaActividadFases'); // método con que crear la lista de opciones del Gestor objeto relacionado.
		$oDatosCampo->setAccion('id_tarea_previa'); // campo que hay que actualizar al cambiar este.
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut iid_tarea_previa de Proceso
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosId_tarea_previa() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_tarea_previa'));
		$oDatosCampo->setEtiqueta(_("tarea previa"));
		$oDatosCampo->setTipo('depende');
		$oDatosCampo->setArgument('ActividadTarea');
		$oDatosCampo->setArgument2('desc_tarea'); // propiedad para ver en listados.
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut smensaje_requisito de Proceso
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosMensaje_requisito() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'mensaje_requisito'));
		$oDatosCampo->setEtiqueta(_("mensaje requisito"));
		$oDatosCampo->setTipo('texto');
		$oDatosCampo->setArgument('70');
		return $oDatosCampo;
	}
}