<?php
/**
 * Endpoint backend que calcula el listado de actividades para la pantalla
 * `actividad_select`. Recibe los filtros via POST y delega en el caso de
 * uso ActividadSelectListado. La respuesta se sirve como JSON mediante
 * ContestarJson::enviar (patron establecido en refactor.md).
 * Resuelve `link_spec`, firma enlaces y renderiza `html_tabla` / advertencia aquí.
 */

use frontend\shared\config\AppUrlConfig;
use frontend\shared\web\ContestarJson;
use frontend\shared\web\Lista;
use src\actividades\application\ActividadSelectListado;
use frontend\shared\security\HashFront;

$input = [
    'continuar' => (string)filter_input(INPUT_POST, 'continuar'),
    'modo' => (string)filter_input(INPUT_POST, 'modo'),
    'status' => (int)filter_input(INPUT_POST, 'status'),
    'id_tipo_activ' => (string)filter_input(INPUT_POST, 'id_tipo_activ'),
    'filtro_lugar' => (string)filter_input(INPUT_POST, 'filtro_lugar'),
    'id_ubi' => (int)filter_input(INPUT_POST, 'id_ubi'),
    'nom_activ' => (string)filter_input(INPUT_POST, 'nom_activ'),
    'periodo' => (string)filter_input(INPUT_POST, 'periodo'),
    'year' => (string)filter_input(INPUT_POST, 'year'),
    'dl_org' => (string)filter_input(INPUT_POST, 'dl_org'),
    'empiezamin' => (string)filter_input(INPUT_POST, 'empiezamin'),
    'empiezamax' => (string)filter_input(INPUT_POST, 'empiezamax'),
    'fases_on' => (array)filter_input(INPUT_POST, 'fases_on', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY),
    'fases_off' => (array)filter_input(INPUT_POST, 'fases_off', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY),
    'publicado' => (int)filter_input(INPUT_POST, 'publicado'),
    'ssfsv' => (string)filter_input(INPUT_POST, 'ssfsv'),
    'sasistentes' => (string)filter_input(INPUT_POST, 'sasistentes'),
    'sactividad' => (string)filter_input(INPUT_POST, 'sactividad'),
    'sactividad2' => (string)filter_input(INPUT_POST, 'sactividad2'),
    'sel' => (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY),
    'scroll_id' => (string)filter_input(INPUT_POST, 'scroll_id'),
];
$stackGo = (int)filter_input(INPUT_POST, 'stack_go');

$useCase = new ActividadSelectListado();
$data = $useCase->ejecutar($input, $stackGo);

if (!empty($data['advertencia_demasiadas']) && is_array($data['advertencia_demasiadas'])) {
    $ad = $data['advertencia_demasiadas'];
    $baseUrl = AppUrlConfig::getPublicAppBaseUrl();
    $cSpec = $ad['continuar_link_spec'] ?? [];
    $vSpec = $ad['volver_link_spec'] ?? [];
    $cPath = is_array($cSpec) ? (string)($cSpec['path'] ?? '') : '';
    $cQuery = is_array($cSpec) && is_array($cSpec['query'] ?? null) ? $cSpec['query'] : [];
    $vPath = is_array($vSpec) ? (string)($vSpec['path'] ?? '') : '';
    $vQuery = is_array($vSpec) && is_array($vSpec['query'] ?? null) ? $vSpec['query'] : [];
    $go_avant = $cPath !== '' ? HashFront::link($baseUrl . '/' . ltrim($cPath, '/') . '?' . http_build_query($cQuery)) : '';
    $go_atras = $vPath !== '' ? HashFront::link($baseUrl . '/' . ltrim($vPath, '/') . '?' . http_build_query($vQuery)) : '';
    $numActiv = (int)($ad['num_actividades'] ?? 0);
    $html_advertencia = '<h2>' . sprintf(_("son %s actividades a mostrar. ¿Seguro que quiere continuar?."), $numActiv) . '</h2>';
    $html_advertencia .= "<input type='button' onclick=fnjs_update_div('#main','" . $go_avant . "') value=" . _("continuar") . ">";
    $html_advertencia .= "<input type='button' onclick=fnjs_update_div('#main','" . $go_atras . "') value=" . _("volver") . ">";
    $data['html_advertencia'] = $html_advertencia;
    $data['html_tabla'] = '';
    unset($data['advertencia_demasiadas']);
} else {
    unset($data['advertencia_demasiadas']);
    $a_valores = $data['a_valores'] ?? [];
    $baseUrl = AppUrlConfig::getPublicAppBaseUrl();
    foreach ($a_valores as $idx => $fila) {
        if (!is_array($fila)) {
            continue;
        }
        foreach ($fila as $colKey => $cell) {
            if (!is_array($cell) || !isset($cell['link_spec'])) {
                continue;
            }
            $spec = $cell['link_spec'];
            $path = (string)($spec['path'] ?? '');
            $query = is_array($spec['query'] ?? null) ? $spec['query'] : [];
            if ($path === '') {
                continue;
            }
            $url = $baseUrl . '/' . ltrim($path, '/') . '?' . http_build_query($query);
            $a_valores[$idx][$colKey]['ira'] = HashFront::link($url);
            unset($a_valores[$idx][$colKey]['link_spec']);
        }
    }
    $oTabla = new Lista();
    $oTabla->setId_tabla('actividad_select');
    $oTabla->setCabeceras($data['a_cabeceras'] ?? []);
    $oTabla->setBotones($data['a_botones'] ?? []);
    $oTabla->setDatos($a_valores);
    $data['html_tabla'] = $oTabla->mostrar_tabla();
}

unset($data['a_cabeceras'], $data['a_botones'], $data['a_valores']);

ContestarJson::enviar('', $data);
