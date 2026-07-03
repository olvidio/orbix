<?php

declare(strict_types=1);

namespace frontend\personas\helpers;

use frontend\notas\helpers\NotasFormSupport;
use frontend\shared\helpers\PayloadCoercion;
use src\permisos\domain\XPermisos;

final class PersonasPayload
{
public static function oPerm(): ?XPermisos
{
    $oPerm = $_SESSION['oPerm'] ?? null;

    return $oPerm instanceof XPermisos ? $oPerm : null;
}

public static function havePermOficina(string $oficina): bool
{
    $oPerm = self::oPerm();

    return $oPerm !== null && $oPerm->have_perm_oficina($oficina);
}

public static function postPayload(mixed $data): array
{
    return is_array($data) ? $data : [];
}

public static function homeFromPayload(array $payload, string $defaultObjPau, string $defaultAviso): array
{
    return [
        'Qobj_pau' => \frontend\shared\helpers\PayloadCoercion::string($payload['Qobj_pau'] ?? $defaultObjPau),
        'titulo' => \frontend\shared\helpers\PayloadCoercion::string($payload['titulo'] ?? ''),
        'dl' => \frontend\shared\helpers\PayloadCoercion::string($payload['dl'] ?? ''),
        'f_nacimiento' => \frontend\shared\helpers\PayloadCoercion::string($payload['f_nacimiento'] ?? ''),
        'situacion' => \frontend\shared\helpers\PayloadCoercion::string($payload['situacion'] ?? ''),
        'f_situacion' => \frontend\shared\helpers\PayloadCoercion::string($payload['f_situacion'] ?? ''),
        'profesion' => \frontend\shared\helpers\PayloadCoercion::string($payload['profesion'] ?? ''),
        'stgr' => \frontend\shared\helpers\PayloadCoercion::string($payload['stgr'] ?? ''),
        'observ' => \frontend\shared\helpers\PayloadCoercion::string($payload['observ'] ?? ''),
        'ctr' => \frontend\shared\helpers\PayloadCoercion::string($payload['ctr'] ?? ''),
        'telfs' => \frontend\shared\helpers\PayloadCoercion::string($payload['telfs'] ?? ''),
        'mails' => \frontend\shared\helpers\PayloadCoercion::string($payload['mails'] ?? ''),
        'aviso' => \frontend\shared\helpers\PayloadCoercion::string($payload['aviso'] ?? $defaultAviso),
    ];
}

public static function editarFormFromPayload(array $payload, int $defaultIdNom, string $defaultObjPau): array
{
    return [
        'id_nom' => \frontend\shared\helpers\PayloadCoercion::int($payload['id_nom'] ?? $defaultIdNom),
        'Qobj_pau' => \frontend\shared\helpers\PayloadCoercion::string($payload['Qobj_pau'] ?? $defaultObjPau),
        'trato' => \frontend\shared\helpers\PayloadCoercion::string($payload['trato'] ?? ''),
        'nom' => \frontend\shared\helpers\PayloadCoercion::string($payload['nom'] ?? ''),
        'apel_fam' => \frontend\shared\helpers\PayloadCoercion::string($payload['apel_fam'] ?? ''),
        'nx1' => \frontend\shared\helpers\PayloadCoercion::string($payload['nx1'] ?? ''),
        'apellido1' => \frontend\shared\helpers\PayloadCoercion::string($payload['apellido1'] ?? ''),
        'nx2' => \frontend\shared\helpers\PayloadCoercion::string($payload['nx2'] ?? ''),
        'apellido2' => \frontend\shared\helpers\PayloadCoercion::string($payload['apellido2'] ?? ''),
        'lugar_nacimiento' => \frontend\shared\helpers\PayloadCoercion::string($payload['lugar_nacimiento'] ?? ''),
        'f_nacimiento' => \frontend\shared\helpers\PayloadCoercion::string($payload['f_nacimiento'] ?? ''),
        'f_situacion' => \frontend\shared\helpers\PayloadCoercion::string($payload['f_situacion'] ?? ''),
        'profesion' => \frontend\shared\helpers\PayloadCoercion::string($payload['profesion'] ?? ''),
        'sacd' => \frontend\shared\helpers\PayloadCoercion::string($payload['sacd'] ?? ''),
        'eap' => \frontend\shared\helpers\PayloadCoercion::string($payload['eap'] ?? ''),
        'inc' => \frontend\shared\helpers\PayloadCoercion::string($payload['inc'] ?? ''),
        'f_inc' => \frontend\shared\helpers\PayloadCoercion::string($payload['f_inc'] ?? ''),
        'ce' => \frontend\shared\helpers\PayloadCoercion::string($payload['ce'] ?? ''),
        'ce_lugar' => \frontend\shared\helpers\PayloadCoercion::string($payload['ce_lugar'] ?? ''),
        'ce_ini' => \frontend\shared\helpers\PayloadCoercion::string($payload['ce_ini'] ?? ''),
        'ce_fin' => \frontend\shared\helpers\PayloadCoercion::string($payload['ce_fin'] ?? ''),
        'observ' => \frontend\shared\helpers\PayloadCoercion::string($payload['observ'] ?? ''),
        'titulo' => \frontend\shared\helpers\PayloadCoercion::string($payload['titulo'] ?? ''),
        'nom_ctr' => \frontend\shared\helpers\PayloadCoercion::string($payload['nom_ctr'] ?? ''),
        'id_ctr' => \frontend\shared\helpers\PayloadCoercion::string($payload['id_ctr'] ?? ''),
        'id_tabla' => \frontend\shared\helpers\PayloadCoercion::string($payload['id_tabla'] ?? ''),
        'dl' => \frontend\shared\helpers\PayloadCoercion::string($payload['dl'] ?? ''),
        'idioma_preferido' => \frontend\shared\helpers\PayloadCoercion::string($payload['idioma_preferido'] ?? ''),
        'situacion' => \frontend\shared\helpers\PayloadCoercion::string($payload['situacion'] ?? ''),
        'nivel_stgr' => \frontend\shared\helpers\PayloadCoercion::string($payload['nivel_stgr'] ?? ''),
        'edad' => \frontend\shared\helpers\PayloadCoercion::string($payload['edad'] ?? ''),
        'opciones_dl' => NotasFormSupport::desplegableOpciones($payload['opciones_dl'] ?? []),
        'opciones_centros' => NotasFormSupport::desplegableOpciones($payload['opciones_centros'] ?? []),
        'opciones_situacion' => NotasFormSupport::desplegableOpciones($payload['opciones_situacion'] ?? []),
        'opciones_lengua' => NotasFormSupport::desplegableOpciones($payload['opciones_lengua'] ?? []),
        'opciones_stgr' => NotasFormSupport::desplegableOpciones($payload['opciones_stgr'] ?? []),
        'opciones_inc' => NotasFormSupport::desplegableOpciones($payload['opciones_inc'] ?? []),
    ];
}

public static function selectTablaFromPayload(array $payload, string $defaultTabla, string $defaultAviso): array
{
    $personasRaw = $payload['personas'] ?? [];
    $personas = [];
    if (is_array($personasRaw)) {
        foreach ($personasRaw as $row) {
            $personas[] = self::selectFilaRow($row);
        }
    }

    return [
        'tabla' => \frontend\shared\helpers\PayloadCoercion::string($payload['tabla'] ?? $defaultTabla),
        'obj_pau' => \frontend\shared\helpers\PayloadCoercion::string($payload['obj_pau'] ?? ''),
        'id_tabla' => \frontend\shared\helpers\PayloadCoercion::string($payload['id_tabla'] ?? ''),
        'permiso' => \frontend\shared\helpers\PayloadCoercion::int($payload['permiso'] ?? 1),
        'sPrefs' => \frontend\shared\helpers\PayloadCoercion::string($payload['sPrefs'] ?? ''),
        'total' => \frontend\shared\helpers\PayloadCoercion::int($payload['total'] ?? 0),
        'aviso' => \frontend\shared\helpers\PayloadCoercion::string($payload['aviso'] ?? $defaultAviso),
        'personas' => $personas,
    ];
}

public static function selectFilaRow(mixed $raw): array
{
    if (!is_array($raw)) {
        return [
            'id_nom' => 0,
            'id_tabla' => '',
            'nom' => '',
            'nombre_ubi' => '',
            'nivel_stgr' => '',
            'situacion' => '',
            'f_situacion' => '',
        ];
    }

    return [
        'id_nom' => \frontend\shared\helpers\PayloadCoercion::int($raw['id_nom'] ?? 0),
        'id_tabla' => \frontend\shared\helpers\PayloadCoercion::string($raw['id_tabla'] ?? ''),
        'nom' => \frontend\shared\helpers\PayloadCoercion::string($raw['nom'] ?? ''),
        'nombre_ubi' => \frontend\shared\helpers\PayloadCoercion::string($raw['nombre_ubi'] ?? ''),
        'nivel_stgr' => \frontend\shared\helpers\PayloadCoercion::string($raw['nivel_stgr'] ?? ''),
        'situacion' => \frontend\shared\helpers\PayloadCoercion::string($raw['situacion'] ?? ''),
        'f_situacion' => \frontend\shared\helpers\PayloadCoercion::string($raw['f_situacion'] ?? ''),
    ];
}

public static function stgrCambioFromPayload(array $payload): array
{
    return [
        'nom' => \frontend\shared\helpers\PayloadCoercion::string($payload['nom'] ?? ''),
        'nivel_stgr' => \frontend\shared\helpers\PayloadCoercion::string($payload['nivel_stgr'] ?? ''),
        'opciones_nivel_stgr' => NotasFormSupport::desplegableOpciones($payload['opciones_nivel_stgr'] ?? []),
    ];
}

public static function trasladoFormFromPayload(array $payload): array
{
    return [
        'titulo' => \frontend\shared\helpers\PayloadCoercion::string($payload['titulo'] ?? ''),
        'id_ctr' => NotasFormSupport::formScalar($payload['id_ctr'] ?? ''),
        'nombre_ctr' => \frontend\shared\helpers\PayloadCoercion::string($payload['nombre_ctr'] ?? ''),
        'dl' => \frontend\shared\helpers\PayloadCoercion::string($payload['dl'] ?? ''),
        'hoy' => \frontend\shared\helpers\PayloadCoercion::string($payload['hoy'] ?? ''),
        'opciones_centros' => NotasFormSupport::desplegableOpciones($payload['opciones_centros'] ?? []),
        'opciones_dl' => NotasFormSupport::desplegableOpciones($payload['opciones_dl'] ?? []),
        'opciones_situacion' => NotasFormSupport::desplegableOpciones($payload['opciones_situacion'] ?? []),
    ];
}
}
