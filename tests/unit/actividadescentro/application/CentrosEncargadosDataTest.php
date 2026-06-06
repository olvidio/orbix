<?php

declare(strict_types=1);

namespace Tests\unit\actividadescentro\application;

use PHPUnit\Framework\TestCase;
use src\actividadescentro\application\CentrosEncargadosData;
use src\actividadescentro\domain\contracts\CentroEncargadoRepositoryInterface;

final class CentrosEncargadosDataTest extends TestCase
{
    private mixed $previousPermActividades = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousPermActividades = $_SESSION['oPermActividades'] ?? null;
        unset($_SESSION['oPermActividades']);
        if (!isset($_SESSION['session_auth']) || !is_array($_SESSION['session_auth'])) {
            $_SESSION['session_auth'] = [];
        }
        $_SESSION['session_auth']['id_usuario'] = $_SESSION['session_auth']['id_usuario'] ?? 1;
    }

    protected function tearDown(): void
    {
        if ($this->previousPermActividades === null) {
            unset($_SESSION['oPermActividades']);
        } else {
            $_SESSION['oPermActividades'] = $this->previousPermActividades;
        }
        parent::tearDown();
    }

    public function test_id_activ_no_positivo_devuelve_vacio(): void
    {
        $repo = $this->createStub(CentroEncargadoRepositoryInterface::class);
        $useCase = new CentrosEncargadosData($repo);

        $out = $useCase->execute(['id_activ' => 0]);

        $this->assertSame([
            'id_activ' => 0,
            'permite_ver' => false,
            'permite_modificar' => false,
            'centros' => [],
        ], $out);
    }

    public function test_con_permiso_true_lista_centros(): void
    {
        $centro = new class {
            public function getId_ubi(): int
            {
                return 9;
            }
            public function getNombre_ubi(): string
            {
                return 'Centro N';
            }
        };

        $repo = $this->createStub(CentroEncargadoRepositoryInterface::class);
        $repo->method('getCentrosEncargadosActividad')->with(3)->willReturn([$centro]);

        $useCase = new CentrosEncargadosData($repo);
        $out = $useCase->execute([
            'id_activ' => 3,
            'id_tipo_activ' => '100000',
            'dl_org' => 'u',
        ]);

        $this->assertSame(3, $out['id_activ']);
        $this->assertTrue($out['permite_ver']);
        $this->assertTrue($out['permite_modificar']);
        $this->assertSame([['id_ubi' => 9, 'nombre_ubi' => 'Centro N']], $out['centros']);
    }
}
