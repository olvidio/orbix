<?php

namespace Tests\integration\ubis\application;

use src\ubis\application\UbisTablaData;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use Tests\factories\ubis\CentroDlFactory;
use Tests\myTest;

/**
 * `UbisTablaData` contra la BD: búsqueda por nombre y paso del filtro serializado a la página siguiente.
 */
class UbisTablaDataTest extends myTest
{
    public function test_busqueda_por_nombre_y_vuelta_con_filtro_serializado(): void
    {
        $repo = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
        $oCentro = (new CentroDlFactory())->createSimple();
        $oCentro->setTipo_ubi('ctrdl');
        $oCentro->setActive(true);
        $oCentro->setSv(true);
        $oCentro->setSf(true);
        $repo->Guardar($oCentro);
        $nombre = (string) $oCentro->getNombre_ubi();

        try {
            $useCase = $GLOBALS['container']->get(UbisTablaData::class);

            $primera = $useCase->execute(['tipo' => 'ctr', 'loc' => 'dl', 'nombre_ubi' => $nombre]);
            $this->assertArrayNotHasKey('error', $primera);
            $this->assertNotEmpty($primera['a_valores']);
            $this->assertIsString($primera['go_back']['sWhere']);

            $segunda = $useCase->execute([
                'tipo' => 'ctr',
                'loc' => 'dl',
                'sWhere' => $primera['go_back']['sWhere'],
                'sWhereD' => $primera['go_back']['sWhereD'],
                'obj_pau' => $primera['go_back']['obj_pau'],
            ]);
            $this->assertArrayNotHasKey('error', $segunda);
            $this->assertCount(count($primera['a_valores']), $segunda['a_valores']);
        } finally {
            $repo->Eliminar($oCentro);
        }
    }

    public function test_sin_criterio_pide_algun_criterio(): void
    {
        $out = $GLOBALS['container']->get(UbisTablaData::class)->execute(['tipo' => 'ctr', 'loc' => 'dl']);

        $this->assertArrayHasKey('error', $out);
    }
}
