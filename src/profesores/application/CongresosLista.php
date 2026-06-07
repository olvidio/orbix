<?php

namespace src\profesores\application;

use src\shared\config\ConfigGlobal;
use src\profesores\domain\contracts\ProfesorCongresoRepositoryInterface;
use src\profesores\domain\services\ProfesorStgrService;
use src\profesores\domain\value_objects\CongresoTipo;

final class CongresosLista
{
    public function __construct(
        private ProfesorStgrService $profesorStgrService,
        private ProfesorCongresoRepositoryInterface $profesorCongresoRepository,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function getTablaData(): array
    {
        $a_cabeceras = [];
        if (ConfigGlobal::mi_ambito() === 'rstgr') {
            $a_cabeceras[1] = _("dl");
        }
        $a_cabeceras[2] = _("apellidos, nombre");
        $a_cabeceras[3] = _("tipo");
        $a_cabeceras[4] = _("lugar");
        $a_cabeceras[5] = _("inicio");
        $a_cabeceras[6] = _("fin");
        $a_cabeceras[7] = _("organiza");

        $a_nomProfesor = $this->profesorStgrService->getArrayProfesoresConDl();

        $a_tiposCong = CongresoTipo::getArrayTiposCongreso();
        $a_valores = [];
        $p = 0;
        foreach ($a_nomProfesor as $id_nom => $aClave) {
            $ap_nom = $aClave['ap_nom'];
            $dl = $aClave['dl'];
            $cProfesorCongreso = $this->profesorCongresoRepository->getProfesorCongresos(['id_nom' => $id_nom]);
            foreach ($cProfesorCongreso as $oProfesorCongreso) {
                $p++;
                $tipo = empty($a_tiposCong[$oProfesorCongreso->getTipo()]) ? '' : $a_tiposCong[$oProfesorCongreso->getTipo()];
                $lugar = $oProfesorCongreso->getLugar();
                $inicio = $oProfesorCongreso->getF_ini()?->getFromLocal();
                $fin = $oProfesorCongreso->getF_fin()?->getFromLocal();
                $organiza = $oProfesorCongreso->getOrganiza();

                if (ConfigGlobal::mi_ambito() === 'rstgr') {
                    $a_valores[$p][1] = $dl;
                }
                $a_valores[$p][2] = $ap_nom;
                $a_valores[$p][3] = $tipo;
                $a_valores[$p][4] = $lugar;
                $a_valores[$p][5] = $inicio;
                $a_valores[$p][6] = $fin;
                $a_valores[$p][7] = $organiza;
            }
        }

        return [
            'id_tabla' => 'tabla_congreso',
            'a_cabeceras' => $a_cabeceras,
            'a_valores' => $a_valores,
        ];
    }
}
