<?php
use actividades\model\entity\GestorActividad;
use actividadescentro\model\entity\GestorCentroEncargado;
use actividadtarifas\model\entity\TipoTarifa;
use core\ConfigGlobal;
use dossiers\model\PermisoDossier;
use permisos\model\PermisosActividadesTrue;
use ubis\model\entity\CentroDl;
use ubis\model\entity\GestorCasaDl;
use web\Lista;
use web\Periodo;
use web\TiposActividades;

/**
* Esta página muestra 
*
*@package	delegacion
*@subpackage	des
*@author	Daniel Serrabou
*@since		17/4/07.
*		
*/

// INICIO Cabecera global de URL de controlador *********************************
require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

$Qque = (string) \filter_input(INPUT_POST, 'que');
$Qver_ctr = (string) \filter_input(INPUT_POST, 'ver_ctr');
$Qid_cdc = (string) \filter_input(INPUT_POST, 'id_cdc');

$Qperiodo = (string) \filter_input(INPUT_POST, 'periodo');
$Qinicio = (string) \filter_input(INPUT_POST, 'inicio');
$Qfin = (string) \filter_input(INPUT_POST, 'fin');
$Qyear = (string) \filter_input(INPUT_POST, 'year');
$Qempiezamin = (string) \filter_input(INPUT_POST, 'empiezamin');
$Qempiezamax = (string) \filter_input(INPUT_POST, 'empiezamax');

	
$miSfsv = ConfigGlobal::mi_sfsv();

$equivalencias_gm_oficina = [
    'n'        => 'sm',
    'agd'      => 'agd',
    's'        => 'sg',
    'sg'       => 'sg',
    'sss+'     => 'des',
    'sr'       => 'sr',
];

$aGrupos=array();

$ver_ctr=empty($Qver_ctr)? 'no' : $Qver_ctr;
$aWhereCasa = [];
$aOperadorCasa = [];
switch ($Qque) {
	case "lista_cdc":
		$tipo="casa";
	    exit ('te estaba esperando!');
		// una lista de casas (id_ubi).
	    if (!empty($aId_ctrs)) {
	        $v = "{".implode(', ',$aId_ctrs)."}";
	        $aWhere['id_ctr'] = $v;
	        $aOperador['id_ctr'] = 'ANY';
	    }
		$condicion_perm = "id_ubi = " .implode(' OR id_ubi =',$Qid_cdc);
		$aWhere['id_ubi'] = 3;
		break;
	case "c_comunes":
	case "c_comunes_sf":
	case "c_comunes_sv":
		// casas comunes: cdr + dlb + sf +sv
		$tipo="casa";
		//$condicion_perm ="tipo_ubi='cdcdl' AND sv='t' AND sf='t' AND tipo_casa ~ 'cdc|cdr'";
		$aWhereCasa['tipo_ubi'] = 'cdcdl';
		$aWhereCasa['tipo_casa'] = 'cdc|cdr';
		$aOperadorCasa['tipo_casa'] = '~';
		$aWhereCasa['sv'] = 't';
		$aWhereCasa['sf'] = 't';
		break;
	case "c_todas":
		// casas de sv
		$tipo="casa";
		//$condicion_perm ="tipo_ubi='cdcdl' AND sv='t' AND tipo_casa ~ 'cdc|cdr'";
		$aWhereCasa['tipo_ubi'] = 'cdcdl';
		$aWhereCasa['tipo_casa'] = 'cdc|cdr';
		$aOperadorCasa['tipo_casa'] = '~';
		$aWhereCasa['sv'] = 't';
		$aWhereCasa['sf'] = 't';
		break;
	case "c_todas_sf":
		// casas de sv
		$tipo="casa";
		//$condicion_perm ="tipo_ubi='cdcdl' AND sf='t' AND tipo_casa ~ 'cdc|cdr'";
		$aWhereCasa['tipo_ubi'] = 'cdcdl';
		$aWhereCasa['tipo_casa'] = 'cdc|cdr';
		$aOperadorCasa['tipo_casa'] = '~';
		$aWhereCasa['sf'] = 't';
		break;
	case "c_todas_sv":
		// casas de scalendariov
		$tipo="casa";
		//$condicion_perm ="tipo_ubi='cdcdl' AND sv='t' AND tipo_casa ~ 'cdc|cdr'";
		$aWhereCasa['tipo_ubi'] = 'cdcdl';
		$aWhereCasa['tipo_casa'] = 'cdc|cdr';
		$aOperadorCasa['tipo_casa'] = '~';
		$aWhereCasa['sv'] = 't';
		break;
	case "o_actual":
		$tipo="oficina";
		// mi oficina actual.
		$mi_of=ConfigGlobal::mi_oficina();
		break;
	case "o_todas":
		$tipo="oficina";
		// todas las oficinas.
		$mi_of='all';
		break;
}


