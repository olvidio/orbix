<?php

use asignaturas\model\entity\Asignatura;
use asignaturas\model\entity\AsignaturaTipo;
use core\ConfigGlobal;
use core\ViewPhtml;
use notas\model\entity\Acta;
use notas\model\entity\GestorActaTribunal;
use notas\model\entity\GestorActaTribunalDl;
use notas\model\getDatosActa;
use personas\model\entity\GestorNombreLatin;
use personas\model\entity\Persona;
use src\asignaturas\application\repositories\AsignaturaRepository;
use src\asignaturas\application\repositories\AsignaturaTipoRepository;
use web\Hash;

/**
 * Esta página sirve para las actas.
 *
 *
 * @package    delegacion
 * @subpackage    estudios
 * @author    Daniel Serrabou
 * @since        24/10/03.
 *
 */

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// Archivos requeridos por esta url **********************************************
include_once(ConfigGlobal::$dir_estilos . '/actas.css.php');

// FIN de  Cabecera global de URL de controlador ********************************

$Qrefresh = (integer)filter_input(INPUT_POST, 'refresh');
$oPosicion->recordar($Qrefresh);

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
    $acta = urldecode(strtok($a_sel[0], "#"));
    // el scroll id es de la página anterior, hay que guardarlo allí
    $oPosicion->addParametro('id_sel', $a_sel, 1);
    $scroll_id = (integer)filter_input(INPUT_POST, 'scroll_id');
    $oPosicion->addParametro('scroll_id', $scroll_id, 1);
} else {
    $Qacta = (string)filter_input(INPUT_POST, 'acta');
    $acta = empty($Qacta) ? '' : urldecode($Qacta);
}

$Qcara = (string)filter_input(INPUT_POST, 'cara');
$cara = empty($Qcara) ? 'A' : $Qcara;

// conversion
$replace = src\configuracion\domain\entity\Config::$replace;
$region_latin = $_SESSION['oConfig']->getNomRegionLatin();
$nombre_prelatura = strtr("PRAELATURA SANCTAE CRUCIS ET OPERIS DEI", $replace);
$reg_stgr = "Stgr" . ConfigGlobal::mi_region();

// acta
$oActa = new Acta($acta);
$id_asignatura = $oActa->getId_asignatura();
$id_activ = $oActa->getId_activ();
$oF_acta = $oActa->getF_acta();
$libro = $oActa->getLibro();
$pagina = $oActa->getPagina();
$linea = $oActa->getLinea();
$lugar = $oActa->getLugar();
$observ = $oActa->getObserv();

$oAsignatura = (new AsignaturaRepository())->findById($id_asignatura);
if ($oAsignatura === null) {
    throw new \Exception(sprintf(_("No se ha encontrado la asignatura con id: %s"), $id_asignatura));
}
$nombre_corto = $oAsignatura->getNombre_corto();
$nombre_asignatura = strtr($oAsignatura->getNombre_asignatura(), $replace);
$any = $oAsignatura->getYear();

$id_tipo = $oAsignatura->getId_tipo();
$oAsignaturaTipo = (new AsignaturaTipoRepository())->findById($id_tipo);
if ($oAsignatura === null) {
    throw new \Exception(sprintf(_("No se ha encontrado el tipo de asignatura con id: %s"), $id_tipo));
}
$curso = strtr($oAsignaturaTipo->getTipoLatinVo()->value() ?? '', $replace);

switch ($any) {
    case 1:
        $any = "I";
        break;
    case 2:
        $any = "II";
        break;
    case 3:
        $any = "III";
        break;
    case 4:
        $any = "IV";
        break;
    default:
        $any = '';
}

// -----------------------------

$cPersonaNotas = getDatosActa::getNotasActa($acta);

