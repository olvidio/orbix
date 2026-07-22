<?php

use frontend\notas\helpers\NotasPayload;
use frontend\actividadestudios\helpers\ActividadestudiosConfig;
use frontend\actividadestudios\helpers\ActaNotasPayload;
use frontend\actividadestudios\helpers\ActividadestudiosPostInput;
use frontend\actividadestudios\helpers\ActividadestudiosRenderSupport;
use frontend\shared\helpers\PayloadCoercion;
use frontend\shared\helpers\ListNavSupport;

/**
 * Pantalla del acta de notas para una asignatura concreta de una actividad.
 *
 * Sucesor de `apps/actividadestudios/controller/acta_notas.php`. Incluye
 * `frontend/notas/controller/acta_ver.php` para pintar el form del acta, y
 * debajo la tabla de alumnos matriculados con su nota. Las acciones de
 * guardar borrador / grabar definitivas apuntan a los endpoints nuevos de
 * `src/actividadestudios/`.
 */

use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\web\Desplegable;
use frontend\shared\web\Posicion;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

$oPosicion = FrontBootstrap::boot();

$navState = ListNavSupport::buildActaNotasReturnParametros();
$ids = ActividadestudiosPostInput::idActivAsignatura();
$id_activ = $ids['id_activ'];
$id_asignatura = $ids['id_asignatura'];

$bloqueRaw = filter_input(INPUT_POST, 'bloque');
$bloque = is_string($bloqueRaw) && $bloqueRaw !== '' ? $bloqueRaw : '#main';

$oPosicion->nav()->enter(
    PayloadCoercion::string($_SERVER['PHP_SELF'] ?? ''),
    $bloque,
    ['id_activ' => $id_activ, 'id_asignatura' => $id_asignatura],
    $navState,
);

$d = ActividadestudiosRenderSupport::stringKeyRow(PostRequest::getDataFromUrl('/src/actividadestudios/acta_notas_data', [
    'id_activ' => $id_activ,
    'id_asignatura' => $id_asignatura,
]));
$datos = ActaNotasPayload::fromPayload($d);

$permiso = $datos['permiso'];
$nom_activ = $datos['nom_activ'];
$matriculados = $datos['matriculados'];
$matriculas_rows = $datos['matriculas_rows'];
$notas = $datos['notas'];
$acta_principal = $datos['acta_principal'];
$acta_notas_a_actas = $datos['acta_notas_a_actas'];
$acta_txt_cursada = $datos['acta_txt_cursada'];

$oDesplActas = new Desplegable();
$oDesplActas->setNombre('acta_nota[]');
$oDesplActas->setOpciones($datos['despl_actas_opciones']);

$msg_err = $datos['msg_err'];
$nota_max_default = ActividadestudiosConfig::notaMaxDefault();

$oHashNotas = new HashFront();
$oHashNotas->setCamposForm('id_nom!nota_num!nota_max!form_preceptor!acta_nota');
$oHashNotas->setCamposNo('que');
$oHashNotas->setArraycamposHidden([
    'id_pau' => (int)filter_input(INPUT_POST, 'id_pau'),
    'id_activ' => $id_activ,
    'opcional' => \frontend\shared\helpers\PayloadCoercion::string(filter_input(INPUT_POST, 'opcional')),
    'primary_key_s' => \frontend\shared\helpers\PayloadCoercion::string(filter_input(INPUT_POST, 'primary_key_s')),
    'id_asignatura' => $id_asignatura,
    'id_nivel' => (int)filter_input(INPUT_POST, 'id_nivel'),
    'matriculados' => $matriculados,
]);

if ($msg_err !== '') {
    echo \frontend\shared\helpers\PayloadCoercion::string($msg_err);
}

if ($matriculados === 0) {
    echo _('no hay ninguna persona matriculada de esta asignatura');
}

$txt_alert_acta = _('primero debe guadar los datos del acta');

// Form del acta (dossier notas). Comparte las variables de scope como antes.
include_once 'frontend/notas/controller/acta_ver.php';

$web = AppUrlConfig::getPublicAppBaseUrl();
$url_matricula_guardar = AppUrlConfig::srcBrowserUrl('/src/actividadestudios/acta_notas_matricula_guardar');
$url_notas_definitivas = AppUrlConfig::srcBrowserUrl('/src/actividadestudios/acta_notas_definitivas_grabar');

$a_campos = [
    'oPosicion' => $oPosicion,
    'oHashNotas' => $oHashNotas,
    'permiso' => $permiso,
    'Qque' => \frontend\shared\helpers\PayloadCoercion::string(filter_input(INPUT_POST, 'que')),
    'matriculas_rows' => $matriculas_rows,
    'oDesplActas' => $oDesplActas,
    'acta_principal' => $acta_principal,
    'txt_alert_acta' => $txt_alert_acta,
    'nota_max_default' => $nota_max_default,
    'url_matricula_guardar' => $url_matricula_guardar,
    'url_notas_definitivas' => $url_notas_definitivas,
    'acta_txt_cursada' => $acta_txt_cursada,
];

$oView = new ViewNewPhtml('frontend\\actividadestudios\\controller');
$oView->renderizar('acta_notas.phtml', $a_campos);
