<?php

namespace Tests\unit\inventario\application;

use PHPUnit\Framework\TestCase;
use src\inventario\application\InventarioCssInlineData;
use src\shared\config\ConfigGlobal;

final class InventarioCssInlineDataTest extends TestCase
{
    private string $previousDirEstilos;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousDirEstilos = ConfigGlobal::$dir_estilos;
        ConfigGlobal::$dir_estilos = dirname(__DIR__, 4) . '/css';
    }

    protected function tearDown(): void
    {
        ConfigGlobal::$dir_estilos = $this->previousDirEstilos;
        parent::tearDown();
    }

    public function test_lee_inventario_css_php(): void
    {
        $data = InventarioCssInlineData::build();
        $this->assertArrayHasKey('css', $data);
        $this->assertStringContainsString('<style>', $data['css']);
        $this->assertStringContainsString('@media print', $data['css']);
    }
}