if (empty($Qempiezamin)) {
    $QempiezaminIso = date('Y-m-d',mktime(0, 0, 0, date('m'), date('d')-40, date('Y')));
} else {
    $oEmpiezamin = web\DateTimeLocal::createFromLocal($Qempiezamin);
    $QempiezaminIso = $oEmpiezamin->getIso();
}
// hasta dentro de 9 meses desde hoy.
if (empty($Qempiezamax)) {
    $QempiezamaxIso = date('Y-m-d',mktime(0, 0, 0, date('m')+9, 0, date('Y')));
} else {
    $oEmpiezamax = web\DateTimeLocal::createFromLocal($Qempiezamax);
    $QempiezamaxIso = $oEmpiezamax->getIso();
}
// periodo.
if (empty($Qperiodo) || $Qperiodo == 'otro') {
    $Qinicio = empty($Qinicio)? $QempiezaminIso : $Qinicio;
    $Qfin = empty($Qfin)? $QempiezamaxIso : $Qfin;
} else {
    $oPeriodo = new Periodo();
    $any=empty($Qyear)? date('Y')+1 : $Qyear;
    $oPeriodo->setAny($any);
    $oPeriodo->setPeriodo($Qperiodo);
    $Qinicio = $oPeriodo->getF_ini_iso();
    $Qfin = $oPeriodo->getF_fin_iso();
}

switch ($tipo) {
	case "casa":
	    /*
		$query_cdc="SELECT nombre_ubi, id_ubi, CASE WHEN sv='t' AND sf='t' THEN 1 ELSE 2 END AS orden
					FROM u_cdc 
					WHERE  $condicion_perm 
					ORDER BY orden, nombre_ubi ";
		//echo "sql casa: $query_cdc<br>";
		 * 
		 */
		$GesCasas = new GestorCasaDl();
		$cCasas = $GesCasas->getCasas($aWhereCasa,$aOperadorCasa);
		foreach ($cCasas as $oCasa) {
			$aGrupos[$oCasa->getId_ubi()]=$oCasa->getNombre_ubi();
		}
		break;
	case "oficina": // tipo asistentes
		$oTiposActividades = new TiposActividades();
		$oTiposActividades->setSfSvId($miSfsv);
        switch ($mi_of) {
            case 'sg':
                $mi_of_id = '[45]';
                $oTiposActividades->setAsistentesId($mi_of_id);
                $aGrupos = array('[45]'=>'s y sg');
                break;
            case 'des':
                $aGrupos = array('6'=>'des');
                break;
            case 'all':
                $aGrupos = 	$oTiposActividades->getAsistentesPosibles();
                break;
            default:
                $oPermisoOficinas = new PermisoDossier();
                $permissions = $oPermisoOficinas->getPermissions();
                $aGrupos = 	$oTiposActividades->getAsistentesPosibles();
                foreach ($aGrupos as $sasistentes) {
                    $oficina = $equivalencias_gm_oficina[$sasistentes]; 
                    $id_perm = $permissions[$oficina];
                    $e = $oPermisoOficinas->have_perm($oficina);
                    if (!$oPermisoOficinas->have_perm($oficina)) {
                        if (($key = array_search($oficina, $aGrupos)) !== false) {
                            unset($aGrupos[$key]);
                        }
                    }
                }
        }
		break;
}

