<?php
/**
 * Esta página muestra una tabla con las actas.
 *
 *
 * @package    delegacion
 * @subpackage    estudios
 * @author    Daniel Serrabou
 * @since        14/10/03.
 *
 */

use asignaturas\model\entity\GestorAsignatura;
use core\ConfigGlobal;
use core\ViewPhtml;
use notas\model\entity\GestorActa;
use notas\model\entity\GestorActaDl;
use notas\model\entity\GestorActaEx;
use web\Hash;
use web\Lista;
use ubis\model\entity\GestorDelegacion;
use function core\curso_est;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$mi_dele = ConfigGlobal::mi_delef();
$mi_region = ConfigGlobal::mi_region();

$Qrefresh = (integer)filter_input(INPUT_POST, 'refresh');
$oPosicion->recordar($Qrefresh);

//Si vengo por medio de Posicion, borro la última
if (isset($_POST['stack'])) {
    $stack = filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    if ($stack != '') {
        $oPosicion2 = new web\Posicion();
        if ($oPosicion2->goStack($stack)) { // devuelve false si no puede ir
            $Qid_sel = $oPosicion2->getParametro('id_sel');
            $Qscroll_id = $oPosicion2->getParametro('scroll_id');
            $oPosicion2->olvidar($stack);
        }
    }
}

$Qtitulo = (string)filter_input(INPUT_POST, 'titulo');
$Qacta = (string)filter_input(INPUT_POST, 'acta');

/*
* Defino un array con los datos actuales, para saber volver después de navegar un rato
*/
$aGoBack = array(
    'titulo' => $Qtitulo,
    'acta' => $Qacta);
$oPosicion->setParametros($aGoBack, 1);

/*miro las condiciones. Si es la primera vez muestro las de este año */
$aWhere = [];
$aOperador = [];
if (!empty($Qacta)) {
    /* se cambia la lógica, por el cambio de nombre de la dl, no de las actas */
    $aWhere['acta'] = $Qacta;
    $aOperador['acta'] = '~';
    $aWhere['_ordre'] = 'f_acta DESC, acta DESC';

    // si es número busca en la dl.
    $matches = [];
    preg_match("/^(\d*)(\/)?(\d*)/", $Qacta, $matches);
    if (!empty($matches[1])) {
        // Si es cr, se mira en todas (las suyas):
        if (ConfigGlobal::mi_ambito() === 'rstgr') {
            $oGesDelegaciones = new GestorDelegacion();
            $aDl = $oGesDelegaciones->getArrayDlRegionStgr([$mi_dele]);
            $Qacta_dl = '';
            foreach ($aDl as $dl) {
                $Qacta_dl .= empty($Qacta_dl) ? '' : "|";
                $Qacta_dl .= empty($matches[3]) ? "$dl " . $matches[1] . '/' . date("y") : "$dl $Qacta";
            }
            $aWhere['acta'] = $Qacta_dl;
            $GesActas = new GestorActa();
        } else {
            $aWhere['acta'] = empty($matches[3]) ? "$mi_dele " . $matches[1] . '/' . date("y") : "$mi_dele $Qacta";
            $GesActas = new GestorActaDl();
        }
        $cActas = $GesActas->getActas($aWhere, $aOperador);
    } else {
        // busca en la tabla de la dl, sin mirar el nombre:
        if (ConfigGlobal::mi_ambito() === 'rstgr') {
            $GesActas = new GestorActa();
            $cActas = $GesActas->getActas($aWhere, $aOperador);
        } else {
            $GesActas = new GestorActaDl();
            $cActas = $GesActas->getActas($aWhere, $aOperador);
            if (empty($cActas)) {
                $GesActas = new GestorActaEx();
                $cActas = $GesActas->getActas($aWhere, $aOperador);
            }
        }
    }
    $titulo = $Qtitulo;
} else {
    $mes = date('m');
    $fin_m = $_SESSION['oConfig']->getMesFinStgr();
    if ($mes > $fin_m) {
        $any = (int) date('Y') + 1;
    } else {
        $any = (int) date('Y');
    }
    $inicurs_ca = curso_est("inicio", $any)->format('Y-m-d');
    $fincurs_ca = curso_est("fin", $any)->format('Y-m-d');
    $txt_curso = "$inicurs_ca - $fincurs_ca";

    $aWhere['f_acta'] = "'$inicurs_ca','$fincurs_ca'";
    $aOperador['f_acta'] = 'BETWEEN';
    $aWhere['_ordre'] = 'f_acta DESC, acta DESC';

    $titulo = ucfirst(sprintf(_("lista de actas del curso %s"), $txt_curso));
    // Si es cr, se mira en todas:
    if (ConfigGlobal::mi_ambito() === 'rstgr') {
        $oGesDelegaciones = new GestorDelegacion();
        $aDl = $oGesDelegaciones->getArrayDlRegionStgr([$mi_dele]);
        $sReg = implode("|", $aDl);
        $Qacta = "^($sReg)";
        $aWhere['acta'] = $Qacta;
        $aOperador['acta'] = '~';
        $GesActas = new GestorActa();
    } else {
        $GesActas = new GestorActaDl();
    }
    $cActas = $GesActas->getActas($aWhere, $aOperador);
}

