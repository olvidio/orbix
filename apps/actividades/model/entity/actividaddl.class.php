<?php
namespace actividades\model\entity;
use core;
//require_once('classes/web/fechas.class');
use cambios\model\entity as cambios;
use procesos\model\entity as procesos;
use cambios\model\gestorAvisoCambios;
/**
 * Classe que implementa l'entitat a_actividades_dl
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 01/10/2010
 */

class ActividadDl Extends ActividadAll {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 * Si només necessita un valor, se li pot passar un integer.
	 * En general se li passa un array amb les claus primàries.
	 *
	 * @param integer|array iid_activ
	 * 						$a_id. Un array con los nombres=>valores de las claves primarias.
	 */
	function __construct($a_id='') {
		$oDbl = $GLOBALS['oDBC'];
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				$nom_id='i'.$nom_id; //imagino que es un integer
				if ($val_id !== '') $this->$nom_id = intval($val_id); // evitem SQL injection fent cast a integer
			}
		} else {
			if (isset($a_id) && $a_id !== '') {
				$this->iid_activ = intval($a_id); // evitem SQL injection fent cast a integer
				$this->aPrimary_key = array('id_activ' => $this->iid_activ);
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('a_actividades_dl');
		$this->setId_tabla('dl');
	}

	/* METODES PUBLICS ----------------------------------------------------------*/

	/**
	 * Desa els atributs de l'objecte a la base de dades.
	 * Si no hi ha el registre, fa el insert, si hi es fa el update.
	 *
	 *@param bool optional $quiet : true per que no apunti els canvis. 0 (per defecte) apunta els canvis.
	 */
	public function DBGuardar($quiet=0) {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		if ($this->DBCarregar('guardar') === false) { $bInsert=true; } else { $bInsert=false; }
		$aDades=array();
		$aDades['id_tipo_activ'] = $this->iid_tipo_activ;
		$aDades['dl_org'] = $this->sdl_org;
		$aDades['nom_activ'] = $this->snom_activ;
		$aDades['id_ubi'] = $this->iid_ubi;
		$aDades['desc_activ'] = $this->sdesc_activ;
		$aDades['f_ini'] = $this->df_ini;
		$aDades['h_ini'] = $this->th_ini;
		$aDades['f_fin'] = $this->df_fin;
		$aDades['h_fin'] = $this->th_fin;
		$aDades['tipo_horario'] = $this->itipo_horario;
		$aDades['precio'] = $this->iprecio;
		$aDades['num_asistentes'] = $this->inum_asistentes;
		$aDades['status'] = $this->istatus;
		$aDades['observ'] = $this->sobserv;
		$aDades['nivel_stgr'] = $this->inivel_stgr;
		$aDades['observ_material'] = $this->sobserv_material;
		$aDades['lugar_esp'] = $this->slugar_esp;
		$aDades['tarifa'] = $this->itarifa;
		$aDades['id_repeticion'] = $this->iid_repeticion;
		$aDades['publicado'] = $this->bpublicado;
		//$aDades['id_tabla'] = $this->sid_tabla;
		$aDades['plazas'] = $this->iplazas;
		array_walk($aDades, 'core\poner_null');
		//para el caso de los boolean false, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
		$aDades['publicado'] = ($aDades['publicado'] === 't')? 'true' : $aDades['publicado'];
		if ( filter_var( $aDades['publicado'], FILTER_VALIDATE_BOOLEAN)) { $aDades['publicado']='true'; } else { $aDades['publicado']='false'; }


		if ($bInsert === false) {
			//UPDATE
			$update="
					id_tipo_activ            = :id_tipo_activ,
					dl_org                   = :dl_org,
					nom_activ                = :nom_activ,
					id_ubi                   = :id_ubi,
					desc_activ               = :desc_activ,
					f_ini                    = :f_ini,
					h_ini                    = :h_ini,
					f_fin                    = :f_fin,
					h_fin                    = :h_fin,
					tipo_horario             = :tipo_horario,
					precio                   = :precio,
					num_asistentes           = :num_asistentes,
					status                   = :status,
					observ                   = :observ,
					nivel_stgr               = :nivel_stgr,
					observ_material          = :observ_material,
					lugar_esp                = :lugar_esp,
					tarifa                   = :tarifa,
					id_repeticion            = :id_repeticion,
					publicado   	         = :publicado,
					plazas                 	 = :plazas";
					//id_tabla                 = :id_tabla,
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_activ='$this->iid_activ'")) === false) {
				$sClauError = 'ActividadDl.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($oDblSt->execute($aDades) === false) {
					$sClauError = 'ActividadDl.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
			// Aunque no tenga el módulo de 'cambios', quizá otra dl si lo tenga.
			// Anoto el cambio si la actividad está publicada
			if (empty($quiet) && (core\ConfigGlobal::is_app_installed('cambios') OR $aDades['publicado'] == 'true')) {
				$oGestorCanvis = new gestorAvisoCambios();
				$shortClassName = (new \ReflectionClass($this))->getShortName();
				$oGestorCanvis->addCanvi($shortClassName, 'UPDATE', $this->iid_activ, $aDades, $this->aDadesActuals);
			}
			$this->setAllAtributes($aDades);
		} else {
			// INSERT
			//$campos="(id_tipo_activ,dl_org,nom_activ,id_ubi,desc_activ,f_ini,h_ini,f_fin,h_fin,tipo_horario,precio,num_asistentes,status,observ,nivel_stgr,observ_material,lugar_esp,tarifa,id_repeticion,publicado,id_tabla,plazas)";
			$campos="(id_tipo_activ,dl_org,nom_activ,id_ubi,desc_activ,f_ini,h_ini,f_fin,h_fin,tipo_horario,precio,num_asistentes,status,observ,nivel_stgr,observ_material,lugar_esp,tarifa,id_repeticion,publicado,plazas)";
			//$valores="(:id_tipo_activ,:dl_org,:nom_activ,:id_ubi,:desc_activ,:f_ini,:h_ini,:f_fin,:h_fin,:tipo_horario,:precio,:num_asistentes,:status,:observ,:nivel_stgr,:observ_material,:lugar_esp,:tarifa,:id_repeticion,:publicado,:id_tabla,:plazas)";		
			$valores="(:id_tipo_activ,:dl_org,:nom_activ,:id_ubi,:desc_activ,:f_ini,:h_ini,:f_fin,:h_fin,:tipo_horario,:precio,:num_asistentes,:status,:observ,:nivel_stgr,:observ_material,:lugar_esp,:tarifa,:id_repeticion,:publicado,:plazas)";		
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
				$sClauError = 'ActividadDl.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($oDblSt->execute($aDades) === false) {
					$sClauError = 'ActividadDl.insertar.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
			$id_auto = $oDbl->lastInsertId('a_actividades_dl_id_auto_seq');
			if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_auto=$id_auto")) === false) {
				$sClauError = 'ActividadDl.carregar.Last';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			}
			$aDadesLast = $oDblSt->fetch(\PDO::FETCH_ASSOC);
			$this->aDades=$aDadesLast;
			$this->setAllAtributes($aDadesLast);

			// generar proceso.
			if (core\configGlobal::is_app_installed('procesos')) {
				$oGestorActividadProcesoTarea = new procesos\GestorActividadProcesoTarea();
				$oGestorActividadProcesoTarea->generarProceso($aDadesLast['id_activ']);
			}
			// anotar cambio.
			// Aunque no tenga el módulo de 'cambios', quizá otra dl si lo tenga.
			// Anoto el cambio si la actividad está publicada
			if (empty($quiet) && (core\ConfigGlobal::is_app_installed('cambios') OR $aDades['publicado'] == 'true')) {
				$oGestorCanvis = new gestorAvisoCambios();
				$shortClassName = (new \ReflectionClass($this))->getShortName();
				$oGestorCanvis->addCanvi($shortClassName, 'INSERT', $aDadesLast['id_activ'], $this->aDades, array());
			}
		}
		return true;
	}

	/**
	 * Carrega els camps de la base de dades com atributs de l'objecte.
	 *
	 */
	public function DBCarregar($que=null) {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		if (isset($this->iid_activ)) {
			if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_activ='$this->iid_activ'")) === false) {
				$sClauError = 'ActividadDl.carregar';
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
					// Hay que guardar los boolean de la misma manera que al guardar los datos ('false','true'):
                    $aDades['publicado'] = ($aDades['publicado'] === 't')? 'true' : $aDades['publicado'];
                    if ( filter_var( $aDades['publicado'], FILTER_VALIDATE_BOOLEAN)) { $aDades['publicado']='true'; } else { $aDades['publicado']='false'; }
					$this->aDadesActuals=$aDades;
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
		if ($this->DBCarregar('guardar') === false) {
		   	// Si no existeix no cal eliminar-el.
			return false;
		} else {
			// Aunque no tenga el módulo de 'cambios', quizá otra dl si lo tenga.
			// Anoto el cambio si la actividad está publicada
			if (core\ConfigGlobal::is_app_installed('cambios') OR $this->bpublicado === TRUE) {
				// ho poso abans d'esborrar perque sino no trova cap valor. En el cas d'error s'hauria d'esborrar l'apunt.
				$oGestorCanvis = new gestorAvisoCambios();
				$shortClassName = (new \ReflectionClass($this))->getShortName();
				$oGestorCanvis->addCanvi($shortClassName, 'DELETE', $this->iid_activ, array(), $this->aDadesActuals);
			}
			if (($oDbl->exec("DELETE FROM $nom_tabla WHERE id_activ='$this->iid_activ'")) === false) {
				$sClauError = 'ActividadDl.eliminar';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			}
			return true;
		}
	}
	
	/* METODES ALTRES  ----------------------------------------------------------*/
	/* METODES PRIVATS ----------------------------------------------------------*/

	/**
	 * Estableix el valor de tots els atributs
	 *
	 * @param array $aDades
	 */
	function setAllAtributes($aDades,$convert=FALSE) {
		if (!is_array($aDades)) return;
		if (array_key_exists('id_schema',$aDades)) $this->setId_schema($aDades['id_schema']);
		if (array_key_exists('id_activ',$aDades)) $this->setId_activ($aDades['id_activ']);
		if (array_key_exists('id_tipo_activ',$aDades)) $this->setId_tipo_activ($aDades['id_tipo_activ']);
		if (array_key_exists('dl_org',$aDades)) $this->setDl_org($aDades['dl_org']);
		if (array_key_exists('nom_activ',$aDades)) $this->setNom_activ($aDades['nom_activ']);
		if (array_key_exists('id_ubi',$aDades)) $this->setId_ubi($aDades['id_ubi']);
		if (array_key_exists('desc_activ',$aDades)) $this->setDesc_activ($aDades['desc_activ']);
		if (array_key_exists('f_ini',$aDades)) $this->setF_ini($aDades['f_ini'],$convert);
		if (array_key_exists('h_ini',$aDades)) $this->setH_ini($aDades['h_ini']);
		if (array_key_exists('f_fin',$aDades)) $this->setF_fin($aDades['f_fin'],$convert);
		if (array_key_exists('h_fin',$aDades)) $this->setH_fin($aDades['h_fin']);
		if (array_key_exists('tipo_horario',$aDades)) $this->setTipo_horario($aDades['tipo_horario']);
		if (array_key_exists('precio',$aDades)) $this->setPrecio($aDades['precio']);
		if (array_key_exists('num_asistentes',$aDades)) $this->setNum_asistentes($aDades['num_asistentes']);
		if (array_key_exists('status',$aDades)) $this->setStatus($aDades['status']);
		if (array_key_exists('observ',$aDades)) $this->setObserv($aDades['observ']);
		if (array_key_exists('nivel_stgr',$aDades)) $this->setNivel_stgr($aDades['nivel_stgr']);
		if (array_key_exists('observ_material',$aDades)) $this->setObserv_material($aDades['observ_material']);
		if (array_key_exists('lugar_esp',$aDades)) $this->setLugar_esp($aDades['lugar_esp']);
		if (array_key_exists('tarifa',$aDades)) $this->setTarifa($aDades['tarifa']);
		if (array_key_exists('id_repeticion',$aDades)) $this->setId_repeticion($aDades['id_repeticion']);
		if (array_key_exists('publicado',$aDades)) $this->setPublicado($aDades['publicado']);
		if (array_key_exists('id_tabla',$aDades)) $this->setId_tabla($aDades['id_tabla']);
		if (array_key_exists('plazas',$aDades)) $this->setPlazas($aDades['plazas']);
	}

	/* METODES GET i SET --------------------------------------------------------*/
}
