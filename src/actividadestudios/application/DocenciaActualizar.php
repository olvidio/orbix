<?php

namespace src\actividadestudios\application;

use src\configuracion\domain\value_objects\ConfigSnapshot;
use src\shared\config\ConfigGlobal;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\value_objects\StatusId;
use src\actividadestudios\domain\contracts\ActividadAsignaturaRepositoryInterface;
use src\actividadestudios\domain\value_objects\TipoActividadAsignatura;
use src\notas\domain\contracts\ActaRepositoryInterface;
use src\profesores\domain\contracts\ProfesorDocenciaStgrRepositoryInterface;
use src\profesores\domain\entity\ProfesorDocenciaStgr;
use frontend\shared\web\Periodo;
use function src\shared\domain\helpers\input_string;

/**
 * Actualiza el dossier `d_docencia_stgr` con la informacion docente derivada
 * de las actividades terminadas del periodo indicado. Para cada actividad
 * terminada recorre sus asignaturas (con profesor asignado) y graba/actualiza
 * la docencia correspondiente (`ProfesorDocenciaStgr`).
 *
 * Sustituye a la rama "continuar" del legacy
 * `apps/actividadestudios/controller/actualizar_docencia.php`.
 */
final class DocenciaActualizar
{
    public function __construct(
        private ActividadAllRepositoryInterface $actividadAllRepository,
        private ProfesorDocenciaStgrRepositoryInterface $profesorDocenciaStgrRepository,
        private ActaRepositoryInterface $actaRepository,
        private ActividadAsignaturaRepositoryInterface $actividadAsignaturaRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     */
    public function execute(array $input): string
    {
        $Qyear = input_string($input, 'year');
        $Qperiodo = input_string($input, 'periodo');
        $Qempiezamin = input_string($input, 'empiezamin');
        $Qempiezamax = input_string($input, 'empiezamax');

        if (empty($Qperiodo)) {
            $Qperiodo = 'curso_ca';
        }

        $oPeriodo = new Periodo();
        $oPeriodo->setAny($Qyear);
        $oPeriodo->setEmpiezaMin($Qempiezamin);
        $oPeriodo->setEmpiezaMax($Qempiezamax);
        $oPeriodo->setPeriodo($Qperiodo);

        $inicioIso = $oPeriodo->getF_ini_iso();
        $finIso = $oPeriodo->getF_fin_iso();
        $txt_curso = $oPeriodo->getTxt_cusro();

        $mi_sfsv = ConfigGlobal::mi_sfsv();
        $id_tipo = '^' . $mi_sfsv . '[123][23]';
        $id_tipo_inv = '^' . $mi_sfsv . '325';

        $aWhere = [
            'f_ini' => "'$inicioIso','$finIso'",
            'status' => StatusId::TERMINADA,
            'id_tipo_activ' => $id_tipo,
        ];
        $aOperador = [
            'f_ini' => 'BETWEEN',
            'id_tipo_activ' => '~',
        ];

        $cActividades = $this->actividadAllRepository->getActividades($aWhere, $aOperador);
        /** @var ConfigSnapshot $oConfig */
        $oConfig = $_SESSION['oConfig'];
        $ini_m = $oConfig->getMesIniStgr();

        foreach ($cActividades as $oActividad) {
            $id_activ = $oActividad->getId_activ();
            $id_tipo_activ = $oActividad->getId_tipo_activ();
            $oFini = $oActividad->getF_ini();
            if ($oFini === null) {
                continue;
            }
            $mes = (int) $oFini->format('m');
            $any = (int) $oFini->format('Y');
            $ini_a = ($mes < $ini_m) ? $any - 1 : $any;

            $cActivAsignaturas = $this->actividadAsignaturaRepository->getActividadAsignaturas(
                ['id_activ' => $id_activ],
                ['id_profesor' => 'IS NOT NULL']
            );

            foreach ($cActivAsignaturas as $oActividadAsignatura) {
                $id_asignatura = $oActividadAsignatura->getId_asignatura();
                $id_profesor = $oActividadAsignatura->getId_profesor();
                if (empty($id_profesor)) {
                    continue;
                }
                $tipo = $oActividadAsignatura->getTipo();
                if (empty($tipo)) {
                    $tipo = TipoActividadAsignatura::TIPO_CA;
                    if (preg_match("/$id_tipo_inv/", (string) $id_tipo_activ)) {
                        $tipo = TipoActividadAsignatura::TIPO_INV;
                    }
                }

                $cActas = $this->actaRepository->getActas(['id_activ' => $id_activ, 'id_asignatura' => $id_asignatura]);
                $acta = '';
                if (count($cActas) > 0) {
                    foreach ($cActas as $oActa) {
                        $acta .= (empty($acta) ? '' : ', ') . $oActa->getActa();
                    }
                }

                $aWhereDocencia = [
                    'id_nom' => $id_profesor,
                    'id_activ' => $id_activ,
                    'id_asignatura' => $id_asignatura,
                ];
                $cProfesorDocencia = $this->profesorDocenciaStgrRepository->getProfesorDocenciasStgr($aWhereDocencia);
                if (count($cProfesorDocencia) > 0) {
                    $oProfesorDocencia = $cProfesorDocencia[0];
                    $oProfesorDocencia->setCurso_inicio($ini_a);
                    $oProfesorDocencia->setTipo($tipo);
                    $oProfesorDocencia->setActa($acta);
                } else {
                    $newId = $this->profesorDocenciaStgrRepository->getNewId();
                    $oProfesorDocencia = new ProfesorDocenciaStgr();
                    $oProfesorDocencia->setId_item($newId);
                    $oProfesorDocencia->setId_nom($id_profesor);
                    $oProfesorDocencia->setId_activ($id_activ);
                    $oProfesorDocencia->setId_asignatura($id_asignatura);
                    $oProfesorDocencia->setCurso_inicio($ini_a);
                    $oProfesorDocencia->setTipo($tipo);
                    $oProfesorDocencia->setActa($acta);
                }
                $this->profesorDocenciaStgrRepository->Guardar($oProfesorDocencia);
            }
        }

        return sprintf(_('Se ha actualizado la docencia para el periodo: %s'), $txt_curso);
    }
}
