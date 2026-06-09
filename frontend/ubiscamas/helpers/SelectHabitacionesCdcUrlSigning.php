<?php

declare(strict_types=1);

namespace frontend\ubiscamas\helpers;

use frontend\shared\config\AppUrlConfig;
use frontend\shared\security\HashFront;

require_once __DIR__ . '/ubiscamas_support.php';

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
        $spec = $in['url_nuevo_spec'] ?? null;
        if ($spec !== null) {
            $path = tessera_imprimir_string($spec['path'] ?? '');
            $query = is_array($spec['query'] ?? null) ? $spec['query'] : [];
            if ($path !== '') {
                $url = $base . '/' . ltrim($path, '/') . '?' . http_build_query($query);
                $urlNuevo = HashFront::link($url);
            }
        }
        $aLinksDl = [];
        foreach ($in['a_links_dl_specs'] ?? [] as $item) {
            $label = tessera_imprimir_string($item['label'] ?? '');
            $itemSpec = $item['spec'] ?? null;
            if ($label === '' || !is_array($itemSpec)) {
                continue;
            }
            $path = tessera_imprimir_string($itemSpec['path'] ?? '');
            $query = is_array($itemSpec['query'] ?? null) ? $itemSpec['query'] : [];
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
