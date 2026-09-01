<?php

declare(strict_types=1);

namespace src\notas\application\support;

use src\notas\domain\entity\PersonaNota;
use src\notas\domain\value_objects\TipoActa;

/**
 * Unique real en `e_notas_dl`: `(id_nom, id_asignatura)` y `(id_nivel, id_nom)`.
 * El formulario de edición manda a veces el `id_nivel` del catálogo (p. ej. Latín IV
 * 1997 = 2312) distinto del almacenado tras el remap 2026 (2212). Tratarlo como alta
 * viola la unique de asignatura.
 *
 * @phpstan-type Pk array{id_nivel: int, tipo_acta: int}
 */
final class PersonaNotaPkPorAsignatura
{
    /**
     * @param list<PersonaNota> $filasMismaAsignatura
     * @return Pk|null null → no hay fila, procede INSERT
     */
    public static function pkParaUpdate(array $filasMismaAsignatura): ?array
    {
        if ($filasMismaAsignatura === []) {
            return null;
        }
        $existente = $filasMismaAsignatura[0];

        return [
            'id_nivel' => $existente->getIdNivelVo()->value(),
            'tipo_acta' => $existente->getTipoActaVo()?->value() ?? TipoActa::FORMATO_ACTA,
        ];
    }
}
