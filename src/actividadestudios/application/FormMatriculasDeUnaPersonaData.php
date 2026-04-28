<?php

namespace src\actividadestudios\application;

use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividadestudios\domain\contracts\MatriculaDlRepositoryInterface;
use src\actividadestudios\domain\contracts\MatriculaRepositoryInterface;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
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
    /**
     * @param array<int, string>|null $sel
     */
    public static function execute(
        int $idNom,
        int $idActiv,
        int $idAsignaturaPost,
        ?array $sel,
    ): array {
        $idAsignaturaReal = 0;
        if (!empty($sel)) {
            $parts = explode('#', $sel[0]);
            $idActiv = (int)($parts[0] ?? 0);
            $idAsignaturaReal = (int)($parts[1] ?? 0);
        }

        $actividadAllRepository = $GLOBALS['container']->get(ActividadAllRepositoryInterface::class);
        $oActividad = $actividadAllRepository->findById($idActiv);
        $nomActiv = $oActividad->getNom_activ();

        $asignaturaRepository = $GLOBALS['container']->get(AsignaturaRepositoryInterface::class);
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

        $matriculaRepository = $GLOBALS['container']->get(MatriculaRepositoryInterface::class);
        if ($idAsignaturaReal > 0) {
            $mod = 'editar';
            $oMatricula = $matriculaRepository->findById($idActiv, $idAsignaturaReal, $idNom);
            $preceptor = $oMatricula->isPreceptor();
            $idPreceptor = $oMatricula->getId_preceptor();
            $oAsignatura = $asignaturaRepository->findById($idAsignaturaReal);
            if ($oAsignatura === null) {
                throw new \RuntimeException(sprintf(_('No se ha encontrado la asignatura con id: %s'), (string)$idAsignaturaReal));
            }
            $nombreCorto = $oAsignatura->getNombre_corto();
            $idNivel = $idAsignaturaReal;
            $idAsignatura = $idAsignaturaReal;
            $chkPreceptor = ($preceptor === true) ? 'checked' : '';
            if (!empty($idPreceptor)) {
                $profesorStgrService = $GLOBALS['container']->get(ProfesorStgrService::class);
                $oDesplProfesoresOpciones = $profesorStgrService->getArrayProfesoresDl();
            }
            $aCamposHidden['id_asignatura'] = $idAsignatura;
            $aCamposHidden['id_nivel'] = $idNivel;
            $aCamposHidden['mod'] = $mod;
        } else {
            $mod = 'nuevo';
            $cAsignaturas = $asignaturaRepository->getAsignaturas(
                ['active' => 't', 'id_nivel' => 3000, '_ordre' => 'id_nivel'],
                ['id_nivel' => '<'],
            );
            $aSuperadasIds = NotaSituacion::getArraySuperadas();
            $cond = implode('|', $aSuperadasIds);
            $personaNotaRepository = $GLOBALS['container']->get(PersonaNotaRepositoryInterface::class);
            $cAsignaturasSuperadas = $personaNotaRepository->getPersonaNotas(
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
            $matriculaDlRepository = $GLOBALS['container']->get(MatriculaDlRepositoryInterface::class);
            $cMatriculas = $matriculaDlRepository->getMatriculas(['id_nom' => $idNom, 'id_activ' => $idActiv]);
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
                $aFaltan[$idNivel] = $oAsignatura->getNombre_corto();
            }
            $oDesplNivelesOpciones = $aFaltan;
            $aCamposHidden['mod'] = $mod;
            $camposForm = 'id_asignatura!id_nivel';
        }

        $cOpcionalesGenericas = $asignaturaRepository->getAsignaturas(
            ['active' => 't', 'id_sector' => 1, 'id_nivel' => 3000, '_ordre' => 'nombre_corto'],
            ['id_nivel' => '<'],
        );
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
