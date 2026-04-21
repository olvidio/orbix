<?php

namespace src\misas\application;

use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\misas\domain\contracts\EncargoCtrRepositoryInterface;
use src\ubis\domain\contracts\CentroEllasRepositoryInterface;
use src\ubis\domain\contracts\CentroEllosRepositoryInterface;
use src\zonassacd\domain\contracts\ZonaRepositoryInterface;

class VerEncargosCentrosData
{
    /**
     * Devuelve los datos del SlickGrid de `EncargoCtr` (encargos visibles para
     * cada centro de una zona) + los desplegables estaticos del modal de
     * edicion (zonas posibles para filtrar encargos, centros de la zona).
     *
     * El desplegable dinamico de encargos (que cambia al seleccionar zona en
     * el modal) no se incluye aqui: el frontend lo pide por separado a
     * `DesplegableEncargosData` cuando el usuario lo necesita.
     */
    public static function getData(int $id_zona): array
    {
        $columns = [
            ['id' => 'centro', 'name' => 'Centro', 'field' => 'centro', 'width' => 120, 'cssClass' => 'cell-title'],
            ['id' => 'encargo', 'name' => 'Encargo', 'field' => 'encargo', 'width' => 200, 'cssClass' => 'cell-title'],
        ];

        $CentroEllosRepository = $GLOBALS['container']->get(CentroEllosRepositoryInterface::class);
        $CentroEllasRepository = $GLOBALS['container']->get(CentroEllasRepositoryInterface::class);
        $EncargoCtrRepository = $GLOBALS['container']->get(EncargoCtrRepositoryInterface::class);
        $EncargoRepository = $GLOBALS['container']->get(EncargoRepositoryInterface::class);

        $rows = [];
        $centros_zona = [];
        if (!empty($id_zona)) {
            $aWhere = [
                'active' => 't',
                'id_zona' => $id_zona,
                '_ordre' => 'nombre_ubi',
            ];
            $cCentros = array_merge(
                $CentroEllosRepository->getCentros($aWhere),
                $CentroEllasRepository->getCentros($aWhere),
            );

            foreach ($cCentros as $oCentro) {
                $id_ubi = $oCentro->getId_ubi();
                $nombre_ubi = $oCentro->getNombre_ubi();
                $centros_zona[$id_ubi] = $nombre_ubi;

                $cEncargosCtr = $EncargoCtrRepository->getEncargosCentro($id_ubi);
                foreach ($cEncargosCtr as $oEncargoCtr) {
                    $id_enc = $oEncargoCtr->getId_enc();
                    $oEncargo = $EncargoRepository->findById($id_enc);
                    if ($oEncargo === null) {
                        $desc_enc = '';
                    } else {
                        $desc_enc = $oEncargo->getDescEncVo()->value();
                    }

                    $rows[] = [
                        'id_item' => $oEncargoCtr->getUuidItemVo()->value(),
                        'id_encargo' => (int)$id_enc,
                        'encargo' => $desc_enc,
                        'id_centro' => (int)$id_ubi,
                        'centro' => $nombre_ubi,
                    ];
                }
            }
        }

        // Desplegable de zonas (sin filtro por rol: sirve para elegir de que
        // zona tomar los encargos al editar, no para filtrar el grid).
        $ZonaRepository = $GLOBALS['container']->get(ZonaRepositoryInterface::class);
        $a_opciones_zona = $ZonaRepository->getArrayZonas();

        return [
            'id_zona' => $id_zona,
            'columns' => $columns,
            'rows' => $rows,
            'a_opciones_zona' => $a_opciones_zona,
            'a_centros_zona' => $centros_zona,
        ];
    }
}
