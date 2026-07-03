<?php

declare(strict_types=1);

namespace frontend\notas\helpers;

use frontend\shared\helpers\PayloadCoercion;

final class ActaImprimirPayload
{
    /**
     * @param array<int|string, mixed> $payload
     * @return array<string, string>
     */
    public static function personasNotasFromPayload(array $payload): array
    {
        $raw = $payload['aPersonasNotas_list'] ?? [];
        if (!is_array($raw)) {
            return [];
        }
        $out = [];
        foreach ($raw as $row) {
            if (!is_array($row)) {
                continue;
            }
            $nom = \frontend\shared\helpers\PayloadCoercion::string($row['nom'] ?? '');
            $nota = \frontend\shared\helpers\PayloadCoercion::string($row['nota'] ?? '');
            $out[$nom] = $nota;
        }

        return $out;
    }

    /**
     * @param array<int|string, mixed> $payload
     * @return list<string>
     */
    public static function examinadoresFromPayload(array $payload): array
    {
        $raw = $payload['examinadores'] ?? [];
        if (!is_array($raw)) {
            return [];
        }
        $out = [];
        foreach ($raw as $item) {
            $out[] = \frontend\shared\helpers\PayloadCoercion::string($item);
        }

        return $out;
    }

    /**
     * @param array<int|string, mixed> $payload
     * @return array{
     *     acta: string,
     *     errores: string,
     *     num_alumnos: int,
     *     lin_tribunal: int,
     *     lin_max_cara_A: int,
     *     alum_cara_A: int,
     *     alum_cara_B: int,
     *     curso: string,
     *     any: string,
     *     nombre_asignatura: string,
     *     libro: string,
     *     pagina: string,
     *     linea: string,
     *     lugar: string,
     *     lugar_fecha: string,
     *     tribunal_html: string,
     *     examinadores: list<string>,
     *     aPersonasNotas: array<string, string>,
     * }
     */
    public static function presentacionFromPayload(array $payload): array
    {
        return [
            'acta' => \frontend\shared\helpers\PayloadCoercion::string($payload['acta'] ?? ''),
            'errores' => \frontend\shared\helpers\PayloadCoercion::string($payload['errores'] ?? ''),
            'num_alumnos' => \frontend\shared\helpers\PayloadCoercion::int($payload['num_alumnos'] ?? 0),
            'lin_tribunal' => \frontend\shared\helpers\PayloadCoercion::int($payload['lin_tribunal'] ?? 0),
            'lin_max_cara_A' => \frontend\shared\helpers\PayloadCoercion::int($payload['lin_max_cara_A'] ?? 0),
            'alum_cara_A' => \frontend\shared\helpers\PayloadCoercion::int($payload['alum_cara_A'] ?? 0),
            'alum_cara_B' => \frontend\shared\helpers\PayloadCoercion::int($payload['alum_cara_B'] ?? 0),
            'curso' => \frontend\shared\helpers\PayloadCoercion::string($payload['curso'] ?? ''),
            'any' => \frontend\shared\helpers\PayloadCoercion::string($payload['any'] ?? ''),
            'nombre_asignatura' => \frontend\shared\helpers\PayloadCoercion::string($payload['nombre_asignatura'] ?? ''),
            'libro' => \frontend\shared\helpers\PayloadCoercion::string($payload['libro'] ?? ''),
            'pagina' => \frontend\shared\helpers\PayloadCoercion::string($payload['pagina'] ?? ''),
            'linea' => \frontend\shared\helpers\PayloadCoercion::string($payload['linea'] ?? ''),
            'lugar' => \frontend\shared\helpers\PayloadCoercion::string($payload['lugar'] ?? ''),
            'lugar_fecha' => \frontend\shared\helpers\PayloadCoercion::string($payload['lugar_fecha'] ?? ''),
            'tribunal_html' => \frontend\shared\helpers\PayloadCoercion::string($payload['tribunal_html'] ?? ''),
            'examinadores' => self::examinadoresFromPayload($payload),
            'aPersonasNotas' => self::personasNotasFromPayload($payload),
        ];
    }
}
