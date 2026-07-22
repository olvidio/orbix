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

    /**
     * @return array<string, mixed>
     */
    public static function postPayload(mixed $data): array
    {
        return is_array($data) ? $data : [];
    }

    /**
     * @param array<string, mixed> $payload
     * @return array{
     *     Qobj_pau: string,
     *     titulo: string,
     *     dl: string,
     *     f_nacimiento: string,
     *     situacion: string,
     *     f_situacion: string,
     *     profesion: string,
     *     stgr: string,
     *     observ: string,
     *     ctr: string,
     *     telfs: string,
     *     mails: string,
     *     aviso: string,
     * }
     */
    public static function homeFromPayload(array $payload, string $defaultObjPau, string $defaultAviso): array
    {
        return [
            'Qobj_pau' => PayloadCoercion::string($payload['Qobj_pau'] ?? $defaultObjPau),
            'titulo' => PayloadCoercion::string($payload['titulo'] ?? ''),
            'dl' => PayloadCoercion::string($payload['dl'] ?? ''),
            'f_nacimiento' => PayloadCoercion::string($payload['f_nacimiento'] ?? ''),
            'situacion' => PayloadCoercion::string($payload['situacion'] ?? ''),
            'f_situacion' => PayloadCoercion::string($payload['f_situacion'] ?? ''),
            'profesion' => PayloadCoercion::string($payload['profesion'] ?? ''),
            'stgr' => PayloadCoercion::string($payload['stgr'] ?? ''),
            'observ' => PayloadCoercion::string($payload['observ'] ?? ''),
            'ctr' => PayloadCoercion::string($payload['ctr'] ?? ''),
            'telfs' => PayloadCoercion::string($payload['telfs'] ?? ''),
            'mails' => PayloadCoercion::string($payload['mails'] ?? ''),
            'aviso' => PayloadCoercion::string($payload['aviso'] ?? $defaultAviso),
        ];
    }

    /**
     * @param array<string, mixed> $payload
     * @return array{
     *     id_nom: int,
     *     Qobj_pau: string,
     *     trato: string,
     *     nom: string,
     *     apel_fam: string,
     *     nx1: string,
     *     apellido1: string,
     *     nx2: string,
     *     apellido2: string,
     *     lugar_nacimiento: string,
     *     f_nacimiento: string,
     *     f_situacion: string,
     *     profesion: string,
     *     sacd: string,
     *     eap: string,
     *     inc: string,
     *     f_inc: string,
     *     ce: string,
     *     ce_lugar: string,
     *     ce_ini: string,
     *     ce_fin: string,
     *     observ: string,
     *     titulo: string,
     *     nom_ctr: string,
     *     id_ctr: string,
     *     id_tabla: string,
     *     dl: string,
     *     idioma_preferido: string,
     *     situacion: string,
     *     nivel_stgr: string,
     *     edad: string,
     *     opciones_dl: array<int|string, string>,
     *     opciones_centros: array<int|string, string>,
     *     opciones_situacion: array<int|string, string>,
     *     opciones_lengua: array<int|string, string>,
     *     opciones_stgr: array<int|string, string>,
     *     opciones_inc: array<int|string, string>,
     * }
     */
    public static function editarFormFromPayload(array $payload, int $defaultIdNom, string $defaultObjPau): array
    {
        return [
            'id_nom' => PayloadCoercion::int($payload['id_nom'] ?? $defaultIdNom),
            'Qobj_pau' => PayloadCoercion::string($payload['Qobj_pau'] ?? $defaultObjPau),
            'trato' => PayloadCoercion::string($payload['trato'] ?? ''),
            'nom' => PayloadCoercion::string($payload['nom'] ?? ''),
            'apel_fam' => PayloadCoercion::string($payload['apel_fam'] ?? ''),
            'nx1' => PayloadCoercion::string($payload['nx1'] ?? ''),
            'apellido1' => PayloadCoercion::string($payload['apellido1'] ?? ''),
            'nx2' => PayloadCoercion::string($payload['nx2'] ?? ''),
            'apellido2' => PayloadCoercion::string($payload['apellido2'] ?? ''),
            'lugar_nacimiento' => PayloadCoercion::string($payload['lugar_nacimiento'] ?? ''),
            'f_nacimiento' => PayloadCoercion::string($payload['f_nacimiento'] ?? ''),
            'f_situacion' => PayloadCoercion::string($payload['f_situacion'] ?? ''),
            'profesion' => PayloadCoercion::string($payload['profesion'] ?? ''),
            'sacd' => PayloadCoercion::string($payload['sacd'] ?? ''),
            'eap' => PayloadCoercion::string($payload['eap'] ?? ''),
            'inc' => PayloadCoercion::string($payload['inc'] ?? ''),
            'f_inc' => PayloadCoercion::string($payload['f_inc'] ?? ''),
            'ce' => PayloadCoercion::string($payload['ce'] ?? ''),
            'ce_lugar' => PayloadCoercion::string($payload['ce_lugar'] ?? ''),
            'ce_ini' => PayloadCoercion::string($payload['ce_ini'] ?? ''),
            'ce_fin' => PayloadCoercion::string($payload['ce_fin'] ?? ''),
            'observ' => PayloadCoercion::string($payload['observ'] ?? ''),
            'titulo' => PayloadCoercion::string($payload['titulo'] ?? ''),
            'nom_ctr' => PayloadCoercion::string($payload['nom_ctr'] ?? ''),
            'id_ctr' => PayloadCoercion::string($payload['id_ctr'] ?? ''),
            'id_tabla' => PayloadCoercion::string($payload['id_tabla'] ?? ''),
            'dl' => PayloadCoercion::string($payload['dl'] ?? ''),
            'idioma_preferido' => PayloadCoercion::string($payload['idioma_preferido'] ?? ''),
            'situacion' => PayloadCoercion::string($payload['situacion'] ?? ''),
            'nivel_stgr' => PayloadCoercion::string($payload['nivel_stgr'] ?? ''),
            'edad' => PayloadCoercion::string($payload['edad'] ?? ''),
            'opciones_dl' => NotasFormSupport::desplegableOpciones($payload['opciones_dl'] ?? []),
            'opciones_centros' => NotasFormSupport::desplegableOpciones($payload['opciones_centros'] ?? []),
            'opciones_situacion' => NotasFormSupport::desplegableOpciones($payload['opciones_situacion'] ?? []),
            'opciones_lengua' => NotasFormSupport::desplegableOpciones($payload['opciones_lengua'] ?? []),
            'opciones_stgr' => NotasFormSupport::desplegableOpciones($payload['opciones_stgr'] ?? []),
            'opciones_inc' => NotasFormSupport::desplegableOpciones($payload['opciones_inc'] ?? []),
        ];
    }

    /**
     * @param array<string, mixed> $payload
     * @return array{
     *     tabla: string,
     *     obj_pau: string,
     *     id_tabla: string,
     *     permiso: int,
     *     sPrefs: string,
     *     total: int,
     *     aviso: string,
     *     personas: list<array{
     *         id_nom: int,
     *         id_tabla: string,
     *         nom: string,
     *         nombre_ubi: string,
     *         nivel_stgr: string,
     *         situacion: string,
     *         f_situacion: string,
     *     }>,
     * }
     */
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
            'tabla' => PayloadCoercion::string($payload['tabla'] ?? $defaultTabla),
            'obj_pau' => PayloadCoercion::string($payload['obj_pau'] ?? ''),
            'id_tabla' => PayloadCoercion::string($payload['id_tabla'] ?? ''),
            'permiso' => PayloadCoercion::int($payload['permiso'] ?? 1),
            'sPrefs' => PayloadCoercion::string($payload['sPrefs'] ?? ''),
            'total' => PayloadCoercion::int($payload['total'] ?? 0),
            'aviso' => PayloadCoercion::string($payload['aviso'] ?? $defaultAviso),
            'personas' => $personas,
        ];
    }

    /**
     * @return array{
     *     id_nom: int,
     *     id_tabla: string,
     *     nom: string,
     *     nombre_ubi: string,
     *     nivel_stgr: string,
     *     situacion: string,
     *     f_situacion: string
     * }
     */
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
            'id_nom' => PayloadCoercion::int($raw['id_nom'] ?? 0),
            'id_tabla' => PayloadCoercion::string($raw['id_tabla'] ?? ''),
            'nom' => PayloadCoercion::string($raw['nom'] ?? ''),
            'nombre_ubi' => PayloadCoercion::string($raw['nombre_ubi'] ?? ''),
            'nivel_stgr' => PayloadCoercion::string($raw['nivel_stgr'] ?? ''),
            'situacion' => PayloadCoercion::string($raw['situacion'] ?? ''),
            'f_situacion' => PayloadCoercion::string($raw['f_situacion'] ?? ''),
        ];
    }

    /**
     * @param array<string, mixed> $payload
     * @return array{
     *     nom: string,
     *     nivel_stgr: string,
     *     opciones_nivel_stgr: array<int|string, string>,
     * }
     */
    public static function stgrCambioFromPayload(array $payload): array
    {
        return [
            'nom' => PayloadCoercion::string($payload['nom'] ?? ''),
            'nivel_stgr' => PayloadCoercion::string($payload['nivel_stgr'] ?? ''),
            'opciones_nivel_stgr' => NotasFormSupport::desplegableOpciones($payload['opciones_nivel_stgr'] ?? []),
        ];
    }

    /**
     * @param array<string, mixed> $payload
     * @return array{
     *     titulo: string,
     *     id_ctr: int|float|string,
     *     nombre_ctr: string,
     *     dl: string,
     *     hoy: string,
     *     opciones_centros: array<int|string, string>,
     *     opciones_dl: array<int|string, string>,
     *     opciones_situacion: array<int|string, string>,
     * }
     */
    public static function trasladoFormFromPayload(array $payload): array
    {
        return [
            'titulo' => PayloadCoercion::string($payload['titulo'] ?? ''),
            'id_ctr' => NotasFormSupport::formScalar($payload['id_ctr'] ?? ''),
            'nombre_ctr' => PayloadCoercion::string($payload['nombre_ctr'] ?? ''),
            'dl' => PayloadCoercion::string($payload['dl'] ?? ''),
            'hoy' => PayloadCoercion::string($payload['hoy'] ?? ''),
            'opciones_centros' => NotasFormSupport::desplegableOpciones($payload['opciones_centros'] ?? []),
            'opciones_dl' => NotasFormSupport::desplegableOpciones($payload['opciones_dl'] ?? []),
            'opciones_situacion' => NotasFormSupport::desplegableOpciones($payload['opciones_situacion'] ?? []),
        ];
    }
}
