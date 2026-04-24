<?php

namespace src\actividades\application;

use core\ConfigGlobal;
use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\actividades\domain\value_objects\StatusId;
use src\actividadescentro\domain\contracts\CentroEncargadoRepositoryInterface;
use src\actividadtarifas\domain\contracts\TipoTarifaRepositoryInterface;
use src\ubis\domain\contracts\CasaRepositoryInterface;
use web\Hash;
use web\Lista;
use web\Periodo;
use web\TiposActividades;

use function core\is_true;

/**
 * Caso de uso: construye los datos (cabeceras + filas) de la pantalla
 * frontend/actividades/controller/lista_activ.php y devuelve tambien el
 * HTML ya renderizado de la tabla (`html_tabla`).
 *
 * La responsabilidad de leer el POST y gestionar `web\Posicion` queda en el
 * controlador frontend. Aqui traducimos un set de filtros + opciones de
 * entorno (permisos, dmz, etc.) al array de datos y al HTML de la tabla.
 */
class ListaActivTabla
{
    /**
     * @param array $input Filtros ya saneados. Claves esperadas:
     *   que, status (int|array), id_tipo_activ, filtro_lugar, id_ubi, periodo,
     *   year, dl_org, empiezamin, empiezamax, c_activ (array), asist (array),
     *   seccion (array), ssfsv, sasistentes, sactividad, snom_tipo, titulo.
     * @param array $opts Opciones de entorno. Claves:
     *   mi_sfsv (int), perm_vcsd (bool), perm_des (bool), perm_sg (bool),
     *   perm_admin (bool), is_dmz (bool).
     * @return array{
     *   titulo: string,
     *   ver_hora: int,
     *   ver_tarifa: int,
     *   ver_sacd: int,
     *   a_cabeceras: array,
     *   a_valores: array,
     *   html_tabla: string
     * }
     */
    public function execute(array $input, array $opts): array
    {
        $Qque = (string)($input['que'] ?? '');
        $Qstatus = $input['status'] ?? 0;
        $Qid_tipo_activ = (string)($input['id_tipo_activ'] ?? '');
        $Qid_ubi = (int)($input['id_ubi'] ?? 0);
        $Qperiodo = (string)($input['periodo'] ?? '');
        $Qyear = (string)($input['year'] ?? '');
        $Qdl_org = (string)($input['dl_org'] ?? '');
        $Qempiezamin = (string)($input['empiezamin'] ?? '');
        $Qempiezamax = (string)($input['empiezamax'] ?? '');
        $Qc_activ = (array)($input['c_activ'] ?? []);
        $Qasist = (array)($input['asist'] ?? []);
        $Qseccion = (array)($input['seccion'] ?? []);
        $Qssfsv = (string)($input['ssfsv'] ?? '');
        $Qsasistentes = (string)($input['sasistentes'] ?? '');
        $Qsactividad = (string)($input['sactividad'] ?? '');
        $Qsnom_tipo = (string)($input['snom_tipo'] ?? '');

        $mi_sfsv = (int)($opts['mi_sfsv'] ?? 0);
        $perm_vcsd = (bool)($opts['perm_vcsd'] ?? false);
        $perm_des = (bool)($opts['perm_des'] ?? false);
        $perm_sg = (bool)($opts['perm_sg'] ?? false);
        $perm_admin = (bool)($opts['perm_admin'] ?? false);
        $is_dmz = (bool)($opts['is_dmz'] ?? false);

        $aWhere = [];
        $aOperador = [];
        if (is_array($Qstatus)) {
            $cond_status = '';
            foreach ($Qstatus as $status) {
                $cond_status .= $status;
            }
            $aWhere['status'] = $cond_status;
            $aOperador['status'] = '~';
        } elseif (!empty($Qstatus)) {
            $aWhere['status'] = $Qstatus;
        }

        if (empty($Qid_tipo_activ)) {
            if ($Qque === 'list_activ_inv_sg' || $Qque === 'list_activ_sr') {
                $codi_activ_v = [];
                foreach ($Qseccion as $seccion_temp) {
                    foreach ($Qasist as $asist_temp) {
                        foreach ($Qc_activ as $c_activ_temp) {
                            $codi_activ_v[] = $seccion_temp . $asist_temp . $c_activ_temp . '...';
                        }
                    }
                }
                $condicion = implode('|', $codi_activ_v);
                $aWhere['id_tipo_activ'] = "^($condicion)";
                $aOperador['id_tipo_activ'] = '~';
            } else {
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
                $oTipoActiv->setActividadText($sactividad);
                $id_tipo_activ = $oTipoActiv->getId_tipo_activ();
                if ($id_tipo_activ !== '......') {
                    $aWhere['id_tipo_activ'] = "^$id_tipo_activ";
                    $aOperador['id_tipo_activ'] = '~';
                }
            }
        } elseif ($Qid_tipo_activ !== '......') {
            $aWhere['id_tipo_activ'] = "^$Qid_tipo_activ";
            $aOperador['id_tipo_activ'] = '~';
        }

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

        $aWhere['_ordre'] = 'f_ini';
        $ActividadRepository = $GLOBALS['container']->get(ActividadRepositoryInterface::class);
        $cActividades = $ActividadRepository->getActividades($aWhere, $aOperador);

        if ($Qque === 'list_active_inv_sg' || $Qque === 'list_activ_sr') {
            // El titulo lo suele enviar el formulario que_lista_activ_sg / que_lista_activ_sr.
            $titulo = ucfirst((string)($input['titulo'] ?? ''));
        } else {
            $titulo = ucfirst(_("listado de actividades"));
        }

        $ver_hora = 0;
        if ($Qque === 'list_activ_compl'
            || $Qque === 'list_activ_inv_sg'
            || $Qque === 'list_activ_sr'
            || $perm_vcsd
            || $perm_des
        ) {
            $ver_hora = 1;
        }

        if (!($perm_sg && $Qque === 'list_activ_inv_sg' && !$perm_admin)) {
            $ver_tarifa = 1;
            $ver_sacd = 1;
        } else {
            $ver_tarifa = 0;
            $ver_sacd = 0;
        }

        $a_cabeceras = [];
        if ($Qque === 'list_activ_compl') {
            $a_cabeceras[] = ucfirst(_("común"));
        }
        $a_cabeceras[] = ['name' => ucfirst(_("empieza")), 'class' => 'fecha'];
        if ($ver_hora === 1) {
            $a_cabeceras[] = ucfirst(_("hora ini"));
        }
        $a_cabeceras[] = ['name' => ucfirst(_("termina")), 'class' => 'fecha'];
        if ($ver_hora === 1) {
            $a_cabeceras[] = ucfirst(_("hora fin"));
        }
        if ($perm_vcsd || $perm_des) {
            $a_cabeceras[] = 'sf/sv';
        }
        $a_cabeceras[] = ucfirst(_("activ."));
        $a_cabeceras[] = ucfirst(_("asist."));
        $a_cabeceras[] = ucfirst(_("tipo actividad"));
        $a_cabeceras[] = ucfirst(_("lugar"));
        if ($ver_tarifa === 1) {
            $a_cabeceras[] = ucfirst(_("tar."));
        }
        $a_cabeceras[] = ucfirst(_("centro"));
        if ($ver_sacd === 1) {
            $a_cabeceras[] = ucfirst(_("sacd"));
            $a_cabeceras[] = ucfirst(_("observaciones"));
        }
        if ($is_dmz === false) {
            $a_cabeceras[] = ['name' => '', 'formatter' => 'clickFormatter'];
        }

        $a_valores = [];
        $CasaRepository = $GLOBALS['container']->get(CasaRepositoryInterface::class);
        $TipoTarifaRepository = $GLOBALS['container']->get(TipoTarifaRepositoryInterface::class);
        $CentroEncargadoRepository = $GLOBALS['container']->get(CentroEncargadoRepositoryInterface::class);
        $ActividadCargoRepository = $GLOBALS['container']->get(ActividadCargoRepositoryInterface::class);

        $i = 0;
        foreach ($cActividades as $oActividad) {
            $i++;
            $id_activ = $oActividad->getId_activ();
            $id_tipo_activ_row = $oActividad->getId_tipo_activ();
            $id_ubi = $oActividad->getId_ubi();
            $f_ini = $oActividad->getF_ini()?->getFromLocal();
            $f_fin = $oActividad->getF_fin()?->getFromLocal();
            $h_ini = $oActividad->getH_ini();
            $h_fin = $oActividad->getH_fin();
            $tarifa = $oActividad->getTarifa();
            $observ = $oActividad->getObserv();

            $oCasa = $CasaRepository->findById($id_ubi);
            $nombre_ubi = $oCasa->getNombre_ubi();
            $comun = '';
            if (is_true($oCasa->isSv())) {
                $comun = 'sv';
            }
            if (is_true($oCasa->isSf())) {
                $comun = 'sf';
            }
            if (is_true($oCasa->isSv()) && is_true($oCasa->isSf())) {
                $comun = 'comun';
            }

            $oTipoActivRow = new TiposActividades($id_tipo_activ_row);
            $ssfsv = $oTipoActivRow->getSfsvText();
            $sasistentesTxt = $oTipoActivRow->getAsistentesText();
            $sactividadTxt = $oTipoActivRow->getActividadText();
            $snom_tipoTxt = $oTipoActivRow->getNom_tipoText();

            if (($perm_sg || $perm_vcsd || $perm_des) && !$perm_admin) {
                if ($snom_tipoTxt === '(sin especificar)') {
                    $snom_tipoTxt = '&nbsp;';
                }
            }

            if ($Qque === 'list_activ_compl') {
                $a_valores[$i][1] = $comun;
            }
            $a_valores[$i][2] = $f_ini;
            $a_valores[$i][4] = $f_fin;
            if ($ver_hora === 1) {
                if (strlen($h_ini ?? '')) {
                    $h_ini = substr($h_ini, 0, strlen($h_ini) - 3);
                }
                if (strlen($h_fin ?? '')) {
                    $h_fin = substr($h_fin, 0, strlen($h_fin) - 3);
                }
                $a_valores[$i][3] = $h_ini;
                $a_valores[$i][5] = $h_fin;
            }
            if ($perm_vcsd || $perm_des) {
                $a_valores[$i][6] = $ssfsv;
            }
            $a_valores[$i][7] = $sactividadTxt;
            $a_valores[$i][8] = $sasistentesTxt;
            $a_valores[$i][9] = $snom_tipoTxt;
            $a_valores[$i][10] = $nombre_ubi;
            if ($ver_tarifa === 1) {
                $oTarifa = $TipoTarifaRepository->findById($tarifa);
                $a_valores[$i][11] = $oTarifa->getLetra();
            }
            $ctrs = '';
            foreach ($CentroEncargadoRepository->getCentrosEncargadosActividad($id_activ) as $oEncargado) {
                $ctrs .= $oEncargado->getNombre_ubi() . ', ';
            }
            $ctrs = substr($ctrs, 0, -2);
            $a_valores[$i][12] = $ctrs;
            if ($ver_sacd === 1) {
                $sacds = '';
                foreach ($ActividadCargoRepository->getActividadSacds($id_activ) as $oPersona) {
                    $sacds .= $oPersona->getPrefApellidosNombre() . '# '; // la coma es separador de apellidos, nombre.
                }
                $sacds = substr($sacds, 0, -2);
                $a_valores[$i][13] = $sacds;
                $a_valores[$i][14] = $observ;
            }
            if ($is_dmz === false) {
                $pagina = Hash::link(ConfigGlobal::getWeb() . '/frontend/asistentes/controller/lista_asistentes.php?' . "id_pau=$id_activ&que=$Qque");
                $a_valores[$i][15] = ['ira' => $pagina, 'valor' => _("ver asistentes")];
            }
        }

        $oTabla = new Lista();
        $oTabla->setId_tabla('lista_activ');
        $oTabla->setCabeceras($a_cabeceras);
        $oTabla->setBotones([]);
        $oTabla->setDatos($a_valores);
        $html_tabla = $oTabla->mostrar_tabla();

        return [
            'titulo' => $titulo,
            'ver_hora' => $ver_hora,
            'ver_tarifa' => $ver_tarifa,
            'ver_sacd' => $ver_sacd,
            'a_cabeceras' => $a_cabeceras,
            'a_valores' => $a_valores,
            'html_tabla' => $html_tabla,
        ];
    }
}
