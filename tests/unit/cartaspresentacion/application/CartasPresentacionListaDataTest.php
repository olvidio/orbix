<?php

namespace Tests\unit\cartaspresentacion\application;

use PHPUnit\Framework\TestCase;
use src\cartaspresentacion\application\CartasPresentacionListaData;
use src\cartaspresentacion\domain\contracts\CartaPresentacionDlRepositoryInterface;
use src\cartaspresentacion\domain\contracts\CartaPresentacionRepositoryInterface;
use src\ubis\domain\contracts\CentroRepositoryInterface;
use src\ubis\domain\contracts\DireccionCentroRepositoryInterface;
use src\ubis\domain\contracts\RelacionCentroDireccionRepositoryInterface;

final class CartasPresentacionListaDataTest extends TestCase
{
    public function test_que_desconocido_devuelve_html_vacio(): void
    {
        $useCase = new CartasPresentacionListaData(
            $this->createMock(CartaPresentacionDlRepositoryInterface::class),
            $this->createMock(CartaPresentacionRepositoryInterface::class),
            $this->createMock(CentroRepositoryInterface::class),
            $this->createMock(DireccionCentroRepositoryInterface::class),
            $this->createMock(RelacionCentroDireccionRepositoryInterface::class),
        );

        $rta = $useCase->execute(['que' => '']);
        $this->assertSame('', $rta['html_lista']);
        $this->assertSame('', $rta['html_errores']);
    }
}
