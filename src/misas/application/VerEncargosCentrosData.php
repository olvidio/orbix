<?php

namespace src\misas\application;

use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\misas\domain\contracts\EncargoCtrRepositoryInterface;
use src\ubis\domain\contracts\CentroEllasRepositoryInterface;
use src\ubis\domain\contracts\CentroEllosRepositoryInterface;
use src\zonassacd\domain\contracts\ZonaRepositoryInterface;

class VerEncargosCentrosData
{

    public function __construct(
        private readonly CentroEllosRepositoryInterface $centroEllosRepository,
        private readonly CentroEllasRepositoryInterface $centroEllasRepository,
        private readonly EncargoCtrRepositoryInterface $encargoCtrRepository,
        private readonly EncargoRepositoryInterface $encargoRepository,
        private readonly ZonaRepositoryInterface $zonaRepository,
    ) {
    }
    /**
     * Devuelve los datos del SlickGrid de `EncargoCtr` (encargos visibles para
     * cada centro de una zona) + los desplegables estaticos del modal de
     * edicion (zonas posibles para filtrar encargos, centros de la zona).
     *
     * El desplegable dinamico de encargos (que cambia al seleccionar zona en
     * el modal) no se incluye aqui: el frontend lo pide por separado a
     * `DesplegableEncargosData` cuando el usuario lo necesita.
     * @return array<string, mixed>
     */
    public function getData(int $id_zona): array
    {
        $columns = [
            ['id' => 'centro', 'name' => 'Centro', 'field' => 'centro', 'width' => 120, 'cssClass' => 'cell-title'],
            ['id' => 'encargo', 'name' => 'Encargo', 'field' => 'encargo', 'width' => 200, 'cssClass' => 'cell-title'],
        ];

        $rows = [];
        $centros_zona = [];
        if (!empty($id_zona)) {
            $aWhere = [
                'active' => 't',
                'id_zona' => $id_zona,
                '_ordre' => 'nombre_ubi',
            ];
            $cCentros = array_merge(
                $this->centroEllosRepository->getCentros($aWhere),
                $this->centroEllasRepository->getCentros($aWhere),
            );

            foreach ($cCentros as $oCentro) {
                $id_ubi = $oCentro->getId_ubi();
                $nombre_ubi = $oCentro->getNombre_ubi();
                $centros_zona[$id_ubi] = $nombre_ubi;

                $cEncargosCtr = $this->encargoCtrRepository->getEncargosCentro($id_ubi);
                foreach ($cEncargosCtr as $oEncargoCtr) {
                    $id_enc = $oEncargoCtr->getId_enc();
                    if ($id_enc === null) {
                        continue;
                    }
                    $oEncargo = $this->encargoRepository->findById($id_enc);
                    if ($oEncargo === null) {
                        $desc_enc = '';
                    } else {
                        $descEncVo = $oEncargo->getDescEncVo();
                        $desc_enc = $descEncVo !== null ? $descEncVo->value() : '';
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
        $a_opciones_zona = $this->zonaRepository->getArrayZonas();

        return [
            'id_zona' => $id_zona,
            'columns' => $columns,
            'rows' => $rows,
            'a_opciones_zona' => $a_opciones_zona,
            'a_centros_zona' => $centros_zona,
        ];
    }
}
