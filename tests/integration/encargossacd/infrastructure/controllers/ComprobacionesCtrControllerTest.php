<?php

declare(strict_types=1);

namespace Tests\integration\encargossacd\infrastructure\controllers;

use PHPUnit\Framework\MockObject\MockObject;
use src\encargossacd\application\EncargoComprobacionesCtr;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdRepositoryInterface;
use src\encargossacd\domain\entity\Encargo;
use src\shared\infrastructure\InProcessSrcDispatch;
use src\shared\security\HashB;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroEllasRepositoryInterface;
use Tests\myTest;
use Tests\support\SustituyeServiciosDelContenedor;

/**
 * `/src/encargossacd/comprobaciones_ctr` borra encargos de centros inactivos: solo con la cápsula que
 * firma la pantalla de listas (`comprobaciones_ctr_data`). Antes se ejecutaba con cualquier petición.
 *
 * El caso de uso va con repositorios simulados: el test no toca la BD.
 */
final class ComprobacionesCtrControllerTest extends myTest
{
    use SustituyeServiciosDelContenedor;

    private const DIR = __DIR__ . '/../../../../../src/encargossacd/infrastructure/ui/http/controllers/';

    private EncargoRepositoryInterface&MockObject $encargos;
    private EncargoSacdRepositoryInterface&MockObject $encargosSacd;
    /** @var array<string, mixed> */
    private array $postAnterior = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->postAnterior = $_POST;
        $this->encargos = $this->createMock(EncargoRepositoryInterface::class);
        $this->encargosSacd = $this->createMock(EncargoSacdRepositoryInterface::class);
        $this->sustituirServicio(EncargoComprobacionesCtr::class, new EncargoComprobacionesCtr(
            $this->createMock(CentroDlRepositoryInterface::class),
            $this->createMock(CentroEllasRepositoryInterface::class),
            $this->encargos,
            $this->encargosSacd,
        ));
    }

    public function tearDown(): void
    {
        $this->reponerServicios();
        $_POST = $this->postAnterior;
        parent::tearDown();
    }

    /**
     * @param array<string, mixed> $post
     * @return array<string, mixed>
     */
    private function ejecutar(string $controlador, array $post): array
    {
        $_POST = $post;
        InProcessSrcDispatch::begin();
        ob_start();
        try {
            require self::DIR . $controlador;
        } finally {
            $salida = (string) ob_get_clean();
            InProcessSrcDispatch::end();
        }
        $json = json_decode($salida, true);
        $this->assertIsArray($json, "$controlador debe emitir solo JSON; emitió: " . substr($salida, 0, 200));

        return $json;
    }

    /**
     * @param array<string, mixed> $json
     * @return array<string, string>
     */
    private function datos(array $json): array
    {
        $datos = json_decode(is_string($json['data'] ?? null) ? $json['data'] : '', true);

        return is_array($datos) ? array_map(static fn (mixed $v): string => is_scalar($v) ? (string) $v : '', $datos) : [];
    }

    public function test_sin_capsula_no_consulta_ni_borra_nada(): void
    {
        $this->encargos->expects($this->never())->method('matching');
        $this->encargos->expects($this->never())->method('Eliminar');
        $this->encargosSacd->expects($this->never())->method('deleteEncargos');

        $json = $this->ejecutar('comprobaciones_ctr.php', []);

        $this->assertFalse($json['success']);
        $this->assertSame(_("Operación no autorizada"), $json['mensaje']);
    }

    public function test_con_la_capsula_de_otra_accion_tampoco(): void
    {
        $this->encargos->expects($this->never())->method('Eliminar');
        $this->encargosSacd->expects($this->never())->method('deleteEncargos');
        $ctx = HashB::sign('propuestas_aprobar');

        $json = $this->ejecutar('comprobaciones_ctr.php', ['ctx_comprobar' => $ctx]);

        $this->assertFalse($json['success']);
    }

    public function test_con_la_capsula_de_la_pantalla_ejecuta_las_comprobaciones(): void
    {
        $encargo = new Encargo();
        $encargo->setId_enc(7);
        $this->encargos->method('matching')->willReturn([$encargo]);
        $this->encargosSacd->expects($this->once())->method('deleteEncargos')->with([7])->willReturn('sacd: 0');
        $ctx = $this->datos($this->ejecutar('comprobaciones_ctr_data.php', []))['ctx_comprobar'] ?? '';

        $json = $this->ejecutar('comprobaciones_ctr.php', ['ctx_comprobar' => $ctx]);

        $this->assertTrue($json['success']);
        $this->assertStringContainsString('sacd: 0', (string) ($this->datos($json)['texto'] ?? ''));
    }
}
