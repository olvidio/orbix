<?php

namespace src\notas\domain\value_objects;

use src\actividades\domain\value_objects\NivelStgrId;

/**
 * Tramo del plan de estudios (bienio / cuadrienio / anio I / anios II-IV) usado
 * por los reportes de asignaturas pendientes.
 *
 * Sustituye a los strings magicos `'bienio' | 'cuadrienio' | 'c1' | 'c2'`
 * dispersos por el modulo `notas`.
 */
enum CursoStgr: string
{
    case BIENIO = 'bienio';
    case CUADRIENIO = 'cuadrienio';
    case C1 = 'c1';
    case C2 = 'c2';

    /**
     * Rango `[desde, hasta]` de `id_nivel` de asignaturas que corresponden al
     * tramo. Se consulta al repositorio con operador `BETWEEN`.
     *
     * @return array{int, int}
     */
    public function rangoNiveles(): array
    {
        return match ($this) {
            self::BIENIO => [1100, 1300],
            self::CUADRIENIO => [2100, 2500],
            self::C1 => [2100, 2113],
            self::C2 => [2200, 2500],
        };
    }

    /**
     * Valores de `nivel_stgr` (tabla `publicv.xa_nivel_stgr`) cuyos alumnos
     * tienen que cursar este tramo.
     *
     * @return array<int, int>
     */
    public function nivelesStgr(): array
    {
        return match ($this) {
            self::BIENIO => [NivelStgrId::B],
            self::CUADRIENIO => [NivelStgrId::C1, NivelStgrId::C2, NivelStgrId::BC],
            self::C1 => [NivelStgrId::C1],
            self::C2 => [NivelStgrId::C2],
        };
    }
}
