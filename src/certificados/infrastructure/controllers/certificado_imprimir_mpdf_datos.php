<?php

// INICIO Cabecera global de URL de controlador *********************************
use asignaturas\model\entity\Asignatura;
use asignaturas\model\entity\GestorAsignatura;
use core\ConfigGlobal;
use notas\model\entity\GestorPersonaNotaDB;
use notas\model\PersonaNota;
use personas\model\entity\Persona;
use src\certificados\application\repositories\CertificadoRepository;
use ubis\model\entity\GestorDelegacion;
use web\ContestarJson;
use web\DateTimeLocal;
use function core\is_true;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$id_item = (string)filter_input(INPUT_POST, 'id_item');

$error_txt = '';
$data = [];

$CertificadoRepository = new CertificadoRepository();
$oCertificado = $CertificadoRepository->findById($id_item);

$id_nom = $oCertificado->getId_nom();
$nom = $oCertificado->getNom();
$idioma = $oCertificado->getIdioma();
$destino = $oCertificado->getDestino();
$certificado = $oCertificado->getCertificado();
$f_certificado = $oCertificado->getF_certificado()->getFromLocal();
$firmado = $oCertificado->isFirmado();
if (is_true($firmado)) {
    $chk_firmado = 'checked';
} else {
    $chk_firmado = '';
}

$oPersona = Persona::NewPersona($id_nom);
if (!is_object($oPersona)) {
    $msg_err = "<br>$oPersona con id_nom: $id_nom en " . __FILE__ . ": line " . __LINE__;
    exit($msg_err);
}
$apellidos_nombre = $oPersona->getApellidosNombre();
$nom = empty($nom) ? $apellidos_nombre : $nom;
$lugar_nacimiento = $oPersona->getLugar_nacimiento();
$f_nacimiento = $oPersona->getF_nacimiento()->getFechaLatin();
$nivel_stgr = $oPersona->getStgr();

$region_latin = $_SESSION['oConfig']->getNomRegionLatin();
$vstgr = $_SESSION['oConfig']->getNomVstgr();
$dir_stgr = $_SESSION['oConfig']->getDirStgr();
$lugar_firma = $_SESSION['oConfig']->getLugarFirma();

$oHoy = new DateTimeLocal();
$lugar_fecha = $lugar_firma . ",  " . $oHoy->getFechaLatin();
$region = $region_latin;

// conversion
$replace = config\model\Config::$replace;

// para los distintos idiomas. Cargar el fichero:
$filename_textos = __DIR__ . '/' . "textos_certificados.php";
if (!empty($idioma)) {
    $dir = ConfigGlobal::$dir_languages . '/' . $idioma;
    $filename_textos = $dir . '/' . "textos_certificados.php";
    if (!file_exists($filename_textos)) {
        $error_txt .= sprintf(_("No existe un fichero con las traducciones para %s"), $idioma);
        ContestarJson::enviar($error_txt, 'ok');
        exit();
    }
}
include($filename_textos);

if ($nivel_stgr === 'r') {
    $txt_superavit = $txt_superavit_1;
    $txt_superavit .= ' ' . $txt_superavit_2;
    $txt_superavit = strtr($txt_superavit, $replace);
} else {
    $txt_superavit = '';
}

$data['id_nom'] = $id_nom;
$data['nom'] = $nom;
$data['certificado'] = $certificado;
$data['lugar_fecha'] = $lugar_fecha;
$data['vstgr'] = $vstgr;
$data['dir_stgr'] = $dir_stgr;
$data['replace'] = $replace;
$data['txt_superavit'] = $txt_superavit;
$data['curso_filosofia'] = $curso_filosofia;
$data['any_I'] = $any_I;
$data['ECTS'] = $ECTS;
$data['iudicium'] = $iudicium;
$data['curso_teologia'] = $curso_teologia;
$data['pie_ects'] = $pie_ects;
$data['any_II'] = $any_II;
$data['any_III'] = $any_III;
$data['any_IV'] = $any_IV;
$data['titulo_1'] = $titulo_1;
$data['titulo_2'] = $titulo_2;
$data['titulo_3'] = $titulo_3;
$data['infra'] = $infra;
$data['sello'] = $sello;
$data['fidem'] = $fidem;
$data['reg_num'] = $reg_num;

// Asignaturas posibles:
$GesAsignaturas = new GestorAsignatura();
$aWhere = [];
$aOperador = [];
$aWhere['status'] = 't';
$aWhere['id_nivel'] = '1100,2500';
$aOperador['id_nivel'] = 'BETWEEN';
$aWhere['_ordre'] = 'id_nivel';
$cAsignaturas = $GesAsignaturas->getAsignaturasAsJson($aWhere, $aOperador);
$data['cAsignaturas'] = $cAsignaturas;

// Asignaturas cursadas:
// solamente las notas de mi región_stgr. Normalmente serian las notas_dl,
// pero para casos como H-Hv...
$gesDelegacion = new GestorDelegacion();
$mi_dl = ConfigGlobal::mi_dele();
$a_mi_region_stgr = $gesDelegacion->mi_region_stgr($mi_dl);
$region_stgr = $a_mi_region_stgr['region_stgr'];
$mi_sfsv = ConfigGlobal::mi_sfsv();
$a_id_schemas_rstgr = $gesDelegacion->getArrayIdSchemaRegionStgr($region_stgr, $mi_sfsv);
if (empty($a_id_schemas_rstgr)) {
    $error_txt .= "\n" . _("Debe definir la región del stgr a la que pertenece");
}

if (empty($error_txt)) {
    $GesNotas = new GestorPersonaNotaDB();
    $aWhere = [];
    $aOperador = [];
    $aWhere['id_schema'] = implode(',', $a_id_schemas_rstgr);
    $aOperador['id_schema'] = 'IN';
    $aWhere['id_nom'] = $id_nom;
    $aWhere['id_nivel'] = '1100,2500';
    $aOperador['id_nivel'] = 'BETWEEN';
    $aWhere['tipo_acta'] = PersonaNota::FORMATO_ACTA;
    $cNotas = $GesNotas->getPersonaNotas($aWhere, $aOperador);
    $aAprobadas = [];
    foreach ($cNotas as $oPersonaNota) {
        $id_asignatura = $oPersonaNota->getId_asignatura();
        $id_nivel = $oPersonaNota->getId_nivel();

        $oAsig = new Asignatura($id_asignatura);
        if ($id_asignatura > 3000) {
            $id_nivel_asig = $id_nivel;
        } else {
            if (!is_true($oAsig->getStatus())) {
                continue;
            }
            $id_nivel_asig = $oAsig->getId_nivel();
        }
        $creditos = $oAsig->getCreditos();
        $n = $id_nivel_asig;
        $aAprobadas[$n]['id_nivel_asig'] = $id_nivel_asig;
        $aAprobadas[$n]['id_nivel'] = $id_nivel;
        $aAprobadas[$n]['id_asignatura'] = $id_asignatura;
        $aAprobadas[$n]['nombre_asignatura'] = $oAsig->getNombre_asignatura();
        $aAprobadas[$n]['creditos'] = number_format(($creditos * 2), 0);
        $aAprobadas[$n]['nota_txt'] = $oPersonaNota->getNota_txt();
    }
    $data['aAprobadas'] = $aAprobadas;
}

// envía una Response
ContestarJson::enviar($error_txt, $data);