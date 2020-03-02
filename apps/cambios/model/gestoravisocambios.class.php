<?php
namespace cambios\model;

use actividades\model\entity\Actividad;
use cambios\model\entity\Cambio;
use cambios\model\entity\CambioDl;
use core\ConfigGlobal;
use procesos\model\entity\GestorActividadProcesoTarea;
use web\DateTimeLocal;
use web\Posicion;

/**
 * Classe para manejar los cambios
 *
 * @package delegación
 * @subpackage model
 * @author
 * @version 1.0
 * @created 3/1/2012
 */

class gestorAvisoCambios {
	/* METODES ESTATICS ----------------------------------------------------------*/

	/**
	 * Recupera un array amb els objectes (de taula) dels que es pot avisar i els seus noms.
	 *
	 * @return array
	 */
	public static function getArrayObjetosPosibles() {
		$aNomTablas_obj = array('ActividadDl' => _("actividad"),
					'ActividadCargoSacd' => _("sacd"),
					'CentroEncargado'=> _("ctr"),
					'ActividadCargoNoSacd' => _("cl"),
					'Asistente' => _("asistencias"),
					'ActividadProcesoTarea'=> _("fases actividad")
		);
		return $aNomTablas_obj;
	}
	/**
	 * Retorna el nombre completo del objeto, para poder crear una nueva instancia.
	 *
	 * @param string nombre corto del objeto.
	 * @return string
	 */
	public static function getFullPathObj($obj_txt) {
        $spath = '';
	    switch ($obj_txt) {
	        case 'Actividad':
	            $spath = 'actividades\\model\\entity\\Actividad';
	            break;
	        case 'ActividadDl':
	            $spath = 'actividades\\model\\entity\\ActividadDl';
	            break;
	        case 'ActividadEx':
	            $spath = 'actividades\\model\\entity\\ActividadEx';
	            break;
	        case 'ActividadCargoSacd':
	            $spath = 'actividadcargos\\model\\entity\\ActividadCargoSacd';
	            break;
	        case 'CentroEncargado':
	            $spath = 'actividadescentro\\model\\entity\\CentroEncargado';
	            break;
	        case 'ActividadCargoNoSacd':
	            $spath = 'actividadcargos\\model\\entity\\ActividadCargoNoSacd';
	            break;
	        case 'Asistente':
	            $spath = 'asistentes\\model\\entity\\Asistente';
	            break;
	        case 'ActividadProcesoTarea':
	            $spath = 'procesos\\model\\entity\\ActividadProcesoTarea';
	            break;
	    }

		return $spath;
	}

	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aDades de GestorCanvis
	 *
	 * @var array
	 */
	 private $aDades;


	/* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 *
	 */
	function __construct() {
	
	}

	/* METODES PUBLICS ----------------------------------------------------------*/

	function muestraMensaje($sClauError,$goto) {
		//$_SESSION['oGestorErrores']->addErrorAppLastError($oDBSt, $sClauError, $file);
		$txt=$this->leerErrorAppLastError();
		if (strstr($txt, 'duplicate key')) {
			echo _("ya existe un registro con esta información");
		} else {
			echo "\n dd".$txt."\n $sClauError <br>";
		}
		$oPosicion = new Posicion();
		$seguir = $oPosicion->link_a($goto,0);
		//$seguir=link_a($goto,0);
		echo "<br><span class='link' onclick=fnjs_update_div('#main',$seguir)>"._("continuar")."</span>";


	}

