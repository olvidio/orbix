<?php

declare(strict_types=1);

namespace Tests\unit\misas\domain\entity;

use src\misas\domain\entity\Plantilla;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\value_objects\TimeLocal;
use Tests\factories\misas\PlantillaFactory;
use Tests\myTest;

final class PlantillaTest extends myTest
{
    public function test_valores_por_defecto_de_los_campos_opcionales(): void
    {
        $o = new Plantilla();

        $this->assertSame('', $o->getDia());
        $this->assertNull($o->getSemana());
        $this->assertNull($o->getT_start());
        $this->assertNull($o->getT_end());
        $this->assertNull($o->getId_nom());
        $this->assertNull($o->getObserv());
    }

    public function test_set_and_get_campos(): void
    {
        $o = new Plantilla();
        $o->setId_item(7);
        $o->setId_ctr(11);
        $o->setTarea(3);
        $o->setDia('l');
        $o->setSemana(2);
        $o->setT_start(TimeLocal::fromString('07:30:00'));
        $o->setT_end(TimeLocal::fromString('08:15:00'));
        $o->setId_nom(10011);
        $o->setObserv('nota');

        $this->assertSame(7, $o->getId_item());
        $this->assertSame(11, $o->getId_ctr());
        $this->assertSame(3, $o->getTarea());
        $this->assertSame('l', $o->getDia());
        $this->assertSame(2, $o->getSemana());
        $this->assertSame('07:30:00', $o->getT_start()->format('H:i:s'));
        $this->assertSame('08:15:00', $o->getT_end()->format('H:i:s'));
        $this->assertSame(10011, $o->getId_nom());
        $this->assertSame('nota', $o->getObserv());
    }

    public function test_los_opcionales_admiten_null_para_vaciarlos(): void
    {
        $o = (new PlantillaFactory())->createSimple(5);
        $o->setT_start(null);
        $o->setT_end(null);
        $o->setSemana(null);
        $o->setId_nom(null);
        $o->setObserv(null);

        $this->assertNull($o->getT_start());
        $this->assertNull($o->getT_end());
        $this->assertNull($o->getSemana());
        $this->assertNull($o->getId_nom());
        $this->assertNull($o->getObserv());
    }

    public function test_acepta_datetimelocal_y_timelocal_en_las_horas(): void
    {
        $o = new Plantilla();
        $o->setT_start(new DateTimeLocal('2024-01-01 09:00:00'));
        $o->setT_end(TimeLocal::fromString('10:00:00'));

        $this->assertInstanceOf(DateTimeLocal::class, $o->getT_start());
        $this->assertInstanceOf(TimeLocal::class, $o->getT_end());
    }

    public function test_from_array_hidrata_como_filas_de_bd(): void
    {
        $o = Plantilla::fromArray([
            'id_item' => 9,
            'id_ctr' => 4,
            'tarea' => 2,
            'dia' => 'm',
            'semana' => null,
            't_start' => TimeLocal::fromString('18:00:00'),
            't_end' => null,
            'id_nom' => null,
            'observ' => null,
        ]);

        $this->assertSame(9, $o->getId_item());
        $this->assertSame(4, $o->getId_ctr());
        $this->assertSame(2, $o->getTarea());
        $this->assertSame('m', $o->getDia());
        $this->assertNull($o->getSemana());
        $this->assertSame('18:00:00', $o->getT_start()->format('H:i:s'));
        $this->assertNull($o->getT_end());
    }

    public function test_to_array_for_database(): void
    {
        $o = (new PlantillaFactory())->createSimple(12);

        $a = $o->toArrayForDatabase();

        $this->assertSame(12, $a['id_item']);
        $this->assertSame(1, $a['id_ctr']);
        $this->assertSame(1, $a['tarea']);
        $this->assertSame('test_dia', $a['dia']);
        $this->assertNull($a['semana']);
        $this->assertNull($a['id_nom']);
        $this->assertNull($a['observ']);
    }
}
