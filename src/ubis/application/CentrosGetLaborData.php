<?php

namespace src\ubis\application;

use src\shared\config\ConfigGlobal;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\CuadrosLaborBits;

class CentrosGetLaborData
{
    public function __construct(
        private CentroDlRepositoryInterface $centroDlRepository,
    ) {
    }
    /**
     * @return array<string, mixed>
     */
    public function execute(): array
    {
        $oGesCentrosDl = $this->centroDlRepository;
        $aWhere = ['active' => 't', '_ordre' => 'nombre_ubi'];
        $cCentrosDl = $oGesCentrosDl->getCentros($aWhere);

        $a_valores = [];
        foreach ($cCentrosDl as $oCentro) {
            $id_ubi = $oCentro->getId_ubi();
            $nombre_ubi = $oCentro->getNombre_ubi();
            $tipo_ctr = $oCentro->getTipo_ctr();
            $tipo_labor = $oCentro->getTipo_labor();

            $a_valores[] = [
                'id_ubi' => $id_ubi,
                'nombre_ubi' => $nombre_ubi,
                'tipo_ctr' => $tipo_ctr,
                'tipo_labor' => (int)$tipo_labor,
            ];
        }

        $a_cabeceras = [
            ['name' => ucfirst(_("centro")), 'width' => 100, 'formatter' => 'clickFormatter'],
            ucfirst(_("tipo de centro")),
            ['name' => ucfirst(_("tipo de labor")), 'width' => 200, 'formatter' => 'clickFormatter2'],
        ];

        return [
            'a_cabeceras' => $a_cabeceras,
            'a_valores' => $a_valores,
            'tipo_labor_bit_map' => CuadrosLaborBits::labeledMap(ConfigGlobal::mi_sfsv()),
        ];
    }
}

