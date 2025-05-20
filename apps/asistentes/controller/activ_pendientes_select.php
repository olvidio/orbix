<?php
/**
 * Este controlador muestra una tabla con las personas que tienen la actividad
 * (ca|crt) pendiente para este curso.
 *
 *
 * @package    orbix
 * @subpackage    asistentes
 * @author    Daniel Serrabou
 * @since        7/11/03.
 * @ajax        23/8/2007.
 *
 */

use actividades\model\entity\GestorActividad;
use asistentes\model\entity\GestorAsistente;
use core\ConfigGlobal;
use core\ViewPhtml;
use personas\model\entity\GestorPersonaAgd;
use personas\model\entity\GestorPersonaDl;
use personas\model\entity\GestorPersonaN;
use ubis\model\entity\CentroDl;
use web\Hash;
use web\Lista;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qany = (integer)filter_input(INPUT_POST, 'any');
$Qtipo_personas = (string)filter_input(INPUT_POST, 'tipo_personas');
$Qsactividad = (string)filter_input(INPUT_POST, 'sactividad');

/*miro las condiciones. Si es la primera vez muestro las de este año */
if (empty($Qany)) {
    $any = date("Y");
} else {
    $any = $Qany;
}
// curso
switch ($any) {
    case date("Y"):
        $txt_curso_1 = ($any - 1) . "/" . $any;
        $chk_any_1 = "selected";
        $chk_any_2 = "";
        break;
    case (date("Y") + 1):
        $chk_any_1 = "";
        $chk_any_2 = "selected";
        break;
}
$any_real = date("Y");
$txt_curso_1 = ($any_real - 1) . "/" . $any_real;
$txt_curso_2 = ($any_real) . "/" . ($any_real + 1);
$txt_curso = ($any - 1) . "/" . $any;

// tipo de personas
$chk_n = '';
$chk_agd = '';
$chk_sacd = '';
switch ($Qtipo_personas) {
    case "n":
        $chk_n = "selected";
        break;
    case "agd":
        $chk_agd = "selected";
        break;
    case "sacd":
        $chk_sacd = "selected";
        break;
}

$mi_dele = ConfigGlobal::mi_delef();
// tipo de actividad
$chk_ca = '';
$chk_crt = '';
switch ($Qsactividad) {
    case 'ca':
        if ($Qtipo_personas === 'n') $id_tipo_activ = '(112...)|(133...)';
        if ($Qtipo_personas === 'sacd') $id_tipo_activ = '(112...)|(133...)';
        if ($Qtipo_personas === 'agd') $id_tipo_activ = '133...';
        if ($Qtipo_personas === 'stgr') $id_tipo_activ = '(112...)|(133...)';
        $chk_ca = "selected";
        $inicurs = core\curso_est("inicio", $any, "est")->format('Y-m-d');
        $fincurs = core\curso_est("fin", $any, "est")->format('Y-m-d');
        break;
    case 'crt':
        // 22.1.09 quito a los que han hecho el crt con sr
        // 25.2.21 quito a los que han hecho el crt con sss+
        if ($Qtipo_personas === 'n') $id_tipo_activ = '1[1376]1...';
        if ($Qtipo_personas === 'agd') $id_tipo_activ = '131...';
        // 25.2.21 quito a los que han hecho el crt con sss+
        if ($Qtipo_personas === 'sacd') $id_tipo_activ = '1[136]1...';
        $chk_crt = 'selected';
        $inicurs = core\curso_est('inicio', $any, 'crt')->format('Y-m-d');
        $fincurs = core\curso_est('fin', $any, 'crt')->format('Y-m-d');
        break;
}
// Actividades del curso y tipo:
$aWhereA = [];
$aOperadorA = [];
$aWhereA['id_tipo_activ'] = $id_tipo_activ;
$aOperadorA['id_tipo_activ'] = '~';
$aWhereA['f_ini'] = "'$inicurs','$fincurs'";
$aOperadorA['f_ini'] = 'BETWEEN';
$GesActividades = new GestorActividad();
$cActividades = $GesActividades->getActividades($aWhereA, $aOperadorA);
$aAsistentes = [];
foreach ($cActividades as $oActividad) {
    $id_activ = $oActividad->getId_activ();
    // Asistentes:
    $GesAsistentes = new GestorAsistente();
    $cAsistentes = $GesAsistentes->getAsistentes(array('id_activ' => $id_activ, 'propio' => 't'));
    foreach ($cAsistentes as $oAsistente) {
        $aAsistentes[] = $oAsistente->getId_nom();
    }
}
// Personas que deberían haber hecho la actividad:
switch ($Qtipo_personas) {
    case "n":
        $GesPersonas = new GestorPersonaN();
        $cPersonas = $GesPersonas->getPersonas(array('situacion' => 'A', 'dl' => $mi_dele));
        $obj_pau = 'PersonaN';
        break;
    case "agd":
        $GesPersonas = new GestorPersonaAgd();
        $cPersonas = $GesPersonas->getPersonas(array('situacion' => 'A', 'dl' => $mi_dele));
        $obj_pau = 'PersonaAgd';
        break;
    case "sacd":
        $GesPersonas = new GestorPersonaDl();
        $cPersonas = $GesPersonas->getPersonas(array('sacd' => 't', 'situacion' => 'A', 'dl' => $mi_dele));
        $obj_pau = 'PersonaDl';
        break;
}

