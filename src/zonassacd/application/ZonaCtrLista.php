<?php

declare(strict_types=1);

namespace src\zonassacd\application;

use src\permisos\domain\XPermisos;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroEllasRepositoryInterface;
use src\ubis\domain\entity\CentroDl;
use src\ubis\domain\entity\CentroEllas;
use src\zonassacd\application\services\CentrosDeZona;
use src\zonassacd\domain\contracts\ZonaRepositoryInterface;

final class ZonaCtrLista
{
    public function __construct(
        private CentroDlRepositoryInterface $centroDlRepository,
        private CentroEllasRepositoryInterface $centroEllasRepository,
        private ZonaRepositoryInterface $zonaRepository,
        private CentrosDeZona $centrosDeZona,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function execute(string $id_zona): array
    {
        $nombreZona = '';
        switch ($id_zona) {
            case 'no':
                $cCentros = $this->activosSinZona(
                    $this->centroDlRepository->getCentros(['active' => 't', '_ordre' => 'nombre_ubi']),
                );
                break;
            case 'no_sf':
                $cCentros = $this->activosSinZona(
                    $this->centroEllasRepository->getCentros(['active' => 't', '_ordre' => 'nombre_ubi']),
                );
                break;
            default:
                $oZona = $this->zonaRepository->findById((int) $id_zona);
                $nombreZona = $oZona?->getNombre_zona() ?? '';
                $cCentros = $this->activosDeZona((int) $id_zona);
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
            $a_valores[$i]['sel'] = $id_ubi;
            $a_valores[$i][1] = $oCentro->getNombre_ubi();
            $a_valores[$i][2] = $nombreZona;
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

    /**
     * @param list<CentroDl|CentroEllas> $cCentros
     * @return list<CentroDl|CentroEllas>
     */
    private function activosSinZona(array $cCentros): array
    {
        $ids = [];
        foreach ($cCentros as $oCentro) {
            $ids[] = (int) $oCentro->getId_ubi();
        }
        $sin = array_flip($this->centrosDeZona->idUbisSinZonaEntre($ids));
        $filtrados = [];
        foreach ($cCentros as $oCentro) {
            if (isset($sin[(int) $oCentro->getId_ubi()])) {
                $filtrados[] = $oCentro;
            }
        }

        return $filtrados;
    }

    /**
     * @return list<CentroDl|CentroEllas>
     */
    private function activosDeZona(int $id_zona): array
    {
        $filtro = CentrosDeZona::whereActivosIn($this->centrosDeZona->idUbisDeZona($id_zona));
        if ($filtro === null) {
            return [];
        }
        [$aWhere, $aOperador] = $filtro;

        return array_merge(
            $this->centroDlRepository->getCentros($aWhere, $aOperador),
            $this->centroEllasRepository->getCentros($aWhere, $aOperador),
        );
    }
}
