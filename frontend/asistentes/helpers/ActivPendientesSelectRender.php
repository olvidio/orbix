<?php

declare(strict_types=1);

namespace frontend\asistentes\helpers;

use frontend\shared\config\AppUrlConfig;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;

/**
 * Completa el JSON de {@see \src\asistentes\application\ActivPendientesSelectData} para la vista.
 */
final class ActivPendientesSelectRender
{
    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public static function enrich(array $payload): array
    {
        if (isset($payload['error'])) {
            return $payload;
        }

        $paths = isset($payload['paths']) && is_array($payload['paths']) ? $payload['paths'] : [];
        $base = rtrim(AppUrlConfig::getPublicAppBaseUrl(), '/');
        $formRel = (string)($paths['form_action'] ?? '');
        $payload['form_action'] = $formRel !== '' ? $base . '/' . ltrim($formRel, '/') : '';

        $hashMain = isset($payload['hash_main']) && is_array($payload['hash_main']) ? $payload['hash_main'] : [];
        $oHashForm = new HashFront();
        $oHashForm->setCamposForm((string)($hashMain['campos_form'] ?? 'tipo_personas!sactividad!any'));
        $cn = (string)($hashMain['campos_no'] ?? '');
        if ($cn !== '') {
            $oHashForm->setCamposNo($cn);
        }
        $hidden = $hashMain['campos_hidden'] ?? [];
        if (is_array($hidden) && $hidden !== []) {
            $oHashForm->setArrayCamposHidden($hidden);
        }
        $payload['hash_campos_html'] = $oHashForm->getCamposHtml();

        $baseUrl = AppUrlConfig::getPublicAppBaseUrl();
        $resolveLinks = static function (array $a_valores) use ($baseUrl): array {
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
                    $url = rtrim($baseUrl, '/') . '/' . ltrim($path, '/') . '?' . http_build_query($query);
                    $a_valores[$idx][$colKey]['ira'] = HashFront::link($url);
                    unset($a_valores[$idx][$colKey]['link_spec']);
                }
            }

            return $a_valores;
        };

        $a_cabeceras = $payload['a_cabeceras_activ_pendientes'] ?? [];
        $a_valores_dl = $resolveLinks((array)($payload['a_valores_activ_pendientes_dl'] ?? []));
        $a_valores_otras = $resolveLinks((array)($payload['a_valores_activ_pendientes_otras'] ?? []));

        $oTablaDl = new Lista();
        $oTablaDl->setId_tabla('activ_pendientes_select');
        $oTablaDl->setCabeceras(is_array($a_cabeceras) ? $a_cabeceras : []);
        $oTablaDl->setDatos($a_valores_dl);
        $payload['tabla_dl_html'] = $oTablaDl->mostrar_tabla();

        $oTablaOtrasDl = new Lista();
        $oTablaOtrasDl->setId_tabla('activ_pendientes_select_otras');
        $oTablaOtrasDl->setCabeceras(is_array($a_cabeceras) ? $a_cabeceras : []);
        $oTablaOtrasDl->setDatos($a_valores_otras);
        $payload['tabla_otras_html'] = $oTablaOtrasDl->mostrar_tabla();

        unset(
            $payload['paths'],
            $payload['hash_main'],
            $payload['a_cabeceras_activ_pendientes'],
            $payload['a_valores_activ_pendientes_dl'],
            $payload['a_valores_activ_pendientes_otras'],
        );

        return $payload;
    }
}
