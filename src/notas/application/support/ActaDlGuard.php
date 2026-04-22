<?php

namespace src\notas\application\support;

/**
 * Validacion compartida para mutaciones de actas: la dl de cabecera del acta
 * ($Qacta empieza por "<dl> ") debe coincidir con la dl del usuario o ser "?"
 * (placeholder para actas inventadas).
 */
final class ActaDlGuard
{
    public static function ensureOwnership(string $acta, string $miDele, string $accion): string
    {
        $dlActa = strtok($acta, ' ');
        if ($dlActa === false || $dlActa === '') {
            return '';
        }
        if ($dlActa === $miDele || $dlActa === '?') {
            return '';
        }
        switch ($accion) {
            case 'nueva':
                return _("No puede generar un acta de otra dl");
            case 'eliminar':
                return _("No puede eliminar un acta de otra dl");
            case 'modificar':
            default:
                return _("No puede modificar un acta de otra dl");
        }
    }
}