	function addCanvi($sObjeto, $sTipoCambio, $iid_activ, $aDadesNew, $aDadesActuals) {
		// poso el nom de l'objecte que gestiona la taula en comptes del nom de la taula.
		$id_user = ConfigGlobal::mi_id_usuario();
		$sfsv = ConfigGlobal::mi_sfsv();
		$oAhora = new DateTimeLocal();
		$ahora_iso = $oAhora->format('Y-m-d H:i:s');
		
        $id_fase_sv = '';
        $id_fase_sf = '';
        $id_status = '';
		
		// per saber el tipus d'activitat.
        switch ($sObjeto) {
            case 'Actividad': //si el canvi és a l'activitat, ja el tinc.
            case 'ActividadDl': //si el canvi és a l'activitat, ja el tinc.
            case 'ActividadEx': //si el canvi és a l'activitat, ja el tinc.
                $iId_tipo_activ = empty($aDadesNew['id_tipo_activ'])? $aDadesActuals['id_tipo_activ'] : $aDadesNew['id_tipo_activ'];
                $sNomActiv = empty($aDadesNew['nom_activ'])? $aDadesActuals['nom_activ'] : $aDadesNew['nom_activ'];
                $dl_org = empty($aDadesActuals['dl_org'])? $aDadesNew['dl_org'] : $aDadesActuals['dl_org'];
                $status = $aDadesNew['status']?? $aDadesActuals['status'];
			break;
            default:
                $oActividad = new Actividad($iid_activ);
                $iId_tipo_activ = $oActividad->getId_tipo_activ();
                $sNomActiv = $oActividad->getNom_activ();
                $dl_org = $oActividad->getDl_org();
                $status = $oActividad->getStatus();
		}
		
		// Si no tengo instalado el módulo de 'cambios', no tengo la tabla en mi esquema.
		// Lo anoto en public
		if (ConfigGlobal::is_app_installed('cambios')) {
            $oActividadCambio = new CambioDl();
            // si no tengo instalado procesos, la fase es el status.
    		if (ConfigGlobal::is_app_installed('procesos')) {
                $oGestorActividadProcesoTarea = new GestorActividadProcesoTarea();
                // para sv:
                $oGestorActividadProcesoTarea->setNomTabla('a_actividad_proceso_sv');
                $id_fase_sv = $oGestorActividadProcesoTarea->getFaseActual($iid_activ);
                // para sf
                $oGestorActividadProcesoTarea->setNomTabla('a_actividad_proceso_sf');
                $id_fase_sf = $oGestorActividadProcesoTarea->getFaseActual($iid_activ);
                
                $id_status = $status;
    		} else {
                $id_status = $status;
    		}
		} else {
		    $id_status = $status;
            $oActividadCambio = new Cambio();
		}
		
		switch ($sTipoCambio) {
			case 'INSERT':
				$oActividadCambio->setId_tipo_cambio(Cambio::TIPO_CMB_INSERT);
				$oActividadCambio->setId_activ($iid_activ);
				$oActividadCambio->setId_tipo_activ($iId_tipo_activ);
                $oActividadCambio->setId_fase_sv($id_fase_sv);
                $oActividadCambio->setId_fase_sf($id_fase_sf);
				$oActividadCambio->setId_status($id_status);
				$oActividadCambio->setDl_org($dl_org);
				$oActividadCambio->setObjeto($sObjeto);
				$oActividadCambio->setQuien_cambia($id_user);
				$oActividadCambio->setSfsv_quien_cambia($sfsv);
				$oActividadCambio->setTimestamp_cambio($ahora_iso);
				$oActividadCambio->setValor_old();
				switch ($sObjeto) {
					case 'Actividad':
					case 'ActividadDl':
					case 'ActividadEx':
						$oActividadCambio->setPropiedad('nom_activ');
						$oActividadCambio->setValor_new($aDadesNew['nom_activ']);
						break;
					case 'Asistente':
					case 'ActividadCargoNoSacd':
					case 'ActividadCargoSacd':
						$oActividadCambio->setPropiedad('id_nom');
						$oActividadCambio->setValor_new($aDadesNew['id_nom']);
						break;
					case 'CentroEncargado':
						$oActividadCambio->setPropiedad('id_ubi');
						$oActividadCambio->setValor_new($aDadesNew['id_ubi']);
						break;
				}
				$oActividadCambio->DBGuardar();
				break;
			case 'UPDATE':
				$result = array_diff_assoc($aDadesNew, $aDadesActuals);
				// OJO para los campos bool no basta... Se mira más abajo.
				$classname = get_class($oActividadCambio);
				foreach ($result as $key=>$value) {
				    if ($aDadesActuals[$key] === FALSE && $value == 'f') { continue; }
				    if ($aDadesActuals[$key] === TRUE && $value == 't') { continue; }
    				$oActividadCambio = new $classname();
					$oActividadCambio->setId_tipo_cambio(Cambio::TIPO_CMB_UPDATE);
					$oActividadCambio->setId_activ($iid_activ);
					$oActividadCambio->setId_tipo_activ($iId_tipo_activ);
					$oActividadCambio->setId_fase_sv($id_fase_sv);
					$oActividadCambio->setId_fase_sf($id_fase_sf);
					$oActividadCambio->setId_status($id_status);
					$oActividadCambio->setDl_org($dl_org);
					$oActividadCambio->setObjeto($sObjeto);
					$oActividadCambio->setPropiedad($key);
					$oActividadCambio->setValor_old($aDadesActuals[$key]);
					$oActividadCambio->setValor_new($value);
					$oActividadCambio->setQuien_cambia($id_user);
                    $oActividadCambio->setSfsv_quien_cambia($sfsv);
					$oActividadCambio->setTimestamp_cambio($ahora_iso);
					$oActividadCambio->DBGuardar();
				}
				break;
			case 'DELETE':
				$oActividadCambio->setId_tipo_cambio(Cambio::TIPO_CMB_DELETE);
				$oActividadCambio->setId_activ($iid_activ);
				$oActividadCambio->setId_tipo_activ($iId_tipo_activ);
				$oActividadCambio->setId_fase_sv($id_fase_sv);
				$oActividadCambio->setId_fase_sf($id_fase_sf);
				$oActividadCambio->setId_status($id_status);
				$oActividadCambio->setDl_org($dl_org);
				$oActividadCambio->setObjeto($sObjeto);
				$oActividadCambio->setValor_new();
				$oActividadCambio->setQuien_cambia($id_user);
				$oActividadCambio->setSfsv_quien_cambia($sfsv);
				$oActividadCambio->setTimestamp_cambio($ahora_iso);
				switch ($sObjeto) {
					case 'Actividad':
					case 'ActividadDl':
					case 'ActividadEx':
						// pongo id_activ = 0, pues al eliminar la actividad se eliminan todas las filas relacionadas.
					    //	Así mantengo el dato que se ha eliminado .
						$oActividadCambio->setId_activ(0);
						$oActividadCambio->setPropiedad('nom_activ');
						$oActividadCambio->setValor_old($aDadesActuals['nom_activ']);
						break;
					case 'Asistente':
					case 'ActividadCargoNoSacd':
					case 'ActividadCargoSacd':
						$oActividadCambio->setPropiedad('id_nom');
						$oActividadCambio->setValor_old($aDadesActuals['id_nom']);
						break;
					case 'CentroEncargado':
						$oActividadCambio->setPropiedad('id_ubi');
						$oActividadCambio->setValor_old($aDadesActuals['id_ubi']);
						break;
				}
				$oActividadCambio->DBGuardar();
				break;
			case 'FASE':
				// només mi fixo en el 'completado'
				// amb els boolean no s'aclara: 0,1,false ,true,f,t...
				if (empty($aDadesNew['completado']) || ($aDadesNew['completado'] === 'off') || ($aDadesNew['completado'] === 'false') || ($aDadesNew['completado'] === 'f')) { $boolCompletadoNew=false; } else { $boolCompletadoNew=true; }
				if (empty($aDadesActuals['completado']) || ($aDadesActuals['completado'] === 'off') || ($aDadesActuals['completado'] === 'false') || ($aDadesActuals['completado'] === 'f')) { $boolCompletadoActual=false; } else { $boolCompletadoActual=true; }

				if ($boolCompletadoNew != $boolCompletadoActual) {
					$oActividadCambio->setId_tipo_cambio(Cambio::TIPO_CMB_FASE);
					$oActividadCambio->setId_activ($iid_activ);
					$oActividadCambio->setId_tipo_activ($iId_tipo_activ);
					$oActividadCambio->setId_fase_sv($id_fase_sv);
					$oActividadCambio->setId_fase_sf($id_fase_sf);
					$oActividadCambio->setId_status($id_status);
					$oActividadCambio->setDl_org($dl_org);
					$oActividadCambio->setObjeto($sObjeto);
					$oActividadCambio->setPropiedad('completado');
					$oActividadCambio->setValor_old($boolCompletadoActual);
					$oActividadCambio->setValor_new($boolCompletadoNew);
					$oActividadCambio->setQuien_cambia($id_user);
                    $oActividadCambio->setSfsv_quien_cambia($sfsv);
					$oActividadCambio->setTimestamp_cambio($ahora_iso);
					$oActividadCambio->DBGuardar();
				}
				break;
		}
	}
}
