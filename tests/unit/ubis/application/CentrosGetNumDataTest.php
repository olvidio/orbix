<?php

declare(strict_types=1);

namespace Tests\unit\ubis\application;

use PHPUnit\Framework\TestCase;
use src\ubis\application\CentrosGetNumData;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;

/**
 * Lista de centros para la vista "números" (payload JSON vía {@see CentrosGetNumData::execute}).
 */
final class CentrosGetNumDataTest extends TestCase
{
    public function test_execute_cumple_contrato_de_claves(): void
    {
        $repo = $this->createMock(CentroDlRepositoryInterface::class);
        $repo->method('getCentros')->willReturn([]);

        $useCase = new CentrosGetNumData($repo);
        $data = $useCase->execute();

        $this->assertSame(['a_cabeceras', 'a_valores'], array_keys($data));
        $this->assertCount(4, $data['a_cabeceras']);
        $this->assertIsArray($data['a_valores']);
    }

    public function test_execute_construye_filas_indexadas_y_rellena_ceros_en_vacios(): void
    {
        $centro = new class {
            public function getId_ubi(): int
            {
                return 3;
            }
            public function getNombre_ubi(): string
            {
                return 'N1';
            }
            public function getN_buzon(): string
            {
                return 'B1';
            }
            public function getNum_pi(): null
            {
                return null;
            }
            public function getNum_cartas(): null
            {
                return null;
            }
        };

        $repo = $this->createMock(CentroDlRepositoryInterface::class);
        $repo->method('getCentros')->willReturn([$centro]);

        $useCase = new CentrosGetNumData($repo);
        $data = $useCase->execute();

        $this->assertSame([
            1 => [
                1 => ['script' => 'fnjs_modificar(3,"num")', 'valor' => 'N1'],
                2 => 'B1',
                3 => '0',
                4 => '0',
            ],
        ], $data['a_valores']);
    }
}
