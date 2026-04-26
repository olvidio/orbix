<?php

namespace src\planning\application;

use src\shared\config\ConfigGlobal;
use src\actividadcargos\domain\contracts\CargoOAsistenteInterface;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\planning\domain\value_objects\PlanningStyle;
use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\actividades\domain\entity\TiposActividades;

/**
 * Recoge las actividades de cada persona dentro de un periodo dado.
 *
 * Modo por defecto (`agruparPorCentro = true`) agrupa las actividades por
 * el nombre del centro de la persona — forma usada por
 * `planning_ctr_select` para listar varios centros.
 *
 * Modo `agruparPorCentro = false` devuelve una lista plana
 * `[ ['p#id#nombre' => [actividad, ...]], ... ]` — forma usada por
 * `planning_persona_ver` para dibujar un unico calendario.
 *
 * Migrado desde `apps/planning/domain/ActividadesDePersona.php` y de la
 * logica inline de `apps/planning/controller/planning_persona_ver.php`
 * (slice 2 de la migracion del modulo planning).
 */
class ActividadesDePersonaService
{
    public static function actividadesPorPersona(
        array|bool $cPersonas,
        string $fin_iso,
        string $inicio_iso,
        DateTimeLocal $oIniPlanning,
        string $inicio_local,
        bool $agruparPorCentro = true
    ): array {
        $ActividadRepository = $GLOBALS['container']->get(ActividadRepositoryInterface::class);
        $CentroDlRepository = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
        $aListaCtr = [];
        $p = 0;
        $persona = [];
        $a_actividades = [];
        $a_actividades2 = [];
        if (!is_array($cPersonas)) {
            return $agruparPorCentro ? $a_actividades2 : $a_actividades;
        }
        foreach ($cPersonas as $oPersona) {
            $aActivPersona = [];
            $id_nom = $oPersona->getId_nom();
            $nombre = $oPersona->getPrefApellidosNombre();

            $nombre_ubi = '';
            if ($agruparPorCentro) {
                $id_ubi = $oPersona->getId_ctr();
                if (empty($id_ubi)) {
                    $nombre_ubi = _("centro?");
                } elseif (!in_array($id_ubi, $aListaCtr, true)) {
                    $oCentroDl = $CentroDlRepository->findById($id_ubi);
                    $nombre_ubi = $oCentroDl->getNombre_ubi();
                    $aListaCtr[$id_ubi] = $nombre_ubi;
                } else {
                    $nombre_ubi = $aListaCtr[$id_ubi];
                }
                $persona[$p] = "p#$id_nom#$nombre#$nombre_ubi";
            } else {
                $persona[$p] = "p#$id_nom#$nombre";
            }

            $aWhere = [
                'f_ini' => "'$fin_iso'",
                'f_fin' => "'$inicio_iso'",
                'status' => '2,3',
            ];
            $aOperador = [
                'f_ini' => '<=',
                'f_fin' => '>=',
                'status' => 'BETWEEN',
            ];

            $cCargoOAsistente = [];
            if (ConfigGlobal::is_app_installed('actividadcargos')) {
                $CargoOAsistente = $GLOBALS['container']->get(CargoOAsistenteInterface::class);
                $cCargoOAsistente = $CargoOAsistente->getCargoOAsistente($id_nom, $aWhere, $aOperador);
            }
            foreach ($cCargoOAsistente as $oCargoOAsistente) {
                $id_activ = $oCargoOAsistente->getId_activ();
                $propio = $oCargoOAsistente->isPropio();

                $aWhere['id_activ'] = $id_activ;
                $cActividades = $ActividadRepository->getActividades($aWhere, $aOperador);
                if (is_array($cActividades) && count($cActividades) === 0) {
                    continue;
                }

                $oActividad = $cActividades[0];
                $id_activ = $oActividad->getId_activ();
                $id_tipo_activ = $oActividad->getId_tipo_activ();
                $oF_ini = $oActividad->getF_ini();
                $h_ini = $oActividad->getH_ini()?->format('H:i');
                $oF_fin = $oActividad->getF_fin();
                $h_fin = $oActividad->getH_fin()?->format('H:i');
                $dl_org = $oActividad->getDl_org();
                $nom_activ = $oActividad->getNom_activ();

                $css = PlanningStyle::clase($id_tipo_activ, $propio, '', $oActividad->getStatus());

                $oTipoActividad = new TiposActividades($id_tipo_activ);
                $ssfsv = $oTipoActividad->getSfsvText();

                if ($oIniPlanning > $oF_ini) {
                    $ini = $inicio_local;
                    $hini = "1:16";
                } else {
                    $ini = (string)$oF_ini->getFromLocal();
                    $hini = (string)$h_ini;
                }
                $fi = (string)$oF_fin->getFromLocal();
                $hfi = (string)$h_fin;

                if (ConfigGlobal::is_app_installed('procesos')) {
                    $_SESSION['oPermActividades']->setActividad($id_activ, $id_tipo_activ, $dl_org);
                    $oPermActiv = $_SESSION['oPermActividades']->getPermisoActual('datos');

                    if ($oPermActiv->have_perm_activ('ocupado') === false) {
                        continue;
                    }
                    if ($oPermActiv->have_perm_activ('ver') === false) {
                        $nom_curt = $ssfsv;
                        $nom_llarg = "$ssfsv ($ini-$fi)";
                    } else {
                        $nom_curt = $oTipoActividad->getAsistentesText() . " " . $oTipoActividad->getActividadText();
                        $nom_llarg = $nom_activ;
                    }
                } else {
                    $nom_curt = $oTipoActividad->getAsistentesText() . " " . $oTipoActividad->getActividadText();
                    $nom_llarg = $nom_activ;
                }

                $aActivPersona[] = [
                    'nom_curt' => $nom_curt,
                    'nom_llarg' => $nom_llarg,
                    'f_ini' => $ini,
                    'h_ini' => $hini,
                    'f_fi' => $fi,
                    'h_fi' => $hfi,
                    'id_tipo_activ' => $id_tipo_activ,
                    'pagina' => '',
                    'id_activ' => $id_activ,
                    'propio' => $propio,
                    'css' => $css,
                ];
            }

            if ($agruparPorCentro) {
                $a_actividades2[$nombre_ubi][] = [$persona[$p] => $aActivPersona];
            } else {
                $a_actividades[] = [$persona[$p] => $aActivPersona];
            }
            $p++;
        }

        return $agruparPorCentro ? $a_actividades2 : $a_actividades;
    }
}
