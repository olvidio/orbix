<?php
/**
 * Esta página muestra una tabla con las actividades que cumplen con la condicion.
 * He quitado la posibilidad de buscar por sacd i por ctr. Quedan las opciones:
 *
 * @param    $que
 *            $status por defecto = 2
 *            $id_tipo_activ
 *            $id_ubi
 *            $periodo
 *            $inicio
 *            $fin
 *            $year
 *            $dl_org
 *            $empiezamin por defecto = 15-oct
 *            $empiezamax por defecto = 15-jun
 *
 * Si el resultado es más de 200, pregunta si quieres seguir.
 *
 * @package    delegacion
 * @subpackage    actividades
 * @author    Daniel Serrabou
 * @since        15/5/02.
 * @ajax        23/8/2007.
 * @last        23/4/2012.
 *
 */

// INICIO Cabecera global de URL de controlador *********************************
use actividadcargos\model\entity\GestorActividadCargo;
use actividades\model\entity\ActividadAll;
use actividades\model\entity\GestorActividad;
use actividadescentro\model\entity\GestorCentroEncargado;
use core\ConfigGlobal;
use core\ViewPhtml;
use permisos\model\PermisosActividadesTrue;
use src\usuarios\application\repositories\PreferenciaRepository;
use ubis\model\entity\Ubi;
use web\Hash;
use web\Lista;
use web\Periodo;
use web\PeriodoQue;
use web\TiposActividades;
use function core\strtoupper_dlb;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

// Declarción de variables ******************************************************
$num_max_actividades = 200;

$mi_sfsv = ConfigGlobal::mi_sfsv();

$oPosicion->recordar();

$Qcontinuar = (string)filter_input(INPUT_POST, 'continuar');
// Sólo sirve para esta pagina: importar, publicar, duplicar
$QGstack = (integer)filter_input(INPUT_POST, 'Gstack');
if (isset($_POST['stack'])) {
    $stack = filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
} else {
    $stack = '';
}

//Si vengo de vuelta con el parámetro 'continuar', los datos no están en el POST,
// sino en $Posicion. Le paso la referecia del stack donde está la información.
if (!empty($Qcontinuar) && $Qcontinuar === 'si' && ($QGstack != '')) {
    $oPosicion->goStack($QGstack);
    $Qque = $oPosicion->getParametro('que');
    $Qstatus = $oPosicion->getParametro('status');
    $Qid_tipo_activ = $oPosicion->getParametro('id_tipo_activ');
    $Qfiltro_lugar = $oPosicion->getParametro('filtro_lugar');
    $Qid_ubi = $oPosicion->getParametro('id_ubi');
    $Qperiodo = $oPosicion->getParametro('periodo');
    $Qyear = $oPosicion->getParametro('year');
    $Qdl_org = $oPosicion->getParametro('dl_org');
    $Qempiezamin = $oPosicion->getParametro('empiezamin');
    $Qempiezamax = $oPosicion->getParametro('empiezamax');
    $Qid_sel = $oPosicion->getParametro('id_sel');
    $Qscroll_id = $oPosicion->getParametro('scroll_id');
    $oPosicion->olvidar($QGstack); //limpio todos los estados hacia delante.
} else { //si vengo de vuelta y tengo los parametros en el $_POST
    $Qid_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    $Qscroll_id = (string)filter_input(INPUT_POST, 'scroll_id');
    //Si vengo por medio de Posicion, borro la última
    if ($stack != '') {
        // No me sirve el de global_object, sino el de la session
        $oPosicion2 = new web\Posicion();
        if ($oPosicion2->goStack($stack)) { // devuelve false si no puede ir
            $Qid_sel = $oPosicion2->getParametro('id_sel');
            $Qscroll_id = $oPosicion2->getParametro('scroll_id');
            $oPosicion2->olvidar($stack);
        }
    }
    $Qque = (string)filter_input(INPUT_POST, 'que');
    $Qstatus = (integer)filter_input(INPUT_POST, 'status');
    $Qtipo_activ_sg = (string)filter_input(INPUT_POST, 'tipo_activ_sg');
    $Qfiltro_lugar = (string)filter_input(INPUT_POST, 'filtro_lugar');
    $Qid_ubi = (integer)filter_input(INPUT_POST, 'id_ubi');
    $Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
    $Qyear = (string)filter_input(INPUT_POST, 'year');
    $Qdl_org = (string)filter_input(INPUT_POST, 'dl_org');
    $Qempiezamin = (string)filter_input(INPUT_POST, 'empiezamin');
    $Qempiezamax = (string)filter_input(INPUT_POST, 'empiezamax');

    // valores por defecto
    if (empty($Qperiodo)) {
        switch ($Qtipo_activ_sg) {
            case 'crt':
                $Qperiodo = 'curso_crt';
                break;
            case 'cv':
                $Qperiodo = 'curso_ca';
                break;
        }
    }

    $Qstatus = empty($Qstatus) ? ActividadAll::STATUS_ACTUAL : $Qstatus;

    $aGoBack = array(
        'que' => $Qque,
        'tipo_activ_sg' => $Qtipo_activ_sg,
        'id_ubi' => $Qid_ubi,
        'periodo' => $Qperiodo,
        'year' => $Qyear,
        'dl_org' => $Qdl_org,
        'status' => $Qstatus,
        'empiezamin' => $Qempiezamin,
        'empiezamax' => $Qempiezamax,
    );
    $oPosicion->setParametros($aGoBack, 1);
}

