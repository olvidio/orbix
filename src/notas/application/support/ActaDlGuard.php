<?php

declare(strict_types=1);

namespace src\notas\application\support;

use src\notas\domain\contracts\MapaPrefijoActaEsquemaRepositoryInterface;

/**
 * Validación compartida para mutaciones de actas: la dl de cabecera del acta
 * ($Qacta empieza por "<dl> ") debe coincidir con la dl del usuario, ser "?"
 * (placeholder), o (salvo alta nueva) un prefijo absorbido mapeado a la misma DL.
 */
final class ActaDlGuard
{
    public function __construct(
        private readonly MapaPrefijoActaEsquemaRepositoryInterface $mapaPrefijoActaEsquemaRepository,
    ) {
    }

    public function ensureOwnership(string $acta, string $miDele, string $accion): string
    {
        $dlActa = strtok($acta, ' ');
        if ($dlActa === false) {
            return '';
        }
        $miDeleSinF = ($miDele !== '' && str_ends_with($miDele, 'f'))
            ? substr($miDele, 0, -1)
            : $miDele;
        if ($dlActa === $miDele || $dlActa === $miDeleSinF || $dlActa === '?') {
            return '';
        }
        // Prefijo absorbido: permitir modificar/eliminar actas históricas de la matriz.
        if ($accion !== 'nueva') {
            $base = ActaPrefijosDeEsquema::esquemaBaseSesion();
            if ($base !== ''
                && $this->mapaPrefijoActaEsquemaRepository->prefijoPerteneceAEsquema((string) $dlActa, $base)
            ) {
                return '';
            }
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
