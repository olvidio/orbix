<?php

namespace src\zonassacd\application;

use src\permisos\domain\XPermisos;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroEllasRepositoryInterface;
use src\zonassacd\domain\contracts\ZonaRepositoryInterface;

final class ZonaCtrLista
{
    public function __construct(
        private CentroDlRepositoryInterface $centroDlRepository,
        private CentroEllasRepositoryInterface $centroEllasRepository,
        private ZonaRepositoryInterface $zonaRepository,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function execute(string $id_zona): array
    {
        $aWhere = [];
        $aOperador = [];
        $cCentros = [];
        switch ($id_zona) {
            case 'no':
                $aWhere['active'] = 't';
                $aWhere['id_zona'] = '';
                $aOperador['id_zona'] = 'IS NULL';
                $aWhere['_ordre'] = 'nombre_ubi';
                $cCentros = $this->centroDlRepository->getCentros($aWhere, $aOperador);
                break;
            case 'no_sf':
                $aWhere['active'] = 't';
                $aWhere['id_zona'] = '';
                $aOperador['id_zona'] = 'IS NULL';
                $aWhere['_ordre'] = 'nombre_ubi';
                $cCentros = $this->centroEllasRepository->getCentros($aWhere, $aOperador);
                break;
            default:
                $aWhere['active'] = 't';
                $aWhere['id_zona'] = $id_zona;
                $aWhere['_ordre'] = 'nombre_ubi';
                $cCentrosDl = $this->centroDlRepository->getCentros($aWhere);
                $cCentrosSf = $this->centroEllasRepository->getCentros($aWhere);
                $cCentros = array_merge($cCentrosDl, $cCentrosSf);
        }

        $oPerm = $_SESSION['oPerm'] ?? null;
        $tienePermDes = $oPerm instanceof XPermisos
            && ($oPerm->have_perm_oficina('des') || $oPerm->have_perm_oficina('vcsd'));

        $a_valores = [];
        $i = 0;
        foreach ($cCentros as $oCentro) {
            $i++;
            $id_ubi = (string) $oCentro->getId_ubi();
            if ($id_ubi[0] === '2' && !$tienePermDes) {
                continue;
            }
            if ($id_ubi[0] === '2') {
                $a_valores[$i]['clase'] = 'tono2';
            }
            $idZonaCentro = $oCentro->getId_zona();
            $oZona = $idZonaCentro !== null ? $this->zonaRepository->findById($idZonaCentro) : null;
            $a_valores[$i]['sel'] = $id_ubi;
            $a_valores[$i][1] = $oCentro->getNombre_ubi();
            $a_valores[$i][2] = $oZona?->getNombre_zona() ?? '';
        }

        return [
            'tipo' => 'tabla',
            'id_tabla' => 'zona_ctr_ajax',
            'a_cabeceras' => [_("centro"), _("zona")],
            'a_botones' => [],
            'con_sel' => $tienePermDes,
            'a_valores' => $a_valores,
            'error' => '',
        ];
    }
}
