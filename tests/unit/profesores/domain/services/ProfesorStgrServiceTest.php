<?php

declare(strict_types=1);

namespace Tests\unit\profesores\domain\services;

use PHPUnit\Framework\TestCase;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\personas\domain\contracts\PersonaPubRepositoryInterface;
use src\personas\domain\entity\PersonaDl;
use src\profesores\domain\contracts\ProfesorStgrRepositoryInterface;
use src\profesores\domain\entity\ProfesorStgr;
use src\profesores\domain\services\ProfesorStgrService;

/** Profesores del stgr en activo con su dl, ordenados por apellidos sin tener en cuenta los acentos. */
final class ProfesorStgrServiceTest extends TestCase
{
    private function persona(int $id, string $ap1, string $situacion = 'A'): PersonaDl
    {
        $p = new PersonaDl();
        $p->setId_nom($id);
        $p->setApellido1($ap1);
        $p->setNom('Ana');
        $p->setSituacion($situacion);
        $p->setDl('dlb');
        return $p;
    }

    public function test_profesores_con_dl_ordenados_sin_acentos_y_solo_activos(): void
    {
        $profes = array_map(static function (int $id): ProfesorStgr {
            $p = new ProfesorStgr();
            $p->setId_nom($id);
            return $p;
        }, [1, 2, 3, 4, 5]);
        $repo = $this->createMock(ProfesorStgrRepositoryInterface::class);
        $repo->method('matching')->willReturn($profes);
        $personas = [1 => $this->persona(1, 'Zapata'), 2 => $this->persona(2, 'Álvarez'), 3 => $this->persona(3, 'Bueno'), 4 => $this->persona(4, 'Aguilar', 'B')];
        $dl = $this->createMock(PersonaDlRepositoryInterface::class);
        $dl->method('findById')->willReturnCallback(static fn (int $id): ?PersonaDl => $personas[$id] ?? null);

        $out = (new ProfesorStgrService($repo, $dl, $this->createMock(PersonaPubRepositoryInterface::class)))->getArrayProfesoresConDl();

        $this->assertSame([2, 3, 1], array_keys($out), 'Álvarez, Bueno, Zapata (sin la baja ni el que no está)');
        $this->assertSame(['id_nom', 'ap_nom', 'dl'], array_keys($out[2]));
        $this->assertSame('dlb', $out[2]['dl']);
    }
}
