<?php

declare(strict_types=1);

namespace frontend\actividadestudios\helpers;

use frontend\notas\helpers\NotasFormSupport;
use frontend\shared\helpers\PayloadCoercion;

final class FormMatriculasPayload
{
    /**
     * @param array<string, mixed> $payload
     * @return array{
     *     nom_activ: string,
     *     mod: string,
     *     id_asignatura_real: int|string,
     *     nombre_corto: string,
     *     chk_preceptor: string,
     *     id_preceptor: int|string,
     *     condicion_js: string,
     *     oDesplNiveles_opciones: array<int|string, string>,
     *     oDesplProfesores_opciones: array<int|string, string>,
     *     camposForm: string,
     *     a_camposHidden: array<string, mixed>,
     * }
     */
    public static function fromPayload(array $payload): array
    {
        return [
            'nom_activ' => PayloadCoercion::string($payload['nom_activ'] ?? ''),
            'mod' => PayloadCoercion::string($payload['mod'] ?? 'nuevo'),
            'id_asignatura_real' => NotasFormSupport::formScalar($payload['id_asignatura_real'] ?? 0),
            'nombre_corto' => PayloadCoercion::string($payload['nombre_corto'] ?? ''),
            'chk_preceptor' => PayloadCoercion::string($payload['chk_preceptor'] ?? ''),
            'id_preceptor' => NotasFormSupport::formScalar($payload['id_preceptor'] ?? ''),
            'condicion_js' => PayloadCoercion::string($payload['condicion_js'] ?? ''),
            'oDesplNiveles_opciones' => NotasFormSupport::desplegableOpciones($payload['oDesplNiveles_opciones'] ?? []),
            'oDesplProfesores_opciones' => NotasFormSupport::desplegableOpciones($payload['oDesplProfesores_opciones'] ?? []),
            'camposForm' => PayloadCoercion::string($payload['camposForm'] ?? ''),
            'a_camposHidden' => ActividadestudiosRenderSupport::stringKeyRow($payload['a_camposHidden'] ?? []),
        ];
    }
}
