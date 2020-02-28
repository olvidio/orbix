<?php
namespace cambios\model\entity;
use actividades\model\entity\Actividad;
use core;
use personas\model\entity\Persona;
use procesos\model\entity\GestorActividadFase;
use procesos\model\entity\GestorActividadProcesoTarea;
use ubis\model\entity\Ubi;
use core\ConfigGlobal;
/**
 * Fitxer amb la Classe que accedeix a la taula av_cambios
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 17/4/2019
 */
/**
 * Classe que implementa l'entitat av_cambios
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 17/4/2019
 */
class CambioDl Extends Cambio {
	/* ATRIBUTS ----------------------------------------------------------------- */
	/* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 * Si només necessita un valor, se li pot passar un integer.
	 * En general se li passa un array amb les claus primàries.
	 *
	 * @param integer|array iid_item_cambio
	 * 						$a_id. Un array con los nombres=>valores de las claves primarias.
	 */
	function __construct($a_id='') {
		$oDbl = $GLOBALS['oDBC'];
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				if (($nom_id == 'id_item_cambio') && $val_id !== '') $this->iid_item_cambio = (int)$val_id; // evitem SQL injection fent cast a integer
			}
		} else {
			if (isset($a_id) && $a_id !== '') {
				$this->iid_item_cambio = intval($a_id); // evitem SQL injection fent cast a integer
				$this->aPrimary_key = array('id_item_cambio' => $this->iid_item_cambio);
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('av_cambios_dl');
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
	    if ($this->DBCarregar('guardar') === FALSE) { $bInsert=TRUE; } else { $bInsert=FALSE; }
	    $aDades=array();
	    $aDades['id_tipo_cambio'] = $this->iid_tipo_cambio;
	    $aDades['id_activ'] = $this->iid_activ;
	    $aDades['id_tipo_activ'] = $this->iid_tipo_activ;
	    $aDades['id_fase_sv'] = $this->iid_fase_sv;
	    $aDades['id_fase_sf'] = $this->iid_fase_sf;
	    $aDades['id_status'] = $this->iid_status;
	    $aDades['dl_org'] = $this->sdl_org;
	    $aDades['objeto'] = $this->sobjeto;
	    $aDades['propiedad'] = $this->spropiedad;
	    $aDades['valor_old'] = $this->svalor_old;
	    $aDades['valor_new'] = $this->svalor_new;
	    $aDades['quien_cambia'] = $this->iquien_cambia;
	    $aDades['sfsv_quien_cambia'] = $this->isfsv_quien_cambia;
	    $aDades['timestamp_cambio'] = $this->itimestamp_cambio;
	    array_walk($aDades, 'core\poner_null');
	    
	    if ($bInsert === FALSE) {
	        //UPDATE
	        $update="
					id_tipo_cambio           = :id_tipo_cambio,
					id_activ                 = :id_activ,
					id_tipo_activ            = :id_tipo_activ,
					id_fase_sv               = :id_fase_sv,
					id_fase_sf               = :id_fase_sf,
					id_status                = :id_status,
					dl_org                   = :dl_org,
					objeto                   = :objeto,
					propiedad                = :propiedad,
					valor_old                = :valor_old,
					valor_new                = :valor_new,
					quien_cambia             = :quien_cambia,
					sfsv_quien_cambia        = :sfsv_quien_cambia,
					timestamp_cambio         = :timestamp_cambio
                    ";
	        if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_item_cambio='$this->iid_item_cambio'")) === FALSE) {
	            $sClauError = 'Cambio.update.prepare';
	            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
	            return FALSE;
	        } else {
	            if ($oDblSt->execute($aDades) === FALSE) {
	                $sClauError = 'Cambio.update.execute';
	                $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
	                return FALSE;
	            }
	        }
	    } else {
	        // INSERT
	        $campos="(id_tipo_cambio,id_activ,id_tipo_activ,id_fase_sv,id_fase_sf,id_status,dl_org,objeto,propiedad,valor_old,valor_new,quien_cambia,sfsv_quien_cambia,timestamp_cambio)";
	        $valores="(:id_tipo_cambio,:id_activ,:id_tipo_activ,:id_fase_sv,:id_fase_sf,:id_status,:dl_org,:objeto,:propiedad,:valor_old,:valor_new,:quien_cambia,:sfsv_quien_cambia,:timestamp_cambio)";
	        if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === FALSE) {
	            $sClauError = 'Cambio.insertar.prepare';
	            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
	            return FALSE;
	        } else {
	            if ($oDblSt->execute($aDades) === FALSE) {
	                $sClauError = 'Cambio.insertar.execute';
	                $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
	                return FALSE;
	            }
	        }
	        $id_seq =  $nom_tabla."_id_item_cambio_seq";
	        $this->id_item_cambio = $oDbl->lastInsertId($id_seq);
	    }
	    $this->setAllAtributes($aDades);
	    // Creo que solo hay que disparar el generador de avisos en las dl que tengan el módulo.
	    if (ConfigGlobal::is_app_installed('cambios')) {
            // Para el caso de poner anotado, no debo disparar el generador de avisos.
            if (empty($quiet)) {
                $this->generarTabla();
            }
	    }
	    return TRUE;
	}
	
	/* METODES ALTRES  ----------------------------------------------------------*/
	
	
	/* METODES PRIVATS ----------------------------------------------------------*/

	/* METODES GET i SET --------------------------------------------------------*/

}
