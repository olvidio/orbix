<?php

namespace src\dbextern\application;

use src\personas\domain\Trasladar;
use src\personas\domain\value_objects\SituacionCode;
use src\shared\config\ConfigGlobal;
use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;

class TrasladarPersonaUseCase
{
    public function __construct(
        private DelegacionRepositoryInterface $delegacionRepository,
        private Trasladar $trasladar,
    ) {
    }

    /**
     * @return array{success: bool, mensaje?: string}
     */
    public function trasladar(int $id_nom_orbix, string $tipo_persona, string $dl): array
    {
        $this->trasladar->setId_nom($id_nom_orbix);

        $aEsquemas = $this->trasladar->getEsquemas($id_nom_orbix, $tipo_persona);
        $esq_org = '';
        foreach ($aEsquemas as $esquema) {
            if (($esquema['situacion'] ?? null) === 'A') {
                $schemaName = $esquema['schemaname'] ?? '';
                $esq_org = is_string($schemaName) ? $schemaName : '';
            }
        }
        $mi_esquema = ConfigGlobal::mi_region_dl();
        $oHoy = new DateTimeLocal();

        $this->trasladar->setDl_persona($dl);
        $this->trasladar->setReg_dl_org($esq_org !== '' ? $esq_org : $mi_esquema);
        $this->trasladar->setReg_dl_dst($mi_esquema);
        $this->trasladar->setF_traslado($oHoy);
        $situacion = SituacionCode::fromNullableString('L');
        if ($situacion !== null) {
            $this->trasladar->setSituacionVo($situacion);
        }

        return $this->trasladar->trasladar();
    }

    /**
     * @return array{success: bool, mensaje?: string}
     */
    public function trasladarA(int $id_nom_orbix, string $tipo_persona, string $dl): array
    {
        $this->trasladar->setId_nom($id_nom_orbix);

        $mi_dele = ConfigGlobal::mi_delef();
        $mi_esquema = ConfigGlobal::mi_region_dl();
        $oHoy = new DateTimeLocal();
        $sfsv_txt = (ConfigGlobal::mi_sfsv() === 1) ? 'v' : 'f';

        $dl_dst = str_starts_with($dl, 'dl') ? $dl : 'cr' . $dl;
        $cDl = $this->delegacionRepository->getDelegaciones(['dl' => $dl_dst, 'active' => 't']);
        if ($cDl === []) {
            return [
                'success' => false,
                'mensaje' => _("No se encontró la delegación destino"),
            ];
        }
        $region_dst = $cDl[0]->getRegionVo()->value();

        if ($region_dst !== ConfigGlobal::mi_region()) {
            return [
                'success' => false,
                'mensaje' => _("Este traslado debe hacerse desde el dossier de traslados")
                    . "\n" . _("Para asegurar que se llena correctamente el campo situación"),
            ];
        }

        $esq_dst = $region_dst . '-' . $dl_dst . $sfsv_txt;

        $this->trasladar->setDl_persona($mi_dele);
        $this->trasladar->setReg_dl_org($mi_esquema);
        $this->trasladar->setReg_dl_dst($esq_dst);
        $this->trasladar->setF_traslado($oHoy);
        $situacion = SituacionCode::fromNullableString('L');
        if ($situacion !== null) {
            $this->trasladar->setSituacionVo($situacion);
        }

        return $this->trasladar->trasladar();
    }
}
