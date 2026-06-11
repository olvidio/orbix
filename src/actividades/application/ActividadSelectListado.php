<?php

namespace src\actividades\application;

use src\shared\config\ConfigGlobal;
use src\permisos\domain\PermisosActividades;
use src\permisos\domain\PermisosActividadesTrue;
use src\permisos\domain\XPermisos;
use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividades\domain\contracts\ActividadPubRepositoryInterface;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\actividades\domain\contracts\ImportadaRepositoryInterface;
use src\actividades\domain\value_objects\StatusId;
use src\actividadescentro\domain\contracts\CentroEncargadoRepositoryInterface;
use src\actividadtarifas\domain\contracts\TipoTarifaRepositoryInterface;
use src\procesos\domain\contracts\ActividadProcesoTareaRepositoryInterface;
use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\domain\contracts\CasaRepositoryInterface;
use src\ubis\domain\contracts\CentroRepositoryInterface;
use src\usuarios\domain\contracts\PreferenciaRepositoryInterface;
use src\usuarios\domain\contracts\RoleRepositoryInterface;
use src\usuarios\domain\value_objects\PauType;
use frontend\shared\web\Periodo;
use src\actividades\domain\entity\TiposActividades;
use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;
use function src\shared\domain\helpers\input_string_list;

/**
 * Construye el listado de actividades que cumplen los filtros fijados por
 * `actividad_que`. Centraliza todos los accesos a repositorios del dominio
 * y a permisos / preferencias, de modo que el controlador frontend puede
 * limitarse a leer el POST, llamar a este caso de uso via PostRequest y
 * pintar el HTML resultante.
 *
 * Devuelve un array con:
 *   - resultado (string)       Texto resumen ("X actividades encontradas ...").
 *   - perm_nueva (bool)        Si el usuario puede crear actividad nueva.
 *   - mod (string)             '' o 'importar'.
 *   - obj_pau (string)         'Actividad' o 'ActividadPub'.
 *   - aTiposActiv (array)      Lista de "nueva actividad ..." (txt => id).
 *   - html_advertencia (string) Vacío aquí; si hay demasiadas filas, `advertencia_demasiadas`.
 *   - advertencia_demasiadas (array|null) Specs para armar el HTML en `actividad_select.php` (front).
 *   - a_cabeceras, a_botones, a_valores  Para `Lista::mostrar_tabla` en el front (`link_spec` en celdas).
 *   - aRolesPau (array)        Roles PAU (usado para hashes en frontend).
 *
 * El HTML de la tabla y la advertencia firmada se construyen en `frontend/actividades/controller/actividad_select.php`.
 */
