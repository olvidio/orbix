<?php
use actividades\model\entity\GestorActividad;
use actividadescentro\model\entity\GestorCentroEncargado;
use actividadtarifas\model\entity\TipoTarifa;
use core\ConfigGlobal;
use dossiers\model\PermisoDossier;
use permisos\model\PermisosActividadesTrue;
use ubis\model\entity\Casa;
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

function nomUbi($id_ubi) {
    $oCasa = new Casa($id_ubi);
    if (empty($oCasa)) {
        // probar con los ctr.
        $oCasa =  new CentroDl($id_ubi);
    }
    return $oCasa->getNombre_ubi();
}

$oPosicion->recordar();

$Qque = (string) \filter_input(INPUT_POST, 'que');
$Qver_ctr = (string) \filter_input(INPUT_POST, 'ver_ctr');

$Qperiodo = (string) \filter_input(INPUT_POST, 'periodo');
$Qyear = (string) \filter_input(INPUT_POST, 'year');
$Qyeardefault= (string) \filter_input(INPUT_POST, 'yeardefault');
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
/*
 * 22.09.2021 Se quita el filtro de tipo_casa = cdc|cdr, para que salgan todas.
 *		$aWhereCasa['tipo_casa'] = 'cdc|cdr';
 *		$aOperadorCasa['tipo_casa'] = '~';
 */
