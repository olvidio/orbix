<?php

namespace src\actividades\application;

use src\shared\config\ConfigGlobal;
use src\dossiers\application\PermisoDossier;
use src\permisos\domain\PermisosActividades;
use src\permisos\domain\PermisosActividadesTrue;

use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\actividades\domain\criteria\ActividadAllField;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\actividadescentro\domain\contracts\CentroEncargadoRepositoryInterface;
use src\actividadtarifas\domain\contracts\TipoTarifaRepositoryInterface;
use src\asistentes\application\services\AsistenteActividadService;
use src\ubis\domain\contracts\CasaDlRepositoryInterface;
use src\ubis\domain\criteria\CasaField;
use src\ubis\domain\contracts\CasaRepositoryInterface;
use src\ubis\application\services\CentroNombreResolver;
use src\ubis\domain\contracts\CentroRepositoryInterface;
use frontend\shared\web\Lista;
use frontend\shared\web\Periodo;
use src\actividades\domain\entity\TiposActividades;

/**
 * Caso de uso para la pantalla `calendario_listas`.
 *
 * Aglutina los accesos a repositorios y servicios necesarios para componer el
 * calendario de actividades de casas o de oficinas, en un periodo dado, y
 * devuelve el HTML listo para inyectar en el DOM.
 *
 * Devuelve:
 *   - html (string)  Lista paginada con el calendario, precedida de las
 *                    advertencias de actividades sin lugar asignado.
 */
final class CalendarioListasDatos
{
    public function __construct(
        private CasaDlRepositoryInterface $casaDlRepository,
        private TipoTarifaRepositoryInterface $tipoTarifaRepository,
        private ActividadRepositoryInterface $actividadRepository,
        private CentroEncargadoRepositoryInterface $centroEncargadoRepository,
        private CasaRepositoryInterface $casaRepository,
        private CentroRepositoryInterface $centroRepository,
        private CentroNombreResolver $centroNombreResolver,
        private AsistenteActividadService $asistenteActividadService,
    ) {
    }

    private const EQUIVALENCIAS_GM_OFICINA = [
        'n' => 'sm',
        'agd' => 'agd',
        's' => 'sg',
        'sg' => 'sg',
        'sss+' => 'des',
        'sr' => 'sr',
    ];

