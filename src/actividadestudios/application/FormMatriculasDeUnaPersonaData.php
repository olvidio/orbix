<?php

namespace src\actividadestudios\application;

use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividadestudios\domain\contracts\MatriculaDlRepositoryInterface;
use src\actividadestudios\domain\contracts\MatriculaRepositoryInterface;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asignaturas\domain\support\PlanEstudiosFilter;
use src\notas\application\PlanEstudiosDePersona;
use src\notas\domain\contracts\PersonaNotaRepositoryInterface;
use src\notas\domain\value_objects\NotaSituacion;
use src\profesores\domain\services\ProfesorStgrService;

/**
 * @return array{
 *   nom_activ: string,
 *   mod: string,
 *   id_asignatura_real: int,
 *   nombre_corto: string,
 *   chk_preceptor: string,
 *   id_preceptor: string|int,
 *   oDesplProfesores_opciones: array<int|string, string>,
 *   oDesplNiveles_opciones: array<int|string, string>,
 *   condicion_js: string,
 *   camposForm: string,
 *   a_camposHidden: array<string, int|string>
 * }
 */
final class FormMatriculasDeUnaPersonaData
{
    public function __construct(
        private ActividadAllRepositoryInterface $actividadAllRepository,
        private AsignaturaRepositoryInterface $asignaturaRepository,
        private MatriculaRepositoryInterface $matriculaRepository,
        private ProfesorStgrService $profesorStgrService,
        private PersonaNotaRepositoryInterface $personaNotaRepository,
        private MatriculaDlRepositoryInterface $matriculaDlRepository,
        private PlanEstudiosDePersona $planEstudiosDePersona,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @return array{
     *   nom_activ: string,
     *   mod: string,
     *   id_asignatura_real: int,
     *   nombre_corto: string,
     *   chk_preceptor: string,
     *   id_preceptor: string|int,
     *   oDesplProfesores_opciones: array<int|string, string>,
     *   oDesplNiveles_opciones: array<int|string, string>,
     *   condicion_js: string,
     *   camposForm: string,
     *   a_camposHidden: array<string, int|string>
     * }
     */
    public function execute(array $input): array
    {
        $idNom = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_nom');
        if ($idNom <= 0) {
            $idNom = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_pau');
        }
        $idActiv = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_activ');
        $idAsignaturaPost = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_asignatura');
        $sel = isset($input['sel']) && is_array($input['sel']) ? $input['sel'] : null;

        $idAsignaturaReal = 0;
        if (!empty($sel)) {
            $sel0 = $sel[0] ?? '';
            $parts = explode('#', is_scalar($sel0) ? (string) $sel0 : '');
            $idActiv = (int) $parts[0];
            $idAsignaturaReal = (int) ($parts[1] ?? 0);
        }

        $oActividad = $this->actividadAllRepository->findById($idActiv);
        if ($oActividad === null) {
            throw new \RuntimeException(sprintf(_('No se ha encontrado actividad con id: %s'), (string) $idActiv));
        }
        $nomActiv = $oActividad->getNom_activ();
        $plan = $this->planEstudiosDePersona->resolve($idNom);

        $chkPreceptor = '';
        $idPreceptor = '';
        $nombreCorto = '';
        $oDesplProfesoresOpciones = [];
        $oDesplNivelesOpciones = [];
        $camposForm = '';
        $aCamposHidden = [
            'id_pau' => $idNom,
            'id_activ' => $idActiv,
        ];

        if ($idAsignaturaReal > 0) {
            $mod = 'editar';
            $oMatricula = $this->matriculaRepository->findById($idActiv, $idAsignaturaReal, $idNom);
            if ($oMatricula === null) {
                throw new \RuntimeException(_('no encuentro la matricula'));
            }
            $preceptor = $oMatricula->isPreceptor();
            $idPreceptor = $oMatricula->getId_preceptor() ?? 0;
            $oAsignatura = $this->asignaturaRepository->findById($idAsignaturaReal);
            if ($oAsignatura === null) {
                throw new \RuntimeException(sprintf(_('No se ha encontrado la asignatura con id: %s'), (string)$idAsignaturaReal));
            }
            $nombreCorto = $oAsignatura->getNombre_corto() ?? '';
            $idNivel = $idAsignaturaReal;
            $idAsignatura = $idAsignaturaReal;
            $chkPreceptor = ($preceptor === true) ? 'checked' : '';
            if (!empty($idPreceptor)) {
                $oDesplProfesoresOpciones = $this->profesorStgrService->getArrayProfesoresDl();
            }
            $aCamposHidden['id_asignatura'] = $idAsignatura;
            $aCamposHidden['id_nivel'] = $idNivel;
            $aCamposHidden['mod'] = $mod;
        } else {
            $mod = 'nuevo';
            [$aWhere, $aOperador] = PlanEstudiosFilter::apply($plan, [
                'active' => 't',
                'id_nivel' => 3000,
                '_ordre' => 'id_nivel',
            ], ['id_nivel' => '<']);
            $cAsignaturas = $this->asignaturaRepository->getAsignaturas($aWhere, $aOperador);
            $aSuperadasIds = NotaSituacion::getArraySuperadas();
            $cond = implode('|', $aSuperadasIds);
            $cAsignaturasSuperadas = $this->personaNotaRepository->getPersonaNotas(
                [
                    'id_situacion' => $cond,
                    'id_nom' => $idNom,
                    'id_nivel' => 3000,
                    '_ordre' => 'id_nivel',
                ],
                ['id_situacion' => '~', 'id_nivel' => '<'],
            );
            $aSuperadas = [];
            foreach ($cAsignaturasSuperadas as $oAsignaturaRow) {
                $aSuperadas[$oAsignaturaRow->getId_nivel()] = $oAsignaturaRow->getId_asignatura();
            }
            $cMatriculas = $this->matriculaDlRepository->getMatriculas(['id_nom' => $idNom, 'id_activ' => $idActiv]);
            $aMatriculadas = [];
            foreach ($cMatriculas as $oMatricula) {
                $aMatriculadas[$oMatricula->getId_nivel()] = $oMatricula->getId_asignatura();
            }
            $aFaltan = [];
            foreach ($cAsignaturas as $oAsignatura) {
                $idNivel = $oAsignatura->getId_nivel();
                if (array_key_exists($idNivel, $aSuperadas)) {
                    continue;
                }
                if (array_key_exists($idNivel, $aMatriculadas)) {
                    continue;
                }
                $aFaltan[$idNivel] = $oAsignatura->getNombre_corto() ?? '';
            }
            $oDesplNivelesOpciones = $aFaltan;
            $aCamposHidden['mod'] = $mod;
            $camposForm = 'id_asignatura!id_nivel';
        }

        [$aWhereOp, $aOperadorOp] = PlanEstudiosFilter::apply($plan, [
            'active' => 't',
            'id_sector' => 1,
            'id_nivel' => 3000,
            '_ordre' => 'nombre_corto',
        ], ['id_nivel' => '<']);
        $cOpcionalesGenericas = $this->asignaturaRepository->getAsignaturas($aWhereOp, $aOperadorOp);
        $condicion = '';
        foreach ($cOpcionalesGenericas as $oOpcional) {
            $condicion .= 'id==' . $oOpcional->getId_nivel() . ' || ';
        }
        $condicionJs = substr($condicion, 0, -4);

        return [
            'nom_activ' => $nomActiv,
            'mod' => $mod,
            'id_asignatura_real' => $idAsignaturaReal,
            'nombre_corto' => $nombreCorto,
            'chk_preceptor' => $chkPreceptor,
            'id_preceptor' => $idPreceptor,
            'oDesplProfesores_opciones' => $oDesplProfesoresOpciones,
            'oDesplNiveles_opciones' => $oDesplNivelesOpciones,
            'condicion_js' => $condicionJs,
            'camposForm' => $camposForm,
            'a_camposHidden' => $aCamposHidden,
        ];
    }
}
