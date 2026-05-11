<?php

declare(strict_types=1);

namespace Tests\unit\actividadescentro\application;

use PHPUnit\Framework\TestCase;
use src\actividadescentro\application\ActivCtrShellData;

final class ActivCtrShellDataTest extends TestCase
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

    public function test_contrato_de_claves_sv(): void
    {
        $_SESSION['session_auth']['sfsv'] = 1;
        $out = ActivCtrShellData::build(['tipo' => 'sg']);

        $expectedKeys = [
            'tipo',
            'url_lista',
            'url_encargados',
            'url_disponibles',
            'url_asignar',
            'url_reordenar',
            'url_eliminar',
        ];
        $this->assertSame($expectedKeys, array_keys($out));
        $this->assertSame('sg', $out['tipo']);
        $this->assertSame(
            'src/actividadescentro/lista_actividades_ctr_data',
            $out['url_lista']['path']
        );
        $this->assertSame(
            'tipo!year!periodo!empiezamin!empiezamax',
            $out['url_lista']['campos_form']
        );
    }

    public function test_sf_remapea_tipos(): void
    {
        $_SESSION['session_auth']['sfsv'] = 2;

        $this->assertSame('sfsg', ActivCtrShellData::build(['tipo' => 'sg'])['tipo']);
        $this->assertSame('sfsr', ActivCtrShellData::build(['tipo' => 'sr'])['tipo']);
        $this->assertSame('sfnagd', ActivCtrShellData::build(['tipo' => 'nagd'])['tipo']);
    }
}
