<?php

namespace src\actividadestudios\application;

use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividadestudios\domain\contracts\ActividadAsignaturaRepositoryInterface;
use src\actividadestudios\domain\contracts\MatriculaRepositoryInterface;
use src\actividadestudios\domain\entity\ActividadAsignatura;
use src\notas\domain\contracts\ActaRepositoryInterface;
use src\notas\domain\entity\Nota;
use src\notas\domain\value_objects\NotaSituacion;
use src\personas\domain\entity\Persona;
use src\shared\config\ConfigGlobal;

/**
 * @return array{
 *   msg_err: string,
 *   permiso: int,
 *   nom_activ: string,
 *   matriculados: int,
 *   matriculas_rows: list<array{nom: string, id_nom: int, nota_num: string|null, nota_max: string|null, preceptor: bool, acta: string|null, editable: bool}>,
 *   notas: string,
 *   despl_actas_opciones: array<int|string, string>,
 *   acta_principal: string,
 *   acta_notas_a_actas: list<string>,
 *   acta_txt_cursada: string,
 * }
 */
final class ActaNotasData
{
    public function __construct(
        private ActividadAsignaturaRepositoryInterface $actividadAsignaturaRepository,
        private ActividadAllRepositoryInterface $actividadAllRepository,
        private MatriculaRepositoryInterface $matriculaRepository,
        private ActaRepositoryInterface $actaRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @return array{
     *   msg_err: string,
     *   permiso: int,
     *   nom_activ: string,
     *   matriculados: int,
     *   matriculas_rows: list<array{nom: string, id_nom: int, nota_num: string|null, nota_max: string|null, preceptor: bool, acta: string|null, editable: bool}>,
     *   notas: string,
     *   despl_actas_opciones: array<int|string, string>,
     *   acta_principal: string,
     *   acta_notas_a_actas: list<string>,
     *   acta_txt_cursada: string,
     * }
     */
    public function execute(array $input): array
    {
        $idActiv = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_activ');
        $idAsignatura = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_asignatura');
        $idSchema = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_schema');

        $msgErr = '';
        $empty = [
            'msg_err' => _('no encuentro la asignatura en la actividad'),
            'permiso' => 1,
            'nom_activ' => '',
            'matriculados' => 0,
            'matriculas_rows' => [],
            'notas' => 'nuevo',
            'despl_actas_opciones' => [],
            'acta_principal' => '',
            'acta_notas_a_actas' => [],
            'acta_txt_cursada' => Nota::getStatusTxt(NotaSituacion::CURSADA),
        ];

        $oActividadAsignatura = $this->resolverActividadAsignatura($idActiv, $idAsignatura, $idSchema);
        if ($oActividadAsignatura === null) {
            return $empty;
        }
        $idSchema = $oActividadAsignatura->getId_schema();
        $permiso = ($idSchema === ConfigGlobal::mi_id_schema()) ? 3 : 1;

        $oActividad = $this->actividadAllRepository->findById($idActiv);
        $nomActiv = $oActividad !== null ? $oActividad->getNom_activ() : '';

        $cMatriculados = $this->matriculaRepository->getMatriculas([
            'id_asignatura' => $idAsignatura,
            'id_activ' => $idActiv,
            'id_schema' => $idSchema,
        ]);
        $matriculados = count($cMatriculados);
        $aPersonasMatriculadas = [];
        if ($matriculados > 0) {
            foreach ($cMatriculados as $oMatricula) {
                $idNom = $oMatricula->getId_nom();
                $oPersona = Persona::findPersonaEnGlobal($idNom);
                if ($oPersona === null) {
                    $msgErr .= "<br>No encuentro a nadie con id_nom: $idNom";
                    continue;
                }
                $nom = $oPersona->getPrefApellidosNombre();
                $aPersonasMatriculadas[$nom] = $oMatricula;
            }
            uksort($aPersonasMatriculadas, [\src\shared\domain\helpers\FuncTablasSupport::class, 'strsinacentocmp']);
        }

        $cActas = $this->actaRepository->getActas([
            'id_activ' => $idActiv,
            'id_asignatura' => $idAsignatura,
            'id_schema' => $idSchema,
            '_ordre' => 'f_acta',
        ]);
        $actaPrincipal = '';
        $nomActa = '';
        $desplActasOpciones = [];
        $notas = 'nuevo';
        $aActasList = [];
        $actasFirmadas = [];
        if ($cActas !== []) {
            $desplActasOpciones = [0 => '', NotaSituacion::CURSADA => Nota::getStatusTxt(NotaSituacion::CURSADA)];
            foreach ($cActas as $oActa) {
                $nomActa = $oActa->getActa();
                $desplActasOpciones[$nomActa] = $oActa->getActa();
                $aActasList[] = $nomActa;
                if ($oActa->tienePdfFirmado()) {
                    $actasFirmadas[$nomActa] = true;
                }
            }
            $notas = 'acta';
            if (count($cActas) === 1) {
                $actaPrincipal = $nomActa;
            }
        } else {
            $desplActasOpciones = ['primero guardar acta'];
        }

        $matriculasRows = [];
        foreach ($aPersonasMatriculadas as $nom => $oMatricula) {
            $matriculasRows[] = [
                'nom' => $nom,
                'id_nom' => $oMatricula->getId_nom(),
                'nota_num' => $oMatricula->getNota_num(),
                'nota_max' => $oMatricula->getNota_max(),
                'preceptor' => (bool) \src\shared\domain\helpers\FuncTablasSupport::isTrue($oMatricula->isPreceptor()),
                'acta' => $oMatricula->getActa(),
                'editable' => !isset($actasFirmadas[(string) $oMatricula->getActa()]),
            ];
        }

        return [
            'msg_err' => $msgErr,
            'permiso' => $permiso,
            'nom_activ' => $nomActiv,
            'matriculados' => $matriculados,
            'matriculas_rows' => $matriculasRows,
            'notas' => $notas,
            'despl_actas_opciones' => $desplActasOpciones,
            'acta_principal' => $actaPrincipal,
            'acta_notas_a_actas' => $aActasList,
            'acta_txt_cursada' => Nota::getStatusTxt(NotaSituacion::CURSADA),
        ];
    }

    private function resolverActividadAsignatura(int $idActiv, int $idAsignatura, int $idSchema): ?ActividadAsignatura
    {
        $where = [
            'id_activ' => $idActiv,
            'id_asignatura' => $idAsignatura,
        ];
        if ($idSchema > 0) {
            $where['id_schema'] = $idSchema;
        }
        $filas = $this->actividadAsignaturaRepository->getActividadAsignaturas($where);
        if ($filas === []) {
            return null;
        }
        if ($idSchema > 0) {
            return $filas[0];
        }
        $mio = ConfigGlobal::mi_id_schema();
        foreach ($filas as $fila) {
            if ($fila->getId_schema() === $mio) {
                return $fila;
            }
        }

        return $filas[0];
    }
}
