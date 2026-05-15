<?php

declare(strict_types=1);

namespace Tests\integration\devel_db_admin\infrastructure\repositories;

use src\devel_db_admin\domain\contracts\MigracionAplicadaRepositoryInterface;
use src\devel_db_admin\domain\entity\MigracionAplicada;
use src\devel_db_admin\domain\value_objects\MigracionDatabase;
use Tests\factories\devel_db_admin\MigracionAplicadaFactory;
use Tests\myTest;

final class PgMigracionAplicadaRepositoryTest extends myTest
{
    private MigracionAplicadaRepositoryInterface $repository;
    private MigracionAplicadaFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(MigracionAplicadaRepositoryInterface::class);
        $this->repository->ensureTabla();
        $this->factory = new MigracionAplicadaFactory();
    }

    public function test_registrar_y_find_by_key(): void
    {
        $migracion = $this->factory->createSimple();

        $this->assertTrue($this->repository->registrar($migracion));

        $guardada = $this->repository->findByKey(
            $migracion->getPrefijo(),
            $migracion->getDescripcion(),
            $migracion->getDatabase(),
        );

        $this->assertInstanceOf(MigracionAplicada::class, $guardada);
        $this->assertSame($migracion->getSha1(), $guardada->getSha1());
        $this->assertTrue($this->repository->existe($migracion->getPrefijo(), $migracion->getDescripcion(), $migracion->getDatabase()));

        $this->repository->Eliminar($migracion);
    }

    public function test_registrar_actualiza_existente(): void
    {
        $migracion = $this->factory->createSimple(database: MigracionDatabase::SV_E);
        $this->repository->registrar($migracion);

        $migracion->setSha1(str_repeat('b', 40));
        $migracion->setMensaje('actualizada');
        $this->assertTrue($this->repository->registrar($migracion));

        $guardada = $this->repository->findByKey(
            $migracion->getPrefijo(),
            $migracion->getDescripcion(),
            $migracion->getDatabase(),
        );
        $this->assertNotNull($guardada);
        $this->assertSame(str_repeat('b', 40), $guardada->getSha1());
        $this->assertSame('actualizada', $guardada->getMensaje());

        $this->repository->Eliminar($migracion);
    }

    public function test_eliminar(): void
    {
        $migracion = $this->factory->createSimple();
        $this->repository->registrar($migracion);

        $this->assertTrue($this->repository->Eliminar($migracion));
        $this->assertNull($this->repository->findByKey(
            $migracion->getPrefijo(),
            $migracion->getDescripcion(),
            $migracion->getDatabase(),
        ));
    }
}
