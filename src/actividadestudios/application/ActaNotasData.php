<?php

namespace src\actividadestudios\application;

use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\actividadestudios\application\support\MatriculaNotaEstado;
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
 *   acta_asignable: string,
 *   acta_notas_a_actas: list<string>,
 *   acta_txt_cursada: string,
 *   hay_alumnos_sin_nota: bool,
 *   puede_nueva_convocatoria: bool,
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
     *   acta_asignable: string,
     *   acta_notas_a_actas: list<string>,
     *   acta_txt_cursada: string,
     *   hay_alumnos_sin_nota: bool,
     *   puede_nueva_convocatoria: bool,
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
            'acta_asignable' => '',
            'acta_notas_a_actas' => [],
            'acta_txt_cursada' => Nota::getStatusTxt(NotaSituacion::CURSADA),
            'hay_alumnos_sin_nota' => false,
            'puede_nueva_convocatoria' => false,
        ];

        $oActividadAsignatura = $this->resolverActividadAsignatura($idActiv, $idAsignatura, $idSchema);
        if ($oActividadAsignatura === null) {
            return $empty;
        }
        $idSchema = $oActividadAsignatura->getId_schema();
        $permiso = ($idSchema === ConfigGlobal::mi_id_schema()) ? 3 : 1;

        $oActividad = $this->actividadAllRepository->findById($idActiv);
        $nomActiv = $oActividad !== null ? $oActividad->getNom_activ() : '';
        $esOrganizador = self::esDlOrganizadora($oActividad);

        $whereMatriculas = [
            'id_asignatura' => $idAsignatura,
            'id_activ' => $idActiv,
        ];
        if (!$esOrganizador) {
            $whereMatriculas['id_schema'] = $idSchema;
        }
        $cMatriculados = $this->matriculaRepository->getMatriculas($whereMatriculas);
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
        $actasNoFirmadas = [];
        $hayActaFirmada = false;
        if ($cActas !== []) {
            $desplActasOpciones = [0 => '', NotaSituacion::CURSADA => Nota::getStatusTxt(NotaSituacion::CURSADA)];
            foreach ($cActas as $oActa) {
                $nomActa = $oActa->getActa();
                $aActasList[] = $nomActa;
                if ($oActa->tienePdfFirmado()) {
                    $actasFirmadas[$nomActa] = true;
                    $hayActaFirmada = true;
                } else {
                    $desplActasOpciones[$nomActa] = $nomActa;
                    $actasNoFirmadas[] = $nomActa;
                }
            }
            $notas = 'acta';
            if (count($cActas) === 1) {
                $actaPrincipal = $nomActa;
            }
        } else {
            $desplActasOpciones = ['primero guardar acta'];
        }
        $actaAsignable = count($actasNoFirmadas) === 1 ? $actasNoFirmadas[0] : '';

        $matriculasRows = [];
        $hayAlumnosSinNota = false;
        foreach ($aPersonasMatriculadas as $nom => $oMatricula) {
            $notaNum = $oMatricula->getNota_num();
            $actaMatricula = $oMatricula->getActa();
            if (!MatriculaNotaEstado::tieneNota($notaNum)) {
                $hayAlumnosSinNota = true;
            }
            $matriculasRows[] = [
                'nom' => $nom,
                'id_nom' => $oMatricula->getId_nom(),
                'nota_num' => $notaNum,
                'nota_max' => $oMatricula->getNota_max(),
                'preceptor' => (bool) \src\shared\domain\helpers\FuncTablasSupport::isTrue($oMatricula->isPreceptor()),
                'acta' => $actaMatricula,
                'editable' => MatriculaNotaEstado::editable($actaMatricula, $notaNum, $actasFirmadas),
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
            'acta_asignable' => $actaAsignable,
            'acta_notas_a_actas' => $aActasList,
            'acta_txt_cursada' => Nota::getStatusTxt(NotaSituacion::CURSADA),
            'hay_alumnos_sin_nota' => $hayAlumnosSinNota,
            'puede_nueva_convocatoria' => $permiso === 3 && $hayAlumnosSinNota && $hayActaFirmada,
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

    private static function esDlOrganizadora(?ActividadAll $actividad): bool
    {
        if ($actividad === null) {
            return false;
        }
        $dlOrg = $actividad->getDl_org() ?? '';

        return $dlOrg !== '' && $dlOrg === ConfigGlobal::mi_delef();
    }
}