$aFaltan = [];
foreach ($cPersonas as $oPersona) {
    $id_nomP = $oPersona->getId_nom();
    if (in_array($id_nomP, $aAsistentes)) continue;
    $ap_nom = $oPersona->getPrefApellidosNombre();
    $id_ubi = $oPersona->getId_ctr();
    $nivel_stgr = $oPersona->getStgr();
    if (!empty($ap_nom)) {
        $aFaltan[$ap_nom] = ['id_nom' => $id_nomP, 'id_ubi' => $id_ubi, 'nivel_stgr' => $nivel_stgr];
    }
}
uksort($aFaltan, "core\strsinacentocmp");

$titulo = ucfirst(sprintf(_("lista de %s sin %s en el curso %s"), $Qtipo_personas, $Qsactividad, $txt_curso));

$a_cabeceras = [_("nº"),
    array('name' => ucfirst(_("nombre de la persona")), 'formatter' => 'clickFormatter'),
    'ctr',
    _("nivel stgr"),
];
$i = 0;
$a_valores = [];
foreach ($aFaltan as $ap_nom => $aDatos) {
    $i++;
    $id_nom = $aDatos['id_nom'];
    $id_ubi = $aDatos['id_ubi'];
    $nivel_stgr = $aDatos['nivel_stgr'];

    $oCentro = new CentroDl($id_ubi);
    $nombre_ubi = $oCentro->getNombre_ubi();

    $aQuery = array('obj_pau' => $obj_pau, 'id_nom' => $id_nom);
    $pagina = Hash::link('apps/personas/controller/home_persona.php?' . http_build_query($aQuery));

    $a_valores[$i][1] = $i;
    $a_valores[$i][2] = array('ira' => $pagina, 'valor' => $ap_nom);
    $a_valores[$i][3] = $nombre_ubi;
    $a_valores[$i][4] = $nivel_stgr;
}


// Al final añado la lista de personas que no están en la dl, pero dependen de aquí.
//  (probablemente harán la actividad en su region actual)
$aWhere = [];
$aOperador = [];
$aWhere['situacion'] = 'A';
$aWhere['dl'] = $mi_dele;
$aOperador['dl'] = '!=';
switch ($Qtipo_personas) {
    case "n":
        $GesPersonas = new GestorPersonaN();
        $cPersonasOtras = $GesPersonas->getPersonas($aWhere, $aOperador);
        break;
    case "agd":
        $GesPersonas = new GestorPersonaAgd();
        $cPersonasOtras = $GesPersonas->getPersonas($aWhere, $aOperador);
        break;
    case "sacd":
        $aWhere['sacd'] = 't';
        $GesPersonas = new GestorPersonaDl();
        $cPersonasOtras = $GesPersonas->getPersonas($aWhere, $aOperador);
        break;
}

$aFaltanOtras = [];
foreach ($cPersonasOtras as $oPersona) {
    $id_nomP = $oPersona->getId_nom();
    if (in_array($id_nomP, $aAsistentes)) continue;
    $ap_nom = $oPersona->getPrefApellidosNombre();
    $id_ubi = $oPersona->getId_ctr();
    $nivel_stgr = $oPersona->getStgr();
    $aFaltanOtras[$ap_nom] = ['id_nom' => $id_nomP, 'id_ubi' => $id_ubi, 'nivel_stgr' => $nivel_stgr];
}
uksort($aFaltanOtras, "core\strsinacentocmp");

$a_valores_2 = [];
foreach ($aFaltanOtras as $ap_nom => $aDatos) {
    $i++;
    $id_nom = $aDatos['id_nom'];
    $id_ubi = $aDatos['id_ubi'];
    $nivel_stgr = $aDatos['nivel_stgr'];

    $oCentro = new CentroDl($id_ubi);
    $nombre_ubi = $oCentro->getNombre_ubi();

    $aQuery = array('obj_pau' => $obj_pau, 'id_nom' => $id_nom);
    $pagina = Hash::link('apps/personas/controller/home_persona.php?' . http_build_query($aQuery));

    $a_valores_2[$i][1] = $i;
    $a_valores_2[$i][2] = array('ira' => $pagina, 'valor' => $ap_nom);
    $a_valores_2[$i][3] = $nombre_ubi;
    $a_valores_2[$i][4] = $nivel_stgr;
}


$oHash = new Hash();
$oHash->setCamposForm('tipo_personas!sactividad!any');

$oTablaDl = new Lista();
$oTablaDl->setId_tabla('activ_pendientes_select');
$oTablaDl->setCabeceras($a_cabeceras);
$oTablaDl->setDatos($a_valores);

$oTablaOtrasDl = new Lista();
$oTablaOtrasDl->setId_tabla('activ_pendientes_select_otras');
$oTablaOtrasDl->setCabeceras($a_cabeceras);
$oTablaOtrasDl->setDatos($a_valores_2);

$a_campos = [
    'oHash' => $oHash,
    'chk_n' => $chk_n,
    'chk_agd' => $chk_agd,
    'chk_sacd' => $chk_sacd,
    'chk_ca' => $chk_ca,
    'chk_crt' => $chk_crt,
    'any_real' => $any_real,
    'chk_any_1' => $chk_any_1,
    'txt_curso_1' => $txt_curso_1,
    'chk_any_2' => $chk_any_2,
    'txt_curso_2' => $txt_curso_2,
    'titulo' => $titulo,
    'oTablaDl' => $oTablaDl,
    'oTablaOtrasDl' => $oTablaOtrasDl,
];

$oView = new ViewPhtml('asistentes\controller');
$oView->renderizar('activ_pendientes.phtml', $a_campos);