<?php

declare(strict_types=1);

namespace Tests\unit\shared\domain\entity;

use src\shared\domain\entity\Entity;
use src\shared\domain\events\EntidadModificada;
use Tests\myTest;

final class EntityTestFixture extends Entity
{
    public function exposeEntityName(): string
    {
        return $this->getEntityName();
    }
}

final class EntityTest extends myTest
{
    public function test_fixture_entity_name_uses_class_short_name(): void
    {
        $e = new EntityTestFixture();
        $this->assertSame('EntityTestFixture', $e->exposeEntityName());
    }

    public function test_marcar_como_nueva_records_domain_event(): void
    {
        $e = new EntityTestFixture();
        $e->marcarComoNueva([]);
        $events = $e->pullDomainEvents();
        $this->assertCount(1, $events);
        $this->assertInstanceOf(EntidadModificada::class, $events[0]);
    }

    public function test_marcar_como_modificada_records_update_event(): void
    {
        $e = new EntityTestFixture();
        $e->marcarComoModificada(['prev' => 1]);
        $events = $e->pullDomainEvents();
        $this->assertCount(1, $events);
        $evt = $events[0];
        $this->assertInstanceOf(EntidadModificada::class, $evt);
        $this->assertSame('UPDATE', $evt->tipoCambio);
    }

    public function test_marcar_como_eliminada_records_delete_event(): void
    {
        $e = new EntityTestFixture();
        $e->marcarComoEliminada(['id' => 9]);
        $events = $e->pullDomainEvents();
        $this->assertCount(1, $events);
        $evt = $events[0];
        $this->assertInstanceOf(EntidadModificada::class, $evt);
        $this->assertSame('DELETE', $evt->tipoCambio);
    }
}
