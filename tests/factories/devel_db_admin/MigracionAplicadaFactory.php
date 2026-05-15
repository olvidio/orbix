<?php

declare(strict_types=1);

namespace Tests\factories\devel_db_admin;

use src\devel_db_admin\domain\entity\MigracionAplicada;
use src\devel_db_admin\domain\value_objects\MigracionDatabase;
use src\devel_db_admin\domain\value_objects\MigracionTipo;

final class MigracionAplicadaFactory
{
    public function createSimple(?string $prefijo = null, ?string $descripcion = null, ?string $database = null): MigracionAplicada
    {
        $migracion = new MigracionAplicada();
        $migracion->setPrefijo($prefijo ?? date('YmdHi'));
        $migracion->setDescripcion($descripcion ?? ('test_' . random_int(1000, 9999)));
        $migracion->setDatabase($database ?? MigracionDatabase::COMUN);
        $migracion->setTipo(MigracionTipo::ESTRUCTURA);
        $migracion->setSha1(sha1((string) random_int(1, PHP_INT_MAX)));
        $migracion->setUsuario('phpunit');
        $migracion->setOk(true);
        $migracion->setMensaje(null);

        return $migracion;
    }
}