    /**
     * @param array<string, mixed> $input
     * @return array<string, mixed>
     */
    public function ejecutar(array $input): array
    {
        $Qque = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'que');
        $Qver_ctr = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'ver_ctr');
        $Qperiodo = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'periodo');
        $Qyear = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'year');
        $Qyeardefault = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'yeardefault');
        $Qempiezamin = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'empiezamin');
        $Qempiezamax = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'empiezamax');
        $Qaid_cdc = is_array($input['id_cdc'] ?? null) ? $input['id_cdc'] : [];

        $miSfsv = ConfigGlobal::mi_sfsv();
        $ver_ctr = empty($Qver_ctr) ? 'no' : $Qver_ctr;

        $criterioCasa = Criteria::create();
        $mi_of = '';
        $tipo = '';
        switch ($Qque) {
            case 'lista_cdc':
                $tipo = 'casa';
                if (!empty($Qaid_cdc)) {
                    $cdcIds = [];
                    foreach ($Qaid_cdc as $cdcId) {
                        if (is_scalar($cdcId)) {
                            $cdcIds[] = (int) $cdcId;
                        }
                    }
                    $criterioCasa = $criterioCasa->where(Filter::in(CasaField::IdUbi, $cdcIds));
                }
                break;
            case 'c_comunes':
            case 'c_comunes_sf':
            case 'c_comunes_sv':
                $tipo = 'casa';
                $criterioCasa = $criterioCasa->where(Filter::eq(CasaField::TipoUbi, 'cdcdl'))
                    ->andWhere(Filter::eq(CasaField::Sv, true))
                    ->andWhere(Filter::eq(CasaField::Sf, true));
                break;
            case 'c_todas':
                $tipo = 'casa';
                $criterioCasa = $criterioCasa->where(Filter::eq(CasaField::TipoUbi, 'cdcdl'));
                break;
            case 'c_todas_sf':
                $tipo = 'casa';
                $criterioCasa = $criterioCasa->where(Filter::eq(CasaField::TipoUbi, 'cdcdl'))
                    ->andWhere(Filter::eq(CasaField::Sf, true));
                break;
            case 'c_todas_sv':
                $tipo = 'casa';
                $criterioCasa = $criterioCasa->where(Filter::eq(CasaField::TipoUbi, 'cdcdl'))
                    ->andWhere(Filter::eq(CasaField::Sv, true));
                break;
            case 'o_actual':
                $tipo = 'oficina';
                $mi_of = ConfigGlobal::mi_oficina();
                break;
            case 'o_todas':
                $tipo = 'oficina';
                $mi_of = 'all';
                break;
            default:
                return [
                    'html' => sprintf(_("opción no definida en switch en %s, linea %s"), __FILE__, __LINE__),
                ];
        }

        $Qyeardefault = empty($Qyeardefault) ? 'next' : $Qyeardefault;
        $oPeriodo = new Periodo();
        $oPeriodo->setDefaultAny($Qyeardefault);
        $oPeriodo->setAny($Qyear);
        $oPeriodo->setEmpiezaMin($Qempiezamin);
        $oPeriodo->setEmpiezaMax($Qempiezamax);
        $oPeriodo->setPeriodo($Qperiodo);

        $inicioIso = $oPeriodo->getF_ini_iso();
        $finIso = $oPeriodo->getF_fin_iso();

        $aGrupos = [];
        $oTiposActividades = null;
        switch ($tipo) {
            case 'casa':
                $CasaDlRepository = $this->casaDlRepository;
                $cCasas = $CasaDlRepository->matching($criterioCasa);
                foreach ($cCasas as $oCasa) {
                    $aGrupos[$oCasa->getId_ubi()] = $oCasa->getNombre_ubi();
                }
                break;
            case 'oficina':
                $oTiposActividades = new TiposActividades();
                $oTiposActividades->setSfSvId($miSfsv);
                if ($mi_of === 'all') {
                    $aGrupos = $oTiposActividades->getAsistentesPosibles();
                } else {
                    $oPermisoOficinas = new PermisoDossier();
                    $aGrupos = $oTiposActividades->getAsistentesPosibles();
                    foreach ($aGrupos as $sasistentes) {
                        $oficina = self::EQUIVALENCIAS_GM_OFICINA[$sasistentes] ?? null;
                        if ($oficina !== null
                            && !$oPermisoOficinas->have_perm_oficina($oficina)
                            && ($key = array_search($sasistentes, $aGrupos)) !== false
                        ) {
                            unset($aGrupos[$key]);
                        }
                    }
                }
                break;
        }

        $warnings = '';
        $a_ubi_activ = [];
        $TipoTarifaRepository = $this->tipoTarifaRepository;
        $ActividadRepository = $this->actividadRepository;
        $CentroEncargadoRepository = $this->centroEncargadoRepository;
        $CasaRepository = $this->casaRepository;
        $CentroRepository = $this->centroRepository;
        $AsistenteActividadService = $this->asistenteActividadService;

        foreach (array_keys($aGrupos) as $key) {
            $criterioBase = Criteria::create()
                ->where(Filter::between(ActividadAllField::FIni, $inicioIso, $finIso))
                ->andWhere(Filter::lt(ActividadAllField::Status, 4));

            $cActividades = [];
            switch ($tipo) {
                case 'casa':
                    $cActividades = $ActividadRepository->matching(
                        $criterioBase
                            ->andWhere(Filter::eq(ActividadAllField::IdUbi, $key))
                            ->orderBy(ActividadAllField::IdUbi)
                            ->addOrderBy(ActividadAllField::FIni)
                    );
                    break;
                case 'oficina':
                    $criterioBase = $criterioBase->orderBy(ActividadAllField::FIni);
                    if ($mi_of === 'des') {
                        $cActividadesSSSC = $ActividadRepository->matching(
                            $criterioBase->andWhere(Filter::matches(ActividadAllField::IdTipoActiv, '^16'))
                        );
                        $cActividadesOtros = $ActividadRepository->matching(
                            $criterioBase->andWhere(Filter::matches(ActividadAllField::IdTipoActiv, '^1(124)|^1(.41)'))
                        );
                        $cActividades = array_merge($cActividadesOtros, $cActividadesSSSC);
                    } elseif ($oTiposActividades !== null) {
                        $oTiposActividades->setAsistentesId($key);
                        $cActividades = $ActividadRepository->matching(
                            $criterioBase->andWhere(Filter::matches(ActividadAllField::IdTipoActiv, $oTiposActividades->getNom_tipoRegexp()))
                        );
                    }
                    break;
            }

            if ($cActividades === []) {
                $a_ubi_activ[$key] = [];
                continue;
            }

            $a = 0;
            foreach ($cActividades as $oActividad) {
                $a++;
                $id_activ = $oActividad->getId_activ();
                $id_tipo_activ = $oActividad->getId_tipo_activ();
                $nom_activ = $oActividad->getNom_activ();
                $dl_org = $oActividad->getDl_org();
                $f_ini = $oActividad->getF_ini()?->getFromLocal();
                $f_fin = $oActividad->getF_fin()?->getFromLocal();
                $h_ini = $oActividad->getH_ini()?->format('H:i') ?? '';
                $h_fin = $oActividad->getH_fin()?->format('H:i') ?? '';
                $tarifa = $oActividad->getTarifa();

                $id_ubi = $oActividad->getId_ubi();
                $lugar_esp = $oActividad->getLugar_esp();
                $nombre_ubi = '';
                if (empty($lugar_esp)) {
                    if (empty($id_ubi)) {
                        $warnings .= sprintf(_("La actividad: %s  No tiene lugar asignado"), $nom_activ) . '<br>';
                    } else {
                        $nombre_ubi = $CasaRepository->findById($id_ubi)?->getNombre_ubi();
                        if (empty($nombre_ubi)) {
                            $nombre_ubi = $CentroRepository->findById($id_ubi)?->getNombre_ubi();
                        }
                    }
                } else {
                    $nombre_ubi = $lugar_esp;
                }

                $oTipoActiv = new TiposActividades($id_tipo_activ);
                $ssfsv = $oTipoActiv->getSfsvText();
                $sasistentes = $oTipoActiv->getAsistentesText();
                $sactividad = $oTipoActiv->getActividadText();
                $snom_tipo = $oTipoActiv->getNom_tipoText();

                if (ConfigGlobal::$dmz) {
                    $num_asistentes = '?';
                } else {
                    $cAsistentes = $AsistenteActividadService->getAsistentesDeActividad($id_activ);
                    $num_asistentes = count($cAsistentes);
                }

                if (ConfigGlobal::is_app_installed('procesos')) {
                    $oPermSesion = $_SESSION['oPermActividades'] ?? null;
                    if (!($oPermSesion instanceof PermisosActividades)) {
                        continue;
                    }
                    $oPermSesion->setActividad($id_activ, (string) $id_tipo_activ, $dl_org ?? '');
                    $oPermActiv = $oPermSesion->getPermisoActual('datos');
                    $oPermCtr = $oPermSesion->getPermisoActual('ctr');
                } else {
                    $oPermActividades = new PermisosActividadesTrue(ConfigGlobal::mi_id_usuario());
                    $oPermActiv = $oPermActividades->getPermisoActual('datos');
                    $oPermCtr = $oPermActividades->getPermisoActual('ctr');
                }

                if (!$oPermActiv->have_perm_action('ocupado')) {
                    continue;
                }

                if (!$oPermActiv->have_perm_action('ver')) {
                    $a_ubi_activ[$key][$a]['sfsv'] = $ssfsv;
                    $a_ubi_activ[$key][$a]['tipo_activ'] = _("ocupado");
                    if ($tipo === 'oficina') {
                        $a_ubi_activ[$key][$a]['cdc'] = "$nombre_ubi";
                    }
                    $a_ubi_activ[$key][$a]['fechas'] = "$f_ini - $f_fin";
                    $a_ubi_activ[$key][$a]['h_ini'] = $h_ini;
                    $a_ubi_activ[$key][$a]['h_fin'] = $h_fin;
                    $a_ubi_activ[$key][$a]['num_asistentes'] = '';
                    $a_ubi_activ[$key][$a]['id_tarifa'] = '';
                } else {
                    $a_ubi_activ[$key][$a]['sfsv'] = $ssfsv;
                    $a_ubi_activ[$key][$a]['tipo_activ'] = "$sasistentes $sactividad $snom_tipo";
                    if ($tipo === 'oficina') {
                        $a_ubi_activ[$key][$a]['cdc'] = "$nombre_ubi";
                    }
                    $a_ubi_activ[$key][$a]['fechas'] = "$f_ini - $f_fin";
                    $a_ubi_activ[$key][$a]['h_ini'] = $h_ini;
                    $a_ubi_activ[$key][$a]['h_fin'] = $h_fin;
                    $a_ubi_activ[$key][$a]['num_asistentes'] = $num_asistentes;
                    $letraTarifa = '';
                    if ($tarifa !== null) {
                        $oTipoTarifa = $TipoTarifaRepository->findById($tarifa);
                        $letraTarifa = $oTipoTarifa?->getLetra() ?? '';
                    }
                    $a_ubi_activ[$key][$a]['id_tarifa'] = $letraTarifa;
                }

                $a_ubi_activ[$key][$a]['ctr_encargados'] = '';
                if ($ver_ctr === 'si' && $oPermCtr->have_perm_action('ver')) {
                    $cCtrsEncargados = $CentroEncargadoRepository->getCentrosEncargados([
                        'id_activ' => $id_activ,
                        '_ordre' => 'num_orden',
                    ]);
                    $txt_ctr = '';
                    foreach ($cCtrsEncargados as $oCentroEncargado) {
                        $id_ubi_ctr = $oCentroEncargado->getId_ubi();
                        $nombre_ctr = $this->centroNombreResolver->nombre($id_ubi_ctr);
                        $txt_ctr .= empty($txt_ctr) ? $nombre_ctr : "; $nombre_ctr";
                    }
                    $a_ubi_activ[$key][$a]['ctr_encargados'] = $txt_ctr;
                }
            }
        }

        switch ($tipo) {
            case 'casa':
                $aCabeceras = [
                    ['name' => _("sv/sf"), 'field' => 'sfsv'],
                    ['name' => _("tipo actividad"), 'field' => 'tipo_activ'],
                    ['name' => _("fechas"), 'field' => 'fechas'],
                    ['name' => _("hora inicio"), 'field' => 'h_ini'],
                    ['name' => _("hora fin"), 'field' => 'h_fin'],
                    ['name' => _("asistentes previstos"), 'field' => 'num_asistentes'],
                    ['name' => _("id_tarifa"), 'field' => 'id_tarifa'],
                    ['name' => _("centros encargados"), 'field' => 'ctr_encargados'],
                ];
                break;
            case 'oficina':
                $aCabeceras = [
                    ['name' => _("sv/sf"), 'field' => 'sfsv'],
                    ['name' => _("tipo actividad"), 'field' => 'tipo_activ'],
                    ['name' => _("cdc"), 'field' => 'cdc'],
                    ['name' => _("fechas"), 'field' => 'fechas'],
                    ['name' => _("hora inicio"), 'field' => 'h_ini'],
                    ['name' => _("hora fin"), 'field' => 'h_fin'],
                    ['name' => _("asistentes previstos"), 'field' => 'num_asistentes'],
                    ['name' => _("id_tarifa"), 'field' => 'id_tarifa'],
                    ['name' => _("centros encargados"), 'field' => 'ctr_encargados'],
                ];
                break;
            default:
                $aCabeceras = [];
        }

        $oTabla = new Lista();
        $oTabla->setGrupos($aGrupos);
        $oTabla->setCabeceras($aCabeceras);
        $oTabla->setDatos($a_ubi_activ);
        $html = $warnings . $oTabla->listaPaginada();

        return [
            'html' => $html,
        ];
    }
}
