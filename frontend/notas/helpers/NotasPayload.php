<?php

declare(strict_types=1);

namespace frontend\notas\helpers;

use frontend\shared\helpers\PayloadCoercion;

final class NotasPayload
{
public static function asigFaltanRow(mixed $raw): array
{
    if (!is_array($raw)) {
        return [
            'id_nom' => 0,
            'id_tabla' => '',
            'nom' => '',
            'nombre_ubi' => '',
            'stgr' => '',
            'asig_txt' => '',
            'telfs' => '',
            'mails' => '',
        ];
    }
    $asigTxt = $raw['asig_txt'] ?? '';
    if (is_int($asigTxt)) {
        $asigTxtStr = (string) $asigTxt;
    } else {
        $asigTxtStr = PayloadCoercion::string($asigTxt);
    }

    return [
        'id_nom' => PayloadCoercion::int($raw['id_nom'] ?? 0),
        'id_tabla' => PayloadCoercion::string($raw['id_tabla'] ?? ''),
        'nom' => PayloadCoercion::string($raw['nom'] ?? ''),
        'nombre_ubi' => PayloadCoercion::string($raw['nombre_ubi'] ?? ''),
        'stgr' => PayloadCoercion::string($raw['stgr'] ?? ''),
        'asig_txt' => $asigTxtStr,
        'telfs' => PayloadCoercion::string($raw['telfs'] ?? ''),
        'mails' => PayloadCoercion::string($raw['mails'] ?? ''),
    ];
}

public static function asigFaltanTablaFromPayload(array $payload): array
{
    $rawRows = $payload['rows'] ?? [];
    $rows = [];
    if (is_array($rawRows)) {
        foreach ($rawRows as $row) {
            $rows[] = self::asigFaltanRow($row);
        }
    }

    return [
        'titulo' => PayloadCoercion::string($payload['titulo'] ?? ''),
        'obj_pau' => PayloadCoercion::string($payload['obj_pau'] ?? ''),
        'rows' => $rows,
    ];
}

public static function botonesModificarTessera(): array
{
    return [
        ['txt' => _('modificar stgr'), 'click' => 'fnjs_modificar("#seleccionados")'],
        ['txt' => _('ver tessera'), 'click' => 'fnjs_tesera("#seleccionados")'],
    ];
}

public static function actaVerFormFromPayload(array $payload): array
{
    $examinadoresRaw = $payload['examinadores'] ?? [];
    $examinadores = [];
    if (is_array($examinadoresRaw)) {
        foreach ($examinadoresRaw as $item) {
            $examinadores[] = PayloadCoercion::string($item);
        }
    }
    $aActasRaw = $payload['a_actas'] ?? [];
    $aActas = [];
    if (is_array($aActasRaw)) {
        foreach ($aActasRaw as $item) {
            $aActas[] = PayloadCoercion::string($item);
        }
    }

    return [
        'notas' => PayloadCoercion::string($payload['notas'] ?? ''),
        'permiso' => PayloadCoercion::int($payload['permiso'] ?? 0),
        'mod' => PayloadCoercion::string($payload['mod'] ?? ''),
        'acta_actual' => PayloadCoercion::string($payload['acta_actual'] ?? ''),
        'acta_new' => PayloadCoercion::string($payload['acta_new'] ?? ''),
        'ult_acta' => PayloadCoercion::string($payload['ult_acta'] ?? ''),
        'f_acta' => PayloadCoercion::string($payload['f_acta'] ?? ''),
        'libro' => PayloadCoercion::string($payload['libro'] ?? ''),
        'ult_lib' => PayloadCoercion::string($payload['ult_lib'] ?? ''),
        'pagina' => PayloadCoercion::string($payload['pagina'] ?? ''),
        'ult_pag' => PayloadCoercion::string($payload['ult_pag'] ?? ''),
        'linea' => PayloadCoercion::string($payload['linea'] ?? ''),
        'ult_lin' => PayloadCoercion::string($payload['ult_lin'] ?? ''),
        'lugar' => PayloadCoercion::string($payload['lugar'] ?? ''),
        'observ' => PayloadCoercion::string($payload['observ'] ?? ''),
        'id_activ' => PayloadCoercion::int($payload['id_activ'] ?? 0),
        'id_asignatura_actual' => PayloadCoercion::string($payload['id_asignatura_actual'] ?? ''),
        'nombre_asignatura' => PayloadCoercion::string($payload['nombre_asignatura'] ?? ''),
        'examinadores' => $examinadores,
        'a_actas' => $aActas,
        'has_pdf' => !empty($payload['has_pdf']),
        'warn_no_id_activ' => !empty($payload['warn_no_id_activ']),
    ];
}

public static function actaSelectFromPayload(array $payload): array
{
    $aAsigRaw = $payload['a_asignaturas'] ?? [];
    $aAsignaturas = [];
    if (is_array($aAsigRaw)) {
        foreach ($aAsigRaw as $key => $value) {
            if (is_int($key)) {
                $aAsignaturas[$key] = PayloadCoercion::string($value);
            } elseif (is_numeric($key)) {
                $aAsignaturas[(int) $key] = PayloadCoercion::string($value);
            }
        }
    }
    $actasRaw = $payload['actas'] ?? [];
    $actas = [];
    if (is_array($actasRaw)) {
        foreach ($actasRaw as $row) {
            if (!is_array($row)) {
                continue;
            }
            $actas[] = [
                'acta' => PayloadCoercion::string($row['acta'] ?? ''),
                'f_acta' => PayloadCoercion::string($row['f_acta'] ?? ''),
                'id_asignatura' => PayloadCoercion::int($row['id_asignatura'] ?? 0),
                'has_pdf' => !empty($row['has_pdf']),
            ];
        }
    }

    return [
        'titulo' => PayloadCoercion::string($payload['titulo'] ?? ''),
        'a_asignaturas' => $aAsignaturas,
        'actas' => $actas,
    ];
}

public static function personaFormFromPayload(array $payload): array
{
    $voRaw = $payload['vo'] ?? [];
    $vo = is_array($voRaw) ? $voRaw : [];
    $nsRaw = $vo['NotaSituacion'] ?? [];
    $taRaw = $vo['TipoActa'] ?? [];
    $neRaw = $vo['NotaEpoca'] ?? [];
    $notaSituacion = is_array($nsRaw) ? $nsRaw : [];
    $tipoActa = is_array($taRaw) ? $taRaw : [];
    $notaEpoca = is_array($neRaw) ? $neRaw : [];
    $helpersRaw = $payload['helpers'] ?? [];
    $helpers = is_array($helpersRaw) ? $helpersRaw : [];

    return [
        'mod' => PayloadCoercion::string($payload['mod'] ?? ''),
        'id_asignatura_real' => PayloadCoercion::string($payload['id_asignatura_real'] ?? ''),
        'id_nivel' => NotasFormSupport::formScalar($payload['id_nivel'] ?? ''),
        'nombre_corto' => PayloadCoercion::string($payload['nombre_corto'] ?? ''),
        'id_situacion' => NotasFormSupport::formScalar($payload['id_situacion'] ?? ''),
        'nota_num' => PayloadCoercion::string($payload['nota_num'] ?? ''),
        'nota_max' => NotasFormSupport::formScalar($payload['nota_max'] ?? ''),
        'acta' => PayloadCoercion::string($payload['acta'] ?? ''),
        'tipo_acta' => NotasFormSupport::formScalar($payload['tipo_acta'] ?? ''),
        'f_acta' => PayloadCoercion::string($payload['f_acta'] ?? ''),
        'f_acta_iso' => PayloadCoercion::string($payload['f_acta_iso'] ?? ''),
        'preceptor' => NotasFormSupport::formBoolOrString($payload['preceptor'] ?? ''),
        'id_preceptor' => NotasFormSupport::formScalar($payload['id_preceptor'] ?? ''),
        'detalle' => PayloadCoercion::string($payload['detalle'] ?? ''),
        'epoca' => NotasFormSupport::formScalar($payload['epoca'] ?? ''),
        'id_activ' => NotasFormSupport::formScalar($payload['id_activ'] ?? ''),
        'nom_activ' => PayloadCoercion::string($payload['nom_activ'] ?? ''),
        'profesores' => NotasFormSupport::desplegableOpciones($payload['profesores'] ?? []),
        'asignaturas_faltan' => NotasFormSupport::desplegableOpciones($payload['asignaturas_faltan'] ?? []),
        'lista_situacion_no_acta' => PayloadCoercion::string($payload['lista_situacion_no_acta'] ?? '"11"'),
        'aOpcionesSituacion' => NotasFormSupport::desplegableOpciones($payload['aOpcionesSituacion'] ?? []),
        'vo' => [
            'NotaSituacion' => array_map(
                static fn (mixed $v): int => PayloadCoercion::int($v),
                $notaSituacion
            ),
            'TipoActa' => array_map(
                static fn (mixed $v): int => PayloadCoercion::int($v),
                $tipoActa
            ),
            'NotaEpoca' => array_map(
                static fn (mixed $v): int => PayloadCoercion::int($v),
                $notaEpoca
            ),
        ],
        'helpers' => [
            'condicion_js' => PayloadCoercion::string($helpers['condicion_js'] ?? ''),
            'op_genericas_json' => PayloadCoercion::string($helpers['op_genericas_json'] ?? ''),
        ],
    ];
}

public static function actividadesBuscarFromPayload(array $payload): array
{
    return [
        'delegaciones' => NotasFormSupport::desplegableOpciones($payload['delegaciones'] ?? []),
        'actividades' => NotasFormSupport::desplegableOpciones($payload['actividades'] ?? []),
        'dl_org_sel' => PayloadCoercion::string($payload['dl_org_sel'] ?? ''),
        'id_activ_sel' => PayloadCoercion::string($payload['id_activ_sel'] ?? ''),
    ];
}

public static function asignaturasPendientesFromPayload(array $payload): array
{
    $cabecerasRaw = $payload['cabeceras'] ?? [];
    $cabeceras = [];
    if (is_array($cabecerasRaw)) {
        foreach ($cabecerasRaw as $item) {
            $cabeceras[] = PayloadCoercion::string($item);
        }
    }
    $filasRaw = $payload['filas'] ?? [];
    $filas = [];
    if (is_array($filasRaw)) {
        foreach ($filasRaw as $key => $row) {
            if (is_int($key) && is_array($row)) {
                $filas[$key] = $row;
            }
        }
    }

    return [
        'cabeceras' => $cabeceras,
        'filas' => $filas,
        'delegaciones' => NotasFormSupport::desplegableOpciones($payload['delegaciones'] ?? []),
        'ambito_rstgr' => !empty($payload['ambito_rstgr']),
    ];
}
}
