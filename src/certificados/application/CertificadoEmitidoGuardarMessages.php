<?php

namespace src\certificados\application;

/**
 * Mensajes legibles al guardar un certificado emitido (errores de BD, etc.).
 */
final class CertificadoEmitidoGuardarMessages
{
    public static function fromThrowable(\Throwable $e): string
    {
        return self::fromDatabaseError($e->getMessage());
    }

    public static function fromDatabaseError(string $raw): string
    {
        if (self::isDuplicatePersonaFecha($raw)) {
            return _(
                "Ya existe un certificado emitido para esta persona con la misma fecha de certificado. "
                . "Cambie la fecha o consulte el listado de certificados ya emitidos."
            );
        }

        return $raw;
    }

    /**
     * Solo la restricción única de persona y fecha (`e_certificados_rstgr_ukey` / `e_certificados_dl_ukey`,
     * o el detalle `Key (id_nom, f_certificado)`); otra clave duplicada se devuelve tal cual.
     */
    private static function isDuplicatePersonaFecha(string $raw): bool
    {
        return preg_match('/e_certificados_(rstgr|dl)_ukey/i', $raw) === 1
            || preg_match('/\(\s*id_nom\s*,\s*f_certificado\s*\)/i', $raw) === 1;
    }
}
