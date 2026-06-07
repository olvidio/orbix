<?php

namespace src\dbextern\application;

use src\personas\domain\Trasladar;
use src\personas\domain\value_objects\SituacionCode;
use src\shared\config\ConfigGlobal;
use src\shared\domain\value_objects\DateTimeLocal;

class BajaPersonaUseCase
{
    public function __construct(
        private Trasladar $trasladar,
    ) {
    }

    /**
     * @return string Error text (empty on success)
     */
    public function __invoke(int $id_nom_orbix, string $tipo_persona, string $dl): string
    {
        $this->trasladar->setId_nom($id_nom_orbix);

        $mi_dele = ConfigGlobal::mi_delef();
        $mi_esquema = ConfigGlobal::mi_region_dl();
        $oHoy = new DateTimeLocal();
        $sfsv_txt = (ConfigGlobal::mi_sfsv() === 1) ? 'v' : 'f';
        $esq_dst = "H-" . $dl . $sfsv_txt;

        $this->trasladar->setDl_persona($mi_dele);
        $this->trasladar->setReg_dl_org($mi_esquema);
        $this->trasladar->setReg_dl_dst($esq_dst);
        $this->trasladar->setF_traslado($oHoy);
        $situacion = SituacionCode::fromNullableString('B');
        if ($situacion !== null) {
            $this->trasladar->setSituacionVo($situacion);
        }

        if ($this->trasladar->cambiarFichaPersona() === false) {
            return _("OJO: Debería cambiar el campo situación. No se ha hecho ningún cambio.");
        }

        return '';
    }
}
