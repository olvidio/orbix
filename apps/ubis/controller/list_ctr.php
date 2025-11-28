<?php

use core\ConfigGlobal;
use core\DBPropiedades;
use core\ViewPhtml;
use src\ubis\domain\contracts\CasaDlRepositoryInterface;
use src\ubis\domain\contracts\CasaExRepositoryInterface;
use src\ubis\domain\contracts\CasaRepositoryInterface;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroExRepositoryInterface;
use src\ubis\domain\contracts\CentroRepositoryInterface;
use web\Desplegable;
use web\Hash;
use web\Lista;
use web\Posicion;

/**
 * Página para realizar algunos listados standard de ubis
 *
 *
 *
 * @package    delegacion
 * @subpackage    ubis
 * @author    Josep Companys
 * @since        15/5/02.
 *
 * Llegamos desde menú: "centros y casas" y
 * submenú "listados"
 * Las funciones que podré hacer con los ubis son
 * idénticas a las que realizamos en submenú "buscar"
 *
 * Se tiene en cuenta si es una vuelta de un go_to
 */
// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$miSfsv = ConfigGlobal::mi_sfsv();

//Si vengo por medio de Posicion, borro la última
if (isset($_POST['stack'])) {
    $stack = filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    if ($stack != '') {
        // No me sirve el de global_object, sino el de la session
        $oPosicion2 = new Posicion();
        if ($oPosicion2->goStack($stack)) { // devuelve false si no puede ir
            $obj_pau = $oPosicion2->getParametro('obj_pau');
            $id_ubi = $oPosicion2->getParametro('id_ubi');
            $Qid_sel = $oPosicion2->getParametro('id_sel');
            $Qscroll_id = $oPosicion2->getParametro('scroll_id');
            $oPosicion2->olvidar($stack);
        }
    }
}

$Qque_lista = (string)filter_input(INPUT_POST, 'que_lista');
$Qloc = (string)filter_input(INPUT_POST, 'loc');

if (empty($Qloc)) $Qloc = ConfigGlobal::mi_region_dl();
if (empty($Qque_lista)) $Qque_lista = 'ctr_n';

$aWhere = [];
$aWhere['status'] = 't';
$aWhere['_ordre'] = 'nombre_ubi';
$aOperador = [];

