<?php

namespace src\dbextern\application;

use src\personas\domain\Trasladar;
use src\personas\domain\value_objects\SituacionCode;
use src\shared\config\ConfigGlobal;
use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;

class TrasladarPersonaUseCase
{
    private DelegacionRepositoryInterface $delegacionRepository;

    public function __construct(DelegacionRepositoryInterface $delegacionRepository)
    {
        $this->delegacionRepository = $delegacionRepository;
    }

    /**
     * Trasladar persona desde otra DL a la DL actual.
     *
     * @return array{success: bool, mensaje?: string}
     */
    public function trasladar(string $id_nom_orbix, string $tipo_persona, string $dl): array
    {
        $oTrasladar = new Trasladar();
        $oTrasladar->setId_nom($id_nom_orbix);

        $aEsquemas = $oTrasladar->getEsquemas($id_nom_orbix, $tipo_persona);
        $esq_org = '';
        foreach ($aEsquemas as $esquema) {
            if ($esquema['situacion'] === 'A') {
                $esq_org = $esquema['schemaname'];
            }
        }
        $mi_esquema = ConfigGlobal::mi_region_dl();
        $oHoy = new DateTimeLocal();

        $oTrasladar->setDl_persona($dl);
        $oTrasladar->setReg_dl_org($esq_org);
        $oTrasladar->setReg_dl_dst($mi_esquema);
        $oTrasladar->setF_traslado($oHoy);
        $oTrasladar->setSituacionVo(SituacionCode::fromNullableString('L'));

        return $oTrasladar->trasladar();
    }

    /**
     * Trasladar persona dentro de la misma región a otra DL.
     *
     * @return array{success: bool, mensaje?: string}
     */
    public function trasladarA(string $id_nom_orbix, string $tipo_persona, string $dl): array
    {
        $oTrasladar = new Trasladar();
        $oTrasladar->setId_nom($id_nom_orbix);

        $mi_dele = ConfigGlobal::mi_delef();
        $mi_esquema = ConfigGlobal::mi_region_dl();
        $oHoy = new DateTimeLocal();
        $sfsv_txt = (ConfigGlobal::mi_sfsv() == 1) ? 'v' : 'f';

        if (str_starts_with($dl, 'dl')) {
            $dl_dst = $dl;
        } else {
            $dl_dst = 'cr' . $dl;
        }
        $cDl = $this->delegacionRepository->getDelegaciones(['dl' => $dl_dst, 'active' => 't']);
        $region_dst = $cDl[0]->getRegionVo()->value();

        if ($region_dst !== ConfigGlobal::mi_region()) {
            return [
                'success' => false,
                'mensaje' => _("Este traslado debe hacerse desde el dossier de traslados")
                    . "\n" . _("Para asegurar que se llena correctamente el campo situación"),
            ];
        }

        $esq_dst = $region_dst . '-' . $dl_dst . $sfsv_txt;

        $oTrasladar->setDl_persona($mi_dele);
        $oTrasladar->setReg_dl_org($mi_esquema);
        $oTrasladar->setReg_dl_dst($esq_dst);
        $oTrasladar->setF_traslado($oHoy);
        $oTrasladar->setSituacionVo(SituacionCode::fromNullableString('L'));

        return $oTrasladar->trasladar();
    }
}
