<?php

namespace src\actividades\application;

use src\shared\config\ConfigGlobal;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\actividadescentro\domain\contracts\CentroEncargadoRepositoryInterface;
use src\ubis\domain\contracts\CasaRepositoryInterface;
use src\usuarios\domain\contracts\PreferenciaRepositoryInterface;
use src\usuarios\domain\entity\Preferencia;
use src\usuarios\domain\value_objects\TipoPreferencia;
use src\usuarios\domain\value_objects\ValorPreferencia;
use frontend\shared\web\Lista;
use frontend\shared\web\Periodo;
use src\actividades\domain\entity\TiposActividades;
use src\permisos\domain\XPermisos;
use function src\shared\domain\helpers\input_string;
use function src\shared\domain\helpers\input_string_list;
use function src\shared\domain\helpers\is_true;

/**
 * Caso de uso que prepara la lista de actividades de San Rafael.
 *
 * - Guarda la preferencia de busqueda del usuario.
 * - Ejecuta los filtros contra `ActividadRepository` y construye el conjunto
 *   final (tipos + ubis compartidas, deduplicando por id_activ).
 * - Devuelve la tabla ya formateada (html_tabla) y los datos crudos
 *   (a_cabeceras + a_valores) para que el frontend pueda exportarlos a CSV
 *   sin tocar `src/`.
 */