if ($Qloc !== 'ex') {
    $a_reg = explode('-', $Qloc);
    $reg = $a_reg[0];
    if ($miSfsv === 1) {
        $dl = substr($a_reg[1], 0, -1); // quito la v o la f.
    } else {
        $dl = $a_reg[1];
    }
    if ($dl == ConfigGlobal::mi_delef()) {
        $objCentro = 'CentroDl';
        $objCasa = 'CasaDl';
    } else {
        $objCentro = 'Centro';
        $objCasa = 'Casa';
        $aWhere['region'] = $reg;
        if ($dl != 'cr') {
            $aWhere['dl'] = $dl;
        }
    }
    switch ($Qque_lista) {
        /*vamos a por los casos de la tabla u_centros dl*/
        case "todos_ctr_dl":
            $obj = $objCentro;
            $aWhere['_ordre'] = 'tipo_ctr,nombre_ubi';
            break;
        case "ctr_n":
            $obj = $objCentro;
            $aWhere['tipo_ctr'] = '^n';
            $aOperador['tipo_ctr'] = '~';
            break;
        case "ctr_nax":
            $obj = $objCentro;
            $aWhere['tipo_ctr'] = '^x';
            $aOperador['tipo_ctr'] = '~';
            break;
        case "ctr_agd":
            $obj = $objCentro;
            $aWhere['tipo_ctr'] = '^a[^p]*'; // que no sea ap (apeadero).
            $aOperador['tipo_ctr'] = '~';
            break;
        case "ctr_s":
            $obj = $objCentro;
            $aWhere['tipo_ctr'] = '^(sj|sm|s)$';
            $aOperador['tipo_ctr'] = '~';
            break;
        case "ctr_sssc":
            $obj = $objCentro;
            $aWhere['tipo_ctr'] = '^(ss|sss)$';
            $aOperador['tipo_ctr'] = '~';
            break;
        case "oc":
            $obj = $objCentro;
            $aWhere['tipo_ctr'] = '(oc)';
            $aOperador['tipo_ctr'] = '~';
            break;
        case "cgi":
            $obj = $objCentro;
            $aWhere['tipo_ctr'] = '(cgi)';
            $aOperador['tipo_ctr'] = '~';
            break;
        /*vamos a por los casos de la tabla u_cdc_dl*/
        case "cdr_cdc_dl":
            $obj = $objCasa;
            $aWhere['tipo_casa'] = 'cdc|cdr';
            $aOperador['tipo_casa'] = '~';
            switch ($miSfsv) {
                case 1:
                    if (($_SESSION['oPerm']->have_perm_oficina('vcsd')) || ($_SESSION['oPerm']->have_perm_oficina('des'))) {
                    } else {
                        $aWhere['sv'] = 't';
                    }
                    break;
                case 2:
                    $aWhere['sf'] = 't';
                    break;
            }
            break;
        case "otros_cdc":
            $obj = $objCasa;
            $aWhere['tipo_casa'] = 'cdc|cdr|cgi';
            $aOperador['tipo_casa'] = '!~';
            switch ($miSfsv) {
                case 1:
                    if (($_SESSION['oPerm']->have_perm_oficina('vcsd')) || ($_SESSION['oPerm']->have_perm_oficina('des'))) {
                        //$condicion_u="WHERE u.tipo_casa!='cgi' AND u.tipo_casa !='cdc' AND u.tipo_casa !='cdr'";
                    } else {
                        $aWhere['sv'] = 't';
                    }
                    break;
                case 2:
                    $aWhere['sf'] = 't';
                    break;
            }
            break;
        default:
            $obj = 'none';
            break;
    }
}
if ($Qloc === 'ex') {
    switch ($Qque_lista) {
        /*vamos a por los casos de la tabla u_centros_ex*/
        case "ctr_n":
            $obj = 'CentroEx';
            $aWhere['tipo_ctr'] = '^n';
            $aOperador['tipo_ctr'] = '~';
            break;
        case "ctr_nax":
            $obj = 'CentroEx';
            $aWhere['tipo_ctr'] = '^x';
            $aOperador['tipo_ctr'] = '~';
            break;
        case "ctr_agd":
            $obj = 'CentroEx';
            $aWhere['tipo_ctr'] = '^a[^p]*'; // que no sea ap (apeadero).
            $aOperador['tipo_ctr'] = '~';
            break;
        case "ctr_s":
            $obj = 'CentroEx';
            $aWhere['tipo_ctr'] = '^(sj|sm|s)$';
            $aOperador['tipo_ctr'] = '~';
            break;
        case "todos_ctr_ex":
            $obj = 'CentroEx';
            break;
        case "dl":
            $obj = 'CentroEx';
            $aWhere['tipo_ctr'] = 'dl';
            break;
        case "cr":
            $obj = 'CentroEx';
            $aWhere['tipo_ctr'] = 'cr';
            break;
        case "cdc_ex":
            $obj = 'CasaEx';
            break;
        default:
            $obj = 'none';
            break;
    }
}