$botones = 0; // para 'añadir acta'
$a_botones = [];
// Si soy region del stgr, no puedo modificar actas: que lo hagan las dl.
if (ConfigGlobal::mi_ambito() === 'rstgr') {
    $a_botones[] = array('txt' => _("modificar"), 'click' => "fnjs_modificar(\"#seleccionados\")");
    $botones = 0;
} else {
    if ($_SESSION['oPerm']->have_perm_oficina('est')) {
        $a_botones[] = array('txt' => _("eliminar"), 'click' => "fnjs_eliminar(\"#seleccionados\")");
        $a_botones[] = array('txt' => _("modificar"), 'click' => "fnjs_modificar(\"#seleccionados\")");
        $botones = 1; // para 'añadir acta'
    }
}

$a_botones[] =  ['txt' => _("imprimir"), 'click' => "fnjs_imprimir(\"#seleccionados\")"];
$a_botones[] =  ['txt' => _("descargar pdf"), 'click' => "fnjs_descargar_pdf(\"#seleccionados\")"];

$a_cabeceras = [['name' => ucfirst(_("acta")), 'formatter' => 'clickFormatter'],
    ['name' => ucfirst(_("fecha")), 'class' => 'fecha'],
    _("asignatura"),
    _("firmada"),
];


$gesAsignatura = new GestorAsignatura();
$a_asignaturas = $gesAsignatura->getArrayAsignaturas();


$i = 0;
$a_valores = [];
foreach ($cActas as $oActa) {
    $i++;
    $acta = $oActa->getActa();
    $f_acta = $oActa->getF_acta()->getFromLocal();
    $id_asignatura = $oActa->getId_asignatura();
    $pdf = $oActa->getpdf();

    if (empty($a_asignaturas[$id_asignatura])) {
        $nombre_corto = sprintf(_("nombre corto no definido para id asignatura: %s"), $id_asignatura);
    } else {
        $nombre_corto = $a_asignaturas[$id_asignatura];
    }
    $acta_2 = urlencode($acta);
    $pagina = Hash::link('apps/notas/controller/acta_ver.php?' . http_build_query(array('acta' => $acta)));
    $a_valores[$i]['sel'] = $acta_2;
    if ($_SESSION['oPerm']->have_perm_oficina('est')) {
        $a_valores[$i][1] = array('ira' => $pagina, 'valor' => $acta);
    } else {
        $a_valores[$i][1] = $acta;
    }
    $a_valores[$i][2] = $f_acta;
    $a_valores[$i][3] = $nombre_corto;
    $a_valores[$i][4] = empty($pdf) ? '' : _("Sí");
}
if (isset($Qid_sel) && !empty($Qid_sel)) {
    $a_valores['select'] = $Qid_sel;
}
if (isset($Qscroll_id) && !empty($Qscroll_id)) {
    $a_valores['scroll_id'] = $Qscroll_id;
}

$oHash = new Hash();
$oHash->setCamposForm('acta');

$oHash1 = new Hash();
$oHash1->setCamposForm('sel!mod');
$oHash1->setCamposNo('sel!scroll_id!mod!refresh');

$oHashDown = new Hash();
$oHashDown->setUrl('apps/notas/controller/acta_pdf_download.php');
$oHashDown->setCamposForm('key');
$h_download = $oHashDown->linkSinVal();

$oTabla = new Lista();
$oTabla->setId_tabla('acta_select');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);

$txt_eliminar = _("esto eliminará los datos del acta, pero no las notas que mantendrán el número de acta");

$a_campos = ['oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'oHash1' => $oHash1,
    'titulo' => $titulo,
    'oTabla' => $oTabla,
    'botones' => $botones,
    'txt_eliminar' => $txt_eliminar,
    'h_download' => $h_download,
];

$oView = new ViewPhtml('notas/controller');
$oView->renderizar('acta_select.phtml', $a_campos);
