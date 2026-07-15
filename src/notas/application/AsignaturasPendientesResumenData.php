<?php

namespace src\notas\application;


use src\actividades\domain\value_objects\NivelStgrId;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asignaturas\domain\support\PlanEstudiosFilter;
use src\asignaturas\domain\value_objects\PlanEstudios;
use src\notas\domain\contracts\PersonaNotaRepositoryInterface;
use src\notas\domain\value_objects\NotaSituacion;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;

/**
 * Resumen: número de alumnos con cada asignatura pendiente, desglosado por
 * tramo (nb, nc1, nc2, n total, ab, ac1, ac2, a total). Sucesor de la lógica
 * embebida en `frontend/notas/controller/asignaturas_pendientes_resumen.php`.
 *
 * @return array{pendientes: array<int, array<string, mixed>>}
 */
final class AsignaturasPendientesResumenData
{

    public function __construct(
        private readonly AsignaturaRepositoryInterface $asignaturaRepository,
        private readonly PersonaDlRepositoryInterface $personaDlRepository,
        private readonly PersonaNotaRepositoryInterface $personaNotaRepository,
        private readonly Tesera $tesera,
        private readonly PlanEstudiosDePersona $planEstudiosDePersona,
    ) {
    }
    /**
     * @return array<string, mixed>
     */
    public function execute(): array
    {
        $AsignaturaRepository = $this->asignaturaRepository;
        $aPendientes = [];
        foreach ([PlanEstudios::PLAN_1997, PlanEstudios::PLAN_2026] as $plan) {
            [$aWhere, $aOperador] = PlanEstudiosFilter::apply($plan, [
                'active' => 't',
                'id_nivel' => '1100,2500',
                '_ordre' => 'id_nivel',
            ], ['id_nivel' => 'BETWEEN']);
            foreach ($AsignaturaRepository->getAsignaturas($aWhere, $aOperador) as $oAsignatura) {
                $id_nivel = $oAsignatura->getId_nivel();
                if (isset($aPendientes[$id_nivel])) {
                    continue;
                }
                $aPendientes[$id_nivel] = [
                    'def' => [
                        'nombre' => $oAsignatura->getNombre_corto(),
                        'creditos' => $oAsignatura->getCreditos(),
                        'year' => $oAsignatura->getYear(),
                    ],
                    'nb' => 0,
                    'nc1' => 0,
                    'nc2' => 0,
                    'ntotal' => 0,
                    'ab' => 0,
                    'ac1' => 0,
                    'ac2' => 0,
                    'atotal' => 0,
                ];
            }
        }
        ksort($aPendientes);

        $a_Asig_isActive = [];
        $a_Asig_nivel = [];
        $cAsignaturasTodas = $AsignaturaRepository->getAsignaturas(['_ordre' => 'id_asignatura']);
        foreach ($cAsignaturasTodas as $oAsignatura) {
            $id_asignatura = $oAsignatura->getId_asignatura();
            $a_Asig_isActive[$id_asignatura] = $oAsignatura->isActive();
            $a_Asig_nivel[$id_asignatura] = $oAsignatura->getId_nivel();
        }

        $aWhere = [];
        $aOperador = [];
        $aWhere['situacion'] = 'A';
        $aWhere['nivel_stgr'] = NivelStgrId::B . ',' . NivelStgrId::C1 . ',' . NivelStgrId::C2;
        $aOperador['nivel_stgr'] = 'IN';
        $aWhere['id_tabla'] = '^[na]';
        $aOperador['id_tabla'] = '~';
        $PersonaDlRepository = $this->personaDlRepository;
        $cPersonas = $PersonaDlRepository->getPersonas($aWhere, $aOperador);

        $PersonaNotaDBRepository = $this->personaNotaRepository;
        $arrayNotasSuperadas = NotaSituacion::getArraySuperadas();
        $a_NivelesStgr = [NivelStgrId::B => 'b', NivelStgrId::C1 => 'c1', NivelStgrId::C2 => 'c2'];

        foreach ($cPersonas as $oPersona) {
            $id_nom = $oPersona->getId_nom();
            $id_tabla = $oPersona->getId_tabla();
            $nivel_stgr = $oPersona->getNivel_stgr();
            if ($nivel_stgr === null || !isset($a_NivelesStgr[$nivel_stgr])) {
                continue;
            }

            $tipo = $id_tabla . $a_NivelesStgr[$nivel_stgr];

            $cNotas = $PersonaNotaDBRepository->getPersonaNotas(['id_nom' => $id_nom]);
            $aAprobadas = [];
            foreach ($cNotas as $oPersonaNota) {
                $id_asignatura = $oPersonaNota->getId_asignatura();
                $id_nivel = $oPersonaNota->getIdNivelVo()->value();
                $id_situacion = $oPersonaNota->getId_situacion();

                if (($a_Asig_isActive[$id_asignatura] ?? null) !== true) {
                    continue;
                }

                if ($id_asignatura > 3000) {
                    $id_nivel_asig = $id_nivel;
                } else {
                    $id_nivel_asig = $a_Asig_nivel[$id_asignatura];
                }
                if (in_array($id_situacion, $arrayNotasSuperadas)) {
                    $aAprobadas[$id_nivel_asig] = 1;
                }
            }

            $plan = $this->planEstudiosDePersona->resolve($id_nom);
            $cAsignaturasPersona = $this->tesera->getAsignaturasPosibles($plan);

            foreach ($cAsignaturasPersona as $oAsignatura) {
                $id_nivel = $oAsignatura->getId_nivel();
                if (empty($aAprobadas[$id_nivel])) {
                    $aPendientes[$id_nivel][$tipo]++;
                }
            }
        }

        return ['pendientes' => $aPendientes];
    }
}
