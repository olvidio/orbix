<?php

/**
 * Aliases globales para el cuerpo procedural de la cuadrícula por zona (Slice 6b).
 *
 * La implementación vive en `cuadricula_zona_grid_data_build.php` (función
 * {@see misas_cuadricula_zona_grid_build()}). Este fragmento existe para que cualquier
 * `require` en espacio global tenga los mismos `use` que el builder; PHPUnit comprueba
 * que ambos ficheros declaran los mismos imports (`CuadriculaZonaGridFragmentUsesMatchParentTest`).
 */

use src\shared\config\ConfigGlobal;
use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\actividades\domain\value_objects\StatusId;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdHorarioRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoTipoRepositoryInterface;
use src\encargossacd\domain\EncargoConstants;
use src\misas\application\services\InicialesSacdService;
use src\misas\domain\contracts\EncargoDiaRepositoryInterface;
use src\misas\domain\EncargosZona;
use src\misas\domain\value_objects\EncargoDiaStatus;
use src\misas\domain\value_objects\PlantillaConfig;
use src\shared\domain\value_objects\DateTimeLocal;
use src\usuarios\domain\contracts\PreferenciaRepositoryInterface;
use src\usuarios\domain\entity\Preferencia;
use src\usuarios\domain\value_objects\TipoPreferencia;
use src\usuarios\domain\value_objects\ValorPreferencia;
use src\zonassacd\domain\contracts\ZonaSacdRepositoryInterface;
use src\actividades\domain\entity\TiposActividades;
