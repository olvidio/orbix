<?php

namespace src\notas\application\support;


use src\notas\domain\contracts\ActaTribunalDlRepositoryInterface;
use src\notas\domain\entity\ActaTribunal;

/**
 * Reconstruye la tabla de tribunales asociada a un acta: elimina los
 * tribunales existentes y graba los nuevos segun el array de POST.
 *
 * Se usa desde ActaNueva y ActaModificar.
 */
final class ActaTribunalSync
{

    public function __construct(
        private readonly ActaTribunalDlRepositoryInterface $actaTribunalDlRepository,
    ) {
    }
    /**
     * @param list<string> $examinadores
     */
    public function rebuild(string $acta, array $examinadores): string
    {
        $error = '';

        $repo = $this->actaTribunalDlRepository;
        $cActaTribunal = $repo->getActasTribunales(['acta' => $acta]);
        foreach ($cActaTribunal as $oActaTribunal) {
            if ($repo->Eliminar($oActaTribunal) === false) {
                $error .= _("hay un error, no se ha eliminado");
                $error .= "\n" . $repo->getErrorTxt();
            }
        }

        if (!empty($examinadores)) {
            $i = 0;
            foreach ($examinadores as $examinador) {
                $i++;
                if (empty($examinador)) {
                    continue;
                }
                $newIdItem = $repo->getNewId();
                $oActaTribunal = new ActaTribunal();
                $oActaTribunal->setId_item($newIdItem);
                $oActaTribunal->setActa($acta);
                $oActaTribunal->setExaminador($examinador);
                $oActaTribunal->setOrden($i);
                if ($repo->Guardar($oActaTribunal) === false) {
                    $error .= _("hay un error, no se ha guardado");
                    $error .= "\n" . $repo->getErrorTxt();
                }
            }
        }

        return $error;
    }
}