$a_ubi_activ = array();
foreach ($aGrupos as $key => $Titulo) {
    $aWhere = [];
    $aOperador = [];
    $aWhere['f_ini'] = "'$Qinicio','$Qfin'";
    $aOperador['f_ini'] = 'BETWEEN';
    $aWhere['status'] = 4;
    $aOperador['status'] = '<';
	switch ($tipo) {
		case "casa":
			$aWhere['id_ubi'] = $key;
			$aWhere['_ordre'] = 'id_ubi,f_ini';

			$oGesActiv = new GestorActividad();
			$cActividades = $oGesActiv->getActividades($aWhere,$aOperador);
		break;
		case "oficina":
			$aWhere['_ordre'] = 'f_ini';
			// $key es el id asistentes
			if ($mi_of=="des") {
                $oGesActiv = new GestorActividad();
			    // los de la sssc
				$aWhere['id_tipo_activ'] = '^16';
                $aOperador['id_tipo_activ'] = '~';
                $cActividadesSSSC = $oGesActiv->getActividades($aWhere,$aOperador);
			    /* otras cv sacd de n, agd:
                112030  sv n ca ordenandos
                114031  sv n cve sacd n
                134030	sv agd cve ordenandos
                134031	sv agd cve sacd n
                134032	sv agd cve sacd agd y n
                */
				$aWhere['id_tipo_activ'] = '112030,114031,134030,134031,134032';
                $aOperador['id_tipo_activ'] = 'ANY';
                $cActividadesOtros = $oGesActiv->getActividades($aWhere,$aOperador);
			    
                $cActividades = array_merge($cActividadesOtros,$cActividadesSSSC);
			   
			} else {
				$oTiposActividades->setAsistentesId($key);
				//$cond_id_tipo_activ= "a.id_tipo_activ::text ~ '".$oTiposActividades->getNom_tipoRegexp()."'";
				$aWhere['id_tipo_activ'] = $oTiposActividades->getNom_tipoRegexp();
                $aOperador['id_tipo_activ'] = '~';
                $oGesActiv = new GestorActividad();
                $cActividades = $oGesActiv->getActividades($aWhere,$aOperador);
			}
			/*
			$sql_act="SELECT a.id_activ, nom_activ, h_ini, h_fin, tarifa, id_tipo_activ
					 FROM a_actividades a
					 WHERE $periodo_sql AND $cond_id_tipo_activ AND status < 4 ORDER BY f_ini"; 
			//echo "sql: $sql_act<br>";
			 * 
			 */
		break;
	}

	if (is_array($cActividades) && count($cActividades) > 1) {
		$a=0;
		foreach ($cActividades as $oActividad) {
			$a++;
			$id_activ = $oActividad->getId_activ();
			$id_tipo_activ = $oActividad->getId_tipo_activ();
			$dl_org = $oActividad->getDl_org();
			$f_ini = $oActividad->getF_ini()->getFromLocal();
			$f_fin = $oActividad->getF_fin()->getFromLocal();
			$h_ini = $oActividad->getH_ini();
			$h_fin = $oActividad->getH_fin();
			$tarifa = $oActividad->getTarifa();
			
			$h_ini = preg_replace('/(\d+):(\d+):(\d+)/', '$1:$2', $h_ini);
			$h_fin = preg_replace('/(\d+):(\d+):(\d+)/', '$1:$2', $h_fin);

			
			$oTipoActiv= new TiposActividades($id_tipo_activ);
			$ssfsv=$oTipoActiv->getSfsvText();
			$sasistentes=$oTipoActiv->getAsistentesText();
			$sactividad=$oTipoActiv->getActividadText();
			$snom_tipo=$oTipoActiv->getNom_tipoText();

			//$oIngreso = new Ingreso(array('id_activ'=>$id_activ));
			//$num_asistentes=$oIngreso->getNum_asistentes();
			$num_asistentes='';
			
			// mirar permisos.
			if(core\ConfigGlobal::is_app_installed('procesos')) {
			    $_SESSION['oPermActividades']->setActividad($id_activ,$id_tipo_activ,$dl_org);
			    $oPermActiv = $_SESSION['oPermActividades']->getPermisoActual('datos');
			    $oPermCtr = $_SESSION['oPermActividades']->getPermisoActual('ctr');
			} else {
			    $oPermActividades = new PermisosActividadesTrue(core\ConfigGlobal::mi_id_usuario());
			    $oPermActiv = $oPermActividades->getPermisoActual('datos');
			    $oPermCtr =  $oPermActividades->getPermisoActual('ctr');
			}

			if (!$oPermActiv->have_perm('ocupado')) { continue; } // no tiene permisos ni para ver.
			if (!$oPermActiv->have_perm('ver')) { // sólo puede ver que està ocupado
				$a_ubi_activ[$key][$a]['svsf']=$ssfsv;
				$a_ubi_activ[$key][$a]['tipo_activ']=_("ocupado");
				$a_ubi_activ[$key][$a]['fechas']="$f_ini - $f_fin";
				$a_ubi_activ[$key][$a]['h_ini']=$h_ini;
				$a_ubi_activ[$key][$a]['h_fin']=$h_fin;
				$a_ubi_activ[$key][$a]['num_asistentes']='';
				$a_ubi_activ[$key][$a]['tarifa']= '';
			} else {
				$a_ubi_activ[$key][$a]['svsf']=$ssfsv;
				$a_ubi_activ[$key][$a]['tipo_activ']="$sasistentes $sactividad $snom_tipo";
				$a_ubi_activ[$key][$a]['fechas']="$f_ini - $f_fin";
				$a_ubi_activ[$key][$a]['h_ini']=$h_ini;
				$a_ubi_activ[$key][$a]['h_fin']=$h_fin;
				$a_ubi_activ[$key][$a]['num_asistentes']=$num_asistentes;
				$oTipoTarifa = new TipoTarifa($tarifa);
				$a_ubi_activ[$key][$a]['tarifa']= $oTipoTarifa->getLetra();
			}

			$a_ubi_activ[$key][$a]['ctr_encargados']=''; //inicializar
	
			if ($ver_ctr == 'si' && $oPermCtr->have_perm('ver')) {
				$oGesEncargados = new GestorCentroEncargado();
				$cCtrsEncargados = $oGesEncargados->getCentrosEncargados(array('id_activ'=>$id_activ,'_ordre'=>'num_orden'));

				$i = 0;
				$txt_ctr = '';
				foreach ($cCtrsEncargados as $oCentroEncargado) {
					$i++;
					$id_ubi = $oCentroEncargado->getId_ubi();
					$Centro = new CentroDl($id_ubi);
					$nombre_ctr = $Centro->getNombre_ubi();
					$txt_ctr .= empty($txt_ctr)? $nombre_ctr : "; $nombre_ctr";
					$a_ubi_activ[$key][$a]['ctr_encargados'] = $txt_ctr;
				}
			}
		}
	} else {
		// oficina sin actividades.
		$a_ubi_activ[$key]=1;
	}
}


// ----------------- HTML ---------------------------------------
$aCabeceras = [ _("sv/sf"),
             _("tipo actividad"),
             _("fechas"),
             _("hora inicio"),
             _("hora fin"),
             _("asistentes"),
             _("tarifa"),
             _("centros encargados"),
            ];

$oTabla = new Lista();
$oTabla->setGrupos($aGrupos);
$oTabla->setCabeceras($aCabeceras);
$oTabla->setDatos($a_ubi_activ);
echo $oTabla->listaPaginada();
?>
