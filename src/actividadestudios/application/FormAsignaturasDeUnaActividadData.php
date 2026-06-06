<?php

namespace src\actividadestudios\application;

use src\actividadestudios\domain\contracts\ActividadAsignaturaDlRepositoryInterface;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asignaturas\domain\value_objects\AsignaturaId;
use src\profesores\domain\ProfesorActividad;
use src\profesores\domain\services\ProfesorAsignaturaService;
use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;

/**
 * @return array{
 *   mod: string,
 *   id_activ: int,
 *   id_asignatura: int,
 *   nombre_corto: string,
 *   chk_avisado: string,
 *   chk_confirmado: string,
 *   chk_preceptor: string,
 *   f_ini: string,
 *   f_fin: string,
 *   oDesplProfesores_opciones: array<int|string, string>,
 *   id_profesor_sel: int|string,
 *   oDesplAsignaturas_opciones: array<int|string, string>,
 *   primary_key_s: string,
 *   camposForm: string,
 *   a_camposHidden: array<string, int|string>
 * }
 */
final class FormAsignaturasDeUnaActividadData
{
    public function __construct(
        private ActividadAsignaturaDlRepositoryInterface $actividadAsignaturaDlRepository,
        private ProfesorAsignaturaService $profesorAsignaturaService,
        private AsignaturaRepositoryInterface $asignaturaRepository,
        private ProfesoresDesplegableData $profesoresDesplegableData,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @return array{
     *   mod: string,
     *   id_activ: int,
     *   id_asignatura: int,
     *   nombre_corto: string,
     *   chk_avisado: string,
     *   chk_confirmado: string,
     *   chk_preceptor: string,
     *   f_ini: string,
     *   f_fin: string,
     *   oDesplProfesores_opciones: array<int|string, string>,
     *   id_profesor_sel: int|string,
     *   oDesplAsignaturas_opciones: array<int|string, string>,
     *   primary_key_s: string,
     *   camposForm: string,
     *   a_camposHidden: array<string, int|string>
     * }
     */
    public function execute(array $input): array
    {
        $pau = input_string($input, 'pau');
        $idPau = input_int($input, 'id_pau');
        $idActivPost = input_int($input, 'id_activ');
        $idAsignaturaPost = input_int($input, 'id_asignatura');
        $sel = isset($input['sel']) && is_array($input['sel']) ? $input['sel'] : null;

        if (!empty($sel)) {
            $sel0 = $sel[0] ?? '';
            $parts = explode('#', is_scalar($sel0) ? (string) $sel0 : '');
            $idActiv = (int) $parts[0];
            $idAsignatura = (int) ($parts[1] ?? 0);
        } else {
            $idActiv = ($pau === 'a') ? $idPau : $idActivPost;
            $idAsignatura = $idAsignaturaPost;
        }

        $chkAvisado = '';
        $chkConfirmado = '';
        $chkPreceptor = '';
        $oDesplAsignaturasOpciones = [];
        $primaryKeyS = '';
        $camposForm = 'f_ini!f_fin!tipo!id_profesor';
        $aCamposHidden = ['id_activ' => $idActiv];

        if ($idAsignatura > 0) {
            $mod = 'editar';
            $oActividadAsignatura = $this->actividadAsignaturaDlRepository->findById($idActiv, $idAsignatura);
            if ($oActividadAsignatura === null) {
                throw new \RuntimeException(_('no encuentro la asignatura de actividad'));
            }

            $aOpciones = $this->profesorAsignaturaService->getArrayTodosProfesoresAsignatura(new AsignaturaId($idAsignatura));
            $idProfesor = $oActividadAsignatura->getId_profesor();
            $idProfesorSel = -1;
            if ($idProfesor !== null) {
                $idProfesorSel = $idProfesor;
                $aOpciones = $this->profesoresDesplegableData->conProfesorAsignadoSiFalta($aOpciones, $idProfesor);
            }

            $aviso = $oActividadAsignatura->getAvis_profesor();
            $chkAvisado = ($aviso === 'a') ? 'selected' : '';
            $chkConfirmado = ($aviso === 'c') ? 'selected' : '';
            $tipo = $oActividadAsignatura->getTipo();
            $chkPreceptor = ($tipo === 'p') ? 'selected' : '';
            $fIni = $oActividadAsignatura->getF_ini()?->getFromLocal() ?? '';
            $fFin = $oActividadAsignatura->getF_fin()?->getFromLocal() ?? '';

            $oAsignatura = $this->asignaturaRepository->findById($idAsignatura);
            if ($oAsignatura === null) {
                throw new \RuntimeException(sprintf(_('No se ha encontrado la asignatura con id: %s'), (string)$idAsignatura));
            }
            $nombreCorto = $oAsignatura->getNombre_corto() ?? '';
            $primaryKeyS = "id_activ=$idActiv AND id_asignatura=$idAsignatura";
            $aCamposHidden['id_asignatura'] = $idAsignatura;
            $aCamposHidden['primary_key_s'] = $primaryKeyS;
        } else {
            $mod = 'nuevo';
            $nombreCorto = '';
            $idProfesorSel = -1;
            $profesorActividad = new ProfesorActividad();
            $aOpciones = $profesorActividad->getArrayProfesoresActividad([$idActiv]);
            $fIni = '';
            $fFin = '';
            if (empty($idActiv)) {
                throw new \InvalidArgumentException(_('debería haber un nombre de asignatura'));
            }
            $oDesplAsignaturasOpciones = $this->asignaturaRepository->getArrayAsignaturasConSeparador(false);
            $camposForm .= '!id_asignatura';
        }

        return [
            'mod' => $mod,
            'id_activ' => $idActiv,
            'id_asignatura' => $idAsignatura,
            'nombre_corto' => $nombreCorto,
            'chk_avisado' => $chkAvisado,
            'chk_confirmado' => $chkConfirmado,
            'chk_preceptor' => $chkPreceptor,
            'f_ini' => $fIni,
            'f_fin' => $fFin,
            'oDesplProfesores_opciones' => $aOpciones,
            'id_profesor_sel' => $idProfesorSel,
            'oDesplAsignaturas_opciones' => $oDesplAsignaturasOpciones,
            'primary_key_s' => $primaryKeyS,
            'camposForm' => $camposForm,
            'a_camposHidden' => $aCamposHidden,
        ];
    }
}
