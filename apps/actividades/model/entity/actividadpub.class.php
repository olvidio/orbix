<?php
namespace actividades\model\entity;
use core;
/**
 * Classe que implementa l'entitat a_actividades_dl
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 01/10/2010
 */

class ActividadPub Extends ActividadAll {
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
		$oDbl = $GLOBALS['oDBPC'];
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
		$this->setNomTabla('av_actividades_pub');
	}

	/* METODES PUBLICS ----------------------------------------------------------*/

	/**
	 * Desa els atributs de l'objecte a la base de dades.
	 * Si no hi ha el registre, fa el insert, si hi es fa el update.
	 *
	 *@param bool optional $quiet : true per que no apunti els canvis. 0 (per defecte) apunta els canvis.
	 */
	public function DBGuardar($quiet=0) {
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
		$aDades['id_tabla'] = $this->sid_tabla;
		$aDades['plazas'] = $this->iplazas;
		array_walk($aDades, 'core\poner_null');
		//para el caso de los boolean FALSE, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
		if ( core\is_true($aDades['publicado']) ) { $aDades['publicado']='true'; } else { $aDades['publicado']='false'; }
		
		$a_pkey = $this->aPrimary_key;
		$dl = $aDades['dl_org'];
		$id_tabla = $this->sid_tabla;
		if ($dl == core\ConfigGlobal::mi_delef()) {
			$oActividad= new ActividadDl($a_pkey);
		} else {
			if ($id_tabla == 'dl') {
				// No se puede guardar cambios en una actividad de otra dl
				return false;
			} else {
				$oActividad= new ActividadEx($a_pkey);
			}
		}
		$oActividad->setAllAtributes($aDades);
		$oActividad->DBGuardar($aDades);
		return true;
	}

	/**
	 * Carrega els camps de la base de dades com atributs de l'objecte.
	 *
	 */
	public function DBCarregar($que=null) {
		$oDbl = $this->getoDbl();
		if (isset($this->iid_activ)) {
			if (($oDblSt = $oDbl->query("SELECT * FROM av_actividades_pub WHERE id_activ='$this->iid_activ'")) === false) {
				$sClauError = get_class($this).'.carregar';
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
					if ( core\is_true($aDades['publicado']) ) { $aDades['publicado']='true'; } else { $aDades['publicado']='false'; }
					$this->aDadesActuals=$aDades;
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
		$a_pkey = $this->aPrimary_key;
		$dl = $this->sdl_org;
		$id_tabla = $this->id_tabla;
		if ($dl == core\ConfigGlobal::mi_delef()) {
			$oActividadAll= new ActividadDl($a_pkey);
		} else {
			if ($id_tabla == 'dl') {
				//$oActividad = new ActividadPub($a_pkey);
				// No se puede eliminar una actividad de otra dl
				echo _("no se puede modificar una actividad de otra dl");
				return false;
			} else {
				$oActividadAll = new ActividadEx($a_pkey);
			}
		}
		$oActividadAll->DBEliminar();
		return true;
	}
	
	/* METODES ALTRES  ----------------------------------------------------------*/
	/* METODES PRIVATS ----------------------------------------------------------*/
}
