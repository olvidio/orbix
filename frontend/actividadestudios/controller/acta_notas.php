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

use frontend\shared\PostRequest;
use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\web\Desplegable;
use frontend\shared\security\HashFront;
use frontend\shared\web\Posicion;

require_once("frontend/shared/global_header_front.inc");
require_once 'apps/core/global_object.inc';

$Qrefresh = (int) filter_input(INPUT_POST, 'refresh');
$oPosicion->recordar($Qrefresh);

$notas = 1;

$a_sel = (array) filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

$Qid_sel = '';
$Qscroll_id = (string) filter_input(INPUT_POST, 'scroll_id');
if (isset($_POST['stack'])) {
    $stack = filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    if ($stack !== '') {
        $oPosicion2 = new Posicion();
        if ($oPosicion2->goStack($stack)) {
            $Qid_sel = $oPosicion2->getParametro('id_sel');
            $Qscroll_id = $oPosicion2->getParametro('scroll_id');
            $oPosicion2->olvidar($stack);
        }
    }
}

if (!empty($a_sel)) {
    $parts = explode('#', $a_sel[0]);
    $id_activ = (int)($parts[0] ?? 0);
    $id_asignatura = (int)($parts[1] ?? 0);
} else {
    $id_asignatura = (int) filter_input(INPUT_POST, 'id_asignatura');
    $id_activ = (int) filter_input(INPUT_POST, 'id_activ');
}

$d = PostRequest::getDataFromUrl('/src/actividadestudios/acta_notas_data', [
    'id_activ' => $id_activ,
    'id_asignatura' => $id_asignatura,
]);

$permiso = (int)($d['permiso'] ?? 1);
$nom_activ = $d['nom_activ'] ?? '';
$matriculados = (int)($d['matriculados'] ?? 0);
$matriculas_rows = $d['matriculas_rows'] ?? [];
$notas = $d['notas'] ?? 'nuevo';
$acta_principal = $d['acta_principal'] ?? '';
$acta_notas_a_actas = $d['acta_notas_a_actas'] ?? [];

$aOpciones = $d['despl_actas_opciones'] ?? [];
$oDesplActas = new Desplegable();
$oDesplActas->setNombre('acta_nota[]');
$oDesplActas->setOpciones($aOpciones);

$msg_err = $d['msg_err'] ?? '';

$nota_max_default = $_SESSION['oConfig']->getNotaMax();

$oHashNotas = new HashFront();
$oHashNotas->setCamposForm('id_nom!nota_num!nota_max!form_preceptor!acta_nota');
$oHashNotas->setCamposNo('que');
$oHashNotas->setArraycamposHidden([
    'id_pau' => (int) filter_input(INPUT_POST, 'id_pau'),
    'id_activ' => $id_activ,
    'opcional' => (string) filter_input(INPUT_POST, 'opcional'),
    'primary_key_s' => (string) filter_input(INPUT_POST, 'primary_key_s'),
    'id_asignatura' => $id_asignatura,
    'id_nivel' => (int) filter_input(INPUT_POST, 'id_nivel'),
    'matriculados' => $matriculados,
]);

if (!empty($msg_err)) {
    echo $msg_err;
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
    'Qque' => (string) filter_input(INPUT_POST, 'que'),
    'matriculas_rows' => $matriculas_rows,
    'oDesplActas' => $oDesplActas,
    'acta_principal' => $acta_principal,
    'txt_alert_acta' => $txt_alert_acta,
    'nota_max_default' => $nota_max_default,
    'url_matricula_guardar' => $url_matricula_guardar,
    'url_notas_definitivas' => $url_notas_definitivas,
];

$oView = new ViewNewPhtml('frontend\\actividadestudios\\controller');
$oView->renderizar('acta_notas.phtml', $a_campos);
