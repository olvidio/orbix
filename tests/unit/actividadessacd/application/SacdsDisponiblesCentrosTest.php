<?php

declare(strict_types=1);

namespace Tests\unit\actividadessacd\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\actividadescentro\domain\contracts\CentroEncargadoRepositoryInterface;
use src\actividadescentro\domain\entity\CentroEncargado;
use src\actividadessacd\application\SacdsDisponiblesData;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdRepositoryInterface;
use src\encargossacd\domain\entity\Encargo;
use src\encargossacd\domain\entity\EncargoSacd;
use src\personas\application\services\PersonaFinderService;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\personas\domain\entity\PersonaDl;
use src\personas\domain\entity\PersonaSacd;
use src\shared\security\HashB;
use Tests\myTest;
use Tests\support\DescribeCriteria;
use Tests\support\SustituyeServiciosDelContenedor;

/**
 * Sacd que se pueden asignar a una actividad: los encargados de los centros que
 * la llevan (con encargossacd) y todos los de la selección, con su cápsula.
 */
final class SacdsDisponiblesCentrosTest extends myTest
{
    use SustituyeServiciosDelContenedor;

    private CentroEncargadoRepositoryInterface&MockObject $centros;
    private EncargoRepositoryInterface&MockObject $encargos;
    private EncargoSacdRepositoryInterface&MockObject $encargosSacd;
    private PersonaSacdRepositoryInterface&MockObject $sacds;
    private mixed $configAnterior = null;

    public function setUp(): void
    {
        parent::setUp();
        $this->configAnterior = $_SESSION['config'] ?? null;
        $_SESSION['config'] = ['a_apps' => ['encargossacd' => 9], 'app_installed' => [9]];
        $this->centros = $this->createMock(CentroEncargadoRepositoryInterface::class);
        $this->encargos = $this->createMock(EncargoRepositoryInterface::class);
        $this->encargosSacd = $this->createMock(EncargoSacdRepositoryInterface::class);
        $this->sacds = $this->createMock(PersonaSacdRepositoryInterface::class);
        $this->sacds->method('getSacdsBySelect')->willReturn([]);
        $finder = $this->createMock(PersonaFinderService::class);
        $finder->method('findPersonaEnGlobalODePaso')->willReturnCallback(function (int $id): ?PersonaDl {
            $p = new PersonaDl();
            $p->setNom('Sacd');
            $p->setApellido1("Ap$id");
            return $p;
        });
        $this->sustituirServicio(PersonaFinderService::class, $finder);
    }

    public function tearDown(): void
    {
        $this->reponerServicios();
        $_SESSION['config'] = $this->configAnterior;
        parent::tearDown();
    }

    private function uc(): SacdsDisponiblesData
    {
        return new SacdsDisponiblesData($this->centros, $this->encargos, $this->encargosSacd, $this->sacds);
    }

    private function encargado(int $ubi, int $orden): CentroEncargado
    {
        $c = new CentroEncargado();
        $c->setId_activ(10);
        $c->setId_ubi($ubi);
        $c->setNum_orden($orden);
        return $c;
    }

    public function test_sacd_de_los_centros_encargados_por_orden(): void
    {
        $this->centros->method('matching')->willReturnCallback(function ($c): array {
            $this->assertSame(['id_activ eq 10'], DescribeCriteria::filtros($c));
            $this->assertSame(['num_orden asc'], DescribeCriteria::orden($c));
            // 501: con encargo y sacd; 502: sin encargo de centro; 503: encargo sin sacd vigente.
            return [$this->encargado(501, 1), $this->encargado(502, 2), $this->encargado(503, 3)];
        });
        $this->encargos->method('matching')->willReturnCallback(function ($c): array {
            $f = DescribeCriteria::filtros($c);
            $this->assertSame('id_tipo_enc matches ^1(00|100|200|300)', $f[1]);
            $ubi = (int) substr($f[0], strlen('id_ubi eq '));
            if ($ubi === 502) {
                return [];
            }
            $e = new Encargo();
            $e->setId_enc($ubi * 10);
            return [$e];
        });
        $this->encargosSacd->method('matching')->willReturnCallback(function ($c): array {
            $f = DescribeCriteria::filtros($c);
            $this->assertSame(['modo matches 2|3', 'f_fin is_null'], array_slice($f, 1));
            if ($f[0] === 'id_enc eq 5030') {
                return [];
            }
            $s = new EncargoSacd();
            $s->setId_nom(20);
            return [$s];
        });

        $out = $this->uc()->execute(['id_activ' => '10', 'seleccion' => '1']);

        $this->assertCount(1, $out['sacds_ctr']);
        $this->assertSame([20, 1], [$out['sacds_ctr'][0]['id_nom'], $out['sacds_ctr'][0]['num_orden']]);
        $this->assertStringContainsString('Ap20', $out['sacds_ctr'][0]['ap_nom']);
        $this->assertSame(['id_activ' => 10, 'id_nom' => 20], HashB::open($out['sacds_ctr'][0]['ctx_asignar'], 'sacd_asignar'));
    }

    public function test_todos_los_sacd_de_la_seleccion(): void
    {
        $this->centros->method('matching')->willReturn([]);
        $s = new PersonaSacd();
        $s->setId_nom(30);
        $s->setNom('Otro');
        $s->setApellido1('Sacd');
        $this->sacds = $this->createMock(PersonaSacdRepositoryInterface::class);
        $this->sacds->expects($this->once())->method('getSacdsBySelect')->with(4)->willReturn([$s]);

        $out = $this->uc()->execute(['id_activ' => '10', 'seleccion' => '4']);

        $this->assertSame([], $out['sacds_ctr']);
        $this->assertSame(30, $out['sacds_todos'][0]['id_nom']);
        $this->assertStringContainsString('Sacd', $out['sacds_todos'][0]['ap_nom']);
        $this->assertSame(['id_activ' => 10, 'id_nom' => 30], HashB::open($out['sacds_todos'][0]['ctx_asignar'], 'sacd_asignar'));
    }

    public function test_sin_encargossacd_o_sin_actividad_no_mira_los_centros(): void
    {
        $this->centros->expects($this->never())->method('matching');
        $this->uc()->execute(['id_activ' => '0']);
        $_SESSION['config'] = ['a_apps' => [], 'app_installed' => []];
        $this->assertSame([], $this->uc()->execute(['id_activ' => '10'])['sacds_ctr']);
    }
}
