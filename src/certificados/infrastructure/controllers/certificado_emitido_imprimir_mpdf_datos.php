<?php

use core\ConfigGlobal;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\certificados\domain\contracts\CertificadoEmitidoRepositoryInterface;
use src\notas\domain\contracts\PersonaNotaDBRepositoryInterface;
use src\notas\domain\value_objects\TipoActa;
use src\personas\domain\entity\Persona;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use web\ContestarJson;
use web\DateTimeLocal;
use function core\is_true;

$id_item = (string)filter_input(INPUT_POST, 'id_item');

$error_txt = '';
$data = [];

$certificadoEmitidoRepository = $GLOBALS['container']->get(CertificadoEmitidoRepositoryInterface::class);
$oCertificadoEmitido = $certificadoEmitidoRepository->findById($id_item);

$id_nom = $oCertificadoEmitido->getId_nom();
$nom = $oCertificadoEmitido->getNomVo()->value();
$idioma = $oCertificadoEmitido->getIdiomaVo()->value();
$destino = $oCertificadoEmitido->getDestino();
$certificado = $oCertificadoEmitido->getCertificado();
$f_certificado = $oCertificadoEmitido->getF_certificado()?->getFromLocal();
$firmado = $oCertificadoEmitido->isFirmado();
if (is_true($firmado)) {
    $chk_firmado = 'checked';
} else {
    $chk_firmado = '';
}

$oPersona = Persona::findPersonaEnGlobal($id_nom);
if ($oPersona === null) {
    $msg_err = "<br>No encuentro a nadie con id_nom: $id_nom en " . __FILE__ . ": line " . __LINE__;
    exit($msg_err);
}
$apellidos_nombre = $oPersona->getApellidosNombre();
$nom = empty($nom) ? $apellidos_nombre : $nom;
$lugar_nacimiento = $oPersona->getLugarNacimientoVo()->value();
$f_nacimiento = $oPersona->getF_nacimiento()->getFechaLatin();
$nivel_stgr = $oPersona->getNivelStgrVo()->value();

$region_latin = $_SESSION['oConfig']->getNomRegionLatin();
$vstgr = $_SESSION['oConfig']->getNomVstgr();
$dir_stgr = $_SESSION['oConfig']->getDirStgr();
$lugar_firma = $_SESSION['oConfig']->getLugarFirma();

$oHoy = new DateTimeLocal();
$lugar_fecha = $lugar_firma . ",  " . $oHoy->getFechaLatin();
$region = $region_latin;

// conversion
$replace = src\configuracion\domain\entity\Config::$replace;

// para los distintos idiomas. Cargar el fichero:
$filename_textos = __DIR__ . '/' . "textos_certificados.php";
if (!empty($idioma)) {
    $dir = ConfigGlobal::$dir_languages . '/' . $idioma;
    $filename_textos = $dir . '/' . "textos_certificados.php";
    if (!file_exists($filename_textos)) {
        $error_txt .= sprintf(_("No existe un fichero con las traducciones para %s"), $idioma);
        ContestarJson::enviar($error_txt, 'ok');
        return;
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
$AsignaturaRepository = $GLOBALS['container']->get(AsignaturaRepositoryInterface::class);
$aWhere = [];
$aOperador = [];
$aWhere['active'] = 't';
$aWhere['id_nivel'] = '1100,2500';
$aOperador['id_nivel'] = 'BETWEEN';
$aWhere['_ordre'] = 'id_nivel';
$cAsignaturas = $AsignaturaRepository->getAsignaturasAsJson($aWhere, $aOperador);
$data['cAsignaturas'] = $cAsignaturas;

// Asignaturas cursadas:
// solamente las notas de mi región_stgr. Normalmente serian las notas_dl,
// pero para casos como H-Hv...
$gesDelegacion = $GLOBALS['container']->get(DelegacionRepositoryInterface::class);
$mi_dl = ConfigGlobal::mi_dele();
$a_mi_region_stgr = $gesDelegacion->mi_region_stgr($mi_dl);
$region_stgr = $a_mi_region_stgr['region_stgr'];
$mi_sfsv = ConfigGlobal::mi_sfsv();
$a_id_schemas_rstgr = $gesDelegacion->getArrayIdSchemaRegionStgr($region_stgr, $mi_sfsv);
if (empty($a_id_schemas_rstgr)) {
    $error_txt .= "\n" . _("Debe definir la región del stgr a la que pertenece");
}

if (empty($error_txt)) {
    $PersonaNotaDBRepository = $GLOBALS['container']->get(PersonaNotaDBRepositoryInterface::class);
    $aWhere = [];
    $aOperador = [];
    $aWhere['id_schema'] = implode(',', $a_id_schemas_rstgr);
    $aOperador['id_schema'] = 'IN';
    $aWhere['id_nom'] = $id_nom;
    $aWhere['id_nivel'] = '1100,2500';
    $aOperador['id_nivel'] = 'BETWEEN';
    $aWhere['tipo_acta'] = TipoActa::FORMATO_ACTA;
    $cNotas = $PersonaNotaDBRepository->getPersonaNotas($aWhere, $aOperador);
    $aAprobadas = [];
    $AsignaturaRepository = $GLOBALS['container']->get(AsignaturaRepositoryInterface::class);
    foreach ($cNotas as $oPersonaNota) {
        $id_asignatura = $oPersonaNota->getIdAsignaturaVo()->value();
        $id_nivel = $oPersonaNota->getIdNivelVo()->value();

        $oAsignatura = $AsignaturaRepository->findById($id_asignatura);
        if ($oAsignatura === null) {
            throw new \Exception(sprintf(_("No se ha encontrado la asignatura con id: %s"), $id_asignatura));
        }
        if ($id_asignatura > 3000) {
            $id_nivel_asig = $id_nivel;
        } else {
            if (!$oAsignatura->isActive()) {
                continue;
            }
            $id_nivel_asig = $oAsignatura->getIdNivelVo()->value();
        }
        $creditos = $oAsignatura->getCreditos();
        $n = $id_nivel_asig;
        $aAprobadas[$n]['id_nivel_asig'] = $id_nivel_asig;
        $aAprobadas[$n]['id_nivel'] = $id_nivel;
        $aAprobadas[$n]['id_asignatura'] = $id_asignatura;
        $aAprobadas[$n]['nombre_asignatura'] = $oAsignatura->getNombre_asignatura();
        $aAprobadas[$n]['creditos'] = number_format(($creditos * 2), 0);
        $aAprobadas[$n]['nota_txt'] = $oPersonaNota->getNota_txt();
    }
    $data['aAprobadas'] = $aAprobadas;
}

// envía una Response
ContestarJson::enviar($error_txt, $data);