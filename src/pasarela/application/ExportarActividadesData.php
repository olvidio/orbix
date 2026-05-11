<?php

namespace src\pasarela\application;

use DateInterval;
use src\actividades\domain\contracts\ActividadDlRepositoryInterface;
use src\actividades\domain\entity\TiposActividades;
use src\actividadescentro\domain\contracts\CentroEncargadoRepositoryInterface;
use src\actividadtarifas\domain\contracts\RelacionTarifaTipoActividadRepositoryInterface;
use src\actividadtarifas\domain\contracts\TipoTarifaRepositoryInterface;
use src\shared\config\ConfigGlobal;
use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\domain\contracts\CasaDlRepositoryInterface;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\TarifaUbiRepositoryInterface;
use src\ubis\domain\entity\Ubi;

/**
 * Caso de uso "exportar actividades": dado un filtro (tipo de actividad, periodo
 * y casas), devuelve cabeceras + filas para el listado de exportación, mezclando
 * datos de actividades con las conversiones de pasarela.
 *
 * Devuelve un array serializable por {@see \src\shared\web\ContestarJson::enviar}
 * con la estructura:
 *  - `a_cabeceras`: nombres de columnas (string[]).
 *  - `a_botones`: array vacío (no hay acciones por fila en el listado actual).
 *  - `a_valores`: filas como `{1: ..., 2: ..., ..., 20: ...}`.
 *  - `errores`: texto con avisos a mostrar antes de la tabla (puede ir vacío).
 *
 * El frontend renderiza la tabla con `frontend\shared\web\Lista` a partir de estos
 * datos; este caso de uso no genera HTML.
 */
