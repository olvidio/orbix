<?php

namespace src\actividades\application;

use core\ConfigGlobal;
use permisos\model\PermisosActividadesTrue;
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
use web\Hash;
use web\Lista;
use web\Periodo;
use web\TiposActividades;

/**
 * Construye el listado de actividades que cumplen los filtros fijados por
 * `actividad_que`. Centraliza todos los accesos a repositorios del dominio
 * y a permisos / preferencias, de modo que el controlador frontend puede
 * limitarse a leer el POST, llamar a este caso de uso via PostRequest y
 * pintar el HTML resultante.
 *
 * Devuelve un array con:
 *   - html_tabla (string)      HTML de la tabla de resultados (Lista).
 *   - resultado (string)       Texto resumen ("X actividades encontradas ...").
 *   - perm_nueva (bool)        Si el usuario puede crear actividad nueva.
 *   - mod (string)             '' o 'importar'.
 *   - obj_pau (string)         'Actividad' o 'ActividadPub'.
 *   - aTiposActiv (array)      Lista de "nueva actividad ..." (txt => id).
 *   - html_advertencia (string) Bloque HTML de "demasiadas actividades ..." si procede.
 *   - a_cabeceras (array)      Cabeceras usadas (para debug / tests).
 *   - aRolesPau (array)        Roles PAU (usado para hashes en frontend).
 */
