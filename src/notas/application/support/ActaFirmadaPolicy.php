<?php

declare(strict_types=1);

namespace src\notas\application\support;

use src\notas\domain\contracts\ActaRepositoryInterface;
use src\notas\domain\entity\Acta;
use src\notas\domain\value_objects\NotaSituacion;

/**
 * Un acta con PDF firmado no admite cambios de cabecera ni de notas.
 */
final class ActaFirmadaPolicy
{
    public function __construct(
        private readonly ActaRepositoryInterface $actaRepository,
    ) {
    }

    public function estaFirmada(?Acta $oActa): bool
    {
        return $oActa !== null && $oActa->tienePdfFirmado();
    }

    /**
     * Mensaje de bloqueo si el identificador corresponde a un acta firmada.
     * Cadenas vacías y la situación «cursada» no son un número de acta.
     */
    public function mensajeSiFirmada(?string $acta): string
    {
        $acta = trim((string) $acta);
        if ($acta === '' || $acta === (string) NotaSituacion::CURSADA) {
            return '';
        }
        $oActa = $this->actaRepository->findById($acta);
        if (!$this->estaFirmada($oActa)) {
            return '';
        }

        return _('El acta está firmada y no se puede modificar');
    }
}
