<?php

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

require_once __DIR__ . '/../helpers/actividadestudios_support.php';
require_once 'frontend/shared/FrontBootstrap.php';

$oPosicion = FrontBootstrap::boot();

$Qrefresh = (int)filter_input(INPUT_POST, 'refresh');
$oPosicion->recordar($Qrefresh);

$notas = 1;

$Qid_sel = '';
$Qscroll_id = tessera_imprimir_string(filter_input(INPUT_POST, 'scroll_id'));
if (isset($_POST['stack'])) {
    $stack = (int)filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    if ($stack !== 0) {
        $oPosicion2 = new Posicion();
        if ($oPosicion2->goStack($stack)) {
            $Qid_sel = $oPosicion2->getParametro('id_sel');
            $Qscroll_id = $oPosicion2->getParametro('scroll_id');
            $oPosicion2->olvidar($stack);
        }
    }
}

$ids = actividadestudios_id_activ_asignatura_from_post();
$id_activ = $ids['id_activ'];
$id_asignatura = $ids['id_asignatura'];

$d = actividadestudios_post_data(PostRequest::getDataFromUrl('/src/actividadestudios/acta_notas_data', [
    'id_activ' => $id_activ,
    'id_asignatura' => $id_asignatura,
]));
$datos = actividadestudios_acta_notas_from_payload($d);

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
$nota_max_default = actividadestudios_nota_max_default();

$oHashNotas = new HashFront();
$oHashNotas->setCamposForm('id_nom!nota_num!nota_max!form_preceptor!acta_nota');
$oHashNotas->setCamposNo('que');
$oHashNotas->setArraycamposHidden([
    'id_pau' => (int)filter_input(INPUT_POST, 'id_pau'),
    'id_activ' => $id_activ,
    'opcional' => tessera_imprimir_string(filter_input(INPUT_POST, 'opcional')),
    'primary_key_s' => tessera_imprimir_string(filter_input(INPUT_POST, 'primary_key_s')),
    'id_asignatura' => $id_asignatura,
    'id_nivel' => (int)filter_input(INPUT_POST, 'id_nivel'),
    'matriculados' => $matriculados,
]);

if ($msg_err !== '') {
    actividadestudios_echo_string($msg_err);
}

if ($matriculados === 0) {
    echo _('no hay ninguna persona matriculada de esta asignatura');
}

$txt_alert_acta = _('primero debe guadar los datos del acta');

// Form del acta (dossier notas). Comparte las variables de scope como antes.
include_once 'frontend/notas/controller/acta_ver.php';

$web = AppUrlConfig::getPublicAppBaseUrl();
$url_matricula_guardar = $web . '/src/actividadestudios/acta_notas_matricula_guardar';
$url_notas_definitivas = $web . '/src/actividadestudios/acta_notas_definitivas_grabar';

$a_campos = [
    'oPosicion' => $oPosicion,
    'oHashNotas' => $oHashNotas,
    'permiso' => $permiso,
    'Qque' => tessera_imprimir_string(filter_input(INPUT_POST, 'que')),
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
