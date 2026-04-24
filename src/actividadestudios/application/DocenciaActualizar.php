<?php

namespace src\actividadestudios\application;

use core\ConfigGlobal;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\value_objects\StatusId;
use src\actividadestudios\domain\contracts\ActividadAsignaturaRepositoryInterface;
use src\actividadestudios\domain\value_objects\TipoActividadAsignatura;
use src\notas\domain\contracts\ActaRepositoryInterface;
use src\profesores\domain\contracts\ProfesorDocenciaStgrRepositoryInterface;
use src\profesores\domain\entity\ProfesorDocenciaStgr;
use web\Periodo;

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
    public static function execute(array $input): string
    {
        $Qyear = (string) ($input['year'] ?? '');
        $Qperiodo = (string) ($input['periodo'] ?? '');
        $Qempiezamin = (string) ($input['empiezamin'] ?? '');
        $Qempiezamax = (string) ($input['empiezamax'] ?? '');

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

        $ActividadAllRepository = $GLOBALS['container']->get(ActividadAllRepositoryInterface::class);
        $cActividades = $ActividadAllRepository->getActividades($aWhere, $aOperador);
        $ini_m = $_SESSION['oConfig']->getMesIniStgr();

        $ProfesorDocenciaStgrRepository = $GLOBALS['container']->get(ProfesorDocenciaStgrRepositoryInterface::class);
        $ActaRepository = $GLOBALS['container']->get(ActaRepositoryInterface::class);
        $ActividadAsignaturaRepository = $GLOBALS['container']->get(ActividadAsignaturaRepositoryInterface::class);

        foreach ($cActividades as $oActividad) {
            $id_activ = $oActividad->getId_activ();
            $id_tipo_activ = $oActividad->getId_tipo_activ();
            $oFini = $oActividad->getF_ini();
            $mes = (int) $oFini->format('m');
            $any = (int) $oFini->format('Y');
            $ini_a = ($mes < $ini_m) ? $any - 1 : $any;

            $cActivAsignaturas = $ActividadAsignaturaRepository->getActividadAsignaturas(
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
                    if (preg_match("/$id_tipo_inv/", $id_tipo_activ)) {
                        $tipo = TipoActividadAsignatura::TIPO_INV;
                    }
                }

                $cActas = $ActaRepository->getActas(['id_activ' => $id_activ, 'id_asignatura' => $id_asignatura]);
                $acta = '';
                if (is_array($cActas)) {
                    foreach ($cActas as $oActa) {
                        $acta .= (empty($acta) ? '' : ', ') . $oActa->getActa();
                    }
                }

                $aWhereDocencia = [
                    'id_nom' => $id_profesor,
                    'id_activ' => $id_activ,
                    'id_asignatura' => $id_asignatura,
                ];
                $cProfesorDocencia = $ProfesorDocenciaStgrRepository->getProfesorDocenciasStgr($aWhereDocencia);
                if (is_array($cProfesorDocencia) && count($cProfesorDocencia) > 0) {
                    $oProfesorDocencia = $cProfesorDocencia[0];
                    $oProfesorDocencia->setCurso_inicio($ini_a);
                    $oProfesorDocencia->setTipo($tipo);
                    $oProfesorDocencia->setActa($acta);
                } else {
                    $newId = $ProfesorDocenciaStgrRepository->getNewId();
                    $oProfesorDocencia = new ProfesorDocenciaStgr();
                    $oProfesorDocencia->setId_item($newId);
                    $oProfesorDocencia->setId_nom($id_profesor);
                    $oProfesorDocencia->setId_activ($id_activ);
                    $oProfesorDocencia->setId_asignatura($id_asignatura);
                    $oProfesorDocencia->setCurso_inicio($ini_a);
                    $oProfesorDocencia->setTipo($tipo);
                    $oProfesorDocencia->setActa($acta);
                }
                $ProfesorDocenciaStgrRepository->Guardar($oProfesorDocencia);
            }
        }

        return sprintf(_('Se ha actualizado la docencia para el periodo: %s'), $txt_curso);
    }
}