// Condiciones de búsqueda.
$aWhere = [];
$aOperador = [];
// Status
if ($Qstatus != 5) {
    $aWhere['status'] = $Qstatus;
}
// Id tipo actividad
if (empty($Qtipo_activ_sg)) {
    $Qtipo_activ_sg = 'crt';
}
switch ($Qtipo_activ_sg) {
    case 'crt':
        $Qid_tipo_activ = '1[45]1';
        break;
    case 'cv':
        $Qid_tipo_activ = '1[45]3';
        break;
}
$aWhere['id_tipo_activ'] = "^$Qid_tipo_activ";
$aOperador['id_tipo_activ'] = '~';

// Lugar
if (!empty($Qid_ubi)) {
    $aWhere['id_ubi'] = $Qid_ubi;
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
if (!empty($Qperiodo) && $Qperiodo === 'desdeHoy') {
    $aWhere['f_fin'] = "'$inicioIso','$finIso'";
    $aOperador['f_fin'] = 'BETWEEN';
} else {
    $aWhere['f_ini'] = "'$inicioIso','$finIso'";
    $aOperador['f_ini'] = 'BETWEEN';
}

// dl Organizadora.
if (!empty($Qdl_org)) {
    $aWhere['dl_org'] = $Qdl_org;
}

$GesActividades = new GestorActividad();

$a_botones = array(
    array('txt' => _('cargos'), 'click' => "jsForm.mandar(\"#seleccionados\",\"carg\")"),
    array('txt' => _('asistentes'), 'click' => "jsForm.mandar(\"#seleccionados\",\"asis\")"),
    array('txt' => _('lista'), 'click' => "jsForm.mandar(\"#seleccionados\",\"list\")"),
    array('txt' => _('ctrs org'), 'click' => "jsForm.mandar(\"#seleccionados\",\"ctrs\")"),
);

$a_cabeceras = [];
$a_cabeceras[] = array('name' => _("inicio"), 'width' => 40, 'class' => 'fecha');
$a_cabeceras[] = array('name' => _("fin"), 'width' => 40, 'class' => 'fecha');
$a_cabeceras[] = array('name' => _("sf"), 'width' => 40);
$a_cabeceras[] = array('name' => ucfirst(_("tipo")), 'width' => 30);
$a_cabeceras[] = array('name' => ucfirst(_("asist.")), 'width' => 30);
$a_cabeceras[] = ucfirst(_("lugar"));
$a_cabeceras[] = ucfirst(_("ctrs"));
$a_cabeceras[] = ucfirst(_("sacd"));
$a_cabeceras[] = ucfirst(_("precio"));

$aWhere['_ordre'] = 'f_ini';
$cActividades = $GesActividades->getActividades($aWhere, $aOperador);
$num_activ = count($cActividades);
if ($num_activ > $num_max_actividades && empty($Qcontinuar)) {
    $go_avant = Hash::link(ConfigGlobal::getWeb() . '/apps/actividades/controller/actividad_select.php?' . http_build_query(array('continuar' => 'si', 'stack' => $oPosicion->getStack())));
    $go_atras = Hash::link(ConfigGlobal::getWeb() . '/apps/actividades/controller/actividad_que.php?' . http_build_query(array('stack' => $oPosicion->getStack())));
    echo "<h2>" . sprintf(_("son %s actividades a mostrar. ¿Seguro que quiere continuar?."), $num_activ) . '</h2>';
    echo "<input type='button' onclick=fnjs_update_div('#main','" . $go_avant . "') value=" . _("continuar") . ">";
    echo "<input type='button' onclick=fnjs_update_div('#main','" . $go_atras . "') value=" . _("volver") . ">";
    die();
}

$i = 0;
$sin = 0;
$a_valores = [];
$sPrefs = '';
$id_usuario = ConfigGlobal::mi_id_usuario();
$tipo = 'tabla_presentacion';
$PreferenciaRepository = new PreferenciaRepository();
$oPreferencia = $PreferenciaRepository->findById($id_usuario, $tipo);
if ($oPreferencia !== null) {
    $sPrefs = $oPreferencia->getPreferencia();
}
foreach ($cActividades as $oActividad) {
    $id_activ = $oActividad->getId_activ();
    $id_tipo_activ = $oActividad->getId_tipo_activ();
    $nom_activ = $oActividad->getNom_activ();
    $dl_org = $oActividad->getDl_org();
    $id_ubi = $oActividad->getId_ubi();
    $lugar_esp = $oActividad->getLugar_esp();
    $f_ini = $oActividad->getF_ini()->getFromLocal();
    $f_fin = $oActividad->getF_fin()->getFromLocal();
    $h_ini = $oActividad->getH_ini();
    $h_fin = $oActividad->getH_fin();
    $precio = $oActividad->getPrecio();
    $observ = $oActividad->getObserv();

    // mirar permisos.
    if (ConfigGlobal::is_app_installed('procesos')) {
        $_SESSION['oPermActividades']->setActividad($id_activ, $id_tipo_activ, $dl_org);
        $oPermActiv = $_SESSION['oPermActividades']->getPermisoActual('datos');
        $oPermSacd = $_SESSION['oPermActividades']->getPermisoActual('sacd');
    } else {
        $oPermActividades = new PermisosActividadesTrue(ConfigGlobal::mi_id_usuario());
        $oPermActiv = $oPermActividades->getPermisoActual('datos');
        $oPermSacd = $oPermActividades->getPermisoActual('sacd');
    }
    $i++;

    $oTipoActividad = new TiposActividades($id_tipo_activ);
    $isfsv = $oTipoActividad->getSfsvId();
    // para ver el nombre en caso de la otra sección
    if ($mi_sfsv != $isfsv && !($_SESSION['oPerm']->have_perm_oficina('des'))) {
        $ssfsv = $oTipoActividad->getSfsvText();
        $sactividad = $oTipoActividad->getActividadText();
        $nom_activ = "$ssfsv $sactividad";
    }

    $ssfsv = $oTipoActividad->getSfsvText();
    $sasistentes = $oTipoActividad->getAsistentesText();
    $sactividad = $oTipoActividad->getActividadText();
    $nom_tipo = $oTipoActividad->getNom_tipoText();
    if (ConfigGlobal::is_app_installed('procesos') && $oPermActiv->have_perm_activ('ocupado') === false) {
        $sin++;
        continue;
    } // no tiene permisos ni para ver.
    if (ConfigGlobal::is_app_installed('procesos') && $oPermActiv->have_perm_activ('ver') === false) { // sólo puede ver que està ocupado
        $a_valores[$i]['sel'] = '';
        $a_valores[$i][1] = sprintf(_('ocupado %s (%s-%s)'), $ssfsv, $f_ini, $f_fin);
        //$a_valores[$i][1]= array( 'ira'=>'x', 'valor'=>'ocupado');
        $a_valores[$i][2] = '';
        $a_valores[$i][3] = '';
        $a_valores[$i][4] = '';
        $a_valores[$i][5] = '';
        $a_valores[$i][6] = '';
        $a_valores[$i][7] = '';
        $a_valores[$i][8] = '';
        $a_valores[$i][9] = '';

    } else {
        // ubi
        if (!empty($id_ubi) && $id_ubi != 1) {
            $oCasa = Ubi::newUbi($id_ubi);
            $nombre_ubi = $oCasa->getNombre_ubi();
        } else {
            if ($id_ubi == 1 && $lugar_esp) $nombre_ubi = $lugar_esp;
            if (!$id_ubi && !$lugar_esp) $nombre_ubi = _("sin determinar");
        }

        // sacd
        $sacds = "";
        if (ConfigGlobal::is_app_installed('actividadessacd')) {
            if ($oPermSacd->have_perm_action('ver') === true) { // sólo si tiene permiso
                $gesCargosActividad = new GestorActividadCargo();
                foreach ($gesCargosActividad->getActividadSacds($id_activ) as $oPersona) {
                    $sacds .= $oPersona->getPrefApellidosNombre() . "# "; // la coma la utilizo como separador de apellidos, nombre.
                }
                $sacds = substr($sacds, 0, -2);
            }
        }
        //ctrs encargados.
        $ctrs = "";
        if (ConfigGlobal::is_app_installed('actividadescentro')) {
            $oEnc = new GestorCentroEncargado();
            $n = 0;
            foreach ($oEnc->getCentrosEncargadosActividad($id_activ) as $oEncargado) {
                $n++;
                $ctrs .= $oEncargado->getNombre_ubi() . ", ";
            }
            $ctrs = (!empty($n)) ? substr($ctrs, 0, -2) : '';
        }

        //coincidente con sf.
        $coincide = $GesActividades->getCoincidencia($oActividad, 'bool');
        $con = ($coincide) ? '*' : '';

        $a_valores[$i]['sel'] = "$id_activ#$nom_activ";
        $a_valores[$i][1] = $f_ini;
        $a_valores[$i][2] = $f_fin;
        $a_valores[$i][3] = $con;
        $a_valores[$i][4] = $sactividad; //ctr
        $a_valores[$i][5] = $sasistentes; //asist
        $a_valores[$i][6] = $nombre_ubi; //asist
        $a_valores[$i][7] = $ctrs;
        $a_valores[$i][8] = $sacds;
        $a_valores[$i][9] = $precio;
    }
}
$num = $i;
if (!empty($a_valores)) {
    if (isset($Qid_sel) && !empty($Qid_sel)) {
        $a_valores['select'] = $Qid_sel;
    }
    if (isset($Qscroll_id) && !empty($Qscroll_id)) {
        $a_valores['scroll_id'] = $Qscroll_id;
    }
}

$aOpciones = array(
    'tot_any' => _('todo el año'),
    'trimestre_1' => _('primer trimestre'),
    'trimestre_2' => _('segundo trimestre'),
    'trimestre_3' => _('tercer trimestre'),
    'trimestre_4' => _('cuarto trimestre'),
    'separador' => '---------',
    'curso_ca' => _('curso ca'),
    'curso_crt' => _('curso crt'),
    'separador1' => '---------',
    'otro' => _('otro')
);

$oFormP = new PeriodoQue();
$oFormP->setFormName('modifica');
$oFormP->setAntes(strtoupper_dlb(_("periodo")));
$oFormP->setPosiblesPeriodos($aOpciones);
$oFormP->setDesplPeriodosOpcion_sel($Qperiodo);
$oFormP->setDesplAnysOpcion_sel($Qyear);
$oFormP->setEmpiezaMin($Qempiezamin);
$oFormP->setEmpiezaMax($Qempiezamax);

$oHash = new Hash();
$oHash->setUrl('apps/actividades/controller/lista_actividades_sg.php');
$a_camposHidden = array(
    'que' => $Qque,
    'tipo_activ_sg' => $Qtipo_activ_sg,
    'id_ubi' => $Qid_ubi,
    'year' => $Qyear,
    'dl_org' => $Qdl_org,
    'status' => $Qstatus,
    'filtro_lugar' => $Qfiltro_lugar,
);
$oHash->setArraycamposHidden($a_camposHidden);
$oHash->setCamposNo('modo!id_tipo_activ!id_ubi!periodo!year!dl_org!status!empiezamin!empiezamax!filtro_lugar');

$oHashSel = new Hash();
$oHashSel->setCamposForm('!sel!mod!queSel');
$oHashSel->setcamposNo('continuar!scroll_id');
$a_camposHiddenSel = array(
    'pau' => 'a',
    'permiso' => '3',
    'tabla' => 'a_actividades',
    'tabla_pau' => 'a_actividades',
    'Gstack' => $oPosicion->getStack(),
);
$oHashSel->setArraycamposHidden($a_camposHiddenSel);

$oTabla = new Lista();
$oTabla->setId_tabla('actividad_select');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);

$oTabla = new Lista();
$oTabla->setId_tabla('lista_actividades_sg');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);

$result_busqueda = sprintf(_("%s actividades encontradas (%s sin permiso)"), $num, $sin);

$a_campos = ['oPosicion' => $oPosicion,
    'oFormP' => $oFormP,
    'oHash' => $oHash,
    'oHashSel' => $oHashSel,
    'Qid_tipo_activ' => $Qid_tipo_activ,
    'que' => $Qque,
    'oTabla' => $oTabla,
    'result_busqueda' => $result_busqueda,
];


$oView = new ViewPhtml('actividades\controller');
$oView->renderizar('lista_actividades_sg.phtml', $a_campos);
