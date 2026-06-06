<?php

namespace Tests\unit\cartaspresentacion\application;

use PHPUnit\Framework\TestCase;
use src\cartaspresentacion\application\CartasPresentacionUbisListaData;
use src\cartaspresentacion\domain\contracts\CartaPresentacionDlRepositoryInterface;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroExRepositoryInterface;
use src\ubis\domain\contracts\DireccionCentroDlRepositoryInterface;
use src\ubis\domain\contracts\DireccionCentroExRepositoryInterface;
use src\ubis\domain\contracts\RelacionCentroDireccionRepositoryInterface;
use src\ubis\domain\contracts\RelacionCentroExDireccionRepositoryInterface;

final class CartasPresentacionUbisListaDataTest extends TestCase
{
    private function useCase(
        ?CartaPresentacionDlRepositoryInterface $repoCarta = null,
    ): CartasPresentacionUbisListaData {
        return new CartasPresentacionUbisListaData(
            $this->createMock(DireccionCentroDlRepositoryInterface::class),
            $this->createMock(CentroDlRepositoryInterface::class),
            $this->createMock(RelacionCentroDireccionRepositoryInterface::class),
            $repoCarta ?? $this->createMock(CartaPresentacionDlRepositoryInterface::class),
            $this->createMock(CentroExRepositoryInterface::class),
            $this->createMock(RelacionCentroExDireccionRepositoryInterface::class),
            $this->createMock(DireccionCentroExRepositoryInterface::class),
        );
    }

    public function test_tipo_lista_desconocido_sin_filas(): void
    {
        $rta = $this->useCase()->execute(['tipo_lista' => '']);
        $this->assertSame('', $rta['tipo_lista']);
        $this->assertSame([], $rta['a_valores']);
    }

    public function test_get_dl_sin_poblacion_solo_pide_repo_cartas(): void
    {
        $repoCarta = $this->createMock(CartaPresentacionDlRepositoryInterface::class);

        $rta = $this->useCase($repoCarta)->execute([
            'tipo_lista' => 'get_dl',
            'poblacion_sel' => '',
        ]);
        $this->assertSame('get_dl', $rta['tipo_lista']);
        $this->assertSame([], $rta['a_valores']);
        $this->assertNotSame('', $rta['explicacion']);
    }
}
