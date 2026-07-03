<?php

declare(strict_types=1);

namespace frontend\actividadestudios\helpers;

use frontend\shared\helpers\PayloadCoercion;

final class CaPosiblesPayload
{
    /**
     * @param array<string, mixed> $payload
     * @return array{
     *     modo: string,
     *     msg_txt: string,
     *     titulo: string,
     *     stgr: string,
     *     aActividades: list<array{nom_activ: string, creditos: string, aLista: array<mixed>}>,
     *     pagina: string,
     *     filas: list<array<string, mixed>>,
     * }
     */
    public static function fromPayload(array $payload): array
    {
        return [
            'modo' => \frontend\shared\helpers\PayloadCoercion::string($payload['modo'] ?? ''),
            'msg_txt' => \frontend\shared\helpers\PayloadCoercion::string($payload['msg_txt'] ?? ''),
            'titulo' => \frontend\shared\helpers\PayloadCoercion::string($payload['titulo'] ?? ''),
            'stgr' => \frontend\shared\helpers\PayloadCoercion::string($payload['stgr'] ?? ''),
            'aActividades' => self::actividades($payload['aActividades'] ?? null),
            'pagina' => ActividadestudiosUrlSigning::signedLink($payload['pagina_link_spec'] ?? null),
            'filas' => self::rows($payload['tabla_filas'] ?? []),
        ];
    }

    /**
     * @return list<array{nom_activ: string, creditos: string, aLista: array<mixed>}>
     */
    private static function actividades(mixed $raw): array
    {
        if (!is_array($raw)) {
            return [];
        }
        $out = [];
        foreach ($raw as $item) {
            if (!is_array($item)) {
                continue;
            }
            $out[] = [
                'nom_activ' => \frontend\shared\helpers\PayloadCoercion::string($item['nom_activ'] ?? ''),
                'creditos' => \frontend\shared\helpers\PayloadCoercion::string($item['creditos'] ?? ''),
                'aLista' => is_array($item['aLista'] ?? null) ? $item['aLista'] : [],
            ];
        }

        return $out;
    }

    /**
     * @return array<string, array{stgr: string, aActividades: list<array{nom_activ: string, creditos: string, aLista: array<mixed>}>}>
     */
    private static function cPersonas(mixed $raw): array
    {
        if (!is_array($raw)) {
            return [];
        }
        $out = [];
        foreach ($raw as $nomPersona => $datos) {
            if (!is_array($datos)) {
                continue;
            }
            $nom = \frontend\shared\helpers\PayloadCoercion::string($nomPersona);
            if ($nom === '') {
                continue;
            }
            $out[$nom] = [
                'stgr' => \frontend\shared\helpers\PayloadCoercion::string($datos['stgr'] ?? ''),
                'aActividades' => self::actividades($datos['aActividades'] ?? null),
            ];
        }

        return $out;
    }

    /**
     * @param array<string, mixed> $row
     * @return array<string, mixed>
     */
    private static function row(array $row): array
    {
        return [
            'msg_txt' => \frontend\shared\helpers\PayloadCoercion::string($row['msg_txt'] ?? ''),
            'texto' => \frontend\shared\helpers\PayloadCoercion::string($row['texto'] ?? ''),
            'nc_bienio' => \frontend\shared\helpers\PayloadCoercion::string($row['nc_bienio'] ?? ''),
            'nc_cuadrienio1' => \frontend\shared\helpers\PayloadCoercion::string($row['nc_cuadrienio1'] ?? ''),
            'nc_cuadrienio2' => \frontend\shared\helpers\PayloadCoercion::string($row['nc_cuadrienio2'] ?? ''),
            'nc_cuadrienio' => \frontend\shared\helpers\PayloadCoercion::string($row['nc_cuadrienio'] ?? ''),
            'nc_repaso' => \frontend\shared\helpers\PayloadCoercion::string($row['nc_repaso'] ?? ''),
            'nc_ce' => \frontend\shared\helpers\PayloadCoercion::string($row['nc_ce'] ?? ''),
            'nc_otros' => \frontend\shared\helpers\PayloadCoercion::string($row['nc_otros'] ?? ''),
            'stgr' => \frontend\shared\helpers\PayloadCoercion::string($row['stgr'] ?? ''),
            'ctr' => \frontend\shared\helpers\PayloadCoercion::string($row['ctr'] ?? ''),
            'ref' => \frontend\shared\helpers\PayloadCoercion::string($row['ref'] ?? ''),
            'height' => \frontend\shared\helpers\PayloadCoercion::string($row['height'] ?? ''),
            'cPersonas' => self::cPersonas($row['cPersonas'] ?? null),
            'aActividades' => self::actividades($row['aActividades'] ?? null),
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    private static function rows(mixed $raw): array
    {
        if (!is_array($raw)) {
            return [];
        }
        $out = [];
        foreach ($raw as $item) {
            $parsed = ActividadestudiosRenderSupport::stringKeyRow($item);
            if ($parsed !== []) {
                $out[] = self::row($parsed);
            }
        }

        return $out;
    }
}