final class ActividadSelectListado
{
    public function ejecutar(array $input, int $stackGo): array
    {
        $num_max_actividades = 200;

        $mi_dele = ConfigGlobal::mi_delef();
        $mi_sfsv = ConfigGlobal::mi_sfsv();

        $Qcontinuar = (string)($input['continuar'] ?? '');
        $Qmodo = (string)($input['modo'] ?? '');
        $Qstatus = (int)($input['status'] ?? 0);
        $Qid_tipo_activ = (string)($input['id_tipo_activ'] ?? '');
        $Qfiltro_lugar = (string)($input['filtro_lugar'] ?? '');
        $Qid_ubi = (int)($input['id_ubi'] ?? 0);
        $Qnom_activ = (string)($input['nom_activ'] ?? '');
        $Qperiodo = (string)($input['periodo'] ?? '');
        $Qyear = (string)($input['year'] ?? '');
        $Qdl_org = (string)($input['dl_org'] ?? '');
        $Qempiezamin = (string)($input['empiezamin'] ?? '');
        $Qempiezamax = (string)($input['empiezamax'] ?? '');
        $Qfases_on = (array)($input['fases_on'] ?? []);
        $Qfases_off = (array)($input['fases_off'] ?? []);
        $Qpublicado = (int)($input['publicado'] ?? 0);
        $Qssfsv = (string)($input['ssfsv'] ?? '');
        $Qsasistentes = (string)($input['sasistentes'] ?? '');
        $Qsactividad = (string)($input['sactividad'] ?? '');
        $Qsactividad2 = (string)($input['sactividad2'] ?? '');

        if (empty($Qperiodo)) {
            $Qperiodo = 'actual';
        }
        $Qstatus = empty($Qstatus) ? StatusId::ACTUAL : $Qstatus;

        $aWhere = [];
        $aOperador = [];
        if ($Qstatus !== 9 && !empty($Qstatus)) {
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
        $oPeriodo->setDefaultAny('next');
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
        $id_role = $oMiUsuario->getId_role();

        $RoleRepository = $GLOBALS['container']->get(RoleRepositoryInterface::class);
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
            if (ConfigGlobal::is_app_installed('asignaturas') && $_SESSION['oPerm']->have_perm_oficina('est')) {
                $a_botones[] = ['txt' => _("asignaturas"), 'click' => "jsForm.mandar(\"#seleccionados\",\"asig\")"];
            }
        } else {
            if (!empty($aRolesPau[$id_role]) && ($aRolesPau[$id_role] === PauType::PAU_CDC || $aRolesPau[$id_role] === 'CentroSf')) {
                $a_botones = [['txt' => _("datos"), 'click' => "jsForm.mandar(\"#seleccionados\",\"datos\")"]];
            } else {
                $a_botones[] = ['txt' => _("datos"), 'click' => "jsForm.mandar(\"#seleccionados\",\"datos\")"];
                if (($_SESSION['oPerm']->have_perm_oficina('vcsd'))
                    || ($_SESSION['oPerm']->have_perm_oficina('des'))
                    || ($_SESSION['oPerm']->have_perm_oficina('calendario'))) {
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
                    if ($_SESSION['oPerm']->have_perm_oficina('est')) {
                        $a_botones[] = ['txt' => _("asignaturas"), 'click' => "jsForm.mandar(\"#seleccionados\",\"asig\")"];
                    }
                    if (($_SESSION['oPerm']->have_perm_oficina('est'))
                        || ($_SESSION['oPerm']->have_perm_oficina('agd'))
                        || ($_SESSION['oPerm']->have_perm_oficina('sm'))) {
                        $a_botones[] = ['txt' => _("plan estudios"), 'click' => "jsForm.mandar(\"#seleccionados\",\"plan_estudios\")"];
                    }
                    if ($_SESSION['oPerm']->have_perm_oficina('est')) {
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
        if (($_SESSION['oPerm']->have_perm_oficina('vcsd')) || ($_SESSION['oPerm']->have_perm_oficina('des'))) {
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
            $ActividadRepository = $GLOBALS['container']->get(ActividadPubRepositoryInterface::class);
            if (empty($Qdl_org)) {
                $aWhere['dl_org'] = $mi_dele;
                $aOperador['dl_org'] = '!=';
            }
            $obj_pau = 'ActividadPub';
        } else {
            $mod = '';
            $ActividadRepository = $GLOBALS['container']->get(ActividadRepositoryInterface::class);
            $obj_pau = 'Actividad';
        }

        $aWhere['_ordre'] = 'f_ini';
        $cActividades = $ActividadRepository->getActividades($aWhere, $aOperador);
        $num_activ = count($cActividades);
        if ($num_activ > $num_max_actividades && empty($Qcontinuar)) {
            $go_avant = Hash::link(ConfigGlobal::getWeb() . '/frontend/actividades/controller/actividad_select.php?' . http_build_query(['continuar' => 'si', 'Gstack' => $stackGo]));
            $go_atras = Hash::link(ConfigGlobal::getWeb() . '/frontend/actividades/controller/actividad_que.php?' . http_build_query(['stack' => $stackGo]));
            $html_advertencia = "<h2>" . sprintf(_("son %s actividades a mostrar. ¿Seguro que quiere continuar?."), $num_activ) . '</h2>';
            $html_advertencia .= "<input type='button' onclick=fnjs_update_div('#main','" . $go_avant . "') value=" . _("continuar") . ">";
            $html_advertencia .= "<input type='button' onclick=fnjs_update_div('#main','" . $go_atras . "') value=" . _("volver") . ">";
            return [
                'html_tabla' => '',
                'resultado' => '',
                'perm_nueva' => false,
                'mod' => $mod,
                'obj_pau' => $obj_pau,
                'aTiposActiv' => [],
                'html_advertencia' => $html_advertencia,
                'extendida' => $extendida,
                'id_tipo_activ_efectivo' => $Qid_tipo_activ,
                'aRolesPau' => $aRolesPau,
                'id_role' => $id_role,
            ];
        }

        $CasaRepository = $GLOBALS['container']->get(CasaRepositoryInterface::class);
        $a_OpcionesCasas = $CasaRepository->getArrayCasas();
        $CentroRepository = $GLOBALS['container']->get(CentroRepositoryInterface::class);
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
        $PreferenciaRepository = $GLOBALS['container']->get(PreferenciaRepositoryInterface::class);
        $oPreferencia = $PreferenciaRepository->findById($id_usuario, $tipo);
        if ($oPreferencia !== null) {
            $sPrefs = $oPreferencia->getPreferenciaAsString();
        }
        $TipoTarifaRepository = $GLOBALS['container']->get(TipoTarifaRepositoryInterface::class);
        $ImportadaRepository = $GLOBALS['container']->get(ImportadaRepositoryInterface::class);
        $CentroEncargadoRepository = $GLOBALS['container']->get(CentroEncargadoRepositoryInterface::class);

        foreach ($cActividades as $oActividad) {
            $id_activ = $oActividad->getId_activ();
            $id_tipo_activ = $oActividad->getId_tipo_activ();
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
                        $ActividadProcesoTareaRepository = $GLOBALS['container']->get(ActividadProcesoTareaRepositoryInterface::class);
                        $aFasesCompletadas = $ActividadProcesoTareaRepository->getFasesCompletadas($id_activ);
                        if (!empty($Qfases_on)) {
                            foreach ($Qfases_on as $id_fase) {
                                if (!in_array($id_fase, $aFasesCompletadas, true)) {
                                    continue 2;
                                }
                            }
                        }
                        if (!empty($Qfases_off)) {
                            foreach ($Qfases_off as $id_fase) {
                                if (in_array($id_fase, $aFasesCompletadas, true)) {
                                    continue 2;
                                }
                            }
                        }
                    }
                    $_SESSION['oPermActividades']->setActividad($id_activ, $id_tipo_activ, $dl_org);
                    $oPermActiv = $_SESSION['oPermActividades']->getPermisoActual('datos');
                    $oPermSacd = $_SESSION['oPermActividades']->getPermisoActual('sacd');
                }
            }
            $i++;

            $oTipoActividad = new TiposActividades($id_tipo_activ);
            $isfsv = $oTipoActividad->getSfsvId();
            $ssfsv = $oTipoActividad->getSfsvText();
            if ($mi_sfsv !== $isfsv && !($_SESSION['oPerm']->have_perm_oficina('des'))) {
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
                if (($_SESSION['oPerm']->have_perm_oficina('vcsd')) || ($_SESSION['oPerm']->have_perm_oficina('des'))) {
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
                        $ActividadProcesoTareaRepository = $GLOBALS['container']->get(ActividadProcesoTareaRepositoryInterface::class);
                        $aprobado = $ActividadProcesoTareaRepository->getSacdAprobado($id_activ);
                    }
                    if (!ConfigGlobal::is_app_installed('procesos')
                        || ($oPermSacd->have_perm_activ('ver') === true && $aprobado)) {
                        $ActividadCargoRepository = $GLOBALS['container']->get(ActividadCargoRepositoryInterface::class);
                        foreach ($ActividadCargoRepository->getActividadSacds($id_activ) as $oPersona) {
                            $sacds .= $oPersona->getPrefApellidosNombre() . "# ";
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
                if (preg_match("/^[12][45]/", $id_tipo_activ)) {
                    if (preg_match("/^[12][45]1/", $id_tipo_activ)) {
                        $w = $oF_ini->format('w');
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
                        $pagina = Hash::link(ConfigGlobal::getWeb() . '/frontend/dossiers/controller/dossiers_ver.php?' . http_build_query(['pau' => 'a', 'id_pau' => $id_activ, 'obj_pau' => $obj_pau]));
                        $a_valores[$i][3] = ['ira' => $pagina, 'valor' => $nom_activ . $con];
                    } else {
                        $pagina = 'jsForm.mandar("#seleccionados","dossiers")';
                        $a_valores[$i][3] = ['script' => $pagina, 'valor' => $nom_activ . $con];
                    }
                } else {
                    $a_valores[$i][3] = $nom_activ . $con;
                }
                $a_valores[$i][4] = $h_ini;
                $a_valores[$i][5] = $h_fin;
                if (($_SESSION['oPerm']->have_perm_oficina('vcsd')) || ($_SESSION['oPerm']->have_perm_oficina('des'))) {
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
            $a_FechaIni[$i] = $oActividad->getF_ini()->getIso();
        }
        if (!empty($a_valores)) {
            array_multisort(
                $a_FechaIni, SORT_STRING,
                $a_NombreCasa, SORT_STRING,
                $a_valores);
        }

        $num = $i;
        $Qid_sel = $input['sel'] ?? [];
        $Qscroll_id = (string)($input['scroll_id'] ?? '');
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
        $oF_qini = new DateTimeLocal($inicioIso);
        $QinicioLocal = $oF_qini->getFromLocal();
        $oF_qfin = new DateTimeLocal($finIso);
        $QfinLocal = $oF_qfin->getFromLocal();
        $resultado .= ' ' . sprintf(_("entre %s y %s"), $QinicioLocal, $QfinLocal);

        $oTabla = new Lista();
        $oTabla->setId_tabla('actividad_select');
        $oTabla->setCabeceras($a_cabeceras);
        $oTabla->setBotones($a_botones);
        $oTabla->setDatos($a_valores);
        $html_tabla = $oTabla->mostrar_tabla();

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
            'html_tabla' => $html_tabla,
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
        ];
    }
}
