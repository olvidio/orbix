<?php

namespace src\actividades\application;

use src\shared\config\ConfigGlobal;
use src\permisos\domain\PermisosActividades;
use src\permisos\domain\PermisosActividadesTrue;
use src\permisos\domain\XPermisos;
use src\shared\domain\helpers\FuncTablasSupport;

use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\actividades\domain\value_objects\StatusId;
use src\actividadescentro\domain\contracts\CentroEncargadoRepositoryInterface;
use src\ubis\domain\entity\Ubi;
use src\usuarios\domain\contracts\PreferenciaRepositoryInterface;
use frontend\shared\web\Periodo;
use src\actividades\domain\entity\TiposActividades;

/**
 * Monta el listado de actividades sf/sg (crt, cv) aplicando los filtros
 * fijados por la pantalla `lista_actividades_sg`. Concentra todos los
 * accesos a repositorios del dominio y devuelve datos listos para serializar.
 * La tabla HTML y la advertencia firmada se arman en `frontend/actividades/controller/lista_actividades_sg.php`.
 *
 * Claves:
 *   - result_busqueda (string)   Texto resumen ("X actividades encontradas (Y sin permiso)").
 *   - id_tipo_activ (string)     Filtro efectivo aplicado (1[45]1 o 1[45]3).
 *   - html_advertencia (string)  Vacío; si >200 actividades, `advertencia_demasiadas`.
 *   - advertencia_demasiadas (array|null)  Specs para el HTML de confirmación en el front.
 *   - a_cabeceras, a_botones, a_valores     Para `Lista::mostrar_tabla` en el front (`link_spec` en celdas si aplica).
 */
