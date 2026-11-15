<?php

declare(strict_types=1);

namespace Tests\unit\actividadescentro\domain;

use src\actividadescentro\domain\contracts\CentroEncargadoRepositoryInterface;
use src\actividadescentro\domain\Info3010;
use src\shared\domain\criteria\Criteria;
use Tests\myTest;
use Tests\support\DescribeCriteria;

/** Dossier 3010 «ctr encargados de la actividad»: dentro del dossier de una actividad lista solo sus centros. */
final class Info3010Test extends myTest
{
    public function test_con_actividad_lista_solo_sus_centros_por_orden(): void
    {
        $criterio = null;
        $repo = $this->createMock(CentroEncargadoRepositoryInterface::class);
        $repo->method('matching')->willReturnCallback(function (Criteria $c) use (&$criterio): array {
            $criterio = $c;

            return [];
        });
        $info = new Info3010($repo);
        $info->setId_pau('3001234');

        $this->assertSame([], $info->getColeccion());
        $this->assertInstanceOf(Criteria::class, $criterio);
        $this->assertSame(['id_activ eq 3001234'], DescribeCriteria::filtros($criterio));
        $this->assertSame(3010, $info->getId_dossier());
    }

    public function test_sin_actividad_no_filtra(): void
    {
        $criterio = null;
        $repo = $this->createMock(CentroEncargadoRepositoryInterface::class);
        $repo->method('matching')->willReturnCallback(function (Criteria $c) use (&$criterio): array {
            $criterio = $c;

            return [];
        });

        (new Info3010($repo))->getColeccion();

        $this->assertInstanceOf(Criteria::class, $criterio);
        $this->assertSame([], DescribeCriteria::filtros($criterio));
    }
}