switch ($obj) {
    case 'Centro':
        $CentroRepository = $GLOBALS['container']->get(CentroRepositoryInterface::class);
        $cUbis = $CentroRepository->getCentros($aWhere, $aOperador);
        break;
    case 'CentroDl':
        $CentroRepository = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
        $cUbis = $CentroRepository->getCentros($aWhere, $aOperador);
        break;
    case 'CentroEx':
        $CentroRepository = $GLOBALS['container']->get(CentroExRepositoryInterface::class);
        $cUbis = $CentroRepository->getCentros($aWhere, $aOperador);
        break;
    case 'Casa':
        $CasaRepository = $GLOBALS['container']->get(CasaRepositoryInterface::class);
        $cUbis = $CasaRepository->getCasas($aWhere, $aOperador);
        break;
    case 'CasaDl':
        $CasaRepository = $GLOBALS['container']->get(CasaDlRepositoryInterface::class);
        $cUbis = $CasaRepository->getCasas($aWhere, $aOperador);
        break;
    case 'CasaEx':
        $CasaRepository = $GLOBALS['container']->get(CasaExRepositoryInterface::class);
        $cUbis = $CasaRepository->getCasas($aWhere, $aOperador);
        break;
    case 'none':
        $cUbis = [];
        break;
}

$aGoBack = array(
    'loc' => $Qloc,
    'que_lista' => $Qque_lista,
);
$oPosicion->setParametros($aGoBack);
$oPosicion->recordar();

$a_botones = [['txt' => _("modificar"), 'click' => "fnjs_modificar(this.form)"]];
if ($_SESSION['oPerm']->have_perm_oficina('admin_sv')) {
    $a_botones[] = ['txt' => _("trasladar"), 'click' => "fnjs_ver_dl()"];
}

$a_cabeceras = [];
$a_cabeceras[] = array('name' => ucfirst(_("centro")), 'formatter' => 'clickFormatter');
$a_cabeceras[] = ucfirst(_("región"));
$a_cabeceras[] = ucfirst(_("tipo ctr o casa"));
$a_cabeceras[] = ucfirst(_("dirección"));
$a_cabeceras[] = ucfirst(_("cp"));
$a_cabeceras[] = ucfirst(_("ciudad"));
$a_cabeceras[] = ucfirst(_("teléfono"));

$i = 0;
$a_valores = [];
if (isset($Qid_sel) && !empty($Qid_sel)) {
    $a_valores['select'] = $Qid_sel;
}
if (isset($Qscroll_id) && !empty($Qscroll_id)) {
    $a_valores['scroll_id'] = $Qscroll_id;
}
foreach ($cUbis as $oCentro) {
    $i++;
    $region = $oCentro->getRegion();
    $id_ubi = $oCentro->getId_ubi();
    $pagina = Hash::link(ConfigGlobal::getWeb() . '/apps/ubis/controller/home_ubis.php?' . http_build_query(array('pau' => 'u', 'id_ubi' => $id_ubi)));
    $ctr = $oCentro->getNombre_ubi();

    if (strstr($obj, 'Centro') !== false) {
        $tipo = $oCentro->getTipo_ctr();
    }
    if (strstr($obj, 'Casa') !== false) {
        $tipo = $oCentro->getTipo_casa();
    }
    $cDirecciones = $oCentro->getDirecciones();

    $poblacion = '';
    $pais = '';
    $direccion = '';
    $c_p = '';
    if (is_array($cDirecciones) & !empty($cDirecciones)) {
        $d = 0;
        foreach ($cDirecciones as $oDireccion) {
            $d++;
            if ($d > 1) {
                $poblacion .= '<br>';
                $pais .= '<br>';
                $direccion .= '<br>';
                $c_p .= '<br>';
            }
            $poblacion .= $oDireccion->getPoblacion();
            $pais .= $oDireccion->getPais();
            $direccion .= $oDireccion->getDireccion();
            $c_p .= $oDireccion->getC_p();
        }
    }

    $a_valores[$i]['sel'] = "$id_ubi";
    $a_valores[$i][1] = array('ira' => $pagina, 'valor' => $ctr);
    $a_valores[$i][2] = $region;
    $a_valores[$i][3] = $tipo;
    $a_valores[$i][4] = $direccion;
    $a_valores[$i][5] = $c_p;

    /*para los ubis cuyo país no sea España se listará entre paréntesis
    tras la población*/
    if (strstr($obj, 'Ex') === false) {
        $a_valores[$i][6] = $poblacion;
    } else {
        if ($pais == "España") {
            $a_valores[$i][6] = $poblacion;
        } else {
            $a_valores[$i][6] = "$poblacion ($pais)";
        }
    }
    $tipo_teleco = "telf";
    /*ninguna restricción para teléfonos. De momento interesan todos*/
    $desc_teleco = "";
    $separador = " ";
    $tels = $oCentro->getTeleco($tipo_teleco, $desc_teleco, $separador);

    $a_valores[$i][7] = $tels;
}

