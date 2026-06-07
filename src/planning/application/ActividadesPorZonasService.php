<?php

namespace src\planning\application;

use frontend\shared\web\Desplegable;
use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividadcargos\domain\contracts\CargoRepositoryInterface;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\actividades\domain\entity\TiposActividades;
use src\actividades\domain\value_objects\StatusId;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdHorarioRepositoryInterface;
use src\permisos\domain\PermisosActividades;
use src\permisos\domain\PermisosActividadesTrue;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\planning\domain\value_objects\PlanningStyle;
use src\shared\config\ConfigGlobal;
use src\shared\domain\value_objects\DateTimeLocal;
use src\zonassacd\domain\contracts\ZonaRepositoryInterface;
use src\zonassacd\domain\contracts\ZonaSacdRepositoryInterface;
use function src\shared\domain\helpers\is_true;

/**
 * Devuelve las actividades agrupadas por zona sacd en un periodo dado.
 */
class ActividadesPorZonasService
{
    public function __construct(
        private CargoRepositoryInterface $cargoRepository,
        private ZonaRepositoryInterface $zonaRepository,
        private ZonaSacdRepositoryInterface $zonaSacdRepository,
        private ActividadRepositoryInterface $actividadRepository,
        private PersonaSacdRepositoryInterface $personaSacdRepository,
        private EncargoRepositoryInterface $encargoRepository,
        private EncargoSacdHorarioRepositoryInterface $encargoSacdHorarioRepository,
        private ActividadCargoRepositoryInterface $actividadCargoRepository,
    ) {
    }

    /**
     * @return array{
     *     actividades_por_zona: array<int, array<int|string, mixed>>,
     *     cabeceras_por_zona: array<int, string>,
     *     zonas: int,
     *     titulo: string,
     *     oIniPlanning: DateTimeLocal,
     *     oFinPlanning: DateTimeLocal,
     * }
     */
    public function execute(
        string $Qid_zona,
        int $Qtrimestre,
        int $Qyear,
        string $Qactividad,
        string $Qpropuesta,
        ?int $id_nom_jefe = null
    ): array {
        $this->cargoRepository->getArrayCargos('sacd');

        $year = empty($Qyear) ? (int)date('Y') + 1 : $Qyear;
        [$ini_trim, $fin_trim] = $this->rangoTrimestre($Qtrimestre);

        $inicio_iso = $year . "/" . $ini_trim;
        $fin_iso = ($Qtrimestre === 5 ? $year + 1 : $year) . "/" . $fin_trim;

        $oIniPlanning = DateTimeLocal::createFromFormat('Y/m/d', $inicio_iso);
        $oFinPlanning = DateTimeLocal::createFromFormat('Y/m/d', $fin_iso);
        if ($oIniPlanning === false || $oFinPlanning === false) {
            throw new \RuntimeException(_('Rango de fechas de planning no válido'));
        }
        $inicio_local = $oIniPlanning->getFromLocal();

        $aa_zonas = $this->zonasAIterar($Qid_zona, $id_nom_jefe);

        $z = 0;
        $actividades_por_zona = [];
        $cabeceras_por_zona = [];

        foreach ($aa_zonas as $a_zonas) {
            $z++;
            $id_zona = $a_zonas['id_zona'];
            $nombre_zona = $a_zonas['nombre_zona'];
            $cZonasSacd = $this->zonaSacdRepository->getZonasSacds(['id_zona' => $id_zona]);
            $p = 0;
            $actividades = [];
            $persona = [];
            foreach ($cZonasSacd as $oZonaSacd) {
                $aActivPersona = [];
                $id_nom = $oZonaSacd->getId_nom();
                $oSacd = $this->personaSacdRepository->findById($id_nom);
                if ($oSacd === null || $oSacd->getSituacion() !== 'A') {
                    continue;
                }

                $ap_nom = $oSacd->getPrefApellidosNombre();
                $persona[$p] = "p#$id_nom#$ap_nom";

                if ($Qactividad === 'si') {
                    $aActivPersona = array_merge(
                        $aActivPersona,
                        $this->actividadesDeSacd(
                            $id_nom,
                            $fin_iso,
                            $inicio_iso,
                            $inicio_local,
                            $oIniPlanning,
                            $Qpropuesta
                        )
                    );
                    $aActivPersona = array_merge(
                        $aActivPersona,
                        $this->ausenciasDeSacd(
                            $id_nom,
                            $fin_iso,
                            $inicio_iso,
                            $inicio_local,
                            $oIniPlanning
                        )
                    );
                    $actividades[$ap_nom] = [$persona[$p] => $aActivPersona];
                    $p++;
                } else {
                    $actividades[$ap_nom] = [$persona[$p] => []];
                    $p++;
                }
            }
            uksort($actividades, "strnatcasecmp");
            $actividades[] = ['###' => []];

            $actividades_por_zona[$z] = $actividades;
            $cabeceras_por_zona[$z] = $nombre_zona;
        }

        $titulo = $z === 1 ? $cabeceras_por_zona[1] : _("planning por zonas");

        return [
            'actividades_por_zona' => $actividades_por_zona,
            'cabeceras_por_zona' => $cabeceras_por_zona,
            'zonas' => $z,
            'titulo' => $titulo,
            'oIniPlanning' => $oIniPlanning,
            'oFinPlanning' => $oFinPlanning,
        ];
    }

