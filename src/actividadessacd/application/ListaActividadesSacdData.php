<?php

namespace src\actividadessacd\application;

use src\shared\config\ConfigGlobal;
use src\permisos\domain\PermisosActividadesTrue;
use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividadcargos\domain\contracts\CargoRepositoryInterface;
use src\actividades\domain\contracts\ActividadDlRepositoryInterface;
use src\actividades\domain\value_objects\StatusId;
use src\actividadescentro\domain\contracts\CentroEncargadoRepositoryInterface;
use src\personas\domain\entity\Persona;
use src\procesos\domain\contracts\ActividadFaseRepositoryInterface;
use src\procesos\domain\contracts\ActividadProcesoTareaRepositoryInterface;
use src\procesos\domain\value_objects\FaseId;
use frontend\shared\web\Periodo;
use function src\shared\domain\helpers\is_true;

/**
 * Caso de uso: construye la tabla principal de la pantalla
 * `actividadessacd/activ_sacd` — actividades del tipo elegido en el
 * periodo + los sacd encargados de cada una, con el centro encargado
 * (si procede) y los flags de permiso (ver / modificar / crear) para
 * que el frontend decida como renderizar cada celda.
 *
 * Soporta:
 *  - Tipos sv / na / sg / sr / sssc / sf / sf_na / sf_sg / sf_sr.
 *  - Tipo `falta_sacd`: todas las actividades, pero filtra para dejar
 *    solo las que no tienen sacd, o que teniendolo no tienen la fase
 *    `FASE_OK_SACD`.
 *
 * Sucesor de la rama `lista_activ` del dispatcher legacy
 * `apps/actividadessacd/controller/activ_sacd_ajax.php`. La rama
 * secundaria `solape` (mismo switch en el legacy) vive en
 * `SolapesSacdData`.
 */
final class ListaActividadesSacdData
{
    public static function execute(array $input): array
    {
        $tipo = (string)($input['tipo'] ?? '');
        $year = (string)($input['year'] ?? '');
        $periodo = (string)($input['periodo'] ?? '');
        $empiezamin = (string)($input['empiezamin'] ?? '');
        $empiezamax = (string)($input['empiezamax'] ?? '');

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
            $ActividadFaseRepository = $GLOBALS['container']->get(ActividadFaseRepositoryInterface::class);
            $oActividadFase = $ActividadFaseRepository->findById(FaseId::FASE_OK_SACD);
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

        $ActividadDlRepository = $GLOBALS['container']->get(ActividadDlRepositoryInterface::class);
        $cActividades = $ActividadDlRepository->getActividades($aWhere, $aOperador);

        $CargoRepository = $GLOBALS['container']->get(CargoRepositoryInterface::class);
        $aIdCargos_sacd = $CargoRepository->getArrayCargos('sacd');
        $txt_where_cargos = implode(',', array_keys($aIdCargos_sacd));

        $ActividadCargoRepository = $GLOBALS['container']->get(ActividadCargoRepositoryInterface::class);
        $CentroEncargadoRepository = $GLOBALS['container']->get(CentroEncargadoRepositoryInterface::class);
        $tieneProcesos = ConfigGlobal::is_app_installed('procesos');
        $ActividadProcesoTareaRepository = $tieneProcesos
            ? $GLOBALS['container']->get(ActividadProcesoTareaRepositoryInterface::class)
            : null;

        $filas = [];
        foreach ($cActividades as $oActividad) {
            $id_activ = (int)$oActividad->getId_activ();
            $id_tipo_activ = (string)$oActividad->getId_tipo_activ();
            $status = (int)$oActividad->getStatus();
            $dl_org = (string)$oActividad->getDl_org();
            $nom_activ = (string)$oActividad->getNom_activ();
            $f_ini = $oActividad->getF_ini()?->getFromLocal();
            $f_fin = $oActividad->getF_fin()?->getFromLocal();

            [$oPermActiv, $oPermCtr, $oPermSacd] = self::resolverPermisos(
                $id_activ, $id_tipo_activ, $dl_org, $tieneProcesos
            );
            if ($oPermActiv->have_perm_activ('ocupado') === false) {
                continue;
            }
            if ($oPermActiv->have_perm_activ('ver') === false) {
                continue;
            }

            $sacd_aprobado = $tieneProcesos
                ? $ActividadProcesoTareaRepository->getSacdAprobado($id_activ)
                : true;
            $clase = is_true($sacd_aprobado) ? 'plaza4' : '';
            if ($status === StatusId::PROYECTO) {
                $clase = 'wrong-soft';
            }

            // Centro encargado: se concatena al nom_activ como "[ctr1, ctr2]".
            if ($oPermCtr->have_perm_activ('ver') === true) {
                $ctrs = '';
                $cCtrs = $CentroEncargadoRepository->getCentrosEncargadosActividad($id_activ);
                if (is_array($cCtrs)) {
                    foreach ($cCtrs as $oUbi) {
                        $ctrs .= $oUbi->getNombre_ubi() . ', ';
                    }
                }
                $ctrs = substr($ctrs, 0, -2);
                if ($ctrs !== '') {
                    $nom_activ = $nom_activ . " [$ctrs]";
                }
            }

            $sacds = [];
            if ($oPermSacd->have_perm_activ('ver') === true) {
                $cCargosActividad = $ActividadCargoRepository->getActividadCargos(
                    [
                        'id_activ' => $id_activ,
                        'id_cargo' => $txt_where_cargos,
                        '_ordre' => 'id_cargo DESC',
                    ],
                    ['id_cargo' => 'IN']
                );
                if (is_array($cCargosActividad)) {
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
            }

            // Filtro especifico para falta_sacd: quedarse solo con las
            // actividades sin sacd, o con sacd pero sin fase ok_sacd.
            if ($tipo === 'falta_sacd'
                && !(!(is_true($sacd_aprobado) && !empty($sacds)) || empty($sacds))
            ) {
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

        $perm_des = isset($_SESSION['oPerm'])
            && $_SESSION['oPerm']->have_perm_oficina('des');

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
        switch ($tipo) {
            case 'sv':
                return '^1';
            case 'na':
                return '^1[13]';
            case 'sg':
                return '^1[45]';
            case 'sr':
                return '^17';
            case 'sssc':
                return '^16';
            case 'sf':
                return '^2';
            case 'sf_na':
                return '^2[123]';
            case 'sf_sg':
                return '^2[45]';
            case 'sf_sr':
                return '^27';
        }
        return null;
    }

    /**
     * @return array{0: object, 1: object, 2: object}  [oPermActiv, oPermCtr, oPermSacd]
     */
    private static function resolverPermisos(
        int $id_activ,
        string $id_tipo_activ,
        string $dl_org,
        bool $tieneProcesos
    ): array {
        if ($tieneProcesos && isset($_SESSION['oPermActividades'])) {
            $_SESSION['oPermActividades']->setActividad($id_activ, $id_tipo_activ, $dl_org);
            $oPerm = $_SESSION['oPermActividades'];
        } else {
            $oPerm = new PermisosActividadesTrue(ConfigGlobal::mi_id_usuario());
        }
        return [
            $oPerm->getPermisoActual('datos'),
            $oPerm->getPermisoActual('ctr'),
            $oPerm->getPermisoActual('sacd'),
        ];
    }
}
