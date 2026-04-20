<?php

namespace Tests\unit\actividades\application;

use src\actividades\application\ListaSrCsvQueDatos;
use src\usuarios\domain\contracts\PreferenciaRepositoryInterface;
use src\usuarios\domain\entity\Preferencia;
use Tests\myTest;

final class ListaSrCsvQueDatosTest extends myTest
{
    private mixed $previousContainer;

    public function setUp(): void
    {
        parent::setUp();
        $this->previousContainer = $GLOBALS['container'] ?? null;
    }

    public function tearDown(): void
    {
        if ($this->previousContainer === null) {
            unset($GLOBALS['container']);
        } else {
            $GLOBALS['container'] = $this->previousContainer;
        }
        parent::tearDown();
    }

    public function test_sin_preferencia_usa_defaults(): void
    {
        $prefRepo = $this->createMock(PreferenciaRepositoryInterface::class);
        $prefRepo->method('findById')->willReturn(null);

        $GLOBALS['container'] = new class($prefRepo) {
            public function __construct(private readonly object $prefRepo) {}

            public function get(string $id): object
            {
                return $this->prefRepo;
            }
        };

        $out = (new ListaSrCsvQueDatos())->ejecutar();

        $this->assertSame('curso_ca', $out['periodo']);
        $this->assertSame('', $out['sel_ubis']);
        $this->assertNotSame('', $out['chk_status_1']);
        $this->assertNotSame('', $out['chk_status_2']);
    }

    public function test_con_preferencia_json_parsea_campos(): void
    {
        $json = json_encode([
            'status' => json_encode([1]),
            'periodo' => 'actual',
            'tipo_activ' => json_encode([3]),
            'ubis_compartidos' => json_encode([7, 8]),
        ]);

        $pref = $this->createMock(Preferencia::class);
        $pref->method('getPreferencia')->willReturn($json);

        $prefRepo = $this->createMock(PreferenciaRepositoryInterface::class);
        $prefRepo->method('findById')->willReturn($pref);

        $GLOBALS['container'] = new class($prefRepo) {
            public function __construct(private readonly object $prefRepo) {}

            public function get(string $id): object
            {
                return $this->prefRepo;
            }
        };

        $out = (new ListaSrCsvQueDatos())->ejecutar();

        $this->assertSame('actual', $out['periodo']);
        $this->assertSame('7,8', $out['sel_ubis']);
        $this->assertNotSame('', $out['chk_status_1']);
        $this->assertSame('', $out['chk_status_2']);
        $this->assertSame('', $out['chk_activ_crt']);
        $this->assertNotSame('', $out['chk_activ_cv']);
    }
}