    /** @return array{0:string,1:string} */
    private function rangoTrimestre(int $Qtrimestre): array
    {
        return match ($Qtrimestre) {
            1 => ["1/1", "3/31"],
            2 => ["4/1", "6/30"],
            3 => ["7/1", "9/30"],
            4 => ["10/1", "12/31"],
            5 => ["12/1", "1/31"],
            6 => ["7/1", "8/31"],
            101 => ["1/1", "1/31"],
            102 => ["2/1", "3/1"],
            103 => ["3/1", "3/31"],
            104 => ["4/1", "4/30"],
            105 => ["5/1", "5/31"],
            106 => ["6/1", "6/30"],
            107 => ["7/1", "7/31"],
            108 => ["8/1", "8/31"],
            109 => ["9/1", "9/30"],
            110 => ["10/1", "10/31"],
            111 => ["11/1", "11/30"],
            112 => ["12/1", "12/31"],
            default => ["1/1", "12/31"],
        };
    }

    /** @return array<int,array{id_zona:int|string, nombre_zona:string}> */
    private function zonasAIterar(string $Qid_zona, ?int $id_nom_jefe): array
    {
        if ($Qid_zona === 'todo_propias') {
            $cZonasSacd = $this->zonaSacdRepository->getZonasSacds(['propia' => 't']);
            $a_zonas = [];
            $a_zonas_o = [];
            foreach ($cZonasSacd as $oZonaSacd) {
                $id_zona = $oZonaSacd->getId_zona();
                if (array_key_exists($id_zona, $a_zonas)) {
                    continue;
                }
                $oZona = $this->zonaRepository->findById((int)$id_zona);
                if ($oZona === null) {
                    continue;
                }
                $a_zonas[$id_zona] = $oZona->getNombre_zona();
                $a_zonas_o[$id_zona] = $oZona->getOrden();
            }
            asort($a_zonas_o);
            $aa = [];
            foreach ($a_zonas_o as $id_zona => $orden) {
                $aa[] = ['id_zona' => $id_zona, 'nombre_zona' => $a_zonas[$id_zona]];
            }
            return $aa;
        }

        if ($Qid_zona === 'todo') {
            $aOpciones = $this->zonaRepository->getArrayZonas($id_nom_jefe);
            $oDesplZonas = new Desplegable();
            $oDesplZonas->setOpciones($aOpciones);
            $oDesplZonas->setBlanco(false);
            return $oDesplZonas->getOpciones()->fetchAll(\PDO::FETCH_ASSOC);
        }

        $oZona = $this->zonaRepository->findById((int)$Qid_zona);
        if ($oZona === null) {
            return [];
        }
        return [[
            'id_zona' => $Qid_zona,
            'nombre_zona' => $oZona->getNombre_zona(),
        ]];
    }

