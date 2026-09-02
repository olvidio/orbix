<?php

namespace src\actividadestudios\application;

use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividadestudios\application\support\AsignaturaNombreDlPrefix;
use src\actividadestudios\domain\contracts\ActividadAsignaturaRepositoryInterface;
use src\actividadestudios\domain\contracts\MatriculaDlRepositoryInterface;
use src\actividadestudios\domain\contracts\MatriculaRepositoryInterface;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asignaturas\domain\support\PlanEstudiosFilter;
use src\notas\application\PlanEstudiosDePersona;
use src\notas\application\support\NivelesOcupadosEnPlan;
use src\notas\domain\contracts\PersonaNotaRepositoryInterface;
use src\notas\domain\value_objects\NotaSituacion;
use src\profesores\domain\services\ProfesorStgrService;
use src\utils_database\domain\contracts\DbSchemaRepositoryInterface;

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
 *   alta_desde_ca: bool,
 *   oDesplAsignaturas_opciones: array<int|string, string>,
 *   camposForm: string,
 *   a_camposHidden: array<string, int|string>
 * }
 */
final class FormMatriculasDeUnaPersonaData
{
    public const MODO_MATRICULAR_CA = 'matricular_ca';

    public function __construct(
        private ActividadAllRepositoryInterface $actividadAllRepository,
        private AsignaturaRepositoryInterface $asignaturaRepository,
        private MatriculaRepositoryInterface $matriculaRepository,
        private ProfesorStgrService $profesorStgrService,
        private PersonaNotaRepositoryInterface $personaNotaRepository,
        private MatriculaDlRepositoryInterface $matriculaDlRepository,
        private PlanEstudiosDePersona $planEstudiosDePersona,
        private ActividadAsignaturaRepositoryInterface $actividadAsignaturaRepository,
        private DbSchemaRepositoryInterface $dbSchemaRepository,
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
     *   alta_desde_ca: bool,
     *   oDesplAsignaturas_opciones: array<int|string, string>,
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
        $modo = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'modo');
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

        $chkPreceptor = '';
        $idPreceptor = '';
        $nombreCorto = '';
        $oDesplProfesoresOpciones = [];
        $oDesplNivelesOpciones = [];
        $oDesplAsignaturasOpciones = [];
        $altaDesdeCa = false;
        $camposForm = '';
        $condicionJs = '';
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
        } elseif ($modo === self::MODO_MATRICULAR_CA) {
            $mod = 'nuevo';
            $altaDesdeCa = true;
            $oDesplAsignaturasOpciones = $this->opcionesAsignaturasDelCa(
                $idActiv,
                $idNom,
                $oActividad->getDl_org() ?? '',
            );
            $aCamposHidden['mod'] = $mod;
            $aCamposHidden['modo'] = self::MODO_MATRICULAR_CA;
            $camposForm = 'id_asignatura';
            $condicionJs = 'false';
        } else {
            $mod = 'nuevo';
            $plan = $this->planEstudiosDePersona->resolve($idNom);
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
            $aSuperadas = NivelesOcupadosEnPlan::ocupados(
                $cAsignaturasSuperadas,
                $plan,
                $this->asignaturaRepository,
            );
            $cMatriculas = $this->matriculaDlRepository->getMatriculas(['id_nom' => $idNom, 'id_activ' => $idActiv]);
            $aMatriculadas = [];
            foreach ($cMatriculas as $oMatricula) {
                $aMatriculadas[$oMatricula->getId_nivel()] = $oMatricula->getId_asignatura();
            }
            $aFaltan = [];
            foreach ($cAsignaturas as $oAsignatura) {
                $idNivel = $oAsignatura->getId_nivel();
                if (isset($aSuperadas[$idNivel])) {
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
            $condicionJs = $condicion !== '' ? substr($condicion, 0, -4) : 'false';
        }

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
            'alta_desde_ca' => $altaDesdeCa,
            'oDesplAsignaturas_opciones' => $oDesplAsignaturasOpciones,
            'camposForm' => $camposForm,
            'a_camposHidden' => $aCamposHidden,
        ];
    }

    /**
     * @return array<int|string, string>
     */
    private function opcionesAsignaturasDelCa(int $idActiv, int $idNom, string $dlOrg): array
    {
        $yaMatriculadas = [];
        foreach ($this->matriculaRepository->getMatriculas(['id_nom' => $idNom, 'id_activ' => $idActiv]) as $oMatricula) {
            $yaMatriculadas[$oMatricula->getId_asignatura()] = true;
        }

        $nombres = $this->asignaturaRepository->getArrayAsignaturasCreditos();
        $filas = [];
        foreach ($this->actividadAsignaturaRepository->getActividadAsignaturas(['id_activ' => $idActiv]) as $oOferta) {
            $idAsignatura = $oOferta->getId_asignatura();
            if (isset($yaMatriculadas[$idAsignatura])) {
                continue;
            }
            $datos = $nombres[$idAsignatura] ?? null;
            $nombre = '';
            if (is_array($datos) && isset($datos['nombre_asignatura']) && is_scalar($datos['nombre_asignatura'])) {
                $nombre = (string) $datos['nombre_asignatura'];
            }
            if ($nombre === '') {
                $oAsignatura = $this->asignaturaRepository->findById($idAsignatura);
                $nombre = $oAsignatura?->getNombre_corto() ?? (string) $idAsignatura;
            }
            $idSchema = $oOferta->getId_schema();
            $filas[] = [
                'id_asignatura' => $idAsignatura,
                'id_schema' => $idSchema,
                'dl' => AsignaturaNombreDlPrefix::dlDesdeIdSchema($this->dbSchemaRepository, $idSchema),
                'nombre' => $nombre,
            ];
        }

        $porAsignatura = [];
        foreach ($filas as $fila) {
            $idAsig = $fila['id_asignatura'];
            $porAsignatura[$idAsig] = ($porAsignatura[$idAsig] ?? 0) + 1;
        }

        $opciones = [];
        foreach ($filas as $fila) {
            $idAsignatura = $fila['id_asignatura'];
            $forzar = ($porAsignatura[$idAsignatura] ?? 0) > 1;
            $clave = $forzar
                ? $idAsignatura . '#' . $fila['id_schema']
                : (string) $idAsignatura;
            $opciones[$clave] = AsignaturaNombreDlPrefix::aplicar(
                $fila['nombre'],
                $fila['dl'],
                $dlOrg,
                $forzar,
            );
        }
        asort($opciones, SORT_NATURAL | SORT_FLAG_CASE);

        return $opciones;
    }
}
