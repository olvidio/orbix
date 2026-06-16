<?php

use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\is_true;

use frontend\shared\config\OrbixRuntime;
use src\actividades\domain\value_objects\NivelStgrId;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\certificados\application\support\CertificadosSession;
use src\certificados\domain\contracts\CertificadoEmitidoRepositoryInterface;
use src\configuracion\domain\value_objects\ConfigSnapshot;
use src\notas\domain\contracts\PersonaNotaOtraRegionStgrRepositoryInterface;
use src\notas\domain\contracts\PersonaNotaRepositoryInterface;
use src\notas\domain\entity\PersonaNota;
use src\notas\domain\entity\PersonaNotaOtraRegionStgr;
use src\notas\domain\value_objects\TipoActa;
use src\personas\domain\entity\Persona;
use src\shared\config\ConfigGlobal;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;

/** @var CertificadoEmitidoRepositoryInterface $certificadoEmitidoRepository */
$certificadoEmitidoRepository = DependencyResolver::get(CertificadoEmitidoRepositoryInterface::class);
/** @var AsignaturaRepositoryInterface $asignaturaRepository */
$asignaturaRepository = DependencyResolver::get(AsignaturaRepositoryInterface::class);
/** @var DelegacionRepositoryInterface $delegacionRepository */
$delegacionRepository = DependencyResolver::get(DelegacionRepositoryInterface::class);
/** @var PersonaNotaRepositoryInterface $personaNotaRepository */
$personaNotaRepository = DependencyResolver::get(PersonaNotaRepositoryInterface::class);

$id_item = input_int($_POST, 'id_item');
$error_txt = '';
$data = [];

$oCertificadoEmitido = $certificadoEmitidoRepository->findById($id_item);
if ($oCertificadoEmitido === null) {
    $error_txt .= '<br>No encuentro certificado emitido con id_item: ' . $id_item;
    ContestarJson::enviar($error_txt, $data);
    return;
}

$id_nom = (int) ($oCertificadoEmitido->getId_nom() ?? 0);
$nom = (string) ($oCertificadoEmitido->getNom() ?? '');
$idioma = (string) ($oCertificadoEmitido->getIdiomaVo()?->value() ?? '');
$certificado = (string) ($oCertificadoEmitido->getCertificado() ?? '');
$f_certificado = $oCertificadoEmitido->getF_certificado()?->getFromLocal() ?? '';
$firmado = $oCertificadoEmitido->isFirmado();
$chk_firmado = is_true($firmado) ? 'checked' : '';

$oConfig = $_SESSION['oConfig'] ?? null;
if (!$oConfig instanceof ConfigSnapshot) {
    ContestarJson::enviar(_('Configuración de sesión no disponible'), $data);
    return;
}

$error_txt .= $oConfig->formatMissingParametersMessage([
    _('nombre región en latín') => $oConfig->regionLatin,
    _('vstgr') => $oConfig->vstgr,
    _('direccion stgr') => $oConfig->dirStgr,
    _('lugar firma') => $oConfig->lugarFirma,
]);

$oPersona = Persona::findPersonaEnGlobal($id_nom);
if ($error_txt === '' && $oPersona === null) {
    $error_txt .= "<br>No encuentro a nadie con id_nom: $id_nom en " . __FILE__ . ': line ' . __LINE__;
}
if ($error_txt !== '') {
    ContestarJson::enviar($error_txt, $data);
    return;
}

$apellidos_nombre = $oPersona->getApellidosNombre();
$nom = $nom === '' ? $apellidos_nombre : $nom;
$lugar_nacimiento = (string) ($oPersona->getLugarNacimientoVo()?->value() ?? $oPersona->getLugar_nacimiento() ?? '');
$f_nacimiento = (string) ($oPersona->getF_nacimiento()?->getFechaLatin() ?? '');
$nivelStgrId = $oPersona->getNivelStgrVo()?->value();

$region_latin = (string) $oConfig->regionLatin;
$vstgr = (string) $oConfig->vstgr;
$dir_stgr = (string) $oConfig->dirStgr;
$lugar_firma = (string) $oConfig->lugarFirma;

$oHoy = new DateTimeLocal();
$lugar_fecha = $lugar_firma . ',  ' . $oHoy->getFechaLatin();
$replace = OrbixRuntime::latinHtmlEntityReplaceMap();