final class ListaActividadesSgListado
{
    public function __construct(
        private ActividadRepositoryInterface $actividadRepository,
        private PreferenciaRepositoryInterface $preferenciaRepository,
        private CentroEncargadoRepositoryInterface $centroEncargadoRepository,
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
        $mi_sfsv = ConfigGlobal::mi_sfsv();

        $Qcontinuar = FuncTablasSupport::inputString($input, 'continuar');
        $Qstatus = FuncTablasSupport::inputInt($input, 'status');
        $Qtipo_activ_sg = FuncTablasSupport::inputString($input, 'tipo_activ_sg');
        $Qid_ubi = FuncTablasSupport::inputInt($input, 'id_ubi');
        $Qperiodo = FuncTablasSupport::inputString($input, 'periodo');
        $Qyear = FuncTablasSupport::inputString($input, 'year');
        $Qdl_org = FuncTablasSupport::inputString($input, 'dl_org');
        $Qempiezamin = FuncTablasSupport::inputString($input, 'empiezamin');
        $Qempiezamax = FuncTablasSupport::inputString($input, 'empiezamax');
        $Qid_sel = is_array($input['sel'] ?? null) ? $input['sel'] : [];
        $Qscroll_id = FuncTablasSupport::inputString($input, 'scroll_id');

        $Qstatus = empty($Qstatus) ? StatusId::ACTUAL : $Qstatus;

        $aWhere = [];
        $aOperador = [];
        if ($Qstatus !== 5) {
            $aWhere['status'] = $Qstatus;
        }

        if (empty($Qtipo_activ_sg)) {
            $Qtipo_activ_sg = 'crt';
        }
        switch ($Qtipo_activ_sg) {
            case 'crt':
                $Qid_tipo_activ = '1[45]1';
                break;
            case 'cv':
                $Qid_tipo_activ = '1[45]3';
                break;
            default:
                $Qid_tipo_activ = '1[45]1';
        }
        $aWhere['id_tipo_activ'] = "^$Qid_tipo_activ";
        $aOperador['id_tipo_activ'] = '~';

        if (!empty($Qid_ubi)) {
            $aWhere['id_ubi'] = $Qid_ubi;
        }

        $oPeriodo = new Periodo();
        $oPeriodo->setDefaultAny('next');
        $oPeriodo->setAny($Qyear);
        $oPeriodo->setEmpiezaMin($Qempiezamin);
        $oPeriodo->setEmpiezaMax($Qempiezamax);
        $oPeriodo->setPeriodo($Qperiodo);

        $inicioIso = $oPeriodo->getF_ini_iso();
        $finIso = $oPeriodo->getF_fin_iso();
        if (!empty($Qperiodo) && $Qperiodo === 'desdeHoy') {
            $aWhere['f_fin'] = "'$inicioIso','$finIso'";
            $aOperador['f_fin'] = 'BETWEEN';
        } else {
            $aWhere['f_ini'] = "'$inicioIso','$finIso'";
            $aOperador['f_ini'] = 'BETWEEN';
        }

        if (!empty($Qdl_org)) {
            $aWhere['dl_org'] = $Qdl_org;
        }

        $ActividadRepository = $this->actividadRepository;

        $a_botones = [
            ['txt' => _('cargos'), 'click' => "jsForm.mandar(\"#seleccionados\",\"carg\")"],
            ['txt' => _('asistentes'), 'click' => "jsForm.mandar(\"#seleccionados\",\"asis\")"],
            ['txt' => _('lista'), 'click' => "jsForm.mandar(\"#seleccionados\",\"list\")"],
            ['txt' => _('ctrs org'), 'click' => "jsForm.mandar(\"#seleccionados\",\"ctrs\")"],
        ];

        $a_cabeceras = [];
        $a_cabeceras[] = ['name' => _("inicio"), 'width' => 40, 'class' => 'fecha'];
        $a_cabeceras[] = ['name' => _("fin"), 'width' => 40, 'class' => 'fecha'];
        $a_cabeceras[] = ['name' => _("sf"), 'width' => 40];
        $a_cabeceras[] = ['name' => ucfirst(_("tipo")), 'width' => 30];
        $a_cabeceras[] = ['name' => ucfirst(_("asist.")), 'width' => 30];
        $a_cabeceras[] = ucfirst(_("lugar"));
        $a_cabeceras[] = ucfirst(_("ctrs"));
        $a_cabeceras[] = ucfirst(_("sacd"));
        $a_cabeceras[] = ucfirst(_("precio"));

        $aWhere['_ordre'] = 'f_ini';
        $cActividades = $ActividadRepository->getActividades($aWhere, $aOperador);
        $num_activ = count($cActividades);

        if ($num_activ > $num_max_actividades && empty($Qcontinuar)) {
            return [
                'result_busqueda' => '',
                'id_tipo_activ' => $Qid_tipo_activ,
                'html_advertencia' => '',
                'advertencia_demasiadas' => [
                    'num_actividades' => $num_activ,
                    'continuar_link_spec' => [
                        'path' => 'frontend/actividades/controller/lista_actividades_sg.php',
                        'query' => ['continuar' => 'si', 'stack' => $stackGo],
                    ],
                    'volver_link_spec' => [
                        'path' => 'frontend/actividades/controller/actividad_que.php',
                        'query' => ['stack' => $stackGo],
                    ],
                ],
                'a_cabeceras' => $a_cabeceras,
                'a_botones' => $a_botones,
                'a_valores' => [],
            ];
        }

        $i = 0;
        $sin = 0;
        $a_valores = [];
        $id_usuario = ConfigGlobal::mi_id_usuario();
        $PreferenciaRepository = $this->preferenciaRepository;
        $oPreferencia = $PreferenciaRepository->findById($id_usuario, 'tabla_presentacion');
        // (sPrefs se calculaba pero no se usaba en el listado; se omite.)
        if ($oPreferencia !== null) {
            // no-op: preservamos la carga por compat con el comportamiento original
            $oPreferencia->getPreferencia();
        }
        $CentroEncargadoRepository = $this->centroEncargadoRepository;

        foreach ($cActividades as $oActividad) {
            $id_activ = $oActividad->getId_activ();
            $id_tipo_activ = $oActividad->getId_tipo_activ();
            $nom_activ = $oActividad->getNom_activ();
            $dl_org = $oActividad->getDl_org();
            $id_ubi = $oActividad->getId_ubi();
            $lugar_esp = $oActividad->getLugar_esp();
            $f_ini = $oActividad->getF_ini()?->getFromLocal();
            $f_fin = $oActividad->getF_fin()?->getFromLocal();
            $precio = $oActividad->getPrecio();

            if (ConfigGlobal::is_app_installed('procesos')) {
                $oPermSesion = $_SESSION['oPermActividades'] ?? null;
                if (!($oPermSesion instanceof PermisosActividades)) {
                    continue;
                }
                $oPermSesion->setActividad($id_activ, (string) $id_tipo_activ, $dl_org ?? '');
                $oPermActiv = $oPermSesion->getPermisoActual('datos');
                $oPermSacd = $oPermSesion->getPermisoActual('sacd');
            } else {
                $oPermActividades = new PermisosActividadesTrue(ConfigGlobal::mi_id_usuario());
                $oPermActiv = $oPermActividades->getPermisoActual('datos');
                $oPermSacd = $oPermActividades->getPermisoActual('sacd');
            }
            $i++;

            $oTipoActividad = new TiposActividades($id_tipo_activ);
            $isfsv = $oTipoActividad->getSfsvId();
            $ssfsv = $oTipoActividad->getSfsvText();
            $oPerm = $_SESSION['oPerm'] ?? null;
            if ($mi_sfsv !== $isfsv && !($oPerm instanceof XPermisos && $oPerm->have_perm_oficina('des'))) {
                $sactividadOtra = $oTipoActividad->getActividadText();
                $nom_activ = "$ssfsv $sactividadOtra";
            }

            $sasistentes = $oTipoActividad->getAsistentesText();
            $sactividad = $oTipoActividad->getActividadText();

            if (ConfigGlobal::is_app_installed('procesos') && $oPermActiv->have_perm_activ('ocupado') === false) {
                $sin++;
                continue;
            }
            if (ConfigGlobal::is_app_installed('procesos') && $oPermActiv->have_perm_activ('ver') === false) {
                $a_valores[$i]['sel'] = '';
                $a_valores[$i][1] = sprintf(_('ocupado %s (%s-%s)'), $ssfsv, $f_ini, $f_fin);
                $a_valores[$i][2] = '';
                $a_valores[$i][3] = '';
                $a_valores[$i][4] = '';
                $a_valores[$i][5] = '';
                $a_valores[$i][6] = '';
                $a_valores[$i][7] = '';
                $a_valores[$i][8] = '';
                $a_valores[$i][9] = '';
                continue;
            }

            $nombre_ubi = '';
            if (!empty($id_ubi) && $id_ubi !== 1) {
                $oCasa = Ubi::newUbi($id_ubi);
                $nombre_ubi = $oCasa?->getNombre_ubi() ?? '';
            } else {
                if ($id_ubi === 1 && $lugar_esp) {
                    $nombre_ubi = $lugar_esp;
                }
                if (!$id_ubi && !$lugar_esp) {
                    $nombre_ubi = _("sin determinar");
                }
            }

            $sacds = "";
            if (ConfigGlobal::is_app_installed('actividadessacd')) {
                if ($oPermSacd->have_perm_action('ver') === true) {
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

            $coincide = $ActividadRepository->getCoincidencia($oActividad);
            $con = ($coincide) ? '*' : '';

            $a_valores[$i]['sel'] = "$id_activ#$nom_activ";
            $a_valores[$i][1] = $f_ini;
            $a_valores[$i][2] = $f_fin;
            $a_valores[$i][3] = $con;
            $a_valores[$i][4] = $sactividad;
            $a_valores[$i][5] = $sasistentes;
            $a_valores[$i][6] = $nombre_ubi;
            $a_valores[$i][7] = $ctrs;
            $a_valores[$i][8] = $sacds;
            $a_valores[$i][9] = $precio;
        }

        $num = $i;
        if (!empty($a_valores)) {
            if (!empty($Qid_sel)) {
                $a_valores['select'] = $Qid_sel;
            }
            if (!empty($Qscroll_id)) {
                $a_valores['scroll_id'] = $Qscroll_id;
            }
        }

        $result_busqueda = sprintf(_("%s actividades encontradas (%s sin permiso)"), $num, $sin);

        return [
            'result_busqueda' => $result_busqueda,
            'id_tipo_activ' => $Qid_tipo_activ,
            'html_advertencia' => '',
            'a_cabeceras' => $a_cabeceras,
            'a_botones' => $a_botones,
            'a_valores' => $a_valores,
        ];
    }
}
