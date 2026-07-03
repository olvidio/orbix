<?php

declare(strict_types=1);

namespace frontend\actividadestudios\helpers;

use frontend\notas\helpers\NotasFormSupport;
use frontend\shared\helpers\PayloadCoercion;

final class FormAsignaturasPayload
{
    /**
     * @param array<string, mixed> $payload
     * @return array{
     *     mod: string,
     *     id_activ: int,
     *     id_asignatura: int,
     *     nombre_corto: string,
     *     chk_avisado: string,
     *     chk_confirmado: string,
     *     chk_preceptor: string,
     *     f_ini: string,
     *     f_fin: string,
     *     oDesplProfesores_opciones: array<int|string, string>,
     *     oDesplAsignaturas_opciones: array<int|string, string>,
     *     id_profesor_sel: int|string,
     *     camposForm: string,
     *     a_camposHidden: array<string, mixed>,
     * }
     */
    public static function fromPayload(array $payload): array
    {
        return [
            'mod' => \frontend\shared\helpers\PayloadCoercion::string($payload['mod'] ?? 'nuevo'),
            'id_activ' => \frontend\shared\helpers\PayloadCoercion::int($payload['id_activ'] ?? 0),
            'id_asignatura' => \frontend\shared\helpers\PayloadCoercion::int($payload['id_asignatura'] ?? 0),
            'nombre_corto' => \frontend\shared\helpers\PayloadCoercion::string($payload['nombre_corto'] ?? ''),
            'chk_avisado' => \frontend\shared\helpers\PayloadCoercion::string($payload['chk_avisado'] ?? ''),
            'chk_confirmado' => \frontend\shared\helpers\PayloadCoercion::string($payload['chk_confirmado'] ?? ''),
            'chk_preceptor' => \frontend\shared\helpers\PayloadCoercion::string($payload['chk_preceptor'] ?? ''),
            'f_ini' => \frontend\shared\helpers\PayloadCoercion::string($payload['f_ini'] ?? ''),
            'f_fin' => \frontend\shared\helpers\PayloadCoercion::string($payload['f_fin'] ?? ''),
            'oDesplProfesores_opciones' => NotasFormSupport::desplegableOpciones($payload['oDesplProfesores_opciones'] ?? []),
            'oDesplAsignaturas_opciones' => NotasFormSupport::desplegableOpciones($payload['oDesplAsignaturas_opciones'] ?? []),
            'id_profesor_sel' => NotasFormSupport::formScalar($payload['id_profesor_sel'] ?? -1),
            'camposForm' => \frontend\shared\helpers\PayloadCoercion::string($payload['camposForm'] ?? ''),
            'a_camposHidden' => ActividadestudiosRenderSupport::stringKeyRow($payload['a_camposHidden'] ?? []),
        ];
    }
}
