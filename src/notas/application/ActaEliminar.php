<?php

namespace src\notas\application;

use src\shared\config\ConfigGlobal;
use src\notas\application\support\ActaDlGuard;
use src\notas\application\support\ActaTribunalSync;
use src\notas\domain\contracts\ActaDlRepositoryInterface;

final class ActaEliminar
{

    public function __construct(
        private readonly ActaDlRepositoryInterface $actaDlRepository,
        private readonly ActaTribunalSync $actaTribunalSync,
        private readonly ActaDlGuard $actaDlGuard,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     */
    public function execute(array $input): string
    {
        $acta = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'acta');
        $aSel = (array)($input['sel'] ?? []);
        if ($aSel !== []) {
            $sel0 = $aSel[0];
            if (is_string($sel0)) {
                $part = strtok($sel0, '#');
                $acta = urldecode(is_string($part) ? $part : '');
            }
        }

        $miDele = ConfigGlobal::mi_delef();
        $err = $this->actaDlGuard->ensureOwnership($acta, $miDele, 'eliminar');
        if ($err !== '') {
            return $err;
        }

        $repo = $this->actaDlRepository;
        $oActa = $repo->findById($acta);
        if ($oActa === null) {
            return _("No se encuentra el acta");
        }

        $error = '';
        if ($repo->Eliminar($oActa) === false) {
            $error .= _("hay un error, no se ha eliminado");
            $error .= "\n" . $repo->getErrorTxt();
        }

        // Cascade: eliminar tambien los tribunales asociados.
        $error .= $this->actaTribunalSync->rebuild($acta, []);

        return $error;
    }
}