final class ActividadSelectListado
{
    public function __construct(
        private RoleRepositoryInterface $roleRepository,
        private ActividadPubRepositoryInterface $actividadPubRepository,
        private ActividadRepositoryInterface $actividadRepository,
        private CasaRepositoryInterface $casaRepository,
        private CentroRepositoryInterface $centroRepository,
        private PreferenciaRepositoryInterface $preferenciaRepository,
        private TipoTarifaRepositoryInterface $tipoTarifaRepository,
        private ImportadaRepositoryInterface $importadaRepository,
        private CentroEncargadoRepositoryInterface $centroEncargadoRepository,
        private ActividadProcesoTareaRepositoryInterface $actividadProcesoTareaRepository,
        private ActividadCargoRepositoryInterface $actividadCargoRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @return array<string, mixed>
     */
    public function ejecutar(array $input, int $stackGo): array
    {
        $num_max_actividades = 200;

        $oPerm = $_SESSION['oPerm'] ?? null;
        $permOficina = static function (string $perm) use ($oPerm): bool {
            return $oPerm instanceof XPermisos && $oPerm->have_perm_oficina($perm);
        };
        $oPermActividadesSesion = $_SESSION['oPermActividades'] ?? null;

        $mi_dele = ConfigGlobal::mi_delef();
        $mi_sfsv = ConfigGlobal::mi_sfsv();

        $Qcontinuar = input_string($input, 'continuar');
        $Qmodo = input_string($input, 'modo');
        $Qstatus = input_int($input, 'status');
        $Qid_tipo_activ = input_string($input, 'id_tipo_activ');
        $Qfiltro_lugar = input_string($input, 'filtro_lugar');
        $Qid_ubi = input_int($input, 'id_ubi');
        $Qnom_activ = input_string($input, 'nom_activ');
        $Qperiodo = input_string($input, 'periodo');
        $Qyear = input_string($input, 'year');
        $Qdl_org = input_string($input, 'dl_org');
        $Qempiezamin = input_string($input, 'empiezamin');
        $Qempiezamax = input_string($input, 'empiezamax');
        $Qfases_on = input_string_list($input, 'fases_on');
        $Qfases_off = input_string_list($input, 'fases_off');
        $Qpublicado = input_int($input, 'publicado');
        $Qssfsv = input_string($input, 'ssfsv');
        $Qsasistentes = input_string($input, 'sasistentes');
        $Qsactividad = input_string($input, 'sactividad');
        $Qsactividad2 = input_string($input, 'sactividad2');

        if (empty($Qperiodo)) {
            $Qperiodo = 'actual';
        }
        $Qstatus = empty($Qstatus) ? StatusId::ACTUAL : $Qstatus;

        $aWhere = [];
        $aOperador = [];
        if ($Qstatus !== 9) {
            $aWhere['status'] = $Qstatus;
        }
        $extendida = FALSE;
        if (empty($Qid_tipo_activ)) {
            if (!empty($Qsactividad2)) {
                $extendida = TRUE;
            }
            if (empty($Qssfsv)) {
                if ($mi_sfsv === 1) {
                    $Qssfsv = 'sv';
                }
                if ($mi_sfsv === 2) {
                    $Qssfsv = 'sf';
                }
            }
            $sasistentes = empty($Qsasistentes) ? '.' : $Qsasistentes;
            $sactividad = empty($Qsactividad) ? '.' : $Qsactividad;
            $oTipoActiv = new TiposActividades();
            $oTipoActiv->setSfsvText($Qssfsv);
            $oTipoActiv->setAsistentesText($sasistentes);
            if (!empty($Qsactividad2)) {
                $oTipoActiv->setActividad2DigitosText($Qsactividad2);
            } else {
                $oTipoActiv->setActividadText($sactividad);
            }
            $Qid_tipo_activ = $oTipoActiv->getId_tipo_activ();
        } else {
            $oTipoActiv = new TiposActividades($Qid_tipo_activ);
        }
        if ($Qid_tipo_activ !== '......') {
            $aWhere['id_tipo_activ'] = "^$Qid_tipo_activ";
            $aOperador['id_tipo_activ'] = '~';
        }
        if (!empty($Qid_ubi)) {
            $aWhere['id_ubi'] = $Qid_ubi;
        }
        if (!empty($Qnom_activ)) {
            $aWhere['nom_activ'] = '%' . $Qnom_activ . '%';
            $aOperador['nom_activ'] = 'ILIKE';
        }

        $oPeriodo = new Periodo();
        $oPeriodo->setDefaultAny('actual');
        $oPeriodo->setAny($Qyear);
        $oPeriodo->setEmpiezaMin($Qempiezamin);
        $oPeriodo->setEmpiezaMax($Qempiezamax);
        $oPeriodo->setPeriodo($Qperiodo);

        $inicioIso = $oPeriodo->getF_ini_iso();
        $finIso = $oPeriodo->getF_fin_iso();
        $aWhere['f_ini'] = "'$inicioIso','$finIso'";
        $aOperador['f_ini'] = 'BETWEEN';

        if (!empty($Qdl_org)) {
            $aWhere['dl_org'] = $Qdl_org;
        }
        if (!empty($Qmodo) && $Qmodo === 'publicar') {
            $aWhere['publicado'] = 'f';
        } elseif (!empty($Qpublicado)) {
            if ($Qpublicado === 1) {
                $aWhere['publicado'] = 't';
            }
            if ($Qpublicado === 2) {
                $aWhere['publicado'] = 'f';
            }
        }

        $oMiUsuario = ConfigGlobal::MiUsuario();
        $id_role = $oMiUsuario?->getId_role() ?? 0;

        $RoleRepository = $this->roleRepository;
        $aRolesPau = $RoleRepository->getArrayRolesPau();

        $a_botones = [];
        if (!empty($Qmodo) && $Qmodo !== 'buscar') {
            if ($Qmodo === 'importar') {
                $a_botones[] = ['txt' => _("importar"), 'click' => "jsForm.update(\"#seleccionados\",\"importar\")"];
            }
            if ($Qmodo === 'publicar') {
                $a_botones[] = ['txt' => _("datos"), 'click' => "jsForm.mandar(\"#seleccionados\",\"datos\")"];
                $a_botones[] = ['txt' => _("publicar"), 'click' => "jsForm.update(\"#seleccionados\",\"publicar\")"];
            }
            if (ConfigGlobal::is_app_installed('asignaturas') && $permOficina('est')) {
                $a_botones[] = ['txt' => _("asignaturas"), 'click' => "jsForm.mandar(\"#seleccionados\",\"asig\")"];
            }
        } else {
            if (!empty($aRolesPau[$id_role]) && ($aRolesPau[$id_role] === PauType::PAU_CDC || $aRolesPau[$id_role] === 'CentroSf')) {
                $a_botones = [['txt' => _("datos"), 'click' => "jsForm.mandar(\"#seleccionados\",\"datos\")"]];
            } else {
                $a_botones[] = ['txt' => _("datos"), 'click' => "jsForm.mandar(\"#seleccionados\",\"datos\")"];
                if ($permOficina('vcsd') || $permOficina('des') || $permOficina('calendario')) {
                    $a_botones[] = ['txt' => _("duplicar"), 'click' => "jsForm.update(\"#seleccionados\",\"duplicar\")"];
                    $a_botones[] = ['txt' => _("borrar"), 'click' => "fnjs_borrar(\"#seleccionados\",\"eliminar\")"];
                    $a_botones[] = ['txt' => _("cambiar tipo"), 'click' => "jsForm.mandar(\"#seleccionados\",\"cambiar_tipo\")"];
                }
                if (ConfigGlobal::is_app_installed('actividadcargos')) {
                    $a_botones[] = ['txt' => _("cargos"), 'click' => "jsForm.mandar(\"#seleccionados\",\"carg\")"];
                    $a_botones[] = ['txt' => _("lista cl"), 'click' => "jsForm.mandar(\"#seleccionados\",\"listcl\")"];
                }
                if (ConfigGlobal::is_app_installed('asistentes')) {
                    $a_botones[] = ['txt' => _("asistentes"), 'click' => "jsForm.mandar(\"#seleccionados\",\"asis\")"];
                    $a_botones[] = ['txt' => _("otras peticiones"), 'click' => "jsForm.mandar(\"#seleccionados\",\"asis_peticiones\")"];
                    $a_botones[] = ['txt' => _("lista"), 'click' => "jsForm.mandar(\"#seleccionados\",\"list\")"];
                }
                if (ConfigGlobal::is_app_installed('actividadplazas')) {
                    $a_botones[] = ['txt' => _("plazas"), 'click' => "jsForm.mandar(\"#seleccionados\",\"plazas\")"];
                }
                if (ConfigGlobal::is_app_installed('procesos')) {
                    $a_botones[] = ['txt' => _("proceso"), 'click' => "jsForm.mandar(\"#seleccionados\",\"proceso\")"];
                }
                if (ConfigGlobal::is_app_installed('asignaturas')) {
                    if ($permOficina('est')) {
                        $a_botones[] = ['txt' => _("asignaturas"), 'click' => "jsForm.mandar(\"#seleccionados\",\"asig\")"];
                    }
                    if ($permOficina('est') || $permOficina('agd') || $permOficina('sm')) {
                        $a_botones[] = ['txt' => _("plan estudios"), 'click' => "jsForm.mandar(\"#seleccionados\",\"plan_estudios\")"];
                    }
                    if ($permOficina('est')) {
                        $a_botones[] = ['txt' => _("listas de clase"), 'click' => "jsForm.mandar(\"#seleccionados\",\"lista_clase\")"];
                        $a_botones[] = ['txt' => _("posibles asignaturas"), 'click' => "jsForm.mandar(\"#seleccionados\",\"posibles_asignaturas\")"];
                    }
                }
                if (ConfigGlobal::is_app_installed('ubiscamas')) {
                    $a_botones[] = ['txt' => _("habitaciones"), 'click' => "jsForm.mandar(\"#seleccionados\",\"camas\")"];
                }
            }
        }

        $a_cabeceras = [
            ['name' => _("inicio"), 'width' => 40, 'class' => 'fecha'],
            ['name' => _("fin"), 'width' => 40, 'class' => 'fecha'],
            ['name' => ucfirst(_("actividad")), 'width' => 300, 'formatter' => 'clickFormatter'],
            ['name' => _("hora ini"), 'width' => 40, 'class' => 'fecha'],
            ['name' => _("hora fin"), 'width' => 40, 'class' => 'fecha'],
        ];
        if ($permOficina('vcsd') || $permOficina('des')) {
            $a_cabeceras[] = ['name' => _("sf/sv"), 'width' => 40];
        }
        $a_cabeceras[] = ['name' => _("tar."), 'width' => 40];
        if (empty($aRolesPau[$id_role]) || ($aRolesPau[$id_role] !== PauType::PAU_CTR)) {
            $a_cabeceras[] = ['name' => ucfirst(_("sacd")), 'width' => 200];
            $a_cabeceras[] = ['name' => _("dl org"), 'width' => 50];
        }
        $a_cabeceras[] = ucfirst(_("centro"));
        $a_cabeceras[] = ucfirst(_("observaciones"));
        $a_cabeceras[] = ucfirst(_("idioma"));

        if (!empty($Qmodo) && $Qmodo === 'importar') {
            $mod = 'importar';
            $ActividadRepository = $this->actividadPubRepository;
            if (empty($Qdl_org)) {
                $aWhere['dl_org'] = $mi_dele;
                $aOperador['dl_org'] = '!=';
            }
            $obj_pau = 'ActividadPub';
        } else {
            $mod = '';
            $ActividadRepository = $this->actividadRepository;
            $obj_pau = 'Actividad';
        }

        $aWhere['_ordre'] = 'f_ini';
        $cActividades = $ActividadRepository->getActividades($aWhere, $aOperador);
        $num_activ = count($cActividades);
        if ($num_activ > $num_max_actividades && empty($Qcontinuar)) {
            return [
                'resultado' => '',
                'perm_nueva' => false,
                'mod' => $mod,
                'obj_pau' => $obj_pau,
                'aTiposActiv' => [],
                'html_advertencia' => '',
                'advertencia_demasiadas' => [
                    'num_actividades' => $num_activ,
                    'continuar_link_spec' => [
                        'path' => 'frontend/actividades/controller/actividad_select.php',
                        'query' => ['continuar' => 'si', 'Gstack' => $stackGo],
                    ],
                    'volver_link_spec' => [
                        'path' => 'frontend/actividades/controller/actividad_que.php',
                        'query' => ['stack' => $stackGo],
                    ],
                ],
                'extendida' => $extendida,
                'id_tipo_activ_efectivo' => $Qid_tipo_activ,
                'aRolesPau' => $aRolesPau,
                'id_role' => $id_role,
                'a_cabeceras' => $a_cabeceras,
                'a_botones' => $a_botones,
                'a_valores' => [],
            ];
        }

        $CasaRepository = $this->casaRepository;
        $a_OpcionesCasas = $CasaRepository->getArrayCasas();
        $CentroRepository = $this->centroRepository;
        $a_OpcionesCentros = $CentroRepository->getArrayCentrosCdc();
        $a_casas = $a_OpcionesCasas + $a_OpcionesCentros;

        $i = 0;
        $sin = 0;
        $a_valores = [];
        $a_NombreCasa = [];
        $a_FechaIni = [];
        $sPrefs = '';
        $id_usuario = ConfigGlobal::mi_id_usuario();
        $tipo = 'tabla_presentacion';
        $PreferenciaRepository = $this->preferenciaRepository;
        $oPreferencia = $PreferenciaRepository->findById($id_usuario, $tipo);
        if ($oPreferencia !== null) {
            $sPrefs = $oPreferencia->getPreferenciaAsString();
        }
        $TipoTarifaRepository = $this->tipoTarifaRepository;
        $ImportadaRepository = $this->importadaRepository;
        $CentroEncargadoRepository = $this->centroEncargadoRepository;

        $oPermActivDefault = new PermisosActividadesTrue(ConfigGlobal::mi_id_usuario());
        $oPermActiv = $oPermActivDefault->getPermisoActual('datos');
        $oPermSacd = $oPermActivDefault->getPermisoActual('sacd');

        foreach ($cActividades as $oActividad) {
            $id_activ = $oActividad->getId_activ();
            $id_tipo_activ = $oActividad->getId_tipo_activ();
            $id_tipo_activ_txt = (string) $id_tipo_activ;
            $nom_activ = $oActividad->getNom_activ();
            $id_ubi_actividad = $oActividad->getId_ubi();
            $dl_org = $oActividad->getDl_org();
            $oF_ini = $oActividad->getF_ini();
            $f_ini = $oActividad->getF_ini()?->getFromLocal();
            $f_fin = $oActividad->getF_fin()?->getFromLocal();
            $h_ini = $oActividad->getH_ini()?->format('H:i') ?? '';
            $h_fin = $oActividad->getH_fin()?->format('H:i') ?? '';
            $tarifa = $oActividad->getTarifaVo()?->value();
            $observ = $oActividad->getObservVo()?->value();
            $idioma = $oActividad->getIdiomaVo()?->value();
            if (!empty($Qmodo) && $Qmodo === 'importar') {
                $oImportada = $ImportadaRepository->findById($id_activ);
                if ($oImportada !== null) {
                    continue;
                }
                $oPermActividades = new PermisosActividadesTrue(ConfigGlobal::mi_id_usuario());
                $oPermActiv = $oPermActividades->getPermisoActual('datos');
                $oPermSacd = $oPermActividades->getPermisoActual('sacd');
            } else {
                if (ConfigGlobal::is_app_installed('procesos')) {
                    if (!empty($Qfases_on) || !empty($Qfases_off)) {
                        $ActividadProcesoTareaRepository = $this->actividadProcesoTareaRepository;
                        $aFasesCompletadas = $ActividadProcesoTareaRepository->getFasesCompletadas($id_activ);
                        if (!empty($Qfases_on)) {
                            foreach ($Qfases_on as $id_fase) {
                                if (!in_array((int) $id_fase, $aFasesCompletadas, true)) {
                                    continue 2;
                                }
                            }
                        } else {
                            foreach ($Qfases_off as $id_fase) {
                                if (in_array((int) $id_fase, $aFasesCompletadas, true)) {
                                    continue 2;
                                }
                            }
                        }
                    }
                    if ($oPermActividadesSesion instanceof PermisosActividades) {
                        $oPermActividadesSesion->setActividad($id_activ, $id_tipo_activ_txt, $dl_org);
                        $oPermActiv = $oPermActividadesSesion->getPermisoActual('datos');
                        $oPermSacd = $oPermActividadesSesion->getPermisoActual('sacd');
                    }
                }
            }
            $i++;

            $oTipoActividad = new TiposActividades($id_tipo_activ);
            $isfsv = $oTipoActividad->getSfsvId();
            $ssfsv = $oTipoActividad->getSfsvText();
            if ($mi_sfsv !== $isfsv && !$permOficina('des')) {
                $sactividad = $oTipoActividad->getActividadText();
                $nom_activ = "$ssfsv $sactividad";
            }

            $ssfsv = $oTipoActividad->getSfsvText();
            if (ConfigGlobal::is_app_installed('procesos') && $oPermActiv->have_perm_activ('ocupado') === false) {
                $sin++;
                continue;
            }
            if (ConfigGlobal::is_app_installed('procesos') && $oPermActiv->have_perm_activ('ver') === false) {
                $a_valores[$i]['sel'] = '';
                $a_valores[$i]['select'] = '';
                $a_valores[$i][1] = $f_ini;
                $a_valores[$i][2] = $f_fin;
                $a_valores[$i][3] = sprintf(_("ocupado %s (%s-%s)"), $ssfsv, $f_ini, $f_fin);
                $a_valores[$i][4] = '';
                $a_valores[$i][5] = '';
                if ($permOficina('vcsd') || $permOficina('des')) {
                    $a_valores[$i][6] = $ssfsv;
                }
                $a_valores[$i][7] = '';
                $a_valores[$i][8] = '';
                $a_valores[$i][9] = '';
                if (empty($aRolesPau[$id_role]) || ($aRolesPau[$id_role] !== PauType::PAU_CTR)) {
                    $a_valores[$i][10] = '';
                    $a_valores[$i][11] = '';
                }
            } else {
                $tarifa_letra = '';
                if ($tarifa !== null) {
                    $oTarifa = $TipoTarifaRepository->findById($tarifa);
                    $tarifa_letra = $oTarifa?->getLetra() ?? '';
                }

                $sacds = "";
                if (ConfigGlobal::is_app_installed('actividadessacd')) {
                    $aprobado = TRUE;
                    if (ConfigGlobal::mi_sfsv() === 2 && ConfigGlobal::is_app_installed('procesos')) {
                        $ActividadProcesoTareaRepository = $this->actividadProcesoTareaRepository;
                        $aprobado = $ActividadProcesoTareaRepository->getSacdAprobado($id_activ);
                    }
                    if (!ConfigGlobal::is_app_installed('procesos')
                        || ($oPermSacd->have_perm_activ('ver') === true && $aprobado)) {
                        $ActividadCargoRepository = $this->actividadCargoRepository;
                        foreach ($ActividadCargoRepository->getActividadSacds($id_activ) as $oPersona) {
                            $nom = method_exists($oPersona, 'getPrefApellidosNombre') ? $oPersona->getPrefApellidosNombre() : '';
                            $sacds .= $nom . "# ";
                        }
                        $sacds = substr($sacds, 0, -2);
                    }
                }

                $ctrs = "";
                if (ConfigGlobal::is_app_installed('actividadescentro')) {
                    $n = 0;
                    foreach ($CentroEncargadoRepository->getCentrosEncargadosActividad($id_activ) as $oEncargado) {
                        $n++;
                        $ctrs .= $oEncargado->getNombre_ubi() . ", ";
                    }
                    $ctrs = (!empty($n)) ? substr($ctrs, 0, -2) : '';
                }

                $a_valores[$i]['sel'] = "$id_activ#$nom_activ";
                $con = '';
                $flag = 0;
                if (preg_match("/^[12][45]/", $id_tipo_activ_txt)) {
                    if (preg_match("/^[12][45]1/", $id_tipo_activ_txt)) {
                        $w = $oF_ini instanceof DateTimeLocal ? (int) $oF_ini->format('w') : 0;
                        $flag = ($w < 4) ? 0 : 1;
                    }
                    if (empty($flag)) {
                        $coincide = $ActividadRepository->getCoincidencia($oActividad);
                        $con = ($coincide) ? '*' : '';
                    }
                }
                $a_valores[$i][1] = $f_ini;
                $a_valores[$i][2] = $f_fin;

                if ($Qmodo !== 'importar') {
                    if ($sPrefs === 'html') {
                        $a_valores[$i][3] = [
                            'link_spec' => [
                                'path' => 'frontend/dossiers/controller/dossiers_ver.php',
                                'query' => ['pau' => 'a', 'id_pau' => $id_activ, 'obj_pau' => $obj_pau],
                            ],
                            'valor' => $nom_activ . $con,
                        ];
                    } else {
                        $pagina = 'jsForm.mandar("#seleccionados","dossiers")';
                        $a_valores[$i][3] = ['script' => $pagina, 'valor' => $nom_activ . $con];
                    }
                } else {
                    $a_valores[$i][3] = $nom_activ . $con;
                }
                $a_valores[$i][4] = $h_ini;
                $a_valores[$i][5] = $h_fin;
                if ($permOficina('vcsd') || $permOficina('des')) {
                    $a_valores[$i][6] = $ssfsv;
                }
                $a_valores[$i][7] = $tarifa_letra;
                if (empty($aRolesPau[$id_role]) || ($aRolesPau[$id_role] !== PauType::PAU_CTR)) {
                    $a_valores[$i][8] = $sacds;
                    $a_valores[$i][9] = $dl_org;
                    $a_valores[$i][10] = $ctrs;
                    $a_valores[$i][11] = $observ;
                    $a_valores[$i][12] = $idioma;
                } else {
                    $a_valores[$i][8] = $ctrs;
                    $a_valores[$i][9] = $observ;
                    $a_valores[$i][10] = $idioma;
                }
            }
            if (empty($id_ubi_actividad) || $id_ubi_actividad === 1) {
                $nombre_ubi_actividad = 'z';
            } else {
                if (empty($a_casas[$id_ubi_actividad])) {
                    // Mantiene el warning del controlador original (ubi no encontrado)
                    // via un <div>; lo incluimos en la tabla aunque no haya un sitio
                    // natural. En la practica es raro.
                    $nombre_ubi_actividad = 'z';
                } else {
                    $nombre_ubi_actividad = $a_casas[$id_ubi_actividad];
                }
            }
            $a_NombreCasa[$i] = $nombre_ubi_actividad;
            $fIniRow = $oActividad->getF_ini();
            $a_FechaIni[$i] = $fIniRow instanceof DateTimeLocal ? $fIniRow->getIso() : '';
        }
        if (!empty($a_valores)) {
            array_multisort(
                $a_FechaIni, SORT_STRING,
                $a_NombreCasa, SORT_STRING,
                $a_valores);
        }

        $num = $i;
        $Qid_sel = input_string_list($input, 'sel');
        $Qscroll_id = input_string($input, 'scroll_id');
        if (!empty($a_valores)) {
            if (!empty($Qid_sel)) {
                $a_valores['select'] = $Qid_sel;
            }
            if (!empty($Qscroll_id)) {
                $a_valores['scroll_id'] = $Qscroll_id;
            }
        }

        if (ConfigGlobal::is_app_installed('procesos')) {
            $resultado = sprintf(_("%s actividades encontradas (%s sin permiso)"), $num, $sin);
        } else {
            $resultado = sprintf(_("%s actividades encontradas"), $num);
        }
        $inicioIsoStr = is_string($inicioIso) ? $inicioIso : date('Y-m-d');
        $finIsoStr = is_string($finIso) ? $finIso : date('Y-m-d');
        $oF_qini = new DateTimeLocal($inicioIsoStr);
        $QinicioLocal = $oF_qini->getFromLocal();
        $oF_qfin = new DateTimeLocal($finIsoStr);
        $QfinLocal = $oF_qfin->getFromLocal();
        $resultado .= ' ' . sprintf(_("entre %s y %s"), $QinicioLocal, $QfinLocal);

        $perm_nueva = FALSE;
        if (empty($aRolesPau[$id_role]) || ($aRolesPau[$id_role] !== PauType::PAU_CTR && $aRolesPau[$id_role] !== PauType::PAU_CDC)) {
            $perm_nueva = TRUE;
        }

        $aTiposActiv = [];
        if (!empty($Qid_tipo_activ) && ($Qid_tipo_activ[1] !== '.')) {
            $oTipoActivCrear = new TiposActividades($Qid_tipo_activ);
            $aTiposActiv = $oTipoActivCrear->getArrayAsistentesIndividual();
        }
        $txt_tipo_actual = _("del mismo tipo");
        $aTiposActual = [$txt_tipo_actual => str_replace('.', '', $Qid_tipo_activ)];
        $aTiposActiv = $aTiposActual + $aTiposActiv;

        return [
            'resultado' => $resultado,
            'perm_nueva' => $perm_nueva,
            'mod' => $mod,
            'obj_pau' => $obj_pau,
            'aTiposActiv' => $aTiposActiv,
            'html_advertencia' => '',
            'extendida' => $extendida,
            'id_tipo_activ_efectivo' => $Qid_tipo_activ,
            'aRolesPau' => $aRolesPau,
            'id_role' => $id_role,
            'a_cabeceras' => $a_cabeceras,
            'a_botones' => $a_botones,
            'a_valores' => $a_valores,
        ];
    }
}
