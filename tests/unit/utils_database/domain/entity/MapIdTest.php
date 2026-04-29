<?php

declare(strict_types=1);

namespace Tests\unit\utils_database\domain\entity;

use src\utils_database\domain\entity\MapId;
use src\utils_database\domain\value_objects\MapIdDl;
use src\utils_database\domain\value_objects\MapIdResto;
use src\utils_database\domain\value_objects\MapObjectCode;
use Tests\myTest;

final class MapIdTest extends myTest
{
    private MapId $mapId;

    public function setUp(): void
    {
        parent::setUp();
        $this->mapId = new MapId();
        $this->mapId->setObjetoVo(new MapObjectCode('Actividad'));
        $this->mapId->setIdRestoVo(new MapIdResto(10));
        $this->mapId->setIdDlVo(new MapIdDl(20));
    }

    public function test_get_set_objeto_vo(): void
    {
        $this->assertSame('Actividad', $this->mapId->getObjetoVo()->value());
        $this->assertSame('Actividad', $this->mapId->getObjeto());
    }

    public function test_get_set_id_resto_vo(): void
    {
        $this->assertSame(10, $this->mapId->getIdRestoVo()->value());
        $this->assertSame(10, $this->mapId->getId_resto());
    }

    public function test_get_set_id_dl_vo(): void
    {
        $this->assertSame(20, $this->mapId->getIdDlVo()->value());
        $this->assertSame(20, $this->mapId->getId_dl());
    }
}
