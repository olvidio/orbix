<?php
use actividadcargos\model\entity\GestorActividadCargo;
use actividadcargos\model\entity\GestorCargo;
use actividades\model\entity\Actividad;
use actividadescentro\model\entity\GestorCentroEncargado;
use actividadessacd\model\ActividadesSacdFunciones;
use core\ConfigGlobal;
use personas\model\entity\GestorPersonaSSSC;
use personas\model\entity\GestorPersonaSacd;
use personas\model\entity\PersonaSacd;
use ubis\model\entity\Ubi;
use usuarios\model\entity\Usuario;
use web\DateTimeLocal;
use web\Periodo;
use web\TiposActividades;

/**
* Esta página muestra las actividades que tiene que atender un sacd. 
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

/* claves:
 *       'com_sacd'
 *       't_propio'
 *       't_f_ini'
 *       't_f_fin'
 *       't_nombre_ubi'
 *       't_sfsv'
 *       't_actividad'
 *       't_asistentes'
 *       't_encargado'
 *       't_observ'
 *       't_nom_tipo'
 *
 */

$Qque = (string) \filter_input(INPUT_POST, 'que');
$Qid_nom = (integer) \filter_input(INPUT_POST, 'id_nom');

$Qperiodo = (string) \filter_input(INPUT_POST, 'periodo');
$Qyear = (string) \filter_input(INPUT_POST, 'year');
$Qempiezamin = (string) \filter_input(INPUT_POST, 'empiezamin');
$Qempiezamax = (string) \filter_input(INPUT_POST, 'empiezamax');

$oPosicion->recordar();

$oDateLocal = new DateTimeLocal();
$hoy_local = $oDateLocal->getFromLocal('.'); 
// ciudad de la dl
$oActividadesSacdFunciones = new ActividadesSacdFunciones();
$poblacion = $oActividadesSacdFunciones->getLugar_dl();
$lugar_fecha= "$poblacion, $hoy_local";

$oMiUsuario = new Usuario(array('id_usuario'=>ConfigGlobal::mi_id_usuario()));
if ($oMiUsuario->isRole('p-sacd')) {
    $Qid_nom = $oMiUsuario->getId_pau();
    $Qque = 'un_sacd';
}
// valores del id_cargo de tipo_cargo = sacd:
$gesCargos = new GestorCargo();
$aIdCargos_sacd = $gesCargos->getArrayCargosDeTipo('sacd');
			
// Si vengo de la página personas_select.php, sólo quiero ver la lista de un sacd.
if ($Qque == "un_sacd") {
    $a_sel = (array)  \filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    if (!empty($a_sel)) { //vengo de un checkbox
        $Qid_nom = (integer) strtok($a_sel[0],"#");
		$Qid_tabla=strtok("#");
        // el scroll id es de la página anterior, hay que guardarlo allí
        $oPosicion->addParametro('id_sel',$a_sel,1);
        $scroll_id = (integer) \filter_input(INPUT_POST, 'scroll_id');
        $oPosicion->addParametro('scroll_id',$scroll_id,1);
    } else {
        if (empty($Qid_nom)) {
            $Qid_nom = (integer)  \filter_input(INPUT_POST, 'id_nom');
        }
        $Qid_tabla = (integer)  \filter_input(INPUT_POST, 'id_tabla');
    }
	// periodo por defecto:
    if (empty($Qperiodo)) {
        $Qperiodo = 'curso_crt';
        $Qyear = date('Y');
    }
}

// periodo.
$oPeriodo = new Periodo();
$oPeriodo->setDefaultAny('next');
$oPeriodo->setAny($Qyear);
$oPeriodo->setEmpiezaMin($Qempiezamin);
$oPeriodo->setEmpiezaMax($Qempiezamax);
$oPeriodo->setPeriodo($Qperiodo);

$inicioIso = $oPeriodo->getF_ini_iso();
$finIso = $oPeriodo->getF_fin_iso();

// los sacd
$mi_dele = ConfigGlobal::mi_delef();
if (empty($Qque)) $Qque = "nagd";
$aWhereP = [];
switch ($Qque) {
	case "nagd":
		$aWhereP['situacion']='A';
		$aWhereP['sacd']='t';
		$aWhereP['dl']=$mi_dele;
		$aWhereP['_ordre']='apellido1,apellido2,nom';
		$GesPersonas = new GestorPersonaSacd();
		$cPersonas = $GesPersonas->getPersonas($aWhereP);
		break;
	case "sssc":
		$aWhereP['situacion']='A';
		$aWhereP['sacd']='t';
		$aWhereP['dl']=$mi_dele;
		$aWhereP['_ordre']='apellido1,apellido2,nom';
		$GesPersonas = new GestorPersonaSSSC();
		$cPersonas = $GesPersonas->getPersonasSSSC($aWhereP);
		break;
	case "un_sacd":
		$oPersona = new PersonaSacd($Qid_nom);
		$cPersonas = array($oPersona);
		break;
}

