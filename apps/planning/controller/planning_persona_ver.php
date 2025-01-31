<?php

use actividadcargos\model\GestorCargoOAsistente;
use actividades\model\entity\GestorActividad;
use actividadestudios\model\entity\GestorActividadAsignaturaDl;
use core\ConfigGlobal;
use core\ViewPhtml;
use personas\model\entity\GestorPersonaAgd;
use personas\model\entity\GestorPersonaDl;
use personas\model\entity\GestorPersonaEx;
use personas\model\entity\GestorPersonaN;
use personas\model\entity\GestorPersonaNax;
use personas\model\entity\GestorPersonaS;
use personas\model\entity\GestorPersonaSSSC;
use planning\domain\Planning;
use planning\domain\PlanningStyle;
use ubis\model\entity\CentroDl;
use web\Hash;
use web\Periodo;
use web\TiposActividades;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");

// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$aid_nom = array();
if (!empty($a_sel)) { //vengo de un checkbox
    // puede ser más de uno
    if (is_array($a_sel) && count($a_sel) > 1) {
        foreach ($a_sel as $nom_sel) {
            $aid_nom[] = $nom_sel;
        }
    } else {
        $aid_nom[] = $a_sel[0];
        // el scroll id es de la página anterior, hay que guardarlo allí
        $oPosicion->addParametro('id_sel', $a_sel, 1);
        $scroll_id = (integer)filter_input(INPUT_POST, 'scroll_id');
        $oPosicion->addParametro('scroll_id', $scroll_id, 1);
    }
}

$Qobj_pau = (string)filter_input(INPUT_POST, 'obj_pau');
$Qmodelo = (integer)filter_input(INPUT_POST, 'modelo');
$goLeyenda = Hash::link(ConfigGlobal::getWeb() . '/apps/zonassacd/controller/leyenda.php?' . http_build_query(array('id_item' => 1)));

$Qyear = (integer)filter_input(INPUT_POST, 'year');
$Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
$Qempiezamin = (string)filter_input(INPUT_POST, 'empiezamin');
$Qempiezamax = (string)filter_input(INPUT_POST, 'empiezamax');

// periodo.
$oPeriodo = new Periodo();
$oPeriodo->setDefaultAny('next');
$oPeriodo->setAny($Qyear);
$oPeriodo->setEmpiezaMin($Qempiezamin);
$oPeriodo->setEmpiezaMax($Qempiezamax);
$oPeriodo->setPeriodo($Qperiodo);

$inicio_iso = $oPeriodo->getF_ini_iso();
$fin_iso = $oPeriodo->getF_fin_iso();
$oIniPlanning = $oPeriodo->getF_ini();
$oFinPlanning = $oPeriodo->getF_fin();
$inicio_local = $oIniPlanning->getFromLocal();

// valores por defecto.
//divisiones por día
$Qdd = 3;
$mod = 0; // 0 u otro valor (1 ver, 2 modificar, 3 eliminar..) el valor se pasa a la página link.
$nueva = 0; // 0 o 1 para asignar una nueva actividad.
// mostrar encabezados arriba y abajo; derecha e izquierda.
if (empty($print)) {
    $doble = 1;
} else {
    $doble = 0;
}
// si es sólo un mes tampoco pongo doble (cabecera y pie)
$interval = $oFinPlanning->diff($oIniPlanning)->format('%m');
if ($interval < 2) $doble = 0;

$Qsacd = '';
$Qctr = '';
$Qtodos_n = '';
$Qtodos_agd = '';
$Qtodos_s = '';

$aWhere = [];
$aOperador = [];
$cabecera_title = ucfirst(_("persona seleccionada"));
$aWhere['id_nom'] = implode(',', $aid_nom);
$aOperador['id_nom'] = 'OR';
switch ($Qobj_pau) {
        case 'PersonaN':
            $GesPersonas = new GestorPersonaN();
            break;
        case 'PersonaAgd':
            $GesPersonas = new GestorPersonaAgd();
            break;
        case 'PersonaNax':
            $GesPersonas = new GestorPersonaNax();
            break;
        case 'PersonaS':
            $GesPersonas = new GestorPersonaS();
            break;
        case 'PersonaSSSC':
            $GesPersonas = new GestorPersonaSSSC();
            break;
        case 'PersonaDl':
            $GesPersonas = new GestorPersonaDl();
            break;
        case 'PersonaEx':
            $GesPersonas = new GestorPersonaEx();
            break;
        default:
            $GesPersonas = new GestorPersonaDl();
    }
