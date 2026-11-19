<?php

/**
 * Revisión de las consultas hechas a la ayuda («Preguntar a la documentación»): qué se ha
 * preguntado, qué no tuvo respuesta y qué se marcó como «no me ha servido».
 *
 * Parámetros: `estado` (1 pendiente, 2 resuelta, 3 descartada) y `ver` (`problemas` | `todas`).
 */

use frontend\shared\config\AppUrlConfig;
use frontend\shared\FrontBootstrap;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashF;

require_once __DIR__ . '/../FrontBootstrap.php';

$oPosicion = FrontBootstrap::boot();

/**
 * @return array{href: string, full_url: string, parametros: string}
 */
function ayudaConsultasEnlace(string $path, string $query = ''): array
{
    $fullUrl = AppUrlConfig::getPublicAppBaseUrl() . $path;

    return [
        'href' => HashF::link($query === '' ? $fullUrl : $fullUrl . '?' . $query),
        'full_url' => $fullUrl,
        'parametros' => HashF::add_hash($query, $fullUrl),
    ];
}

$estadoRaw = $_POST['estado'] ?? $_GET['estado'] ?? '';
$verRaw = $_POST['ver'] ?? $_GET['ver'] ?? '';

$data = PostRequest::getDataFromUrl('/src/shared/ayuda_consultas_lista_data', [
    'estado' => is_scalar($estadoRaw) ? (string) $estadoRaw : '',
    'ver' => is_scalar($verRaw) ? (string) $verRaw : '',
]);

$estado = is_numeric($data['estado'] ?? null) ? (int) $data['estado'] : 1;
$ver = ($data['ver'] ?? '') === 'todas' ? 'todas' : 'problemas';
$path = '/frontend/shared/controller/ayuda_consultas_lista.php';

// Un enlace por estado (conserva «ver») y uno por cada «ver» (conserva el estado).
$enlacesEstado = [];
foreach (is_array($data['opciones_estado'] ?? null) ? $data['opciones_estado'] : [] as $opcion) {
    if (!is_array($opcion) || !isset($opcion[0], $opcion[1])) {
        continue;
    }
    $enlacesEstado[] = [
        'texto' => (string) $opcion[1],
        'actual' => (int) $opcion[0] === $estado,
        'enlace' => ayudaConsultasEnlace($path, 'estado=' . (int) $opcion[0] . '&ver=' . $ver),
    ];
}
$enlacesVer = [];
foreach (['problemas' => _("sin respuesta o que no sirvieron"), 'todas' => _("todas")] as $valor => $texto) {
    $enlacesVer[] = [
        'texto' => $texto,
        'actual' => $valor === $ver,
        'enlace' => ayudaConsultasEnlace($path, 'estado=' . $estado . '&ver=' . $valor),
    ];
}

$urlEstado = AppUrlConfig::srcBrowserUrl('/src/shared/ayuda_consultas_estado_update');
$oHash = new HashF();
$oHash->setUrl($urlEstado);
$oHash->setCamposForm('ctx_estado');

$oView = new ViewNewPhtml('frontend\shared\controller');
echo $oView->renderizar('ayuda_consultas_lista.phtml', [
    'oPosicion' => $oPosicion,
    'aviso_txt' => is_string($data['aviso_txt'] ?? null) ? $data['aviso_txt'] : '',
    'grupos' => is_array($data['grupos'] ?? null) ? $data['grupos'] : [],
    'total_consultas' => is_numeric($data['total_consultas'] ?? null) ? (int) $data['total_consultas'] : 0,
    'tsv' => is_string($data['tsv'] ?? null) ? $data['tsv'] : '',
    'enlacesEstado' => $enlacesEstado,
    'enlacesVer' => $enlacesVer,
    'enlaceActual' => ayudaConsultasEnlace($path, 'estado=' . $estado . '&ver=' . $ver),
    'enlacePreguntar' => ayudaConsultasEnlace('/frontend/shared/controller/ayuda_preguntar.php'),
    'urlEstado' => $urlEstado,
    'hashEstado' => $oHash->getParamAjaxEnArray(),
], false);
