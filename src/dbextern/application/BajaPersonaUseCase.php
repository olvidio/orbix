<?php

namespace src\dbextern\application;

use src\personas\domain\Trasladar;
use src\personas\domain\value_objects\SituacionCode;
use src\shared\config\ConfigGlobal;
use src\shared\domain\value_objects\DateTimeLocal;

class BajaPersonaUseCase
{
    /**
     * Da de baja a una persona (fallecido o traslado a otra región).
     *
     * @return string Error text (empty on success)
     */
    public function __invoke(string $id_nom_orbix, string $tipo_persona, string $dl): string
    {
        $oTrasladar = new Trasladar();
        $oTrasladar->setId_nom($id_nom_orbix);

        $mi_dele = ConfigGlobal::mi_delef();
        $mi_esquema = ConfigGlobal::mi_region_dl();
        $oHoy = new DateTimeLocal();
        $sfsv_txt = (ConfigGlobal::mi_sfsv() == 1) ? 'v' : 'f';
        $esq_dst = "H-" . $dl . $sfsv_txt;

        $oTrasladar->setDl_persona($mi_dele);
        $oTrasladar->setReg_dl_org($mi_esquema);
        $oTrasladar->setReg_dl_dst($esq_dst);
        $oTrasladar->setF_traslado($oHoy);
        $oTrasladar->setSituacionVo(SituacionCode::fromNullableString('B'));

        if ($oTrasladar->cambiarFichaPersona() === false) {
            return _("OJO: Debería cambiar el campo situación. No se ha hecho ningún cambio.");
        }

        return '';
    }
}
