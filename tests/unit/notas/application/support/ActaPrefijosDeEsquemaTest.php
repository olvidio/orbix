<?php

declare(strict_types=1);

namespace Tests\unit\notas\application\support;

use PHPUnit\Framework\TestCase;
use src\notas\application\support\ActaPrefijosDeEsquema;
use src\notas\domain\contracts\MapaPrefijoActaEsquemaRepositoryInterface;

/**
 * Prefijos de acta propios de la dl de la sesión: los del mapa de esquemas (dl absorbidas) más la dl actual, y qué
 * actas se pueden crear en formato acta.
 */
final class ActaPrefijosDeEsquemaTest extends TestCase
{
    /** @var array<string, mixed> */
    private array $authAnterior;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authAnterior = $_SESSION['session_auth'] ?? [];
        $this->sesion('H-dlbv', 1);
    }

    protected function tearDown(): void
    {
        $_SESSION['session_auth'] = $this->authAnterior;
        parent::tearDown();
    }

    private function sesion(string $esquema, int $sfsv): void
    {
        $_SESSION['session_auth'] = array_merge($this->authAnterior, ['esquema' => $esquema, 'sfsv' => $sfsv]);
    }

    /**
     * @param list<string> $mapa
     */
    private function prefijos(array $mapa, ?string $esperaBase = null): ActaPrefijosDeEsquema
    {
        $repo = $this->createMock(MapaPrefijoActaEsquemaRepositoryInterface::class);
        if ($esperaBase === null) {
            $repo->method('prefijosPorEsquemaBase')->willReturn($mapa);
        } else {
            $repo->expects($this->once())->method('prefijosPorEsquemaBase')->with($esperaBase)->willReturn($mapa);
        }

        return new ActaPrefijosDeEsquema($repo);
    }

    public function test_esquema_base_de_la_sesion(): void
    {
        $this->assertSame('H-dlb', ActaPrefijosDeEsquema::esquemaBaseSesion());

        $this->sesion('', 1);
        $this->assertSame('', ActaPrefijosDeEsquema::esquemaBaseSesion());
    }

    public function test_prefijos_del_mapa_con_la_dl_de_la_sesion_delante(): void
    {
        $this->assertSame(['dlb', 'crb', 'dlx'], $this->prefijos(['crb', 'dlx'], 'H-dlb')->prefijosSesion());
        $this->assertSame(['crb', 'dlb'], $this->prefijos(['crb', 'dlb', 'crb'])->prefijosSesion(), 'sin repetidos; si el mapa ya la tiene, en su orden');
    }

    public function test_en_sf_la_dl_con_f_delante_y_sin_f_al_final(): void
    {
        $this->sesion('H-dlbf', 2);

        $this->assertSame(['dlbf', 'crb', 'dlb'], $this->prefijos(['crb'])->prefijosSesion());
    }

    public function test_sin_esquema_no_hay_prefijos_ni_se_consulta_el_mapa(): void
    {
        $this->sesion('', 1);
        $repo = $this->createMock(MapaPrefijoActaEsquemaRepositoryInterface::class);
        $repo->expects($this->never())->method('prefijosPorEsquemaBase');
        $prefijos = new ActaPrefijosDeEsquema($repo);

        $this->assertSame([], $prefijos->prefijosSesion());
        $this->assertSame('', $prefijos->patronBusquedaPorNumero('12', true));
    }

    public function test_prefijo_y_numero_de_un_acta(): void
    {
        $this->assertSame('dlb', ActaPrefijosDeEsquema::prefijoDeActa(' dlb 12/24 '));
        $this->assertSame('', ActaPrefijosDeEsquema::prefijoDeActa('?'));
        $this->assertSame('', ActaPrefijosDeEsquema::prefijoDeActa(''));
        $this->assertSame('12/24', ActaPrefijosDeEsquema::numeroDeActa('dlb 12/24?'));
        $this->assertSame('', ActaPrefijosDeEsquema::numeroDeActa('dlb 12'));
    }

    public function test_actas_permitidas_en_formato_acta(): void
    {
        $prefijos = $this->prefijos(['crb']);

        $this->assertTrue($prefijos->actaPermitidaEnFormatoActa(''), 'vacía: se inventará');
        $this->assertTrue($prefijos->actaPermitidaEnFormatoActa('?'));
        $this->assertTrue($prefijos->actaPermitidaEnFormatoActa('? 3/24'));
        $this->assertTrue($prefijos->actaPermitidaEnFormatoActa('dlb 3/24'));
        $this->assertTrue($prefijos->actaPermitidaEnFormatoActa('crb 3/24'), 'prefijo de una dl absorbida');
        $this->assertFalse($prefijos->actaPermitidaEnFormatoActa('dlmE 3/24'), 'de otra dl');
    }

    public function test_patron_de_busqueda_por_numero(): void
    {
        $prefijos = $this->prefijos(['crb']);
        $y = date('y');

        $this->assertSame("dlb 12/$y|crb 12/$y", $prefijos->patronBusquedaPorNumero('12', true));
        $this->assertSame('dlb 12/24|crb 12/24', $prefijos->patronBusquedaPorNumero('12/24', false));
    }
}