    /**
     * @return array<int,array<string, mixed>>
     */
    private function actividadesDeSacd(
        int|string $id_nom,
        string $fin_iso,
        string $inicio_iso,
        string $inicio_local,
        DateTimeLocal $oIniPlanning,
        string $Qpropuesta
    ): array {
        $aActivPersona = [];
        $aWhereAct = [
            'f_ini' => "'$fin_iso'",
            'f_fin' => "'$inicio_iso'",
        ];
        $aOperadorAct = [
            'f_ini' => '<=',
            'f_fin' => '>=',
        ];
        if (!is_true($Qpropuesta)) {
            $aWhereAct['status'] = StatusId::ACTUAL;
        } else {
            $aWhereAct['status'] = StatusId::BORRABLE;
            $aOperadorAct['status'] = '!=';
        }

        $cAsistentes = $this->actividadCargoRepository->getAsistenteCargoDeActividad(
            ['id_nom' => $id_nom],
            [],
            $aWhereAct,
            $aOperadorAct
        );

        foreach ($cAsistentes as $aAsistente) {
            $id_activRaw = $aAsistente['id_activ'] ?? null;
            $id_activ = is_numeric($id_activRaw) ? (int)$id_activRaw : 0;
            $propio = is_bool($aAsistente['propio']) ? $aAsistente['propio'] : is_true($aAsistente['propio']);
            $plazaRaw = $aAsistente['plaza'] ?? null;
            $plaza = is_int($plazaRaw) || is_string($plazaRaw) ? $plazaRaw : null;
            $idCargoRaw = $aAsistente['id_cargo'] ?? null;
            $id_cargo = is_scalar($idCargoRaw) && $idCargoRaw !== '' ? (string)$idCargoRaw : '';

            $aWhereAct['id_activ'] = $id_activ;
            $cActividades = $this->actividadRepository->getActividades($aWhereAct, $aOperadorAct);
            if (count($cActividades) === 0) {
                continue;
            }

            $oActividad = $cActividades[0];
            $id_tipo_activ = $oActividad->getId_tipo_activ();
            $oF_ini = $oActividad->getF_ini();
            $oF_fin = $oActividad->getF_fin();
            $h_ini = $oActividad->getH_ini()?->format('H:i');
            $h_fin = $oActividad->getH_fin()?->format('H:i');
            $dl_org = $oActividad->getDl_org();
            $nom_activ = $oActividad->getNom_activ();

            $oTipoActividad = new TiposActividades($id_tipo_activ);
            $ssfsv = $oTipoActividad->getSfsvText();

            if ($oF_ini !== null && $oIniPlanning > $oF_ini) {
                $ini = $inicio_local;
                $hini = "1:16";
            } else {
                $ini = (string)($oF_ini?->getFromLocal() ?? $inicio_local);
                $hini = (string)$h_ini;
            }
            $fi = (string)($oF_fin?->getFromLocal() ?? '');
            $hfi = (string)$h_fin;

            if (is_true($Qpropuesta)) {
                $nom_curt = $oTipoActividad->getAsistentesText() . " " . $oTipoActividad->getActividadText();
                $nom_llarg = $nom_activ;
            } else {
                if (ConfigGlobal::is_app_installed('procesos')) {
                    $oPermSesion = $_SESSION['oPermActividades'] ?? null;
                    if ($oPermSesion instanceof PermisosActividades) {
                        $oPermSesion->setActividad($id_activ, (string)$id_tipo_activ, $dl_org);
                        $permiso_ver = $oPermSesion->havePermisoSacd(
                            $id_cargo === '' ? null : (int)$id_cargo,
                            $propio
                        );
                    } else {
                        $permiso_ver = true;
                    }
                } else {
                    $permiso_ver = true;
                }

                if ($permiso_ver === false) {
                    continue;
                }

                $oPermSesion = $_SESSION['oPermActividades'] ?? null;
                if ($oPermSesion instanceof PermisosActividades) {
                    $oPermSesion->setActividad($id_activ, (string)$id_tipo_activ, $dl_org);
                    $oPermActiv = $oPermSesion->getPermisoActual('datos');
                    if ($oPermActiv->have_perm_activ('ver') === false) {
                        $nom_curt = $ssfsv;
                        $nom_llarg = "$ssfsv ($ini-$fi)";
                    } else {
                        $nom_curt = $oTipoActividad->getAsistentesText() . " " . $oTipoActividad->getActividadText();
                        $nom_llarg = $nom_activ;
                    }
                } else {
                    $oPermActividades = new PermisosActividadesTrue(ConfigGlobal::mi_id_usuario());
                    $oPermActiv = $oPermActividades->getPermisoActual('datos');
                    if ($oPermActiv->have_perm_activ('ver') === false) {
                        $nom_curt = $ssfsv;
                        $nom_llarg = "$ssfsv ($ini-$fi)";
                    } else {
                        $nom_curt = $oTipoActividad->getAsistentesText() . " " . $oTipoActividad->getActividadText();
                        $nom_llarg = $nom_activ;
                    }
                }
            }

            $css = PlanningStyle::clase($id_tipo_activ, $propio, $plaza, $oActividad->getStatus());

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
                'plaza' => $plaza,
                'css' => $css,
            ];
        }
        return $aActivPersona;
    }

    /**
     * @return array<int,array<string, mixed>>
     */
    private function ausenciasDeSacd(
        int|string $id_nom,
        string $fin_iso,
        string $inicio_iso,
        string $inicio_local,
        DateTimeLocal $oIniPlanning
    ): array {
        $aActivPersona = [];
        $aWhereE = [
            'id_nom' => $id_nom,
            'f_ini' => "'$fin_iso'",
            'f_fin' => "'$inicio_iso'",
        ];
        $aOperadorE = [
            'f_ini' => '<=',
            'f_fin' => '>=',
        ];
        $cAusencias = $this->encargoSacdHorarioRepository->getEncargoSacdHorarios($aWhereE, $aOperadorE);
        foreach ($cAusencias as $oTareaHorarioSacd) {
            $id_enc = $oTareaHorarioSacd->getId_enc();
            $oF_ini = $oTareaHorarioSacd->getF_ini();
            $oF_fin = $oTareaHorarioSacd->getF_fin();

            try {
                $oEncargo = $this->encargoRepository->findById($id_enc);
            } catch (\InvalidArgumentException) {
                continue;
            }
            if ($oEncargo === null) {
                continue;
            }
            $id_tipo_enc = $oEncargo->getId_tipo_enc();
            $id = (string)$id_tipo_enc;
            if ($id[0] !== '7' && $id[0] !== '4') {
                continue;
            }

            if ($oF_ini !== null && $oIniPlanning > $oF_ini) {
                $ini = $inicio_local;
                $hini = '5:00';
            } else {
                $ini = (string)($oF_ini?->getFromLocal() ?? $inicio_local);
                $hini = '5:00';
            }
            $fi = (string)($oF_fin?->getFromLocal() ?? '');
            $hfi = '22:00';

            $propio = "p";
            $nom_llarg = (string)($oEncargo->getDesc_enc() ?? '');
            if ($nom_llarg === '') {
                continue;
            }
            $nom_curt = ($nom_llarg[0] === 'A') ? 'a' : 'x';
            if ($ini !== $fi) {
                $nom_llarg .= " ($ini-$fi)";
            } else {
                $nom_llarg .= " ($ini)";
            }

            $aActivPersona[] = [
                'nom_curt' => $nom_curt,
                'nom_llarg' => $nom_llarg,
                'f_ini' => $ini,
                'h_ini' => $hini,
                'f_fi' => $fi,
                'h_fi' => $hfi,
                'id_tipo_activ' => null,
                'pagina' => '',
                'id_activ' => $id_enc,
                'propio' => $propio,
            ];
        }
        return $aActivPersona;
    }
}
