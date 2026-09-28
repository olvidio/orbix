<?php

declare(strict_types=1);

namespace Tests\unit\casas\application;

use PHPUnit\Framework\TestCase;
use src\casas\application\GrupoCasaListaData;
use src\casas\domain\contracts\GrupoCasaRepositoryInterface;
use src\casas\domain\entity\GrupoCasa;
use src\shared\security\HashB;
use src\ubis\domain\contracts\CasaDlRepositoryInterface;
use src\ubis\domain\entity\Casa;

final class GrupoCasaListaDataTest extends TestCase
{
    private mixed $previousOPerm = null;
    private bool $hadOPerm = false;

    protected function setUp(): void
    {
        parent::setUp();
        $this->hadOPerm = array_key_exists('oPerm', $_SESSION ?? []);
        $this->previousOPerm = $_SESSION['oPerm'] ?? null;
        unset($_SESSION['oPerm']);
    }

    protected function tearDown(): void
    {
        if ($this->hadOPerm) {
            $_SESSION['oPerm'] = $this->previousOPerm;
        } else {
            unset($_SESSION['oPerm']);
        }
        parent::tearDown();
    }

    public function test_fila_lleva_ctx_eliminar_valido_por_id_item(): void
    {
        $grupo = $this->createMock(GrupoCasa::class);
        $grupo->method('getId_item')->willReturn(77);
        $grupo->method('getId_ubi_padre')->willReturn(10);
        $grupo->method('getId_ubi_hijo')->willReturn(20);

        $repoGrupo = $this->createMock(GrupoCasaRepositoryInterface::class);
        $repoGrupo->method('getGrupoCasas')->willReturn([$grupo]);

        $casa = $this->createMock(Casa::class);
        $casa->method('getNombre_ubi')->willReturn('Casa X');

        $repoCasa = $this->createMock(CasaDlRepositoryInterface::class);
        $repoCasa->method('findById')->willReturn($casa);

        $result = (new GrupoCasaListaData($repoGrupo, $repoCasa))->execute();

        $fila = $result['a_valores'][1];
        $script = $fila[4]['script'];

        $this->assertStringStartsWith('fnjs_eliminar(', $script);

        preg_match('/^fnjs_eliminar\((.*)\)$/', $script, $matches);
        $ctxEliminarJson = $matches[1];
        $ctxEliminar = json_decode($ctxEliminarJson, true);

        $this->assertIsString($ctxEliminar);
        $this->assertSame(['id_item' => '77'], HashB::open($ctxEliminar, 'grupo_eliminar'));
    }
}
