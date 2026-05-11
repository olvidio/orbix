<?php

namespace Tests\unit\cartaspresentacion\application;

use PHPUnit\Framework\TestCase;
use src\cartaspresentacion\application\CartasPresentacionShellData;
use src\shared\config\ConfigGlobal;

final class CartasPresentacionShellDataTest extends TestCase
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

    public function test_build_incluye_mi_dele_y_paths(): void
    {
        $data = CartasPresentacionShellData::build();

        $this->assertSame(ConfigGlobal::mi_delef(), $data['mi_dele']);
        $this->assertArrayHasKey('paths', $data);
        $this->assertSame('src/cartaspresentacion/poblaciones_data', $data['paths']['poblaciones']);
        $this->assertArrayHasKey('hash_lista', $data);
        $this->assertArrayHasKey('hash_eliminar', $data);
    }
}