$txt_superavit_1 = $txt_superavit_2 = $curso_filosofia = $any_I = $ECTS = $iudicium = '';
$curso_teologia = $pie_ects = $any_II = $any_III = $any_IV = $titulo_1 = $titulo_2 = $titulo_3 = '';
$infra = $sello = $fidem = $reg_num = '';

$filename_textos = __DIR__ . '/textos_certificados.php';
if ($idioma !== '') {
    $dir = ConfigGlobal::$dir_languages . '/' . $idioma;
    $filename_textos = $dir . '/textos_certificados.php';
    if (!file_exists($filename_textos)) {
        $error_txt .= sprintf(
            _('No existe un fichero con las traducciones para %s. Deje el idioma en blanco para latín o elija un idioma disponible (p. ej. es_ES.UTF-8).'),
            $idioma,
        );
        ContestarJson::enviar($error_txt, $data);
        return;
    }
}
include $filename_textos;

$txt_superavit = $nivelStgrId === NivelStgrId::R
    ? strtr($txt_superavit_1 . ' ' . $txt_superavit_2, $replace)
    : '';

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
$data['f_certificado'] = $f_certificado;
$data['chk_firmado'] = $chk_firmado;

$aWhere = ['active' => 't', 'id_nivel' => '1100,2500', '_ordre' => 'id_nivel'];
$aOperador = ['id_nivel' => 'BETWEEN'];
$data['cAsignaturas'] = $asignaturaRepository->getAsignaturasAsJson($aWhere, $aOperador);

$mi_dl = ConfigGlobal::mi_dele();
$a_mi_region_stgr = $delegacionRepository->mi_region_stgr($mi_dl);
$region_stgr = is_string($a_mi_region_stgr['region_stgr'] ?? null) ? $a_mi_region_stgr['region_stgr'] : '';
$a_id_schemas_rstgr = $delegacionRepository->getArrayIdSchemaRegionStgr($region_stgr, ConfigGlobal::mi_sfsv());
if ($a_id_schemas_rstgr === []) {
    $error_txt .= "\n" . _('Debe definir la región del stgr a la que pertenece');
}

if ($error_txt === '') {
    $aWhereNotas = [
        'id_schema' => implode(',', $a_id_schemas_rstgr),
        'id_nom' => $id_nom,
        'id_nivel' => '1100,2500',
        'tipo_acta' => TipoActa::FORMATO_ACTA,
    ];
    $aOperadorNotas = ['id_schema' => 'IN', 'id_nivel' => 'BETWEEN'];
    $cNotas = $personaNotaRepository->getPersonaNotas($aWhereNotas, $aOperadorNotas);

    $esquemaRegionStgr = CertificadosSession::esquemaRegionStgr();
    if ($esquemaRegionStgr !== '') {
        /** @var PersonaNotaOtraRegionStgrRepositoryInterface $personaNotaOtraRepo */
        $personaNotaOtraRepo = DependencyResolver::make(
            PersonaNotaOtraRegionStgrRepositoryInterface::class,
            ['esquema_region_stgr' => $esquemaRegionStgr],
        );
        $cNotas = array_merge($cNotas, $personaNotaOtraRepo->getPersonaNotas($aWhereNotas, $aOperadorNotas));
    }

    /** @var list<PersonaNota|PersonaNotaOtraRegionStgr> $cNotas */
    $aAprobadas = [];
    foreach ($cNotas as $oPersonaNota) {
        $idAsignaturaVo = $oPersonaNota->getIdAsignaturaVo();
        $idNivelVo = $oPersonaNota->getIdNivelVo();
        $id_asignatura = $idAsignaturaVo->value();
        $id_nivel = $idNivelVo->value();
        $oAsignatura = $asignaturaRepository->findById($id_asignatura);
        if ($oAsignatura === null) {
            throw new \Exception(sprintf(_('No se ha encontrado la asignatura con id: %s'), $id_asignatura));
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
        $aAprobadas[$n]['creditos'] = number_format($creditos * 2, 0);
        $aAprobadas[$n]['nota_txt'] = $oPersonaNota->getNota_txt();
    }
    $data['aAprobadas'] = $aAprobadas;
}

ContestarJson::enviar($error_txt, $data);