switch ($Qque) {
	case "lista_cdc":
	    // Esta viene de apps/casas/controller/casa_que.php
        $Qaid_cdc = (array)  \filter_input(INPUT_POST, 'id_cdc', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
		$tipo="casa";
		// una lista de casas (id_ubi).
	    if (!empty($Qaid_cdc)) {
	        $v = "{".implode(', ',$Qaid_cdc)."}";
	        $aWhereCasa['id_ubi'] = $v;
	        $aOperadorCasa['id_ubi'] = 'ANY';
	    }
		break;
	case "c_comunes":
	case "c_comunes_sf":
	case "c_comunes_sv":
		// casas comunes: cdr + dlb + sf +sv
		$tipo="casa";
		$aWhereCasa['tipo_ubi'] = 'cdcdl';
		$aWhereCasa['sv'] = 't';
		$aWhereCasa['sf'] = 't';
		break;
	case "c_todas":
		$tipo="casa";
		$aWhereCasa['tipo_ubi'] = 'cdcdl';
		break;
	case "c_todas_sf":
		// casas de sf
		$tipo="casa";
		$aWhereCasa['tipo_ubi'] = 'cdcdl';
		$aWhereCasa['sf'] = 't';
		break;
	case "c_todas_sv":
		// casas de calendario sv
		$tipo="casa";
		$aWhereCasa['tipo_ubi'] = 'cdcdl';
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
	default:
		$err_switch = sprintf(_("opción no definida en switch en %s, linea %s"), __FILE__, __LINE__);
		exit ($err_switch);
}
// valores por defecto
$Qyeardefault = empty($Qyeardefault)? 'next' : $Qyeardefault;

// periodo.
$oPeriodo = new Periodo();
$oPeriodo->setDefaultAny($Qyeardefault);
$oPeriodo->setAny($Qyear);
$oPeriodo->setEmpiezaMin($Qempiezamin);
$oPeriodo->setEmpiezaMax($Qempiezamax);
$oPeriodo->setPeriodo($Qperiodo);


$inicioIso = $oPeriodo->getF_ini_iso();
$finIso = $oPeriodo->getF_fin_iso();

switch ($tipo) {
	case "casa":
		$GesCasas = new GestorCasaDl();
		$cCasas = $GesCasas->getCasas($aWhereCasa,$aOperadorCasa);
		foreach ($cCasas as $oCasa) {
			$aGrupos[$oCasa->getId_ubi()]=$oCasa->getNombre_ubi();
		}
		break;
	case "oficina": // tipo asistentes
		$oTiposActividades = new TiposActividades();
		$oTiposActividades->setSfSvId($miSfsv);
		if ($mi_of == 'all') {
			$aGrupos = 	$oTiposActividades->getAsistentesPosibles();
		} else {
			$oPermisoOficinas = new PermisoDossier();
			$aGrupos = 	$oTiposActividades->getAsistentesPosibles();
			foreach ($aGrupos as $sasistentes) {
				$oficina = $equivalencias_gm_oficina[$sasistentes]; 
				if (!$oPermisoOficinas->have_perm_oficina($oficina) &&
					($key = array_search($sasistentes, $aGrupos)) !== false)
				{
					unset($aGrupos[$key]);
				}
			}
        }
		break;
	default:
		$err_switch = sprintf(_("opción no definida en switch en %s, linea %s"), __FILE__, __LINE__);
		exit ($err_switch);
}

$a_ubi_activ = array();
foreach (array_keys($aGrupos) as $key) {
    $aWhere = [];
    $aOperador = [];
    $aWhere['f_ini'] = "'$inicioIso','$finIso'";
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
				$aWhere['id_tipo_activ'] = $oTiposActividades->getNom_tipoRegexp();
                $aOperador['id_tipo_activ'] = '~';
                $oGesActiv = new GestorActividad();
                $cActividades = $oGesActiv->getActividades($aWhere,$aOperador);
			}
		break;
		default:
			$err_switch = sprintf(_("opción no definida en switch en %s, linea %s"), __FILE__, __LINE__);
			exit ($err_switch);
	}

	if (is_array($cActividades) && count($cActividades) > 0) {
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

			
			$id_ubi = $oActividad->getId_ubi();
			$nombre_ubi = nomUbi($id_ubi);
			
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

			if (!$oPermActiv->have_perm_action('ocupado')) { continue; } // no tiene permisos ni para ver.
			if (!$oPermActiv->have_perm_action('ver')) { // sólo puede ver que està ocupado
				$a_ubi_activ[$key][$a]['sfsv']=$ssfsv;
				$a_ubi_activ[$key][$a]['tipo_activ']=_("ocupado");
				if ($tipo == 'oficina') {
				    $a_ubi_activ[$key][$a]['cdc']="$nombre_ubi";
				}
				$a_ubi_activ[$key][$a]['fechas']="$f_ini - $f_fin";
				$a_ubi_activ[$key][$a]['h_ini']=$h_ini;
				$a_ubi_activ[$key][$a]['h_fin']=$h_fin;
				$a_ubi_activ[$key][$a]['num_asistentes']='';
				$a_ubi_activ[$key][$a]['tarifa']= '';
			} else {
				$a_ubi_activ[$key][$a]['sfsv']=$ssfsv;
				$a_ubi_activ[$key][$a]['tipo_activ']="$sasistentes $sactividad $snom_tipo";
				if ($tipo == 'oficina') {
				    $a_ubi_activ[$key][$a]['cdc']="$nombre_ubi";
				}
				$a_ubi_activ[$key][$a]['fechas']="$f_ini - $f_fin";
				$a_ubi_activ[$key][$a]['h_ini']=$h_ini;
				$a_ubi_activ[$key][$a]['h_fin']=$h_fin;
				$a_ubi_activ[$key][$a]['num_asistentes']=$num_asistentes;
				$oTipoTarifa = new TipoTarifa($tarifa);
				$a_ubi_activ[$key][$a]['tarifa']= $oTipoTarifa->getLetra();
			}

			$a_ubi_activ[$key][$a]['ctr_encargados']=''; //inicializar
	
			if ($ver_ctr == 'si' && $oPermCtr->have_perm_action('ver')) {
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
switch ($tipo) {
    case 'casa':
        $aCabeceras = [ _("sv/sf"),
                     _("tipo actividad"),
                     _("fechas"),
                     _("hora inicio"),
                     _("hora fin"),
                     _("asistentes"),
                     _("tarifa"),
                     _("centros encargados"),
                    ];
        break;
    case 'oficina':
        $aCabeceras = [ _("sv/sf"),
                     _("tipo actividad"),
                     _("cdc"),
                     _("fechas"),
                     _("hora inicio"),
                     _("hora fin"),
                     _("asistentes"),
                     _("tarifa"),
                     _("centros encargados"),
                    ];
        break;
	default:
		$err_switch = sprintf(_("opción no definida en switch en %s, linea %s"), __FILE__, __LINE__);
		exit ($err_switch);
}

$oTabla = new Lista();
$oTabla->setGrupos($aGrupos);
$oTabla->setCabeceras($aCabeceras);
$oTabla->setDatos($a_ubi_activ);
echo $oTabla->listaPaginada();