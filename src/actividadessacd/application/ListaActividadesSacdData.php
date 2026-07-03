<?php

namespace src\actividadessacd\application;

use src\shared\config\ConfigGlobal;
use src\permisos\domain\PermisosActividades;
use src\permisos\domain\PermisosActividadesTrue;
use src\permisos\domain\XPermisos;
use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividadcargos\domain\contracts\CargoRepositoryInterface;
use src\actividades\domain\contracts\ActividadDlRepositoryInterface;
use src\actividades\domain\value_objects\StatusId;
use src\actividadescentro\domain\contracts\CentroEncargadoRepositoryInterface;
use src\personas\domain\entity\Persona;
use src\procesos\domain\contracts\ActividadFaseRepositoryInterface;
use src\procesos\domain\contracts\ActividadProcesoTareaRepositoryInterface;
use src\procesos\domain\PermAccion;
use src\procesos\domain\value_objects\FaseId;
use frontend\shared\web\Periodo;
use src\shared\domain\helpers\FuncTablasSupport;

/**
 * Caso de uso: construye la tabla principal de la pantalla activ_sacd.
 */
final class ListaActividadesSacdData
{
    public function __construct(
        private ActividadDlRepositoryInterface $actividadDlRepository,
        private CargoRepositoryInterface $cargoRepository,
        private ActividadCargoRepositoryInterface $actividadCargoRepository,
        private CentroEncargadoRepositoryInterface $centroEncargadoRepository,
        private ActividadFaseRepositoryInterface $actividadFaseRepository,
        private ActividadProcesoTareaRepositoryInterface $actividadProcesoTareaRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @return array<string, mixed>
     */
    public function execute(array $input): array
    {
        $tipo = FuncTablasSupport::inputString($input, 'tipo');
        $year = FuncTablasSupport::inputString($input, 'year');
        $periodo = FuncTablasSupport::inputString($input, 'periodo');
        $empiezamin = FuncTablasSupport::inputString($input, 'empiezamin');
        $empiezamax = FuncTablasSupport::inputString($input, 'empiezamax');

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

        $txt_fase_ok_sacd = '';
        if ($tipo === 'falta_sacd') {
            $oActividadFase = $this->actividadFaseRepository->findById(FaseId::FASE_OK_SACD);
            if ($oActividadFase !== null) {
                $txt_fase_ok_sacd = (string)$oActividadFase->getDesc_fase();
            }
        } else {
            $regex = self::regexPorTipo($tipo);
            if ($regex !== null) {
                $aWhere['id_tipo_activ'] = $regex;
                $aOperador['id_tipo_activ'] = '~';
            }
        }

        $cActividades = $this->actividadDlRepository->getActividades($aWhere, $aOperador);

        $aIdCargos_sacd = $this->cargoRepository->getArrayCargos('sacd');
        $txt_where_cargos = implode(',', array_keys($aIdCargos_sacd));

        $tieneProcesos = ConfigGlobal::is_app_installed('procesos');

        $filas = [];
        foreach ($cActividades as $oActividad) {
            $id_activ = (int)$oActividad->getId_activ();
            $id_tipo_activ = (string)$oActividad->getId_tipo_activ();
            $status = (int)$oActividad->getStatus();
            $dl_org = (string)$oActividad->getDl_org();
            $nom_activ = (string)$oActividad->getNom_activ();
            $f_ini = $oActividad->getF_ini()?->getFromLocal();
            $f_fin = $oActividad->getF_fin()?->getFromLocal();

            [$oPermActiv, $oPermCtr, $oPermSacd] = $this->resolverPermisos(
                $id_activ, $id_tipo_activ, $dl_org, $tieneProcesos
            );
            if ($oPermActiv->have_perm_activ('ocupado') === false) {
                continue;
            }
            if ($oPermActiv->have_perm_activ('ver') === false) {
                continue;
            }

            $sacd_aprobado = $tieneProcesos
                ? $this->actividadProcesoTareaRepository->getSacdAprobado($id_activ)
                : true;
            $clase = FuncTablasSupport::isTrue($sacd_aprobado) ? 'plaza4' : '';
            if ($status === StatusId::PROYECTO) {
                $clase = 'wrong-soft';
            }

            if ($oPermCtr->have_perm_activ('ver') === true) {
                $ctrs = '';
                $cCtrs = $this->centroEncargadoRepository->getCentrosEncargadosActividad($id_activ);
                foreach ($cCtrs as $oUbi) {
                    $ctrs .= $oUbi->getNombre_ubi() . ', ';
                }
                $ctrs = substr($ctrs, 0, -2);
                if ($ctrs !== '') {
                    $nom_activ = $nom_activ . " [$ctrs]";
                }
            }

            $sacds = [];
            if ($oPermSacd->have_perm_activ('ver') === true) {
                $cCargosActividad = $this->actividadCargoRepository->getActividadCargos(
                    [
                        'id_activ' => $id_activ,
                        'id_cargo' => $txt_where_cargos,
                        '_ordre' => 'id_cargo DESC',
                    ],
                    ['id_cargo' => 'IN']
                );
                foreach ($cCargosActividad as $oCargo) {
                    $id_nom = (int)$oCargo->getId_nom();
                    $oPersona = Persona::findPersonaEnGlobal($id_nom);
                    $ap_nom = is_object($oPersona)
                        ? (string)$oPersona->getPrefApellidosNombre()
                        : (string)$oPersona;
                    $sacds[] = [
                        'id_nom' => $id_nom,
                        'id_cargo' => (int)$oCargo->getId_cargo(),
                        'ap_nom' => $ap_nom,
                    ];
                }
            }

            if ($tipo === 'falta_sacd' && (FuncTablasSupport::isTrue($sacd_aprobado) || count($sacds) === 0)) {
                continue;
            }

            $filas[] = [
                'id_activ' => $id_activ,
                'nom_activ' => $nom_activ,
                'f_ini' => $f_ini,
                'f_fin' => $f_fin,
                'clase' => $clase,
                'perm_modificar' => $oPermSacd->have_perm_activ('modificar') === true,
                'perm_crear' => $oPermSacd->have_perm_activ('crear') === true,
                'sacds' => $sacds,
            ];
        }

        $oPerm = $_SESSION['oPerm'] ?? null;
        $perm_des = $oPerm instanceof XPermisos && $oPerm->have_perm_oficina('des');

        return [
            'titulo' => ucfirst(_("listado de actividades")),
            'tipo' => $tipo,
            'inicio_iso' => $inicioIso,
            'fin_iso' => $finIso,
            'texto_fase_ok_sacd' => $txt_fase_ok_sacd,
            'mostrar_nota_falta_sacd' => $tipo === 'falta_sacd',
            'perm_des' => $perm_des,
            'filas' => $filas,
        ];
    }

    private static function regexPorTipo(string $tipo): ?string
    {
        return match ($tipo) {
            'sv' => '^1',
            'na' => '^1[13]',
            'sg' => '^1[45]',
            'sr' => '^17',
            'sssc' => '^16',
            'sf' => '^2',
            'sf_na' => '^2[123]',
            'sf_sg' => '^2[45]',
            'sf_sr' => '^27',
            default => null,
        };
    }

    /**
     * @return array{0: PermAccion, 1: PermAccion, 2: PermAccion}
     */
    private function resolverPermisos(
        int $id_activ,
        string $id_tipo_activ,
        string $dl_org,
        bool $tieneProcesos
    ): array {
        if ($tieneProcesos) {
            $oPermSesion = $_SESSION['oPermActividades'] ?? null;
            if ($oPermSesion instanceof PermisosActividades) {
                $oPermSesion->setActividad($id_activ, $id_tipo_activ, $dl_org);
                return [
                    $oPermSesion->getPermisoActual('datos'),
                    $oPermSesion->getPermisoActual('ctr'),
                    $oPermSesion->getPermisoActual('sacd'),
                ];
            }
        }
        $oPerm = new PermisosActividadesTrue(ConfigGlobal::mi_id_usuario());
        return [
            $oPerm->getPermisoActual('datos'),
            $oPerm->getPermisoActual('ctr'),
            $oPerm->getPermisoActual('sacd'),
        ];
    }
}
