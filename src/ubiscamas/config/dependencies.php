<?php

use src\ubiscamas\application\CamaFormData;
use src\ubiscamas\application\HabitacionFormData;
use src\ubiscamas\application\HabitacionesCamaLista;
use src\ubiscamas\application\UpdateCamaAsistente;
use src\ubiscamas\domain\Select_habitaciones_cdc;
use src\ubiscamas\domain\contracts\HabitacionRepositoryInterface;
use src\ubiscamas\domain\contracts\HabitacionDlRepositoryInterface;
use src\ubiscamas\domain\contracts\CamaRepositoryInterface;
use src\ubiscamas\domain\contracts\CamaDlRepositoryInterface;
use src\ubiscamas\infrastructure\persistence\postgresql\PgHabitacionRepository;
use src\ubiscamas\infrastructure\persistence\postgresql\PgHabitacionDlRepository;
use src\ubiscamas\infrastructure\persistence\postgresql\PgCamaRepository;
use src\ubiscamas\infrastructure\persistence\postgresql\PgCamaDlRepository;
use function DI\autowire;

return [
    HabitacionRepositoryInterface::class => autowire(PgHabitacionRepository::class),
    HabitacionDlRepositoryInterface::class => autowire(PgHabitacionDlRepository::class),
    CamaRepositoryInterface::class => autowire(PgCamaRepository::class),
    CamaDlRepositoryInterface::class => autowire(PgCamaDlRepository::class),

    CamaFormData::class => autowire(CamaFormData::class),
    HabitacionFormData::class => autowire(HabitacionFormData::class),
    HabitacionesCamaLista::class => autowire(HabitacionesCamaLista::class),
    UpdateCamaAsistente::class => autowire(UpdateCamaAsistente::class),
    Select_habitaciones_cdc::class => autowire(Select_habitaciones_cdc::class),
];
