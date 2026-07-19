<?php

declare(strict_types=1);

namespace frontend\asistentes\helpers;

use frontend\actividades\helpers\ActividadesListaSupport;
use frontend\shared\config\AppUrlConfig;
use frontend\shared\helpers\PayloadCoercion;
use frontend\shared\security\HashFront;

/**
 * Tabla de peticiones de cambio de actividad (dossier asistentes).
 */
final class AsistentesTablaPeticionesSupport
{
    /**
     * @param array<int|string, mixed> $payload
     * @return array{api_save_url: string, cabeceras: list<array<string, mixed>|string>, botones: list<array<string, mixed>>, valores: array<int|string, mixed>}
     */
    public static function fromPayload(array $payload): array
    {
        $paths = is_array($payload['paths'] ?? null) ? $payload['paths'] : [];
        $apiSaveUrl = AppUrlConfig::browserUrlFromAppRelative(
            \frontend\shared\helpers\PayloadCoercion::string($paths['asistente_guardar'] ?? '')
        );

        return [
            'api_save_url' => $apiSaveUrl,
            'cabeceras' => ActividadesListaSupport::cabeceras($payload['a_cabeceras'] ?? []),
            'botones' => ActividadesListaSupport::botones($payload['a_botones'] ?? []),
            'valores' => ActividadesListaSupport::datos($payload['a_valores'] ?? []),
        ];
    }

    /**
     * @param array<int|string, mixed> $fila
     * @return array<int|string, mixed>
     */
    public static function resolveCell(array $fila, string $apiSaveUrl): array
    {
        if (!array_key_exists(2, $fila) || !is_array($fila[2])) {
            return $fila;
        }
        $col2 = $fila[2];
        $parts = self::peticionesParts($col2['peticiones_parts'] ?? []);
        $out = '';
        foreach ($parts as $p) {
            if ($p['t'] === 'p') {
                $out .= $p['s'];
            } elseif ($p['t'] === 'm' && $p['h'] !== []) {
                $oHash = new HashFront();
                $oHash->setUrl($apiSaveUrl);
                $oHash->setArrayCamposHidden($p['h']);
                $param = $oHash->getParamAjax();
                $out .= '<span class="link" onClick="fnjs_cambiar_actividad(\'' . $param . '\')">'
                    . htmlspecialchars($p['s'], ENT_QUOTES, 'UTF-8') . '</span>';
            }
        }
        $fila[2] = $out;

        return $fila;
    }

    /**
     * @return list<array{t: string, s: string, h: array<string, mixed>}>
     */
    private static function peticionesParts(mixed $raw): array
    {
        if (!is_array($raw)) {
            return [];
        }
        $out = [];
        foreach ($raw as $part) {
            $out[] = self::peticionPart($part);
        }

        return $out;
    }

    /**
     * @return array{t: string, s: string, h: array<string, mixed>}
     */
    private static function peticionPart(mixed $raw): array
    {
        if (!is_array($raw)) {
            return ['t' => '', 's' => '', 'h' => []];
        }

        return [
            't' => \frontend\shared\helpers\PayloadCoercion::string($raw['t'] ?? ''),
            's' => \frontend\shared\helpers\PayloadCoercion::string($raw['s'] ?? ''),
            'h' => AsistentesRenderSupport::hashCamposHidden($raw['h'] ?? []),
        ];
    }
}
