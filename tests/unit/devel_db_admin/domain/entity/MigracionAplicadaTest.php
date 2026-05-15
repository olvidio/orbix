<?php

declare(strict_types=1);

namespace Tests\unit\devel_db_admin\domain\entity;

use src\devel_db_admin\domain\entity\MigracionAplicada;
use src\devel_db_admin\domain\value_objects\MigracionDatabase;
use src\devel_db_admin\domain\value_objects\MigracionTipo;
use Tests\myTest;

final class MigracionAplicadaTest extends myTest
{
    private MigracionAplicada $entity;

    public function setUp(): void
    {
        parent::setUp();
        $this->entity = new MigracionAplicada();
        $this->entity->setPrefijo('202605141630');
        $this->entity->setDescripcion('add_col');
        $this->entity->setDatabaseVo(new MigracionDatabase(MigracionDatabase::SV_E));
        $this->entity->setTipoVo(new MigracionTipo(MigracionTipo::ESTRUCTURA));
        $this->entity->setSha1(str_repeat('a', 40));
        $this->entity->setUsuario('tester');
        $this->entity->setOk(true);
    }

    public function test_getters_basicos(): void
    {
        $this->assertSame('202605141630', $this->entity->getPrefijo());
        $this->assertSame('add_col', $this->entity->getDescripcion());
        $this->assertSame(MigracionDatabase::SV_E, $this->entity->getDatabase());
        $this->assertSame(MigracionTipo::ESTRUCTURA, $this->entity->getTipo());
        $this->assertSame(str_repeat('a', 40), $this->entity->getSha1());
        $this->assertSame('tester', $this->entity->getUsuario());
        $this->assertTrue($this->entity->isOk());
    }

    public function test_set_ok_desde_string_postgresql(): void
    {
        $this->entity->setOk('f');
        $this->assertFalse($this->entity->isOk());

        $this->entity->setOk('t');
        $this->assertTrue($this->entity->isOk());
    }
}
