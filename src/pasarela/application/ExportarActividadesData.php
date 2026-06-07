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
 * y casas), devuelve cabeceras + filas para el listado de exportación.
 */
final class ExportarActividadesData
{
    public function __construct(
        private readonly CasaDlRepositoryInterface $casaDlRepository,
        private readonly CentroDlRepositoryInterface $centroDlRepository,
        private readonly ActividadDlRepositoryInterface $actividadDlRepository,
        private readonly TipoTarifaRepositoryInterface $tipoTarifaRepository,
        private readonly CentroEncargadoRepositoryInterface $centroEncargadoRepository,
        private readonly TarifaUbiRepositoryInterface $tarifaUbiRepository,
        private readonly RelacionTarifaTipoActividadRepositoryInterface $relacionTarifaTipoActividadRepository,
        private readonly Conversiones $conversiones,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @return array{a_cabeceras: list<string>, a_botones: list<mixed>, a_valores: array<int, array<int, mixed>>, errores: string}
     */
    public function execute(array $input): array
    {
        $idTipoRaw = $input['id_tipo_activ'] ?? '';
        $Qid_tipo_activ = is_string($idTipoRaw) ? $idTipoRaw : '';

        $mi_sfsv = ConfigGlobal::mi_sfsv();

        $aCasasDl = $this->casaDlRepository->getArrayCasas();

        $aWhere = [];
        $aOperador = [];
        if ($Qid_tipo_activ === '') {
            $ssfsvRaw = $input['isfsv_val'] ?? '';
            $Qssfsv = is_string($ssfsvRaw) ? $ssfsvRaw : '';
            $asistentesRaw = $input['iasistentes_val'] ?? '';
            $Qsasistentes = is_string($asistentesRaw) ? $asistentesRaw : '';
            $actividadRaw = $input['iactividad_val'] ?? '';
            $Qsactividad = is_string($actividadRaw) ? $actividadRaw : '';

            if ($Qssfsv === '') {
                if ($mi_sfsv === 1) {
                    $Qssfsv = 'sv';
                }
                if ($mi_sfsv === 2) {
                    $Qssfsv = 'sf';
                }
            }
            $sasistentes = $Qsasistentes === '' ? '.' : $Qsasistentes;
            $sactividad = $Qsactividad === '' ? '.' : $Qsactividad;
            $oTipoActiv = new TiposActividades();
            $oTipoActiv->setSfsvId($Qssfsv);
            $oTipoActiv->setAsistentesId($sasistentes);
            $oTipoActiv->setActividadId($sactividad);
            $Qid_tipo_activ = (string)$oTipoActiv->getId_tipo_activ();
        }
        if ($Qid_tipo_activ !== '......') {
            $aWhere['id_tipo_activ'] = "^$Qid_tipo_activ";
            $aOperador['id_tipo_activ'] = '~';
        }

        $err = '';

        $inicioRaw = $input['inicio_iso'] ?? '';
        $finRaw = $input['fin_iso'] ?? '';
        $inicioIso = is_string($inicioRaw) ? $inicioRaw : '';
        $finIso = is_string($finRaw) ? $finRaw : '';
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

        $Qaid_cdc = $input['id_cdc'] ?? [];
        if (!is_array($Qaid_cdc)) {
            $Qaid_cdc = [];
        }
        /** @var list<int|string> $Qaid_cdc */
        if ($Qaid_cdc !== []) {
            $v = '{' . implode(', ', array_map('strval', $Qaid_cdc)) . '}';
            $aWhere['id_ubi'] = $v;
            $aOperador['id_ubi'] = 'ANY';
        }

        $aCentrosPosibles = $this->centroDlRepository->getArrayCentros();
        $aCentrosPosiblesSinSgAgd = [];
        foreach ($aCentrosPosibles as $id_ubi => $nombre_ubi) {
            $nombre_ubi_sin = preg_replace(['/^agd/', '/^sg/'], '', (string)$nombre_ubi, 1);
            $aCentrosPosiblesSinSgAgd[$id_ubi] = $nombre_ubi_sin;
        }

        $cActividades = $this->actividadDlRepository->getActividades($aWhere, $aOperador);

        $a_cabeceras = [
            _('activada'),
            _('casa'),
            _('perfil'),
            _('nombre'),
            _('tipo'),
            _('fecha inicio'),
            _('hora inicio'),
            _('fecha fin'),
            _('hora fin'),
            _('activación'),
            _('plazas max.'),
            _('organizador 1'),
            _('organizador 2'),
            _('organizador 3'),
            _('texto aviso'),
            _('contribución obligatoria'),
            _('contribución reserva'),
            _('contribución general'),
            _('contribución estudiante'),
            _('contribución no duerme'),
        ];

        $aConversion_nombre = $this->conversiones->getArrayNombre();
        $aConversion_tipo = $this->conversiones->getArrayTipo();
        $aConversion_perfil = $this->conversiones->getArrayPerfil();
        $aConversion_activacion = $this->conversiones->getArrayActivacion();
        $aContribucion_reserva = $this->conversiones->getArrayContribucionReserva();
        $aTanto_por_cien_contribucion_no_duerme = $this->conversiones->getArrayContribucionNoDuerme();

        $a_valores = [];
        $i = 0;
        $oHoy = new DateTimeLocal();

        foreach ($cActividades as $oActividad) {
            $i++;
            $id_tipo_activ = $oActividad->getId_tipo_activ();
            $id_activ = $oActividad->getId_activ();
            $id_ubi = $oActividad->getId_ubi();
            $oF_ini = $oActividad->getF_ini();
            $f_ini = $oActividad->getF_ini()?->getFromLocal();
            $h_ini_raw = $oActividad->getH_ini()?->format('H:i');
            $f_fin = $oActividad->getF_fin()?->getFromLocal();
            $h_fin_raw = $oActividad->getH_fin()?->format('H:i');

            $h_ini = $h_ini_raw;
            if ($h_ini !== null && $h_ini !== '') {
                $h_ini = preg_replace('/(\d+):(\d+):(\d+)/', '$1:$2', $h_ini);
            }
            $h_fin = $h_fin_raw;
            if ($h_fin !== null && $h_fin !== '') {
                $h_fin = preg_replace('/(\d+):(\d+):(\d+)/', '$1:$2', $h_fin);
            }

            $activacion = $aConversion_activacion[$id_tipo_activ] ?? null;
            $oF_ini_dup = $oF_ini !== null ? clone $oF_ini : null;
            if ($activacion === 'upload') {
                $f_activacion = $oHoy->getFromLocal();
            } else {
                $num_dias_activacion = (int)$activacion;
                if (is_numeric($activacion) && $oF_ini_dup instanceof DateTimeLocal) {
                    $oF_ini_dup->sub(new DateInterval("P{$num_dias_activacion}D"));
                    $f_activacion = $oF_ini_dup->getFromLocal();
                } else {
                    $oTipoActividad = new TiposActividades($id_tipo_activ);
                    $svsf = $oTipoActividad->getSfsvText();
                    $asistentes = $oTipoActividad->getAsistentesText();
                    $actividad = $oTipoActividad->getActividadText();
                    $tipo_txt = "$svsf $asistentes $actividad";
                    $err .= sprintf(_('valor no válido para la activación del tipo de actividad %s'), $tipo_txt);
                    $err .= '<br>';
                    $f_activacion = '?';
                }
            }
            $nombre_ubi = empty($aCasasDl[$id_ubi]) ? '??' : $aCasasDl[$id_ubi];

            $plazas_totales = $oActividad->getPlazas();
            if (empty($plazas_totales)) {
                $oCasa = Ubi::NewUbi((int)$id_ubi);
                $plazas_max = null;
                if (is_object($oCasa) && method_exists($oCasa, 'getPlazas')) {
                    $plazas_max = $oCasa->getPlazas();
                }
                $plazas_totales = $plazas_max;
            }

            $aWhereCentros = [];
            $aWhereCentros['id_activ'] = $id_activ;
            $aWhereCentros['_ordre'] = 'num_orden DESC';
            $cCentrosEncargados = $this->centroEncargadoRepository->getCentrosEncargados($aWhereCentros);
            $aCentrosEncargados = [0 => '', 1 => '', 2 => ''];
            if ($cCentrosEncargados !== []) {
                for ($n = 0; $n < 4; $n++) {
                    if (!empty($cCentrosEncargados[$n])) {
                        $id_ctr = $cCentrosEncargados[$n]->getId_ubi();
                        $aCentrosEncargados[$n] = empty($aCentrosPosiblesSinSgAgd[$id_ctr]) ? '?' : $aCentrosPosiblesSinSgAgd[$id_ctr];
                    }
                }
            }

            $contribucion_obligatoria = ($id_tipo_activ < 200000) ? _('NO') : _('SÍ');
            $numero_de_dias = $oActividad->getDuracion();
            $id_tarifa = $oActividad->getTarifa();
            if (empty($id_tarifa)) {
                $cTipoActivTarifas = $this->relacionTarifaTipoActividadRepository->getTipoActivTarifas(['id_tipo_activ' => $id_tipo_activ]);
                if ($cTipoActivTarifas === []) {
                    $oTipoActivdad = new TiposActividades($id_tipo_activ);
                    $nom_tipo_actividad = $oTipoActivdad->getNom();
                    $err .= sprintf(_('No está definido el tipo tarifa para el tipo de actividad: %s'), $nom_tipo_actividad);
                    $err .= '<br>';
                    $id_tarifa = 0;
                } else {
                    $oTipoActivTarifa = $cTipoActivTarifas[0];
                    $id_tarifa = $oTipoActivTarifa->getId_tarifa();
                }
            }
            $year = $oF_ini?->format('Y') ?? '';
            $cTarifas = $this->tarifaUbiRepository->getTarifaUbis([
                'id_ubi' => $id_ubi,
                'year' => $year,
                'id_tarifa' => $id_tarifa,
                '_ordre' => 'year,id_tarifa',
            ]);
            $cantidad = 0;
            $cantidad_estudiante = 0;
            if ($cTarifas === []) {
                $oTipoTarifa = $this->tipoTarifaRepository->findById($id_tarifa);
                $nombre_tarifa = $oTipoTarifa?->getLetra() ?? '?';
                $err .= sprintf(_('No está definida la id_tarifa %s para la casa %s'), $nombre_tarifa, $nombre_ubi);
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

            $a_valores[$i][1] = _('Sí');
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
            $a_valores[$i][15] = '';
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
