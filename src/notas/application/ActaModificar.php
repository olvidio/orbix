<?php

namespace src\notas\application;

use src\shared\config\ConfigGlobal;
use src\notas\application\support\ActaDlGuard;
use src\notas\application\support\ActaTribunalSync;
use src\notas\domain\contracts\ActaDlRepositoryInterface;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\helpers\FuncTablasSupport;

final class ActaModificar
{

    public function __construct(
        private readonly ActaDlRepositoryInterface $actaDlRepository,
        private readonly ActaTribunalSync $actaTribunalSync,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     */
    public function execute(array $input): string
    {
        $acta = FuncTablasSupport::inputString($input, 'acta');
        $aSel = (array)($input['sel'] ?? []);
        if ($aSel !== []) {
            $sel0 = $aSel[0];
            if (is_string($sel0)) {
                $part = strtok($sel0, '#');
                $acta = urldecode(is_string($part) ? $part : '');
            }
        }

        $miDele = ConfigGlobal::mi_delef();
        $err = ActaDlGuard::ensureOwnership($acta, $miDele, 'modificar');
        if ($err !== '') {
            return $err;
        }

        $rawF_acta = FuncTablasSupport::inputString($input, 'f_acta');
        $parsedF_acta = $rawF_acta === '' ? null : DateTimeLocal::createFromLocal($rawF_acta);
        $oF_acta = $parsedF_acta instanceof DateTimeLocal ? $parsedF_acta : null;

        $repo = $this->actaDlRepository;
        $oActa = $repo->findById($acta);
        if ($oActa === null) {
            return _("No se encuentra el acta");
        }

        $oActa->setId_asignatura(FuncTablasSupport::inputInt($input, 'id_asignatura'));
        $oActa->setId_activ(FuncTablasSupport::inputInt($input, 'id_activ'));
        $oActa->setF_acta($oF_acta);
        $oActa->setLibro(FuncTablasSupport::inputInt($input, 'libro'));
        $oActa->setPagina(FuncTablasSupport::inputInt($input, 'pagina'));
        $oActa->setLinea(FuncTablasSupport::inputInt($input, 'linea'));
        $oActa->setLugar(FuncTablasSupport::inputString($input, 'lugar'));
        $oActa->setObserv(FuncTablasSupport::inputString($input, 'observ'));

        $error = '';
        if ($repo->Guardar($oActa) === false) {
            $error .= _("hay un error, no se ha guardado");
            $error .= "\n" . $repo->getErrorTxt();
        }

        $examinadores = [];
        foreach ((array) ($input['examinadores'] ?? []) as $ex) {
            if (is_string($ex) && $ex !== '') {
                $examinadores[] = $ex;
            }
        }
        $error .= $this->actaTribunalSync->rebuild($acta, $examinadores);

        return $error;
    }
}
