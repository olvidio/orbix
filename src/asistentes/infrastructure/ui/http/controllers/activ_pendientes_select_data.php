<?php

use frontend\shared\config\AppUrlConfig;
use frontend\shared\web\ContestarJson;
use frontend\shared\web\Lista;
use src\asistentes\application\ActivPendientesSelectData;
use frontend\shared\security\HashFront;

$data = ActivPendientesSelectData::build($_POST);

/**
 * @param array<int|string, mixed> $a_valores
 * @return array<int|string, mixed>
 */
$resolveLinks = static function (array $a_valores): array {
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
    return $a_valores;
};

$a_cabeceras = $data['a_cabeceras_activ_pendientes'] ?? [];
$a_valores_dl = $resolveLinks($data['a_valores_activ_pendientes_dl'] ?? []);
$a_valores_otras = $resolveLinks($data['a_valores_activ_pendientes_otras'] ?? []);

$oTablaDl = new Lista();
$oTablaDl->setId_tabla('activ_pendientes_select');
$oTablaDl->setCabeceras($a_cabeceras);
$oTablaDl->setDatos($a_valores_dl);
$data['tabla_dl_html'] = $oTablaDl->mostrar_tabla();

$oTablaOtrasDl = new Lista();
$oTablaOtrasDl->setId_tabla('activ_pendientes_select_otras');
$oTablaOtrasDl->setCabeceras($a_cabeceras);
$oTablaOtrasDl->setDatos($a_valores_otras);
$data['tabla_otras_html'] = $oTablaOtrasDl->mostrar_tabla();

unset(
    $data['a_cabeceras_activ_pendientes'],
    $data['a_valores_activ_pendientes_dl'],
    $data['a_valores_activ_pendientes_otras'],
);

ContestarJson::enviar('', $data);
