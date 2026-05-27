<?php

declare(strict_types=1);

namespace frontend\ubiscamas\helpers;

use frontend\shared\config\AppUrlConfig;
use frontend\shared\security\HashFront;

/**
 * Firma URLs hacia habitacion_form para el widget CDC (specs desde
 * {@see \src\ubiscamas\domain\Select_habitaciones_cdc::getSegmentData()},
 * invocación típica {@see \frontend\ubiscamas\helpers\SelectHabitacionesCdcRender}).
 */
final class SelectHabitacionesCdcUrlSigning
{
    /**
     * @param array{
     *   url_nuevo_spec?: array{path?: string, query?: array<string, mixed>},
     *   a_links_dl_specs?: list<array{label?: string, spec?: array{path?: string, query?: array<string, mixed>}}>
     * } $in
     * @return array{url_nuevo: string, aLinks_dl: array<string, string>}
     */
    public static function sign(array $in): array
    {
        $base = rtrim(AppUrlConfig::getPublicAppBaseUrl(), '/');
        $urlNuevo = '';
        if (!empty($in['url_nuevo_spec']) && is_array($in['url_nuevo_spec'])) {
            $spec = $in['url_nuevo_spec'];
            $path = (string)($spec['path'] ?? '');
            $query = is_array($spec['query'] ?? null) ? $spec['query'] : [];
            if ($path !== '') {
                $url = $base . '/' . ltrim($path, '/') . '?' . http_build_query($query);
                $urlNuevo = HashFront::link($url);
            }
        }
        $aLinksDl = [];
        foreach ($in['a_links_dl_specs'] ?? [] as $item) {
            if (!is_array($item)) {
                continue;
            }
            $label = (string)($item['label'] ?? '');
            $spec = $item['spec'] ?? null;
            if ($label === '' || !is_array($spec)) {
                continue;
            }
            $path = (string)($spec['path'] ?? '');
            $query = is_array($spec['query'] ?? null) ? $spec['query'] : [];
            if ($path === '') {
                continue;
            }
            $url = $base . '/' . ltrim($path, '/') . '?' . http_build_query($query);
            $aLinksDl[$label] = HashFront::link($url);
        }

        return [
            'url_nuevo' => $urlNuevo,
            'aLinks_dl' => $aLinksDl,
        ];
    }
}
