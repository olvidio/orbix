<?php

use src\misas\application\AnadirCtrTarea;
use src\misas\application\BuscarPlanCtrData;
use src\misas\application\BuscarPlanSacdData;
use src\misas\application\CambiarStatusPantallaData;
use src\misas\application\CrearNuevoPeriodoData;
use src\misas\application\CuadriculaUpdate;
use src\misas\application\CuadriculaZonaGridData;
use src\misas\application\DesplegableCentrosZonaData;
use src\misas\application\DesplegableEncargosData;
use src\misas\application\DesplegableSacdData;
use src\misas\application\EliminarEncargoCentro;
use src\misas\application\EliminarEncargoZona;
use src\misas\application\GuardarEncargoCentro;
use src\misas\application\GuardarEncargoZona;
use src\misas\application\GuardarHorarioTarea;
use src\misas\application\HorarioTareaData;
use src\misas\application\ImportarPlantillaData;
use src\misas\application\ModificarEncargosCentrosData;
use src\misas\application\ModificarEncargosData;
use src\misas\application\ModificarInicialesSacdZonaData;
use src\misas\application\NuevoStatusPeriodo;
use src\misas\application\PlanDeMisasPantallaData;
use src\misas\application\QuitarHorarioPlantilla;
use src\misas\application\UpdateIniciales;
use src\misas\application\VerEncargosCentrosData;
use src\misas\application\VerEncargosZonaData;
use src\misas\application\VerInicialesZonaData;
use src\misas\application\VerMisasZonaData;
use src\misas\application\VerPlanCtrData;
use src\misas\application\VerPlanSacdData;
use src\misas\application\ZonaSacdDatosGet;
use src\misas\application\ZonaSacdDatosPut;
use src\misas\application\services\InicialesSacdService;
use src\misas\application\support\IdNomJefeResolver;
use src\misas\domain\contracts\EncargoCtrRepositoryInterface;
use src\misas\domain\contracts\EncargoDiaRepositoryInterface;
use src\misas\domain\contracts\InicialesSacdRepositoryInterface;
use src\misas\domain\contracts\PlantillaRepositoryInterface;
use src\misas\infrastructure\persistence\postgresql\PgEncargoCtrRepository;
use src\misas\infrastructure\persistence\postgresql\PgEncargoDiaRepository;
use src\misas\infrastructure\persistence\postgresql\PgInicialesSacdRepository;
use src\misas\infrastructure\persistence\postgresql\PgPlantillaRepository;
use function DI\autowire;

return [
    EncargoCtrRepositoryInterface::class => autowire(PgEncargoCtrRepository::class),
    EncargoDiaRepositoryInterface::class => autowire(PgEncargoDiaRepository::class),
    InicialesSacdRepositoryInterface::class => autowire(PgInicialesSacdRepository::class),
    PlantillaRepositoryInterface::class => autowire(PgPlantillaRepository::class),

    IdNomJefeResolver::class => autowire(IdNomJefeResolver::class),
    InicialesSacdService::class => autowire(InicialesSacdService::class),

    AnadirCtrTarea::class => autowire(AnadirCtrTarea::class),
    BuscarPlanCtrData::class => autowire(BuscarPlanCtrData::class),
    BuscarPlanSacdData::class => autowire(BuscarPlanSacdData::class),
    CambiarStatusPantallaData::class => autowire(CambiarStatusPantallaData::class),
    CrearNuevoPeriodoData::class => autowire(CrearNuevoPeriodoData::class),
    CuadriculaUpdate::class => autowire(CuadriculaUpdate::class),
    CuadriculaZonaGridData::class => autowire(CuadriculaZonaGridData::class),
    DesplegableCentrosZonaData::class => autowire(DesplegableCentrosZonaData::class),
    DesplegableEncargosData::class => autowire(DesplegableEncargosData::class),
    DesplegableSacdData::class => autowire(DesplegableSacdData::class),
    EliminarEncargoCentro::class => autowire(EliminarEncargoCentro::class),
    EliminarEncargoZona::class => autowire(EliminarEncargoZona::class),
    GuardarEncargoCentro::class => autowire(GuardarEncargoCentro::class),
    GuardarEncargoZona::class => autowire(GuardarEncargoZona::class),
    GuardarHorarioTarea::class => autowire(GuardarHorarioTarea::class),
    HorarioTareaData::class => autowire(HorarioTareaData::class),
    ImportarPlantillaData::class => autowire(ImportarPlantillaData::class),
    ModificarEncargosCentrosData::class => autowire(ModificarEncargosCentrosData::class),
    ModificarEncargosData::class => autowire(ModificarEncargosData::class),
    ModificarInicialesSacdZonaData::class => autowire(ModificarInicialesSacdZonaData::class),
    NuevoStatusPeriodo::class => autowire(NuevoStatusPeriodo::class),
    PlanDeMisasPantallaData::class => autowire(PlanDeMisasPantallaData::class),
    QuitarHorarioPlantilla::class => autowire(QuitarHorarioPlantilla::class),
    UpdateIniciales::class => autowire(UpdateIniciales::class),
    VerEncargosCentrosData::class => autowire(VerEncargosCentrosData::class),
    VerEncargosZonaData::class => autowire(VerEncargosZonaData::class),
    VerInicialesZonaData::class => autowire(VerInicialesZonaData::class),
    VerMisasZonaData::class => autowire(VerMisasZonaData::class),
    VerPlanCtrData::class => autowire(VerPlanCtrData::class),
    VerPlanSacdData::class => autowire(VerPlanSacdData::class),
    ZonaSacdDatosGet::class => autowire(ZonaSacdDatosGet::class),
    ZonaSacdDatosPut::class => autowire(ZonaSacdDatosPut::class),
];