final class ListaSrCsvListado
{
    public function __construct(
        private PreferenciaRepositoryInterface $preferenciaRepository,
        private ActividadRepositoryInterface $actividadRepository,
        private CentroEncargadoRepositoryInterface $centroEncargadoRepository,
        private CasaRepositoryInterface $casaRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @return array<string, mixed>
     */
    public function ejecutar(array $input): array
    {
        $mi_sfsv = ConfigGlobal::mi_sfsv();
        $oPerm = $_SESSION['oPerm'] ?? null;
        $permOficina = static function (string $perm) use ($oPerm): bool {
            return $oPerm instanceof XPermisos && $oPerm->have_perm_oficina($perm);
        };

        $Qperiodo = input_string($input, 'periodo');
        $Qyear = input_string($input, 'year');
        $Qdl_org = input_string($input, 'dl_org');
        $Qempiezamin = input_string($input, 'empiezamin');
        $Qempiezamax = input_string($input, 'empiezamax');
        $Qa_activ = input_string_list($input, 'c_activ');
        $Qa_status = input_string_list($input, 'status');
        $Qa_id_cdc = input_string_list($input, 'id_cdc');

        if (empty($Qperiodo)) {
            $Qperiodo = 'curso_ca';
        }

        $json_status = json_encode($Qa_status);
        $json_activ = json_encode($Qa_activ);
        $json_cdc = json_encode($Qa_id_cdc);
        $aPref = [
            'status' => $json_status,
            'periodo' => $Qperiodo,
            'tipo_activ' => $json_activ,
            'ubis_compartidos' => $json_cdc,
        ];
        $json_busqueda = json_encode($aPref);
        if (!is_string($json_busqueda)) {
            $json_busqueda = '{}';
        }
        $id_usuario = ConfigGlobal::mi_id_usuario();
        $tipo = 'busqueda_activ_sr';
        $PreferenciaRepository = $this->preferenciaRepository;
        $oPreferencia = $PreferenciaRepository->findById($id_usuario, $tipo);
        if ($oPreferencia === null) {
            $oPreferencia = new Preferencia();
            $oPreferencia->setId_usuario($id_usuario);
            $oPreferencia->setTipoVo(new TipoPreferencia($tipo));
        }
        $oPreferencia->setPreferenciaVo(new ValorPreferencia($json_busqueda));
        $pref_error = '';
        if ($PreferenciaRepository->Guardar($oPreferencia) === false) {
            $pref_error = _("hay un error, no se ha guardado la preferencia") . "\n" . $PreferenciaRepository->getErrorTxt();
        }

        $aWhere = [];
        $aOperador = [];
        if ($Qa_status !== []) {
            if (count($Qa_status) > 1) {
                $aWhere['status'] = '[' . implode('', $Qa_status) . ']';
            } else {
                $aWhere['status'] = $Qa_status[0];
            }
        } else {
            $aWhere['status'] = '.';
        }
        $aOperador['status'] = '~';

        $cv_crt = '';
        if ($Qa_activ !== []) {
            if (count($Qa_activ) > 1) {
                $cond_act = '[' . implode('', $Qa_activ) . ']';
            } else {
                $cond_act = $Qa_activ[0];
            }
        } else {
            $cond_act = '.';
        }
        if ($mi_sfsv == 1) {
            $condicion = '^17' . $cond_act;
        } else {
            $condicion = '^2[789]' . $cond_act;
        }
        $aWhere['id_tipo_activ'] = $condicion;
        $aOperador['id_tipo_activ'] = '~';

        $oPeriodo = new Periodo();
        $oPeriodo->setDefaultAny('next');
        $oPeriodo->setAny($Qyear);
        $oPeriodo->setEmpiezaMin($Qempiezamin);
        $oPeriodo->setEmpiezaMax($Qempiezamax);
        $oPeriodo->setPeriodo($Qperiodo);

        $inicioIso = $oPeriodo->getF_ini_iso();
        $finIso = $oPeriodo->getF_fin_iso();
        $inicioIsoStr = is_string($inicioIso) ? $inicioIso : date('Y-m-d');
        $finIsoStr = is_string($finIso) ? $finIso : date('Y-m-d');
        if ($Qperiodo === 'desdeHoy') {
            $aWhere['f_fin'] = "'$inicioIsoStr','$finIsoStr'";
            $aOperador['f_fin'] = 'BETWEEN';
        } else {
            $aWhere['f_ini'] = "'$inicioIsoStr','$finIsoStr'";
            $aOperador['f_ini'] = 'BETWEEN';
        }
        if (!empty($Qdl_org)) {
            $aWhere['dl_org'] = $Qdl_org;
        }
        $aWhere['_ordre'] = 'f_ini';

        $ActividadRepository = $this->actividadRepository;
        $cActividades_1 = $ActividadRepository->getActividades($aWhere, $aOperador);
        $cActividadesxTipo = [];
        foreach ($cActividades_1 as $oActividad) {
            $key = 's' . $oActividad->getId_activ();
            $cActividadesxTipo[$key] = $oActividad;
        }

        $cActividadesxUbi = [];
        if ($Qa_id_cdc !== []) {
            unset($aWhere['id_tipo_activ']);
            unset($aOperador['id_tipo_activ']);
            $cond_ubis = '{' . implode(', ', $Qa_id_cdc) . '}';
            $aWhere['id_ubi'] = $cond_ubis;
            $aOperador['id_ubi'] = 'ANY';
            $cActividades_2 = $ActividadRepository->getActividades($aWhere, $aOperador);
            foreach ($cActividades_2 as $oActividad) {
                $key = 's' . $oActividad->getId_activ();
                $cActividadesxUbi[$key] = $oActividad;
            }
        }

        $cActividades = array_merge($cActividadesxTipo, $cActividadesxUbi);

        $a_cabeceras = [];
        $a_cabeceras[] = ucfirst(_("status"));
        $a_cabeceras[] = ['name' => ucfirst(_("empieza")), 'class' => 'fecha'];
        $a_cabeceras[] = ['name' => ucfirst(_("termina")), 'class' => 'fecha'];
        $a_cabeceras[] = ucfirst(_("nom activ."));
        $a_cabeceras[] = ucfirst(_("asist."));
        $a_cabeceras[] = ucfirst(_("actividad"));
        $a_cabeceras[] = ucfirst(_("tipo actividad"));
        $a_cabeceras[] = ucfirst(_("lugar"));
        $a_cabeceras[] = ucfirst(_("centro"));

        $a_valores = [];
        $i = 0;
        $CentroEncargadoRepository = $this->centroEncargadoRepository;
        $CasaRepository = $this->casaRepository;
        foreach ($cActividades as $oActividad) {
            $i++;
            $id_activ = $oActividad->getId_activ();
            $id_tipo_activ = $oActividad->getId_tipo_activ();
            $status = $oActividad->getStatus();
            $id_ubi = $oActividad->getId_ubi();
            $nom_activ = $oActividad->getNom_activ();
            $f_ini = $oActividad->getF_ini()?->getFromLocal();
            $f_fin = $oActividad->getF_fin()?->getFromLocal();

            $oUbi = $CasaRepository->findById((int) $id_ubi);
            $nombre_ubi = $oUbi?->getNombre_ubi() ?? '';

            $oTipoActiv = new TiposActividades($id_tipo_activ);
            $sasistentes = $oTipoActiv->getAsistentesText();
            $sactividad = $oTipoActiv->getActividadText();
            $snom_tipo = $oTipoActiv->getNom_tipoText();

            if (($permOficina('sg') || $permOficina('vcsd') || $permOficina('des')) && !$permOficina('admin')) {
                if ($snom_tipo === "(sin especificar)") {
                    $snom_tipo = "";
                }
            }

            $a_valores[$i][1] = $status;
            $a_valores[$i][2] = $f_ini;
            $a_valores[$i][4] = $f_fin;
            $a_valores[$i][7] = $nom_activ;
            $a_valores[$i][8] = $sasistentes;
            $a_valores[$i][9] = $sactividad;
            $a_valores[$i][10] = $snom_tipo;
            $a_valores[$i][11] = $nombre_ubi;

            $ctrs = '';
            foreach ($CentroEncargadoRepository->getCentrosEncargadosActividad($id_activ) as $oEncargado) {
                $ctrs .= $oEncargado->getNombre_ubi() . ', ';
            }
            $ctrs = substr($ctrs, 0, -2);
            $a_valores[$i][12] = $ctrs;
        }

        $oTabla = new Lista();
        $oTabla->setId_tabla('lista_activ');
        $oTabla->setCabeceras($a_cabeceras);
        $oTabla->setDatos($a_valores);
        $html_tabla = $oTabla->mostrar_tabla();

        return [
            'html_tabla' => $html_tabla,
            'a_cabeceras' => $a_cabeceras,
            'a_valores' => $a_valores,
            'titulo' => ucfirst(_("listado de actividades")),
            'pref_error' => $pref_error,
        ];
    }
}
