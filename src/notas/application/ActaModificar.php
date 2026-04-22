<?php

namespace src\notas\application;

use core\ConfigGlobal;
use src\notas\application\support\ActaDlGuard;
use src\notas\application\support\ActaTribunalSync;
use src\notas\domain\contracts\ActaDlRepositoryInterface;
use src\shared\domain\value_objects\DateTimeLocal;

final class ActaModificar
{
    public static function execute(array $input): string
    {
        $acta = (string)($input['acta'] ?? '');
        $aSel = (array)($input['sel'] ?? []);
        if (!empty($aSel)) {
            $acta = urldecode(strtok($aSel[0], "#"));
        }

        $miDele = ConfigGlobal::mi_delef();
        $err = ActaDlGuard::ensureOwnership($acta, $miDele, 'modificar');
        if ($err !== '') {
            return $err;
        }

        $oF_acta = null;
        $f_acta = (string)($input['f_acta'] ?? '');
        if (!empty($f_acta)) {
            $oF_acta = DateTimeLocal::createFromLocal($f_acta);
        }

        $repo = $GLOBALS['container']->get(ActaDlRepositoryInterface::class);
        $oActa = $repo->findById($acta);
        if ($oActa === null) {
            return _("No se encuentra el acta");
        }

        $oActa->setId_asignatura((int)($input['id_asignatura'] ?? 0));
        $oActa->setId_activ((int)($input['id_activ'] ?? 0));
        $oActa->setF_acta($oF_acta);
        $oActa->setLibro((int)($input['libro'] ?? 0));
        $oActa->setPagina((int)($input['pagina'] ?? 0));
        $oActa->setLinea((int)($input['linea'] ?? 0));
        $oActa->setLugar((string)($input['lugar'] ?? ''));
        $oActa->setObserv((string)($input['observ'] ?? ''));

        $error = '';
        if ($repo->Guardar($oActa) === false) {
            $error .= _("hay un error, no se ha guardado");
            $error .= "\n" . $repo->getErrorTxt();
        }

        $examinadores = (array)($input['examinadores'] ?? []);
        $error .= ActaTribunalSync::rebuild($acta, $examinadores);

        return $error;
    }
}
