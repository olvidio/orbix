<?php

declare(strict_types=1);

namespace frontend\actividadestudios\helpers;

use frontend\shared\helpers\PayloadCoercion;

final class E43Payload
{
    /**
     * @param array<string, mixed> $payload
     * @return array{
     *     msg_err: string,
     *     nom: string,
     *     txt_nacimiento: string,
     *     dl_origen: string,
     *     dl_destino: string,
     *     txt_actividad: string,
     *     matriculas: int,
     *     aAsignaturasMatriculadas: list<array{nom_asignatura: string, nota: string, f_acta: string, acta: string}>,
     * }
     */
    public static function fromPayload(array $payload): array
    {
        return [
            'msg_err' => PayloadCoercion::string($payload['msg_err'] ?? ''),
            'nom' => PayloadCoercion::string($payload['nom'] ?? ''),
            'txt_nacimiento' => PayloadCoercion::string($payload['txt_nacimiento'] ?? ''),
            'dl_origen' => PayloadCoercion::string($payload['dl_origen'] ?? ''),
            'dl_destino' => PayloadCoercion::string($payload['dl_destino'] ?? ''),
            'txt_actividad' => PayloadCoercion::string($payload['txt_actividad'] ?? ''),
            'matriculas' => PayloadCoercion::int($payload['matriculas'] ?? 0),
            'aAsignaturasMatriculadas' => self::notasRows($payload['aAsignaturasMatriculadas'] ?? []),
        ];
    }

    /**
     * @return list<array{nom_asignatura: string, nota: string, f_acta: string, acta: string}>
     */
    private static function notasRows(mixed $raw): array
    {
        if (!is_array($raw)) {
            return [];
        }
        $out = [];
        foreach ($raw as $row) {
            $parsed = ActividadestudiosRenderSupport::stringKeyRow($row);
            if ($parsed !== []) {
                $out[] = self::notaRow($parsed);
            }
        }

        return $out;
    }

    /**
     * @param array<string, mixed> $row
     * @return array{nom_asignatura: string, nota: string, f_acta: string, acta: string}
     */
    private static function notaRow(array $row): array
    {
        return [
            'nom_asignatura' => PayloadCoercion::string($row['nom_asignatura'] ?? ''),
            'nota' => PayloadCoercion::string($row['nota'] ?? ''),
            'f_acta' => PayloadCoercion::string($row['f_acta'] ?? ''),
            'acta' => PayloadCoercion::string($row['acta'] ?? ''),
        ];
    }
}
