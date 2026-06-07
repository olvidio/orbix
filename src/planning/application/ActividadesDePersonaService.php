<?php

namespace src\planning\application;

use src\actividadcargos\domain\contracts\CargoOAsistenteInterface;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\actividades\domain\entity\TiposActividades;
use src\personas\domain\entity\PersonaEx;
use src\personas\domain\entity\PersonaGlobal;
use src\personas\domain\entity\PersonaSacd;
use src\permisos\domain\PermisosActividades;
use src\permisos\domain\PermisosActividadesTrue;
use src\planning\domain\value_objects\PlanningStyle;
use src\shared\config\ConfigGlobal;
use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;

/**
 * Recoge las actividades de cada persona dentro de un periodo dado.
 */
class ActividadesDePersonaService
{
    public function __construct(
        private ActividadRepositoryInterface $actividadRepository,
        private CentroDlRepositoryInterface $centroDlRepository,
        private CargoOAsistenteInterface $cargoOAsistente,
    ) {
    }

    /**
     * @param iterable<PersonaGlobal|PersonaSacd|PersonaEx>|bool $cPersonas
     * @return array<int|string, array<int, array<string, list<array<string, mixed>>>>>|list<array<string, list<array<string, mixed>>>>
     */
    public function actividadesPorPersona(
        iterable|bool $cPersonas,
        string $fin_iso,
        string $inicio_iso,
        DateTimeLocal $oIniPlanning,
        string $inicio_local,
        bool $agruparPorCentro = true
    ): array {
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
                } elseif (!array_key_exists($id_ubi, $aListaCtr)) {
                    $oCentroDl = $this->centroDlRepository->findById($id_ubi);
                    $nombre_ubi = $oCentroDl?->getNombre_ubi() ?? _("centro?");
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
                $cCargoOAsistente = $this->cargoOAsistente->getCargoOAsistente((int)$id_nom);
            }
            foreach ($cCargoOAsistente as $oCargoOAsistente) {
                $id_activ = $oCargoOAsistente->getId_activ();
                $propio = $oCargoOAsistente->isPropio();

                $aWhere['id_activ'] = $id_activ;
                $cActividades = $this->actividadRepository->getActividades($aWhere, $aOperador);
                if (count($cActividades) === 0) {
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
                $fi = (string)($oF_fin?->getFromLocal() ?? '');
                $hfi = (string)$h_fin;

                if (ConfigGlobal::is_app_installed('procesos')) {
                    $oPermSesion = $_SESSION['oPermActividades'] ?? null;
                    if ($oPermSesion instanceof PermisosActividades) {
                        $oPermSesion->setActividad($id_activ, (string)$id_tipo_activ, $dl_org);
                        $oPermActiv = $oPermSesion->getPermisoActual('datos');
                    } else {
                        $oPermActividades = new PermisosActividadesTrue(ConfigGlobal::mi_id_usuario());
                        $oPermActiv = $oPermActividades->getPermisoActual('datos');
                    }

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
