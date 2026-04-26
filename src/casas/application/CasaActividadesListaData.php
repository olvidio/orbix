<?php

namespace src\casas\application;

use src\shared\config\ConfigGlobal;
use src\permisos\domain\PermisosActividadesTrue;
use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\actividadescentro\domain\contracts\CentroEncargadoRepositoryInterface;
use src\actividadtarifas\domain\contracts\TipoTarifaRepositoryInterface;
use src\procesos\domain\contracts\ActividadProcesoTareaRepositoryInterface;
use src\ubis\domain\contracts\CasaDlRepositoryInterface;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use frontend\shared\web\Periodo;
use src\actividades\domain\entity\TiposActividades;

/**
 * Data builder: listado de actividades por casa y periodo (pantalla
 * `casa_que` con `tipo_lista=lista_activ` y `que=lista_activ`).
 *
 * Devuelve cabeceras y filas listas para `frontend\shared\web\Lista`, aplicando los
 * permisos de la sesión.
 */
final class CasaActividadesListaData
{
    public static function execute(array $input): array
    {
        $periodo = (string)($input['periodo'] ?? '');
        $year = (string)($input['year'] ?? '');
        $empiezamin = (string)($input['empiezamin'] ?? '');
        $empiezamax = (string)($input['empiezamax'] ?? '');
        /** @var array $ids_ubi */
        $ids_ubi = (array)($input['id_cdc'] ?? []);

        $aCabeceras = [
            ucfirst((string)_("empieza")),
            ucfirst((string)_("hora ini")),
            ucfirst((string)_("termina")),
            ucfirst((string)_("hora fin")),
            (string)_("sf/sv"),
            ucfirst((string)_("activ.")),
            ucfirst((string)_("asist.")),
            ucfirst((string)_("tipo actividad")),
            ucfirst((string)_("lugar")),
            ucfirst((string)_("tar.")),
            ucfirst((string)_("centro")),
            ucfirst((string)_("sacd")),
            ucfirst((string)_("observaciones")),
        ];

        $aGrupos = [];
        $CasaDl = $GLOBALS['container']->get(CasaDlRepositoryInterface::class);
        foreach ($ids_ubi as $id_ubi) {
            $id_ubi = (int)$id_ubi;
            if ($id_ubi === 0) { continue; }
            $oCasa = $CasaDl->findById($id_ubi);
            if ($oCasa === null) { continue; }
            $aGrupos[$id_ubi] = $oCasa->getNombreUbiVo()?->value() ?? '';
        }

        $oPeriodo = new Periodo();
        $oPeriodo->setDefaultAny('next');
        $oPeriodo->setAny($year);
        $oPeriodo->setEmpiezaMin($empiezamin);
        $oPeriodo->setEmpiezaMax($empiezamax);
        $oPeriodo->setPeriodo($periodo);
        $inicioIso = $oPeriodo->getF_ini_iso();
        $finIso = $oPeriodo->getF_fin_iso();

        $TipoTarifaRepository = $GLOBALS['container']->get(TipoTarifaRepositoryInterface::class);
        $ActividadRepository = $GLOBALS['container']->get(ActividadRepositoryInterface::class);
        $CentroEncargadoRepository = $GLOBALS['container']->get(CentroEncargadoRepositoryInterface::class);
        $ActividadProcesoTareaRepository = $GLOBALS['container']->get(ActividadProcesoTareaRepositoryInterface::class);
        $CentroDl = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);

