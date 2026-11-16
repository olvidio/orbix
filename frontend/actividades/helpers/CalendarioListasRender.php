<?php

declare(strict_types=1);

namespace frontend\actividades\helpers;

use frontend\shared\web\Lista;

/**
 * Lista paginada del calendario de casas u oficinas (`calendario_listas`), con los datos de
 * `/src/actividades/calendario_listas_datos`: primero los avisos de actividades sin lugar y después la lista agrupada.
 */
final class CalendarioListasRender
{
    /**
     * @param array<string, mixed> $data
     */
    public static function html(array $data): string
    {
        if (isset($data['mensaje']) && is_string($data['mensaje'])) {
            return htmlspecialchars($data['mensaje'], ENT_QUOTES, 'UTF-8');
        }

        $avisos = '';
        foreach (is_array($data['avisos'] ?? null) ? $data['avisos'] : [] as $aviso) {
            $avisos .= htmlspecialchars((string) $aviso, ENT_QUOTES, 'UTF-8') . '<br>';
        }

        $oTabla = new Lista();
        $oTabla->setGrupos(is_array($data['grupos'] ?? null) ? $data['grupos'] : []);
        $oTabla->setCabeceras(is_array($data['cabeceras'] ?? null) ? $data['cabeceras'] : []);
        $oTabla->setDatos(is_array($data['datos'] ?? null) ? $data['datos'] : []);

        return $avisos . $oTabla->listaPaginada();
    }
}
