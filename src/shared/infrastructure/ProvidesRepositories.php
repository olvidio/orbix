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
use src\ubis\domain\contracts\DireccionCasaDlRepositoryInterface;
use src\ubis\domain\contracts\DireccionCasaExRepositoryInterface;
use src\ubis\domain\contracts\DireccionCasaRepositoryInterface;
use src\ubis\domain\contracts\DireccionCentroDlRepositoryInterface;
use src\ubis\domain\contracts\DireccionCentroExRepositoryInterface;
use src\ubis\domain\contracts\DireccionCentroRepositoryInterface;
use src\ubis\domain\contracts\TelecoCdcDlRepositoryInterface;
use src\ubis\domain\contracts\TelecoCdcExRepositoryInterface;
use src\ubis\domain\contracts\TelecoCdcRepositoryInterface;
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
    protected function getRepositoryMap(): array
    {
        return [
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
    }

    protected function getMetodoMap(): array
    {
        return [
            'Centro' => 'getCentros',
            'CentroDl' => 'getCentros',
            'CentroEx' => 'getCentros',
            'Casa' => 'getCasas',
            'CasaDl' => 'getCasas',
            'CasaEx' => 'getCasas',
        ];
    }

    protected function getDireccionRepositoryMap(): array
    {
        return [
            // Compatibilidad: fuera de ubis se ha usado este helper como alias de repositorio general.
            'PersonaN' => PersonaNRepositoryInterface::class,
            'PersonaNax' => PersonaNaxRepositoryInterface::class,
            'PersonaAgd' => PersonaAgdRepositoryInterface::class,
            'PersonaS' => PersonaSRepositoryInterface::class,
            'PersonaSSSC' => PersonaSSSCRepositoryInterface::class,
            'PersonaEx' => PersonaExRepositoryInterface::class,
            'ActividadAll' => ActividadAllRepositoryInterface::class,
            'Centro' => DireccionCentroRepositoryInterface::class,
            'CentroDl' => DireccionCentroDlRepositoryInterface::class,
            'CentroEx' => DireccionCentroExRepositoryInterface::class,
            'Casa' => DireccionCasaRepositoryInterface::class,
            'CasaDl' => DireccionCasaDlRepositoryInterface::class,
            'CasaEx' => DireccionCasaExRepositoryInterface::class,
        ];
    }

    protected function getTelecoRepositoryMap(): array
    {
        return [
            'Centro' => TelecoCtrRepositoryInterface::class,
            'CentroDl' => TelecoCtrDlRepositoryInterface::class,
            'CentroEx' => TelecoCtrExRepositoryInterface::class,
            'Casa' => TelecoCdcRepositoryInterface::class,
            'CasaDl' => TelecoCdcDlRepositoryInterface::class,
            'CasaEx' => TelecoCdcExRepositoryInterface::class,
        ];
    }

    protected function getRepositoryClass(string $entityType): string
    {
        $repositoryMap = $this->getRepositoryMap();
        if (!isset($repositoryMap[$entityType])) {
            throw new \InvalidArgumentException("Repository for entity type '$entityType' not found");
        }
        return $repositoryMap[$entityType];
    }

    protected function getRepository(string $entityType): object
    {
        return $GLOBALS['container']->get($this->getRepositoryClass($entityType));
    }

    protected function getMetodo(string $entityType): string
    {
        $metodoMap = $this->getMetodoMap();
        if (!isset($metodoMap[$entityType])) {
            throw new \InvalidArgumentException("Method for entity type '$entityType' not found");
        }
        return $metodoMap[$entityType];
    }

    protected function getDireccionRepositoryClass(string $entityType): string
    {
        $repositoryMap = $this->getDireccionRepositoryMap();
        if (!isset($repositoryMap[$entityType])) {
            throw new \InvalidArgumentException("Address repository for entity type '$entityType' not found");
        }
        return $repositoryMap[$entityType];
    }

    protected function getDireccionRepository(string $entityType): object
    {
        return $GLOBALS['container']->get($this->getDireccionRepositoryClass($entityType));
    }

    protected function getTelecoRepositoryClass(string $entityType): string
    {
        $repositoryMap = $this->getTelecoRepositoryMap();
        if (!isset($repositoryMap[$entityType])) {
            throw new \InvalidArgumentException("Teleco repository for entity type '$entityType' not found");
        }
        return $repositoryMap[$entityType];
    }

    protected function getTelecoRepository(string $entityType): object
    {
        return $GLOBALS['container']->get($this->getTelecoRepositoryClass($entityType));
    }

    protected function getEntityTypeByRepositoryClass(string $repositoryClass): string
    {
        $entityType = array_search($repositoryClass, $this->getRepositoryMap(), true);
        if ($entityType === false) {
            throw new \InvalidArgumentException("Entity type for repository class '$repositoryClass' not found");
        }
        return $entityType;
    }
}
