<?php

namespace src\notas\application;

use function src\shared\domain\helpers\input_string;

use src\notas\domain\contracts\ActaRepositoryInterface;

/**
 * Elimina el PDF firmado asociado a un `Acta` (sin borrar el acta).
 */
final class ActaPdfEliminar
{

    public function __construct(
        private readonly ActaRepositoryInterface $actaRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     */
    public function execute(array $input): string
    {
        $acta = input_string($input, 'acta_num');
        if (empty($acta)) {
            return _("No se encuentra el acta");
        }

        $ActaRepository = $this->actaRepository;
        $oActa = $ActaRepository->findById($acta);
        if ($oActa === null) {
            return _("No se encuentra el acta");
        }
        $oActa->setPdf('');
        if ($ActaRepository->Guardar($oActa) === false) {
            return (string)$ActaRepository->getErrorTxt();
        }

        return '';
    }
}