// para ordenar
$errores = '';
$aPersonasNotas = [];
$oGesNomLatin = new GestorNombreLatin();
foreach ($cPersonaNotas as $oPersonaNota) {
    $id_situacion = $oPersonaNota->getId_situacion();
    $id_nom = $oPersonaNota->getId_nom();
    $oPersona = Persona::NewPersona($id_nom);
    if (!is_object($oPersona)) {
        $errores .= "<br>" . sprintf(_("existe una nota de la que no se tiene acceso al nombre (id_nom = %s): es de otra dl o 'de paso' borrado."), $id_nom);
        $errores .= " " . _("no aparece en la lista");
        continue;
    }
    $nom = $oPersona->getApellidosUpperNombre();
    $nota = $oPersonaNota->getNota_txt();
    $aPersonasNotas[$nom] = $nota;
}
uksort($aPersonasNotas, "core\strsinacentocmp"); // compara sin contar los acentos i insensitive.

$num_alumnos = count($aPersonasNotas);

// tribunal:
// Si es cr, se mira en todas:
if (ConfigGlobal::mi_ambito() === 'rstgr') {
    $GesTribunal = new GestorActaTribunal();
} else {
    $GesTribunal = new GestorActaTribunalDl();
}
$cTribunal = $GesTribunal->getActasTribunales(array('acta' => $acta, '_ordre' => 'orden'));
$num_examinadores = count($cTribunal);

// Definición del número de lineas de las páginas y los numeros de alumnos----------------
$lin_A4 = 45;                                        // número máximo de lineas en un A4
$lin_encabezado = 16;                                // número de lineas del encabezado asignatura + pie
$lin_encabezado_tribunal = 4;                        // número de lineas del encabezado tribunal
$lin_tribunal = $lin_encabezado_tribunal + 2 * $num_examinadores;  // número de lineas del tribunal

$lin_max_cara_A = $lin_A4 - $lin_encabezado - 2;    // número máximo de lineas en la cara A

if ($num_alumnos > $lin_max_cara_A) {
    $alum_cara_A = $lin_max_cara_A;
} else {
    $alum_cara_A = $num_alumnos;
}
$alum_cara_B = $num_alumnos - $alum_cara_A;

$caraA = Hash::link('apps/notas/controller/acta_imprimir.php?' . http_build_query(array('cara' => 'A', 'acta' => $acta, 'refresh' => 1)));
$caraB = Hash::link('apps/notas/controller/acta_imprimir.php?' . http_build_query(array('cara' => 'B', 'acta' => $acta, 'refresh' => 1)));

$oHash = new Hash();
$oHash->setUrl(ConfigGlobal::getWeb() . '/apps/notas/controller/acta_2_mpdf.php');
$oHash->setCamposForm('acta');
$h = $oHash->linkSinVal();

$lugar_fecha = $lugar . ",  " . $oF_acta->getFechaLatin();

$a_campos = [
    'oPosicion' => $oPosicion,
    'h' => $h,
    'cara' => $cara,
    'caraA' => $caraA,
    'caraB' => $caraB,
    'acta' => $acta,
    'errores' => $errores,
    'curso' => $curso,
    'any' => $any,
    'region_latin' => $region_latin,
    'nombre_prelatura' => $nombre_prelatura,
    'reg_stgr' => $reg_stgr,
    'nombre_asignatura' => $nombre_asignatura,
    'alum_cara_A' => $alum_cara_A,
    'alum_cara_B' => $alum_cara_B,
    'aPersonasNotas' => $aPersonasNotas,
    'num_alumnos' => $num_alumnos,
    'lin_max_cara_A' => $lin_max_cara_A,
    'lin_tribunal' => $lin_tribunal,
    'cTribunal' => $cTribunal,
    'lugar' => $lugar,
    'lugar_fecha' => $lugar_fecha,
    'libro' => $libro,
    'pagina' => $pagina,
    'linea' => $linea,
];

$oView = new ViewPhtml('notas\controller');
$oView->renderizar('acta_imprimir.phtml', $a_campos);
