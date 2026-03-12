<?php

namespace src\personas\domain\entity;

use src\personas\application\services\PersonaFinderService;

/**
 * @deprecated Esta clase está deprecada. Use PersonaFinderService en su lugar.
 *
 * Esta clase se mantiene temporalmente por compatibilidad con código legacy,
 * pero todos los métodos ahora delegan al servicio PersonaFinderService que
 * cumple con los principios de DDD (capa de aplicación).
 */
class Persona
{
    /**
     * @deprecated Use PersonaFinderService::findPersonaEnGlobal() en su lugar
     *
     * Busca una persona por id_nom en el esquema global (local).
     * Este método es un wrapper temporal para mantener compatibilidad.
     *
     * @param int $id_nom ID de la persona a buscar
     * @return PersonaDl|PersonaPub|null
     */
    public static function findPersonaEnGlobal($id_nom): PersonaDl|PersonaPub|null
    {
        $service = $GLOBALS['container']->get(PersonaFinderService::class);
        return $service->findPersonaEnGlobal($id_nom);
    }

    /**
     * @deprecated Use PersonaFinderService::buscarEnTodasRegiones() en su lugar
     *
     * Busca una persona en todas las regiones/esquemas disponibles.
     * Este método es un wrapper temporal para mantener compatibilidad.
     *
     * @param int $id_nom ID de la persona a buscar
     * @return array Array de PersonaGlobal encontradas
     */
    public static function buscarEnTodasRegiones($id_nom): array
    {
        $service = $GLOBALS['container']->get(PersonaFinderService::class);
        return $service->buscarEnTodasRegiones($id_nom);
    }
}
