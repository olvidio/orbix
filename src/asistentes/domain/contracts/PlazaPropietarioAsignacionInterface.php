<?php

namespace src\asistentes\domain\contracts;

use src\asistentes\domain\entity\Asistente;

/**
 * Valida y asigna propietario de plaza al subir estado por encima de {@see \src\actividadplazas\domain\value_objects\PlazaId::DENEGADA}.
 */
interface PlazaPropietarioAsignacionInterface
{
    /**
     * @param bool $permitirSinPlazaLibre Incorporar 1ª petición: mantiene el propietario ya
     *        elegido aunque su cupo esté lleno. El resto de altas debe dejarlo en false.
     * @return string vacio si ok, mensaje de error si no hay propiedad posible
     */
    public function asegurar(
        Asistente $asistente,
        int $plazaActual,
        int $plazaNueva,
        bool $permitirSinPlazaLibre = false,
    ): string;
}
