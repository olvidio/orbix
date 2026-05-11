<?php

namespace Tests\unit\actividadplazas\application;

use PHPUnit\Framework\TestCase;
use src\actividadplazas\application\PlazasBalanceData;

/**
 * Validaciones antes de sesion `oConfig` y consultas a repos.
 */
final class PlazasBalanceDataTest extends TestCase
{
    private mixed $previousContainer;
    private array $previousSession;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousContainer = $GLOBALS['container'] ?? null;
        $this->previousSession = $_SESSION ?? [];
        $_SESSION['session_auth'] = [
            'id_usuario' => 1,
            'esquema' => 'H-dlv',
            'sfsv' => 1,
        ];
    }

    protected function tearDown(): void
    {
        if ($this->previousContainer === null) {
            unset($GLOBALS['container']);
        } else {
            $GLOBALS['container'] = $this->previousContainer;
        }
        $_SESSION = $this->previousSession;
        parent::tearDown();
    }

    public function test_falta_dl(): void
    {
        $GLOBALS['container'] = new class {
            public function get(string $id): never
            {
                throw new \RuntimeException('Unexpected DI: ' . $id);
            }
        };

        $out = PlazasBalanceData::execute(['dl' => '', 'id_tipo_activ' => '123456']);
        $this->assertArrayHasKey('error', $out);
        $this->assertSame('', $out['dlB']);
    }

    public function test_dl_igual_a_la_propia(): void
    {
        $GLOBALS['container'] = new class {
            public function get(string $id): never
            {
                throw new \RuntimeException('Unexpected DI: ' . $id);
            }
        };

        $out = PlazasBalanceData::execute(['dl' => 'dl', 'id_tipo_activ' => '123456']);
        $this->assertArrayHasKey('error', $out);
        $this->assertSame('dl', $out['dlB']);
    }
}
