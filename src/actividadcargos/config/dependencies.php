<?php

use src\actividadcargos\application\ActividadCargoEditar;
use src\actividadcargos\application\ActividadCargoEliminar;
use src\actividadcargos\application\ActividadCargoNuevo;
use src\actividadcargos\application\FormCargosDeActividadData;
use src\actividadcargos\application\FormCargosPersonasEnActividadData;
use src\actividadcargos\application\Select_cargos_de_actividad;
use src\actividadcargos\application\Select_cargos_personas_en_actividad;
use src\actividadcargos\domain\InfoCargo;
use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividadcargos\domain\contracts\CargoOAsistenteInterface;
use src\actividadcargos\domain\contracts\CargoRepositoryInterface;
use src\actividadcargos\infrastructure\persistence\postgresql\PgActividadCargoDlRepository;
use src\actividadcargos\infrastructure\persistence\postgresql\PgCargoOAsistente;
use src\actividadcargos\infrastructure\persistence\postgresql\PgCargoRepository;
use function DI\autowire;

return [
    // Mapeos de Interfaces a Implementaciones
    CargoRepositoryInterface::class => autowire(PgCargoRepository::class),
    CargoOAsistenteInterface::class => autowire(PgCargoOAsistente::class),
    ActividadCargoRepositoryInterface::class => autowire(PgActividadCargoDlRepository::class),

    // Casos de uso / Application classes
    ActividadCargoNuevo::class => autowire(ActividadCargoNuevo::class),
    ActividadCargoEditar::class => autowire(ActividadCargoEditar::class),
    ActividadCargoEliminar::class => autowire(ActividadCargoEliminar::class),
    FormCargosDeActividadData::class => autowire(FormCargosDeActividadData::class),
    FormCargosPersonasEnActividadData::class => autowire(FormCargosPersonasEnActividadData::class),
    Select_cargos_de_actividad::class => autowire(Select_cargos_de_actividad::class),
    Select_cargos_personas_en_actividad::class => autowire(Select_cargos_personas_en_actividad::class),
    InfoCargo::class => autowire(InfoCargo::class),
];
