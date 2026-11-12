<?php

declare(strict_types=1);

namespace Tests\unit\personas\application;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use src\personas\application\services\PersonaFinderService;
use src\personas\domain\contracts\PersonaAllRepositoryInterface;
use src\personas\domain\contracts\PersonaDlRepositoryFactoryInterface;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\personas\domain\contracts\PersonaExRepositoryInterface;
use src\personas\domain\contracts\PersonaPubRepositoryInterface;
use src\personas\domain\entity\PersonaDl;
use src\personas\domain\entity\PersonaEx;
use src\personas\domain\entity\PersonaPub;
use Tests\support\DescribeCriteria;

/**
 * Búsqueda de una persona por id_nom: en la dl (activa), en global, de paso
 * (id negativo), incluyendo no activas, y para listados (dl sin región stgr).
 */
final class PersonaFinderServiceBusquedasTest extends TestCase
{
    private PersonaDlRepositoryInterface&MockObject $dl;
    private PersonaPubRepositoryInterface&MockObject $pub;
    private PersonaExRepositoryInterface&MockObject $ex;
    private PersonaAllRepositoryInterface&MockObject $all;
    /** @var list<list<string>> */
    private array $consultas = [];

    protected function setUp(): void
    {
        $this->dl = $this->createMock(PersonaDlRepositoryInterface::class);
        $this->pub = $this->createMock(PersonaPubRepositoryInterface::class);
        $this->ex = $this->createMock(PersonaExRepositoryInterface::class);
        $this->all = $this->createMock(PersonaAllRepositoryInterface::class);
    }

    private function svc(): PersonaFinderService
    {
        $factory = $this->createMock(PersonaDlRepositoryFactoryInterface::class);
        $factory->method('create')->willReturn($this->dl);
        return new PersonaFinderService($factory, $this->pub, $this->ex, $this->all);
    }

    /** @param array<string, list<PersonaDl>> $porConsulta clave: filtros unidos por «|» */
    private function enDl(array $porConsulta): void
    {
        $this->dl->method('matching')->willReturnCallback(function ($c) use ($porConsulta): array {
            $f = implode('|', DescribeCriteria::filtros($c));
            $this->consultas[] = DescribeCriteria::filtros($c);
            return $porConsulta[$f] ?? [];
        });
    }

    private function pub(string $situacion): PersonaPub
    {
        $p = new PersonaPub();
        $p->setId_nom(100555);
        $p->setSituacion($situacion);
        return $p;
    }

    public function test_en_la_dl_solo_activas(): void
    {
        $p = new PersonaDl();
        $this->enDl(['id_nom eq 100555|situacion eq A' => [$p]]);
        $this->assertSame($p, $this->svc()->findPersonaEnDl(100555));
        $this->assertNull($this->svc()->findPersonaEnDl(100556));
    }

    public function test_en_global_primero_la_dl_y_luego_global_si_esta_activa(): void
    {
        $local = new PersonaDl();
        $this->enDl(['id_nom eq 1|situacion eq A' => [$local]]);
        $this->all->method('findByIdNomParaLookup')->willReturnCallback(fn (int $id, ?int $schema): ?PersonaPub => match ($id) {
            2 => $this->pub('A'),
            3 => $this->pub('L'),
            default => null,
        });
        $svc = $this->svc();
        $this->assertSame($local, $svc->findPersonaEnGlobal(1));
        $this->assertSame('A', $svc->findPersonaEnGlobal(2)->getSituacion());
        $this->assertNull($svc->findPersonaEnGlobal(3), 'en global pero no activa');
        $this->assertNull($svc->findPersonaEnGlobal(4));
    }

    public function test_de_paso_con_id_negativo(): void
    {
        $ex = new PersonaEx();
        $this->ex->expects($this->once())->method('findById')->with(-5)->willReturn($ex);
        $this->dl->expects($this->never())->method('matching');
        $this->assertSame($ex, $this->svc()->findPersonaEnGlobalODePaso(-5));
    }

    public function test_incluyendo_no_activos(): void
    {
        $inactivaLocal = new PersonaDl();
        $this->enDl(['id_nom eq 7' => [$inactivaLocal]]);
        $this->all->method('findByIdNomParaLookup')->willReturnCallback(fn (int $id): ?PersonaPub => $id === 8 ? $this->pub('L') : null);
        $svc = $this->svc();
        $this->assertSame($inactivaLocal, $svc->findPersonaEnGlobalIncluyendoNoActivos(7));
        $this->assertSame('L', $svc->findPersonaEnGlobalIncluyendoNoActivos(8)->getSituacion(), 'de global aunque no esté activa');
        $this->assertNull($svc->findPersonaEnGlobalIncluyendoNoActivos(9));
        $this->ex->method('findById')->willReturn(new PersonaEx());
        $this->assertInstanceOf(PersonaEx::class, $svc->findPersonaEnGlobalIncluyendoNoActivos(-3));
    }

    public function test_para_listado_si_la_dl_no_tiene_region_stgr_busca_en_pub(): void
    {
        $this->dl->method('matching')->willReturn([]);
        $this->all->method('findByIdNomParaLookup')->willThrowException(new \RuntimeException('falta indicar a que región del stgr pertenece la dl: dlz'));
        $pub = $this->pub('A');
        $this->pub->expects($this->once())->method('findByIdParaListado')->willReturnCallback(function (int $id, array &$problemas, bool &$marca) use ($pub): ?PersonaPub {
            $marca = true;
            return $pub;
        });
        $problemas = [];
        $marca = false;
        $this->assertSame($pub, $this->svc()->findPersonaParaListado(100555, $problemas, $marca));
        $this->assertTrue($marca);
        $this->assertNotSame([], $problemas, 'se apunta la dl sin región');
    }

    public function test_para_listado_otro_error_se_propaga_y_la_activa_se_devuelve(): void
    {
        $local = new PersonaDl();
        $this->enDl(['id_nom eq 1|situacion eq A' => [$local]]);
        $this->all->method('findByIdNomParaLookup')->willThrowException(new \RuntimeException('otra cosa'));
        $svc = $this->svc();
        $this->assertSame($local, $svc->findPersonaParaListado(1));
        $this->expectExceptionMessage('otra cosa');
        $svc->findPersonaParaListado(2);
    }

    public function test_para_listado_de_paso(): void
    {
        $this->ex->method('findById')->willReturn(new PersonaEx());
        $this->assertInstanceOf(PersonaEx::class, $this->svc()->findPersonaParaListado(-1));
    }
}