$cPersonas = $GesPersonas->getPersonas($aWhere, $aOperador);

$aGoBackComun = array(
    'modelo' => $Qmodelo,
    'year' => $Qyear,
    'periodo' => $Qperiodo,
    'empiezamax' => $Qempiezamax,
    'empiezamin' => $Qempiezamin,
);

$GesActividades = new GestorActividad();
$sCdc = '';
$sin_activ = 0;
$Qid_ubi = '';
$GesActividadAsignaturas = new GestorActividadAsignaturaDl();
$aWhere = array('f_ini' => "'$inicio_iso','$fin_iso'");
$aOperador = array('f_ini' => 'BETWEEN');
$GesActividadAsignaturas->getActividadAsignaturas($aWhere, $aOperador);
//por cada persona busco las actividades.
$p = 0;
$persona = [];
$a_actividades = [];
$a_actividades2 = [];
foreach ($cPersonas as $oPersona) {
    $aActivPersona = array();
    $id_nom = $oPersona->getId_nom();
    $nombre = $oPersona->getPrefApellidosNombre();

    if (!empty($buscar_ctr)) {
        $id_ubi = $oPersona->getId_ctr();
        if (empty($id_ubi)) {
            $nombre_ubi = "centro?";
        } elseif (!in_array($id_ubi, $aListaCtr)) {
            $oCentro = new CentroDl($id_ubi);
            $nombre_ubi = $oCentro->getNombre_ubi();
            $aListaCtr[$id_ubi] = $nombre_ubi;
        } else {
            $nombre_ubi = $aListaCtr[$id_ubi];
        }
        $persona[$p] = "p#$id_nom#$nombre#$nombre_ubi";
    } else {
        $persona[$p] = "p#$id_nom#$nombre";
    }

    // Seleccionar sólo las del periodo y actuales o terminadas
    $aWhere = array();
    $aOperador = [];
    $aWhere['f_ini'] = "'$fin_iso'";
    $aOperador['f_ini'] = '<=';
    $aWhere['f_fin'] = "'$inicio_iso'";
    $aOperador['f_fin'] = '>=';
    $aWhere['status'] = '2,3';
    $aOperador['status'] = 'BETWEEN';

    if (ConfigGlobal::is_app_installed('actividadcargos')) {
        $GesCargoOAsistente = new GestorCargoOAsistente();
        $cCargoOAsistente = $GesCargoOAsistente->getCargoOAsistente($id_nom, $aWhere, $aOperador);
    } else {
        //$oGesAsistentes = new asistentes\GestorActividadCargo();
        echo "ja veurem...";
    }
    foreach ($cCargoOAsistente as $oCargoOAsistente) {
        $id_activ = $oCargoOAsistente->getId_activ();
        $propio = $oCargoOAsistente->getPropio();

        $aWhere['id_activ'] = $id_activ;
        $cActividades = $GesActividades->getActividades($aWhere, $aOperador);
        if (is_array($cActividades) && count($cActividades) == 0) continue;

        $oActividad = $cActividades[0]; // sólo debería haber una.
        $id_activ = $oActividad->getId_activ();
        $id_tipo_activ = $oActividad->getId_tipo_activ();
        $oF_ini = $oActividad->getF_ini();
        $h_ini = $oActividad->getH_ini();
        $oF_fin = $oActividad->getF_fin();
        $h_fin = $oActividad->getH_fin();
        $dl_org = $oActividad->getDl_org();
        $nom_activ = $oActividad->getNom_activ();

        $css = PlanningStyle::clase($id_tipo_activ, $propio, '', $oActividad->getStatus());

        $oTipoActividad = new TiposActividades($id_tipo_activ);
        $ssfsv = $oTipoActividad->getSfsvText();

        //para el caso de que la actividad comience antes
        //del periodo de inicio obligo a que tome una hora de inicio
        //en el entorno de las primeras del día (a efectos del planning
        //ya es suficiente con la 1:16 de la madrugada)
        if ($oIniPlanning > $oF_ini) {
            $ini = $inicio_local;
            $hini = "1:16";
        } else {
            $ini = (string)$oF_ini->getFromLocal();
            $hini = (string)$h_ini;
        }
        $fi = (string)$oF_fin->getFromLocal();
        $hfi = (string)$h_fin;

        // mirar permisos.
        if (ConfigGlobal::is_app_installed('procesos')) {
            $_SESSION['oPermActividades']->setActividad($id_activ, $id_tipo_activ, $dl_org);
            $oPermActiv = $_SESSION['oPermActividades']->getPermisoActual('datos');

            if ($oPermActiv->have_perm_activ('ocupado') === false) continue; // no tiene permisos ni para ver.
            if ($oPermActiv->have_perm_activ('ver') === false) { // sólo puede ver que està ocupado
                $nom_curt = $ssfsv;
                $nom_llarg = "$ssfsv ($ini-$fi)";
            } else {
                $nom_curt = $oTipoActividad->getAsistentesText() . " " . $oTipoActividad->getActividadText();
                $nom_llarg = $nom_activ;
            }
        } else {
            $nom_curt = $oTipoActividad->getAsistentesText() . " " . $oTipoActividad->getActividadText();
            $nom_llarg = $nom_activ;
        }

        $aActivPersona[] = array(
            'nom_curt' => $nom_curt,
            'nom_llarg' => $nom_llarg,
            'f_ini' => $ini,
            'h_ini' => $hini,
            'f_fi' => $fi,
            'h_fi' => $hfi,
            'id_tipo_activ' => $id_tipo_activ,
            'pagina' => '',
            'id_activ' => $id_activ,
            'propio' => $propio,
            'css' => $css,
        );
    }
    // En los profesores, añado las clases del stgr en actividades
    /*
     $cAsignaturas = $GesActividadAsignaturas->getActividadAsignaturasProfesor($id_nom);
     if ($cAsignaturas !== false) {
     foreach ($cAsignaturas as $oActividadAsignatura) {
     $id_activ = $oActividadAsignatura->getId_activ();
     $oActividad = new actividades\Actividad($id_activ);
     $nom_activ = $oActividad->getNom_activ();

     $f_ini = $oActividadAsignatura->getF_ini()->getFromLocal();
     $f_fin = $oActividadAsignatura->getF_fin()->getFromLocal();

     $nom_curt = _("clases stgr");
     $nom_llarg = $nom_curt." "._("en")." ".$nom_activ;
     $aActivPersona[]=array(
     'nom_curt'=>$nom_curt,
     'nom_llarg'=>$nom_llarg,
     'f_ini'=>$f_ini,
     'h_ini'=>'',
     'f_fi'=>$f_fin,
     'h_fi'=>'',
     'id_tipo_activ'=>'',
     'pagina'=>'',
     'id_activ'=>$id_activ,
     'propio'=>''
     );

     }
     }
     */
    if (!empty($buscar_ctr)) {
        $a_actividades2[$nombre_ubi][] = array($persona[$p] => $aActivPersona);
    } else {
        $a_actividades[] = array($persona[$p] => $aActivPersona);
    }
    $p++;

    $aGoBack1 = array(
        'sacd' => $Qsacd,
        'ctr' => $Qctr,
        'todos_n' => $Qtodos_n,
        'todos_agd' => $Qtodos_agd,
        'todos_s' => $Qtodos_s,
        'id_ubi' => $Qid_ubi,
    );
}

