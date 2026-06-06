<?php

namespace src\asistentes\domain\contracts;

use src\asistentes\domain\entity\Asistente;

/**
 * Valida y asigna propietario de plaza al subir estado por encima de {@see \src\actividadplazas\domain\value_objects\PlazaId::DENEGADA}.
 */
interface PlazaPropietarioAsignacionInterface
{
    /**
     * @return string vacio si ok, mensaje de error si no hay propiedad posible
     */
    public function asegurar(Asistente $asistente, int $plazaActual, int $plazaNueva): string;
}
