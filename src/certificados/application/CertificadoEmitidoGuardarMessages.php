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
                'Ya existe un certificado emitido para esta persona con la misma fecha de certificado. '
                . 'Cambie la fecha o consulte el listado de certificados ya emitidos.'
            );
        }

        return $raw;
    }

    private static function isDuplicatePersonaFecha(string $raw): bool
    {
        return stripos($raw, 'duplicate key') !== false
            || stripos($raw, 'e_certificados_rstgr_ukey') !== false
            || stripos($raw, 'unique constraint') !== false && stripos($raw, 'id_nom') !== false;
    }
}
