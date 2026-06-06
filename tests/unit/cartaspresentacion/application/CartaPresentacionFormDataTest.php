<?php

namespace Tests\unit\cartaspresentacion\application;

use PHPUnit\Framework\TestCase;
use src\cartaspresentacion\application\CartaPresentacionFormData;
use src\cartaspresentacion\domain\contracts\CartaPresentacionRepositoryInterface;
use src\cartaspresentacion\domain\entity\CartaPresentacion;
use src\shared\config\ConfigGlobal;
use src\ubis\domain\contracts\CentroRepositoryInterface;
use src\ubis\domain\contracts\DireccionCentroRepositoryInterface;
use src\ubis\domain\entity\Centro;
use src\ubis\domain\entity\Direccion;

final class CartaPresentacionFormDataTest extends TestCase
{
    /** @var array<string, mixed> */
    private array $previousSession;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousSession = $_SESSION ?? [];
        ConfigGlobal::setTest_mode(true);
        $_SESSION['session_auth'] = [
            'esquema' => 'H-dlbv',
            'sfsv' => 1,
        ];
    }

    protected function tearDown(): void
    {
        $_SESSION = $this->previousSession;
        ConfigGlobal::setTest_mode(false);
        parent::tearDown();
    }

    public function test_faltan_ids(): void
    {
        $useCase = new CartaPresentacionFormData(
            $this->createMock(DireccionCentroRepositoryInterface::class),
            $this->createMock(CentroRepositoryInterface::class),
            $this->createMock(CartaPresentacionRepositoryInterface::class),
        );

        $rta = $useCase->execute([]);
        $this->assertFalse($rta['ok']);
        $this->assertNotSame('', $rta['mensaje']);
    }

    public function test_centro_no_encontrado(): void
    {
        $repoDir = $this->createMock(DireccionCentroRepositoryInterface::class);
        $repoDir->method('findById')->willReturn(null);

        $repoCentro = $this->createMock(CentroRepositoryInterface::class);
        $repoCentro->method('findById')->willReturn(null);

        $useCase = new CartaPresentacionFormData(
            $repoDir,
            $repoCentro,
            $this->createMock(CartaPresentacionRepositoryInterface::class),
        );

        $rta = $useCase->execute(['id_ubi' => 1, 'id_direccion' => 2]);
        $this->assertFalse($rta['ok']);
        $this->assertNotSame('', $rta['mensaje']);
    }

    public function test_otra_dl_sin_ser_cr(): void
    {
        $oCentro = $this->createMock(Centro::class);
        $oCentro->method('getNombre_ubi')->willReturn('Centro X');
        $oCentro->method('getDl')->willReturn('otra');
        $oCentro->method('getTipo_ctr')->willReturn('dl');

        $repoDir = $this->createMock(DireccionCentroRepositoryInterface::class);
        $repoDir->method('findById')->willReturn(null);

        $repoCentro = $this->createMock(CentroRepositoryInterface::class);
        $repoCentro->method('findById')->willReturn($oCentro);

        $useCase = new CartaPresentacionFormData(
            $repoDir,
            $repoCentro,
            $this->createMock(CartaPresentacionRepositoryInterface::class),
        );

        $rta = $useCase->execute(['id_ubi' => 1, 'id_direccion' => 2]);
        $this->assertFalse($rta['ok']);
        $this->assertSame('Centro X', $rta['nombre_ubi']);
        $this->assertNotSame('', $rta['mensaje']);
    }

    public function test_exito_misma_dl_sin_carta(): void
    {
        $miDl = ConfigGlobal::mi_delef();

        $oCentro = $this->createMock(Centro::class);
        $oCentro->method('getNombre_ubi')->willReturn('Mi centro');
        $oCentro->method('getDl')->willReturn($miDl);
        $oCentro->method('getTipo_ctr')->willReturn('dl');

        $repoDir = $this->createMock(DireccionCentroRepositoryInterface::class);
        $repoDir->method('findById')->willReturn(null);

        $repoCentro = $this->createMock(CentroRepositoryInterface::class);
        $repoCentro->method('findById')->willReturn($oCentro);

        $repoCarta = $this->createMock(CartaPresentacionRepositoryInterface::class);
        $repoCarta->method('findById')->with(5, 6)->willReturn(null);

        $rta = (new CartaPresentacionFormData($repoDir, $repoCentro, $repoCarta))
            ->execute(['id_ubi' => 5, 'id_direccion' => 6]);
        $this->assertTrue($rta['ok']);
        $this->assertSame('Mi centro', $rta['nombre_ubi']);
        $this->assertSame('', $rta['pres_nom']);
        $this->assertArrayHasKey('hash_update', $rta);
    }

    public function test_exito_cr_extranjero_con_carta(): void
    {
        $oCentro = $this->createMock(Centro::class);
        $oCentro->method('getNombre_ubi')->willReturn('CR casa');
        $oCentro->method('getDl')->willReturn('xx');
        $oCentro->method('getTipo_ctr')->willReturn('cr');

        $oDir = $this->createMock(Direccion::class);
        $oDir->method('getNom_sede')->willReturn('Sede A');

        $oCarta = $this->createMock(CartaPresentacion::class);
        $oCarta->method('getPres_nom')->willReturn('Dir');
        $oCarta->method('getPres_telf')->willReturn('111');
        $oCarta->method('getPres_mail')->willReturn('a@b.c');
        $oCarta->method('getZona')->willReturn('z');
        $oCarta->method('getObserv')->willReturn('o');

        $repoDir = $this->createMock(DireccionCentroRepositoryInterface::class);
        $repoDir->method('findById')->willReturn($oDir);

        $repoCentro = $this->createMock(CentroRepositoryInterface::class);
        $repoCentro->method('findById')->willReturn($oCentro);

        $repoCarta = $this->createMock(CartaPresentacionRepositoryInterface::class);
        $repoCarta->method('findById')->willReturn($oCarta);

        $rta = (new CartaPresentacionFormData($repoDir, $repoCentro, $repoCarta))
            ->execute(['id_ubi' => 1, 'id_direccion' => 2]);
        $this->assertTrue($rta['ok']);
        $this->assertStringContainsString('Sede A', $rta['nombre_ubi']);
        $this->assertSame('Dir', $rta['pres_nom']);
        $this->assertSame('111', $rta['pres_telf']);
        $this->assertSame('a@b.c', $rta['pres_mail']);
        $this->assertSame('z', $rta['zona']);
        $this->assertSame('o', $rta['observ']);
    }
}