        $a_valores = [];
        foreach ($aGrupos as $id_ubi => $titulo) {
            $id_ubi = (int)$id_ubi;
            $aWhere = [
                'id_ubi' => $id_ubi,
                'f_ini' => "'$inicioIso','$finIso'",
                'status' => 4,
                '_ordre' => 'f_ini',
            ];
            $aOperador = ['f_ini' => 'BETWEEN', 'status' => '<'];
            $cActividades = $ActividadRepository->getActividades($aWhere, $aOperador);
            if ($cActividades === false) { continue; }

            $a = 0;
            foreach ($cActividades as $oActividad) {
                $a++;
                $id_activ = (int)$oActividad->getId_activ();
                $id_tipo_activ = (string)$oActividad->getId_tipo_activ();
                $dl_org = $oActividad->getDl_org();
                $f_ini = $oActividad->getF_ini()?->getFromLocal() ?? '';
                $h_ini = (string)$oActividad->getH_ini();
                $f_fin = $oActividad->getF_fin()?->getFromLocal() ?? '';
                $h_fin = (string)$oActividad->getH_fin();
                $id_tarifa = (string)$oActividad->getTarifa();
                $observ = (string)$oActividad->getObserv();
                if ($h_ini !== '') { $h_ini = substr($h_ini, 0, strlen($h_ini) - 3); }
                if ($h_fin !== '') { $h_fin = substr($h_fin, 0, strlen($h_fin) - 3); }

                if (ConfigGlobal::is_app_installed('procesos')) {
                    $_SESSION['oPermActividades']->setActividad($id_activ, $id_tipo_activ, $dl_org);
                    $oPermActiv = $_SESSION['oPermActividades']->getPermisoActual('datos');
                    $oPermCtr = $_SESSION['oPermActividades']->getPermisoActual('ctr');
                    $oPermSacd = $_SESSION['oPermActividades']->getPermisoActual('sacd');
                } else {
                    $oPermActividades = new PermisosActividadesTrue(ConfigGlobal::mi_id_usuario());
                    $oPermActiv = $oPermActividades->getPermisoActual('datos');
                    $oPermCtr = $oPermActividades->getPermisoActual('ctr');
                    $oPermSacd = $oPermActividades->getPermisoActual('sacd');
                }

                if (!$oPermActiv->have_perm_action('ocupado')) { continue; }

                $oTipoActiv = new TiposActividades($id_tipo_activ);
                if (!$oPermActiv->have_perm_action('ver')) {
                    $ssfsv = $oTipoActiv->getSfsvText();
                    $sasistentes = '';
                    $sactividad = '';
                    $snom_tipo = (string)_('ocupado');
                    $observ = '';
                } else {
                    $ssfsv = $oTipoActiv->getSfsvText();
                    $sasistentes = $oTipoActiv->getAsistentesText();
                    $sactividad = $oTipoActiv->getActividadText();
                    $snom_tipo = $oTipoActiv->getNom_tipoText();
                }

                $nombre_ubi = $CasaDl->findById($id_ubi)?->getNombreUbiVo()?->value() ?? '';

                $txt_ctr = '';
                if ($oPermCtr->have_perm_action('ver')) {
                    foreach ($CentroEncargadoRepository->getCentrosEncargadosActividad($id_activ) as $oCentroEncargado) {
                        $id_ctr = $oCentroEncargado->getId_ubi();
                        $oCentroDl = $CentroDl->findById($id_ctr);
                        $nombre_ctr = $oCentroDl?->getNombre_ubi() ?? '';
                        $txt_ctr .= $txt_ctr === '' ? $nombre_ctr : "; $nombre_ctr";
                    }
                }

                $txt_sacds = '';
                if (ConfigGlobal::is_app_installed('actividadessacd')) {
                    $aprobado = true;
                    if (ConfigGlobal::mi_sfsv() === 2) {
                        $aprobado = (bool)$ActividadProcesoTareaRepository->getSacdAprobado($id_activ);
                    }
                    if (!ConfigGlobal::is_app_installed('procesos')
                        || ($oPermSacd->have_perm_activ('ver') === true && $aprobado)) {
                        $ActividadCargoRepository = $GLOBALS['container']->get(ActividadCargoRepositoryInterface::class);
                        foreach ($ActividadCargoRepository->getActividadSacds($id_activ) as $oPersona) {
                            $nom_sacd = $oPersona->getPrefApellidosNombre();
                            $txt_sacds .= $txt_sacds === '' ? $nom_sacd : "# $nom_sacd";
                        }
                    }
                }

                $oTipoTarifa = $TipoTarifaRepository->findById($id_tarifa);
                $letra_tarifa = $oTipoTarifa?->getLetra() ?? '';

                $a_valores[$id_ubi][$a] = [
                    1 => $f_ini,
                    2 => $h_ini,
                    3 => $f_fin,
                    4 => $h_fin,
                    5 => $ssfsv,
                    6 => $sactividad,
                    7 => $sasistentes,
                    8 => $snom_tipo,
                    9 => $nombre_ubi,
                    10 => $letra_tarifa,
                    11 => $txt_ctr,
                    12 => $txt_sacds,
                    13 => $observ,
                ];
            }
        }

        return [
            'ok' => true,
            'a_cabeceras' => $aCabeceras,
            'a_valores' => $a_valores,
            'a_grupos' => $aGrupos,
        ];
    }
}