$aGoBack = array_merge($aGoBackComun, $aGoBack1);
$oPosicion->setParametros($aGoBack, 1);


$goLeyenda = Hash::link(ConfigGlobal::getWeb() . '/apps/planning/controller/leyenda.php?' . http_build_query(array('id_item' => 1)));
$Qmodelo = (integer)filter_input(INPUT_POST, 'modelo');
switch ($Qmodelo) {
    case 2:
        $print = 1;
    case 1:
        include_once(ConfigGlobal::$dir_estilos . '/calendario.css.php');
        //include_once('apps/web/calendario.php');
        break;
    case 3:
        include_once(ConfigGlobal::$dir_estilos . '/calendario_grid.css.php');
        include_once('apps/web/calendario_grid.php');
        break;
}
// para los estilos. Las variables están en la página css.
$oPlanning = new Planning();
$oPlanning->setColorColumnaUno($colorColumnaUno);
$oPlanning->setColorColumnaDos($colorColumnaDos);
$oPlanning->setTable_border($table_border);

$oPlanning->setDd($Qdd);
$oPlanning->setInicio($oIniPlanning);
$oPlanning->setFin($oFinPlanning);
$oPlanning->setActividades($a_actividades);
$oPlanning->setMod($mod);
$oPlanning->setNueva($nueva);
$oPlanning->setDoble($doble);


$a_campos = ['oPosicion' => $oPosicion,
    'oPlanning' => $oPlanning,
    'goLeyenda' => $goLeyenda,
    'cabecera_title' => $cabecera_title,
    'a_actividades2' => $a_actividades2,
];

$oView = new ViewPhtml('planning/controller');
$oView->renderizar('planning_persona_ver.phtml', $a_campos);
