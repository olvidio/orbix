<?php

namespace src\notas\application;

use core\ConfigGlobal;
use src\notas\application\support\ActaDlGuard;
use src\notas\application\support\ActaTribunalSync;
use src\notas\domain\contracts\ActaDlRepositoryInterface;

final class ActaEliminar
{
    public static function execute(array $input): string
    {
        $acta = (string)($input['acta'] ?? '');
        $aSel = (array)($input['sel'] ?? []);
        if (!empty($aSel)) {
            $acta = urldecode(strtok($aSel[0], "#"));
        }

        $miDele = ConfigGlobal::mi_delef();
        $err = ActaDlGuard::ensureOwnership($acta, $miDele, 'eliminar');
        if ($err !== '') {
            return $err;
        }

        $repo = $GLOBALS['container']->get(ActaDlRepositoryInterface::class);
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
        $error .= ActaTribunalSync::rebuild($acta, []);

        return $error;
    }
}
