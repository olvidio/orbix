<?php

namespace src\shared\infrastructure;

use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\personas\domain\contracts\PersonaAgdRepositoryInterface;
use src\personas\domain\contracts\PersonaExRepositoryInterface;
use src\personas\domain\contracts\PersonaNaxRepositoryInterface;
use src\personas\domain\contracts\PersonaNRepositoryInterface;
use src\personas\domain\contracts\PersonaSRepositoryInterface;
use src\personas\domain\contracts\PersonaSSSCRepositoryInterface;
use src\ubis\domain\contracts\CasaDlRepositoryInterface;
use src\ubis\domain\contracts\CasaExRepositoryInterface;
use src\ubis\domain\contracts\CasaRepositoryInterface;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroExRepositoryInterface;
use src\ubis\domain\contracts\CentroRepositoryInterface;
use src\ubis\domain\contracts\TelecoCdcDlRepositoryInterface;
use src\ubis\domain\contracts\TelecoCdcExRepositoryInterface;
use src\ubis\domain\contracts\TelecoUbiRepositoryInterface;
use src\ubis\domain\contracts\TelecoCtrDlRepositoryInterface;
use src\ubis\domain\contracts\TelecoCtrExRepositoryInterface;
use src\ubis\domain\contracts\TelecoCtrRepositoryInterface;

/**
 * Trait ProvidesRepositories
 *
 * Proporciona acceso a repositorios desde el contenedor de dependencias.
 * Útil para controladores y servicios que necesitan obtener repositorios dinámicamente.
 */
trait ProvidesRepositories
{
    /**
     * Obtiene un repositorio del contenedor de dependencias según el tipo de entidad
     *
     * @param string $entityType Tipo de entidad (PersonaN, PersonaEx, Centro, Casa, etc.)
     * @return object El repositorio correspondiente
     * @throws \InvalidArgumentException Si el tipo de entidad no es reconocido
     */
    protected function getRepository(string $entityType): object
    {
        $repositoryMap = [
            'PersonaN' => PersonaNRepositoryInterface::class,
            'PersonaNax' => PersonaNaxRepositoryInterface::class,
            'PersonaAgd' => PersonaAgdRepositoryInterface::class,
            'PersonaS' => PersonaSRepositoryInterface::class,
            'PersonaSSSC' => PersonaSSSCRepositoryInterface::class,
            'PersonaEx' => PersonaExRepositoryInterface::class,
            'Centro' => CentroRepositoryInterface::class,
            'CentroDl' => CentroDlRepositoryInterface::class,
            'CentroEx' => CentroExRepositoryInterface::class,
            'Casa' => CasaRepositoryInterface::class,
            'CasaDl' => CasaDlRepositoryInterface::class,
            'CasaEx' => CasaExRepositoryInterface::class,
            'ActividadAll' => ActividadAllRepositoryInterface::class,
        ];

        if (!isset($repositoryMap[$entityType])) {
            throw new \InvalidArgumentException("Repository for entity type '$entityType' not found");
        }

        return $GLOBALS['container']->get($repositoryMap[$entityType]);
    }
}
