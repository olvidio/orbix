<?php

declare(strict_types=1);

namespace Tests\unit\personas\domain\services;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use src\personas\domain\contracts\TelecoPersonaRepositoryInterface;
use src\personas\domain\entity\TelecoPersona;
use src\personas\domain\services\TelecoPersonaService;
use src\ubis\domain\contracts\DescTelecoRepositoryInterface;
use src\ubis\domain\entity\DescTeleco;
use Tests\support\DescribeCriteria;

/** Teléfonos y mails de una persona por tipo, con o sin descripción, y el mail principal. */
final class TelecoPersonaServiceTest extends TestCase
{
    private TelecoPersonaRepositoryInterface&MockObject $telecos;
    private DescTelecoRepositoryInterface&MockObject $descs;
    /** @var list<string> */
    private array $filtros = [];
    /** @var list<string> */
    private array $orden = [];

    protected function setUp(): void
    {
        $this->telecos = $this->createMock(TelecoPersonaRepositoryInterface::class);
        $this->descs = $this->createMock(DescTelecoRepositoryInterface::class);
        $this->descs->method('findById')->willReturnCallback(function (int $id): ?DescTeleco {
            if ($id !== 13) {
                return null;
            }
            $d = new DescTeleco();
            $d->setDesc_teleco('principal');
            return $d;
        });
    }

    private function svc(): TelecoPersonaService
    {
        return new TelecoPersonaService($this->descs, $this->telecos);
    }

    /** @param list<array{0: string, 1: ?int}> $filas número, id_desc_teleco */
    private function devuelve(array $filas): void
    {
        $this->telecos->method('matching')->willReturnCallback(function ($c) use ($filas): array {
            $this->filtros = DescribeCriteria::filtros($c);
            $this->orden = DescribeCriteria::orden($c);
            return array_map(static function (array $f): TelecoPersona {
                $t = new TelecoPersona();
                $t->setNumTelecoVo($f[0]);
                $t->setId_desc_teleco($f[1]);
                return $t;
            }, $filas);
        });
    }

    public function test_telefonos_con_descripcion_y_separador(): void
    {
        $this->devuelve([['93 1', 13], ['93 2', 99], ['93 3', null]]);
        $this->assertSame('93 1(principal) / 93 2() / 93 3', $this->svc()->getTelecosPorTipo(100555, 'telf', ' / ', '*'));
        $this->assertSame(['id_nom eq 100555', 'id_tipo_teleco eq 1'], $this->filtros);
    }

    public function test_sin_descripcion_y_separador_por_defecto(): void
    {
        $this->devuelve([['600 1', 13], ['600 2', 14]]);
        $this->descs->expects($this->never())->method('findById');
        $this->assertSame('600 1.-<br>600 2', $this->svc()->getTelecosPorTipo(100555, 'móvil', '', '*', false));
        $this->assertSame('id_tipo_teleco eq 2', $this->filtros[1]);
    }

    public function test_filtra_por_una_descripcion_concreta(): void
    {
        $this->devuelve([['j@x.org', 14]]);
        $this->assertSame('j@x.org', $this->svc()->getTelecosPorTipo(100555, 'e-mail', ' / ', '14'));
        $this->assertSame(['id_nom eq 100555', 'id_tipo_teleco eq 3', 'id_desc_teleco eq 14'], $this->filtros);
    }

    public function test_sin_telecos_devuelve_vacio(): void
    {
        $this->devuelve([]);
        $this->assertSame('', $this->svc()->getTelecosPorTipo(100555, 'telf', ' / ', '*'));
    }

    public function test_mail_principal_o_primero(): void
    {
        $this->devuelve([['a@x.org', 13], ['b@x.org', 15]]);
        $this->assertSame('a@x.org', $this->svc()->getEmailPrincipalOPrimero(100555));
        $this->assertSame(['id_nom eq 100555', 'id_tipo_teleco eq 3'], $this->filtros, 'sin filtrar por descripción: el principal (13) sale primero por orden');
        $this->assertSame(['id_desc_teleco asc'], $this->orden);

        $this->svc()->getEmailPrincipalOPrimero(100555, 14);
        $this->assertSame('id_desc_teleco eq 14', $this->filtros[2]);
    }

    public function test_sin_mail_devuelve_vacio(): void
    {
        $this->devuelve([]);
        $this->assertSame('', $this->svc()->getEmailPrincipalOPrimero(100555));
    }
}