// llista d'activitats posibles en el periode.
if (empty($inicioIso) OR empty($finIso)) {
    exit ("<br>"._("falta determinar un periodo"));
}
/*
$aWhere = [];
$aOperador = [];
$aWhere['f_ini'] = "'$inicioIso','$finIso'";
$aOperador['f_ini'] = 'BETWEEN';
$aWhere['status'] = 2;
$aWhere['_ordre']='f_ini';
$GesActividades = new GestorActividad();
$cActividades = $GesActividades->getActividades($aWhere,$aOperador);
$a_id_actividades = [];
$a_actividades = [];
foreach ($cActividades as $oActividad) {
	$id_activ = $oActividad->getId_activ();
	$a_id_actividades[] = $id_activ;
	$a_actividades[$id_activ] = $oActividad;
}
unset($cActividades);
*/

$s=0;
$array_actividades = [];
foreach ($cPersonas as $oPersona) {
	$s++;
	$id_nom=$oPersona->getId_nom();
	$nom_ap=$oPersona->getApellidosNombre();
	$idioma = $oPersona->getLengua();

	$array_actividades[$id_nom]['txt']['com_sacd'] = $oActividadesSacdFunciones->getTraduccion('com_sacd', $idioma);
    $array_actividades[$id_nom]['txt']['t_propio'] = $oActividadesSacdFunciones->getTraduccion('t_propio',$idioma);
    $array_actividades[$id_nom]['txt']['t_f_ini'] = $oActividadesSacdFunciones->getTraduccion('t_f_ini',$idioma);
    $array_actividades[$id_nom]['txt']['t_f_fin'] = $oActividadesSacdFunciones->getTraduccion('t_f_fin',$idioma);
    $array_actividades[$id_nom]['txt']['t_nombre_ubi'] = $oActividadesSacdFunciones->getTraduccion('t_nombre_ubi',$idioma);
    $array_actividades[$id_nom]['txt']['t_sfsv'] = $oActividadesSacdFunciones->getTraduccion('t_sfsv',$idioma);
    $array_actividades[$id_nom]['txt']['t_actividad'] = $oActividadesSacdFunciones->getTraduccion('t_actividad',$idioma);
    $array_actividades[$id_nom]['txt']['t_asistentes'] = $oActividadesSacdFunciones->getTraduccion('t_asistentes',$idioma);
    $array_actividades[$id_nom]['txt']['t_encargado'] = $oActividadesSacdFunciones->getTraduccion('t_encargado',$idioma);
    $array_actividades[$id_nom]['txt']['t_observ'] = $oActividadesSacdFunciones->getTraduccion('t_observ',$idioma);
    $array_actividades[$id_nom]['txt']['t_nom_tipo'] = $oActividadesSacdFunciones->getTraduccion('t_nom_tipo',$idioma);
	
	$array_actividades[$id_nom]['nom_ap']=$nom_ap;
	
	// busco los datos de las actividades 
	$aWhereAct = [];
	$aOperadorAct = [];
	$aWhereAct['f_ini']="'$finIso'";
	$aOperadorAct['f_ini']='<=';
	$aWhereAct['f_fin']="'$inicioIso'";
	$aOperadorAct['f_fin']='>=';
	$aWhereAct['status']='2';
	/*
	 $aWhere = ['id_nom' => $id_nom, 'plaza' => Asistente::PLAZA_PEDIDA];
	 $aOperador = ['plaza' => '>='];
	 */
	$aWhere = ['id_nom' => $id_nom];
	$aOperador = [];
	
	$oGesActividadCargo = new GestorActividadCargo();
	$cAsistentes = $oGesActividadCargo ->getAsistenteCargoDeActividad($aWhere,$aOperador,$aWhereAct,$aOperadorAct);
	
	$ord_activ = [];
	foreach ($cAsistentes as $aAsistente) {
	    $id_activ = $aAsistente['id_activ'];
	    $propio = $aAsistente['propio'];
	    $plaza = $aAsistente['plaza'];
	    $id_cargo = empty($aAsistente['id_cargo'])? '' : $aAsistente['id_cargo'];
	    
        $_SESSION['oPermActividades']->setId_activ($id_activ);
			
        if(core\ConfigGlobal::is_app_installed('procesos')) {
            $permiso_ver = FALSE;
            $oPermActiv = $_SESSION['oPermActividades']->getPermisoActual('datos');
            $oPermSacd = $_SESSION['oPermActividades']->getPermisoActual('sacd');
            $oPermAsisSacd = $_SESSION['oPermActividades']->getPermisoActual('asistentesSacd');
            // para ver la actividad:
            if ($oPermActiv->have_perm_activ('ver') === FALSE) {
                continue;
            }
            // si es solo cargo, tiene propio='f' como sacd de la actividad
            if (!empty($id_cargo)) {
                if ($oPermSacd->have_perm_activ('ver') === TRUE) {
                    $permiso_ver = TRUE;
                }
                //si también asiste. tiene propio = 't'
                if ($propio == 't' && $oPermAsisSacd->have_perm_activ('ver') === TRUE) {
                    $permiso_ver = TRUE;
                }
            } else {
                // sólo asiste
                if ($oPermAsisSacd->have_perm_activ('ver') === TRUE) {
                    $permiso_ver = TRUE;
                }
            }
        } else {
            $permiso_ver = TRUE;
        }
        
        if ($permiso_ver === FALSE) { continue; }
        
        $oActividad = new Actividad($id_activ);
        $id_tipo_activ = $oActividad->getId_tipo_activ();
        $id_ubi = $oActividad->getId_ubi();
        $lugar_esp = $oActividad->getLugar_esp();
        $oF_ini = $oActividad->getF_ini();
        $oF_fin = $oActividad->getF_fin();
        $h_ini = $oActividad->getH_ini();
        $h_fin = $oActividad->getH_fin();
        $observ = $oActividad->getObserv();
        
        $f_ini=$oF_ini->formatRoman();
        $f_fin=$oF_fin->formatRoman();

        if (!empty($h_ini)) {
               $h_ini = preg_replace('/(\d{2}):(\d{2}):(\d{2})/','\1:\2',$h_ini);
               $f_ini.=" ($h_ini)";
        }
        if (!empty($h_fin)) {
               $h_fin = preg_replace('/(\d{2}):(\d{2}):(\d{2})/','\1:\2',$h_fin);
               $f_fin.=" ($h_fin)";
        }

        $oTipoActiv= new TiposActividades($id_tipo_activ);
        $ssfsv=$oTipoActiv->getSfsvText();
        $sasistentes=$oTipoActiv->getAsistentesText();
        $sactividad=$oTipoActiv->getActividadText();
        $snom_tipo=$oTipoActiv->getNom_tipoText();
        // lugar 
        if (empty($lugar_esp)) {
            $oCasa = Ubi::NewUbi($id_ubi);
            $nombre_ubi = $oCasa->getNombre_ubi();
        } else {
            $nombre_ubi = $lugar_esp;
        }

        // ctr que organiza:
        $GesCentroEncargado = new GestorCentroEncargado();
        $ctrs = '';
        foreach($GesCentroEncargado->getCentrosEncargadosActividad($id_activ) as $oCentro) {;
            if (!empty($ctrs)) $ctrs.=", ";
            $ctrs .= $oCentro->getNombre_ubi();
        }

        $cargo='';
        if (!empty($id_cargo) && !array_key_exists($id_cargo, $aIdCargos_sacd)) {
            $cargo='te carrec';
        }
        $array_act=array( "propio" => $propio,
                            "f_ini" => $f_ini,
                            "f_fin" =>		$f_fin, 
                            "nombre_ubi" =>	$nombre_ubi, 
                            "id_activ" 	=>	$id_activ, 
                            "sfsv" 		=>	$ssfsv, 
                            "asistentes" =>	$sasistentes, 
                            "actividad" =>	$sactividad,
                            "nom_tipo" =>	$snom_tipo, 
                            "observ" =>		$observ, 
                            "cargo" =>		$cargo, 
                            "encargado" =>	$ctrs
                        );
        //if (!empty($id_activ)) { $array_actividades[$id_nom]['actividades'][]= $array_act; }
        // para ordenar por fecha_ini
        $f_ord = $oF_ini->format('Ymd');
        // ojo. Si hay más de una actividad que empieza el mismo día, hay que poner algo para distinguirlas: les sumo un dia.
        if (isset($ord_activ) && array_key_exists($f_ord,$ord_activ)) {
            $f_ord++;
            $ord_activ[$f_ord]=$array_act;
        } else {
            $ord_activ[$f_ord]=$array_act;
        }
	}
	if (!empty($ord_activ)) {
		ksort($ord_activ);
		$array_actividades[$id_nom]['actividades']= $ord_activ;
	} else {
		$array_actividades[$id_nom]['actividades']= '';
	}
	$ord_activ=array();
} // fin del while de los sacd

$a_campos = ['oPosicion' => $oPosicion,
    'array_actividades' => $array_actividades,
    'Qque' => $Qque,
    'mi_dele' => $mi_dele,
    'lugar_fecha' => $lugar_fecha,
];

$oView = new core\View('actividadessacd/controller');
echo $oView->render('com_sacd_activ_print.phtml',$a_campos);