final class ExportarActividadesData
{
    public static function execute(array $input): array
    {
        $Qid_tipo_activ = (string)($input['id_tipo_activ'] ?? '');

        $mi_sfsv = ConfigGlobal::mi_sfsv();

        $CasaDlReposiroty = $GLOBALS['container']->get(CasaDlRepositoryInterface::class);
        $aCasasDl = $CasaDlReposiroty->getArrayCasas();

        $aWhere = [];
        $aOperador = [];
        if (empty($Qid_tipo_activ)) {
            $Qssfsv = (string)($input['isfsv_val'] ?? '');
            $Qsasistentes = (string)($input['iasistentes_val'] ?? '');
            $Qsactividad = (string)($input['iactividad_val'] ?? '');

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
            $oTipoActiv->setSfsvId($Qssfsv);
            $oTipoActiv->setAsistentesId($sasistentes);
            $oTipoActiv->setActividadId($sactividad);
            $Qid_tipo_activ = $oTipoActiv->getId_tipo_activ();
        }
        if ($Qid_tipo_activ !== '......') {
            $aWhere['id_tipo_activ'] = "^$Qid_tipo_activ";
            $aOperador['id_tipo_activ'] = '~';
        }

        $err = '';

        // Periodo: el frontend calcula las fechas con `frontend\shared\web\Periodo`
        // y las envía ya en formato ISO. Aquí recibimos los límites listos.
        $inicioIso = (string)($input['inicio_iso'] ?? '');
        $finIso = (string)($input['fin_iso'] ?? '');
        if ($inicioIso === '' || $finIso === '') {
            return [
                'a_cabeceras' => [],
                'a_botones' => [],
                'a_valores' => [],
                'errores' => _('Periodo no válido'),
            ];
        }
        $aWhere['f_ini'] = "'$inicioIso','$finIso'";
        $aOperador['f_ini'] = 'BETWEEN';

        // Posible selección múltiple de casas (id_ubi).
        $Qaid_cdc = $input['id_cdc'] ?? [];
        if (!is_array($Qaid_cdc)) {
            $Qaid_cdc = [];
        }
        if (!empty($Qaid_cdc)) {
            $v = "{" . implode(', ', $Qaid_cdc) . "}";
            $aWhere['id_ubi'] = $v;
            $aOperador['id_ubi'] = 'ANY';
        }

        // Posibles centros encargados.
        $gesCentrosDl = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
        $aCentrosPosibles = $gesCentrosDl->getArrayCentros();
        $aCentrosPosiblesSinSgAgd = [];
        foreach ($aCentrosPosibles as $id_ubi => $nombre_ubi) {
            $nombre_ubi_sin = preg_replace(['/^agd/', '/^sg/'], '', $nombre_ubi, 1);
            $aCentrosPosiblesSinSgAgd[$id_ubi] = $nombre_ubi_sin;
        }

        $ActividadDlRepository = $GLOBALS['container']->get(ActividadDlRepositoryInterface::class);
        $cActividades = $ActividadDlRepository->getActividades($aWhere, $aOperador);

        $a_cabeceras = [
            _("activada"),
            _("casa"),
            _("perfil"),
            _("nombre"),
            _("tipo"),
            _("fecha inicio"),
            _("hora inicio"),
            _("fecha fin"),
            _("hora fin"),
            _("activación"),
            _("plazas max."),
            _("organizador 1"),
            _("organizador 2"),
            _("organizador 3"),
            _("texto aviso"),
            _("contribución obligatoria"),
            _("contribución reserva"),
            _("contribución general"),
            _("contribución estudiante"),
            _("contribución no duerme"),
        ];

        $oConversiones = new Conversiones();
        $aConversion_nombre = $oConversiones->getArrayNombre();
        $aConversion_tipo = $oConversiones->getArrayTipo();
        $aConversion_perfil = $oConversiones->getArrayPerfil();
        $aConversion_activacion = $oConversiones->getArrayActivacion();
        $aContribucion_reserva = $oConversiones->getArrayContribucionReserva();
        $aTanto_por_cien_contribucion_no_duerme = $oConversiones->getArrayContribucionNoDuerme();

        $a_valores = [];
        $i = 0;
        $oHoy = new DateTimeLocal();
        $TipoTarifaRepository = $GLOBALS['container']->get(TipoTarifaRepositoryInterface::class);
        $CentroEncargadoRepository = $GLOBALS['container']->get(CentroEncargadoRepositoryInterface::class);
        $TarifaUbiRepository = $GLOBALS['container']->get(TarifaUbiRepositoryInterface::class);

        foreach ($cActividades as $oActividad) {
            $i++;
            $id_tipo_activ = $oActividad->getId_tipo_activ();
            $id_activ = $oActividad->getId_activ();
            $id_ubi = $oActividad->getId_ubi();
            $oF_ini = $oActividad->getF_ini();
            $f_ini = $oActividad->getF_ini()?->getFromLocal();
            $h_ini = $oActividad->getH_ini()?->format('H:i');
            $f_fin = $oActividad->getF_fin()?->getFromLocal();
            $h_fin = $oActividad->getH_fin()?->format('H:i');

            if (!empty($h_ini)) {
                $h_ini = preg_replace('/(\d+):(\d+):(\d+)/', '$1:$2', $h_ini ?? '');
            }
            if (!empty($h_fin)) {
                $h_fin = preg_replace('/(\d+):(\d+):(\d+)/', '$1:$2', $h_fin ?? '');
            }

            // Calcular fecha de activación.
            $activacion = $aConversion_activacion[$id_tipo_activ] ?? null;
            $oF_ini_dup = $oF_ini !== null ? clone $oF_ini : null;
            if ($activacion === 'upload') {
                $f_activacion = $oHoy->getFromLocal();
            } else {
                $num_dias_activacion = (int)$activacion;
                if (is_numeric($activacion) && $oF_ini_dup !== null) {
                    $oF_ini_dup->sub(new DateInterval("P{$num_dias_activacion}D"));
                    $f_activacion = $oF_ini_dup->getFromLocal();
                } else {
                    $oTipoActividad = new TiposActividades($id_tipo_activ);
                    $svsf = $oTipoActividad->getSfsvText();
                    $asistentes = $oTipoActividad->getAsistentesText();
                    $actividad = $oTipoActividad->getActividadText();
                    $tipo_txt = "$svsf $asistentes $actividad";
                    $err .= sprintf(_("valor no válido para la activación del tipo de actividad %s"), $tipo_txt);
                    $err .= '<br>';
                    $f_activacion = '?';
                }
            }
            $nombre_ubi = empty($aCasasDl[$id_ubi]) ? '??' : $aCasasDl[$id_ubi];

            // Plazas.
            $plazas_totales = $oActividad->getPlazas();
            if (empty($plazas_totales)) {
                $oCasa = Ubi::NewUbi($id_ubi);
                $plazas_max = null;
                if (method_exists($oCasa, 'getPlazas')) {
                    $plazas_max = $oCasa->getPlazas();
                }
                $plazas_totales = $plazas_max;
            }

            // Centros encargados.
            $aWhereCentros = [];
            $aWhereCentros['id_activ'] = $id_activ;
            $aWhereCentros['_ordre'] = 'num_orden DESC';
            $cCentrosEncargados = $CentroEncargadoRepository->getCentrosEncargados($aWhereCentros);
            $aCentrosEncargados = [0 => '', 1 => '', 2 => ''];
            if (!empty($cCentrosEncargados)) {
                for ($n = 0; $n < 4; $n++) {
                    if (!empty($cCentrosEncargados[$n])) {
                        $id_ctr = $cCentrosEncargados[$n]->getId_ubi();
                        $aCentrosEncargados[$n] = empty($aCentrosPosiblesSinSgAgd[$id_ctr]) ? '?' : $aCentrosPosiblesSinSgAgd[$id_ctr];
                    }
                }
            }

            // Contribuciones.
            $contribucion_obligatoria = ($id_tipo_activ < 200000) ? _("NO") : _("SÍ");
            $numero_de_dias = $oActividad->getDuracion();
            $id_tarifa = $oActividad->getTarifa();
            if (empty($id_tarifa)) {
                $RelacionTarifaTipoActividadRepository = $GLOBALS['container']->get(RelacionTarifaTipoActividadRepositoryInterface::class);
                $cTipoActivTarifas = $RelacionTarifaTipoActividadRepository->getTipoActivTarifas(['id_tipo_activ' => $id_tipo_activ]);
                if (empty($cTipoActivTarifas)) {
                    $oTipoActivdad = new TiposActividades($id_tipo_activ);
                    $nom_tipo_actividad = $oTipoActivdad->getNom();
                    $err .= sprintf(_("No está definido el tipo tarifa para el tipo de actividad: %s"), $nom_tipo_actividad);
                    $err .= '<br>';
                    $id_tarifa = 0;
                } else {
                    $oTipoActivTarifa = $cTipoActivTarifas[0];
                    $id_tarifa = $oTipoActivTarifa->getId_tarifa();
                }
            }
            $year = $oF_ini?->format('Y') ?? '';
            $cTarifas = $TarifaUbiRepository->getTarifaUbis([
                'id_ubi' => $id_ubi,
                'year' => $year,
                'id_tarifa' => $id_tarifa,
                '_ordre' => 'year,id_tarifa',
            ]);
            $cantidad = 0;
            $cantidad_estudiante = 0;
            if (empty($cTarifas)) {
                $oTipoTarifa = $TipoTarifaRepository->findById($id_tarifa);
                $nombre_tarifa = $oTipoTarifa?->getLetra() ?? '?';
                $err .= sprintf(_("No está definida la id_tarifa %s para la casa %s"), $nombre_tarifa, $nombre_ubi);
                $err .= '<br>';
            } else {
                foreach ($cTarifas as $oTarifa) {
                    $id_serie = $oTarifa->getId_serie();
                    if ($id_serie === 1) {
                        $cantidad = $oTarifa->getCantidad();
                    }
                    if ($id_serie === 2) {
                        $cantidad_estudiante = $oTarifa->getCantidad();
                    }
                }
            }

            $contribucion_general = $numero_de_dias * $cantidad;
            $contribucion_estudiante = $numero_de_dias * $cantidad_estudiante;
            $tanto_por_cien_no_duerme = $aTanto_por_cien_contribucion_no_duerme[$id_tipo_activ] ?? null;
            if (is_numeric($tanto_por_cien_no_duerme)) {
                $contribucion_no_duerme = (int)$tanto_por_cien_no_duerme / 100 * $contribucion_general;
            } else {
                $contribucion_no_duerme = '?';
            }

            $a_valores[$i][1] = _("Sí");
            $a_valores[$i][2] = $nombre_ubi;
            $a_valores[$i][3] = $aConversion_perfil[$id_tipo_activ] ?? '';
            $a_valores[$i][4] = ucfirst((string)($aConversion_nombre[$id_tipo_activ] ?? ''));
            $a_valores[$i][5] = $aConversion_tipo[$id_tipo_activ] ?? '';
            $a_valores[$i][6] = $f_ini;
            $a_valores[$i][7] = $h_ini;
            $a_valores[$i][8] = $f_fin;
            $a_valores[$i][9] = $h_fin;
            $a_valores[$i][10] = $f_activacion;
            $a_valores[$i][11] = $plazas_totales;
            $a_valores[$i][12] = $aCentrosEncargados[0];
            $a_valores[$i][13] = $aCentrosEncargados[1];
            $a_valores[$i][14] = $aCentrosEncargados[2];
            $a_valores[$i][15] = ''; // aviso
            $a_valores[$i][16] = $contribucion_obligatoria;
            $a_valores[$i][17] = $aContribucion_reserva[$id_tipo_activ] ?? '';
            $a_valores[$i][18] = $contribucion_general;
            $a_valores[$i][19] = $contribucion_estudiante;
            $a_valores[$i][20] = $contribucion_no_duerme;
        }

        return [
            'a_cabeceras' => $a_cabeceras,
            'a_botones' => [],
            'a_valores' => $a_valores,
            'errores' => $err,
        ];
    }
}
