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
            $nom = PayloadCoercion::string($row['nom'] ?? '');
            $nota = PayloadCoercion::string($row['nota'] ?? '');
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
            $out[] = PayloadCoercion::string($item);
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
            'acta' => PayloadCoercion::string($payload['acta'] ?? ''),
            'errores' => PayloadCoercion::string($payload['errores'] ?? ''),
            'num_alumnos' => PayloadCoercion::int($payload['num_alumnos'] ?? 0),
            'lin_tribunal' => PayloadCoercion::int($payload['lin_tribunal'] ?? 0),
            'lin_max_cara_A' => PayloadCoercion::int($payload['lin_max_cara_A'] ?? 0),
            'alum_cara_A' => PayloadCoercion::int($payload['alum_cara_A'] ?? 0),
            'alum_cara_B' => PayloadCoercion::int($payload['alum_cara_B'] ?? 0),
            'curso' => PayloadCoercion::string($payload['curso'] ?? ''),
            'any' => PayloadCoercion::string($payload['any'] ?? ''),
            'nombre_asignatura' => PayloadCoercion::string($payload['nombre_asignatura'] ?? ''),
            'libro' => PayloadCoercion::string($payload['libro'] ?? ''),
            'pagina' => PayloadCoercion::string($payload['pagina'] ?? ''),
            'linea' => PayloadCoercion::string($payload['linea'] ?? ''),
            'lugar' => PayloadCoercion::string($payload['lugar'] ?? ''),
            'lugar_fecha' => PayloadCoercion::string($payload['lugar_fecha'] ?? ''),
            'tribunal_html' => PayloadCoercion::string($payload['tribunal_html'] ?? ''),
            'examinadores' => self::examinadoresFromPayload($payload),
            'aPersonasNotas' => self::personasNotasFromPayload($payload),
        ];
    }
}
