<?php

namespace src\actividadessacd\application;

use src\shared\config\ConfigGlobal;
use src\actividadcargos\domain\contracts\CargoOAsistenteInterface;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\contracts\ActividadDlRepositoryInterface;
use src\actividades\domain\value_objects\StatusId;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\personas\domain\entity\Persona;
use src\procesos\domain\contracts\ActividadFaseRepositoryInterface;
use src\procesos\domain\contracts\ActividadProcesoTareaRepositoryInterface;
use src\procesos\domain\value_objects\FaseId;
use frontend\shared\web\Periodo;

/**
 * Caso de uso: construye el listado de sacd con actividades incompatibles (solapes).
 */
final class SolapesSacdData
{
    public function __construct(
        private ActividadFaseRepositoryInterface $actividadFaseRepository,
        private ActividadDlRepositoryInterface $actividadDlRepository,
        private PersonaSacdRepositoryInterface $personaSacdRepository,
        private CargoOAsistenteInterface $cargoOAsistenteRepository,
        private ActividadAllRepositoryInterface $actividadAllRepository,
        private ActividadProcesoTareaRepositoryInterface $actividadProcesoTareaRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @return array<string, mixed>
     */
    public function execute(array $input): array
    {
        $year = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'year');
        $periodo = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'periodo');
        $empiezamin = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'empiezamin');
        $empiezamax = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'empiezamax');

        $oActividadFase = $this->actividadFaseRepository->findById(FaseId::FASE_OK_SACD);
        $txt_fase_ok_sacd = $oActividadFase !== null
            ? (string)$oActividadFase->getDesc_fase()
            : '';

        $oPeriodo = new Periodo();
        $oPeriodo->setDefaultAny('next');
        $oPeriodo->setAny($year);
        $oPeriodo->setEmpiezaMin($empiezamin);
        $oPeriodo->setEmpiezaMax($empiezamax);
        $oPeriodo->setPeriodo($periodo);
        $inicioIso = $oPeriodo->getF_ini_iso();
        $finIso = $oPeriodo->getF_fin_iso();

        $aWhere = [
            'f_ini' => "'$inicioIso','$finIso'",
            'status' => StatusId::TERMINADA,
            '_ordre' => 'f_ini',
        ];
        $aOperador = [
            'f_ini' => 'BETWEEN',
            'status' => '<',
        ];

        $cActividades = $this->actividadDlRepository->getActividades($aWhere, $aOperador);

        $mi_dl = ConfigGlobal::mi_delef();
        $cSacds = $this->personaSacdRepository->getPersonas(
            [
                'id_tabla' => "'n','a'",
                'sacd' => 't',
                'dl' => $mi_dl,
                '_ordre' => 'apellido1,apellido2,nom',
            ],
            ['id_tabla' => 'IN']
        );

        $a_solapes = $this->cargoOAsistenteRepository->getSolapes($cSacds, $cActividades);

        $filas = [];
        foreach ($a_solapes as $id_nom => $aId_activ) {
            $id_nom = (int)$id_nom;
            $oPersona = Persona::findPersonaEnGlobal($id_nom);
            $nom_sacd = is_object($oPersona)
                ? (string)$oPersona->getApellidosNombre()
                : (string)$oPersona;

            $actividades = [];
            $id_ubi_anterior = '';
            foreach ($aId_activ as $id_activ) {
                $oActividad = $this->actividadAllRepository->findById((int)$id_activ);
                if ($oActividad === null) {
                    continue;
                }
                $nom_activ = (string)$oActividad->getNom_activ();
                $id_ubi = (string)$oActividad->getId_ubi();
                $status = (int)$oActividad->getStatus();
                $sacd_aprobado = $this->actividadProcesoTareaRepository->getSacdAprobado((int)$id_activ);
                $clase = \src\shared\domain\helpers\FuncTablasSupport::isTrue($sacd_aprobado) ? 'plaza4' : '';
                if ($status === StatusId::PROYECTO) {
                    $clase = 'wrong-soft';
                }
                if ($id_ubi_anterior === $id_ubi) {
                    $clase .= ' tachado';
                }
                $id_ubi_anterior = $id_ubi;
                $actividades[] = [
                    'clase' => trim($clase),
                    'nom_activ' => $nom_activ,
                ];
            }

            $filas[] = [
                'id_nom' => $id_nom,
                'nom_sacd' => $nom_sacd,
                'actividades' => $actividades,
            ];
        }

        return [
            'titulo' => ucfirst(_("listado de sacd con actividades incompatibles")),
            'inicio_iso' => $inicioIso,
            'fin_iso' => $finIso,
            'texto_fase_ok_sacd' => $txt_fase_ok_sacd,
            'filas' => $filas,
        ];
    }
}
