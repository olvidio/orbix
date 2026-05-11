<?php

declare(strict_types=1);

namespace Tests\unit\ubis\application;

use PHPUnit\Framework\TestCase;
use src\shared\config\ConfigGlobal;
use src\ubis\application\UbisTiposLaborEtiquetas;
use src\ubis\domain\CuadrosLaborBits;

final class UbisTiposLaborEtiquetasTest extends TestCase
{
    private bool $hadSessionSfsv = false;
    private mixed $previousSessionSfsv = null;

    protected function setUp(): void
    {
        parent::setUp();
        if (!isset($_SESSION['session_auth']) || !is_array($_SESSION['session_auth'])) {
            $_SESSION['session_auth'] = [];
        }
        $this->hadSessionSfsv = array_key_exists('sfsv', $_SESSION['session_auth']);
        $this->previousSessionSfsv = $this->hadSessionSfsv ? $_SESSION['session_auth']['sfsv'] : null;
        $_SESSION['session_auth']['sfsv'] = 1;
    }

    protected function tearDown(): void
    {
        if ($this->hadSessionSfsv) {
            $_SESSION['session_auth']['sfsv'] = $this->previousSessionSfsv;
        } else {
            unset($_SESSION['session_auth']['sfsv']);
        }
        parent::tearDown();
    }

    public function test_mapBitToEtiqueta_es_inverso_de_labeledMap(): void
    {
        $expected = array_flip(CuadrosLaborBits::labeledMap(ConfigGlobal::mi_sfsv()));
        $this->assertSame($expected, UbisTiposLaborEtiquetas::mapBitToEtiqueta());
    }
}
