<?php

declare(strict_types=1);

namespace frontend\notas\helpers;

use frontend\shared\helpers\PayloadCoercion;

/**
 * Normalización de payload para tessera_imprimir / tessera_imprimir_mpdf.
 */
final class TesseraImprimirPayload
{
    /**
     * @return array{id_nivel_asig: int, id_nivel: int, id_asignatura: int, nombre_asignatura: string, acta: string, fecha_local: string, nota: string}
     */
    public static function emptyRow(): array
    {
        return [
            'id_nivel_asig' => 0,
            'id_nivel' => 0,
            'id_asignatura' => 0,
            'nombre_asignatura' => '',
            'acta' => '',
            'fecha_local' => '',
            'nota' => '',
        ];
    }

    /**
     * @return array{id_nivel: int, id_asignatura: int, nombre_asignatura: string}
     */
    public static function asignaturaRow(mixed $raw): array
    {
        if (!is_array($raw)) {
            return ['id_nivel' => 0, 'id_asignatura' => 0, 'nombre_asignatura' => ''];
        }

        return [
            'id_nivel' => \frontend\shared\helpers\PayloadCoercion::int($raw['id_nivel'] ?? 0),
            'id_asignatura' => \frontend\shared\helpers\PayloadCoercion::int($raw['id_asignatura'] ?? 0),
            'nombre_asignatura' => \frontend\shared\helpers\PayloadCoercion::string($raw['nombre_asignatura'] ?? ''),
        ];
    }

    /**
     * @return array{id_nivel_asig: int, id_nivel: int, id_asignatura: int, nombre_asignatura: string, acta: string, fecha_local: string, nota: string}
     */
    public static function aprobadaRow(mixed $raw): array
    {
        if (!is_array($raw)) {
            return self::emptyRow();
        }

        return [
            'id_nivel_asig' => \frontend\shared\helpers\PayloadCoercion::int($raw['id_nivel_asig'] ?? 0),
            'id_nivel' => \frontend\shared\helpers\PayloadCoercion::int($raw['id_nivel'] ?? 0),
            'id_asignatura' => \frontend\shared\helpers\PayloadCoercion::int($raw['id_asignatura'] ?? 0),
            'nombre_asignatura' => \frontend\shared\helpers\PayloadCoercion::string($raw['nombre_asignatura'] ?? ''),
            'acta' => \frontend\shared\helpers\PayloadCoercion::string($raw['acta'] ?? ''),
            'fecha_local' => \frontend\shared\helpers\PayloadCoercion::string($raw['fecha_local'] ?? ''),
            'nota' => \frontend\shared\helpers\PayloadCoercion::string($raw['nota'] ?? ''),
        ];
    }

    /**
     * @param array<int|string, mixed> $payload
     * @return list<array{id_nivel: int, id_asignatura: int, nombre_asignatura: string}>
     */
    public static function asignaturasFromPayload(array $payload): array
    {
        $raw = $payload['c_asignaturas'] ?? [];
        if (!is_array($raw)) {
            return [];
        }
        $out = [];
        foreach ($raw as $item) {
            $out[] = self::asignaturaRow($item);
        }

        return $out;
    }

    /**
     * @param array<int|string, mixed> $payload
     * @return array<int|string, array{id_nivel_asig: int, id_nivel: int, id_asignatura: int, nombre_asignatura: string, acta: string, fecha_local: string, nota: string}>
     */
    public static function aprobadasFromPayload(array $payload): array
    {
        $raw = $payload['a_aprobadas'] ?? [];
        if (!is_array($raw)) {
            return [];
        }
        $out = [];
        foreach ($raw as $key => $item) {
            $out[$key] = self::aprobadaRow($item);
        }

        return $out;
    }

    /**
     * @param array<int|string, array{id_nivel_asig: int, id_nivel: int, id_asignatura: int, nombre_asignatura: string, acta: string, fecha_local: string, nota: string}> $aAprobadas
     * @param array{id_nivel_asig: int, id_nivel: int, id_asignatura: int, nombre_asignatura: string, acta: string, fecha_local: string, nota: string} $rowEmpty
     * @return array{id_nivel_asig: int, id_nivel: int, id_asignatura: int, nombre_asignatura: string, acta: string, fecha_local: string, nota: string}
     */
    public static function currentAprobadaRow(array $aAprobadas, array $rowEmpty): array
    {
        if (key($aAprobadas) === null) {
            return $rowEmpty;
        }
        $rowCurrent = current($aAprobadas);

        return is_array($rowCurrent) ? self::aprobadaRow($rowCurrent) : $rowEmpty;
    }

    public static function fechaLocal(string $fechaRaw): string
    {
        $fecha = explode('-', $fechaRaw);
        $any = substr($fecha[0], 2);
        $fechaok = ($fecha[2] ?? '') . '.' . ($fecha[1] ?? '') . '.' . $any;
        if (($fecha[1] ?? '') === '00') {
            return '';
        }

        return $fechaok;
    }
}
