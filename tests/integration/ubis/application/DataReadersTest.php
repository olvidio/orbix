<?php

namespace Tests\integration\ubis\application;

use src\ubis\application\CentrosSListaData;
use src\ubis\application\DelegacionQueData;
use src\ubis\application\DireccionesQueData;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use Tests\factories\ubis\CentroDlFactory;
use Tests\myTest;

/**
 * Tests de integración "smoke" para clases de solo lectura de ubis/application.
 *
 * Sólo comprueban que se ejecutan contra la BD y devuelven la estructura esperada.
 */
class DataReadersTest extends myTest
{
    public function test_CentrosSListaData_devuelve_estructura_correcta(): void
    {
        $result = CentrosSListaData::execute();

        $this->assertArrayHasKey('a_cabeceras', $result);
        $this->assertArrayHasKey('a_valores', $result);
        $this->assertArrayHasKey('num_total_s', $result);
        $this->assertIsArray($result['a_cabeceras']);
        $this->assertIsArray($result['a_valores']);
        $this->assertIsInt($result['num_total_s']);
        $this->assertCount(2, $result['a_cabeceras']);
        $this->assertGreaterThanOrEqual(0, $result['num_total_s']);
    }

    public function test_DelegacionQueData_devuelve_opciones(): void
    {
        $result = DelegacionQueData::execute();

        $this->assertArrayHasKey('opciones_dl_destino', $result);
        $this->assertIsArray($result['opciones_dl_destino']);
    }

    public function test_DireccionesQueData_sobre_centroDl_real(): void
    {
        $repo = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
        $factory = new CentroDlFactory();
        $oCentro = $factory->createSimple();
        $id = $oCentro->getId_ubi();
        $oCentro->setTipo_ubi('ctrdl');
        $repo->Guardar($oCentro);

        try {
            $result = DireccionesQueData::execute($id);
            $this->assertArrayHasKey('tipo_ubi', $result);
            $this->assertArrayHasKey('titulo', $result);
            $this->assertIsString($result['titulo']);
        } finally {
            $oFinal = $repo->findById($id);
            if ($oFinal !== null) {
                $repo->Eliminar($oFinal);
            }
        }
    }
}
