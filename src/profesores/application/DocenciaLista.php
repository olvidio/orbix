<?php

namespace src\profesores\application;

use src\shared\config\ConfigGlobal;
use src\actividadestudios\domain\value_objects\TipoActividadAsignatura;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\profesores\domain\contracts\ProfesorDocenciaStgrRepositoryInterface;
use src\profesores\domain\services\ProfesorStgrService;

final class DocenciaLista
{
    public function __construct(
        private AsignaturaRepositoryInterface $asignaturaRepository,
        private ProfesorStgrService $profesorStgrService,
        private ProfesorDocenciaStgrRepositoryInterface $profesorDocenciaStgrRepository,
    ) {
    }

    /**
     * @return array{id_tabla: string, a_cabeceras: array<int, string>, a_valores: array<int, array<int, mixed>>}
     */
    public function getTablaData(): array
    {
        $cAsignaturas = $this->asignaturaRepository->getAsignaturas();
        $a_asignaturas = [];
        foreach ($cAsignaturas as $oAsignatura) {
            $a_asignaturas[$oAsignatura->getId_asignatura()] = $oAsignatura->getNombre_corto();
        }

        $a_cabeceras = [];
        if (ConfigGlobal::mi_ambito() === 'rstgr') {
            $a_cabeceras[1] = _("dl");
        }
        $a_cabeceras[2] = _("apellidos, nombre");
        $a_cabeceras[3] = _("incio curso");
        $a_cabeceras[4] = _("asignatura");
        $a_cabeceras[5] = _("modo");
        $a_cabeceras[6] = _("acta");

        $a_nomProfesor = $this->profesorStgrService->getArrayProfesoresConDl();

        $a_tipos_docendia = TipoActividadAsignatura::getTiposActividad();
        $a_valores = [];
        $p = 0;
        foreach ($a_nomProfesor as $id_nom => $aClave) {
            $ap_nom = $aClave['ap_nom'];
            $dl = $aClave['dl'];
            $cProfesorDocenciaStgr = $this->profesorDocenciaStgrRepository->getProfesorDocenciasStgr(['id_nom' => $id_nom]);
            foreach ($cProfesorDocenciaStgr as $oProfesorDocenciaStgr) {
                $p++;
                $id_asignatura = $oProfesorDocenciaStgr->getIdAsignaturaVo()->value();
                $nom_asignatura = empty($a_asignaturas[$id_asignatura]) ? '?' : $a_asignaturas[$id_asignatura];

                $tipo = $oProfesorDocenciaStgr->getTipoVo()?->value();
                $modo = empty($tipo) ? '' : $a_tipos_docendia[$tipo];

                if (ConfigGlobal::mi_ambito() === 'rstgr') {
                    $a_valores[$p][1] = $dl;
                }
                $a_valores[$p][2] = $ap_nom;
                $a_valores[$p][3] = $oProfesorDocenciaStgr->getCurso_inicio();
                $a_valores[$p][4] = $nom_asignatura;
                $a_valores[$p][5] = $modo;
                $a_valores[$p][6] = $oProfesorDocenciaStgr->getActaVo()?->value();
            }
        }

        return [
            'id_tabla' => 'tabla_docencia',
            'a_cabeceras' => $a_cabeceras,
            'a_valores' => $a_valores,
        ];
    }
}
