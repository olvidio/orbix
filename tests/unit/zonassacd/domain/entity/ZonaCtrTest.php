<?php

declare(strict_types=1);

namespace Tests\unit\zonassacd\domain\entity;

use src\zonassacd\domain\entity\ZonaCtr;
use Tests\myTest;

class ZonaCtrTest extends myTest
{
    private ZonaCtr $zonaCtr;

    public function setUp(): void
    {
        parent::setUp();
        $this->zonaCtr = new ZonaCtr();
        $this->zonaCtr->setId_ubi(1042);
        $this->zonaCtr->setId_zona(9);
    }

    public function test_set_and_get_id_ubi(): void
    {
        $this->assertSame(1042, $this->zonaCtr->getId_ubi());
        $this->zonaCtr->setId_ubi(2005);
        $this->assertSame(2005, $this->zonaCtr->getId_ubi());
    }

    public function test_set_and_get_id_zona(): void
    {
        $this->assertSame(9, $this->zonaCtr->getId_zona());
        $this->zonaCtr->setId_zona(3);
        $this->assertSame(3, $this->zonaCtr->getId_zona());
    }

    public function test_from_array(): void
    {
        $fila = ZonaCtr::fromArray(['id_ubi' => 1042, 'id_zona' => 7]);
        $this->assertSame(1042, $fila->getId_ubi());
        $this->assertSame(7, $fila->getId_zona());
    }
}
