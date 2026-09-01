<?php

declare(strict_types=1);

namespace src\notas\application;

use src\notas\application\support\ActaContenidoImpreso;
use src\notas\application\support\ActaFirmadaPolicy;
use src\notas\domain\contracts\ActaRepositoryInterface;

/**
 * Anota la huella del contenido académico en el momento de imprimir el acta.
 */
final class ActaMarcarImpresa
{
    public function __construct(
        private readonly ActaRepositoryInterface $actaRepository,
        private readonly ActaContenidoImpreso $contenidoImpreso,
        private readonly ActaFirmadaPolicy $firmadaPolicy,
    ) {
    }

    public function execute(string $acta): string
    {
        $acta = trim($acta);
        if ($acta === '') {
            return _('Falta el acta');
        }
        $oActa = $this->actaRepository->findById($acta);
        if ($oActa === null) {
            return sprintf(_('No se encuentra el acta: %s'), $acta);
        }
        if ($this->firmadaPolicy->estaFirmada($oActa)) {
            return '';
        }

        $hash = $this->contenidoImpreso->hashDeActa($oActa);
        if ($oActa->getHash_impreso() === $hash) {
            return '';
        }
        $oActa->setHash_impreso($hash);
        if ($this->actaRepository->Guardar($oActa) === false) {
            return (string) $this->actaRepository->getErrorTxt();
        }

        return '';
    }
}