$oDesplDl = new Desplegable();
$oDesplDl->setNombre('loc');
$oDesplDl->setAction('fnjs_actualizar()');
$oDBPropiedades = new DBPropiedades();
$aOpcionesDl = $oDBPropiedades->array_posibles_esquemas();
$aOpcionesDl['ex'] = _("otras");
$oDesplDl->setOpciones($aOpcionesDl);
$oDesplDl->setOpcion_sel($Qloc);
$oDesplDl->setAction('fnjs_limpiar()');


$oDesplLista = new Desplegable();
$oDesplLista->setNombre('que_lista');
$oDesplLista->setAction('fnjs_actualizar()');
if ($Qloc !== 'ex') {
    $aOpciones = array('' => '',
        'ctr_n' => ucfirst(_("sólo centros de n")),
        'todos_ctr_dl' => ucfirst(_("todos los ctr de la dl")),
        'ctr_agd' => ucfirst(_("sólo centros de agd")),
        'ctr_s' => ucfirst(_("sólo centros de s")),
    );
    if ($miSfsv == 1) { // sv
        $aOpciones['ctr_sssc'] = ucfirst(_("sólo centros de sss+"));
    }
    if ($miSfsv == 2) { // sf
        $aOpciones['ctr_nax'] = ucfirst(_("sólo centros de nax"));
    }
    $aOpciones['oc'] = ucfirst(_("sólo obras corporativas"));
    $aOpciones['cdr_cdc_dl'] = ucfirst(_("casas de retiros y de cv"));
    $aOpciones['cgi'] = ucfirst(_("sólo colegios"));
    $aOpciones['otros_cdc'] = ucfirst(_("resto casas cdc"));
}
if ($Qloc == 'ex') {
    $aOpciones = array('' => '',
        'ctr_n_ex' => ucfirst(_("sólo centros de n")),
        'ctr_agd_ex' => ucfirst(_("sólo centros de agd")),
        'ctr_s_ex' => ucfirst(_("sólo centros de s")),
        'todos_ctr_ex' => ucfirst(_("todos los centros")),
        'dl' => ucfirst(_("sólo delegaciones")),
        'cr' => ucfirst(_("sólo comisiones regionales")),
        'cdc_ex' => ucfirst(_("todas las casas")),
    );
}
$oDesplLista->setOpciones($aOpciones);
$oDesplLista->setOpcion_sel($Qque_lista);

$oTabla = new Lista();
$oTabla->setId_tabla('list_ctr');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);

$oHash = new Hash();
$oHash->setCamposForm('loc!que_lista');

$oHash1 = new Hash();
$oHash1->setCamposForm('sel');
$oHash1->setcamposNo('scroll_id!dl_dst');
$a_camposHidden1 = array(
    'que_lista' => $Qque_lista,
    'dl_dst' => '', // solo útil en el caso de traslado de dl
);
$oHash1->setArraycamposHidden($a_camposHidden1);


$oHash2 = new Hash();
$oHash2->setUrl(ConfigGlobal::getWeb() . '/apps/ubis/controller/delegacion_que.php');
$oHash2->setCamposForm('');
$h2 = $oHash2->linkSinVal();


$a_campos = ['oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'oDesplDl' => $oDesplDl,
    'oDesplLista' => $oDesplLista,
    'oHash1' => $oHash1,
    'oTabla' => $oTabla,
    'h2' => $h2,
];

$oView = new ViewPhtml('ubis\controller');
$oView->renderizar('list_ctr.phtml', $a_campos);